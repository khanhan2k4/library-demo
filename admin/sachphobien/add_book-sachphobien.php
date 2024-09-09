<?php
// Kết nối đến cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'library');

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

$sql_theloai = "SELECT id, ten_the_loai FROM the_loai";
$result_theloai = $conn->query($sql_theloai);

// Kiểm tra nếu form đã được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $image_path = $_POST['image_path'];
    $tomtat = $_POST['tomtat'];
    $theloai = $_POST['theloai'];
    $publisher = $_POST['publisher'];
    $release_date = $_POST['release_date'];
    $Day = $_POST['Day'];
    $so_luong = $_POST['so_luong'];

    $sql = "INSERT INTO popular_books (title, author, image_path,tomtat,theloai,publisher, release_date,Day,so_luong) VALUES ('$title', '$author', '$image_path', ' $tomtat','$theloai','$publisher','$release_date', '$Day','$so_luong')";
    if ($conn->query($sql) === TRUE) {
        // Chuyển hướng về trang dulieutrangchu.php sau khi thêm sách thành công
        header("Location: dulieutrangchu-sachphobien.php");
        exit();
    } else {
        echo "Lỗi: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm sách mới</title>
    <link rel="stylesheet" href="../css/add-books.css">
    
</head>
<body>
    <div class="container">
        <form action="add_book-sachphobien.php" method="post">
            <h2>Thêm sách mới</h2>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            
            <label for="author">Author:</label>
            <input type="text" id="author" name="author" required>
            
           
            <label for="image_path">Image:</label>
            <input type="file" id="image_path" name="image_path" accept="image/*" required>

            <label for="tomtat">Tóm tắt:</label>
            <input type="text" id="tomtat" name="tomtat" required>

            <label for="theloai">Thể loại:</label>
    <select id="theloai" name="theloai" required>
        <option value="">Chọn thể loại</option>
        
        <?php
        // Tạo các tùy chọn thể loại từ cơ sở dữ liệu
        if ($result_theloai->num_rows > 0) {
            while($row = $result_theloai->fetch_assoc()) {
                echo '<option value="' . $row["ten_the_loai"] . '">' . $row["ten_the_loai"] . '</option>';
            
            }
        } else {
            echo '<option value="">Không có thể loại</option>';
        }
        ?>
    </select>
            
            <label for="publisher">Nhà xuất bản:</label>
            <input type="text" id="publisher" name="publisher" required>
            
            
            <label for="release_date">Release date:</label>
            <input type="date" id="release_date" name="release_date" required>

            <label for="Day">Dãy:</label>
            <input type="text" id="Day" name="Day" required>
            
            <label for="so_luong">Số lượng:</label>
            <input type="number" id="so_luong" name="so_luong" required>
            
            <div style="display: flex;">
                <input type="submit" value="Thêm sách">
                <input type="button" value="Hủy" onclick="window.history.back();">
            </div>


        </form>
    </div>
</body>
</html>