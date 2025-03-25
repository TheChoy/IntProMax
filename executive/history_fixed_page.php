<?php
include 'username.php';

$query_all = mysqli_query(
  $conn,
  "SELECT * , 
        SUM(CASE WHEN ambulance_level = '1' THEN 1 ELSE 0 END) AS ambulance_level1,
        SUM(CASE WHEN ambulance_level = '2' THEN 1 ELSE 0 END) AS ambulance_level2,
        SUM(CASE WHEN ambulance_level = '3' THEN 1 ELSE 0 END) AS ambulance_level3
        from repair 
        INNER JOIN ambulance on ambulance.ambulance_id = repair.ambulance_id
        INNER JOIN repair_staff on repair.repair_staff_id = repair_staff.repair_staff_id
        GROUP BY repair_type"
);

$all_data = mysqli_fetch_all($query_all, MYSQLI_ASSOC);

// ---------------------------------------------------------------------------------

// เตรียมข้อมูลสำหรับแสดงผลในกราฟ
$labels = [];
$level1Data = [];
$level2Data = [];
$level3Data = [];

foreach ($all_data as $row) {
  $labels[] = $row['repair_type'];
  $level1Data[] = $row['ambulance_level1'];
  $level2Data[] = $row['ambulance_level2'];
  $level3Data[] = $row['ambulance_level3'];
}

// ---------------------------------------------------------------------------------

// ข้อมูลที่ปรากฏในฟิลเตอร์

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

// ------------------------------

// ข้อมูลของวันนี้
// นับจำนวนรถทั้งหมด
$count_all_ambu_query = mysqli_query(
  $conn,
  "SELECT COUNT(ambulance_id) as AllAmbu FROM ambulance"
);
$all_ambu_data = mysqli_fetch_all($count_all_ambu_query, MYSQLI_ASSOC);

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

// เก็บจำนวนรถทั้งหมดไว้ในตัวแปรชื่อว่า $notReady_ambu
$notReady_ambu = 0;
foreach ($notReady_ambu_data as $num) {
    foreach ($num as $key => $value) {
        $notReady_ambu = $value;
    }
}

// ------------------------------

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css?ts=<?php echo time(); ?>">
  <link rel="stylesheet" href="css/history_fixed_page.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Itim&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="history_script copy.js?ts=<?php echo time(); ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <title>ประวัติการส่งซ่อมรถและอุปกรณ์การแพทย์</title>

  <style>
    canvas {
      width: 80% !important;
      height: 60% !important;
      max-width: 800px;
      max-height: 600px;
      margin: auto;
      display: block;
    }
  </style>
</head>

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
      <a href="history_fixed_page.php" class="nav-item active">ประวัติการส่งซ่อมรถและอุปกรณ์การแพทย์</a>
      <a href="static_car_page.php" class="nav-item">สถิติการใช้งานรถ</a>
      <a href="summary_buy.php" class="nav-item">สรุปยอดขาย</a>
    </nav>
  </header>

  <main class="main-content">
    <h1 style="text-align: center;">ประวัติการซ่อมรถพยาบาลและอุปกรณ์ทางการแพทย์</h1>
    <div class="search-section">
      <div class="filter-icon">
        <i class="fa-solid fa-filter"></i>
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

          <label for="">ประเภท:</label>
          <select class="filter-select" id="select_type" name="option">
            <option value="" selected>เลือกประเภทของสิ่งที่ซ่อม</option>
            <?php foreach ($type_data as $row) { ?>
              <option value="<?php echo $row["repair_type"]; ?>">
                <?php echo $row["repair_type"]; ?>
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

          <label for="">สาเหตุ:</label>
          <select id="reason_select" class="filter-select">
            <option value="" selected>เลือกสาเหตุ</option>
            <?php foreach ($reason_data as $row) { ?>
              <option value="<?php echo $row["repair_reason"]; ?>">
                <?php echo $row["repair_reason"]; ?>
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
            <option value=" BETWEEN 1 AND 10000">ต่ำกว่า 10,000 บาท</option>
            <option value=" BETWEEN 10000 AND 50000">10,000-50,000 บาท</option>
            <option value=" > 50000">มากกว่า 50,000 บาท</option>
          </select>
        </div>
      </div>
  </main>

  <div id="contentDiv">
    <canvas id="summary"></canvas>
  </div>

  <script>
    function JSONparse(graphData) {
      let obj = JSON.parse(graphData);
      console.log(obj.labels);
      AmbuChart.data.labels = obj.labels;
      AmbuChart.data.datasets[0].data = obj.level1Data;
      AmbuChart.data.datasets[1].data = obj.level2Data;
      AmbuChart.data.datasets[2].data = obj.level3Data;
      console.log("datasets[0] : ", AmbuChart.data.datasets[0].data);
      console.log("datasets[1] : ", AmbuChart.data.datasets[1].data);
      console.log("datasets[2] : ", AmbuChart.data.datasets[2].data);
      AmbuChart.update();
    }

    var ambuData = {
      allAmbu: <?php echo $all_ambu; ?>,
      readyAmbu: <?php echo $ready_ambu; ?>,
      notReadyAmbu: <?php echo $notReady_ambu; ?>,
      labels: <?php echo json_encode($labels); ?>,
      level1Data: <?php echo json_encode($level1Data); ?>,
      level2Data: <?php echo json_encode($level2Data); ?>,
      level3Data: <?php echo json_encode($level3Data); ?>
    };


    Chart.defaults.elements.bar.borderRadius = 5;
    // Bar Chart แสดงจำนวนครั้งการซ่อมของรถแต่ละคัน
    var AmbuChart = new Chart(document.getElementById("summary"), {
      type: 'bar',
      data: {
        labels: ambuData.labels,
        datasets: [{
            label: 'ระดับ 1',
            data: ambuData.level1Data,
            backgroundColor: 'rgba(131, 255, 141, 0.5)',
            borderColor: 'rgba(68, 206, 79, 0.5)',
            borderWidth: 2
          }, {
            label: 'ระดับ 2',
            data: ambuData.level2Data,
            backgroundColor: 'rgba(99, 213, 255, 0.5)',
            borderColor: 'rgba(64, 158, 193, 0.5)',
            borderWidth: 2
          },
          {
            label: 'ระดับ 3',
            data: ambuData.level3Data,
            backgroundColor: 'rgba(255, 99, 132, 0.5)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2
          }
        ]
      },
      options: {
        responsive: true,
        scales: {
          x: {
            stacked: true,
            title: {
              display: true,
              text: 'ประเภทการซ่อม'
            }

          },
          y: {
            stacked: true,
            title: {
              display: true,
              text: 'จำนวนครั้งที่ซ่อม'
            },
            ticks: {
              beginAtZero: true,
              stepSize: 1
            }
          }
        },
        plugins: {
          title: {
            display: true,
            text: 'สรุปการซ่อมรถพยาบาลและอุปกรณ์ทางการแพทย์',
            font: {
              size: 18
            }
          },
          legend: {
            display: true,
            position: 'bottom',
            labels: {
              boxWidth: 20,
              padding: 15
            }
          }
        }
      }
    });
  </script>

</body>

</html>