<?php
// admin/delete_brand.php

// 1. بدء الجلسة وفحص الصلاحية لحماية النظام
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

// 3. التحقق من استقبال المعرّف عبر الرابط (GET)
if (isset($_GET['id'])) {
    $brand_id = intval($_GET['id']);

    // استعلام الحذف الآمن للماركة
    $sql = "DELETE FROM brands WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $brand_id);

    if ($stmt->execute()) {
        header("Location: manage_brand.php?success=تم حذف الماركة التجارية بنجاح.");
        exit();
    } else {
        // التحقق إذا كانت الماركة مرتبطة بهواتف تمنع حذفها بسبب المفاتيح الأجنبية
        if ($conn->errno == 1451) {
            die("<script>alert('عذراً، لا يمكن حذف هذه الماركة لأن هناك هواتف مرتبطة بها في المتجر!'); window.location.href='manage_brand.php';</script>");
        } else {
            die("حدث خطأ أثناء محاولة الحذف: " . $conn->error);
        }
    }
    $stmt->close();
} else {
    header("Location: manage_brand.php");
    exit();
}

$conn->close();
?>
