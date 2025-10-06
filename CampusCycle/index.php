<?php
session_start();

// --- Database Connection ---
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "testdb";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Handle Logout ---
if (isset($_GET['action']) && $_GET['action'] === "logout") {
    $_SESSION = [];
    session_destroy();

    if (isset($_COOKIE['remember_email'])) {
        setcookie("remember_email", "", time() - 3600, "/");
    }

    echo "<script>alert('You have been logged out successfully!'); window.location.href='index.php';</script>";
    exit();
}

// --- Auto-login from Cookie ---
if (isset($_COOKIE['remember_email']) && !isset($_SESSION['email'])) {
    $_SESSION['email'] = $_COOKIE['remember_email'];
}

// --- Handle Login ---
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    $sql = "SELECT * FROM users WHERE email=? AND password=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password); // ⚠️ Plain text password for demo
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['email'] = $email;

        if ($remember) {
            setcookie("remember_email", $email, time() + (7 * 24 * 60 * 60), "/");
        }

        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login System</title>
</head>
<body>
<?php if (isset($_SESSION['email'])): ?>
    <!-- Dashboard Section -->
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?> 🎉</h2>
    <p>You are logged in successfully.</p>
    <a href="index.php?action=logout">Logout</a>

<?php else: ?>
    <!-- Login Form Section -->
    <h2>Login</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>

    <form method="post">
        <label>Email:</label>
        <input type="text" name="email" required><br><br>

        <label>Password:</label>
        <input type="password" name="password" required><br><br>

        <label>
            <input type="checkbox" name="remember"> Remember Me
        </label><br><br>

        <input type="submit" name="login" value="Login">
    </form>
<?php endif; ?>
</body>
</html>
