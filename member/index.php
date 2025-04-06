<?php
<<<<<<< HEAD
session_start();

// ถ้าไม่ได้ล็อกอิน ให้ redirect กลับไปหน้า login
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login_form.php");
//     exit();
// }

// $user_id = $_SESSION['user_id']; // ดึงค่า user_id จาก session
=======

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

>>>>>>> b8baf0e802209a1a4d139e119c1a87fe62d73857
?>

<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_form_ambulance.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>Document</title>
    <script src="javascrip_member/index.js" defer></script>
</head>
<div class="top-navbar">
    <nav class="nav-links">
<<<<<<< HEAD
=======
        
>>>>>>> b8baf0e802209a1a4d139e119c1a87fe62d73857
        <div><a href="order_emergency.php">ชำระเงินเคสฉุกเฉิน</a></div>
        <div><a href="contact.html">ติดต่อเรา</a></div>
        <div class="dropdown">
            <img src="image/user.png" alt="Logo" class="nav-logo">
            <div class="dropdown-menu">
                <a href="profile.html">โปรไฟล์</a>
                <a href="order-history.html">ประวัติคำสั่งซื้อ</a>
                <a href="claim.php?user_id=<?php echo $user_id; ?>">เคลมสินค้า</a>
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
        <div><a href="index.php" style="color: #E88B71;">หน้าแรก</a></div>
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

<body>

<<<<<<< HEAD
=======
    <div class="container">
        <h1>ยินดีต้อนรับ <?php echo $_SESSION['user_email']; ?></h1>
    </div>

>>>>>>> b8baf0e802209a1a4d139e119c1a87fe62d73857
</body>

</html>