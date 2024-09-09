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
            margin-top:-3%;
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
            margin-top:-5%;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;"> 
        <a href="javascript:void(0);" class="link" onclick="redirectToPreviousPage()"><<<</a>  
        Biểu đồ tròn hiển thị số lượng sách theo từng thể loại
        <a href="javascript:void(0);" class="link" onclick="redirectToPage()">>>></a>
    </h1>
    <div class="chart-container">
        <canvas id="bookChart" width="1200" height="600"></canvas> <!-- Tăng kích thước lên gấp 3 lần -->
    </div>
    <script>
        function redirectToPreviousPage() {
            window.location.href = 'thongkeslsach_cot.php'; // <<< chuyển lại
        }

        function redirectToPage() {
            window.location.href = 'thongkeslsach_mien.php'; // >>> chuyển đi
        }

        const labels = <?php echo $labels_json; ?>;
        const data = <?php echo $data_json; ?>;
        
        const ctx = document.getElementById('bookChart').getContext('2d');
        const bookChart = new Chart(ctx, {
            type: 'pie', // Thay đổi loại biểu đồ từ 'bar' sang 'pie'
            data: {
                labels: labels,
                datasets: [{
                    label: 'Số lượng sách',
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
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