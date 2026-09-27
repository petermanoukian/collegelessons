<?php
// 1. Verify physical HTTP request method
$physicalMethod = $_SERVER['REQUEST_METHOD'];

// 2. Determine the intended logical method
$requestMethod = $physicalMethod;

if ($physicalMethod === 'POST' && isset($_POST['_method'])) {
    // Override the method with the uppercase hidden field value
    $requestMethod = strtoupper($_POST['_method']);
}

// 3. Route execution based on the spoofed method
switch ($requestMethod) {
    case 'PUT':
        // Capture and sanitize input values
        $username = isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : '';
        $email    = isset($_POST['email'])    ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8')    : '';

        echo "<h2>[PUT Request Processed]</h2>";
        echo "<p>User record updated successfully!</p>";
        echo "<ul>";
        echo "<li><strong>New Username:</strong> " . $username . "</li>";
        echo "<li><strong>New Email:</strong> " . $email . "</li>";
        echo "</ul>";
        break;

    case 'POST':
        echo "<h2>[Standard POST Request]</h2>";
        echo "<p>Creating a new record...</p>";
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo "Error: Unsupported request method (" . htmlspecialchars($requestMethod) . ").";
        break;
}
?>