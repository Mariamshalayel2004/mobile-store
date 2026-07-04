<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "mobile_store";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

$user_id = $_SESSION['user_id'];

$sql = "SELECT orders.order_date, phones.model, phones.price 
        FROM orders 
        JOIN order_items ON orders.id = order_items.order_id 
        JOIN phones ON order_items.phone_id = phones.id 
        WHERE orders.user_id = $user_id 
        ORDER BY orders.order_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلباتي | Mobile Store</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: right; border-bottom: 1px solid #ddd; }
        th { background-color: #343a40; color: white; }
        .back-link { display: inline-block; margin-bottom: 15px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="back-link">← العودة للرئيسية</a>
    <h2>طلباتي السابقة</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>السعر</th>
                    <th>تاريخ الطلب</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['model']); ?></td>
                        <td>$<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['order_date']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>لا توجد طلبات سابقة لديك.</p>
    <?php endif; ?>
</div>

</body>
</html>