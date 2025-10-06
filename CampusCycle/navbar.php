<?php
session_start();
?>
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
    <a href="profile.php">Profile</a>
    <a href="addproduct.php">Add Your Product</a>
    <a href="editlisting.php">Edit Listing</a>
    <a href="wallet.php">Wallet</a>
    <a href="leaderboard.php">Leaderboard</a>
    <a href="admin.php">Admin Panel</a>
    <a href="faq.html">FAQs</a>

    <?php if (isset($_SESSION['email'])): ?>
      <!-- Show logout only if logged in -->
      <a href="logout.php" style="color: #ff9800;">Logout</a>
    <?php else: ?>
      <!-- Show login/signup if not logged in -->
      <a href="login.html" style="color: #ff9800;">Login</a>
      <a href="signup.html" style="color: #ff9800;">Signup</a>
    <?php endif; ?>
  </nav>
</div>
