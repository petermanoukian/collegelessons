-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 23, 2026 at 04:11 PM
-- Server version: 8.4.3
-- PHP Version: 8.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `college`
--

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` tinyint UNSIGNED NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'N/A',
  `gender` enum('Male','Female') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Male',
  `membership` enum('Member','Full Member') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Member',
  `newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumb` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `username`, `first_name`, `last_name`, `age`, `email`, `nickname`, `gender`, `membership`, `newsletter`, `img`, `thumb`, `file`, `created_at`) VALUES
(1, 'user1', 'bedik', 'manoukian', 8, 'bedikmanoukian@gmail.com', 'N/A', 'Male', 'Full Member', 1, NULL, NULL, NULL, '2026-09-15 14:50:40'),
(2, 'user2', 'namer', 'updatrer', 25, 'bedikmanoukian@gmail.net', 'N/A', 'Male', 'Member', 1, 'uploads/student/img/1790086369_6ab28ce14b20c.jpg', 'uploads/student/img/thumb/1790086369_6ab28ce14b20c.jpg', 'uploads/student/file/1790086369_doc_6ab28ce152724.jpg', '2026-09-15 15:02:46'),
(3, 'user3', 'Peter', 'Manoukian', 7, 'thewizard_it@hotmail.com', 'N/A', 'Male', 'Member', 0, NULL, NULL, NULL, '2026-09-15 15:03:28'),
(4, 'user4', 'hgfhfg', 'hgfhf', 8, 'test@test.com', 'N/A', 'Female', 'Full Member', 0, NULL, NULL, NULL, '2026-09-15 15:03:52'),
(5, 'peterman', 'Bedros', 'Manoukian', 2, 'thewizard_it@hotmail.ws', 'nickname1', 'Male', 'Member', 1, NULL, NULL, NULL, '2026-09-16 15:37:20'),
(6, 'test', 'test', 'testlast', 7, 'test@test.net', 'nickname1', 'Female', 'Full Member', 1, NULL, NULL, NULL, '2026-09-16 15:38:14'),
(7, 'peterman2', 'Bedros', 'Manoukian', 4, 'thewizard_it@yahoo.com', 'nickname2', 'Female', 'Full Member', 1, 'uploads/student/img/1790086395_6ab28cfb9da9d.jpg', 'uploads/student/img/thumb/1790086395_6ab28cfb9da9d.jpg', 'uploads/student/file/1790086395_doc_6ab28cfba9def.docx', '2026-09-16 15:39:05'),
(8, 'pic1', 'Bedros', 'Manoukian', 11, 'thewizard_it@google.com', 'gogole222', 'Male', 'Member', 0, 'uploads/student/img/1790083837_6ab282fdf1b82.jpg', 'uploads/student/img/thumb/1790083837_6ab282fdf1b82.jpg', 'uploads/student/file/1790083838_doc_6ab282fe07c56.pdf', '2026-09-22 13:30:38'),
(10, 'piuc444', 'jkjhk', 'k', 22, 'thewizard_it@hotmail.me', 'ghggh', 'Male', 'Member', 1, 'uploads/student/img/1790172964_6ab3df24c6af9.jpg', 'uploads/student/img/thumb/1790172964_6ab3df24c6af9.jpg', 'uploads/student/file/1790172700_doc_6ab3de1c9f40d.jpg', '2026-09-23 14:11:40'),
(11, 'pic4', 'hgf', 'hhfg', 23, 'peter@peter.net', 'hghfgf', 'Male', 'Member', 0, 'uploads/student/img/1790172942_6ab3df0e1e859.jpg', 'uploads/student/img/thumb/1790172942_6ab3df0e1e859.jpg', 'uploads/student/file/1790172942_doc_6ab3df0e2401a.jpg', '2026-09-23 14:15:42'),
(12, 'pic6', 'jhjhh', 'hjh', 4, 'thewizard_it@hotmail.am', 'N/A', 'Male', 'Member', 0, NULL, NULL, NULL, '2026-09-23 14:39:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `last_name` (`last_name`),
  ADD KEY `first_name` (`first_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
