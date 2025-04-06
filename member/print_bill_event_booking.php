<?php
session_start();
include 'username.php';

if (empty($_SESSION['logged_in'])) {
    header("Location: ../login.php");
    exit();
}

$member_id = $_SESSION['user_id'];

if (!isset($_GET['event_ids'])) {
    echo "ไม่พบรหัสคำสั่งซื้อ";
    exit();
}

$event_ids = explode(',', $_GET['event_ids']);
$event_ids = array_map('intval', $event_ids);
$placeholders = implode(',', array_fill(0, count($event_ids), '?'));

$sql = "SELECT 
            event_booking.event_booking_id,
            event_booking.event_booking_type,
            event_booking.event_booking_location,
            event_booking.event_booking_province,
            event_booking.event_booking_date,
            event_booking.event_booking_start_time,
            event_booking.event_booking_finish_time,
            event_booking.event_booking_price,
            member.member_firstname,
            member.member_lastname,
            member.member_phone,
            ambulance.ambulance_plate
        FROM event_booking
        JOIN member ON event_booking.member_id = member.member_id
        JOIN ambulance ON event_booking.ambulance_id = ambulance.ambulance_id
        WHERE event_booking.member_id = ? AND event_booking.event_booking_id IN ($placeholders)
        ORDER BY event_booking.event_booking_date DESC";

// ตัวแปรสำหรับบีบอัดการส่งค่า
$stmt = $conn->prepare($sql);

// กำหนดประเภทของตัวแปรที่ส่งไป (1 สำหรับ integer ของ member_id และจำนวนที่เท่ากับ event_ids)
$types = str_repeat('i', count($event_ids) + 1); // เพิ่ม 1 สำหรับ $member_id
$params = array_merge([$member_id], $event_ids); // รวมค่า $member_id และ event_ids

// ทำการ binding parameter
$stmt->bind_param($types, ...$params); // ใช้ ...$params เพื่อส่งผ่านหลายๆ ค่า

// Execute statement
$stmt->execute();
$result = $stmt->get_result();


$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ใบเสร็จรับเงิน</title>
    <link rel="stylesheet" href="css/style_bill.css">
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
            <h2>ใบเสร็จจองรถสำหรับงาน Event / Receipt Event Booking</h2>
            <p style="margin-top: -10px;">(ต้นฉบับ / Original)</p>
        </div>

        <?php if (count($orders) > 0): ?>
            <?php $first = $orders[0]; ?>
            <div class="detail">
                <p><strong>เลขที่ใบเสร็จ / Receipt No.:</strong> <?= htmlspecialchars($first['event_booking_id']) ?></p>
                <p><strong>ลูกค้า / Customer:</strong> <?= htmlspecialchars($first['member_firstname'] . ' ' . $first['member_lastname']) ?></p>
                <p><strong>เบอร์โทร / Phone:</strong> <?= htmlspecialchars($first['member_phone']) ?></p>
                <p><strong>วันที่ / Date:</strong> <?= date("d/m/Y") ?></p>
                <p><strong>ออกโดย / Issuer:</strong> ระบบอัตโนมัติ</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ลำดับ<br>No.</th>
                        <th>สถานที่จัด Event<br>Event location</th>
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
                        $total += $order['event_booking_price'];
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($order['event_booking_location'])." ". htmlspecialchars($order['event_booking_province']) ?></td>
                            <td><?= htmlspecialchars($order['ambulance_plate']) ?></td>
                            <td><?= htmlspecialchars($order['event_booking_date']) ?><br><?= htmlspecialchars($order['event_booking_start_time']) ?> - <?= htmlspecialchars($order['event_booking_finish_time']) ?></td>
                            <td class="text-end"><?= number_format($order['event_booking_price'], 2) ?></td>

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