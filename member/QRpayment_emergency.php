<?php
include('username.php'); // เชื่อมต่อฐานข้อมูล

// รับค่า order_id จาก URL
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

$ids = $_GET['order_id'];
$sql = "SELECT * 
                FROM order_emergency_case
                WHERE order_emergency_case_id = '$ids'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
?>

<html lang="en">
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_QR_payment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>Document</title>
    <!-- <script src="javascrip_member/QRpayment.js" defer></script> -->
</head>

<body>
    <div class="top-navbar">
        <nav class="nav-links">
            <div><a href="order_emergency.php">ชำระเงินเคสฉุกเฉิน</a></div>
            <div><a href="contact.html">ติดต่อเรา</a></div>
            <div class="dropdown">
                <img src="image/user.png" alt="Logo" class="nav-logo">
                <div class="dropdown-menu">
                    <a href="profile.html">โปรไฟล์</a>
                    <a href="order-history.html">ประวัติคำสั่งซื้อ</a>
                    <a href="claim.php">เคลมสินค้า</a>
<<<<<<< HEAD
                    <a href="logout.html">ออกจากระบบ</a>
=======
                    <a href="../logout.php">ออกจากระบบ</a>
>>>>>>> b8baf0e802209a1a4d139e119c1a87fe62d73857
                </div>
            </div>
            <a href="index.php">
                <img src="image/united-states-of-america.png" alt="Logo" class="nav-logo">
            </a>
        </nav>
    </div>
    <!-- Navbar ชั้นล่าง -->
    <div class="main-navbar">
        <nav class="nav-links">
            <div><a href="index.php">หน้าแรก</a></div>
            <div><a href="reservation_car.php">จองคิวรถ</a></div>
            <a href="index.php">
                <img src="image/Logo.png" alt="Logo" class="nav-logo1">
            </a>
            <div><a href="shopping.php">ซื้อ/เช่าอุปกรณ์ทางการแพทย์</a></div>
        </nav>

        <div class="cart-icon">
<<<<<<< HEAD
            <a href="cart.html">
=======
            <a href="cart.php">
>>>>>>> b8baf0e802209a1a4d139e119c1a87fe62d73857
                <i class="fas fa-shopping-cart"></i>
            </a>
        </div>
    </div>

    <section class="QRcode">
        <img src="image/QRcode.jpeg" alt="" class="qr-preview" id="qr-preview"><br>
        <p>ยอดค้างชำระทั้งหมด : ฿<?php echo number_format($row['order_emergency_case_price'], 2); ?></p>
        <br><br>
        <div class="bottom-row">
            <p>แนบหลักฐานยืนยัน</p>
            <button class="upload-btn" id="upload-btn">อัพโหลด</button>
        </div><br>
        <div class="QR-buttons">
            <!-- กดปุ่มยืนยัน -->
            <button class="confirm" id="confirm-btn">ยืนยัน</button>
        </div>
    </section>

    <script>
        // เมื่อกดปุ่ม "ยืนยัน"
        document.getElementById("confirm-btn").addEventListener("click", function() {
            let orderId = "<?php echo $order_id; ?>"; // รับค่า order_id จาก PHP

            // ส่งข้อมูลไปยัง PHP ด้วย AJAX
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "update_payment_status.php", true); // ส่งข้อมูลไปที่ไฟล์ PHP ที่ทำการอัปเดตฐานข้อมูล
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            // ข้อมูลที่จะส่ง
            let params = "order_id=" + orderId;

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // ตรวจสอบผลลัพธ์จากเซิร์ฟเวอร์
                    let response = xhr.responseText;
                    if (response === "success") {
                        window.location.href = "success_payment.html"; // ไปหน้า success_payment.html โดยตรง
                    } else {
                        // สามารถแสดงข้อความบนหน้าเว็บได้ เช่น
                        document.getElementById("error-message").innerText = "เกิดข้อผิดพลาดในการชำระเงิน";
                    }
                }
            };

            // ส่งข้อมูลไปที่เซิร์ฟเวอร์
            xhr.send(params);
        });
    </script>

    <div id="error-message" style="color: red;"></div>
</body>

</html>