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

$conn->query("INSERT IGNORE INTO brands (id, name) VALUES (1, 'Apple'), (2, 'Samsung'), (3, 'Xiaomi')");

$check_count = $conn->query("SELECT COUNT(*) AS total FROM phones");
$total_phones = $check_count->fetch_assoc()['total'];

if ($total_phones < 8) {
    $conn->query("SET FOREIGN_KEY_CHECKS = 0;");
    $conn->query("TRUNCATE TABLE phones;");
    $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
    $conn->query("INSERT INTO phones (model, price, brand_id) VALUES 
        ('iPhone 17 Pro Max', 1200.00, 1),
        ('Samsung Galaxy S24 Ultra', 1100.00, 2),
        ('Xiaomi 14 Ultra', 850.00, 3), 
        ('Google Pixel 8 Pro', 750.00, 2),
        ('Apple Watch 11', 399.00, 1),
        ('iphone11', 450.00, 1),
        ('iphone15', 799.00, 1),
        ('Samsung Galaxy S21', 350.00, 2)");
}

$brands_result = $conn->query("SELECT * FROM brands");

if (isset($_GET['brand_id'])) {
    $brand_id = (int)$_GET['brand_id'];
    $sql = "SELECT phones.*, brands.name AS brand_name 
            FROM phones 
            LEFT JOIN brands ON phones.brand_id = brands.id 
            WHERE phones.brand_id = $brand_id";
} elseif (isset($_GET['show']) && $_GET['show'] == 'all') {
    $sql = "SELECT phones.*, brands.name AS brand_name 
            FROM phones 
            LEFT JOIN brands ON phones.brand_id = brands.id";
} else {
    $sql = "SELECT phones.*, brands.name AS brand_name 
            FROM phones 
            LEFT JOIN brands ON phones.brand_id = brands.id 
            LIMIT 4";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Mobile Store | الرئيسية</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; position: relative; }
        header { background-color: #343a40; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header-right { display: flex; align-items: center; gap: 15px; }
        .menu-toggle-btn { background: none; border: none; color: white; font-size: 24px; cursor: pointer; }
        .nav-links a { color: #f8f9fa; text-decoration: none; margin-right: 15px; font-weight: bold; }
        
        .sidebar-menu { position: fixed; top: 0; right: -300px; width: 300px; height: 100vh; background-color: #ffffff; box-shadow: -2px 0 10px rgba(0,0,0,0.1); z-index: 1000; transition: right 0.3s ease; padding: 20px; box-sizing: border-box; }
        .sidebar-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px; }
        .sidebar-link { display: block; color: #333; text-decoration: none; padding: 12px 15px; font-size: 16px; font-weight: 600; border-bottom: 1px solid #f5f5f5; }
        .sidebar-link:hover { background-color: #f8f9fa; color: #007bff; }
        
        .brand-list { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; }
        .brand-list.show { max-height: 500px; }
        .brand-toggle-btn { cursor: pointer; font-weight: bold; padding: 12px 15px; color: #333; border-bottom: 1px solid #f5f5f5; }
        
        .menu-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0,0,0,0.4); z-index: 999; display: none; }
        .sidebar-menu.open { right: 0; }
        .menu-overlay.open { display: block; }
        
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 25px; }
        .product-card { background: #fff; border-radius: 8px; padding: 20px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .product-img { width: 100%; height: 180px; object-fit: contain; }
        .brand-badge { background-color: #e3f2fd; color: #0d47a1; padding: 5px 10px; border-radius: 20px; font-size: 12px; }
        .product-price { font-size: 20px; color: #28a745; font-weight: bold; margin-bottom: 15px; }
        .btn-buy { display: inline-block; width: 100%; padding: 10px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>

<div class="menu-overlay" id="menuOverlay" onclick="toggleMenu()"></div>

<div class="sidebar-menu" id="sidebarMenu">
    <div class="sidebar-header">
        <span class="sidebar-title">القائمة الرئيسية</span>
        <button class="close-menu-btn" onclick="toggleMenu()">✕</button>
    </div>
    <div class="sidebar-content">
        <a href="index.php?show=all" class="sidebar-link">📱 جميع الأجهزة</a>
        
        <div class="brand-toggle-btn" onclick="toggleBrands()">البراندات ▾</div>
        <div class="brand-list" id="brandList">
            <?php while($brand = $brands_result->fetch_assoc()): ?>
                <a href="index.php?brand_id=<?php echo $brand['id']; ?>" class="sidebar-link"><?php echo $brand['name']; ?></a>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<header>
    <div class="header-right">
        <button class="menu-toggle-btn" onclick="toggleMenu()">☰</button>
        <h1>📱 Mobile Store</h1>
    </div>
    <div class="nav-links">
    <a href="admin/login.php">لوحة التحكم</a>
    <a href="index.php">الرئيسية</a>
    <?php if (isset($_SESSION['user_name'])): ?>
        <a href="my_orders.php">طلباتي</a>
        <a href="logout.php">تسجيل الخروج</a>
        <span style="color: #ffc107; font-weight: bold; margin-right: 15px;">
            أهلاً، <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </span>
    <?php else: ?>
        <a href="login.php">تسجيل الدخول</a>
    <?php endif; ?>
</div>
</header>

<div class="container">
    <h2 style="text-align: center; margin-bottom: 30px;">مرحبا بك في متجر mobile_store</h2>
    
    <div class="products-grid">
        <?php 
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $model_lower = strtolower($row['model']);
                if (strpos($model_lower, 'apple watch') !== false) $img_src = 'images/watch.png';
                elseif (strpos($model_lower, 'iphone11') !== false) $img_src = 'images/iphone11.jpg';
                elseif (strpos($model_lower, 'iphone15') !== false) $img_src = 'images/iphone15.jpg';
                elseif (strpos($model_lower, 'samsung galaxy s21') !== false) $img_src = 'images/Samsung Galaxy S21.jpg';
                elseif (strpos($model_lower, 'iphone 17') !== false) $img_src = 'images/iphone.jpg';
                elseif (strpos($model_lower, 'samsung') !== false) $img_src = 'images/samsung.jpg';
                elseif (strpos($model_lower, 'xiaomi') !== false) $img_src = 'images/xiaomi.jpg';
                elseif (strpos($model_lower, 'pixel') !== false) $img_src = 'images/pixel.jpg';
                elseif (strpos($model_lower, 'huawei') !== false) $img_src = 'images/Huawei Nova 14 Pro.jpg';
                else $img_src = 'images/default.jpg'; 
                
                echo '<div class="product-card">';
                echo '<img src="'.$img_src.'" class="product-img">';
                echo '<span class="brand-badge">'.htmlspecialchars($row['brand_name']).'</span>';
                echo '<div>'.htmlspecialchars($row['model']).'</div>';
                echo '<div class="product-price">$'.number_format($row['price'], 2).'</div>';
                echo '<a href="make_order.php?phone_id='.$row['id'].'" class="btn-buy">شراء الآن</a>';
                echo '</div>';
            }
        } else {
            echo '<p>لا توجد أجهزة حالياً.</p>';
        }
        ?>
    </div>
</div>

<script>
function toggleMenu() {
    document.getElementById('sidebarMenu').classList.toggle('open');
    document.getElementById('menuOverlay').classList.toggle('open');
}
function toggleBrands() {
    document.getElementById('brandList').classList.toggle('show');
}
</script>
</body>
</html>
