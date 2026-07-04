<?php

session_start();

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "mobile_store"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$error_msg = "";

if (isset($_POST['submit_login'])) {
    
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $pass  = $_POST['password'];

    if (!empty($email) && !empty($pass)) {
        
        $sql = "SELECT id, name, password, role FROM users WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($pass, $user['password']) || $pass === $user['password']) {
                
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                header("Location: index.php");
                exit();
                
            } else {
                $error_msg = "كلمة المرور التي أدخلتها غير صحيحة!";
            }
        } else {
            $error_msg = "عذراً، هذا البريد الإلكتروني غير مسجل في النظام!";
        }
    } else {
        $error_msg = "يرجى كتابة البريد الإلكتروني وكلمة المرور بالكامل!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Mobile Store | تسجيل دخول للإدمن</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f7f9fa; 
            padding: 50px 20px; 
        }
        .container { 
            max-width: 450px; 
            margin: 60px auto; 
            background: #ffffff; 
            padding: 35px; 
            border-radius: 5px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
        }
        h2 { 
            text-align: center; 
            color: #333; 
            margin-bottom: 25px; 
            font-size: 24px;
        }
        .alert-danger { 
            background-color: #f8d7da; 
            color: #721c24; 
            padding: 10px; 
            border-radius: 4px; 
            margin-bottom: 20px; 
            text-align: center; 
            font-size: 14px; 
        }
        .form-control { 
            margin-bottom: 20px; 
            text-align: right;
        }
        .form-control label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #555; 
            font-size: 14px;
        }
        .form-control input { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #cccccc; 
            border-radius: 4px; 
            box-sizing: border-box; 
            background-color: #e8f0fe; 
        }
        .btn-block { 
            width: 100%; 
            padding: 12px; 
            background-color: #1e88e5; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            font-size: 16px; 
            font-weight: bold; 
            cursor: pointer; 
        }
        .btn-block:hover { 
            background-color: #1565c0; 
        }
    </style>
</head>
<body>

<div class="container">
    <h2>تسجيل الدخول للادمن</h2>

    <?php if(!empty($error_msg)): ?>
        <div class="alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-control">
            <label for="email">البريد الإلكتروني:</label>
            <input type="text" id="email" name="email" placeholder="أدخل البريد الإلكتروني" autocomplete="off" required>
        </div>

        <div class="form-control">
            <label for="password">كلمة المرور:</label>
            <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="new-password" required>
        </div>

        <button type="submit" name="submit_login" class="btn-block">دخول</button>

<div style="margin-top: 15px; text-align: center;">
    <a href="../index.php" style="text-decoration: none; color: #007bff;">← العودة إلى المتجر</a>
</div>
        
        


</body>
</html>
