<?php
session_start(); // Đảm bảo rằng session được khởi tạo để sử dụng thông tin admin

require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $book_id = $_POST['book_id'];

    // Lấy tên admin từ session
    $admin_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'admin'; // Sử dụng giá trị từ session hoặc mặc định là 'admin'

    // Kiểm tra xem sách đã được trả hay chưa
    $check_query = "SELECT * FROM sach_da_tra WHERE user_id = '$user_id' AND book_id = '$book_id'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        echo "Sách đã được trả trước đó.";
    } else {
        // Lấy thông tin tên sách từ bảng sach_da_muon
        $book_query = "SELECT ten_sach FROM sach_da_muon WHERE user_id = '$user_id' AND book_id = '$book_id'";
        $book_result = $conn->query($book_query);
        $row = $book_result->fetch_assoc();
        $ten_sach = $row['ten_sach'];

        // Chuẩn bị thông báo trả sách
        $thong_bao = $ten_sach . " đã trả thành công";

        // Thêm dữ liệu vào bảng sach_da_tra với thông báo trả sách
        $insert_query = "INSERT INTO sach_da_tra (user_id, book_id, thong_bao, ngay_tra, admin_name) 
                         VALUES ('$user_id', '$book_id', '$thong_bao', NOW(), '$admin_name')";
        
        if ($conn->query($insert_query) === TRUE) {
            // Xóa sách đã trả khỏi bảng sach_da_muon
            $delete_query = "DELETE FROM sach_da_muon 
                             WHERE user_id = '$user_id' AND book_id = '$book_id'";
            $conn->query($delete_query);

            echo "success"; // Trả về thông báo thành công
        } else {
            echo "error"; // Trả về thông báo lỗi
        }
    }
}

$conn->close();
?>
