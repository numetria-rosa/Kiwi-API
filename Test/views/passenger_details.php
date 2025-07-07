<?php
$page_title = 'Détails des passagers';
$page_css = 'passenger-details';
$page_js = 'passenger-details';

session_start();

// Check if session data exists
if (!isset($_SESSION['data']) || !isset($_SESSION['booking_data'])) {
    header('Location: /Test/views/error.php');
    exit;
}

$data = $_SESSION['data'];
$bookingData = $_SESSION['booking_data'];

// Get passenger counts from session or default to 1 adult
$adults = $_SESSION['passenger_counts']['adults'] ?? 1;
$children = $_SESSION['passenger_counts']['children'] ?? 0;
$infants = $_SESSION['passenger_counts']['infants'] ?? 0;
$departure = $_SESSION['departure'] ?? '';
$destination = $_SESSION['destination'] ?? '';
// Get flight details for summary
$outbound_display_segments = [];
$route_display_string = 'N/A';
$depart_date_formatted = 'N/A';
$depart_time_formatted = 'N/A';

// Handle different data paths for one-way vs. round-trip
if (isset($_SESSION['flight_summary_for_passenger_page'])) {
    // Case 1: Coming from a round-trip, data is pre-processed and stored in session.
    $summary = $_SESSION['flight_summary_for_passenger_page'];
    $city_from = htmlspecialchars($summary['city_from'] ?? 'N/A');
    $fly_from = htmlspecialchars($summary['fly_from'] ?? 'N/A');
    $city_to = htmlspecialchars($summary['city_to'] ?? 'N/A');
    $fly_to = htmlspecialchars($summary['fly_to'] ?? 'N/A');
    
    $route_display_string = "{$city_from} ({$fly_from}) → {$city_to} ({$fly_to})";
    $depart_date_formatted = htmlspecialchars($summary['depart_date'] ?? 'N/A');
    $depart_time_formatted = htmlspecialchars($summary['depart_time'] ?? 'N/A');

    // Clean up the temporary session variable
    unset($_SESSION['flight_summary_for_passenger_page']);

} else if (isset($data['route']) && is_array($data['route']) && !empty($data['route'])) {
    // Case 2: Coming from a one-way flight, process data as before.
    $outbound_display_segments = $data['route'];
    
    // This logic now only runs for the one-way case.
    $first_seg = $outbound_display_segments[0];
    $city_from = htmlspecialchars($first_seg['cityFrom'] ?? $first_seg['src_name'] ?? 'N/A');
    $fly_from = htmlspecialchars($first_seg['flyFrom'] ?? $first_seg['src_station'] ?? 'N/A');
    
    $last_seg = end($outbound_display_segments);
    $city_to = htmlspecialchars($last_seg['cityTo'] ?? $last_seg['dst_name'] ?? 'N/A');
    $fly_to = htmlspecialchars($last_seg['flyTo'] ?? $last_seg['dst_station'] ?? 'N/A');

    $route_display_string = "{$city_from} ({$fly_from}) → {$city_to} ({$fly_to})";

    $depart_datetime_raw = $first_seg['local_departure'] ?? null;
    if ($depart_datetime_raw) {
        try {
            $dt = new DateTime($depart_datetime_raw);
            $depart_date_formatted = $dt->format('D, d M Y');
            $depart_time_formatted = $dt->format('H:i');
        } catch (Exception $e) { 
            $depart_date_formatted = 'Error';
            $depart_time_formatted = 'Error';
        }
    }
} else {
    // Fallback if no valid flight data structure is found
    $route_display_string = 'Route information unavailable.';
}

// Process baggage data
$baggageData = $data['baggage'] ?? [];
$formattedBaggage = [];

// Create array of passenger indices based on their order
$passengerIndices = [];
$currentIndex = 0;

// Add adult indices (0 to adults-1)
for ($i = 0; $i < $adults; $i++) {
    $passengerIndices[] = $currentIndex++;
}

// Add child indices (adults to adults+children-1)
for ($i = 0; $i < $children; $i++) {
    $passengerIndices[] = $currentIndex++;
}

// Add infant indices (adults+children to adults+children+infants-1)
for ($i = 0; $i < $infants; $i++) {
    $passengerIndices[] = $currentIndex++;
}

