<?php
session_start();
include 'username.php';

// รับค่าราคา total ที่ส่งมาจาก cart
$total_price = $_POST['price_total'] ?? 0; // ถ้าไม่มีค่าใน price_total จะใช้ค่า 0

$member_id = $_SESSION['user_id'] ?? null;

if (!$member_id || !isset($_SESSION["strProductID"])) {
    header("Location: cart.php");
    exit();
}

// วนลูปสินค้าแล้ว insert เข้าฐานข้อมูล
for ($i = 0; $i <= (int)$_SESSION["intLine"]; $i++) {
    if (!empty($_SESSION["strProductID"][$i])) {
        $equipment_id = $_SESSION["strProductID"][$i];
        $quantity = $_SESSION["strQty"][$i];

        $sql = "SELECT * FROM equipment WHERE equipment_id = '$equipment_id'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        $price = $row['equipment_price_per_unit'];
        $total = ($price * $quantity) + 120;

        // INSERT ข้อมูลคำสั่งซื้อ
        $insert = "INSERT INTO order_equipment (
            member_id,
            equipment_id,
            order_equipment_price,
            order_equipment_buy_type,
            order_equipment_type,
            order_equipment_quantity,
            order_equipment_total
        ) VALUES (
            '$member_id',
            '$equipment_id',
            '$price',
            'QR Promptpay',
            'ซื้อ',
            '$quantity',
            '$total'
        )";

        mysqli_query($conn, $insert);
    }
}

// ล้างตะกร้า
unset($_SESSION["strProductID"]);
unset($_SESSION["strQty"]);
unset($_SESSION["intLine"]);

// ส่งไปหน้า QR พร้อมราคาทั้งหมด
header("Location: QRpayment_order.php?price_total=" . $total_price);
exit();
?>