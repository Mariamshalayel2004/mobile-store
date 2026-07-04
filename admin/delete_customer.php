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

if (isset($_GET['id'])) {
    
    $customer_id = intval($_GET['id']);

    $sql = "DELETE FROM users WHERE id = ? AND role = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);

    if ($stmt->execute()) {
        header("Location: manage_customer.php");
        exit();
    } else {
        die("حدث خطأ أثناء محاولة حذف حساب العميل: " . $conn->error);
    }
    
    $stmt->close();
} else {
    header("Location: manage_customer.php");
    exit();
}

$conn->close();
?>
