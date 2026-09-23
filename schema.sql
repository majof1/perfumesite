-- schema.sql - MySQL Database Initialization Script for Luxury Perfume Site

CREATE DATABASE IF NOT EXISTS `perfume_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `perfume_db`;

-- Table: Users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `role` ENUM('customer', 'admin') DEFAULT 'customer',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: Categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: Products
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT DEFAULT NULL,
  `name` VARCHAR(150) NOT NULL,
  `brand` VARCHAR(100) DEFAULT 'Élixir De Luxe',
  `description` TEXT NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `original_price` DECIMAL(10, 2) DEFAULT NULL,
  `volume` VARCHAR(30) DEFAULT '100ml Eau De Parfum',
  `gender` ENUM('Men', 'Women', 'Unisex') DEFAULT 'Unisex',
  `top_notes` VARCHAR(255) DEFAULT NULL,
  `heart_notes` VARCHAR(255) DEFAULT NULL,
  `base_notes` VARCHAR(255) DEFAULT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `stock` INT DEFAULT 50,
  `rating` DECIMAL(3, 2) DEFAULT 4.80,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_bestseller` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: Orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(30) NOT NULL UNIQUE,
  `user_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(100) NOT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `city` VARCHAR(50) NOT NULL,
  `state` VARCHAR(50) NOT NULL,
  `subtotal` DECIMAL(10, 2) NOT NULL,
  `shipping_fee` DECIMAL(10, 2) NOT NULL,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'Card / Paystack',
  `payment_status` ENUM('Pending', 'Paid', 'Failed') DEFAULT 'Paid',
  `order_status` ENUM('Processing', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Processing',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: Order Items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT DEFAULT NULL,
  `product_name` VARCHAR(150) NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `quantity` INT NOT NULL,
  `subtotal` DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Woody & Oriental', 'woody-oriental', 'Deep, rich, and mysterious fragrances infused with Oud, Amber, and Cedarwood.'),
(2, 'Floral & Velvet', 'floral-velvet', 'Sensual and luxurious florals crafted from May Rose, Jasmine, and Velvet Vanilla.'),
(3, 'Fresh & Citrus', 'fresh-citrus', 'Exhilarating aquatic breezes and vibrant Bergamot citrus blends.'),
(4, 'Gourmand & Spice', 'gourmand-spice', 'Decadent blends of Saffron, Dark Cocoa, and Warm Cinnamon.');

-- Seed Sample Products (All prices in Nigerian Naira ₦)
INSERT INTO `products` (`id`, `category_id`, `name`, `brand`, `description`, `price`, `original_price`, `volume`, `gender`, `top_notes`, `heart_notes`, `base_notes`, `image_url`, `stock`, `rating`, `is_featured`, `is_bestseller`) VALUES
(1, 1, 'Oud Imperial Royale', 'Maison De Luxe', 'An enigmatic masterwork combining rare Cambodian Oud with smoky amber resin and Bulgarian rose. Created for connoisseurs of timeless opulence.', 135000.00, 150000.00, '100ml Extrait de Parfum', 'Unisex', 'Bergamot, Pink Pepper', 'Cambodian Oud, Taif Rose', 'Smoky Amber, Vetiver, Leather', 'assets/images/perfume1.jpg', 35, 4.95, 1, 1),
(2, 2, 'Midnight Velvet Rose', 'Velvet Noir', 'An intoxicating nocturnal bouquet featuring black orchid, midnight iris, and smooth Madagascar vanilla bean with a hint of dark plum.', 95000.00, 110000.00, '100ml Eau de Parfum', 'Women', 'Black Plum, Damask Rose', 'Black Orchid, Iris, Jasmine', 'Madagascar Vanilla, Musk', 'assets/images/perfume2.jpg', 42, 4.88, 1, 1),
(3, 3, 'Sapphire Aqua Intense', 'Azure Luxe', 'An invigorating ocean breeze paired with Sicilian bergamot, salty sea minerals, and crisp white cedar. Refreshing, luminous, and bold.', 78000.00, 85000.00, '100ml Eau de Parfum', 'Men', 'Sicilian Bergamot, Sea Salt', 'Blue Mint, Lavender', 'White Cedarwood, Ambergris', 'assets/images/perfume3.jpg', 50, 4.79, 1, 0),
(4, 4, 'Crimson Saffron & Gold', 'Royale Scent', 'A fiery, addictive elixir of red saffron blossoms, warm cardamoms, and caramelized bourbon vanilla grounded in dark patchouli.', 115000.00, 130000.00, '100ml Extrait de Parfum', 'Unisex', 'Saffron, Italian Lemon', 'Cardamom, Cinnamon Bark', 'Bourbon Vanilla, Patchouli', 'assets/images/perfume4.jpg', 20, 4.92, 1, 1),
(5, 1, 'Noir Vetiver & Leather', 'Shadow Luxe', 'A sophisticated leather EDP for the modern gentleman, combining vetiver grass, smoky cypress, and refined suede notes.', 88000.00, 98000.00, '100ml Eau de Parfum', 'Men', 'Grapefruit, Cardamom', 'Cypress, Tuscan Leather', 'Haitian Vetiver, Tonka Bean', 'assets/images/perfume5.jpg', 28, 4.85, 0, 1),
(6, 2, 'Celestial Jasmine Blossom', 'Aura Parfums', 'Luminous white petals of Egyptian jasmine bathed in golden nectar, solar neroli, and sandalwood soft velvet base.', 72000.00, NULL, '100ml Eau de Parfum', 'Women', 'Solar Neroli, Mandora', 'Egyptian Jasmine, Orange Flower', 'White Sandalwood, Cashmere', 'assets/images/perfume6.jpg', 40, 4.80, 0, 0),
(7, 4, 'Amber Nectar Absolute', 'Elixir Privé', 'A velvety gourmand luxury scent infused with roasted tonka beans, liquid amber resin, and honeyed dates.', 105000.00, 120000.00, '100ml Extrait de Parfum', 'Unisex', 'Honeyed Dates, Nutmeg', 'Roasted Tonka, Benzoin', 'Golden Amber, Vanilla', 'assets/images/perfume7.jpg', 18, 4.90, 1, 0),
(8, 3, 'Azure Citrus Elixir', 'Azure Luxe', 'Bright Mediterranean yuzu citrus bursting over crushed mint leaves and clean musk. Electric freshness for everyday elegance.', 65000.00, 75000.00, '100ml Eau de Parfum', 'Unisex', 'Yuzu, Grapefruit, Mint', 'Green Tea, Water Lily', 'Clean Musk, Driftwood', 'assets/images/perfume8.jpg', 60, 4.75, 0, 0);
