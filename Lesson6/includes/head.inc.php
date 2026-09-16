<?php require_once __DIR__ . '/config.php'; ?>
<?php
function asset($path) {
    $fullPath = __DIR__ . '/..' . $path; // Adjust relative path to public root if needed
    $version = file_exists($fullPath) ? filemtime($fullPath) : time();
    return BASE_URL . $path . '?v=' . $version;
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Lesson 6') ?> - <?= COLLEGE_NAME ?></title>

    <!-- External CSS Imports dynamically using BASE_URL -->


<!-- Usage in your HTML head: -->
<link rel="stylesheet" href="<?= asset('/css/main.css') ?>">
<link rel="stylesheet" href="<?= asset('/css/header.css') ?>">
<link rel="stylesheet" href="<?= asset('/css/form.css') ?>">
<link rel="stylesheet" href="<?= asset('/css/footer.css') ?>">
</head>