<?php
session_start();
require_once '../api/db_connect.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = intval($_SESSION['user_id']);

$sql = "SELECT id, message, is_read, created_at
        FROM notifications_user
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$notifications = [];
while ($row = $res->fetch_assoc()) {
    $notifications[] = $row;
}

echo json_encode($notifications);
?>
