<?php
// กำหนดค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost"; // ชื่อเซิร์ฟเวอร์ฐานข้อมูล (localhost คือเครื่องเดียวกัน)
$username = "root"; // ชื่อผู้ใช้ MySQL
$password = "1234"; // รหัสผ่าน MySQL
$dbname = "car_report"; // ชื่อฐานข้อมูลที่ใช้

// สร้างการเชื่อมต่อกับฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบว่าการเชื่อมต่อสำเร็จหรือไม่
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // แสดงข้อผิดพลาดหากเชื่อมต่อไม่ได้
}
header("Location: repair.php");

$date = date('Y-m-d');
$car_number = $_POST['car_number'] ?? '';
$category = $_POST['category'] ?? '';
$device = $_POST['device'] ?? '';
$reason = $_POST['reason'] ?? '';
$reporter = 1;
$status = 'รอดำเนินการ';

// เพิ่มข้อมูลลงในตาราง car_report
$stmt = $conn->prepare("INSERT INTO repair (ambulance_id, repair_staff_id, repair_date, repair_type, repairing, repair_reason, repair_status ) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisssss", $car_number, $reporter, $date, $category , $device, $reason, $status);
$stmt->execute();

$stmt->close();
$conn->close();
?>