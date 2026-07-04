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

mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$success_msg = "";
$error_msg   = "";

if (isset($_POST['submit_customer'])) {
    $name  = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $role  = 0; 

    if (!empty($name) && !empty($phone) && !empty($email) && !empty($pass)) {
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, phone, email, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $phone, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $success_msg = "تم إضافة العميل الجديد بنجاح.";
        } else {
            if ($conn->errno == 1062) {
                $error_msg = "عذراً، هذا البريد الإلكتروني مسجل لعميل آخر مسبقاً!";
            } else {
                $error_msg = "حدث خطأ أثناء إضافة العميل.";
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
    <title>إضافة عميل جديد</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        
        .form-container { 
            width: 100%; 
            max-width: 500px; 
            background: #ffffff; 
            padding: 30px; 
            border-radius: 5px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            border-top: 4px solid #007bff; 
            margin: 20px;
        }
        h2 { color: #333; text-align: center; margin-bottom: 25px; }
        
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; }
        
        .form-control { margin-bottom: 15px; text-align: right; }
        .form-control label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
        .form-control input { width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box; background-color: #f8f9fa; }
        
        .btn-submit { background-color: #007bff; color: white; padding: 12px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; margin-top: 10px; }
        .btn-submit:hover { background-color: #0069d9; }
        
        .back-link { display: block; text-align: center; margin-top: 15px; color: #007bff; text-decoration: none; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>إضافة حساب عميل جديد</h2>

    <?php if(!empty($success_msg)): ?>
        <div class="alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <?php if(!empty($error_msg)): ?>
        <div class="alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="add_customer.php" method="POST">
        <div class="form-control">
            <label>الاسم الكامل:</label>
            <input type="text" name="name" placeholder="أدخل اسم العميل" required>
        </div>

        <div class="form-control">
            <label>رقم الهاتف:</label>
            <input type="text" name="phone" placeholder="مثال: 0790000000" required>
        </div>

        <div class="form-control">
             <label>البريد الإلكتروني:</label>
             <input type="email" name="email" placeholder="customer@example.com" autocomplete="off" required>
        </div>

        <div class="form-control">
             <label>كلمة المرور:</label>
             <input type="password" name="password" placeholder="أدخل كلمة المرور" autocomplete="new-password" required>
        </div>

        <button type="submit" name="submit_customer" class="btn-submit">حفظ بيانات العميل</button>
        <a href="manage_customer.php" class="back-link">← العودة لقائمة العملاء</a>
    </form>
</div>

</body>
</html>
