<?php
// Start session to access flash messages
session_start();

// Set page title for header
$pageTitle = 'Add New Record (AJAX Validation)';

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
        <a href="view.php" class="nav-link">&rarr; View Saved Records by table</a> | 
        <a href="viewbydiv.php" class="nav-link">&rarr; View Saved Records by div</a>
        |
        <a href="form.php" class="nav-link">&larr; Back to Standard Registration Form</a>

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

        <!-- Form submits to process.php -->
        <form id="registrationForm" action="process.php" method="POST">
            
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required minlength="4">
                <span id="usernameError" class="error-message" style="color: #dc3545; display: none;"></span>
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
                <span id="ageError" class="error-message" style="color: #dc3545; display: none;">Age must be between 2 and 28 to submit.</span>
            </div>

            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required>
                <span id="emailError" class="error-message" style="color: #dc3545; display: none;"></span>
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

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="newsletter" name="newsletter" value="1">
                    <label for="newsletter">Receive Newsletter</label>
                </div>
            </div>

            <button type="submit" id="submitBtn">Submit Record</button>
        </form>
    </main>

    <?php require_once 'includes/footer.php'; ?>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
    // Validation flags
    let isUsernameValid = true;
    let isEmailValid = true;
    let isAgeValid = true;

    function toggleSubmitButton() {
        if (isUsernameValid && isEmailValid && isAgeValid) {
            $('#submitBtn').prop('disabled', false);
        } else {
            $('#submitBtn').prop('disabled', true);
        }
    }

    function validateAge() {
        const ageValue = parseInt($('#age').val(), 10);
        if (isNaN(ageValue) || ageValue < 2 || ageValue > 28) {
            $('#ageError').show();
            isAgeValid = false;
        } else {
            $('#ageError').hide();
            isAgeValid = true;
        }
        toggleSubmitButton();
    }

    // Perform AJAX checks on keyup / change
    $(document).ready(function() {

        function checkAvailability() {
            const username = $('#username').val().trim();
            const email = $('#email').val().trim();

            if (username.length < 4 && email.length === 0) {
                return;
            }

            $.ajax({
                url: 'ajax/check_user_email.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    username: username,
                    email: email
                },
                success: function(response) {
                    // Check Username Status
                    if (username.length >= 4) {
                        if (response.username_exists) {
                            $('#usernameError').text('Username "' + username + '" is already taken.').show();
                            isUsernameValid = false;
                        } else {
                            $('#usernameError').hide();
                            isUsernameValid = true;
                        }
                    } else {
                        $('#usernameError').hide();
                        isUsernameValid = false; // standard minlength validation
                    }

                    // Check Email Status
                    if (email.length > 0) {
                        if (response.email_exists) {
                            $('#emailError').text('Email "' + email + '" is already registered.').show();
                            isEmailValid = false;
                        } else {
                            $('#emailError').hide();
                            isEmailValid = true;
                        }
                    }

                    toggleSubmitButton();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        }

        // Trigger check on typing (keyup) and blur events
        $('#username, #email').on('keyup blur', function() {
            checkAvailability();
        });
    });
    </script>

</body>
</html>