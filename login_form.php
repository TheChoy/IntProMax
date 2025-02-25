<?php
session_start();
include('username.php'); // ไฟล์เชื่อมฐานข้อมูล

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ใช้ Prepared Statement ป้องกัน SQL Injection
    $sql = "SELECT member_id, member_password FROM member WHERE member_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // ตรวจสอบว่าพบผู้ใช้หรือไม่
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // เช็ครหัสผ่าน (ถ้าเก็บแบบ plain text)
        if ($password === $row['member_password']) {
            $_SESSION['user_id'] = $row['member_id']; // เก็บ user_id ใน session
            
            // ไปหน้าหลัก (ต้องเป็น .php เพื่อใช้ session ได้)
            header("Location: index.php"); 
            exit();
        } else {
            $error = "รหัสผ่านไม่ถูกต้อง";
        }
    } else {
        $error = "ไม่พบอีเมลนี้ในระบบ";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link rel="stylesheet" href="css/login_form.css">
</head>
<body>
    <div class="login-container">
        <h1>Log in</h1>

        <!-- แสดงข้อความผิดพลาดถ้ามี -->
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

        <!-- ฟอร์มเข้าสู่ระบบ -->
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" >Log in</button>
        </form>

        <div class="links">
            <span>New user? <a href="#">sign up</a></span>
            <span><a href="#">Forgot password?</a></span>
        </div>
    </div>
</body>
</html>
