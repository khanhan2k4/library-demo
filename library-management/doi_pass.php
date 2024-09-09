<?php
session_start();
// Liên kết file config.php để kết nối cơ sở dữ liệu
require_once 'config.php';

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    header('Location: forgot_password.php');
    exit;
}

$error = '';
$success = '';

// Xử lý khi người dùng gửi form đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);
    
    // Kiểm tra xem mật khẩu và xác nhận mật khẩu có khớp nhau không
    if ($password !== $confirm_password) {
        $error = "Mật khẩu và mật khẩu xác nhận không khớp.";
    } elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự.";
    } else {
        // Lấy thông tin user_id từ session
        $user_id = $_SESSION['user_id'];
        
        // Cập nhật mật khẩu trong bảng `users` (bỏ mã hóa)
        $sql = "UPDATE users SET password = '$password' WHERE id = '$user_id'";
        
        if ($conn->query($sql) === TRUE) {
            // Cập nhật thành công
            $success = "Mật khẩu đã được đổi thành công.";
            // Xoá mã xác nhận trong bảng reset_pass sau khi đổi mật khẩu thành công
            $conn->query("DELETE FROM reset_pass WHERE user_id = '$user_id'");
            
            // Huỷ session để đảm bảo người dùng không thể sử dụng lại session này
            session_destroy();
            
            // Chuyển hướng người dùng về trang đăng nhập
            header('Location: login.php');
            exit;
        } else {
            $error = "Có lỗi xảy ra trong quá trình cập nhật mật khẩu. Vui lòng thử lại.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đặt lại mật khẩu</title>
    <link rel="stylesheet" href="css/forgot_password.css" />
  </head>
  <body>
    <div class="container">
      <h2 class="title">Đặt Lại Mật Khẩu ?</h2>

      <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
      <?php elseif ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
      <?php endif; ?>

      <form class="container-form" method="POST" action="">
        <div class="form">
          <label class="form-title" for="password">Nhập mật khẩu mới</label>
          <input class="form-text" type="password" name="password" required />
        </div>
        <div class="form">
          <label class="form-title" for="confirm_password">Nhập lại mật khẩu</label>
          <input class="form-text" type="password" name="confirm_password" required />
        </div>
        <button class="btn-send" type="submit">Gửi</button>
      </form>
    </div>
  </body>
</html>
