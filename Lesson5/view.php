<?php
// Set page title for header
$pageTitle = 'View records by Table';

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
        <a href="form.php" class="nav-link">&larr; Back to Registration Form</a>
        | 
        <a href="formajax.php" class="nav-link">&rarr; AJAX Registration Form</a> | 
        <a href="viewbydiv.php" class="nav-link">&rarr; View Saved Records by div</a>
        
        <h2>Saved Submissions</h2>

        <?php if (!empty($statusMessage)): ?>
            <div class="alert-success"><?= $statusMessage ?></div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert-error" style="color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($records)): ?>
            <table class="records-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Age</th>
                        <th>Email</th>
                        <th>Nickname</th>
                        <th>Gender</th>
                        <th>Membership</th>
                        <th>Newsletter</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $index => $row): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['age']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['nickname']) ?></td>
                            <td><?= htmlspecialchars($row['gender']) ?></td>
                            <td><?= htmlspecialchars($row['membership']) ?></td>
                            <td><?= $row['newsletter'] ? 'Yes' : 'No' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No records stored yet.</p>
        <?php endif; ?>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>