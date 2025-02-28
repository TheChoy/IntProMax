<?php
require('username.php');

// ดึงค่าจากฟอร์ม
$source = isset($_POST['source']) ? $_POST['source'] : [];
$level = isset($_POST['level']) ? $_POST['level'] : [];
$province = isset($_POST['province']) ? $_POST['province'] : '';
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
$age_min = isset($_POST['age_min']) ? $_POST['age_min'] : 1;
$age_max = isset($_POST['age_max']) ? $_POST['age_max'] : 120;
$region = isset($_POST['region']) ? $_POST['region'] : [];
$date_start = isset($_POST['date_start']) ? $_POST['date_start'] : '2025-01-01';
$date_end = isset($_POST['date_end']) ? $_POST['date_end'] : '2025-01-31';

//sql
// สร้าง SQL Query
$sql = "
SELECT 
    m.member_firstname, 
    m.member_birthdate,
    m.member_gender,
    merged.member_id,
    merged.ambulance_id,
    merged.booking_date,
    merged.province,
    merged.region,
    merged.source,
    IF(merged.source = 'emergency', 
        merged.emergency_case_patient_gender,  -- ดึงเพศจาก emergency_case_report_patient_gender
        m.member_gender) AS gender,  -- ดึงเพศจาก member
    IF(merged.source = 'emergency', 
        merged.emergency_case_patient_age,  -- ดึงอายุจาก emergency_case_patient_age
        TIMESTAMPDIFF(YEAR, m.member_birthdate, CURDATE())) AS age,
    a.ambulance_level -- เพิ่มข้อมูลจากตาราง ambulance
FROM (
    SELECT ab.member_id, ab.ambulance_id, ab.ambulance_booking_date AS booking_date, 
           ab.ambulance_booking_province AS province, ab.ambulance_booking_region AS region, 
           'ambulance' AS source, NULL AS emergency_case_patient_age, NULL AS emergency_case_patient_gender
    FROM ambulance_booking AS ab
    UNION
    SELECT eb.member_id, eb.ambulance_id, eb.event_booking_date AS booking_date, 
           eb.event_booking_province AS province, eb.event_booking_region AS region, 
           'event' AS source, NULL AS emergency_case_patient_age, NULL AS emergency_case_patient_gender
    FROM event_booking AS eb
    UNION
    SELECT ecr.order_emergency_case_id, ecr.ambulance_id, ecr.order_emergency_case_date AS booking_date, 
           'กรุงเทพมหานคร' AS province, 'ภาคกลาง' AS region,
           'emergency' AS source, ecr.order_emergency_case_patient_age, ecr.order_emergency_case_patient_gender
    FROM order_emergency_case AS ecr
) AS merged
LEFT JOIN member AS m ON merged.member_id = m.member_id
LEFT JOIN ambulance AS a ON merged.ambulance_id = a.ambulance_id -- เชื่อมกับตาราง ambulance
WHERE 1=1
";

// กรองประเภทงาน
if (!empty($source)) {
    $source_list = implode("','", $source);
    $sql .= " AND merged.source IN ('$source_list')";
}

// กรองระดับรถ
if (!empty($level)) {
    $level_list = implode("','", $level);
    $sql .= " AND a.ambulance_level IN ('$level_list')";
}

// กรองจังหวัด
if ($province !== 'ทั้งหมด' && !empty($province)) {
    $sql .= " AND merged.province = '$province'";
}

// กรองเพศ
if ($gender !== 'ทั้งหมด' && !empty($gender)) {
    $sql .= " AND IF(merged.source = 'emergency', 
                merged.emergency_case_patient_gender, 
                m.member_gender) = '$gender'";
}

// กรองอายุ
$sql .= " AND IF(merged.source = 'emergency', 
            merged.emergency_case_patient_age,  
            TIMESTAMPDIFF(YEAR, m.member_birthdate, CURDATE())) 
          BETWEEN $age_min AND $age_max";

