<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Processing Result</title>
</head>
<body>

    <h2>Form Submission Results</h2>

    <?php
    // Get the HTTP submission method (returns "GET" or "POST")
    $method = $_SERVER['REQUEST_METHOD'];

    // $_REQUEST holds data from BOTH $_GET and $_POST automatically
    $username = isset($_REQUEST['username']) ? $_REQUEST['username'] : 'Not provided';
    $email    = isset($_REQUEST['email']) ? $_REQUEST['email'] : 'Not provided';
    $age      = isset($_REQUEST['age']) ? $_REQUEST['age'] : 'Not provided';
    ?>

    <!-- Print the method used -->
    <p><strong>Submission Method:</strong> This form was submitted via <u><?php echo $method; ?></u>.</p>

    <hr>

    <h3>Received Data:</h3>
    <ul>
        <li><strong>Username:</strong> <?php echo $username; ?></li>
        <li><strong>Email:</strong> <?php echo $email; ?></li>
        <li><strong>Age:</strong> <?php echo $age; ?></li>
    </ul>

    <br>
    <a href="entry.php">Go back to form</a>

</body>
</html>