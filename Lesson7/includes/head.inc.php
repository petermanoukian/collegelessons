<?php require_once __DIR__ . '/config.php'; ?>
<?php
if (!function_exists('asset')) {
    function asset($path) {
        $fullPath = __DIR__ . '/..' . $path;
        $version = file_exists($fullPath) ? filemtime($fullPath) : time();
        return BASE_URL . $path . '?v=' . $version;
    }
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Lesson 7') ?> - <?= COLLEGE_NAME ?></title>

    <!-- Base CSS Stylesheets -->
    <link rel="stylesheet" href="<?= asset('/css/main.css') ?>">
    <link rel="stylesheet" href="<?= asset('/css/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('/css/form.css') ?>">
    <link rel="stylesheet" href="<?= asset('/css/footer.css') ?>">

    <!-- Inject Page-Specific Styles or Scripts -->
    <?php if (isset($extraHead)) echo $extraHead; ?>
</head>