// กรองภูมิภาค
if (!empty($region)) {
    $region_list = implode("','", $region);
    $sql .= " AND merged.region IN ('$region_list')";
}

// กรองวันที่การจอง
$sql .= " AND merged.booking_date BETWEEN '$date_start-01' AND '$date_end-31'";

$result = $conn->query($sql);

// เตรียมข้อมูลจากฐานข้อมูล
$chartData = [];

// ดึงข้อมูลจากฐานข้อมูล
// ดึงข้อมูลจากฐานข้อมูล
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // กำหนด source เป็นข้อความที่คุณต้องการ
        $source = $row['source']; // ประเภทการจอง
        if ($source == 'emergency') {
            $source = 'รับเคสฉุกเฉิน'; // แสดงข้อความสำหรับ emergency
        } elseif ($source == 'ambulance') {
            $source = 'รับส่งผู้ป่วย'; // แสดงข้อความสำหรับ ambulance
        } elseif ($source == 'event') {
            $source = 'รับงาน EVENT'; // แสดงข้อความสำหรับ event
        }

        $level = $row['ambulance_level'];   // ระดับรถ (1,2,3)

        // ตรวจสอบว่าหมวดหมู่หลัก (source) ถูกกำหนดหรือยัง
        if (!isset($chartData[$source])) {
            $chartData[$source] = ['1' => 0, '2' => 0, '3' => 0]; // ตั้งค่าเริ่มต้นที่ 0
        }

        // เพิ่มค่าจำนวนตามระดับรถ
        $chartData[$source][$level]++;
    }
}

// สร้าง Labels และ Values สำหรับกราฟ
$chartLabels = array_keys($chartData); // Labels: ["เคสฉุกเฉิน", "รับส่งผู้ป่วย", "รับงาน EVENT"]

// แยกข้อมูลแต่ละ level ออกมาให้เป็น dataset
$chartLevels = ['1' => [], '2' => [], '3' => []];

foreach ($chartLabels as $source) {
    foreach ($chartLevels as $level => &$values) {
        $values[] = $chartData[$source][$level];
    }
}

// ส่งออกข้อมูลเป็น JSON สำหรับ JavaScript
$chartDataJson = json_encode([ 
    'labels' => $chartLabels,
    'datasets' => [
        ['label' => 'ระดับ 1', 'data' => $chartLevels['1'], 'backgroundColor' => 'rgba(255, 99, 132, 0.6)'],
        ['label' => 'ระดับ 2', 'data' => $chartLevels['2'], 'backgroundColor' => 'rgba(54, 162, 235, 0.6)'],
        ['label' => 'ระดับ 3', 'data' => $chartLevels['3'], 'backgroundColor' => 'rgba(75, 192, 192, 0.6)']
    ]
]);

$conn->close();

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
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
    <title>สถิติการใช้งานรถ</title>
</head>

