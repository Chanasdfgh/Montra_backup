<?php
session_start();
require_once '../api/db_connect.php';

// Set proper timezone to avoid expiry mismatch
date_default_timezone_set('Asia/Manila');

if (!isset($_GET['token'])) {
    die("Invalid request. No token provided.");
}

$token = $_GET['token'];

// Fetch token info from database
$stmt = $conn->prepare("SELECT id, reset_token, token_expiry FROM users WHERE reset_token=?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid token. No matching token found in database.");
}

$user = $result->fetch_assoc();

// Check if token expired
if (strtotime($user['token_expiry']) < time()) {
    die("Token expired. Please request a new password reset.");
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Update password and remove token
    $stmt_update = $conn->prepare("UPDATE users SET password=?, reset_token=NULL, token_expiry=NULL WHERE id=?");
    $stmt_update->bind_param("si", $new_password, $user['id']);
    $stmt_update->execute();

    $message = "Password reset successfully. <a href='login.php'>Login here</a>.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | Montra Studio</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<div class="login-section">
    <div class="login-card">
        <h2>Reset Password</h2>
        <?php if ($message) echo "<p>$message</p>"; ?>
        <form method="POST" action="">
            <input type="password" name="password" placeholder="Enter new password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</div>
</body>
</html>
