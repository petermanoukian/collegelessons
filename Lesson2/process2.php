<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Processing - Process 2</title>
</head>
<body>

    <h2>Form Submission Results (Process 2)</h2>

    <?php
    // Detect the submission method
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'POST') {
        echo "<p>This form was submitted via <strong>POST</strong>.</p>";

        // Read from $_POST and sanitize with addslashes
        $username = isset($_POST['username']) ? addslashes($_POST['username']) : 'Not provided';
        $email    = isset($_POST['email']) ? addslashes($_POST['email']) : 'Not provided';
        $age      = isset($_POST['age']) ? addslashes($_POST['age']) : 'Not provided';

    } elseif ($method === 'GET') {
        echo "<p>This form was submitted via <strong>GET</strong>.</p>";

        // Read from $_GET and sanitize with addslashes
        $username = isset($_GET['username']) ? addslashes($_GET['username']) : 'Not provided';
        $email    = isset($_GET['email']) ? addslashes($_GET['email']) : 'Not provided';
        $age      = isset($_GET['age']) ? addslashes($_GET['age']) : 'Not provided';

    } else {
        echo "<p>Unsupported request method.</p>";
        exit;
    }
    ?>

    <hr>

    <h3>Processed Values (with addslashes):</h3>
    <ul>
        <li><strong>Username:</strong> <?php echo $username; ?></li>
        <li><strong>Email:</strong> <?php echo $email; ?></li>
        <li><strong>Age:</strong> <?php echo $age; ?></li>
    </ul>

    <br>
    <a href="entry.php">Go back to form</a>

</body>
</html>