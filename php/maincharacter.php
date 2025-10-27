<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../api/db_connect.php';

$isLoggedIn = isset($_SESSION['user_id']);

$id = 1; // Main Character package
$result = mysqli_query($conn, "SELECT * FROM packages WHERE id=$id");
$package = mysqli_fetch_assoc($result);

$includes = explode(',', $package['includes']);
$images = explode(',', $package['images']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Our Services | Mantra Studio</title>
  <link rel="stylesheet" href="..\css\maincharacter.css" />
    <link rel="stylesheet" href="..\css\footer.css">
    <style>
    .header {
  position: relative;
  z-index: 10;
}
.notification-icon {
  position: relative;
  z-index: 10000; /* make sure dropdown stays on top */
}

    /* Notification Bell Styling */
    .notification-icon {
  position: relative;
  margin-right: 15px;
  cursor: pointer;
  display: inline-block;
}

.notification-icon img {
  width: 30px;
  height: 30px;
  vertical-align: middle;
}

.notif-count {
  position: absolute;
  top: -6px;
  right: -6px;
  background: red;
  color: white;
  font-size: 11px;
  font-weight: bold;
  border-radius: 50%;
  padding: 3px 6px;
  display: none;
}

.notif-dropdown {
  position: absolute;
  right: 0;
  top: 40px;
  width: 250px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
  z-index: 9999; /* Make sure it’s above everything */
  display: none;
}

.notif-dropdown.active {
  display: block !important;
}

.notif-dropdown p {
  padding: 10px;
  border-bottom: 1px solid #eee;
  font-size: 14px;
  color: #333;
  margin: 0;
  background: white;
}

.notif-dropdown p:hover {
  background: #f5f5f5;
}

.nav-icons {
  display: flex;
  align-items: center;
  gap: 5px; /* adjust spacing between icons */
}
  </style>
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
      <a href="../html/services.html">Services</a>
      <a href="../html/aboutus.html">About us</a>
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


      <div class="profile-icon">
        <a href="../php/profile.php">
          <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Profile">
        </a>
      </div>
    </div>
  </header>
<br/><br/><br/><br/><br/><br/>
  <!-- HERO SECTION -->
  <section class="hero-section">
    <h1>Our Services</h1>
    <p>Home / Services / Packages</p>
  </section>

  <!-- MAIN PACKAGE -->
  <section class="service-package">
    <div class="package-card">
      <div class="package-img">
       <img src="../uploads/<?= htmlspecialchars($package['main_image']) ?>" alt="Main Character Package">

      </div>

      <div class="package-content">
       <h2><?= htmlspecialchars($package['name']) ?></h2>
<p><?= nl2br(htmlspecialchars($package['description'])) ?></p>

        <br/> <br/><br/><br/><br/><br/> <br/>

       <div class="price-box">
  <h3>₱<?= number_format($package['price'], 2) ?></h3>
  <?php if ($isLoggedIn): ?>
    <a class="service-card" href="../php/booking.php?package=maincharacter&price=<?= $package['price'] ?>">
      <button class="btn-book">Book Now</button>
    </a>
  <?php else: ?>
    <a href="login.php"><button class="btn-book">Log in to Book</button></a>
  <?php endif; ?>
</div>



      </div>
    </div>

    <!-- IMAGE CAROUSEL -->
<div class="carousel">
  <?php foreach ($images as $img): ?>
    <img src="../uploads/<?= htmlspecialchars(trim($img)) ?>" alt="Package Image">
  <?php endforeach; ?>
</div>


    <!-- PACKAGE DETAILS -->
    
  </section>
  <br/><br/><br/><br/>
  <div class="package-details">
      <h3>Main Character Package Includes:</h3>
      <ul>
  <?php foreach ($includes as $item): ?>
    <li><?= htmlspecialchars(trim($item)) ?></li>
  <?php endforeach; ?>
</ul>

    </div>

  <!-- FOOTER -->
  

   
  </footer><footer class="footer">
  <h2>Get In Touch</h2>
  <p class="footer-tagline">Capturing moments. Creating stories. Celebrating you.</p>
  <div class="footer-grid">
    <div class="footer-logo">
      <img src="..\images\1 4.png" alt="Mantra Studio Logo">
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
  const carousel = document.querySelector('.carousel');
  const next = document.querySelector('.next');
  const prev = document.querySelector('.prev');

  // Duplicate images to simulate infinite loop
  const images = Array.from(carousel.children);
  images.forEach(img => {
    const clone = img.cloneNode(true);
    carousel.appendChild(clone);
  });

  const scrollAmount = 300;
  let scrollPos = 0;

  next.addEventListener('click', () => {
    scrollPos += scrollAmount;
    if (scrollPos >= carousel.scrollWidth / 2) {
      scrollPos = 0; // reset to start
    }
    carousel.scrollTo({ left: scrollPos, behavior: 'smooth' });
  });

  prev.addEventListener('click', () => {
    scrollPos -= scrollAmount;
    if (scrollPos < 0) {
      scrollPos = carousel.scrollWidth / 2 - scrollAmount;
    }
    carousel.scrollTo({ left: scrollPos, behavior: 'smooth' });
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
