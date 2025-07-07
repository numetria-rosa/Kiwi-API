<?php
// Définir le titre et les styles spécifiques à la page
$page_title = 'Trouvez des vols pas chers et explorez de nouvelles destinations';
$page_css = 'home';
$page_js = 'home';

// Inclure l'en-tête
include 'includes/header.php';
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

<!-- Section hero -->
<section class="hero">
    <div class="hero-decoration">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="container">
        <div class="hero-content">
            <h1>We hack the system, you fly for less</h1>
            <p>Réservez des vols pas chers que les autres sites ne trouvent tout simplement pas.</p>
        </div>
    </div>
</section>

<!-- Search Form -->
<div class="container">
    <div class="search-form-container">

        <!-- Tabs -->
        <div class="search-form-tabs">
            <div class="search-form-tab active" data-type="flights">Flights</div>
            <div class="search-form-tab" data-type="hotels">Hotels</div>
            <div class="search-form-tab" data-type="cars">Cars</div>
        </div>

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
                <div class="search-form-locations">
                    <div class="location-group">
                        <div class="search-form-group compact">
                            <label for="departure">From</label>
                            <div class="autocomplete-container">
                                <input type="text" id="departure" name="departure" value="" required autocomplete="off">
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
                                <input type="text" id="destination" name="destination" value="" required autocomplete="off">
                                <div id="destination-results" class="autocomplete-results"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Dates and Passengers -->
                <div class="search-form-side-group">
                    <div class="search-form-group compact date-group">
                        <label for="departure-date">Departure</label>
                        <input type="text" id="departure-date" name="departure_date" class="date-range-picker" value="2025-04-20" required>
                    </div>
                    <div class="search-form-group compact date-group return-date-group">
                        <label for="return-date-display">Return</label>
                        <input type="text" id="return-date-display" class="date-range-picker" value="2025-04-20" readonly>
                        <input type="hidden" id="return-date" name="return_date" value="2025-04-20">
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
                                    <input type="number" id="adults" name="adults" value="1" min="1" required>
                                </div>
                                <div class="search-form-group compact">
                                    <label for="children">Children</label>
                                    <input type="number" id="children" name="children" value="0" min="0">
                                </div>
                                <div class="search-form-group compact">
                                    <label for="infants">Infants</label>
                                    <input type="number" id="infants" name="infants" value="0" min="0">
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
                                        <input type="number" id="adult_hand_bag" name="adult_hand_bag" value="1" min="0" readonly>
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
                                        <input type="number" id="adult_hold_bag" name="adult_hold_bag" value="0" min="0" readonly>
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
                                        <input type="number" id="child_hand_bag" name="child_hand_bag" value="0" min="0" readonly>
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
                                        <input type="number" id="child_hold_bag" name="child_hold_bag" value="0" min="0" readonly>
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
    margin-top: -50px;
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
    gap: 1rem;
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
    width: 100%; /* or set a fixed width like 300px */
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
        startDate: moment('2025-04-20'),
        endDate: moment('2025-04-20'),
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
    const initialStartDate = moment('2025-04-20').format('YYYY-MM-DD');
    const initialEndDate = moment('2025-04-20').format('YYYY-MM-DD');
    $('#departure-date').val(initialStartDate);
    $('#return-date-display').val(initialEndDate);
    $('#return-date').val(initialEndDate);
});
</script>

