<?php
include 'con_repair.php';

$data = json_decode(file_get_contents('php://input'), true);

$repair_id = $data['repair_id'];
$value = $data['value'];
$type = $data['type'];

if ($type === 'date') {
    $stmt = $conn->prepare("SELECT repair_date FROM repair WHERE repair_id = ?");
    $stmt->bind_param("i", $repair_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $repair_date = $row['repair_date'];

    if ($value < $repair_date) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'วันเสร็จสิ้นต้องไม่ก่อนวันรับซ่อม']);
        exit;
    }
    $sql = "UPDATE repair SET repair_success_datetime = ? WHERE repair_id = ?";
    
} else if ($type === 'status') {
    if ($value === 'เสร็จสิ้น') {
        $stmt = $conn->prepare("SELECT repair_success_datetime, repair_cost FROM repair WHERE repair_id = ?");
        $stmt->bind_param("i", $repair_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (empty($row['repair_success_datetime']) || $row['repair_cost'] <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'กรุณากรอกวันที่เสร็จสิ้นและราคาซ่อมก่อนเปลี่ยนสถานะ']);
            exit;
        }
    }
    $sql = "UPDATE repair SET repair_status = ? WHERE repair_id = ?";

} else if ($type === 'cost') {
    $sql = "UPDATE repair SET repair_cost = ? WHERE repair_id = ?";
}else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid type']);
    exit;
}

// อัปเดต repair
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $value, $repair_id);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Update repair failed: ' . $conn->error]);
    exit;
}
$stmt->close();

// ดึง ambulance_id ที่เกี่ยวข้อง
$stmt = $conn->prepare("SELECT ambulance_id FROM repair WHERE repair_id = ?");
$stmt->bind_param("i", $repair_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$ambulance_id = $row['ambulance_id'];
$stmt->close();

// ตรวจสอบสถานะซ่อม
$checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE ambulance_id = ? AND repair_status IN ('รอดำเนินการ', 'กำลังดำเนินการ')");
$checkStmt->bind_param("i", $ambulance_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$countRow = $checkResult->fetch_assoc();
$checkStmt->close();

$status = ($countRow['count'] > 0) ? 'ไม่พร้อม' : 'พร้อม';

// อัปเดตสถานะรถพยาบาล
$updateAmbu = $conn->prepare("UPDATE ambulance SET ambulance_status = ? WHERE ambulance_id = ?");
$updateAmbu->bind_param("si", $status, $ambulance_id);
if (!$updateAmbu->execute()) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Update ambulance status failed: ' . $conn->error]);
    exit;
}
$updateAmbu->close();

// ส่งกลับหลังจากอัปเดตครบ
http_response_code(200);
echo json_encode(['status' => 'success']);

$conn->close();
?>
