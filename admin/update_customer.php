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

if (isset($_GET['id'])) {
    $customer_id = intval($_GET['id']);
    
    $sql_fetch = "SELECT id, name, phone, email FROM users WHERE id = ? AND role = 0";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $customer_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    
    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
    } else {
        die("العميل غير موجود في النظام!");
    }
    $stmt_fetch->close();
} else {
    header("Location: manage_customer.php");
    exit();
}

if (isset($_POST['update_customer'])) {
    $name  = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    if (!empty($name) && !empty($phone) && !empty($email)) {
        
        $sql_update = "UPDATE users SET name = ?, phone = ?, email = ? WHERE id = ? AND role = 0";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $name, $phone, $email, $customer_id);

        if ($stmt_update->execute()) {
            $success_msg = "تم تحديث بيانات العميل بنجاح.";
            $customer['name']  = $name;
            $customer['phone'] = $phone;
            $customer['email'] = $email;
        } else {
            if ($conn->errno == 1062) {
                $error_msg = "عذراً، البريد الإلكتروني مستخدم بالفعل من قبل حساب آخر!";
            } else {
                $error_msg = "حدث خطأ أثناء التحديث: " . $conn->error;
            }
        }
        $stmt_update->close();
    } else {
        $error_msg = "يرجى تعبئة جميع الحقول المطلوبة!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | تعديل بيانات العميل</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; display: flex; }
        
        .sidebar { width: 250px; background-color: #343a40; color: white; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .sidebar h3 { text-align: center; color: #ffc107; margin-bottom: 30px; border-bottom: 1px solid #4b545c; padding-bottom: 15px; }
        .sidebar a { display: block; color: #c2c7d0; text-decoration: none; padding: 12px 10px; border-radius: 4px; margin-bottom: 5px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; color: #fff; }
        
        .main-content { flex: 1; padding: 30px; box-sizing: border-box; }
        .form-container { max-width: 500px; background: #ffffff; padding: 25px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-top: 4px solid #ffc107; margin-top: 20px; }
        h2 { color: #333; margin-bottom: 20px; }
        
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        
        .form-control { margin-bottom: 15px; text-align: right; }
        .form-control label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
        .form-control input { width: 100%; padding: 10px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; background-color: #e8f0fe; }
        .btn-submit { background-color: #ffc107; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; }
        .btn-submit:hover { background-color: #e0a800; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #007bff; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>لوحة الإدارة</h3>
    <a href="index.php">🏠 الرئيسية (الاحصائيات)</a>
    <a href="manage_customer.php" class="active">👥 إدارة العملاء</a>
    <a href="manage_admin.php">🔑 إدارة المدراء</a>
    <a href="manage_brand.php">🏷️ إدارة الماركات</a>
    <a href="manage_mobile.php">📱 إدارة الهواتف</a>
    <a href="manage_order.php">📦 إدارة الطلبات</a>
    <a href="../index.php" target="_blank">🌐 عرض المتجر للزبائن</a>
</div>

<div class="main-content">
    <div class="form-container">
        <h2>تعديل بيانات العميل الحالي</h2>

        <?php if(!empty($success_msg)): ?>
            <div class="alert-success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <?php if(!empty($error_msg)): ?>
            <div class="alert-danger"><?php echo $error_msg; ?></div>
        <?php endif; ?>

            <form action="" method="POST">
            <div class="form-control">
            <label for="name">الاسم الكامل للزبون:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($customer['name']); ?>" required>
            </div>

            <div class="form-control">
                <label for="phone">رقم الهاتف النشط:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
            </div>

            <div class="form-control">
                <label for="email">البريد الإلكتروني المعتمد:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
            </div>

            <button type="submit" name="update_customer" class="btn-submit">تحديث البيانات وحفظها</button>
            <a href="manage_customer.php" class="back-link">← إلغاء والعودة للقائمة</a>
        </form>
    </div>
</div>

</body>
</html>
