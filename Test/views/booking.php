<?php
// Définir le titre et les styles spécifiques à la page
$page_title = 'Réservation de vol';
$page_css = 'booking';
$page_js = 'booking';

// Récupérer l'ID du vol depuis l'URL
$flight_id = isset($_GET['flight_id']) ? sanitize_input($_GET['flight_id']) : '';



// Pour cet exemple, nous allons simuler un vol
// Dans une implémentation réelle, nous le récupérerions depuis l'API Tequila
$flight = [
    'id' => $flight_id,
    'price' => 150.50,
    'currency' => 'EUR',
    'departure_airport' => 'CDG',
    'departure_city' => 'Paris',
    'arrival_airport' => 'LHR',
    'arrival_city' => 'Londres',
    'departure_time' => '08:45',
    'arrival_time' => '09:40',
    'duration' => '01:55',
    'carrier' => 'British Airways',
    'carrier_code' => 'BA',
    'flight_number' => 'BA303',
    'stops' => 0,
    'outbound_date' => '2023-06-15',
    'return_date' => '2023-06-22',
    'return_departure_time' => '18:30',
    'return_arrival_time' => '19:25',
    'return_duration' => '01:55',
    'return_carrier' => 'Air France',
    'return_carrier_code' => 'AF',
    'return_flight_number' => 'AF1680',
    'aircraft_type' => 'Airbus A320',
    'cabin_class' => 'economy',
    'is_roundtrip' => true,
    'baggage_options' => [
        [
            'id' => 'cabin',
            'type' => 'cabin',
            'weight' => '8kg',
            'price' => 0.00,
            'included' => true
        ],
        [
            'id' => 'checked_23',
            'type' => 'checked',
            'weight' => '23kg',
            'price' => 30.00,
            'included' => false
        ],
        [
            'id' => 'checked_32',
            'type' => 'checked',
            'weight' => '32kg',
            'price' => 60.00,
            'included' => false
        ]
    ],
    'seat_selection' => [
        'available' => true,
        'price_standard' => 10.00,
        'price_extra_legroom' => 25.00,
        'price_premium' => 35.00
    ]
];

// Vérifier si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['user_id']);
$user = [];

if ($is_logged_in) {
    // Récupérer les informations de l'utilisateur
    $userModel = new User($pdo);
    $user = $userModel->getById($_SESSION['user_id']);
}

// Traiter le formulaire
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_submit'])) {
    // Dans une implémentation réelle, on traiterait le formulaire ici
    // Pour l'exemple, on simule une réservation réussie

    // Rediriger vers la page de paiement
    redirect('payment', ['booking_id' => 'DEMO-' . rand(10000, 99999)]);
}

// Inclure l'en-tête
include('../includes/header.php');  // Correct relative path to header.php
include('../config/database.php');
?>

