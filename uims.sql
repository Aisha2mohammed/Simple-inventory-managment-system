
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 20, 2025 at 06:38 AM
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
-- Database: `uims`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrow_requests`
--

CREATE TABLE `borrow_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `borrow_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `return_date` timestamp NOT NULL DEFAULT (current_timestamp() + interval 6 month),
  `status` enum('pending','approved','rejected','returned') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_requests`
--

INSERT INTO `borrow_requests` (`request_id`, `user_id`, `item_id`, `quantity`, `borrow_date`, `return_date`, `status`) VALUES
(5, 7, '2', 5, '2025-01-19 20:15:37', '2025-07-19 20:15:37', 'approved'),
(6, 7, '1', 4, '2025-01-19 21:12:52', '2025-07-19 21:12:52', 'approved'),
(7, 7, '3', 6, '2025-01-19 21:19:34', '2025-07-19 21:19:34', 'rejected'),
(8, 7, '3', 3, '2025-01-19 21:33:51', '2025-07-19 21:33:51', 'pending'),
(9, 7, '4', 5, '2025-01-19 21:47:52', '2025-07-19 21:47:52', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `item_id` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `subcategory` varchar(255) NOT NULL,
  `material` varchar(255) NOT NULL,
  `condition` enum('new','old') NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('available','borrowed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`item_id`, `category`, `subcategory`, `material`, `condition`, `quantity`, `status`, `created_at`) VALUES
('1', 'renewable', 'Electronics', 'Desktop', 'new', 2, 'available', '2025-01-19 06:16:57'),
('2', 'renewable', 'Furniture', 'Chair', 'new', 2, 'available', '2025-01-19 06:33:39'),
('3', 'renewable', 'Furniture', 'Table', 'new', 11, 'available', '2025-01-19 06:36:52'),
('4', 'non-renewable', 'Metals', 'Aluminum', 'new', 3, 'available', '2025-01-19 07:13:00'),
('5', 'renewable', 'Solar Panels', 'Polycrystalline', 'new', 2, 'available', '2025-01-19 13:48:24'),
('6', 'non-renewable', 'Chemicals', 'Solvent', 'new', 7, 'available', '2025-01-19 22:04:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','store_manager','store_keeper','department_head','inventory_employee') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'Aisha ', 'aisha@gmail.com', '$2y$10$m.OHpFBrsZVDHHTaI/a.9uGRk30hQry2gGRhrN8enVeBgyMe3qyYa', 'admin', '2025-01-15 05:42:39'),
(3, 'amir', 'amir@gmail.com', '$2y$10$VIFA0MQyipUxBdSZUyHGduTQ4AlSD8GJuH0HOCSL4iaCsj1Cp4EjO', 'department_head', '2025-01-15 05:44:12'),
(6, 'sara', 'sara@gmail.com', '$2y$10$6NL2UqqGrGCD7kk68WRXwu17YDoj9P8iSiyFk.MIhAFckP0Rj2Gxi', 'store_manager', '2025-01-15 05:47:24'),
(7, 'guru', 'guru@gmail.com', '$2y$10$AYwWbZqcAmYMgT0viodbS.Kx0J6UP0TGSY2sjU0TrJAxEBKU0AWDO', 'store_keeper', '2025-01-15 05:48:04'),
(8, 'ali', 'ali@gmail.com', '$2y$10$Hial6MPPBSGWVLMichMzaOybVF0nV2FdlXm.5hTElFMQc7X1OXFT2', 'department_head', '2025-01-15 05:49:07'),
(9, 'pop', 'pop@gmail.com', '$2y$10$Q47Mztbwav60LrBq6WfFrOEpWBgR69I59qWwmWSrdhlOfu.9dRs.O', 'inventory_employee', '2025-01-15 05:49:46'),
(18, 'munu', 'munu@gmail.com', '$2y$10$Q55Ju/NjvMmNB2AG./T.J.agAzPoN8JRwveAxvF81YG/H8mvnmyYO', 'store_manager', '2025-01-15 21:16:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow_requests`
--
ALTER TABLE `borrow_requests`
  ADD CONSTRAINT `borrow_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `borrow_requests_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`item_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;