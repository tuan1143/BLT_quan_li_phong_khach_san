<?php
// Bắt đầu session để lưu trạng thái đăng nhập
session_start();

// Biến lưu trữ thông báo lỗi
$error_message = '';

// Xử lý khi người dùng gửi form (nhấn nút Đăng ký)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra nếu thông tin bị bỏ trống
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = 'Vui lòng nhập đầy đủ thông tin!';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Mật khẩu không khớp!';
    } else {
        // Kết nối CSDL và thực hiện đăng ký
        try {
            require_once '../../config/database.php'; // Kết nối đến CSDL
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Hash mật khẩu
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Chuẩn bị câu lệnh SQL để thêm người dùng mới
            $stmt = $conn->prepare("INSERT INTO NhanVien (username, password_hash) VALUES (?, ?)");
            $stmt->execute([$username, $password_hash]);

            // Đăng ký thành công, chuyển hướng đến trang đăng nhập
            header('Location: login.php');
            exit;

        } catch(PDOException $e) {
            $error_message = 'Lỗi kết nối CSDL: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Quản lý Khách sạn</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Đăng ký tài khoản</h1>
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div>
                <label for="username">Tên đăng nhập</label>
                <input id="username" name="username" type="text" required>
            </div>
            <div>
                <label for="password">Mật khẩu</label>
                <input id="password" name="password" type="password" required>
            </div>
            <div>
                <label for="confirm_password">Xác nhận mật khẩu</label>
                <input id="confirm_password" name="confirm_password" type="password" required>
            </div>
            <button type="submit">Đăng ký</button>
        </form>
        <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
    </div>
</body>
</html>