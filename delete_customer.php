<?php
// admin/delete_customer.php

// 1. بدء الجلسة وفحص صلاحية الإدمن لحماية عملية الحذف
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

// 3. التحقق من إرسال رقم المعرّف للعميل عبر الرابط (GET)
if (isset($_GET['id'])) {
    
    // تحويل القيمة لرقم صحيح (Integer) لحماية الكود من الثغرات
    $customer_id = intval($_GET['id']);

    // تجهيز استعلام الحذف الآمن باستخدام Prepared Statement (بشرط أن يكون role = 0 لحماية حسابات الإدمن من الحذف بالخطأ)
    $sql = "DELETE FROM users WHERE id = ? AND role = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);

    // تنفيذ الاستعلام وإعادة التوجيه لصفحة الإدارة
    if ($stmt->execute()) {
        header("Location: manage_customer.php");
        exit();
    } else {
        die("حدث خطأ أثناء محاولة حذف حساب العميل: " . $conn->error);
    }
    
    $stmt->close();
} else {
    // إذا تم الدخول للملف مباشرة بدون تحديد id يتم إرجاعه لصفحة الإدارة
    header("Location: manage_customer.php");
    exit();
}

$conn->close();
?>
