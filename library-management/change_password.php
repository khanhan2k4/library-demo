<?php

// Liên kết file config.php để kết nối cơ sở dữ liệu
require_once 'config.php'; // Kết nối MySQL được thực hiện từ file config.php

// Kiểm tra nếu form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy thông tin từ form
    $user_id = $_POST['user_id'];
    $current_password = $_POST['current_password']; // Mật khẩu hiện tại
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Truy vấn để lấy mật khẩu hiện tại của người dùng từ database
    $sql = "SELECT password FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Lấy mật khẩu hiện tại từ cơ sở dữ liệu
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        // Kiểm tra mật khẩu hiện tại có khớp với mật khẩu trong database không
        if ($current_password !== $stored_password) {
            echo "Mật khẩu hiện tại không đúng!";
            exit();
        }

        // Kiểm tra nếu mật khẩu mới và xác nhận mật khẩu khớp nhau
        if ($new_password !== $confirm_password) {
            echo "Mật khẩu mới và xác nhận mật khẩu không khớp!";
            exit();
        }

        // Không mã hóa mật khẩu, lưu trực tiếp vào cơ sở dữ liệu
        $sql = "UPDATE users SET password = '$new_password' WHERE id = '$user_id'";

        if ($conn->query($sql) === TRUE) {
            echo "Cập nhật mật khẩu thành công!";
            header("Location: profile.php");
            exit(); // Dừng script sau khi chuyển hướng
        } else {
            echo "Lỗi cập nhật: " . $conn->error;
        }

    } else {
        echo "ID người dùng không tồn tại!";
    }
}

$conn->close();
?>
