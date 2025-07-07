/**
 * Script pour la page de résultats de recherche
 */

document.addEventListener('DOMContentLoaded', function() {
    // Gestion du slider de prix
    const priceRange = document.getElementById('price-range');
    const priceValue = document.getElementById('price-value');

    if (priceRange && priceValue) {
        // Mettre à jour l'affichage du prix lorsque le slider est déplacé
        priceRange.addEventListener('input', function() {
            priceValue.textContent = this.value + ' €';
        });

        // Filtrer les résultats lorsque le slider est relâché
        priceRange.addEventListener('change', function() {
            filterResults();
        });
    }

    // Gestion des filtres par cases à cocher
    const checkboxFilters = document.querySelectorAll('.filter-group input[type="checkbox"]');

    if (checkboxFilters.length > 0) {
        checkboxFilters.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                filterResults();
            });
        });
    }

    // Tri des résultats
    const sortSelect = document.getElementById('sort-by');

    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortResults(this.value);
        });
    }

    // Boutons de détails des vols
    const detailButtons = document.querySelectorAll('.btn-details');

    if (detailButtons.length > 0) {
        detailButtons.forEach(button => {
            button.addEventListener('click', function() {
                const flightCard = this.closest('.flight-card');
                toggleFlightDetails(flightCard);
            });
        });
    }

    // Bouton pour charger plus de résultats
    const loadMoreButton = document.querySelector('.load-more button');

    if (loadMoreButton) {
        loadMoreButton.addEventListener('click', function() {
            loadMoreResults();
        });
    }
});

/**
 * Filtrer les résultats en fonction des critères sélectionnés
 */
