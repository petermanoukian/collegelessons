<?php
// Load config directly first to establish ROOT_PATH and BASE_URL constants
require_once dirname(__DIR__) . '/includes/config.php';

// Include common process script using ROOT_PATH
require_once ROOT_PATH . '/includes/operations/common_process.php';

// Verify request
if ($requestMethod !== 'POST' && !($requestMethod === 'GET' && isset($_GET['username']))) {
    header('Location: ' . BASE_URL . '/students');
    exit;
}

$id = filter_var($inputData['id'] ?? null, FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['error'] = "Invalid record ID for update.";
    header('Location: ' . BASE_URL . '/students');
    exit;
}

$inputs = getSanitizedFormInputs($inputData);

// Basic server validation
if (empty($inputs['username']) || empty($inputs['firstName']) || empty($inputs['lastName']) || empty($inputs['email']) || $inputs['age'] < 2 || $inputs['age'] > 28) {
    $_SESSION['error'] = "Invalid or missing required form fields.";
    header('Location: ' . BASE_URL . "/edit.php?id={$id}");
    exit;
}

try {
    // Check existing record
    $fetchStmt = $pdo->prepare("SELECT img, thumb, file FROM students WHERE id = :id LIMIT 1");
    $fetchStmt->execute([':id' => $id]);
    $existing = $fetchStmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing) {
        $_SESSION['error'] = "Record not found.";
        header('Location: ' . BASE_URL . '/students');
        exit;
    }

    // Check unique username or email for other records
    $checkStmt = $pdo->prepare("SELECT username, email FROM students WHERE (username = :username OR email = :email) AND id != :id");
    $checkStmt->execute([
        ':username' => $inputs['username'],
        ':email'    => $inputs['email'],
        ':id'       => $id
    ]);
    $rows = $checkStmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($rows)) {
        $errors = [];
        foreach ($rows as $row) {
            if (strcasecmp($row['username'], $inputs['username']) === 0) {
                $errors[] = "Username '{$inputs['username']}' is taken by another account.";
            }
            if (strcasecmp($row['email'], $inputs['email']) === 0) {
                $errors[] = "Email '{$inputs['email']}' is registered to another account.";
            }
        }
        $_SESSION['error'] = implode(' ', array_unique($errors));
        header('Location: ' . BASE_URL . "/edit.php?id={$id}");
        exit;
    }

    // Directories and defaults
    $dirs        = prepareUploadDirectories();
    $dbImgPath   = $existing['img'];
    $dbThumbPath = $existing['thumb'];
    $dbFilePath  = $existing['file'];

    // Handle new uploads & purge old ones using ROOT_PATH
    list($newImg, $newThumb) = handleProfileImageUpload($_FILES['profile_image'] ?? null, $dirs, BASE_URL . "/edit.php?id={$id}");
    if ($newImg) {
        if (!empty($existing['img']) && file_exists(ROOT_PATH . '/' . $existing['img'])) {
            @unlink(ROOT_PATH . '/' . $existing['img']);
        }
        if (!empty($existing['thumb']) && file_exists(ROOT_PATH . '/' . $existing['thumb'])) {
            @unlink(ROOT_PATH . '/' . $existing['thumb']);
        }
        $dbImgPath   = $newImg;
        $dbThumbPath = $newThumb;
    }

    $newFile = handleAttachmentUpload($_FILES['attachment'] ?? null, $dirs, BASE_URL . "/edit.php?id={$id}");
    if ($newFile) {
        if (!empty($existing['file']) && file_exists(ROOT_PATH . '/' . $existing['file'])) {
            @unlink(ROOT_PATH . '/' . $existing['file']);
        }
        $dbFilePath = $newFile;
    }

    // Execute update query
    $sql = "UPDATE students SET 
                username   = :username,
                first_name = :first_name,
                last_name  = :last_name,
                age        = :age,
                email      = :email,
                nickname   = :nickname,
                gender     = :gender,
                membership = :membership,
                newsletter = :newsletter,
                img        = :img,
                thumb      = :thumb,
                file       = :file
            WHERE id = :id";

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
        ':file'       => $dbFilePath,
        ':id'         => $id
    ]);

    header('Location: ' . BASE_URL . '/students?status=success&method=' . $requestMethod);
    exit;

} catch (PDOException $e) {
    $_SESSION['error'] = ($e->getCode() == 23000) 
        ? "Update Error: Username or Email is already registered to another account." 
        : "Database Error: " . $e->getMessage();
    header('Location: ' . BASE_URL . "/edit.php?id={$id}");
    exit;
}