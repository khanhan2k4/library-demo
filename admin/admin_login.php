<?php
session_start(); // Bắt đầu session

// Include file config để kết nối cơ sở dữ liệu
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    // Truy vấn kiểm tra thông tin đăng nhập
    $result = $conn->query("SELECT * FROM admin WHERE username='$username' AND password='$password'");

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['username'] = $admin['username'];
        $_SESSION['first_name'] = $admin['first_name']; // Lưu tên người dùng
        $_SESSION['avatar'] = $admin['avatar']; // Lưu avatar của người dùng

        header("Location: home_admin.php");
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu.";
    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Đăng nhập</title>
    <link rel="stylesheet" href="css/admin_login.css">
</head>
<body>
    <div class="login-container">
        <h1>Admin Đăng nhập</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <button type="submit">Đăng nhập</button>
        </form>
    </div>
</body>
</html>