// Process hold baggage
if (!empty($baggageData['combinations']['hold_bag'])) {
    foreach ($baggageData['combinations']['hold_bag'] as $holdBag) {
        // Filter passengers based on conditions
        $eligiblePassengers = [];
        foreach ($passengerIndices as $index) {
            $passengerType = $index < $adults ? 'adult' : ($index < ($adults + $children) ? 'child' : 'infant');
            if (in_array($passengerType, $holdBag['conditions']['passenger_groups'])) {
                $eligiblePassengers[] = $index;
            }
        }
        
        if (!empty($eligiblePassengers)) {
            $formattedBaggage[] = [
                'combination' => [
                    'indices' => $holdBag['indices'],
                    'category' => 'hold_bag',
                    'conditions' => $holdBag['conditions'],
                    'price' => $holdBag['price']
                ],
                'passengers' => $eligiblePassengers
            ];
        }
    }
}

// Process hand baggage
if (!empty($baggageData['combinations']['hand_bag'])) {
    foreach ($baggageData['combinations']['hand_bag'] as $handBag) {
        // Filter passengers based on conditions
        $eligiblePassengers = [];
        foreach ($passengerIndices as $index) {
            $passengerType = $index < $adults ? 'adult' : ($index < ($adults + $children) ? 'child' : 'infant');
            if (in_array($passengerType, $handBag['conditions']['passenger_groups'])) {
                $eligiblePassengers[] = $index;
            }
        }
        
        if (!empty($eligiblePassengers)) {
            $formattedBaggage[] = [
                'combination' => [
                    'indices' => $handBag['indices'],
                    'category' => 'hand_bag',
                    'conditions' => $handBag['conditions'],
                    'price' => $handBag['price']
                ],
                'passengers' => $eligiblePassengers
            ];
        }
    }
}

// Store formatted baggage data in session for later use
$_SESSION['formatted_baggage'] = $formattedBaggage;

// Get baggage combinations for display
$baggageCombinations = $data['baggage']['combinations'] ?? [];
$baggageDefinitions = $data['baggage']['definitions'] ?? [];

include('../includes/header.php');
?>

