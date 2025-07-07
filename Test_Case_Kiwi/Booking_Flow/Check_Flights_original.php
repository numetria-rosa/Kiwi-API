<?php

require_once __DIR__ . '/../../vendor/autoload.php';
session_start();

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$endpoint = "https://api.tequila.kiwi.com/v2";
$apikey = "15C5Z-ZDZJAlqfdlMGRpoFUQ0v09SQyN";
$visitorUniqId = "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855";



$session_id = $_SESSION['session_id'] ?? ($_SESSION['flight_result']['search_id'] ?? null);
$booking_token = $_SESSION['booking_token'] ?? ($_SESSION['flight_result']['data'][0]['id'] ?? null);



$adults = 1;
$children = null;
$infants = null;
$currency = 'EUR';
$bnum = 2;


if ($session_id && $booking_token) {
    echo "Session ID: " . htmlspecialchars($session_id) . "<br><hr>";
    echo "Booking Token: " . htmlspecialchars($booking_token) . "<br><hr>";
    CheckFlights($booking_token, $session_id, $visitorUniqId, $adults, $children, $infants, $currency, $bnum);
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

            if (!empty($data['flights'][0])) {
                $flight = $data['flights'][0];

                echo "<form method='GET' action='Save_Booking.php' style='margin-bottom: 20px;'>";

                // Hidden fields to pass values
                echo "<input type='hidden' name='visitor_uniqid' value='" . htmlspecialchars($visitorUniqId) . "'>";
                echo "<input type='hidden' name='booking_token' value='" . htmlspecialchars($bookingToken) . "'>";
                echo "<input type='hidden' name='session_id' value='" . htmlspecialchars($sessionId) . "'>";

                echo "<button type='submit' style='
                all: unset;
                cursor: pointer;
                display: block;
                width: 100%;
            '>";


                $totalPrice = $data['total'] ?? 'N/A';
                $cityFrom = $flight['src_name'] ?? 'N/A';
                $cityTo = $flight['dst_name'] ?? 'N/A';
                $departAirport = $flight ['src_station'] ?? 'N/A';
                $ArrivalAirport = $flight ['dst_station'] ?? 'N/A';
                $ticketPrice = $data['tickets_price'] ?? 'N/A';
                $airlineName = $flight['airline']['Name'] ?? 'N/A';
                $operatingAirline = $flight['operating_airline']['name'] ?? 'N/A';
                $operatingAirlineIata = $flight['operating_airline']['iata'] ?? 'N/A';
                $operatingFlightNo = $flight['operating_flight_no'] ?? 'N/A';
                $sessionId = $data['session_id'] ?? $sessionId;
                $bookingToken = $data['booking_token'] ?? $bookingToken;
                


                $departDateRaw = $flight['local_departure'] ?? null;
                $arrivalDateRaw = $flight['local_arrival'] ?? null;
                
                if ($departDateRaw) {
                    $departDateTime = new DateTime($departDateRaw);
                    $departDate = $departDateTime->format('D, d M Y'); // e.g., Wed, 30 Apr 2025
                    $departTime = $departDateTime->format('H:i');      // e.g., 06:10
                } else {
                    $departDate = $departTime = 'N/A';
                }
                
                if ($arrivalDateRaw) {
                    $arrivalDateTime = new DateTime($arrivalDateRaw);
                    $arrivalDate = $arrivalDateTime->format('D, d M Y'); // e.g., Wed, 30 Apr 2025
                    $arrivalTime = $arrivalDateTime->format('H:i');      // e.g., 08:10
                } else {
                    $arrivalDate = $arrivalTime = 'N/A';
                }
               

                echo "<div class='flight-detail-container' style='border: 2px solid #ffcc00; padding: 20px; border-radius: 10px; background-color: #f9f9f9;'>";
                echo "<h2>Flight Details</h2>";

                echo "<strong>Total Price:</strong> $totalPrice EUR<br>";
                echo "<strong>Departure City:</strong> $cityFrom <br>";
                echo "<strong>Arrival City:</strong> $cityTo <br>";
                echo "<strong>Airport (Dep) :</strong> $departAirport  <br>";
                echo "<strong>Airport (Arrival) :</strong> $ArrivalAirport  <br>";
                echo "<strong>Departure Date:</strong> $departDate <br>";
                echo "<strong>Departure Time:</strong> $departTime <br>";
                echo "<strong>Arrival Date:</strong> $arrivalDate <br>";
                echo "<strong>Arrival Time:</strong> $arrivalTime <br>";
                echo "<strong>Tickets Price:</strong> $ticketPrice EUR<br>";
                echo "<strong>Carrier:</strong> $airlineName<br>";
                echo "<strong>Airline IATA :</strong>$operatingAirlineIata<br>";
                echo "<strong>Flight No:</strong> $operatingFlightNo<br>";
                echo "<strong>Connection number:</strong>$operatingAirlineIata $operatingFlightNo<br>";
                echo "<strong>Session ID:</strong> $sessionId<br>";
                echo "<strong>Booking Token:</strong> $bookingToken<br>";

                // Initialize the total bags amount
                $totalBagsAmount = 0;

                if (!empty($data['baggage']['combinations'])) {
                    echo "<hr><strong>🧳 Baggage Combinations with Prices:</strong><br>";

                    $definitions = $data['baggage']['definitions'];

                    // HAND BAGS
                    if (!empty($data['baggage']['combinations']['hand_bag'])) {
                        echo "<br>👜 <strong>Hand Baggage:</strong><br>";
                        foreach ($data['baggage']['combinations']['hand_bag'] as $handBag) {
                            if (!empty($handBag['indices'])) {
                                foreach ($handBag['indices'] as $index) {
                                    $details = $definitions['hand_bag'][$index];
                                    $r = $details['restrictions'];
                                    $price = $details['price'];
                                    echo "- <em>{$details['category']}</em>: {$r['weight']}kg, {$r['length']}x{$r['width']}x{$r['height']} cm<br>";
                                    echo "  <strong>Price:</strong> {$price['amount']} {$price['currency']}<br>";
                                    $totalBagsAmount += $price['amount']; // Add amount to total
                                }
                            } else {
                                echo "- No additional hand baggage (Personal Item)<br>";
                            }
                        }
                    }

                    // HOLD BAGS
                    if (!empty($data['baggage']['combinations']['hold_bag'])) {
                        echo "<br>🧳 <strong>Checked/Hold Baggage:</strong><br>";
                        foreach ($data['baggage']['combinations']['hold_bag'] as $holdBag) {
                            if (!empty($holdBag['indices'])) {
                                foreach ($holdBag['indices'] as $index) {
                                    $details = $definitions['hold_bag'][$index];
                                    $r = $details['restrictions'];
                                    $price = $details['price'];
                                    echo "- <em>{$details['category']}</em>: {$r['weight']}kg, {$r['length']}x{$r['width']}x{$r['height']} cm<br>";
                                    echo "  <strong>Price:</strong> {$price['amount']} {$price['currency']}<br>";
                                    echo "  <strong>Base:</strong> {$price['base']} {$price['currency']}<br>";
                                    echo "  <strong>Service:</strong> {$price['service']} {$price['currency']}<br>";
                                    echo "  <strong>Service Flat:</strong> {$price['service_flat']} {$price['currency']}<br>";
                                    echo "  <strong>Merchant:</strong> {$price['merchant']} {$price['currency']}<br><br>";
                                    $totalBagsAmount += $price['amount']; // Add amount to total
                                }
                            } else {
                                echo "Checked Bag = hold_bag<br>";
                            }
                        }
                    }

                    // Output the total bags amount
                    echo "<hr><strong>Total Bags Amount:</strong> {$totalBagsAmount} EUR<br>";
                    // Serialize baggage info as JSON and escape it
                        $baggageJson = htmlspecialchars(json_encode($data['baggage']), ENT_QUOTES, 'UTF-8');

                        // Add it as a hidden input field
                        echo "<input type='hidden' name='baggage_json' value='{$baggageJson}'>";

                }

                echo "</div>";
                echo "</div>";
                echo "</button>";
                echo "</form>";
                return $data;
            }
        }

        // Wait for next attempt
        sleep($interval);
    }

    echo "<hr>⛔ Max attempts reached or booking conditions not met.<br>";
    return $data ?? null;
}
?>
