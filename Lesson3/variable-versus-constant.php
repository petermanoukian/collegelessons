<?php

// 1. Declare Constants (Values that DO NOT change)
define("SITE_NAME", "My School");
const TAX_RATE = 0.10; // 10% tax

// 2. Declare Variables (Values that CAN change)
$studentName = "Alex";
$price = 100;

// 3. Combine them into one single variable
$totalPrice = $price + ($price * TAX_RATE);

$message = "Welcome to " . SITE_NAME . "!<br>";
$message .= "Student: " . $studentName . "<br>";
$message .= "Total Cost: $" . $totalPrice . "<br>";

// 4. Print out the final message
echo $message;

?>