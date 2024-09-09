<?php
session_start();
// Liên kết file config.php để kết nối cơ sở dữ liệu
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin người dùng từ form
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $email = $conn->real_escape_string($_POST['email']);

    // Kiểm tra trong bảng users xem ID và Email có tồn tại không
    $sql = "SELECT * FROM users WHERE id = '$user_id' AND email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Nếu tồn tại, tạo mã xác nhận và lưu vào bảng reset_pass (tạo mã xác nhận)
        $verification_code = rand(100000, 999999);

        // Lưu mã xác nhận vào bảng reset_pass
        $sql_insert = "INSERT INTO reset_pass (user_id, email, verification_code) 
                       VALUES ('$user_id', '$email', '$verification_code')
                       ON DUPLICATE KEY UPDATE verification_code = '$verification_code', created_at = NOW()";
        $conn->query($sql_insert);

        // Lưu user_id và email vào session để sử dụng sau này
        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;

        // Chuyển hướng đến trang nhập mã xác nhận
        header("Location: nhap_ma.php");
        exit;
    } else {
        $error = "ID người dùng hoặc email không hợp lệ!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quên Mật Khẩu</title>
    <link rel="stylesheet" href="css/forgot_password.css" />
  </head>
  <body>
    <div class="container">
      <h2 class="title">Quên Mật Khẩu ?</h2>
      <p class="content">Nhập id và email để đặt lại mật khẩu:</p>

      <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
      <?php endif; ?>

      <form class="container-form" method="POST" action="">
        <div class="form">
          <label class="form-title" for="user_id">Nhập id người dùng.</label>
          <input class="form-text" type="text" name="user_id" required />
        </div>
        <div class="form">
          <label class="form-title" for="email">Email của bạn.</label>
          <input class="form-text" type="email" name="email" required />
        </div>
        <button class="btn-send" type="submit">Gửi</button>
      </form>
    </div>
  </body>
</html>
