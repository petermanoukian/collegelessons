<?php
session_start();

require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/includes/connection.inc.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod !== 'POST') {
    header('Location: ' . BASE_URL . '/user/views/login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($username) || empty($password)) {
    $_SESSION['error'] = "Both username/email and password are required.";
    header('Location: ' . BASE_URL . '/user/views/login.php');
    exit;
}

try {
    // Check user by username OR email
    $stmt = $pdo->prepare("SELECT id, username, password, activated FROM students WHERE username = :username OR email = :email");
    $stmt->execute([
        ':username' => $username,
        ':email'    => $username
    ]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $_SESSION['error'] = "Invalid credentials.";
        header('Location: ' . BASE_URL . '/user/views/login.php');
        exit;
    }

    // Check if account is active
    if ($student['activated'] !== 'active') {
        $_SESSION['error'] = "Account is not activated yet. Please check your activation code.";
        header('Location: ' . BASE_URL . '/activate.php');
        exit;
    }

    // Verify password against stored hash
    if (!password_verify($password, $student['password'])) {
        $_SESSION['error'] = "Invalid credentials.";
        header('Location: ' . BASE_URL . '/user/views/login.php');
        exit;
    }

    // Login success - store user info in session
    $_SESSION['user_id'] = $student['id'];
    $_SESSION['username'] = $student['username'];
    $_SESSION['success'] = "Welcome back, " . htmlspecialchars($student['username']) . "!";

    header('Location: ' . BASE_URL . '/user/views/dashboard.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header('Location: ' . BASE_URL . '/user/views/login.php');
    exit;
}