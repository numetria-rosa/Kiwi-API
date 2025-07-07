<?php

require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../Save_Booking.php';  // Include the SaveBooking function

// Define the test parameters
$passengers = [[
    "birthday" => "1990-01-01",
    "cardno" => "D25845822",
    "category" => "adult",
    "email" => "test@kiwi.com",
    "expiration" => "2030-12-10",
    "title" => "ms",
    "name" => "Test",
    "surname" => "Test",
    "nationality" => "CZ",
    "phone" => "+44857282842"
]];

$baggage = [[
    "combination" => [
        "indices" => [0],
        "category" => "hold_bag",
        "conditions" => [
            "passenger_groups" => ["adult", "child"]
        ],
        "price" => [
            "currency" => "EUR",
            "amount" => 120.0,
            "base" => 80.0,
            "service" => 40.0,
            "service_flat" => 0.0,
            "merchant" => 0.0
        ]
    ],
    "passengers" => [0]
]];

// Replace with actual values when testing
$apiKey = "15C5Z-ZDZJAlqfdlMGRpoFUQ0v09SQyN"; // API key
$bookingToken = 'HH_gS6FhmuaANZHHyzPgbX2OGxMoN5JlfueGLNEiok2xCbldZoi2DmqFGvSStEirKUowTjwInSpMSR3cP5okyY6panjHkgktc3Kmgjb3-r6a0AtbjEQK0MV-ESqCI7gzjLho5WSLDDJjGaZIH5Xc_P-kG7VJDXa-_XUTRuHso_62kxHiBQ1YgP_kbj0HKM4s_-PoH4yqvmGq7cb3dcKxtwatfeMJfQN7ivA_0QSMHEXbiKA65dvwRxifUX6undGJZQddrRXHU2WetTQgeRBuiUFlqlnCmuLiku7kHDGTsw38lHTTiXNaA26O-7ARTwEGmglbDlBK1_jnBH2W7Gu9Ft_0rmZlGY9sdMkGndNKbtyorWJjAlpJpTXjvzTF3PSWrU8j_Ss_Ij3lcHIBYf4SplYPGwFCniSn4mlZd8S9IrL0DLVTOwjN7Uchbt89tCTZhFTinmyZ6V6WuBIQfXl0W9FQr66tC5XojxN_HNjy4e_d80tOcoJdeihCthlAsqta1K0CYkLZti2iNwVHOxsmxV6MbRMWE3Ren00uT3sp6H6XCHjDU6UudfRpdSMotJoAb6Ljxf3nwC6qkG4gc07b9HVH3Q2dvMuaYkGZhSiKK22INtmSQv1D7VwHEvY9Am85wa7M1hG37Reg4sFFdWLJeUO250m5DjwMQFSaD8KwN3jjd3Ga7YtsXS8-9hB7D7YSGfAbQoGrIG1Y8dGamQHddKK_kwfVfC76rvuIEjJOk8cOqBjUih2xJtSyyWtaC7NQn49ZEFLxriY7clxUpgsjDF3O4RiSHl1iU-u-o58X5FsMB2z3ctZFAVjZ-YhEanpMsqZgrcpWDGQlwZQEjFzYkdRG6Qq0nXW_cikh46efJexRBYuLv29oKfkBowzJ0Wd-a7e8Y-Fb8Lf7WRHWZW8bKnh3rNPrLbGVMBBKujHPQc4jlRguKl_w2qGmoVUYeOaZKon1rPvuan4qWzr_MzCh0ZvYeV_QR11eybfVi9VdFZ7gqCW0l2jtGahwzR81RyVTc';
$sessionId = "f5b4fbba-b0a0-506e-2bd8-68cc733dcbae";
$visitorUniqId ="e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855" ;

// Call the SaveBooking function
SaveBooking($apiKey, $visitorUniqId, $bookingToken, $sessionId, $passengers, $baggage);

?>
