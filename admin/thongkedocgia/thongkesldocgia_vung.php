<?php
$conn = new mysqli('localhost', 'root', '', 'library');

if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

// Truy vấn dữ liệu
$sql = "SELECT COUNT(*) AS total_users FROM users";
$result = $conn->query($sql);

$total_users = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_users = $row['total_users'];
}

$conn->close();
// Tính toán phần đã đạt và phần chưa đạt
$target = 1000; // Mục tiêu là 100%
$achieved = $total_users;
$remaining = $target - $achieved;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Biểu đồ vùng</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        canvas {
            display: block;
        }
        .link {
            color: black;
            text-decoration: none;
        }
        .link:hover {
            color: blue;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;">
        <a href="javascript:void(0);" class="link" onclick="redirectToPreviousPage()"><<<</a>
        Biểu đồ vùng hiển thị số lượng độc giả
        <a href="javascript:void(0);" class="link" onclick="redirectToPage()">>>></a>
    </h1>
    <canvas id="userAreaChart" width="1200" height="600"></canvas>
    <script>
        function redirectToPreviousPage() {
            window.location.href = 'thongkesldocgia_tron.php'; // <<< chuyển lai
        }

        function redirectToPage() {
            window.location.href = '../home_admin.php'; // >>> chuyển đi
        }

        var ctx = document.getElementById('userAreaChart').getContext('2d');
        var userAreaChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Đã đạt', 'Chưa đạt'],
                datasets: [{
                    label: 'Số lượng độc giả',
                    data: [<?php echo $achieved; ?>, <?php echo $remaining; ?>],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)', // Màu cho phần đã đạt
                        'rgba(255, 99, 132, 0.2)'  // Màu cho phần chưa đạt
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1,
                    fill: true // Tạo vùng
                }]
            },
            options: {
                responsive: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 1000 // Đặt giá trị tối đa của trục y là 1000
                    }
                }
            }
        });
    </script>
</body>
</html>