<div class="passenger-details-container">
    <div class="container">
        <!-- Flight Summary -->
        <div class="flight-summary">
            <h2>Résumé du vol</h2>
            <div class="flight-info">
                <div class="route">
                    <span class="fromto"><?php echo $route_display_string; ?></span>
                </div>
                <div class="date">
                    <?php echo $depart_date_formatted; ?>
                </div>
                <div class="time">
                    <?php echo $depart_time_formatted; ?>
                </div>
            </div>
        </div>

        <!-- Baggage Selection -->
        <div class="baggage-selection">
            <h2>Options de bagages</h2>
            <div class="baggage-options">
                <?php
                // Function to render baggage options
                function renderBaggageOptions($type, $combinations, $definitions) {
                    if (empty($combinations[$type])) {
                        return;
                    }

                    $title = $type === 'hand_bag' ? 'Bagages à main' : 'Bagages enregistrés';
                    $icon = $type === 'hand_bag' ? 'cabinbag.webp' : 'holdbag.webp';
                    
                    echo "<div class='baggage-category'>";
                    echo "<h3><img src='../assets/img/addon/{$icon}' alt='' class='baggage-icon'> {$title}</h3>";
                    
                    foreach ($combinations[$type] as $bag) {
                        if (!empty($bag['indices'])) {
                            $bundleItems = [];
                            $totalPrice = 0;
                            $totalBasePrice = 0;
                            $totalServicePrice = 0;
                            
                            // First pass: collect all items in the bundle
                            foreach ($bag['indices'] as $index) {
                                $details = $definitions[$type][$index] ?? null;
                                if (!$details) continue;
                                
                                $restrictions = $details['restrictions'] ?? [];
                                $weight = $restrictions['weight'] ?? 'N/A';
                                $name = $details['name'] ?? ($type === 'hand_bag' ? 'Bagage à main' : 'Bagage enregistré');
                                
                                $bundleItems[] = "1× {$name} ({$weight} kg)";
                                
                                $price = $details['price'] ?? null;
                                if ($price) {
                                    $totalPrice += $price['amount'] ?? 0;
                                    $totalBasePrice += $price['base'] ?? 0;
                                    $totalServicePrice += $price['service'] ?? 0;
                                }
                            }
                            
                            // Display the bundle
                            if (!empty($bundleItems)) {
                                echo "<div class='baggage-item'>";
                                echo "<div class='baggage-info'>";
                                echo "<div class='baggage-dimensions'>";
                                echo "<span class='bundle-items'>" . implode(" + ", $bundleItems) . "</span>";
                                echo "</div>";
                                echo "</div>";
                                echo "<div class='baggage-price'>";
                                if ($totalPrice == 0) {
                                    echo "<span class='included'>Inclus</span>";
                                } else {
                                    echo "<div class='price-breakdown'>";
                                    echo "<div class='price-details'>";
                                    echo "<span class='base-price'>{$totalBasePrice} EUR</span>";
                                    echo "<span class='service-price'>+ {$totalServicePrice} EUR frais</span>";
                                    echo "</div>";
                                    echo "<span class='total-price'>{$totalPrice} EUR</span>";
                                    echo "</div>";
                                }
                                echo "</div>";
                                echo "</div>";
                            }
                        }
                    }
                    
                    echo "</div>";
                }

                renderBaggageOptions('hand_bag', $baggageCombinations, $baggageDefinitions);
                renderBaggageOptions('hold_bag', $baggageCombinations, $baggageDefinitions);
                ?>
            </div>
        </div>

        <!-- Passenger Details Form -->
        <form action="/Test_Case_Kiwi/Booking_Flow/Save_Booking.php" method="post" class="passenger-form" id="paymentForm">
            <input type="hidden" name="visitor_uniqid" value="<?php echo $bookingData['visitorUniqId']; ?>">
            <input type="hidden" name="session_id" value="<?php echo $bookingData['sessionId']; ?>">
            <input type="hidden" name="booking_token" value="<?php echo $bookingData['bookingToken']; ?>">
            <input type="hidden" name="health_declaration_checked" value="true">
            <input type="hidden" name="lang" value="en">
            <input type="hidden" name="locale" value="en">
            <input type="hidden" name="baggage" value='<?php echo json_encode($formattedBaggage); ?>'>

            <h2>Informations des passagers</h2>
            
            <?php
            // Function to render passenger form fields
            function renderPassengerFields($type, $count, $indexOffset = 0) {
                $typeLabels = [
                    'adult' => 'Adulte',
                    'child' => 'Enfant',
                    'infant' => 'Bébé'
                ];
                
                for ($i = 0; $i < $count; $i++) {
                    $index = $i + $indexOffset;
                    $passengerNumber = $i + 1;
                    echo "<div class='passenger-section'>";
                    echo "<h3>{$typeLabels[$type]} {$passengerNumber}</h3>";
                    
                    echo "<div class='form-group'>";
                    echo "<label>Civilité</label>";
                    echo "<select data-field='title' required>";
                    echo "<option value='mr'>Mr</option>";
                    echo "<option value='ms'>Ms</option>";
                    echo "</select>";
                    echo "</div>";

                    echo "<div class='form-group'>";
                    echo "<label>Prénom</label>";
                    echo "<input type='text' data-field='name' value='Test' required>";
                    echo "</div>";

                    echo "<div class='form-group'>";
                    echo "<label>Nom</label>";
                    echo "<input type='text' data-field='surname' value='Test' required>";
                    echo "</div>";

                    echo "<div class='form-group'>";
                    echo "<label>Date de naissance</label>";
                    echo "<input type='date' data-field='birthday' value='1999-05-09' required>";
                    echo "</div>";

                    echo "<div class='form-group'>";
                    echo "<label>Nationalité</label>";
                    echo "<input type='text' data-field='nationality' value='CZ' required>";
                    echo "</div>";

                    echo "<div class='form-group'>";
                    echo "<label>Email</label>";
                    echo "<input type='email' data-field='email' value='email.test@gmail.com' required>";
                    echo "</div>";

                    echo "<div class='form-group'>";
                    echo "<label>Téléphone</label>";
                    echo "<input type='tel' data-field='phone' value='+44857282842' required>";
                    echo "</div>";

                    echo "<div class='form-group'>";
                    echo "<label>Numéro de carte d'identité</label>";
                    echo "<input type='text' data-field='cardno' value='D25845822' required>";
                    echo "</div>";

                    echo "<div class='form-group'>";
                    echo "<label>Date d'expiration</label>";
                    echo "<input type='date' data-field='expiration' value='2026-12-12' required>";
                    echo "</div>";

                    echo "<input type='hidden' data-field='category' value='$type'>";
                    echo "</div>";
                }
            }

            $indexOffset = 0;
            renderPassengerFields('adult', $adults, $indexOffset);
            $indexOffset += $adults;
            renderPassengerFields('child', $children, $indexOffset);
            $indexOffset += $children;
            renderPassengerFields('infant', $infants, $indexOffset);
            $indexOffset += $infants;

            ?>

            <div class="form-submit">
                <button type="submit" class="btn btn-primary" id="submitBtn">Continuer vers le paiement</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('paymentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Traitement en cours...';

    try {
        // Step 0: Gather passenger data
        console.log('Step 0: Gather passenger data');
        const form = this;
        const formData = new FormData(form);
        const passengers = [];
        // Find all passenger sections
        const passengerSections = form.querySelectorAll('.passenger-section');
        passengerSections.forEach((section, idx) => {
            const passenger = {};
            section.querySelectorAll('[data-field]').forEach(input => {
                passenger[input.getAttribute('data-field')] = input.value;
            });
            passengers.push(passenger);
        });
        // Remove all passengers[] fields from FormData
        for (let pair of formData.keys()) {
            if (pair.startsWith('passengers[')) {
                formData.delete(pair);
            }
        }
        // Add passengers as JSON string
        formData.append('passengers', JSON.stringify(passengers));

        // Step 1: Save Booking with timeout handling
        console.log('Step 1: Save Booking');
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 25000); // 25 second timeout

        try {
            const saveBookingResponse = await fetch('/Test_Case_Kiwi/Booking_Flow/Save_Booking.php', {
                method: 'POST',
                body: formData,
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            
            if (!saveBookingResponse.ok) {
                throw new Error(`HTTP error! status: ${saveBookingResponse.status}`);
            }
            
            const saveBookingData = await saveBookingResponse.json();
            console.log('Save Booking Response:', saveBookingData);
            
            if (saveBookingData.error) {
                throw new Error('Save Booking failed: ' + (saveBookingData.message || 'Unknown error'));
            }
            
            if (!saveBookingData.booking_id || !saveBookingData.payu_token) {
                console.error('Save Booking Response Structure:', saveBookingData.raw_response);
                throw new Error('Invalid Save Booking response: missing booking_id or payu_token');
            }

            // Step 2: Tokenize Data
            console.log('Step 2: Tokenize Data');
            const tokenizeData = {
                booking_id: saveBookingData.booking_id,
                payu_token: saveBookingData.payu_token,
                visitor_uniqid: '<?php echo $bookingData['visitorUniqId']; ?>'
            };
            console.log('Sending to Tokenize:', tokenizeData);
            
            const tokenizeResponse = await fetch('/Test_Case_Kiwi/Booking_Flow/Tokenize_Data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(tokenizeData)
            });
            
            const tokenizeResult = await tokenizeResponse.json();
            console.log('Tokenize Response:', tokenizeResult);
            
            if (tokenizeResult.error) {
                throw new Error('Tokenize failed: ' + (tokenizeResult.message || 'Unknown error'));
            }
            
            if (!tokenizeResult.response || !tokenizeResult.response.token) {
                console.error('Tokenize Response Structure:', tokenizeResult);
                throw new Error('Invalid Tokenize response: missing token');
            }

            // Step 3: Confirm Payment Zooz
            console.log('Step 3: Confirm Payment Zooz');
            const confirmPaymentResponse = await fetch('/Test_Case_Kiwi/Booking_Flow/confirm_payment_zooz.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    booking_id: saveBookingData.booking_id,
                    paymentToken: saveBookingData.payu_token,
                    paymentMethodToken: tokenizeResult.response.token,
                    tokenizeData: tokenizeResult
                })
            });
            
            const confirmPaymentData = await confirmPaymentResponse.json();
            console.log('Confirm Payment Response:', confirmPaymentData);
            
            if (confirmPaymentData.error) {
                throw new Error('Payment confirmation failed: ' + (confirmPaymentData.message || 'Unknown error'));
            }
            
            if (confirmPaymentData.status === 0) {
                // Payment successful
                window.location.href = '/Test_Case_Kiwi/Booking_Flow/confirm_payment_zooz.php?status=success';
            } else {
                throw new Error('Payment confirmation failed: ' + (confirmPaymentData.msg || 'Unknown error'));
            }
        } catch (error) {
            if (error.name === 'AbortError') {
                throw new Error('Request timed out. Please try again.');
            }
            throw error;
        }
    } catch (error) {
        console.error('Error:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack
        });
        
        // Show a more user-friendly error message
        let errorMessage = 'Une erreur est survenue lors du traitement du paiement. ';
        if (error.message.includes('timed out')) {
            errorMessage += 'Le serveur met trop de temps à répondre. Veuillez réessayer dans quelques instants.';
        } else {
            errorMessage += error.message;
        }
        
        alert(errorMessage);
        submitBtn.disabled = false;
        submitBtn.textContent = 'Continuer vers le paiement';
    }
});
</script>

