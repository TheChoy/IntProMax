<?php
    include('username.php');

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $data = file_get_contents("php://input");
        $user = json_decode($data, true);

        // ตรวจสอบว่าข้อมูลครบถ้วนหรือไม่
        if (!isset($user["member_id"], $user["equipment_id"], $user["order_equipment_type"], 
                  $user["order_equipment_price"], $user["order_equipment_quantity"], $user["order_equipment_total"], 
                  $user["order_equipment_buy_type"], $user["order_equipment_months"])) {
            die("Missing required fields");
        }

        $member_id = $user["member_id"];
        $equipment_id = $user["equipment_id"];
        $order_equipment_type  = $user["order_equipment_type"];
        $order_equipment_price = $user["order_equipment_price"];
        $order_equipment_quantity = $user["order_equipment_quantity"];
        $order_equipment_total = $user["order_equipment_total"];
        $order_equipment_months =  $user["order_equipment_months"];
        $order_equipment_buy_type = $user["order_equipment_buy_type"];


        echo  $member_id ;

        // ✅ ใช้ backticks `` ครอบชื่อ table `order` (เพราะเป็นคำสงวนใน MySQL)
        $sql = "INSERT INTO `order_equipment` 
                (member_id, equipment_id, order_equipment_type, order_equipment_price, order_equipment_quantity, order_equipment_total, order_equipment_months,order_equipment_buy_type)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        // ✅ ใช้ Prepared Statement ป้องกัน SQL Injection
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("iissiiis", 
            $member_id, 
            $equipment_id, 
            $order_equipment_type, 
            $order_equipment_price, 
            $order_equipment_quantity, 
            $order_equipment_total, 
            $order_equipment_months,
            $order_equipment_buy_type 
        );

        if ($stmt->execute()) {
            echo "Insert success";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Invalid request method";
    }
?>
