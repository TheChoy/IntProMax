<?php
include 'username.php';

// รับข้อมูลจากไคลเอนต์
$order_total = isset($_GET['price_total']) ? $_GET['price_total'] : 0;
$order_id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูล JSON ที่ส่งมาจากไคลเอนต์
    $data = json_decode(file_get_contents("php://input"), true);
    $equipment_id = isset($data['equipment_id']) ? (int)$data['equipment_id'] : 0;

    if ($equipment_id > 0 && $order_id > 0) {
        // ดึงข้อมูล order_equipment_quantity จากฐานข้อมูลโดยใช้ order_id
        $sql_order = "SELECT order_equipment_quantity FROM order_equipment WHERE order_equipment_id = ?";
        $stmt_order = mysqli_prepare($conn, $sql_order);
        if ($stmt_order) {
            mysqli_stmt_bind_param($stmt_order, "i", $order_id);
            mysqli_stmt_execute($stmt_order);
            mysqli_stmt_bind_result($stmt_order, $order_equipment_quantity);
            mysqli_stmt_fetch($stmt_order);
            mysqli_stmt_close($stmt_order);

            // ตรวจสอบค่าของ order_equipment_quantity ว่าไม่เป็น null หรือ 0
            if (is_null($order_equipment_quantity) || $order_equipment_quantity <= 0) {
                echo json_encode(["status" => "error", "message" => "Invalid order quantity"]);
                exit;
            }

            // ตรวจสอบสต็อกสินค้าก่อนการอัปเดต
            $sql_check_stock = "SELECT equipment_quantity FROM equipment WHERE equipment_id = ?";
            $stmt_check_stock = mysqli_prepare($conn, $sql_check_stock);
            mysqli_stmt_bind_param($stmt_check_stock, "i", $equipment_id);
            mysqli_stmt_execute($stmt_check_stock);
            mysqli_stmt_bind_result($stmt_check_stock, $equipment_quantity);
            mysqli_stmt_fetch($stmt_check_stock);
            mysqli_stmt_close($stmt_check_stock);

            // หากสต็อกไม่เพียงพอ ให้ยกเลิก
            if ($equipment_quantity < $order_equipment_quantity) {
                echo json_encode(["status" => "error", "message" => "Not enough stock for equipment ID: $equipment_id"]);
                exit;
            }

            // อัปเดตจำนวนสินค้าในตาราง equipment โดยลดตาม order_equipment_quantity
            $sql_update_stock = "UPDATE equipment
                                 SET equipment_quantity = equipment_quantity - ?
                                 WHERE equipment_id = ? AND equipment_quantity >= ?";
            $stmt_update_stock = mysqli_prepare($conn, $sql_update_stock);
            if ($stmt_update_stock) {
                mysqli_stmt_bind_param($stmt_update_stock, "iii", $order_equipment_quantity, $equipment_id, $order_equipment_quantity);
                mysqli_stmt_execute($stmt_update_stock);

                // ตรวจสอบว่ามีการเปลี่ยนแปลงข้อมูลหรือไม่
                if (mysqli_affected_rows($conn) > 0) {
                    echo json_encode(["status" => "success", "message" => "Stock updated"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "No stock updated or equipment not found"]);
                }

                mysqli_stmt_close($stmt_update_stock);
                exit;
            } else {
                echo json_encode(["status" => "error", "message" => "Database error during stock update"]);
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_QR_payment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>ชำระเงิน</title>
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
                    <a href="logout.html">ออกจากระบบ</a>
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
            <a href="cart.html">
                <i class="fas fa-shopping-cart"></i>
            </a>
        </div>
    </div>

    <body>
        <section class="QRcode">
            <img src="image/QRcode.jpeg" alt="" class="qr-preview" id="qr-preview"><br>
            <?php echo "ยอดชำระทั้งหมด: ฿" . number_format($order_total, 2);  ?>
            <br><br>
            <div class="bottom-row">
                <p>แนบหลักฐานยืนยัน</p>
                <button class="upload-btn" id="upload-btn">อัพโหลด</button>
            </div><br>
            <div class="QR-buttons">
                <button class="cancle">ยกเลิก</button>
                <button class="confirm" id="confirm-btn">ยืนยัน</button>

            </div>
        </section>
    </body>
    <script>
        document.getElementById("confirm-btn").addEventListener("click", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const equipmentId = urlParams.get("id"); // ค่าที่ได้จาก URL
            const orderTotal = <?= $order_total ?>; // ตัวแปรที่ได้จาก PHP

            fetch(window.location.href, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        equipment_id: equipmentId // ส่ง equipment_id ไปยัง PHP
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        if (orderTotal > 100000) {
                            window.location.href = "approve_payment.html";
                        } else {
                            window.location.href = "success_payment.html";
                        }
                    } else {
                        alert("ไม่สามารถอัปเดตจำนวนสินค้าได้: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์");
                });
        });
    </script>

</html>