<?php
include 'db.php';

$sql = "SELECT id, name, email, phone, address FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer List</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; text-align: center; }
        table { width: 85%; margin: 20px auto; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>

    <h2>Registered Customers List</h2>

    <?php
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email Address</th>
                <th>Phone Number</th>
                <th>Address</th>
              </tr>";
        
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";
            echo "<td>" . $row["phone"] . "</td>";
            echo "<td>" . $row["address"] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No customers registered yet.</p>";
    }

    $conn->close();
    ?>

</body>
</html>