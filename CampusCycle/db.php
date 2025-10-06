<?php
// Database configuration
$host = "localhost";     // usually localhost
$user = "root";          // your DB username
$pass = "";              // your DB password
$dbname = "campuscycle"; // your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset to utf8 for proper encoding
$conn->set_charset("utf8");
?>
