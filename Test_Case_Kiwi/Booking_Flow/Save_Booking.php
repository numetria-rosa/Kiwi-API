<?php 

require_once __DIR__ . '/../../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$endpoint = "https://api.tequila.kiwi.com/v2";
$apikey = "15C5Z-ZDZJAlqfdlMGRpoFUQ0v09SQyN";

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $visitorUniqId = $_POST['visitor_uniqid'] ?? '';
    $bookingToken = $_POST['booking_token'] ?? '';
    $sessionId = $_POST['session_id'] ?? '';
    $passengers = isset($_POST['passengers']) ? json_decode($_POST['passengers'], true) : [];
    $baggage = json_decode($_POST['baggage'], true) ?? [];

    if (empty($visitorUniqId) || empty($bookingToken) || empty($sessionId)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Missing required parameters']);
        exit;
    }

    // Call SaveBooking function
    $result = SaveBooking($apikey, $visitorUniqId, $bookingToken, $sessionId, $passengers, $baggage);
    
    // Set JSON header
    header('Content-Type: application/json');
    
    // Return the result as JSON
    echo json_encode($result);
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

function SaveBooking($apiKey, $visitorUniqId, $bookingToken, $sessionId, $passengers, $baggage, $lang = "en", $locale = "en", $healthDeclarationChecked = true)

{


    global $endpoint , $apikey;

    // Set up Monolog Logger
    $rqLogger = new Logger('RQ_SaveBooking');
    $rqLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Save_Booking/RQ_SaveBooking.txt', Logger::INFO));

    $rsLogger = new Logger('RS_SaveBooking');
    $rsLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Save_Booking/RS_SaveBooking.txt', Logger::INFO));

    $curl = curl_init();

    $postData = [
        "health_declaration_checked" => $healthDeclarationChecked,
        "lang" => $lang,
        "locale" => $locale,
        "booking_token" => $bookingToken,
        "session_id" => $sessionId,
        "passengers" => $passengers,
        "baggage" => $baggage,
        "payment_gateway" => "payu"
    ];

    $url = $endpoint . '/booking/save_booking';

    // Log the request
    $rqLogger->info("Sending SaveBooking Request:");
    $rqLogger->info("Request URL: $url");
    $rqLogger->info("Post Data: " . json_encode($postData));
    $rqLogger->info("Headers: " . json_encode([
        'apikey: ' . $apiKey,
        'visitor_uniqid: ' . $visitorUniqId,
        'Content-Type: application/json'
    ]));

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
            'apikey: ' . $apiKey,
            'visitor_uniqid: ' . $visitorUniqId,
            'Content-Type: application/json'
        ),
        CURLOPT_SSL_VERIFYPEER => false, // For testing only
        CURLOPT_SSL_VERIFYHOST => false  // For testing only
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    $errno = curl_errno($curl);
    curl_close($curl);

    // Log the response
    $rsLogger->info("SaveBooking Response (HTTP $httpCode):");
    $rsLogger->info("Response: " . $response);
    if ($error) {
        $rsLogger->error("CURL Error: " . $error);
        $rsLogger->error("CURL Error Number: " . $errno);
    }

    // If there was a CURL error
    if ($error) {
        return [
            'error' => true,
            'message' => 'CURL Error: ' . $error,
            'curl_errno' => $errno,
            'http_code' => $httpCode
        ];
    }

    // If there was an error with the API call
    if ($httpCode !== 200) {
        return [
            'error' => true,
            'message' => 'API call failed',
            'http_code' => $httpCode,
            'response' => json_decode($response, true)
        ];
    }

    $result = json_decode($response, true);
    
    // If JSON decode failed
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'error' => true,
            'message' => 'Invalid JSON response: ' . json_last_error_msg(),
            'raw_response' => $response
        ];
    }

    // Extract the required fields from the response
    $bookingId = $result['booking_id'] ?? null;
    $payuToken = $result['payu_token'] ?? null;

    // If we don't have the required fields, try to find them in the response
    if (!$bookingId || !$payuToken) {
        // Log the full response structure for debugging
        $rsLogger->info("Full response structure: " . json_encode($result, JSON_PRETTY_PRINT));
        
        // Try to find the fields in different possible locations
        if (isset($result['data'])) {
            $bookingId = $result['data']['booking_id'] ?? $bookingId;
            $payuToken = $result['data']['payu_token'] ?? $payuToken;
        }
        
        if (isset($result['result'])) {
            $bookingId = $result['result']['booking_id'] ?? $bookingId;
            $payuToken = $result['result']['payu_token'] ?? $payuToken;
        }
    }

    // Return the extracted data
    return [
        'booking_id' => $bookingId,
        'payu_token' => $payuToken,
        'raw_response' => $result, // Include the raw response for debugging
        "payment" => [
            "order_id" => (string)$bookingId,
            "token" => $payuToken,
            "gate" => "pos",
            "email" => "test@kiwi.com"
        ]
    ];
}
