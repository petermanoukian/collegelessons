<?php
session_start();
require_once dirname(__DIR__, 2) . '/includes/config.php';
require_once ROOT_PATH . '/includes/connection.inc.php';

$pageTitle = 'User Login';
$requestMethod = $_SERVER['REQUEST_METHOD'];
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once ROOT_PATH . '/includes/head.inc.php'; ?>

<body>

    <?php require_once ROOT_PATH . '/includes/header.inc.php'; ?>

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

        <!-- Method Detector Badge -->
        <div class="method-badge <?= ($requestMethod === 'POST') ? 'method-post' : 'method-get' ?>">
            Current Request Method: <strong><?= $requestMethod ?></strong>
        </div>

        <form id="loginForm" action="<?= BASE_URL ?>/user/operations/process_login.php" method="POST">            
            <div class="form-grid">

                <div class="form-group">
                    <label for="username">Username or Email *</label>
                    <input type="text" id="username" name="username" required placeholder="Enter username or email">                
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" required placeholder="Enter password" style="width: 100%; padding-right: 40px;">
                        <button type="button" onclick="toggleVisibility('password', this)" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 14px;">👁️</button>
                    </div>
                </div>

            </div>

            <button type="submit" id="submitBtn" style="margin-top: 15px;">Login</button>
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

    <?php require_once ROOT_PATH . '/includes/footer.php'; ?>
    <?php require_once ROOT_PATH . '/includes/script.inc.php'; ?>

</body>
</html>