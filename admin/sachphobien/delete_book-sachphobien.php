<?php
$conn = new mysqli('localhost', 'root', '', 'library');

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

$id = $_GET['id'];

$sql = "DELETE FROM popular_books WHERE id='$id'";

if ($conn->query($sql) === TRUE) {
    echo "Xóa sách thành công!";
    header("Location: dulieutrangchu-sachphobien.php");
    exit();
} else {
    echo "Lỗi: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>