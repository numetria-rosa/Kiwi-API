<?php


// Définir le titre et les styles spécifiques à la page
$page_title = 'Résultats de recherche';
$page_css = 'search';
$page_js = 'search';

session_start();
// Define search form variables from SESSION, with sensible defaults
$departure = $_SESSION['departure'] ?? '';
$destination = $_SESSION['destination'] ?? '';
$departure_date = $_SESSION['departure_date'] ?? date('Y-m-d');
$return_date = $_SESSION['return_date'] ?? date('Y-m-d');
$trip_type = 'round-trip';
$cabin_class = $_SESSION['cabin_class'] ?? 'M';
$adults = $_SESSION['adults'] ?? 1;
$children = $_SESSION['children'] ?? 0;
$infants = $_SESSION['infants'] ?? 0;
$adult_hand_bag = $_SESSION['adult_hand_bag'] ?? 1;
$adult_hold_bag = $_SESSION['adult_hold_bag'] ?? 0;
$child_hand_bag = $_SESSION['child_hand_bag'] ?? 0;
$child_hold_bag = $_SESSION['child_hold_bag'] ?? 0;
$BagNum = $_SESSION['total_bags'] ?? '';


if (!isset($_SESSION['flight_result'])) {
    echo "No flight data found.";
    exit;
}

$result = $_SESSION['flight_result'];
$flights = $result['data'] ;
$currency = $result['currency'] ;

// echo "<pre>";
//  print_r($result);
//  echo "</pre>";

function calculate_duration($departure_time, $arrival_time) {
    $departure = new DateTime($departure_time);
    $arrival = new DateTime($arrival_time);
    $interval = $departure->diff($arrival);
    
    $hours = $interval->h;
    $minutes = $interval->i;
    
    return sprintf("%dh%d ", $hours, $minutes);
}

include('../includes/header.php');  // Correct relative path to header.php
include('../config/database.php');
require_once '../models/Airport.php';
$airportModel = new Airport($pdo);
$airportNameCache = [];
function getAirportName($code, $airportModel, &$cache) {
    if (isset($cache[$code])) return $cache[$code];
    $airport = $airportModel->getByCode($code);
    $cache[$code] = $airport && isset($airport['name']) ? $airport['name'] : $airlineCode;
    return $cache[$code];
}

