<?php
include 'con_repair.php';

// รับค่า JSON และแปลงเป็น PHP array
$data = json_decode(file_get_contents('php://input'), true);

$repair_id = $data['repair_id'];
$value = $data['value'];
$type = $data['type'];

// เตรียม query ตามประเภทการอัพเดท
if ($type === 'date') {
    $sql = "UPDATE repair SET repair_success_datetime = ? WHERE repair_id = ?";
} else if ($type === 'status') {
    $sql = "UPDATE repair SET repair_status = ? WHERE repair_id = ?";
}

// เตรียมและ execute คำสั่ง SQL
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $value, $repair_id);

if ($stmt->execute()) {
    http_response_code(200);
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
?>