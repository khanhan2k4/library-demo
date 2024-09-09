<?php
$conn = new mysqli('localhost', 'root', '', 'library');

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT id, title, author, image_path,tomtat,theloai,publisher, release_date, Day, so_luong FROM popular_books WHERE id LIKE '%$search%' OR title LIKE '%$search%' OR author LIKE '%$search%' OR theloai LIKE '%$search%' OR publisher LIKE '%$search%'";
} else {
    $sql = "SELECT id, title, author, image_path,tomtat,theloai,publisher, release_date, Day, so_luong FROM popular_books";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách sách phổ biến</title>
    <link rel="stylesheet" href="../css/dulieu.css">
    <link rel="stylesheet" href="../css/dulieu_search.css">
    <style>
        
    </style>
</head>
<body>

    <div class="fixed-menu">
        <h2>Danh sách sách phổ biến</h2>
        <form method="get" action="dulieutrangchu-sachphobien.php">
            <input style="width:21%;" type="text" name="search" placeholder="Tìm kiếm theo ID, tên sách, tác giả, thể loại, nhà xuất bản" value="<?php echo $search; ?>">
            <input type="submit" value="Tìm kiếm">
            <input type="button" value="Hủy tìm kiếm" onclick="window.history.back();">
            <input type="button" value="Trang chủ" onclick="window.location.href='../home_admin.php';">
        </form>
        <a href="add_book-sachphobien.php"><input type="button" value="Thêm sách mới"></a>
    </div>
    
    <table class="bang">
    <tr>
        <th>STT</th>
        <th>ID</th>
        <th>Title</th>
        <th>Author</th>
        <th>Image</th>
        <th>Tóm tắt</th>
        <th>Thể loại</th>
        <th>Nhà xuất bản</th>
        <th>Release Date</th>
        <th>Dãy</th>
        <th>Số lượng</th>
        <th>Actions</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        $stt = 1; // Khởi tạo biến đếm STT
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $stt . "</td>"; // Hiển thị STT
            echo "<td>" . $row["id"] . "</td>";
            //title
            echo '<td>';
            if ($row["title"] !== null) {
                echo '<span data-toggle="tooltip" title="' . htmlspecialchars($row["title"]) . '">' . substr($row["title"], 0, 30) . '...</span>';
            } else {
                echo 'Null';
            }
            echo '</td>';
            //title
            //author
            echo '<td>';
            if ($row["author"] !== null) {
                echo '<span data-toggle="tooltip" title="' . htmlspecialchars($row["author"]) . '">' . substr($row["author"], 0, 6) . '...</span>';
            } else {
                echo 'Null';
            }
            echo '</td>';
            //author
            echo "<td><img src='../images/" . $row["image_path"] . "' alt='Book Image' width='50'></td>";
            //tóm tắt
            echo '<td>';
            if ($row["tomtat"] !== null) {
                echo '<span data-toggle="tooltip" title="' . htmlspecialchars($row["tomtat"]) . '">' . substr($row["tomtat"], 0, 50) . '...</span>';
            } else {
                echo 'Không có mô tả';
            }
            echo '</td>';
            //tóm tắt
           //thể loại
           echo '<td>';
           if ($row["theloai"] !== null) {
               echo '<span data-toggle="tooltip" title="' . htmlspecialchars($row["theloai"]) . '">' . substr($row["theloai"], 0, 20) . '...</span>';
           } else {
               echo 'Null';
           }
           echo '</td>';
           //thể loại
          //nhà xuất bản
          echo '<td>';
          if ($row["publisher"] !== null) {
              echo '<span data-toggle="tooltip" title="' . htmlspecialchars($row["publisher"]) . '">' . substr($row["publisher"], 0, 20) . '...</span>';
          } else {
              echo 'Null';
          }
          echo '</td>';
          //nhà xuất bản
            echo "<td>" . $row["release_date"] . "</td>";
            //Dãy 
            echo '<td>';
            if ($row["Day"] !== null) {
                echo '<span data-toggle="tooltip" title="' . htmlspecialchars($row["Day"]) . '">' . substr($row["Day"], 0, 20) . '...</span>';
            } else {
                echo 'Null';
            }
            echo '</td>';
            //Dãy
            echo "<td>" . $row["so_luong"] . "</td>";
            echo "<td class='action-links'>
                    <a href='edit_book-sachphobien.php?id=" . $row["id"] . "'>Sửa</a>
                    <a href='delete_book-sachphobien.php?id=" . $row["id"] . "' onclick='return confirm(\"Bạn có chắc chắn muốn xóa sách này?\")'>Xóa</a>
                  </td>";
            echo "</tr>";

            $stt++; // Tăng STT sau mỗi lần lặp
        }
    } else {
        echo "<tr><td colspan='9'>Không có dữ liệu</td></tr>"; // Sửa lại colspan cho đúng số cột
    }
    $conn->close();
    ?>
</table>

</body>
</html>