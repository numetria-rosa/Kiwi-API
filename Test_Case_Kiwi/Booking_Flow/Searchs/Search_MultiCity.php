<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$endpoint = "https://api.tequila.kiwi.com/v2";
$apikey = "1JOoUDWa-KInhHVzo1zSABlvWGMUe7h3";

function SearchMulti($requestsArray) {
    global $endpoint, $apikey;

    $url = $endpoint . '/flights_multi';

    $payload = json_encode(['requests' => $requestsArray]);

    // Log Request
    $rqLogger = new Logger('RQ_SearchMulti');
    $rqLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Searchs/Multi/RQ_SearchMulti.txt', Logger::INFO));
    $rqLogger->info("Request URL: " . $url);
    $rqLogger->info("Payload: " . $payload);

    // CURL
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => array(
            'apikey: ' . $apikey,
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    // Log Response
    $rsLogger = new Logger('RS_SearchMulti');
    $rsLogger->pushHandler(new StreamHandler('C:/xampp/htdocs/Test_Case_Kiwi/Queues/Searchs/Multi/RS_SearchMulti.txt', Logger::INFO));
    $rsLogger->info($response);

    curl_close($curl);

    return json_decode($response, true);
}
?>
