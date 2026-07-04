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

$customers_res = $conn->query("SELECT id, name FROM users WHERE role = 0 ORDER BY name ASC");
$phones_res    = $conn->query("SELECT id, model, price FROM phones ORDER BY model ASC");

if (isset($_POST['submit_order'])) {
    $user_id  = intval($_POST['user_id']);
    $phone_id = intval($_POST['phone_id']);
    $qty      = intval($_POST['qty']);

    if ($user_id > 0 && $phone_id > 0 && $qty > 0) {
        
        $phone_query = $conn->query("SELECT price, model FROM phones WHERE id = $phone_id");
        if ($phone_query->num_rows > 0) {
            $phone_data = $phone_query->fetch_assoc();
            $price = $phone_data['price'];
            
            $total_price = $price * $qty;

            $sql = "INSERT INTO orders (user_id) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);

            if ($stmt->execute()) {
                $success_msg = "تم إضافة فاتورة طلب الشراء بنجاح! الحساب الإجمالي الافتراضي: $" . number_format($total_price, 2);
            } else {
                $error_msg = "حدث خطأ أثناء إضافة الطلب: " . $conn->error;
            }
            $stmt->close();
        } else {
            $error_msg = "الجهاز المختار غير موجود!";
        }
    } else {
        $error_msg = "يرجى اختيار العميل والجهاز وتحديد الكمية بشكل صحيح!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | إضافة طلب</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; display: flex; }
        
        .sidebar { width: 250px; background-color: #343a40; color: white; min-height: 100vh; padding: 20px; box-sizing: border-box; }
        .sidebar h3 { text-align: center; color: #ffc107; margin-bottom: 30px; border-bottom: 1px solid #4b545c; padding-bottom: 15px; }
        .sidebar a { display: block; color: #c2c7d0; text-decoration: none; padding: 12px 10px; border-radius: 4px; margin-bottom: 5px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background-color: #495057; color: #fff; }
        
        .main-content { flex: 1; padding: 30px; box-sizing: border-box; }
        
        .form-container { max-width: 650px; background: #ffffff; padding: 35px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-top: 4px solid #007bff; margin: 40px auto; }
        
        h2 { color: #333; margin-bottom: 25px; font-family: sans-serif; text-align: center; font-size: 26px; font-weight: bold; }
        
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; }
        
        .form-control { margin-bottom: 20px; text-align: right; }
        .form-control label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; font-size: 15px; }
        .form-control input, .form-control select { width: 100%; padding: 12px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; background-color: #e8f0fe; font-size: 15px; }
        
        .btn-submit { background-color: #007bff; color: white; padding: 12px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; font-family: sans-serif; }
        .btn-submit:hover { background-color: #0056b3; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #007bff; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>إضافة طلب</h2>

    <?php if(!empty($success_msg)): ?>
        <div class="alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <?php if(!empty($error_msg)): ?>
        <div class="alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="add_order.php" method="POST">
        <div class="form-control">
            <label for="user_id">العميل المشتري:</label>
            <select id="user_id" name="user_id" required>
                <option value="">-- اختر العميل من قاعدة البيانات --</option>
                <?php 
                if ($customers_res->num_rows > 0) {
                    while($c_row = $customers_res->fetch_assoc()) {
                        echo "<option value='".$c_row['id']."'>".$c_row['name']."</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="form-control">
            <label for="phone_id">الجهاز المطلوب شراؤه:</label>
            <select id="phone_id" name="phone_id" required>
                <option value="">-- اختر الهاتف المعروض --</option>
                <?php 
                if ($phones_res->num_rows > 0) {
                    while($p_row = $phones_res->fetch_assoc()) {
                        echo "<option value='".$p_row['id']."'>".$p_row['model']." ($".number_format($p_row['price'], 2).")</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="form-control">
            <label for="qty">الكمية المطلوبة (Qty):</label>
            <input type="number" id="qty" name="qty" min="1" value="1" required>
        </div>

        <button type="submit" name="submit_order" class="btn-submit">حفظ الطلب</button>
        <a href="manage_order.php" class="back-link">← العودة لقائمة الطلبات</a>
    </form>
</div>

</body>
</html>
