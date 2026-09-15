<?php require_once __DIR__ . '/config.php'; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Lesson 4') ?> - <?= COLLEGE_NAME ?></title>

    <!-- External CSS Imports using config BASE_URL -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/main.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/header.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/form.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/footer.css">
</head>