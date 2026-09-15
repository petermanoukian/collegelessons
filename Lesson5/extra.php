<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User Profile</title>
</head>
<body>

    <h2>Edit User Profile</h2>

    <!-- Physical form method MUST be POST -->
    <form action="process3.php" method="POST">
        
        <!-- Hidden input spoofing the PUT verb -->
        <input type="hidden" name="_method" value="PUT">

        <!-- User fields -->
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" value="JohnDoe" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="john@example.com" required><br><br>

        <button type="submit">Update Profile</button>
    </form>

</body>
</html>