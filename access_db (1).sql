-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2025 at 08:33 PM
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
-- Database: `access_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `priority` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL,
  `posted_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `category`, `content`, `start_date`, `end_date`, `priority`, `status`, `posted_by`, `created_at`, `updated_at`) VALUES
(1, 'hahahahah', 'Club Notice', 'ihidoiisadcuiudiasduasdidioassaoioasioasiosad', '2025-05-13', '2025-05-14', 'Normal', 'Active', 1, '2025-05-11 04:56:30', '2025-05-11 04:56:30'),
(2, 'hsdhhadhdh', 'Academic', 'dadasdasdasdasd', '2025-05-14', '2025-05-14', 'Normal', 'Active', 1, '2025-05-11 04:57:23', '2025-05-11 04:57:23'),
(3, 'ELECTION 2025', 'Other', ' VOTE WISELY', '2025-05-12', '2025-05-12', 'High', 'Active', 2, '2025-05-12 07:38:10', '2025-05-12 07:38:10');

-- --------------------------------------------------------

--
-- Table structure for table `announcement_attachments`
--

CREATE TABLE `announcement_attachments` (
  `id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcement_attachments`
--

INSERT INTO `announcement_attachments` (`id`, `announcement_id`, `file_name`, `file_path`, `file_type`, `file_size`, `uploaded_at`) VALUES
(1, 1, 'bg.jpg', 'uploads/announcements/68202dfe0d41c_bg.jpg', 'image/jpeg', 13594, '2025-05-11 04:56:30'),
(2, 2, 'JASONBARS.png', 'uploads/announcements/68202e3316e1a_JASONBARS.png', 'image/png', 110023, '2025-05-11 04:57:23'),
(3, 3, '3.png', 'uploads/announcements/6821a562c9450_3.png', 'image/png', 180793, '2025-05-12 07:38:10');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `category`, `content`, `image_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 'fdfbb', 'healthcare', 'fdbdfbd', 'uploads/68205b488c4f7.jpg', 'published', '2025-05-11 08:09:44', '2025-05-11 08:09:55'),
(2, 'tyjtyjtyj', 'yjytjy', 'tyjtyjtyj', 'uploads/6820624b229f5.jpg', 'published', '2025-05-11 08:39:39', '2025-05-11 08:39:39');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `requester_name` varchar(255) NOT NULL,
  `service_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `feedback_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `requester_name`, `service_id`, `rating`, `feedback_text`, `created_at`) VALUES
(5, 'Michael Brown', 3, 5, 'Amazing work! The team went above and beyond our expectations.', '2025-05-07 07:01:32'),
(6, 'Emily Davis', 3, 4, 'Very satisfied with the service. Professional and efficient.', '2025-05-06 07:01:32'),
(7, 'David Wilson', 1, 5, 'Best service I have ever received. Will definitely use again!', '2025-05-05 07:01:32'),
(8, 'Lisa Anderson', 2, 4, 'Good service, friendly staff. Would use again.', '2025-05-04 07:01:32'),
(9, 'James Taylor', 3, 5, 'Exceptional service! The team was very thorough and professional.', '2025-05-03 07:01:32'),
(10, 'Jennifer Martinez', 1, 4, 'Great experience. The staff was very helpful and accommodating.', '2025-05-02 07:01:32'),
(12, 'Jason Baroro', 1, 4, 'goods', '2025-05-12 03:48:48'),
(13, 'Jason Baroro', 1, 4, 'goods', '2025-05-12 03:48:53'),
(14, 'Jason Baroro', 1, 4, 'goods', '2025-05-12 03:50:50'),
(15, 'Jason Baroro', 2, 3, 'okay lang', '2025-05-12 03:51:19'),
(16, 'Jason Baroro', 2, 3, 'okay lang', '2025-05-12 03:56:06'),
(17, 'baroro', 1, 1, 'edfsff', '2025-05-12 04:03:41'),
(18, 'baroro', 1, 1, 'edfsff', '2025-05-12 04:03:52');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `media_type` enum('image','video') NOT NULL DEFAULT 'image',
  `upload_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `description`, `event`, `path`, `media_type`, `upload_date`) VALUES
