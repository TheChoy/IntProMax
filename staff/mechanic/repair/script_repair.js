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

function validateAndUpdateRepairDate(input, repairId) {
  const selectedDate = new Date(input.value);
  const repairDate = new Date(input.dataset.repairDate);

  if (selectedDate < repairDate) {
    alert("วันเสร็จสิ้นต้องไม่ก่อนวันรับซ่อม!");
    input.value = '';
    return;
  }

  // เรียกฟังก์ชันส่งข้อมูลไปอัปเดต
  updateRepair(repairId, input.value, 'date');
}
function validateAndUpdateStatus(selectElement, repairId) {
  const selectedStatus = selectElement.value;

  if (selectedStatus === "เสร็จสิ้น") {
    // หา input วันเสร็จและราคาจากแถวเดียวกัน
    const row = selectElement.closest('tr');
    const dateInput = row.querySelector('input[type="date"]');
    const costInput = row.querySelector('input[type="number"]');

    if (!dateInput.value) {
      alert("กรุณากรอกวันที่เสร็จสิ้นก่อนเปลี่ยนสถานะเป็น 'เสร็จสิ้น'");
      selectElement.value = "รอดำเนินการ"; // reset
      return;
    }

    if (!costInput.value || parseFloat(costInput.value) <= 0) {
      alert("กรุณากรอกราคาซ่อมก่อนเปลี่ยนสถานะเป็น 'เสร็จสิ้น'");
      selectElement.value = "รอดำเนินการ"; // reset
      return;
    }
  }

  // ถ้าผ่านเงื่อนไข ให้ส่งค่าต่อไป
  updateRepair(repairId, selectedStatus, 'status');
}