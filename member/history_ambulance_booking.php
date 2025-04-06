<?php
session_start();
include 'username.php';

if (empty($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

$member_id = $_SESSION['user_id'];

$sql = "SELECT 
            ambulance_booking.ambulance_booking_location,
            ambulance_booking.ambulance_booking_hospital_waypoint,
            ambulance_booking.ambulance_booking_date,
            ambulance_booking.ambulance_booking_start_time,
            ambulance_booking.ambulance_booking_finish_time,
            ambulance_booking.ambulance_booking_price,
            member.member_firstname,
            member.member_lastname,
            member.member_phone,
            ambulance.ambulance_plate
        FROM ambulance_booking
        JOIN member ON ambulance_booking.member_id = member.member_id
        JOIN ambulance ON ambulance_booking.ambulance_id = ambulance.ambulance_id
        WHERE ambulance_booking.member_id = ?
        ORDER BY ambulance_booking.ambulance_booking_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ประวัติคำสั่งซื้อ</title>
    <link rel="stylesheet" href="css/style_history.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <a href="history.php">ประวัติคำสั่งซื้อ</a>
                    <a href="history_ambulance_booking.php">ประวัติการจองรถ</a>
                    <a href="claim.php">เคลมสินค้า</a>
                    <a href="../logout.php">ออกจากระบบ</a>
                </div>
            </div>
            <a href="index.php">
                <img src="image/united-states-of-america.png" alt="Logo" class="nav-logo">
            </a>
        </nav>
    </div>

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
            <a href="cart.php">
                <i class="fas fa-shopping-cart"></i>
            </a>
        </div>
    </div>

    <div class="custom-dropdown">
        <select class="dropdown-select" onchange="window.location.href=this.value;">
            <option value="" selected hidden>เลือกประเภทการจอง</option>
            <option value="history_ambulance_booking.php">จองรถสำหรับผู้ป่วย</option>
            <option value="history_event_booking.php">จองรถสำหรับงาน Event</option>
        </select>
    </div>

    <!-- HTML แสดงตาราง -->
    <div class="table-responsive mt-4">
        <table class="table table-bordered table-striped">
            <thead class="table-dark text-center">
                <tr>
                    <th>เส้นทาง</th>
                    <th>ทะเบียนรถ</th>
                    <th>วันเวลาจอง</th>
                    <th>ค่าบริการ (บาท)</th>
                    <th>ชื่อผู้จอง</th>
                    <th>เบอร์โทร</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['ambulance_booking_location']) ?> - <?= htmlspecialchars($row['ambulance_booking_hospital_waypoint']) ?></td>
                        <td><?= htmlspecialchars($row['ambulance_plate']) ?></td>
                        <td>
                            <?= htmlspecialchars($row['ambulance_booking_date']) ?><br>
                            <?= htmlspecialchars($row['ambulance_booking_start_time']) ?> - <?= htmlspecialchars($row['ambulance_booking_finish_time']) ?>
                        </td>
                        <td class="text-end"><?= number_format($row['ambulance_booking_price'], 2) ?></td>
                        <td><?= htmlspecialchars($row['member_firstname'] . ' ' . $row['member_lastname']) ?></td>
                        <td><?= htmlspecialchars($row['member_phone']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>