<?php
$firstName = "John";
$lastName  = "Doe";

// Combining using the dot (.) operator
$fullName = $firstName . " " . $lastName;

echo $fullName . "<br>"; // Output: John Doe
?>


<?php
$city    = "Yerevan";
$country = "Armenia";

// PHP evaluates variables directly inside double quotes
$location = "Location: $city, $country";

echo $location . "<br>"; // Output: Location: Yerevan, Armenia
?>