<?php
// submit_comment.php file này danh cho bảng danhsach
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = $conn->real_escape_string($_POST['comment']);
    $rating = (int)$_POST['rating'];
    $book_id = $conn->real_escape_string($_POST['book_id']);
    $user_id = $_SESSION['user_id'] ?? null;

    if ($user_id && !empty($comment) && $rating > 0) {
        $sql = "INSERT INTO danhgia (comment, rating, book_id, user_id, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisi", $comment, $rating, $book_id, $user_id);

        if ($stmt->execute()) {
            echo "Bình luận đã được lưu.";
        } else {
            echo "Lỗi: " . $stmt->error;
        }
    } else {
        echo "Vui lòng đăng nhập và điền đầy đủ thông tin.";
    }
}
?>