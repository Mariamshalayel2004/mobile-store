<?php
// login.php

// 1. بدء الجلسة لتخزين بيانات المستخدم بعد نجاح تسجيل الدخول
session_start();

// إذا كان المستخدم مسجل دخوله بالفعل، يتم توجيهه مباشرة حسب صلاحيته
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 1) {
        header("Location: admin/index.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// 2. الاتصال بقاعدة البيانات
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

// 3. معالجة البيانات القادمة من نموذج تسجيل الدخول عند الإرسال (POST)
if (isset($_POST['submit_login'])) {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    if (!empty($email) && !empty($pass)) {
        
        // تجهيز استعلام جلب الحساب بناءً على البريد الإلكتروني لحماية الكود من ثغرات الـ SQL Injection
        $sql = "SELECT id, name, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // فحص ما إذا كان البريد الإلكتروني مسجلاً في النظام
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // فحص ومطابقة كلمة المرور المدخلة مع الكلمة المشفرة المخزنة بالداتابيز
            if (password_verify($pass, $user['password'])) {
                
                // تخزين البيانات الهامة في الجلسة (Session)
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role']; // 1 للإدمن، 0 للزبون

                // التوجيه الذكي للمستخدم حسب مستوى صلاحيته في النظام
                if ($user['role'] == 1) {
                    header("Location: admin/index.php"); // لوحة تحكم الإدمن
                } else {
                    header("Location: index.php"); // واجهة متجر الزبائن
                }
                exit();
                
            } else {
                $error_msg = "كلمة المرور التي أدخلتها غير صحيحة!";
            }
        } else {
            $error_msg = "عذراً، هذا البريد الإلكتروني غير مسجل في النظام!";
        }
        $stmt->close();
    } else {
        $error_msg = "يرجى كتابة البريد الإلكتروني وكلمة المرور بالكامل!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>متجر الهواتف | تسجيل الدخول</title>
    <style>
        /* الحفاظ على نفس ستايل التصميم والألوان الظاهرة في صورتك تماماً */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f7f9fa; 
            padding: 50px 20px; 
        }
        .container { 
            max-width: 450px; 
            margin: 0 auto; 
            background: #ffffff; 
            padding: 30px; 
            border-radius: 5px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            border-top: 4px solid #007bff; 
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
        }
        .form-control input { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #cccccc; 
            border-radius: 4px; 
            box-sizing: border-box; 
            background-color: #e8f0fe; /* نفس درجة اللون الأزرق الفاتح في الحقول بصورتك */
        }
        .btn-block { 
            width: 100%; 
            padding: 12px; 
            background-color: #007bff; 
            color: white; 
            border: none; 
            border-radius: 4px; 
            font-size: 16px; 
            font-weight: bold; 
            cursor: pointer; 
        }
        .btn-block:hover { 
            background-color: #0056b3; 
        }
        .links-container {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            line-height: 1.8;
        }
        .links-container a {
            color: #007bff;
            text-decoration: none;
        }
        .links-container a:hover {
            text-decoration: underline;
        }
        .register-text {
            color: #555;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>تسجيل الدخول للمتجر</h2>

    <!-- عرض رسالة الخطأ في حال تعذر تسجيل الدخول -->
    <?php if(!empty($error_msg)): ?>
        <div class="alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-control">
            <label for="email">البريد الإلكتروني:</label>
            <!-- تمت إضافة خاصية autocomplete="off" هنا -->
            <input type="email" id="email" name="email" placeholder="أدخل بريدك الإلكتروني" autocomplete="off" required>
        </div>

        <div class="form-control">
            <label for="password">كلمة المرور:</label>
            <!-- تمت إضافة خاصية autocomplete="new-password" هنا -->
            <input type="password" id="password" name="password" placeholder="أدخل كلمة المرور" autocomplete="new-password" required>
        </div>

        <button type="submit" name="submit_login" class="btn-block">دخول</button>
        
        <!-- إضافة روابط التنقل والإنشاء الجديدة أسفل الزر مباشرة -->
        <!-- الروابط المحدثة لملف login.php -->
        <div class="links-container">
            <div class="register-text">
                ليس لديك حساب؟ <a href="register.php">تسجيل جديد</a>
            </div>
            <div>
                <a href="index.php">← العودة لتصفح المتجر</a>
            </div>
        </div>
    </form>
</div>

</body>
</html>
