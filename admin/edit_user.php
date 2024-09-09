<?php
require 'config.php';

// Kiểm tra nếu form đã được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $so_dien_thoai = $_POST['so_dien_thoai'];

    // Lấy thông tin avatar hiện tại từ cơ sở dữ liệu
    $sql = "SELECT avatar FROM users WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentAvatar = $row['avatar'];
    } else {
        echo "Không tìm thấy người dùng.";
        exit();
    }

    // Xử lý avatar
    $avatar = $currentAvatar; // Giữ avatar hiện tại mặc định

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        // Xử lý upload avatar mới
        $targetDir = 'avatar/';
        $targetFile = $targetDir . basename($_FILES['avatar']['name']);
        
        // Di chuyển file tải lên
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
            $avatar = basename($_FILES['avatar']['name']);
        } else {
            echo "Lỗi khi tải lên file.";
            exit();
        }
    }

    // Cập nhật thông tin người dùng
    $sql = "UPDATE users SET username='$username', password='$password', first_name='$first_name', last_name='$last_name', email='$email', avatar='$avatar', so_dien_thoai='$so_dien_thoai' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Cập nhật thông tin người dùng thành công!";
        header("Location: danhsach_users.php");
        exit();
    } else {
        echo "Lỗi: " . $sql . "<br>" . $conn->error;
    }
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM users WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy người dùng.";
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa thông tin người dùng</title>
    <link rel="stylesheet" href="css/edit_books.css">
</head>
<body>
    <div class="container">
        <form action="edit_user.php" method="post" enctype="multipart/form-data">
            <h2>Sửa thông tin người dùng</h2>
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo $row['username']; ?>" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?php echo $row['password']; ?>" required>

           <div style="display: flex;"> Hiển thị mật khẩu<input type="checkbox" id="showPassword" onclick="togglePassword()"> </div>

            <label for="first_name">Họ:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo $row['first_name']; ?>" required>

            <label for="last_name">Tên:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo $row['last_name']; ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $row['email']; ?>" required>

            <label for="avatar">Avatar:</label>
            <input type="file" id="avatar" name="avatar" accept="image/*">

            <label for="so_dien_thoai">Số điện thoại:</label>
            <input type="text" id="so_dien_thoai" name="so_dien_thoai" value="<?php echo $row['so_dien_thoai']; ?>" required>
            
            <div style="display: flex;">
                <input type="submit" value="Cập nhật người dùng">
                <input type="button" value="Hủy" onclick="window.history.back();">
            </div>
        </form>
    </div>

    <script>
    function togglePassword() {
        var passwordField = document.getElementById('password');
        var showPassword = document.getElementById('showPassword');
        if (showPassword.checked) {
            passwordField.type = 'text';
        } else {
            passwordField.type = 'password';
        }
    }
    </script>
</body>
</html>


