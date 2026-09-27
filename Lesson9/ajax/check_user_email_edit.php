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
$id       = (int)($_POST['id'] ?? 0);
$username = trim($_POST['username'] ?? '');
$email    = trim($_POST['email'] ?? '');

$response = [
    'username_exists' => false,
    'email_exists'    => false,
];

try {
    // Check Username excluding the current record ID
    if (!empty($username)) {
        $stmt = $pdo->prepare("SELECT 1 FROM students WHERE username = :username AND id != :id LIMIT 1");
        $stmt->execute([
            ':username' => $username,
            ':id'       => $id
        ]);
        if ($stmt->fetchColumn()) {
            $response['username_exists'] = true;
        }
    }

    // Check Email excluding the current record ID
    if (!empty($email)) {
        $stmt = $pdo->prepare("SELECT 1 FROM students WHERE email = :email AND id != :id LIMIT 1");
        $stmt->execute([
            ':email' => $email,
            ':id'    => $id
        ]);
        if ($stmt->fetchColumn()) {
            $response['email_exists'] = true;
        }
    }

    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database query failed.'
    ]);
}