<?php

header('Content-Type: application/text; charset=utf-8');

//ตรวจสอบว่า JSON ที่รับมาถูกต้องหรือไม่
function isValidJSON($str)
{
    json_decode($str);
    return json_last_error() == JSON_ERROR_NONE;
}

//รับ JSON จากการกรอก input ในหน้า repair.php
$json_params = file_get_contents("php://input");

?>

<?php

include 'username.php';

//แปลง JSON ที่รับมาให้กลายเป็น array
if (strlen($json_params) > 0 && isValidJSON($json_params))
$json_data = json_decode($json_params,true);

//สร้างไว้เก็บประโยค WHERE
//โดยมีเงื่อนไขคือ ถ้า input ไม่เป็นค่าว่าง ให้เพิ่มเงื่อนไขนั้น ๆ เข้าไปใน WHERE
//ถ้า input เป็นค่าว่าง ไม่ต้องทำอะไร
$whereClauses = array();
if (! empty($json_data['date1']) && ! empty($json_data['date2'])) {
    $whereClauses[0] = "repair_date BETWEEN '$json_data[date1]' AND '$json_data[date2]'";
}

$levelClauses = array();
$level = '';
if (! empty($json_data['level1']) || ! empty($json_data['level2']) || ! empty($json_data['level3'])) {
    if (! empty($json_data['level1'])) {
        $levelClauses[0] = "ambulance_level = '$json_data[level1]'";
    }
    if (! empty($json_data['level2'])) {
        $levelClauses[1] = "ambulance_level = '$json_data[level2]'";
    }
    if (! empty($json_data['level3'])) {
        $levelClauses[2] = "ambulance_level = '$json_data[level3]'";
    }
}
if (count($levelClauses) > 0) {
    $level = ' ( ' . implode(' OR ', $levelClauses) . ' ) ';
}

if (! empty($json_data['type'])) {
    $whereClauses[4] = "repair_type = '$json_data[type]'";
}
if (! empty($json_data['reason'])) {
    $whereClauses[5] = "repair_reason='$json_data[reason]'";
}
if (! empty($json_data['repairing'])) {
    $whereClauses[6] = "repair_repairing='$json_data[repairing]'";
}
if (! empty($json_data['status'])) {
    $whereClauses[7] = "repair_status='$json_data[status]'";
}
if (! empty($json_data['cost'])) {
    $whereClauses[8] = "( repair_cost $json_data[cost] )";
}

//สร้าง string เก็บประโยค WHERE ตัวเต็มที่ได้จากการรวม $whereClauses แล้วเชื่อมด้วย AND
$where = '';
if (count($whereClauses) > 0) {
    $where = ' WHERE ' . implode(' AND ', $whereClauses,) . ' AND ' . $level;
} else {
    $where = ' WHERE' . $level;
}

// echo $where;

$query_all = mysqli_query($conn, 
        "SELECT * from repair 
        INNER JOIN ambulance on ambulance.ambulance_id = repair.ambulance_id
        INNER JOIN repair_staff on repair.repair_staff_id = repair_staff.repair_staff_id
        $where");
$all_data = mysqli_fetch_all($query_all, MYSQLI_ASSOC);

?>

<table>
    <thead>
        <tr>
            <th>ทะเบียนรถพยาบาล</th>
            <th>ระดับรถ</th>
            <th>บันทึกโดย</th>
            <th>วันที่บันทึก(ว-ด-ป)</th>
            <th>วันที่ซ่อมเสร็จ</th>
            <th>ประเภทการซ่อม</th>
            <th>สาเหตุ</th>
            <th>อุปกรณ์/อะไหล่</th>
            <th>สถานะการซ่อม</th>
            <th>ค่าใช้จ่าย(บาท)</th>
        </tr>
    </thead>
    <tbody>
            <!-- เมื่อเข้ามาครั้งแรก จะแสดงข้อมูลทั้งหมดจากตาราง repair -->
            <?php foreach ($all_data as $rs_result) { ?>
        <tr>
            <td><?php echo $rs_result['ambulance_plate']; ?></td>
            <td><?php echo $rs_result['ambulance_level']; ?></td>

            <td><?php echo $rs_result['repair_staff_firstname']; ?></td>
            <td><?php echo $rs_result['repair_date']; ?></td>
            <td><?php echo $rs_result['repair_success_datetime']; ?></td>

            <td><?php echo $rs_result['repair_type']; ?></td>
            <td><?php echo $rs_result['repair_reason']; ?></td>
            <td><?php echo $rs_result['repair_repairing']; ?></td>
            <td><?php echo $rs_result['repair_status']; ?></td>
            <td>
                <?php if ($rs_result['repair_cost'] == '0') { ?>
                    <?php echo "-" ?>
                <?php } else { ?>
                    <?php echo $rs_result['repair_cost'] ?>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>

    </tr>
</table>