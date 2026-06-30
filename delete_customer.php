<?php
include 'db.php';

// 1. التأكد من أن هناك ID قادم من الرابط
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 2. كود الحذف
    $sql = "DELETE FROM users WHERE id = $id";

    // 3. تنفيذ الحذف وإعادة التوجيه
    if ($conn->query($sql) === TRUE) {
        header("Location: admin.php?msg=تم الحذف بنجاح");
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>