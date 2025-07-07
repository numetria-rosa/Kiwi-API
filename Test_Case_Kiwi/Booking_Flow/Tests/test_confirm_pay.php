<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../Confirm_Payment.php';  // Include the ConfirmPayment function

// Define the test parameters for the ConfirmPayment function
$bookingId = "628586662"; // Example booking ID
$transactionId = "sandbox_628586662"; // Example transaction ID


// Call the ConfirmPayment function with the test parameters
ConfirmPayment($bookingId, $transactionId);

?>
