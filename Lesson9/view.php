<?php
$pageTitle = 'View records by Table Layout and search/group/distinct filters';

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
        <a href="viewbydiv.php" class="nav-link">&rarr; View Saved Records by Div Layout</a> |
        <a href="view.php" class="nav-link">&rarr; View Saved Records by Table (RESET)</a> 
        
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

        <!-- RECORDS DISPLAY (TABLE LAYOUT) -->
        <?php if (!empty($records)): ?>
            <div class="table-responsive">
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $index => $row): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <div class="user-cell-content">
                                        <strong><?= htmlspecialchars($row['username']) ?></strong>
                                        
                                        <!-- Thumbnail linked to original image -->
                                        <?php if (!empty($row['thumb']) && !empty($row['img'])): ?>
                                            <a href="<?= htmlspecialchars($row['img']) ?>" target="_blank" title="View Full Image">
                                                <img src="<?= htmlspecialchars($row['thumb']) ?>" alt="Profile" class="user-thumb">
                                            </a>
                                        <?php elseif (!empty($row['img'])): ?>
                                            <a href="<?= htmlspecialchars($row['img']) ?>" target="_blank" title="View Full Image">
                                                <img src="<?= htmlspecialchars($row['img']) ?>" alt="Profile" class="user-thumb">
                                            </a>
                                        <?php endif; ?>

                                        <!-- Attached File Link -->
                                        <?php if (!empty($row['file'])): ?>
                                            <a href="<?= htmlspecialchars($row['file']) ?>" target="_blank" style="font-size:0.85rem;">&rsaquo; View File</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                <td><?= htmlspecialchars($row['age']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['nickname']) ?></td>
                                <td><?= htmlspecialchars($row['gender']) ?></td>
                                <td><?= htmlspecialchars($row['membership']) ?></td>
                                <td><?= $row['newsletter'] ? 'Yes' : 'No' ?></td>
                                <td class="action-links">
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No matching records found.</p>
        <?php endif; ?>
    </main>

    <?php require_once 'includes/footer.php'; ?>

</body>
</html>