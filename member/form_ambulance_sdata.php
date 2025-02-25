<?php
include 'username.php';

//รับค่าจากฟอร์ม
$pickup_location = $_POST['pickup-location'] ?? '';
$hospital = $_POST["hospital"];
$province = $_POST["province"];
$symptom = $_POST["symptom"];
$allergy = $_POST["allergy"];
$payment_method = $_POST['payment_method_hospital'];
$ambulance_id = rand(1, 10);
$member_id = rand(1, 10);
// Mapping hospital codes to names
$hospitalMap = [
    "hospital1" => "โรงพยาบาลมหาวิทยาลัยนเรศวร",
    "hospital2" => "โรงพยาบาลจุฬาลงกรณ์",
    "hospital3" => "โรงพยาบาลกรุงเทพมหานคร",
    "hospital4" => "โรงพยาบาลพระมงกุฎเกล้า"
];
// Mapping symtom codes to names
$symptomMap = [
    "symptom1" => "เกี่ยวกับระบบทางเดินหายใจ",
    "symptom2" => "เกี่ยวกับระบบไหลเวียนเลือด",
    "symptom3" => "เกี่ยวกับกล้ามเนื้อและกระดูก",
    "symptom4" => "โรคเรื้อรัง",
    "symptom5" => "สุขภาพจิต"
];
$allergyMap = [
    "allergy1" => "อาหารทะเล",
    "allergy2" => "นมวัว",
    "allergy3" => "ถั่วลิสง",
    "allergy4" => "ไข่",
    "allergy5" => "ยาปฏิชีวนะ",
    "allergy6" => "ยาชา"
];
$provinceMap = [
    "province1" => "กรุงเทพมหานคร",
    "province2" => "กระบี่",
    "province3" => "กาญจนบุรี",
    "province4" => "กาฬสินธุ์",
    "province5" => "กำแพงเพชร",
    "province6" => "ขอนแก่น",
    "province7" => "จันทบุรี",
    "province8" => "ฉะเชิงเทรา",
    "province9" => "ชลบุรี",
    "province10" => "ชัยนาท",
    "province11" => "ชัยภูมิ",
    "province12" => "ชุมพร",
    "province13" => "เชียงราย",
    "province14" => "เชียงใหม่",
    "province15" => "ตรัง",
    "province16" => "ตราด",
    "province17" => "ตาก",
    "province18" => "นครนายก",
    "province19" => "นครปฐม",
    "province20" => "นครพนม",
    "province21" => "นครราชสีมา",
    "province22" => "นครศรีธรรมราช",
    "province23" => "นครสวรรค์",
    "province24" => "นนทบุรี",
    "province25" => "นราธิวาส",
    "province26" => "น่าน",
    "province27" => "บึงกาฬ",
    "province28" => "บุรีรัมย์",
    "province29" => "ปทุมธานี",
    "province30" => "ประจวบคีรีขันธ์",
    "province31" => "ปราจีนบุรี",
    "province32" => "ปัตตานี",
    "province33" => "พะเยา",
    "province34" => "พระนครศรีอยุธยา",
    "province35" => "พังงา",
    "province36" => "พัทลุง",
    "province37" => "พิจิตร",
    "province38" => "พิษณุโลก",
    "province39" => "เพชรบุรี",
    "province40" => "เพชรบูรณ์",
    "province41" => "แพร่",
    "province42" => "ภูเก็ต",
    "province43" => "มหาสารคาม",
    "province44" => "มุกดาหาร",
    "province45" => "แม่ฮ่องสอน",
    "province46" => "ยโสธร",
    "province47" => "ยะลา",
    "province48" => "ร้อยเอ็ด",
    "province49" => "ระนอง",
    "province50" => "ระยอง",
    "province51" => "ราชบุรี",
    "province52" => "ลพบุรี",
    "province53" => "ลำปาง",
    "province54" => "ลำพูน",
    "province55" => "เลย",
    "province56" => "ศรีสะเกษ",
    "province57" => "สกลนคร",
    "province58" => "สงขลา",
    "province59" => "สตูล",
    "province60" => "สมุทรปราการ",
    "province61" => "สมุทรสงคราม",
    "province62" => "สมุทรสาคร",
    "province63" => "สระแก้ว",
    "province64" => "สระบุรี",
    "province65" => "สิงห์บุรี",
    "province66" => "สุโขทัย",
    "province67" => "สุพรรณบุรี",
    "province68" => "สุราษฎร์ธานี",
    "province69" => "สุรินทร์",
    "province70" => "หนองคาย",
    "province71" => "หนองบัวลำภู",
    "province72" => "อ่างทอง",
    "province73" => "อำนาจเจริญ",
    "province74" => "อุดรธานี",
    "province75" => "อุตรดิตถ์",
    "province76" => "อุทัยธานี",
    "province77" => "อุบลราชธานี"
];
// ตรวจสอบและแทนค่าชื่อโรงพยาบาล
$hospital = $hospitalMap[$hospital] ?? $hospital;
//ตรวจสอบและแทนค่าชื่อโรค
$symptom = $symptomMap[$symptom] ?? $symptom;
//ตรวจสอบและแทนชื่อการแพ้ยา/อาหาร
$allergy = $allergyMap[$allergy] ?? $allergy;
//ตรวจสอบและแทนชื่อจังหวัด
$province = $provinceMap[$province] ?? $province;
// สร้างคำสั่ง SQL
print_r($_POST);
$sql = "INSERT INTO ambulance_booking 
        (member_id,ambulance_id,ambulance_booking_location, ambulance_booking_hospital_waypoint, ambulance_booking_province, ambulance_booking_disease, ambulance_booking_allergy_medicine,ambulance_booking_buy_type) 
        VALUES ('$member_id','$ambulance_id','$pickup_location', '$hospital', '$province', '$symptom', '$allergy','$payment_method')";
// บันทึกข้อมูล
if ($conn->query($sql) === TRUE) {
    echo "✅ บันทึกการจองเรียบร้อยแล้ว!";
} else {
    echo "❌ เกิดข้อผิดพลาด: " . $sql . "<br>" . $connect->error;
}
// ปิดการเชื่อมต่อ
$conn->close();
?>
