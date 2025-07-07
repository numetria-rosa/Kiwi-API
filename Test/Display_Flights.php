<?php

// Include the file that contains the SearchFlightOneWay function
require_once 'C:/xampp/htdocs/Test_Case_Kiwi/Booking_Flow/Searchs/Search_One_Way.php';

// Call the search function
$searchResults = SearchFlightOneWay(
    'TUN', 'ORY', '30/04/2025', '30/04/2025', 1, '', '', 
    'M', '', 1, 1, '', ''
);

// Prepare $results array for the view
$results = [];

foreach ($searchResults['data'] as $flight) {
    $departureDate = new DateTime($flight['local_departure']);
    $arrivalDate = new DateTime($flight['local_arrival']);
    $duration = $departureDate->diff($arrivalDate)->format('%hh %imins');

    $results[] = [
        'departure_time' => $departureDate->format('H:i'),
        'arrival_time' => $arrivalDate->format('H:i'),
        'departure_airport' => $flight['flyFrom'],
        'arrival_airport' => $flight['flyTo'],
        'departure_city' => $flight['cityFrom'],
        'arrival_city' => $flight['cityTo'],
        'duration' => $duration,
        'stops' => count($flight['route']) - 1, // Number of stops
        'price' => $flight['price'],
        'currency' => $searchResults['currency'],
        'booking_token' => $flight['booking_token'] ?? ''
    ];
}

// Pass $results to the view
include 'C:/xampp/htdocs/Test/views/search_results.php';
