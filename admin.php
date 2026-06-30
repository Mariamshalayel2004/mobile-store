<?php
include 'db.php';

// عرض رسالة التأكيد بعد الحذف إذا كانت موجودة في الرابط
if (isset($_GET['msg'])) {
    echo "<h3 style='color: green;'>" . $_GET['msg'] . "</h3>";
}

$sql = "SELECT id, name, email, phone, address FROM users";
$result = $conn->query($sql);
?>

<table border='1' width='100%'>
    <tr style='background-color: gray;'>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Action</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";
            echo "<td>" . $row["phone"] . "</td>";
            echo "<td>" . $row["address"] . "</td>";
            // هنا الرابط الذي يرسل الـ ID الخاص بكل زبون لصفحة الحذف
            echo "<td><a href='delete_customer.php?id=" . $row["id"] . "'>Delete</a></td>";
            echo "</tr>";
        }
    }
    ?>
</table>