<body>
    <header class="header">
        <div class="logo-section">
            <img src="img/logo.jpg" alt="" class="logo">
            <h1 href="ceo_home_page.html" style="font-family: Itim;">CEO - HOME</h1>
        </div>
        <nav class="nav" style="margin-left: 20%;">
            <a href="approve_page.html" class="nav-item">อนุมัติคำสั่งซื้อ/เช่า</a>
            <a href="approve_clam_page.html" class="nav-item">อนุมัติเคลม</a>
            <a href="summary_page.html" class="nav-item">สรุปยอดขาย</a>
            <a href="case_report_page.html" class="nav-item">ดูสรุปรายงานเคส</a>
            <a href="history_fixed_page.html" class="nav-item">ประวัติการส่งซ่อมรถและอุปกรณ์การแพทย์</a>
            <a href="static_car_page.html" class="nav-item active">สถิติการใช้งานรถ</a>
        </nav>
    </header>
    <h1 class="header-static-car-page">ดูสถิติการใช้งานรถ</h1>
    <br>

    <main class="main-content">
        <div class="search-section">
            <div class="filter-icon">
                <i class="fa-solid fa-filter"></i> <!-- ไอคอน Filter -->
            </div>

            <div class="filter-sidebar" id="filterSidebar">
                <div class="sidebar-header">
                    <h2>ตัวกรอง</h2>
                    <button class="close-sidebar">&times;</button>
                </div>
                <form action="" method="POST">
                    <div class="sidebar-content">
                        <label for="">เลือกประเภทงาน:</label>
                        <input type="checkbox" name="source[]" value="emergency" checked> รับเคสฉุกเฉิน
                        <br>
                        <input type="checkbox" name="source[]" value="ambulance" checked> รับส่งผู้ป่วย
                        <br>
                        <input type="checkbox" name="source[]" value="event" checked> รับงาน EVENT
                        <br>

                        <label for="">เลือกระดับรถ:</label>
                        <input type="checkbox" name="level[]" value="1" checked> ระดับ 1
                        <br>
                        <input type="checkbox" name="level[]" value="2" checked> ระดับ 2
                        <br>
                        <input type="checkbox" name="level[]" value="3" checked> ระดับ 3
                        <br>

                        <label for="filter-price">จังหวัด:</label>
                        <select id="filter-price-list" name="province" class="filter-select">
                            <option value="" selected hidden>กรุณาเลือกจังหวัด</option>
                            <option value="ทั้งหมด" selected>ทั้งหมด</option>
                            <option value="กรุงเทพมหานคร">กรุงเทพมหานคร</option>
                            <option value="กระบี่">กระบี่</option>
                            <option value="กาญจนบุรี">กาญจนบุรี</option>
                            <option value="กาฬสินธุ์">กาฬสินธุ์</option>
                            <option value="กำแพงเพชร">กำแพงเพชร</option>
                            <option value="ขอนแก่น">ขอนแก่น</option>
                            <option value="จันทบุรี">จันทบุรี</option>
                            <option value="ฉะเชิงเทรา">ฉะเชิงเทรา</option>
                            <option value="ชลบุรี">ชลบุรี</option>
                            <option value="ชัยนาท">ชัยนาท</option>
                            <option value="ชัยภูมิ">ชัยภูมิ</option>
                            <option value="ชุมพร">ชุมพร</option>
                            <option value="เชียงราย">เชียงราย</option>
                            <option value="เชียงใหม่">เชียงใหม่</option>
                            <option value="ตรัง">ตรัง</option>
                            <option value="ตราด">ตราด</option>
                            <option value="ตาก">ตาก</option>
                            <option value="นครนายก">นครนายก</option>
                            <option value="นครปฐม">นครปฐม</option>
                            <option value="นครพนม">นครพนม</option>
                            <option value="นครราชสีมา">นครราชสีมา</option>
                            <option value="นครศรีธรรมราช">นครศรีธรรมราช</option>
                            <option value="นครสวรรค์">นครสวรรค์</option>
                            <option value="นนทบุรี">นนทบุรี</option>
                            <option value="นราธิวาส">นราธิวาส</option>
                            <option value="น่าน">น่าน</option>
                            <option value="บึงกาฬ">บึงกาฬ</option>
                            <option value="บุรีรัมย์">บุรีรัมย์</option>
                            <option value="ปทุมธานี">ปทุมธานี</option>
                            <option value="ประจวบคีรีขันธ์">ประจวบคีรีขันธ์</option>
                            <option value="ปราจีนบุรี">ปราจีนบุรี</option>
                            <option value="ปัตตานี">ปัตตานี</option>
                            <option value="พะเยา">พะเยา</option>
                            <option value="พระนครศรีอยุธยา">พระนครศรีอยุธยา</option>
                            <option value="พังงา">พังงา</option>
                            <option value="พัทลุง">พัทลุง</option>
                            <option value="พิจิตร">พิจิตร</option>
                            <option value="พิษณุโลก">พิษณุโลก</option>
                            <option value="เพชรบุรี">เพชรบุรี</option>
                            <option value="เพชรบูรณ์">เพชรบูรณ์</option>
                            <option value="แพร่">แพร่</option>
                            <option value="ภูเก็ต">ภูเก็ต</option>
                            <option value="มหาสารคาม">มหาสารคาม</option>
                            <option value="มุกดาหาร">มุกดาหาร</option>
                            <option value="แม่ฮ่องสอน">แม่ฮ่องสอน</option>
                            <option value="ยโสธร">ยโสธร</option>
                            <option value="ยะลา">ยะลา</option>
                            <option value="ร้อยเอ็ด">ร้อยเอ็ด</option>
                            <option value="ระนอง">ระนอง</option>
                            <option value="ระยอง">ระยอง</option>
                            <option value="ราชบุรี">ราชบุรี</option>
                            <option value="ลพบุรี">ลพบุรี</option>
                            <option value="ลำปาง">ลำปาง</option>
                            <option value="ลำพูน">ลำพูน</option>
                            <option value="เลย">เลย</option>
                            <option value="ศรีสะเกษ">ศรีสะเกษ</option>
                            <option value="สกลนคร">สกลนคร</option>
                            <option value="สงขลา">สงขลา</option>
                            <option value="สตูล">สตูล</option>
                            <option value="สมุทรปราการ">สมุทรปราการ</option>
                            <option value="สมุทรสงคราม">สมุทรสงคราม</option>
                            <option value="สมุทรสาคร">สมุทรสาคร</option>
                            <option value="สระแก้ว">สระแก้ว</option>
                            <option value="สระบุรี">สระบุรี</option>
                            <option value="สิงห์บุรี">สิงห์บุรี</option>
                            <option value="สุโขทัย">สุโขทัย</option>
                            <option value="สุพรรณบุรี">สุพรรณบุรี</option>
                            <option value="สุราษฎร์ธานี">สุราษฎร์ธานี</option>
                            <option value="สุรินทร์">สุรินทร์</option>
                            <option value="หนองคาย">หนองคาย</option>
                            <option value="หนองบัวลำภู">หนองบัวลำภู</option>
                            <option value="อ่างทอง">อ่างทอง</option>
                            <option value="อุดรธานี">อุดรธานี</option>
                            <option value="อุตรดิตถ์">อุตรดิตถ์</option>
                            <option value="อุทัยธานี">อุทัยธานี</option>
                            <option value="อุบลราชธานี">อุบลราชธานี</option>
                            <option value="อำนาจเจริญ">อำนาจเจริญ</option>
                        </select>

                        <label for="gender">เพศ:</label>
                        <select name="gender" class="filter-select">
                            <option value="" selected hidden>กรุณาเลือกเพศ</option>
                            <option value="ทั้งหมด">ทั้งหมด</option>
                            <option value="ชาย">ชาย</option>
                            <option value="หญิง">หญิง</option>
                        </select>
                        <br>

                        <label for="">อายุ:</label>
                        <input class="input-age" name="age_min" value="1" min="1" max="120" type="number"> ถึง
                        <input class="input-age" name="age_max" value="120" min="1" max="120" type="number"> ปี
                        <br>

                        <label for="">เลือกภูมิภาค:</label>
                        <input type="checkbox" name="region[]" value="ภาคเหนือ" checked> ภาคเหนือ
                        <br>
                        <input type="checkbox" name="region[]" value="ภาคตะวันออกเฉียงเหนือ" checked> ภาคตะวันออกเฉียงเหนือ
                        <br>
                        <input type="checkbox" name="region[]" value="ภาคกลาง" checked> ภาคกลาง
                        <br>
                        <input type="checkbox" name="region[]" value="ภาคใต้" checked> ภาคใต้
                        <br>

                        <label for="">ปี/เดือน:</label>
                        <input class="month-selected" name="date_start" id="calendarSelect" type="text" placeholder="ปี/เดือน" value="2025-01"> ถึง
                        <input class="month-selected" name="date_end" id="calendarSelect" type="text" placeholder="ปี/เดือน" value="2025-01">

                        <button type="submit">กรองข้อมูล</button>
                    </div>
                </form>
    </main>

    <canvas id="bookingChart" width="500" height="100"></canvas>


