<?php
include 'username.php';

$province = $_POST['province'];
$place_event_location = $_POST['place_event_location'];
$place_event_detail = $_POST['place_event_detail'];
$type =$_POST['event_type'];
$nurse_number = $_POST['nurse_number'];
$ambulance_number = $_POST['ambulance_number'];
$payment_method = $_POST['payment_method_event'];

$ambulance_id = rand(1, 10);
$member_id = rand(1, 10);
// $ambulance_level = $_POST['level'];
// $date = $_POST['date'];  //
// $bookingType = $_POST['booking-type'];
// $level = $_POST['level'];
// $time_S = $_POST['time-slot'];
// $time_F = $_POST['time-slot2'];

print_r($_POST);
$sql = "INSERT INTO event_booking (member_id,ambulance_id,event_booking_province,event_booking_location, event_booking_detail, event_booking_type,event_booking_amount_nurse, event_booking_amount_ambulance,event_booking_buy_type) 
        VALUES ('$member_id','$ambulance_id','$province','$place_event_location','$place_event_detail', '$type', '$nurse_number', '$ambulance_number', '$payment_method')";



if ($conn->query($sql) === TRUE) {
    echo "ข้อมูลถูกบันทึกเรียบร้อยแล้ว";
} else {
    echo "เกิดข้อผิดพลาด: " . $sql . "<br>" . $connect->error;
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
