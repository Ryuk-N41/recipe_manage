-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2025 at 07:58 PM
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
-- Database: `recipe_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cuisines`
--

CREATE TABLE `cuisines` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cuisines`
--

INSERT INTO `cuisines` (`id`, `name`) VALUES
(1, 'Italian'),
(2, 'Mexican'),
(3, 'Chinese'),
(4, 'Indian'),
(5, 'American'),
(6, 'French'),
(7, 'Japanese'),
(8, 'Thai');

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `unit` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `recipe_id`, `name`, `quantity`, `unit`) VALUES
(1, 1, 'Spaghetti', 400.00, 'grams'),
(2, 1, 'Eggs', 3.00, 'pieces'),
(3, 1, 'Parmesan Cheese', 100.00, 'grams'),
(4, 1, 'Pancetta', 150.00, 'grams'),
(5, 1, 'Black Pepper', 2.00, 'teaspoons'),
(6, 1, 'Salt', 1.00, 'teaspoons'),
(7, 2, 'Broccoli', 200.00, 'grams'),
(8, 2, 'Carrots', 2.00, 'pieces'),
(9, 2, 'Bell Peppers', 2.00, 'pieces'),
(10, 2, 'Snow Peas', 100.00, 'grams'),
(11, 2, 'Garlic', 3.00, 'cloves'),
(12, 2, 'Soy Sauce', 3.00, 'tablespoons'),
(13, 2, 'Vegetable Oil', 2.00, 'tablespoons'),
(14, 3, 'All-Purpose Flour', 2.50, 'cups'),
(15, 3, 'Butter', 1.00, 'cup'),
(16, 3, 'Brown Sugar', 1.00, 'cup'),
(17, 3, 'White Sugar', 0.50, 'cup'),
(18, 3, 'Eggs', 2.00, 'pieces'),
(19, 3, 'Chocolate Chips', 2.00, 'cups'),
(20, 3, 'Vanilla Extract', 2.00, 'teaspoons'),
(21, 3, 'Baking Soda', 1.00, 'teaspoons'),
(22, 4, 'Chicken', 500.00, 'grams'),
(23, 4, 'Onion', 2.00, 'pieces'),
(24, 4, 'Tomatoes', 3.00, 'pieces'),
(25, 4, 'Ginger', 1.00, 'tablespoons'),
(26, 4, 'Garlic', 4.00, 'cloves'),
(27, 4, 'Curry Powder', 2.00, 'tablespoons'),
(28, 4, 'Oil', 3.00, 'tablespoons'),
(29, 4, 'Salt', 1.00, 'teaspoons'),
(30, 5, 'Romaine Lettuce', 1.00, 'head'),
(31, 5, 'Croutons', 1.00, 'cup'),
(32, 5, 'Parmesan Cheese', 0.50, 'cup'),
(33, 5, 'Mayonnaise', 3.00, 'tablespoons'),
(34, 5, 'Lemon Juice', 2.00, 'tablespoons'),
(35, 5, 'Garlic', 2.00, 'cloves');

-- --------------------------------------------------------

--
-- Table structure for table `meal_types`
--

CREATE TABLE `meal_types` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meal_types`
--