<style>
:root {
    --primary-color: #00a991;
    --primary-hover: #009882;
    --text-primary: #2d3748;
    --text-secondary: #4a5568;
    --text-muted: #718096;
    --border-color: #e2e8f0;
    --bg-light: #f8fafc;
    --bg-white: #ffffff;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
    --radius-sm: 8px;
    --radius-md: 12px;
    --spacing-xs: 8px;
    --spacing-sm: 16px;
    --spacing-md: 24px;
    --spacing-lg: 32px;
}

.passenger-details-container {
    padding: var(--spacing-lg) 0;
    background: var(--bg-light);
    min-height: 100vh;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--spacing-md);
}

/* Flight Summary Section */
.flight-summary {
    background: var(--bg-white);
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
}

.flight-summary h2 {
    color: var(--text-primary);
    font-size: 1.5rem;
    margin-bottom: var(--spacing-md);
    font-weight: 600;
}

.flight-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-md);
    align-items: center;
}

.route {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    font-size: 1.25rem;
    font-weight: 500;
}

.arrow {
    color: var(--primary-color);
    font-weight: bold;
}

.date, .time {
    color: var(--text-secondary);
    font-size: 1.1rem;
}

/* Baggage Selection Section */
.baggage-selection {
    background: var(--bg-white);
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    margin-bottom: var(--spacing-md);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
}

