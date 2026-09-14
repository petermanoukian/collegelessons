<?php

// ---------------------------------------------------
// 1. SCALAR TYPES (Holds a single value)
// ---------------------------------------------------

// String: Text inside single (' ') or double (" ") quotes
$siteName = "My First Website";

// Integer: Whole numbers without decimals
$studentAge = 20;

// Float (or Double): Numbers with a decimal point
$courseRating = 4.8;

// Boolean: True or False
$isEnrolled = true;


// ---------------------------------------------------
// 2. COMPOUND TYPES (Holds multiple values)
// ---------------------------------------------------

// Indexed Array: Simple list of items
$topics = ["Variables", "Data Types", "Functions"];

// Associative Array: Key-Value pairs
$studentProfile = [
    "name" => "John Doe",
    "age"  => $studentAge,
    "grade" => "A"
];

// Object: Instance of a class
class Student {
    public string $name = "Alice";
}
$studentObject = new Student();


// ---------------------------------------------------
// 3. SPECIAL TYPES
// ---------------------------------------------------

// NULL: Variable with no value assigned
$emptyVariable = null;

// Resource: External reference (e.g., file handle)
$filePointer = fopen("php://temp", "r");


// ---------------------------------------------------
// OUTPUT EXAMPLES FOR BEGINNERS (FOR BROWSER DISPLAY)
// ---------------------------------------------------

echo "=== BASIC OUTPUT ===<br>";
echo "Site Name: " . $siteName . "<br>";
echo "Age: " . $studentAge . "<br>";
echo "Rating: " . $courseRating . "<br>";
echo "Is Enrolled: " . ($isEnrolled ? "Yes" : "No") . "<br>";

echo "<br>=== PRINTING ARRAYS ===<br>";
echo "<pre>";
print_r($topics);
echo "</pre>";

echo "<br>=== INSPECTING TYPES & VALUES ===<br>";
echo "<pre>";
var_dump($courseRating);
var_dump($studentProfile);
echo "</pre>";

// Close file resource
fclose($filePointer);

?>