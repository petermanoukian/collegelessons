<?php
// Always start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include Configuration (Guarantees ROOT_PATH & BASE_URL exist everywhere)
require_once __DIR__ . '/../config.php';

// Include DB Connection using ROOT_PATH
require_once ROOT_PATH . '/includes/connection.inc.php';

// Detect request method
$requestMethod = $_SERVER['REQUEST_METHOD'];
$inputData     = ($requestMethod === 'POST') ? $_POST : $_GET;

/**
 * Server-side MIME validation via finfo
 */
function isValidMimeType($tmpFilePath, $allowedMimeTypes) {
    if (!file_exists($tmpFilePath)) {
        return false;
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $realMimeType = finfo_file($finfo, $tmpFilePath);
    finfo_close($finfo);

    return in_array($realMimeType, $allowedMimeTypes, true);
}

/**
 * Native GD Thumbnail Creator
 */
function createThumbnail($sourcePath, $targetPath, $targetWidth = 150, $targetHeight = 150) {
    $imageInfo = @getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }

    $type       = $imageInfo[2];
    $origWidth  = $imageInfo[0];
    $origHeight = $imageInfo[1];

    // 1. Create source image resource based on type
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
        case IMAGETYPE_TIFF_II:
        case IMAGETYPE_TIFF_MM:
            if (function_exists('imagecreatefromtiff')) {
                $srcImg = @imagecreatefromtiff($sourcePath);
            } else {
                return false;
            }
            break;
        default:
            return false;
    }

    if (!$srcImg) {
        return false;
    }

    $thumb = imagecreatetruecolor($targetWidth, $targetHeight);

    // Preserve transparency for PNG and WebP
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }

    // Resample image into thumbnail canvas
    imagecopyresampled($thumb, $srcImg, 0, 0, 0, 0, $targetWidth, $targetHeight, $origWidth, $origHeight);

    // 2. Output and save the thumbnail file to disk
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
        case IMAGETYPE_TIFF_II:
        case IMAGETYPE_TIFF_MM:
            // GD lacks native imagetiff(); save thumbnail as JPEG
            imagejpeg($thumb, $targetPath, 85);
            break;
        default:
            imagejpeg($thumb, $targetPath, 85);
            break;
    }

    imagedestroy($srcImg);
    imagedestroy($thumb);
    return true;
}

/**
 * Extract and sanitize standardized form inputs
 */
function getSanitizedFormInputs($data) {
    return [
        'username'   => trim($data['username'] ?? ''),
        'firstName'  => trim($data['firstname'] ?? ''),
        'lastName'   => trim($data['lastname'] ?? ''),
        'age'        => (int)($data['age'] ?? 0),
        'email'      => trim($data['email'] ?? ''),
        'nickname'   => !empty(trim($data['nickname'] ?? '')) ? trim($data['nickname']) : 'N/A',
        'gender'     => $data['gender'] ?? '',
        'membership' => $data['membership'] ?? 'Member',
        'newsletter' => isset($data['newsletter']) ? 1 : 0
    ];
}

/**
 * Helper to ensure target directories exist using ROOT_PATH
 */
function prepareUploadDirectories() {
    $dirs = [
        'img'   => ROOT_PATH . '/uploads/student/img/',
        'thumb' => ROOT_PATH . '/uploads/student/img/thumb/',
        'file'  => ROOT_PATH . '/uploads/student/file/'
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    return $dirs;
}

/**
 * Handle optional image upload & MIME validation
 */
function handleProfileImageUpload($file, $dirs, $redirectUrl) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return [null, null];
    }

    $imgTmp  = $file['tmp_name'];
    $imgName = $file['name'];
    $imgExt  = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));

    $allowedImgExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'tiff', 'tif', 'bmp', 'svg', 'ico', 'avif'];
    $allowedImgMimes = [
        'image/jpeg', 'image/pjpeg', 'image/png', 'image/gif',
        'image/webp', 'image/tiff', 'image/x-tiff', 'image/bmp',
        'image/x-ms-bmp', 'image/svg+xml', 'image/x-icon', 'image/avif'
    ];

    if (!in_array($imgExt, $allowedImgExts) || !isValidMimeType($imgTmp, $allowedImgMimes)) {
        $_SESSION['error'] = "Security Alert: Invalid profile picture type.";
        header('Location: ' . $redirectUrl);
        exit;
    }

    $uniqueImgName   = time() . '_' . uniqid() . '.' . $imgExt;
    $targetImgFile   = $dirs['img'] . $uniqueImgName;
    $targetThumbFile = $dirs['thumb'] . $uniqueImgName;

    if (move_uploaded_file($imgTmp, $targetImgFile)) {
        $thumbCreated = createThumbnail($targetImgFile, $targetThumbFile, 150, 150);
        $dbImg        = 'uploads/student/img/' . $uniqueImgName;
        $dbThumb      = $thumbCreated ? 'uploads/student/img/thumb/' . $uniqueImgName : $dbImg;
        return [$dbImg, $dbThumb];
    }

    return [null, null];
}

/**
 * Handle optional document attachment upload & MIME validation
 */
function handleAttachmentUpload($file, $dirs, $redirectUrl) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $fileTmp  = $file['tmp_name'];
    $fileName = $file['name'];
    $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedFileExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'tiff', 'tif', 'bmp', 'pdf', 'doc', 'docx', 'txt'];
    $allowedFileMimes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/tiff', 'image/x-tiff', 'image/bmp',
        'application/pdf', 'text/plain', 'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    if (!in_array($fileExt, $allowedFileExts) || !isValidMimeType($fileTmp, $allowedFileMimes)) {
        $_SESSION['error'] = "Security Alert: Invalid attachment file type.";
        header('Location: ' . $redirectUrl);
        exit;
    }

    $uniqueFileName = time() . '_doc_' . uniqid() . '.' . $fileExt;
    $targetDocFile  = $dirs['file'] . $uniqueFileName;

    if (move_uploaded_file($fileTmp, $targetDocFile)) {
        return 'uploads/student/file/' . $uniqueFileName;
    }

    return null;
}