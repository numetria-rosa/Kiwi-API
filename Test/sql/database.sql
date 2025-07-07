-- Base de données pour le projet Kiwi

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS kiwi_clone;
USE kiwi_clone;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    country VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des compagnies aériennes
CREATE TABLE IF NOT EXISTS airlines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(5) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    logo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des aéroports
CREATE TABLE IF NOT EXISTS airports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(5) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des recherches de vols sauvegardées
CREATE TABLE IF NOT EXISTS saved_searches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    departure VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    departure_date DATE NOT NULL,
    return_date DATE,
    trip_type ENUM('round-trip', 'one-way') NOT NULL DEFAULT 'round-trip',
    cabin_class ENUM('economy', 'premium_economy', 'business', 'first') NOT NULL DEFAULT 'economy',
    adults INT NOT NULL DEFAULT 1,
    children INT NOT NULL DEFAULT 0,
    infants INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des alertes de prix
CREATE TABLE IF NOT EXISTS price_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    departure VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    departure_date DATE NOT NULL,
    return_date DATE,
    trip_type ENUM('round-trip', 'one-way') NOT NULL DEFAULT 'round-trip',
    cabin_class ENUM('economy', 'premium_economy', 'business', 'first') NOT NULL DEFAULT 'economy',
    max_price DECIMAL(10, 2) NOT NULL,
    alert_email BOOLEAN NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table des réservations
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    booking_reference VARCHAR(20) NOT NULL UNIQUE,
    total_price DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'EUR',
    status ENUM('pending', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending',
    payment_method VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des vols réservés
CREATE TABLE IF NOT EXISTS booked_flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    airline_code VARCHAR(5) NOT NULL,
    flight_number VARCHAR(10) NOT NULL,
    departure_airport VARCHAR(5) NOT NULL,
    arrival_airport VARCHAR(5) NOT NULL,
    departure_datetime DATETIME NOT NULL,
    arrival_datetime DATETIME NOT NULL,
    cabin_class ENUM('economy', 'premium_economy', 'business', 'first') NOT NULL DEFAULT 'economy',
    is_return BOOLEAN NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Table des passagers
CREATE TABLE IF NOT EXISTS passengers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    passport_number VARCHAR(50),
    passport_expiry DATE,
    nationality VARCHAR(100),
    is_primary BOOLEAN NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

-- Table des options additionnelles (bagages, sièges, etc.)
CREATE TABLE IF NOT EXISTS booking_extras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    passenger_id INT NOT NULL,
    flight_id INT NOT NULL,
    extra_type ENUM('baggage', 'seat', 'meal', 'insurance') NOT NULL,
    extra_details VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'EUR',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (passenger_id) REFERENCES passengers(id) ON DELETE CASCADE,
    FOREIGN KEY (flight_id) REFERENCES booked_flights(id) ON DELETE CASCADE
);

-- Insertion de quelques compagnies aériennes
INSERT INTO airlines (code, name, logo) VALUES
('AF', 'Air France', 'af.png'),
('BA', 'British Airways', 'ba.png'),
('LH', 'Lufthansa', 'lh.png'),
('U2', 'EasyJet', 'u2.png'),
('FR', 'Ryanair', 'fr.png'),
('IB', 'Iberia', 'ib.png'),
('VY', 'Vueling', 'vy.png'),
('KL', 'KLM', 'kl.png');

-- Insertion de quelques aéroports populaires
INSERT INTO airports (code, name, city, country, latitude, longitude) VALUES
('CDG', 'Charles de Gaulle', 'Paris', 'France', 49.009722, 2.547778),
('ORY', 'Orly', 'Paris', 'France', 48.725278, 2.359444),
('LHR', 'Heathrow', 'Londres', 'Royaume-Uni', 51.477500, -0.461389),
('LGW', 'Gatwick', 'Londres', 'Royaume-Uni', 51.148056, -0.190278),
('MAD', 'Adolfo Suárez Madrid-Barajas', 'Madrid', 'Espagne', 40.472222, -3.560833),
('BCN', 'Barcelona-El Prat', 'Barcelone', 'Espagne', 41.297222, 2.078333),
('FCO', 'Leonardo da Vinci–Fiumicino', 'Rome', 'Italie', 41.800833, 12.238889),
('AMS', 'Amsterdam Schiphol', 'Amsterdam', 'Pays-Bas', 52.308056, 4.764167),
('FRA', 'Frankfurt am Main', 'Francfort', 'Allemagne', 50.033333, 8.570556),
('MUC', 'Munich', 'Munich', 'Allemagne', 48.353889, 11.786111);
