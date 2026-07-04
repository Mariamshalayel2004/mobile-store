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
    $order_id = intval($_GET['id']);

    $conn->query("DELETE FROM order_items WHERE order_id = $order_id");

    $sql = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        header("Location: manage_order.php?success=تم إلغاء الفاتورة بنجاح.");
        exit();
    } else {
        die("حدث خطأ أثناء إلغاء الفاتورة: " . $conn->error);
    }
    $stmt->close();
} else {
    header("Location: manage_order.php");
    exit();
}
$conn->close();
?>
