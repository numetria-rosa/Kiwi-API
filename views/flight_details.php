<?php
// Définir le titre et les styles spécifiques à la page
$page_title = 'Détails du vol';
$page_css = 'flight-details';
$page_js = 'flight-details';

// Récupérer l'ID du vol depuis l'URL
$flight_id = isset($_GET['id']) ? sanitize_input($_GET['id']) : '';

// Si aucun ID n'est fourni, rediriger vers la page d'accueil
if (empty($flight_id)) {
    redirect('home');
}

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
    'booking_token' => 'abcdefghijklmnopqrstuvwxyz123456',
    'baggage_options' => [
        [
            'type' => 'cabin',
            'weight' => '8kg',
            'price' => 0.00,
            'included' => true
        ],
        [
            'type' => 'checked',
            'weight' => '23kg',
            'price' => 30.00,
            'included' => false
        ],
        [
            'type' => 'checked',
            'weight' => '32kg',
            'price' => 60.00,
            'included' => false
        ]
    ],
    'seat_selection' => [
        'available' => true,
        'price_from' => 10.00
    ],
    'refundable' => false,
    'changes_allowed' => true,
    'change_fee' => 50.00
];

// Inclure l'en-tête
include 'includes/header.php';
?>

<div class="flight-details-container">
    <div class="container">
        <!-- Résumé du prix et bouton de réservation (barre latérale mobile en mode fixe) -->
        <div class="flight-booking-summary">
            <div class="booking-summary-content">
                <div class="price-summary">
                    <div class="total-price"><?php echo format_price($flight['price'], $flight['currency']); ?></div>
                    <div class="price-details">Prix total, tous frais inclus</div>
                </div>
                <a href="<?php echo generate_url('booking', ['flight_id' => $flight['id']]); ?>" class="btn btn-primary btn-book">Réserver ce vol</a>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="flight-details-content">
            <!-- Fil d'Ariane -->
            <div class="breadcrumbs">
                <a href="<?php echo generate_url('home'); ?>">Accueil</a>
                <span class="separator">&gt;</span>
                <a href="<?php echo generate_url('search', ['departure' => $flight['departure_city'], 'destination' => $flight['arrival_city']]); ?>">Résultats de recherche</a>
                <span class="separator">&gt;</span>
                <span class="current">Détails du vol</span>
            </div>

            <!-- Titre de la page -->
            <h1 class="page-title"><?php echo $flight['departure_city']; ?> &rarr; <?php echo $flight['arrival_city']; ?></h1>

            <?php if ($flight['is_roundtrip']): ?>
            <div class="flight-dates">
                <?php echo date('d M Y', strtotime($flight['outbound_date'])); ?> - <?php echo date('d M Y', strtotime($flight['return_date'])); ?>
            </div>
            <?php else: ?>
            <div class="flight-dates">
                <?php echo date('d M Y', strtotime($flight['outbound_date'])); ?>
            </div>
            <?php endif; ?>

            <!-- Détails des vols -->
            <div class="flight-section">
                <h2 class="section-title">Détails de votre vol</h2>

                <!-- Vol aller -->
                <div class="flight-card detailed">
                    <div class="flight-header">
                        <div class="flight-type">Vol aller</div>
                        <div class="flight-date"><?php echo date('d M Y', strtotime($flight['outbound_date'])); ?></div>
                    </div>
                    <div class="flight-body">
                        <div class="flight-timeline">
                            <div class="timeline-start">
                                <div class="time"><?php echo $flight['departure_time']; ?></div>
                                <div class="location">
                                    <div class="airport-code"><?php echo $flight['departure_airport']; ?></div>
                                    <div class="city"><?php echo $flight['departure_city']; ?></div>
                                </div>
                            </div>
                            <div class="timeline-duration">
                                <div class="duration-line"></div>
                                <div class="duration-text"><?php echo $flight['duration']; ?></div>
                                <?php if ($flight['stops'] > 0): ?>
                                <div class="stops-text"><?php echo $flight['stops']; ?> escale<?php echo $flight['stops'] > 1 ? 's' : ''; ?></div>
                                <?php else: ?>
                                <div class="stops-text">Direct</div>
                                <?php endif; ?>
                            </div>
                            <div class="timeline-end">
                                <div class="time"><?php echo $flight['arrival_time']; ?></div>
                                <div class="location">
                                    <div class="airport-code"><?php echo $flight['arrival_airport']; ?></div>
                                    <div class="city"><?php echo $flight['arrival_city']; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="flight-info">
                            <div class="carrier-logo">
                                <img src="assets/img/airlines/<?php echo strtolower($flight['carrier_code']); ?>.png" alt="<?php echo $flight['carrier']; ?>">
                            </div>
                            <div class="carrier-details">
                                <div class="carrier-name"><?php echo $flight['carrier']; ?></div>
                                <div class="flight-number"><?php echo $flight['flight_number']; ?></div>
                                <div class="aircraft-type"><?php echo $flight['aircraft_type']; ?></div>
                            </div>
                            <div class="cabin-class">
                                <span class="label">Classe :</span>
                                <span class="value"><?php echo ucfirst($flight['cabin_class']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($flight['is_roundtrip']): ?>
                <!-- Vol retour -->
                <div class="flight-card detailed">
                    <div class="flight-header">
                        <div class="flight-type">Vol retour</div>
                        <div class="flight-date"><?php echo date('d M Y', strtotime($flight['return_date'])); ?></div>
                    </div>
                    <div class="flight-body">
                        <div class="flight-timeline">
                            <div class="timeline-start">
                                <div class="time"><?php echo $flight['return_departure_time']; ?></div>
                                <div class="location">
                                    <div class="airport-code"><?php echo $flight['arrival_airport']; ?></div>
                                    <div class="city"><?php echo $flight['arrival_city']; ?></div>
                                </div>
                            </div>
                            <div class="timeline-duration">
                                <div class="duration-line"></div>
                                <div class="duration-text"><?php echo $flight['return_duration']; ?></div>
                                <div class="stops-text">Direct</div>
                            </div>
                            <div class="timeline-end">
                                <div class="time"><?php echo $flight['return_arrival_time']; ?></div>
                                <div class="location">
                                    <div class="airport-code"><?php echo $flight['departure_airport']; ?></div>
                                    <div class="city"><?php echo $flight['departure_city']; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="flight-info">
                            <div class="carrier-logo">
                                <img src="assets/img/airlines/<?php echo strtolower($flight['return_carrier_code']); ?>.png" alt="<?php echo $flight['return_carrier']; ?>">
                            </div>
                            <div class="carrier-details">
                                <div class="carrier-name"><?php echo $flight['return_carrier']; ?></div>
                                <div class="flight-number"><?php echo $flight['return_flight_number']; ?></div>
                                <div class="aircraft-type"><?php echo $flight['aircraft_type']; ?></div>
                            </div>
                            <div class="cabin-class">
                                <span class="label">Classe :</span>
                                <span class="value"><?php echo ucfirst($flight['cabin_class']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Options de bagages -->
            <div class="flight-section">
                <h2 class="section-title">Options de bagages</h2>
                <div class="baggage-options">
                    <?php foreach ($flight['baggage_options'] as $baggage): ?>
                    <div class="baggage-option">
                        <div class="baggage-type">
                            <?php if ($baggage['type'] === 'cabin'): ?>
                            <span class="baggage-icon cabin"></span>
                            <span class="baggage-name">Bagage à main</span>
                            <?php else: ?>
                            <span class="baggage-icon checked"></span>
                            <span class="baggage-name">Bagage en soute</span>
                            <?php endif; ?>
                        </div>
                        <div class="baggage-info">
                            <div class="weight"><?php echo $baggage['weight']; ?></div>
                            <?php if ($baggage['included']): ?>
                            <div class="price included">Inclus</div>
                            <?php else: ?>
                            <div class="price"><?php echo format_price($baggage['price'], $flight['currency']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Conditions tarifaires -->
            <div class="flight-section">
                <h2 class="section-title">Conditions tarifaires</h2>
                <div class="fare-conditions">
                    <div class="fare-condition">
                        <div class="condition-icon refund <?php echo $flight['refundable'] ? 'allowed' : 'not-allowed'; ?>"></div>
                        <div class="condition-details">
                            <div class="condition-name">Remboursement</div>
                            <div class="condition-value">
                                <?php if ($flight['refundable']): ?>
                                Possible avec frais de <?php echo format_price($flight['refund_fee'], $flight['currency']); ?>
                                <?php else: ?>
                                Non remboursable
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="fare-condition">
                        <div class="condition-icon change <?php echo $flight['changes_allowed'] ? 'allowed' : 'not-allowed'; ?>"></div>
                        <div class="condition-details">
                            <div class="condition-name">Modifications</div>
                            <div class="condition-value">
                                <?php if ($flight['changes_allowed']): ?>
                                Possibles avec frais de <?php echo format_price($flight['change_fee'], $flight['currency']); ?>
                                <?php else: ?>
                                Non modifiable
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="fare-condition">
                        <div class="condition-icon seat allowed"></div>
                        <div class="condition-details">
                            <div class="condition-name">Sélection de siège</div>
                            <div class="condition-value">
                                <?php if ($flight['seat_selection']['available']): ?>
                                Disponible à partir de <?php echo format_price($flight['seat_selection']['price_from'], $flight['currency']); ?>
                                <?php else: ?>
                                Non disponible
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prix détaillé -->
            <div class="flight-section">
                <h2 class="section-title">Détail du prix</h2>
                <div class="price-breakdown">
                    <div class="price-item">
                        <div class="price-label">Tarif de base</div>
                        <div class="price-value"><?php echo format_price($flight['price'] * 0.85, $flight['currency']); ?></div>
                    </div>
                    <div class="price-item">
                        <div class="price-label">Taxes et frais</div>
                        <div class="price-value"><?php echo format_price($flight['price'] * 0.15, $flight['currency']); ?></div>
                    </div>
                    <div class="price-item total">
                        <div class="price-label">Prix total</div>
                        <div class="price-value"><?php echo format_price($flight['price'], $flight['currency']); ?></div>
                    </div>
                </div>
            </div>

            <!-- Bouton de réservation -->
            <div class="booking-button-container">
                <a href="<?php echo generate_url('booking', ['flight_id' => $flight['id']]); ?>" class="btn btn-primary btn-lg btn-book">Réserver ce vol</a>
            </div>
        </div>
    </div>
</div>

<?php
// Inclure le pied de page
include 'includes/footer.php';
?>
