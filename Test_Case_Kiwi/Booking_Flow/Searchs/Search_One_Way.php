<?php
session_start();

require_once __DIR__ . '/../../../vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$endpoint = "https://api.tequila.kiwi.com/v2";
$apikey = "15C5Z-ZDZJAlqfdlMGRpoFUQ0v09SQyN";

function sanitizeInput($value) {
    return ($value === "0" || $value === "" || $value === null) ? "" : $value;
}

$fly_from         = sanitizeInput($_GET['departure'] ?? "");
$fly_to           = sanitizeInput($_GET['destination'] ?? "");
$date_from        = sanitizeInput($_GET['departure_date'] ?? "");
$date_to          = $date_from;  // no range for one-way

$adults           = sanitizeInput($_GET['adults'] ?? "");
$children         = sanitizeInput($_GET['children'] ?? "");
$infants          = sanitizeInput($_GET['infants'] ?? "");

$cabin_class      = sanitizeInput($_GET['cabin_class'] ?? "M");
$mix_with_cabins  = ""; // Set this based on a checkbox or your logic if needed

$adult_hold_bag   = sanitizeInput($_GET['adult_hold_bag'] ?? "1");
$adult_hand_bag   = sanitizeInput($_GET['adult_hand_bag'] ?? "");
$child_hold_bag   = sanitizeInput($_GET['child_hold_bag'] ?? "");
$child_hand_bag   = sanitizeInput($_GET['child_hand_bag'] ?? "");

echo "<pre>";
print_r($_GET);
echo "</pre>";


function SearchFlightOneWay(
    $fly_from, $fly_to, $date_from, $date_to, $adults, $children, $infant,
    $selected_cabins, $mix_with_cabins, $adult_hold_bag , $adult_hand_bag,
    $child_hold_bag, $child_hand_bag, $limit = 500
) {
    global $endpoint, $apikey;

    $queryParams = [
        'fly_from' => $fly_from,
        'fly_to' => $fly_to,
        'date_from' => $date_from,
        'date_to' => $date_from, 
        'max_fly_duration' => 20,
        'ret_from_diff_city' => false, 
        'ret_to_diff_city' => false,
        'one_for_city' => 0,
        'one_per_date' => 0,
        'adults' => $adults,
        'children' => $children,
        'infant' => $infant,
        'selected_cabins' => $selected_cabins,
        'mix_with_cabins' => $mix_with_cabins,
        'adult_hold_bag' => $adult_hold_bag,
        'adult_hand_bag' => $adult_hand_bag,
        'child_hold_bag' => $child_hold_bag,
        'child_hand_bag' => $child_hand_bag,
        'only_working_days' => false,
        'only_weekends' => false,
        'partner_market' => 'us',
        'limit' => $limit
    ];

    $url = $endpoint . '/search?' . http_build_query($queryParams);

    // Log Request
    $requestLogger = new Logger('RQ_SearchFlightOneWay');
    $requestLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Searchs/One_Way/RQ_SearchFlightOneWay.txt', Logger::INFO));
    $requestLogger->info("Request URL: " . $url);
    $requestLogger->info("Query Parameters: " . json_encode($queryParams));

    // Curl request
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'apikey: ' . $apikey
        ],
    ]);

    $response = curl_exec($curl);

    // Log Response
    $responseLogger = new Logger('RS_SearchFlightOneWay');
    $responseLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Searchs/One_Way/RS_SearchFlightOneWay.txt', Logger::INFO));
    $responseLogger->info($response);

    curl_close($curl);

    return json_decode($response, true);
}


$result = SearchFlightOneWay(
    $fly_from, $fly_to, $date_from, $date_to,
    $adults, $children, $infants,
    $cabin_class, $mix_with_cabins,
    $adult_hold_bag, $adult_hand_bag,
    $child_hold_bag, $child_hand_bag
);


// echo "<pre>";
// print_r($result);
// echo "</pre>";

// echo '<div style="max-width: 1000px; margin: 30px auto; font-family: Arial, sans-serif;">';
// echo '<h2 style="text-align:center;">Search Results</h2>';
// echo '<p style="text-align:center;"><strong>Search ID:</strong> ' . htmlspecialchars($result['search_id']) . '</p>';

// foreach ($result['data'] as $flight) {
//     // Check if seats are null or not available
//     if (isset($flight['availability']['seats']) && $flight['availability']['seats'] !== null) {
//         $sessionId = $result['search_id']; // Assuming you want to pass the search ID as session ID
//         $bookingToken = isset($flight['booking_token']) ? htmlspecialchars($flight['booking_token']) : '';

//         // Create a link to Check_Flights.php with session_id and booking_token as URL parameters
//         $checkFlightsLink = '../Check_Flights.php?session_id=' . urlencode($sessionId) . '&booking_token=' . urlencode($bookingToken);

