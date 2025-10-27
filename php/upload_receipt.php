<?php
session_start();
require_once '../api/db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['receipt']) && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);
    $user_id = $_SESSION['user_id'];
    
    // Define both upload directories
    $adminUploadDir = 'D:/xampp/htdocs/admin_montra/uploads/';
    $userUploadDir = 'D:/xampp/htdocs/montra_website/uploads/';

    // Create directories if they don't exist
    if (!is_dir($adminUploadDir)) {
        mkdir($adminUploadDir, 0777, true);
    }
    if (!is_dir($userUploadDir)) {
        mkdir($userUploadDir, 0777, true);
    }

    // File setup
    $fileName = basename($_FILES['receipt']['name']);
    $fileTmp = $_FILES['receipt']['tmp_name'];
    $uniqueName = time() . '_' . $fileName;
    $targetPathAdmin = $adminUploadDir . $uniqueName;
    $targetPathUser = $userUploadDir . $uniqueName;
    $fileType = strtolower(pathinfo($targetPathAdmin, PATHINFO_EXTENSION));

    // Allow only images
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (!in_array($fileType, $allowedTypes)) {
        die("Only JPG, JPEG, and PNG files are allowed.");
    }

    // Move file to admin uploads folder
    if (move_uploaded_file($fileTmp, $targetPathAdmin)) {
        // Copy file to user uploads folder
        copy($targetPathAdmin, $targetPathUser);

        // Save filename (not full path) to DB
        $stmt = $conn->prepare("UPDATE bookings SET receipt_image = ? WHERE id = ? AND user_id = ?");
        if (!$stmt) {
            die("Database error: " . $conn->error);
        }
        $stmt->bind_param("sii", $uniqueName, $booking_id, $user_id);
        $stmt->execute();

        $stmt->close();
        $conn->close();

        header("Location: profile.php?upload=success");
        exit();
    } else {
        die("Error uploading file.");
    }
} else {
    die("Invalid request.");
}
?>
