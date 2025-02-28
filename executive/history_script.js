document.addEventListener('DOMContentLoaded', () => {

  // ดึงตัวรับ input จากหน้าเว็บด้วย id เพื่อใส่ eventListener (filterTable)
  // ประเภท
  const filterType = document.getElementById("select_type");
  filterType.addEventListener('input', filterTable);

  // วันที่
  const filterDate1 = document.getElementById("calendarSelect1");
  filterDate1.addEventListener('input', filterTable);
  const filterDate2 = document.getElementById("calendarSelect2");
  filterDate2.addEventListener('input', filterTable);

  // ระดับรถ
  const filterLevel1 = document.getElementById("level_select1");
  filterLevel1.addEventListener('input', filterTable);
  const filterLevel2 = document.getElementById("level_select2");
  filterLevel2.addEventListener('input', filterTable);
  const filterLevel3 = document.getElementById("level_select3");
  filterLevel3.addEventListener('input', filterTable);

  //เหตุผล
  const filterReason = document.getElementById("reason_select");
  filterReason.addEventListener('input', filterTable);

  //อะไหล่
  const filterRepairing = document.getElementById("repairing_select");
  filterRepairing.addEventListener('input', filterTable);

  //สถานะ
  const filterStatus = document.getElementById("status_select");
  filterStatus.addEventListener('input', filterTable);

  // ค่าใช้จ่าย
  const filterCost = document.getElementById("cost_select");
  filterCost.addEventListener('input', filterTable);

  //-----------------------------------------------------------------

  // สคริปต์สำหรับเปิด-ปิด Sidebar

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

  //-----------------------------------------------------------------

  // รับค่าจาก PHP จากตัวแปรที่เราตั้งไว้ใน <script> ขอ history_fixed_page.php
  const { allAmbu, readyAmbu, notReadyAmbu, ambuLabels, ambuCars, typeLabels, typeCounts } = ambuData;

  // คำนวณ % รถที่ไม่พร้อมต่อจำนวนรถทั้งหมด
  let percent = (notReadyAmbu / allAmbu) * 100;

  // ถ้า % รถที่ไม่พร้อมมากกว่า 65 ให้ขึ้นแจ้งเตือน
  if (percent > 65) {
    alert("รถพยาบาลไม่พร้อมใช้งานมากกว่า 65%");
  }
  // console.log(percent);

  console.log("AmbuData:", ambuData);


  //----------------------------
  // Bar Chart แสดงจำนวนครั้งการซ่อมของรถแต่ละคัน
  const AmbuChart = new Chart(document.getElementById("ambulance_fixed"), {
    type: 'bar',
    data: {
      labels: ambuLabels,
      datasets: [{
        label: 'จำนวนครั้งที่ซ่อม',
        data: ambuCars,
        backgroundColor: 'rgba(54, 162, 235, 0.5)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      plugins: {
        title: {
          display: true,
          text: 'จำนวนการซ่อมรถพยาบาล level 1-3'
        }
      }
    }
  });

  //----------------------------
  // Doughnut Chart แสดงสัดส่วนประเภทการซ่อมทั้งหมด
  const TypeChart = new Chart(document.getElementById("type_fixed"), {
    type: 'doughnut',
    data: {
      labels: typeLabels,
      datasets: [{
        label: 'จำนวน',
        data: typeCounts,
        backgroundColor: ['rgb(255, 99, 132)', 'rgb(54, 162, 235)'],
        borderColor: 'rgb(166, 169, 175)',
        borderWidth: 1
      }]
    },
    options: {
      plugins: {
        title: {
          display: true,
          text: 'สัดส่วนของประเภทการซ่อม'
        }
      }
    }
  });

});


async function filterTable() {


  //ดึง id ของ div ที่ต้องการแสดงผลลัพธ์จากการกรอง
  const contentDiv = document.getElementById("main-content");

  //รับค่าที่ user ใส่มา
  const filterDate1 = document.getElementById("calendarSelect1").value;
  const filterDate2 = document.getElementById("calendarSelect2").value;

  let filterLevel1, filterLevel2, filterLevel3;
  if (document.getElementById("level_select1").checked == true) {
    filterLevel1 = 1;
  } else { filterLevel1 = ""; }
  if (document.getElementById("level_select2").checked == true) {
    filterLevel2 = 2;
  } else { filterLevel2 = ""; }
  if (document.getElementById("level_select3").checked == true) {
    filterLevel3 = 3;
  } else { filterLevel3 = ""; }

  const filterType = document.getElementById("select_type").value;
  const filterReason = document.getElementById("reason_select").value;
  const filterRepairing = document.getElementById("repairing_select").value;
  const filterStatus = document.getElementById("status_select").value;
  const filterCost = document.getElementById("cost_select").value;

  //สร้าง object เก็บ
  let data = {
    "date1": filterDate1,
    "date2": filterDate2,
    "level1": filterLevel1,
    "level2": filterLevel2,
    "level3": filterLevel3,
    "type": filterType,
    "reason": filterReason,
    "repairing": filterRepairing,
    "status": filterStatus,
    "cost": filterCost
  }

  console.log(data);


  //ส่งข้อมูลไปที่ filter_result.php ด้วย fetch API ในรูปแบบ JSON
  await fetch("filterFixed.php", {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
      "Content-type": "application/json; charset=UTF-8"
    }
  })
    //รับข้อมูลที่ส่งกลับมาเป็น text
    .then((response) => response.text())
    //แสดงข้อมูลที่ได้ใน div ที่กำหนดไว้
    .then((text) => contentDiv.innerHTML = text)

}

