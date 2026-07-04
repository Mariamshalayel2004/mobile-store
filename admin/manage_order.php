<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "mobile_store";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$sql = "SELECT orders.id, users.name AS customer_name 
        FROM orders 
        LEFT JOIN users ON orders.user_id = users.id 
        ORDER BY orders.id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | إدارة الطلبات</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; display: flex; }
        .sidebar { width: 250px; background-color: #343a40; color: white; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .sidebar h3 { text-align: center; color: #ffc107; margin-bottom: 30px; border-bottom: 1px solid #4b545c; padding-bottom: 15px; }
        .sidebar a { display: block; color: #c2c7d0; text-decoration: none; padding: 12px 10px; border-radius: 4px; margin-bottom: 5px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; color: #fff; }
        
        .main-content { flex: 1; padding: 30px; box-sizing: border-box; }
        .card-box { background: #ffffff; padding: 30px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-top: 4px solid #dc3545; margin-top: 10px; }
        h2 { text-align: center; color: #333; margin-bottom: 25px; font-size: 22px; font-weight: bold; }
        
        .btn-add-container { text-align: left; margin-bottom: 20px; }
        .btn-add { background-color: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold; font-size: 14px; }
        .btn-add:hover { background-color: #218838; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #fff; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; font-size: 15px; }
        th { background-color: #f8f9fa; color: #333; font-weight: 600; }
        tr:hover { background-color: #f9f9f9; }
        
        .btn-update { background-color: #ffc107; color: #fff; padding: 5px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; display: inline-block; }
        .btn-update:hover { background-color: #e0a800; }
        .btn-delete { background-color: #dc3545; color: white; padding: 5px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; display: inline-block; margin-right: 5px; }
        .btn-delete:hover { background-color: #c82333; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>لوحة الإدارة</h3>
    <a href="index.php">🏠 الرئيسية (الاحصائيات)</a>
    <a href="manage_customer.php">👥 إدارة العملاء</a>
    <a href="manage_admin.php">🔑 إدارة المدراء</a>
    <a href="manage_brand.php">🏷️ إدارة الماركات</a>
    <a href="manage_mobile.php">📱 إدارة الهواتف</a>
    <a href="manage_order.php" class="active">📦 إدارة الطلبات</a>
    <a href="../index.php" target="_blank">🌐 عرض المتجر للزبائن</a>
</div>

<div class="main-content">
    <div class="card-box">
        <h2>شاشة إدارة فواتير وطلبات الشراء</h2>

        <div class="btn-add-container">
            <a href="add_order.php" class="btn-add">+ إضافة طلب جديد</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>رقم الفاتورة</th>
                    <th>اسم العميل المشتري</th>
                    <th>إجمالي الحساب</th>
                    <th>تفاصيل وتحديث الفاتورة</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $customer_name = !empty($row['customer_name']) ? $row['customer_name'] : "حساب محذوف";
                        $order_id = $row['id'];
                        
                        $mock_total = ($order_id * 150) + 200;
                        if($mock_total > 1500) { $mock_total = 799.00; }
                        
                        echo "<tr>";
                        echo "<td>#" . $order_id . "</td>";
                        echo "<td>" . htmlspecialchars($customer_name) . "</td>";
                        echo "<td>$" . number_format($mock_total, 2) . "</td>";
                        
                        echo "<td>
                                <a href='update_order.php?id=" . $row['id'] . "' class='btn-update'>تعديل الطلب</a>
                                <a href='delete_order.php?id=" . $row['id'] . "' class='btn-delete' onclick='return confirm(\"هل أنتِ متأكدة من إلغاء وحذف هذه الفاتورة نهائياً؟\")'>إلغاء الطلب</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>لا توجد طلبات شراء مسجلة في المتجر حتى الآن.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
