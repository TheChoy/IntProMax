<?php
include 'username.php';

// กำหนดค่า default
if (empty($_GET)) {
    // ตั้งค่า default สำหรับระดับรถ
    $_GET['ambulance_level'] = ['1', '2', '3'];

    // ตั้งค่า default สำหรับภูมิภาค
    $_GET['region'] = ['ภาคเหนือ', 'ภาคกลาง', 'ภาคตะวันออกเฉียงเหนือ', 'ภาคใต้'];

    // ตั้งค่า default สำหรับวันที่
    $_GET['start_month'] = date('Y-m', strtotime('0 month'));
    $_GET['end_month'] = date('Y-m');
}

$selectedMonth = $_GET['selected_month'] ?? date('Y-m');

$booking_where_sql_ambulance = "1=1";
$booking_where_sql_event = "1=1";
$booking_where_sql_emergency = "1=1";

if (!empty($_GET['selected_month'])) {
    $month = mysqli_real_escape_string($conn, $_GET['selected_month']);
    $booking_where_sql_ambulance .= " AND ab.ambulance_booking_date LIKE '$month%'";
    $booking_where_sql_event .= " AND eb.event_booking_date LIKE '$month%'";
    $booking_where_sql_emergency .= " AND ecr.order_emergency_case_date LIKE '$month%'";
}

if (!empty($_GET['start_month']) && !empty($_GET['end_month'])) {
    $start_month = mysqli_real_escape_string($conn, $_GET['start_month']);
    $end_month = mysqli_real_escape_string($conn, $_GET['end_month']);

    $booking_where_sql_ambulance .= " AND ab.ambulance_booking_date BETWEEN '$start_month-01' AND '$end_month-31'";
    $booking_where_sql_event .= " AND eb.event_booking_date BETWEEN '$start_month-01' AND '$end_month-31'";
    $booking_where_sql_emergency .= " AND ecr.order_emergency_case_date BETWEEN '$start_month-01' AND '$end_month-31'";

    // สำหรับ order_equipment
    $where_clauses[] = "order_equipment_date BETWEEN '$start_month-01' AND '$end_month-31'";
}

if (!empty($_GET['province'])) {
    $province = mysqli_real_escape_string($conn, $_GET['province']);
    $booking_where_sql_ambulance .= " AND (ab.ambulance_booking_province = '$province')";
    $booking_where_sql_event .= " AND (eb.event_booking_province = '$province')";
    $booking_where_sql_emergency .= " AND ('กรุงเทพมหานคร' = '$province')";
}

if (!empty($_GET['region']) && is_array($_GET['region'])) {
    $regions = array_map(fn($r) => "'" . mysqli_real_escape_string($conn, $r) . "'", $_GET['region']);
    $in_region = implode(",", $regions);
    $booking_where_sql_ambulance .= " AND ab.ambulance_booking_region IN ($in_region)";
    $booking_where_sql_event .= " AND eb.event_booking_region IN ($in_region)";
    $booking_where_sql_emergency .= " AND 'ภาคกลาง' IN ($in_region)";
}

if (!empty($_GET['ambulance_level']) && is_array($_GET['ambulance_level'])) {
    $levels = array_map(fn($lvl) => "'" . mysqli_real_escape_string($conn, $lvl) . "'", $_GET['ambulance_level']);
    $booking_level_filter = "a.ambulance_level IN (" . implode(",", $levels) . ")";
} else {
    // เมื่อไม่มี checkbox ถูกเลือก
    $booking_level_filter = "1=0"; // เงื่อนไขที่ทำให้ไม่มีข้อมูล
}

if (!empty($_GET['gender'])) {
    $gender = mysqli_real_escape_string($conn, $_GET['gender']);
    $booking_where_sql_ambulance .= " AND (m.member_gender = '$gender')";
    $booking_where_sql_event .= " AND (m.member_gender = '$gender')";
    $booking_where_sql_emergency .= " AND (ecr.order_emergency_case_patient_gender = '$gender')";
}

