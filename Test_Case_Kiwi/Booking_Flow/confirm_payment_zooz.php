<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function ConfirmPaymentZooz($bookingId, $orderId, $paymentToken, $paymentMethodToken) {
    // Set up Monolog Logger
    $rqLogger = new Logger('RQ_ConfirmPaymentZooz');
    $rqLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Confirm_Payment_Zooz/RQ.log', Logger::INFO));

    $rsLogger = new Logger('RS_ConfirmPaymentZooz');
    $rsLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Confirm_Payment_Zooz/RS.log', Logger::INFO));

    $curl = curl_init();

    // Read the Tokenize_Data response to get payment_details
    $tokenizeResponse = file_get_contents('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Tokenize Data/RS.log');
    
    // Extract the JSON response from the log line
    if (preg_match('/Response: ({.*?}) \[/', $tokenizeResponse, $matches)) {
        $tokenizeData = json_decode($matches[1], true);
        
        if ($tokenizeData) {
            // Format the payment details as required
            $paymentDetails = [
                "status" => $tokenizeData['status'],
                "token" => $tokenizeData['token'],
                "encrypted_cvv" => $tokenizeData['encrypted_cvv'],
                "bin_number" => "421111",
                "last_4_digits" => "1111",
                "holder_name" => $tokenizeData['holder_name'],
                "expiration" => $tokenizeData['expiration'] . "T00:00:00.000Z",
                "vendor" => $tokenizeData['vendor'],
                "issuer" => $tokenizeData['issuer'],
                "country_code" => $tokenizeData['country_code'],
                "level" => $tokenizeData['level'],
                "type" => $tokenizeData['type'],
                "pass_luhn_validation" => $tokenizeData['pass_luhn_validation']
            ];
        } else {
            $rsLogger->error("Failed to parse tokenization response JSON");
            return [
                'error' => true,
                'message' => 'Failed to parse tokenization response JSON'
            ];
        }
    } else {
        $rsLogger->error("Failed to find tokenization response in log file");
        return [
            'error' => true,
            'message' => 'Failed to find tokenization response in log file'
        ];
    }

    $postData = [
        "payment_details" => $paymentDetails,
        "booking_id" => $bookingId,
        "order_id" => $orderId,
        "paymentToken" => $paymentToken, // This is the payu_token from save_booking
        "paymentMethodToken" => $paymentMethodToken, // This is the token from tokenize response
        "sandbox" => true,
        "language" => "en-GB"
    ];

    // Log the request
    $rqLogger->info("Sending ConfirmPaymentZooz Request:");
    $rqLogger->info("Request URL: https://api.tequila.kiwi.com/v2/booking/confirm_payment_zooz");
    $rqLogger->info("Post Data: " . json_encode($postData));

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.tequila.kiwi.com/v2/booking/confirm_payment_zooz',
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
    $rsLogger->info("ConfirmPaymentZooz Response (HTTP $httpCode):");
    $rsLogger->info("Response: " . $response);
    if ($error) {
        $rsLogger->error("CURL Error: " . $error);
        $rsLogger->error("CURL Error Number: " . $errno);
    }

    $result = json_decode($response, true);

    // Handle async payment status (2)
    if (isset($result['status']) && $result['status'] === 2) {
        // Wait for 2 seconds before retrying
        sleep(2);
        return ConfirmPaymentZooz($bookingId, $orderId, $paymentToken, $paymentMethodToken);
    }

    return $result;
}

// Handle direct API calls
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['booking_id'], $data['paymentToken'], $data['paymentMethodToken'])) {
        $result = ConfirmPaymentZooz(
            $data['booking_id'], // booking_id from save_booking
            $data['booking_id'], // order_id is the same as booking_id
            $data['paymentToken'], // payu_token from save_booking
            $data['paymentMethodToken'] // token from tokenize response
        );
        
        header('Content-Type: application/json');
        echo json_encode($result);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required parameters']);
    }
} 