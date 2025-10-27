<?php
session_start();
require_once '../api/db_connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT first_name, last_name, email, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed (user): " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}
$stmt->close();

// Fetch user bookings (use correct column names from your table)
$bookings = [];
$sql = "SELECT id, package_name, preferred_date, preferred_time, total_price, addons, status, receipt_image, created_at 
        FROM bookings 
        WHERE user_id = ?
        ORDER BY created_at DESC";
$stmt2 = $conn->prepare($sql);
if (!$stmt2) {
    die("Prepare failed (bookings): " . $conn->error);
}
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();

while ($row = $result2->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt2->close();
$conn->close();

// Group bookings by status for display
$grouped = [
    'pending' => [],
    'approved' => [],
    'completed' => [],
    'rejected' => []
];

foreach ($bookings as $b) {
    $status = $b['status'] ?? 'pending';
    if (!isset($grouped[$status])) {
        $status = 'pending';
    }
    $grouped[$status][] = $b;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile | Montra Studio</title>
  <link rel="stylesheet" href="../css/profile.css">
  <link rel="stylesheet" href="../css/footer.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>
  <!-- HEADER -->
 <header class="header">
  <div class="logo">
    <a href="../php/homepage.php"><img src="../images/LOGO.png" alt="Montra Studio Logo" style="height:100px; width:300px;"></a>
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

  <!-- Profile Section -->
 <main>
   <div class="profile-container">
    <div class="profile-card">
      <div class="profile-top">
        <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="User Avatar" class="profile-img">
        <div>
          <h2>Welcome, <?= htmlspecialchars($user['first_name']) ?>!</h2>
          <p><?= htmlspecialchars($user['email']) ?></p>
          <div class="info-section">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name'] . " " . $user['last_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Member Since:</strong> <?= (new DateTime($user['created_at']))->format('F j, Y') ?></p>
          </div>
        </div>
      </div>

      <!-- Booking Status Section -->
      <div class="booking-section">
        <h3>Your Bookings</h3>

        <?php
        $statuses = ['pending' => 'Pending', 'approved' => 'Approved', 'completed' => 'Completed', 'rejected' => 'Rejected'];
        foreach ($statuses as $key => $label):
        ?>
          <h4><?= $label ?> (<?= count($grouped[$key]) ?>)</h4>

          <?php if (empty($grouped[$key])): ?>
            <p class="text-muted">No <?= strtolower($label) ?> bookings.</p>
          <?php else: ?>
            <table class="bookings">
              <thead>
                <tr>
                  <th>Package</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Add-ons</th>
                  <th>Total (₱)</th>
                  <th>Status</th>
                  <th>Booked On</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($grouped[$key] as $b): ?>
  <tr>
    <td><?= htmlspecialchars($b['package_name']) ?></td>
    <td><?= htmlspecialchars($b['preferred_date']) ?></td>
    <td><?= htmlspecialchars($b['preferred_time']) ?></td>
    <td><?= nl2br(htmlspecialchars($b['addons'] ?: '—')) ?></td>
    <td>₱<?= number_format($b['total_price'], 2) ?></td>
    <td><span class="badge <?= htmlspecialchars($b['status']) ?>"><?= ucfirst($b['status']) ?></span></td>
    <td><?= htmlspecialchars((new DateTime($b['created_at']))->format('M d, Y')) ?></td>
  </tr>

  <?php if ($b['status'] === 'pending'): ?>
    <tr>
      <td colspan="7" style="background:#f9f9f9; text-align:center;">
        <strong>To confirm your booking, please pay ₱100 and upload your receipt below. Our Gcash acc is 09671087944</strong><br><br>

        <?php if (!empty($b['receipt_image'])): ?>
  <div style="text-align:center;">
    <p>✅ Receipt uploaded. Click the image to view full size.</p>
    <img src="../uploads/<?= htmlspecialchars($b['receipt_image']) ?>"
         alt="Receipt"
         style="max-width:200px;border-radius:8px;cursor:pointer;transition:transform 0.2s;"
         onclick="openReceiptModal('../uploads/<?= htmlspecialchars($b['receipt_image']) ?>')">
  </div>
<?php else: ?>
  <form action="upload_receipt.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
    <input type="file" name="receipt" accept="image/*" required>
    <button type="submit" style="margin-left:10px;padding:6px 12px;">Upload Receipt</button>
  </form>
<?php endif; ?>

      </td>
    </tr>
  <?php endif; ?>
<?php endforeach; ?>

              </tbody>
            </table>
          <?php endif; ?>

        <?php endforeach; ?>
      </div>
    </div>
  </div>



 </main>
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
<div id="receiptModal" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.8);
    justify-content:center;
    align-items:center;
    z-index:1000;
">
  <img id="receiptPreview" src="" alt="Full Receipt" style="max-width:90%; max-height:90%; border-radius:12px;">
</div>

<script>
  const modal = document.getElementById('receiptModal');
  const preview = document.getElementById('receiptPreview');

  function openReceiptModal(src) {
    preview.src = src;
    modal.style.display = 'flex';
  }

  modal.addEventListener('click', () => {
    modal.style.display = 'none';
    preview.src = '';
  });
   // NEW: Profile dropdown toggle
  const profileToggle = document.getElementById('profileToggle');
  const profileMenu = document.getElementById('profileMenu');
  profileToggle.addEventListener('click', () => profileMenu.classList.toggle('show'));
  window.addEventListener('click', (e) => {
    if (!profileMenu.contains(e.target) && !profileToggle.contains(e.target)) {
      profileMenu.classList.remove('show');
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
</script> <?php endif; ?> </body> </html>
