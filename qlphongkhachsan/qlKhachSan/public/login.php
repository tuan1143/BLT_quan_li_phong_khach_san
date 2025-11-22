<?php
session_start();

$db_host = '127.0.0.1';
$db_name = 'quanly_khachsan';
$db_user = 'root';
$db_pass = '123456';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error_message = 'Vui lòng nhập cả username và password.';
    } else {
        try {
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT id_nhanvien, username, password_hash FROM nhanvien WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && isset($user['password_hash']) && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id_nhanvien'];
                $_SESSION['username'] = $user['username'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error_message = 'Sai username hoặc password.';
            }
        } catch (PDOException $e) {
            $error_message = 'Lỗi kết nối DB: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Đăng nhập</title>
    <!-- thay link CSS chung bằng file riêng -->
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="hotel-brand">
            <img src="assets/img/logo.png" alt="Logo" class="login-logo">
            <h1>Trung Tuấn Hotel</h1>
            <div class="subtitle">Sang trọng - Đẳng cấp</div>
        </div>
        
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" autocomplete="off" novalidate>
            <div class="input-wrap">
                <label for="username">Tài khoản</label>
                <input id="username" name="username" type="text" required autofocus>
            </div>

            <div class="input-wrap">
                <label for="password">Mật khẩu</label>
                <input id="password" name="password" type="password" required>
            </div>

            <div class="login-actions">
                <small></small>
                <a href="#">Quên mật khẩu?</a>
            </div>

            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>