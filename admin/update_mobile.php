<?php
// update_mobile.php

// 1. الاتصال بقاعدة البيانات
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

// 2. جلب بيانات الهاتف الحالي بناءً على الـ id الممرر عبر الرابط
if (isset($_GET['id'])) {
    $phone_id = intval($_GET['id']);
    
    $sql_fetch = "SELECT * FROM phones WHERE id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $phone_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    
    if ($result->num_rows > 0) {
        $phone = $result->fetch_assoc();
    } else {
        die("الهاتف غير موجود في النظام!");
    }
    $stmt_fetch->close();
} else {
    header("Location: manage_mobile.php");
    exit();
}

// 3. جلب جميع الماركات التجارية لتعبئة القائمة المنسدلة (Select Dropdown)
$brands_result = $conn->query("SELECT * FROM brands ORDER BY name ASC");

// 4. معالجة تحديث البيانات عند إرسال الفورم (POST)
if (isset($_POST['update_mobile'])) {
    $model    = trim($_POST['model']);
    $price    = trim($_POST['price']);
    $brand_id = $_POST['brand_id'];

    if (!empty($model) && !empty($price) && !empty($brand_id)) {
        
        // استعلام التحديث لجدول الهواتف
        $sql_update = "UPDATE phones SET model = ?, price = ?, brand_id = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sdii", $model, $price, $brand_id, $phone_id);

        if ($stmt_update->execute()) {
            $success_msg = "تم تحديث بيانات الهاتف بنجاح.";
            // تحديث المتغير لعرض القيم المحدثة داخل الحقول فوراً
            $phone['model']    = $model;
            $phone['price']    = $price;
            $phone['brand_id'] = $brand_id;
        } else {
            $error_msg = "حدث خطأ أثناء تحديث البيانات: " . $conn->error;
        }
        $stmt_update->close();
    } else {
        $error_msg = "يرجى تعبئة جميع الحقول واختيار الماركة!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | تعديل بيانات الهاتف</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f7f9fa; padding: 30px; }
        .container { max-width: 450px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); border-top: 4px solid #ffc107; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .alert-success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .form-control { margin-bottom: 15px; }
        .form-control label { display: block; margin-bottom: 5px; font-weight: 600; color: #555; }
        .form-control input, .form-control select { width: 100%; padding: 10px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; }
        .btn-block { width: 100%; padding: 10px; background-color: #ffc107; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
        .btn-block:hover { background-color: #e0a800; color: #fff; }
        .back-link { display: block; text-align: center; margin-top: 15px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <h2>تعديل بيانات الهاتف</h2>

    <?php if(!empty($success_msg)): ?>
        <div class="alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>

    <?php if(!empty($error_msg)): ?>
        <div class="alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="update_mobile.php?id=<?php echo $phone_id; ?>" method="POST">
        
        <div class="form-control">
            <label for="brand_id">الماركة التجارية:</label>
            <select id="brand_id" name="brand_id" required>
                <option value="">-- اختر الماركة --</option>
                <?php 
                if ($brands_result->num_rows > 0) {
                    while($b_row = $brands_result->fetch_assoc()) {
                        $selected = ($b_row['id'] == $phone['brand_id']) ? "selected" : "";
                        echo "<option value='".$b_row['id']."' $selected>".$b_row['name']."</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="form-control">
            <label for="model">اسم وموديل الهاتف:</label>
            <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($phone['model']); ?>" required>
        </div>

        <div class="form-control">
            <label for="price">سعر الهاتف:</label>
            <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($phone['price']); ?>" required>
        </div>

        <button type="submit" name="update_mobile" class="btn-block">تحديث البيانات</button>
        <a href="manage_mobile.php" class="back-link">العودة لشاشة إدارة الهواتف</a>
    </form>
</div>

</body>
</html>
