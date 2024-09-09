<?php

require_once 'config.php'; // Liên kết file cấu hình để kết nối với cơ sở dữ liệu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'mark_all_as_read') {
        // Cập nhật tất cả thông báo thành đã đọc
        $update_query = "UPDATE sach_da_tra SET read_status = 1 WHERE read_status = 0";
        if ($conn->query($update_query) === TRUE) {
            echo 'success';
        } else {
            echo 'error';
        }
    }
}

$conn->close();
?>
