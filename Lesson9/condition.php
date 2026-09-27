<?php

// ==========================================
// 1. ALL COMPARISON OPERATORS (>, <, >=, <=, ==, !=, ===)
// ==========================================

$score = 85;

// > (Greater Than) & < (Less Than)
if ($score > 90) {
    echo "Score is strictly greater than 90<br>";
} elseif ($score < 60) {
    echo "Score is strictly less than 60<br>";
} 

// >= (Greater Than or Equal To) & <= (Less Than or Equal To)
if ($score >= 85) {
    echo "Score is greater than or equal to 85 (Grade B)<br>";
}

if ($score <= 85) {
    echo "Score is less than or equal to 85<br>";
}

// == (Equal Value) vs === (Equal Value & Identical Data Type)
$userAge = "18"; // String type

if ($userAge == 18) {
    echo "Loose Equality (==): Converts '18' string to integer 18 - Match!<br>";
}

if ($userAge === 18) {
    echo "Strict Equality (===): Checks type as well - Will NOT run because string != int<br>";
} else {
    echo "Strict Equality (===): Failed because '18' is a string and 18 is an integer<br>";
}

// != (Not Equal)
if ($score != 100) {
    echo "Score is not equal to 100<br>";
}

echo "<hr>";

// ==========================================
// 2. LOGICAL AND (&&)
// ==========================================

$age = 17;
$hasParentConsent = true;

// Both conditions must evaluate to true
if ($age >= 18 && $hasParentConsent) {
    echo "Adult with consent - Full access<br>";
} elseif ($age < 18 && $hasParentConsent) {
    // Age is under 18 AND parent consent is true
    echo "Minor with parent consent - Access allowed for students aged 15-18<br>";
} else {
    echo "Access denied<br>";
}

echo "<hr>";

// ==========================================
// 3. LOGICAL OR (||)
// ==========================================

$day = "Saturday";

// At least ONE condition must be true
if ($day == "Saturday" || $day == "Sunday") {
    echo "It is the weekend! No school today.<br>";
} elseif ($day == "Friday") {
    echo "Almost the weekend!<br>";
} else {
    echo "Regular school day.<br>";
}

echo "<hr>";

// ==========================================
// 4. SWITCH STATEMENT WITH LOGICAL AND (&&) & OR (||)
// ==========================================

// We use two variables to test complex combined logic inside the switch
$studentAge = 16;
$hasID = true;

// Passing 'true' into switch allows checking true/false condition expressions in each case
switch (true) {
    // BOTH conditions must be true (AND)
    case ($studentAge >= 15 && $hasID === true):
        echo "Switch (AND): Student is 15 or older AND has an ID - Full admission granted.<br>";
        break;

    // AT LEAST ONE condition must be true (OR)
    case ($studentAge >= 15 || $hasID === true):
        echo "Switch (OR): Student either meets age requirement OR has an ID - Partial access.<br>";
        break;

    default:
        echo "Switch Default: Student does not meet any requirements.<br>";
        break;
}

// Example showing switch matching multiple variables at once:
$hasPassedExam = true;
$hasCompletedHomework = true;

switch (true) {
    // Triggers ONLY when BOTH conditions are true
    case ($hasPassedExam && $hasCompletedHomework):
        echo "Switch (TWO Conditions Met): Passed exam AND finished homework - Final Grade: A<br>";
        break;

    case ($hasPassedExam || $hasCompletedHomework):
        echo "Switch (ONE Condition Met): Passed exam OR finished homework - Final Grade: B<br>";
        break;

    default:
        echo "Switch: Neither requirement met - Final Grade: F<br>";
        break;
}

echo "<hr>";

// ==========================================
// 5. FOR LOOP
// ==========================================

// Runs an exact number of times: (initializer; condition; increment)
echo "<strong>For Loop Example:</strong><br>";
for ($i = 1; $i <= 5; $i++) {
    echo "Student #$i is present<br>";
}

echo "<hr>";

// ==========================================
// 6. WHILE LOOP
// ==========================================

echo "<strong>While Loop Example:</strong><br>";
$counter = 1;

// Checks the condition FIRST before running the code block
while ($counter <= 5) {
    echo "Task $counter completed<br>";
    $counter++; // Must increment counter to avoid infinite loops
}

echo "<hr>";

// ==========================================
// 7. DO-WHILE LOOP
// ==========================================

echo "<strong>Do-While Loop Example:</strong><br>";
$step = 1;

// Executes the code block AT LEAST ONCE before checking the condition
do {
    echo "Running attempt #$step<br>";
    $step++;
} while ($step <= 5);

echo "<hr>";

// ==========================================
// 8. LOOPS WITH 'CONTINUE' (SKIPPING ITERATIONS)
// ==========================================

echo "<strong>For Loop with Continue (Skips #3):</strong><br>";
for ($i = 1; $i <= 5; $i++) {
    if ($i == 3) {
        continue; // Skips iteration 3 and jumps directly to $i = 4
    }
    echo "For Loop Number: $i<br>";
}

echo "<br><strong>While Loop with Continue (Skips #3):</strong><br>";
$x = 0;
while ($x < 5) {
    $x++;
    if ($x == 3) {
        continue; // Skips printing 3 and jumps back to condition check
    }
    echo "While Loop Number: $x<br>";
}

echo "<br><strong>Do-While Loop with Continue (Skips #3):</strong><br>";
$y = 0;
do {
    $y++;
    if ($y == 3) {
        continue; // Skips printing 3 and jumps to condition check
    }
    echo "Do-While Loop Number: $y<br>";
} while ($y < 5);

?>