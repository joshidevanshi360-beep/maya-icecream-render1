-- =====================================================
-- Maya Ice Cream Shop - MySQL Database
-- Database: maya_icecream
-- For XAMPP / phpMyAdmin import
-- =====================================================

-- Drop database if it already exists and create fresh
DROP DATABASE IF EXISTS maya_icecream;
CREATE DATABASE maya_icecream CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE maya_icecream;

-- =====================================================
-- 1. USERS TABLE
-- =====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 2. ADMIN TABLE
-- =====================================================
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 3. PRODUCTS TABLE
-- =====================================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    image VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 4. CART TABLE
-- =====================================================
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- =====================================================
-- 5. ORDERS TABLE
-- =====================================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    mobile VARCHAR(15) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =====================================================
-- 6. ORDER_ITEMS TABLE
-- =====================================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- =====================================================
-- 7. PAYMENTS TABLE
-- =====================================================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_status VARCHAR(30) NOT NULL DEFAULT 'Success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- =====================================================
-- SAMPLE DATA: ADMIN
-- Default admin username: admin
-- Default admin password: admin123
-- =====================================================
INSERT INTO admin (username, password) VALUES
('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMy.MQDqo1Z3c2ePp3QqN4ZkT3ZkT3ZkT3Z');

-- =====================================================
-- SAMPLE DATA: PRODUCTS
-- =====================================================
INSERT INTO products (name, description, price, category, image) VALUES
-- Chocolate
('Classic Chocolate', 'Rich and creamy chocolate ice cream made with premium cocoa powder and a smooth finish.', 180.00, 'Chocolate', 'https://images.pexels.com/photos/7761734/pexels-photo-7761734.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Double Choco Delight', 'Double the chocolate with dark cocoa chunks blended into a velvety scoop.', 220.00, 'Chocolate', 'https://images.pexels.com/photos/19087694/pexels-photo-19087694.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Choco Fudge Brownie', 'Decadent fudge brownie pieces folded into rich chocolate ice cream.', 250.00, 'Chocolate', 'https://images.pexels.com/photos/31815432/pexels-photo-31815432.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),

-- Vanilla
('Pure Vanilla Bean', 'Classic vanilla ice cream made with real vanilla beans for an authentic taste.', 160.00, 'Vanilla', 'https://images.pexels.com/photos/30627481/pexels-photo-30627481.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Vanilla Caramel Swirl', 'Smooth vanilla ice cream with ribbons of golden caramel running through.', 200.00, 'Vanilla', 'https://images.pexels.com/photos/13878328/pexels-photo-13878328.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),

-- Strawberry
('Fresh Strawberry', 'Made with real strawberry pieces for a naturally sweet and fruity experience.', 180.00, 'Strawberry', 'https://images.pexels.com/photos/5535554/pexels-photo-5535554.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Strawberry Cheesecake', 'Creamy strawberry ice cream with cheesecake bits and a berry swirl.', 240.00, 'Strawberry', 'https://images.pexels.com/photos/5535556/pexels-photo-5535556.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),

-- Mango
('Alphonso Mango', 'Seasonal Alphonso mango ice cream bursting with tropical sweetness.', 210.00, 'Mango', 'https://images.pexels.com/photos/29530032/pexels-photo-29530032.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Mango Tango Sorbet', 'A refreshing dairy-free mango sorbet with a tangy tropical kick.', 190.00, 'Mango', 'https://images.pexels.com/photos/7190365/pexels-photo-7190365.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),

-- Butterscotch
('Butterscotch Crunch', 'Golden butterscotch ice cream with crunchy caramelized bits.', 200.00, 'Butterscotch', 'https://images.pexels.com/photos/13878326/pexels-photo-13878326.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Caramel Butterscotch', 'Smooth butterscotch blended with salted caramel ribbons.', 230.00, 'Butterscotch', 'https://images.pexels.com/photos/30627481/pexels-photo-30627481.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),

-- Cookies & Cream
('Cookies & Cream', 'Vanilla ice cream loaded with crushed chocolate cookie pieces.', 220.00, 'Cookies & Cream', 'https://images.pexels.com/photos/19087694/pexels-photo-19087694.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Oreo Blast', 'Extra cookies blended into creamy vanilla for the ultimate cookie lover scoop.', 260.00, 'Cookies & Cream', 'https://images.pexels.com/photos/11512980/pexels-photo-11512980.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),

-- Special Flavours
('Rainbow Sherbet', 'A vibrant swirl of orange, raspberry, and lime sherbet.', 250.00, 'Special Flavours', 'https://images.pexels.com/photos/1362534/pexels-photo-1362534.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Blue Lagoon', 'A fun blue-colored ice cream with tropical fruit flavors and sprinkles.', 270.00, 'Special Flavours', 'https://images.pexels.com/photos/8093197/pexels-photo-8093197.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Sundae Special', 'A grand sundae with three scoops, whipped cream, nuts, and cherry on top.', 350.00, 'Special Flavours', 'https://images.pexels.com/photos/15953040/pexels-photo-15953040.jpeg?auto=compress&cs=tinysrgb&h=650&w=940'),
('Kulfi Royale', 'Traditional Indian kulfi with pistachio and cardamom flavors.', 280.00, 'Special Flavours', 'https://images.pexels.com/photos/29699512/pexels-photo-29699512.jpeg?auto=compress&cs=tinysrgb&h=650&w=940');

-- =====================================================
-- SAMPLE DATA: TEST USER
-- Email: test@example.com
-- Password: password123
-- =====================================================
INSERT INTO users (full_name, email, mobile, password) VALUES
('Test User', 'test@example.com', '9876543210', '$2y$10$N9qo8uLOickgx2ZMRZoMy.MQDqo1Z3c2ePp3QqN4ZkT3ZkT3ZkT3Z');
