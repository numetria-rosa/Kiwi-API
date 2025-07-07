<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Définir le titre et les styles spécifiques à la page
$page_title = 'Résultats de recherche';
$page_css = 'search';
$page_js = 'search';




// Inclure l'en-tête
include '../includes/header.php';
?>

<!-- Barre de recherche simplifiée -->
<div class="search-header">
    <div class="container">
        <form action="<?php echo generate_url('search'); ?>" method="GET" class="search-form-simple">
            <input type="hidden" name="route" value="search">
            <input type="hidden" name="trip_type" value="<?php echo $trip_type; ?>">
            <input type="hidden" name="cabin_class" value="<?php echo $cabin_class; ?>">

            <div class="search-form-row">
                <!-- Origine -->
                <div class="search-form-group">
                    <label for="departure">De</label>
                    <input type="text" id="departure" name="departure" value="<?php echo $departure; ?>" required>
                </div>

                <!-- Destination -->
                <div class="search-form-group">
                    <label for="destination">À</label>
                    <input type="text" id="destination" name="destination" value="<?php echo $destination; ?>" required>
                </div>

                <!-- Date départ -->
                <div class="search-form-group">
                    <label for="departure-date">Départ</label>
                    <input type="date" id="departure-date" name="departure_date" value="<?php echo $departure_date; ?>" required>
                </div>

                <!-- Date retour -->
                <?php if ($trip_type === 'round-trip'): ?>
                <div class="search-form-group">
                    <label for="return-date">Retour</label>
                    <input type="date" id="return-date" name="return_date" value="<?php echo $return_date; ?>">
                </div>
                <?php endif; ?>

                <!-- Bouton de recherche -->
                <div class="search-form-group">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Résultats de recherche -->
