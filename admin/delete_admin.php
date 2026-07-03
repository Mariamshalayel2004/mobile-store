<?php
// admin/delete_admin.php

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

// 3. التحقق من إرسال رقم المعرّف عبر الرابط (GET)
if (isset($_GET['id'])) {
    
    $admin_id = intval($_GET['id']);
    $current_admin_id = $_SESSION['user_id'];

    // حماية النظام: منع المدير من حذف حسابه الشخصي المسجل به حالياً
    if ($admin_id === $current_admin_id) {
        header("Location: manage_admin.php?error=لا يمكن للمدير حذف حسابه الخاص أثناء تسجيل الدخول!");
        exit();
    }

    // تجهيز استعلام الحذف الآمن (بشرط أن يكون role = 1 لحماية جداول العملاء)
    $sql = "DELETE FROM users WHERE id = ? AND role = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);

    if ($stmt->execute()) {
        header("Location: manage_admin.php?success=تم حذف المشرف وسحب الصلاحيات بنجاح.");
        exit();
    } else {
        die("حدث خطأ أثناء محاولة حذف حساب المدير: " . $conn->error);
    }
    
    $stmt->close();
} else {
    header("Location: manage_admin.php");
    exit();
}

$conn->close();
?>
