<?php

// ...existing code...
$dbHost = '127.0.0.1';
$dbUser = 'root';
$dbPass = ''; // chỉnh nếu MySQL có mật khẩu

$sqlFile = __DIR__ . '/003_full_seed.sql';
if (!file_exists($sqlFile)) {
    echo "SQL file not found: $sqlFile\n";
    exit(1);
}
$sql = file_get_contents($sqlFile);

$mysqli = new mysqli($dbHost, $dbUser, $dbPass);
if ($mysqli->connect_errno) {
    echo "Connect failed: " . $mysqli->connect_error . "\n";
    exit(1);
}

if (! $mysqli->multi_query($sql)) {
    echo "Error running SQL: (" . $mysqli->errno . ") " . $mysqli->error . "\n";
    $mysqli->close();
    exit(1);
}
do {
    if ($res = $mysqli->store_result()) { $res->free(); }
} while ($mysqli->more_results() && $mysqli->next_result());

echo "SQL seed executed.\n";

$mysqli->select_db('quanly_khachsan');

$plain = 'admin123';
$hash = password_hash($plain, PASSWORD_DEFAULT);
$hashEsc = $mysqli->real_escape_string($hash);

$query = "INSERT INTO nhanvien (username, password_hash, ho_ten, chuc_vu)
          VALUES ('admin', '$hashEsc', 'Quản trị viên', 'Admin')
          ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), ho_ten = VALUES(ho_ten), chuc_vu = VALUES(chuc_vu)";

if ($mysqli->query($query) === TRUE) {
    echo "Admin created/updated (username: admin, password: admin123)\n";
} else {
    echo "Failed to create/update admin: (" . $mysqli->errno . ") " . $mysqli->error . "\n";
}

$mysqli->close();
// ...existing code...