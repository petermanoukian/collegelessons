<?php
// Always start the session first to handle flash messages
session_start();

// Include the database connection ($pdo)
require_once __DIR__ . '/includes/connection.inc.php';

// Detect request method
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Helper function to verify actual MIME types on server using PHP Fileinfo
function isValidMimeType($tmpFilePath, $allowedMimeTypes) {
    if (!file_exists($tmpFilePath)) {
        return false;
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $realMimeType = finfo_file($finfo, $tmpFilePath);
    finfo_close($finfo);

    return in_array($realMimeType, $allowedMimeTypes, true);
}

// Helper function to create thumbnail using native PHP GD library
function createThumbnail($sourcePath, $targetPath, $targetWidth = 150, $targetHeight = 150) {
    $imageInfo = @getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }

    $type = $imageInfo[2];
    $origWidth = $imageInfo[0];
    $origHeight = $imageInfo[1];

    switch ($type) {
        case IMAGETYPE_JPEG:
            $srcImg = @imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $srcImg = @imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $srcImg = @imagecreatefromgif($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            $srcImg = @imagecreatefromwebp($sourcePath);
            break;
        case IMAGETYPE_BMP:
            $srcImg = @imagecreatefrombmp($sourcePath);
            break;
        default:
            // Formats like TIFF or SVG cannot be thumbnailed natively via basic GD
            return false;
    }

    if (!$srcImg) {
        return false;
    }

    $thumb = imagecreatetruecolor($targetWidth, $targetHeight);

    // Preserve transparency for PNG and WEBP
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }

    // Resize image proportionally
    imagecopyresampled($thumb, $srcImg, 0, 0, 0, 0, $targetWidth, $targetHeight, $origWidth, $origHeight);

    // Save thumbnail
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumb, $targetPath, 85);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumb, $targetPath);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumb, $targetPath);
            break;
        case IMAGETYPE_WEBP:
            imagewebp($thumb, $targetPath, 85);
            break;
        case IMAGETYPE_BMP:
            imagebmp($thumb, $targetPath);
            break;
    }

    imagedestroy($srcImg);
    imagedestroy($thumb);
    return true;
}