INSERT INTO `meal_types` (`id`, `name`) VALUES
(1, 'Breakfast'),
(2, 'Lunch'),
(3, 'Dinner'),
(4, 'Dessert'),
(5, 'Snack'),
(6, 'Appetizer'),
(7, 'Side Dish'),
(8, 'Soup'),
(9, 'Salad');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` decimal(2,1) NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `recipe_id`, `user_id`, `rating`, `review`, `created_at`) VALUES
(1, 1, 2, 4.5, 'Excellent recipe! Very authentic taste.', '2025-12-17 18:53:37'),
(2, 1, 3, 5.0, 'My family loved it! Will make again.', '2025-12-17 18:53:37'),
(3, 2, 2, 4.0, 'Healthy and delicious.', '2025-12-17 18:53:37'),
(4, 3, 3, 4.5, 'Best cookies I have ever made!', '2025-12-17 18:53:37'),
(5, 4, 2, 5.0, 'Perfect spice level.', '2025-12-17 18:53:37'),
(6, 5, 3, 3.5, 'Good but needed more dressing.', '2025-12-17 18:53:37');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `details` text NOT NULL,
  `servings` int(11) NOT NULL,
  `cuisine_id` int(11) DEFAULT NULL,
  `meal_type_id` int(11) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `title`, `description`, `details`, `servings`, `cuisine_id`, `meal_type_id`, `image`, `created_at`) VALUES
(1, 'Spaghetti Carbonara', 'Classic Italian pasta with eggs and pancetta', '1. Cook spaghetti al dente\r\n2. In a bowl, whisk eggs with grated cheese\r\n3. Cook pancetta until crispy\r\n4. Mix everything together off heat\r\n5. Season with black pepper\r\n6. Serve immediately', 4, 1, 3, NULL, '2025-12-17 18:53:36'),
(2, 'Vegetable Stir Fry', 'Quick and healthy Asian-style vegetables', '1. Heat oil in wok\r\n2. Add garlic and ginger\r\n3. Add carrots and broccoli first\r\n4. Add bell peppers and snow peas\r\n5. Add sauce (soy + oyster sauce)\r\n6. Stir fry for 3-4 minutes\r\n7. Serve with rice', 2, 3, 3, NULL, '2025-12-17 18:53:36'),
(3, 'Chocolate Chip Cookies', 'Soft and chewy homemade cookies', '1. Preheat oven to 350°F\r\n2. Cream butter and sugars\r\n3. Add eggs and vanilla\r\n4. Mix dry ingredients separately\r\n5. Combine wet and dry ingredients\r\n6. Fold in chocolate chips\r\n7. Bake for 10-12 minutes\r\n8. Cool on wire rack', 24, 5, 4, NULL, '2025-12-17 18:53:36'),
(4, 'Chicken Curry', 'Spicy Indian chicken curry', '1. Heat oil, add onions\r\n2. Add ginger-garlic paste\r\n3. Add chicken pieces\r\n4. Add curry powder and spices\r\n5. Add tomatoes and water\r\n6. Simmer for 20 minutes\r\n7. Add cream or yogurt\r\n8. Garnish with cilantro', 4, 4, 3, NULL, '2025-12-17 18:53:36'),
(5, 'Caesar Salad', 'Classic salad with creamy dressing', '1. Wash and chop romaine lettuce\r\n2. Make dressing: mayo, garlic, anchovy, lemon\r\n3. Add croutons\r\n4. Add parmesan cheese\r\n5. Toss everything together\r\n6. Serve immediately', 2, 5, 9, NULL, '2025-12-17 18:53:36');

-- --------------------------------------------------------

--
-- Table structure for table `timers`
--

CREATE TABLE `timers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in seconds',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timers`
--

INSERT INTO `timers` (`id`, `user_id`, `recipe_id`, `label`, `duration`, `created_at`) VALUES
(1, 2, 1, 'Pasta Cooking', 600, '2025-12-17 18:53:37'),
(2, 2, 1, 'Sauce Preparation', 300, '2025-12-17 18:53:37'),
(3, 3, 3, 'Cookie Baking', 720, '2025-12-17 18:53:37'),
(4, 3, 4, 'Curry Simmering', 1200, '2025-12-17 18:53:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-12-17 18:53:36'),
(2, 'john', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '2025-12-17 18:53:36'),
(3, 'sarah', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '2025-12-17 18:53:36'),
(4, 'Rakin', 'abc@gmail.com', '$2y$10$S5P9fYNl4SuChfhUOqwy4efpSFKKsLoaBaV.94pFwLr3jQu977RqW', 'user', '2025-12-17 18:54:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cuisines`
--
ALTER TABLE `cuisines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ingredients_recipe` (`recipe_id`);

--
-- Indexes for table `meal_types`
--
ALTER TABLE `meal_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rating` (`recipe_id`,`user_id`),
  ADD KEY `idx_ratings_recipe` (`recipe_id`),
  ADD KEY `idx_ratings_user` (`user_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recipes_title` (`title`),
  ADD KEY `idx_recipes_cuisine` (`cuisine_id`),
  ADD KEY `idx_recipes_meal_type` (`meal_type_id`);

--
-- Indexes for table `timers`
--
ALTER TABLE `timers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `idx_timers_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cuisines`
--
ALTER TABLE `cuisines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `meal_types`
--
ALTER TABLE `meal_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `timers`
--
ALTER TABLE `timers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`cuisine_id`) REFERENCES `cuisines` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`meal_type_id`) REFERENCES `meal_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `timers`
--
ALTER TABLE `timers`
  ADD CONSTRAINT `timers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `timers_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
