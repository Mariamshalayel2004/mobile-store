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

$brands_result = $conn->query("SELECT * FROM brands ORDER BY name ASC");

if (isset($_POST['submit_mobile'])) {
    $model    = trim($_POST['model']);
    $price    = trim($_POST['price']);
    $brand_id = $_POST['brand_id'];

    if (!empty($model) && !empty($price) && !empty($brand_id)) {
        
        $sql = "INSERT INTO phones (model, price, brand_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdi", $model, $price, $brand_id);

        if ($stmt->execute()) {
            $success_msg = "تم إضافة الهاتف الذكي بنجاح في المتجر.";
        } else {
            $error_msg = "حدث خطأ أثناء إضافة الهاتف: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_msg = "يرجى تعبئة جميع الحقول واختيار الماركة المناسبة!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | إضافة هاتف</title>
   <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
    
    .form-container { 
        width: 100%; 
        max-width: 600px; 
        background: #ffffff; 
        padding: 30px; 
        border-radius: 6px; 
        box-shadow: 0 0 10px rgba(0,0,0,0.1); 
        border-top: 4px solid #1e88e5; 
    }
    
    h2 { text-align: center; color: #333; margin-bottom: 25px; font-size: 24px; }
    
    .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; }
    .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; }
    
    .form-control { margin-bottom: 15px; text-align: right; }
    .form-control label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
    .form-control input, .form-control select { width: 100%; padding: 12px; border: 1px solid #ced4da; border-radius: 4px; box-sizing: border-box; background-color: #f8f9fa; }
    
    .btn-submit { background-color: #1e88e5; color: white; padding: 12px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; width: 100%; margin-top: 10px; }
    .btn-submit:hover { background-color: #1565c0; }
    
    .back-link-container { display: block; text-align: center; margin-top: 20px; font-size: 14px; }
    .back-link { color: #1e88e5; text-decoration: none; font-weight: bold; }
    .back-link:hover { text-decoration: underline; }
</style>
</head>
<body>

<div class="form-container">
    <h2>إضافة هاتف جديد للمتجر</h2>

    <?php if(!empty($success_msg)): ?>
        <div class="alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <?php if(!empty($error_msg)): ?>
        <div class="alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="add_mobile.php" method="POST">
        <div class="form-control">
            <label for="brand_id">اختر الماركة التجارية:</label>
            <select id="brand_id" name="brand_id" required>
                <option value="">-- اختر من الماركات المتاحة --</option>
                <?php 
                if ($brands_result->num_rows > 0) {
                    while($b_row = $brands_result->fetch_assoc()) {
                        echo "<option value='".$b_row['id']."'>".$b_row['name']."</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="form-control">
            <label for="model">اسم وموديل الهاتف:</label>
            <input type="text" id="model" name="model" placeholder="مثال: iPhone 15 Pro" autocomplete="off" required>
        </div>

        <div class="form-control">
            <label for="price">سعر الهاتف (بالعملة المحلية):</label>
            <input type="number" step="0.01" id="price" name="price" placeholder="مثال: 999.99" required>
        </div>

        <button type="submit" name="submit_mobile" class="btn-submit">حفظ الهاتف في المتجر</button>
        
        <div class="back-link-container">
            <a href="manage_mobile.php" class="back-link">← العودة إلى إدارة الهواتف</a>
        </div>
    </form>
</div>

</body>
</html>
