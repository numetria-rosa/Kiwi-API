# Kiwi Flight Booking Website – Tequila API Integration

This project is a **fully functional flight booking website** that integrates the **Tequila API from Kiwi.com**. It allows users to search for and book low-cost flights using real-time flight data provided by Kiwi.

---

## 📁 Project Structure

```
kiwi_final-main/
├── airlines.sql
├── api/
│   └── tequila.php
├── assets/
│   ├── css/
│   │   ├── auth.css
│   │   ├── booking.css
│   │   ├── flight-details.css
│   │   ├── home.css
│   │   ├── reset.css
│   │   ├── responsive.css
│   │   ├── search.css
│   │   └── style.css
│   ├── fonts/
│   │   └── circular-pro.css
│   ├── img/
│   │   └── airlines/ (many airline images)
│   └── js/
│       ├── home.js
│       ├── main.js
│       └── search.js
├── car.json
├── composer.json
├── composer.lock
├── config/
│   ├── api_config.php
│   └── database.php
├── cv.py
├── includes/
│   ├── footer.php
│   ├── functions.php
│   └── header.php
├── index.php
├── kiwi.html
├── models/
│   ├── Airport.php
│   ├── Booking.php
│   ├── Location.php
│   └── User.php
├── project_structure.txt
├── README.md
├── sql/
│   └── database.sql
├── Test/
│   ├── api/
│   ├── assets/
│   ├── bootstrap/
│   ├── config/
│   ├── css/
│   ├── files/
│   ├── icons/
│   ├── images/
│   ├── includes/
│   ├── js/
│   ├── models/
│   ├── sql/
│   ├── views/
│   ├── Display_Flights.php
│   ├── index.php
│   ├── project_structure.txt
│   └── README.md
├── Test_Case_Kiwi/
│   └── Booking_Flow/
│       ├── Check_Flights.php
│       ├── Check_Flights_original.php
│       ├── Check_Flights_round.php
│       ├── Confirm_Payment.php
│       ├── confirm_payment_zooz.php
│       ├── Save_Booking.php
│       ├── Tokenize_Data.php
│       ├── Searchs/
│       │   ├── debug_raw_input.json
│       │   ├── error_log.txt
│       │   ├── flight_details_results.json
│       │   ├── Search_MultiCity.php
│       │   ├── Search_One_Way.php
│       │   └── Search_Round_Trip.php
│       └── Tests/
│           ├── test_check_flights.php
│           ├── test_confirm_pay.php
│           ├── test_multi.php
│           ├── test_round_trip.php
│           ├── test_save_booking.php
│           └── test_search_one_way.php
├── vendor/
│   ├── autoload.php
│   ├── composer/
│   ├── monolog/
│   └── psr/
├── views/
│   ├── airport_search.php
│   ├── booking.php
│   ├── flight_details.php
│   ├── home.php
│   ├── login.php
│   ├── register.php
│   ├── search_results.php
│   ├── test_airport.php
│   └── test_db.php
```

---

## 🚀 Installation

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd kiwi_final-main
   ```
2. **Install PHP dependencies:**
   If you use Composer (recommended):
   ```bash
   composer install
   ```
3. **Set up the database:**
   - Create a new MySQL database (e.g., `kiwi_flights`).
   - Import the schema:
     - Use a tool like phpMyAdmin or the MySQL CLI to import `sql/database.sql`.
4. **Configure environment:**
   - Copy your Tequila API key from Kiwi.com and set it in `config/api_config.php`.
   - Set your database credentials in `config/database.php`.
5. **Run the application:**
   - Use a local server (e.g., XAMPP, WAMP, MAMP) or PHP's built-in server:
     ```bash
     php -S localhost:8000
     ```
   - Open your browser and go to `http://localhost:8000` or the appropriate local address.

---

## ✅ Requirements

- PHP 7.4 or higher  
- MySQL 5.7 or higher  
- PHP PDO extension  
- PHP cURL extension

---

## ✈️ Tequila API Integration

This application uses the [Tequila API](https://tequila.kiwi.com/) by Kiwi.com to:

- Search flights in real time
- Fetch flight and location details
- Simulate flight bookings and payment flows
- Display booking confirmation

> You must create an account on the Kiwi.com Tequila portal to obtain an API key and configure it in `config/api_config.php`.

---

## 📄 License

This project is intended for **development purposes only**.
