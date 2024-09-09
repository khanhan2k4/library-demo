<?php
// Bắt đầu phiên
session_start();

// Kết nối tới MySQL
require 'config.php';

// Lấy ID của admin từ session
// Giả sử ID admin đã được lưu trong session khi đăng nhập
$admin_id = $_SESSION['admin_id'];

// Truy vấn thông tin admin từ bảng admin
$sql = "SELECT avatar, first_name, last_name, email, so_dien_thoai FROM admin WHERE admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_id); // "s" ở đây đại diện cho chuỗi (string)
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra nếu có dữ liệu admin
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    echo "Không tìm thấy thông tin admin!";
    exit();
}

// Đóng kết nối
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý thông tin</title>
    <link rel="stylesheet" href="css/profile.css" />
  </head>
  <body>
    <div class="container">
      <h1>Quản lý thông tin</h1>
      <div class="profile-management">
        <!-- Sidebar -->
        <div class="sidebar">
          <ul>
            <li><a href="#" onclick="showForm('general')">Tổng quát</a></li>
            <li><a href="#" onclick="showForm('password')">Đổi mật khẩu</a></li>
            <li><a href="#" onclick="showForm('personal')">Cập nhật thông tin</a></li>
          </ul>
        </div>

        <!-- Nội dung các form -->
        <div class="content">
          <!-- Form Tổng quát (chỉ hiển thị thông tin) -->
          <div id="general" class="form-section active">
            <h2>Tổng quát</h2>
            <div class="info-display">
              <!-- Avatar (hiển thị, không chỉnh sửa) -->
              <div class="form-group">
                <label>Avatar</label>
                <div class="avatar-preview">
                  <img src="<?php echo htmlspecialchars($admin['avatar']); ?>" id="avatar-preview" alt="Avatar" class="avatar-image" />
                </div>
              </div>

              <!-- Họ tên admin -->
              <div class="form-group">
                <label>Họ tên admin</label>
                <p><?php echo htmlspecialchars($admin['first_name']) . ' ' . htmlspecialchars($admin['last_name']); ?></p>
              </div>

              <!-- ID -->
              <div class="form-group">
                <label>ID</label>
                <p><?php echo htmlspecialchars($admin_id); ?></p>
              </div>

              <!-- Email -->
              <div class="form-group">
                <label>E-mail</label>
                <p><?php echo htmlspecialchars($admin['email']); ?></p>
              </div>

              <!-- Số điện thoại -->
              <div class="form-group">
                <label>Số điện thoại</label>
                <p><?php echo htmlspecialchars($admin['so_dien_thoai']); ?></p>
              </div>

              <div class="form-buttons">
                <a href="home_admin.php"><button type="submit" class="save-btn">Trang chủ</button></a>
              </div>
            </div>
          </div>

          <!-- Form Đổi mật khẩu -->
          <div id="password" class="form-section" style="display: none">
            <h2>Đổi mật khẩu</h2>
            <form action="change_password.php" method="POST">
              <!-- ID Admin -->
              <div class="form-group">
                <label for="admin-id">ID admin</label>
                <input type="text" id="admin-id" name="admin_id" value="<?php echo htmlspecialchars($admin_id); ?>" readonly />
              </div>

              <!-- Mật khẩu hiện tại -->
              <div class="form-group">
                <label for="current-password">Mật khẩu hiện tại</label>
                <input type="password" id="current-password" name="current_password" placeholder="Nhập mật khẩu hiện tại" required />
              </div>

              <!-- Mật khẩu mới -->
              <div class="form-group">
                <label for="new-password">Mật khẩu mới</label>
                <input type="password" id="new-password" name="new_password" placeholder="Nhập mật khẩu mới" required />
              </div>

              <!-- Xác nhận mật khẩu mới -->
              <div class="form-group">
                <label for="confirm-password">Xác nhận mật khẩu mới</label>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Xác nhận mật khẩu mới" required />
              </div>

              <div class="form-buttons">
                <button type="submit" class="save-btn">Cập nhật</button>
                <button type="button" class="cancel-btn" onclick="window.location.href='profile.php';">Hủy</button>

              </div>
            </form>
          </div>

          <!-- Form Cập nhật thông tin cá nhân -->
          <div id="personal" class="form-section" style="display: none">
            <h2>Cập nhật thông tin</h2>
            <form action="update_info.php" method="POST" enctype="multipart/form-data">
              <!-- Avatar (có thể cập nhật) -->
              <div class="form-group">
                <label for="avatar">Avatar</label>
                <div class="avatar-preview">
                  <img src="<?php echo htmlspecialchars($admin['avatar']); ?>" id="avatar-preview" alt="Avatar" class="avatar-image" />
                </div>
                <input type="file" id="avatar" name="avatar" onchange="previewAvatar(event)" />
              </div>

              <!-- Tên người dùng -->
              <div class="form-group">
                <label for="last_name">Tên người dùng</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($admin['last_name']); ?>" />
              </div>

              <!-- Họ người dùng -->
              <div class="form-group">
                <label for="first_name">Họ người dùng</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($admin['first_name']); ?>" />
              </div>

              <!-- Email -->
              <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" />
              </div>

              <!-- Số điện thoại -->
              <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($admin['so_dien_thoai']); ?>" />
              </div>

              <div class="form-buttons">
                <button type="submit" class="save-btn">Lưu cập nhật</button>
                <button type="button" class="cancel-btn" onclick="window.location.href='profile.php';">Hủy</button>

              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <script src="js/profile.js"></script>
  </body>
</html>
