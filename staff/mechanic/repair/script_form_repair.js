document.addEventListener('DOMContentLoaded', () => {
    const dateField = document.getElementById('currentDate');
    const noteField = document.getElementById('note');
    const reasonField = document.getElementById('reason');
    const categoryField = document.getElementById('category');
    const deviceField = document.getElementById('device');
    const cancelButton = document.getElementById('cancel-button');
    const levelField = document.getElementById('level');
    const numberField = document.getElementById('number');
    const otherCauseRow = document.getElementById('other-cause-row');
    const otherCauseField = document.getElementById('note');

    const getCurrentDate = () => {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    dateField.value = getCurrentDate();
    noteField.disabled = true;

    reasonField.addEventListener('change', () => {
        if (reasonField.value === 'other') {
            otherCauseRow.style.display = 'block';
            otherCauseField.required = true;
            noteField.disabled = false;
            reasonField.name = ''; // ไม่ส่งค่าจาก select
            otherCauseField.name = 'reason'; // ส่งค่าจาก input text แทน
        } else {
            otherCauseRow.style.display = 'none';
            otherCauseField.required = false;
            otherCauseField.value = '';
            noteField.disabled = true;
            reasonField.name = 'reason';// ส่งค่าจาก select
            otherCauseField.name = '';
        }
    });

    levelField.addEventListener('change', () => {
        const level = levelField.value;
        let numberOptions = '';

        if (level === 'ระดับ 1') {
            numberOptions = `
                <option value="2">ขค5678</option>
                <option value="4">ตฎ1142</option>
            `;
        } else if (level === 'ระดับ 2') {
            numberOptions = `
                <option value="1">กข1234</option>
                <option value="5">ลนณ886</option>
            `;
        } else if (level === 'ระดับ 3') {
            numberOptions = `
                <option value="3">ฉช378</option>
            `;
        }
        numberField.innerHTML = `<option value="" disabled selected>ระบุทะเบียนรถ</option>${numberOptions}`;
    });

    categoryField.addEventListener('change', () => {
        const category = categoryField.value;
        let deviceOptions = '';
        let reasonOptions = '';

        if (category === 'รถพยาบาล') {
            deviceOptions = `
                <option value="ความสะอาด">ความสะอาด</option>
                <option value="เครื่องยนต์">เครื่องยนต์</option>
                <option value="ล้อรถ">ล้อรถ</option>
                <option value="ประตูรถ">ประตูรถ</option>
                <option value="เบรก">เบรก</option>
                <option value="ไฟรถ">ไฟรถ</option>
            `;
            reasonOptions = `
                <option value="ชำรุด">ชำรุด</option>
                <option value="หมดอายุ">หมดอายุ</option>
                <option value="other">อื่นๆ</option>
            `;
        } else if (category === 'อุปกรณ์ทางการแพทย์') {
            deviceOptions = `
                <option value="เครื่องAED">เครื่องAED</option>
                <option value="เครื่องช่วยหายใจ">เครื่องช่วยหายใจ</option>
                <option value="ถังออกซิเจน">ถังออกซิเจน</option>
                <option value="เครื่องวัดความดัน">เครื่องวัดความดัน</option>
                <option value="เครื่องวัดชีพจร">เครื่องวัดชีพจร</option>
                <option value="เตียงพยาบาล">เตียงพยาบาล</option>
                <option value="เปลสนาม">เปลสนาม</option>
                <option value="อุปกรณ์ปฐมพยาบาล">อุปกรณ์ปฐมพยาบาล</option>
                <option value="อุปกรณ์การดาม">อุปกรณ์การดาม</option>
            `;
            reasonOptions = `
                <option value="ชำรุด">ชำรุด</option>
                <option value="หมดอายุ">หมดอายุ</option>
                <option value="other">อื่นๆ</option>
            `;
        }

        deviceField.innerHTML = `<option value="" disabled selected>ระบุอุปกรณ์</option>${deviceOptions}`;
        reasonField.innerHTML = `<option value="" disabled selected>สาเหตุ</option>${reasonOptions}`;
    });

    cancelButton.addEventListener('click', () => {
        window.location.href = 'repair.php';
    });
});
document.querySelector('.formReportRepair').addEventListener('submit', function (e) {
    e.preventDefault();

    // ตรวจสอบว่าฟิลด์ที่จำเป็นถูกกรอกครบหรือไม่
    const required = ['level', 'number', 'category', 'device', 'reason'];
    const isEmpty = required.some(id => !document.getElementById(id).value);

    if (isEmpty) {
        alert('กรุณากรอกข้อมูลให้ครบถ้วน');
        return;
    }

    // ถ้าข้อมูลครบถ้วน ให้ส่งฟอร์ม
    this.submit();
});
//