</body>

<script>
    // สคริปต์สำหรับเปิด-ปิด Sidebar
    document.addEventListener("DOMContentLoaded", () => {
        const filterIcon = document.querySelector(".filter-icon");
        const sidebar = document.getElementById("filterSidebar");
        const closeSidebar = document.querySelector(".close-sidebar");

        // เปิด Sidebar
        filterIcon.addEventListener("click", () => {
            sidebar.classList.add("open");
        });

        // ปิด Sidebar
        closeSidebar.addEventListener("click", () => {
            sidebar.classList.remove("open");
        });

        // ปิด Sidebar เมื่อคลิกนอก Sidebar
        document.addEventListener("click", (e) => {
            if (!sidebar.contains(e.target) && !filterIcon.contains(e.target)) {
                sidebar.classList.remove("open");
            }
        });

    });
    // ตั้งค่าปฏิทิน Flatpickr
    flatpickr("#calendarSelect", {
        dateFormat: "Y-m", // รูปแบบวันที่เป็น YYYY-MM
        plugins: [
            new monthSelectPlugin({
                shorthand: true, // ใช้ชื่อย่อของเดือน
                dateFormat: "Y-m", // รูปแบบวันที่
                altFormat: "F Y" // รูปแบบการแสดงผลเป็น Full Month และ Year
            })
        ],
        onChange: function(selectedDates, dateStr, instance) {
            updateChart(dateStr);
        }
    });

    //เงื่อนไขให้ถ้าเลือกจังหวัดแล้วจะเลือกภูมิภาคไม่ได้ แต่ถ้าเลือกภูมิภาคจะเลือกจังหวัดไม่ได้
    document.addEventListener("DOMContentLoaded", function() {
        const provinceSelect = document.getElementById("filter-price-list");
        const regionCheckboxes = document.querySelectorAll("input[name='region[]']");

        function toggleSelection() {
            if (provinceSelect.value && provinceSelect.value !== "ทั้งหมด") {
                regionCheckboxes.forEach(checkbox => {
                    checkbox.disabled = true;
                });
            } else if ([...regionCheckboxes].some(checkbox => checkbox.checked)) {
                provinceSelect.disabled = true;
            } else {
                provinceSelect.disabled = false;
                regionCheckboxes.forEach(checkbox => {
                    checkbox.disabled = false;
                });
            }
        }

        provinceSelect.addEventListener("change", toggleSelection);
        regionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener("change", toggleSelection);
        });
    });

    
    document.addEventListener("DOMContentLoaded", function() {
    var chartData = <?php echo $chartDataJson; ?>;

    // Find the maximum data value from the datasets
    let maxDataValue = 0;
    chartData.datasets.forEach(function(dataset) {
        dataset.data.forEach(function(value) {
            if (value > maxDataValue) {
                maxDataValue = value;
            }
        });
    });

    // Set the Y-axis max value to be the max data value + 5
    var ctx = document.getElementById('bookingChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: chartData.datasets
        },
        options: {
            responsive: true,
            scales: {
                x: { stacked: false },
                y: {
                    stacked: false, 
                    beginAtZero: true,  // Ensure it starts at 0
                    min: 0,             // Set the minimum value to 0
                    max: maxDataValue + 5  // Set the maximum value to max data value + 5
                }
            }
        }
    });
});

</script>

</html>
