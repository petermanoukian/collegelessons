<?php
// Set page title for header
$pageTitle = 'View records by Div Layout';

// Include central database connection ($pdo)
require_once __DIR__ . '/includes/connection.inc.php';

$statusMessage = '';
if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $methodUsed = htmlspecialchars($_GET['method'] ?? 'POST');
    $statusMessage = "Record successfully saved using $methodUsed request!";
}

// Fetch records from database
try {
    $sql = "SELECT id, username, first_name, last_name, age, email, nickname, gender, membership, newsletter, created_at 
            FROM students 
            ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $records = [];
    $errorMessage = "Database Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/head.inc.php'; ?>
<body>

    <?php require_once 'includes/header.inc.php'; ?>

    <main class="wide-container">
        <a href="form.php" class="nav-link">&larr; Back to Registration Form</a> | 
        <a href="formajax.php" class="nav-link">&rarr; AJAX Registration Form</a> | 
        <a href="view.php" class="nav-link">&rarr; View Saved Records by table</a> 

        <h2>Saved Submissions</h2>

        <?php if (!empty($statusMessage)): ?>
            <div class="alert-success"><?= $statusMessage ?></div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert-error" style="color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <?php
        if (!empty($records)) {
        ?>
        <div class = 'padding-bottom full-width'>
        <?php
            $total = count($records);

            foreach ($records as $index => $row) {
                // Open a new 3-item group wrapper at the start of every 3 items
                if ($index % 3 === 0) {
                    if ($index > 0) {
                        echo '<hr class="divider">';
                    }
                    echo '<div class="records-group">';
                }

                // Individual 33% Record Card Div
                ?>
                <div class="record-card">
                    <h3>#<?= $index + 1 ?> — <?= htmlspecialchars($row['username']) ?></h3>
                    <p><strong>Name:</strong> <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                    <p><strong>Age:</strong> <?= htmlspecialchars($row['age']) ?></p>
                    <p><strong>Nickname:</strong> <?= htmlspecialchars($row['nickname']) ?></p>
                    <p><strong>Gender:</strong> <?= htmlspecialchars($row['gender']) ?></p>
                    <p><strong>Membership:</strong> <?= htmlspecialchars($row['membership']) ?></p>
                    <p><strong>Newsletter:</strong> <?= $row['newsletter'] ? 'Yes' : 'No' ?></p>
                </div>
                <?php

                // Close group wrapper at 3 items or end of records
                if (($index + 1) % 3 === 0 || ($index + 1) === $total) {
                    echo '</div>'; 
                }
            
            

            }
            ?>

        </div>
        <?php
        } else {
            echo '<p>No records stored yet.</p>';
        }
        ?>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>