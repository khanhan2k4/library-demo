<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hộp Thư Đến</title>
    <link rel="stylesheet" href="css/notification.css">
    <style>
        /* Thêm một số kiểu CSS cơ bản */
        .navbar {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: center;
        }
        .container {
            display: flex;
            margin: 20px;
        }
        .inbox, .message-content {
            padding: 10px;
            border: 1px solid #ddd;
            margin-right: 10px;
        }
        .message-item {
            cursor: pointer;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .message-item:hover {
            background-color: #f0f0f0;
        }
        .delete-button {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 12px;
        }

        /* CSS cho nút Trang chủ */
        .home-button {
            background-color: #007bff; /* Màu nền xanh dương */
            color: white; /* Màu chữ trắng */
            border: none;
            border-radius: 5px; /* Bo tròn các góc */
            padding: 10px 20px; /* Khoảng cách trong nút */
            cursor: pointer; /* Hiển thị con trỏ chuột khi di chuột qua nút */
            font-size: 16px; /* Kích thước chữ */
            text-decoration: none; /* Loại bỏ gạch chân */
            transition: background-color 0.3s ease; /* Hiệu ứng chuyển màu nền khi di chuột */
        }

        .home-button:hover {
            background-color: #0056b3; /* Màu nền khi di chuột qua */
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1 style="color: black;">Hộp Thư Đến</h1>
    </div>
    <a href="index.php"><button class="home-button">Trang chủ</button></a>
    <div class="container">
        <div class="inbox">
            <h2>Danh Sách Tin Nhắn</h2>
            <ul>
                <?php
                session_start(); // Bắt đầu session để lấy user_id

                // Kết nối cơ sở dữ liệu
                require_once 'config.php'; // Liên kết file cấu hình để kết nối với cơ sở dữ liệu

                // Kiểm tra nếu user_id có trong session
                if (!isset($_SESSION['user_id'])) {
                    echo "<li>Vui lòng đăng nhập để xem tin nhắn</li>";
                    exit();
                }

                $user_id = $_SESSION['user_id']; // Lấy user_id từ session

                // Truy vấn dữ liệu từ bảng sach_da_tra theo user_id
                $sql = "SELECT id, admin_name, ngay_tra FROM sach_da_tra WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id); // Gán giá trị user_id vào câu truy vấn
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<li class='message-item' data-id='" . $row["id"] . "'>";
                        echo "<span class='sender'>" . htmlspecialchars($row["admin_name"]) . "</span>";
                        echo "<span class='time'>" . htmlspecialchars($row["ngay_tra"]) . "</span>";
                        echo "<button class='delete-button' data-id='" . $row["id"] . "'>Xóa</button>";
                        echo "</li>";
                    }
                } else {
                    echo "<li>Không có tin nhắn nào</li>";
                }

                $stmt->close();
                $conn->close();
                ?>
            </ul>
        </div>
        
        <div class="message-content" id="message-content">
            <h2>Nội Dung Tin Nhắn</h2>
            <p>Chọn một tin nhắn để xem nội dung.</p>
        </div>
    </div>

    <script>
        document.querySelectorAll('.message-item').forEach(function(item) {
            item.addEventListener('click', function() {
                let messageId = this.getAttribute('data-id');

                fetch('message_content.php?id=' + messageId)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('message-content').innerHTML = `
                        <h2>Nội Dung Tin Nhắn</h2>
                        <p><strong>Book ID:</strong> ${data.book_id}</p>
                        <p><strong>Thông báo:</strong> ${data.thong_bao}</p>
                    `;
                });
            });
        });

        document.querySelectorAll('.delete-button').forEach(function(button) {
            button.addEventListener('click', function() {
                if (confirm('Bạn có chắc chắn muốn xóa tin nhắn này không?')) {
                    let messageId = this.getAttribute('data-id');

                    fetch('delete_message.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id=${messageId}`
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data === 'success') {
                            alert('Tin nhắn đã được xóa.');
                            location.reload(); // Reload lại trang để cập nhật danh sách tin nhắn
                        } else {
                            alert('Có lỗi xảy ra.');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
