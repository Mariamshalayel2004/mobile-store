<?php
// admin/add_customer.php

// 1. بدء الجلسة وفحص الصلاحية لحماية الصفحة
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: login.php");
    exit();
}

// 2. الاتصال المباشر بقاعدة البيانات
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

// 3. معالجة البيانات القادمة من الفورم (POST)
if (isset($_POST['submit_customer'])) {
    $name  = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $role  = 0; // القيمة 0 تعني زبون/عميل دائماً

    if (!empty($name) && !empty($phone) && !empty($email) && !empty($pass)) {
        
        // تشفير كلمة المرور لحماية الحساب
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

        // استعلام الإدخال الآمن (Prepared Statement)
        $sql = "INSERT INTO users (name, phone, email, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $phone, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $success_msg = "تم إضافة العميل الجديد بنجاح.";
        } else {
            if ($conn->errno == 1062) {
                $error_msg = "عذراً، هذا البريد الإلكتروني مسجل لعميل آخر مسبقاً!";
            } else {
                $error_msg = "حدث خطأ أثناء إضافة العميل: " . $conn->error;
            }
        }
        $stmt->close();
    } else {
        $error_msg = "يرجى تعبئة جميع الحقول المطلوبة بالكامل!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | إضافة عميل جديد</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; display: flex; }
        
        /* القائمة الجانبية الموحدة */
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
        .btn-submit { background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; }
        .btn-submit:hover { background-color: #218838; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #007bff; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>لوحة الإدارة</h3>
    <a href="index.php">🏠 الرئيسية (الاحصائيات)</a>
    <a href="manage_customer.php" class="active">👥 إدارة العملاء</a>
    <a href="manage_admin.php">🔑 إدارة المدراء</a>
    <a href="manage_brand.php">🏷️ إدارة الماركات</a>
    <a href="manage_mobile.php">📱 إدارة الهواتف</a>
    <a href="manage_order.php">📦 إدارة الطلبات</a>
    <a href="../index.php" target="_blank">🌐 عرض المتجر للزبائن</a>
</div>

<div class="main-content">
    <div class="form-container">
        <h2>إضافة حساب عميل جديد</h2>

        <?php if(!empty($success_msg)): ?>
            <div class="alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if(!empty($error_msg)): ?>
            <div class="alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="add_customer.php" method="POST">
            <div class="form-control">
                <label for="name">الاسم الكامل:</label>
                <input type="text" id="name" name="name" placeholder="أدخل اسم العميل" required>
            </div>

            <div class="form-control">
                <label for="phone">رقم الهاتف:</label>
                <input type="text" id="phone" name="phone" placeholder="مثال: 0790000000" autocomplete="off" required>
            </div>

            <div class="form-control">
                <label for="email">البريد الإلكتروني:</label>
                <input type="email" id="email" name="email" placeholder="customer@example.com" autocomplete="off" required>
            </div>

            <div class="form-control">
                <label for="password">كلمة المرور:</label>
                <input type="password" id="password" name="password" placeholder="أدخل كلمة المرور" autocomplete="new-password" required>
            </div>

            <button type="submit" name="submit_customer" class="btn-submit">حفظ بيانات العميل</button>
            <a href="manage_customer.php" class="back-link">← العودة لقائمة العملاء</a>
        </form>
    </div>
</div>

</body>
</html>
