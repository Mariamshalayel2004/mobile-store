<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Customer Registration</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        form { display: inline-block; text-align: left; border: 1px solid #ccc; padding: 20px; border-radius: 10px; }
        input { display: block; margin-bottom: 15px; width: 250px; padding: 8px; }
        button { padding: 10px 20px; background-color: #28a745; color: white; border: none; cursor: pointer; width: 100%; }
        button:hover { background-color: #218838; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
    </style>
</head>
<body>

    <h2>Create a New Account</h2>
    
    <form action="add_customer.php" method="POST">
        <label>Full Name:</label>
        <input type="text" name="name" required>

        <label>Email Address:</label>
        <input type="email" name="email" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Phone Number:</label>
        <input type="text" name="phone" required>

        <label>Address:</label>
        <input type="text" name="address" required>

        <button type="submit">Register</button>
    </form>

</body>
</html>
2. ملف add_customer.php (استقبال وحفظ البيانات)
PHP
<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, phone) 
            VALUES ('$name', '$email', '$password', '$phone')";

    if ($connection->query($sql) === TRUE) {
        echo "<h3 style='color: green; text-align: center; margin-top:50px;'>Customer registered successfully!</h3>";
    } else {
        echo "<h3 style='color: red; text-align: center; margin-top:50px;'>Error registering customer: " . $connection->error . "</h3>";
    }

    $connection->close();
}
?>