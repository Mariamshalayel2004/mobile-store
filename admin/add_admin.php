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
$conn->set_charset("utf8mb4");

$success_msg = "";
$error_msg   = "";

if (isset($_POST['submit_admin_register'])) {
    $name     = trim($_POST['name']);
    $phone    = trim($_POST['phone']);
    $email    = trim($_POST['email']);
    $pass     = $_POST['password'];
    $role     = 1; 

    if (!empty($name) && !empty($phone) && !empty($email) && !empty($pass)) {
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, phone, email, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $phone, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $success_msg = "تم إنشاء حساب المدير الجديد بنجاح.";
        } else {
            if ($conn->errno == 1062) {
                $error_msg = "عذراً، هذا البريد الإلكتروني مسجل لمدير أو مستخدم آخر مسبقاً!";
            } else {
                $error_msg = "حدث خطأ أثناء إنشاء الحساب: " . $conn->error;
            }
        }
        $stmt->close();
    } else {
        $error_msg = "يرجى تعبئة جميع الحقول المطلوبة بالكامل!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | إضافة أدمن جديد</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f7f9fa; 
            padding: 40px 20px; 
        }
        .container { 
            max-width: 450px; 
            margin: 40px auto; 
            background: #ffffff; 
            padding: 30px; 
            border-radius: 5px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            border-top: 4px solid #1e88e5; 
        }
        h2 { 
            text-align: center; 
            color: #333; 
            margin-bottom: 25px; 
            font-size: 24px;
        }
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; font-size: 14px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; font-size: 14px; }
        
        .form-control { margin-bottom: 15px; text-align: right; }
        .form-control label { display: block; margin-bottom: 6px; font-weight: 600; color: #555; }
        .form-control input { width: 100%; padding: 10px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; background-color: #e8f0fe; }
        .btn-submit { background-color: #1e88e5; color: white; padding: 12px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; }
        .btn-submit:hover { background-color: #1565c0; }
        
        .links-container {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }
        .links-container a {
            color: #1e88e5;
            text-decoration: none;
            font-weight: bold;
        }
        .links-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>تسجيل مشرف نظام جديد</h2>

    <?php if(!empty($success_msg)): ?>
        <div class="alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <?php if(!empty($error_msg)): ?>
        <div class="alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="add_admin.php" method="POST">
        <div class="form-control">
            <label for="name">اسم الإدمن الكامل:</label>
            <input type="text" id="name" name="name" placeholder="أدخل اسم الإدمن" required>
        </div>

        <div class="form-control">
            <label for="phone">رقم هاتف العمل:</label>
            <input type="text" id="phone" name="phone" placeholder="مثال: 0790000000" autocomplete="off" required>
        </div>

        <div class="form-control">
            <label for="email">البريد الإلكتروني المهني:</label>
            <input type="email" id="email" name="email" placeholder="admin@domain.com" autocomplete="off" required>
        </div>

        <div class="form-control">
            <label for="password">تعيين كلمة المرور:</label>
            <input type="password" id="password" name="password" placeholder="أدخل كلمة مرور الإدمن" autocomplete="new-password" required>
        </div>

        <button type="submit" name="submit_admin_register" class="btn-submit">إنشاء حساب الإدمن</button>
        
        <div class="links-container">
            <a href="manage_admin.php">← العودة لقائمة المدراء</a>
            <a href="login.php">العودة لشاشة تسجيل الدخول</a>
        </div>
    </form>
</div>

</body>
</html>
