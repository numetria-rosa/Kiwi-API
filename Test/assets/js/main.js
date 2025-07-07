/**
 * Script principal pour le projet Kiwi
 */

// Attendre que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    // Menu mobile
    const menuToggle = document.querySelector('.menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });
    }

    // Sélecteur de type de voyage (aller-retour, aller simple)
    const tripTypeButtons = document.querySelectorAll('.trip-type-button');

    if (tripTypeButtons.length > 0) {
        tripTypeButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Retirer la classe active de tous les boutons
                tripTypeButtons.forEach(btn => btn.classList.remove('active'));

                // Ajouter la classe active au bouton cliqué
                this.classList.add('active');

                // Mettre à jour le champ caché
                const tripTypeInput = document.querySelector('#trip-type');
                if (tripTypeInput) {
                    tripTypeInput.value = this.dataset.type;
                }

                // Afficher/masquer le champ date de retour
                const returnDateGroup = document.querySelector('.return-date-group');
                if (returnDateGroup) {
                    returnDateGroup.style.display = this.dataset.type === 'round-trip' ? 'block' : 'none';
                }
            });
        });
    }

    // Gestion des dates
    const dateInputs = document.querySelectorAll('input[type="date"]');

    if (dateInputs.length > 0) {
        dateInputs.forEach(input => {
            // Définir la date minimale (aujourd'hui)
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');

            input.setAttribute('min', `${yyyy}-${mm}-${dd}`);

            // Si c'est la date de départ, mettre à jour la date minimale du retour
            if (input.id === 'departure-date') {
                input.addEventListener('change', function() {
                    const returnDateInput = document.querySelector('#return-date');
                    if (returnDateInput) {
                        returnDateInput.setAttribute('min', this.value);

                        // Si la date de retour est antérieure à la nouvelle date de départ, la mettre à jour
                        if (returnDateInput.value && returnDateInput.value < this.value) {
                            returnDateInput.value = this.value;
                        }
                    }
                });
            }
        });
    }

    // Formulaire de recherche
    const searchForm = document.querySelector('.search-form');

    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validation des champs
            const departureInput = document.querySelector('#departure');
            const destinationInput = document.querySelector('#destination');
            const departureDateInput = document.querySelector('#departure-date');

            let isValid = true;

            if (!departureInput.value.trim()) {
                showError(departureInput, 'Veuillez saisir un lieu de départ');
                isValid = false;
            } else {
                clearError(departureInput);
            }

            if (!destinationInput.value.trim()) {
                showError(destinationInput, 'Veuillez saisir une destination');
                isValid = false;
            } else {
                clearError(destinationInput);
            }

            if (!departureDateInput.value) {
                showError(departureDateInput, 'Veuillez sélectionner une date de départ');
                isValid = false;
            } else {
                clearError(departureDateInput);
            }

            // Si le formulaire est valide, le soumettre
            if (isValid) {
                this.submit();
            }
        });
    }

    // Fonction pour afficher une erreur
    function showError(input, message) {
        const formGroup = input.closest('.search-form-group');
        const errorElement = formGroup.querySelector('.error-message') || document.createElement('div');

        errorElement.className = 'error-message';
        errorElement.textContent = message;

        if (!formGroup.querySelector('.error-message')) {
            formGroup.appendChild(errorElement);
        }

        input.classList.add('error');
    }

    // Fonction pour effacer une erreur
    function clearError(input) {
        const formGroup = input.closest('.search-form-group');
        const errorElement = formGroup.querySelector('.error-message');

        if (errorElement) {
            formGroup.removeChild(errorElement);
        }

        input.classList.remove('error');
    }
});
