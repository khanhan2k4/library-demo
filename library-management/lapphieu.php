<?php
if (isset($_GET['book_id'])) {
    $book_id = htmlspecialchars($_GET['book_id']);
} else {
    die("Không xác định được ID sách.");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lập Phiếu Mượn</title>
    <link rel="stylesheet" href="css/lap_phieu.css">
</head>
<body>

<div class="container">
    <h2>Lập Phiếu Mượn</h2>
    <form action="process_lapphieu.php" method="POST">
        <div class="form-group">
            <!-- ID người dùng -->
            <label for="id">ID Người Dùng</label>
            <input type="text" id="id" name="user_id" required>
        </div>
        <div class="form-group">
            <label for="ho_va_ten">Họ và Tên</label>
            <input type="text" id="ho_va_ten" name="ho_va_ten" required>
        </div>
        <div class="form-group">
            <label for="so_dien_thoai">Số điện thoại</label>
            <input type="text" id="so_dien_thoai" name="so_dien_thoai" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
    <!-- ID sách đã chọn -->
    <?php if (isset($_GET['book_id'])): ?>
        <input type="text" name="book_id" value="<?php echo htmlspecialchars($_GET['book_id']); ?>">
    <?php else: ?>
        <p class="error">Không xác định được ID sách.</p>
    <?php endif; ?>
</div>

        <div class="form-group">
            <button type="submit">Lập Phiếu Mượn</button>
        </div>
    </form>
</div>

</body>
</html>
