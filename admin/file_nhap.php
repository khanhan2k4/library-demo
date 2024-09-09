<?php

require 'config.php';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];

    // Kiểm tra nếu giá trị tìm kiếm là số
    if (is_numeric($search)) {
        // Sử dụng câu lệnh chuẩn bị để an toàn hơn
        $sql = "SELECT user_id, book_id, ten_sach, ngay_muon, ten_nguoi_muon 
                FROM sach_da_muon 
                WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $search); // Chỉ cho phép kiểu số nguyên
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        echo "Vui lòng nhập một số hợp lệ để tìm kiếm theo User ID.";
        $result = false;
    }
} else {
    $sql = "SELECT user_id, book_id, ten_sach, ngay_muon, ten_nguoi_muon FROM sach_da_muon";
    $result = $conn->query($sql);
}

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
        <form method="get" action="danhsach_sachdamuon.php">
            <input style="width:22%;" type="text" name="search" placeholder="Tìm kiếm theo User ID" value="<?php echo htmlspecialchars($search); ?>">
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
        <th>Actions</th>
    </tr>
    <?php
    if ($result && $result->num_rows > 0) {
        $stt = 1;
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $stt . "</td>";
            echo "<td>" . htmlspecialchars($row["user_id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["book_id"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["ten_sach"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["ngay_muon"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["ten_nguoi_muon"]) . "</td>";
            echo "<td class='action-links'>
                    <a href='edit_sachdamuon.php?id=" . $row["user_id"] . "'>Sửa</a>
                    <a href='delete_sachdamuon.php?id=" . $row["user_id"] . "' onclick='return confirm(\"Bạn có chắc chắn muốn xóa mục này?\")'>Xóa</a>
                  </td>";
            echo "</tr>";

            $stt++;
        }
    } else {
        echo "<tr><td colspan='7'>Không có dữ liệu</td></tr>";
    }
    $conn->close();
    ?>
    </table>

</body>
</html>