$where_clauses = [];
if (!empty($_GET['selected_month'])) {
    $month = mysqli_real_escape_string($conn, $_GET['selected_month']);
    $where_clauses[] = "order_equipment_date LIKE '$month%'";
}
if (!empty($_GET['gender'])) {
    $gender = mysqli_real_escape_string($conn, $_GET['gender']);
    $where_clauses[] = "member_gender = '$gender'";
}
if (!empty($_GET['order_type'])) {
    $type = mysqli_real_escape_string($conn, $_GET['order_type']);
    $where_clauses[] = "order_equipment_type = '$type'";
}
if (!empty($_GET['province'])) {
    $province = mysqli_real_escape_string($conn, $_GET['province']);
    $where_clauses[] = "member_province = '$province'";
}
if (!empty($_GET['region']) && is_array($_GET['region'])) {
    $regions = array_map(function ($r) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, $r) . "'";
    }, $_GET['region']);
    $where_clauses[] = "member_region IN (" . implode(",", $regions) . ")";
}
$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

// แก้ไข SQL query หลัก
$sql = "
SELECT 
    merged.source AS source_type, 
    a.ambulance_level, 
    SUM(merged.reservation_price) AS total_sales
FROM (
    SELECT 
        ab.member_id, 
        ab.ambulance_id, 
        ab.ambulance_booking_date AS booking_date, 
        ab.ambulance_booking_province AS province, 
        ab.ambulance_booking_region AS region, 
        'ambulance' AS source,
        m.member_gender,
        ab.ambulance_booking_price AS reservation_price
    FROM ambulance_booking AS ab
    JOIN member m ON ab.member_id = m.member_id
    WHERE $booking_where_sql_ambulance

    UNION ALL

    SELECT 
        eb.member_id, 
        eb.ambulance_id, 
        eb.event_booking_date AS booking_date, 
        eb.event_booking_province AS province, 
        eb.event_booking_region AS region, 
        'event' AS source,
        m.member_gender,
        eb.event_booking_price AS reservation_price
    FROM event_booking AS eb
    JOIN member m ON eb.member_id = m.member_id
    WHERE $booking_where_sql_event

    UNION ALL

    SELECT 
        ecr.order_emergency_case_id,
        ecr.ambulance_id, 
        ecr.order_emergency_case_date AS booking_date,
        'กรุงเทพมหานคร' AS province,
        'ภาคกลาง' AS region,
        'emergency' AS source,
        ecr.order_emergency_case_patient_gender AS member_gender,
        ecr.order_emergency_case_price AS reservation_price
    FROM order_emergency_case AS ecr
    WHERE $booking_where_sql_emergency
) AS merged
LEFT JOIN ambulance AS a ON merged.ambulance_id = a.ambulance_id
WHERE $booking_level_filter
GROUP BY merged.source, a.ambulance_level
ORDER BY source_type, ambulance_level
";

$sqrt = "
SELECT
    COALESCE(SUM(CASE WHEN order_equipment_type = 'ซื้อ' THEN order_equipment_total ELSE 0 END), 0) AS total_purchase,
    COALESCE(SUM(CASE WHEN order_equipment_type = 'เช่า' THEN order_equipment_total ELSE 0 END), 0) AS total_rent,
    COUNT(*) as total_records
FROM order_equipment
LEFT JOIN equipment ON order_equipment.equipment_id = equipment.equipment_id
LEFT JOIN member ON order_equipment.member_id = member.member_id
$where_sql
";

$result_booking = mysqli_query($conn, $sql);
$result_equipment = mysqli_query($conn, $sqrt);

// Initialize variables สำหรับยอดขายแต่ละระดับ
$ambulance_sales_level1 = 0;
$ambulance_sales_level2 = 0;
$ambulance_sales_level3 = 0;
$event_sales_level1 = 0;
$event_sales_level2 = 0;
$event_sales_level3 = 0;
$emergency_sales_level1 = 0;
$emergency_sales_level2 = 0;
$emergency_sales_level3 = 0;

