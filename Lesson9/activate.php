<?php
// Start session to access flash messages
session_start();
require_once __DIR__ . '/includes/config.php';

// Set page title for header
$pageTitle = 'Account Activation';

// Include central database connection
require_once __DIR__ . '/includes/connection.inc.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];
$prefilledCode = htmlspecialchars($_GET['code'] ?? '');

$isAlreadyActivated = false;

// If a code is passed in general, check its activation status immediately
if (!empty($prefilledCode)) {
    try {
        $stmt = $pdo->prepare("SELECT activated FROM students WHERE code = :code");
        $stmt->execute([':code' => $prefilledCode]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student && $student['activated'] === 'active') {
            $isAlreadyActivated = true;
        }
    } catch (PDOException $e) {
        // Handle error quietly
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/head.inc.php'; ?>

<body>

    <?php require_once 'includes/header.inc.php'; ?>

    <main>
        <h2><?= htmlspecialchars($pageTitle) ?></h2>

        <!-- Flash Message Display -->
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="flash-message flash-error" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="flash-message flash-success" style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #c3e6cb;">
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Already Activated Alert Message -->
        <?php if ($isAlreadyActivated): ?>
            <div class="flash-message flash-warning" style="background-color: #fff3cd; color: #856404; padding: 12px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #ffeeba;">
                This account is already activated. <a href="<?= BASE_URL ?>/user/views/login.php" style="color: #856404; font-weight: bold; text-decoration: underline;">Click here to log in</a>.
            </div>
        <?php endif; ?>

        <!-- Method Detector Badge -->
        <div class="method-badge <?= ($requestMethod === 'POST') ? 'method-post' : 'method-get' ?>">
            Current Request Method: <strong><?= $requestMethod ?></strong>
        </div>

        <form id="activationForm" action="<?= BASE_URL ?>/operations/process_activate.php" method="POST">            
            <div class="form-grid">

                <div class="form-group">
                    <label for="code">Activation Code *</label>
                    <input type="text" id="code" name="code" value="<?= $prefilledCode ?>" required maxlength="6" placeholder="Enter 6-digit code" <?= $isAlreadyActivated ? 'disabled' : '' ?>>                
                </div>

                <div class="form-group">
                    <label for="password">New Password *</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" required minlength="6" placeholder="Enter new password" style="width: 100%; padding-right: 40px;" <?= $isAlreadyActivated ? 'disabled' : '' ?>>
                        <button type="button" onclick="toggleVisibility('password', this)" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 14px;" <?= $isAlreadyActivated ? 'disabled' : '' ?>>👁️</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password *</label>
                    <div style="position: relative;">
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6" placeholder="Confirm new password" style="width: 100%; padding-right: 40px;" <?= $isAlreadyActivated ? 'disabled' : '' ?>>
                        <button type="button" onclick="toggleVisibility('confirm_password', this)" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 14px;" <?= $isAlreadyActivated ? 'disabled' : '' ?>>👁️</button>
                    </div>
                </div>

            </div>

            <button type="submit" id="submitBtn" style="margin-top: 15px;" <?= $isAlreadyActivated ? 'disabled' : '' ?>>Activate Account</button>
        </form>
    </main>

    <script>
    function toggleVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.textContent = '🙈';
        } else {
            input.type = 'password';
            btn.textContent = '👁️';
        }
    }
    </script>

    <?php require_once 'includes/footer.php'; ?>
    <?php require_once 'includes/script.inc.php'; ?>

</body>
</html>