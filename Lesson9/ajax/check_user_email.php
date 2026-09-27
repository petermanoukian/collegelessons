<?php
// Set content type to JSON
header('Content-Type: application/json');

// Include central database connection ($pdo)
require_once __DIR__ . '/../includes/connection.inc.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. POST required.'
    ]);
    exit;
}

// Retrieve posted inputs
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');

$response = [
    'username_exists' => false,
    'email_exists'    => false,
];

try {
    // Check Username if provided
    if (!empty($username)) {
        $stmt = $pdo->prepare("SELECT 1 FROM students WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        if ($stmt->fetchColumn()) {
            $response['username_exists'] = true;
        }
    }

    // Check Email if provided
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT 1 FROM students WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetchColumn()) {
            $response['email_exists'] = true;
        }
    }

    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database query failed.'
    ]);
}