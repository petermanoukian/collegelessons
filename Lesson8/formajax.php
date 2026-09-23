<?php
// Start session to access flash messages
session_start();
require_once __DIR__ . '/includes/config.php';

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
        <a href="students" class="nav-link">&rarr; View Saved Records by div</a>
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

        <form id="registrationForm" action="<?= BASE_URL ?>/operations/process.php" method="POST" enctype="multipart/form-data">            
            <div class="form-grid">

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

                <!-- ALL IMAGE TYPES ALLOWED (Including TIFF) -->
                <div class="form-group">
                    <label for="profile_image">Profile Picture (All Images Allowed, including TIFF) *</label>
                    
                    <!-- Image Preview Container -->
                    <div id="imagePreviewContainer" style="margin-bottom: 8px; display: none;">
                        <img id="imagePreview" src="#" alt="Image Preview" style="max-width: 150px; height: auto; border: 1px solid #ccc; padding: 3px; border-radius: 4px;">
                    </div>

                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                    <span id="imageError" class="error-message" style="color: #dc3545; display: none;"></span>
                </div>

                <!-- ATTACHMENTS (Images, PDF, DOC, DOCX, TXT) -->
                <div class="form-group">
                    <label for="attachment">Attachment (Image, PDF, DOC, DOCX, TXT)</label>
                    <input type="file" id="attachment" name="attachment" accept="image/*, application/pdf, .doc, .docx, text/plain">
                    <span id="attachmentError" class="error-message" style="color: #dc3545; display: none;"></span>
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

    <!-- jQuery CDN -->
     <!--
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        -->
    <?php require_once 'includes/script.inc.php'; ?>
    <script>
    let isUsernameValid = true;
    let isEmailValid = true;
    let isAgeValid = true;
    let isFileValid = true;

    function toggleSubmitButton() {
        if (isUsernameValid && isEmailValid && isAgeValid && isFileValid) {
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

    // Client-side file type & MIME check
    function validateFiles() {
        const imgInput = $('#profile_image')[0];
        const docInput = $('#attachment')[0];
        isFileValid = true;

        // 1. Profile Image Validation (Any Image Type)
        if (imgInput.files.length > 0) {
            const file = imgInput.files[0];
            const ext = file.name.split('.').pop().toLowerCase();
            const allowedImgExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'tiff', 'tif', 'bmp', 'svg', 'ico', 'avif'];

            // Check mime type starts with image/ OR extension matches image list
            if (!file.type.startsWith('image/') && !allowedImgExts.includes(ext)) {
                $('#imageError').text('Invalid file type! Profile picture must be an image format (JPG, PNG, TIFF, WEBP, etc.).').show();
                isFileValid = false;
            } else {
                $('#imageError').hide();
            }
        }

        // 2. Attachment Validation (Images, PDF, DOC, DOCX, TXT)
        if (docInput.files.length > 0) {
            const file = docInput.files[0];
            const ext = file.name.split('.').pop().toLowerCase();
            const allowedDocExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'tiff', 'tif', 'bmp', 'pdf', 'doc', 'docx', 'txt'];

            if (!allowedDocExts.includes(ext)) {
                $('#attachmentError').text('Invalid attachment type. Allowed: Images, PDF, DOC, DOCX, TXT.').show();
                isFileValid = false;
            } else {
                $('#attachmentError').hide();
            }
        }

        toggleSubmitButton();
    }

    $(document).ready(function() {
        $('#profile_image, #attachment').on('change', validateFiles);

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
                data: { username: username, email: email },
                success: function(response) {
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
                        isUsernameValid = false;
                    }

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

        $('#username, #email').on('keyup blur', function() {
            checkAvailability();
        });
    });

    $('#profile_image').on('change', function() {
        const file = this.files[0];
        
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
                $('#imagePreviewContainer').show();
            };
            
            reader.readAsDataURL(file);
        } else {
            // Hide preview if file is invalid or deselected
            $('#imagePreviewContainer').hide();
            $('#imagePreview').attr('src', '#');
        }
    });


    </script>

</body>
</html> 