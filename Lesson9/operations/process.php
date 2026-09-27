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

    // 1. Generate 6-digit activation code
    $activationCode = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    // 2. Insert record (password stays NULL, activated defaults to 'inactive')
    $sql = "INSERT INTO students (username, code, first_name, last_name, age, email, nickname, gender, membership, newsletter, img, thumb, file) 
            VALUES (:username, :code, :first_name, :last_name, :age, :email, :nickname, :gender, :membership, :newsletter, :img, :thumb, :file)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username'   => $inputs['username'],
        ':code'       => $activationCode,
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

    // 3. Attempt to send email via mail()
    $mailSent = false;
    try {
        $to = $inputs['email'];
        $subject = "Your Activation Code";
        $message = "Hello " . htmlspecialchars($inputs['firstName']) . ",\n\nYour activation code is: " . $activationCode;
        $headers = "From: no-reply@localhost\r\n" .
                   "Reply-To: no-reply@localhost\r\n" .
                   "X-Mailer: PHP/" . phpversion();

        $mailSent = @mail($to, $subject, $message, $headers);
    } catch (Throwable $e) {
        $mailSent = false;
    }

    // 4. Set session flash message
    $msg = "Student created successfully! Activation Code: <strong>{$activationCode}</strong>";
    if ($mailSent) {
        $msg .= " Activation email sent via local mailer";
    } else {
        $msg .= " Local email could not be sent";
    }
    $_SESSION['success'] = $msg;

$codeMessage = urlencode("The activation code is $activationCode Sent Mail " . ($mailSent ? '1' : '0'));
    header('Location: ' . BASE_URL . '/students?status=success&method=' . $requestMethod . '&msg=' . $codeMessage);
    exit;
   

} catch (PDOException $e) {
    $_SESSION['error'] = ($e->getCode() == 23000) 
        ? "Registration Error: Username or Email is already registered." 
        : "Database Error: " . $e->getMessage();
    header('Location: ' . BASE_URL . '/addstudents');
    exit;
}