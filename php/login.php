<?php
session_start();
require_once '../api/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            header("Location: ../php/services.php");
            exit;
        } else {
            echo "<script>alert('Invalid password!');</script>";
        }
    } else {
        echo "<script>alert('No user found with that email!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Montra Studio</title>
  <link rel="stylesheet" href="../css/login.css">
  <link rel="stylesheet" href="../css/dashboard.css">
   <link rel="stylesheet" href="../css/footer.css">
<style> /* Dropdown profile styles */
 body { font-family: 'Inter', sans-serif;  margin:0;   background-color: #fff9f9;}
    .profile-icon { position: relative; display: inline-block; }
    .profile-icon img { width: 30px; height: 30px; border-radius: 50%; border: 2px solid #ddd; cursor: pointer; transition: all 0.3s ease; }
    .profile-icon img:hover { border-color: #aaa; transform: scale(1.05); }
    .dropdown-menu { display: none; position: absolute; top: 55px; right: 0; background-color: #fff; border-radius: 10px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); width: 230px; overflow: hidden; z-index: 100; animation: dropdownFade 0.25s ease-in-out; }
    .dropdown-user { background: #f8f8f8; padding: 15px; text-align: left; }
    .dropdown-user p { margin: 3px 0; color: #333; font-size: 14px; }
    .dropdown-user strong { font-weight: 600; color: #222; }
    .member-since { font-size: 12px; color: #777; margin-top: 5px; }
    .dropdown-item { display: block; padding: 12px 16px; text-decoration: none; color: #333; font-size: 14px; transition: background-color 0.2s ease; }
    .dropdown-item:hover { background-color: #f2f2f2; }
    .logout { color: #c0392b; font-weight: 500; }
    .dropdown-menu hr { margin: 8px 0; border: none; border-top: 1px solid #e0e0e0; }
    @keyframes dropdownFade { from { opacity: 0; transform: translateY(-8px);} to { opacity: 1; transform: translateY(0);} }
         .header {
  position: relative;
  z-index: 10;
}</style>
</head>
<body>
  <!-- HEADER -->
  <header class="header">
    <div class="logo"> 
      <img src="../images/LOGO.png" alt="Montra Studio Logo" style="height: 100px; width: 300px;"> 
    </div>

    <div class="header-right">
      <nav class="nav">
        <a href="../php/homepage.php">Home</a>
        <a href="../php/services.php">Services</a>
        <a href="../html/aboutus.html">About us</a>
      </nav>

     
    </div>
  </header>

  <!-- LOGIN CARD -->
  <div class="login-section">
    <div class="login-card">
      <h2>Login</h2>
      <p>Don’t Have an Account? <a href="register.php">Sign up Here</a></p>

      <form method="POST" action="">
        <div class="form-group">
          <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="form-group password-field">
          <input type="password" id="password" name="password" placeholder="Password" required>
          <span class="toggle-password" onclick="togglePassword()">&#128065;</span>
        </div>

        <button type="submit" class="btn-login">Sign In</button>
        <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
      </form>
    </div>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById("password");
      input.type = input.type === "password" ? "text" : "password";
    }
  </script>

  <!-- FOOTER -->
  <footer class="footer">
    <h2>Get In Touch</h2>
    <p class="footer-tagline">Capturing moments. Creating stories. Celebrating you.</p>
    <div class="footer-grid">
      <div class="footer-logo">
        <img src="../images/1 4.png" alt="Mantra Studio Logo">
      </div>
      <div class="footer-links">
        <div>
          <h4>Quick Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">Bookings</a></li>
            <li><a href="#">Gallery</a></li>
            <li><a href="#">About Us</a></li>
          </ul>
        </div>
        <div>
          <h4>Support</h4>
          <ul>
            <li><a href="#">FAQs</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
          </ul>
        </div>
        <div>
          <h4>Contacts</h4>
          <ul>
            <li>+0908126802823</li>
            <li>075-B Tapuac, Dagupan, Pangasinan</li>
            <li>MontraTeam@gmail.com</li>
          </ul>
        </div>
      </div>
    </div>
    <p class="footer-copy">© 2025 MontraStudio. All rights reserved.</p>
  </footer>
</body>
</html>
