<?php
// กำหนดค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost"; // ชื่อเซิร์ฟเวอร์ฐานข้อมูล (localhost คือเครื่องเดียวกัน)
$username = "root"; // ชื่อผู้ใช้ MySQL
$password = ""; // รหัสผ่าน MySQL
$dbname = "intpro"; // ชื่อฐานข้อมูลที่ใช้

// สร้างการเชื่อมต่อกับฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบว่าการเชื่อมต่อสำเร็จหรือไม่
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // แสดงข้อผิดพลาดหากเชื่อมต่อไม่ได้
}
echo "Connected successfully <br>";

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

header("Location: car_report_success.php");
exit();

$conn->close();
