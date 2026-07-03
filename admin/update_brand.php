<?php
// admin/update_brand.php

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

// 3. جلب اسم الماركة الحالي لعرضه في النموذج قبل التعديل (GET)
if (isset($_GET['id'])) {
    $brand_id = intval($_GET['id']);
    
    $sql_fetch = "SELECT * FROM brands WHERE id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $brand_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    
    if ($result->num_rows > 0) {
        $brand = $result->fetch_assoc();
    } else {
        die("الماركة التجارية غير موجودة في النظام!");
    }
    $stmt_fetch->close();
} else {
    header("Location: manage_brand.php");
    exit();
}

// 4. معالجة البيانات القادمة من الفورم عند الضغط على زر التحديث (POST)
if (isset($_POST['update_brand'])) {
    $brand_name = trim($_POST['brand_name']);

    if (!empty($brand_name)) {
        
        // تجهيز استعلام التحديث (UPDATE)
        $sql_update = "UPDATE brands SET name = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $brand_name, $brand_id);

        if ($stmt_update->execute()) {
            $success_msg = "تم تحديث اسم الماركة التجارية بنجاح.";
            $brand['name'] = $brand_name; // تحديث القيمة المعروضة في الحقل فوراً
        } else {
            if ($conn->errno == 1062) {
                $error_msg = "عذراً، هذا الاسم مستخدم بالفعل لماركة تجارية أخرى!";
            } else {
                $error_msg = "حدث خطأ أثناء التحديث: " . $conn->error;
            }
        }
        $stmt_update->close();
    } else {
        $error_msg = "يرجى كتابة اسم الماركة التجارية أولاً!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | تعديل الماركة</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; display: flex; }
        
        /* القائمة الجانبية الموحدة والثابتة */
        .sidebar { width: 250px; background-color: #343a40; color: white; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .sidebar h3 { text-align: center; color: #ffc107; margin-bottom: 30px; border-bottom: 1px solid #4b545c; padding-bottom: 15px; }
        .sidebar a { display: block; color: #c2c7d0; text-decoration: none; padding: 12px 10px; border-radius: 4px; margin-bottom: 5px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; color: #fff; }
        
        .main-content { flex: 1; padding: 30px; box-sizing: border-box; }
        .form-container { max-width: 500px; background: #ffffff; padding: 25px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-top: 4px solid #ffc107; margin-top: 20px; }
        h2 { color: #333; margin-bottom: 20px; }
        
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        
        .form-control { margin-bottom: 15px; text-align: right; }
        .form-control label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
        .form-control input { width: 100%; padding: 10px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; background-color: #e8f0fe; }
        .btn-submit { background-color: #ffc107; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; }
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
    <a href="manage_brand.php" class="active">🏷️ إدارة الماركات</a>
    <a href="manage_mobile.php">📱 إدارة الهواتف</a>
    <a href="manage_order.php">📦 إدارة الطلبات</a>
    <a href="../index.php" target="_blank">🌐 عرض المتجر للزبائن</a>
</div>

<div class="main-content">
    <div class="form-container">
        <h2>تعديل الماركة التجارية</h2>

        <?php if(!empty($success_msg)): ?>
            <div class="alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if(!empty($error_msg)): ?>
            <div class="alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="update_brand.php?id=<?php echo $brand_id; ?>" method="POST">
            <div class="form-control">
                <label for="brand_name">اسم الماركة التجارية:</label>
                <input type="text" id="brand_name" name="brand_name" value="<?php echo htmlspecialchars($brand['name']); ?>" required>
            </div>

            <button type="submit" name="update_brand" class="btn-submit">تحديث اسم الماركة</button>
            <a href="manage_brand.php" class="back-link">← العودة لقائمة الماركات</a>
        </form>
    </div>
</div>

</body>
</html>
