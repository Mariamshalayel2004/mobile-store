-- إنشاء جدول المستخدمين (الزبائن والإدارة)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL, 
    role TINYINT DEFAULT 0 CHECK (role IN (0, 1)), -- 0: زبون، 1: مدير
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- إنشاء جدول المنتجات (الهواتف)
CREATE TABLE phones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(50) NOT NULL, -- الشركة المصنعة (مثل Apple, Samsung)
    model VARCHAR(100) NOT NULL, -- موديل الهاتف (مثل iPhone 15 Pro)
    price DECIMAL(10, 2) NOT NULL, -- سعر الهاتف
    storage_capacity VARCHAR(20) NOT NULL, -- مساحة التخزين (مثل 256GB)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);