<?php

require_once __DIR__ . '/../../vendor/autoload.php';
session_start();

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$endpoint = "https://api.tequila.kiwi.com/v2";
$apikey = "15C5Z-ZDZJAlqfdlMGRpoFUQ0v09SQyN";
$visitorUniqId = "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855";

// Retrieve session_id and booking_token from the URL parameters
$sessionId = $_GET['search_id'] ?? null;  // Using 'search_id' from the URL
$bookingToken = $_GET['booking_token'] ?? null;  // Using 'booking_token' from the URL

// Store data in session
$_SESSION['booking_data'] = [
    'visitorUniqId' => $visitorUniqId,
    'sessionId' => $sessionId,
    'bookingToken' => $bookingToken
];

$adults = $_GET['adults'] ?? "";
$children = $_GET['children'] ?? "";
$infants = $_GET['infants'] ?? "";

// Convert string values to integers, defaulting to 0 if empty
$adults = empty($adults) ? 0 : (int)$adults;
$children = empty($children) ? 0 : (int)$children;
$infants = empty($infants) ? 0 : (int)$infants;

$nbPassengers = $adults + $children + $infants;

// Store passenger counts in session
$_SESSION['passenger_counts'] = [
    'adults' => $adults,
    'children' => $children,
    'infants' => $infants,
    'total' => $nbPassengers
];

$currency = 'EUR';
// $bnum  = null;

$bnum =  $_GET['bags'] ?? "";

if ($sessionId && $bookingToken) {
    echo "Session ID: " . htmlspecialchars($sessionId) . "<br><hr>";
    echo "Booking Token: " . htmlspecialchars($bookingToken) . "<br><hr>";
    CheckFlights($bookingToken, $sessionId, $visitorUniqId, $adults, $children, $infants, $currency, $bnum);
} else {
    echo "Session ID or Booking Token is missing.";
}

function CheckFlights($bookingToken, $sessionId, $visitorUniqId, $adults, $children, $infants, $currency, $bnum, $max_attempts = 10, $interval = 3)
{
    global $endpoint, $apikey;

    $rqLogger = new Logger('RQ_CheckFlights');
    $rqLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Check_Flights/RQ_CheckFlights.txt', Logger::INFO));

    $rsLogger = new Logger('RS_CheckFlights');
    $rsLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Check_Flights/RS_CheckFlights.txt', Logger::INFO));

    for ($attempt = 1; $attempt <= $max_attempts; $attempt++) {
        echo "<strong>⏳ Attempt #$attempt:</strong><br>";

        $params = [
            'booking_token'   => $bookingToken,
            'bnum'            => $bnum,
            'adults'          => $adults,
            'children'        => $children,
            'infants'         => $infants,
            'session_id'      => $sessionId,
            'currency'        => $currency,
            'visitor_uniqid'  => $visitorUniqId
        ];

        $url = $endpoint . '/booking/check_flights?' . http_build_query($params);

        $rqLogger->info("Attempt #$attempt");
        $rqLogger->info("Request URL: $url");

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "apikey: $apikey",
                "accept: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        $rsLogger->info("Response: $response");

        $data = json_decode($response, true);

        if (
            isset($data['flights_checked'], $data['price_change'], $data['flights_invalid']) &&
            $data['flights_checked'] === true &&
            $data['price_change'] === false &&
            $data['flights_invalid'] === false
        ) {
            echo "<hr><strong>✅ Flights checked successfully and conditions met: ['flights_checked'] = true / ['price_change'] = false / ['flights_invalid'] = false  </strong><br>";
            echo "<hr>";
        
            // Prepare the data payload for the session. Start with the API response.
            $session_payload = $data;

            // Attempt to find the original, more detailed flight 'route' (segments) 
            // from the initial search results, which should be in $_SESSION['flight_result']['data']
            $original_detailed_route = null;
            // $bookingToken is available in this function's scope as a parameter
            if (isset($_SESSION['flight_result']['data']) && is_array($_SESSION['flight_result']['data'])) {
                foreach ($_SESSION['flight_result']['data'] as $search_flight_offer) {
                    if (isset($search_flight_offer['booking_token']) && $search_flight_offer['booking_token'] === $bookingToken) {
                        if (isset($search_flight_offer['route']) && is_array($search_flight_offer['route']) && !empty($search_flight_offer['route'])) {
                            $original_detailed_route = $search_flight_offer['route'];
                        }
                        break; 
                    }
                }
            }

            if ($original_detailed_route) {
                // Inject the detailed route from search results into the session payload.
                // flight_details.php will look for 'route' at the top level of $_SESSION['data'].
                $session_payload['route'] = $original_detailed_route;

                // If the check_flights API response ($data) also has a $data['flights'][0]['route'] structure,
                // update it too for consistency, though flight_details.php prioritizes the top-level 'route'.
                if (isset($session_payload['flights'][0]) && is_array($session_payload['flights'][0])) {
                    $session_payload['flights'][0]['route'] = $original_detailed_route;
                }
                $rsLogger->info("Merged original detailed route into session payload for booking token: $bookingToken");
            } else {
                $rsLogger->warning("Original detailed route not found in search_results for booking token: $bookingToken. Session payload will use route from check_flights API if available.");
            }
        
            // Store the (potentially enhanced) data array in session
            $_SESSION['data'] = $session_payload; 
        
            // Redirect to flight_details.php after storing the data
            header('Location: /Test/views/flight_details.php');
            exit;
        } 
    }
}

?>
