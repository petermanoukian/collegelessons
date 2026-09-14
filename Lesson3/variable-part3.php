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


<?php
$item  = "Laptop";
$price = 899.99;
$count = 2;

// Combining strings, integers, floats, and text
$summary = "Item: " . $item . " | Quantity: " . $count . " | Total: $" . ($price * $count);

echo $summary . "<br>"; 
// Output: Item: Laptop | Quantity: 2 | Total: $1799.98
?>

<?php
$message = "Welcome to the course.";
$message .= " Lesson 4 covers variable combination.";
$message .= " Practice makes perfect!";

echo $message . "<br>";
// Output: Welcome to the course. Lesson 4 covers variable combination. Practice makes perfect!
?>