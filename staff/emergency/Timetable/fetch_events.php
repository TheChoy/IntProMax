<?php
// กำหนดเขตเวลาให้เป็น Asia/Bangkok เพื่อให้เวลาแสดงผลถูกต้อง
// ป้องกันปัญหาการแสดงเวลาผิดจาก Time Zone ของเซิร์ฟเวอร์
date_default_timezone_set('Asia/Bangkok');  

// เชื่อมต่อฐานข้อมูล MySQL
$con = new mysqli('localhost', 'root', '1234', 'intpro'); // ใช้ database intpro

// ตรวจสอบการเชื่อมต่อฐานข้อมูล ถ้าล้มเหลวจะแสดงข้อความข้อผิดพลาด
if ($con->connect_error) {
    die(json_encode(['error' => 'Connection Failed: ' . $con->connect_error]));
}

// สร้างคำสั่ง SQL สำหรับดึงข้อมูลจากฐานข้อมูล
$sql = "
    SELECT 
        ambulance_booking_location AS title,
        'ambulance' as type,
        CONCAT(ambulance_booking_date, 'T', ambulance_booking_start_time) AS start, 
        CASE 
            WHEN ambulance_booking_fisnish_time IS NULL OR ambulance_booking_fisnish_time = '' 
            THEN CONCAT(ambulance_booking_date, 'T', ADDTIME(ambulance_booking_start_time, '01:00:00')) 
            ELSE CONCAT(ambulance_booking_date, 'T', ambulance_booking_fisnish_time) 
        END AS end
    FROM ambulance_booking

    UNION

    SELECT 
        event_booking_location AS title, 
        'event' as type,
        CONCAT(event_booking_date, 'T', event_booking_start_time) AS start, 
        CASE 
            WHEN event_booking_finish_time IS NULL OR event_booking_finish_time = '' 
            THEN CONCAT(event_booking_date, 'T', ADDTIME(event_booking_start_time, '01:00:00')) 
            ELSE CONCAT(event_booking_date, 'T', event_booking_finish_time) 
        END AS end
    FROM event_booking
";

// รันคำสั่ง SQL และตรวจสอบว่ามีข้อผิดพลาดหรือไม่
$result = $con->query($sql);
if (!$result) {
    die(json_encode(['error' => 'Query Failed: ' . $con->error]));
}

$events = [];
// วนลูปดึงข้อมูลแต่ละแถวจากฐานข้อมูล และบันทึกเป็น JSON Array
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'title' => $row['title'], // ชื่อ Event
        'start' => $row['start'], // วันที่และเวลาเริ่มต้น
        'end' => $row['end'], // วันที่และเวลาสิ้นสุด
        'type' => $row['type'], // ประเภทของ Event (ambulance หรือ event)
        'allDay' => false // กำหนดให้ Event ไม่เป็นแบบ All-day event
    ];
}

// ปิดการเชื่อมต่อฐานข้อมูล
$con->close();

// กำหนดให้ข้อมูลที่ส่งออกเป็น JSON และให้รองรับภาษาไทยได้อย่างถูกต้อง
header('Content-Type: application/json');
echo json_encode($events, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
