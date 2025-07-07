document.addEventListener('DOMContentLoaded', function() {
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
    flightForm.addEventListener('submit', function() {
        const inputs = flightForm.querySelectorAll('input[type="number"]');
        inputs.forEach(input => {
            if (input.value === "0") {
                input.value = "";
            }
        });

        // Check if the adult_hold_bag is empty, if so, set it to 1 by default
        const adultHoldBagInput = document.getElementById('adult_hold_bag');
        if (!adultHoldBagInput.value) {
            adultHoldBagInput.value = 1; // Set default value to 1 if it's not selected
        }
    });

    // Trigger initial state
    if (tripTypeInput.value === "one-way") {
        flightForm.action = oneWayAction;
        document.querySelector('.return-date-group').style.display = "none";
    } else {
        flightForm.action = roundTripAction;
        document.querySelector('.return-date-group').style.display = "block";
    }
});