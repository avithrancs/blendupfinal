-- Create DB (change name if you want)
CREATE DATABASE IF NOT EXISTS blendup_final CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blendup_final;

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Drinks (CRUD target)
CREATE TABLE IF NOT EXISTS drinks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  category ENUM('Smoothies','Juices','Seasonal') NOT NULL,
  image_url VARCHAR(500) DEFAULT NULL,
  is_featured TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  order_type ENUM('delivery','pickup') NOT NULL,
  status ENUM('pending','preparing','delivered') NOT NULL DEFAULT 'pending',
  total DECIMAL(10,2) NOT NULL,
  address_json JSON NULL,
  payment_method ENUM('card','cash') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order Items
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  drink_id INT NOT NULL,
  drink_name VARCHAR(120) NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL,
  customizations VARCHAR(255) NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (drink_id) REFERENCES drinks(id)
);

-- Seed an admin and some drinks
INSERT IGNORE INTO users (name, email, password_hash, role)
VALUES ('Admin', 'admin@blendup.local', '$2y$10$2c8nTOi6r5nYp7DkJ4aN5euxmFZo5gEw0Q4krkK5o1j2oE4yZbMtm', 'admin');
-- password: admin123

INSERT INTO drinks (name, price, category, image_url, is_featured) VALUES
('Green Goddess', 8.99, 'Smoothies', 'https://images.unsplash.com/photo-1610970881699-44a5587cabec?w=400&h=300&fit=crop&crop=center', 1),
('Tropical Paradise', 9.49, 'Smoothies', 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=400&h=300&fit=crop&crop=center', 1),
('Fresh Orange Juice', 5.99, 'Juices', 'https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=400&h=300&fit=crop&crop=center', 0);



-- Seed 100 drinks for BlendUp
-- Assumes table: drinks(id, name, price, category, image_url, is_featured, created_at)

INSERT INTO drinks (name, price, category, image_url, is_featured, created_at) VALUES

('Tropical Mango Smoothie', 4.99, 'Smoothies', 'https://picsum.photos/seed/sm1/400/300', 1, NOW()),
('Berry Blast Smoothie', 5.49, 'Smoothies', 'https://picsum.photos/seed/sm2/400/300', 0, NOW()),
('Green Detox Smoothie', 5.99, 'Smoothies', 'https://picsum.photos/seed/sm3/400/300', 1, NOW()),
('Strawberry Banana Smoothie', 4.79, 'Smoothies', 'https://picsum.photos/seed/sm4/400/300', 0, NOW()),
('Pineapple Coconut Smoothie', 5.29, 'Smoothies', 'https://picsum.photos/seed/sm5/400/300', 0, NOW()),
('Avocado Spinach Smoothie', 6.29, 'Smoothies', 'https://picsum.photos/seed/sm6/400/300', 0, NOW()),
('Mango Passion Smoothie', 4.99, 'Smoothies', 'https://picsum.photos/seed/sm7/400/300', 0, NOW()),
('Peach Yogurt Smoothie', 4.79, 'Smoothies', 'https://picsum.photos/seed/sm8/400/300', 0, NOW()),
('Banana Oat Smoothie', 4.59, 'Smoothies', 'https://picsum.photos/seed/sm9/400/300', 0, NOW()),
('Blueberry Protein Smoothie', 5.99, 'Smoothies', 'https://picsum.photos/seed/sm10/400/300', 1, NOW()),
('Raspberry Chia Smoothie', 5.49, 'Smoothies', 'https://picsum.photos/seed/sm11/400/300', 0, NOW()),
('Kiwi Lime Smoothie', 4.89, 'Smoothies', 'https://picsum.photos/seed/sm12/400/300', 0, NOW()),
('Carrot Orange Smoothie', 5.29, 'Smoothies', 'https://picsum.photos/seed/sm13/400/300', 0, NOW()),
('Apple Cinnamon Smoothie', 4.99, 'Smoothies', 'https://picsum.photos/seed/sm14/400/300', 0, NOW()),
('Papaya Ginger Smoothie', 5.49, 'Smoothies', 'https://picsum.photos/seed/sm15/400/300', 0, NOW()),
('Dragonfruit Smoothie', 6.49, 'Smoothies', 'https://picsum.photos/seed/sm16/400/300', 1, NOW()),
('Watermelon Mint Smoothie', 4.79, 'Smoothies', 'https://picsum.photos/seed/sm17/400/300', 0, NOW()),
('Coconut Almond Smoothie', 5.19, 'Smoothies', 'https://picsum.photos/seed/sm18/400/300', 0, NOW()),
('Chocolate Banana Smoothie', 4.99, 'Smoothies', 'https://picsum.photos/seed/sm19/400/300', 0, NOW()),
('Honey Date Smoothie', 5.59, 'Smoothies', 'https://picsum.photos/seed/sm20/400/300', 0, NOW()),
('Pear Ginger Smoothie', 5.09, 'Smoothies', 'https://picsum.photos/seed/sm21/400/300', 0, NOW()),
('Fig Walnut Smoothie', 5.79, 'Smoothies', 'https://picsum.photos/seed/sm22/400/300', 0, NOW()),
('Cherry Vanilla Smoothie', 5.29, 'Smoothies', 'https://picsum.photos/seed/sm23/400/300', 0, NOW()),
('Pomegranate Smoothie', 5.89, 'Smoothies', 'https://picsum.photos/seed/sm24/400/300', 0, NOW()),
('Banana Peanut Smoothie', 5.39, 'Smoothies', 'https://picsum.photos/seed/sm25/400/300', 0, NOW()),
('Fresh Orange Juice', 3.99, 'Juices', 'https://picsum.photos/seed/ju1/400/300', 1, NOW()),
('Apple Carrot Juice', 4.29, 'Juices', 'https://picsum.photos/seed/ju2/400/300', 0, NOW()),
('Pineapple Juice', 3.79, 'Juices', 'https://picsum.photos/seed/ju3/400/300', 0, NOW()),
('Mango Juice', 4.49, 'Juices', 'https://picsum.photos/seed/ju4/400/300', 0, NOW()),
('Grapefruit Juice', 3.99, 'Juices', 'https://picsum.photos/seed/ju5/400/300', 0, NOW()),
('Lemonade', 2.99, 'Juices', 'https://picsum.photos/seed/ju6/400/300', 0, NOW()),
('Cucumber Mint Juice', 3.89, 'Juices', 'https://picsum.photos/seed/ju7/400/300', 0, NOW()),
('Carrot Beet Juice', 4.59, 'Juices', 'https://picsum.photos/seed/ju8/400/300', 0, NOW()),
('Pomegranate Juice', 4.99, 'Juices', 'https://picsum.photos/seed/ju9/400/300', 1, NOW()),
('Watermelon Juice', 3.69, 'Juices', 'https://picsum.photos/seed/ju10/400/300', 0, NOW()),
('Pumpkin Spice Smoothie', 6.49, 'Seasonal', 'https://picsum.photos/seed/ss1/400/300', 1, NOW()),
('Cranberry Apple Juice', 4.29, 'Seasonal', 'https://picsum.photos/seed/ss2/400/300', 0, NOW()),
('Winter Berry Smoothie', 5.79, 'Seasonal', 'https://picsum.photos/seed/ss3/400/300', 0, NOW()),
('Mango Lassi', 4.99, 'Seasonal', 'https://picsum.photos/seed/ss4/400/300', 1, NOW()),
('Guava Breeze', 4.39, 'Seasonal', 'https://picsum.photos/seed/ss5/400/300', 0, NOW());

