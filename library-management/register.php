<?php
// Liên kết file config.php
require_once 'config.php'; // Liên kết file cấu hình để kết nối với cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['terms'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];

        // Kiểm tra xem tên tài khoản đã tồn tại chưa
        $checkUsername = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $checkUsername->bind_param("s", $username);
        $checkUsername->execute();
        $result = $checkUsername->get_result();

        if ($result->num_rows > 0) {
            // Tên tài khoản đã tồn tại
            $error_message = "Tên đăng nhập đã được sử dụng. Vui lòng chọn tên khác.";
        } else {
            // Tạo ID ngẫu nhiên
            $ID = generateID();

            // Cập nhật câu lệnh SQL để bao gồm ID
            $sql = "INSERT INTO users (ID, first_name, last_name, username, password, email) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $ID, $first_name, $last_name, $username, $password, $email);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                echo "Lỗi: " . $stmt->error;
            }
        }
    } else {
        $error_message = "Bạn cần chấp nhận điều khoản và điều kiện.";
    }
}

// Hàm để tạo ID ngẫu nhiên
function generateID() {
    $characters = '0123456789';
    $ID = '';
    for ($i = 0; $i < 6; $i++) {
        $ID .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $ID;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng ký</title>
    <link rel="stylesheet" type="text/css" href="css/register.css">
</head>
<body>
    <div class="form-container">
        <h1>Đăng ký</h1>
        <form method="post" action="">
            <input type="text" name="first_name" placeholder="Họ" required>
            <input type="text" name="last_name" placeholder="Tên" required>
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <input type="email" name="email" placeholder="Gmail" required>

            <!-- Thêm checkbox để chấp nhận điều khoản -->
            <label>
                <input type="checkbox" name="terms" required>
                Tôi chấp nhận điều khoản và điều kiện
            </label>
            
            <!-- Hiển thị thông báo lỗi nếu có -->
            <?php if (isset($error_message)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            
            <button type="submit">Đăng ký</button>
        </form>
        <a href="login.php" class="login">Đăng nhập</a>
    </div>

    <script>
        // Lấy phần tử checkbox và nút submit
        const termsCheckbox = document.querySelector('input[name="terms"]');
        const submitButton = document.querySelector('button[type="submit"]');

        // Lắng nghe sự kiện thay đổi trên checkbox
        termsCheckbox.addEventListener('change', function() {
            // Kích hoạt hoặc vô hiệu hóa nút submit dựa trên trạng thái của checkbox
            submitButton.disabled = !this.checked;
        });
    </script>
</body>
</html>
