<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Validation Example</title>
</head>
<body>

    <h2>User Registration Form</h2>

    <!-- Form uses an onsubmit event to run JavaScript validation before posting -->
    <form id="userForm" action="process2.php" method="POST" onsubmit="return validateForm()">
        
        <!-- Username: HTML Required -->
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <!-- Email: HTML Required & Email Format -->
        <label for="email">Email Address:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <!-- Age: Simple text input, validated via JavaScript -->
        <label for="age">Age:</label><br>
        <input type="text" id="age" name="age"><br><br>

        <button type="submit">Submit Form</button>

    </form>

    <script>
    function validateForm() {
        var ageInput = document.getElementById("age");
        var ageValue = ageInput.value.trim();

        // If age is provided, check if it is a valid positive integer
        if (ageValue !== "") {
            // Check if the value contains non-numeric characters or is not a whole number
            if (isNaN(ageValue) || ageValue.includes(".") || parseInt(ageValue) <= 0) {
                alert("Please enter a valid whole number for age!");
                
                // Empty the invalid value
                ageInput.value = "";
                
                // Focus back on the age input field
                ageInput.focus();
                
                // Prevent form submission
                return false;
            }
        }

        // Allow form submission if validation passes
        return true;
    }
    </script>

</body>
</html>