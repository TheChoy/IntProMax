<?php
include 'username.php';

$order_total = isset($_GET['price_total']) ? $_GET['price_total'] : 0;
$order_id = isset($_GET['id']) ? $_GET['id'] : 0;

// หากกดยืนยันแล้วให้ลด stock
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $equipment_id = isset($data['equipment_id']) ? (int)$data['equipment_id'] : 0;

    $sql = "UPDATE equipment SET equipment_quantity = equipment_quantity - 1 WHERE equipment_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $equipment_id);
    mysqli_stmt_execute($stmt);

    echo json_encode(["status" => "success"]);
    exit();
}

// โหลดข้อมูลสินค้า
$medical_equipment_sql = "SELECT * 
    FROM equipment
    LEFT JOIN order_equipment ON equipment.equipment_id = order_equipment.equipment_id
    WHERE equipment.equipment_id = '$order_id'";
$result_medical_equipment = mysqli_query($conn, $medical_equipment_sql);
$row = mysqli_fetch_assoc($result_medical_equipment);
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
            const equipmentId = urlParams.get("id");
            const orderTotal = <?= $order_total ?>;

            fetch(window.location.href, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        equipment_id: equipmentId
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
                        alert("ไม่สามารถอัปเดตจำนวนสินค้าได้");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์");
                });
        });
    </script>

</html>