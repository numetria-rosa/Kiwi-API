<?php
$page_title = 'Détails du vol';
$page_css = 'flight-details';
$page_js = 'flight-details';

session_start(); // Start the session

// Ensure calculate_duration is defined early
if (!function_exists('calculate_duration')) {
    function calculate_duration($departure_time, $arrival_time) {
        if (!$departure_time || !$arrival_time) return 'N/A';
        try {
            $departure = new DateTime($departure_time);
            $arrival = new DateTime($arrival_time);
            $interval = $departure->diff($arrival);
            // Ensure hours and minutes are at least two digits if needed for specific formatting, though %d is usually fine.
            return sprintf("%dh%02dm", $interval->h, $interval->i); // %02dm for two-digit minutes
        } catch (Exception $e) {
            error_log("Error calculating duration: " . $e->getMessage() . " for times " . $departure_time . " and " . $arrival_time);
            return 'N/A';
        }
    }
}

$segments_list = [];
$overall_city_from = $overall_city_to = $overall_depart_airport = $overall_arrival_airport = 'N/A';
$overall_depart_date_formatted = $overall_depart_time_formatted = 'N/A';
$overall_duration = 'N/A';
$stops_text = 'N/A'; // Default to N/A, set to Direct or X escale(s) later
$is_direct_flight = false;

$BagsFlightsTotalPrice = '0.00'; // For the top summary price (including bags if applicable)
$basePrice = '0.00';
$servicePrice = '0.00';
$totalTicketsPrice = '0.00'; // Price for tickets only
$adultsPrice = '0.00';
$childrenPrice = '0.00';
$infantsPrice = '0.00';

$page_data_valid = false;

