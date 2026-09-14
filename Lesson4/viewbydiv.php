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
        <p>Lesson 4: Submissions Viewer (Div Layout)</p>
    </header>

    <main class="wide-container">
        <a href="form.php" class="nav-link">&larr; Back to Registration Form</a> | 
        <a href="view.php" class="nav-link">&rarr; View Saved Records by table</a> 

        <h2>Saved Submissions</h2>

        <?php if (!empty($statusMessage)): ?>
            <div class="alert-success"><?= $statusMessage ?></div>
        <?php endif; ?>

        <?php
        if (file_exists($filePath) && filesize($filePath) > 0) {
            $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $total = count($lines);

            foreach ($lines as $index => $line) {
                // Open a new 3-item group wrapper at the start of every 3 items
                if ($index % 3 === 0) {
                    if ($index > 0) {
                        echo '<hr class="divider">';
                    }
                    echo '<div class="records-group">';
                }

                // Individual 33% Record Div
                echo '<div class="record-card">';
                echo '<strong>#' . ($index + 1) . '</strong> ' . htmlspecialchars($line);
                echo '</div>';

                // Close group wrapper at 3 items or end of records
                if (($index + 1) % 3 === 0 || ($index + 1) === $total) {
                    echo '</div>'; 
                }
            }
        } else {
            echo '<p>No records stored yet.</p>';
        }
        ?>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>