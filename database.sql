-- 1. جدول المستخدمين
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20)NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role TINYINT DEFAULT 0 -- 0 للمستخدم، 1 للمدير
);
CREATE TABLE brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);
-- 3. جدول الهواتف (المنتجات)
CREATE TABLE phones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model VARCHAR(100) NOT NULL, -- اسم الموديل مثل: iPhone 15 Pro
    price DECIMAL(10, 2) NOT NULL,
    brand_id INT, -- ربط الهاتف بالبراند الخاص به
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL
);
-- 4. جدول الطلبات
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
-- 5. جدول تفاصيل الطلب
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    phone_id INT,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (phone_id) REFERENCES phones(id)
);
