<?php
// Start session to access flash messages
session_start();

// Set page title for header
$pageTitle = 'Add New Record';

// Include central database connection
require_once __DIR__ . '/includes/connection.inc.php';

$requestMethod = $_SERVER['REQUEST_METHOD'];
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/head.inc.php'; ?>

<body>

    <?php require_once 'includes/header.inc.php'; ?>

    <main>
        <a href="formajax.php" class="nav-link">&rarr; AJAX Registration Form</a> | 
        <a href="view.php" class="nav-link">&rarr; View Saved Records by table</a> | 
        <a href="viewbydiv.php" class="nav-link">&rarr; View Saved Records by div</a>

        <h2><?= htmlspecialchars($pageTitle) ?></h2>

        <!-- Flash Message Display -->
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="flash-message flash-error" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f5c6cb;">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Method Detector Badge -->
        <div class="method-badge <?= ($requestMethod === 'POST') ? 'method-post' : 'method-get' ?>">
            Current Request Method: <strong><?= $requestMethod ?></strong>
        </div>

        <!-- Added enctype="multipart/form-data" for file uploads -->
        <form id="registrationForm" action="process.php" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" required minlength="4">
                </div>

                <div class="form-group">
                    <label for="firstname">First Name *</label>
                    <input type="text" id="firstname" name="firstname" required>
                </div>

                <div class="form-group">
                    <label for="lastname">Last Name *</label>
                    <input type="text" id="lastname" name="lastname" required>
                </div>

                <div class="form-group">
                    <label for="age">Age * (Must be 2–28)</label>
                    <input type="number" id="age" name="age" required oninput="validateAge()">
                    <span id="ageError" class="error-message" style="display:none; color: #dc3545;">Age must be between 2 and 28 to submit.</span>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="nickname">Nickname (Optional)</label>
                    <input type="text" id="nickname" name="nickname">
                </div>

                <div class="form-group">
                    <label for="gender">Gender *</label>
                    <select id="gender" name="gender" required>
                        <option value="">-- Select Gender --</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Membership Type *</label>
                    <div class="radio-group">
                        <input type="radio" id="member" name="membership" value="Member" checked>
                        <label for="member">Member</label>
                        
                        <input type="radio" id="full_member" name="membership" value="Full Member">
                        <label for="full_member">Full Member</label>
                    </div>
                </div>

                <!-- NEW FIELD 1: Image Only -->
                <div class="form-group">
                    <label for="profile_image">Profile Picture (Images only) *</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/png, image/jpeg, image/gif, image/webp">
                </div>

                <!-- NEW FIELD 2: Image, PDF, DOC, DOCX, TXT -->
                <div class="form-group">
                    <label for="attachment">Attachment (Image, PDF, DOC, DOCX, TXT)</label>
                    <input type="file" id="attachment" name="attachment" accept="image/*, application/pdf, .doc, .docx, text/plain, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                </div>

                <div class="form-group full-width">
                    <div class="checkbox-group">
                        <input type="checkbox" id="newsletter" name="newsletter" value="1">
                        <label for="newsletter">Receive Newsletter</label>
                    </div>
                </div>

            </div>

            <button type="submit" id="submitBtn" style="margin-top: 15px;">Submit Record</button>
        </form>
    </main>

    <?php require_once 'includes/footer.php'; ?>

    <script>
    function validateAge() {
        const ageInput = document.getElementById('age');
        const ageError = document.getElementById('ageError');
        const submitBtn = document.getElementById('submitBtn');
        const ageValue = parseInt(ageInput.value, 10);

        if (isNaN(ageValue) || ageValue < 2 || ageValue > 28) {
            ageError.style.display = 'block';
            submitBtn.disabled = true;
        } else {
            ageError.style.display = 'none';
            submitBtn.disabled = false;
        }
    }
    </script>

</body>
</html>