.baggage-selection h2 {
    color: var(--text-primary);
    font-size: 1.5rem;
    margin-bottom: var(--spacing-md);
    font-weight: 600;
}

.baggage-options {
    display: grid;
    gap: var(--spacing-md);
}

.baggage-category {
    background: var(--bg-light);
    border-radius: var(--radius-sm);
    padding: var(--spacing-md);
    border: 1px solid var(--border-color);
}

.baggage-category h3 {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--spacing-md);
}

.baggage-icon {
    width: 90px;
    height: 120px;
    object-fit: contain;
}

.baggage-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: var(--spacing-sm) 0;
    border-bottom: 1px solid var(--border-color);
}

.baggage-item:last-child {
    border-bottom: none;
}

.baggage-dimensions {
    font-size: 0.95rem;
    color: var(--text-secondary);
}

.bundle-items {
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
}

.baggage-price {
    text-align: right;
}

.baggage-price .included {
    color: var(--primary-color);
    font-weight: 600;
}

.price-breakdown {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: var(--spacing-xs);
}

.base-price {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.service-price {
    color: var(--text-muted);
    font-size: 0.85rem;
}

.total-price {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 1.1rem;
}

/* Passenger Form Section */
.passenger-form {
    background: var(--bg-white);
    border-radius: var(--radius-md);
    padding: var(--spacing-md);
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border-color);
}

.passenger-form h2 {
    color: var(--text-primary);
    font-size: 1.5rem;
    margin-bottom: var(--spacing-md);
    font-weight: 600;
}

.passenger-section {
    margin-bottom: var(--spacing-lg);
    padding-bottom: var(--spacing-lg);
    border-bottom: 1px solid var(--border-color);
}

.passenger-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.passenger-section h3 {
    color: var(--text-primary);
    font-size: 1.25rem;
    margin-bottom: var(--spacing-md);
    font-weight: 600;
}

.form-group {
    margin-bottom: var(--spacing-md);
}

.form-group label {
    display: block;
    margin-bottom: var(--spacing-xs);
    font-weight: 500;
    color: var(--text-secondary);
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    font-size: 1rem;
    transition: all 0.2s ease;
    background: var(--bg-white);
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(0, 169, 145, 0.1);
}

.form-submit {
    margin-top: var(--spacing-lg);
    text-align: center;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    padding: 16px 32px;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 200px;
}

.btn-primary:hover {
    background: var(--primary-hover);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 0 var(--spacing-sm);
    }
    
    .flight-info {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .route {
        justify-content: center;
    }
    
    .baggage-item {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-sm);
    }
    
    .baggage-price {
        width: 100%;
    }
    
    .price-breakdown {
        align-items: flex-start;
    }
    
    .btn-primary {
        width: 100%;
    }
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.flight-summary,
.baggage-selection,
.passenger-form {
    animation: fadeIn 0.3s ease-out;
}
</style>

<?php include('../includes/footer.php'); ?> 