//         // Extract flight number and airline name (if available)
//         $flight_no = isset($flight['route'][0]['flight_no']) ? htmlspecialchars($flight['route'][0]['flight_no']) : 'N/A';
        
//         // Convert the flight times to DateTime objects
//         $departureDate = new DateTime($flight['local_departure']);
//         $arrivalDate = new DateTime($flight['local_arrival']);
        
//         // Format the dates and times
//         $formattedDeparture = $departureDate->format('l, F j, Y \a\t g:i A');
//         $formattedArrival = $arrivalDate->format('l, F j, Y \a\t g:i A');
        
//         $airline_name = isset($flight['route'][0]['airline']) ? htmlspecialchars($flight['route'][0]['airline']) : 'N/A';

//         echo '
//         <a href="' . $checkFlightsLink . '" style="text-decoration: none; color: inherit;">
//             <div style="
//                 margin: 20px auto;
//                 padding: 20px;
//                 border: 1px solid #ddd;
//                 border-radius: 10px;
//                 background-color: #f9f9f9;
//                 box-shadow: 0 4px 10px rgba(0,0,0,0.1);
//                 transition: transform 0.2s, box-shadow 0.2s;
//             " onmouseover="this.style.transform=\'scale(1.02)\'; this.style.boxShadow=\'0 6px 15px rgba(0,0,0,0.2)\';" onmouseout="this.style.transform=\'scale(1)\'; this.style.boxShadow=\'0 4px 10px rgba(0,0,0,0.1)\';">
//                 <p><strong>From:</strong> ' . htmlspecialchars($flight['cityFrom']) . ' (' . htmlspecialchars($flight['flyFrom']) . ')</p>
//                 <p><strong>To:</strong> ' . htmlspecialchars($flight['cityTo']) . ' (' . htmlspecialchars($flight['flyTo']) . ')</p>
//                 <p><strong>Departure:</strong> ' . $formattedDeparture . '</p>
//                 <p><strong>Arrival:</strong> ' . $formattedArrival . '</p>
//                 <p><strong>Flight Number:</strong> ' . $flight_no . '</p>
//                 <p><strong>Airline:</strong> ' . $airline_name . '</p>
//                 <p><strong>Price:</strong> ' . htmlspecialchars($flight['price']) . ' ' . htmlspecialchars($result['currency']) . '</p>';

//         if (isset($flight['availability'])) {
//             echo '<p><strong>Available Seats:</strong> ' . 
//                 (isset($flight['availability']['seats']) ? htmlspecialchars($flight['availability']['seats']) : 'Not specified') . 
//                 '</p>';
//         } else {
//             echo '<p><strong>Available Seats:</strong> Not specified</p>';
//         }

//         if (isset($flight['booking_token'])) {
//             echo '
//                 <p><strong>Booking Token:</strong></p>
//                 <code style="display:block; word-wrap:break-word; background:#eee; padding:10px; border-radius:5px;">' 
//                 . htmlspecialchars($flight['booking_token']) . '</code>';
//         } else {
//             echo '<p style="color: red;">Booking token not found.</p>';
//         }

//         echo '</div></a>';
//     }
// }

// echo '</div>';

//Save the result in session
$_SESSION['flight_result'] = $result;

$_SESSION['adults'] = $adults;
$_SESSION['children'] = $children;
$_SESSION['infants'] = $infants;
$_SESSION['departure'] = $fly_from;
$_SESSION['destination'] = $fly_to;
$_SESSION['departure_date'] = $date_from;
$_SESSION['return_date'] = $date_to; // for round-trip, otherwise can be same as departure
$_SESSION['trip_type'] = 'one-way'; // or from GET if you want to support both
$_SESSION['cabin_class'] = $cabin_class;
$_SESSION['adult_hand_bag'] = $adult_hand_bag;
$_SESSION['adult_hold_bag'] = $adult_hold_bag;
$_SESSION['child_hand_bag'] = $child_hand_bag;
$_SESSION['child_hold_bag'] = $child_hold_bag;

$adult_hold = is_numeric($adult_hold_bag) ? (int)$adult_hold_bag * (int)$adults : 0;
$adult_hand = is_numeric($adult_hand_bag) ? (int)$adult_hand_bag * (int)$adults : 0;
$child_hold = is_numeric($child_hold_bag) ? (int)$child_hold_bag * (int)$children : 0;
$child_hand = is_numeric($child_hand_bag) ? (int)$child_hand_bag * (int)$children : 0;

$_SESSION['total_bags'] = $adult_hold + $adult_hand + $child_hold + $child_hand;
// Redirect to the display page
header("Location: ../../../Test/views/search_results.php");

exit;

?>

