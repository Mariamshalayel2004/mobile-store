<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../login.php"); 
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

$res_customers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 0");
$count_customers = $res_customers->fetch_assoc()['total'];

$res_phones = $conn->query("SELECT COUNT(*) AS total FROM phones");
$count_phones = $res_phones->fetch_assoc()['total'];

$res_orders = $conn->query("SELECT COUNT(*) AS total FROM orders");
$count_orders = $res_orders->fetch_assoc()['total'];


?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | الرئيسية</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; display: flex; }
        
        .sidebar { width: 250px; background-color: #343a40; color: white; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .sidebar h3 { text-align: center; color: #ffc107; margin-bottom: 30px; border-bottom: 1px solid #4b545c; padding-bottom: 15px; }
        .sidebar a { display: block; color: #c2c7d0; text-decoration: none; padding: 12px 10px; border-radius: 4px; margin-bottom: 5px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; color: #fff; }
        
        .main-content { flex: 1; padding: 30px; box-sizing: border-box; }
        .header-dash { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: #fff; padding: 15px 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .header-dash h2 { margin: 0; color: #333; }
        .btn-logout { background-color: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }
        .stat-card { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); text-align: center; border-right: 5px solid #007bff; }
        .stat-card.blue { border-right-color: #007bff; }
        .stat-card.green { border-right-color: #28a745; }
        .stat-card.orange { border-right-color: #fd7e14; }
        .stat-card.purple { border-right-color: #6f42c1; }
        
        .stat-card h4 { margin: 0; color: #777; font-size: 16px; margin-bottom: 10px; }
        .stat-card .number { font-size: 28px; font-weight: bold; color: #333; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>لوحة الإدارة</h3>
    <a href="index.php" class="active">🏠 الرئيسية (الاحصائيات)</a>
    <a href="manage_customer.php">👥 إدارة العملاء</a>
    <a href="manage_admin.php">🔑 إدارة المدراء</a>
    <a href="manage_brand.php">🏷️ إدارة الماركات</a>
    <a href="manage_mobile.php">📱 إدارة الهواتف</a>
    <a href="manage_order.php">📦 إدارة الطلبات</a>
    <a href="../index.php" target="_blank">🌐 عرض المتجر للزبائن</a>
</div>

<div class="main-content">
    <div class="header-dash">
        <h2>أهلاً بك يا مدير النظام: <?php echo htmlspecialchars($_SESSION['user_name']); ?> 👋</h2>
        <a href="../logout.php" class="btn-logout">تسجيل الخروج</a>
    </div>

    <h3 style="color:#555; margin-bottom:20px;">نظرة عامة على أداء المتجر:</h3>
    
    <div class="stats-grid">
        <div class="stat-card blue">
            <h4>إجمالي العملاء</h4>
            <div class="number"><?php echo $count_customers; ?></div>
        </div>

        <div class="stat-card green">
            <h4>الأجهزة بالمتجر</h4>
            <div class="number"><?php echo $count_phones; ?></div>
        </div>

        <div class="stat-card orange">
            <h4>طلبات الشراء</h4>
            <div class="number"><?php echo $count_orders; ?></div>
        </div>

        
    </div>
</div>

</body>
</html>
