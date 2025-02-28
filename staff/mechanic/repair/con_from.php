<?php
include 'con_repair.php';
session_start();

$date = date('Y-m-d');
$car_number = $_POST['car_number'] ?? '';
$category = $_POST['category'] ?? '';
$device = $_POST['device'] ?? '';
$reason = $_POST['reason'] ?? '';
$reporter = $_SESSION['user_id'];
$status = 'รอดำเนินการ';

// เพิ่มข้อมูลลงในตาราง car_report
$stmt = $conn->prepare("INSERT INTO repair (ambulance_id, repair_staff_id, repair_date, repair_type, repair_repairing, repair_reason, repair_status ) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisssss", $car_number, $reporter, $date, $category , $device, $reason, $status);
$stmt->execute();

header("Location: repair.php");

$stmt->close();
$conn->close();
?>