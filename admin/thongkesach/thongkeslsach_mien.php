<?php

$conn = new mysqli('localhost', 'root', '', 'library');

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Truy vấn dữ liệu từ bảng books và popular_books dựa trên tên thể loại
$sql = "SELECT the_loai.ten_the_loai, SUM(so_luong) AS so_luong
        FROM (
            SELECT theloai AS ten_the_loai, so_luong FROM books
            UNION ALL
            SELECT theloai AS ten_the_loai, so_luong FROM popular_books
            UNION ALL
            SELECT theloai AS ten_the_loai, so_luong FROM truyentranh
            UNION ALL
            SELECT theloai AS ten_the_loai, so_luong FROM truyencotich
            UNION ALL
            SELECT theloai AS ten_the_loai, so_luong FROM truyenkinhdi
            UNION ALL
            SELECT theloai AS ten_the_loai, so_luong FROM tieuthuyet
            UNION ALL
            SELECT theloai AS ten_the_loai, so_luong FROM sachchuyennganh
            UNION ALL
            SELECT theloai AS ten_the_loai, so_luong FROM sachtruyencamhung
            UNION ALL
            SELECT theloai AS ten_the_loai, so_luong FROM sachvanhoaxahoi
            UNION ALL
            SELECT theloai AS ten_the_loai, so_luong FROM sachkhamphabian
        ) AS combined
        JOIN the_loai ON combined.ten_the_loai = the_loai.ten_the_loai
        GROUP BY the_loai.ten_the_loai";
$result = $conn->query($sql);

if (!$result) {
    die("Lỗi truy vấn: " . $conn->error);
}

$labels = [];
$data = [];
$total_books = 0;

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $labels[] = $row['ten_the_loai'];
        $data[] = $row['so_luong'];
        $total_books += $row['so_luong'];
    }
} else {
    echo "0 kết quả";
}
$conn->close();

// Chuyển đổi dữ liệu thành JSON
$labels_json = json_encode($labels);
$data_json = json_encode($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê số lượng sách</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        table {
            width: 50%;
            margin: 20px auto;
            border-collapse: collapse;
            margin-top:-10%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        /* thẻ a>>>và<<< */
        .link {
            color: black;
            text-decoration: none;
        }
        .link:hover {
            color: blue;
        }
        /* thẻ a */
        .chart-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin-top:-10%;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;"> 
        <a href="javascript:void(0);" class="link" onclick="redirectToPreviousPage()"><<<</a>  
        Biểu đồ miền hiển thị số lượng sách theo từng thể loại
        <a href="javascript:void(0);" class="link" onclick="redirectToPage()">>>></a>
    </h1>
    <div class="chart-container">
        <canvas id="bookChart" width="800" height="400"></canvas> <!-- Tăng kích thước lên gấp 3 lần -->
    </div>
    <script>
        function redirectToPreviousPage() {
            window.location.href = 'thongkeslsach_tron.php'; // <<< chuyển lại
        }

        function redirectToPage() {
            window.location.href = '../home_admin.php'; // >>> chuyển đi
        }

        const labels = <?php echo $labels_json; ?>;
        const data = <?php echo $data_json; ?>;
        
        const ctx = document.getElementById('bookChart').getContext('2d');
        const bookChart = new Chart(ctx, {
            type: 'line', // Thay đổi loại biểu đồ từ 'pie' sang 'line'
            data: {
                labels: labels,
                datasets: [{
                    label: 'Số lượng sách',
                    data: data,
                    fill: true, // Để hiển thị biểu đồ miền
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return labels[tooltipItem.dataIndex] + ': ' + data[tooltipItem.dataIndex];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <table>
        <tr>
            <th>Thể loại</th>
            <th>Số lượng sách</th>
            <th>Phần trăm (%)</th>
        </tr>
        <?php 
        foreach ($labels as $index => $label): ?>
        <tr>
            <td><?php echo $label; ?></td>
            <td><?php echo $data[$index]; ?></td>
            <td><?php echo round(($data[$index] / $total_books) * 100, 2); ?>%</td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>