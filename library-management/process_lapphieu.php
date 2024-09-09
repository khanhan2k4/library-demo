<?php
session_start();
require_once 'config.php'; // Kết nối với cơ sở dữ liệu

// Kiểm tra nếu form đã được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $book_id = $conn->real_escape_string($_POST['book_id']);
    $ho_va_ten = $conn->real_escape_string($_POST['ho_va_ten']);
    $so_dien_thoai = $conn->real_escape_string($_POST['so_dien_thoai']);
    $email = $conn->real_escape_string($_POST['email']);

    // Kiểm tra nếu ID người dùng đúng với tài khoản đang đăng nhập
    if ($user_id != $_SESSION['user_id']) {
        // Nếu không đúng, thông báo lỗi
        echo "ID không đúng với tài khoản hiện tại. Vui lòng hãy kiểm tra lại ID và thử lại.";
        exit();
    }

    // Danh sách các bảng có cấu trúc giống nhau
    $tables = ['books', 'popular_books', 'truyentranh','truyenkinhdi','tieuthuyet','truyencotich','sachchuyennganh','sachtruyencamhung','sachkhamphabian','sachvanhoaxahoi']; // Bạn có thể thêm các bảng khác vào đây

    // Bắt đầu giao dịch
    $conn->begin_transaction();

    try {
        $ten_sach = '';
        $sach_da_tim_thay = false;

        // Lặp qua các bảng để tìm sách
        foreach ($tables as $table) {
            $sql_get_title = "SELECT title FROM $table WHERE id = ?";
            $stmt_title = $conn->prepare($sql_get_title);
            $stmt_title->bind_param("s", $book_id);
            $stmt_title->execute();
            $stmt_title->bind_result($ten_sach);
            $stmt_title->fetch();
            $stmt_title->close();

            if (!empty($ten_sach)) {
                $sach_da_tim_thay = true;
                break; // Dừng lại nếu tìm thấy sách
            }
        }

        if ($sach_da_tim_thay) {
            $da_giam_so_luong = false;

            // Lặp qua các bảng để giảm số lượng sách
            foreach ($tables as $table) {
                $sql_update_quantity = "UPDATE $table SET so_luong = so_luong - 1 WHERE id = ? AND so_luong > 0";
                $stmt_update = $conn->prepare($sql_update_quantity);
                $stmt_update->bind_param("s", $book_id);
                $stmt_update->execute();

                if ($stmt_update->affected_rows > 0) {
                    $da_giam_so_luong = true;
                    break; // Dừng lại nếu đã giảm số lượng sách
                }
            }

            if ($da_giam_so_luong) {
                // Lưu thông tin vào bảng sach_da_muon cùng với tên sách
                $sql_insert_sach_da_muon = "INSERT INTO sach_da_muon (user_id, book_id, ten_sach, ngay_muon, ten_nguoi_muon) VALUES (?, ?, ?, NOW(), ?)";
                $stmt_insert = $conn->prepare($sql_insert_sach_da_muon);
                $stmt_insert->bind_param("isss", $user_id, $book_id, $ten_sach, $ho_va_ten);
                $stmt_insert->execute();

                // Xác nhận giao dịch
                $conn->commit();

                // Chuyển hướng người dùng đến trang 'sach_da_muon.php'
                header("Location: sach_da_muon.php");
                exit;
            } else {
                throw new Exception("Sách không còn để mượn.");
            }
        } else {
            throw new Exception("Không tìm thấy tên sách.");
        }
    } catch (Exception $e) {
        // Hủy giao dịch nếu có lỗi
        $conn->rollback();
        die("Có lỗi xảy ra: " . $e->getMessage());
    }
} else {
    die("Yêu cầu không hợp lệ.");
}

$conn->close();
?>
