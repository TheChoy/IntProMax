<?php
    include 'connect.php'; 
    //เลือกเฉพาะรถที่มีระดับ 2 หรือ 3
    $query_ambulance = mysqli_query($conn, "SELECT * FROM ambulance WHERE ambulance_level = '2' OR ambulance_level = '3'");
    $ambulance_data = mysqli_fetch_all($query_ambulance, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style-emergency_report.css">
    <script src="script-emergency_report.js" defer></script>
    <title>รายงานเคสฉุกเฉิน</title>
</head>

<body>

    <div class="title">
        <h1>รายงานเคสฉุกเฉิน</h1>
    </div>
    <form action="process.php" method="post" class="box">
        <div class="row">
            <label for="contact">ผู้ติดต่อ</label>
            <input type="text" name="contact" id="contact" required>

            <label for="contact_number">เบอร์โทรติดต่อ</label>
            <input type="text" name="contact_number" id="contact_number" required>
        </div>

        <div class="row">
            <label for="patient_name">ชื่อผู้ป่วย</label>
            <input type="text" name="patient_name" id="patient_name" required>

            <label for="patient_age">อายุผู้ป่วย</label>
            <input type="number" name="patient_age" id="patient_age" required min="1">
        </div>
        <div class="row">
            <label for="gender">เพศผู้ป่วย</label>
            <select id="gender" name="gender" required>
                <option value="" disabled selected>ระบุเพศ</option>
                <option value="ชาย">ชาย</option>
                <option value="หญิง">หญิง</option>
            </select>
        </div>

        <div class="row">
            <label for="cause">สาเหตุ/อาการป่วย</label>
            <select id="cause" name="cause" required>
                <option value="" disabled selected>ระบุสาเหตุ</option>
                <option value="อุบัติเหตุ">อุบัติเหตุ</option>
                <option value="อาการป่วย">อาการป่วย</option>
                <option value="other">อื่นๆ</option>
            </select>
        </div>
        <div class="row" id="other-cause-row" style="display: none;">
            <label for="other-cause">ระบุรายละเอียด</label>
            <input type="text" name="other-cause" id="other-cause">
        </div>

        <div class="row">
            <label for="filter-zone-list">เขตที่เกิดเหตุ</label>
            <select id="filter-zone-list" name="filter-zone-list" class="filter-select">
                <option value="" selected hidden>กรุณาเลือกเขต</option>
                <option value="พระนคร">พระนคร</option>
                <option value="ดุสิต">ดุสิต</option>
                <option value="หนองจอก">หนองจอก</option>
                <option value="บางรัก">บางรัก</option>
                <option value="บางเขน">บางเขน</option>
                <option value="บางกะปิ">บางกะปิ</option>
                <option value="ปทุมวัน">ปทุมวัน</option>
                <option value="ป้อมปราบศัตรูพ่าย">ป้อมปราบศัตรูพ่าย</option>
                <option value="พระโขนง">พระโขนง</option>
                <option value="มีนบุรี">มีนบุรี</option>
                <option value="ลาดกระบัง">ลาดกระบัง</option>
                <option value="ยานนาวา">ยานนาวา</option>
                <option value="สัมพันธวงศ์">สัมพันธวงศ์</option>
                <option value="พญาไท">พญาไท</option>
                <option value="ธนบุรี">ธนบุรี</option>
                <option value="บางกอกใหญ่">บางกอกใหญ่</option>
                <option value="ห้วยขวาง">ห้วยขวาง</option>
                <option value="คลองสาน">คลองสาน</option>
                <option value="ตลิ่งชัน">ตลิ่งชัน</option>
                <option value="บางกอกน้อย">บางกอกน้อย</option>
                <option value="บางขุนเทียน">บางขุนเทียน</option>
                <option value="ภาษีเจริญ">ภาษีเจริญ</option>
                <option value="หนองแขม">หนองแขม</option>
                <option value="ราษฎร์บูรณะ">ราษฎร์บูรณะ</option>
                <option value="บางพลัด">บางพลัด</option>
                <option value="ดินแดง">ดินแดง</option>
                <option value="บึงกุ่ม">บึงกุ่ม</option>
                <option value="สาทร">สาทร</option>
                <option value="บางซื่อ">บางซื่อ</option>
                <option value="จตุจักร">จตุจักร</option>
                <option value="บางคอแหลม">บางคอแหลม</option>
                <option value="ประเวศ">ประเวศ</option>
                <option value="คลองเตย">คลองเตย</option>
                <option value="สวนหลวง">สวนหลวง</option>
                <option value="จอมทอง">จอมทอง</option>
                <option value="ดอนเมือง">ดอนเมือง</option>
                <option value="ราชเทวี">ราชเทวี</option>
                <option value="ลาดพร้าว">ลาดพร้าว</option>
                <option value="วัฒนา">วัฒนา</option>
                <option value="บางแค">บางแค</option>
                <option value="หลักสี่">หลักสี่</option>
                <option value="สายไหม">สายไหม</option>
                <option value="คันนายาว">คันนายาว</option>
                <option value="สะพานสูง">สะพานสูง</option>
                <option value="วังทองหลาง">วังทองหลาง</option>
                <option value="คลองสามวา">คลองสามวา</option>
                <option value="บางนา">บางนา</option>
                <option value="ทวีวัฒนา">ทวีวัฒนา</option>
                <option value="บางบอน">บางบอน</option>
            </select>
        </div>
        <div class="row">
            <label for="start-point">สถานที่ต้นทาง</label>
            <input type="text" name="start-point" id="start-point" placeholder="ระบุรายละเอียดเพิ่มเติม" required>
        </div>
        <div class="row">
            <label for="end-point">สถานที่ปลายทาง</label>
            <select id="hospital" name="hospital" required>
                <option value="" disabled selected>ระบุโรงพยาบาล</option>
            </select>
        </div>
        <div class="row">
            <label for="ambulance_id">รถพยาบาลที่ออกปฏิบัติงาน</label>
            <select id="ambulance_id" name="ambulance_id" required>
                <option value="" selected hidden>กรุณาเลือกรถ</option>
                <!-- แสดงตัวเลือกแค่รถระดับ 2 หรือ 3 ตามที่ query มา-->
                <?php foreach($ambulance_data as $row) { ?>
                    <option value="<?php echo $row["ambulance_id"];?>">
                        <?php echo $row["ambulance_id"];?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="button">
            <button type="submit" class="submit-button">บันทึก</button>
            <button type="button" class="cancel-button">ยกเลิก</button>
        </div>
    </form>
</body>

</html>