<div class="booking-container">
    <div class="container">
        <!-- Étapes de réservation -->
        <div class="booking-steps">
            <div class="step active">
                <span class="step-number">1</span>
                <span class="step-name">Informations de vol</span>
            </div>
            <div class="step">
                <span class="step-number">2</span>
                <span class="step-name">Paiement</span>
            </div>
            <div class="step">
                <span class="step-number">3</span>
                <span class="step-name">Confirmation</span>
            </div>
        </div>

        <div class="booking-content">
            <!-- Résumé du vol et prix (barre latérale) -->
            <div class="flight-summary">
                <h2 class="summary-title">Résumé de votre vol</h2>

                <!-- Vol aller -->
                <div class="summary-flight">
                    <div class="summary-flight-header">
                        <div class="summary-flight-type">Vol aller</div>
                        <div class="summary-flight-date"><?php echo date('d M Y', strtotime($flight['outbound_date'])); ?></div>
                    </div>
                    <div class="summary-flight-details">
                        <div class="summary-airline">
                            <img src="assets/img/airlines/<?php echo strtolower($flight['carrier_code']); ?>.png" alt="<?php echo $flight['carrier']; ?>" class="airline-logo">
                            <span class="airline-name"><?php echo $flight['carrier']; ?> <?php echo $flight['flight_number']; ?></span>
                        </div>
                        <div class="summary-route">
                            <div class="summary-departure">
                                <div class="summary-time"><?php echo $flight['departure_time']; ?></div>
                                <div class="summary-location"><?php echo $flight['departure_airport']; ?> - <?php echo $flight['departure_city']; ?></div>
                            </div>
                            <div class="summary-duration">
                                <div class="duration-line"></div>
                                <div class="duration-text"><?php echo $flight['duration']; ?></div>
                            </div>
                            <div class="summary-arrival">
                                <div class="summary-time"><?php echo $flight['arrival_time']; ?></div>
                                <div class="summary-location"><?php echo $flight['arrival_airport']; ?> - <?php echo $flight['arrival_city']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($flight['is_roundtrip']): ?>
                <!-- Vol retour -->
                <div class="summary-flight">
                    <div class="summary-flight-header">
                        <div class="summary-flight-type">Vol retour</div>
                        <div class="summary-flight-date"><?php echo date('d M Y', strtotime($flight['return_date'])); ?></div>
                    </div>
                    <div class="summary-flight-details">
                        <div class="summary-airline">
                            <img src="assets/img/airlines/<?php echo strtolower($flight['return_carrier_code']); ?>.png" alt="<?php echo $flight['return_carrier']; ?>" class="airline-logo">
                            <span class="airline-name"><?php echo $flight['return_carrier']; ?> <?php echo $flight['return_flight_number']; ?></span>
                        </div>
                        <div class="summary-route">
                            <div class="summary-departure">
                                <div class="summary-time"><?php echo $flight['return_departure_time']; ?></div>
                                <div class="summary-location"><?php echo $flight['arrival_airport']; ?> - <?php echo $flight['arrival_city']; ?></div>
                            </div>
                            <div class="summary-duration">
                                <div class="duration-line"></div>
                                <div class="duration-text"><?php echo $flight['return_duration']; ?></div>
                            </div>
                            <div class="summary-arrival">
                                <div class="summary-time"><?php echo $flight['return_arrival_time']; ?></div>
                                <div class="summary-location"><?php echo $flight['departure_airport']; ?> - <?php echo $flight['departure_city']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Prix -->
                <div class="summary-price">
                    <h3 class="summary-price-title">Prix total</h3>
                    <div class="price-breakdown">
                        <div class="price-row">
                            <div class="price-label">Vol(s)</div>
                            <div class="price-value"><?php echo format_price($flight['price'], $flight['currency']); ?></div>
                        </div>
                        <div class="price-row baggage-price">
                            <div class="price-label">Bagages</div>
                            <div class="price-value" id="baggage-price">0,00 €</div>
                        </div>
                        <div class="price-row seat-price">
                            <div class="price-label">Sièges</div>
                            <div class="price-value" id="seat-price">0,00 €</div>
                        </div>
                        <div class="price-row total">
                            <div class="price-label">Total</div>
                            <div class="price-value" id="total-price"><?php echo format_price($flight['price'], $flight['currency']); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire de réservation -->
            <div class="booking-form-container">
                <h1 class="booking-title">Réservation de votre vol</h1>

                <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
                <?php endif; ?>

                <form action="<?php echo generate_url('booking', ['flight_id' => $flight_id]); ?>" method="POST" class="booking-form">
                    <!-- Section Passagers -->
                    <div class="booking-section">
                        <h2 class="section-title">Informations des passagers</h2>

                        <div class="passenger-container" id="passenger-1">
                            <h3 class="passenger-title">Passager 1 (Contact principal)</h3>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="passenger_1_first_name">Prénom</label>
                                    <input type="text" id="passenger_1_first_name" name="passengers[1][first_name]" required value="<?php echo $is_logged_in ? $user['first_name'] : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label for="passenger_1_last_name">Nom</label>
                                    <input type="text" id="passenger_1_last_name" name="passengers[1][last_name]" required value="<?php echo $is_logged_in ? $user['last_name'] : ''; ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="passenger_1_dob">Date de naissance</label>
                                    <input type="date" id="passenger_1_dob" name="passengers[1][date_of_birth]" required max="<?php echo date('Y-m-d', strtotime('-1 day')); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="passenger_1_nationality">Nationalité</label>
                                    <select id="passenger_1_nationality" name="passengers[1][nationality]" required>
                                        <option value="">Sélectionnez une nationalité</option>
                                        <option value="FR">France</option>
                                        <option value="BE">Belgique</option>
                                        <option value="CH">Suisse</option>
                                        <option value="CA">Canada</option>
                                        <!-- Ajouter d'autres pays au besoin -->
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="passenger_1_passport">Numéro de passeport/CNI</label>
                                    <input type="text" id="passenger_1_passport" name="passengers[1][passport_number]" required>
                                </div>
                                <div class="form-group">
                                    <label for="passenger_1_passport_expiry">Date d'expiration</label>
                                    <input type="date" id="passenger_1_passport_expiry" name="passengers[1][passport_expiry]" required min="<?php echo date('Y-m-d', strtotime('+6 months')); ?>">
                                    <div class="form-hint">Doit être valide au moins 6 mois après la date de retour</div>
                                </div>
                            </div>

                            <?php if (!$is_logged_in): ?>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="passenger_1_email">Email</label>
                                    <input type="email" id="passenger_1_email" name="passengers[1][email]" required>
                                </div>
                                <div class="form-group">
                                    <label for="passenger_1_phone">Téléphone</label>
                                    <input type="tel" id="passenger_1_phone" name="passengers[1][phone]" required>
                                </div>
                            </div>
                            <?php else: ?>
                            <input type="hidden" name="passengers[1][email]" value="<?php echo $user['email']; ?>">
                            <input type="hidden" name="passengers[1][phone]" value="<?php echo $user['phone']; ?>">
                            <?php endif; ?>

                            <!-- Options de bagage -->
                            <div class="baggage-options">
                                <h4 class="options-subtitle">Options de bagage</h4>

                                <div class="options-grid">
                                    <?php foreach ($flight['baggage_options'] as $baggage): ?>
                                    <div class="option-card" data-price="<?php echo $baggage['price']; ?>">
                                        <input type="radio" id="passenger_1_baggage_<?php echo $baggage['id']; ?>" name="passengers[1][baggage]" value="<?php echo $baggage['id']; ?>" <?php echo $baggage['included'] ? 'checked' : ''; ?> class="baggage-option">
                                        <label for="passenger_1_baggage_<?php echo $baggage['id']; ?>" class="option-label">
                                            <div class="option-header">
                                                <span class="option-icon <?php echo $baggage['type']; ?>"></span>
                                                <span class="option-name">
                                                    <?php if ($baggage['type'] === 'cabin'): ?>
                                                    Bagage à main
                                                    <?php else: ?>
                                                    Bagage en soute
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                            <div class="option-details">
                                                <span class="option-weight"><?php echo $baggage['weight']; ?></span>
                                                <span class="option-price">
                                                    <?php if ($baggage['included']): ?>
                                                    Inclus
                                                    <?php else: ?>
                                                    +<?php echo format_price($baggage['price'], $flight['currency']); ?>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Sélection de siège -->
                            <?php if ($flight['seat_selection']['available']): ?>
                            <div class="seat-selection">
                                <h4 class="options-subtitle">Sélection de siège</h4>

                                <div class="options-grid">
                                    <div class="option-card" data-price="0">
                                        <input type="radio" id="passenger_1_seat_auto" name="passengers[1][seat]" value="auto" checked class="seat-option">
                                        <label for="passenger_1_seat_auto" class="option-label">
                                            <div class="option-header">
                                                <span class="option-icon auto"></span>
                                                <span class="option-name">Attribution automatique</span>
                                            </div>
                                            <div class="option-details">
                                                <span class="option-description">Le siège vous sera attribué automatiquement lors de l'enregistrement</span>
                                                <span class="option-price">Gratuit</span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="option-card" data-price="<?php echo $flight['seat_selection']['price_standard']; ?>">
                                        <input type="radio" id="passenger_1_seat_standard" name="passengers[1][seat]" value="standard" class="seat-option">
                                        <label for="passenger_1_seat_standard" class="option-label">
                                            <div class="option-header">
                                                <span class="option-icon standard"></span>
                                                <span class="option-name">Siège standard</span>
                                            </div>
                                            <div class="option-details">
                                                <span class="option-description">Choisissez votre siège standard</span>
                                                <span class="option-price">+<?php echo format_price($flight['seat_selection']['price_standard'], $flight['currency']); ?></span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="option-card" data-price="<?php echo $flight['seat_selection']['price_extra_legroom']; ?>">
                                        <input type="radio" id="passenger_1_seat_legroom" name="passengers[1][seat]" value="legroom" class="seat-option">
                                        <label for="passenger_1_seat_legroom" class="option-label">
                                            <div class="option-header">
                                                <span class="option-icon legroom"></span>
                                                <span class="option-name">Plus d'espace pour les jambes</span>
                                            </div>
                                            <div class="option-details">
                                                <span class="option-description">Siège avec espace supplémentaire pour les jambes</span>
                                                <span class="option-price">+<?php echo format_price($flight['seat_selection']['price_extra_legroom'], $flight['currency']); ?></span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="option-card" data-price="<?php echo $flight['seat_selection']['price_premium']; ?>">
                                        <input type="radio" id="passenger_1_seat_premium" name="passengers[1][seat]" value="premium" class="seat-option">
                                        <label for="passenger_1_seat_premium" class="option-label">
                                            <div class="option-header">
                                                <span class="option-icon premium"></span>
                                                <span class="option-name">Siège premium</span>
                                            </div>
                                            <div class="option-details">
                                                <span class="option-description">Siège premium en avant de la cabine</span>
                                                <span class="option-price">+<?php echo format_price($flight['seat_selection']['price_premium'], $flight['currency']); ?></span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Bouton pour ajouter un passager -->
                        <div class="add-passenger">
                            <button type="button" class="btn btn-secondary btn-add-passenger">
                                <span class="btn-icon">+</span>
                                Ajouter un passager
                            </button>
                        </div>
                    </div>

                    <!-- Section Paiement -->
                    <div class="booking-section">
                        <h2 class="section-title">Options de paiement</h2>

                        <div class="payment-methods">
                            <div class="payment-method">
                                <input type="radio" id="payment_card" name="payment_method" value="card" checked>
                                <label for="payment_card" class="payment-label">
                                    <span class="payment-icon card"></span>
                                    <span class="payment-name">Carte de crédit/débit</span>
                                </label>
                            </div>

                            <div class="payment-method">
                                <input type="radio" id="payment_paypal" name="payment_method" value="paypal">
                                <label for="payment_paypal" class="payment-label">
                                    <span class="payment-icon paypal"></span>
                                    <span class="payment-name">PayPal</span>
                                </label>
                            </div>

                            <div class="payment-method">
                                <input type="radio" id="payment_apple" name="payment_method" value="apple_pay">
                                <label for="payment_apple" class="payment-label">
                                    <span class="payment-icon apple"></span>
                                    <span class="payment-name">Apple Pay</span>
                                </label>
                            </div>
                        </div>

                        <div class="payment-details" id="card-details">
                            <p class="payment-info">Les détails de paiement seront collectés à l'étape suivante.</p>
                        </div>
                    </div>

                    <!-- Conditions et soumettre -->
                    <div class="booking-section">
                        <div class="terms-agreement">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">J'accepte les <a href="#" target="_blank">conditions générales de vente</a> et la <a href="#" target="_blank">politique de confidentialité</a>.</label>
                        </div>

                        <div class="booking-submit">
                            <input type="hidden" name="booking_submit" value="1">
                            <button type="submit" class="btn btn-primary btn-lg">Procéder au paiement</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le pied de page
include 'includes/footer.php';
?>
