<?php

echo "=== 1. IF / ELSEIF / ELSE ===<br>";
$score = 85;

if ($score >= 90) {
    echo "Grade: A<br>";
} elseif ($score >= 80) {
    echo "Grade: B<br>";
} else {
    echo "Grade: C or lower<br>";
}

echo "<br>=== 2. SWITCH STATEMENT ===<br>";
$userRole = "editor";

switch ($userRole) {
    case "admin":
        echo "Full system access.<br>";
        break;
    case "editor":
        echo "Can create and edit content.<br>";
        break;
    default:
        echo "Read-only access.<br>";
}

echo "<br>=== 3. FOR LOOP ===<br>";
for ($x = 1; $x <= 3; $x++) {
    echo "Item number " . $x . "<br>";
}

echo "<br>=== 4. WHILE LOOP ===<br>";
$stock = 3;
while ($stock > 0) {
    echo "Item sold! Remaining stock: " . ($stock - 1) . "<br>";
    $stock--;
}

echo "<br>=== 5. DO...WHILE LOOP ===<br>";
$attempt = 1;
do {
    echo "Connection attempt " . $attempt . "<br>";
    $attempt++;
} while ($attempt <= 2);

?>