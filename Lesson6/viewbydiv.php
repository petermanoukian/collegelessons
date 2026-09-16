<?php
$pageTitle = 'View records by Div Layout and search/group/distinct filters';

// Execute shared POST query processing
require_once __DIR__ . '/includes/operations/searchquery.php';
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/head.inc.php'; ?>
<body>

    <?php require_once 'includes/header.inc.php'; ?>

    <main class="wide-container">
        <a href="form.php" class="nav-link">&larr; Back to Registration Form</a> | 
        <a href="formajax.php" class="nav-link">&rarr; AJAX Registration Form</a> | 
        <a href="view.php" class="nav-link">&rarr; View Saved Records by table</a> |
        <a href="viewbydiv.php" class="nav-link">&rarr; View Saved Records by Div Layout (RESET)</a> 
        <h2>Saved Submissions</h2>

        <?php if (!empty($statusMessage)): ?>
            <div class="alert-success"><?= $statusMessage ?></div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert-error" style="color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <!-- Render Shared Forms -->
        <?php require_once 'includes/searchform.php'; ?>

        <!-- RECORDS DISPLAY (CARDS GRID) -->
        <?php if (!empty($records)): ?>
            <div class="padding-bottom full-width">
                <?php
                $total = count($records);

                foreach ($records as $index => $row) {
                    if ($index % 3 === 0) {
                        if ($index > 0) {
                            echo '<hr class="divider">';
                        }
                        echo '<div class="records-group">';
                    }
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
                    if (($index + 1) % 3 === 0 || ($index + 1) === $total) {
                        echo '</div>'; 
                    }
                }
                ?>
            </div>
        <?php else: ?>
            <p>No matching records found.</p>
        <?php endif; ?>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>