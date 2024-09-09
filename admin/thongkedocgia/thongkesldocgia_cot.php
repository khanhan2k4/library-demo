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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Biểu đồ cột</title>
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
    <h1 style="text-align:center; margin-top: 6%;"> <a href="javascript:void(0);" class="link" onclick="redirectToPreviousPage()"><<<</a>  Biểu đồ cột hiển thị số lượng độc giả  <a href="javascript:void(0);" class="link" onclick="redirectToPage()">>>></a></h1>
    <canvas id="userChart" width="400" height="200"></canvas>
    <script>

function redirectToPreviousPage() {
            window.location.href = '../home_admin.php'; // <<< chuyển lai
        }


function redirectToPage() {
            window.location.href = 'thongkesldocgia_tron.php'; // >>> chuyển đi
        }
        
        var ctx = document.getElementById('userChart').getContext('2d');
        var userChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Tổng độc giả'],
                datasets: [{
                    label: 'Số lượng độc giả',
                    data: [<?php echo $total_users; ?>],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 1000 // Đặt giá trị tối đa của trục y là 4 (Đặt chỉ tiêu là 1000 độc giả)
                    }
                }
            }
        });
    </script>
</body>
</html>