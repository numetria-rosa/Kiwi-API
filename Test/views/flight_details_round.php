<?php
$page_title = 'Détails du vol aller-retour';
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
            return sprintf("%dh%dm", $interval->h, $interval->i);
        } catch (Exception $e) {
            // Log error or return N/A if dates are invalid
            error_log("Error calculating duration: " . $e->getMessage());
            return 'N/A';
        }
    }
}

// Define calculateBaggagePrice function early
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

// Initialize lists for segments
$outbound_segments_list = [];
$return_segments_list = [];

// Initialize overall leg information variables
$overall_outbound_city_from = $overall_outbound_city_to = $overall_outbound_depart_airport = $overall_outbound_arrival_airport = 'N/A';
$overall_outbound_depart_date_formatted = $overall_outbound_arrival_date_formatted = 'N/A';
$overall_outbound_depart_time_formatted = $overall_outbound_arrival_time_formatted = 'N/A';
$overall_outbound_duration = 'N/A';
$outbound_stops_text = 'Direct';

$overall_return_city_from = $overall_return_city_to = $overall_return_depart_airport = $overall_return_arrival_airport = 'N/A';
$overall_return_depart_date_formatted = $overall_return_arrival_date_formatted = 'N/A';
$overall_return_depart_time_formatted = $overall_return_arrival_time_formatted = 'N/A';
$overall_return_duration = 'N/A';
$return_stops_text = 'Direct';

