<?php
$dirPath  = __DIR__ . '/data';
$filePath = $dirPath . '/submissions.txt';

// Detect request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Verify submission presence across GET or POST
if ($requestMethod === 'POST' || ($requestMethod === 'GET' && isset($_GET['username']))) {

    $inputData = ($requestMethod === 'POST') ? $_POST : $_GET;
   
    // Sanitize input data
    $username   = htmlspecialchars(trim($inputData['username'] ?? ''));
    $firstName  = htmlspecialchars(trim($inputData['firstname'] ?? ''));
    $lastName   = htmlspecialchars(trim($inputData['lastname'] ?? ''));
    $age        = (int)($inputData['age'] ?? 0);
    $email      = htmlspecialchars(trim($inputData['email'] ?? ''));
    $nickname   = !empty($inputData['nickname']) ? htmlspecialchars(trim($inputData['nickname'])) : 'N/A';
    $gender     = htmlspecialchars($inputData['gender'] ?? 'N/A');
    $membership = htmlspecialchars($inputData['membership'] ?? 'N/A');
    $newsletter = isset($inputData['newsletter']) ? 'Yes' : 'No';

    // Format output line
    $entry = "[$requestMethod] $username | $firstName $lastName | Age: $age | $email | Nickname: $nickname | Gender: $gender | Type: $membership | Newsletter: $newsletter" . PHP_EOL;

    // Ensure data directory exists
    if (!is_dir($dirPath)) {
        mkdir($dirPath, 0777, true);
    }

    // Append entry to data file (creates file if it does not exist)
    file_put_contents($filePath, $entry, FILE_APPEND | LOCK_EX);

    // Redirect to view records page
    header('Location: view.php?status=success&method=' . $requestMethod);
    exit;
} else {
    // If accessed directly without parameters, return to form
    header('Location: form.php');
    exit;
}