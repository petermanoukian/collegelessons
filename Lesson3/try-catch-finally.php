<?php

define("SITE_NAME", "My School");
$studentName = "Alex";

// Change variable (Works fine)
$studentName = "John";

// Try changing constant safely using try...catch...finally
try {
    define("SITE_NAME", "New School"); // Throws an error because it's already defined
} catch (Throwable $e) {
    echo "<strong>Error Caught:</strong> " . $e->getMessage() . "<br>";
} finally {
    echo "<em>Finally block executed! (This ALWAYS runs, error or no error).</em><br><br>";
}

// The script CONTINUES running
$message = "Welcome to " . SITE_NAME . "!<br>";
$message .= "Student: " . $studentName . "<br>";

echo $message;

?>