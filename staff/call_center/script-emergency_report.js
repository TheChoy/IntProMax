document.addEventListener('DOMContentLoaded', () => {
    loadHospitals();
    const form = document.querySelector('.box');
    const cancelButton = document.querySelector('.cancel-button');
    const reasonField = document.getElementById('cause');
    const otherCauseRow = document.getElementById('other-cause-row');
    const otherCauseField = document.getElementById('other-cause');
    const districtSelect = document.getElementById("filter-zone-list");
    const costInput = document.getElementById("cost");

    // ดึงค่าใช้จ่ายจากตัวแปรที่ส่งจาก PHP
    const costPerDistrict = window.costPerDistrict;

    // โหลดค่าใช้จ่ายเมื่อเปลี่ยนเขต
    districtSelect.addEventListener('change', updateCost);
    updateCost(); // โหลดค่าเริ่มต้นเมื่อเปิดหน้า

    // ฟังก์ชันอัปเดตค่าใช้จ่าย
    function updateCost() {
        const selectedDistrict = districtSelect.value;
        if (costPerDistrict[selectedDistrict]) {
            costInput.value = costPerDistrict[selectedDistrict] + " บาท";
        } else {
            costInput.value = "";
        }
    }

    cancelButton.addEventListener('click', () => {
        form.reset();
        updateCost(); // รีเซ็ตค่าใช้จ่าย
    });

    reasonField.addEventListener('change', () => {
        if (reasonField.value === 'other') {
            otherCauseRow.style.display = 'block';
            otherCauseField.required = true; // ทำให้ฟิลด์ข้อความเป็น required
        } else {
            otherCauseRow.style.display = 'none';
            otherCauseField.required = false; // ทำให้ฟิลด์ข้อความไม่เป็น required
            otherCauseField.value = ''; // ล้างค่าฟิลด์ข้อความ
        }
    });
});

async function loadHospitals() {
    const jsonUrl = 'hospital.json'; // แทนที่ด้วย URL หรือ path ของไฟล์ JSON

    try {
        const response = await fetch(jsonUrl);
        if (!response.ok) throw new Error('ไม่สามารถโหลดไฟล์ JSON ได้');
        const hospitals = await response.json();

        // เพิ่มตัวเลือกใน Dropdown
        const dropdown = document.getElementById('hospital');
        hospitals.forEach(hospital => {
            const option = document.createElement('option');
            option.value = hospital.hospital_name;
            option.textContent = hospital.hospital_name;
            dropdown.appendChild(option);
        });

    } catch (error) {
        console.error('เกิดข้อผิดพลาด:', error);
    }
}
