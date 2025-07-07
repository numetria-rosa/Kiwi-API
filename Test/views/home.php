<?php
// Définir le titre et les styles spécifiques à la page
$page_title = 'Trouvez des vols pas chers et explorez de nouvelles destinations';
$page_css = 'home';
$page_js = 'home';

// Inclure l'en-tête
include 'includes/header.php';
?>

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

<!-- Formulaire de recherche -->
<div class="container">
    <div class="search-form-container">
        <!-- Onglets de type de recherche -->
        <div class="search-form-tabs">
            <div class="search-form-tab active" data-type="flights">Vols</div>
            <div class="search-form-tab" data-type="hotels">Hôtels</div>
            <div class="search-form-tab" data-type="cars">Voitures</div>
        </div>

            <!-- Formulaire de recherche de vols -->
            <form action="/Test_Case_Kiwi/Booking_Flow/Searchs/Search_One_Way.php" method="post">
            <input type="hidden" name="route" value="search">
            <input type="hidden" name="type" value="flights" id="search-type">
            <input type="hidden" name="trip_type" value="round-trip" id="trip-type">

            <!-- Type de voyage (aller-retour, aller simple) -->
            <div class="search-form-row">
                <div class="trip-type-selector">
                    <button type="button" class="trip-type-button active" data-type="round-trip">Aller-retour</button>
                    <button type="button" class="trip-type-button" data-type="one-way">Aller simple</button>
                </div>

                <div class="traveler-selector">
                    <select name="cabin_class" id="cabin-class">
                        <option value="M">Économie</option>
                        <option value="W">Économie Premium</option>
                        <option value="B">Affaires</option>
                        <option value="F">Première</option>
                    </select>
                </div>
            </div>

            <!-- Origine et destination -->
            <div class="search-form-group">
                <label for="departure">De</label>
                <input type="text" id="fly_from" name="fly_from" placeholder="Ville, aéroport ou pays" required>
            </div>

            <div class="search-form-group">
                <label for="destination">À</label>
                <input type="text" id="fly_to" name="fly_to" placeholder="Ville, aéroport ou pays" required>
            </div>

            <div class="search-form-group">
                <label for="add-more">Ajouter une escale</label>
                <button type="button" id="add-more" class="btn-add-more">+ Ajouter</button>
            </div>

            <!-- Dates -->
            <div class="search-form-group">
                <label for="departure-date">Départ</label>
                <input type="date" id="date_from" name="date_from" required>
            </div>

            <div class="search-form-group return-date-group">
                <label for="return-date">Retour</label>
                <input type="date" id="date_to" name="date_to">
            </div>

                <!-- Passagers -->
                <div class="search-form-group">
                <label class="section-title">Passagers</label>
                <div class="input-row">
                    <div class="input-label">Adultes</div>
                    <div class="number-input">
                    <button type="button" class="decrement">−</button>
                    <input type="number" id="adults" name="adults" value="1" min="1" max="9" readonly>
                    <button type="button" class="increment">+</button>
                    </div>
                </div>

                <div class="input-row">
                    <div class="input-label">Enfants</div>
                    <div class="number-input">
                    <button type="button" class="decrement">−</button>
                    <input type="number" id="children" name="children" value="0" min="0" max="9" readonly>
                    <button type="button" class="increment">+</button>
                    </div>
                </div>

                <div class="input-row">
                    <div class="input-label">Bébés</div>
                    <div class="number-input">
                    <button type="button" class="decrement">−</button>
                    <input type="number" id="infant" name="infants" value="0" min="0" max="9" readonly>
                    <button type="button" class="increment">+</button>
                    </div>
                </div>
                </div>

                <!-- Bagages -->
                <div class="search-form-group">
                <label class="section-title">Bagages</label>
                <div class="input-row">
                    <div class="input-label">Bagage en soute</div>
                    <div class="number-input">
                    <button type="button" class="decrement">−</button>
                    <input type="number" name="hold_bags" value="0" min="0" max="5" readonly>
                    <button type="button" class="increment">+</button>
                    </div>
                </div>

                <div class="input-row">
                    <div class="input-label">Bagage cabine</div>
                    <div class="number-input">
                    <button type="button" class="decrement">−</button>
                    <input type="number" name="hand_bags" value="1" min="0" max="5" readonly>
                    <button type="button" class="increment">+</button>
                    </div>
                </div>
                </div>

                <!-- Submit Button -->
                <div class="search-form-group search-submit">
                <button type="submit" class="btn btn-primary btn-lg">Rechercher</button>
                </div>

        
        </form>

    </div>
</div>
<script>
  document.querySelectorAll('.number-input').forEach(wrapper => {
    const input = wrapper.querySelector('input');
    const increment = wrapper.querySelector('.increment');
    const decrement = wrapper.querySelector('.decrement');

    increment.addEventListener('click', () => {
      const max = parseInt(input.max);
      if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
      }
    });

    decrement.addEventListener('click', () => {
      const min = parseInt(input.min);
      if (parseInt(input.value) > min) {
        input.value = parseInt(input.value) - 1;
      }
    });
  });
</script>

<style>
  .search-form-group {
    margin-bottom: 30px;
  }

  .section-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
  }

  .input-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
    padding: 10px 15px;
    border-radius: 12px;
    background: #f9f9f9;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  }

  .input-label {
    font-weight: 500;
    color: #555;
  }

  .number-input {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .number-input input {
  width: 50px;
  height: 40px;
  text-align: center;
  font-size: 16px;
  font-weight: 500;
  border: 1px solid #ccc;
  border-radius: 8px;
  background-color: #fff;
  color: #333;
  padding: 0; /* Remove extra space */
  appearance: textfield; /* Remove browser-specific UI like spinners */
  box-shadow: none; /* Prevent inner white glow */
}

.number-input input::-webkit-outer-spin-button,
.number-input input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}


  .number-input button {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 8px;
    background-color: #e0e0e0;
    font-size: 20px;
    font-weight: bold;
    color: #333;
    cursor: pointer;
    transition: background 0.2s;
  }

  .number-input button:hover {
    background-color: #d4d4d4;
  }

  .search-options {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 20px;
  }

  .search-options label {
    font-size: 14px;
    color: #333;
    display: block;
    margin-bottom: 5px;
  }

  .search-submit button {
    padding: 12px 30px;
    font-size: 16px;
    border-radius: 10px;
  }
</style>

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
