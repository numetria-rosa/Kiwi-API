<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../Searchs/Search_MultiCity.php';

$multiRequests = [
    [
        "fly_to" => "DJE",
        "fly_from" => "ORY",
        "date_from" => "17/05/2025",
        "date_to" => "17/05/2025",
        "adults" => 1
    ],
    [
        "fly_to" => "BCN",
        "fly_from" => "DJE",
        "date_from" => "20/05/2025",
        "date_to" => "20/05/2025",
        "adults" => 1
    ]
];

$response = SearchMulti($multiRequests);

echo "<pre>";
print_r($response);
echo "</pre>";

?>
