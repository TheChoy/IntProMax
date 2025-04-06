<?php
// เริ่ม session ก่อนใช้งานตัวแปร session
session_start();

// ตั้ง timezone เป็น Bangkok
date_default_timezone_set('Asia/Bangkok');

// เชื่อมต่อฐานข้อมูล
$con = new mysqli('localhost', 'root', '', 'intpro');

// ตรวจสอบการเชื่อมต่อ
if ($con->connect_error) {
    die(json_encode(['error' => 'Connection Failed: ' . $con->connect_error]));
}

// ตรวจสอบว่า login แล้วหรือยัง
if (!isset($_SESSION['logged_in'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$staff_id = $_SESSION['user_id'];

// ดึง ambulance_id ที่ assign กับเจ้าหน้าที่คนนี้
$ambulance_ids = [];
$sql = "SELECT ambulance_id FROM assigns WHERE emergency_staff_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $ambulance_ids[] = $row['ambulance_id'];
}

if (empty($ambulance_ids)) {
    echo json_encode([]);
    exit;
}

// เตรียม placeholder และ types
$placeholders = implode(',', array_fill(0, count($ambulance_ids), '?'));
$types = str_repeat('i', count($ambulance_ids));

$events = [];

// ดึงข้อมูล ambulance_booking
$ambulance_sql = "SELECT * FROM ambulance_booking WHERE ambulance_id IN ($placeholders)";
$ambulance_stmt = $con->prepare($ambulance_sql);
$ambulance_stmt->bind_param($types, ...$ambulance_ids);
$ambulance_stmt->execute();
$ambulance_result = $ambulance_stmt->get_result();

while ($row = $ambulance_result->fetch_assoc()) {
    $events[] = [
        'id'    => $row['ambulance_booking_id'],
        'title' => "🚑 " . $row['ambulance_booking_location'] .
                   " (" . $row['ambulance_booking_province'] . ")" .
                   " | โรค: " . $row['ambulance_booking_disease'] .
                   " | จุดพัก: " . $row['ambulance_booking_hospital_waypoint'],
        'start' => $row['ambulance_booking_date'] . 'T' . $row['ambulance_booking_start_time'],
        'end'   => $row['ambulance_booking_date'] . 'T' . $row['ambulance_booking_finish_time'],
        'type'  => 'ambulance'
    ];
}

// ดึงข้อมูล event_booking
$event_sql = "SELECT * FROM event_booking WHERE ambulance_id IN ($placeholders)";
$event_stmt = $con->prepare($event_sql);
$event_stmt->bind_param($types, ...$ambulance_ids);
$event_stmt->execute();
$event_result = $event_stmt->get_result();

while ($row = $event_result->fetch_assoc()) {
    $events[] = [
        'id'    => $row['event_booking_id'],
        'title' => "🎪 " . $row['event_booking_type'] .
                   " @ " . $row['event_booking_location'] .
                   " | พยาบาล: " . $row['event_booking_amount_nurse'] .
                   " | จังหวัด: " . $row['event_booking_province'],
        'start' => $row['event_booking_date'] . 'T' . $row['event_booking_start_time'],
        'end'   => $row['event_booking_date'] . 'T' . $row['event_booking_finish_time'],
        'type'  => 'event'
    ];
}

// ส่งข้อมูลกลับแบบ JSON
echo json_encode($events, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>