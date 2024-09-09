<?php
session_start();

// Liên kết file config.php
require_once 'config.php'; // Liên kết file cấu hình để kết nối với cơ sở dữ liệu

// Kiểm tra nếu có ID trong URL
if (isset($_GET['id'])) {
    $book_id = $conn->real_escape_string($_GET['id']);
    
    // Truy vấn thông tin sách từ cơ sở dữ liệu
    // Chọn bảng sách mà bạn muốn (books hoặc popular_books)
    $table_name = 'truyentranh'; // Hoặc 'popular_books'
    $sql = "SELECT * FROM $table_name WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    } else {
        die("Sách không tồn tại.");
    }
} else {
    die("ID sách không được xác định.");
}

// Lấy ID của người dùng từ session
$user_id = $_SESSION['user_id'] ?? null;

// Truy vấn thông tin người dùng từ bảng users (nếu có người dùng đã đăng nhập)
if ($user_id) {
    $sql = "SELECT avatar, first_name, last_name FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $avatar = htmlspecialchars($user['avatar']);
        $first_name = htmlspecialchars($user['first_name']);
        $last_name = htmlspecialchars($user['last_name']);
    } else {
        $avatar = 'default.png';
        $first_name = 'Guest';
        $last_name = '';
    }
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

// phần code nhận xét, đánh giá
// Truy vấn dữ liệu từ bảng danhgia
$sql_get_ids = "SELECT id FROM danhgia WHERE book_id = ?";
$stmt = $conn->prepare($sql_get_ids);
$stmt->bind_param("s", $book_id); // Sử dụng "s" cho kiểu varchar
$stmt->execute();
$result_ids = $stmt->get_result();

$comments = []; // Khởi tạo mảng để lưu trữ tất cả các bình luận

if ($result_ids->num_rows > 0) {
    while ($row_id = $result_ids->fetch_assoc()) {
        $id = $row_id['id'];

        // Truy vấn dữ liệu từ bảng danhgia với id đã lấy được
        $sql = "SELECT d.id, d.rating, d.comment, d.created_at, d.user_id, d.book_id, u.first_name, u.last_name, b.title 
                FROM danhgia d 
                JOIN users u ON d.user_id = u.id 
                JOIN truyentranh b ON d.book_id = b.id 
                WHERE d.id = ? AND d.book_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $id, $book_id); // Sử dụng "ss" cho kiểu varchar
        $stmt->execute();
        $result_reviews = $stmt->get_result();

        // Kiểm tra kết quả truy vấn
        if ($result_reviews === false) {
            die("Lỗi truy vấn: " . $conn->error);
        }

        // Thêm các bình luận vào mảng $comments
        if ($result_reviews->num_rows > 0) {
            while($row = $result_reviews->fetch_assoc()) {
                $comments[] = $row;
            }
        }
    }
} else {
    echo "Không tìm thấy bình luận nào trong bảng danhgia.";
}
//phần code nhận xét, đánh giá

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin sách</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/slider.css">
    <link rel="stylesheet" href="css/ramat.css">
    <link rel="stylesheet" href="css/book-detail.css">
    <script type="text/javascript" src="../library-management/js/jquery.min.js"></script>
      <!-- css cho phần nhận xét đánh giá -->
      <style>
.star {
    font-size: 24px; /* Kích thước ngôi sao */
    cursor: pointer; /* Con trỏ chuột thay đổi khi di chuột qua ngôi sao */
    color: #ccc; /* Màu ngôi sao mặc định */
}

#rating .star:hover,
#rating .star.selected {
    color: #f39c12; /* Màu ngôi sao khi được chọn hoặc di chuột qua */
}
.comment {
    border: 1px solid #ddd; /* Đường viền nhạt */
    padding: 10px;
    margin-bottom: 10px; /* Khoảng cách giữa các bình luận */
    border-radius: 5px; /* Bo góc */
    background-color: #f9f9f9; /* Màu nền nhạt */
}

.no-comments {
    text-align: center; /* Căn giữa thông báo không có bình luận */
    color: #888; /* Màu chữ xám nhạt */
    font-style: italic; /* Chữ in nghiêng */
}
</style>
     <!-- css cho phần nhận xét đánh giá -->
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

