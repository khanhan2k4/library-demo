<?php
session_start();

// Liên kết file config.php
require_once 'config.php'; // Liên kết file cấu hình để kết nối với cơ sở dữ liệu
// Lấy ID của người dùng từ session
$user_id = $_SESSION['user_id'];

// Truy vấn thông tin người dùng từ bảng users
$sql = "SELECT avatar, first_name, last_name FROM users WHERE id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Lấy thông tin người dùng
    $user = $result->fetch_assoc();
    $avatar = htmlspecialchars($user['avatar']);
    $first_name = htmlspecialchars($user['first_name']);
    $last_name = htmlspecialchars($user['last_name']);
} else {
    $avatar = 'default.png';
    $first_name = 'Guest';
    $last_name = '';
}

// Truy vấn menu cấp 1 (menu chính)
$sql_c1 = "SELECT * FROM menus WHERE parent_id IS NULL ORDER BY position";
$result_c1 = $conn->query($sql_c1);

// Lấy dữ liệu menu con (cấp 2 và cấp 3)
$sql_c2 = "SELECT * FROM menus WHERE parent_id IS NOT NULL ORDER BY parent_id, position";
$result_c2 = $conn->query($sql_c2);

// Tổ chức lại dữ liệu menu con theo parent_id
$submenus = [];
while ($row_c2 = $result_c2->fetch_assoc()) {
    $submenus[$row_c2['parent_id']][] = $row_c2;
}

// Truy vấn số lượng thông báo chưa đọc
$sql = "SELECT COUNT(*) AS notification_count FROM sach_da_tra WHERE read_status = 0"; // Giả sử bạn có cột read_status để theo dõi thông báo đã đọc
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$notification_count = $row['notification_count'];

// Truy vấn dữ liệu hình trên "table images"
$sql = "SELECT image_path FROM images";
$result = $conn->query($sql);

//tìm sách
// Truy vấn dữ liệu hình trên "table images"
// Truy vấn dữ liệu hình trên "table images"
$image_query = "SELECT image_path FROM images";
$image_result = $conn->query($image_query);
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $book_query = "SELECT id, title, author, image_path, tomtat, theloai, publisher, release_date, Day, so_luong FROM truyencotich WHERE id LIKE '%$search%' OR title LIKE '%$search%' OR author LIKE '%$search%' OR theloai LIKE '%$search%' OR publisher LIKE '%$search%'";
} else {
    $book_query = "SELECT * FROM truyencotich ORDER BY release_date DESC"; // Sắp xếp theo ngày phát hành mới nhất
}

$book_result = $conn->query($book_query);
//tìm sách


$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/slider.css">
    <link rel="stylesheet" href="css/ramat.css">
    <script type="text/javascript" src="../library-management/js/jquery.min.js"></script>



    
</head>
<body>

<!-- Thanh menu nằm dọc -->

<div class="sidebar">
    <h2>Library</h2>
    <ul class="menu-c1">
        <?php
        // Lặp qua menu cấp 1
        if ($result_c1->num_rows > 0) {
            while ($row_c1 = $result_c1->fetch_assoc()) {
                $url_c1 = $row_c1['url'] ? htmlspecialchars($row_c1['url']) : '#';
                echo '<li class="menu-c1-container">';
                echo '<div class="menu-c1-title-container"><a class="menu-c1-title" href="' . $url_c1 . '">' . htmlspecialchars($row_c1['title']) . '</a></div>';

                // Hiển thị menu cấp 2 nếu có
                if (isset($submenus[$row_c1['id']])) {
                    echo '<ul class="menu-c2">';
                    foreach ($submenus[$row_c1['id']] as $row_c2) {
                        $url_c2 = $row_c2['url'] ? htmlspecialchars($row_c2['url']) : '#';
                        echo '<li class="menu-c2-item-container">';
                        echo '<div class="menu-c2-item-container-title"><a class="menu-c2-item" href="' . $url_c2 . '">' . htmlspecialchars($row_c2['title']) . '</a></div>';

                        // Hiển thị menu cấp 3 nếu có
                        if (isset($submenus[$row_c2['id']])) {
                            echo '<ul class="menu-c3">';
                            foreach ($submenus[$row_c2['id']] as $row_c3) {
                                $url_c3 = $row_c3['url'] ? htmlspecialchars($row_c3['url']) : '#';
                                echo '<li class="menu-c3-container-item"><a class="menu-c3-item" href="' . $url_c3 . '">' . htmlspecialchars($row_c3['title']) . '</a></li>';
                            }
                            echo '</ul>';
                        }

                        echo '</li>';
                    }
                    echo '</ul>';
                }

                echo '</li>';
            }
        }
        ?>
    </ul>
</div>

<script src="../library-management/js/menu.js"></script>

    <!-- phần nội dung trang chủ -->
<div class="content">
        <!-- Thanh tìm kiếm (search) -->
    <div class="search-container">
    <form action="truyencotich.php" method="get">
      <input  type="text" name="search" placeholder="Tìm kiếm theo ID, tên sách, tác giả, thể loại, nhà xuất bản" value="<?php echo $search; ?>">
      <button type="submit">Search</button>
      </form>
      <div class="avatar" style="display: flex;">
            <!-- Biểu tượng chuông -->
            <div class="notification-icon" id="notification-icon">
                <svg
                    class="bell"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    width="24px"
                    height="24px"
                >
                    <path d="M0 0h24v24H0z" fill="none" />
                    <path
                        d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-2.97-2.16-5.43-5-5.92V4c0-.83-.67-1.5-1.5-1.5S10 3.17 10 4v1.08C7.16 5.57 5 8.03 5 11v5l-1 1v1h16v-1l-1-1z"
                        fill="black"
                    />
                </svg>
                <span class="notification-count" id="notification-count"><?php echo $notification_count; ?></span>
            </div>
            <a href="<?php echo isset($_SESSION['user_id']) ? 'profile.php' : 'login.php'; ?>" style="display: flex; align-items: center;">
                <img src="avatar/<?php echo $avatar; ?>" alt="Avatar" />
                <h4><?php echo $first_name . ' ' . $last_name; ?></h4>
            </a>
        </div>  
    </div>

    <script>
        document.getElementById('notification-icon').addEventListener('click', function() {
            // Chuyển hướng đến trang thông báo
            window.location.href = 'notification.php';

            // Ẩn số thông báo khi nhấp vào chuông
            document.getElementById('notification-count').style.display = 'none';

            // Gửi yêu cầu đến server để đánh dấu thông báo là đã đọc
            fetch('mark_notifications_read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'action=mark_all_as_read'
            });
        });
    </script>
 
  
 <!--Hiển thị truyen ranh  -->

 <div id="Sách vừa ra mắt" class="Show_eye">
    <h3>Truyện Cổ Tích.</h3>
    <div class="book-grid">
        <?php while ($book = $book_result->fetch_assoc()): ?>
            <div class="book-item">
                <a href="book-detail-cotich.php?id=<?php echo htmlspecialchars($book['id']); ?>" class="book-link">
                    <img src="images/<?php echo htmlspecialchars($book['image_path']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-image">
                    <div class="book-info">
                        <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                        <p><?php echo htmlspecialchars($book['author']); ?></p>
                        <p><?php echo htmlspecialchars($book['release_date']); ?></p>
                        <button>Đặt mượn</button>
                    </div>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</div>
     <!-- kết thúc phần nội dung trang chủ -->
</body>
</html>
