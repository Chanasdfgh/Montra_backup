<?php 
session_start();
require_once '../api/db_connect.php';

// Check if user is logged in before assigning
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// If logged in, fetch user details
if ($user_id) {
    $stmt = $conn->prepare("SELECT first_name, last_name, email, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    $user = null; // Ensures profile dropdown doesn't break
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us | Montra Studio</title>
  <link rel="stylesheet" href="../css/aboutus.css">
  <link rel="stylesheet" href="../css/footer.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <style>

     body { font-family: 'Inter', sans-serif; background:#f7f7f7; margin:0; }

    .booking-section { margin-top: 40px; }
    table.bookings { width: 100%; border-collapse: collapse; margin-top: 20px; background:#fff; border-radius:10px; overflow:hidden; }
    table.bookings th, table.bookings td { padding: 12px 16px; border-bottom: 1px solid #eee; text-align: left; }
    table.bookings th { background:#111; color:#fff; font-weight:600; }
    table.bookings tr:last-child td { border-bottom: none; }
    .badge { padding:6px 10px; border-radius:8px; color:#fff; font-weight:600; font-size:0.9em; }
    .badge.approved { background:#28a745; }
    .profile-container { display:flex; justify-content:center; padding:40px 20px; }
    .profile-card { max-width:1100px; width:100%; background:#fff; border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,0.08); padding:30px; }
    h2 { margin-bottom:10px; }
    .text-muted { color:#777; font-size:14px; }

    /* Dropdown profile styles (reuse from your current UI) */
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
}
 /* ===== Enhanced PROFILE DROPDOWN ===== */
    .profile-icon { position: relative; display: inline-block; }
    .profile-icon img { 
        width: 32px; 
        height: 32px; 
        border-radius: 50%; 
        border: 2px solid #ddd; 
        cursor: pointer; 
        transition: all 0.3s ease; 
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .profile-icon img:hover { border-color: #666; transform: scale(1.1); }

    .dropdown-menu { 
        display: none; 
        position: absolute; 
        top: 55px; 
        right: 0; 
        background: #fff; 
        border-radius: 14px; 
        box-shadow: 0 8px 30px rgba(0,0,0,0.1); 
        width: 250px; 
        overflow: hidden; 
        z-index: 100; 
        animation: dropdownFade 0.25s ease-in-out; 
        transition: all 0.25s ease;
    }

    .dropdown-user { 
        background: linear-gradient(145deg, #fafafa, #f0f0f0); 
        padding: 18px 16px; 
        text-align: left; 
        border-bottom: 1px solid #eee; 
    }
    .dropdown-user p { 
        margin: 3px 0; 
        color: #333; 
        font-size: 14px; 
        line-height: 1.4; 
    }
    .dropdown-user strong { font-weight: 600; color: #111; }
    .member-since { font-size: 12px; color: #888; margin-top: 6px; }

    .dropdown-item { 
        display: block; 
        padding: 12px 16px; 
        text-decoration: none; 
        color: #333; 
        font-size: 14px; 
        transition: background-color 0.25s ease, transform 0.2s;
    }
    .dropdown-item:hover { 
        background-color: #f7f7f7; 
        transform: translateX(3px);
    }

    .logout { color: #c0392b; font-weight: 600; }
    .dropdown-menu hr { margin: 8px 0; border: none; border-top: 1px solid #e0e0e0; }

    @keyframes dropdownFade { 
        from { opacity: 0; transform: translateY(-8px);} 
        to { opacity: 1; transform: translateY(0);} 
    }

    /* ===== Enhanced NOTIFICATION DROPDOWN ===== */
    .notification-icon {
      position: relative;
      margin-right: 20px;
      cursor: pointer;
      display: inline-block;
      transition: transform 0.2s ease;
    }

    .notification-icon:hover {
      transform: scale(1.1);
    }

    .notification-icon img {
      width: 30px;
      height: 30px;
      vertical-align: middle;
      filter: drop-shadow(0 2px 3px rgba(0,0,0,0.2));
    }

    .notif-count {
      position: absolute;
      top: -6px;
      right: -6px;
      background: #e74c3c;
      color: white;
      font-size: 11px;
      font-weight: bold;
      border-radius: 50%;
      padding: 3px 6px;
      display: none;
      box-shadow: 0 0 6px rgba(231,76,60,0.6);
    }

    .notif-dropdown {
      position: absolute;
      right: 0;
      top: 45px;
      width: 280px;
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      z-index: 9999;
      display: none;
      overflow: hidden;
      animation: dropdownFade 0.25s ease-in-out;
    }

    .notif-dropdown.active {
      display: block !important;
    }

    .notif-dropdown p {
      padding: 12px 16px;
      border-bottom: 1px solid #f0f0f0;
      font-size: 14px;
      color: #333;
      margin: 0;
      background: #fff;
      transition: background 0.2s ease;
    }

    .notif-dropdown p:hover {
      background: #f8f9fa;
    }

    .notif-dropdown p:last-child {
      border-bottom: none;
    }

        
.nav-icons {
  display: flex;
  align-items: center;
  gap: 5px; /* adjust spacing between icons */
}
    </style>
</head>
<body>
  <!-- Header Section -->
  <section class="hero-section">
    <div class="overlay">
   <header class="header">
  <div class="">
    <a href="../html/homepage.html"><img src="../images/logo0.png" alt="Montra Studio Logo" style="height:70px; width:150px;"></a>
  </div>

  <div class="header-right">
    <nav class="nav">
      <a href="../php/homepage.php">Home</a>
      <a href="../php/services.php">Services</a>
      <a href="../php/aboutus.php">About us</a>
    </nav>
 <?php if (isset($_SESSION['user_id'])): ?>
<!-- Notification Bell (Only for logged-in users) -->
   <div class="nav-icons">
<div class="notification-icon">
  <img src="https://cdn-icons-png.flaticon.com/512/1827/1827392.png" 
       alt="Notifications" id="notifBell">
  <span id="notifCount" class="notif-count"></span>

  <!-- Added: Default message inside dropdown so it's visible -->
  <div id="notifDropdown" class="notif-dropdown">
    <p style="text-align:center; color:#888;">Loading notifications...</p>
  </div>
</div>
<?php endif; ?>
   <?php if ($user): ?>
<div class="profile-icon dropdown">
  <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Profile" class="dropdown-toggle" id="profileToggle">
  <div class="dropdown-menu" id="profileMenu">
    <div class="dropdown-user">
      <p><strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong></p>
      <p><?= htmlspecialchars($user['email']) ?></p>
      <p class="member-since">Member since <?= (new DateTime($user['created_at']))->format('F Y') ?></p>
    </div>
    <hr>
    <a href="pending_bookings.php" class="dropdown-item">Pending Bookings</a>
    <a href="approved_bookings.php" class="dropdown-item">Approved Bookings</a>
    <a href="completed_bookings.php" class="dropdown-item">Completed Bookings</a>
    <a href="rejected_bookings.php" class="dropdown-item">Rejected Bookings</a>
    <hr>
    <a href="logout.php" class="dropdown-item logout">Logout</a>
  </div>
</div>
<?php else: ?>
<a href="../php/login.php" class="btn outline">Login</a>
<?php endif; ?>
  </div>
</header>
    </div>
    <div class="hero-content">
      <h1>About Us</h1>
      <p>At Montra Studio, we offer self-service photobooths with easy controls and instant results, customizable backdrops and props to match your vibe, high-quality prints and digital copies on the spot, and a cozy private space where you can relax, pose, and play—perfect whether you’re with friends, celebrating your individuality, or simply looking for something fun and different to do.</p>
      <a href="services.php" class="btn">See Our Services</a>
    </div>
  </section>

  <!-- What We Do -->
  <section class="what-we-do">
    <h2>WHAT WE DO</h2>
    <p>At Montra Studio, we offer self-service photoshoots with easy controls and instant results that allow you to bring your vision to life. With high-quality lighting, professional-grade equipment, and a comfortable space, we make creating art fun and effortless for everyone.</p>
  </section>

  <!-- Mission Section -->
  <section class="mission-vision">
    <div class="mission">
      <img src="../images/image 40.png" alt="Studio interior">
      <div class="text">
        <h3>Our mission</h3>
        <p>At Montra Studio, our mission is to create a space where people can freely express themselves, cultivate creativity, and connect meaningfully through the art of photography. We aim to bring out confidence, individuality, and authenticity in every client.</p>
        <a href="services.php" class="btn">See Our Services</a>
      </div>
    </div>

    <div class="vision">
      <div class="text">
        <h3>Our Vision</h3>
        <p>Our vision is to become the leading photostudio that redefines how people capture memories—transforming ordinary shoots into extraordinary snapshots, and inspiring a culture where self-expression and creativity are always welcomed.</p>
        <a href="#" class="btn-outline">Contact Us</a>
      </div>
      <img src="../images/image 39.jpg" alt="Studio building">
    </div>
  </section>

  <!-- Footer -->
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
  <p class="footer-copy">© 2025 MOntraStudio. All rights reserved.</p>
</footer>

<script>
const profileToggle = document.getElementById('profileToggle');
const profileMenu = document.getElementById('profileMenu');
profileToggle.addEventListener('click', () => {
  profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
});
window.addEventListener('click', (event) => {
  if (!profileToggle.contains(event.target) && !profileMenu.contains(event.target)) {
    profileMenu.style.display = 'none';
  }
});
</script>
 <?php if (isset($_SESSION['user_id'])): ?>
  <!-- Notifications Script -->
  <script>
document.addEventListener("DOMContentLoaded", function () {
  const bell = document.getElementById('notifBell');
  const dropdown = document.getElementById('notifDropdown');
  const notifCount = document.getElementById('notifCount');

  if (!bell || !dropdown) return;

  function fetchNotifications() {
    fetch('../php/fetch_notifications.php')
      .then(res => res.json())
      .then(data => {
        dropdown.innerHTML = ''; // clear old

        if (data.length > 0) {
          notifCount.textContent = data.length;
          notifCount.style.display = 'inline-block';

          data.forEach(item => {
            const p = document.createElement('p');
            p.textContent = item.message;
            dropdown.appendChild(p);
          });
        } else {
          notifCount.style.display = 'none';
          dropdown.innerHTML = '<p style="color:#777;text-align:center;">No new notifications</p>';
        }
      })
      .catch(err => {
        console.error('Notification fetch error:', err);
        dropdown.innerHTML = '<p style="color:red;text-align:center;">Error loading notifications</p>';
      });
  }

  fetchNotifications();
  setInterval(fetchNotifications, 5000);

  bell.addEventListener('click', () => {
    dropdown.classList.toggle('active');
  });
});

</script>

  <?php endif; ?>
</body>
</html>