function filterResults() {
    console.log('Filtrage des résultats...');

    // Récupérer la valeur du slider de prix
    const priceRange = document.getElementById('price-range');
    const maxPrice = priceRange ? priceRange.value : 500;

    // Récupérer les compagnies aériennes sélectionnées
    const selectedAirlines = [];
    const airlineCheckboxes = document.querySelectorAll('input[name="airline"]:checked');

    airlineCheckboxes.forEach(checkbox => {
        selectedAirlines.push(checkbox.value);
    });

    // Récupérer les types d'escales sélectionnés
    const selectedStops = [];
    const stopCheckboxes = document.querySelectorAll('input[name="stops"]:checked');

    stopCheckboxes.forEach(checkbox => {
        selectedStops.push(parseInt(checkbox.value));
    });

    // Récupérer les horaires de départ sélectionnés
    const selectedTimes = [];
    const timeCheckboxes = document.querySelectorAll('input[name="departure_time"]:checked');

    timeCheckboxes.forEach(checkbox => {
        selectedTimes.push(checkbox.value);
    });

    // Dans un cas réel, ces filtres seraient envoyés au serveur via AJAX
    // Pour cet exemple, nous allons simuler un filtrage côté client
    const flightCards = document.querySelectorAll('.flight-card');

    flightCards.forEach(card => {
        const price = parseFloat(card.querySelector('.price-amount').textContent.replace(/[^0-9,.]/g, '').replace(',', '.'));

        // Vérifier si le prix est inférieur au maximum
        const matchesPrice = price <= maxPrice;

        // Pour un exemple plus complet, nous devrions également vérifier les autres critères
        // (compagnies, escales, horaires) mais nous simplifions pour cet exemple

        if (matchesPrice) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

/**
 * Trier les résultats en fonction du critère sélectionné
 *
 * @param {string} sortCriterion Critère de tri
 */
function sortResults(sortCriterion) {
    console.log('Tri des résultats par', sortCriterion);

    const resultsContainer = document.querySelector('.search-results-list');
    const flightCards = Array.from(document.querySelectorAll('.flight-card'));

    // Fonction de comparaison pour trier par prix croissant
    const sortByPriceAsc = (a, b) => {
        const priceA = parseFloat(a.querySelector('.price-amount').textContent.replace(/[^0-9,.]/g, '').replace(',', '.'));
        const priceB = parseFloat(b.querySelector('.price-amount').textContent.replace(/[^0-9,.]/g, '').replace(',', '.'));
        return priceA - priceB;
    };

    // Fonction de comparaison pour trier par prix décroissant
    const sortByPriceDesc = (a, b) => {
        return sortByPriceAsc(b, a);
    };

    // Fonction de comparaison pour trier par durée
    const sortByDurationAsc = (a, b) => {
        const durationA = a.querySelector('.flight-duration').textContent;
        const durationB = b.querySelector('.flight-duration').textContent;

        // Convertir la durée en minutes pour faciliter la comparaison
        const minutesA = convertDurationToMinutes(durationA);
        const minutesB = convertDurationToMinutes(durationB);

        return minutesA - minutesB;
    };

    // Fonction de comparaison pour trier par heure de départ
    const sortByDepartureAsc = (a, b) => {
        const departureA = a.querySelector('.departure-time').textContent;
        const departureB = b.querySelector('.departure-time').textContent;

        // Convertir l'heure en minutes depuis minuit
        const minutesA = convertTimeToMinutes(departureA);
        const minutesB = convertTimeToMinutes(departureB);

        return minutesA - minutesB;
    };

    // Trier les cartes en fonction du critère sélectionné
    switch (sortCriterion) {
        case 'price_asc':
            flightCards.sort(sortByPriceAsc);
            break;
        case 'price_desc':
            flightCards.sort(sortByPriceDesc);
            break;
        case 'duration_asc':
            flightCards.sort(sortByDurationAsc);
            break;
        case 'departure_asc':
            flightCards.sort(sortByDepartureAsc);
            break;
    }

    // Retirer les cartes actuelles
    flightCards.forEach(card => card.remove());

    // Ajouter les cartes triées avant le bouton "Charger plus"
    const loadMoreButton = resultsContainer.querySelector('.load-more');

    flightCards.forEach(card => {
        resultsContainer.insertBefore(card, loadMoreButton);
    });
}

/**
 * Convertir une durée au format "HH:MM" en minutes
 *
 * @param {string} duration Durée au format "HH:MM"
 * @return {number} Durée en minutes
 */
function convertDurationToMinutes(duration) {
    const parts = duration.split(':');
    return parseInt(parts[0]) * 60 + parseInt(parts[1]);
}

/**
 * Convertir une heure au format "HH:MM" en minutes depuis minuit
 *
 * @param {string} time Heure au format "HH:MM"
 * @return {number} Minutes depuis minuit
 */
function convertTimeToMinutes(time) {
    const parts = time.split(':');
    return parseInt(parts[0]) * 60 + parseInt(parts[1]);
}

/**
 * Afficher/masquer les détails d'un vol
 *
 * @param {Element} flightCard Carte de vol
 */
function toggleFlightDetails(flightCard) {
    // Dans un cas réel, cette fonction afficherait des détails supplémentaires
    // comme les informations de bagages, les conditions tarifaires, etc.
    const detailsButton = flightCard.querySelector('.btn-details');

    if (detailsButton.classList.contains('active')) {
        detailsButton.classList.remove('active');
        detailsButton.textContent = 'Voir les détails';

        // Supprimer la section de détails si elle existe
        const detailsSection = flightCard.querySelector('.flight-extended-details');
        if (detailsSection) {
            detailsSection.remove();
        }
    } else {
        detailsButton.classList.add('active');
        detailsButton.textContent = 'Masquer les détails';

        // Créer et ajouter une section de détails
        const detailsSection = document.createElement('div');
        detailsSection.className = 'flight-extended-details';
        detailsSection.innerHTML = `
            <div class="flight-extended-details-content">
                <div class="flight-details-section">
                    <h4>Informations tarifaires</h4>
                    <p>Ce tarif inclut :</p>
                    <ul>
                        <li>Bagage à main</li>
                        <li>Bagage en cabine</li>
                    </ul>
                    <p>Options payantes :</p>
                    <ul>
                        <li>Bagage en soute : à partir de 30 €</li>
                        <li>Choix du siège : à partir de 10 €</li>
                    </ul>
                </div>
                <div class="flight-details-section">
                    <h4>Conditions tarifaires</h4>
                    <p>Billet non remboursable</p>
                    <p>Modifications possibles avec frais</p>
                </div>
            </div>
        `;

        flightCard.appendChild(detailsSection);
    }
}

/**
 * Charger plus de résultats
 */
function loadMoreResults() {
    // Dans un cas réel, cette fonction ferait une requête AJAX pour récupérer
    // plus de résultats du serveur. Pour cet exemple, nous allons simuler cela.
    const loadMoreButton = document.querySelector('.load-more button');

    if (loadMoreButton) {
        loadMoreButton.textContent = 'Chargement...';
        loadMoreButton.disabled = true;

        // Simuler un délai de chargement
        setTimeout(() => {
            loadMoreButton.textContent = 'Charger plus de résultats';
            loadMoreButton.disabled = false;

            // Afficher un message indiquant qu'il n'y a plus de résultats
            const noMoreResults = document.createElement('p');
            noMoreResults.className = 'no-more-results';
            noMoreResults.textContent = 'Tous les résultats ont été chargés.';

            document.querySelector('.load-more').replaceWith(noMoreResults);
        }, 1500);
    }
}
