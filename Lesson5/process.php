<?php
// Always start the session first to handle flash messages
session_start();

// Include the database connection ($pdo)
require_once __DIR__ . '/includes/connection.inc.php';

// Detect request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Verify submission presence across POST or GET
if ($requestMethod === 'POST' || ($requestMethod === 'GET' && isset($_GET['username']))) {

    $inputData = ($requestMethod === 'POST') ? $_POST : $_GET;

    // Collect and trim raw user inputs
    $username   = trim($inputData['username'] ?? '');
    $firstName  = trim($inputData['firstname'] ?? '');
    $lastName   = trim($inputData['lastname'] ?? '');
    $age        = (int)($inputData['age'] ?? 0);
    $email      = trim($inputData['email'] ?? '');
    $nickname   = !empty(trim($inputData['nickname'] ?? '')) ? trim($inputData['nickname']) : 'N/A';
    $gender     = $inputData['gender'] ?? '';
    $membership = $inputData['membership'] ?? 'Member';
    $newsletter = isset($inputData['newsletter']) ? 1 : 0;

    // 1. Server-side basic validation
    if (empty($username) || empty($firstName) || empty($lastName) || empty($email) || $age < 2 || $age > 28) {
        $_SESSION['error'] = "Invalid or missing required form fields.";
        header('Location: form.php');
        exit;
    }

    try {
        // Check if either username or email already exists
        // Query without LIMIT 1 to catch matches across different rows
        $checkSql = "SELECT username, email FROM students WHERE username = :username OR email = :email";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            ':username' => $username,
            ':email'    => $email,
        ]);
        $rows = $checkStmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            $errors = [];
            $usernameTaken = false;
            $emailTaken = false;

            foreach ($rows as $row) {
                if (!$usernameTaken && strcasecmp($row['username'], $username) === 0) {
                    $errors[] = "Username '{$username}' is already taken.";
                    $usernameTaken = true;
                }
                if (!$emailTaken && strcasecmp($row['email'], $email) === 0) {
                    $errors[] = "Email '{$email}' is already registered.";
                    $emailTaken = true;
                }
            }

            // Join errors into a single clear flash message (or pass array)
            $_SESSION['error'] = implode(' ', $errors);

            header('Location: form.php');
            exit;
        }

 

        // 3. Insert Record
        $sql = "INSERT INTO students (username, first_name, last_name, age, email, nickname, gender, membership, newsletter) 
                VALUES (:username, :first_name, :last_name, :age, :email, :nickname, :gender, :membership, :newsletter)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username'   => $username,
            ':first_name'  => $firstName,
            ':last_name'   => $lastName,
            ':age'        => $age,
            ':email'      => $email,
            ':nickname'   => $nickname,
            ':gender'     => $gender,
            ':membership' => $membership,
            ':newsletter' => $newsletter
        ]);

        // Redirect to view records page upon success
        header('Location: view.php?status=success&method=' . $requestMethod);
        exit;

    } catch (PDOException $e) {
        // Fallback for duplicate unique entries (e.g. duplicate email)
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = "Registration Error: Username or Email is already registered.";
        } else {
            $_SESSION['error'] = "Database Error: " . $e->getMessage();
        }
        
        header('Location: form.php');
        exit;
    }

} else {
    // Return to form if accessed directly
    header('Location: form.php');
    exit;
}
?>