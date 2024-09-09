<?php
// Kết nối tới cơ sở dữ liệu
require_once 'config.php'; // Liên kết file cấu hình để kết nối với cơ sở dữ liệu

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['book_id'];

    // Danh sách các bảng cần kiểm tra
    $tables = ['books', 'popular_books', 'truyentranh', 'truyenkinhdi','tieuthuyet','truyencotich','sachchuyennganh','sachtruyencamhung','sachkhamphabian','sachvanhoaxahoi']; // Thêm tên các bảng tại đây

    $book_found = false;
    $availability = "book_not_found";

    foreach ($tables as $table) {
        // Kiểm tra số lượng sách từ bảng hiện tại
        $sql = "SELECT so_luong FROM $table WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $book_id);
        $stmt->execute();
        $stmt->bind_result($so_luong);
        $stmt->fetch();
        $stmt->close();

        if (isset($so_luong)) {
            // Nếu tìm thấy sách trong bảng hiện tại
            $book_found = true;
            if ($so_luong >= 1) {
                $availability = "available";
            } else {
                $availability = "unavailable";
            }
            break; // Dừng vòng lặp khi đã tìm thấy sách trong một bảng
        }
    }

    echo $availability; // Trả về kết quả tìm kiếm
}

$conn->close();
?>
