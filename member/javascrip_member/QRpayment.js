document.addEventListener("DOMContentLoaded", () => {
    const uploadBtn = document.getElementById("upload-btn");
    const qrPreview = document.getElementById("qr-preview");
    const cancelBtn = document.getElementById("cancel-btn");
    const confirmBtn = document.getElementById("confirm-btn");

    // สร้าง input สำหรับอัพโหลดไฟล์
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/*';

    // เมื่อมีการเลือกไฟล์
    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                qrPreview.src = e.target.result; // อัปเดตรูปภาพใน preview
                // แสดงปุ่มยืนยันและยกเลิกหลังจากเลือกไฟล์
                cancelBtn.style.display = 'inline-block';
                confirmBtn.style.display = 'inline-block';
            };
            reader.readAsDataURL(file);
        }
    });

    // เมื่อคลิกที่ปุ่ม "อัพโหลด"
    uploadBtn.addEventListener('click', () => {
        fileInput.click(); // เปิดหน้าต่างเลือกไฟล์
    });

    // เมื่อคลิกที่ปุ่ม "ยืนยัน"
    confirmBtn.addEventListener('click', () => {
        alert("ยืนยันการอัพโหลด");
        cancelBtn.style.display = 'none'; // ซ่อนปุ่มยืนยันหลังการยืนยัน
        confirmBtn.style.display = 'none'; // ซ่อนปุ่มยกเลิกหลังการยืนยัน
    });

    // เมื่อคลิกที่ปุ่ม "ยกเลิก"
    cancelBtn.addEventListener('click', () => {
        qrPreview.src = 'image/QRcode.jpeg'; // คืนค่าเป็นภาพเริ่มต้น
        cancelBtn.style.display = 'none'; // ซ่อนปุ่มยืนยันหลังการยกเลิก
        confirmBtn.style.display = 'none'; // ซ่อนปุ่มยกเลิกหลังการยกเลิก
    });
});

function submitForm() {
    // สามารถใช้ AJAX เพื่อส่งข้อมูลที่ต้องการก่อนเปลี่ยนหน้า
    let formData = { "key": "value" };

    fetch('success_payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        // ส่งข้อมูลสำเร็จแล้ว ทำการเปลี่ยนหน้า
        window.location.href = 'success_payment.html'; // ไปยังไฟล์เป้าหมาย
    })
    .catch(error => console.error('Error:', error));
}


