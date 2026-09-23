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
            // Formats like TIFF/SVG might not be thumbnailable via standard GD library without Imagick
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
        header('Location: addstudents');
        exit;
    }

    try {
        // 1. Check if either username or email already exists
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

            $_SESSION['error'] = implode(' ', $errors);
            header('Location: addstudents');
            exit;
        }

        // 2. Define Upload Directories
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

        $dbImgPath   = null;
        $dbThumbPath = null;
        $dbFilePath  = null;

        // 3. Optional Image Upload & Server-side Validation
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

            // Verify both file extension AND real server MIME type
            if (!in_array($imgExt, $allowedImgExts) || !isValidMimeType($imgTmp, $allowedImgMimes)) {
                $_SESSION['error'] = "Security Alert: Invalid profile picture type.";
                header('Location: addstudents');
                exit;
            }

            $uniqueImgName = time() . '_' . uniqid() . '.' . $imgExt;

            $targetImgFile   = $imgDir . $uniqueImgName;
            $targetThumbFile = $thumbDir . $uniqueImgName;

            if (move_uploaded_file($imgTmp, $targetImgFile)) {
                // Try creating 150x150 thumbnail (if supported by GD library)
                $thumbCreated = createThumbnail($targetImgFile, $targetThumbFile, 150, 150);

                // Relative URLs for DB
                $dbImgPath   = 'uploads/student/img/' . $uniqueImgName;
                $dbThumbPath = $thumbCreated ? 'uploads/student/img/thumb/' . $uniqueImgName : $dbImgPath;
            }
        }

        // 4. Optional File Attachment Upload & Server-side Validation
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

            // Verify both extension AND real MIME type
            if (!in_array($fileExt, $allowedFileExts) || !isValidMimeType($fileTmp, $allowedFileMimes)) {
                $_SESSION['error'] = "Security Alert: Invalid attachment file type.";
                header('Location: addstudents');
                exit;
            }

            $uniqueFileName = time() . '_doc_' . uniqid() . '.' . $fileExt;
            $targetDocFile  = $fileDir . $uniqueFileName;

            if (move_uploaded_file($fileTmp, $targetDocFile)) {
                $dbFilePath = 'uploads/student/file/' . $uniqueFileName;
            }
        }

        // 5. Insert Record into Database
        $sql = "INSERT INTO students (username, first_name, last_name, age, email, nickname, gender, membership, newsletter, img, thumb, file) 
                VALUES (:username, :first_name, :last_name, :age, :email, :nickname, :gender, :membership, :newsletter, :img, :thumb, :file)";

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
            ':file'       => $dbFilePath
        ]);

        // Redirect to clean students URL upon success
        header('Location: students?status=success&method=' . $requestMethod);
        exit;

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = "Registration Error: Username or Email is already registered.";
        } else {
            $_SESSION['error'] = "Database Error: " . $e->getMessage();
        }
        
        header('Location: addstudents');
        exit;
    }

} else {
    header('Location: addstudents');
    exit;
}