<!-- Section destinations populaires -->
<section class="popular-destinations">
    <div class="container">
        <h2 class="section-title">Destinations populaires</h2>

        <div class="destinations-grid">
            <!-- Destination 1 -->
            <a href="<?php echo generate_url('search', ['departure' => 'Paris', 'destination' => 'Londres']); ?>" class="destination-card">
                <div class="destination-image">
                    <img src="assets/img/destinations/london.jpg" alt="Londres">
                </div>
                <div class="destination-content">
                    <h3 class="destination-name">Londres</h3>
                    <div class="destination-meta">
                        <span>Paris → Londres</span>
                        <span class="destination-price">Dès 45 €</span>
                    </div>
                </div>
            </a>

            <!-- Destination 2 -->
            <a href="<?php echo generate_url('search', ['departure' => 'Paris', 'destination' => 'Barcelone']); ?>" class="destination-card">
                <div class="destination-image">
                    <img src="assets/img/destinations/barcelona.jpg" alt="Barcelone">
                </div>
                <div class="destination-content">
                    <h3 class="destination-name">Barcelone</h3>
                    <div class="destination-meta">
                        <span>Paris → Barcelone</span>
                        <span class="destination-price">Dès 67 €</span>
                    </div>
                </div>
            </a>

            <!-- Destination 3 -->
            <a href="<?php echo generate_url('search', ['departure' => 'Paris', 'destination' => 'Rome']); ?>" class="destination-card">
                <div class="destination-image">
                    <img src="assets/img/destinations/rome.jpg" alt="Rome">
                </div>
                <div class="destination-content">
                    <h3 class="destination-name">Rome</h3>
                    <div class="destination-meta">
                        <span>Paris → Rome</span>
                        <span class="destination-price">Dès 59 €</span>
                    </div>
                </div>
            </a>

            <!-- Destination 4 -->
            <a href="<?php echo generate_url('search', ['departure' => 'Paris', 'destination' => 'Lisbonne']); ?>" class="destination-card">
                <div class="destination-image">
                    <img src="assets/img/destinations/lisbon.jpg" alt="Lisbonne">
                </div>
                <div class="destination-content">
                    <h3 class="destination-name">Lisbonne</h3>
                    <div class="destination-meta">
                        <span>Paris → Lisbonne</span>
                        <span class="destination-price">Dès 82 €</span>
                    </div>
                </div>
            </a>

            <!-- Destination 5 -->
            <a href="<?php echo generate_url('search', ['departure' => 'Paris', 'destination' => 'Amsterdam']); ?>" class="destination-card">
                <div class="destination-image">
                    <img src="assets/img/destinations/amsterdam.jpg" alt="Amsterdam">
                </div>
                <div class="destination-content">
                    <h3 class="destination-name">Amsterdam</h3>
                    <div class="destination-meta">
                        <span>Paris → Amsterdam</span>
                        <span class="destination-price">Dès 56 €</span>
                    </div>
                </div>
            </a>

            <!-- Destination 6 -->
            <a href="<?php echo generate_url('search', ['departure' => 'Paris', 'destination' => 'Berlin']); ?>" class="destination-card">
                <div class="destination-image">
                    <img src="assets/img/destinations/berlin.jpg" alt="Berlin">
                </div>
                <div class="destination-content">
                    <h3 class="destination-name">Berlin</h3>
                    <div class="destination-meta">
                        <span>Paris → Berlin</span>
                        <span class="destination-price">Dès 75 €</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Section garantie Kiwi -->
<section class="guarantee-section">
    <div class="container">
        <div class="guarantee-container">
            <div class="guarantee-content">
                <h2 class="guarantee-title">La nouvelle garantie Kiwi.com</h2>
                <p class="guarantee-description">Surmontez toutes les anxiétés liées au voyage grâce à notre garantie complète.</p>

                <div class="guarantee-features">
                    <div class="guarantee-feature">
                        <div class="guarantee-feature-icon">✓</div>
                        <div class="guarantee-feature-text">Enregistrement automatique pour faciliter votre voyage</div>
                    </div>
                    <div class="guarantee-feature">
                        <div class="guarantee-feature-icon">✓</div>
                        <div class="guarantee-feature-text">Options de vols alternatifs pour les correspondances manquées</div>
                    </div>
                    <div class="guarantee-feature">
                        <div class="guarantee-feature-icon">✓</div>
                        <div class="guarantee-feature-text">Crédit instantané pour les vols annulés</div>
                    </div>
                    <div class="guarantee-feature">
                        <div class="guarantee-feature-icon">✓</div>
                        <div class="guarantee-feature-text">Carte d'embarquement en direct pour un accès facile</div>
                    </div>
                </div>

                <a href="#" class="btn btn-primary mt-3">Découvrir plus</a>
            </div>

            <div class="guarantee-image">
                <img src="assets/img/guarantee.jpg" alt="Garantie Kiwi.com">
            </div>
        </div>
    </div>
</section>

<!-- Section newsletter -->
<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-container">
            <h2 class="newsletter-title">Abonnez-vous à la newsletter Kiwi.com</h2>
            <p class="newsletter-description">Recevez des offres exclusives et des promotions directement dans votre boîte de réception.</p>

            <form class="newsletter-form">
                <input type="email" placeholder="Votre adresse e-mail" required>
                <button type="submit" class="btn btn-primary">S'abonner</button>
            </form>
        </div>
    </div>
</section>

<?php
// Inclure le pied de page
include 'includes/footer.php';
?>
