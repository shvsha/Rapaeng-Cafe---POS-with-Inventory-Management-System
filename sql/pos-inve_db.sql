-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2025 at 02:17 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos-inve_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `full_name` text NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `full_name`, `username`, `password`) VALUES
(1, 'Aina Sekairi', 'ainahatdog', '1234567');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `customer_id`, `menu_id`, `quantity`, `added_at`) VALUES
(10, 1, 3, 1, '2025-12-04 11:39:23');

-- --------------------------------------------------------

--
-- Table structure for table `cashier`
--

CREATE TABLE `cashier` (
  `cashier_id` int(11) NOT NULL,
  `full_name` text NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cashier`
--

INSERT INTO `cashier` (`cashier_id`, `full_name`, `username`, `password`) VALUES
(1, 'Neil Monje', 'Saleiri', 'monje123');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `name`) VALUES
(1, 'Coffee'),
(2, 'Non Coffee'),
(3, 'Snacks'),
(4, 'Light Bites');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `full_name` text NOT NULL,
  `email` text NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `phone_number` varchar(30) NOT NULL DEFAULT '',
  `gender` enum('male','female','other') NOT NULL DEFAULT 'other',
  `date_of_birth` date NOT NULL DEFAULT '1970-01-01',
  `last_username_change` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `full_name`, `email`, `username`, `password`, `phone_number`, `gender`, `date_of_birth`, `last_username_change`) VALUES
(1, 'Ivan Raphaelle Nigos', 'nigosivan@yahoo.com', 'shasha', 'ivannigos', '09456079385', 'male', '2005-06-01', '2025-12-03 20:55:26'),
(2, 'Enoch Karsten Aguinaldo', 'enoch@example.com', 'kars10', 'enoch123', '09154436020', 'male', '2015-03-07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_addresses`
--

CREATE TABLE `customer_addresses` (
  `address_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL DEFAULT '',
  `address_line` text NOT NULL,
  `city` text NOT NULL,
  `province` text NOT NULL,
  `postal_code` varchar(20) NOT NULL DEFAULT '',
  `phone_number` varchar(30) NOT NULL DEFAULT '',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_addresses`
--

INSERT INTO `customer_addresses` (`address_id`, `customer_id`, `label`, `address_line`, `city`, `province`, `postal_code`, `phone_number`, `is_default`, `created_at`) VALUES
(1, 1, 'Address', 'Saint Louis Ville, Talogtog', 'San Juan', 'La Union', '2514', '', 0, '2025-12-03 13:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `kitchen_orders`
--

CREATE TABLE `kitchen_orders` (
  `id` int(11) NOT NULL,
  `order_ref` varchar(80) NOT NULL,
  `table_name` varchar(60) DEFAULT '',
  `customer_name` varchar(120) DEFAULT '',
  `items` text NOT NULL,
  `status` char(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kitchen_orders`
--

INSERT INTO `kitchen_orders` (`id`, `order_ref`, `table_name`, `customer_name`, `items`, `status`, `created_at`, `updated_at`) VALUES
(17, 'K-176476915518', 'B12', 'Muadz', '[{\"menu_id\":\"3\",\"name\":\"Cappucino\",\"price\":28,\"quantity\":1}]', '1', '2025-12-03 21:39:15', '2025-12-03 21:39:15'),
(18, 'K-176476918016', 'B12', 'Muadz', '[{\"menu_id\":\"3\",\"name\":\"Cappucino\",\"price\":28,\"quantity\":1},{\"menu_id\":\"2\",\"name\":\"Espresso\",\"price\":27,\"quantity\":2}]', '2', '2025-12-03 21:39:40', '2025-12-03 21:40:44');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `price` decimal(50,0) NOT NULL,
  `description` text NOT NULL,
  `images` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menu_id`, `name`, `price`, `description`, `images`, `category_id`) VALUES
(1, 'Americano', 27, 'Classic espresso shot with hot water, highlighting bold, smooth flavors.', 'americano-pic.jpg', 1),
(2, 'Espresso', 27, 'Rich, concentrated espresso served in its purest form for a strong pick-me-up.', 'espresso-pic.jpg', 1),
(3, 'Cappucino', 28, 'Espresso topped with velvety steamed milk and a light layer of froth.', 'cappucino-pic.jpg', 1),
(4, 'Latte', 28, 'Creamy steamed milk blended with smooth espresso for a comforting treat.', 'latte-pic.jpg', 1),
(5, 'Macchiato', 22, 'Espresso “stained” with a touch of foamed milk for a subtle, balanced flavor.', 'macchiato-pic.jpg', 1),
(6, 'Black Coffee', 22, 'Freshly brewed coffee with a bold, full-bodied taste, served straight up.', 'black-coffee-pic.jpg', 1),
(7, 'Chocolate', 20, 'Rich, velvety chocolate drink, smooth and indulgent.', 'chocolate-pic.jpg', 2),
(8, 'Green Tea Latte', 20, 'Frothy milk infused with earthy green tea for a soothing treat.', 'green-tea-latte.jpg', 2),
(9, 'Milk Tea', 20, 'Classic milk tea with a perfect balance of creamy and robust tea flavors.', 'milk-tea.jpg', 2),
(10, 'Matcha', 22, 'Premium matcha blended with water for a pure, vibrant green tea experience.', 'matcha.jpg', 2),
(11, 'Matcha Latte', 22, 'Creamy milk combined with finely whisked matcha for a smooth, earthy flavor.', 'matcha-latte.jpg', 2),
(12, 'Vanilla Latte', 22, 'Espresso and steamed milk enhanced with delicate vanilla notes.', 'vanilla-latte.jpg', 2),
(13, 'Burger', 20, 'Juicy patty on a fresh bun with crisp greens, tomato, cucumber, and tangy vinaigrette.', 'burger.jpg', 3),
(14, 'French Fries', 20, 'Golden, crispy fries lightly seasoned to perfection.', 'french-fries.jpg', 3),
(15, 'Oat Meal', 20, 'Hearty oats with fresh fruits, nuts, and a touch of honey for a wholesome start.', 'oat-meal.jpg', 3),
(16, 'Cookies', 20, 'Freshly baked, soft, and chewy cookies with a perfect balance of sweetness.', 'cookies.png', 3),
(17, 'Macarons', 20, 'Colorful, delicate French macarons with creamy, flavorful fillings.', 'macarons.jpg', 3),
(18, 'Hot Macarons', 20, 'Warm, freshly baked macarons with melt-in-your-mouth centers.', 'hot-macarons.jpg', 3),
(19, 'Avocado Toast', 20, 'Crispy bread topped with ripe avocado, fresh greens, and a drizzle of aioli.', 'avocado-toast.jpg', 4),
(20, 'Bagels', 20, 'Soft bagel with savory fillings like cheese, meats, and fresh vegetables.', 'bagels.jpg', 4),
(21, 'Yogurt', 20, 'Creamy yogurt paired with fresh fruit and house-made toppings.', 'yogurt.jpg', 4),
(22, 'Yogurt Parfait', 22, 'Layered yogurt, granola, and seasonal fruits for a refreshing treat.', 'yougrt-parfait.jpg', 4),
(23, 'Sandwiches', 22, 'Artisan bread filled with premium meats, cheese, and fresh greens.', 'sandwiches.jpg', 4),
(24, 'Hot Dog', 22, 'Classic hot dog served with gourmet toppings and house-made sauces.', 'hot-dog.jpg', 4);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `total_amount` int(11) NOT NULL,
  `create_at` date NOT NULL,
  `status` char(1) NOT NULL DEFAULT '1',
  `delivery_eta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `total_amount`, `create_at`, `status`, `delivery_eta`) VALUES
(1, 1, 176, '2025-11-27', '1', NULL),
(2, 1, 24, '2025-11-27', '1', NULL),
(3, 1, 119, '2025-12-03', '3', '2025-12-03 21:20:13'),
(4, 1, 59, '2025-12-03', '3', '2025-12-03 21:58:13'),
(5, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:12'),
(6, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:07'),
(7, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:08'),
(8, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:09'),
(9, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:10'),
(10, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:11'),
(11, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:03'),
(12, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:04'),
(13, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:05'),
(14, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:05'),
(15, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:06'),
(16, 1, 31, '2025-12-03', '3', '2025-12-03 21:57:56'),
(17, 1, 31, '2025-12-03', '3', '2025-12-03 21:57:59'),
(18, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:00'),
(19, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:01'),
(20, 1, 31, '2025-12-03', '3', '2025-12-03 21:58:02'),
(21, 1, 31, '2025-12-03', '3', '2025-12-03 21:57:54');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `quantity`, `price`) VALUES
(1, 1, 16, 4, 20),
(2, 1, 8, 1, 20),
(3, 1, 9, 3, 20),
(4, 2, 11, 1, 22),
(5, 3, 16, 3, 20),
(6, 3, 19, 1, 20),
(7, 3, 4, 1, 28),
(8, 4, 2, 2, 27),
(9, 5, 3, 1, 28),
(10, 6, 3, 1, 28),
(11, 7, 3, 1, 28),
(12, 8, 3, 1, 28),
(13, 9, 3, 1, 28),
(14, 10, 3, 1, 28),
(15, 11, 3, 1, 28),
(16, 12, 3, 1, 28),
(17, 13, 3, 1, 28),
(18, 14, 3, 1, 28),
(19, 15, 3, 1, 28),
(20, 16, 3, 1, 28),
(21, 17, 3, 1, 28),
(22, 18, 3, 1, 28),
(23, 19, 3, 1, 28),
(24, 20, 3, 1, 28),
(25, 21, 3, 1, 28);

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `stock_id` int(11) NOT NULL,
  `item_name` text NOT NULL,
  `stock_images` varchar(255) NOT NULL,
  `total_stocks` int(11) NOT NULL,
  `stock_avai` int(11) NOT NULL,
  `item_price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`stock_id`, `item_name`, `stock_images`, `total_stocks`, `stock_avai`, `item_price`) VALUES
(1, 'Espresso beans', 'espresso-beans.jpg', 229, 95, 550.00),
(2, 'Coffee grounds', 'coffee-grounds.jpg', 494, 103, 300.00),
(3, 'Water', 'water.jpg', 419, 54, 20.00),
(4, 'Fresh milk', 'fresh-milk.jpeg', 207, 150, 90.00),
(5, 'Milk foam', 'milk-foam.jpg', 475, 108, 70.00),
(6, 'Cocoa powder', 'cocoa-powder.jpg', 241, 147, 120.00),
(7, 'Sugar', 'sugar.jpg', 328, 72, 40.00),
(8, 'Chocolate syrup', 'chocolate-syrup.jpg', 449, 99, 150.00),
(9, 'Matcha powder', 'matcha-powder.jpg', 483, 75, 180.00),
(10, 'Black tea leaves', 'black-tea.jpg', 326, 85, 90.00),
(11, 'Creamer', 'creamer.png', 345, 87, 80.00),
(12, 'Vanilla syrup', 'vanilla-syrup.png', 321, 141, 130.00),
(13, 'Burger buns', 'burger-buns.jpg', 291, 132, 55.00),
(14, 'Beef patty', 'beef-patty.jpg', 248, 85, 300.00),
(15, 'Chicken patty', 'chicken-patty.jpg', 290, 94, 250.00),
(16, 'Lettuce', 'lettuce.jpg', 283, 57, 35.00),
(17, 'Tomato', 'tomato.jpg', 362, 98, 30.00),
(18, 'Cheese', 'cheese.jpg', 430, 90, 45.00),
(19, 'Mayo', 'mayo.jpg', 415, 87, 25.00),
(20, 'Ketchup', 'ketchup.jpg', 417, 99, 25.00),
(21, 'Sandwich bread', 'sandwich-bread.jpg', 278, 135, 50.00),
(22, 'Potatoes', 'potatoes.jpg', 339, 127, 120.00),
(23, 'Cooking oil', 'cooking-oil.jpg', 330, 137, 200.00),
(24, 'Oats', 'oats.jpg', 214, 114, 95.00),
(25, 'Fruits', 'fruits.jpg', 207, 72, 150.00),
(26, 'Honey', 'honey.jpg', 212, 103, 160.00),
(27, 'Flour', 'flour.jpg', 364, 62, 70.00),
(28, 'Butter', 'butter.jpg', 498, 108, 120.00),
(29, 'Eggs', 'eggs.jpg', 473, 131, 12.00),
(30, 'Chocolate chips', 'chocolate-chips.jpeg', 300, 73, 200.00),
(31, 'Almond flour', 'almond-flour.jpg', 251, 65, 180.00),
(32, 'Food coloring', 'food-coloring.jpg', 275, 130, 100.00),
(33, 'Ganache filling', 'ganache.jpg', 270, 128, 250.00),
(34, 'Avocado', 'avocado.jpg', 256, 111, 60.00),
(35, 'Salt', 'salt.jpg', 346, 111, 20.00),
(36, 'Pepper', 'pepper.jpg', 367, 148, 20.00),
(37, 'Olive oil', 'olive-oil.jpg', 262, 60, 220.00),
(38, 'Bagels', 'bagels.jpg', 475, 75, 70.00),
(39, 'Cream cheese', 'cream-cheese.jpg', 353, 129, 90.00),
(40, 'Yogurt', 'yogurt.jpg', 330, 129, 60.00),
(41, 'Granola', 'granola.jpg', 394, 136, 110.00),
(42, 'Bread', 'bread.jpg', 306, 69, 45.00),
(43, 'Hot dog sausage', 'hotdog-sausage.jpg', 476, 51, 90.00),
(44, 'Hot dog bun', 'hotdog-bun.jpg', 289, 95, 40.00),
(45, 'Mustard', 'mustard.jpg', 309, 96, 30.00),
(46, 'Onions', 'onions.jpg', 267, 123, 25.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock_orders`
--

CREATE TABLE `stock_orders` (
  `order_id` int(11) NOT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `subtotal` double DEFAULT 0,
  `tax` double DEFAULT 0,
  `total` double DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_orders`
--

INSERT INTO `stock_orders` (`order_id`, `processed_by`, `subtotal`, `tax`, `total`, `created_at`) VALUES
(1, 1, 0, 0, 0, '2025-11-30 20:24:28'),
(2, 1, 0, 0, 0, '2025-11-30 20:24:30'),
(3, 1, 240, 24, 264, '2025-11-30 21:09:03'),
(4, 1, 240, 24, 264, '2025-11-30 21:09:09'),
(5, 1, 1630, 163, 1793, '2025-11-30 21:20:32'),
(6, 1, 1500, 150, 1650, '2025-11-30 21:27:34'),
(7, 1, 600, 60, 660, '2025-11-30 21:27:58'),
(8, 1, 1510, 151, 1661, '2025-11-30 21:28:08'),
(9, 1, 1150, 115, 1265, '2025-11-30 21:31:06'),
(10, 1, 90, 9, 99, '2025-12-04 19:50:33'),
(11, 1, 550, 55, 605, '2025-12-04 20:48:04'),
(12, 1, 300, 30, 330, '2025-12-04 20:51:43'),
(13, 1, 550, 55, 605, '2025-12-04 20:54:50'),
(14, 1, 2220, 222, 2442, '2025-12-04 20:55:01'),
(15, 1, 120, 12, 132, '2025-12-04 21:16:22');

-- --------------------------------------------------------

--
-- Table structure for table `stock_order_items`
--

CREATE TABLE `stock_order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` double DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_order_items`
--

INSERT INTO `stock_order_items` (`id`, `order_id`, `stock_id`, `quantity`, `unit_price`) VALUES
(1, 1, 2, 3, 0),
(2, 2, 3, 2, 0),
(3, 3, 6, 2, 120),
(4, 4, 6, 2, 120),
(5, 5, 2, 2, 300),
(6, 5, 1, 1, 550),
(7, 5, 4, 2, 90),
(8, 5, 3, 3, 20),
(9, 5, 6, 2, 120),
(10, 6, 22, 1, 120),
(11, 6, 21, 4, 50),
(12, 6, 32, 10, 100),
(13, 6, 31, 1, 180),
(14, 7, 2, 2, 300),
(15, 8, 1, 2, 550),
(16, 8, 2, 1, 300),
(17, 8, 3, 1, 20),
(18, 8, 4, 1, 90),
(19, 9, 2, 2, 300),
(20, 9, 1, 1, 550),
(21, 10, 10, 1, 90),
(22, 11, 1, 1, 550),
(23, 12, 2, 1, 300),
(24, 13, 1, 1, 550),
(25, 14, 2, 2, 300),
(26, 14, 1, 2, 550),
(27, 14, 3, 2, 20),
(28, 14, 6, 4, 120),
(29, 15, 6, 1, 120);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `cashier`
--
ALTER TABLE `cashier`
  ADD PRIMARY KEY (`cashier_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `kitchen_orders`
--
ALTER TABLE `kitchen_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`stock_id`);

--
-- Indexes for table `stock_orders`
--
ALTER TABLE `stock_orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `stock_order_items`
--
ALTER TABLE `stock_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cashier`
--
ALTER TABLE `cashier`
  MODIFY `cashier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kitchen_orders`
--
ALTER TABLE `kitchen_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `stock_orders`
--
ALTER TABLE `stock_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `stock_order_items`
--
ALTER TABLE `stock_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD CONSTRAINT `customer_addresses_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stock_order_items`
--
ALTER TABLE `stock_order_items`
  ADD CONSTRAINT `stock_order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `stock_orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