if (isset($_SESSION['data'])) {
    $data = $_SESSION['data'];
    
    $BagsFlightsTotalPrice = number_format((float)($data['total'] ?? ($data['price']['amount'] ?? 0)), 2);

    // Primary source for segments for one-way details page
    if (isset($data['route']) && is_array($data['route']) && !empty($data['route'])) {
        $segments_list = $data['route'];
    } elseif (isset($data['flights'][0]['route']) && is_array($data['flights'][0]['route']) && !empty($data['flights'][0]['route'])) {
        $segments_list = $data['flights'][0]['route'];
    } elseif (isset($data['flights'][0]) && is_array($data['flights'][0]) && !isset($data['flights'][0]['route']) && isset($data['flights'][0]['flyFrom'])) {
        // Check for a key like 'flyFrom' to ensure it's a segment-like object
        $segments_list = [$data['flights'][0]]; 
    }

    if (!empty($segments_list)) {
        $page_data_valid = true; // We have segments to process
        $first_segment = $segments_list[0];
        $last_segment = end($segments_list);
        reset($segments_list);

        $overall_city_from = $first_segment['cityFrom'] ?? 'N/A';
        $overall_depart_airport = $first_segment['flyFrom'] ?? 'N/A';
        $overall_city_to = $last_segment['cityTo'] ?? 'N/A';
        $overall_arrival_airport = $last_segment['flyTo'] ?? 'N/A';

        $overall_depart_date_raw = $first_segment['local_departure'] ?? null;
        $overall_arrival_date_raw = $last_segment['local_arrival'] ?? null;

        if ($overall_depart_date_raw) {
            try {
                $dt = new DateTime($overall_depart_date_raw);
                $overall_depart_date_formatted = $dt->format('D, d M Y');
                $overall_depart_time_formatted = $dt->format('H:i');
            } catch (Exception $e) {
                error_log("Error parsing overall departure date: " . $e->getMessage());
            }
        }
        
        $overall_duration = calculate_duration($overall_depart_date_raw, $overall_arrival_date_raw);
        
        if (count($segments_list) === 1) {
            $stops_text = 'Direct';
            $is_direct_flight = true;
        } else {
            $stops_text = (count($segments_list) - 1) . ' escale(s)';
            $is_direct_flight = false;
        }
    }

    // Price breakdown logic (remains similar, ensure $data keys are correct)
    $fare_details = $data['fare'] ?? ($data['tickets_price_split'] ?? []);
    $basePrice = $fare_details['base'] ?? '0';
    $servicePrice = $fare_details['service'] ?? '0';
    $adultsPrice = $fare_details['adults'] ?? ($data['adults_price'] ?? 0);
    $childrenPrice = $fare_details['children'] ?? ($data['children_price'] ?? 0);
    $infantsPrice = $fare_details['infants'] ?? ($data['infants_price'] ?? 0);

    if (isset($fare_details['amount'])) {
        $totalTicketsPrice = $fare_details['amount'];
    } elseif(isset($data['price']['amount']) && empty($fare_details)) { 
         $totalTicketsPrice = $data['price']['amount'];
    } else {
        $calculated_total = (float)$adultsPrice + (float)$childrenPrice + (float)$infantsPrice;
        if ($calculated_total > 0) {
            $totalTicketsPrice = $calculated_total;
        } elseif (isset($data['tickets_price'])) { 
            $totalTicketsPrice = $data['tickets_price'];
        } elseif ($BagsFlightsTotalPrice !== '0.00' && (float)$basePrice == 0 && (float)$servicePrice == 0 && $calculated_total == 0) {
             // If BagsFlightsTotalPrice is the only price, use it for tickets total as a last resort.
            $totalTicketsPrice = str_replace(',', '', $BagsFlightsTotalPrice); // remove comma for float conversion
        } else {
            $totalTicketsPrice = '0'; // Default to 0 if no other source
        }
    }
    
    $basePrice = number_format((float)$basePrice, 2);
    $servicePrice = number_format((float)$servicePrice, 2);
    $totalTicketsPrice = number_format((float)$totalTicketsPrice, 2);
    $adultsPrice = number_format((float)$adultsPrice, 2);
    $childrenPrice = number_format((float)$childrenPrice, 2);
    $infantsPrice = number_format((float)$infantsPrice, 2);
    
    // Initialize totalBaggagePrice
    $totalBaggagePrice = 0;
    
    // Calculate baggage price if baggage data exists
    if (isset($data['baggage']['combinations']) && !empty($data['baggage']['combinations'])) {
        $definitions = $data['baggage']['definitions'] ?? [];
        
        if (!function_exists('calculateBaggagePrice')) {
            function calculateBaggagePrice($bagType, $combinations, $definitions) {
                $total = 0;
                if (!empty($combinations[$bagType]) && !empty($definitions[$bagType])) {
                    foreach ($combinations[$bagType] as $bag) {
                        if (!empty($bag['indices'])) {
                            foreach ($bag['indices'] as $index) {
                                if(isset($definitions[$bagType][$index])) {
                                    $details = $definitions[$bagType][$index];
                                    $price = $details['price'] ?? ['amount' => 0];
                                    if ($price['amount'] > 0) {
                                        $total += $price['amount'];
                                    }
                                }
                            }
                        }
                    }
                }
                return $total;
            }
        }

        $handBagPrice = calculateBaggagePrice('hand_bag', $data['baggage']['combinations'], $definitions);
        $holdBagPrice = calculateBaggagePrice('hold_bag', $data['baggage']['combinations'], $definitions);
        $totalBaggagePrice = $handBagPrice + $holdBagPrice;
    }
    
    // Calculate final total (tickets + baggage)
    $totalFinal = number_format((float)$totalTicketsPrice + $totalBaggagePrice, 2);

} else {
    // $page_data_valid remains false
}

// unset($_SESSION['data']); // Or unset specific keys like $_SESSION['data']['route'], $_SESSION['data']['price'] etc.

include('../includes/header.php');
?>

