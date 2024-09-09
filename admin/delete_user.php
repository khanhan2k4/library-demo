<?php
require 'config.php';

$id = $_GET['id'];

$sql = "DELETE FROM users WHERE id='$id'";

if ($conn->query($sql) === TRUE) {
    echo "Xóa sách thành công!";
    header("Location: danhsach_users.php");
    exit();
} else {
    echo "Lỗi: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>