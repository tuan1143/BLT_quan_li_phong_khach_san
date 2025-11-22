<?php
<?php
// Nội dung động: lấy dữ liệu từ DB
require_once __DIR__ . '/../includes/db.php';
$db = getDB();
$stmt = $db->query("SELECT p.id_phong, p.ten_phong, p.trang_thai, p.ghi_chu, l.ten_loaiphong
                    FROM phong p
                    LEFT JOIN loaiphong l ON p.id_loaiphong = l.id_loaiphong
                    ORDER BY p.ten_phong");
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách loại phòng cho form
$stmt2 = $db->query("SELECT id_loaiphong, ten_loaiphong FROM loaiphong ORDER BY ten_loaiphong");
$types = $stmt2->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="content-header">
    <h2>Quản lý phòng</h2>
    <div class="actions">
        <button id="btnAddRoom" class="btn">Thêm phòng</button>
    </div>
</div>

<div class="content-card">
    <table class="rooms-table">
        <thead>
            <tr>
                <th>Mã</th>
                <th>Phòng</th>
                <th>Loại phòng</th>
                <th>Trạng thái</th>
                <th>Ghi chú</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $r): ?>
            <tr data-id="<?php echo $r['id_phong']; ?>">
                <td><?php echo htmlspecialchars($r['id_phong']); ?></td>
                <td class="room-name"><?php echo htmlspecialchars($r['ten_phong']); ?></td>
                <td class="room-type"><?php echo htmlspecialchars($r['ten_loaiphong']); ?></td>
                <td class="room-status"><?php echo htmlspecialchars($r['trang_thai']); ?></td>
                <td class="room-note"><?php echo htmlspecialchars($r['ghi_chu']); ?></td>
                <td class="actions-col">
                    <button class="btn small edit-btn">Sửa</button>
                    <button class="btn small danger delete-btn">Xóa</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal form (ẩn) -->
<div id="roomModal" class="modal" aria-hidden="true">
  <div class="modal-panel">
    <h3 id="modalTitle">Thêm phòng</h3>
    <form id="roomForm">
      <input type="hidden" name="id_phong" id="id_phong" value="">
      <label>Phòng
        <input type="text" name="ten_phong" id="ten_phong" required>
      </label>
      <label>Loại phòng
        <select name="id_loaiphong" id="id_loaiphong" required>
          <option value="">-- Chọn loại --</option>
          <?php foreach($types as $t): ?>
            <option value="<?php echo $t['id_loaiphong']; ?>"><?php echo htmlspecialchars($t['ten_loaiphong']); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Trạng thái
        <select name="trang_thai" id="trang_thai" required>
          <option value="Trong">Trong</option>
          <option value="DangO">Đang ở</option>
          <option value="BaoTri">Bảo trì</option>
        </select>
      </label>
      <label>Ghi chú
        <input type="text" name="ghi_chu" id="ghi_chu">
      </label>
      <div class="modal-actions">
        <button type="submit" class="btn">Lưu</button>
        <button type="button" id="btnCancel" class="btn muted">Hủy</button>
      </div>
    </form>
  </div>
</div>

<!-- JS inline nhỏ để khởi tạo biến (rooms.js sẽ xử lý hành vi) -->
<script>
window.roomsConfig = {
  actionsUrl: 'includes/rooms_actions.php'
};
</script>