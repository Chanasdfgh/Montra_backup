<?php
session_start();
require_once '../api/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $package = $_POST['package'];
    $base_price = floatval($_POST['base_price']);
    $preferred_date = $_POST['preferred_date'];
    $preferred_time = $_POST['preferred_time'];
    $contact_person = $_POST['contact_person'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $special_request = $_POST['special_request'] ?? '';
    
    // Handle add-ons (array)
    $addons = $_POST['addons'] ?? []; // This will be an array
    $addons_str = implode(', ', $addons); // Convert to string for DB storage

    // Calculate total price
    $total_price = $base_price;
    if (in_array('instant_photo', $addons)) $total_price += 500;
    if (in_array('custom_frame', $addons)) $total_price += 300;
    if (in_array('extended_time', $addons)) $total_price += 100;

    $stmt = $conn->prepare("INSERT INTO bookings 
        (user_id, package_name, preferred_date, preferred_time, contact_person, email, phone, special_request, addons, total_price)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "issssssssd",
        $user_id,
        $package,
        $preferred_date,
        $preferred_time,
        $contact_person,
        $email,
        $phone,
        $special_request,
        $addons_str,
        $total_price
    );

    if ($stmt->execute()) {
    header("Location: profile.php");
    exit();
} else {
    echo "Database Error: " . $stmt->error;
}


    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
