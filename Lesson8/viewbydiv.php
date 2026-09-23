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



        <!-- Render Shared Forms -->
        <?php require_once 'includes/searchform.php'; ?>


        <!-- FLASH MESSAGES CONTAINER -->
        <div id="flash-message-container">
            <?php if (!empty($statusMessage)): ?>
                <div class="alert-success"><?= $statusMessage ?></div>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <div class="alert-error">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>
        </div>

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

                        <!-- Profile Thumbnail with Full Image Link -->
                        <?php if (!empty($row['thumb']) && !empty($row['img'])): ?>
                            <div class="card-avatar">
                                <a href="<?= htmlspecialchars($row['img']) ?>" target="_blank" title="View Full Image">
                                    <img src="<?= htmlspecialchars($row['thumb']) ?>" alt="Profile" class="user-thumb">
                                </a>
                            </div>
                        <?php elseif (!empty($row['img'])): ?>
                            <div class="card-avatar">
                                <a href="<?= htmlspecialchars($row['img']) ?>" target="_blank" title="View Full Image">
                                    <img src="<?= htmlspecialchars($row['img']) ?>" alt="Profile" class="user-thumb">
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- File Attachment Link -->
                        <?php if (!empty($row['file'])): ?>
                            <p class="card-file-link">
                                <strong>Attachment:</strong> 
                                <a href="<?= htmlspecialchars($row['file']) ?>" target="_blank">View File</a>
                            </p>
                        <?php endif; ?>

                        <p><strong>Name:</strong> <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                        <p><strong>Age:</strong> <?= htmlspecialchars($row['age']) ?></p>
                        <p><strong>Nickname:</strong> <?= htmlspecialchars($row['nickname']) ?></p>
                        <p><strong>Gender:</strong> <?= htmlspecialchars($row['gender']) ?></p>
                        <p><strong>Membership:</strong> <?= htmlspecialchars($row['membership']) ?></p>
                        <p><strong>Newsletter:</strong> <?= $row['newsletter'] ? 'Yes' : 'No' ?></p>

                        <!-- Action Buttons -->
                        <div class="card-actions">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn-edit">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                        </div>
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
    <?php require_once 'includes/script.inc.php'; ?>
    <script>
    $(document).ready(function() {
        var $flashContainer = $('#flash-message-container');
        var $alert = $flashContainer.find('.alert-success, .alert-error');

        if ($alert.length > 0) {
            $('html, body').animate({
                scrollTop: $flashContainer.offset().top - 20
            }, 1200);
        }
    });
    </script>

</body>
</html>