<?php

// --- 1. Math Operations ---
$num1 = 10;
$num2 = 3;

$sum        = $num1 + $num2;
$difference = $num1 - $num2;
$product    = $num1 * $num2;
$quotient   = $num1 / $num2;
$remainder  = $num1 % $num2;

echo "=== ARITHMETIC OPERATORS ===<br>";
echo "10 + 3 = " . $sum . "<br>";
echo "10 - 3 = " . $difference . "<br>";
echo "10 * 3 = " . $product . "<br>";
echo "10 / 3 = " . $quotient . "<br>";
echo "10 % 3 = " . $remainder . "<br><br>";

// --- 2. Assignment Shortcuts ---
$score = 50;
$score += 10; // $score is now 60
$score -= 5;  // $score is now 55

echo "=== ASSIGNMENT SHORTCUTS ===<br>";
echo "Updated score: " . $score . "<br><br>";

// --- 3. Comparison Operators ---
$val1 = 10;
$val2 = "10";

echo "=== COMPARISON OPERATORS ===<br>";
echo "Is 10 == '10'? ";
var_dump($val1 == $val2); // true (same value)
echo "<br>";

echo "Is 10 === '10'? ";
var_dump($val1 === $val2); // false (different types: int vs string)
echo "<br><br>";

// --- 4. Logical Operators ---
$age = 20;
$hasLicense = true;

$canDrive = ($age >= 18) && $hasLicense;

echo "=== LOGICAL OPERATORS ===<br>";
echo "Can drive? ";
var_dump($canDrive); // true
echo "<br>";

?>