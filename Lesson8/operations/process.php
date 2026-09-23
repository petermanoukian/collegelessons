<?php
// Load config directly first to establish ROOT_PATH and BASE_URL constants
require_once dirname(__DIR__) . '/includes/config.php';

// Include common process script using ROOT_PATH
require_once ROOT_PATH . '/includes/operations/common_process.php';

// Verify request
if ($requestMethod !== 'POST' && !($requestMethod === 'GET' && isset($_GET['username']))) {
    header('Location: ' . BASE_URL . '/addstudents');
    exit;
}

$inputs = getSanitizedFormInputs($inputData);

// Basic server validation
if (empty($inputs['username']) || empty($inputs['firstName']) || empty($inputs['lastName']) || empty($inputs['email']) || $inputs['age'] < 2 || $inputs['age'] > 28) {
    $_SESSION['error'] = "Invalid or missing required form fields.";
    header('Location: ' . BASE_URL . '/addstudents');
    exit;
}

try {
    // Unique checks
    $checkStmt = $pdo->prepare("SELECT username, email FROM students WHERE username = :username OR email = :email");
    $checkStmt->execute([
        ':username' => $inputs['username'],
        ':email'    => $inputs['email']
    ]);
    $rows = $checkStmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($rows)) {
        $errors = [];
        foreach ($rows as $row) {
            if (strcasecmp($row['username'], $inputs['username']) === 0) {
                $errors[] = "Username '{$inputs['username']}' is already taken.";
            }
            if (strcasecmp($row['email'], $inputs['email']) === 0) {
                $errors[] = "Email '{$inputs['email']}' is already registered.";
            }
        }
        $_SESSION['error'] = implode(' ', array_unique($errors));
        header('Location: ' . BASE_URL . '/addstudents');
        exit;
    }

    // Process files
    $dirs = prepareUploadDirectories();
    list($dbImgPath, $dbThumbPath) = handleProfileImageUpload($_FILES['profile_image'] ?? null, $dirs, BASE_URL . '/addstudents');
    $dbFilePath = handleAttachmentUpload($_FILES['attachment'] ?? null, $dirs, BASE_URL . '/addstudents');

    // Insert record
    $sql = "INSERT INTO students (username, first_name, last_name, age, email, nickname, gender, membership, newsletter, img, thumb, file) 
            VALUES (:username, :first_name, :last_name, :age, :email, :nickname, :gender, :membership, :newsletter, :img, :thumb, :file)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username'   => $inputs['username'],
        ':first_name'  => $inputs['firstName'],
        ':last_name'   => $inputs['lastName'],
        ':age'        => $inputs['age'],
        ':email'      => $inputs['email'],
        ':nickname'   => $inputs['nickname'],
        ':gender'     => $inputs['gender'],
        ':membership' => $inputs['membership'],
        ':newsletter' => $inputs['newsletter'],
        ':img'        => $dbImgPath,
        ':thumb'      => $dbThumbPath,
        ':file'       => $dbFilePath
    ]);

    header('Location: ' . BASE_URL . '/students?status=success&method=' . $requestMethod);
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = ($e->getCode() == 23000) 
        ? "Registration Error: Username or Email is already registered." 
        : "Database Error: " . $e->getMessage();
    header('Location: ' . BASE_URL . '/addstudents');
    exit;
}