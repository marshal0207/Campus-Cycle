<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <script>
        function confirmLogout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "logout.php";
            }
        }
    </script>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h2>
    <p>You are logged in successfully.</p>
    <button onclick="confirmLogout()">Logout</button>
</body>
</html>
