<?php

define("SITE_NAME", "My School");
$studentName = "Alex";

// Change variable (Works fine)
$studentName = "John";

// Try changing constant safely using try...catch
try {
    define("SITE_NAME", "New School"); // Throws a TypeError in PHP 8+
} catch (Throwable $e) {
    echo "<strong>Error Caught:</strong> " . $e->getMessage() . "<br><br>";
}

// The script CONTINUES running after the catch block
$message = "Welcome to " . SITE_NAME . "!<br>";
$message .= "Student: " . $studentName . "<br>";

echo $message;

?>