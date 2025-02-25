<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_form_event.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>จองคิวงาน Event</title>
    <script src="javascrip_member/form_event.js" defer></script>
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
                    <a href="logout.html">ออกจากระบบ</a>
                </div>
            </div>
            <a href="index.html">
                <img src="image/united-states-of-america.png" alt="Logo" class="nav-logo">
            </a>
        </nav>
    </div>

    <div class="main-navbar">
        <nav class="nav-links">
            <div><a href="index.html">หน้าแรก</a></div>
            <div><a href="reservation_car.php" style="color: #FFB898">จองคิวรถ</a></div>
            <a href="index.html">
                <img src="image/Logo.png" alt="Logo" class="nav-logo1">
            </a>
            <div><a href="shopping.php">ซื้อ/เช่าอุปกรณ์ทางการแพทย์</a></div>
        </nav>

        <div class="cart-icon">
            <a href="cart.html">
                <i class="fas fa-shopping-cart"></i>
            </a>
        </div>
    </div>

    <div class="form-title">
        <h3>จองคิวงาน Event</h3>
    </div>

    <div class="form-container">
        <div class="current-date-time">
            <span class="car-info">รถคันที่ <span id="car-number"></span> รถระดับ 2</span>
            <span class="work-date">วันปฏิบัติงาน <span id="selected-date"> 25/01/2568</span></span>
            <span class="work-time">เวลา <span id="current-time">09:00-11:00 น. </span></span>
        </div>

        <form action="some_other_page.html" method="POST">
            <div class="form-group">
                <label for="province">จังหวัด</label>
                <select id="province" name="province" required
                    style="width: 25%; padding: 8px; border: 1px solid #ccc; border-radius: 15px;">
                    <option value="" selected hidden>เลือกจังหวัด</option>
                    <option value="province1">กรุงเทพมหานคร</option>
                    <option value="province2">กระบี่</option>
                    <option value="province3">กาญจนบุรี</option>
                    <option value="province4">กาฬสินธุ์</option>
                    <option value="province5">กำแพงเพชร</option>
                    <option value="province6">ขอนแก่น</option>
                    <option value="province7">จันทบุรี</option>
                    <option value="province8">ฉะเชิงเทรา</option>
                    <option value="province9">ชลบุรี</option>
                    <option value="province10">ชัยนาท</option>
                    <option value="province11">ชัยภูมิ</option>
                    <option value="province12">ชุมพร</option>
                    <option value="province13">เชียงราย</option>
                    <option value="province14">เชียงใหม่</option>
                    <option value="province15">ตรัง</option>
                    <option value="province16">ตราด</option>
                    <option value="province17">ตาก</option>
                    <option value="province18">นครนายก</option>
                    <option value="province19">นครปฐม</option>
                    <option value="province20">นครพนม</option>
                    <option value="province21">นครราชสีมา</option>
                    <option value="province22">นครศรีธรรมราช</option>
                    <option value="province23">นครสวรรค์</option>
                    <option value="province24">นนทบุรี</option>
                    <option value="province25">นราธิวาส</option>
                    <option value="province26">น่าน</option>
                    <option value="province27">บึงกาฬ</option>
                    <option value="province28">บุรีรัมย์</option>
                    <option value="province29">ปทุมธานี</option>
                    <option value="province30">ประจวบคีรีขันธ์</option>
                    <option value="province31">ปราจีนบุรี</option>
                    <option value="province32">ปัตตานี</option>
                    <option value="province33">พะเยา</option>
                    <option value="province34">พระนครศรีอยุธยา</option>
                    <option value="province35">พังงา</option>
                    <option value="province36">พัทลุง</option>
                    <option value="province37">พิจิตร</option>
                    <option value="province38">พิษณุโลก</option>
                    <option value="province39">เพชรบุรี</option>
                    <option value="province40">เพชรบูรณ์</option>
                    <option value="province41">แพร่</option>
                    <option value="province42">ภูเก็ต</option>
                    <option value="province43">มหาสารคาม</option>
                    <option value="province44">มุกดาหาร</option>
                    <option value="province45">แม่ฮ่องสอน</option>
                    <option value="province46">ยโสธร</option>
                    <option value="province47">ยะลา</option>
                    <option value="province48">ร้อยเอ็ด</option>
                    <option value="province49">ระนอง</option>
                    <option value="province50">ระยอง</option>
                    <option value="province51">ราชบุรี</option>
                    <option value="province52">ลพบุรี</option>
                    <option value="province53">ลำปาง</option>
                    <option value="province54">ลำพูน</option>
                    <option value="province55">เลย</option>
                    <option value="province56">ศรีสะเกษ</option>
                    <option value="province57">สกลนคร</option>
                    <option value="province58">สงขลา</option>
                    <option value="province59">สตูล</option>
                    <option value="province60">สมุทรปราการ</option>
                    <option value="province61">สมุทรสงคราม</option>
                    <option value="province62">สมุทรสาคร</option>
                    <option value="province63">สระแก้ว</option>
                    <option value="province64">สระบุรี</option>
                    <option value="province65">สิงห์บุรี</option>
                    <option value="province66">สุโขทัย</option>
                    <option value="province67">สุพรรณบุรี</option>
                    <option value="province68">สุราษฎร์ธานี</option>
                    <option value="province69">สุรินทร์</option>
                    <option value="province70">หนองคาย</option>
                    <option value="province71">หนองบัวลำภู</option>
                    <option value="province72">อ่างทอง</option>
                    <option value="province73">อำนาจเจริญ</option>
                    <option value="province74">อุดรธานี</option>
                    <option value="province75">อุตรดิตถ์</option>
                    <option value="province76">อุทัยธานี</option>
                    <option value="province77">อุบลราชธานี</option>
                </select>
            </div>
            <div class="form-group">
                <label for="place_event_detail">รายละเอียดสถานที่</label>
                <textarea id="place_event_detail" name="place_event_detail" rows="4" cols="50" required></textarea>
            </div>

            <div class="form-group">
                <label for="type">ประเภทงาน</label>
                <select id="type" name="type" required
                    style="width: 30%; padding: 8px; border: 1px solid #ccc; border-radius: 15px;">
                    <option value="" selected hidden>เลือกประเภทงาน</option>
                    <option value="type1">กีฬาสีและการแข่งขัน</option>
                    <option value="type2">งานชุมนุม</option>
                    <option value="type3">งานพิธีการ</option>
                    <option value="type4">อุตสาหกรรมก่อสร้าง</option>
                    <option value="type5">กิจกรรมเด็กหรือผู้สูงวัย</option>
                    <option value="type6">คัดกรองโรค</option>
                </select>
            </div>
            <div class="form-group">
                <label for="nurse_number">จำนวนพยาบาล</label>
                <input type="number" id="nurse_number" name="nurse_number" required min="1" step="1" value="1"
                    style="text-align: center; width: 100px;" oninput="validateNumber(event) "> คน/คัน
            </div>

            <div class="form-group">
                <label for="ambulance_number">จำนวนรถพยาบาล</label>
                <input type="number" id="ambulance_number" name="ambulance_number" required min="1" step="1" value="1"
                    style="text-align: center; width: 100px;" oninput="validateNumber(event)"> คัน
            </div>

            <div class="form-group">
                <label for="payment-method">วิธีการชำระเงิน</label>
                <div class="payment-options">
                    <button type="button" id="payment-qr" class="payment-button">QR Promptpay</button>
                    <button type="button" id="payment-credit" class="payment-button">บัตรเครดิต</button>
                </div>
            </div>
            <div class="form-submit">
                <button type="button" id="cancel-button" class="cancel-button"
                    style="background-color: #F8E6DE;">ยกเลิก</button>
                <button type="submit" style="background-color: #FFB898;" id="submit-button">ยืนยัน</button>
            </div>
        </form>
    </div>
</body>

</html>