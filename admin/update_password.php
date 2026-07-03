<?php
// admin/update_password.php

// 1. بدء الجلسة وفحص الصلاحية لحماية الصفحة
session_start(); 
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: login.php");
    exit();
}

// 2. الاتصال بقاعدة البيانات
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

// 3. التحقق من استقبال معرف المدير المطلوب تعديل باسورده عبر الرابط (GET)
if (isset($_GET['id'])) {
    $admin_id = intval($_GET['id']);
} else {
    header("Location: manage_admin.php");
    exit();
}

// 4. معالجة البيانات عند إرسال النموذج (POST)
if (isset($_POST['submit_password'])) {
    $new_pass     = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if (!empty($new_pass) && !empty($confirm_pass)) {
        
        // التحقق من تطابق كلمتي المرور الجديدتين
        if ($new_pass === $confirm_pass) {
            
            // تشفير كلمة المرور الجديدة لحماية أمن النظام
            $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
            
            // استعلام تحديث الباسورد للمدير المحدد
            $sql_update = "UPDATE users SET password = ? WHERE id = ? AND role = 1";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $hashed_password, $admin_id);
            
            if ($stmt_update->execute()) {
                $success_msg = "تم تحديث كلمة المرور للمشرف بنجاح.";
            } else {
                $error_msg = "حدث خطأ أثناء تحديث البيانات: " . $conn->error;
            }
            $stmt_update->close();
            
        } else {
            $error_msg = "كلمة المرور الجديدة وتأكيدها غير متطابقين!";
        }
    } else {
        $error_msg = "يرجى ملء جميع الحقول المطلوبة!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | تغيير كلمة المرور</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; display: flex; }
        
        /* القائمة الجانبية الموحدة والثابتة */
        .sidebar { width: 250px; background-color: #343a40; color: white; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .sidebar h3 { text-align: center; color: #ffc107; margin-bottom: 30px; border-bottom: 1px solid #4b545c; padding-bottom: 15px; }
        .sidebar a { display: block; color: #c2c7d0; text-decoration: none; padding: 12px 10px; border-radius: 4px; margin-bottom: 5px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; color: #fff; }
        
        .main-content { flex: 1; padding: 30px; box-sizing: border-box; }
        .form-container { max-width: 500px; background: #ffffff; padding: 25px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-top: 4px solid #6c757d; margin-top: 20px; }
        h2 { color: #333; margin-bottom: 20px; }
        
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        
        .form-control { margin-bottom: 15px; text-align: right; }
        .form-control label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
        .form-control input { width: 100%; padding: 10px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; background-color: #e8f0fe; }
        .btn-submit { background-color: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; }
        .btn-submit:hover { background-color: #5a6268; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #007bff; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>لوحة الإدارة</h3>
    <a href="index.php">🏠 الرئيسية (الاحصائيات)</a>
    <a href="manage_customer.php">👥 إدارة العملاء</a>
    <a href="manage_admin.php" class="active">🔑 إدارة المدراء</a>
    <a href="manage_brand.php">🏷️ إدارة الماركات</a>
    <a href="manage_mobile.php">📱 إدارة الهواتف</a>
    <a href="manage_order.php">📦 إدارة الطلبات</a>
    <a href="../index.php" target="_blank">🌐 عرض المتجر للزبائن</a>
</div>

<div class="main-content">
    <div class="form-container">
        <h2>تغيير كلمة مرور المشرف</h2>

        <?php if(!empty($success_msg)): ?>
            <div class="alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if(!empty($error_msg)): ?>
            <div class="alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="update_password.php?id=<?php echo $admin_id; ?>" method="POST">
            <div class="form-control">
                <label for="new_password">كلمة المرور الجديدة:</label>
                <input type="password" id="new_password" name="new_password" placeholder="أدخل كلمة المرور الجديدة" required>
            </div>

            <div class="form-control">
                <label for="confirm_password">تأكيد كلمة المرور الجديدة:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="أعد إدخال كلمة المرور" required>
            </div>

            <button type="submit" name="submit_password" class="btn-submit">تحديث كلمة المرور</button>
            <a href="manage_admin.php" class="back-link">← إلغاء والعودة للقائمة</a>
        </form>
    </div>
</div>

</body>
</html>
