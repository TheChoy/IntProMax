<?php
//-----------Session and Login-------------
session_start();
include 'username.php';

// ถ้าไม่ได้ล็อกอิน ให้ redirect กลับไปหน้า login
if (empty($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

// เรียก member_id จาก session มาใช้ :
// $_SESSION['user_id'];
//------------------------------------------

// รับค่าจาก URL หรือ POST
$booking_date = $_GET['booking_date'] ?? 'ไม่มีวันที่';
$booking_start_time = $_GET['booking_start_time'] ?? 'ไม่มีเวลา';

$sql = "SELECT member_firstname, member_lastname 
        FROM member 
        ORDER BY RAND() 
        LIMIT 1";

$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_car.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <title>ฟอร์มจองรถ</title>
    <style>
        .form-container {
            display: none;
        }

        .form-container.active {
            display: block;
        }
    </style>
</head>

<body>
    <div class="top-navbar">
        <nav class="nav-links">
            <div><a href="contact.html">ติดต่อเรา</a></div>
            <div class="dropdown">
                <img src="image/user.png" alt="Logo" class="nav-logo">
                <div class="dropdown-menu">
                    <a href="profile.html">โปรไฟล์</a>
                    <a href="order-history.html">ประวัติคำสั่งซื้อ</a>
                    <a href="claim.php">เคลมสินค้า</a>
                    <a href="../logout.php">ออกจากระบบ</a>
                </div>
            </div>
            <a href="index.html">
                <img src="image/united-states-of-america.png" alt="Logo" class="nav-logo">
            </a>
        </nav>
    </div>

    <div class="main-navbar">
        <nav class="nav-links">
            <div><a href="index.php">หน้าแรก</a></div>
            <div><a href="reservation_car.php" style="color: #FFB898">จองคิวรถ</a></div>
            <a href="index.php">
                <img src="image/Logo.png" alt="Logo" class="nav-logo1">
            </a>
            <div><a href="shopping.php">ซื้อ/เช่าอุปกรณ์ทางการแพทย์</a></div>
        </nav>

        <div class="cart-icon">
            <a href="cart.php">
                <i class="fas fa-shopping-cart"></i>
            </a>
        </div>
    </div>
    <br>

    <!-- Dropdown for selecting forms -->
    <div style="text-align: center; font-weight: bold;" class="form-select">
        <label for="formSelect">ประเภทการจอง</label>
        <select id="formSelect" name="event_type" required
            style="width: 30%; padding: 8px; border: 1px solid #ccc; border-radius: 15px;">
            <option value="form1">จองงาน Event</option>
            <option value="form2">จองสำหรับรับส่งผู้ป่วย</option>
        </select>
    </div>

    <!-- Form 1 -->
    <form action="insert_data_form.php" method="POST">
        <div id="form1" class="form-container active">
            <h2 style="text-align: center;">จองงาน Event</h2>
            <div id="selectedDateTime">
                <p>วันที่เลือก: <?php echo $booking_date; ?></span></p>
                <p>เวลาที่เลือก: <?php echo $booking_start_time; ?></span></p>
            </div>
            <br>
            <!-- สร้าง form ซ่อนไว้สำหรับส่งข้อมูล -->
            <form id="bookingForm">
                <input type="hidden" name="booking_date" id="bookingDate" value="<?php echo $booking_date; ?>">
                <input type="hidden" name="booking_start_time" id="bookingTime" value="<?php echo $booking_start_time; ?>">
            </form>

            <div class="form-group">

                <div class="radio">
                    <label for="level">ระดับรถ</label>

                    <div>
                        <input type="radio" id="first" name="level" value="1" required onchange="calculatePrice()"> ระดับ 1
                    </div>
                    <div>
                        <input type="radio" id="basic" name="level" value="2" required onchange="calculatePrice()"> ระดับ 2
                    </div>
                    <div>
                        <input type="radio" id="advanced" name="level" value="3" required onchange="calculatePrice()"> ระดับ 3
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="province1">จังหวัด</label>
                <select id="province1" name="province1" required
                    style="width: 25%; padding: 8px; border: 1px solid #ccc; border-radius: 15px;">
                    <option value="" selected hidden>เลือกจังหวัด</option>
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
                    <option value="อำนาจเจริญ">อำนาจเจริญ</option>
                    <option value="อุดรธานี">อุดรธานี</option>
                    <option value="อุตรดิตถ์">อุตรดิตถ์</option>
                    <option value="อุทัยธานี">อุทัยธานี</option>
                    <option value="อุบลราชธานี">อุบลราชธานี</option>
                </select>
            </div>
            <div class="form-group">
                <label for="place_event_location">สถานที่รับงาน</label>
                <input type="text" id="pickup-location" name="place_event_location" required>
            </div>
            <div class="form-group">
                <label for="place_event_detail">รายละเอียดสถานที่</label>
                <textarea id="place_event_detail" name="place_event_detail" rows="4" cols="50" required></textarea>
            </div>

            <div class="form-group">
                <label for="type">ประเภทงาน</label>
                <select id="type" name="event_type" required
                    style="width: 30%; padding: 8px; border: 1px solid #ccc; border-radius: 15px;">
                    <option value="" selected hidden>เลือกประเภทงาน</option>
                    <option value="กีฬาสีและการแข่งขัน">กีฬาสีและการแข่งขัน</option>
                    <option value="งานชุมนุม">งานชุมนุม</option>
                    <option value="งานพิธีการ">งานพิธีการ</option>
                    <option value="อุตสาหกรรมก่อสร้าง">อุตสาหกรรมก่อสร้าง</option>
                    <option value="กิจกรรมเด็กหรือผู้สูงวัย">กิจกรรมเด็กหรือผู้สูงวัย</option>
                    <option value="คัดกรองโรค">คัดกรองโรค</option>
                </select>
            </div>
            <div class="form-group">
                <label for="nurse_number">จำนวนพยาบาล</label>
                <input type="number" id="nurse_number" name="nurse_number" required min="0" step="1" value="1"
                    style="text-align: center; width: 100px;" oninput="calculatePrice()"> คน/คัน
            </div>

            <div class="form-group">
                <label for="ambulance_number">จำนวนรถพยาบาล</label>
                <input type="number" id="ambulance_number" name="ambulance_number" required min="1" step="1" value="1"
                    style="text-align: center; width: 100px;" oninput="calculatePrice()"> คัน
                <!-- oninput="validateNumber(event) -->
            </div>

            <div class="form-group">
                <label for="payment_method">วิธีการชำระเงิน</label>
                <input type="hidden" id="payment_method_event" name="payment_method_event">
                <div class="payment-options">
                    <button type="button" id="payment-qr" class="payment-button">QR Promptpay</button>
                    <button type="button" id="payment-credit" class="payment-button">บัตรเครดิต</button>
                </div>
            </div>

            <!-- แสดงราคาค่าบริการ -->
            <div class="form-group">
                <p id="priceDisplay1" style="text-align: center; font-size: 18px;">ราคาค่าบริการ : 0 บาท</p>
            </div>

            <!-- เก็บราคาสำหรับส่งไปยัง Backend -->
            <input type="hidden" id="calculatedPrice1" name="calculatedPrice1">

            <div class="form-submit">
                <button type="button" id="cancel-button" class="cancel-button"
                    style="background-color: #F8E6DE;">ยกเลิก</button>
                <button type="submit" name="submit_event" style="background-color: #FFB898;" id="submit-button" onclick="submitPaymentEvent()">ยืนยัน</button>
            </div>
    </form>
    </div>

    <!-- Form 2 -->
    <form action="insert_data_form.php" method="post">
        <div id="form2" class="form-container">
            <h2 style="text-align: center;">จองสำหรับรับส่งผู้ป่วย</h2>
            <div id="selectedDateTime">
                <p>วันที่เลือก: <?php echo $booking_date; ?></span></p>
                <p>เวลาที่เลือก: <?php echo $booking_start_time; ?></span></p>
            </div>
            <br>
            <!-- สร้าง form ซ่อนไว้สำหรับส่งข้อมูล -->
            <form id="bookingForm">
                <input type="hidden" name="booking_date" id="bookingDate" value="<?php echo $booking_date; ?>">
                <input type="hidden" name="booking_start_time" id="bookingTime" value="<?php echo $booking_start_time; ?>">
            </form>
            <div class="form-group">

                <div class="radio">
                    <label for="level">ระดับรถ</label>

                    <div>
                        <input type="radio" id="first" name="level" value="1" require onchange="calculatePrice()"> ระดับ 1
                    </div>
                    <div>
                        <input type="radio" id="basic" name="level" value="2" require onchange="calculatePrice()"> ระดับ 2
                    </div>
                    <div>
                        <input type="radio" id="advanced" name="level" value="3" require onchange="calculatePrice()"> ระดับ 3
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="patient-name">ชื่อผู้ป่วย</label>
                <label for="patient-name"><?php while ($row = $result->fetch_assoc()) {
                                                echo $row['member_firstname'] . " " . $row['member_lastname'];
                                            }; ?>
                </label>
            </div>
            <div class="form-group">
                <label for="province2">จังหวัด</label>
                <select id="province2" name="province2" required
                    style="width: 25%; padding: 8px; border: 1px solid #ccc; border-radius: 15px;">
                    <option value="" selected hidden>เลือกจังหวัด</option>
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
                    <option value="อำนาจเจริญ">อำนาจเจริญ</option>
                    <option value="อุดรธานี">อุดรธานี</option>
                    <option value="อุตรดิตถ์">อุตรดิตถ์</option>
                    <option value="อุทัยธานี">อุทัยธานี</option>
                    <option value="อุบลราชธานี">อุบลราชธานี</option>
                </select>
            </div>
            <div class="form-group">
                <label for="pickup-location">สถานที่รับผู้ป่วย</label>
                <input type="text" id="pickup-location" name="pickup-location" required>
            </div>

            <div class="form-group">
                <label for="hospital">โรงพยาบาล</label>
                <select id="hospital" name="hospital" required>
                    <option value="" selected hidden>เลือกโรงพยาบาล</option>
                    <option value="โรงพยาบาลรามาธิบดี">โรงพยาบาลรามาธิบดี</option>
                    <option value="โรงพยาบาลกรุงเทพ">โรงพยาบาลกรุงเทพ</option>
                    <option value="โรงพยาบาลมะเร็งกรุงเทพ">โรงพยาบาลมะเร็งกรุงเทพ</option>
                    <option value="โรงพยาบาลนพรัตนราชธานี">โรงพยาบาลนพรัตนราชธานี</option>
                </select>
            </div>
            <div class="form-group">
                <label for="symptom">อาการ/โรค</label>
                <select id="symptom" name="symptom" required
                    style="width: 30%; padding: 8px; border: 1px solid #ccc; border-radius: 15px;">
                    <option value="" selected hidden>เลือกอาการ/โรค</option>
                    <option value="เกี่ยวกับระบบทางเดินหายใจ">เกี่ยวกับระบบทางเดินหายใจ</option>
                    <option value="เกี่ยวกับระบบไหลเวียนเลือด">เกี่ยวกับระบบไหลเวียนเลือด</option>
                    <option value="เกี่ยวกับกล้ามเนื้อและกระดูก">เกี่ยวกับกล้ามเนื้อและกระดูก</option>
                    <option value="โรคเรื้อรัง">โรคเรื้อรัง</option>
                    <option value="สุขภาพจิต">สุขภาพจิต</option>
                </select>
            </div>
            <div class="form-group">
                <label for="allergy">แพ้ยา/อาหาร</label>
                <select id="allergy" name="allergy" required
                    style="width: 30%; padding: 8px; border: 1px solid #ccc; border-radius: 15px;">
                    <option value="" selected hidden>เลือกแพ้ยา/อาหาร</option>
                    <option value="อาหารทะเล">อาหารทะเล</option>
                    <option value="นมวัว">นมวัว</option>
                    <option value="ถั่วลิสง">ถั่วลิสง</option>
                    <option value="ไข่">ไข่</option>
                    <option value="ยาปฏิชีวนะ">ยาปฏิชีวนะ</option>
                    <option value="ยาชา">ยาชา</option>
                </select>
            </div>

            <div class="form-group">
                <label for="payment-method">วิธีการชำระเงิน</label>
                <input type="hidden" id="payment_method_hospital" name="payment_method_hospital">
                <div class="payment-options">
                    <button type="button" id="payment-qr2" class="payment-button">QR Promptpay</button>
                    <button type="button" id="payment-credit2" class="payment-button">บัตรเครดิต</button>
                </div>
            </div>

            <!-- แสดงราคาค่าบริการ -->
            <div class="form-group">
                <p id="priceDisplay2" style="text-align: center; font-size: 18px;">ราคาค่าบริการ: 0 บาท</p>
            </div>

            <!-- เก็บราคาสำหรับส่งไปยัง Backend -->
            <input type="hidden" id="calculatedPrice2" name="calculatedPrice2">

            <div class="form-submit">
                <button type="button" id="cancel-button" class="cancel-button"
                    style="background-color: #F8E6DE;">ยกเลิก</button>
                <button type="submit" name="submit_ambulance" style="background-color: #FFB898;" id="submit-button">ยืนยัน</button>
            </div>
    </form>
    </div>

    <script>
        // ฟังก์ชันที่จะส่งข้อมูลไปยัง QRpayment.php เมื่อคลิกปุ่ม "ยืนยัน"
        function submitPaymentEvent() {
            var calculatedPrice = document.getElementById("calculatedPrice1").value; // รับค่า calculatedPrice1
            var url = "QRpayment.php?total_price=" + calculatedPrice; // สร้าง URL สำหรับส่งข้อมูล
            window.location.href = url; // เปลี่ยนหน้าผ่าน URL ที่ส่งข้อมูล
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("payment-qr").addEventListener("click", function() {
                document.getElementById("payment_method_event").value = "QR Promptpay";
            });

            document.getElementById("payment-credit").addEventListener("click", function() {
                document.getElementById("payment_method_event").value = "บัตรเครดิต";
            });
        });

        // การเลือกวิธีการชำระเงิน
        let selectedPaymentMethod = "";

        document.getElementById('payment-qr').addEventListener('click', function() {
            selectedPaymentMethod = 'QR Promptpay';
            document.getElementById('payment-qr').style.border = "2px solid #2D5696";
            document.getElementById('payment-credit').style.border = "none";
        });

        document.getElementById('payment-credit').addEventListener('click', function() {
            selectedPaymentMethod = 'บัตรเครดิต';
            document.getElementById('payment-credit').style.border = "2px solid #2D5696";
            document.getElementById('payment-qr').style.border = "none";
        });

        // ยกเลิกการจอง
        document.getElementById('cancel-button').addEventListener('click', function() {
            // รีเซ็ตฟอร์มเมื่อคลิกปุ่มยกเลิก
            document.querySelector("form").reset();
            document.getElementById('payment-qr').style.border = "none";
            document.getElementById('payment-credit').style.border = "none";
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("payment-qr2").addEventListener("click", function() {
                document.getElementById("payment_method_hospital").value = "QR Promptpay";
            });

            document.getElementById("payment-credit2").addEventListener("click", function() {
                document.getElementById("payment_method_hospital").value = "บัตรเครดิต";
            });
        });

        // การเลือกวิธีการชำระเงิน
        let selectedPaymentMethod2 = "";

        document.getElementById('payment-qr2').addEventListener('click', function() {
            selectedPaymentMethod = 'QR Promptpay';
            document.getElementById('payment-qr2').style.border = "2px solid #2D5696";
            document.getElementById('payment-credit2').style.border = "none";
        });

        document.getElementById('payment-credit2').addEventListener('click', function() {
            selectedPaymentMethod = 'บัตรเครดิต';
            document.getElementById('payment-credit2').style.border = "2px solid #2D5696";
            document.getElementById('payment-qr2').style.border = "none";
        });

        // ยกเลิกการจอง
        document.getElementById('cancel-button').addEventListener('click', function() {
            // รีเซ็ตฟอร์มเมื่อคลิกปุ่มยกเลิก
            document.querySelector("form").reset();
            document.getElementById('payment-qr2').style.border = "none";
            document.getElementById('payment-credit2').style.border = "none";
        });
    </script>


    <script>
        function selectPayment(paymentType, inputId) {
            document.getElementById(inputId).value = paymentType;
        }
        // กำหนด Layer ตามจังหวัด
        const layers = {
            "layer_0": ["กรุงเทพมหานคร"],
            "layer_1": ["นนทบุรี", "ปทุมธานี", "สมุทรปราการ", "สมุทรสาคร", "นครปฐม", "พระนครศรีอยุธยา", "สมุทรสงคราม"], // น้อยกว่า 100 km
            "layer_2": ["ราชบุรี", "ฉะเชิงเทรา", "สระบุรี", "นครนายก", "ปราจีนบุรี", "ชลบุรี", "ลพบุรี", "กาญจนบุรี", "สระแก้ว", "เพชรบุรี", "ระยอง", "อ่างทอง", "สิงห์บุรี", "สุพรรณบุรี"], //น้อยกว่า 200 km
            "layer_3": ["ตาก", "พิษณุโลก", "สุโขทัย", "อุตรดิตถ์", "ชัยนาท", "นครสวรรค์", "อุทัยธานี", "กำแพงเพชร", "พิจิตร", "เพชรบูรณ์", "นครราชสีมา", "ชัยภูมิ", "บุรีรัมย์", "ศรีสะเกษ", "ร้อยเอ็ด", "มหาสารคาม", "ขอนแก่น", "สุรินทร์", "ประจวบคีรีขันธ์", "เลย", "จันทบุรี", "ตราด", "ชุมพร", "ระนอง"], // น้อยกว่า 500 km
            "layer_4": ["อุบลราชธานี", "อำนาจเจริญ", "ยโสธร", "มุกดาหาร", "กาฬสินธุ์", "สกลนคร", "นครพนม", "หนองบัวลำภู", "หนองคาย", "บึงกาฬ", "อุดรธานี", "น่าน", "แพร่", "พะเยา", "ลำปาง", "ลำพูน", "สุราษฎร์ธานี"], //น้อยกว่า 700 km
            "layer_5": ["นครศรีธรรมราช", "พัทลุง", "สงขลา", "ยะลา", "ปัตตานี", "นราธิวาส", "ภูเก็ต", "พังงา", "กระบี่", "ตรัง", "สตูล", "เชียงราย", "เชียงใหม่", "แม่ฮ่องสอน"] // 700 km ขึ้นไป
        };

        // ราคาต่อ Layer
        const layerPrices = {
            "layer_0": 200,
            "layer_1": 500,
            "layer_2": 800,
            "layer_3": 1500,
            "layer_4": 2000,
            "layer_5": 3000
        };

        // ราคาต่อระดับรถ
        const vehicleLevelPrices = {
            "1": 600, // ระดับ 1
            "2": 1000, // ระดับ 2
            "3": 1400 // ระดับ 3
        };

        // ราคาต่อระดับรถของจองรถสำหรับรับส่งผู้ป่วย + พนักงาน2คน  200บาท
        const vehicleLevelPrices2 = {
            "1": 600, // ระดับ 1
            "2": 1000, // ระดับ 2
            "3": 1400 // ระดับ 3
        };

        // อัตราเพิ่มของงาน Event (แพงกว่าปกติ)
        const eventMultiplier = 1.5; // 1.5 เท่าของราคาปกติ
        // ราคาสำหรับพยาบาล
        const nursePrice = 100; // พยาบาล 100 บาท/คน
        // ค่าเริ่มต้นของพยาบาลใน form2 (3 คน)
        const defaultNurseCountForm2 = 0;
        // ค่าเริ่มต้นของรถพยาบาลใน form2 (1 คัน)
        const defaultAmbulanceCountForm2 = 1;



        // ฟังก์ชันคำนวณราคา
        function calculatePrice() {
            let province1 = document.getElementById("province1").value;
            let province2 = document.getElementById("province2").value;
            let bookingType = document.getElementById("formSelect").value;
            let price = 0;

            // หาระดับรถที่เลือก
            let vehicleLevel = document.querySelector('input[name="level"]:checked');
            let vehicleLevelCost = vehicleLevel ? vehicleLevelPrices[vehicleLevel.value] : 0;

            // คำนวณราคาใน Form1 (Event)
            if (province1) {
                for (const [layer, provinces] of Object.entries(layers)) {
                    if (provinces.includes(province1)) {
                        price = layerPrices[layer];
                        break;
                    }
                }

                // ถ้าเลือกงาน Event เพิ่มราคาตามอัตรา
                if (bookingType === "form1") {
                    price = Math.ceil(price * eventMultiplier);
                }

                // รับค่าพยาบาลและรถพยาบาลจาก input
                let nurseCount = parseInt(document.getElementById("nurse_number").value) || 0;
                let ambulanceCount = parseInt(document.getElementById("ambulance_number").value) || 0;

                // ราคาของรถพยาบาลใน form1 จะเท่ากับราคาของระดับรถที่เลือก
                let ambulanceCost = ambulanceCount * vehicleLevelCost;

                // คำนวณค่าพยาบาลทั้งหมดโดยคูณ nursePrice กับจำนวนรถพยาบาลที่เลือก
                let nurseCost = nurseCount * nursePrice;

                // เพิ่มค่าพยาบาล, รถพยาบาล และระดับรถ
                let extraCost = (nurseCount * nursePrice * ambulanceCount) + ambulanceCost;
                price += extraCost;


                // // เพิ่มค่าพยาบาล, รถพยาบาล และระดับรถ
                // let extraCost = nurseCost + ambulanceCost;
                // price += extraCost;



                document.getElementById("priceDisplay1").innerText = "ราคาค่าบริการ: " + price.toLocaleString() + " บาท";
                document.getElementById("calculatedPrice1").value = price;
            }

            // คำนวณราคาใน Form2 (รับส่งผู้ป่วย)
            if (province2) {
                let price2 = 0; // กำหนดค่าเริ่มต้นให้เป็น 0

                // หาระดับรถที่เลือก
                let vehicleLevel = document.querySelector('input[name="level"]:checked');
                let vehicleLevelCost2 = vehicleLevel ? vehicleLevelPrices2[vehicleLevel.value] : 0;

                // คำนวณราคาจากจังหวัดใน form2
                for (const [layer, provinces] of Object.entries(layers)) {
                    if (provinces.includes(province2)) {
                        price2 = layerPrices[layer];
                        break;
                    }
                }

                // เพิ่มราคาตามระดับรถที่เลือก
                price2 += vehicleLevelCost2;

                // รับค่ารถพยาบาลจาก input (ค่าเริ่มต้นคือ 1 คัน)
                let ambulanceCount = parseInt(document.getElementById("ambulance_number").value) || defaultAmbulanceCountForm2;
                let ambulanceCost = ambulanceCount * vehicleLevelCost2;

                // รับค่าพยาบาลจาก input (ค่าเริ่มต้นคือ 2 คน)
                let nurseCount = parseInt(document.getElementById("nurse_number").value) || defaultNurseCountForm2;
                let nurseCost = nurseCount * nursePrice;

                // แสดงผลราคาสำหรับ form2
                if (bookingType === "form2") {
                    document.getElementById("priceDisplay2").innerText = "ราคาค่าบริการ: " + price2.toLocaleString() + " บาท";
                    document.getElementById("calculatedPrice2").value = price2;
                }
            }
        }

        // กำหนดค่าเริ่มต้นของพยาบาลและรถพยาบาลใน form2
        document.addEventListener("DOMContentLoaded", function() {
            let nurseInput = document.getElementById("nurse_number");
            let ambulanceInput = document.getElementById("ambulance_number");

            if (nurseInput) {
                nurseInput.value = defaultNurseCountForm2;
            }

            if (ambulanceInput) {
                ambulanceInput.value = defaultAmbulanceCountForm2;
            }

            // เพิ่ม event listener ให้ระดับรถเพื่อให้ราคาปรับตามเมื่อเปลี่ยน
            document.querySelectorAll('input[name="level"]').forEach(level => {
                level.addEventListener("change", calculatePrice);
            });

            // คำนวณราคาตั้งแต่โหลดหน้าเว็บ
            calculatePrice();
        });
        // ฟังก์ชันเปลี่ยนฟอร์ม
        function switchForm() {
            const selectedForm = document.getElementById('formSelect').value;
            const forms = document.querySelectorAll('.form-container');

            forms.forEach(form => {
                form.style.display = "none"; // ซ่อนฟอร์มทั้งหมด
                resetFormValues(form); // รีเซ็ตค่าของทุกฟอร์มที่ถูกซ่อน
            });

            // แสดงฟอร์มที่เลือก
            const selectedFormElement = document.getElementById(selectedForm);
            selectedFormElement.style.display = "block";
            // document.getElementById(selectedForm).style.display = "block"; // แสดงฟอร์มที่เลือก

            // รีเซ็ตค่าของ input ต่าง ๆ
            resetFormValues(selectedFormElement);

            // รีเซ็ตค่าบริการเป็น 0 ของทั้ง form1 และ form2
            const priceDisplays1 = document.getElementById("form1").querySelectorAll('.price-display');
            priceDisplays1.forEach(display => {
                display.innerText = "ราคาค่าบริการ: 0 บาท"; // ตั้งค่าบริการเป็น 0
            });

            const priceDisplays2 = document.getElementById("form2").querySelectorAll('.price-display');
            priceDisplays2.forEach(display => {
                display.innerText = "ราคาค่าบริการ: 0 บาท"; // ตั้งค่าบริการเป็น 0
            });

            const calculatedPrices1 = document.getElementById("form1").querySelectorAll('.calculated-price');
            calculatedPrices1.forEach(input => {
                input.value = 0; // ตั้งค่าบริการเป็น 0 ใน input ที่ใช้เก็บราคาคำนวณ
            });

            const calculatedPrices2 = document.getElementById("form2").querySelectorAll('.calculated-price');
            calculatedPrices2.forEach(input => {
                input.value = 0; // ตั้งค่าบริการเป็น 0 ใน input ที่ใช้เก็บราคาคำนวณ
            });


            // คำนวณราคาทันทีเมื่อเปลี่ยนประเภทการจอง
            calculatePrice();
        }

        // ผูกฟังก์ชันกับการเลือกจังหวัดและประเภทการจอง
        document.getElementById("province1").addEventListener("change", calculatePrice);
        document.getElementById("province2").addEventListener("change", calculatePrice);
        document.getElementById("formSelect").addEventListener("change", () => {
            switchForm();
            calculatePrice(); // เรียกคำนวณราคาเมื่อเปลี่ยนประเภท
        });

        // เรียกใช้งานเมื่อหน้าโหลดครั้งแรก
        switchForm();

        // ฟังก์ชันรีเซ็ตค่าของฟอร์ม
        function resetFormValues(form) {
            // รีเซ็ตค่าของทุก input ในฟอร์มที่เลือก
            const inputElements = form.querySelectorAll('input, select');

            inputElements.forEach(input => {
                if (input.type === 'radio' || input.type === 'checkbox') {
                    input.checked = false; // รีเซ็ตค่าของ radio หรือ checkbox
                } else if (input.type === 'number') {
                    input.value = ''; // รีเซ็ตค่าของ input ที่เป็นตัวเลข
                } else if (input.type === 'select-one') {
                    input.selectedIndex = 0; // รีเซ็ตค่าของ select เป็นตัวเลือกแรก
                }
            });

            // รีเซ็ตค่าของพยาบาลและรถพยาบาลใน form2
            if (form.id === "form2") {
                document.getElementById("nurse_number").value = defaultNurseCountForm2;
                document.getElementById("ambulance_number").value = defaultAmbulanceCountForm2;
            }
        }
    </script>

    <script>
        //ตรวจการรับค่าจังหวัดและเก็บเป็นภูมิภาค
        function checkRegion() {
            const provinces = {
                "ภาคเหนือ": ["เชียงใหม่", "เชียงราย", "ลำปาง", "ลำพูน", "แพร่", "น่าน", "พะเยา", "แม่ฮ่องสอน", "อุตรดิตถ์", "สุโขทัย", "พิษณุโลก", "ตาก", "เพชรบูรณ์", "นครสวรรค์", "กำแพงเพชร", "พิจิตร", "อุทัยธานี"],
                "ภาคกลาง": ["กรุงเทพมหานคร", "สมุทรปราการ", "นนทบุรี", "ปทุมธานี", "พระนครศรีอยุธยา", "สระบุรี", "ลพบุรี", "อ่างทอง", "ชัยนาท", "สิงห์บุรี", "นครนายก", "นครปฐม", "สุพรรณบุรี", "สมุทรสาคร", "สมุทรสงคราม", "เพชรบุรี", "ประจวบคีรีขันธ์", "ราชบุรี", "กาญจนบุรี",
                    // รวมภาคตะวันออก
                    "ชลบุรี", "ระยอง", "จันทบุรี", "ตราด", "ฉะเชิงเทรา", "ปราจีนบุรี", "สระแก้ว"
                ],
                "ภาคตะวันออกเฉียงเหนือ": ["ขอนแก่น", "นครราชสีมา", "อุดรธานี", "อุบลราชธานี", "หนองคาย", "มหาสารคาม", "ร้อยเอ็ด", "สุรินทร์", "บุรีรัมย์", "ศรีสะเกษ", "กาฬสินธุ์", "ชัยภูมิ", "ยโสธร", "สกลนคร", "หนองบัวลำภู", "นครพนม", "บึงกาฬ", "มุกดาหาร", "อำนาจเจริญ"],
                "ภาคใต้": ["ภูเก็ต", "สุราษฎร์ธานี", "สงขลา", "นราธิวาส", "ยะลา", "ปัตตานี", "พังงา", "กระบี่", "ตรัง", "นครศรีธรรมราช", "พัทลุง", "ชุมพร", "ระนอง", "สตูล"]
            };

            let province = document.getElementById("province").value;
            let regionInput = document.querySelector(".region input");

            let region = "ไม่พบภูมิภาค";
            for (let key in provinces) {
                if (provinces[key].includes(province)) {
                    region = key;
                    break;
                }
            }
            regionInput.value = region;
        }
    </script>

</body>

</html>