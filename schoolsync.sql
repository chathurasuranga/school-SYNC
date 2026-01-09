-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 01, 2026 at 03:18 AM
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
-- Database: `schoolsync`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `module_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `class_id`, `title`, `created_at`, `module_id`) VALUES
(1, 2, 'dfhgdfhdfh', '2025-12-26 11:02:20', NULL),
(2, 2, 'DCDC', '2025-12-27 13:35:33', 1),
(3, 2, '123', '2025-12-27 14:32:27', 1),
(4, 2, '3', '2025-12-27 16:35:28', 1),
(5, 2, 'සමන් ', '2025-12-28 13:17:04', 1);

-- --------------------------------------------------------

--
-- Table structure for table `class_enrollments`
--

CREATE TABLE `class_enrollments` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_enrollments`
--

INSERT INTO `class_enrollments` (`id`, `class_id`, `student_id`) VALUES
(1, 2, 1),
(2, 2, 2),
(3, 2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

CREATE TABLE `criteria` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `criteria_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `criteria`
--

INSERT INTO `criteria` (`id`, `assignment_id`, `criteria_name`) VALUES
(1, 1, 'fdghdfh'),
(2, 1, 'dfhdfhdf'),
(3, 1, 'dfhdfhh'),
(4, 2, 'C'),
(5, 2, 'ZCZC'),
(6, 3, 'gjfgj'),
(7, 3, 'fgjfgj'),
(8, 3, 'fgjfgjfgj'),
(9, 4, 'xbcxbcx'),
(10, 4, 'xcbxcbxcb'),
(11, 5, 'කමල්'),
(12, 5, 'නිමල්');

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `final_percentage` decimal(5,2) NOT NULL,
  `graded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`id`, `assignment_id`, `student_id`, `final_percentage`, `graded_at`) VALUES
(1, 1, 1, 66.67, '2025-12-27 14:06:25'),
(2, 1, 2, 91.67, '2025-12-27 14:06:30'),
(3, 3, 2, 100.00, '2025-12-27 14:32:38'),
(4, 3, 1, 100.00, '2025-12-27 16:36:58'),
(5, 2, 2, 100.00, '2025-12-27 16:36:26'),
(6, 2, 1, 100.00, '2025-12-27 16:36:25'),
(7, 4, 1, 75.00, '2025-12-27 16:37:51'),
(8, 4, 2, 100.00, '2025-12-27 16:35:41');

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `class_id`, `module_name`, `created_at`) VALUES
(1, 2, '2', '2025-12-27 13:35:23');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `grade` int(11) NOT NULL,
  `class_letter` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `grade`, `class_letter`) VALUES
