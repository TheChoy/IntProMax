<?php
    include 'username.php';

    $order_total = isset($_GET['price_total']) ? $_GET['price_total'] : 0;
    $order_equipment_id = isset($_GET['order_equipment_id']) ? (int)$_GET['order_equipment_id'] : 0;

    // หากมีการ POST เช่น กดยืนยัน หรือ ยกเลิก
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        $equipment_id = isset($data['equipment_id']) ? (int)$data['equipment_id'] : 0;
        $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
        $action = isset($data['action']) ? $data['action'] : 'confirm';
        $order_id = isset($data['order_equipment_id']) ? (int)$data['order_equipment_id'] : 0;

        if ($action === 'confirm') {
            // ไม่ต้องลด stock ตรงนี้แล้ว เพราะทำใน insert_order.php แล้ว
            echo json_encode(["status" => "success"]);
            exit();
        } elseif ($action === 'cancel') {
            $sql = "UPDATE equipment SET equipment_quantity = equipment_quantity + ? WHERE equipment_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $quantity, $equipment_id);
            $stmt->execute();

            $delete_order = $conn->prepare("DELETE FROM order_equipment WHERE order_equipment_id = ?");
            $delete_order->bind_param("i", $order_id);
            $delete_order->execute();

            echo json_encode(["status" => "cancelled"]);
            exit();
        }
    }

    // ตรวจสอบ timeout 10 นาที
    if ($order_equipment_id) {
        $stmt = $conn->prepare("SELECT order_equipment_date, order_equipment_quantity, equipment_id FROM order_equipment WHERE order_equipment_id = ?");
        $stmt->bind_param("i", $order_equipment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
    
        if ($order) {
            $order_time = strtotime($order['order_equipment_date']);
            $now = time();
            $diff = $now - $order_time;
    
            if ($diff > 600) {
                // คืนของ + ลบออเดอร์
                $qty = $order['order_equipment_quantity'];
                $equipment_id = $order['equipment_id'];
    
                $return_sql = $conn->prepare("UPDATE equipment SET equipment_quantity = equipment_quantity + ? WHERE equipment_id = ?");
                $return_sql->bind_param("ii", $qty, $equipment_id);
                $return_sql->execute();
    
                $delete_order = $conn->prepare("DELETE FROM order_equipment WHERE order_equipment_id = ?");
                $delete_order->bind_param("i", $order_equipment_id);
                $delete_order->execute();
    
                echo "<script>alert('หมดเวลาการชำระเงิน กรุณาทำรายการใหม่'); window.location.href = 'shopping.php';</script>";
                exit();
            }
        }
    }
    

    // โหลดข้อมูลสินค้า
    $medical_equipment_sql = "SELECT * FROM equipment LEFT JOIN order_equipment ON equipment.equipment_id = order_equipment.equipment_id WHERE order_equipment.order_equipment_id = ?";
    $stmt = $conn->prepare($medical_equipment_sql);
    $stmt->bind_param("i", $order_equipment_id);
    $stmt->execute();
    $result_medical_equipment = $stmt->get_result();
    $row = $result_medical_equipment->fetch_assoc();
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
                <a href="cart.php">
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
            <script>
                const urlParams = new URLSearchParams(window.location.search);
                const equipmentId = urlParams.get("id");
                const quantity = urlParams.get("quantity");
                const orderId = urlParams.get("order_equipment_id"); // ✅ ใช้ค่า id

                const confirmBtn = document.getElementById("confirm-btn");
                confirmBtn.addEventListener("click", () => {
                    fetch(window.location.href, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            equipment_id: equipmentId,
                            quantity: quantity,
                            action: "confirm",
                            order_equipment_id: orderId
                        })

                    }).then(res => res.json()).then(data => {
                        if (data.status === "success") {
                            const total = parseFloat(urlParams.get("price_total") || 0); // ✅ ดึงจาก URL
                            if (total > 100000) {
                                window.location.href = "approve_payment.html";
                            } else {
                                window.location.href = "success_payment.html";
                            }
                        }

                    });
                });

                document.querySelector(".cancle").addEventListener("click", () => {
                    fetch(window.location.href, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            equipment_id: equipmentId,
                            quantity: quantity,
                            action: "cancel",
                            order_equipment_id: orderId
                        })

                    }).then(res => res.json()).then(data => {
                        if (data.status === "cancelled") {
                            alert("ยกเลิกสำเร็จ");
                            window.location.href = "shopping.php";
                        }
                    });
                });

                setTimeout(() => {
                    alert("หมดเวลาการชำระเงิน กรุณาสั่งซื้อใหม่");
                    window.location.href = "shopping.php";
                }, 600000); // 10 นาที
            </script>
        </body>

    </html>