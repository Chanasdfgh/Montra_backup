<?php
session_start();
require_once '../api/db_connect.php';

// Load PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate reset token
        $token = bin2hex(random_bytes(50));

        // Store token in DB with MySQL-generated expiry
        $stmt_update = $conn->prepare("UPDATE users SET reset_token=?, token_expiry=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email=?");
        $stmt_update->bind_param("ss", $token, $email);
        $stmt_update->execute();

        // Reset link
        $reset_link = "http://localhost/montra_website/php/reset_password.php?token=$token";

        // Send email via PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'caramatcristine@gmail.com'; // your Gmail
            $mail->Password   = 'tivm kjid xjjk hysc';   // Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('yourgmail@gmail.com', 'Montra Studio');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Montra Studio Password Reset';
            $mail->Body    = "Hi! Click this link to reset your password: <a href='$reset_link'>$reset_link</a>";

            $mail->send();
            $message = "Check your email for the password reset link.";
        } catch (Exception $e) {
            $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    } else {
        $message = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | Montra Studio</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<div class="login-section">
    <div class="login-card">
        <h2>Forgot Password</h2>
        <?php if ($message) echo "<p>$message</p>"; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <p><a href="login.php">Back to Login</a></p>
    </div>
</div>
</body>
</html>
