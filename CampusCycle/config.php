<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "campuscycle";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>
