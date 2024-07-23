-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2024 at 10:30 PM
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
-- Database: `all php`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `family_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 2,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `family_name`, `email`, `password`, `phone_number`, `role_id`, `is_admin`, `profile_image`) VALUES
(1, 'a', 'a', 'a', 'a', 'yazan@live.com', '$2y$10$K2w649tED5qkewyJYqXFqubhidJbeXtjYhEhwQ/SJtc6umeJYYPWS', '0798525908', 2, 0, 'llllll.PNG'),
(4, 'q', 'q', 'q', 'q', 'q@q.com', '$2y$10$PznF52ekA6s9UEFey1YDW.D9MSLGsElMkbIaMk.37rCQPAgs99fF2', '0798525908', 2, 0, 'llllll.PNG'),
(10, 'yazan', 'haitham', 'younis', 'abo sbeah', 'yazan@haitham.com', '$2y$10$Hj/66OJvw1vsZ/oOC1Ahz.0KPW4kOU0anue1roMUSWevOeIGErYzG', '0798525908', 1, 0, 'WhatsApp_Image_2024-05-05_at_10.18.38_AM.jpeg'),
(11, 'b', 'b', 'b', 'b', 'b@b.com', '$2y$10$NZ3ZtGFBwmcT.zw8MO48EOrcA1bbbRl5tyg1UuHrSxBzieg/.gXZa', '0798525908', 2, 0, 'dddd.PNG');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
