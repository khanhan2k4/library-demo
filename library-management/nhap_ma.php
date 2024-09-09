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
$verification_code = '';

// Lấy mã xác nhận từ bảng `reset_pass`
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];

$sql = "SELECT verification_code FROM reset_pass WHERE user_id = '$user_id' AND email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $verification_code = $row['verification_code']; // Lấy mã xác nhận từ cơ sở dữ liệu
} else {
    $error = "Không tìm thấy mã xác nhận. Vui lòng thử lại.";
}

// Xử lý khi người dùng gửi mã
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = $conn->real_escape_string($_POST['input_code']); // Mã người dùng nhập

    if ($input_code === $verification_code) {
        // Nếu mã nhập đúng, chuyển hướng tới trang đổi mật khẩu
        header('Location: doi_pass.php');
        exit;
    } else {
        $error = "Mã xác nhận không chính xác. Vui lòng thử lại.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nhập mã xác nhận</title>
    <link rel="stylesheet" href="css/forgot_password.css" />
  </head>
  <body>
    <div class="container">
      <h2 class="title">Quên Mật Khẩu ?</h2>
      <p class="content">Nhập mã để đặt lại mật khẩu:</p>

      <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
      <?php endif; ?>

      <form class="container-form" method="POST" action="">
        <div class="form">
          <!-- Nhập mã xác nhận -->
          <label class="form-title" for="input_code">Nhập Mã:</label>
          <input class="form-text" type="text" name="input_code" required />
        </div>
        <div class="form-code">
          <!-- Hiển thị mã xác nhận từ cơ sở dữ liệu -->
          <label class="form-title" for="verification_code">Hiện Mã:</label>
          <input class="form-code-1" type="text" value="<?php echo htmlspecialchars($verification_code); ?>" readonly />
        </div>
        <button class="btn-send" type="submit">Gửi</button>
      </form>
    </div>
  </body>
</html>
