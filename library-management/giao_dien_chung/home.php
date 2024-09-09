<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'library');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

// Lấy dữ liệu menu chính
$menu_query = "SELECT * FROM menus WHERE parent_id IS NULL ORDER BY position";
$menu_result = $conn->query($menu_query);

// Lấy dữ liệu menu con
$submenu_query = "SELECT * FROM menus WHERE parent_id IS NOT NULL ORDER BY parent_id, position";
$submenu_result = $conn->query($submenu_query);

$submenus = [];
while ($submenu = $submenu_result->fetch_assoc()) {
    $submenus[$submenu['parent_id']][] = $submenu;
}

// Truy vấn dữ liệu hinh
$sql = "SELECT image_path FROM images";
$result = $conn->query($sql);


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/slider.css">
    
</head>
<body>

    <div class="sidebar">  <!-- Thanh menu nằm dọc -->
        <h2>Library</h2>
        <?php while ($menu = $menu_result->fetch_assoc()): ?>
            <div class="menu-item" onclick="toggleSubmenu('submenu-<?php echo $menu['id']; ?>')">
                <?php echo htmlspecialchars($menu['title']); ?>
                <?php if (isset($submenus[$menu['id']])): ?>
                    <div id="submenu-<?php echo $menu['id']; ?>" class="submenu">
                        <?php foreach ($submenus[$menu['id']] as $submenu): ?>
                            <div class="submenu-item">
                                <!-- thêm thẻ "a" để chuyển -->
                            <a href="<?php echo getSubmenuLink($submenu['title']); ?>">

                            <?php echo htmlspecialchars($submenu['title']); ?>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- phần nội dung trang chủ -->
    <div class="content">
        <h1>Trang chủ</h1>

        <!-- Thanh tìm kiếm (search) -->
        <div class="search-container">
    <form action="search.php" method="get">
        <input type="text" placeholder="Tìm kiếm sách..." name="query" required>
        <button type="submit">Tìm kiếm</button>
    </form>
</div>
 
  
<!--Ảnh ở đầu trang -->
<div class="slider">
        <div class="list">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="item_anh">';
                    echo '<img src="' . htmlspecialchars($row["image_path"]) . '" alt="">';
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
    <p>Sách vừa ra mắt.</p>

<!--Hiển thị sách vừa ra mắt  -->
   <p>Sách Phổ biến.</p>


   </div>
     <!-- kết thúc phần nội dung trang chủ -->

<!-- php -->

 <!-- Hàm để chuyển trang  liên kết của menu con -->
<?php
function getSubmenuLink($submenuTitle) {
    switch ($submenuTitle) {
        case 'Sách vừa ra mắt':
            return 'ramat.php';
        case 'Sách mượn nhiều':
            return 'sach-muon-nhieu.php';
        case 'Sách phổ biến hiện nay':
            return 'sach-pho-bien.php';
        case 'Truyện tranh':
            return 'truyen-tranh.php';
        case 'Sách giáo khoa':
            return 'sach-giao-khoa.php';
        case 'Tạp chí':
            return 'tap-chi.php';
        case 'Khoa học':
                return 'khoahoc.php';    
        default:
        
            return '#'; // Liên kết mặc định nếu không khớp
    }
}
?>
<!-- Kết thúc Hàm để chuyển trang  liên kết của menu con -->

<!-- Hàm để hiển thị các thành phần của menu -->
    <script>
    // Sử dụng IIFE để tránh xung đột biến toàn cục
    (function () {
        function toggleSubmenu(id) {
            var submenu = document.getElementById(id);
            if (submenu.style.display === 'block') {
                submenu.style.display = 'none';
            } else {
                submenu.style.display = 'block';
            }
        }

        // Gắn hàm toggleSubmenu vào window để có thể truy cập từ HTML
        window.toggleSubmenu = toggleSubmenu;
    })();
</script>
<!-- kết thúc Hàm để hiển thị các thành phần của menu -->


</body>
</html>
