<?php

require_once 'config.php'; // Liên kết file cấu hình để kết nối với cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id > 0) {
        // Xóa tin nhắn khỏi cơ sở dữ liệu
        $delete_query = "DELETE FROM sach_da_tra WHERE id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }

        $stmt->close();
    } else {
        echo 'error';
    }
}

$conn->close();
?>
