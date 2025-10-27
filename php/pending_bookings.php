<?php
session_start();
require_once '../api/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../php/login.php");
    exit();
}

// Fetch user info
$user_id = $_SESSION['user_id'];
$query = "SELECT first_name, last_name, email, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch only pending bookings
$sql = "SELECT id, package_name, preferred_date, preferred_time, special_request, total_price, addons, status, receipt_image, created_at 
        FROM bookings 
        WHERE user_id = ? AND status = 'pending'
        ORDER BY created_at DESC";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$bookings = $result2->fetch_all(MYSQLI_ASSOC);
$stmt2->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pending Bookings | Montra Studio</title>
  <link rel="stylesheet" href="../css/profile.css">
  <link rel="stylesheet" href="../css/footer.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    .profile-icon {
  position: relative;
  display: inline-block;
}

.profile-icon img {
  width: 30px;
  height: 30px;
  border-radius: 50%;

  cursor: pointer;
  transition: all 0.3s ease;
}

.profile-icon img:hover {
  border-color: #020337ff;
  transform: scale(1.02);
}

/* Dropdown menu styling */
.dropdown-menu {
  display: none;
  position: absolute;
  top: 55px;
  right: 0;
  background-color: #fff;
  border-radius: 10px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
  width: 230px;
  overflow: hidden;
  z-index: 100;
  animation: dropdownFade 0.25s ease-in-out;
}

/* User info section */
.dropdown-user {
  background: #f8f8f8;
  padding: 15px;
  text-align: left;
}

.dropdown-user p {
  margin: 3px 0;
  color: #333;
  font-size: 14px;
}

.dropdown-user strong {
  font-weight: 600;
  color: #222;
}

.member-since {
  font-size: 12px;
  color: #777;
  margin-top: 5px;
}

/* Links inside dropdown */
.dropdown-item {
  display: block;
  padding: 12px 16px;
  text-decoration: none;
  color: #333;
  font-size: 14px;
  transition: background-color 0.2s ease;
}

.dropdown-item:hover {
  background-color: #f2f2f2;
}

/* Logout link */
.logout {
  color: #c0392b;
  font-weight: 500;
}

/* Divider */
.dropdown-menu hr {
  margin: 8px 0;
  border: none;
  border-top: 1px solid #e0e0e0;
}

/* Subtle open animation */
@keyframes dropdownFade {
  from {
    opacity: 0;
    transform: translateY(-8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
 body { font-family: 'Inter', sans-serif;  margin:0;   background-color: #fff9f9;}

    .booking-section { margin-top: 40px; }
    .booking-section { margin: 40px auto; max-width: 1100px; background: #fff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); padding: 30px; }
    table.bookings { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table.bookings th, table.bookings td { padding: 10px; border: 1px solid #e6e6e6; text-align: left; vertical-align: middle; }
    table.bookings th { background:#111; color:#fff; }
    .badge { padding:6px 10px; border-radius:8px; color:#fff; font-weight:600; font-size:0.9em; }
    .badge.pending { background:#ffc107; color:#222; }
    .upload-section { text-align:center; background:#f9f9f9; padding:15px; border-radius:8px; margin-top:10px; }
    .upload-section img { max-width:200px; border-radius:8px; cursor:pointer; transition:transform 0.2s; }
    .upload-section img:hover { transform:scale(1.05); }
 /* Dropdown profile styles */
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

<!-- ✅ Keep the top bar intact -->
 <header class="header">
  <div class="logo">
    <a href="../html/homepage.html"><img src="../images/LOGO.png" alt="Montra Studio Logo" style="height:100px; width:300px;"></a>
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

<!-- 🧾 Pending Bookings -->
<main>
  <section class="booking-section">
    <h2>Your Pending Bookings</h2>

    <?php if (empty($bookings)): ?>
      <p style="margin-top:10px; color:#666;">You have no pending bookings at the moment.</p>
    <?php else: ?>
      <table class="bookings">
        <thead>
          <tr>
            <th>Package</th>
            <th>Date</th>
            <th>Time</th>
            <th>Add-ons</th>
             <th>Special request</th>
            <th>Total (₱)</th>
            <th>Status</th>
            <th>Booked On</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bookings as $b): ?>
            <tr>
              <td><?= htmlspecialchars($b['package_name']) ?></td>
              <td><?= htmlspecialchars($b['preferred_date']) ?></td>
              <td><?= htmlspecialchars($b['preferred_time']) ?></td>
              <td><?= nl2br(htmlspecialchars($b['addons'] ?: '—')) ?></td>
              <td><?= nl2br(htmlspecialchars($b['special_request'] ?: '—')) ?></td>
              <td>₱<?= number_format($b['total_price'], 2) ?></td>
              <td><span class="badge pending">Pending</span></td>
              <td><?= htmlspecialchars((new DateTime($b['created_at']))->format('M d, Y')) ?></td>
            </tr>

            <tr>
              <td colspan="7">
                <div class="upload-section">
                  <strong>To confirm your booking, please pay ₱100 and upload your receipt below. GCash: 09671087944</strong><br><br>

                  <?php if (!empty($b['receipt_image'])): ?>
                    <p>✅ Receipt uploaded. Click the image to view full size.</p>
                    <img src="../uploads/<?= htmlspecialchars($b['receipt_image']) ?>" 
                         alt="Receipt"
                         onclick="openReceiptModal('../uploads/<?= htmlspecialchars($b['receipt_image']) ?>')">
                  <?php else: ?>
                    <form action="upload_receipt.php" method="POST" enctype="multipart/form-data">
                      <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                      <input type="file" name="receipt" accept="image/*" required>
                      <button type="submit" style="margin-left:10px;padding:6px 12px;">Upload Receipt</button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
</main>

<!-- 📸 Modal for full-size receipt -->
<div id="receiptModal" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.8);
    justify-content:center;
    align-items:center;
    z-index:1000;">
  <img id="receiptPreview" src="" alt="Full Receipt" style="max-width:90%; max-height:90%; border-radius:12px;">
</div>

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

  // Modal for receipts
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