// คำนวณยอดขายแยกตามระดับ Level 1, 2, 3
while ($row = mysqli_fetch_assoc($result_booking)) {
    if ($row['source_type'] === 'ambulance') {
        if ($row['ambulance_level'] == 1) {
            $ambulance_sales_level1 += $row['total_sales'];
        } elseif ($row['ambulance_level'] == 2) {
            $ambulance_sales_level2 += $row['total_sales'];
        } elseif ($row['ambulance_level'] == 3) {
            $ambulance_sales_level3 += $row['total_sales'];
        }
    } elseif ($row['source_type'] === 'event') {
        if ($row['ambulance_level'] == 1) {
            $event_sales_level1 += $row['total_sales'];
        } elseif ($row['ambulance_level'] == 2) {
            $event_sales_level2 += $row['total_sales'];
        } elseif ($row['ambulance_level'] == 3) {
            $event_sales_level3 += $row['total_sales'];
        }
    } elseif ($row['source_type'] === 'emergency') {
        if ($row['ambulance_level'] == 1) {
            $emergency_sales_level1 += $row['total_sales'];
        } elseif ($row['ambulance_level'] == 2) {
            $emergency_sales_level2 += $row['total_sales'];
        } elseif ($row['ambulance_level'] == 3) {
            $emergency_sales_level3 += $row['total_sales'];
        }
    }
}

$ambulance_sales = 0;
$event_sales = 0;
$emergency_sales = 0;
if ($result_booking && mysqli_num_rows($result_booking) > 0) {
    while ($row = mysqli_fetch_assoc($result_booking)) {
        if ($row['source_type'] == 'ambulance') $ambulance_sales += $row['total_sales'];
        elseif ($row['source_type'] == 'event') $event_sales += $row['total_sales'];
        elseif ($row['source_type'] == 'emergency') $emergency_sales += $row['total_sales'];
    }
}

$purchase_sales = 0;
$rent_sales = 0;
$has_equipment_data = false;

if ($result_equipment && mysqli_num_rows($result_equipment) > 0) {
    $row = mysqli_fetch_assoc($result_equipment);
    if ($row['total_records'] > 0) {
        $purchase_sales = $row['total_purchase'];
        $rent_sales = $row['total_rent'];
        $has_equipment_data = true;
    }
}

if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');
    echo json_encode([
        // ยอดขายแยกตามประเภทงาน
        'ambulance_sales' => $ambulance_sales,
        'event_sales' => $event_sales,
        'emergency_sales' => $emergency_sales,

        // ยอดขายแยกตามระดับ Level 1, 2, 3 สำหรับ Ambulance, Event, Emergency
        'ambulance_sales_level1' => $ambulance_sales_level1,
        'ambulance_sales_level2' => $ambulance_sales_level2,
        'ambulance_sales_level3' => $ambulance_sales_level3,
        'event_sales_level1' => $event_sales_level1,
        'event_sales_level2' => $event_sales_level2,
        'event_sales_level3' => $event_sales_level3,
        'emergency_sales_level1' => $emergency_sales_level1,
        'emergency_sales_level2' => $emergency_sales_level2,
        'emergency_sales_level3' => $emergency_sales_level3,

        // ยอดขายอุปกรณ์
        'purchase_sales' => $purchase_sales,
        'rent_sales' => $rent_sales
    ]);
    exit;
}
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="summary_buy.css?v=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="summary_buy.js"></script>
    <title>สรุปยอดขาย</title>


</head>
<style>
    .chart {
        width: 900px;
        height: 100px;
        margin: auto;
        margin-top: 20px;
    }
</style>

