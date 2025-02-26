<?php
include("username.php");

?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_success_claims.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>Document</title>
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
                    <a href="logout.html">ออกจากระบบ</a>
                </div>
            </div>
            <a href="index.html">
                <img src="image/united-states-of-america.png" alt="Logo" class="nav-logo">
            </a>
        </nav>
    </div>

    <!-- Navbar ชั้นล่าง -->
    <div class="main-navbar">
        <nav class="nav-links">
            <div><a href="index.html">หน้าแรก</a></div>
            <div><a href="reservation_car.php">จองคิวรถ</a></div>
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

    <div class="search-section">
        <div class="search-container">
            <form method="GET" action="shopping.php">
                <input type="text" name="q" placeholder="ค้นหา..." class="search-input" value="">
                <button class="search-button">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

</body>

</html>