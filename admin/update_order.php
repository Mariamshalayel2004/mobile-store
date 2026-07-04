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

$success_msg = "";
$error_msg   = "";

if (isset($_GET['id'])) {
    $order_id = intval($_GET['id']);
} else {
    header("Location: manage_order.php");
    exit();
}

$sql_fetch = "SELECT orders.id, users.name AS customer_name 
              FROM orders 
              LEFT JOIN users ON orders.user_id = users.id 
              WHERE orders.id = ?";
$stmt_fetch = $conn->prepare($sql_fetch);
$stmt_fetch->bind_param("i", $order_id);
$stmt_fetch->execute();
$result = $stmt_fetch->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
} else {
    die("الفاتورة المطلوبة غير موجودة في النظام!");
}
$stmt_fetch->close();

if (isset($_POST['submit_update_order'])) {
    $success_msg = "تم تحديث وحفظ تفاصيل الفاتورة بنجاح.";
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل وتعديل الطلب</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; display: flex; }
        
        .sidebar { width: 250px; background-color: #343a40; color: white; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .sidebar h3 { text-align: center; color: #ffc107; margin-bottom: 30px; border-bottom: 1px solid #4b545c; padding-bottom: 15px; }
        .sidebar a { display: block; color: #c2c7d0; text-decoration: none; padding: 12px 10px; border-radius: 4px; margin-bottom: 5px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; color: #fff; }
        
        .main-content { flex: 1; padding: 30px; box-sizing: border-box; }
        .form-container { max-width: 650px; background: #ffffff; padding: 35px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-top: 4px solid #ffc107; margin: 40px auto; }
        
        h2 { color: #333; margin-bottom: 25px; font-family: sans-serif; text-align: center; font-size: 24px; font-weight: bold; }
        
        .order-info { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 25px; border: 1px solid #eee; text-align: right; }
        .order-info p { margin: 10px 0; font-size: 15px; color: #333; }
        .order-info strong { color: #555; }
        
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; }
        
        .form-control { margin-bottom: 20px; text-align: right; }
        .form-control label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 15px; }
        .form-control input { width: 100%; padding: 12px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; background-color: #e8f0fe; font-size: 15px; }
        
        .btn-submit { background-color: #ffc107; color: #fff; padding: 12px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; }
        .btn-submit:hover { background-color: #e0a800; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #007bff; text-decoration: none; font-weight: bold; }
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
    <div class="form-container">
        <h2>تفاصيل وتعديل الطلب رقم #<?php echo $order['id']; ?></h2>

        <?php if(!empty($success_msg)): ?>
            <div class="alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <div class="order-info">
            <p><strong>اسم العميل صاحب الطلب:</strong> <?php echo htmlspecialchars($order['customer_name'] ? $order['customer_name'] : 'user'); ?></p>
            <p><strong>تاريخ تسجيل الطلب:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>الإجمالي الحالي المعتمد:</strong> $350.00</p>
        </div>

        <form action="" method="POST">
            <div class="form-control">
                <label for="manual_price">تعديل قيمة الفاتورة الإجمالية يدوياً (اختياري):</label>
                <input type="number" step="0.01" id="manual_price" name="manual_price" value="350.00">
            </div>

            <button type="submit" name="submit_update_order" class="btn-submit">تحديث بيانات الفاتورة</button>
            <a href="manage_order.php" class="back-link">← العودة لقائمة الطلبات</a>
        </form>
    </div>
</div>

</body>
</html>
