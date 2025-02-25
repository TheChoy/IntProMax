<?php
include 'con_repair.php';

// แก้ไข query ให้เพิ่มเงื่อนไข WHERE repair_status IN ('รอดำเนินการ', 'กำลังดำเนินการ') และ repair_reason ไม่เป็นค่าว่าง
$query_result = mysqli_query($conn, "SELECT * FROM repair WHERE repair_status IN ('รอดำเนินการ', 'กำลังดำเนินการ')");
$repair_data = mysqli_fetch_all($query_result, MYSQLI_ASSOC);

// คงไว้เหมือนเดิมเพื่อใช้ในการกรอง
$ambulance_query = mysqli_query($conn, "SELECT DISTINCT ambulance_id FROM repair ORDER BY ambulance_id");
$ambulance_data = mysqli_fetch_all($ambulance_query, MYSQLI_ASSOC);

$status_query = mysqli_query($conn, "SELECT DISTINCT repair_status FROM repair WHERE repair_status IN ('รอดำเนินการ', 'กำลังดำเนินการ')");
$status_data = mysqli_fetch_all($status_query, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_repair.css">
    <script src="script_repair.js?ts=<?php echo time(); ?>" defer></script>
    <title>การซ่อมอุปกรณ์และรถพยาบาล</title>
</head>

<body>
    <nav>
        <ul class="menu">
            <li><a href="..\\car_report\\car_report.php">รายงานสภาพรถพยาบาล</a></li>
            <li><a href="repair.php">การซ่อมอุปกรณ์และรถพยาบาล</a></li>
        </ul>
    </nav>
    <div class="header">
        <h1 class="title">การซ่อมอุปกรณ์และรถพยาบาล</h1>
    </div>
    <div class="table-container">
        <form action="repair.php" method="post">
            <div>
                <div class="filter-section">
                    <div>
                        <label for="filter-date">วันที่รับซ่อม:</label>
                        <input type="date" id="filter-date">

                        <label for="filter-ambulance-ID">ID รถพยาบาล:</label>
                        <select id="filter-ambulance-ID">
                            <option value="">-- ID รถพยาบาล --</option>
                            <?php foreach ($ambulance_data as $row) { ?>
                                <option value="<?php echo $row["ambulance_id"]; ?>">
                                    <?php echo $row["ambulance_id"]; ?>
                                </option>
                            <?php } ?>
                        </select>

                        <label for="filter-status">สถานะการซ่อม:</label>
                        <select id="filter-status">
                            <option value="">-- เลือกสถานะ --</option>
                            <?php foreach ($status_data as $row) { ?>
                                <option value="<?php echo $row["repair_status"]; ?>">
                                    <?php echo $row["repair_status"]; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div>
                        <div class="add-button">
                            <button type="button" onclick="addRepair()">เพิ่มการแจ้งซ่อม +</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div>
            <table id="my-list">
                <thead>
                    <tr>
                        <th>วันที่รับซ่อม</th>
                        <th>ID รถพยาบาล</th>
                        <th>ประเภทการซ่อม</th>
                        <th>อุปกรณ์/อะไหล่</th>
                        <th>สาเหตุ</th>
                        <th>วันที่เสร็จสิ้น</th>
                        <th>ID ผู้รายงาน</th>
                        <th>สถานะการซ่อม</th>
                    </tr>
                </thead>
                <tbody id="repair-table-body">
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
                                <select onchange="updateRepair(<?php echo $rs_result['repair_id']; ?>, this.value, 'status')">
                                    <option value="รอดำเนินการ" <?php echo ($rs_result['repair_status'] == 'รอดำเนินการ') ? 'selected' : ''; ?>>รอดำเนินการ</option>
                                    <option value="กำลังดำเนินการ" <?php echo ($rs_result['repair_status'] == 'กำลังดำเนินการ') ? 'selected' : ''; ?>>กำลังดำเนินการ</option>
                                    <option value="เสร็จสิ้น" <?php echo ($rs_result['repair_status'] == 'เสร็จสิ้น') ? 'selected' : ''; ?>>เสร็จสิ้น</option>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>