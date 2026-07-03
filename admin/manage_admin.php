<?php
// admin/manage_admin.php

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

// جلب جميع المدراء (role = 1) من الداتابيز
$sql = "SELECT id, name, phone, email FROM users WHERE role = 1 ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | إدارة المدراء</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; display: flex; }
        
        /* شريط التنقل الجانبي الموحد */
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
        
        /* تنسيق الأزرار الإدارية الثلاثية والزر الأزرق الجديد */
        .btn-edit { background-color: #ffc107; color: #fff; padding: 5px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; margin-left: 5px; display: inline-block; }
        .btn-edit:hover { background-color: #e0a800; }
        
        /* تحديث لون الزر إلى الأزرق وتغيير النص */
        .btn-password { background-color: #1e88e5; color: #fff; padding: 5px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; margin-left: 5px; display: inline-block; }
        .btn-password:hover { background-color: #1565c0; }
        
        .btn-delete { background-color: #dc3545; color: white; padding: 5px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; font-weight: bold; display: inline-block; }
        .btn-delete:hover { background-color: #c82333; }
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
    <div class="card-box">
        <h2>شاشة إدارة مدراء النظام</h2>

        <div class="btn-add-container">
            <a href="add_admin.php" class="btn-add">+ إضافة مدير جديد</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>رقم المعرف</th>
                    <th>اسم المدير</th>
                    <th>رقم الهاتف</th>
                    <th>البريد الإلكتروني</th>
                    <th>العمليات الإدارية</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        // تعديل نص الزر واللون الأزرق بنجاح هنا
                        echo "<td>
                                <a href='update_password.php?id=" . $row['id'] . "' class='btn-password'>تغيير كلمة السر</a>
                                <a href='update_admin.php?id=" . $row['id'] . "' class='btn-edit'>تعديل</a>
                                <a href='delete_admin.php?id=" . $row['id'] . "' class='btn-delete' onclick='return confirm(\"هل أنتِ متأكدة من حذف حساب هذا المشرف؟\")'>حذف</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>لا يوجد مدراء مسجلين حالياً.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