<!-- Phần nội dung -->
<div class="content">
    <h2>Thông tin sách</h2>
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

            // Kiểm tra và ẩn số thông báo
            let notificationCountElement = document.getElementById('notification-count');
            if (notificationCountElement) {
                notificationCountElement.innerText = ''; // Xóa nội dung của phần tử
                notificationCountElement.style.display = 'none'; // Ẩn phần tử hiển thị số lượng thông báo
            }

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

    <!-- Thông tin sách -->
    <div class="detail">
        <div class="hinh">
            <img src="images/<?php echo htmlspecialchars($book['image_path']); ?>" alt="Hình bìa sách">
        </div>
        <div class="profile">
            <h2>Tên tác phẩm: <?php echo htmlspecialchars($book['title']); ?></h2>
            <p><strong>Tác giả:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
            <p><strong>Ngày phát hành:</strong> <?php echo htmlspecialchars($book['release_date']); ?></p>
            <p><strong>Thể loại:</strong> <?php echo htmlspecialchars($book['theloai']); ?></p>
            <p><strong>Mô tả:</strong> <?php echo htmlspecialchars($book['tomtat']); ?></p>
            <p><strong>Nhà xuất bản:</strong> <?php echo htmlspecialchars($book['publisher']); ?></p>
            <div style="margin-top: 20px;">
                <button onclick="datMuon()">Đặt mượn</button>
                <button onclick="hienThiNhanXet()">Nhận xét</button>
            </div>
        </div>
    </div>
     <!-- code liên quan phần đánh giá, nhận xét -->
     <div style="width:100%;" id="nhanxet" style="display:none; margin-top: 20px;">
        <textarea style="margin-left:25px; width:35%" id="binhluan" rows="4" cols="50" placeholder="Bình luận ..."></textarea>
        <br>
        <div style="margin-left:25px; width:35%" id="rating">
            <span class="star" data-value="1">&#9733;</span>
            <span class="star" data-value="2">&#9733;</span>
            <span class="star" data-value="3">&#9733;</span>
            <span class="star" data-value="4">&#9733;</span>
            <span class="star" data-value="5">&#9733;</span>
        </div>
        <br>
        <button style=" width: 15%;background-color: #007bff; color: #fff;border: none;border-radius: 4px;font-size: 16px;cursor: pointer; margin-left:25px; " onclick="guiNhanXet()">Gửi</button>
        <div id="danhSachBinhLuan" style="margin-top: 20px;">
    <h3 style="margin-left:25px;">Bình luận</h3>
    <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $comment): ?>
            <div class='comment'>
                <p><strong>ID độc giả:</strong> <?php echo htmlspecialchars($comment['user_id']); ?></p>
                <p><strong>Người dùng:</strong> <?php echo htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']); ?></p>
                <p><strong>Sách:</strong> <?php echo htmlspecialchars($comment['title']); ?></p>
                <p><strong>Ngày đánh giá:</strong> <?php echo htmlspecialchars($comment['created_at']); ?></p>
                <p><strong>Đánh giá:</strong> <?php echo htmlspecialchars($comment['rating']); ?> sao</p>
                <p><strong>Bình luận:</strong> <?php echo htmlspecialchars($comment['comment']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class='no-comments'>
            <p>Chưa có bình luận nào.</p>
        </div>
    <?php endif; ?>
</div>
     <!-- code liên quan phẩn đánh giá, nhận xét -->
</div>



<!-- script liên quan đến phần đánh giá, nhận xét -->
<script>
function hienThiNhanXet() {
    var nhanxet = document.getElementById("nhanxet");
    if (nhanxet.style.display === "none") {
        nhanxet.style.display = "block";
    } else {
        nhanxet.style.display = "none";
    }
}

function guiNhanXet() {
    var comment = document.getElementById('binhluan').value;
    var rating = document.querySelectorAll('#rating .star.selected').length; // Đếm số ngôi sao được chọn

    // Kiểm tra nếu người dùng chưa nhập bình luận hoặc chưa chọn đánh giá
    if (comment.trim() === '' || rating === 0) {
        alert('Vui lòng nhập bình luận và chọn đánh giá trước khi gửi.');
        return; // Ngăn việc gửi dữ liệu nếu không hợp lệ
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'submit_comment.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            location.reload(); // Tải lại trang để hiển thị bình luận mới
        }
    };
    xhr.send('comment=' + encodeURIComponent(comment) + '&rating=' + rating + '&book_id=<?php echo $book_id; ?>');
}

document.querySelectorAll('#rating .star').forEach(function(star, index, stars) {
    star.addEventListener('click', function() {
        // Loại bỏ lớp 'selected' khỏi tất cả các ngôi sao
        stars.forEach(function(s) {
            s.classList.remove('selected');
        });

        // Thêm lớp 'selected' cho tất cả các ngôi sao từ đầu đến ngôi sao được chọn
        for (let i = 0; i <= index; i++) {
            stars[i].classList.add('selected');
        }
    });
});

document.querySelectorAll('#rating .star').forEach(function(star, index, stars) {
    star.addEventListener('click', function() {
        // Loại bỏ lớp 'selected' khỏi tất cả các ngôi sao
        stars.forEach(function(s) {
            s.classList.remove('selected');
        });

        // Thêm lớp 'selected' cho tất cả các ngôi sao từ đầu đến ngôi sao được chọn
        for (let i = 0; i <= index; i++) {
            stars[i].classList.add('selected');
        }
    });
});

</script>
 <!-- script liên quan đến phần đánh giá, nhận xét -->



<script>
function getBookIdFromUrl() {
    var params = new URLSearchParams(window.location.search);
    return params.get("id");
}

function datMuon() {
    var book_id = getBookIdFromUrl();
    if (book_id) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "check_quantity.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = xhr.responseText;
                if (response == "available") {
                    window.location.href = "lapphieu.php?book_id=" + book_id;
                } else {
                    alert("Sách hiện không có sẵn.");
                }
            }
        };
        xhr.send("book_id=" + book_id);
    } else {
        alert("Không thể xác định ID sách.");
    }
}
</script>
</body>
</html>
