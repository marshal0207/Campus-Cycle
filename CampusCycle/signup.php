<?php
session_start();

$usersFile = __DIR__ . '/users.txt';

// Connect to MySQL
$servername = "localhost";
$username = "root";
$password = ""; // default XAMPP password
$dbname = "campuscycle";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle signup request via POST (from JS fetch)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'signup') {
    // Sanitize inputs
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    $response = ['success' => false, 'message' => 'Signup failed!'];

    // Validate inputs
    if (strlen($fullname) < 2) {
        $response['message'] = "Full name must be at least 2 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $response['message'] = "Password must be at least 6 characters.";
    } else {
        // Check if email already exists in DB
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $exists_db = $stmt->num_rows > 0;
        $stmt->close();

        // Check if email already exists in users.txt
        $exists_txt = false;
        if (file_exists($usersFile)) {
            $file = fopen($usersFile, 'r');
            while (($line = fgets($file)) !== false) {
                $parts = explode('|', trim($line));
                if (count($parts) >= 2 && $email === $parts[1]) {
                    $exists_txt = true;
                    break;
                }
            }
            fclose($file);
        }

        if ($exists_db || $exists_txt) {
            $response['message'] = "Email already registered!";
        } else {
            // Hash password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into DB
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fullname, $email, $hashed_password);
            if ($stmt->execute()) {
                // Also write to users.txt (for legacy reasons)
                $file = fopen($usersFile, 'a');
                fwrite($file, $fullname . '|' . $email . '|' . $hashed_password . PHP_EOL);
                fclose($file);

                $_SESSION['email'] = $email;
                $_SESSION['fullname'] = $fullname;
                $response['success'] = true;
                $response['message'] = "Signup successful! Welcome, $fullname";
            } else {
                $response['message'] = "Database error: Could not create user.";
            }
            $stmt->close();
        }
    }

    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Retrieve all students from DB
$students = [];
$result = $conn->query("SELECT id, fullname, email FROM users ORDER BY id DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Signup - Campus Cycle</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="signup.css" />
  <style>
    /* Dark table style for Registered Students */
.students-table-container {
  margin: 2em auto;
  max-width: 700px;
  width: 95%;
}
.form-box {
  background: #1e1e1e;
  border-radius: 12px;
  box-shadow: 0 0 15px rgba(255,152,0,0.5);
}
.students-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', Arial, sans-serif;
  background: #1e1e1e;
  color: #fff;
  border-radius: 12px;
  overflow: hidden;
}
.students-table th, .students-table td {
  padding: 12px 10px;
  border-bottom: 1px solid #333;
  text-align: left;
}
.students-table th {
  background: #ff9800;
  color: #1e1e1e;
  font-weight: 600;
  letter-spacing: 0.5px;
}
.students-table tr:last-child td {
  border-bottom: none;
}
.students-table caption {
  caption-side: top;
  font-size: 1.3em;
  font-weight: 600;
  margin-bottom: 0.5em;
  color: #ff9800;
}
@media (max-width: 600px) {
  .students-table th, .students-table td {
    padding: 8px 5px;
    font-size: 0.95em;
  }
}
  </style>
</head>
<body>
<header>
  <div class="heading">
    <h1>Campus Cycle</h1>
    <h5>Campus Cycle is for Students to Sell, Exchange or Donate Used Items...</h5>
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
      <a href="login.php">Login</a>
      <a href="profile.html">Profile</a>
      <a href="addproduct.html">Add Your Product</a>
      <a href="editlisting.html">Edit Listing</a>
      <a href="wallet.html">Wallet</a>
      <a href="leaderboard.html">Leaderboard</a>
      <a href="admin.html">Admin Panel</a>
      <a href="faq.html">FAQs</a>
    </nav>
  </div>
</header>

<main class="main-content">
  <form class="form-box" id="signupForm" method="POST" autocomplete="off">
    <h2>Signup</h2>
    <input type="text" id="fullname" name="fullname" placeholder="Full Name" required />
    <input type="email" id="email" name="email" placeholder="Email" required />
    <input type="password" id="password" name="password" placeholder="Password" required />
    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required />
    <button type="submit">Create Account</button>
    <p>Already have an account? 
      <a href="login.php" style="color: #ff9800;">Login</a>
    </p>
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

<!-- Show student data from MySQL -->
<div class="students-table-container">
  <div class="form-box" style="max-width: 600px; margin: 40px auto; padding: 20px; background: #1e1e1e; border-radius: 12px; box-shadow: 0 0 15px rgba(255,152,0,0.5);">
    <h2 style="text-align:center; color:#ff9800; margin-bottom:20px;">Registered Students</h2>
    <table class="students-table" style="width:100%; border-collapse: collapse; color:white;">
      <thead>
        <tr style="background:#ff9800; color:#1e1e1e;">
          <th style="padding:12px; text-align:left;">ID</th>
          <th style="padding:12px; text-align:left;">Full Name</th>
          <th style="padding:12px; text-align:left;">Email</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($students)): ?>
          <?php foreach ($students as $student): ?>
            <tr style="border-bottom:1px solid #333;">
              <td style="padding:10px;"><?php echo htmlspecialchars($student['id']); ?></td>
              <td style="padding:10px;"><?php echo htmlspecialchars($student['fullname']); ?></td>
              <td style="padding:10px;"><?php echo htmlspecialchars($student['email']); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="3" style="text-align:center; padding:15px; color:#ccc;">No students registered yet.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Popup Modal -->
<div id="popup-modal" class="popup-modal">
  <div class="popup-content">
    <span id="popup-close" class="popup-close">&times;</span>
    <p id="popup-message"></p>
  </div>
</div>

<script>
  function showPopup(message) {
    document.getElementById('popup-message').textContent = message;
    document.getElementById('popup-modal').classList.add('active');
  }
  document.getElementById('popup-close').onclick = function() {
    document.getElementById('popup-modal').classList.remove('active');
  };
  window.onclick = function(event) {
    const modal = document.getElementById('popup-modal');
    if (event.target === modal) modal.classList.remove('active');
  };

  document.getElementById('signupForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const fullname = document.getElementById('fullname').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (fullname.length < 2) {
      showPopup('Please enter your full name.');
      return;
    }
    if (!emailPattern.test(email)) {
      showPopup('Please enter a valid email address.');
      return;
    }
    if (password.length < 6) {
      showPopup('Password must be at least 6 characters.');
      return;
    }
    if (password !== confirmPassword) {
      showPopup('Passwords do not match.');
      return;
    }

    fetch('signup.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `action=signup&fullname=${encodeURIComponent(fullname)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
    })
    .then(res => res.json())
    .then(data => {
      showPopup(data.message);
      if(data.success) {
        setTimeout(() => { window.location.href = 'login.php'; }, 1500);
      }
    })
    .catch(err => {
      console.error(err);
      showPopup("Error: Could not reach server.");
    });
  });
</script>
</body>
</html>
