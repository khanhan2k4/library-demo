<?php
session_start();
require_once 'config.php'; // Kết nối với cơ sở dữ liệu

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    die("Bạn phải đăng nhập để xem sách đã mượn.");
}

$user_id = $_SESSION['user_id'];

// Lấy ngày hiện tại
$current_date = new DateTime();

// Truy vấn danh sách sách đã mượn của người dùng từ bảng books và popular_books
$sql = "
    SELECT sdm.book_id, b.title, b.image_path, sdm.ngay_muon, sdm.ngay_tra
    FROM sach_da_muon sdm
    JOIN books b ON sdm.book_id = b.id
    WHERE sdm.user_id = ?

    UNION
    SELECT sdm.book_id, pb.title, pb.image_path, sdm.ngay_muon, sdm.ngay_tra
    FROM sach_da_muon sdm
    JOIN popular_books pb ON sdm.book_id = pb.id
    WHERE sdm.user_id = ?

    UNION
    SELECT sdm.book_id, pb.title, pb.image_path, sdm.ngay_muon, sdm.ngay_tra
    FROM sach_da_muon sdm
    JOIN truyentranh pb ON sdm.book_id = pb.id
    WHERE sdm.user_id = ?

    UNION
    SELECT sdm.book_id, pb.title, pb.image_path, sdm.ngay_muon, sdm.ngay_tra
    FROM sach_da_muon sdm
    JOIN truyenkinhdi pb ON sdm.book_id = pb.id
    WHERE sdm.user_id = ?

    UNION
    SELECT sdm.book_id, pb.title, pb.image_path, sdm.ngay_muon, sdm.ngay_tra
    FROM sach_da_muon sdm
    JOIN tieuthuyet pb ON sdm.book_id = pb.id
    WHERE sdm.user_id = ?

    UNION
    SELECT sdm.book_id, pb.title, pb.image_path, sdm.ngay_muon, sdm.ngay_tra
    FROM sach_da_muon sdm
    JOIN truyencotich pb ON sdm.book_id = pb.id
    WHERE sdm.user_id = ?

    UNION
    SELECT sdm.book_id, pb.title, pb.image_path, sdm.ngay_muon, sdm.ngay_tra
    FROM sach_da_muon sdm
    JOIN sachchuyennganh pb ON sdm.book_id = pb.id
    WHERE sdm.user_id = ?

    UNION
    SELECT sdm.book_id, pb.title, pb.image_path, sdm.ngay_muon, sdm.ngay_tra
    FROM sach_da_muon sdm
    JOIN sachtruyencamhung pb ON sdm.book_id = pb.id
    WHERE sdm.user_id = ?

    UNION
    SELECT sdm.book_id, pb.title, pb.image_path, sdm.ngay_muon, sdm.ngay_tra
    FROM sach_da_muon sdm
    JOIN sachvanhoaxahoi pb ON sdm.book_id = pb.id
    WHERE sdm.user_id = ?

    UNION
    SELECT sdm.book_id, pb.title, pb.image_path, sdm.ngay_muon, sdm.ngay_tra
    FROM sach_da_muon sdm
    JOIN sachkhamphabian pb ON sdm.book_id = pb.id
    WHERE sdm.user_id = ?

    ORDER BY ngay_muon DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiiiiiiii", $user_id, $user_id, $user_id,$user_id,$user_id,$user_id,$user_id,$user_id,$user_id,$user_id);
$stmt->execute();
$result = $stmt->get_result();

// Tính tổng số sách đã mượn
$total_books_borrowed = $result->num_rows;
$stt = 1; // Khởi tạo biến đếm cho số thứ tự
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sách đã mượn</title>
</head>
<body>
    <h1>Danh sách sách đã mượn</h1>

    <table border="1">
        <thead>
            <tr>
                <th>Số thứ tự</th>
                <th>ID Sách</th>
                <th>Tiêu đề sách</th>
                <th>Hình ảnh</th>
                <th>Ngày mượn</th>
                <th>Ngày trả</th>
                <th>Thông báo</th> <!-- Thêm cột thông báo -->
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    // Chuyển đổi ngay_muon và ngay_tra thành đối tượng DateTime
                    $ngay_muon = new DateTime($row['ngay_muon']);
                    $ngay_tra = new DateTime($row['ngay_tra']);
                    
                    // Kiểm tra nếu ngày hiện tại nằm trong khoảng giữa ngay_muon và ngay_tra
                    if ($current_date >= $ngay_muon && $current_date <= $ngay_tra) {
                        $thong_bao = "<span style='color: green;'>Còn hạn</span>";
                    } else {
                        $thong_bao = "<span style='color: red;'>Hết hạn</span>";
                    }
                    ?>
                    <tr>
                        <td><?php echo $stt++; ?></td> <!-- Hiển thị số thứ tự -->
                        <td><?php echo htmlspecialchars($row['book_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><img src="images/<?php echo htmlspecialchars($row['image_path']); ?>" alt="Hình ảnh sách" width="100"></td>
                        <td><?php echo htmlspecialchars($row['ngay_muon']); ?></td>
                        <td><?php echo htmlspecialchars($row['ngay_tra']) ?: 'Chưa trả'; ?></td> <!-- Hiển thị ngày trả, nếu có -->
                        <td><?php echo $thong_bao; ?></td> <!-- Hiển thị thông báo -->
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">Bạn chưa mượn sách nào.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p>Tổng số sách đã mượn: <?php echo $total_books_borrowed; ?></p>

    <a href="index.php">Trang chủ</a>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
