document.addEventListener('DOMContentLoaded', () => {
    loadHospitals();
    const form = document.querySelector('.box');
    const cancelButton = document.querySelector('.cancel-button');
    const reasonField = document.getElementById('cause');
    const otherCauseRow = document.getElementById('other-cause-row');
    const otherCauseField = document.getElementById('other-cause');

    // form.addEventListener('submit', (event) => {
    //     event.preventDefault();
    //     window.location.href = 'emergency_report_success.html'; // เปลี่ยนหน้าไปยัง emergency_report_success.html
    // });

    cancelButton.addEventListener('click', () => {
        form.reset(); // รีเซ็ตหน้าฟอร์ม
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

function getCurrentDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = now.getMonth() + 1;
    const day = now.getDate();

    return `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
}

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