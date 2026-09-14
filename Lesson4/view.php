<?php
$filePath = __DIR__ . '/data/submissions.txt';
$statusMessage = '';

if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $methodUsed = htmlspecialchars($_GET['method'] ?? 'POST');
    $statusMessage = "Record successfully saved using $methodUsed request!";
}
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/header.php'; ?>
<body>

    <header>
        <h1>PHP & Web Development Lessons</h1>
        <p>Lesson 4: Submissions Viewer</p>
    </header>

    <main>
        <a href="form.php" class="nav-link">&larr; Back to Registration Form</a>
        | 
        <a href="viewbydiv.php" class="nav-link">&rarr; View Saved Records by div</a>
        <h2>Saved Submissions</h2>

        <?php if (!empty($statusMessage)): ?>
            <div class="alert-success"><?= $statusMessage ?></div>
        <?php endif; ?>

        <?php
        if (file_exists($filePath) && filesize($filePath) > 0) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            echo '<table class="records-table">';
            echo '<tr><th>#</th><th>Submission Entry</th></tr>';
            foreach ($lines as $index => $line) {
                echo '<tr>';
                echo '<td>' . ($index + 1) . '</td>';
                echo '<td>' . htmlspecialchars($line) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>No records stored yet.</p>';
        }
        ?>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>