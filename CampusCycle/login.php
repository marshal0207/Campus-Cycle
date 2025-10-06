<?php
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

// Handle login request via POST (from JS fetch)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $fullname, $hash);

    $response = ['success' => false, 'message' => 'Invalid credentials'];

    if ($stmt->num_rows === 1) {
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            $_SESSION['email'] = $email;
            $_SESSION['fullname'] = $fullname;
            $response['success'] = true;
            $response['message'] = "Login successful! Welcome, $fullname";
        } else {
            $response['message'] = "Incorrect password!";
        }
    } else {
        $response['message'] = "Email not registered!";
    }

    $stmt->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Campus Cycle</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="login.css" />
</head>
<body>
<?php if (isset($_GET['logout']) && $_GET['logout'] == 1): ?>
<script>
    alert("You have been logged out successfully!");
</script>
<?php endif; ?>
  <header>
    <div class="heading">
      <h1>Campus Cycle</h1>
      <h5>
        Campus Cycle is for Students to Sell, Exchange or Donate Used Items...
      </h5>
      <hr />
    </div>
    <div id="Navbar">
      <div class="nav-top">
        <a id="logo">campuscycle.com</a>
        <div class="search-container">
          <input type="text" placeholder="Search here.." />
          <button>Search</button>
        </div>
      </div>
      <nav>
        <a href="homepage1.html">Home</a>
        <a href="profile.html">Profile</a>
        <a href="addproduct.html">Add Your Product</a>
        <a href="editlisting.html">Edit Listing</a>
        <a href="wallet.html">Wallet</a>
        <a href="leaderboard.html">Leaderboard</a>
        <a href="admin.html">Admin Panel</a>
        <a href="faq.html">FAQs</a>
        <a href="logout.php" style="color: red; font-weight: bold;">Logout</a>
      </nav>
    </div>
  </header>

  <main class="main-content">
    <form class="form-box" id="loginForm" autocomplete="off" action="login.php" method="POST">
      <h2>Login</h2>
      <input type="email" id="email" placeholder="Email" required />
      <input type="password" id="password" placeholder="Password" required minlength="6" />
      <button type="submit">Login</button>
      <p>
        Don't have an account?
        <a href="signup.html" style="color: #ff9800;">Signup</a>
      </p>
      <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    </form>
  </main>

  <footer class="footer">
    <p>© 2023 Campus Cycle. All rights reserved.</p>
    <div>
      <h3>Contact Us:</h3>
      <p>Mobile: 9328554406</p>
      <p>Email: support@campuscycle.com</p>
      <p>Address: Main Campus, University Grounds</p>
    </div>
  </footer>

  <!-- Popup Modal -->
  <div id="popup-modal" class="popup-modal">
    <div class="popup-content">
      <span id="popup-close" class="popup-close">&times;</span>
      <p id="popup-message"></p>
    </div>
  </div>

  <script>
    // Popup logic
    function showPopup(message) {
      document.getElementById('popup-message').textContent = message;
      document.getElementById('popup-modal').classList.add('active');
    }
    document.getElementById('popup-close').onclick = function() {
      document.getElementById('popup-modal').classList.remove('active');
    };
    window.onclick = function(event) {
      const modal = document.getElementById('popup-modal');
      if (event.target === modal) {
        modal.classList.remove('active');
      }
    };

    // Form validation & login
    document.getElementById('loginForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;

      if (!email || !password) {
        showPopup("Please fill in all fields.");
        return;
      }

      fetch('login.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=login&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
      })
      .then(res => res.json())
      .then(data => {
        showPopup(data.message);
        if (data.success) {
          setTimeout(() => {
            window.location.href = "profile.html";
          }, 1500);
        }
      })
      .catch(err => {
        console.error(err);
        showPopup("Something went wrong. Please try again.");
      });
    });
  </script>
</body>
</html>