<body>
    <header class="header">
        <div class="logo-section">
            <img src="img/logo.jpg" alt="" class="logo">
            <h1 href="ceo_home_page.html" style="font-family: Itim;">CEO - HOME</h1>
        </div>
        <nav class="nav" style="margin-left: 20%;">
            <a href="approve_page.php" class="nav-item">อนุมัติคำสั่งซื้อ/เช่า</a>
            <a href="approve_claim_page.php" class="nav-item">อนุมัติเคลม</a>
            <a href="summary_page.php" class="nav-item">สถิติคำสั่งซื้อ/เช่าสินค้า</a>
            <a href="case_report_page.php" class="nav-item">ดูสรุปรายงานเคส</a>
            <a href="history_fixed_page.php" class="nav-item">ประวัติการส่งซ่อมรถและอุปกรณ์การแพทย์</a>
            <a href="static_car_page.php" class="nav-item">สถิติการใช้งานรถ</a>
            <a href="summary_buy.php" class="nav-item active">สรุปยอดขาย</a>
        </nav>
    </header>
    <h1 class="header-summary-buy-page">สรุปยอดขาย</h1>
    <br>

    <main class="main-content">
        <div class="search-section">
            <div class="filter-icon">
                <i class="fa-solid fa-filter"></i> <!-- ไอคอน Filter -->
            </div>
        </div>
        <div class="filter-sidebar" id="filterSidebar">
            <div class="sidebar-header">
                <h2>ตัวกรอง</h2>
                <button type="button" class="close-sidebar">&times;</button>
            </div>

            <form method="GET" action="summary_buy.php" id="filterForm">
                <div class="date-range">
                    <div>
                        <label for="start_month">เริ่มต้น:</label>
                        <input type="text" id="start_month" class="month-selected" name="start_month"
                            placeholder="เลือกเดือน/ปี" value="<?= $_GET['start_month'] ?? '' ?>">
                    </div>
                    <div>
                        <label for="end_month">สิ้นสุด:</label>
                        <input type="text" id="end_month" class="month-selected" name="end_month"
                            placeholder="เลือกเดือน/ปี" value="<?= $_GET['end_month'] ?? '' ?>">
                    </div>
                </div>

                <br>
                <label>เพศ:</label>
                <select name="gender" class="filter-select">
                    <option value="">ทั้งหมด</option>
                    <option value="ชาย" <?= ($_GET['gender'] ?? '') == 'ชาย' ? 'selected' : '' ?>>ชาย</option>
                    <option value="หญิง" <?= ($_GET['gender'] ?? '') == 'หญิง' ? 'selected' : '' ?>>หญิง</option>
                </select>

                <label>ประเภทคำสั่งซื้อ:</label>
                <select name="order_type" class="filter-select">
                    <option value="">ทั้งหมด</option>
                    <option value="ซื้อ" <?= ($_GET['order_type'] ?? '') == 'ซื้อ' ? 'selected' : '' ?>>ซื้อ</option>
                    <option value="เช่า" <?= ($_GET['order_type'] ?? '') == 'เช่า' ? 'selected' : '' ?>>เช่า</option>
                </select>

                <label>เลือกระดับรถ:</label><br>
                <?php
                $selected_levels = $_GET['ambulance_level'] ?? [];
                foreach ([1, 2, 3] as $level) {
                    $checked = in_array($level, $selected_levels) ? 'checked' : '';
                    echo "<label><input type='checkbox' name='ambulance_level[]' value='$level' $checked checked> ระดับ $level</label><br>";
                }
                ?>

                <label>จังหวัด:</label>
                <select id="province_selected" name="province" class="filter-select">
                    <option value="">ทั้งหมด</option>
                    <?php
                    $provinces = [
                        'กรุงเทพมหานคร',
                        'กระบี่',
                        'กาญจนบุรี',
                        'กาฬสินธุ์',
                        'กำแพงเพชร',
                        'ขอนแก่น',
                        'จันทบุรี',
                        'ฉะเชิงเทรา',
                        'ชลบุรี',
                        'ชัยนาท',
                        'ชัยภูมิ',
                        'ชุมพร',
                        'เชียงใหม่',
                        'เชียงราย',
                        'ตรัง',
                        'ตราด',
                        'ตาก',
                        'นครนายก',
                        'นครปฐม',
                        'นครพนม',
                        'นครราชสีมา',
                        'นครศรีธรรมราช',
                        'นครสวรรค์',
                        'นนทบุรี',
                        'นราธิวาส',
                        'น่าน',
                        'บึงกาฬ',
                        'บุรีรัมย์',
                        'ปทุมธานี',
                        'ประจวบคีรีขันธ์',
                        'ปราจีนบุรี',
                        'ปัตตานี',
                        'พระนครศรีอยุธยา',
                        'พะเยา',
                        'พังงา',
                        'พัทลุง',
                        'พิจิตร',
                        'พิษณุโลก',
                        'เพชรบุรี',
                        'เพชรบูรณ์',
                        'แพร่',
                        'ภูเก็ต',
                        'มหาสารคาม',
                        'มุกดาหาร',
                        'แม่ฮ่องสอน',
                        'ยโสธร',
                        'ยะลา',
                        'ร้อยเอ็ด',
                        'ระนอง',
                        'ระยอง',
                        'ราชบุรี',
                        'ลพบุรี',
                        'ลำปาง',
                        'ลำพูน',
                        'เลย',
                        'ศรีสะเกษ',
                        'สกลนคร',
                        'สงขลา',
                        'สตูล',
                        'สมุทรปราการ',
                        'สมุทรสงคราม',
                        'สมุทรสาคร',
                        'สระแก้ว',
                        'สระบุรี',
                        'สิงห์บุรี',
                        'สุโขทัย',
                        'สุพรรณบุรี',
                        'สุราษฎร์ธานี',
                        'สุรินทร์',
                        'สตูล',
                        'หนองคาย',
                        'หนองบัวลำภู',
                        'อ่างทอง',
                        'อำนาจเจริญ',
                        'อุดรธานี',
                        'อุตรดิตถ์',
                        'อุทัยธานี',
                        'อุบลราชธานี'
                    ];
                    foreach ($provinces as $p) {
                        $selected = ($_GET['province'] ?? '') == $p ? 'selected' : '';
                        echo "<option value='$p' $selected>$p</option>";
                    }
                    ?>
                </select>

                <label>เลือกภูมิภาค:</label><br>
                <?php
                $selected_regions = $_GET['region'] ?? [];
                $regions = ['ภาคเหนือ', 'ภาคกลาง', 'ภาคตะวันออกเฉียงเหนือ', 'ภาคใต้'];
                foreach ($regions as $r) {
                    $checked = in_array($r, $selected_regions) ? 'checked' : '';
                    echo "<label><input type='checkbox' name='region[]' value='$r' $checked checked> $r</label><br>";
                }
                ?>
            </form>
            <a href="summary_buy.php" class="reset-button" style="margin-left: 12px; margin-top:10px;" id="reset-button">Reset</a>
        </div>
        </div>

        <canvas class="chart" id="salesChart"></canvas>

    </main>
    <script>
        const monthSelectConfig = {
            dateFormat: "Y-m",
            altInput: true,
            altFormat: "F Y",
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "Y-m",
                    altFormat: "F Y"
                })
            ],
            disableMobile: true
        };

        // กำหนดค่า default dates
        const today = new Date();

        // Initialize start_month flatpickr
        const startMonthPicker = flatpickr("#start_month", {
            ...monthSelectConfig,
            defaultDate: "<?= $_GET['start_month'] ?? date('Y-m') ?>",
            maxDate: today,
            onChange: function(selectedDates, dateStr) {
                if (selectedDates[0]) {
                    // Reset end_month configuration
                    endMonthPicker.destroy();
                    
                    // Reinitialize end_month with updated config
                    const endConfig = {
                        ...monthSelectConfig,
                        defaultDate: endMonthPicker.selectedDates[0] || selectedDates[0],
                        minDate: selectedDates[0],
                        maxDate: today,
                        onChange: function(selectedDates, dateStr) {
                            if (selectedDates[0]) {
                                const startDate = startMonthPicker.selectedDates[0];
                                if (startDate && selectedDates[0] < startDate) {
                                    this.setDate(startDate);
                                }
                            }
                        }
                    };
                    
                    endMonthPicker = flatpickr("#end_month", endConfig);
                }
            }
        });

        // Initialize end_month flatpickr
        let endMonthPicker = flatpickr("#end_month", {
            ...monthSelectConfig,
            defaultDate: "<?= $_GET['end_month'] ?? date('Y-m') ?>",
            minDate: startMonthPicker.selectedDates[0] || "<?= $_GET['start_month'] ?? date('Y-m') ?>",
            maxDate: today
        });

        //chart
        var ctx = document.getElementById('salesChart').getContext('2d');

        var salesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['รับส่งผู้ป่วย', 'รับงาน Event', 'รับเคสฉุกเฉิน', 'อุปกรณ์ทางการแพทย์'],
                datasets: [{
                        label: 'รถระดับ 1',
                        data: [
                            <?php echo $ambulance_sales_level1; ?>,
                            <?php echo $event_sales_level1; ?>,
                            <?php echo $emergency_sales_level1; ?>,
                            0 // Equipment ไม่มีการแยกระดับ
                        ],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'รถระดับ 2',
                        data: [
                            <?php echo $ambulance_sales_level2; ?>,
                            <?php echo $event_sales_level2; ?>,
                            <?php echo $emergency_sales_level2; ?>,
                            0
                        ],
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'รถระดับ 3',
                        data: [
                            <?php echo $ambulance_sales_level3; ?>,
                            <?php echo $event_sales_level3; ?>,
                            <?php echo $emergency_sales_level3; ?>,
                            0
                        ],
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'ซื้อ',
                        data: [0, 0, 0, <?php echo $has_equipment_data ? $purchase_sales : 'null'; ?>],
                        backgroundColor: 'rgba(248, 148, 248, 0.36)',
                        borderColor: 'rgb(255, 39, 219)',
                        borderWidth: 1
                    },
                    {
                        label: 'เช่า',
                        data: [0, 0, 0, <?php echo $has_equipment_data ? $rent_sales : 'null'; ?>],
                        backgroundColor: 'rgba(64, 201, 255, 0.34)',
                        borderColor: 'rgb(0, 204, 255)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: false,
                        beginAtZero: true
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.raw === null) {
                                    return context.dataset.label + ': ไม่มีข้อมูล';
                                }
                                return context.dataset.label + ': ' + context.raw;
                            }
                        }
                    }
                }
            }
        });

        function updateChart() {
            const form = document.getElementById("filterForm");
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();

            // Update the URL (optional)
            const urlParams = new URLSearchParams(formData);
            window.history.replaceState({}, '', `?${urlParams}`);

            fetch(`summary_buy.php?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    // อัปเดตข้อมูลในกราฟ
                    salesChart.data.datasets[0].data = [
                        data.ambulance_sales_level1,
                        data.event_sales_level1,
                        data.emergency_sales_level1,
                        0 // Equipment ไม่มีระดับ
                    ];
                    salesChart.data.datasets[1].data = [
                        data.ambulance_sales_level2,
                        data.event_sales_level2,
                        data.emergency_sales_level2,
                        0
                    ];
                    salesChart.data.datasets[2].data = [
                        data.ambulance_sales_level3,
                        data.event_sales_level3,
                        data.emergency_sales_level3,
                        0
                    ];
                    salesChart.data.datasets[3].data = [
                        0,
                        0,
                        0,
                        data.purchase_sales > 0 ? data.purchase_sales : null
                    ];
                    salesChart.data.datasets[4].data = [
                        0,
                        0,
                        0,
                        data.rent_sales > 0 ? data.rent_sales : null
                    ];

                    // อัปเดตกราฟ
                    salesChart.update();
                });
        }
    </script>
</body>

</html>