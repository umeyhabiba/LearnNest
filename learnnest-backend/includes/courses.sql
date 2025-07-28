-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2025 at 08:52 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `learnnest`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `students` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `description`, `duration`, `students`, `status`, `price`, `updated_at`) VALUES
(1, 'MERN STACK', 'Web Development', '10 Hours', 10, 'Draft', 12.00, '2025-07-19 22:15:00'),
(12, 'Graphic Designing', 'This is a Graphic Designing Course.', '10', 100, 'Published', 12.00, NULL),
(13, 'Python Programming', 'Python code', '20', 20, 'Draft', 20.00, '2025-07-19 22:13:39'),
(15, 'Prompt Engineering', 'Prompting ', '2 hours', 10, 'Published', 5.00, '2025-07-19 22:13:20'),
(17, 'AI', 'Artificial Intelligence', '8', 28, 'Draft', 50.00, '2025-07-19 19:17:13'),
(18, 'AI/ML', 'Artificial Intelligence', '11', 20, 'Draft', 200.00, '2025-07-19 22:13:06'),
(20, 'AI/ML (2)', 'Machine Learning', '10', 10, 'Draft', 29.00, '2025-07-19 22:12:47'),
(21, 'IELTS', 'English Language', '20', 10, 'Published', 35.00, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
