<?php
include 'username.php';

$query_all = mysqli_query(
    $conn,
    "SELECT * from repair 
        INNER JOIN ambulance on ambulance.ambulance_id = repair.ambulance_id
        INNER JOIN repair_staff on repair.repair_staff_id = repair_staff.repair_staff_id"
);
$all_data = mysqli_fetch_all($query_all, MYSQLI_ASSOC);

// ข้อมูลเฉพาะวันนี้
$query_today = mysqli_query(
    $conn,
    "SELECT * from repair 
        INNER JOIN ambulance on ambulance.ambulance_id = repair.ambulance_id
        INNER JOIN repair_staff on repair.repair_staff_id = repair_staff.repair_staff_id
    WHERE repair_date = CURRENT_DATE"
);
$today_data = mysqli_fetch_all($query_today, MYSQLI_ASSOC);


// ประเภท
$type_query = mysqli_query(
    $conn,
    "SELECT DISTINCT repair_type FROM repair"
);
$type_data = mysqli_fetch_all($type_query, MYSQLI_ASSOC);

// เหตุผล
$reason_query = mysqli_query(
    $conn,
    "SELECT DISTINCT repair_reason FROM repair"
);
$reason_data = mysqli_fetch_all($reason_query, MYSQLI_ASSOC);

// อะไหล่ที่ซ่อม
$repairing_query = mysqli_query(
    $conn,
    "SELECT DISTINCT repair_repairing FROM repair"
);
$repairing_data = mysqli_fetch_all($repairing_query, MYSQLI_ASSOC);

// สถานะ
$status_query = mysqli_query(
    $conn,
    "SELECT DISTINCT repair_status FROM repair"
);
$status_data = mysqli_fetch_all($status_query, MYSQLI_ASSOC);

//----------------------------
// Bar Chart แสดงจำนวนครั้งการซ่อมของรถแต่ละ level
// เลือก level รถพยาบาลใช้เป็น labels
$ambu_query = mysqli_query(
    $conn,
    "SELECT DISTINCT ambulance_level FROM ambulance ORDER BY ambulance_level"
);
$ambu_data = mysqli_fetch_all($ambu_query, MYSQLI_ASSOC);

// เตรียมอาร์เรย์นับจำนวน
$countAmLevel = array_fill_keys(array_column($ambu_data, 'ambulance_level'), 0);

// นับจำนวนครั้งที่แต่ละ ambulance_level ปรากฏใน $all_data
foreach ($today_data as $row) {
    if (isset($countAmLevel[$row['ambulance_level']])) {
        $countAmLevel[$row['ambulance_level']]++;
    }
}
//----------------------------

//----------------------------
// Pie Chart แสดงสัดส่วนประเภทการซ่อม
// ใช้ $type_data ที่เคย query ไปแล้วข้างบน

// เตรียมอาร์เรย์นับจำนวน
$countType = array_fill_keys(array_column($type_data, 'repair_type'), 0);
// นับจำนวนครั้งที่แต่ละ ambulance_id ปรากฏใน $all_data
foreach ($today_data as $row) {
    if (isset($countType[$row['repair_type']])) {
        $countType[$row['repair_type']]++;
    }
}
//----------------------------

//----------------------------
// นับจำนวนรถทั้งหมด
$count_all_ambu_query = mysqli_query(
    $conn,
    "SELECT COUNT(ambulance_id) as AllAmbu FROM ambulance"
);
$all_ambu_data = mysqli_fetch_all($count_all_ambu_query, MYSQLI_ASSOC);
// print_r($all_ambu_data);

// เก็บจำนวนรถทั้งหมดไว้ในตัวแปรชื่อว่า $all_ambu
$all_ambu = 0;
foreach ($all_ambu_data as $num) {
    foreach ($num as $key => $value) {
        $all_ambu = $value;
    }
}

// นับจำนวนรถที่พร้อม
$count_ready_ambu_query = mysqli_query(
    $conn,
    "SELECT COUNT(ambulance_id) as readyAmbu FROM ambulance WHERE ambulance_status='พร้อม'"
);
$ready_ambu_data = mysqli_fetch_all($count_ready_ambu_query, MYSQLI_ASSOC);
// print_r($all_ambu_data);

// เก็บจำนวนรถทั้งหมดไว้ในตัวแปรชื่อว่า $ready_ambu
$ready_ambu = 0;
foreach ($ready_ambu_data as $num) {
    foreach ($num as $key => $value) {
        $ready_ambu = $value;
    }
}

// นับจำนวนรถที่ไม่พร้อม
$count_notReady_ambu_query = mysqli_query(
    $conn,
    "SELECT COUNT(ambulance_id) as readyAmbu FROM ambulance WHERE ambulance_status='ไม่พร้อม'"
);
$notReady_ambu_data = mysqli_fetch_all($count_notReady_ambu_query, MYSQLI_ASSOC);
// print_r($all_ambu_data);

// เก็บจำนวนรถทั้งหมดไว้ในตัวแปรชื่อว่า $ready_ambu
$notReady_ambu = 0;
foreach ($notReady_ambu_data as $num) {
    foreach ($num as $key => $value) {
        $notReady_ambu = $value;
    }
}
//----------------------------
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?ts=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/history_fixed_page.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css"> -->
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="history_script.js?ts=<?php echo time(); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>ประวัติการส่งซ่อมรถและอุปกรณ์การแพทย์</title>
</head>

