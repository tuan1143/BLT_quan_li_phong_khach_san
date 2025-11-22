<?php
<?php
// Chạy file này bằng PHP của XAMPP để tạo DB/bảng và user admin với password "admin123".
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = ''; // nếu MySQL của bạn có mật khẩu, điền vào đây

$sqlFile = __DIR__ . '/001_create_tables.sql';
if (!file_exists($sqlFile)) {
    echo "Không tìm thấy file SQL: $sqlFile\n";
    exit(1);
}

try {
    $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Thực thi file SQL (tạo DB và bảng)
    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);
    echo "Migrations executed.\n";

    // Chuyển vào database
    $pdo->exec("USE quanly_khachsan");

    // Tạo user admin nếu chưa tồn tại, hoặc cập nhật password nếu đã có
    $username = 'admin';
    $plainPassword = 'admin123';
    $passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM NhanVien WHERE username = ?");
    $stmt->execute([$username]);
    $exists = (int)$stmt->fetchColumn();

    if ($exists === 0) {
        $ins = $pdo->prepare("INSERT INTO NhanVien (username, password_hash, ho_ten, chuc_vu) VALUES (?, ?, ?, ?)");
        $ins->execute([$username, $passwordHash, 'Quản trị viên', 'Admin']);
        echo "Admin user created (username: admin, password: admin123).\n";
    } else {
        $upd = $pdo->prepare("UPDATE NhanVien SET password_hash = ? WHERE username = ?");
        $upd->execute([$passwordHash, $username]);
        echo "Admin user exists — password updated to admin123.\n";
    }

} catch (PDOException $e) {
    echo "Lỗi PDO: " . $e->getMessage() . "\n";
    exit(1);
}