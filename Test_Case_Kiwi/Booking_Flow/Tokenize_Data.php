<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Set up Monolog Logger
$rqLogger = new Logger('TokenizeLogger');
$rqLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Tokenize Data/RQ.log', Logger::INFO));

$rsLogger = new Logger('TokenizeLogger');
$rsLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Tokenize Data/RS.log', Logger::INFO));

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);
    
    // Log the raw input and parsed data
    $rqLogger->info("Raw input: " . $rawInput);
    $rqLogger->info("Parsed data: " . json_encode($data));
    
    if (!isset($data['booking_id']) || !isset($data['payu_token'])) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Missing required parameters']);
        exit;
    }

    $bookingId = $data['booking_id'];
    $payuToken = $data['payu_token'];
    $visitorUniqId = $data['visitor_uniqid'] ?? '';

    // Log the extracted values
    $rqLogger->info("Extracted values: " . json_encode([
        'booking_id' => $bookingId,
        'payu_token' => $payuToken,
        'visitor_uniqid' => $visitorUniqId
    ]));

    $curl = curl_init();

    $postData = [
        "card" => [
            "number" => "5555444444444444",
            "cvv" => "123",
            "expirationMonth" => "01",
            "expirationYear" => "26",
            "holder" => "TEST APPROVE"
        ],
        "payment" => [
            "order_id" => (string)$bookingId,
            "token" => $payuToken,
            "gate" => "pos",
            "email" => "test@kiwi.com"
        ]
    ];

    $tokenizeUrl = 'https://fe.payments-kiwi-dev.com/tokenize/';

    // Log the request
    $rqLogger->info("RQ - Tokenize Request " . json_encode([
        'url' => $tokenizeUrl,
        'headers' => [
            'apikey: ElatC22pP-gvmWAsXGztrpcE1mqGsDVR',
            'Content-Type: application/json',
            'visitor_uniqid: ' . $visitorUniqId
        ],
        'body' => $postData
    ]));

    curl_setopt_array($curl, array(
        CURLOPT_URL => $tokenizeUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
            'apikey: ElatC22pP-gvmWAsXGztrpcE1mqGsDVR',
            'Content-Type: application/json',
            'visitor_uniqid: ' . $visitorUniqId,
            'Accept: application/json'
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
    $rsLogger->info("RS - Tokenize Response (HTTP $httpCode)");
    $rsLogger->info("Response: " . $response);
    if ($error) {
        $rsLogger->error("CURL Error: " . $error);
        $rsLogger->error("CURL Error Number: " . $errno);
    }

    // If there was a CURL error
    if ($error) {
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'message' => 'CURL Error: ' . $error,
            'curl_errno' => $errno,
            'http_code' => $httpCode
        ]);
        exit;
    }

    // If the response is not successful, return an error
    if ($httpCode !== 200) {
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'message' => 'Tokenize API call failed',
            'http_code' => $httpCode,
            'response' => json_decode($response, true)
        ]);
        exit;
    }

    // Parse the response
    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        header('Content-Type: application/json');
        echo json_encode([
            'error' => true,
            'message' => 'Invalid JSON response: ' . json_last_error_msg(),
            'raw_response' => $response
        ]);
        exit;
    }

    // Return the response
    header('Content-Type: application/json');
    echo json_encode([
        'response' => $result
    ]);
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}
