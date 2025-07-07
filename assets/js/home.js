document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("flight-search-form");

    form.addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        let alertMessage = "=== Données envoyées depuis home.php ===\n";

        // Prepare alert message with form data
        for (let [key, value] of formData.entries()) {
            alertMessage += `${key}: ${value}\n`;
        }

        // Show the first alert with form data
        alert(alertMessage);

        // Convert date to dd/mm/yyyy format
        function formatDate(dateString) {
            const [year, month, day] = dateString.split('-');
            return `${day}/${month}/${year}`;
        }

        // Prepare data to send to Search_One_Way.php
        const data = {
            fly_from: formData.get("departure"),
            fly_to: formData.get("destination"),
            date_from: formatDate(formData.get("departure_date")), // Format the date here
            date_to: formatDate(formData.get("departure_date")), // Format the date here
            adults: parseInt(formData.get("adults")) || "",
            children: parseInt(formData.get("children")) || "",
            infant: parseInt(formData.get("infants")) || "",
            selected_cabins: formData.get("cabin_class") || "M", // updated to use selected value
            mix_with_cabins: "",
            adult_hold_bag: "",
            adult_hand_bag: "",
            child_hold_bag: "",
            child_hand_bag: ""
        };

        // Remove 0s as empty strings
        for (let key in data) {
            if (data[key] === 0) data[key] = "";
        }

        // Create alert message for data being sent to Search_One_Way.php
        let sendMessage = "=== Données envoyées à Search_One_Way.php ===\n";
        for (let key in data) {
            sendMessage += `${key}: ${data[key]}\n`;
        }
        console.log(sendMessage);

        // Show the second alert with prepared data
        alert(sendMessage);

        // Log the form data
        console.log("=== RAW FORM DATA FROM home.php ===");
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        // Send the data to the server
        fetch("http://localhost/Test_Case_Kiwi/Booking_Flow/Searchs/Search_One_Way.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
            .then((res) => res.json())
            .then((json) => {
                const resultDiv = document.getElementById("search-results");
                resultDiv.innerHTML = "";

                if (json.data && json.data.length > 0) {
                    json.data.forEach(flight => {
                        const flightInfo = `
                        <div class="flight-result">
                            <p><strong>De:</strong> ${flight.cityFrom} (${flight.flyFrom})</p>
                            <p><strong>À:</strong> ${flight.cityTo} (${flight.flyTo})</p>
                            <p><strong>Prix:</strong> ${flight.price} €</p>
                            <p><strong>Heure de départ:</strong> ${new Date(flight.dTime * 1000).toLocaleString()}</p>
                            <p><strong>Heure d'arrivée:</strong> ${new Date(flight.aTime * 1000).toLocaleString()}</p>
                            <hr>
                        </div>
                        `;
                        resultDiv.insertAdjacentHTML("beforeend", flightInfo);
                    });
                } else {
                    resultDiv.innerHTML = "<p>Aucun vol trouvé.</p>";
                }
            })
            .catch((err) => {
                console.error("Erreur:", err);
                document.getElementById("search-results").innerHTML = "<p>Une erreur est survenue lors de la recherche.</p>";
            });
    });
});