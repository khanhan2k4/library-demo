<?php
require 'config.php';

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql = "SELECT id, username, password, first_name, last_name, email, avatar, so_dien_thoai 
    FROM users 
    WHERE id LIKE '%$search%' OR username LIKE '%$search%' OR first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%' OR so_dien_thoai LIKE '%$search%'";
} else {
    $sql = "SELECT id, username, password, first_name, last_name, email, avatar, so_dien_thoai FROM users";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách người dùng</title>
    <link rel="stylesheet" href="css/dulieu.css">
    <link rel="stylesheet" href="css/dulieu_search.css">
</head>
<body>

   <div class="fixed-menu">
        <h2>Danh sách người dùng</h2>
        <form method="get" action="danhsach_users.php">
            <input style="width:22%;" type="text" name="search" placeholder="Tìm kiếm theo ID, tên người dùng, tên, email, số điện thoại" value="<?php echo $search; ?>">
            <input type="submit" value="Tìm kiếm">
            <input type="button" value="Hủy tìm kiếm" onclick="window.history.back();">
            <input type="button" value="Trang chủ" onclick="window.location.href='home_admin.php';">
        </form>
   </div>

    <table class="bang">
    <tr>
        <th>STT</th>
        <th>ID</th>
        <th>Username</th>
        <th>Password</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Avatar</th>
        <th>Số điện thoại</th>
        <th>Actions</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        $stt = 1;
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $stt . "</td>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["password"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["first_name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["last_name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
            echo "<td><img src='avatar/" . $row["avatar"] . "' alt='Avatar' width='50'></td>";
            echo "<td>" . htmlspecialchars($row["so_dien_thoai"]) . "</td>";
            echo "<td class='action-links'>
                    <a href='edit_user.php?id=" . $row["id"] . "'>Sửa</a>
                    <a href='delete_user.php?id=" . $row["id"] . "' onclick='return confirm(\"Bạn có chắc chắn muốn xóa người dùng này?\")'>Xóa</a>
                  </td>";
            echo "</tr>";

            $stt++;
        }
    } else {
        echo "<tr><td colspan='10'>Không có dữ liệu</td></tr>";
    }
    $conn->close();
    ?>
    </table>

</body>
</html>
