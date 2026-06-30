<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, phone, address) 
            VALUES ('$name', '$email', '$password', '$phone', '$address')";

    if ($conn->query($sql) === TRUE) {
        echo "<h3 style='color: green; text-align: center; margin-top:50px;'>Customer registered successfully!</h3>";
    } else {
        echo "<h3 style='color: red; text-align: center; margin-top:50px;'>Error registering customer: " . $conn->error . "</h3>";
    }

    $conn->close();
}
?>