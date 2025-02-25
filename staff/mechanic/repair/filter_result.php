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

include 'con_repair.php';

//แปลง JSON ที่รับมาให้กลายเป็น array
if (strlen($json_params) > 0 && isValidJSON($json_params))
    $json_data = json_decode($json_params, true);

//สร้างไว้เก็บประโยค WHERE
//โดยมีเงื่อนไขคือ ถ้า input ไม่เป็นค่าว่าง ให้เพิ่มเงื่อนไขนั้น ๆ เข้าไปใน WHERE
//ถ้า input เป็นค่าว่าง ไม่ต้องทำอะไร
$whereClauses = array();
if (! empty($json_data['date'])) {

    $whereClauses[0] = "repair_date = '$json_data[date]'";
}
if (! empty($json_data['ambuID'])) {

    $whereClauses[1] = "ambulance_id='$json_data[ambuID]'";
}
if (! empty($json_data['status'])) {

    $whereClauses[2] = "repair_status='$json_data[status]'";
}

$whereClauses[] = "repair_reason IS NOT NULL AND repair_reason <> ''";
$whereClauses[] = "repair_status IN ('รอดำเนินการ', 'กำลังดำเนินการ')";

//สร้าง string เก็บประโยค WHERE ตัวเต็มที่ได้จากการรวม $whereClauses แล้วเชื่อมด้วย AND
$where = '';
if (count($whereClauses) > 0) {
    $where = ' WHERE ' . implode(' AND ', $whereClauses);
}

//ดึงข้อมูลจากฐานข้อมูล โดยเงื่อนไข WHERE เรียกมาจาก $where
$query_result = mysqli_query($conn, "SELECT * FROM repair $where");
//เก็บข้อมูลทั้งหมดที่ได้จากการ query ไว้ในตัวแปร $repair_data
$repair_data = mysqli_fetch_all($query_result, MYSQLI_ASSOC);

?>


<table>
    <thead>
        <tr>
            <th>วันที่รับซ่อม</th>
            <th>ทะเบียนรถ</th>
            <th>ประเภทการซ่อม</th>
            <th>อุปกรณ์/อะไหล่</th>
            <th>สาเหตุ</th>
            <th>วันที่เสร็จสิ้น</th>
            <th>ผู้รายงาน</th>
            <th>สถานะการซ่อม</th>
        </tr>
    </thead>
    <tbody id="repair-table-body">
        <!-- ดึงข้อมูลจากตาราง repair มาแสดงผล -->
        <?php foreach ($repair_data as $rs_result) { ?>
            <tr>
                <td><?php echo $rs_result['repair_date']; ?></td>
                <td><?php echo $rs_result['ambulance_id']; ?></td>
                <td><?php echo $rs_result['repair_type']; ?></td>
                <td><?php echo $rs_result['repairing']; ?></td>
                <td><?php echo $rs_result['repair_reason']; ?></td>
                <td>
                    <?php if ($rs_result['repair_success_datetime'] !== null) { ?>
                        <?php echo $rs_result['repair_success_datetime']; ?>
                    <?php } else { ?>
                        <input type="datetime-local"
                            value="<?php echo $rs_result['repair_success_datetime']; ?>"
                            onchange="updateRepair(<?php echo $rs_result['repair_id']; ?>, this.value, 'date')">
                    <?php } ?>
                </td>
                <td><?php echo $rs_result['repair_staff_id']; ?></td>
                <td>
                    <!-- เมื่อถูกอัพเดตว่า "เสร็จสิ้น" จะ diable select -->
                    <!-- เมื่อถูกอัพเดตว่า "กำลังดำเนินการ" จะ diable "กำลังดำเนินการ" -->
                    <!-- เมื่อถูกอัพเดตว่า "รอดำเนินการ" จะแสดงตัวเลือกทุกอัน -->
                    <?php if ($rs_result['repair_status'] == 'เสร็จสิ้น') { ?>
                        <select disabled="updateRepair(<?php echo $rs_result['repair_id']; ?>, this.value, 'status')">
                            <option value="เสร็จสิ้น" <?php echo ($rs_result['repair_status'] == 'เสร็จสิ้น') ? 'selected' : ''; ?>>เสร็จสิ้น</option>
                        </select>
                    <?php } elseif ($rs_result['repair_status'] == 'กำลังดำเนินการ') { ?>
                        <select onchange="updateRepair(<?php echo $rs_result['repair_id']; ?>, this.value, 'status')">
                            <option disabled value="กำลังดำเนินการ" <?php echo ($rs_result['repair_status'] == 'กำลังดำเนินการ') ? 'selected' : ''; ?>>กำลังดำเนินการ</option>
                            <option value="เสร็จสิ้น" <?php echo ($rs_result['repair_status'] == 'เสร็จสิ้น') ? 'selected' : ''; ?>>เสร็จสิ้น</option>
                        </select>
                    <?php } else { ?>
                        <select onchange="updateRepair(<?php echo $rs_result['repair_id']; ?>, this.value, 'status')">
                            <option value="รอดำเนินการ" <?php echo ($rs_result['repair_status'] == 'รอดำเนินการ') ? 'selected' : ''; ?>>รอดำเนินการ</option>
                            <option value="กำลังดำเนินการ" <?php echo ($rs_result['repair_status'] == 'กำลังดำเนินการ') ? 'selected' : ''; ?>>กำลังดำเนินการ</option>
                            <option value="เสร็จสิ้น" <?php echo ($rs_result['repair_status'] == 'เสร็จสิ้น') ? 'selected' : ''; ?>>เสร็จสิ้น</option>
                        </select>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>