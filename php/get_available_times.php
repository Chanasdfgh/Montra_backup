<?php
// php/get_available_times.php
header('Content-Type: application/json; charset=utf-8');
require_once '../api/db_connect.php';

if (!isset($_GET['date'])) {
    echo json_encode(['error' => 'Missing date']);
    exit;
}

$date = $_GET['date'];

// helper: generate all half-hour start times from 08:00 to 18:30
function generate_slots(): array {
    $slots = [];
    $dt = new DateTimeImmutable('08:00');
    $end = new DateTimeImmutable('18:30'); // last start
    while ($dt <= $end) {
        $slots[] = $dt->format('H:i');
        $dt = $dt->modify('+30 minutes');
    }
    return $slots;
}

// fetch approved bookings on that date
$sql = "SELECT preferred_time, addons FROM bookings WHERE preferred_date = ? AND status = 'approved'";
$stmt = $conn->prepare($sql);
if (!$stmt) { echo json_encode(['error' => $conn->error]); exit; }
$stmt->bind_param('s', $date);
$stmt->execute();
$res = $stmt->get_result();

$occupied = []; // start times (H:i) that are blocked

while ($row = $res->fetch_assoc()) {
    // preferred_time is TIME, e.g. "08:00:00"
    $time = substr($row['preferred_time'], 0, 5); // "08:00"
    $occupied[$time] = true;
    // if booking has extension, block next slot too
    $addons = $row['addons'] ?? '';
    if (strpos($addons, 'extended_time') !== false) {
        // compute next half-hour
        $dt = DateTime::createFromFormat('H:i', $time);
        if ($dt !== false) {
            $next = $dt->modify('+30 minutes')->format('H:i');
            $occupied[$next] = true;
        }
    }
}
$stmt->close();

// build available list by removing occupied
$all = generate_slots();
$available = [];
foreach ($all as $start) {
    if (!isset($occupied[$start])) {
        // also compute label end time
        $dt = DateTime::createFromFormat('H:i', $start);
        $end = $dt->modify('+30 minutes')->format('H:i');
        $available[] = ['value' => $start, 'label' => "{$start} - {$end}"];
    }
}

echo json_encode(['slots' => $available]);
