<?php
// admin/add_brand.php

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

if (isset($_POST['submit_brand'])) {
    $brand_name = trim($_POST['brand_name']);

    if (!empty($brand_name)) {
        // استخدام استعلام آمن لحفظ الماركة
        $sql = "INSERT INTO brands (name) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $brand_name);

        if ($stmt->execute()) {
            $success_msg = "تم إضافة الماركة التجارية بنجاح.";
        } else {
            // فحص كود الخطأ 1062 لمنع الاسم المكرر في قاعدة البيانات
            if ($conn->errno == 1062) {
                $error_msg = "عذراً، هذه الماركة التجارية مضافة بالفعل في النظام!";
            } else {
                $error_msg = "حدث خطأ أثناء الإضافة: " . $conn->error;
            }
        }
        $stmt->close();
    } else {
        $error_msg = "يرجى كتابة اسم الماركة أولاً!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | إضافة ماركة</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; display: flex; }
        .sidebar { width: 250px; background-color: #343a40; color: white; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .sidebar h3 { text-align: center; color: #ffc107; margin-bottom: 30px; border-bottom: 1px solid #4b545c; padding-bottom: 15px; }
        .sidebar a { display: block; color: #c2c7d0; text-decoration: none; padding: 12px 10px; border-radius: 4px; margin-bottom: 5px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; color: #fff; }
        
        .main-content { flex: 1; padding: 30px; box-sizing: border-box; }
        .form-container { max-width: 500px; background: #ffffff; padding: 25px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-top: 4px solid #28a745; margin-top: 20px; }
        h2 { color: #333; margin-bottom: 20px; }
        
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        
        .form-control { margin-bottom: 15px; text-align: right; }
        .form-control label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
        .form-control input { width: 100%; padding: 10px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; background-color: #e8f0fe; }
        .btn-submit { background-color: #28a745; color: white; padding: 12px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; }
        .btn-submit:hover { background-color: #218838; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #007bff; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>لوحة الإدارة</h3>
    <a href="index.php">🏠 الرئيسية (الاحصائيات)</a>
    <a href="manage_customer.php">👥 إدارة العملاء</a>
    <a href="manage_admin.php">🔑 إدارة المدراء</a>
    <a href="manage_brand.php" class="active">🏷️ إدارة الماركات</a>
    <a href="manage_mobile.php">📱 إدارة الهواتف</a>
    <a href="manage_order.php">📦 إدارة الطلبات</a>
    <a href="../index.php" target="_blank">🌐 عرض المتجر للزبائن</a>
</div>

<div class="main-content">
    <div class="form-container">
        <h2>إضافة ماركة تجارية جديدة</h2>

        <?php if(!empty($success_msg)): ?>
            <div class="alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if(!empty($error_msg)): ?>
            <div class="alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="add_brand.php" method="POST">
            <div class="form-control">
                <label for="brand_name">اسم الماركة التجارية:</label>
                <input type="text" id="brand_name" name="brand_name" placeholder="مثال: Apple, Samsung, Huawei" required>
            </div>

            <button type="submit" name="submit_brand" class="btn-submit">حفظ الماركة</button>
            <a href="manage_brand.php" class="back-link">← العودة لقائمة الماركات</a>
        </form>
    </div>
</div>

</body>
</html>