(1, 'Alice Johnson', 6, 'A'),
(2, 'Bob Smith', 6, 'A'),
(3, 'Chathura', 6, 'B'),
(4, 'Liam Smith', 6, 'A'),
(5, 'Emma Johnson', 6, 'A'),
(6, 'Noah Williams', 6, 'B'),
(7, 'Olivia Brown', 6, 'B'),
(8, 'William Jones', 6, 'C'),
(9, 'Sophia Garcia', 6, 'C'),
(10, 'James Miller', 7, 'A'),
(11, 'Ava Davis', 7, 'A'),
(12, 'Oliver Rodriguez', 7, 'B'),
(13, 'Isabella Martinez', 7, 'B'),
(14, 'Benjamin Hernandez', 7, 'C'),
(15, 'Mia Lopez', 7, 'C'),
(16, 'Lucas Gonzalez', 8, 'A'),
(17, 'Charlotte Wilson', 8, 'A'),
(18, 'Mason Anderson', 8, 'B'),
(19, 'Amelia Thomas', 8, 'B'),
(20, 'Ethan Taylor', 8, 'C'),
(21, 'Evelyn Moore', 8, 'C'),
(22, 'Alexander Jackson', 9, 'A'),
(23, 'Abigail Martin', 9, 'A'),
(24, 'Henry Lee', 9, 'B'),
(25, 'Harper Perez', 9, 'B'),
(26, 'Sebastian Thompson', 9, 'C'),
(27, 'Emily White', 9, 'C'),
(28, 'Jack Harris', 6, 'D'),
(29, 'Ella Sanchez', 6, 'D'),
(30, 'Samuel Clark', 7, 'D'),
(31, 'Madison Ramirez', 7, 'D'),
(32, 'Daniel Lewis', 8, 'D'),
(33, 'Scarlett Robinson', 8, 'D'),
(34, 'Matthew Walker', 9, 'D'),
(35, 'Victoria Young', 9, 'D'),
(36, 'David Allen', 6, 'A'),
(37, 'Aria King', 6, 'B'),
(38, 'Joseph Wright', 7, 'A'),
(39, 'Grace Scott', 7, 'B'),
(40, 'Carter Torres', 8, 'A'),
(41, 'Chloe Nguyen', 8, 'B'),
(42, 'Owen Hill', 9, 'A'),
(43, 'Camila Flores', 9, 'B'),
(44, 'Wyatt Green', 6, 'C'),
(45, 'Penelope Adams', 6, 'D'),
(46, 'John Nelson', 7, 'C'),
(47, 'Layla Baker', 7, 'D'),
(48, 'Jack Hall', 8, 'C'),
(49, 'Lillian Rivera', 8, 'D'),
(50, 'Luke Campbell', 9, 'C'),
(51, 'Nora Mitchell', 9, 'D'),
(52, 'Isaac Carter', 6, 'A'),
(53, 'Zoey Roberts', 6, 'B'),
(54, 'Gabriel Gomez', 7, 'A'),
(55, 'Mila Phillips', 7, 'B'),
(56, 'Julian Evans', 8, 'A'),
(57, 'Bella Turner', 8, 'B'),
(58, 'Levi Diaz', 9, 'A'),
(59, 'Aurora Parker', 9, 'B'),
(60, 'Dylan Cruz', 6, 'C'),
(61, 'Lucy Edwards', 6, 'D'),
(62, 'Grayson Collins', 7, 'C'),
(63, 'Anna Reyes', 7, 'D'),
(64, 'Christopher Stewart', 8, 'C'),
(65, 'Hazel Morris', 8, 'D'),
(66, 'Joshua Morales', 9, 'C'),
(67, 'Aaliyah Murphy', 9, 'D'),
(68, 'Andrew Cook', 6, 'A'),
(69, 'Claire Rogers', 6, 'B'),
(70, 'Lincoln Gutierrez', 7, 'A'),
(71, 'Audrey Ortiz', 7, 'B'),
(72, 'Mateo Morgan', 8, 'A'),
(73, 'Savannah Cooper', 8, 'B'),
(74, 'Ryan Peterson', 9, 'A'),
(75, 'Brooklyn Bailey', 9, 'B'),
(76, 'Jaxon Reed', 6, 'C'),
(77, 'Leah Kelly', 6, 'D'),
(78, 'Nathan Howard', 7, 'C'),
(79, 'Zoe Ramos', 7, 'D'),
(80, 'Aaron Kim', 8, 'C'),
(81, 'Stella Cox', 8, 'D'),
(82, 'Isaiah Ward', 9, 'C'),
(83, 'Paisley Richardson', 9, 'D'),
(84, 'Thomas Watson', 6, 'A'),
(85, 'Skylar Brooks', 6, 'B'),
(86, 'Charles Chavez', 7, 'A'),
(87, 'Maya Wood', 7, 'B'),
(88, 'Caleb James', 8, 'A'),
(89, 'Genesis Bennett', 8, 'B'),
(90, 'Josiah Gray', 9, 'A'),
(91, 'Caroline Mendoza', 9, 'B'),
(92, 'Christian Ruiz', 6, 'C'),
(93, 'Kennedy Hughes', 6, 'D'),
(94, 'Hunter Price', 7, 'C'),
(95, 'Sadie Alvarez', 7, 'D'),
(96, 'Eli Castillo', 8, 'C'),
(97, 'Gabriela Sanders', 8, 'D'),
(98, 'Jonathan Patel', 9, 'C'),
(99, 'Alice Myers', 9, 'D'),
(100, 'Connor Long', 6, 'A'),
(101, 'Elena Ross', 6, 'B'),
(102, 'Landon Foster', 7, 'A'),
(103, 'Adeline Jiminez', 7, 'B');