// Check if session data exists
if (isset($_SESSION['data'])) {
    $data = $_SESSION['data']; // Retrieve the data from session

    $all_segments_from_api = $data['flights'] ?? ($data['route'] ?? []);

    if (!empty($all_segments_from_api)) {
        foreach ($all_segments_from_api as $segment) {
            // Assuming segment has a 'return' flag: 0 for outbound, 1 for return
            if (isset($segment['return']) && $segment['return'] === 0) {
                $outbound_segments_list[] = $segment;
            } elseif (isset($segment['return']) && $segment['return'] === 1) {
                $return_segments_list[] = $segment;
            }
        }
    }

    // Calculate overall outbound leg information
    if (!empty($outbound_segments_list)) {
        $outbound_first_segment = $outbound_segments_list[0];
        $outbound_last_segment = end($outbound_segments_list);
        reset($outbound_segments_list); // Important if array is used again in a loop

        $overall_outbound_city_from = $outbound_first_segment['cityFrom'] ?? $outbound_first_segment['src_name'] ?? 'N/A';
        $overall_outbound_depart_airport = $outbound_first_segment['flyFrom'] ?? $outbound_first_segment['src_station'] ?? 'N/A';
        $overall_outbound_city_to = $outbound_last_segment['cityTo'] ?? $outbound_last_segment['dst_name'] ?? 'N/A';
        $overall_outbound_arrival_airport = $outbound_last_segment['flyTo'] ?? $outbound_last_segment['dst_station'] ?? 'N/A';

        $overall_outbound_depart_date_raw = $outbound_first_segment['local_departure'] ?? null;
        $overall_outbound_arrival_date_raw = $outbound_last_segment['local_arrival'] ?? null;

        if ($overall_outbound_depart_date_raw) {
            $dt = new DateTime($overall_outbound_depart_date_raw);
            $overall_outbound_depart_date_formatted = $dt->format('D, d M Y');
            $overall_outbound_depart_time_formatted = $dt->format('H:i');
        }
        if ($overall_outbound_arrival_date_raw) {
            $dt = new DateTime($overall_outbound_arrival_date_raw);
            $overall_outbound_arrival_date_formatted = $dt->format('D, d M Y'); // Though arrival date for overall leg might not be primary display
            $overall_outbound_arrival_time_formatted = $dt->format('H:i');
        }
        $overall_outbound_duration = calculate_duration($overall_outbound_depart_date_raw, $overall_outbound_arrival_date_raw);
        $outbound_stops_text = count($outbound_segments_list) > 1 ? (count($outbound_segments_list) - 1) . ' escale(s)' : 'Direct';
        
        // Store flight summary info in session to pass to passenger_details.php
        $_SESSION['flight_summary_for_passenger_page'] = [
            'city_from' => $overall_outbound_city_from,
            'fly_from' => $overall_outbound_depart_airport,
            'city_to' => $overall_outbound_city_to,
            'fly_to' => $overall_outbound_arrival_airport,
            'depart_date' => $overall_outbound_depart_date_formatted,
            'depart_time' => $overall_outbound_depart_time_formatted
        ];
    }

    // Calculate overall return leg information
    if (!empty($return_segments_list)) {
        $return_first_segment = $return_segments_list[0];
        $return_last_segment = end($return_segments_list);
        reset($return_segments_list);

        $overall_return_city_from = $return_first_segment['cityFrom'] ?? $return_first_segment['src_name'] ?? 'N/A';
        $overall_return_depart_airport = $return_first_segment['flyFrom'] ?? $return_first_segment['src_station'] ?? 'N/A';
        $overall_return_city_to = $return_last_segment['cityTo'] ?? $return_last_segment['dst_name'] ?? 'N/A';
        $overall_return_arrival_airport = $return_last_segment['flyTo'] ?? $return_last_segment['dst_station'] ?? 'N/A';

        $overall_return_depart_date_raw = $return_first_segment['local_departure'] ?? null;
        $overall_return_arrival_date_raw = $return_last_segment['local_arrival'] ?? null;

        if ($overall_return_depart_date_raw) {
            $dt = new DateTime($overall_return_depart_date_raw);
            $overall_return_depart_date_formatted = $dt->format('D, d M Y');
            $overall_return_depart_time_formatted = $dt->format('H:i');
        }
        if ($overall_return_arrival_date_raw) {
            $dt = new DateTime($overall_return_arrival_date_raw);
            $overall_return_arrival_date_formatted = $dt->format('D, d M Y');
            $overall_return_arrival_time_formatted = $dt->format('H:i');
        }
        $overall_return_duration = calculate_duration($overall_return_depart_date_raw, $overall_return_arrival_date_raw);
        $return_stops_text = count($return_segments_list) > 1 ? (count($return_segments_list) - 1) . ' escale(s)' : 'Direct';
    }

    // Price related information (seems to be top-level in $data)
    $outboundTotalPrice = $data['total'] ?? ($data['price']['amount'] ?? 'N/A'); // Adjusted to check common price locations

    // Price breakdown
    $ticketsPriceSplit = $data['tickets_price_split'] ?? [];
    $basePrice = $ticketsPriceSplit['base'] ?? '0';
    $servicePrice = $ticketsPriceSplit['service'] ?? '0';
    $totalPrice = $ticketsPriceSplit['amount'] ?? '0';
    $adultsPrice = $data['adults_price'] ?? 0;
    $childrenPrice = $data['children_price'] ?? 0;
    $infantsPrice = $data['infants_price'] ?? 0;

    // Format prices to 2 decimal places
    $basePrice = number_format((float)$basePrice, 2);
    $servicePrice = number_format((float)$servicePrice, 2);
    $totalPrice = number_format((float)$totalPrice, 2);
    $adultsPrice = number_format($adultsPrice, 2);
    $childrenPrice = number_format($childrenPrice, 2);
    $infantsPrice = number_format($infantsPrice, 2);
    
    // Initialize totalBaggagePrice
    $totalBaggagePrice = 0;
    
    // Calculate baggage price if baggage data exists
    if (isset($data['baggage']['combinations']) && !empty($data['baggage']['combinations'])) {
        $definitions = $data['baggage']['definitions'] ?? [];

        $handBagPrice = calculateBaggagePrice('hand_bag', $data['baggage']['combinations'], $definitions);
        $holdBagPrice = calculateBaggagePrice('hold_bag', $data['baggage']['combinations'], $definitions);
        $totalBaggagePrice = $handBagPrice + $holdBagPrice;
    }
    
    // Calculate final total (tickets + baggage)
    $total = number_format((float)$totalPrice + $totalBaggagePrice, 2);
    // Now you can display all the variables in HTML
    // echo "<strong>Total Price:</strong> $outboundTotalPrice<br>";
    // echo "<strong>Flight From:</strong> $outboundCityFrom<br>";
    // echo "<strong>Flight To:</strong> $outboundCityTo<br>";
    // echo "<strong>Departing From:</strong> $outboundDepartAirport<br>";
    // echo "<strong>Arriving At:</strong> $outboundArrivalAirport<br>";
    // echo "<strong>Airline:</strong> $outboundAirlineName<br>";
    // echo "<strong>Operating Airline:</strong> $outboundOperatingAirline<br>";
    // echo "<strong>Flight Number:</strong> $outboundOperatingFlightNo<br>";
    // echo "<strong>Departure Date:</strong> $outboundDepartDate at $outboundDepartTime<br>";
    // echo "<strong>Arrival Date:</strong> $outboundArrivalDate at $outboundArrivalTime<br>";
} else {
    echo "No flight data available.";
}

