<?php
// Bắt đầu phiên làm việc để truy cập session
session_start();

// Kiểm tra nếu admin đã đăng nhập
if (!isset($_SESSION['admin_id'])) {
    die("Bạn cần đăng nhập để cập nhật thông tin.");
}

// Kết nối đến MySQL
require 'config.php';

// Kiểm tra nếu form đã được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form và lọc dữ liệu đầu vào
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $admin_id = $_SESSION['admin_id']; // Lấy ID admin từ session

    // Kiểm tra nếu có upload avatar mới
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $avatar_name = $_FILES['avatar']['name'];
        $avatar_tmp_name = $_FILES['avatar']['tmp_name'];
        $avatar_folder = 'avatar/' . $avatar_name;

        // Di chuyển tệp avatar đến thư mục lưu trữ
        if (move_uploaded_file($avatar_tmp_name, $avatar_folder)) {
            // Cập nhật avatar và các thông tin khác
            $sql = "UPDATE admin SET avatar='$avatar_folder', first_name='$first_name', last_name='$last_name', email='$email', so_dien_thoai='$phone' WHERE admin_id='$admin_id'";
        } else {
            echo "Không thể tải lên tệp.";
        }
    } else {
        // Nếu không có avatar, chỉ cập nhật thông tin khác
        $sql = "UPDATE admin SET first_name='$first_name', last_name='$last_name', email='$email', so_dien_thoai='$phone' WHERE admin_id='$admin_id'";
    }

    // Thực hiện câu lệnh SQL
    if ($conn->query($sql) === TRUE) {
        // Sau khi cập nhật thành công, chuyển hướng admin đến profile.php
        header("Location: profile.php");
        exit(); // Dừng script sau khi chuyển hướng
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

// Đóng kết nối
$conn->close();
?>