<body>
    <header class="header">
        <div class="logo-section">
            <img src="img/logo.jpg" alt="" class="logo">
            <h1 href="ceo_home_page.html" style="font-family: Itim;">CEO - HOME</h1>
        </div>
        <nav class="nav" style="margin-left: 20%;">
            <a href="approve_page.php" class="nav-item">อนุมัติคำสั่งซื้อ/เช่า</a>
            <a href="approve_clam_page.php" class="nav-item">อนุมัติเคลม</a>
            <a href="summary_page.php" class="nav-item">สรุปยอดขาย</a>
            <a href="case_report_page.php" class="nav-item">ดูสรุปรายงานเคส</a>
            <a href="history_fixed_page.php" class="nav-item active">ประวัติการส่งซ่อมรถและอุปกรณ์การแพทย์</a>
            <a href="static_car_page.php" class="nav-item">สถิติการใช้งานรถ</a>
        </nav>
    </header>
    <br>

    <div>
        <div class="myChart">
            <!-- canvas แสดงตารางจำนวนการซ่อมรถพยาบาล level 1-3 -->
            <div class="thisChart" id="chartLeft">
                <canvas id="ambulance_fixed"></canvas>
            </div>
            <!-- canvas แสดงตารางสัดส่วนของประเภทการซ่อม -->
            <div class="thisChart" id="chartRight">
                <canvas id="type_fixed"></canvas>
            </div>
            <!-- ตารางแสดงจำนวนรถพยาบาลทั้งหมด รถพยาบาลที่พร้อม รถพยาบาลที่ไม่พร้อม -->
            <table style="width: auto; height: 50px;">
                <thead>
                    <tr>
                        <th>รถพยาบาลทั้งหมด</th>
                        <th>พร้อม</th>
                        <th>ไม่พร้อม</th>
                    </tr>
                </thead>
                <tbody>
                        <td><?php echo $all_ambu?></td>
                        <td><?php echo $ready_ambu?></td>
                        <td style="color: white; background-color:red;"><?php echo $notReady_ambu?></td>
                </tbody>
            </table>
        </div>

    </div>

    <div class="search-section">
        <div class="filter-icon">
            <i class="fa-solid fa-filter"></i> <!-- ไอคอน Filter -->
        </div>

        <div class="filter-sidebar" id="filterSidebar">
            <div class="sidebar-header">
                <h2>ตัวกรอง</h2>
                <button class="close-sidebar">&times;</button>
            </div>
            <div class="sidebar-content">
                <label for="calendarSelect">เลือกวันที่:</label>
                <input class="calendar-selected" id="calendarSelect1" type="date" placeholder="เลือกวันที่"> ถึง
                <input class="calendar-selected" id="calendarSelect2" type="date" placeholder="เลือกวันที่">

                <label for="">ระดับรถ:</label>
                <div class="checkbox">
                    <input id="level_select1" type="checkbox" value="1" checked> Level 1
                    <input id="level_select2" type="checkbox" value="2" checked> Level 2
                    <input id="level_select3" type="checkbox" value="3" checked> Level 3
                </div> <br>

                <label for="">สาเหตุ:</label>
                <select id="reason_select" class="filter-select">
                    <option value="" selected>เลือกสาเหตุ</option>
                    <?php foreach ($reason_data as $row) { ?>
                        <option value="<?php echo $row["repair_reason"]; ?>">
                            <?php echo $row["repair_reason"]; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="">อะไหล่:</label>
                <select id="repairing_select" class="filter-select">
                    <option value="" selected>เลือกอะไหล่</option>
                    <?php foreach ($repairing_data as $row) { ?>
                        <option value="<?php echo $row["repair_repairing"]; ?>">
                            <?php echo $row["repair_repairing"]; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="">สถานะการซ่อม:</label>
                <select id="status_select" class="filter-select">
                    <option value="" selected>สถานะการซ่อม</option>
                    <?php foreach ($status_data as $row) { ?>
                        <option value="<?php echo $row["repair_status"]; ?>">
                            <?php echo $row["repair_status"]; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="">ค่าใช้จ่าย:</label>
                <select id="cost_select" class="filter-select">
                    <option value="" selected>ค่าใช้จ่าย</option>
                    <option value= " BETWEEN 1 AND 10000">ต่ำกว่า 10,000 บาท</option>
                    <option value= " BETWEEN 10000 AND 50000">10,000-50,000 บาท</option>
                    <option value= " > 50000">มากกว่า 50,000 บาท</option>
                </select>

            </div>
        </div>

    </div>



    <select class="select" id="select_type" name="option">
        <option value="" selected>เลือกประเภทของสิ่งที่ซ่อม</option>
        <?php foreach ($type_data as $row) { ?>
            <option value="<?php echo $row["repair_type"]; ?>">
                <?php echo $row["repair_type"]; ?>
            </option>
        <?php } ?>
    </select>



    <main id="main-content" class="main-content">
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
    </main>

    <script>

    //ตั้งตัวแปรไว้ให้ history_script.js ดึงไปใช้ต่อ
    const ambuData = {
        allAmbu: <?php echo $all_ambu; ?>,
        readyAmbu: <?php echo $ready_ambu; ?>,
        notReadyAmbu: <?php echo $notReady_ambu; ?>,
        ambuLabels: <?php echo json_encode(array_column($ambu_data, 'ambulance_level')); ?>,
        ambuCars: <?php echo json_encode($countAmLevel); ?>,
        typeLabels: <?php echo json_encode(array_column($type_data, 'repair_type')); ?>,
        typeCounts: <?php echo json_encode(array_map(fn($label) => $countType[$label] ?? 0, array_column($type_data, 'repair_type'))); ?>
    };
      
    </script>
</body>

</html>