include('../includes/header.php');
?>

<div class="flight-details-container">
    <div class="container">
        <!-- Booking Summary -->
        <div class="flight-booking-summary">
            <div class="booking-summary-content">
                <div class="price-summary">
                    <div class="price-header">
                        <h2>Résumé de votre réservation</h2>
                        <div class="total-price"><?php echo $total; ?> EUR</div>
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
                                <span class="price-value"><?php echo $totalPrice; ?> EUR</span>
                                
                            </div>
                        </div>
                        <div class="price-category">
                            <h3>Par passager</h3>
                            <div class="price-item">
                                <span class="price-label">Adultes</span>
                                <span class="price-value"><?php echo $adultsPrice; ?> EUR</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Enfants</span>
                                <span class="price-value"><?php echo $childrenPrice; ?> EUR</span>
                            </div>
                            <div class="price-item">
                                <span class="price-label">Bébés</span>
                                <span class="price-value"><?php echo $infantsPrice; ?> EUR</span>
                            </div>
                        </div>
                        <?php
                        // Add baggage price breakdown
                        if (!empty($data['baggage']['combinations'])) {
                            $definitions = $data['baggage']['definitions'];
                            $totalBaggagePrice = 0;

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
                    // Initialize default values and validate session data
                    $visitorUniqId = $_SESSION['booking_data']['visitorUniqId'] ?? '';
                    $sessionId = $_SESSION['booking_data']['sessionId'] ?? '';
                    $bookingToken = $_SESSION['booking_data']['bookingToken'] ?? '';
                    
                    // Validate that we have all required data
                    if (empty($visitorUniqId) || empty($sessionId) || empty($bookingToken)) {
                        // Log the error or handle it appropriately
                        error_log("Missing booking data in session: visitorUniqId=" . $visitorUniqId . 
                                ", sessionId=" . $sessionId . 
                                ", bookingToken=" . $bookingToken);
                    }
                    
                    // Construct the URL to passenger details page with passenger counts
                    $bookingUrl = '/Test/views/passenger_details.php?' . http_build_query([
                        'adults' => $_SESSION['passenger_counts']['adults'] ?? 1,
                        'children' => $_SESSION['passenger_counts']['children'] ?? 0,
                        'infants' => $_SESSION['passenger_counts']['infants'] ?? 0
                    ]);
                    echo $bookingUrl;
                ?>" class="btn btn-primary btn-book">Réserver ce vol</a>
            </div>
        </div>

        <!-- Flight Details -->
        <div class="flight-details-content">
            <div class="breadcrumbs">
                <a href="<?php echo generate_url('home'); ?>">Accueil</a>
                <span class="separator">›</span>
                <a href="<?php echo generate_url('search', ['departure' => $overall_outbound_city_from, 'destination' => $overall_outbound_city_to]); ?>">Résultats de recherche</a>
                <span class="separator">›</span>
                <span class="current">Détails du vol</span>
            </div>

            <h1 class="page-title"><?php echo $overall_outbound_city_from; ?> → <?php echo $overall_outbound_city_to; ?> <?php echo (!empty($return_segments_list) ? ('→ ' . $overall_return_city_to) : ''); ?></h1>

            <!-- Outbound Flight -->
            <div class="flight-section">
                <h2 class="section-title">Vol aller</h2>
                <div class="flight-dates"><?php echo $overall_outbound_depart_date_formatted; ?></div>
                <div class="leg-summary" style="margin-bottom: 15px; padding: 10px; background-color: #f0f8ff; border-radius: 5px;">
                    <strong>De:</strong> <?php echo $overall_outbound_city_from; ?> (<?php echo $overall_outbound_depart_airport; ?>)
                    <strong>À:</strong> <?php echo $overall_outbound_city_to; ?> (<?php echo $overall_outbound_arrival_airport; ?>)<br>
                    <strong>Durée totale du vol aller:</strong> <?php echo $overall_outbound_duration; ?> (<?php echo $outbound_stops_text; ?>)
                </div>

                <?php if (!empty($outbound_segments_list)): ?>
                    <?php foreach ($outbound_segments_list as $s_idx => $segment_data): ?>
                        <?php
                        // Extract details for THIS segment_data
                        $seg_city_from = $segment_data['cityFrom'] ?? $segment_data['src_name'] ?? 'N/A';
                        $seg_city_to = $segment_data['cityTo'] ?? $segment_data['dst_name'] ?? 'N/A';
                        $seg_depart_airport = $segment_data['flyFrom'] ?? $segment_data['src_station'] ?? 'N/A';
                        $seg_arrival_airport = $segment_data['flyTo'] ?? $segment_data['dst_station'] ?? 'N/A';
                        
                        $seg_depart_datetime_raw = $segment_data['local_departure'] ?? null;
                        $seg_arrival_datetime_raw = $segment_data['local_arrival'] ?? null;

                        $seg_depart_date_formatted = 'N/A';
                        $seg_depart_time_formatted = 'N/A';
                        $seg_arrival_date_formatted = 'N/A'; // For display within segment card if needed
                        $seg_arrival_time_formatted = 'N/A';

                        if ($seg_depart_datetime_raw) {
                            $dt_dep = new DateTime($seg_depart_datetime_raw);
                            $seg_depart_date_formatted = $dt_dep->format('D, d M Y');
                            $seg_depart_time_formatted = $dt_dep->format('H:i');
                        }
                        if ($seg_arrival_datetime_raw) {
                            $dt_arr = new DateTime($seg_arrival_datetime_raw);
                            $seg_arrival_date_formatted = $dt_arr->format('D, d M Y');
                            $seg_arrival_time_formatted = $dt_arr->format('H:i');
                        }
                        
                        $seg_duration = calculate_duration($seg_depart_datetime_raw, $seg_arrival_datetime_raw);
                        
                        // Airline info - ensure robust checking for nested arrays/objects
                        $seg_airline_name_display = 'N/A';
                        if (isset($segment_data['airline']) && is_array($segment_data['airline']) && isset($segment_data['airline']['Name'])) {
                            $seg_airline_name_display = $segment_data['airline']['Name'];
                        } elseif (isset($segment_data['operating_airline']) && is_array($segment_data['operating_airline']) && isset($segment_data['operating_airline']['name'])) {
                            $seg_airline_name_display = $segment_data['operating_airline']['name'];
                        }

                        $seg_op_airline_name = (isset($segment_data['operating_airline']) && is_array($segment_data['operating_airline']) && isset($segment_data['operating_airline']['name'])) ? $segment_data['operating_airline']['name'] : $seg_airline_name_display;
                        $seg_op_airline_iata = strtolower((isset($segment_data['operating_airline']) && is_array($segment_data['operating_airline']) && isset($segment_data['operating_airline']['iata'])) ? $segment_data['operating_airline']['iata'] : '');
                        $seg_op_flight_no = $segment_data['operating_flight_no'] ?? ($segment_data['flight_no'] ?? 'N/A');
                        ?>
                        <div class="flight-card detailed segment-card <?php echo ($s_idx > 0) ? 'connection-segment' : ''; ?>" style="margin-top: 15px;">
                            <div class="flight-header">
                                <div class="flight-type">
                                    Segment <?php echo $s_idx + 1; ?>: <?php echo $seg_city_from; ?> → <?php echo $seg_city_to; ?>
                                </div>
                                <div class="flight-date"><?php echo $seg_depart_date_formatted; ?></div>
                            </div>
                            <div class="flight-body">
                                <div class="flight-timeline">
                                    <div class="timeline-start">
                                        <div class="time"><?php echo $seg_depart_time_formatted; ?></div>
                                        <div class="location">
                                            <div class="airport-code"><?php echo $seg_depart_airport; ?></div>
                                            <div class="city"><?php echo $seg_city_from; ?></div>
                                        </div>
                                    </div>
                                    <div class="timeline-duration">
                                        <div class="duration-line"></div>
                                        <div class="duration-text"><?php echo $seg_duration; ?></div>
                                        <div class="stops-text">Direct</div> <!-- Each segment is direct -->
                                    </div>
                                    <div class="timeline-end">
                                        <div class="time"><?php echo $seg_arrival_time_formatted; ?></div>
                                        <div class="location">
                                            <div class="airport-code"><?php echo $seg_arrival_airport; ?></div>
                                            <div class="city"><?php echo $seg_city_to; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flight-info">
                                    <div class="carrier-logo">
                                        <?php if ($seg_op_airline_iata): ?>
                                            <img src="../assets/img/airlines/<?php echo $seg_op_airline_iata; ?>.png" alt="<?php echo $seg_op_airline_name; ?>" onerror="this.style.display='none'">
                                        <?php endif; ?>
                                    </div>
                                    <div class="carrier-details">
                                        <div class="carrier-name"><?php echo $seg_op_airline_name; ?></div>
                                        <div class="flight-number"><?php echo $seg_op_flight_no; ?></div>
                                    </div>
                                    <div class="cabin-class">
                                        <span class="label">Classe :</span>
                                        <span class="value">Economy</span> <!-- Assuming Economy, adjust if data available -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($s_idx < count($outbound_segments_list) - 1): ?>
                            <?php
                            $next_segment_data = $outbound_segments_list[$s_idx + 1];
                            $layover_arrival_raw = $segment_data['local_arrival'] ?? null;
                            $layover_departure_raw = $next_segment_data['local_departure'] ?? null;
                            $layover_duration = calculate_duration($layover_arrival_raw, $layover_departure_raw);
                            $layover_city = $segment_data['cityTo'] ?? $segment_data['dst_name'] ?? 'N/A';
                            ?>
                            <div class="connection-info-card" style="margin: 15px 0; padding: 15px; background-color: #fff8e1; border-left: 4px solid #ffc107; border-radius: 4px;">
                                 <h4 style="margin-top:0; margin-bottom: 5px;">escale à <?php echo $layover_city; ?></h4>
                                 <p style="margin-bottom:0;">Durée de la correspondance: <?php echo $layover_duration; ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun détail de vol aller disponible.</p>
                <?php endif; ?>
            </div>

            <!-- Return Flight -->
            <div class="flight-section">
                <h2 class="section-title">Vol retour</h2>
                 <?php if (!empty($return_segments_list)): ?>
                    <div class="flight-dates"><?php echo $overall_return_depart_date_formatted; ?></div>
                    <div class="leg-summary" style="margin-bottom: 15px; padding: 10px; background-color: #f0f8ff; border-radius: 5px;">
                        <strong>De:</strong> <?php echo $overall_return_city_from; ?> (<?php echo $overall_return_depart_airport; ?>)
                        <strong>À:</strong> <?php echo $overall_return_city_to; ?> (<?php echo $overall_return_arrival_airport; ?>)<br>
                        <strong>Durée totale du vol retour:</strong> <?php echo $overall_return_duration; ?> (<?php echo $return_stops_text; ?>)
                    </div>

                    <?php foreach ($return_segments_list as $s_idx => $segment_data): ?>
                        <?php
                        // Extract details for THIS segment_data
                        $seg_city_from = $segment_data['cityFrom'] ?? $segment_data['src_name'] ?? 'N/A';
                        $seg_city_to = $segment_data['cityTo'] ?? $segment_data['dst_name'] ?? 'N/A';
                        $seg_depart_airport = $segment_data['flyFrom'] ?? $segment_data['src_station'] ?? 'N/A';
                        $seg_arrival_airport = $segment_data['flyTo'] ?? $segment_data['dst_station'] ?? 'N/A';
                        
                        $seg_depart_datetime_raw = $segment_data['local_departure'] ?? null;
                        $seg_arrival_datetime_raw = $segment_data['local_arrival'] ?? null;

                        $seg_depart_date_formatted = 'N/A';
                        $seg_depart_time_formatted = 'N/A';
                        $seg_arrival_date_formatted = 'N/A';
                        $seg_arrival_time_formatted = 'N/A';

                        if ($seg_depart_datetime_raw) {
                            $dt_dep = new DateTime($seg_depart_datetime_raw);
                            $seg_depart_date_formatted = $dt_dep->format('D, d M Y');
                            $seg_depart_time_formatted = $dt_dep->format('H:i');
                        }
                        if ($seg_arrival_datetime_raw) {
                            $dt_arr = new DateTime($seg_arrival_datetime_raw);
                            $seg_arrival_date_formatted = $dt_arr->format('D, d M Y');
                            $seg_arrival_time_formatted = $dt_arr->format('H:i');
                        }
                        
                        $seg_duration = calculate_duration($seg_depart_datetime_raw, $seg_arrival_datetime_raw);
                        
                        $seg_airline_name_display = 'N/A';
                        if (isset($segment_data['airline']) && is_array($segment_data['airline']) && isset($segment_data['airline']['Name'])) {
                            $seg_airline_name_display = $segment_data['airline']['Name'];
                        } elseif (isset($segment_data['operating_airline']) && is_array($segment_data['operating_airline']) && isset($segment_data['operating_airline']['name'])) {
                            $seg_airline_name_display = $segment_data['operating_airline']['name'];
                        }
                        
                        $seg_op_airline_name = (isset($segment_data['operating_airline']) && is_array($segment_data['operating_airline']) && isset($segment_data['operating_airline']['name'])) ? $segment_data['operating_airline']['name'] : $seg_airline_name_display;
                        $seg_op_airline_iata = strtolower((isset($segment_data['operating_airline']) && is_array($segment_data['operating_airline']) && isset($segment_data['operating_airline']['iata'])) ? $segment_data['operating_airline']['iata'] : '');
                        $seg_op_flight_no = $segment_data['operating_flight_no'] ?? ($segment_data['flight_no'] ?? 'N/A');
                        ?>
                        <div class="flight-card detailed segment-card <?php echo ($s_idx > 0) ? 'connection-segment' : ''; ?> <?php echo ($s_idx == 0 && count($return_segments_list) > 0) ? 'return' : ''; // Add .return class for first return segment ?>" style="margin-top: 15px;">
                            <div class="flight-header">
                                <div class="flight-type">
                                    Segment <?php echo $s_idx + 1; ?>: <?php echo $seg_city_from; ?> → <?php echo $seg_city_to; ?>
                                </div>
                                <div class="flight-date"><?php echo $seg_depart_date_formatted; ?></div>
                            </div>
                            <div class="flight-body">
                                <div class="flight-timeline">
                                    <div class="timeline-start">
                                        <div class="time"><?php echo $seg_depart_time_formatted; ?></div>
                                        <div class="location">
                                            <div class="airport-code"><?php echo $seg_depart_airport; ?></div>
                                            <div class="city"><?php echo $seg_city_from; ?></div>
                                        </div>
                                    </div>
                                    <div class="timeline-duration">
                                        <div class="duration-line"></div>
                                        <div class="duration-text"><?php echo $seg_duration; ?></div>
                                        <div class="stops-text">Direct</div>
                                    </div>
                                    <div class="timeline-end">
                                        <div class="time"><?php echo $seg_arrival_time_formatted; ?></div>
                                        <div class="location">
                                            <div class="airport-code"><?php echo $seg_arrival_airport; ?></div>
                                            <div class="city"><?php echo $seg_city_to; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flight-info">
                                    <div class="carrier-logo">
                                        <?php if ($seg_op_airline_iata): ?>
                                            <img src="../assets/img/airlines/<?php echo $seg_op_airline_iata; ?>.png" alt="<?php echo $seg_op_airline_name; ?>" onerror="this.style.display='none'">
                                        <?php endif; ?>
                                    </div>
                                    <div class="carrier-details">
                                        <div class="carrier-name"><?php echo $seg_op_airline_name; ?></div>
                                        <div class="flight-number"><?php echo $seg_op_flight_no; ?></div>
                                    </div>
                                    <div class="cabin-class">
                                        <span class="label">Classe :</span>
                                        <span class="value">Economy</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($s_idx < count($return_segments_list) - 1): ?>
                            <?php
                            $next_segment_data = $return_segments_list[$s_idx + 1];
                            $layover_arrival_raw = $segment_data['local_arrival'] ?? null;
                            $layover_departure_raw = $next_segment_data['local_departure'] ?? null;
                            $layover_duration = calculate_duration($layover_arrival_raw, $layover_departure_raw);
                            $layover_city = $segment_data['cityTo'] ?? $segment_data['dst_name'] ?? 'N/A';
                            ?>
                            <div class="connection-info-card" style="margin: 15px 0; padding: 15px; background-color: #fff8e1; border-left: 4px solid #ffc107; border-radius: 4px;">
                                 <h4 style="margin-top:0; margin-bottom: 5px;">escale à <?php echo $layover_city; ?></h4>
                                 <p style="margin-bottom:0;">Durée de la correspondance: <?php echo $layover_duration; ?></p>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                     <div class="flight-dates">N/A</div>
                    <p>Aucun détail de vol retour disponible.</p>
                <?php endif; ?>
            </div>

            <!-- Baggage Options -->
            <?php if (isset($_SESSION['data']['baggage'])): // Check if baggage data exists in session data ?>
            <div class="flight-section">
                <h2 class="section-title">Options de bagages</h2>
                <div class="baggage-options">
                    <?php
                    // Ensure $data is available, it should be from earlier in the script
                    $data_for_baggage = $_SESSION['data']; 
                    $totalBagsAmount = 0;

                    if (!empty($data_for_baggage['baggage']['combinations'])) {
                        $definitions = $data_for_baggage['baggage']['definitions'];

                        // Define renderBagItems function locally if not already defined globally
                        if (!function_exists('renderBagItems_round')) { // Use a unique name to avoid conflicts if included elsewhere
                            function renderBagItems_round($bagType, $combinations, $definitions) {
                                if (!empty($combinations[$bagType])) {
                                    $title = $bagType === 'hand_bag' ? 'Bagages à main' : 'Bagages enregistrés';
                                    $icon = $bagType === 'hand_bag' ? 'cabinbag.webp' : 'holdbag.webp';

                                    echo "<div class='baggage-category'>";
                                    echo "<h3><img src='../assets/img/addon/{$icon}' alt='' class='baggage-icon'> {$title}</h3>";

                                    foreach ($combinations[$bagType] as $bag) {
                                        if (!empty($bag['indices'])) {
                                            foreach ($bag['indices'] as $index) {
                                                if (!isset($definitions[$bagType][$index])) {
                                                    continue; // Skip if definition is missing
                                                }
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
                                                    echo "<span class='base-price'>" . htmlspecialchars($price['base']) . " EUR</span>";
                                                    echo "<span class='service-price'>+ " . htmlspecialchars($price['service']) . " EUR frais</span>";
                                                    echo "</div>";
                                                    echo "<span class='total-price'>" . htmlspecialchars($price['amount']) . " EUR</span>";
                                                    echo "</div>";
                                                }
                                                echo "</div>";
                                                echo "</div>";
                                            }
                                        }
                                    }
                                    echo "</div>"; // Close baggage-category
                                }
                            }
                        }

                        renderBagItems_round('hand_bag', $data_for_baggage['baggage']['combinations'], $definitions);
                        renderBagItems_round('hold_bag', $data_for_baggage['baggage']['combinations'], $definitions);

                        $baggageJson = htmlspecialchars(json_encode($data_for_baggage['baggage']), ENT_QUOTES, 'UTF-8');
                        echo "<input type='hidden' name='baggage_json' value='{$baggageJson}'>";
                    } else {
                        echo "<p>Aucune option de bagage spécifique n\\'est disponible pour ce vol ou les informations de bagages n\\'ont pas été chargées.</p>";
                    }
                    ?>
                </div>
            </div>
            <?php else: ?>
                <div class="flight-section">
                    <h2 class="section-title">Options de bagages</h2>
                    <p>Les informations sur les options de bagages ne sont pas disponibles.</p>
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
