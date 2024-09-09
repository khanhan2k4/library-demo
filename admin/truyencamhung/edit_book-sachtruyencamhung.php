<?php
$conn = new mysqli('localhost', 'root', '', 'library');

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

// Lấy dữ liệu thể loại từ bảng `the_loai`
$sql_theloai = "SELECT ten_the_loai FROM the_loai";
$result_theloai = $conn->query($sql_theloai);

// Kiểm tra nếu form đã được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $tomtat = $_POST['tomtat'];
    $theloai = $_POST['theloai'];
    $publisher = $_POST['publisher'];
    $release_date = $_POST['release_date'];
    $Day = $_POST['Day'];
    $so_luong = $_POST['so_luong'];

    // Lấy thông tin ảnh sách hiện tại từ cơ sở dữ liệu
    $sql = "SELECT image_path FROM sachtruyencamhung WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentImagePath = $row['image_path'];
    } else {
        echo "Không tìm thấy sách.";
        exit();
    }

    // Xử lý ảnh sách
    $image_path = $currentImagePath; // Giữ ảnh sách hiện tại mặc định

    if (isset($_FILES['image_path']) && $_FILES['image_path']['error'] === UPLOAD_ERR_OK) {
        // Xử lý upload ảnh sách mới
        $targetDir = '../images/';
        $targetFile = $targetDir . basename($_FILES['image_path']['name']);
        
        // Di chuyển file tải lên
        if (move_uploaded_file($_FILES['image_path']['tmp_name'], $targetFile)) {
            $image_path = basename($_FILES['image_path']['name']);
        } else {
            echo "Lỗi khi tải lên file.";
            exit();
        }
    }

    // Cập nhật thông tin sách
    $sql = "UPDATE sachtruyencamhung SET title='$title', author='$author', image_path='$image_path', tomtat='$tomtat', theloai='$theloai', publisher='$publisher', release_date='$release_date', Day='$Day', so_luong='$so_luong' WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Cập nhật sách thành công!";
        header("Location: dulieutrangchu-sachtruyencamhung.php");
        exit();
    } else {
        echo "Lỗi: " . $sql . "<br>" . $conn->error;
    }
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM sachtruyencamhung WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Không tìm thấy sách.";
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa thông tin sách</title>
    <link rel="stylesheet" href="../css/edit_books.css">
</head>
<body>
    <div class="container">
        <form action="edit_book-sachtruyencamhung.php" method="post" enctype="multipart/form-data">
            <h2>Sửa thông tin sách</h2>
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?php echo $row['title']; ?>" required>

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" value="<?php echo $row['author']; ?>" required>

            <label for="image_path">Image:</label>
            <img src="images/<?php echo $row['image_path']; ?>" alt="Current Image" style="width: 100px; height: 100px;">
            <input type="file" id="image_path" name="image_path" accept="image/*">

            <label for="tomtat">Tóm tắt:</label>
            <input type="text" id="tomtat" name="tomtat" value="<?php echo $row['tomtat']; ?>" required>

            <label for="theloai">Thể loại:</label>
            <select id="theloai" name="theloai" required>
                <option value="">Chọn thể loại</option>
                <?php
                // Tạo các tùy chọn thể loại từ cơ sở dữ liệu
                if ($result_theloai->num_rows > 0) {
                    while($theloai_row = $result_theloai->fetch_assoc()) {
                        $selected = ($theloai_row["ten_the_loai"] == $row['theloai']) ? "selected" : "";
                        echo '<option value="' . $theloai_row["ten_the_loai"] . '" ' . $selected . '>' . $theloai_row["ten_the_loai"] . '</option>';
                    }
                } else {
                    echo '<option value="">Không có thể loại</option>';
                }
                ?>
            </select>

            <label for="publisher">Nhà xuất bản:</label>
            <input type="text" id="publisher" name="publisher" value="<?php echo $row['publisher']; ?>" required>

            <label for="release_date">Release date:</label>
            <input type="date" id="release_date" name="release_date" value="<?php echo $row['release_date']; ?>" required>

            <label for="Day">Day:</label>
            <input type="text" id="Day" name="Day" value="<?php echo $row['Day']; ?>" required>

            <label for="so_luong">Số lượng:</label>
            <input type="number" id="so_luong" name="so_luong" value="<?php echo $row['so_luong']; ?>" required>

            <div style="display: flex;">
                <input type="submit" value="Cập nhật sách">
                <input type="button" value="Hủy" onclick="window.history.back();">
            </div>
        </form>
    </div>
</body>
</html>
