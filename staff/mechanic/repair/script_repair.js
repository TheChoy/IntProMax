
document.addEventListener('DOMContentLoaded', () => {

  // ดึงตัวรับ input จากหน้าเว็บด้วย id เพื่อใส่ eventListener (filterTable)

  const filterDate = document.getElementById("filter-date");
  filterDate.addEventListener('input', filterTable);

  const filterAmbuID = document.getElementById("filter-ambulance-ID");
  filterAmbuID.addEventListener('input', filterTable);

  const filterStatus = document.getElementById("filter-status");
  filterStatus.addEventListener('input', filterTable);

});

async function filterTable() {

  //ดึง id ของ div ที่ต้องการแสดงผลลัพธ์จากการกรอง
  const contentDiv = document.getElementById("my-list");

  //รับค่าที่ user ใส่มา
  const filterDate = document.getElementById("filter-date").value;
  const filterAmbuID = document.getElementById("filter-ambulance-ID").value;
  const filterStatus = document.getElementById("filter-status").value;

  //สร้าง object เก็บ
  let data = {
    "date": filterDate,
    "ambuID": filterAmbuID,
    "status": filterStatus
  }

  //ส่งข้อมูลไปที่ filter_result.php ด้วย fetch API ในรูปแบบ JSON
  await fetch("filter_result.php", {
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

function addRepair() {
  // เอาไว้เชื่อมกับ from_repair.php
  window.location.href = 'from_repair.html';
}

function updateRepair(repairId, value, type) {
  fetch('update_repair.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ repair_id: repairId, value: value, type: type })
  }).then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        location.reload();  // โหลดหน้าใหม่เพื่ออัปเดตข้อมูล
      } else {
        alert('เกิดข้อผิดพลาด: ' + data.message);
      }
    });
}
function addRepair() {
  window.location.href = "from_repair.php";
}


// function deleteRepair(index) {
//     let repairs = JSON.parse(localStorage.getItem('repairs')) || [];
//     repairs.splice(index, 1);
//     localStorage.setItem('repairs', JSON.stringify(repairs));
//     loadRepairs(); // Reload the table
// }

// ฟังก์ชันบันทึกข้อมูล
// function saveRepair() {
//     const rows = document.querySelectorAll("tbody tr");
//     const updatedData = [];

//     rows.forEach(row => {
//         const rowData = {
//             dateReceived: row.cells[0].textContent.trim(),
//             license: row.cells[1].querySelector("input").value,
//             repairType: row.cells[2].textContent.trim(),
//             equipment: row.cells[3].textContent.trim(),
//             reason: row.cells[4].textContent.trim(),
//             dueDate: row.cells[5].querySelector("input").value,
//             reporter: row.cells[6].textContent.trim(),
//             status: row.cells[7].querySelector("select").value
//         };
//         updatedData.push(rowData);
//     });

//     console.log("ข้อมูลที่บันทึก:", updatedData);
//     alert("ข้อมูลถูกบันทึกเรียบร้อยแล้ว!");