<?php
// Set page title for header
$pageTitle = 'Database Connection Test';

// Include central database connection
require_once __DIR__ . '/includes/connection.inc.php';
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . '/includes/head.inc.php'; ?>
<body>

    <?php require_once __DIR__ . '/includes/header.inc.php'; ?>

    <main>
        <h2>Database Connection Test</h2>

        <?php if (isset($pdo)): ?>
            <?php
                // Fetch MySQL server version to verify query execution
                $version = $pdo->query('SELECT VERSION()')->fetchColumn();
            ?>
            <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb; margin-top: 15px;">
                <strong>Success!</strong> Connected to <code>database </code> successfully.<br>
                <strong>MySQL Server Version:</strong> <?= htmlspecialchars($version) ?>
            </div>
        <?php else: ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb; margin-top: 15px;">
                <strong>Error:</strong> PDO instance <code>$pdo</code> is not available. Check <code>connection.inc.php</code>.
            </div>
        <?php endif; ?>

        <p style="margin-top: 20px;">
            <a href="form.php" class="nav-link">&larr; Return to Registration Form</a>
        </p>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>