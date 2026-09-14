<?php
$requestMethod = $_SERVER['REQUEST_METHOD'];
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once 'includes/header.php'; ?>
<body>

    <header>
        <h1>PHP & Web Development Lessons</h1>
        <p>Lesson 4: Forms (GET vs POST), Validation, and File Storage</p>
    </header>

    <main>
        <a href="view.php" class="nav-link">&rarr; View Saved Records by table</a> | 
        <a href="viewbydiv.php" class="nav-link">&rarr; View Saved Records by div</a>

        <h2>Student Registration</h2>

        <!-- Method Detector Badge -->
        <div class="method-badge <?= ($requestMethod === 'POST') ? 'method-post' : 'method-get' ?>">
            Current Request Method: <strong><?= $requestMethod ?></strong>
        </div>

        <!-- Form submits to process.php -->
        <form id="registrationForm" action="process.php" method="POST">
            
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
                <span id="ageError" class="error-message">Age must be between 2 and 28 to submit.</span>
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