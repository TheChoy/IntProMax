<?php
include 'con_repair.php';

// กำหนดค่าคงที่
session_start();
$current_date = date('Y-m-d'); // วันที่ปัจจุบัน
$registration_car = $_POST['registration_car'] ?? ''; // หมายเลขทะเบียนรถ
$id_staff = $_SESSION['user_id']; // รหัสพนักงานซ่อม (ค่าเริ่มต้น)


foreach ($_POST["status"] as $section_title => $status) {
    $repair_reason = $_POST["reason"][$section_title] ?? '';
    $type = in_array($section_title, ['เครื่องAED', 'เครื่องช่วยหายใจ', 'ถังออกซิเจน', 'เครื่องวัดความดัน', 'เครื่องวัดชีพจร', 'เตียงพยาบาล', 'เปลสนาม', 'อุปกรณ์ปฐมพยาบาล', 'อุปกรณ์การดาม']) ? 'อุปกรณ์ทางการแพทย์' : 'รถพยาบาล';
    if ($status == "ไม่พร้อม"){
        $status = "รอดำเนินการ";
    }
    $stmt3 = $conn->prepare("INSERT INTO repair (ambulance_id, repair_staff_id, repair_date, repair_type, repair_repairing, repair_reason, repair_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt3->bind_param("iisssss", $registration_car, $id_staff, $current_date, $type, $section_title, $repair_reason, $status);
    $stmt3->execute();
    $stmt3->close();
}
$sqlCheck = "SELECT COUNT(*) as count FROM repair WHERE ambulance_id = ? AND repair_status IN ('รอดำเนินการ', 'กำลังดำเนินการ')";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("i", $registration_car);
$stmtCheck->execute();
$result = $stmtCheck->get_result();
$row = $result->fetch_assoc();
$stmtCheck->close();

$ambu_status = ($row['count'] > 0) ? 'ไม่พร้อม' : 'พร้อม';

$sqlUpdate = "UPDATE ambulance SET ambulance_status = ? WHERE ambulance_id = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("si", $ambu_status, $registration_car);
$stmtUpdate->execute();
$stmtUpdate->close();

header("Location: car_report_success.php");
exit();

$conn->close();
