<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../Searchs/Search_One_Way.php';


$endpoint = 'https://api.tequila.kiwi.com/v2'; 
$apikey = '15C5Z-ZDZJAlqfdlMGRpoFUQ0v09SQyN'; 

// Call the function
$result = SearchFlightOneWay(
    'TUN',        // fly_from (e.g. Tunis)
    'ORY',        // fly_to (e.g. Paris Orly)
    '20/04/2025', // date_from
    '20/04/2025', // date_to
    '1',          // adults
    '',           // children (empty because 0)
    '',           // infant (empty because 0)
    'M',          // selected_cabins
    '',           // mix_with_cabins
    '1',          // adult_hold_bag
    '1',          // adult_hand_bag
    '',           // child_hold_bag
    ''            // child_hand_bag
);
echo "<pre>";
print_r($response);
echo "</pre>";

