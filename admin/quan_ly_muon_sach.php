<?php
// Include file config để kết nối cơ sở dữ liệu
require 'config.php';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT user_id, book_id, ten_sach, ngay_muon, ten_nguoi_muon, ngay_tra 
            FROM sach_da_muon 
            WHERE user_id LIKE '%$search%'";
} else {
    $sql = "SELECT user_id, book_id, ten_sach, ngay_muon, ten_nguoi_muon, ngay_tra 
            FROM sach_da_muon";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách sách đã mượn</title>
    <link rel="stylesheet" href="css/dulieu.css">
    <link rel="stylesheet" href="css/dulieu_search.css">
</head>
<body>

   <div class="fixed-menu">
        <h2>Danh sách sách đã mượn</h2>
        <form method="get" action="quan_ly_muon_sach.php">
            <input style="width:22%;" type="text" name="search" placeholder="Tìm kiếm theo User ID" value="<?php echo $search; ?>">
            <input type="submit" value="Tìm kiếm">
            <input type="button" value="Hủy tìm kiếm" onclick="window.history.back();">
            <input type="button" value="Trang chủ" onclick="window.location.href='home_admin.php';">
        </form>
   </div>

   <table class="bang">
    <tr>
        <th>STT</th>
        <th>User ID</th>
        <th>Mã sách</th>
        <th>Tên sách</th>
        <th>Ngày mượn</th>
        <th>Tên người mượn</th>
        <th>Ngày trả sách</th>
        <th>Trả sách</th> <!-- Thêm cột Trả sách -->
    </tr>
    <?php
    if ($result->num_rows > 0) {
        $stt = 1;
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $stt . "</td>";
            echo "<td>" . htmlspecialchars($row["user_id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["book_id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["ten_sach"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["ngay_muon"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["ten_nguoi_muon"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["ngay_tra"]) . "</td>";
            echo "<td><input type='checkbox' class='return-book' data-user-id='" . $row["user_id"] . "' data-book-id='" . $row["book_id"] . "'></td>"; // Checkbox
            echo "</tr>";

            $stt++;
        }
    } else {
        echo "<tr><td colspan='8'>Không có dữ liệu</td></tr>";
    }
    ?>
    <script>
document.querySelectorAll('.return-book').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            let userId = this.getAttribute('data-user-id');
            let bookId = this.getAttribute('data-book-id');
            
            // Hiển thị thông báo xác nhận
            let confirmReturn = confirm("Bạn có muốn trả sách không?");
            
            if (confirmReturn) {
                // Nếu người dùng chọn "Yes", gửi yêu cầu đến server để xử lý trả sách
                fetch('process_return.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `user_id=${userId}&book_id=${bookId}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        alert('Đã trả sách thành công');
                        // Reload lại trang để cập nhật danh sách
                        location.reload();
                    } else {
                        alert('Có lỗi xảy ra');
                    }
                });
            } else {
                // Nếu người dùng chọn "No", bỏ chọn checkbox
                this.checked = false;
            }
        }
    });
});
</script>

</table>
</body>
</html>
