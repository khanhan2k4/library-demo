<?php
session_start();

// Include file config để kết nối cơ sở dữ liệu
require 'config.php';

// Lấy ID của quản trị viên từ session
$admin_id = $_SESSION['admin_id'];

// Truy vấn thông tin quản trị viên từ bảng admin
$sql = "SELECT avatar, first_name, last_name FROM admin WHERE admin_id = '$admin_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Lấy thông tin quản trị viên
    $admin = $result->fetch_assoc();
    $avatar = htmlspecialchars($admin['avatar']);
    $first_name = htmlspecialchars($admin['first_name']);
    $last_name = htmlspecialchars($admin['last_name']);
} else {
    $avatar = 'default.png';
    $first_name = 'Guest';
    $last_name = '';
}

// Truy vấn menu cấp 1 (menu chính)
$sql_c1 = "SELECT * FROM menus_admin WHERE parent_id IS NULL ORDER BY position";
$result_c1 = $conn->query($sql_c1);

// Lấy dữ liệu menu con (cấp 2 và cấp 3)
$sql_c2 = "SELECT * FROM menus_admin WHERE parent_id IS NOT NULL ORDER BY parent_id, position";
$result_c2 = $conn->query($sql_c2);

// Tổ chức lại dữ liệu menu con theo parent_id
$submenus = [];
while ($row_c2 = $result_c2->fetch_assoc()) {
    $submenus[$row_c2['parent_id']][] = $row_c2;
}

// Truy vấn dữ liệu hình trên "table images"
$sql = "SELECT image_path FROM images";
$result = $conn->query($sql);

// Truy vấn dữ liệu sách trên table "books"
$book_query = "SELECT * FROM books ORDER BY release_date DESC"; // Sắp xếp theo ngày phát hành mới nhất
$book_result = $conn->query($book_query);


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
    <link rel="stylesheet" href="css/footer.css"> 

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
        
        <h2>Trang Chủ</h2>    <!-- phần nội dung trang chủ -->

        <!-- Thanh tìm kiếm (search) -->
    <div class="search-container">
      <form action="search.php" method="get">
        <input
          type="text"
          placeholder="Tìm kiếm sách..."
          name="query"
          required
        />
        <button type="submit">Search</button>
      </form>
      <!-- Danh sách lựa chọn cho lọc -->
  

      <div class="avatar" style="display: flex;">
            <!-- Biểu tượng chuông -->
            
            <a href="<?php echo isset($_SESSION['admin_id']) ? 'profile.php' : 'admin_login.php'; ?>" style="display: flex; align-items: center;">
                <img src="<?php echo $avatar; ?>" alt="Avatar" />
                <h4><?php echo $first_name . ' ' . $last_name; ?></h4>
            </a>
        </div>  
    </div>

    
 
  
<!--Ảnh ở đầu trang -->
    <div class="slider">
        <div class="list">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="item_anh">';
                    echo '<img src="images/anhtrang_chu/' . htmlspecialchars($row["image_path"]) . '" alt="">';
                    echo '</div>';
                }
            } else {
                echo "Không có hình ảnh.";
            }
            ?>
        </div>
    </div>
    <?php
    ?>
    <script src=""></script> <!-- Link tới file JavaScript của bạn -->

 <!--Hiển thị sách vừa ra mắt  -->

 <div id="Sách vừa ra mắt" class="Show_eye">
    <h3>Sách vừa ra mắt.</h3>
    <div class="book-grid">
        <?php while ($book = $book_result->fetch_assoc()): ?>
            <div class="book-item">
                <a href="book-detail.php?id=<?php echo htmlspecialchars($book['id']); ?>" class="book-link">
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


<!--Hiển thị sách phổ biến  -->

<div id="Sách phổ biến" class="Show_eye">
    <h3>Sách phổ biến.</h3>
    <div class="book-grid">
        <?php
        $conn = new mysqli('localhost', 'root', '', 'library');

        // Kiểm tra kết nối
        if ($conn->connect_error) {
            die("Kết nối không thành công: " . $conn->connect_error);
        }
        // Truy vấn dữ liệu từ bảng 'popular_books'
        $sql = "SELECT id, title, author, image_path, release_date FROM popular_books";
        $book_result = $conn->query($sql);

        // Hiển thị kết quả
        if ($book_result->num_rows > 0): 
            while ($book = $book_result->fetch_assoc()): ?>
                <div class="book-item">
                    <a href="book-phobien-detail.php?id=<?php echo htmlspecialchars($book['id']); ?>" class="book-link">
                    <img src="images/<?php echo htmlspecialchars($book['image_path']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>" class="book-image">

                        <div class="book-info">
                            <h4><?php echo htmlspecialchars($book['title']); ?></h4>
                            <p><?php echo htmlspecialchars($book['author']); ?></p>
                            <p><?php echo htmlspecialchars($book['release_date']); ?></p>
                            <button>Đặt mượn</button>
                        </div>
                    </a>
                </div>
            <?php endwhile; 
        else: ?>
            <p>Không có sách phổ biến nào.</p>
        <?php endif; 
        
        // Đóng kết nối
        $conn->close();
        ?>
    </div>
</div>
<!-- phân chân trang-->
<footer class="footer">
        <div class="frame-footer" style="padding-left: 30px;">
            <div class="frame-footer-item">
                 <h4 class="footer-title">TRỤ SỞ CHÍNH</h4>
                <li class="footer-item">Tầng 6, Tòa nhà Flemington, 182 Lê Đại Hành, P.15, Q.11, Hồ Chí Minh.</li>
                <li class="footer-item">Tổng đài hỗ trợ: 1900.636.099 ( Thứ 2 đến Thứ 6 từ 8h đến 18h; Thứ 7 và Chủ nhật từ 8h00 đến 17h00 )
                </li>
                <li>Số hỗ trợ ngoài giờ: 0901.866.099</li>
            </div>
            <div class="frame-footer-item">
                <h4 class="footer-title">VĂN PHÒNG HÀ NỘI</h4>
                <li class="footer-item">Tầng 3, tòa nhà The Artemis, số 03 Lê Trọng Tấn, Khương Mai, Thanh Xuân, Hà Nội.</li>
                <li class="footer-item">Tổng đài hỗ trợ: 1900.636.099 ( Thứ 2 đến Thứ 6 từ 8h đến 18h; Thứ 7 và Chủ nhật từ 8h00 đến 17h00 )
                </li>
                <li>Hotline tư vấn dịch vụ: 0708.789.886</li>
            </div>
            <div class="frame-footer-item">
                <h4 class="footer-title">VĂN PHÒNG BÌNH DƯƠNG</h4>
                <li class="footer-item">Số 38 đường D4, KDC Chánh Nghĩa, P. Chánh Nghĩa, Tp. Thủ Dầu Một, Tỉnh Bình Dương.</li>
                <li class="footer-item">Tổng đài hỗ trợ: 1900.636.099 ( Thứ 2 đến Thứ 6 từ 8h đến 18h; Thứ 7 và Chủ nhật từ 8h00 đến 17h00 )
                </li>
                <li>Hotline tư vấn dịch vụ: 0976.82.81.81</li>
            </div>
        </div>
    </footer>
    <footer class="footer-below">
        
        <div class="footer-center">
            <h4>Liên hệ hỗ trợ | </h4>
            <p style="padding-left: 20px;">Số điện thoại tiếp nhận khiếu nại: 0903.119.101</p>
        </div>
        <img class="footer-img" src="images/img/logo-da-thong-bao.webp" alt="">
    </footer>
   

   </div>
     <!-- kết thúc phần nội dung trang chủ -->


</body>
</html>