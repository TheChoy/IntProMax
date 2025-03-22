<?php
include 'con_repair.php';
session_start();

$date = date('Y-m-d');
$ambulance_id = $_POST['car_number'] ?? '';
$category = $_POST['category'] ?? '';
$device = $_POST['device'] ?? '';
$reason = $_POST['reason'] ?? '';
$reporter = $_SESSION['user_id'];
$re_status = 'รอดำเนินการ';

// เพิ่มข้อมูลลงในตาราง car_report
$stmt = $conn->prepare("INSERT INTO repair (ambulance_id, repair_staff_id, repair_date, repair_type, repair_repairing, repair_reason, repair_status ) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisssss", $ambulance_id, $reporter, $date, $category , $device, $reason, $re_status);
$stmt->execute();

$sqlCheck = "SELECT COUNT(*) as count FROM repair WHERE ambulance_id = ? AND repair_status IN ('รอดำเนินการ', 'กำลังดำเนินการ')";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("i", $ambulance_id);
$stmtCheck->execute();
$result = $stmtCheck->get_result();
$row = $result->fetch_assoc();
$stmtCheck->close();

// ถ้ามี -> ไม่พร้อม, ถ้าไม่มี -> พร้อม
$ambu_status = ($row['count'] > 0) ? 'ไม่พร้อม' : 'พร้อม';

// อัปเดตสถานะรถพยาบาล
$sqlUpdate = "UPDATE ambulance SET ambulance_status = ? WHERE ambulance_id = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("si", $ambu_status, $ambulance_id);
$stmtUpdate->execute();
$stmtUpdate->close();

header("Location: repair.php");

$stmt->close();
$conn->close();
?>