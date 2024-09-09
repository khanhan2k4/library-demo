<?php
session_start();
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
    <script src="js/roll.js"></script>


    
</head>
<body>

<!-- Thanh menu nằm dọc -->

<div class="sidebar">
    <h2>Library</h2>
    
    <!-- Menu cấp 1 -->
    <?php while ($menu = $menu_result->fetch_assoc()): ?>
        <div class="menu-item">
            <!-- Liên kết cho menu cấp 1 -->
            <a href="<?php echo getMenuLink($menu['title']); ?>">
                <?php echo htmlspecialchars($menu['title']); ?>
            </a>
            
            <!-- Kiểm tra xem có menu cấp 2 không -->
            <?php if (isset($submenus[$menu['id']])): ?>
                <div id="submenu-<?php echo $menu['id']; ?>" class="submenu">
                    
                    <!-- Menu cấp 2 -->
                    <?php foreach ($submenus[$menu['id']] as $submenu): ?>
                        <div class="submenu-item">
                            <!-- Liên kết cho menu cấp 2 -->
                            <a href="<?php echo getSubmenuLink($submenu['title']); ?>">
                                <?php echo htmlspecialchars($submenu['title']); ?>
                            </a>

                            <!-- Kiểm tra xem có menu cấp 3 không -->
                            <?php if (isset($submenus[$submenu['id']])): ?>
                                <div id="submenu-<?php echo $submenu['id']; ?>" class="submenu">
                                    
                                    <!-- Menu cấp 3 -->
                                    <?php foreach ($submenus[$submenu['id']] as $subsubmenu): ?>
                                        <div class="subsubmenu-item">
                                            <!-- Liên kết cho menu cấp 3 -->
                                            <a href="<?php echo getSubmenuLink($subsubmenu['title']); ?>">
                                                <?php echo htmlspecialchars($subsubmenu['title']); ?>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>


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
        <button type="submit">Tìm kiếm</button>
      </form>

      <!-- Thêm avatar kế bên thanh tìm kiếm -->
      <div class="avatar">
        <a href="" style="display: flex; align-items: center;">
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



<!--Hiển thị sách phổ biến  -->


   </div>
     <!-- kết thúc phần nội dung trang chủ -->

<!-- hàm chuyển trang liên kết của menu cấp 1 -->

<?php
function getMenuLink($title) {
    // Mảng ánh xạ tiêu đề menu với các trang đích
    $menuLinks = [
        'Trang Chủ' => 'index.php',
        'Sách phổ biến' => 'sach-pho-bien.php',
        'Thể loại' => 'the-loai.php',
        'Tác giả' => 'tac-gia.php',
        // Thêm các menu khác ở đây
    ];

    // Kiểm tra xem tiêu đề có tồn tại trong mảng hay không
    if (isset($menuLinks[$title])) {
        return $menuLinks[$title]; // Trả về trang tương ứng
    } else {
        return 'default-page.php'; // Trang mặc định nếu tiêu đề không khớp
    }
}
?> 

<!-- Kết thúc hàm chuyển trang liên kết của menu cấp 1 -->
 <!-- Hàm để chuyển trang  liên kết của menu cấp 2 và cấp 3-->
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
        case 'Xem thông tin':
                return 'profile.php'; 
        case 'Đăng xuất':
                return 'logout.php';   
        default:
        
            return '#'; // Liên kết mặc định nếu không khớp
    }
}
?>

<!-- Kết thúc Hàm để chuyển trang  liên kết của menu con -->

<!-- Hàm để hiển thị các thành phần của menu -->
    <script>
    // Sử dụng IIFE để tránh xung đột biến toàn cục
    document.querySelectorAll('.menu-item').forEach(item => {
  item.addEventListener('click', () => {
    item.classList.toggle('active');
  });
});

</script>
<!-- kết thúc Hàm để hiển thị các thành phần của menu -->

</body>
</html>
