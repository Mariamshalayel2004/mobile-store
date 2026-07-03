<?php

session_start();
$conn = new mysqli("localhost", "root", "", "mobile_store");
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['user_id'])) {
    die("يرجى تسجيل الدخول أولاً لإتمام الشراء.");
}

if (isset($_GET['phone_id'])) {
    $phone_id = (int)$_GET['phone_id'];
    $user_id = $_SESSION['user_id'];

    $stmt1 = $conn->prepare("INSERT INTO orders (user_id) VALUES (?)");
    $stmt1->bind_param("i", $user_id);
    
    if ($stmt1->execute()) {
        $order_id = $stmt1->insert_id; 

        $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, phone_id, quantity) VALUES (?, ?, 1)");
        $stmt2->bind_param("ii", $order_id, $phone_id);
        
        if ($stmt2->execute()) {
            echo "<script>alert('تمت عملية الشراء بنجاح!'); window.location.href='index.php';</script>";
        } else {
            echo "حدث خطأ أثناء حفظ تفاصيل الطلب.";
        }
    } else {
        echo "حدث خطأ أثناء إنشاء الطلب.";
    }
} else {
    echo "لم يتم تحديد جهاز.";
}
?>