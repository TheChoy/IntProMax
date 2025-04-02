<?php

header('Content-Type: application/text; charset=utf-8');

//ตรวจสอบว่า JSON ที่รับมาถูกต้องหรือไม่
function isValidJSON($str)
{
    json_decode($str);
    return json_last_error() == JSON_ERROR_NONE;
}

//รับ JSON จากการกรอก input ในหน้า shopping.php
$json_params = file_get_contents("php://input");

?>

<?php

include 'username.php';

//แปลง JSON ที่รับมาให้กลายเป็น array
if (strlen($json_params) > 0 && isValidJSON($json_params))
$json_data = json_decode($json_params,true);

//สร้างไว้เก็บประโยค WHERE
//โดยมีเงื่อนไขคือ ถ้า input ไม่เป็นค่าว่าง ให้เพิ่มเงื่อนไขนั้น ๆ เข้าไปใน WHERE
//ถ้า input เป็นค่าว่าง ไม่ต้องทำอะไร
$whereClauses = array();
if (! empty($json_data['category'])) {
    
    $whereClauses[0] = "equipment_type = '$json_data[category]' AND";
}

$whereClauses[1] = " equipment_price_per_unit BETWEEN '$json_data[minPrice]' AND '$json_data[maxPrice]'";

if (! empty($json_data['priceSort'])) {
    if ($json_data['priceSort'] === "first") {
        $whereClauses[2] = "ORDER BY equipment_price_per_unit DESC";
    } elseif ($json_data['priceSort'] === "basic") {
        $whereClauses[2] = "ORDER BY equipment_price_per_unit ASC";
    }
}

//สร้าง string เก็บประโยค WHERE ตัวเต็มที่ได้จากการรวม $whereClauses
$where = '';
if (count($whereClauses) > 0) {
    $where = ' WHERE ' . implode(' ', $whereClauses);
}

//ดึงข้อมูลจากฐานข้อมูล โดยเงื่อนไข WHERE เรียกมาจาก $where
$result = mysqli_query($conn, "SELECT * FROM equipment $where");

?>

<!-- ส่งข้อมูลด้านล่างนี้ไปแสดงที่ div id="prodContian" ในหน้า shopping.php -->
<section class="product-container">
    <?php   
        while ($row = mysqli_fetch_assoc($result)) {
        ?>
        <div class="product">
            <a href="product_details.php?id=<?= $row['equipment_id'] ?>">
                <img src="image/<?= $row['equipment_image'] ?>" alt="<?= $row['equipment_name'] ?>">
                <br><br>
                <p><?= $row['equipment_name'] ?></p>
                <p class="cost">฿ <?= number_format($row['equipment_price_per_unit'],) ?></p>
            </a>
        </div>
        <?php
        }
        mysqli_close($conn);
    ?>
</section>
