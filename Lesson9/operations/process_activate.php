<?php
// 1. Always start session first so flash messages work
session_start();

// 2. Load config & connection
require_once dirname(__DIR__) . '/includes/config.php';
require_once ROOT_PATH . '/includes/connection.inc.php';

// 3. Get request method directly from $_SERVER
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod !== 'POST') {
    header('Location: ' . BASE_URL . '/activate.php');
    exit;
}

$code = trim($_POST['code'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Basic validation
if (empty($code) || empty($password) || empty($confirmPassword)) {
    $_SESSION['error'] = "All fields are required.";
    header('Location: ' . BASE_URL . '/activate.php' . (!empty($code) ? '?code=' . urlencode($code) : ''));
    exit;
}

if ($password !== $confirmPassword) {
    $_SESSION['error'] = "Passwords do not match.";
    header('Location: ' . BASE_URL . '/activate.php?code=' . urlencode($code));
    exit;
}

try {
    // Check code existence
    $stmt = $pdo->prepare("SELECT id, username, activated FROM students WHERE code = :code");
    $stmt->execute([':code' => $code]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $_SESSION['error'] = "Invalid activation code. Please check and try again.";
        header('Location: ' . BASE_URL . '/activate.php?code=' . urlencode($code));
        exit;
    }

    if ($student['activated'] === 'active') {
        $_SESSION['error'] = "This account is already activated. Please login.";
        header('Location: ' . BASE_URL . '/user/views/login.php');
        exit;
    }

    // Hash password & activate
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $updateStmt = $pdo->prepare("UPDATE students SET password = :password, activated = 'active' WHERE id = :id");
    $updateStmt->execute([
        ':password' => $hashedPassword,
        ':id'       => $student['id']
    ]);

    $_SESSION['success'] = "Account activated successfully! You can now log in.";
    header('Location: ' . BASE_URL . '/user/views/login.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Database error during activation: " . $e->getMessage();
    header('Location: ' . BASE_URL . '/activate.php?code=' . urlencode($code));
    exit;
}