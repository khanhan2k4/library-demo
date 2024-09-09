<?php
session_start();
require_once 'config.php'; // Kết nối với cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    die("Bạn phải đăng nhập để thực hiện hành động này.");
}

$user_id = $_SESSION['user_id'];

// Kiểm tra nếu có `book_id` từ yêu cầu POST
if (isset($_POST['book_id'])) {
    $book_id = $conn->real_escape_string($_POST['book_id']); // Xử lý book_id để tránh SQL Injection

    // Xóa sách khỏi bảng `sach_da_muon`
    $delete_sql = "DELETE FROM sach_da_muon WHERE user_id = ? AND book_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("ss", $user_id, $book_id); // Sử dụng `ss` cho kiểu chuỗi (VARCHAR)
    
    if ($stmt->execute()) {
        // Danh sách các bảng cần kiểm tra
        $book_tables = ['books', 'popular_books', 'truyentranh', 'truyenkinhdi','tieuthuyet','truyencotich','sachchuyennganh','sachtruyencamhung','sachkhamphabian','sachvanhoaxahoi']; // Thêm các bảng khác tại đây

        $updated = false;
        foreach ($book_tables as $table) {
            // Cập nhật lại số lượng sách trong bảng hiện tại
            $update_sql = "UPDATE $table SET so_luong = so_luong + 1 WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("s", $book_id); // Kiểu chuỗi cho book_id
            $update_stmt->execute();

            // Nếu số lượng sách được cập nhật, đánh dấu thành công và dừng lại
            if ($conn->affected_rows > 0) {
                $updated = true;
                break;
            }
        }

        if ($updated) {
            echo "Sách đã được trả thành công.";
        } else {
            echo "Không thể cập nhật số lượng sách, ID sách không hợp lệ.";
        }
    } else {
        echo "Có lỗi xảy ra khi trả sách.";
    }

    $stmt->close();
} else {
    echo "ID sách không hợp lệ.";
}

$conn->close();
?>