<div class="container">
    <div class="search-results-container">
        <!-- Filtres de recherche -->
        <aside class="search-filters">
            <h3>Filtres</h3>

            <!-- Prix -->
            <div class="filter-group">
                <h4>Prix</h4>
                <div class="price-slider">
                    <input type="range" min="0" max="500" value="200" class="slider" id="price-range">
                    <div class="price-display">
                        <span>0 €</span>
                        <span id="price-value">200 €</span>
                        <span>500 €</span>
                    </div>
                </div>
            </div>

            <!-- Escales -->
            <div class="filter-group">
                <h4>Escales</h4>
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="stops" value="0" checked> Direct uniquement
                    </label>
                    <label>
                        <input type="checkbox" name="stops" value="1"> 1 escale maximum
                    </label>
                    <label>
                        <input type="checkbox" name="stops" value="2"> 2 escales maximum
                    </label>
                </div>
            </div>

            <!-- Compagnies aériennes -->
            <div class="filter-group">
                <h4>Compagnies aériennes</h4>
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="airline" value="AF" checked> Air France
                    </label>
                    <label>
                        <input type="checkbox" name="airline" value="BA" checked> British Airways
                    </label>
                    <label>
                        <input type="checkbox" name="airline" value="U2" checked> EasyJet
                    </label>
                </div>
            </div>

            <!-- Horaires -->
            <div class="filter-group">
                <h4>Horaires de départ</h4>
                <div class="time-filter">
                    <div class="time-period">
                        <input type="checkbox" name="departure_time" value="morning" id="morning-departure">
                        <label for="morning-departure">Matin (6h - 12h)</label>
                    </div>
                    <div class="time-period">
                        <input type="checkbox" name="departure_time" value="afternoon" id="afternoon-departure">
                        <label for="afternoon-departure">Après-midi (12h - 18h)</label>
                    </div>
                    <div class="time-period">
                        <input type="checkbox" name="departure_time" value="evening" id="evening-departure">
                        <label for="evening-departure">Soir (18h - 00h)</label>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Liste des résultats -->
        <div class="search-results-list">
            <div class="search-results-header">
                <h2><?php echo count($results); ?> résultats trouvés</h2>

                <div class="search-sort">
                    <label for="sort-by">Trier par :</label>
                    <select id="sort-by">
                        <option value="price_asc">Prix croissant</option>
                        <option value="price_desc">Prix décroissant</option>
                        <option value="duration_asc">Durée croissante</option>
                        <option value="departure_asc">Heure de départ</option>
                    </select>
                </div>
            </div>

            <!-- Liste des vols -->
            <?php foreach ($results as $flight): ?>
            <div class="flight-card">
                <div class="flight-main">
                    <div class="flight-info">
                        <!-- Aller -->
                        <div class="flight-leg">
                            <div class="flight-details">
                                <div class="flight-time">
                                    <div class="departure-time"><?php echo $flight['departure_time']; ?></div>
                                    <div class="flight-duration"><?php echo $flight['duration']; ?></div>
                                    <div class="arrival-time"><?php echo $flight['arrival_time']; ?></div>
                                </div>
                                <div class="flight-path">
                                    <div class="airport-code"><?php echo $flight['departure_airport']; ?></div>
                                    <div class="flight-line">
                                        <?php if ($flight['stops'] === 0): ?>
                                        <div class="flight-icon direct"></div>
                                        <?php else: ?>
                                        <div class="flight-icon with-stops"></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="airport-code"><?php echo $flight['arrival_airport']; ?></div>
                                </div>
                                <div class="flight-cities">
                                    <div class="city"><?php echo $flight['departure_city']; ?></div>
                                    <div class="city-spacer"></div>
                                    <div class="city"><?php echo $flight['arrival_city']; ?></div>
                                </div>
                            </div>
                            <div class="flight-carrier">
                                <div class="carrier-logo">
                                    <img src="assets/img/airlines/<?php echo strtolower($flight['carrier_code']); ?>.png" alt="<?php echo $flight['carrier']; ?>">
                                </div>
                                <div class="carrier-name"><?php echo $flight['carrier']; ?></div>
                            </div>
                        </div>

                        <?php if ($trip_type === 'round-trip'): ?>
                        <!-- Retour -->
                        <div class="flight-leg return-leg">
                            <div class="flight-details">
                                <div class="flight-time">
                                    <div class="departure-time"><?php echo $flight['return_departure_time']; ?></div>
                                    <div class="flight-duration"><?php echo $flight['return_duration']; ?></div>
                                    <div class="arrival-time"><?php echo $flight['return_arrival_time']; ?></div>
                                </div>
                                <div class="flight-path">
                                    <div class="airport-code"><?php echo $flight['arrival_airport']; ?></div>
                                    <div class="flight-line">
                                        <div class="flight-icon direct"></div>
                                    </div>
                                    <div class="airport-code"><?php echo $flight['departure_airport']; ?></div>
                                </div>
                                <div class="flight-cities">
                                    <div class="city"><?php echo $flight['arrival_city']; ?></div>
                                    <div class="city-spacer"></div>
                                    <div class="city"><?php echo $flight['departure_city']; ?></div>
                                </div>
                            </div>
                            <div class="flight-carrier">
                                <div class="carrier-logo">
                                    <img src="assets/img/airlines/<?php echo strtolower($flight['return_carrier_code']); ?>.png" alt="<?php echo $flight['return_carrier']; ?>">
                                </div>
                                <div class="carrier-name"><?php echo $flight['return_carrier']; ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="flight-price">
                        <div class="price-amount"><?php echo format_price($flight['price'], $flight['currency']); ?></div>
                        <a href="<?php echo generate_url('flight', ['id' => $flight['id']]); ?>" class="btn btn-primary">Sélectionner</a>
                    </div>
                </div>

                <div class="flight-details-toggle">
                    <button type="button" class="btn-details">Voir les détails</button>
                </div>
            </div>
            <?php endforeach; ?>

           
        </div>
    </div>
</div>

<?php
// Inclure le pied de page
include '../includes/footer.php';
?>
