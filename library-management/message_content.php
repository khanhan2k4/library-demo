<?php
header('Content-Type: application/json');
session_start();

require_once 'config.php'; // Liên kết file cấu hình để kết nối với cơ sở dữ liệu

// Kiểm tra nếu user_id có trong session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Người dùng chưa đăng nhập']);
    exit();
}

$user_id = $_SESSION['user_id']; // Lấy user_id từ session
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // Truy vấn dữ liệu từ bảng sach_da_tra theo id và user_id
    $sql = "SELECT book_id, thong_bao FROM sach_da_tra WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $user_id); // Gán giá trị id và user_id vào câu truy vấn
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Không tìm thấy dữ liệu']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'ID không hợp lệ']);
}

$conn->close();
?>
