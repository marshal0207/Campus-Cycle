<?php
// Database configuration
$host = "sql12.freesqldatabase.com";     // usually localhost
$user = "sql12801706";          // your DB username
$pass = "58d7sWvpga";              // your DB password
$dbname = "sql12801706"; // your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset to utf8 for proper encoding
$conn->set_charset("utf8");
?>

