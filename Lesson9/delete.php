<?php
session_start();

// Include central database connection
require_once __DIR__ . '/includes/connection.inc.php';

// Get ID from GET or POST request
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['error'] = 'Invalid or missing record ID.';
    header('Location: view.php');
    exit;
}

try {
    // 1. Fetch existing record to find associated files
    $stmt = $pdo->prepare("SELECT img, thumb, file FROM students WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($record) {
        // 2. Delete physical files from server if present
        if (!empty($record['img']) && file_exists(__DIR__ . '/' . $record['img'])) {
            @unlink(__DIR__ . '/' . $record['img']);
        }
        if (!empty($record['thumb']) && file_exists(__DIR__ . '/' . $record['thumb'])) {
            @unlink(__DIR__ . '/' . $record['thumb']);
        }
        if (!empty($record['file']) && file_exists(__DIR__ . '/' . $record['file'])) {
            @unlink(__DIR__ . '/' . $record['file']);
        }

        // 3. Delete database record
        $deleteStmt = $pdo->prepare("DELETE FROM students WHERE id = :id LIMIT 1");
        $deleteStmt->execute([':id' => $id]);

        $_SESSION['success'] = 'Record #' . $id . ' and all associated files deleted successfully.';
    } else {
        $_SESSION['error'] = 'Record not found or already deleted.';
    }

} catch (PDOException $e) {
    $_SESSION['error'] = 'Database error while deleting record: ' . $e->getMessage();
}

// Redirect back to view page
header('Location: view.php');
exit;