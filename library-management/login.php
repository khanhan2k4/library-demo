<?php
session_start(); // Bắt đầu session

// Liên kết file config.php
require_once 'config.php'; // Liên kết tới file cấu hình

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Bảo vệ chống SQL injection
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    // Truy vấn kiểm tra tài khoản
    $result = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password'");
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Lưu thông tin người dùng vào session
        $_SESSION['user_id'] = $user['id'];  // Lưu id người dùng vào session
        $_SESSION['is_admin'] = $user['is_admin']; // Kiểm tra nếu là admin
        $_SESSION['first_name'] = $user['first_name']; // Lưu tên người dùng
        $_SESSION['avatar'] = $user['avatar']; // Lưu avatar của người dùng
        
        // Điều hướng đến trang phù hợp (admin hoặc trang chính)
        header("Location: index.php");
        exit(); // Dừng script để đảm bảo không có mã nào chạy sau header
    } else {
        $error = "Sai tên đăng nhập hoặc mật khẩu.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css"> <!-- Liên kết đến file CSS -->
    <style>
        /* CSS cho giao diện đăng nhập */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        .login-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #5cb85c;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #4cae4c;
        }

        .login-container a {
            text-decoration: none;
            color: #333;
            display: block;
            margin-top: 10px;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h1>Đăng nhập</h1>
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="">
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng nhập</button>
    </form>
    <a href="register.php">Đăng ký</a>
    <a href="forgot_password.php">Quên mật khẩu?</a> <!-- Thêm liên kết quên mật khẩu -->
</div>

</body>
</html>
