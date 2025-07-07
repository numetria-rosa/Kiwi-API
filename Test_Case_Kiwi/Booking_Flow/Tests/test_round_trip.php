<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../Searchs/Search_Round_Trip.php';


$fly_from = 'TUN';              // Tunis
$fly_to = 'CDG';                // Paris Charles de Gaulle
$date_from = '20/04/2025';      // Departure window start
$date_to = '21/04/2025';        // Departure window end
$return_from = '24/04/2025';    // Return window start
$return_to = '25/04/2025';      // Return window end

$adults = 1;
$children = null;
$infant = null;

$cabin_class = 'M';             // Economy
$mix_with_cabins = '';          // No mixing
$adult_hold_bag = 1;
$adult_hand_bag = 1;
$child_hold_bag = '';
$child_hand_bag = '';

$response = SearchFlightRoundTrip(
    $fly_from, $fly_to, $date_from, $date_to,
    $return_from, $return_to, $adults, $children, $infant,
    $cabin_class, $mix_with_cabins,
    $adult_hold_bag, $adult_hand_bag,
    $child_hold_bag, $child_hand_bag
);

// Pretty print response
echo "<pre>";
print_r($response);
echo "</pre>";

?>
