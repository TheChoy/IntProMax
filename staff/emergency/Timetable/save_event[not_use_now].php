// หน้านี้ยังไม่ได้ใช้งานจริง


<?php
date_default_timezone_set('Asia/Bangkok'); // ตั้งค่าโซนเวลา

// เชื่อมต่อฐานข้อมูล
$con = new mysqli('localhost', 'root', '1234', 'intpro');
if ($con->connect_error) {
    die('Connection Failed: ' . $con->connect_error);
}

// บันทึกข้อมูล
// ตรวจสอบว่ามีข้อมูลที่จำเป็นส่งมาหรือไม่หากครบก็จะทำการบันทึกข้อมูลลงในฐานข้อมูล
if (isset($_POST['title'], $_POST['type'], $_POST['start'], $_POST['end'])) { 
    $title = $_POST['title'];
    $type = $_POST['type'];
    $start = $_POST['start'];
    $end = $_POST['end'];

    // คำสั่ง SQL สำหรับบันทึกข้อมูลลงในตาราง time_table
    $sql = "INSERT INTO time_table (title, type, start_datetime, end_datetime) VALUES (?, ?, ?, ?)"; // ใช้ ? เป็น placeholder เพื่อป้องกัน SQL Injection
    $stmt = $con->prepare($sql);
    // ผูกค่าตัวแปร $title, $type, $start, $end กับ SQL โดยกำหนดประเภทข้อมูลเป็น "ssss" (String ทั้งหมด)
    $stmt->bind_param("ssss", $title, $type, $start, $end);


    // เรียกใช้คำสั่ง SQL ผ่าน execute() หากสำเร็จจะแสดงข้อความว่า "Event saved successfully" และหากไม่สำเร็จจะแสดงข้อความว่า error พร้อมกับข้อความผิดพลาด
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Event saved successfully']);
    } else {
        echo json_encode(['error' => $stmt->error]);
    }
    $stmt->close();

//กรณีข้อมูลที่ส่งมาไม่ครบ จะส่งข้อความข้อผิดพลาดแบบ JSON กลับไป
} else {
    echo json_encode(['error' => 'Invalid input']);
}

$con->close();
?>