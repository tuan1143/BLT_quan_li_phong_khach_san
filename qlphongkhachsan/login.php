<?php
// Bắt đầu session để lưu trạng thái đăng nhập
session_start();

// 1. Thông tin kết nối CSDL (Thay đổi cho phù hợp)
$db_host = 'localhost';
$db_name = 'quanly_khachsan'; // Tên DB từ phần trước
$db_user = 'root';
$db_pass = ''; // Mật khẩu của bạn

// Biến lưu trữ thông báo lỗi
$error_message = '';

// 2. Xử lý khi người dùng gửi form (nhấn nút Đăng nhập)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra nếu thông tin bị bỏ trống
    if (empty($username) || empty($password)) {
        $error_message = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!';
    } else {
        try {
            // 3. Kết nối CSDL bằng PDO (An toàn hơn)
            $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 4. Chuẩn bị câu lệnh SQL (Chống SQL Injection)
            $stmt = $conn->prepare("SELECT * FROM NhanVien WHERE username = ?");
            $stmt->execute([$username]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 5. Kiểm tra người dùng và mật khẩu
            // Dùng password_verify() để so sánh mật khẩu đã hash
            if ($user && password_verify($password, $user['password_hash'])) {
                
                // 6. Đăng nhập thành công: Lưu thông tin vào Session
                $_SESSION['user_id'] = $user['id_nhanvien'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['ho_ten'] = $user['ho_ten'];
                $_SESSION['chuc_vu'] = $user['chuc_vu'];

                // Chuyển hướng đến trang quản lý (dashboard)
                header('Location: dashboard.php');
                exit;

            } else {
                // Đăng nhập thất bại
                $error_message = 'Tên đăng nhập hoặc mật khẩu không chính xác!';
            }

        } catch(PDOException $e) {
            // Lỗi kết nối CSDL
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
    <title>Đăng nhập - Quản lý Khách sạn</title>
    <!-- Tải Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Thêm font Inter cho đẹp hơn */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-lg">
        <div class="text-center">
            <!-- Bạn có thể thêm logo ở đây -->
            <h1 class="text-3xl font-bold text-gray-900">Quản lý Khách sạn</h1>
            <p class="mt-2 text-gray-600">Đăng nhập vào hệ thống</p>
        </div>

        <!-- Form Đăng nhập -->
        <form class="space-y-6" action="login.php" method="POST">
            <!-- Hiển thị lỗi nếu có -->
            <?php if (!empty($error_message)): ?>
                <div class="p-3 bg-red-100 text-red-700 border border-red-300 rounded-lg">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Tên đăng nhập</label>
                <div class="mt-1">
                    <input id="username" name="username" type="text" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="ví dụ: admin">
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
                <div class="mt-1">
                    <input id="password" name="password" type="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="••••••••">
                </div>
            </div>

            <!-- Nút Đăng nhập -->
            <div>
                <button type="submit"
                        class="w-full px-4 py-2 text-lg font-medium text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    Đăng nhập
                </button>
            </div>
        </form>
    </div>

</body>
</html>
