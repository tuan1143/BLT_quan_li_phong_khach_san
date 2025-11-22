<?php
// Cấu hình kết nối cơ sở dữ liệu
$host = 'localhost';
$db_name = 'hotel_booking_system'; // Tên cơ sở dữ liệu
$db_user = 'root'; // Tên người dùng CSDL
$db_pass = ''; // Mật khẩu CSDL

try {
    // Tạo kết nối PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối CSDL thất bại: " . $e->getMessage());
}
?>