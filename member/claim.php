<?php
<<<<<<< HEAD
session_start(); // เริ่มต้น session เพื่อใช้ข้อมูลที่เก็บไว้ใน session ในการเข้าถึงข้อมูลสมาชิก
require("username.php"); // รวมไฟล์ที่เชื่อมต่อกับฐานข้อมูลเพื่อใช้ในการ query ข้อมูล
=======
//-----------Session and Login-------------
session_start();
include 'username.php';

// ถ้าไม่ได้ล็อกอิน ให้ redirect กลับไปหน้า login
if (empty($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

// เรียก member_id จาก session มาใช้
$member_id = $_SESSION['user_id'];
//------------------------------------------

>>>>>>> b8baf0e802209a1a4d139e119c1a87fe62d73857
// SQL Query เพื่อดึงข้อมูล
$sql = "SELECT `order_equipment`.order_equipment_id, `order_equipment`.member_id, equipment.equipment_name, equipment.equipment_image, claim.claim_approve
        FROM `order_equipment`
        JOIN equipment ON `order_equipment`.equipment_id = equipment.equipment_id
        LEFT JOIN claim ON `order_equipment`.equipment_id = claim.equipment_id AND claim.claim_approve = 'รออนุมัติ'
<<<<<<< HEAD
        WHERE `order_equipment`.member_id = 1"; // ดึงข้อมูลคำสั่งซื้อจาก member_id ที่ระบุ (สามารถเปลี่ยนเป็น $_SESSION['member_id'] เมื่อทำการ login สำเร็จ)
=======
        WHERE `order_equipment`.member_id = $member_id"; // ดึงข้อมูลคำสั่งซื้อจาก member_id ที่ระบุ (สามารถเปลี่ยนเป็น $_SESSION['member_id'] เมื่อทำการ login สำเร็จ)
>>>>>>> b8baf0e802209a1a4d139e119c1a87fe62d73857
// ใช้ LEFT JOIN เพื่อตรวจสอบว่ามีเคลมที่ยังไม่ได้อนุมัติหรือไม่ โดยจะใช้ claim_status = 'รออนุมัติ' เพื่อดูสถานะ
// ประมวลผลการ Query
$result = $conn->query($sql); // ประมวลผลคำสั่ง SQL และเก็บผลลัพธ์ในตัวแปร $result

// ปิดการเชื่อมต่อ
$conn->close(); // ปิดการเชื่อมต่อกับฐานข้อมูลหลังจากใช้งานเสร็จ
?>

<html lang="th">

<head>
    <meta charset="UTF-8"> <!-- กำหนดการเข้ารหัสข้อมูลให้เป็น UTF-8 สำหรับภาษาไทย -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- กำหนดการแสดงผลให้รองรับเบราว์เซอร์รุ่นเก่า -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- กำหนดการตอบสนองต่ออุปกรณ์มือถือ -->
    <link rel="stylesheet" href="css/style_claim.css"> <!-- ลิงก์ไปยังไฟล์ CSS สำหรับการจัดรูปแบบ -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"> <!-- ใช้ไอคอนจาก Font Awesome -->
    <title>Claim</title> <!-- กำหนดชื่อหน้าเว็บ -->
</head>
<div class="top-navbar">
    <nav class="nav-links">
        <div><a href="order_emergency.php">ชำระเงินเคสฉุกเฉิน</a></div>
        <div><a href="contact.html">ติดต่อเรา</a></div> <!-- ลิงก์ไปยังหน้าติดต่อ -->
        <div class="dropdown">
            <img src="image/user.png" alt="Logo" class="nav-logo"> <!-- แสดงภาพไอคอนของผู้ใช้ -->
            <div class="dropdown-menu"> <!-- เมนูที่แสดงเมื่อคลิกที่ไอคอนผู้ใช้ -->
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
        <div><a href="index.php">หน้าแรก</a></div> <!-- ลิงก์ไปยังหน้าแรก -->
        <div><a href="reservation_car.php">จองคิวรถ</a></div> <!-- ลิงก์ไปยังหน้าจองคิวรถ -->
        <a href="index.php">
            <img src="image/Logo.png" alt="Logo" class="nav-logo1">
        </a>
        <div><a href="shopping.php">ซื้อ/เช่าอุปกรณ์ทางการแพทย์</a></div> <!-- ลิงก์ไปยังหน้าซื้อหรือเช่าอุปกรณ์ -->
    </nav>
</div>

<body>

    <h1>เคลม/ต่ออายุการใช้งาน</h1> <!-- หัวข้อของหน้าเว็บ -->

    <div class="product-container">

        <?php while ($row = mysqli_fetch_assoc($result)) { ?> <!-- วนลูปเพื่อแสดงข้อมูลที่ได้จากการ Query -->
            <div class="product"> <!-- แสดงข้อมูลของแต่ละอุปกรณ์ -->
                <p hidden>id: <?php echo $row["order_equipment_id"]; ?></p> <!-- เก็บข้อมูล order_id ไว้ที่ไม่แสดงผล -->
                <img src="image/<?php echo $row["equipment_image"]; ?>" alt="product"> <!-- แสดงภาพของอุปกรณ์ -->
                <h2> <?php echo $row["equipment_name"]; ?> </h2> <!-- แสดงชื่อของอุปกรณ์ -->

                <?php if ($row["claim_approve"] == 'รออนุมัติ') { ?> <!-- ถ้าสถานะของเคลมเป็น "รออนุมัติ" -->
                    <button disabled>รออนุมัติ</button> <!-- ปุ่มจะถูกปิดการใช้งาน -->
                <?php } else { ?>
                    <a href="submit_claim.php?equipment_name=<?php echo $row['equipment_name'] ?>"><button>เคลม/ต่ออายุการใช้งาน</button></a> <!-- หากสถานะไม่ใช่ "รออนุมัติ" จะแสดงปุ่มเพื่อไปยังหน้าเคลม -->
                <?php } ?>
            </div>
        <?php } ?>

    </div>

</body>

</html>