(1, 'kkknknknk', 'nknknknknknkn', 'kkkkk', 'uploads/gallery/682053ad26e57_bg.jpg', 'image', '2025-05-11 09:37:17'),
(3, 'kkknknknk', 'nknknknknknkn', 'kkkkk', 'uploads/gallery/682053ad29fb0_JASONBARS.png', 'image', '2025-05-11 09:37:17'),
(4, 'iuluillloi', 'iolillol', 'kluil', 'uploads/gallery/682055dab7d75_WIN_20250408_12_16_15_Pro.jpg', 'image', '2025-05-11 09:46:34'),
(5, 'iuluillloi', 'iolillol', 'kluil', 'uploads/gallery/682055dabf7f6_WIN_20250408_12_16_18_Pro.jpg', 'image', '2025-05-11 09:46:34'),
(6, 'tt', 'gyugfytfyt', 'ftyfcytfdyt', 'uploads/gallery/68220ef57ab82_WIN_20250408_12_16_34_Pro.jpg', 'image', '2025-05-12 17:08:37');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `department` varchar(50) NOT NULL,
  `member_type` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `name`, `email`, `department`, `member_type`, `status`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, 'Jason Baroro', 'jasonbaroro4@gmail.com', 'BSIT', 'Student Member', 'Active', 'uploads/members/682027fc9528c.jpg', '2025-05-11 04:30:52', '2025-05-11 04:30:52'),
(2, 'mae rodriguez', 'verniearmamento9@gmail.com', 'BTLED-IA', 'Student Member', 'Inactive', 'uploads/members/6820282437757.jpg', '2025-05-11 04:31:32', '2025-05-11 04:31:32');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Document Processing', 'Assistance with document preparation and processing', '2025-05-11 07:20:21'),
(2, 'Editing', 'Editing services', '2025-05-11 07:20:21'),
(3, 'Photography', 'Any Event Photography', '2025-05-11 07:20:21'),
(4, 'Others\r\n', 'Any Services that needs computer-related', '2025-05-11 07:20:21');

-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `service_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `requester_name` varchar(255) NOT NULL,
  `request_date` datetime DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Declined') DEFAULT 'Pending',
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_requests`
--

INSERT INTO `service_requests` (`id`, `service_id`, `service_name`, `category`, `requester_name`, `request_date`, `status`, `description`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'Document Processing', 'Document Processing', 'John Smith', '2024-03-15 09:30:00', 'Pending', 'Need help with document preparation for application.', 'Initial consultation required', '2025-05-12 14:59:38', '2025-05-12 14:59:38'),
(2, 2, 'Video competion', 'Editing', 'Maria Garcia', '2024-03-14 14:15:00', 'Approved', 'Seeking professional editing for film making.', 'Assigned to senior editor', '2025-05-12 14:59:38', '2025-05-12 14:59:38'),
(3, 3, 'Photography on IT days', 'Photography', 'Ahmed Hassan', '2024-03-13 11:00:00', 'Pending', 'Need help for IT days.', NULL, '2025-05-12 14:59:38', '2025-05-12 14:59:38'),
(4, 4, 'Others', 'Computer Services', 'Sarah Johnson', '2024-03-12 16:45:00', 'Declined', 'Need computer-related technical assistance.', 'Referred to IT support', '2025-05-12 14:59:38', '2025-05-12 14:59:38'),
(5, NULL, 'ukkkhkhkuk', 'Others\r\n', 'Jason Baroro', '2025-05-12 17:18:02', 'Declined', 'uktyukyik', NULL, '2025-05-12 15:18:02', '2025-05-12 15:18:40'),
(6, NULL, 'ghbnfgb', 'Document Processing', 'Jason Baroro', '2025-05-12 17:19:22', 'Approved', 'fbfbbbbbf', NULL, '2025-05-12 15:19:22', '2025-05-12 15:19:31');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `student_id`, `fullname`, `password`, `created_at`) VALUES
(1, '2023304706', 'Arlyn Kaye Allona Baluyos', '2023304706', '2025-05-12 02:03:35'),
(2, '2023304700', 'Lorie A. Tac-an', '2023304700', '2025-05-12 02:03:35'),
(3, '2023304604', 'Mae Rodriguez', '2023304604', '2025-05-12 02:03:35'),
(4, '2023304673', 'Alyssa Mae Rodriguez', '2023304673', '2025-05-12 02:03:35'),
(5, '2023394814', 'Gellyn S Rabino', '2023394814', '2025-05-12 02:03:35'),
(6, '2023304707', 'Jason Baroro', '2023304707', '2025-05-12 02:03:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-05-11 04:24:32'),
(2, 'maerodriguez', 'maerodriguez', '2025-05-12 05:01:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posted_by` (`posted_by`);

--
-- Indexes for table `announcement_attachments`
--
ALTER TABLE `announcement_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcement_id` (`announcement_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_service_id` (`service_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `announcement_attachments`
--
ALTER TABLE `announcement_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
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
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `announcement_attachments`
--
ALTER TABLE `announcement_attachments`
  ADD CONSTRAINT `announcement_attachments_ibfk_1` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service_requests` (`id`);

--
-- Constraints for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD CONSTRAINT `fk_service_id` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