<div class="flight-details-container">
    <div class="container">
        <?php if ($page_data_valid): ?>
        <!-- Booking Summary -->
        <div class="flight-booking-summary">
            <div class="booking-summary-content">
                <div class="price-summary">
                    <div class="price-header">
                        <h2>Résumé de votre réservation</h2>
                        <div class="total-price"><?php echo $totalFinal; ?> EUR</div>
                    </div>
                    <div class="price-breakdown">
                        <div class="price-category">
                            <h3>Prix des billets</h3>
                            <div class="price-item">
                                <span class="price-label">Base</span>
                                <span class="price-value"><?php echo $basePrice; ?> EUR</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Frais de service</span>
                                <span class="price-value"><?php echo $servicePrice; ?> EUR</span>
                            </div>
                            <div class="price-item total">
                                <span class="price-label">Total billets</span>
                                <span class="price-value"><?php echo $totalTicketsPrice; ?> EUR</span>
                            </div>
                        </div>
                        <?php if ((float)str_replace(',', '', $adultsPrice) > 0 || (float)str_replace(',', '', $childrenPrice) > 0 || (float)str_replace(',', '', $infantsPrice) > 0) : ?>
                        <div class="price-category">
                            <h3>Par passager</h3>
                            <?php if ((float)str_replace(',', '', $adultsPrice) > 0): ?>
                            <div class="price-item">
                                <span class="price-label">Adultes</span>
                                <span class="price-value"><?php echo $adultsPrice; ?> EUR</span>
                            </div>
                            <?php endif; ?>
                            <?php if ((float)str_replace(',', '', $childrenPrice) > 0): ?>
                            <div class="price-item">
                                <span class="price-label">Enfants</span>
                                <span class="price-value"><?php echo $childrenPrice; ?> EUR</span>
                            </div>
                            <?php endif; ?>
                            <?php if ((float)str_replace(',', '', $infantsPrice) > 0): ?>
                            <div class="price-item">
                                <span class="price-label">Bébés</span>
                                <span class="price-value"><?php echo $infantsPrice; ?> EUR</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php
                        // Add baggage price breakdown
                        if (isset($data['baggage']['combinations']) && !empty($data['baggage']['combinations'])) {
                            $definitions = $data['baggage']['definitions'] ?? [];
                            $totalBaggagePrice = 0;
                            if (!function_exists('calculateBaggagePrice')) {
                                function calculateBaggagePrice($bagType, $combinations, $definitions) {
                                    $total = 0;
                                    if (!empty($combinations[$bagType]) && !empty($definitions[$bagType])) {
                                        foreach ($combinations[$bagType] as $bag) {
                                            if (!empty($bag['indices'])) {
                                                foreach ($bag['indices'] as $index) {
                                                    if(isset($definitions[$bagType][$index])) {
                                                        $details = $definitions[$bagType][$index];
                                                        $price = $details['price'] ?? ['amount' => 0];
                                                        if ($price['amount'] > 0) {
                                                            $total += $price['amount'];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    return $total;
                                }
                            }

                            $handBagPrice = calculateBaggagePrice('hand_bag', $data['baggage']['combinations'], $definitions);
                            $holdBagPrice = calculateBaggagePrice('hold_bag', $data['baggage']['combinations'], $definitions);
                            $totalBaggagePrice = $handBagPrice + $holdBagPrice;

                            if ($totalBaggagePrice > 0) {
                                echo "<div class='price-category'>";
                                echo "<h3>Bagages supplémentaires</h3>";
                                if ($handBagPrice > 0) {
                                    echo "<div class='price-item'>";
                                    echo "<span class='price-label'>Bagages à main</span>";
                                    echo "<span class='price-value'>" . number_format($handBagPrice, 2) . " EUR</span>";
                                    echo "</div>";
                                }
                                if ($holdBagPrice > 0) {
                                    echo "<div class='price-item'>";
                                    echo "<span class='price-label'>Bagages enregistrés</span>";
                                    echo "<span class='price-value'>" . number_format($holdBagPrice, 2) . " EUR</span>";
                                    echo "</div>";
                                }
                                echo "<div class='price-item total'>";
                                echo "<span class='price-label'>Total bagages</span>";
                                echo "<span class='price-value'>" . number_format($totalBaggagePrice, 2) . " EUR</span>";
                                echo "</div>";
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
                <a href="<?php 
                    $visitorUniqId = $_SESSION['booking_data']['visitorUniqId'] ?? '';
                    $sessionId = $_SESSION['booking_data']['sessionId'] ?? '';
                    $bookingToken = $data['booking_token'] ?? ''; // Get from main $data array
                                        
                    $bookingUrlParams = array_filter([
                        'adults' => $_SESSION['passenger_counts']['adults'] ?? 1,
                        'children' => $_SESSION['passenger_counts']['children'] ?? 0,
                        'infants' => $_SESSION['passenger_counts']['infants'] ?? 0,
                        'visitorUniqId' => $visitorUniqId,
                        'sessionId' => $sessionId,
                        'bookingToken' => $bookingToken
                    ]);
                    
                    if (empty($bookingToken)) {
                        error_log("Critical: Missing bookingToken for flight_details.php. Data: " . json_encode($data));
                        echo '#error-missing-booking-token'; 
                    } else {
                        echo '/Test/views/passenger_details.php?' . http_build_query($bookingUrlParams);
                    }
                ?>" class="btn btn-primary btn-book">Réserver ce vol</a>
            </div>
        </div>

        <!-- Flight Details Content -->
        <div class="flight-details-content">
            <div class="breadcrumbs">
                <a href="<?php echo function_exists('generate_url') ? generate_url('home') : '#home'; ?>">Accueil</a>
                <span class="separator">›</span>
                <a href="<?php echo function_exists('generate_url') ? generate_url('search', ['departure' => $overall_city_from, 'destination' => $overall_city_to]) : '#search'; ?>">Résultats de recherche</a>
                <span class="separator">›</span>
                <span class="current">Détails du vol</span>
            </div>

            <h1 class="page-title"><?php echo htmlspecialchars($overall_city_from); ?> → <?php echo htmlspecialchars($overall_city_to); ?></h1>

            <div class="flight-dates">
                <?php echo htmlspecialchars($overall_depart_date_formatted); ?>
            </div>
            
            <div class="flight-section">
                <h2 class="section-title">Détails de votre vol</h2>
                 <div class="leg-summary" style="margin-bottom: 20px; padding: 15px; background-color: #e9f5fe; border-radius: 8px; border: 1px solid #b3d7f2;">
                    <p style="margin:0; font-size: 1.1em;"><strong>Trajet:</strong> <?php echo htmlspecialchars($overall_city_from); ?> (<?php echo htmlspecialchars($overall_depart_airport); ?>) → <?php echo htmlspecialchars($overall_city_to); ?> (<?php echo htmlspecialchars($overall_arrival_airport); ?>)</p>
                    <p style="margin:5px 0 0; font-size: 1em;"><strong>Durée totale:</strong> <?php echo htmlspecialchars($overall_duration); ?> <span style="color: #555;">(<?php echo htmlspecialchars($stops_text); ?>)</span></p>
                </div>

                <?php foreach ($segments_list as $s_idx => $segment_data): ?>
                    <?php
                    // Extract details for THIS segment_data
                    $seg_city_from = $segment_data['cityFrom'] ?? 'N/A';
                    $seg_city_to = $segment_data['cityTo'] ?? 'N/A';
                    $seg_depart_airport = $segment_data['flyFrom'] ?? 'N/A';
                    $seg_arrival_airport = $segment_data['flyTo'] ?? 'N/A';
                    
                    $seg_depart_datetime_raw = $segment_data['local_departure'] ?? null;
                    $seg_arrival_datetime_raw = $segment_data['local_arrival'] ?? null;

                    $seg_depart_date_formatted = 'N/A';
                    $seg_depart_time_formatted = 'N/A';
                    $seg_arrival_time_formatted = 'N/A';

                    if ($seg_depart_datetime_raw) {
                        try {
                            $dt_dep = new DateTime($seg_depart_datetime_raw);
                            $seg_depart_date_formatted = $dt_dep->format('D, d M Y');
                            $seg_depart_time_formatted = $dt_dep->format('H:i');
                        } catch (Exception $e) { error_log("Seg depart date parse error: " . $e->getMessage()); }
                    }
                    if ($seg_arrival_datetime_raw) {
                         try {
                            $dt_arr = new DateTime($seg_arrival_datetime_raw);
                            $seg_arrival_time_formatted = $dt_arr->format('H:i');
                        } catch (Exception $e) { error_log("Seg arrival date parse error: " . $e->getMessage()); }
                    }
                    
                    $seg_duration = calculate_duration($seg_depart_datetime_raw, $seg_arrival_datetime_raw);
                    
                    $seg_marketing_airline_iata = $segment_data['airline'] ?? 'N/A';
                    $seg_operating_carrier_iata = $segment_data['operating_carrier'] ?? $seg_marketing_airline_iata;
                    $seg_operating_airline_name = $segment_data['operating_airline_name'] ?? $seg_operating_carrier_iata; 
                    $seg_flight_number_display = ($segment_data['operating_flight_no'] ?? $segment_data['flight_no'] ?? 'N/A');
                    if ($seg_flight_number_display !== 'N/A' && $seg_operating_carrier_iata !== 'N/A') {
                        $seg_flight_number_display = $seg_operating_carrier_iata . ' ' . $seg_flight_number_display;
                    }
                    $fare_category = htmlspecialchars($segment_data['fare_category'] ?? ($segment_data['fare_classes'] ?? 'Economy'));

                    ?>
                    <div class="flight-card detailed segment-card" style="margin-top: 15px; <?php if (!$is_direct_flight && $s_idx > 0) echo 'border-top: 2px dashed #ddd; padding-top: -1px;'; ?>">
                        <div class="flight-header">
                            <div class="flight-type">
                                <?php if (!$is_direct_flight): ?>
                                    Segment <?php echo $s_idx + 1; ?>: 
                                <?php endif; ?>
                                <?php echo htmlspecialchars($seg_city_from); ?> (<?php echo htmlspecialchars($seg_depart_airport); ?>) → <?php echo htmlspecialchars($seg_city_to); ?> (<?php echo htmlspecialchars($seg_arrival_airport); ?>)
                            </div>
                            <?php if (!$is_direct_flight): // Show individual segment date only if multi-segment and potentially different
                                // Or always show if $overall_depart_date_formatted !== $seg_depart_date_formatted ?>
                                <div class="flight-date" style="font-size: 0.9em; color: #555;"><?php echo htmlspecialchars($seg_depart_date_formatted); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="flight-body">
                            <div class="flight-timeline">
                                <div class="timeline-start">
                                    <div class="time"><?php echo htmlspecialchars($seg_depart_time_formatted); ?></div>
                                    <div class="location">
                                        <div class="airport-code"><?php echo htmlspecialchars($seg_depart_airport); ?></div>
                                        <div class="city"><?php echo htmlspecialchars($seg_city_from); ?></div>
                                    </div>
                                </div>
                                <div class="timeline-duration">
                                    <div class="duration-line"></div>
                                    <div class="duration-text"><?php echo htmlspecialchars($seg_duration); ?></div>
                                    <div class="stops-text" style="color: #007bff;">Vol direct</div> 
                                </div>
                                <div class="timeline-end">
                                    <div class="time"><?php echo htmlspecialchars($seg_arrival_time_formatted); ?></div>
                                    <div class="location">
                                        <div class="airport-code"><?php echo htmlspecialchars($seg_arrival_airport); ?></div>
                                        <div class="city"><?php echo htmlspecialchars($seg_city_to); ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flight-info">
                                <div class="carrier-logo">
                                    <?php if ($seg_operating_carrier_iata && $seg_operating_carrier_iata !== 'N/A'): ?>
                                        <img src="../assets/img/airlines/<?php echo strtolower(htmlspecialchars($seg_operating_carrier_iata)); ?>.png" alt="<?php echo htmlspecialchars($seg_operating_airline_name); ?>" onerror="this.style.display='none'">
                                    <?php endif; ?>
                                </div>
                                <div class="carrier-details">
                                    <div class="carrier-name"><?php echo htmlspecialchars($seg_operating_airline_name); ?></div>
                                    <div class="flight-number"><?php echo htmlspecialchars($seg_flight_number_display); ?></div>
                                </div>
                                <div class="cabin-class">
                                    <span class="label">Classe :</span>
                                    <span class="value"><?php echo $fare_category; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!$is_direct_flight && $s_idx < count($segments_list) - 1): ?>
                        <?php
                        $next_segment_data = $segments_list[$s_idx + 1];
                        $layover_arrival_raw = $segment_data['local_arrival'] ?? null;
                        $layover_departure_raw = $next_segment_data['local_departure'] ?? null;
                        $layover_duration = calculate_duration($layover_arrival_raw, $layover_departure_raw);
                        $layover_city = $segment_data['cityTo'] ?? 'N/A';
                        $layover_airport = $segment_data['flyTo'] ?? 'N/A';
                        ?>
                        <div class="connection-info-card" style="margin: 20px auto; padding: 15px; background-color: #f0f0f0; border-left: 5px solid #ffc107; border-radius: 8px; max-width: 95%;">
                             <h4 style="margin-top:0; margin-bottom: 8px; font-size: 1.1em; color: #333;">Escale à <?php echo htmlspecialchars($layover_city); ?> (<?php echo htmlspecialchars($layover_airport); ?>)</h4>
                             <p style="margin-bottom:0; font-size: 1em; color: #555;">Durée de la correspondance: <?php echo htmlspecialchars($layover_duration); ?></p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
                <div class="alert alert-warning" role="alert" style="padding: 20px; margin-top: 20px; text-align: center;">
                    <h4 class="alert-heading">Données de vol non disponibles</h4>
                    <p>Les détails pour ce vol ne sont pas disponibles actuellement. Cela peut être dû à des informations manquantes ou une erreur temporaire.</p>
                    <hr>
                    <p class="mb-0">Veuillez <a href="<?php echo function_exists('generate_url') ? generate_url('search_form') : '#'; ?>" class="alert-link">retourner à la recherche</a> et essayer à nouveau, ou contactez notre support si le problème persiste.</p>
                </div>
            <?php endif; ?>

            <!-- Baggage Options -->
            <?php if ($page_data_valid && isset($data['baggage'])): // Only show baggage if main flight data is valid ?> 
            <div class="flight-section">
                <h2 class="section-title">Options de bagages</h2>
                <div class="baggage-options">
                    <?php
                    $totalBagsAmount = 0;

                    if (!empty($data['baggage']['combinations'])) {
                        $definitions = $data['baggage']['definitions'];

                        function renderBagItems($bagType, $combinations, $definitions) {
                            global $totalBagsAmount;

                            if (!empty($combinations[$bagType])) {
                                $title = $bagType === 'hand_bag' ? 'Bagages à main' : 'Bagages enregistrés';
                                $icon = $bagType === 'hand_bag' ? 'cabinbag.webp' : 'holdbag.webp';

                                echo "<div class='baggage-category'>";
                                echo "<h3><img src='../assets/img/addon/{$icon}' alt='' class='baggage-icon'> {$title}</h3>";

                                foreach ($combinations[$bagType] as $bag) {
                                    if (!empty($bag['indices'])) {
                                        foreach ($bag['indices'] as $index) {
                                            $details = $definitions[$bagType][$index];
                                            $r = $details['restrictions'];
                                            $price = $details['price'];

                                            echo "<div class='baggage-item'>";
                                            echo "<div class='baggage-dimensions'>";
                                            echo "<span class='baggage-weight'>{$r['weight']}kg</span>";
                                            echo "<span class='baggage-measurements'>{$r['length']}x{$r['width']}x{$r['height']} cm</span>";
                                            echo "</div>";
                                            echo "<div class='baggage-price'>";
                                            if ($price['amount'] == 0) {
                                                echo "<span class='included'>Inclus</span>";
                                            } else {
                                                echo "<div class='price-breakdown'>";
                                                echo "<div class='price-details'>";
                                                echo "<span class='base-price'>{$price['base']} EUR</span>";
                                                echo "<span class='service-price'>+ {$price['service']} EUR frais</span>";
                                                echo "</div>";
                                                echo "<span class='total-price'>{$price['amount']} EUR</span>";
                                                echo "</div>";
                                                $totalBagsAmount += $price['amount'];
                                            }
                                            echo "</div>";
                                            echo "</div>";
                                        }
                                    }
                                }

                                echo "</div>";
                            }
                        }

                        renderBagItems('hand_bag', $data['baggage']['combinations'], $definitions);
                        renderBagItems('hold_bag', $data['baggage']['combinations'], $definitions);

                        $baggageJson = htmlspecialchars(json_encode($data['baggage']), ENT_QUOTES, 'UTF-8');
                        echo "<input type='hidden' name='baggage_json' value='{$baggageJson}'>";
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Modern Flight Details Styling */
.flight-details-container {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 40px 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Booking Summary */
.flight-booking-summary {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    margin-bottom: 40px;
    overflow: hidden;
}

.booking-summary-content {
    padding: 32px;
}

.price-summary {
    margin-bottom: 24px;
}

.price-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.price-header h2 {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.total-price {
    font-size: 32px;
    font-weight: 700;
    color: #00a991;
}

.price-breakdown {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
}

.price-category {
    margin-bottom: 20px;
}

.price-category:last-child {
    margin-bottom: 0;
}

.price-category h3 {
    font-size: 16px;
    font-weight: 600;
    color: #666;
    margin-bottom: 12px;
}

.price-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e0e0e0;
}

.price-item:last-child {
    border-bottom: none;
}

.price-item.total {
    font-weight: 600;
    color: #333;
}

.price-label {
    color: #666;
    font-size: 14px;
}

.price-value {
    color: #333;
    font-weight: 500;
}

.btn-book {
    width: 100%;
    padding: 16px;
    font-size: 16px;
    font-weight: 600;
    background: #00a991;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-book:hover {
    background: #009882;
    transform: translateY(-1px);
}

/* Flight Details Content */
.flight-details-content {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    padding: 32px;
}

.breadcrumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 24px;
    font-size: 14px;
    color: #666;
}

.breadcrumbs a {
    color: #00a991;
    text-decoration: none;
    transition: color 0.2s ease;
}

.breadcrumbs a:hover {
    color: #009882;
}

.separator {
    color: #999;
}

.page-title {
    font-size: 32px;
    font-weight: 700;
    color: #333;
    margin-bottom: 16px;
}

.flight-dates {
    font-size: 18px;
    color: #666;
    margin-bottom: 32px;
}

/* Flight Section */
.flight-section {
    margin-bottom: 40px;
}

.section-title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin-bottom: 24px;
}

/* Flight Card */
.flight-card.detailed {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e0e0e0;
    overflow: hidden;
}

.flight-header {
    background: #f8f9fa;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e0e0e0;
}

.flight-type {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.flight-date {
    font-size: 14px;
    color: #666;
}

.flight-body {
    padding: 24px;
}

.flight-timeline {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
}

.timeline-start, .timeline-end {
    text-align: center;
}

.time {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.location {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

.airport-code {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.city {
    font-size: 14px;
    color: #666;
}

.timeline-duration {
    flex: 1;
    margin: 0 24px;
    text-align: center;
    position: relative;
}

.duration-line {
    height: 2px;
    background: #e0e0e0;
    margin: 1px 0;
}

.duration-text {
    font-size: 12px;
    color: #666;
    margin-bottom: 4px;
}

.stops-text {
    font-size: 14px;
    font-weight: 500;
    color: #00a991;
}

.flight-info {
    display: flex;
    align-items: center;
    gap: 24px;
    padding-top: 24px;
    border-top: 1px solid #e0e0e0;
}

.carrier-logo {
    width: 48px;
    height: 48px;
    background: #fff;
    border-radius: 8px;
    padding: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.carrier-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.carrier-details {
    flex: 1;
}

.carrier-name {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
}

.flight-number {
    font-size: 14px;
    color: #666;
}

.cabin-class {
    font-size: 14px;
    color: #666;
}

.cabin-class .label {
    color: #999;
}

/* Baggage Options */
.baggage-options {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 24px;
}

.baggage-category {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 24px;
    text-align: center;
}

.baggage-category h3 {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
}

.baggage-icon {
    width: 90px;
    height: 120px;
    background: transparent;
    padding: 0;
    box-shadow: none;
    border-radius: 0;
    margin-bottom: 12px;
}

.baggage-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 16px;
    border-bottom: 1px solid #e0e0e0;
}

.baggage-item:last-child {
    border-bottom: none;
}

.baggage-dimensions {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    font-size: 14px;
    color: #666;
}

.baggage-weight {
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.baggage-measurements {
    font-size: 14px;
    color: #666;
}

.baggage-price {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-top: 8px;
}

.baggage-price .included {
    color: #00a991;
}

.baggage-price .price-breakdown {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

.baggage-price .base-price {
    font-size: 14px;
    color: #666;
}

.baggage-price .service-price {
    font-size: 12px;
    color: #999;
}

.baggage-price .total-price {
    font-size: 16px;
    color: #333;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 0 16px;
    }

    .booking-summary-content,
    .flight-details-content {
        padding: 24px;
    }

    .price-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .flight-timeline {
        flex-direction: column;
        gap: 16px;
    }

    .timeline-duration {
        margin: 16px 0;
    }

    .flight-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
}
</style>

<?php
// Inclure le pied de page
include '../includes/footer.php';
?>