-- --------------------------------------------------------

--
-- Table structure for table `student_criteria_marks`
--

CREATE TABLE `student_criteria_marks` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `criteria_id` int(11) NOT NULL,
  `score` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_criteria_marks`
--

INSERT INTO `student_criteria_marks` (`id`, `assignment_id`, `student_id`, `criteria_id`, `score`) VALUES
(1, 1, 1, 1, 2),
(2, 1, 1, 2, 3),
(3, 1, 1, 3, 3),
(4, 1, 2, 1, 3),
(5, 1, 2, 2, 4),
(6, 1, 2, 3, 4),
(7, 3, 2, 6, 4),
(8, 3, 2, 7, 4),
(9, 3, 2, 8, 4),
(19, 4, 2, 9, 4),
(20, 4, 2, 10, 4),
(21, 2, 1, 4, 4),
(22, 2, 1, 5, 4),
(23, 2, 2, 4, 4),
(24, 2, 2, 5, 4),
(25, 3, 1, 6, 4),
(26, 3, 1, 7, 4),
(27, 3, 1, 8, 4),
(28, 4, 1, 9, 3),
(29, 4, 1, 10, 3);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_classes`
--

CREATE TABLE `teacher_classes` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_classes`
--

INSERT INTO `teacher_classes` (`id`, `teacher_id`, `class_name`, `created_at`) VALUES
(2, 2, '6A', '2025-12-26 11:01:00'),
(3, 2, '6B', '2025-12-26 11:05:38'),
(4, 2, '6C', '2025-12-26 15:39:50'),
(5, 1, '6A', '2025-12-26 16:42:41'),
(6, 2, '6A', '2025-12-28 14:05:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher') DEFAULT 'teacher',
  `full_name` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `is_first_login` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `full_name`, `bio`, `is_first_login`, `created_at`, `profile_pic`, `phone_number`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System Admin', NULL, 0, '2025-12-26 10:35:10', NULL, NULL),
(2, 'geethani', '$2y$10$1rm5IfGocv0yBdiNYnxb3eQ8FaZ/6LnvUByvwGYjfjpHlQpAM1V02', 'teacher', 'Geethani Teacher', '', 0, '2025-12-26 10:35:10', 'profile_2_1766767268.jpeg', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `class_enrollments`
--
ALTER TABLE `class_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_criteria_marks`
--
ALTER TABLE `student_criteria_marks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_score` (`student_id`,`criteria_id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `criteria_id` (`criteria_id`);

--
-- Indexes for table `teacher_classes`
--
ALTER TABLE `teacher_classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `class_enrollments`
--
ALTER TABLE `class_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `student_criteria_marks`
--
ALTER TABLE `student_criteria_marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `teacher_classes`
--
ALTER TABLE `teacher_classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `teacher_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `class_enrollments`
--
ALTER TABLE `class_enrollments`
  ADD CONSTRAINT `class_enrollments_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `teacher_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_enrollments_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `criteria`
--
ALTER TABLE `criteria`
  ADD CONSTRAINT `criteria_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `marks`
--
ALTER TABLE `marks`
  ADD CONSTRAINT `marks_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `marks_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `teacher_classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_criteria_marks`
--
ALTER TABLE `student_criteria_marks`
  ADD CONSTRAINT `student_criteria_marks_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_criteria_marks_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_criteria_marks_ibfk_3` FOREIGN KEY (`criteria_id`) REFERENCES `criteria` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_classes`
--
ALTER TABLE `teacher_classes`
  ADD CONSTRAINT `teacher_classes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
