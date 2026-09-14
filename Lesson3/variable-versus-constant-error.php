<?php

// 1. Declare Constant and Variable
define("SITE_NAME", "My School");
$studentName = "Alex";

// 2. Try changing the Variable (THIS WORKS FINE)
$studentName = "John"; 

// 3. Try changing the Constant (THIS WILL CAUSE A FATAL ERROR)
define("SITE_NAME", "New School"); 

// Combine and print
$message = "Welcome to " . SITE_NAME . "!<br>";
$message .= "Student: " . $studentName . "<br>";

echo $message;

?>