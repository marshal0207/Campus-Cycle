<?php
// Start session (optional)
session_start();

// Connect to MySQL
$servername = "localhost";
$username = "root";
$password = ""; // default XAMPP password
$dbname = "campuscycle";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Message for confirmation
$success_msg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Basic validation
    if(empty($fullname) || empty($email) || empty($message)) {
        $success_msg = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $success_msg = "Invalid email format!";
    } else {
        // Insert into MySQL
        $stmt = $conn->prepare("INSERT INTO form_submissions (fullname, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $fullname, $email, $message);
        if ($stmt->execute()) {
            $success_msg = "Form submitted successfully!";
        } else {
            $success_msg = "Error submitting form!";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Cycle Form</title>
    <style>
        body { font-family: Arial; background:#1e1e1e; color:#fff; }
        .container { max-width: 500px; margin:50px auto; background:#2c2c2c; padding:20px; border-radius:8px; }
        input, textarea { width:100%; margin:10px 0; padding:10px; border-radius:4px; border:none; }
        button { padding:10px 20px; background:orange; color:#fff; border:none; border-radius:4px; cursor:pointer; }
        .message { margin:10px 0; color: lightgreen; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Submit Your Details</h2>
        <?php if($success_msg) echo "<div class='message'>$success_msg</div>"; ?>
        <form method="POST" action="">
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <textarea name="message" placeholder="Your Message" required></textarea>
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