// Use locations table for airport name lookup
$locationNameCache = [];
function getLocationName($code, $pdo, &$cache) {
    if (isset($cache[$code])) return $cache[$code];
    $stmt = $pdo->prepare('SELECT name FROM locations WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $cache[$code] = $row && isset($row['name']) ? $row['name'] : null;
    return $cache[$code];
}

?>


<!-- Add Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Add DateRangePicker Dependencies -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>



<style>
/* Modern Variables */
:root {
    --primary-color: #00a991;
    --secondary-color: #2d3436;
    --accent-color: #0984e3;
    --background-light: #f8f9fa;
    --text-dark: #2d3436;
    --text-light: #636e72;
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
    --border-radius: 8px;
    --transition: all 0.3s ease;
    
    /* Typography */
    --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    --font-heading: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

/* Base Typography */
body {
    font-family: var(--font-primary);
    line-height: 1.6;
    color: var(--text-dark);
}

h1, h2, h3, h4, h5, h6 {
    font-family: var(--font-heading);
    font-weight: 600;
    line-height: 1.3;
}

/* Hero Section */
.hero {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

.hero-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.hero h1 {
    font-family: var(--font-heading);
    font-size: 3.5rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    margin-bottom: 1.5rem;
    line-height: 1.2;
}

.hero p {
    font-size: 1.25rem;
    font-weight: 400;
    opacity: 0.9;
    margin-bottom: 2rem;
    letter-spacing: -0.01em;
}

/* Search Form Container */
.search-form-container {
    background: transparent;
    padding: 0;
    margin: 0 auto;
    max-width: 1200px;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    
    
}

.search-form {
    width: 100%;
    max-width: 1100px;
    display: flex;
    flex-direction: row;
    gap: 1rem;
    align-items: center;
    padding: 0.5rem 1rem;
}

/* Main Row Styles */
.search-form-main-row {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 1rem;
    margin: 0;
    width: 100%;
}

/* Side Group Styles */
.search-form-side-group {
    display: flex;
    flex-direction: row;
    gap: 1rem;
    align-items: center;
}

/* Location Styles */
.search-form-locations {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.location-group {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 1rem;
    width: 100%;
}

/* Form Group Styles */
.search-form-group.compact {
    margin: 0;
    min-width: 150px;
    position: relative;
}

.search-form-group label {
    color: #fff;
    font-weight: 500;
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
    display: block;
    transition: color 0.2s ease;
}

.search-form-group input,
.search-form-group select {
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.95rem;
    border-radius: 6px;
    border: 1px solid #ddd;
    background: #fff;
    color: #222;
    height: 38px;
    transition: all 0.2s ease;
}

.search-form-group input:focus,
.search-form-group select:focus {
    border-color: #008f7a;
    box-shadow: 0 0 0 2px rgba(0, 143, 122, 0.1);
    outline: none;
}

/* Dropdown Styles */
.dropdown-section.compact {
    min-width: 150px;
    position: relative;
}

.dropdown-trigger {
    width: 100%;
    background: #fff;
    color: #222;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    font-size: 0.95rem;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid #ddd;
    transition: all 0.2s ease;
    cursor: pointer;
}

.dropdown-trigger:hover {
    border-color: #008f7a;
    background: #f8f9fa;
}

.dropdown-trigger i {
    color: #008f7a;
    transition: transform 0.2s ease;
}

.dropdown-trigger:hover i {
    transform: translateY(1px);
}

/* Date Group Styles */
.date-group {
    min-width: 150px;
}

.date-range-picker {
    cursor: pointer;
    background: #fff;
}

.date-range-picker:hover {
    border-color: #008f7a;
}

/* Submit Button Styles */
.search-submit {
    margin-left: 1rem;
}

.btn-primary {
    background: #008f7a;
    color: white;
    padding: 0.5rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover {
    background: #007a68;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn-primary:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Dropdown Content Styles */
.dropdown-content {
    background: white;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 1rem;
    min-width: 250px;
    border: 1px solid #eee;
    animation: fadeIn 0.2s ease-out;
}

/* Responsive Styles */
@media (max-width: 1024px) {
    .search-form-main-row {
        flex-wrap: wrap;
    }
    
    .search-form-side-group {
        width: 48%;
    }
    
    .search-form-locations {
        width: 100%;
        margin: 0.5rem 0;
        justify-content: center;
    }
    
    .location-group {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .search-form-side-group {
        width: 100%;
    }
    
    .search-form-secondary-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-submit {
        margin: 0.5rem 0 0 0;
    }
    
    .btn-primary {
        width: 100%;
    }
}

/* Animation Keyframes */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

body {
    padding-top: 20px;
}

.search-header {
    background-color: var(--primary-color);
    padding: 16px 0;
    margin-bottom: 24px;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
}

/* Search Header Styles */
.search-header-green {
    width: 100%;
    background: #008f7a;
    padding: 0.75rem 0;
    margin: 0;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 100;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.search-form-container {
    background: transparent;
    padding: 0;
    margin: 0 auto;
    max-width: 1200px;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    
}

.search-form {
    width: 100%;
    max-width: 1100px;
    display: flex;
    flex-direction: row;
    gap: 1rem;
    align-items: center;
    padding: 0.5rem 1rem;
}

/* Main Row Styles */
.search-form-main-row {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 1rem;
    margin: 0;
    width: 100%;
}

/* Side Group Styles */
.search-form-side-group {
    display: flex;
    flex-direction: row;
    gap: 1rem;
    align-items: center;
}

/* Location Styles */
.search-form-locations {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.location-group {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 1rem;
    width: 100%;
}

/* Form Group Styles */
.search-form-group.compact {
    margin: 0;
    min-width: 150px;
    position: relative;
}

.search-form-group label {
    color: #fff;
    font-weight: 500;
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
    display: block;
    transition: color 0.2s ease;
}

.search-form-group input,
.search-form-group select {
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.95rem;
    border-radius: 6px;
    border: 1px solid #ddd;
    background: #fff;
    color: #222;
    height: 38px;
    transition: all 0.2s ease;
}

.search-form-group input:focus,
.search-form-group select:focus {
    border-color: #008f7a;
    box-shadow: 0 0 0 2px rgba(0, 143, 122, 0.1);
    outline: none;
}

/* Dropdown Styles */
.dropdown-section.compact {
    min-width: 150px;
    position: relative;
}

.dropdown-trigger {
    width: 100%;
    background: #fff;
    color: #222;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    font-size: 0.95rem;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid #ddd;
    transition: all 0.2s ease;
    cursor: pointer;
}

.dropdown-trigger:hover {
    border-color: #008f7a;
    background: #f8f9fa;
}

.dropdown-trigger i {
    color: #008f7a;
    transition: transform 0.2s ease;
}

.dropdown-trigger:hover i {
    transform: translateY(1px);
}

/* Date Group Styles */
.date-group {
    min-width: 150px;
}

.date-range-picker {
    cursor: pointer;
    background: #fff;
}

.date-range-picker:hover {
    border-color: #008f7a;
}

/* Submit Button Styles */
.search-submit {
    margin-left: 1rem;
}

.btn-primary {
    background: #008f7a;
    color: white;
    padding: 0.5rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover {
    background: #007a68;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.btn-primary:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Dropdown Content Styles */
.dropdown-content {
    background: white;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    padding: 1rem;
    min-width: 250px;
    border: 1px solid #eee;
    animation: fadeIn 0.2s ease-out;
}

/* Responsive Styles */
@media (max-width: 1024px) {
    .search-form-main-row {
        flex-wrap: wrap;
    }
    
    .search-form-side-group {
        width: 48%;
    }
    
    .search-form-locations {
        width: 100%;
        margin: 0.5rem 0;
        justify-content: center;
    }
    
    .location-group {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .search-form-side-group {
        width: 100%;
    }
    
    .search-form-secondary-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-submit {
        margin: 0.5rem 0 0 0;
    }
    
    .btn-primary {
        width: 100%;
    }
}

/* Animation Keyframes */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

body {
    padding-top: 20px;
}

.search-header {
    background-color: var(--primary-color);
    padding: 16px 0;
    margin-bottom: 24px;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
}

.flight-card {
    background: #fff;
    border-radius: 12px;
    padding: 18px 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    transition: box-shadow 0.2s, min-height 0.3s;
    position: relative;
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
    display: flex;
    flex-direction: column;
    min-height: 307px;
    /* Remove fixed height, allow to grow */
}
.flight-price-details-row {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 0;
    margin-top: -20px;
}
.flight-price {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 12px;
    min-width: 0;
    width: auto;
    flex: 0 0 auto;
    /* Only as wide as content */
    white-space: nowrap;
}
.btn-details-toggle.main-details-btn {
    margin-left: 0;
    margin-right: 16px;
    /* Place button right beside price */
}
/* Center summary-row in flight-summary */
.flight-summary {
    flex: 1;
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 10px;
    margin-bottom: 10px;
    border-radius: 0 12px 12px 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.summary-row {
    display: flex;
    align-items: center;
    gap: 18px;
    font-size: 1.05rem;
    justify-content: center;
    width: 100%;
}
/* Separation line between Allez and Retour */
.flight-summary-row + .flight-summary-row {
    border-top: 1.5px solid #e0e0e0;
    margin-top: 12px;
    padding-top: 12px;
}
</style>
<!-- Barre de recherche simplifiée -->
<div class="search-header">
<div class="container">
    <div class="search-form-container">

       

        <!-- Flight Search Form -->
        <form id="flight-search-form" method="GET" action="/Test_Case_Kiwi/Booking_Flow/Searchs/Search_One_Way.php" class="search-form">
            <input type="hidden" name="route" value="search">
            <input type="hidden" name="type" value="flights" id="search-type">
            <input type="hidden" name="trip_type" value="round-trip" id="trip-type">

            <!-- Main Search Row -->
            <div class="search-form-main-row">
                <!-- Left Side: Trip Type and Cabin Class -->
                <div class="search-form-side-group">
                    <div class="dropdown-section compact">
                        <button type="button" class="dropdown-trigger" id="trip-type-trigger">
                            <i class="fas fa-plane"></i>
                            <span id="trip-type-summary">Round-trip</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-content" id="trip-type-dropdown">
                            <div class="radio-group">
                                <label class="radio-option">
                                    <input type="radio" name="trip_type_radio" value="round-trip" checked>
                                    <span class="radio-label">Round-trip</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="trip_type_radio" value="one-way">
                                    <span class="radio-label">One-way</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown-section compact">
                        <button type="button" class="dropdown-trigger" id="cabin-class-trigger">
                            <i class="fas fa-chair"></i>
                            <span id="cabin-class-summary">Economy</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-content" id="cabin-class-dropdown">
                            <div class="radio-group">
                                <label class="radio-option">
                                    <input type="radio" name="cabin_class_radio" value="M" checked>
                                    <span class="radio-label">Economy</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="cabin_class_radio" value="W">
                                    <span class="radio-label">Premium Economy</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="cabin_class_radio" value="B">
                                    <span class="radio-label">Business</span>
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="cabin_class_radio" value="F">
                                    <span class="radio-label">First</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Center: Locations -->
                 <!-- Center: Locations -->
                 <div class="search-form-locations">
                    <div class="location-group">
                        <div class="search-form-group compact">
                            <label for="departure">From</label>
                            <div class="autocomplete-container">
                                <input type="text" id="departure" name="departure" value="<?php echo htmlspecialchars($departure); ?>" required autocomplete="off">
                                <div id="departure-results" class="autocomplete-results"></div>
                            </div>
                        </div>
                        <div class="location-swap">
                            <button type="button" class="swap-button">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                        </div>
                        <div class="search-form-group compact">
                            <label for="destination">To</label>
                            <div class="autocomplete-container">
                                <input type="text" id="destination" name="destination" value="<?php echo htmlspecialchars($destination); ?>" required autocomplete="off">
                                <div id="destination-results" class="autocomplete-results"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Dates and Passengers -->
                  <!-- Right Side: Dates and Passengers -->
                  <div class="search-form-side-group">
                    <div class="search-form-group compact date-group">
                        <label for="departure-date">Departure</label>
                        <input type="text" id="departure-date" name="departure_date" class="date-range-picker" value="<?php echo htmlspecialchars($departure_date); ?>" required>
                    </div>
                    <div class="search-form-group compact date-group return-date-group">
                        <label for="return-date-display">Return</label>
                        <input type="text" id="return-date-display" class="date-range-picker" value="<?php echo htmlspecialchars($return_date); ?>" readonly>
                        <input type="hidden" id="return-date" name="return_date" value="<?php echo htmlspecialchars($return_date); ?>">
                    </div>
                </div>
            </div>

            <!-- Secondary Row: Passengers and Baggage -->
            <div class="search-form-secondary-row">
                <div class="dropdown-section compact">
                    <button type="button" class="dropdown-trigger" id="passenger-trigger">
                        <i class="fas fa-users"></i>
                        <span id="passenger-summary">1 Adult</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-content" id="passenger-dropdown">
                        <fieldset class="passenger-section">
                            <legend>Passengers</legend>
                            <div class="search-form-row grouped">
                                <div class="search-form-group compact">
                                    <label for="adults">Adults</label>
                                    <input type="number" id="adults" name="adults" value="<?php echo htmlspecialchars($adults); ?>" min="1" required>
                                </div>
                                <div class="search-form-group compact">
                                    <label for="children">Children</label>
                                    <input type="number" id="children" name="children" value="<?php echo htmlspecialchars($children); ?>" min="0">
                                </div>
                                <div class="search-form-group compact">
                                    <label for="infants">Infants</label>
                                    <input type="number" id="infants" name="infants" value="<?php echo htmlspecialchars($infants); ?>" min="0">
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="dropdown-section compact">
                    <button type="button" class="dropdown-trigger" id="baggage-trigger">
                        <i class="fas fa-suitcase"></i>
                        <span id="baggage-summary">1 Hand Bag</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-content" id="baggage-dropdown">
                        <fieldset class="baggage-section">
                            <legend>Baggage</legend>
                            <div class="search-form-row grouped">
                                <!-- Adult Hand Baggage -->
                                <div class="baggage-group compact">
                                    <div class="baggage-header">
                                        <i class="fas fa-user"></i>
                                        <span>Adult Hand Baggage</span>
                                        <small>Max 7kg</small>
                                    </div>
                                    <div class="baggage-controls">
                                        <button type="button" class="baggage-btn minus" data-type="adult_hand_bag">-</button>
                                        <input type="number" id="adult_hand_bag" name="adult_hand_bag" value="<?php echo htmlspecialchars($adult_hand_bag); ?>" min="0" readonly>
                                        <button type="button" class="baggage-btn plus" data-type="adult_hand_bag">+</button>
                                    </div>
                                </div>

                                <!-- Adult Checked Baggage -->
                                <div class="baggage-group compact">
                                    <div class="baggage-header">
                                        <i class="fas fa-user"></i>
                                        <span>Adult Checked Baggage</span>
                                        <small>Max 23kg</small>
                                    </div>
                                    <div class="baggage-controls">
                                        <button type="button" class="baggage-btn minus" data-type="adult_hold_bag">-</button>
                                        <input type="number" id="adult_hold_bag" name="adult_hold_bag" value="<?php echo htmlspecialchars($adult_hold_bag); ?>" min="0" readonly>
                                        <button type="button" class="baggage-btn plus" data-type="adult_hold_bag">+</button>
                                    </div>
                                </div>

                                <!-- Child Hand Baggage -->
                                <div class="baggage-group compact">
                                    <div class="baggage-header">
                                        <i class="fas fa-child"></i>
                                        <span>Child Hand Baggage</span>
                                        <small>Max 7kg</small>
                                    </div>
                                    <div class="baggage-controls">
                                        <button type="button" class="baggage-btn minus" data-type="child_hand_bag">-</button>
                                        <input type="number" id="child_hand_bag" name="child_hand_bag" value="<?php echo htmlspecialchars($child_hand_bag); ?>" min="0" readonly>
                                        <button type="button" class="baggage-btn plus" data-type="child_hand_bag">+</button>
                                    </div>
                                </div>

                                <!-- Child Checked Baggage -->
                                <div class="baggage-group compact">
                                    <div class="baggage-header">
                                        <i class="fas fa-child"></i>
                                        <span>Child Checked Baggage</span>
                                        <small>Max 23kg</small>
                                    </div>
                                    <div class="baggage-controls">
                                        <button type="button" class="baggage-btn minus" data-type="child_hold_bag">-</button>
                                        <input type="number" id="child_hold_bag" name="child_hold_bag" value="<?php echo htmlspecialchars($child_hold_bag); ?>" min="0" readonly>
                                        <button type="button" class="baggage-btn plus" data-type="child_hold_bag">+</button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="search-submit">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>

        <div id="search-results"></div>
    </div>
</div>
</div>
<style>
/* Modern Variables */
:root {
    --primary-color: #00a991;
    --secondary-color: #2d3436;
    --accent-color: #0984e3;
    --background-light: #f8f9fa;
    --text-dark: #2d3436;
    --text-light: #636e72;
    --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
    --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
    --border-radius: 8px;
    --transition: all 0.3s ease;
    
    /* Typography */
    --font-primary: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    --font-heading: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

/* Base Typography */
body {
    font-family: var(--font-primary);
    line-height: 1.6;
    color: var(--text-dark);
}

h1, h2, h3, h4, h5, h6 {
    font-family: var(--font-heading);
    font-weight: 600;
    line-height: 1.3;
}

/* Hero Section */
.hero {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

.hero-content {
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
    position: relative;
    z-index: 2;
}

.hero h1 {
    font-family: var(--font-heading);
    font-size: 3.5rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    margin-bottom: 1.5rem;
    line-height: 1.2;
}

.hero p {
    font-size: 1.25rem;
    font-weight: 400;
    opacity: 0.9;
    margin-bottom: 2rem;
    letter-spacing: -0.01em;
}

/* Search Form Container */
.search-form-container {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    padding: 2rem;
    margin-top: -6px;
    position: relative;
    z-index: 10;
    max-width: 1152px;

}

.search-form-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    border-bottom: 2px solid #eee;
    padding-bottom: 1rem;
}

.search-form-tab {
    padding: 0.75rem 1.5rem;
    cursor: pointer;
    border-radius: var(--border-radius);
    transition: var(--transition);
    font-family: var(--font-primary);
    font-weight: 500;
    letter-spacing: -0.01em;
}

.search-form-tab.active {
    background: var(--primary-color);
    color: white;
}

/* Form Elements */
.search-form-row.grouped {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}

.search-form-group {
    flex: 1 1 200px;
    min-width: 150px;
}

.search-form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-dark);
    font-weight: 500;
    letter-spacing: -0.01em;
}

.search-form-group input,
.search-form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    transition: var(--transition);
    font-family: var(--font-primary);
    font-size: 0.95rem;
    letter-spacing: -0.01em;
    margin-right: 53px;
}

.search-form-group input:focus,
.search-form-group select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(0, 169, 145, 0.1);
    outline: none;
}

/* Trip Type Selector */
.trip-type-selector {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.trip-type-button {
    padding: -0.25rem 1.5rem;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    background: white;
    cursor: pointer;
    transition: var(--transition);
}

.trip-type-button.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

/* Fieldsets */
fieldset {
    margin: 2rem 0;
    padding: 1.5rem;
    border: 1px solid #eee;
    border-radius: var(--border-radius);
    background: var(--background-light);
}

legend {
    padding: 0 1rem;
    font-weight: 600;
    color: var(--text-dark);
    font-size: 1.1rem;
}

/* Search Options */
.search-options {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    margin: 2rem 0;
    padding: 1rem;
    background: var(--background-light);
    border-radius: var(--border-radius);
}

.search-options label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

/* Submit Button */
.search-submit {
    text-align: center;
    margin-top: 2rem;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    padding: 1rem 2rem;
    border: none;
    border-radius: var(--border-radius);
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    font-family: var(--font-primary);
    letter-spacing: -0.01em;
}

.btn-primary:hover {
    background: #008f7a;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Popular Destinations */
.popular-destinations {
    padding: 4rem 0;
    background: var(--background-light);
}

.section-title {
    text-align: center;
    font-size: 2.5rem;
    margin-bottom: 3rem;
    color: var(--text-dark);
    font-family: var(--font-heading);
    font-weight: 600;
    letter-spacing: -0.02em;
}

.destinations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    padding: 0 1rem;
}

.destination-card {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    text-decoration: none;
    color: var(--text-dark);
}

.destination-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.destination-image {
    height: 200px;
    overflow: hidden;
}

.destination-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.destination-card:hover .destination-image img {
    transform: scale(1.1);
}

.destination-content {
    padding: 1.5rem;
}

.destination-name {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    font-family: var(--font-heading);
    font-weight: 600;
    letter-spacing: -0.01em;
}

.destination-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--text-light);
    font-size: 0.95rem;
    font-weight: 400;
    letter-spacing: -0.01em;
}

.destination-price {
    color: var(--primary-color);
    font-weight: 600;
    letter-spacing: -0.01em;
}

/* Guarantee Section */
.guarantee-section {
    padding: 4rem 0;
    background: white;
}

.guarantee-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}

.guarantee-content {
    padding-right: 2rem;
}

.guarantee-title {
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
    color: var(--text-dark);
    font-family: var(--font-heading);
    font-weight: 600;
    letter-spacing: -0.02em;
}

.guarantee-description {
    font-size: 1.1rem;
    color: var(--text-light);
    margin-bottom: 2rem;
    font-weight: 400;
    letter-spacing: -0.01em;
    line-height: 1.6;
}

.guarantee-features {
    display: grid;
    gap: 1.5rem;
}

.guarantee-feature {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.guarantee-feature-icon {
    background: var(--primary-color);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.guarantee-feature-text {
    font-weight: 500;
    letter-spacing: -0.01em;
}

.guarantee-image img {
    width: 100%;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
}

/* Newsletter Section */
.newsletter-section {
    padding: 4rem 0;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
}

.newsletter-container {
    text-align: center;
    max-width: 600px;
    margin: 0 auto;
}

.newsletter-title {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    font-family: var(--font-heading);
    font-weight: 600;
    letter-spacing: -0.02em;
}

.newsletter-description {
    margin-bottom: 2rem;
    opacity: 0.9;
    font-size: 1.1rem;
    font-weight: 400;
    letter-spacing: -0.01em;
    line-height: 1.6;
}

.newsletter-form {
    display: flex;
    gap: 1rem;
    max-width: 500px;
    margin: 0 auto;
}

.newsletter-form input {
    flex: 1;
    padding: 1rem;
    border: none;
    border-radius: var(--border-radius);
    font-size: 1rem;
}

.newsletter-form button {
    padding: 1rem 2rem;
    background: white;
    color: var(--primary-color);
    border: none;
    border-radius: var(--border-radius);
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.newsletter-form button:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero h1 {
        font-size: 2.5rem;
        letter-spacing: -0.02em;
    }
    
    .guarantee-container {
        grid-template-columns: 1fr;
    }
    
    .newsletter-form {
        flex-direction: column;
    }
    
    .search-form-container {
        margin-top: -30px;
        padding: 1.5rem;
    }
    
    .section-title,
    .guarantee-title,
    .newsletter-title {
        font-size: 2rem;
        letter-spacing: -0.02em;
    }
    
    .destination-name {
        font-size: 1.25rem;
    }
}

/* Dropdown Section Styles */
.dropdown-section {
    position: relative;
    flex: 1;
    min-width: 200px;
}

.dropdown-trigger {
    width: 78%;
    padding: 1rem;
    background: white;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    transition: var(--transition);
    font-size: 1rem;
    color: var(--text-dark);
    height: 100%;
}

.dropdown-trigger:hover {
    border-color: var(--primary-color);
}

.dropdown-trigger i:first-child {
    color: var(--primary-color);
    font-size: 1.25rem;
    width: 24px;
    text-align: center;
}

.dropdown-trigger span {
    flex: 1;
    text-align: left;
    font-weight: 500;
}

.dropdown-trigger i:last-child {
    color: var(--text-light);
    transition: var(--transition);
}

.dropdown-trigger.active i:last-child {
    transform: rotate(180deg);
}

.dropdown-content {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    margin-top: 0.5rem;
    z-index: 1000;
    display: none;
    padding: 1rem;
    min-width: 250px;
}
#baggage-dropdown {
    width: 614px;
}

.dropdown-content.active {
    display: block;
}

/* Passenger Section Styles */
.passenger-section {
    background: white;
    border: none;
    padding: 0;
    margin: 0;
}

.passenger-section legend {
    display: none;
}

/* Baggage Section Styles */
.baggage-section {
    background: white;
    border: none;
    padding: 0;
    margin: 0;
    max-width: 600px;
}

.baggage-section legend {
    display: none;
    max-width: 600px;
}

.baggage-group {
    background: var(--background-light);
    border-radius: var(--border-radius);
    padding: 1rem;
    margin-bottom: 1rem;
    transition: var(--transition);
}

.baggage-group:hover {
    box-shadow: var(--shadow-sm);
}

.baggage-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.baggage-header i {
    font-size: 1.25rem;
    color: var(--primary-color);
    width: 24px;
    text-align: center;
}

.baggage-header span {
    font-weight: 500;
    color: var(--text-dark);
    flex: 1;
}

.baggage-header small {
    color: var(--text-light);
    font-size: 0.85rem;
}

.baggage-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.baggage-btn {
    width: 32px;
    height: 32px;
    border: 2px solid var(--primary-color);
    border-radius: 50%;
    background: white;
    color: var(--primary-color);
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    padding: 0;
    line-height: 1;
}

.baggage-btn:hover {
    background: var(--primary-color);
    color: white;
}

.baggage-btn:disabled {
    border-color: #ddd;
    color: #ddd;
    cursor: not-allowed;
}

.baggage-btn:disabled:hover {
    background: white;
}

.baggage-controls input {
    width: 50px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    padding: 0.5rem;
    font-size: 1rem;
    font-weight: 500;
    color: var(--text-dark);
    background: white;
    -moz-appearance: textfield;
}

.baggage-controls input::-webkit-outer-spin-button,
.baggage-controls input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .baggage-group {
        padding: 0.75rem;
    }

    .baggage-header {
        flex-wrap: wrap;
    }

    .baggage-header small {
        width: 100%;
        margin-left: 2.5rem;
    }
}

/* Add these new styles after the existing dropdown styles */
.radio-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.radio-option {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: var(--transition);
}

.radio-option:hover {
    background: var(--background-light);
}

.radio-option input[type="radio"] {
    width: 18px;
    height: 18px;
    margin: 0;
    accent-color: var(--primary-color);
}

.radio-label {
    font-size: 1rem;
    color: var(--text-dark);
    font-weight: 500;
}

/* Add these new styles after the existing styles */
.search-form-main-row {
    display: flex;
    gap: 0rem;
    margin-bottom: 1rem;
    align-items: flex-start;
}

.search-form-side-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    flex: 0 0 180px;
}

.search-form-locations {
    flex: 1;
    min-width: 300px;
}

.search-form-secondary-row {
    display: flex;
    flex-direction: column;
    align-items: center; /* or center if you want centered dropdowns */
    gap: 0.5rem;
    margin-left: 41px;

}
.location-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.location-swap {
    display: flex;
    align-items: center;
    justify-content: center;
}

.swap-button {
    width: 32px;
    height: 32px;
    border: 1px solid #ddd;
    border-radius: 50%;
    background: white;
    color: var(--text-light);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
}

.swap-button:hover {
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.search-form-group.compact {
    margin-bottom: 0;
    margin-left: 12px;

}

.search-form-group.compact label {
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.search-form-group.compact input {
    padding: 0.5rem;
    font-size: 0.9rem;
}

.dropdown-section.compact .dropdown-trigger {
    padding: 0.5rem;
    font-size: 0.9rem;
}
.dropdown-section.compact {
    width: 135%; /* or set a fixed width like 300px */
}


.baggage-group.compact {
    padding: 0.75rem;
}

.baggage-group.compact .baggage-header {
    margin-bottom: 0.5rem;
}

.baggage-group.compact .baggage-header span {
    font-size: 0.9rem;
}

.baggage-group.compact .baggage-header small {
    font-size: 0.75rem;
}

.search-submit {
    margin-left: auto;
}

.search-submit .btn {
    padding: 0.5rem 1.5rem;
    font-size: 0.9rem;
}

.date-group {
    flex: 1;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .search-form-main-row {
        flex-direction: column;
    }

    .search-form-side-group {
        flex: 1;
        width: 100%;
    }

    .search-form-locations {
        width: 100%;
    }
}

@media (max-width: 768px) {



    .search-submit {
        margin-left: 0;
        margin-top: 1rem;
    }
}

/* Add these styles after your existing styles */
.autocomplete-container {
    position: relative;
    width: 100%;
}

.autocomplete-results {
    display: none;
    position: absolute;
    top: 113%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-lg);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
    width: 321px;
}

.autocomplete-item {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}

.autocomplete-item:last-child {
    border-bottom: none;
}

.autocomplete-item:hover {
    background-color: #f8f9fa;
}

.airport-name {
    font-weight: bold;
    color: var(--text-dark);
    display: block;
    margin-bottom: 2px;
}

.airport-details {
    font-size: 0.85rem;
    color: var(--text-light);
}

/* Add these styles after your existing styles */
.daterangepicker {
    font-family: var(--font-primary);
    border: none;
    box-shadow: var(--shadow-lg);
    border-radius: var(--border-radius);
}

.daterangepicker .calendar-table {
    border: none;
    background-color: white;
}

.daterangepicker td.active, 
.daterangepicker td.active:hover {
    background-color: var(--primary-color);
}

.daterangepicker td.in-range {
    background-color: rgba(0, 169, 145, 0.1);
    color: var(--text-dark);
}

.daterangepicker .calendar-table .next span,
.daterangepicker .calendar-table .prev span {
    border-color: var(--text-light);
}
@media (min-width: 564px) {
    .daterangepicker .drp-calendar.left .calendar-table {
        padding-right: 18px;
    }}

.daterangepicker .ranges li.active {
    background-color: var(--primary-color);
}

.daterangepicker .ranges li:hover {
    background-color: rgba(0, 169, 145, 0.1);
}

.date-range-picker {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: var(--border-radius);
    transition: var(--transition);
    font-family: var(--font-primary);
    font-size: 0.95rem;
    letter-spacing: -0.01em;
    cursor: pointer;
    background-color: white;
}

.date-range-picker:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(0, 169, 145, 0.1);
    outline: none;
}

#return-date-display {
    background-color: #f8f9fa;
    cursor: pointer;
}

#return-date-display:hover {
    border-color: var(--primary-color);
}

.return-date-group {
    margin-top: 0.5rem;
}
</style>


<!-- Add Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tripTypeButtons = document.querySelectorAll('.trip-type-button');
    const tripTypeInput = document.getElementById('trip-type');
    const flightForm = document.getElementById('flight-search-form');

    const oneWayAction = "/Test_Case_Kiwi/Booking_Flow/Searchs/Search_One_Way.php";
    const roundTripAction = "/Test_Case_Kiwi/Booking_Flow/Searchs/Search_Round_Trip.php";

  // Function to format date to dd/mm/yyyy
  function formatDate(date) {
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0'); 
        const year = d.getFullYear();
        return `${day}/${month}/${year}`;
    }
    // Trip type toggle
    tripTypeButtons.forEach(button => {
        button.addEventListener('click', () => {
            tripTypeButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const selectedType = button.getAttribute('data-type');
            tripTypeInput.value = selectedType;

            if (selectedType === "one-way") {
                flightForm.action = oneWayAction;
                document.querySelector('.return-date-group').style.display = "none"; // hide return field
            } else {
                flightForm.action = roundTripAction;
                document.querySelector('.return-date-group').style.display = "block"; // show return field
            }
        });
    });

                // Replace "0" values with empty string before submitting
            flightForm.addEventListener('submit', function () {
                const inputs = flightForm.querySelectorAll('input[type="number"]');
                inputs.forEach(input => {
                    if (input.value === "0") {
                        input.value = "";
                    }
                });

            
            });

    // Trigger initial state
    if (tripTypeInput.value === "one-way") {
        flightForm.action = oneWayAction;
        document.querySelector('.return-date-group').style.display = "none";
    } else {
        flightForm.action = roundTripAction;
        document.querySelector('.return-date-group').style.display = "block";
    }

    // Dropdown functionality
    const dropdowns = {
        passenger: {
            trigger: document.getElementById('passenger-trigger'),
            content: document.getElementById('passenger-dropdown'),
            summary: document.getElementById('passenger-summary')
        },
        baggage: {
            trigger: document.getElementById('baggage-trigger'),
            content: document.getElementById('baggage-dropdown'),
            summary: document.getElementById('baggage-summary')
        }
    };

    // Toggle dropdowns
    Object.values(dropdowns).forEach(dropdown => {
        dropdown.trigger.addEventListener('click', () => {
            dropdown.trigger.classList.toggle('active');
            dropdown.content.classList.toggle('active');
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        Object.values(dropdowns).forEach(dropdown => {
            if (!dropdown.trigger.contains(e.target) && !dropdown.content.contains(e.target)) {
                dropdown.trigger.classList.remove('active');
                dropdown.content.classList.remove('active');
            }
        });
    });

    // Update passenger summary
    function updatePassengerSummary() {
        const adults = parseInt(document.getElementById('adults').value) || 0;
        const children = parseInt(document.getElementById('children').value) || 0;
        const infants = parseInt(document.getElementById('infants').value) || 0;
        
        let summary = [];
        if (adults) summary.push(`${adults} Adult${adults > 1 ? 's' : ''}`);
        if (children) summary.push(`${children} Child${children > 1 ? 'ren' : ''}`);
        if (infants) summary.push(`${infants} Infant${infants > 1 ? 's' : ''}`);
        
        dropdowns.passenger.summary.textContent = summary.join(', ') || 'Select Passengers';
    }

    // Update baggage summary
    function updateBaggageSummary() {
        const adultHandBag = parseInt(document.getElementById('adult_hand_bag').value) || 0;
        const adultHoldBag = parseInt(document.getElementById('adult_hold_bag').value) || 0;
        const childHandBag = parseInt(document.getElementById('child_hand_bag').value) || 0;
        const childHoldBag = parseInt(document.getElementById('child_hold_bag').value) || 0;
        
        let summary = [];
        if (adultHandBag) summary.push(`${adultHandBag} Adult Hand Bag${adultHandBag > 1 ? 's' : ''}`);
        if (adultHoldBag) summary.push(`${adultHoldBag} Adult Checked Bag${adultHoldBag > 1 ? 's' : ''}`);
        if (childHandBag) summary.push(`${childHandBag} Child Hand Bag${childHandBag > 1 ? 's' : ''}`);
        if (childHoldBag) summary.push(`${childHoldBag} Child Checked Bag${childHoldBag > 1 ? 's' : ''}`);
        
        dropdowns.baggage.summary.textContent = summary.join(', ') || 'Select Baggage';
    }

    // Handle baggage controls with passenger limits
    document.querySelectorAll('.baggage-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const type = btn.dataset.type;
            const input = document.getElementById(type);
            const currentValue = parseInt(input.value);
            const adults = parseInt(document.getElementById('adults').value) || 0;
            const children = parseInt(document.getElementById('children').value) || 0;
            
            // Determine if this is for adults or children
            const isAdult = type.startsWith('adult_');
            const passengerCount = isAdult ? adults : children;
            
            if (btn.classList.contains('plus')) {
                // Check if adding would exceed passenger count
                if (currentValue < passengerCount) {
                    input.value = currentValue + 1;
                }
            } else if (btn.classList.contains('minus') && currentValue > 0) {
                input.value = currentValue - 1;
            }
            
            updateBaggageSummary();
            updateBaggageButtonStates();
        });
    });

    // Update baggage limits when passenger count changes
    function updateBaggageLimits() {
        const adults = parseInt(document.getElementById('adults').value) || 0;
        const children = parseInt(document.getElementById('children').value) || 0;
        
        // Reset baggage counts if they exceed new passenger limits
        const adultHandBag = document.getElementById('adult_hand_bag');
        const adultHoldBag = document.getElementById('adult_hold_bag');
        const childHandBag = document.getElementById('child_hand_bag');
        const childHoldBag = document.getElementById('child_hold_bag');
        
        if (parseInt(adultHandBag.value) > adults) adultHandBag.value = adults;
        if (parseInt(adultHoldBag.value) > adults) adultHoldBag.value = adults;
        if (parseInt(childHandBag.value) > children) childHandBag.value = children;
        if (parseInt(childHoldBag.value) > children) childHoldBag.value = children;
        
        updateBaggageSummary();
        updateBaggageButtonStates();
    }

    // Update button states based on current values and limits
    function updateBaggageButtonStates() {
        const adults = parseInt(document.getElementById('adults').value) || 0;
        const children = parseInt(document.getElementById('children').value) || 0;
        
        document.querySelectorAll('.baggage-btn').forEach(btn => {
            const type = btn.dataset.type;
            const input = document.getElementById(type);
            const currentValue = parseInt(input.value);
            const isAdult = type.startsWith('adult_');
            const passengerCount = isAdult ? adults : children;
            
            if (btn.classList.contains('plus')) {
                btn.disabled = currentValue >= passengerCount;
            } else if (btn.classList.contains('minus')) {
                btn.disabled = currentValue <= 0;
            }
        });
    }

    // Add event listeners to passenger inputs
    ['adults', 'children', 'infants'].forEach(id => {
        document.getElementById(id).addEventListener('change', () => {
            updatePassengerSummary();
            updateBaggageLimits();
        });
    });

    // Initialize summaries and button states
    updatePassengerSummary();
    updateBaggageSummary();
    updateBaggageButtonStates();

    // Trip Type Dropdown
    const tripTypeTrigger = document.getElementById('trip-type-trigger');
    const tripTypeDropdown = document.getElementById('trip-type-dropdown');
    const tripTypeSummary = document.getElementById('trip-type-summary');
    const tripTypeRadios = document.querySelectorAll('input[name="trip_type_radio"]');

    tripTypeTrigger.addEventListener('click', () => {
        tripTypeTrigger.classList.toggle('active');
        tripTypeDropdown.classList.toggle('active');
    });

    tripTypeRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            const selectedType = radio.value;
            tripTypeInput.value = selectedType;
            tripTypeSummary.textContent = selectedType === 'round-trip' ? 'Round-trip' : 'One-way';
            
            if (selectedType === "one-way") {
                flightForm.action = oneWayAction;
                document.querySelector('.return-date-group').style.display = "none";
            } else {
                flightForm.action = roundTripAction;
                document.querySelector('.return-date-group').style.display = "block";
            }
            
            tripTypeDropdown.classList.remove('active');
            tripTypeTrigger.classList.remove('active');
        });
    });

    // Cabin Class Dropdown
    const cabinClassTrigger = document.getElementById('cabin-class-trigger');
    const cabinClassDropdown = document.getElementById('cabin-class-dropdown');
    const cabinClassSummary = document.getElementById('cabin-class-summary');
    const cabinClassRadios = document.querySelectorAll('input[name="cabin_class_radio"]');
    const cabinClassSelect = document.getElementById('cabin-class');

    cabinClassTrigger.addEventListener('click', () => {
        cabinClassTrigger.classList.toggle('active');
        cabinClassDropdown.classList.toggle('active');
    });

    cabinClassRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            const selectedClass = radio.value;
            cabinClassSelect.value = selectedClass;
            cabinClassSummary.textContent = radio.nextElementSibling.textContent;
            cabinClassDropdown.classList.remove('active');
            cabinClassTrigger.classList.remove('active');
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (!tripTypeTrigger.contains(e.target) && !tripTypeDropdown.contains(e.target)) {
            tripTypeTrigger.classList.remove('active');
            tripTypeDropdown.classList.remove('active');
        }
        if (!cabinClassTrigger.contains(e.target) && !cabinClassDropdown.contains(e.target)) {
            cabinClassTrigger.classList.remove('active');
            cabinClassDropdown.classList.remove('active');
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const departureInput = document.getElementById('departure');
    const destinationInput = document.getElementById('destination');
    const departureResults = document.getElementById('departure-results');
    const destinationResults = document.getElementById('destination-results');
    let timeoutId;

    function createAutocompleteItem(airport) {
        const div = document.createElement('div');
        div.className = 'autocomplete-item';
        div.innerHTML = `
            <span class="airport-name">${airport.name} (${airport.code})</span>
            <span class="airport-details">${airport.city_name}, ${airport.country_name}</span>
        `;
        div.dataset.id = airport.code;
        div.dataset.name = airport.name;
        return div;
    }

    function handleAutocomplete(input, resultsDiv) {
        const query = input.value.trim();
        console.log('Handling autocomplete for query:', query);
        
        if (query.length < 3) {
            resultsDiv.style.display = 'none';
            return;
        }

        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            fetch(`/views/airport_search.php?query=${encodeURIComponent(query)}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Received data:', data);
                    resultsDiv.innerHTML = '';
                    
                    if (data.error) {
                        console.error('Server error:', data.error);
                        console.error('Error message:', data.message);
                        resultsDiv.style.display = 'none';
                        return;
                    }
                    
                    if (data && data.length > 0) {
                        data.forEach(airport => {
                            const item = createAutocompleteItem(airport);
                            item.addEventListener('click', () => {
                                // Set the display value to the full airport name
                                input.value = airport.name;
                                // Store the airport code in a data attribute
                                input.dataset.code = airport.code;
                                resultsDiv.style.display = 'none';
                            });
                            resultsDiv.appendChild(item);
                        });
                        resultsDiv.style.display = 'block';
                    } else {
                        resultsDiv.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching airports:', error);
                    resultsDiv.style.display = 'none';
                });
        }, 300);
    }

    // Add event listeners for both inputs
    [departureInput, destinationInput].forEach(input => {
        const resultsDiv = input.id === 'departure' ? departureResults : destinationResults;
        
        // Input event for typing
        input.addEventListener('input', () => {
            console.log('Input event triggered for:', input.id);
            handleAutocomplete(input, resultsDiv);
        });
        
        // Focus event
        input.addEventListener('focus', () => {
            console.log('Focus event triggered for:', input.id);
            if (input.value.length >= 3) {
                handleAutocomplete(input, resultsDiv);
            }
        });

        // Close results when clicking outside
        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !resultsDiv.contains(e.target)) {
                resultsDiv.style.display = 'none';
            }
        });
    });

    // Update the swap button functionality
    const swapButton = document.querySelector('.swap-button');
    swapButton.addEventListener('click', () => {
        const tempValue = departureInput.value;
        const tempCode = departureInput.dataset.code;
        departureInput.value = destinationInput.value;
        departureInput.dataset.code = destinationInput.dataset.code;
        destinationInput.value = tempValue;
        destinationInput.dataset.code = tempCode;
    });

    // Update form submission to use the airport codes
    const flightForm = document.getElementById('flight-search-form');
    flightForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Create hidden inputs for the airport codes
        const departureCode = document.createElement('input');
        departureCode.type = 'hidden';
        departureCode.name = 'departure';
        departureCode.value = departureInput.dataset.code || departureInput.value;
        
        const destinationCode = document.createElement('input');
        destinationCode.type = 'hidden';
        destinationCode.name = 'destination';
        destinationCode.value = destinationInput.dataset.code || destinationInput.value;
        
        // Remove any existing hidden inputs
        const existingHidden = this.querySelectorAll('input[type="hidden"][name="departure"], input[type="hidden"][name="destination"]');
        existingHidden.forEach(input => input.remove());
        
        // Add the new hidden inputs
        this.appendChild(departureCode);
        this.appendChild(destinationCode);
        
        // Submit the form
        this.submit();
    });
});

// Update the DateRangePicker initialization
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DateRangePicker
    $('.date-range-picker').daterangepicker({
        startDate: moment('<?php echo htmlspecialchars($departure_date); ?>'),
        endDate: moment('<?php echo htmlspecialchars($return_date); ?>'),
        minDate: moment(),
        autoApply: true,
        showCustomRangeLabel: false,
        alwaysShowCalendars: true,
        opens: 'center',
        drops: 'auto',
        singleDatePicker: false,
        locale: {
            format: 'YYYY-MM-DD',
            separator: ' - ',
            applyLabel: 'Apply',
            cancelLabel: 'Cancel',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            weekLabel: 'W',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        }
    });

    // Update the display values when dates are selected
    $('.date-range-picker').on('apply.daterangepicker', function(ev, picker) {
        const startDate = picker.startDate.format('YYYY-MM-DD');
        const endDate = picker.endDate.format('YYYY-MM-DD');
        
        // Update departure date input
        $('#departure-date').val(startDate);
        
        // Update return date input
        $('#return-date-display').val(endDate);
        $('#return-date').val(endDate);
    });

    // Make both inputs trigger the same date picker
    $('#return-date-display').on('click', function() {
        $('#departure-date').trigger('click');
    });

    // Set initial values
    const initialStartDate = moment('<?php echo htmlspecialchars($departure_date); ?>').format('YYYY-MM-DD');
    const initialEndDate = moment('<?php echo htmlspecialchars($return_date); ?>').format('YYYY-MM-DD');
    $('#departure-date').val(initialStartDate);
    $('#return-date-display').val(initialEndDate);
    $('#return-date').val(initialEndDate);
});
</script>
<!-- Résultats de recherche -->
<div class="container">
    <div class="search-results-container" style="margin-top: 56px;">
    <aside class="search-filters">
            <div class="filters-content">
                <h3>Filtres</h3>

                <!-- Prix -->
                <div class="filter-group">
                    <h4>Prix</h4>
                    <div class="price-slider-container">
                        <div class="price-range-header">
                            <span class="price-label">Prix maximum</span>
                            <span class="price-value" id="price-value">2000 <?php echo htmlspecialchars($currency); ?></span>
                        </div>
                        <div class="price-slider">
                            <input type="range" min="0" max="2000" value="2000" class="slider" id="price-range">
                            <div class="price-track">
                                <div class="price-track-fill"></div>
                            </div>
                        </div>
                        <div class="price-range-footer">
                            <span class="min-price">0 <?php echo htmlspecialchars($currency); ?></span>
                            <span class="max-price">2000 <?php echo htmlspecialchars($currency); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Escales -->
                <div class="filter-group">
                    <h4>Escales</h4>
                    <div class="checkbox-group">
                        <label class="modern-checkbox">
                            <input type="checkbox" name="stops" value="0">
                            <span class="checkmark"></span>
                            <span class="label-text">Direct uniquement</span>
                        </label>
                        <label class="modern-checkbox">
                            <input type="checkbox" name="stops" value="1">
                            <span class="checkmark"></span>
                            <span class="label-text">1 escale maximum</span>
                        </label>
                        <label class="modern-checkbox">
                            <input type="checkbox" name="stops" value="2">
                            <span class="checkmark"></span>
                            <span class="label-text">2 escales maximum</span>
                        </label>
                    </div>
                </div>

                <!-- Airlines -->
                <div class="filter-group">
                    <h4>Compagnies aériennes</h4>
                    <div class="checkbox-group" id="airlines-filter">
                        <?php
                        // Get unique airlines from the results
                        $uniqueAirlines = [];
                        foreach ($flights as $flight) {
                            foreach ($flight['route'] as $segment) {
                                $airlineCode = $segment['airline'];
                                if (!isset($uniqueAirlines[$airlineCode])) {
                                    if (isDatabaseConnected()) {
                                        $query = "SELECT Name FROM carrier WHERE id = :iata_code";
                                        $stmt = $pdo->prepare($query);
                                        $stmt->bindParam(':iata_code', $airlineCode, PDO::PARAM_STR);
                                        $stmt->execute();
                                        $airline = $stmt->fetch(PDO::FETCH_ASSOC);
                                        $airlineName = $airline ? $airline['Name'] : $airlineCode;
                                    } else {
                                        $airlineName = $airlineCode;
                                    }
                                    $uniqueAirlines[$airlineCode] = $airlineName;
                                }
                            }
                        }
                        foreach ($uniqueAirlines as $code => $name): ?>
                            <label class="modern-checkbox">
                                <input type="checkbox" name="airlines" value="<?php echo htmlspecialchars($code); ?>">
                                <span class="checkmark"></span>
                                <span class="label-text"><?php echo htmlspecialchars($name); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
               
            </div>
        </aside>
        <!-- Liste des résultats -->
        <div class="search-results-list">
            <div class="search-results-header">
                <h2><?php echo count($flights); ?> résultats trouvés</h2>

            </div>

         <!-- Liste des vols -->
         <?php 
         $flights_per_page = 10;
         $total_flights = count($flights);
         $total_pages = ceil($total_flights / $flights_per_page);
         $current_page = isset($_GET['page']) ? max(1, min($total_pages, intval($_GET['page']))) : 1;
         $start_index = ($current_page - 1) * $flights_per_page;
         $end_index = min($start_index + $flights_per_page, $total_flights);
         
         for ($i = $start_index; $i < $end_index; $i++): 
             $flight = $flights[$i];
             if (isset($flight['availability']['seats']) && $flight['availability']['seats'] === null) {
                 continue;
             }
            $outboundSegmentsArray = array_filter($flight['route'], function($segment) {
                return isset($segment['return']) && $segment['return'] === 0;
            });
            $outboundSegments = array_values($outboundSegmentsArray);
             $returnSegmentsArray = array_filter($flight['route'], function($segment) {
                 return isset($segment['return']) && $segment['return'] === 1;
             });
             $returnSegments = array_values($returnSegmentsArray);
            $isDirectFlight = count($outboundSegments) === 1;
            ?>
            <div class="flight-card" data-stops="<?php echo count($outboundSegments) - 1; ?>" data-airlines="<?php echo htmlspecialchars(implode(',', array_unique(array_column($flight['route'], 'airline')))); ?>">
                <!-- OUTBOUND SUMMARY -->
                <div class="flight-summary-row">
                    <div class="flight-summary-label allez-label">Allez</div>
                    <div class="flight-summary">
                        <div class="summary-row">
                            <span class="summary-time"><strong><?php echo (new DateTime($outboundSegments[0]['local_departure']))->format('H:i'); ?></strong></span>
                            <span class="summary-airport"><?php 
                                $code = $outboundSegments[0]['flyFrom'];
                                $name = getLocationName($code, $pdo, $locationNameCache);
                                echo $name ? htmlspecialchars($name) : $code; 
                            ?></span>
                            <span class="summary-arrow">→</span>
                            <span class="summary-time"><strong><?php echo (new DateTime($outboundSegments[count($outboundSegments)-1]['local_arrival']))->format('H:i'); ?></strong></span>
                            <span class="summary-airport"><?php 
                                $code = $outboundSegments[count($outboundSegments)-1]['flyTo'];
                                $name = getLocationName($code, $pdo, $locationNameCache);
                                echo $name ? htmlspecialchars($name) : $code; 
                            ?></span>
                            <span class="summary-duration">
                                    <?php
                                $dep = new DateTime($outboundSegments[0]['local_departure']);
                                $arr = new DateTime($outboundSegments[count($outboundSegments)-1]['local_arrival']);
                                $interval = $dep->diff($arr);
                                echo $interval->h . 'h' . $interval->i . 'm';
                                            ?>
                                        </span>
                            <span class="summary-stops"><?php echo count($outboundSegments)-1 === 0 ? 'Direct' : (count($outboundSegments)-1) . ' stop' . (count($outboundSegments)-1 === 1 ? '' : 's'); ?></span>
                            <span class="summary-airline">
                                <img src="../assets/img/airlines/<?php echo strtoupper($outboundSegments[0]['airline']); ?>.png" alt="<?php echo $outboundSegments[0]['airline']; ?>" style="height: 20px; vertical-align: middle;"> 
                                
                            </span>
                            <?php if(count($outboundSegments) > 1): ?>
                            <?php endif; ?>
                        </div>
                        <?php if(count($outboundSegments) > 1): ?>
                        <div class="flight-details-content" style="display:none;">
                            <?php foreach ($outboundSegments as $segIndex => $seg): ?>
                                <div class="flight-segment-detail">
                                    <span><strong><?php echo (new DateTime($seg['local_departure']))->format('H:i'); ?></strong> (<?php echo $seg['flyFrom']; ?>)
                                        <?php if (isset($seg['airportFromName'])): ?>
                                            - <?php echo htmlspecialchars($seg['airportFromName']); ?>
                                        <?php endif; ?>
                                        → <strong><?php echo (new DateTime($seg['local_arrival']))->format('H:i'); ?></strong> (<?php echo $seg['flyTo']; ?>)
                                        <?php if (isset($seg['airportToName'])): ?>
                                            - <?php echo htmlspecialchars($seg['airportToName']); ?>
                                        <?php endif; ?>
                                    </span>
                                    <span><?php echo $seg['cityFrom']; ?> → <?php echo $seg['cityTo']; ?> | <?php echo $seg['airline']; ?> <?php echo $seg['flight_no']; ?></span>
                                </div>
                                <?php if ($segIndex < count($outboundSegments) - 1): ?>
                                    <?php
                                    $currentArrival = new DateTime($seg['local_arrival']);
                                    $nextDeparture = new DateTime($outboundSegments[$segIndex + 1]['local_departure']);
                                    $layover = $currentArrival->diff($nextDeparture);
                                    ?>
                                    <div class="flight-stop-info">
                                        <span>Stop at <strong><?php echo $seg['cityTo']; ?> (<?php echo $seg['flyTo']; ?>)
                                            <?php if (isset($seg['airportToName'])): ?>
                                                - <?php echo htmlspecialchars($seg['airportToName']); ?>
                                            <?php endif; ?>
                                        </strong></span>
                                        <span>Layover: <strong><?php echo $layover->h . 'h ' . $layover->i . 'm'; ?></strong></span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- RETURN SUMMARY -->
                <div class="flight-summary-row">
                    <div class="flight-summary-label retour-label">Retour</div>
                    <div class="flight-summary">
                        <div class="summary-row">
                            <span class="summary-time"><strong><?php echo (new DateTime($returnSegments[0]['local_departure']))->format('H:i'); ?></strong></span>
                            <span class="summary-airport"><?php 
                                $code = $returnSegments[0]['flyFrom'];
                                $name = getLocationName($code, $pdo, $locationNameCache);
                                echo $name ? htmlspecialchars($name) : $code; 
                            ?></span>
                            <span class="summary-arrow">→</span>
                            <span class="summary-time"><strong><?php echo (new DateTime($returnSegments[count($returnSegments)-1]['local_arrival']))->format('H:i'); ?></strong></span>
                            <span class="summary-airport"><?php 
                                $code = $returnSegments[count($returnSegments)-1]['flyTo'];
                                $name = getLocationName($code, $pdo, $locationNameCache);
                                echo $name ? htmlspecialchars($name) : $code; 
                            ?></span>
                            <span class="summary-duration">
                                    <?php
                                $dep = new DateTime($returnSegments[0]['local_departure']);
                                $arr = new DateTime($returnSegments[count($returnSegments)-1]['local_arrival']);
                                $interval = $dep->diff($arr);
                                echo $interval->h . 'h' . $interval->i . 'm';
                                            ?>
                                        </span>
                            <span class="summary-stops"><?php echo count($returnSegments)-1 === 0 ? 'Direct' : (count($returnSegments)-1) . ' stop' . (count($returnSegments)-1 === 1 ? '' : 's'); ?></span>
                            <span class="summary-airline">
                                <img src="../assets/img/airlines/<?php echo strtoupper($returnSegments[0]['airline']); ?>.png" alt="<?php echo $returnSegments[0]['airline']; ?>" style="height: 20px; vertical-align: middle;"> 
                               
                            </span>
                            <?php if(count($returnSegments) > 1): ?>
                            <?php endif; ?>
                        </div>
                        <?php if(count($returnSegments) > 1): ?>
                        <div class="flight-details-content" style="display:none;">
                            <?php foreach ($returnSegments as $segIndex => $seg): ?>
                                <div class="flight-segment-detail">
                                    <span><strong><?php echo (new DateTime($seg['local_departure']))->format('H:i'); ?></strong> (<?php echo $seg['flyFrom']; ?>)
                                        <?php if (isset($seg['airportFromName'])): ?>
                                            - <?php echo htmlspecialchars($seg['airportFromName']); ?>
                                        <?php endif; ?>
                                        → <strong><?php echo (new DateTime($seg['local_arrival']))->format('H:i'); ?></strong> (<?php echo $seg['flyTo']; ?>)
                                        <?php if (isset($seg['airportToName'])): ?>
                                            - <?php echo htmlspecialchars($seg['airportToName']); ?>
                                        <?php endif; ?>
                                    </span>
                                    <span><?php echo $seg['cityFrom']; ?> → <?php echo $seg['cityTo']; ?> | <?php echo $seg['airline']; ?> <?php echo $seg['flight_no']; ?></span>
                                </div>
                                <?php if ($segIndex < count($returnSegments) - 1): ?>
                                    <?php
                                    $currentArrival = new DateTime($seg['local_arrival']);
                                    $nextDeparture = new DateTime($returnSegments[$segIndex + 1]['local_departure']);
                                    $layover = $currentArrival->diff($nextDeparture);
                                    ?>
                                    <div class="flight-stop-info">
                                        <span>Stop at <strong><?php echo $seg['cityTo']; ?> (<?php echo $seg['flyTo']; ?>)
                                            <?php if (isset($seg['airportToName'])): ?>
                                                - <?php echo htmlspecialchars($seg['airportToName']); ?>
                                            <?php endif; ?>
                                        </strong></span>
                                        <span>Layover: <strong><?php echo $layover->h . 'h ' . $layover->i . 'm'; ?></strong></span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- PRICE & DETAILS BUTTON -->
                <div class="flight-price-details-row">
                    <button class="btn-details-toggle main-details-btn" type="button" onclick="toggleDetails(this)">Details</button>
                    <div class="flight-price">
                        <div class="price-amount"><?php echo number_format((float)$flight['price'], 2) . " " . htmlspecialchars($currency); ?></div>
                        <a href="http://localhost/Test_Case_Kiwi/Booking_Flow/Check_Flights_round.php?booking_token=<?php echo $flight['booking_token']; ?>&search_id=<?php echo $result['search_id']; ?>&adults=<?php echo $adults; ?>&children=<?php echo $children; ?>&infants=<?php echo $infants; ?>&bags=<?php echo $BagNum; ?>" class="btn btn-primary" style="padding: 8px 12px;">Sélectionner</a>
                    </div>
                </div>
                <!-- DETAILS SECTION (hidden by default) -->
                <div class="flight-details-content main-details-content" style="display:none;">
                    <div class="details-leg-label allez-label">Allez</div>
                    <?php foreach ($outboundSegments as $segIndex => $seg): ?>
                        <div class="flight-segment-detail">
                            <span><strong><?php echo (new DateTime($seg['local_departure']))->format('H:i'); ?></strong> (<?php echo $seg['flyFrom']; ?>)
                                <?php if (isset($seg['airportFromName'])): ?>
                                    - <?php echo htmlspecialchars($seg['airportFromName']); ?>
                                <?php endif; ?>
                                → <strong><?php echo (new DateTime($seg['local_arrival']))->format('H:i'); ?></strong> (<?php echo $seg['flyTo']; ?>)
                                <?php if (isset($seg['airportToName'])): ?>
                                    - <?php echo htmlspecialchars($seg['airportToName']); ?>
                                <?php endif; ?>
                            </span>
                            <span><?php echo $seg['cityFrom']; ?> → <?php echo $seg['cityTo']; ?> | <?php echo $seg['airline']; ?> <?php echo $seg['flight_no']; ?></span>
                        </div>
                        <?php if ($segIndex < count($outboundSegments) - 1): ?>
                            <?php
                            $currentArrival = new DateTime($seg['local_arrival']);
                            $nextDeparture = new DateTime($outboundSegments[$segIndex + 1]['local_departure']);
                            $layover = $currentArrival->diff($nextDeparture);
                            ?>
                            <div class="flight-stop-info">
                                <span>Stop at <strong><?php echo $seg['cityTo']; ?> (<?php echo $seg['flyTo']; ?>)
                                    <?php if (isset($seg['airportToName'])): ?>
                                        - <?php echo htmlspecialchars($seg['airportToName']); ?>
                                    <?php endif; ?>
                                </strong></span>
                                <span>Layover: <strong><?php echo $layover->h . 'h ' . $layover->i . 'm'; ?></strong></span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <div class="details-leg-label retour-label">Retour</div>
                    <?php foreach ($returnSegments as $segIndex => $seg): ?>
                        <div class="flight-segment-detail">
                            <span><strong><?php echo (new DateTime($seg['local_departure']))->format('H:i'); ?></strong> (<?php echo $seg['flyFrom']; ?>)
                                <?php if (isset($seg['airportFromName'])): ?>
                                    - <?php echo htmlspecialchars($seg['airportFromName']); ?>
                                <?php endif; ?>
                                → <strong><?php echo (new DateTime($seg['local_arrival']))->format('H:i'); ?></strong> (<?php echo $seg['flyTo']; ?>)
                                <?php if (isset($seg['airportToName'])): ?>
                                    - <?php echo htmlspecialchars($seg['airportToName']); ?>
                                <?php endif; ?>
                            </span>
                            <span><?php echo $seg['cityFrom']; ?> → <?php echo $seg['cityTo']; ?> | <?php echo $seg['airline']; ?> <?php echo $seg['flight_no']; ?></span>
                        </div>
                        <?php if ($segIndex < count($returnSegments) - 1): ?>
                            <?php
                            $currentArrival = new DateTime($seg['local_arrival']);
                            $nextDeparture = new DateTime($returnSegments[$segIndex + 1]['local_departure']);
                            $layover = $currentArrival->diff($nextDeparture);
                            ?>
                            <div class="flight-stop-info">
                                <span>Stop at <strong><?php echo $seg['cityTo']; ?> (<?php echo $seg['flyTo']; ?>)
                                    <?php if (isset($seg['airportToName'])): ?>
                                        - <?php echo htmlspecialchars($seg['airportToName']); ?>
                                    <?php endif; ?>
                                </strong></span>
                                <span>Layover: <strong><?php echo $layover->h . 'h ' . $layover->i . 'm'; ?></strong></span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <!-- <div class="flight-details-toggle">
                    <a href="<?php echo $flight['deep_link']; ?>" class="btn-details">Voir les détails</a>
                </div> -->
            </div>
        <?php endfor; ?>
        <script>
        function toggleDetails(btn) {
            const card = btn.closest('.flight-card');
            const details = card.querySelector('.main-details-content');
            if (details.style.display === 'none' || details.style.display === '') {
                details.style.display = 'block';
                btn.textContent = 'Hide Details';
            } else {
                details.style.display = 'none';
                btn.textContent = 'Details';
            }
        }
        </script>
        <style>
        .flight-card {
            background: #fff;
            border-radius: 12px;
            padding: 18px 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            transition: box-shadow 0.2s, min-height 0.3s;
            position: relative;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            display: flex;
            flex-direction: column;
            min-height: 307px;
            /* Remove fixed height, allow to grow */
        }
        .flight-summary-row {
            display: flex;
            align-items: stretch;
            margin-bottom: 0;
        }
        .flight-summary-label {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            background: #00a991;
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            border-radius: 10px 0 0 10px;
            padding: 18px 8px;
            margin-right: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            letter-spacing: 1px;
            margin-right:17px;
        }
        .allez-label {
            background: #00a991;
        }
        .retour-label {
            background: #0984e3;
        }
        .flight-summary {
            flex: 1;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-radius: 0 12px 12px 0;
        }
        .flight-summary:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .summary-row {
            display: flex;
            align-items: center;
            gap: 18px;
            font-size: 1.05rem;
        }
        .summary-time, .summary-airport, .summary-arrow, .summary-duration, .summary-stops, .summary-airline {
            margin-right: 8px;
        }
        .summary-arrow {
            font-size: 1.2rem;
            color: #00a991;
        }
        .btn-details-toggle {
            background: #00a991;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 6px 18px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.2s;
            margin-left: auto;
        }
        .btn-details-toggle:hover {
            background: #007a68;
        }
        .flight-details-content {
            margin-top: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px 18px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            animation: fadeIn 0.3s;
        }
        .flight-segment-detail {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
            font-size: 0.98rem;
        }
        .flight-segment-detail:last-child {
            border-bottom: none;
        }
        </style>
        <style>
        .flight-stop-info {
            background: #eef6ff;
            border-left: 4px solid #007bff;
            border-radius: 6px;
            margin: 10px 0 10px 0;
            padding: 10px 16px;
            font-size: 0.97rem;
            color: #222;
            display: flex;
            gap: 18px;
            align-items: center;
        }
        </style>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?page=<?php echo $current_page - 1; ?>" class="pagination-btn prev">
                <span class="pagination-icon">←</span>
                Précédent
            </a>
        <?php endif; ?>

        <div class="pagination-numbers">
            <?php
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);

            if ($start_page > 1) {
                echo '<a href="?page=1" class="pagination-number">1</a>';
                if ($start_page > 2) {
                    echo '<span class="pagination-dots">...</span>';
                }
            }

            for ($i = $start_page; $i <= $end_page; $i++) {
                $active_class = $i === $current_page ? 'active' : '';
                echo "<a href=\"?page={$i}\" class=\"pagination-number {$active_class}\">{$i}</a>";
            }

            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo '<span class="pagination-dots">...</span>';
                }
                echo "<a href=\"?page={$total_pages}\" class=\"pagination-number\">{$total_pages}</a>";
            }
            ?>
        </div>

        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?php echo $current_page + 1; ?>" class="pagination-btn next">
                Suivant
                <span class="pagination-icon">→</span>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
        

         
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const priceSlider = document.getElementById('price-range');
    const priceValue = document.getElementById('price-value');
    const priceTrackFill = document.querySelector('.price-track-fill');
    const flightCards = Array.from(document.querySelectorAll('.flight-card'));
    const stopCheckboxes = document.querySelectorAll('input[name="stops"]');
    const airlineCheckboxes = document.querySelectorAll('input[name="airlines"]');
    const currency = '<?php echo htmlspecialchars($currency); ?>';
    const resultsPerPage = 10;
    let currentPage = 1;

    // Get all flight prices and find min/max
    const flightPrices = flightCards.map(card => {
        const priceElement = card.querySelector('.price-amount');
        const priceText = priceElement.textContent;
        return parseFloat(priceText.replace(/[^0-9.-]+/g, ''));
    });

    const minPrice = Math.min(...flightPrices);
    const maxPrice = Math.max(...flightPrices);

    // Update slider min/max based on actual flight prices
    priceSlider.min = minPrice;
    priceSlider.max = maxPrice;
    priceSlider.value = maxPrice; // Set to maxPrice initially

    // Update price display elements
    document.querySelector('.min-price').textContent = minPrice + ' ' + currency;
    document.querySelector('.max-price').textContent = maxPrice + ' ' + currency;
    priceValue.textContent = maxPrice + ' ' + currency; // Show max price initially

    // Update track fill on load
    updateTrackFill();

    function updateTrackFill() {
        const percentage = ((priceSlider.value - priceSlider.min) / (priceSlider.max - priceSlider.min)) * 100;
        priceTrackFill.style.transform = `scaleX(${percentage / 100})`;
    }

    // Filtering logic
    function getFilteredCards() {
        const selectedPrice = parseFloat(priceSlider.value);
        const selectedStops = Array.from(stopCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
        const selectedAirlines = Array.from(airlineCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        return flightCards.filter(card => {
            // Price filter
            const priceElement = card.querySelector('.price-amount');
            const priceText = priceElement.textContent;
            const price = parseFloat(priceText.replace(/[^0-9.-]+/g, ''));
            const matchesPrice = price <= selectedPrice;

            // Stops filter
            const stops = card.getAttribute('data-stops');
            const matchesStops = selectedStops.length === 0 || selectedStops.includes(stops);

            // Airlines filter
            const airlines = card.getAttribute('data-airlines').split(',');
            const matchesAirlines = selectedAirlines.length === 0 || airlines.some(code => selectedAirlines.includes(code));

            return matchesPrice && matchesStops && matchesAirlines;
        });
    }

    function showPage(page, filteredCards) {
        // Hide all cards
        flightCards.forEach(card => card.style.display = 'none');
        // Show only cards for this page
        const start = (page - 1) * resultsPerPage;
        const end = start + resultsPerPage;
        filteredCards.slice(start, end).forEach(card => card.style.display = 'block');
    }

    function renderPagination(filteredCards) {
        const pagination = document.getElementById('pagination');
        pagination.innerHTML = '';
        const totalPages = Math.ceil(filteredCards.length / resultsPerPage);
        if (totalPages <= 1) return;
        // Prev button
        if (currentPage > 1) {
            const prev = document.createElement('a');
            prev.href = '#';
            prev.className = 'pagination-btn prev';
            prev.innerHTML = '<span class="pagination-icon">←</span> Précédent';
            prev.addEventListener('click', function(e) {
                e.preventDefault();
                currentPage--;
                update();
            });
            pagination.appendChild(prev);
        }
        // Page numbers
        const numbersDiv = document.createElement('div');
        numbersDiv.className = 'pagination-numbers';
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);
        if (startPage > 1) {
            const first = document.createElement('a');
            first.href = '#';
            first.className = 'pagination-number';
            first.textContent = '1';
            first.addEventListener('click', function(e) { e.preventDefault(); currentPage = 1; update(); });
            numbersDiv.appendChild(first);
            if (startPage > 2) {
                const dots = document.createElement('span');
                dots.className = 'pagination-dots';
                dots.textContent = '...';
                numbersDiv.appendChild(dots);
            }
        }
        for (let i = startPage; i <= endPage; i++) {
            const num = document.createElement('a');
            num.href = '#';
            num.className = 'pagination-number' + (i === currentPage ? ' active' : '');
            num.textContent = i;
            num.addEventListener('click', function(e) { e.preventDefault(); currentPage = i; update(); });
            numbersDiv.appendChild(num);
        }
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const dots = document.createElement('span');
                dots.className = 'pagination-dots';
                dots.textContent = '...';
                numbersDiv.appendChild(dots);
            }
            const last = document.createElement('a');
            last.href = '#';
            last.className = 'pagination-number';
            last.textContent = totalPages;
            last.addEventListener('click', function(e) { e.preventDefault(); currentPage = totalPages; update(); });
            numbersDiv.appendChild(last);
        }
        pagination.appendChild(numbersDiv);
        // Next button
        if (currentPage < totalPages) {
            const next = document.createElement('a');
            next.href = '#';
            next.className = 'pagination-btn next';
            next.innerHTML = 'Suivant <span class="pagination-icon">→</span>';
            next.addEventListener('click', function(e) {
                e.preventDefault();
                currentPage++;
                update();
            });
            pagination.appendChild(next);
        }
    }

    function update() {
        const filteredCards = getFilteredCards();
        const totalPages = Math.ceil(filteredCards.length / resultsPerPage);
        if (currentPage > totalPages) currentPage = totalPages || 1;
        showPage(currentPage, filteredCards);
        renderPagination(filteredCards);
    }

    // Update price display and apply filters when slider moves
    priceSlider.addEventListener('input', function() {
        const value = parseFloat(this.value);
        priceValue.textContent = value + ' ' + currency;
        updateTrackFill();
        currentPage = 1;
        update();
    });

    // Apply filters when stop checkboxes change
    stopCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            currentPage = 1;
            update();
        });
    });

    // Apply filters when airline checkboxes change
    airlineCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            currentPage = 1;
            update();
        });
    });

    // Initial filter and pagination
    update();
});
</script>


<style>
/* Modern Search Filters */
.search-filters {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    position: sticky;
    top: 24px;
    max-height: calc(100vh - 100px);
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--primary-color) #f0f0f0;
    margin-top: 10px;
    width: 275px;
    margin-left: 10px;

}


.search-filters h3 {
    font-family: var(--font-heading);
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 24px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f0f0f0;
}

.filter-group {
    margin-bottom: 28px;
}

.filter-group h4 {
    font-family: var(--font-heading);
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 16px;
}

/* Modern Price Slider */
.price-slider-container {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    margin: 8px 0;
}

.price-range-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.price-label {
    font-size: 0.9rem;
    color: var(--text-light);
    font-weight: 500;
}

.price-value {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-color);
    background: white;
    padding: 4px 12px;
    border-radius: 16px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.price-slider {
    position: relative;
    height: 40px;
    display: flex;
    align-items: center;
    margin: 8px 0;
}

.slider {
    -webkit-appearance: none;
    width: 100%;
    height: 4px;
    background: transparent;
    outline: none;
    position: relative;
    z-index: 2;
}

.price-track {
    position: absolute;
    width: 100%;
    height: 4px;
    background: #e0e0e0;
    border-radius: 2px;
    top: 50%;
    transform: translateY(-50%);
}

.price-track-fill {
    position: absolute;
    height: 100%;
    background: var(--primary-color);
    border-radius: 2px;
    width: 100%;
    transform-origin: left;
    transform: scaleX(1);
    transition: transform 0.2s ease;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 24px;
    height: 24px;
    background: white;
    border: 2px solid var(--primary-color);
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
    position: relative;
    z-index: 3;
}

.slider::-webkit-slider-thumb:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.slider::-webkit-slider-thumb:active {
    transform: scale(0.95);
}

.price-range-footer {
    display: flex;
    justify-content: space-between;
    margin-top: 12px;
    font-size: 0.85rem;
    color: var(--text-light);
}

/* Modern Checkboxes */
.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.modern-checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 8px 0;
    position: relative;
}

.modern-checkbox input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.checkmark {
    position: relative;
    height: 20px;
    width: 20px;
    background-color: white;
    border: 2px solid #e0e0e0;
    border-radius: 4px;
    margin-right: 12px;
    transition: all 0.2s ease;
}

.modern-checkbox:hover .checkmark {
    border-color: var(--primary-color);
}

.modern-checkbox input:checked ~ .checkmark {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
    left: 6px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.modern-checkbox input:checked ~ .checkmark:after {
    display: block;
}

.label-text {
    font-size: 0.95rem;
    color: var(--text-dark);
    transition: color 0.2s ease;
}

.modern-checkbox:hover .label-text {
    color: var(--primary-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    .search-filters {
        position: static;
        margin-bottom: 24px;
    }
}

/* Filters Scroll */
.search-filters {
    max-height: calc(100vh - 100px);
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--primary-color) #f0f0f0;
}

.search-filters::-webkit-scrollbar {
    width: 6px;
}

.search-filters::-webkit-scrollbar-track {
    background: #f0f0f0;
    border-radius: 3px;
}

.search-filters::-webkit-scrollbar-thumb {
    background-color: var(--primary-color);
    border-radius: 3px;
}

.filters-content {
    padding-right: 12px;
}

/* Pagination Styles */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 16px;
    margin-top: 32px;
    padding: 16px 0;
}

.pagination-numbers {
    display: flex;
    align-items: center;
    gap: 8px;
}

.pagination-number {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 8px;
    border-radius: 8px;
    background: white;
    color: var(--text-dark);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid #e0e0e0;
}

.pagination-number:hover {
    background: #f8f9fa;
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.pagination-number.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.pagination-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 8px;
    background: white;
    color: var(--text-dark);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid #e0e0e0;
}

.pagination-btn:hover {
    background: #f8f9fa;
    border-color: var(--primary-color);
    color: var(--primary-color);
}

.pagination-icon {
    font-size: 1.2rem;
    line-height: 1;
}

.pagination-dots {
    color: var(--text-light);
    padding: 0 4px;
}

/* Responsive Pagination */
@media (max-width: 768px) {
    .pagination {
        flex-wrap: wrap;
        gap: 12px;
    }

    .pagination-numbers {
        order: 2;
        width: 100%;
        justify-content: center;
    }

    .pagination-btn {
        order: 1;
    }

    .pagination-btn.next {
        order: 3;
    }
}

.flight-main {
    background: white;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    max-width: 900px;
    margin: 0 auto;
}

.flight-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
}

.flight-leg {
    flex: 1;
    min-width: 0;
}

.flight-segment {
    border: 1px solid #dddfe4;
    border-radius: 8px;
    padding: 12px;
    background-color: #fff;
    width: 100%;
    max-width: 600px;
}

.segment-times {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 8px;
}

.time-group {
    text-align: center;
    min-width: 80px;
}

.flight-line {
    flex: 1;
    position: relative;
    height: 2px;
    background: rgb(0, 0, 0);
    margin: 0 8px;
}

.segment-airline {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
    padding-top: 6px;
    border-top: 1px dashed #ccc;
}

.flight-price {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 12px;
    min-width: 120px;
    margin-bottom: 46px;
}

.price-amount {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--primary-color);
}
</style>


<?php
// Inclure le pied de page
include '../includes/footer.php';
?>
