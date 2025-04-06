<?php
session_start();
include 'username.php';

if (empty($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

$member_id = $_SESSION['user_id'];

if (!isset($_GET['booking_ids'])) {
    echo "ไม่พบรหัสคำสั่งซื้อ";
    exit();
}

$booking_ids = explode(',', $_GET['booking_ids']);
$booking_ids = array_map('intval', $booking_ids);
$placeholders = implode(',', array_fill(0, count($booking_ids), '?'));

$sql = "SELECT 
            ambulance_booking.ambulance_booking_id,
            ambulance_booking.ambulance_booking_location,
            ambulance_booking.ambulance_booking_hospital_waypoint,
            ambulance_booking.ambulance_booking_date,
            ambulance_booking.ambulance_booking_start_time,
            ambulance_booking.ambulance_booking_finish_time,
            ambulance_booking.ambulance_booking_price,
            member.member_firstname,
            member.member_lastname,
            member.member_address,
            member.member_phone,
            ambulance.ambulance_plate
        FROM ambulance_booking
        JOIN member ON ambulance_booking.member_id = member.member_id
        JOIN ambulance ON ambulance_booking.ambulance_id = ambulance.ambulance_id
        WHERE ambulance_booking.member_id = ? AND ambulance_booking.ambulance_booking_id IN ($placeholders)
        ORDER BY ambulance_booking.ambulance_booking_date DESC";

$stmt = $conn->prepare($sql);
$types = str_repeat('i', count($booking_ids) + 1); // Include member_id as the first parameter
$params = array_merge([$member_id], $booking_ids);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<style>
    body {
        font-family: Tahoma, sans-serif;
        padding: 5px;
        background: #fff;
    }

    .receipt {
        max-width: 900px;
        margin: auto;
        padding: 30px;
    }

    .header,
    .footer {
        text-align: center;
    }

    .company-info {
        float: right;
        text-align: right;
    }

    .logo {
        width: 120px;
    }

    .clearfix::after {
        content: "";
        display: table;
        clear: both;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 5px;
        font-size: 12px;
    }

    th,
    td {
        padding: 10px;
        text-align: center;
        font-size: 12px;
        border-right: 1px solid #000;
        /* เส้นคั่นแนวตั้ง */
    }

    /* ปรับเส้นคั่นแนวตั้งสำหรับคอลัมน์แรก */
    th:first-child,
    td:first-child {
        border-left: 1px solid #000;
        /* เส้นคั่นทางด้านซ้ายของคอลัมน์แรก */
    }

    th {
        border-top: 2px solid #000;
        /* เส้นคั่นด้านบนหัวตาราง */
        border-bottom: 2px solid #000;
        /* เส้นคั่นด้านล่างหัวตาราง */
    }

    td {
        border-bottom: none;
        /* ลบเส้นคั่นแนวนอนในแถวข้อมูล */
    }

    tfoot td {
        border-top: 2px solid #000;
        /* เส้นคั่นด้านบนของตารางรวม */
        border-bottom: 2px solid #000;
        /* เพิ่มเส้นคั่นด้านล่างในแถวสุดท้าย */
    }

    .no-border {
        border: none;
    }

    .signature-section {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
    }

    .signature {
        width: 40%;
        text-align: center;
    }

    .signature-line {
        margin-top: 60px;
        border-top: 1px dotted #000;
    }

    /* ซ่อนข้อมูลที่ไม่ต้องการแสดงตอนพิมพ์ */
    @media print {
        body {
            visibility: hidden;
        }

        .receipt {
            visibility: visible;
            position: absolute;
            top: 0;
        }

        .footer {
            display: none;
        }

        /* ซ่อน URL หรือข้อความไม่ต้องการแสดง */
        footer {
            display: none;
        }

        /* ซ่อนข้อความหรือคำสั่งที่เกี่ยวข้องกับการพิมพ์ที่ขอบล่างซ้าย */
        @page {
            margin: 0;
        }

        .receipt {
            margin-bottom: 0;
        }
    }
</style>
</head>
<meta charset="UTF-8">
<title>ใบเสร็จรับเงิน</title>
<link rel="stylesheet" href="css/style_bill.css">
<style>
    /* Your existing CSS code */
</style>
</head>

<body onload="window.print()">
    <div class="receipt">
        <div class="clearfix">
            <img src="image/Logo.png" alt="Logo" class="logo">
            <div class="company-info">
                <strong>บริษัท Sky Medical Service จำกัด</strong><br>
                123 ถนนสุขภาพดี แขวงใจดี เขตปลอดภัย กรุงเทพฯ 10200<br>
                โทร: 097-20-30-555| อีเมล: skymedicalservice@gmail.com
            </div>
        </div>
        <hr>
        <div class="header">
            <h2>ใบเสร็จค่าจองรถรับส่งผู้ป่วย / Receipt Ambulance Booking</h2>
            <p style="margin-top: -10px;">(ต้นฉบับ / Original)</p>
        </div>

        <!-- แสดงชื่อของลูกค้าและวันที่แยกจากตาราง -->
        <?php if (count($orders) > 0): ?>
            <?php $first = $orders[0]; ?>
            <div class="detail">
                <p><strong>เลขที่ใบเสร็จ / Receipt No.:</strong> <?= htmlspecialchars($first['ambulance_booking_id']) ?></p>
                <p><strong>ลูกค้า / Customer:</strong> <?= htmlspecialchars($first['member_firstname'] . ' ' . $first['member_lastname']) ?></p>
                <p><strong>เบอร์โทร / Phone:</strong> <?= htmlspecialchars($first['member_phone']) ?></p>
                <p><strong>วันที่ / Date:</strong> <?= date("d/m/Y") ?></p>
                <p><strong>ออกโดย / Issuer:</strong> ระบบอัตโนมัติ</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ลำดับ<br>No.</th>
                        <th>เส้นทาง<br>Route</th>
                        <th>เลขทะเบียนรถ<br>Vehicle registration number</th>
                        <th>วันเวลาเดินทาง<br>Travel date and time</th>
                        <th>ราคารวม<br>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $total = 0;
                    foreach ($orders as $order):
                        $total += $order['ambulance_booking_price'];
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($order['ambulance_booking_location']) ?> <strong>ไป</strong> <?= htmlspecialchars($order['ambulance_booking_hospital_waypoint']) ?></td>
                            <td><?= htmlspecialchars($order['ambulance_plate']) ?></td>
                            <td><?= htmlspecialchars($order['ambulance_booking_date']) ?><br><?= htmlspecialchars($order['ambulance_booking_start_time']) ?> - <?= htmlspecialchars($order['ambulance_booking_finish_time']) ?></td>
                            <td class="text-end"><?= number_format($order['ambulance_booking_price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;"><strong>รวมทั้งสิ้น (บาท)</strong></td>
                        <td><strong><?= number_format($total, 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        <?php else: ?>
            <p><strong>ไม่พบข้อมูลใบเสร็จ</strong></p>
        <?php endif; ?>

        <div class="signature-section">
            <div class="signature">
                <div class="signature-line"></div>
                ผู้รับสินค้า / Receiver
            </div>
            <div class="signature">
                <div class="signature-line"></div>
                ผู้จัดทำ / Issuer
            </div>
        </div>

        <div class="footer">
            <p>ขอบคุณที่ใช้บริการ</p>
        </div>
    </div>
</body>

</html>