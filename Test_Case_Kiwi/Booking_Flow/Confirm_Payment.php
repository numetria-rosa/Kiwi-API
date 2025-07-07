<?php

require_once __DIR__ . '/../../vendor/autoload.php';


if (isset($_GET['booking_id']) && isset($_GET['transaction_id'])) {
    $bookingId = $_GET['booking_id'];
    $transactionId = $_GET['transaction_id'];

    ConfirmPayment($bookingId, $transactionId);
} else {
    echo "Missing booking ID or transaction ID.";
}

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Global variables
$endpoint = "https://api.tequila.kiwi.com/v2";
$apikey = "15C5Z-ZDZJAlqfdlMGRpoFUQ0v09SQyN";

// Function to confirm payment with only necessary parameters
function ConfirmPayment($bookingId, $transactionId)
{
    global $endpoint ,$apikey;
    // Set up Monolog Logger
    $rqLogger = new Logger('RQ_ConfirmPayment');
    $rqLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Confirm_Payment/RQ_ConfirmPayment.txt', Logger::INFO));

    $rsLogger = new Logger('RS_ConfirmPayment');
    $rsLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Confirm_Payment/RS_ConfirmPayment.txt', Logger::INFO));

    // Prepare curl request
    $curl = curl_init();

    // Prepare POST data
    $postData = [
        "booking_id" => $bookingId,
        "transaction_id" => $transactionId
    ];

    // Log the request
    $rqLogger->info("Sending ConfirmPayment Request:");
    $rqLogger->info("Request URL: " . $endpoint . '/booking/confirm_payment');
    $rqLogger->info("Post Data: " . json_encode($postData));

    curl_setopt_array($curl, array(
        CURLOPT_URL => $endpoint . '/booking/confirm_payment',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
            'apikey: ' . $apikey,
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    // Log the response
    $rsLogger->info("ConfirmPayment Response:");
    $rsLogger->info($response);

    echo $response;
}