// Verify submission presence across POST or GET
if ($requestMethod === 'POST' || ($requestMethod === 'GET' && isset($_GET['username']))) {

    $inputData = ($requestMethod === 'POST') ? $_POST : $_GET;

    // Collect ID
    $id = filter_var($inputData['id'] ?? null, FILTER_VALIDATE_INT);

    if (!$id) {
        $_SESSION['error'] = "Invalid record ID for update.";
        header('Location: students');
        exit;
    }

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

    // Server-side basic validation
    if (empty($username) || empty($firstName) || empty($lastName) || empty($email) || $age < 2 || $age > 28) {
        $_SESSION['error'] = "Invalid or missing required form fields.";
        header("Location: edit.php?id={$id}");
        exit;
    }

    try {
        // 1. Fetch current record to preserve files if new ones are not uploaded
        $fetchStmt = $pdo->prepare("SELECT img, thumb, file FROM students WHERE id = :id LIMIT 1");
        $fetchStmt->execute([':id' => $id]);
        $existingRecord = $fetchStmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingRecord) {
            $_SESSION['error'] = "Record not found.";
            header('Location: students');
            exit;
        }

        // 2. Check if username or email is already taken by ANOTHER record
        $checkSql = "SELECT username, email FROM students WHERE (username = :username OR email = :email) AND id != :id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            ':username' => $username,
            ':email'    => $email,
            ':id'       => $id
        ]);
        $rows = $checkStmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            $errors = [];
            $usernameTaken = false;
            $emailTaken = false;

            foreach ($rows as $row) {
                if (!$usernameTaken && strcasecmp($row['username'], $username) === 0) {
                    $errors[] = "Username '{$username}' is already taken by another account.";
                    $usernameTaken = true;
                }
                if (!$emailTaken && strcasecmp($row['email'], $email) === 0) {
                    $errors[] = "Email '{$email}' is already registered to another account.";
                    $emailTaken = true;
                }
            }

            $_SESSION['error'] = implode(' ', $errors);
            header("Location: edit.php?id={$id}");
            exit;
        }

        // 3. Define Upload Directories
        $baseUploadDir = __DIR__ . '/uploads/student/';
        $imgDir        = $baseUploadDir . 'img/';
        $thumbDir      = $imgDir . 'thumb/';
        $fileDir       = $baseUploadDir . 'file/';

        // Automatically create directories if they don't exist
        foreach ([$imgDir, $thumbDir, $fileDir] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        // Retain existing DB file paths by default
        $dbImgPath   = $existingRecord['img'];
        $dbThumbPath = $existingRecord['thumb'];
        $dbFilePath  = $existingRecord['file'];

        // 4. Handle Optional Image Upload & Server-side MIME Validation
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $imgTmp  = $_FILES['profile_image']['tmp_name'];
            $imgName = $_FILES['profile_image']['name'];
            $imgExt  = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));

            $allowedImgExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'tiff', 'tif', 'bmp', 'svg', 'ico', 'avif'];
            $allowedImgMimes = [
                'image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 
                'image/webp', 'image/tiff', 'image/x-tiff', 'image/bmp', 
                'image/x-ms-bmp', 'image/svg+xml', 'image/x-icon', 'image/avif'
            ];

            // MIME + Extension check
            if (!in_array($imgExt, $allowedImgExts) || !isValidMimeType($imgTmp, $allowedImgMimes)) {
                $_SESSION['error'] = "Security Alert: Invalid profile picture file type.";
                header("Location: edit.php?id={$id}");
                exit;
            }

            $uniqueImgName = time() . '_' . uniqid() . '.' . $imgExt;

            $targetImgFile   = $imgDir . $uniqueImgName;
            $targetThumbFile = $thumbDir . $uniqueImgName;

            if (move_uploaded_file($imgTmp, $targetImgFile)) {
                // Try to create 150x150 thumbnail
                $thumbCreated = createThumbnail($targetImgFile, $targetThumbFile, 150, 150);

                // Delete old image files if they exist
                if (!empty($existingRecord['img']) && file_exists(__DIR__ . '/' . $existingRecord['img'])) {
                    @unlink(__DIR__ . '/' . $existingRecord['img']);
                }
                if (!empty($existingRecord['thumb']) && file_exists(__DIR__ . '/' . $existingRecord['thumb'])) {
                    @unlink(__DIR__ . '/' . $existingRecord['thumb']);
                }

                // Update URLs for DB
                $dbImgPath   = 'uploads/student/img/' . $uniqueImgName;
                $dbThumbPath = $thumbCreated ? 'uploads/student/img/thumb/' . $uniqueImgName : $dbImgPath;
            }
        }

        // 5. Handle Optional Attachment Upload & Server-side MIME Validation
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $fileTmp  = $_FILES['attachment']['tmp_name'];
            $fileName = $_FILES['attachment']['name'];
            $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedFileExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'tiff', 'tif', 'bmp', 'pdf', 'doc', 'docx', 'txt'];
            $allowedFileMimes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/tiff', 'image/x-tiff', 'image/bmp',
                'application/pdf', 'text/plain', 'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];

            // MIME + Extension check
            if (!in_array($fileExt, $allowedFileExts) || !isValidMimeType($fileTmp, $allowedFileMimes)) {
                $_SESSION['error'] = "Security Alert: Invalid attachment file type.";
                header("Location: edit.php?id={$id}");
                exit;
            }

            $uniqueFileName = time() . '_doc_' . uniqid() . '.' . $fileExt;
            $targetDocFile  = $fileDir . $uniqueFileName;

            if (move_uploaded_file($fileTmp, $targetDocFile)) {
                // Delete old attachment file
                if (!empty($existingRecord['file']) && file_exists(__DIR__ . '/' . $existingRecord['file'])) {
                    @unlink(__DIR__ . '/' . $existingRecord['file']);
                }

                $dbFilePath = 'uploads/student/file/' . $uniqueFileName;
            }
        }

        // 6. Update Record in Database
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
            ':username'   => $username,
            ':first_name'  => $firstName,
            ':last_name'   => $lastName,
            ':age'        => $age,
            ':email'      => $email,
            ':nickname'   => $nickname,
            ':gender'     => $gender,
            ':membership' => $membership,
            ':newsletter' => $newsletter,
            ':img'        => $dbImgPath,
            ':thumb'      => $dbThumbPath,
            ':file'       => $dbFilePath,
            ':id'         => $id
        ]);

        // Redirect to clean students URL with updated status
        header('Location: students?status=success&method=' . $requestMethod);
        exit;

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = "Update Error: Username or Email is already registered to another account.";
        } else {
            $_SESSION['error'] = "Database Error: " . $e->getMessage();
        }

        header("Location: edit.php?id={$id}");
        exit;
    }

} else {
    header('Location: students');
    exit;
}