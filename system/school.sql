-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 11, 2025 at 01:56 PM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u484771290_smartschool`
--

-- --------------------------------------------------------

--
-- Table structure for table `alumni_events`
--

CREATE TABLE `alumni_events` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `event_for` varchar(100) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section` varchar(255) NOT NULL,
  `from_date` datetime NOT NULL,
  `to_date` datetime NOT NULL,
  `note` text NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `is_active` int(11) NOT NULL,
  `event_notification_message` text NOT NULL,
  `show_onwebsite` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `alumni_events`
--

INSERT INTO `alumni_events` (`id`, `title`, `event_for`, `session_id`, `class_id`, `section`, `from_date`, `to_date`, `note`, `photo`, `is_active`, `event_notification_message`, `show_onwebsite`, `created_at`) VALUES
(1, 'children\'s day', 'all', NULL, NULL, 'null', '2024-11-11 00:00:00', '2024-11-15 23:59:00', '', '', 0, 'Children\'s Day, also known as \'Bal Diwas\', is celebrated in India on November 14 to commemorate the birth anniversary of Jawaharlal Nehru, who advocated for children\'s education and rights.', 0, '2024-11-25 07:55:57'),
(2, 'Diwali Celebration Holiday', 'all', NULL, NULL, 'null', '2024-10-25 00:00:00', '2024-10-31 23:59:00', '', '', 0, 'To all students, Hoping this Diwali turns out to be an extra special one for you and your dear ones and may success be always with you. Happy Diwali Greetings !!\r\n', 0, '2024-11-25 07:57:05'),
(3, 'Government scholarship exam, 2024', 'all', NULL, NULL, 'null', '2024-10-14 00:00:00', '2024-10-20 23:59:00', '', '', 0, ' All the students of std 5 & 8 are hereby informed that you can apply for scholarship exam through our school. \r\nLast date for application will be 14.10.24.', 0, '2024-11-25 07:57:39'),
(4, 'National Level Workshop for Social Science Teachers Teaching in Class VI to X (Online)', 'all', NULL, NULL, 'null', '2024-07-08 00:00:00', '2024-07-25 23:59:00', '', '', 0, 'National Level Workshop for Social Science Teachers Teaching in Class VI to X (Online)', 0, '2024-11-25 07:58:18'),
(5, 'BIS students of level 6 A performed activity on the process of respiratory system', 'all', NULL, NULL, 'null', '2024-03-06 00:00:00', '2024-03-28 23:59:00', '', '', 0, 'Humans respiratory system consists of nostrils, nasal cavity, pharynx, larynx, trachea, bronchi, and lungs. It also helps in the movement of air inside and outside of the body', 0, '2024-11-25 07:59:07');

-- --------------------------------------------------------

--
-- Table structure for table `alumni_students`
--

CREATE TABLE `alumni_students` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `current_email` varchar(255) NOT NULL,
  `current_phone` varchar(255) NOT NULL,
  `occupation` text NOT NULL,
  `address` text NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendence_type`
--

CREATE TABLE `attendence_type` (
  `id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `key_value` varchar(50) NOT NULL,
  `long_lang_name` varchar(250) DEFAULT NULL,
  `long_name_style` varchar(250) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `for_qr_attendance` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `attendence_type`
--

INSERT INTO `attendence_type` (`id`, `type`, `key_value`, `long_lang_name`, `long_name_style`, `is_active`, `for_qr_attendance`, `created_at`, `updated_at`) VALUES
(1, 'Present', '<b class=\"text text-success\">P</b>', 'present', 'label label-success', 'yes', 1, '2023-12-13 07:53:10', '0000-00-00'),
(2, 'Late With Excuse', '<b class=\"text text-warning\">E</b>', 'late_with_excuse', 'label label-warning text-dark', 'no', 0, '2023-12-13 07:51:03', '0000-00-00'),
(3, 'Late', '<b class=\"text text-warning\">L</b>', 'late', 'label label-warning text-dark', 'yes', 1, '2023-12-13 07:51:09', '0000-00-00'),
(4, 'Absent', '<b class=\"text text-danger\">A</b>', 'absent', 'label label-danger', 'yes', 0, '2023-12-15 06:18:05', '0000-00-00'),
(5, 'Holiday', 'H', 'holiday', 'label label-info', 'yes', 0, '2023-12-14 12:57:13', '0000-00-00'),
(6, 'Half Day', '<b class=\"text text-warning\">F</b>', 'half_day', 'label label-warning text-dark', 'yes', 1, '2023-12-15 06:18:37', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `aws_s3_settings`
--

CREATE TABLE `aws_s3_settings` (
  `id` int(11) NOT NULL,
  `api_key` varchar(250) DEFAULT NULL,
  `api_secret` varchar(250) DEFAULT NULL,
  `bucket_name` varchar(250) DEFAULT NULL,
  `region` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `behaviour_settings`
--

CREATE TABLE `behaviour_settings` (
  `id` int(11) NOT NULL,
  `comment_option` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `behaviour_settings`
--

INSERT INTO `behaviour_settings` (`id`, `comment_option`, `created_at`) VALUES
(1, '[\"student\",\"parent\"]', '2022-12-31 18:49:38');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `book_title` varchar(100) NOT NULL,
  `book_no` varchar(50) NOT NULL,
  `isbn_no` varchar(100) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `rack_no` varchar(100) NOT NULL,
  `publish` varchar(100) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `perunitcost` float(10,2) DEFAULT NULL,
  `postdate` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `available` varchar(10) DEFAULT 'yes',
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `book_title`, `book_no`, `isbn_no`, `subject`, `rack_no`, `publish`, `author`, `qty`, `perunitcost`, `postdate`, `description`, `available`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'The Road', '1234', 'Is4567', 'rooad', '678', 'jems patel', 'jordan', 10, 56.00, '2024-11-25', '', 'yes', 'no', '2024-11-25 09:52:01', NULL),
(2, 'The giant', '5678', 'IS5678', 'giant', '5678', 'Jems', 'puma', 56, 45.00, '2024-11-25', '', 'yes', 'no', '2024-11-25 09:52:48', NULL),
(3, 'The Fish', '1234', 'IS233', 'other', '567', 'jensi patel', 'jensi', 34, 678.00, '2024-11-25', '', 'yes', 'no', '2024-11-25 11:31:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `book_issues`
--

CREATE TABLE `book_issues` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `duereturn_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `is_returned` int(11) DEFAULT 0,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `captcha`
--

CREATE TABLE `captcha` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `captcha`
--

INSERT INTO `captcha` (`id`, `name`, `status`, `created_at`) VALUES
(1, 'userlogin', 0, '2021-01-19 08:10:29'),
(2, 'login', 0, '2021-01-19 08:10:31'),
(3, 'admission', 0, '2021-01-19 04:48:11'),
(4, 'complain', 0, '2021-01-19 04:48:13'),
(5, 'contact_us', 0, '2021-01-19 04:48:15'),
(6, 'guest_login_signup', 0, '2022-12-07 14:11:31');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'General', 'no', '2024-11-25 10:03:35', NULL),
(2, 'OBC', 'no', '2024-11-25 10:03:37', NULL),
(3, 'Special', 'no', '2024-11-25 10:03:49', NULL),
(4, 'Physically Challenged', 'no', '2024-11-25 10:03:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exams`
--

CREATE TABLE `cbse_exams` (
  `id` int(11) NOT NULL,
  `total_working_days` int(11) DEFAULT 0,
  `cbse_term_id` int(11) DEFAULT NULL,
  `cbse_exam_assessment_id` int(11) DEFAULT NULL,
  `cbse_exam_grade_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `exam_code` varchar(200) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `description` mediumtext NOT NULL,
  `is_publish` int(1) NOT NULL,
  `is_active` int(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `use_exam_roll_no` int(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_exams`
--

INSERT INTO `cbse_exams` (`id`, `total_working_days`, `cbse_term_id`, `cbse_exam_assessment_id`, `cbse_exam_grade_id`, `name`, `exam_code`, `session_id`, `description`, `is_publish`, `is_active`, `created_by`, `use_exam_roll_no`, `created_at`) VALUES
(1, 0, 1, 3, 1, 'FORMATIVE EXAM', NULL, 19, '', 1, 1, 1, 0, '2024-12-09 10:46:29'),
(2, 0, 1, 3, 1, 'CAT 2', NULL, 19, '', 1, 1, 1, 0, '2024-12-09 10:49:08'),
(3, 150, 2, 1, 1, 'Half Yearly Periodic Assessments  Exam', NULL, 19, '', 1, 1, 1, 0, '2024-12-09 10:50:04'),
(4, 150, 2, 1, 2, 'Periodic Assessments Test-II', NULL, 19, 'Periodic Assessments Test-II', 1, 1, 1, 0, '2024-12-09 10:50:45'),
(5, 150, 2, 1, 1, 'CBSE Monthly  Exam Practice', NULL, 19, 'CBSE Monthly  Exam Practice', 1, 1, 1, 0, '2024-12-09 10:51:35');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_assessments`
--

CREATE TABLE `cbse_exam_assessments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_exam_assessments`
--

INSERT INTO `cbse_exam_assessments` (`id`, `name`, `description`) VALUES
(1, 'Periodic Assessment', 'An examination is a formal test that you take to show your knowledge or ability in a particular subject, or to obtain a qualification. If you have a medical examination, a doctor looks at your body, feels it, or does simple tests in order to check how healthy you are. '),
(2, 'Summative Assessment 1', 'An examination is a formal test that you take to show your knowledge or ability in a particular subject, or to obtain a qualification. If you have a medical examination, a doctor looks at your body, feels it, or does simple tests in order to check how healthy you are. '),
(3, 'CBET EXAM', '');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_assessment_types`
--

CREATE TABLE `cbse_exam_assessment_types` (
  `id` int(11) NOT NULL,
  `cbse_exam_assessment_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(100) NOT NULL,
  `maximum_marks` float NOT NULL,
  `pass_percentage` float NOT NULL,
  `description` mediumtext NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_exam_assessment_types`
--

INSERT INTO `cbse_exam_assessment_types` (`id`, `cbse_exam_assessment_id`, `name`, `code`, `maximum_marks`, `pass_percentage`, `description`, `created_by`, `created_at`) VALUES
(1, 1, 'Theory', 'TH02', 100, 35, 'An examination is a formal test that you take to show your knowledge or ability in a particular subject, or to obtain a qualification. If you have a medical examination, a doctor looks at your body, feels it, or does simple tests in order to check how healthy you are. ', 1, '2024-12-09 10:35:59'),
(2, 1, 'Practical', 'PC03', 75, 25, 'Practical exams test students\' practical skills and techniques usually in laboratory, clinical or field settings', 1, '2024-12-09 10:35:59'),
(3, 1, 'Assignment', 'AS05', 20, 15, 'An examination is a formal test that you take to show your knowledge or ability in a particular subject, or to obtain a qualification. If you have a medical examination, a doctor looks at your body, feels it, or does simple tests in order to check how healthy you are. ', 1, '2024-12-09 10:35:59'),
(4, 2, 'Theory', 'TH02', 100, 35, 'An examination is a formal test that you take to show your knowledge or ability in a particular subject, or to obtain a qualification. If you have a medical examination, a doctor looks at your body, feels it, or does simple tests in order to check how healthy you are. ', 1, '2024-12-09 10:37:03'),
(5, 2, 'Practical', 'PC03', 75, 25, 'Practical exams test students\' practical skills and techniques usually in laboratory, clinical or field settings', 1, '2024-12-09 10:37:03'),
(6, 3, 'Theory ', '1001', 100, 50, '', 1, '2024-12-09 10:38:00'),
(7, 3, 'Practical ', '1002', 100, 50, '', 1, '2024-12-09 10:38:00');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_class_sections`
--

CREATE TABLE `cbse_exam_class_sections` (
  `id` int(11) NOT NULL,
  `cbse_exam_id` int(11) NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_exam_class_sections`
--

INSERT INTO `cbse_exam_class_sections` (`id`, `cbse_exam_id`, `class_section_id`, `created_at`) VALUES
(19, 4, 16, '2024-12-10 08:22:48'),
(20, 4, 17, '2024-12-10 08:22:48'),
(21, 4, 18, '2024-12-10 08:22:48'),
(22, 4, 19, '2024-12-10 08:22:48'),
(23, 3, 16, '2024-12-10 08:22:57'),
(24, 3, 17, '2024-12-10 08:22:57'),
(25, 3, 18, '2024-12-10 08:22:57'),
(26, 3, 19, '2024-12-10 08:22:57'),
(27, 2, 26, '2024-12-10 08:23:04'),
(28, 1, 26, '2024-12-10 08:23:10'),
(29, 5, 1, '2024-12-10 10:07:40'),
(30, 5, 2, '2024-12-10 10:07:40'),
(31, 5, 3, '2024-12-10 10:07:40'),
(32, 5, 4, '2024-12-10 10:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_grades`
--

CREATE TABLE `cbse_exam_grades` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_exam_grades`
--

INSERT INTO `cbse_exam_grades` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'EXAM Grade 1', 'An examination is a formal test that you take to show your knowledge or ability in a particular subject, or to obtain a qualification. If you have a medical examination, a doctor looks at your body, feels it, or does simple tests in order to check how healthy you are.	', '2024-12-09 10:46:10'),
(2, 'Exam grade 1', 'a degree or step in a scale, as of rank, advancement, quality, value, or intensity.	', '2024-12-09 10:45:09'),
(3, 'CBET GRADING', '', '2024-12-09 10:49:20');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_grades_range`
--

CREATE TABLE `cbse_exam_grades_range` (
  `id` int(11) NOT NULL,
  `cbse_exam_grade_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `minimum_percentage` float NOT NULL,
  `maximum_percentage` float NOT NULL,
  `description` mediumtext NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_exam_grades_range`
--

INSERT INTO `cbse_exam_grades_range` (`id`, `cbse_exam_grade_id`, `name`, `minimum_percentage`, `maximum_percentage`, `description`, `created_by`, `created_at`) VALUES
(1, 1, 'A+', 90, 100, 'Excellent', 1, '2024-12-09 10:41:51'),
(2, 1, 'A', 80, 90, 'Very Good', 1, '2024-12-09 10:41:51'),
(3, 1, 'B+', 70, 80, 'Good', 1, '2024-12-09 10:41:51'),
(4, 1, 'B', 60, 70, 'Better', 1, '2024-12-09 10:41:51'),
(5, 1, 'C+', 50, 60, 'Keep Hard Working', 1, '2024-12-09 10:41:51'),
(6, 1, 'D', 40, 50, 'Keep Hard Working', 1, '2024-12-09 10:41:51'),
(7, 2, 'A+', 90, 100, 'Excellent', 1, '2024-12-09 10:45:09'),
(8, 2, 'A', 80, 90, 'Very Good', 1, '2024-12-09 10:45:09'),
(9, 2, 'B+', 70, 80, 'Better', 1, '2024-12-09 10:45:09'),
(10, 2, 'B', 60, 70, 'Good', 1, '2024-12-09 10:45:09'),
(11, 2, 'C+', 50, 60, 'Keep Hard Working', 1, '2024-12-09 10:45:09'),
(12, 2, 'D', 40, 50, 'Keep Hard Working', 1, '2024-12-09 10:45:09'),
(13, 3, 'Mastery', 90, 100, 'Excellent', 1, '2024-12-09 10:49:20'),
(14, 3, 'proficiency	', 80, 90, 'Very Good', 1, '2024-12-09 10:49:20'),
(15, 3, 'competent	', 60, 64, 'Better', 1, '2024-12-09 10:49:20'),
(16, 3, 'Not yet competent	', 33, 45, 'Keep hard working', 1, '2024-12-09 10:49:20');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_observations`
--

CREATE TABLE `cbse_exam_observations` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_exam_observations`
--

INSERT INTO `cbse_exam_observations` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Cbse Exam Observation 1	', 'In an observational study, researchers study how participants perform certain behaviours or activities without telling them what methods or behaviours to choose.	\r\n', '2024-12-09 10:36:32'),
(2, 'Cbse Exam Observation 2	', 'In an observational study, researchers study how participants perform certain behaviours or activities without telling them what methods or behaviours to choose.	\r\n', '2024-12-09 10:37:11');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_students`
--

CREATE TABLE `cbse_exam_students` (
  `id` int(11) NOT NULL,
  `cbse_exam_id` int(11) NOT NULL,
  `student_session_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `roll_no` varchar(20) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `total_present_days` int(11) DEFAULT NULL,
  `delete_student_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_exam_students`
--

INSERT INTO `cbse_exam_students` (`id`, `cbse_exam_id`, `student_session_id`, `staff_id`, `roll_no`, `remark`, `total_present_days`, `delete_student_id`, `created_at`) VALUES
(13, 4, 25, 1, NULL, 'Good', 120, 0, '2024-12-10 10:01:21'),
(14, 4, 26, 1, NULL, 'Excellent', 51, 0, '2024-12-10 10:01:21'),
(15, 4, 28, 1, NULL, 'Good', 52, 0, '2024-12-10 10:01:21'),
(16, 4, 30, 1, NULL, 'Excellent', 85, 0, '2024-12-10 10:01:21'),
(17, 4, 32, 1, NULL, 'Great', 95, 0, '2024-12-10 10:01:21'),
(18, 4, 35, 1, NULL, 'Excellent', 75, 0, '2024-12-10 10:01:21'),
(19, 4, 37, 1, NULL, 'Excellent', 120, 0, '2024-12-10 10:01:21'),
(20, 4, 38, 1, NULL, 'Good', 140, 0, '2024-12-10 10:01:21'),
(21, 4, 40, 1, NULL, 'Great', 48, 0, '2024-12-10 10:01:21'),
(22, 4, 43, 1, NULL, 'Excellent', 75, 0, '2024-12-10 10:01:21'),
(23, 4, 45, 1, NULL, 'Good ', 95, 0, '2024-12-10 10:01:21'),
(24, 4, 47, 1, NULL, 'Good', 96, 0, '2024-12-10 10:01:21'),
(25, 3, 25, 1, NULL, 'Excellent', 90, 0, '2024-12-10 10:01:27'),
(26, 3, 26, 1, NULL, 'Good', 90, 0, '2024-12-10 10:01:27'),
(27, 3, 28, 1, NULL, 'Great', 56, 0, '2024-12-10 10:01:27'),
(28, 3, 30, 1, NULL, 'Excellent', 78, 0, '2024-12-10 10:01:27'),
(29, 3, 32, 1, NULL, 'Excellent', 78, 0, '2024-12-10 10:01:27'),
(30, 3, 35, 1, NULL, 'Great', 78, 0, '2024-12-10 10:01:27'),
(31, 3, 37, 1, NULL, 'Excellent', 90, 0, '2024-12-10 10:01:27'),
(32, 3, 38, 1, NULL, 'Good', 78, 0, '2024-12-10 10:01:27'),
(33, 3, 40, 1, NULL, 'Good', 90, 0, '2024-12-10 10:01:27'),
(34, 3, 43, 1, NULL, 'Great', 67, 0, '2024-12-10 10:01:27'),
(35, 3, 45, 1, NULL, 'Good', 110, 0, '2024-12-10 10:01:27'),
(36, 3, 47, 1, NULL, 'Excellent', 110, 0, '2024-12-10 10:01:27'),
(37, 5, 1, 1, NULL, 'Good', 100, 0, '2024-12-10 10:09:00'),
(38, 5, 14, 1, NULL, 'Very Good', 98, 0, '2024-12-10 10:09:00'),
(39, 5, 19, 1, NULL, 'Very Good', 130, 0, '2024-12-10 10:09:00'),
(40, 5, 21, 1, NULL, 'Very Good', 101, 0, '2024-12-10 10:09:00'),
(41, 5, 24, 1, NULL, 'Very Good', 100, 0, '2024-12-10 10:09:00'),
(42, 5, 4, 1, NULL, 'Very Good', 120, 0, '2024-12-10 10:09:00'),
(43, 5, 3, 1, NULL, 'Good', 110, 0, '2024-12-10 10:09:00'),
(44, 5, 5, 1, NULL, 'Keep hard working', 54, 0, '2024-12-10 10:09:00'),
(45, 5, 7, 1, NULL, 'Good', 51, 0, '2024-12-10 10:09:00'),
(46, 5, 9, 1, NULL, 'Good', 42, 0, '2024-12-10 10:09:00'),
(47, 5, 11, 1, NULL, 'Good', 51, 0, '2024-12-10 10:09:00'),
(48, 5, 12, 1, NULL, 'Excellent', 85, 0, '2024-12-10 10:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_student_subject_rank`
--

CREATE TABLE `cbse_exam_student_subject_rank` (
  `id` int(11) NOT NULL,
  `cbse_template_id` int(11) DEFAULT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  `rank_percentage` float(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_exam_student_subject_rank`
--

INSERT INTO `cbse_exam_student_subject_rank` (`id`, `cbse_template_id`, `student_session_id`, `subject_id`, `rank`, `rank_percentage`, `created_at`) VALUES
(1, 1, 25, 1, 1, 100.00, '2025-02-11 13:35:53'),
(2, 1, 26, 1, 1, 100.00, '2025-02-11 13:35:53'),
(3, 1, 28, 1, 1, 100.00, '2025-02-11 13:35:53'),
(4, 1, 30, 1, 1, 100.00, '2025-02-11 13:35:53'),
(5, 1, 32, 1, 1, 100.00, '2025-02-11 13:35:53'),
(6, 1, 35, 1, 1, 100.00, '2025-02-11 13:35:53'),
(7, 1, 37, 1, 1, 100.00, '2025-02-11 13:35:53'),
(8, 1, 38, 1, 1, 100.00, '2025-02-11 13:35:53'),
(9, 1, 40, 1, 1, 100.00, '2025-02-11 13:35:53'),
(10, 1, 43, 1, 1, 100.00, '2025-02-11 13:35:53'),
(11, 1, 45, 1, 1, 100.00, '2025-02-11 13:35:53'),
(12, 1, 47, 1, 1, 100.00, '2025-02-11 13:35:53'),
(13, 1, 3, 1, 2, 75.38, '2025-02-11 13:35:53'),
(14, 1, 14, 1, 3, 72.82, '2025-02-11 13:35:53'),
(15, 1, 9, 1, 4, 69.74, '2025-02-11 13:35:53'),
(16, 1, 7, 1, 5, 63.08, '2025-02-11 13:35:53'),
(17, 1, 19, 1, 6, 62.05, '2025-02-11 13:35:53'),
(18, 1, 5, 1, 7, 58.97, '2025-02-11 13:35:53'),
(19, 1, 11, 1, 8, 52.31, '2025-02-11 13:35:53'),
(20, 1, 4, 1, 9, 46.15, '2025-02-11 13:35:53'),
(21, 1, 21, 1, 10, 44.62, '2025-02-11 13:35:53'),
(22, 1, 1, 1, 11, 43.59, '2025-02-11 13:35:53'),
(23, 1, 24, 1, 12, 41.54, '2025-02-11 13:35:53'),
(24, 1, 12, 1, 13, 18.46, '2025-02-11 13:35:53'),
(25, 1, 1, 2, 1, 100.00, '2025-02-11 13:35:53'),
(26, 1, 3, 2, 1, 100.00, '2025-02-11 13:35:53'),
(27, 1, 4, 2, 1, 100.00, '2025-02-11 13:35:53'),
(28, 1, 5, 2, 1, 100.00, '2025-02-11 13:35:53'),
(29, 1, 7, 2, 1, 100.00, '2025-02-11 13:35:53'),
(30, 1, 9, 2, 1, 100.00, '2025-02-11 13:35:53'),
(31, 1, 11, 2, 1, 100.00, '2025-02-11 13:35:53'),
(32, 1, 12, 2, 1, 100.00, '2025-02-11 13:35:53'),
(33, 1, 14, 2, 1, 100.00, '2025-02-11 13:35:53'),
(34, 1, 19, 2, 1, 100.00, '2025-02-11 13:35:53'),
(35, 1, 21, 2, 1, 100.00, '2025-02-11 13:35:53'),
(36, 1, 24, 2, 1, 100.00, '2025-02-11 13:35:53'),
(37, 1, 25, 2, 2, 0.00, '2025-02-11 13:35:53'),
(38, 1, 26, 2, 2, 0.00, '2025-02-11 13:35:53'),
(39, 1, 28, 2, 2, 0.00, '2025-02-11 13:35:53'),
(40, 1, 30, 2, 2, 0.00, '2025-02-11 13:35:53'),
(41, 1, 32, 2, 2, 0.00, '2025-02-11 13:35:53'),
(42, 1, 35, 2, 2, 0.00, '2025-02-11 13:35:53'),
(43, 1, 37, 2, 2, 0.00, '2025-02-11 13:35:53'),
(44, 1, 38, 2, 2, 0.00, '2025-02-11 13:35:53'),
(45, 1, 40, 2, 2, 0.00, '2025-02-11 13:35:53'),
(46, 1, 43, 2, 2, 0.00, '2025-02-11 13:35:53'),
(47, 1, 45, 2, 2, 0.00, '2025-02-11 13:35:53'),
(48, 1, 47, 2, 2, 0.00, '2025-02-11 13:35:53'),
(49, 1, 1, 3, 1, 100.00, '2025-02-11 13:35:53'),
(50, 1, 3, 3, 1, 100.00, '2025-02-11 13:35:53'),
(51, 1, 4, 3, 1, 100.00, '2025-02-11 13:35:53'),
(52, 1, 5, 3, 1, 100.00, '2025-02-11 13:35:53'),
(53, 1, 7, 3, 1, 100.00, '2025-02-11 13:35:53'),
(54, 1, 9, 3, 1, 100.00, '2025-02-11 13:35:53'),
(55, 1, 11, 3, 1, 100.00, '2025-02-11 13:35:53'),
(56, 1, 12, 3, 1, 100.00, '2025-02-11 13:35:53'),
(57, 1, 14, 3, 1, 100.00, '2025-02-11 13:35:53'),
(58, 1, 19, 3, 1, 100.00, '2025-02-11 13:35:53'),
(59, 1, 21, 3, 1, 100.00, '2025-02-11 13:35:53'),
(60, 1, 24, 3, 1, 100.00, '2025-02-11 13:35:53'),
(61, 1, 25, 3, 2, 0.00, '2025-02-11 13:35:53'),
(62, 1, 26, 3, 2, 0.00, '2025-02-11 13:35:53'),
(63, 1, 28, 3, 2, 0.00, '2025-02-11 13:35:53'),
(64, 1, 30, 3, 2, 0.00, '2025-02-11 13:35:53'),
(65, 1, 32, 3, 2, 0.00, '2025-02-11 13:35:53'),
(66, 1, 35, 3, 2, 0.00, '2025-02-11 13:35:53'),
(67, 1, 37, 3, 2, 0.00, '2025-02-11 13:35:53'),
(68, 1, 38, 3, 2, 0.00, '2025-02-11 13:35:53'),
(69, 1, 40, 3, 2, 0.00, '2025-02-11 13:35:53'),
(70, 1, 43, 3, 2, 0.00, '2025-02-11 13:35:53'),
(71, 1, 45, 3, 2, 0.00, '2025-02-11 13:35:53'),
(72, 1, 47, 3, 2, 0.00, '2025-02-11 13:35:53'),
(73, 1, 1, 4, 1, 100.00, '2025-02-11 13:35:53'),
(74, 1, 3, 4, 1, 100.00, '2025-02-11 13:35:53'),
(75, 1, 4, 4, 1, 100.00, '2025-02-11 13:35:53'),
(76, 1, 5, 4, 1, 100.00, '2025-02-11 13:35:53'),
(77, 1, 7, 4, 1, 100.00, '2025-02-11 13:35:53'),
(78, 1, 9, 4, 1, 100.00, '2025-02-11 13:35:53'),
(79, 1, 11, 4, 1, 100.00, '2025-02-11 13:35:53'),
(80, 1, 12, 4, 1, 100.00, '2025-02-11 13:35:53'),
(81, 1, 14, 4, 1, 100.00, '2025-02-11 13:35:53'),
(82, 1, 19, 4, 1, 100.00, '2025-02-11 13:35:53'),
(83, 1, 21, 4, 1, 100.00, '2025-02-11 13:35:53'),
(84, 1, 24, 4, 1, 100.00, '2025-02-11 13:35:53'),
(85, 1, 25, 4, 2, 0.00, '2025-02-11 13:35:53'),
(86, 1, 26, 4, 2, 0.00, '2025-02-11 13:35:53'),
(87, 1, 28, 4, 2, 0.00, '2025-02-11 13:35:53'),
(88, 1, 30, 4, 2, 0.00, '2025-02-11 13:35:53'),
(89, 1, 32, 4, 2, 0.00, '2025-02-11 13:35:53'),
(90, 1, 35, 4, 2, 0.00, '2025-02-11 13:35:53'),
(91, 1, 37, 4, 2, 0.00, '2025-02-11 13:35:53'),
(92, 1, 38, 4, 2, 0.00, '2025-02-11 13:35:53'),
(93, 1, 40, 4, 2, 0.00, '2025-02-11 13:35:53'),
(94, 1, 43, 4, 2, 0.00, '2025-02-11 13:35:53'),
(95, 1, 45, 4, 2, 0.00, '2025-02-11 13:35:53'),
(96, 1, 47, 4, 2, 0.00, '2025-02-11 13:35:53'),
(97, 1, 1, 5, 1, 100.00, '2025-02-11 13:35:53'),
(98, 1, 3, 5, 1, 100.00, '2025-02-11 13:35:53'),
(99, 1, 4, 5, 1, 100.00, '2025-02-11 13:35:53'),
(100, 1, 5, 5, 1, 100.00, '2025-02-11 13:35:53'),
(101, 1, 7, 5, 1, 100.00, '2025-02-11 13:35:53'),
(102, 1, 9, 5, 1, 100.00, '2025-02-11 13:35:53'),
(103, 1, 11, 5, 1, 100.00, '2025-02-11 13:35:53'),
(104, 1, 12, 5, 1, 100.00, '2025-02-11 13:35:53'),
(105, 1, 14, 5, 1, 100.00, '2025-02-11 13:35:53'),
(106, 1, 19, 5, 1, 100.00, '2025-02-11 13:35:53'),
(107, 1, 21, 5, 1, 100.00, '2025-02-11 13:35:53'),
(108, 1, 24, 5, 1, 100.00, '2025-02-11 13:35:53'),
(109, 1, 25, 5, 2, 0.00, '2025-02-11 13:35:53'),
(110, 1, 26, 5, 2, 0.00, '2025-02-11 13:35:53'),
(111, 1, 28, 5, 2, 0.00, '2025-02-11 13:35:53'),
(112, 1, 30, 5, 2, 0.00, '2025-02-11 13:35:53'),
(113, 1, 32, 5, 2, 0.00, '2025-02-11 13:35:53'),
(114, 1, 35, 5, 2, 0.00, '2025-02-11 13:35:53'),
(115, 1, 37, 5, 2, 0.00, '2025-02-11 13:35:53'),
(116, 1, 38, 5, 2, 0.00, '2025-02-11 13:35:53'),
(117, 1, 40, 5, 2, 0.00, '2025-02-11 13:35:53'),
(118, 1, 43, 5, 2, 0.00, '2025-02-11 13:35:53'),
(119, 1, 45, 5, 2, 0.00, '2025-02-11 13:35:53'),
(120, 1, 47, 5, 2, 0.00, '2025-02-11 13:35:53');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_timetable`
--

CREATE TABLE `cbse_exam_timetable` (
  `id` int(11) NOT NULL,
  `cbse_exam_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `time_from` time NOT NULL,
  `time_to` time NOT NULL,
  `duration` int(11) NOT NULL,
  `room_no` varchar(255) NOT NULL,
  `is_written` int(1) NOT NULL DEFAULT 1,
  `written_maximum_marks` float NOT NULL,
  `is_practical` int(1) NOT NULL,
  `practical_maximum_mark` float DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_exam_timetable`
--

INSERT INTO `cbse_exam_timetable` (`id`, `cbse_exam_id`, `subject_id`, `date`, `time_from`, `time_to`, `duration`, `room_no`, `is_written`, `written_maximum_marks`, `is_practical`, `practical_maximum_mark`, `created_by`, `created_at`) VALUES
(1, 5, 1, '2024-12-10', '15:31:49', '00:00:00', 45, '435', 1, 0, 0, NULL, NULL, '2024-12-10 10:02:03'),
(2, 2, 3, '2024-12-11', '15:45:21', '00:00:00', 30, '235', 1, 0, 0, NULL, NULL, '2024-12-10 10:15:34'),
(3, 3, 2, '2024-12-05', '09:30:30', '00:00:00', 120, '12', 1, 0, 0, NULL, NULL, '2024-12-10 10:17:54'),
(4, 3, 5, '2024-12-07', '09:30:30', '00:00:00', 120, '120', 1, 0, 0, NULL, NULL, '2024-12-10 10:17:54'),
(5, 3, 4, '2024-12-10', '09:30:30', '00:00:00', 120, '15', 1, 0, 0, NULL, NULL, '2024-12-10 10:17:54'),
(6, 4, 2, '2024-11-05', '10:30:30', '00:00:00', 90, '90', 1, 0, 0, NULL, NULL, '2024-12-10 10:27:22'),
(7, 4, 5, '2024-11-06', '10:30:30', '00:00:00', 90, '12', 1, 0, 0, NULL, NULL, '2024-12-10 10:27:22'),
(8, 4, 4, '2024-11-08', '10:30:30', '00:00:00', 90, '14', 1, 0, 0, NULL, NULL, '2024-12-10 10:27:22'),
(9, 4, 3, '2024-11-12', '10:30:30', '00:00:00', 90, '15', 1, 0, 0, NULL, NULL, '2024-12-10 10:27:22');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_exam_timetable_assessment_types`
--

CREATE TABLE `cbse_exam_timetable_assessment_types` (
  `id` int(11) NOT NULL,
  `cbse_exam_timetable_id` int(11) DEFAULT NULL,
  `cbse_exam_assessment_type_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_exam_timetable_assessment_types`
--

INSERT INTO `cbse_exam_timetable_assessment_types` (`id`, `cbse_exam_timetable_id`, `cbse_exam_assessment_type_id`, `created_at`) VALUES
(1, 1, 1, '2024-12-10 10:02:03'),
(2, 1, 2, '2024-12-10 10:09:17'),
(3, 1, 3, '2024-12-10 10:09:17'),
(4, 2, 6, '2024-12-10 10:15:34'),
(5, 2, 7, '2024-12-10 10:15:34'),
(6, 3, 1, '2024-12-10 10:17:54'),
(7, 4, 1, '2024-12-10 10:17:54'),
(8, 5, 1, '2024-12-10 10:17:54'),
(9, 5, 2, '2024-12-10 10:17:54'),
(10, 5, 3, '2024-12-10 10:17:54'),
(11, 6, 1, '2024-12-10 10:27:22'),
(12, 7, 1, '2024-12-10 10:27:22'),
(13, 8, 1, '2024-12-10 10:27:22'),
(14, 8, 2, '2024-12-10 10:27:22'),
(15, 8, 3, '2024-12-10 10:27:22'),
(16, 9, 1, '2024-12-10 10:27:22'),
(17, 9, 2, '2024-12-10 10:27:22'),
(18, 9, 3, '2024-12-10 10:27:22');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_marksheet_type`
--

CREATE TABLE `cbse_marksheet_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_code` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_marksheet_type`
--

INSERT INTO `cbse_marksheet_type` (`id`, `name`, `short_code`) VALUES
(1, 'All Terms', 'all_term'),
(2, 'Term Wise', 'term_wise'),
(3, 'Single Exam Without Term', 'exam_wise'),
(4, 'Multiple Exams Without Term', 'without_term');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_observation_class_section`
--

CREATE TABLE `cbse_observation_class_section` (
  `id` int(11) NOT NULL,
  `cbse_observation_parameter_id` int(11) NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_observation_parameters`
--

CREATE TABLE `cbse_observation_parameters` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` mediumtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_observation_parameters`
--

INSERT INTO `cbse_observation_parameters` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Behaviour', '', '2024-12-09 10:35:44'),
(2, 'Game', '', '2024-12-09 10:35:48'),
(3, 'Painting	', '', '2024-12-09 10:35:53'),
(4, 'Art & Culture	', '', '2024-12-09 10:35:59');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_observation_subparameter`
--

CREATE TABLE `cbse_observation_subparameter` (
  `id` int(11) NOT NULL,
  `cbse_exam_observation_id` int(11) NOT NULL,
  `cbse_observation_parameter_id` int(11) NOT NULL,
  `maximum_marks` float NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_observation_subparameter`
--

INSERT INTO `cbse_observation_subparameter` (`id`, `cbse_exam_observation_id`, `cbse_observation_parameter_id`, `maximum_marks`, `description`, `created_by`, `created_at`) VALUES
(1, 1, 1, 20, NULL, NULL, '2024-12-09 10:36:32'),
(2, 2, 2, 20, NULL, NULL, '2024-12-09 10:37:11'),
(3, 2, 3, 25, NULL, NULL, '2024-12-09 10:37:11');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_observation_terms`
--

CREATE TABLE `cbse_observation_terms` (
  `id` int(11) NOT NULL,
  `cbse_exam_observation_id` int(11) NOT NULL,
  `cbse_term_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_observation_terms`
--

INSERT INTO `cbse_observation_terms` (`id`, `cbse_exam_observation_id`, `cbse_term_id`, `session_id`, `description`) VALUES
(1, 1, 3, 19, 'Cbse Exam Observation 2'),
(2, 2, 2, 19, 'Cbse Exam Observation 2');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_observation_term_student_subparameter`
--

CREATE TABLE `cbse_observation_term_student_subparameter` (
  `id` int(11) NOT NULL,
  `cbse_ovservation_term_id` int(11) DEFAULT NULL,
  `cbse_observation_subparameter_id` int(11) DEFAULT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `obtain_marks` float(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_student_exam_ranks`
--

CREATE TABLE `cbse_student_exam_ranks` (
  `id` int(11) NOT NULL,
  `cbse_exam_id` int(11) NOT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  `rank_percentage` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_student_exam_ranks`
--

INSERT INTO `cbse_student_exam_ranks` (`id`, `cbse_exam_id`, `student_session_id`, `rank`, `rank_percentage`, `created_at`) VALUES
(25, 4, 25, 1, 0, '2024-12-10 10:29:14'),
(26, 4, 26, 1, 0, '2024-12-10 10:29:14'),
(27, 4, 28, 1, 0, '2024-12-10 10:29:14'),
(28, 4, 30, 1, 0, '2024-12-10 10:29:14'),
(29, 4, 32, 1, 0, '2024-12-10 10:29:14'),
(30, 4, 35, 1, 0, '2024-12-10 10:29:14'),
(31, 4, 37, 1, 0, '2024-12-10 10:29:14'),
(32, 4, 38, 1, 0, '2024-12-10 10:29:14'),
(33, 4, 40, 1, 0, '2024-12-10 10:29:14'),
(34, 4, 43, 1, 0, '2024-12-10 10:29:14'),
(35, 4, 45, 1, 0, '2024-12-10 10:29:14'),
(36, 4, 47, 1, 0, '2024-12-10 10:29:14'),
(37, 5, 3, 1, 75, '2024-12-10 10:29:25'),
(38, 5, 14, 2, 73, '2024-12-10 10:29:25'),
(39, 5, 9, 3, 70, '2024-12-10 10:29:25'),
(40, 5, 7, 4, 63, '2024-12-10 10:29:25'),
(41, 5, 19, 5, 62, '2024-12-10 10:29:25'),
(42, 5, 5, 6, 59, '2024-12-10 10:29:25'),
(43, 5, 11, 7, 52, '2024-12-10 10:29:25'),
(44, 5, 4, 8, 46, '2024-12-10 10:29:25'),
(45, 5, 21, 9, 45, '2024-12-10 10:29:25'),
(46, 5, 1, 10, 44, '2024-12-10 10:29:25'),
(47, 5, 24, 11, 42, '2024-12-10 10:29:25'),
(48, 5, 12, 12, 18, '2024-12-10 10:29:25'),
(49, 3, 25, 1, 0, '2024-12-10 10:29:38'),
(50, 3, 26, 1, 0, '2024-12-10 10:29:38'),
(51, 3, 28, 1, 0, '2024-12-10 10:29:38'),
(52, 3, 30, 1, 0, '2024-12-10 10:29:38'),
(53, 3, 32, 1, 0, '2024-12-10 10:29:38'),
(54, 3, 35, 1, 0, '2024-12-10 10:29:38'),
(55, 3, 37, 1, 0, '2024-12-10 10:29:38'),
(56, 3, 38, 1, 0, '2024-12-10 10:29:38'),
(57, 3, 40, 1, 0, '2024-12-10 10:29:38'),
(58, 3, 43, 1, 0, '2024-12-10 10:29:38'),
(59, 3, 45, 1, 0, '2024-12-10 10:29:38'),
(60, 3, 47, 1, 0, '2024-12-10 10:29:38');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_student_subject_marks`
--

CREATE TABLE `cbse_student_subject_marks` (
  `id` int(11) NOT NULL,
  `cbse_exam_timetable_assessment_type_id` int(11) NOT NULL,
  `cbse_exam_timetable_id` int(11) DEFAULT NULL,
  `cbse_exam_student_id` int(11) DEFAULT NULL,
  `cbse_exam_assessment_type_id` int(11) DEFAULT NULL,
  `is_absent` int(1) NOT NULL DEFAULT 0,
  `marks` float(10,2) DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_student_subject_marks`
--

INSERT INTO `cbse_student_subject_marks` (`id`, `cbse_exam_timetable_assessment_type_id`, `cbse_exam_timetable_id`, `cbse_exam_student_id`, `cbse_exam_assessment_type_id`, `is_absent`, `marks`, `note`, `created_at`) VALUES
(13, 1, 1, 37, 1, 0, 56.00, 'Good', '2024-12-10 10:13:22'),
(14, 2, 1, 37, 2, 0, 16.00, 'Good', '2024-12-10 10:13:22'),
(15, 3, 1, 37, 3, 0, 13.00, 'Good', '2024-12-10 10:13:22'),
(16, 1, 1, 38, 1, 0, 65.00, 'Good', '2024-12-10 10:13:22'),
(17, 2, 1, 38, 2, 0, 64.00, 'Good', '2024-12-10 10:13:22'),
(18, 3, 1, 38, 3, 0, 13.00, 'Good', '2024-12-10 10:13:22'),
(19, 1, 1, 39, 1, 0, 56.00, 'absent', '2024-12-10 10:13:22'),
(20, 2, 1, 39, 2, 0, 65.00, 'absent', '2024-12-10 10:13:22'),
(21, 3, 1, 39, 3, 1, 0.00, 'absent', '2024-12-10 10:13:22'),
(22, 1, 1, 40, 1, 0, 54.00, 'Good', '2024-12-10 10:13:22'),
(23, 2, 1, 40, 2, 0, 14.00, 'Good', '2024-12-10 10:13:22'),
(24, 3, 1, 40, 3, 0, 19.00, 'Good', '2024-12-10 10:13:22'),
(25, 1, 1, 41, 1, 1, 0.00, 'Good', '2024-12-10 10:13:22'),
(26, 2, 1, 41, 2, 0, 67.00, 'Good', '2024-12-10 10:13:22'),
(27, 3, 1, 41, 3, 0, 14.00, 'Good', '2024-12-10 10:13:22'),
(28, 1, 1, 42, 1, 0, 24.00, 'Good', '2024-12-10 10:13:22'),
(29, 2, 1, 42, 2, 0, 53.00, 'Good', '2024-12-10 10:13:22'),
(30, 3, 1, 42, 3, 0, 13.00, 'Good', '2024-12-10 10:13:22'),
(31, 1, 1, 43, 1, 0, 67.00, 'Good', '2024-12-10 10:13:22'),
(32, 2, 1, 43, 2, 0, 65.00, 'Good', '2024-12-10 10:13:22'),
(33, 3, 1, 43, 3, 0, 15.00, 'Good', '2024-12-10 10:13:22'),
(34, 1, 1, 44, 1, 0, 56.00, 'Good', '2024-12-10 10:13:22'),
(35, 2, 1, 44, 2, 0, 45.00, 'Good', '2024-12-10 10:13:22'),
(36, 3, 1, 44, 3, 0, 14.00, 'Good', '2024-12-10 10:13:22'),
(37, 1, 1, 45, 1, 0, 45.00, 'Good', '2024-12-10 10:13:22'),
(38, 2, 1, 45, 2, 0, 65.00, 'Good', '2024-12-10 10:13:22'),
(39, 3, 1, 45, 3, 0, 13.00, 'Good', '2024-12-10 10:13:22'),
(40, 1, 1, 46, 1, 0, 99.00, 'Good', '2024-12-10 10:13:22'),
(41, 2, 1, 46, 2, 0, 24.00, 'Good', '2024-12-10 10:13:22'),
(42, 3, 1, 46, 3, 0, 13.00, 'Good', '2024-12-10 10:13:22'),
(43, 1, 1, 47, 1, 0, 76.00, 'Good', '2024-12-10 10:13:22'),
(44, 2, 1, 47, 2, 0, 14.00, 'Good', '2024-12-10 10:13:22'),
(45, 3, 1, 47, 3, 0, 12.00, 'Good', '2024-12-10 10:13:22'),
(46, 1, 1, 48, 1, 0, 9.00, 'Good', '2024-12-10 10:13:22'),
(47, 2, 1, 48, 2, 0, 15.00, 'Good', '2024-12-10 10:13:22'),
(48, 3, 1, 48, 3, 0, 12.00, 'Good', '2024-12-10 10:13:22');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_student_subject_result`
--

CREATE TABLE `cbse_student_subject_result` (
  `id` int(11) NOT NULL,
  `cbse_exam_timetable_id` int(11) DEFAULT NULL,
  `cbse_exam_student_id` int(11) DEFAULT NULL,
  `note` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cbse_student_template_rank`
--

CREATE TABLE `cbse_student_template_rank` (
  `id` int(11) NOT NULL,
  `cbse_template_id` int(11) DEFAULT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `rank` int(20) DEFAULT NULL,
  `rank_percentage` float(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_student_template_rank`
--

INSERT INTO `cbse_student_template_rank` (`id`, `cbse_template_id`, `student_session_id`, `rank`, `rank_percentage`, `created_at`) VALUES
(1, 1, 3, 1, 95.08, '2025-02-11 13:35:53'),
(2, 1, 14, 2, 94.56, '2025-02-11 13:35:53'),
(3, 1, 9, 3, 93.95, '2025-02-11 13:35:53'),
(4, 1, 7, 4, 92.62, '2025-02-11 13:35:53'),
(5, 1, 19, 5, 92.41, '2025-02-11 13:35:53'),
(6, 1, 5, 6, 91.79, '2025-02-11 13:35:53'),
(7, 1, 11, 7, 90.46, '2025-02-11 13:35:53'),
(8, 1, 4, 8, 89.23, '2025-02-11 13:35:53'),
(9, 1, 21, 9, 88.92, '2025-02-11 13:35:53'),
(10, 1, 1, 10, 88.72, '2025-02-11 13:35:53'),
(11, 1, 24, 11, 88.31, '2025-02-11 13:35:53'),
(12, 1, 12, 12, 83.69, '2025-02-11 13:35:53'),
(13, 1, 25, 13, 20.00, '2025-02-11 13:35:53'),
(14, 1, 26, 13, 20.00, '2025-02-11 13:35:53'),
(15, 1, 28, 13, 20.00, '2025-02-11 13:35:53'),
(16, 1, 30, 13, 20.00, '2025-02-11 13:35:53'),
(17, 1, 32, 13, 20.00, '2025-02-11 13:35:53'),
(18, 1, 35, 13, 20.00, '2025-02-11 13:35:53'),
(19, 1, 37, 13, 20.00, '2025-02-11 13:35:53'),
(20, 1, 38, 13, 20.00, '2025-02-11 13:35:53'),
(21, 1, 40, 13, 20.00, '2025-02-11 13:35:53'),
(22, 1, 43, 13, 20.00, '2025-02-11 13:35:53'),
(23, 1, 45, 13, 20.00, '2025-02-11 13:35:53'),
(24, 1, 47, 13, 20.00, '2025-02-11 13:35:53');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_template`
--

CREATE TABLE `cbse_template` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `orientation` varchar(1) NOT NULL DEFAULT 'P',
  `description` varchar(255) NOT NULL,
  `gradeexam_id` int(11) DEFAULT NULL,
  `remarkexam_id` int(11) DEFAULT NULL,
  `is_weightage` varchar(10) NOT NULL,
  `marksheet_type` varchar(50) NOT NULL,
  `created_by` int(11) NOT NULL,
  `header_image` varbinary(500) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `left_logo` varchar(200) DEFAULT NULL,
  `right_logo` varchar(200) DEFAULT NULL,
  `exam_name` varchar(200) DEFAULT NULL,
  `school_name` varchar(200) DEFAULT NULL,
  `exam_center` varchar(200) DEFAULT NULL,
  `session_id` int(11) NOT NULL,
  `left_sign` varchar(200) DEFAULT NULL,
  `middle_sign` varchar(200) DEFAULT NULL,
  `right_sign` varchar(200) DEFAULT NULL,
  `background_img` varchar(200) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `content_footer` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `is_name` int(1) DEFAULT 1,
  `is_father_name` int(1) DEFAULT 1,
  `is_mother_name` int(1) DEFAULT 1,
  `exam_session` int(1) DEFAULT 1,
  `is_admission_no` int(1) DEFAULT 1,
  `is_division` int(1) NOT NULL DEFAULT 1,
  `is_roll_no` int(1) DEFAULT 1,
  `is_photo` int(1) DEFAULT 1,
  `is_class` int(1) NOT NULL DEFAULT 0,
  `is_section` int(1) NOT NULL DEFAULT 0,
  `is_dob` int(1) DEFAULT 1,
  `is_remark` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_template`
--

INSERT INTO `cbse_template` (`id`, `name`, `orientation`, `description`, `gradeexam_id`, `remarkexam_id`, `is_weightage`, `marksheet_type`, `created_by`, `header_image`, `title`, `left_logo`, `right_logo`, `exam_name`, `school_name`, `exam_center`, `session_id`, `left_sign`, `middle_sign`, `right_sign`, `background_img`, `content`, `content_footer`, `date`, `is_name`, `is_father_name`, `is_mother_name`, `exam_session`, `is_admission_no`, `is_division`, `is_roll_no`, `is_photo`, `is_class`, `is_section`, `is_dob`, `is_remark`) VALUES
(1, 'Single Exam  CBSE Without TEM1', 'P', 'The details of Template Term, Code, Description column which you recently added.	', 3, 3, '', 'all_term', 1, NULL, NULL, NULL, NULL, NULL, 'Mount Carmel School', 'Smart School , Delhi', 19, NULL, NULL, NULL, NULL, NULL, '', '2024-12-12', 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1),
(2, 'Multiple Exam Without TEM2', 'P', 'The details of Template Term, Code, Description column which you recently added.	', NULL, NULL, '', '', 1, NULL, NULL, NULL, NULL, NULL, 'Mount cramel', 'delhi', 19, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1),
(3, 'All Term Wise Cbse Tem4', 'L', '-', NULL, NULL, '', '', 1, NULL, NULL, NULL, NULL, NULL, 'mount carmel', '', 19, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(4, 'Monthly Exam Practice Template', 'L', '', NULL, NULL, '', '', 1, NULL, NULL, NULL, NULL, NULL, 'mount carmel', 'delhi', 19, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1),
(5, 'Internal Assessments Test-II Template', 'L', 'Internal Assessments Test-II Template	', NULL, NULL, '', '', 1, NULL, NULL, NULL, NULL, NULL, 'mount carmel', 'delhi', 19, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1),
(6, 'CBSE Monthly Week Test', 'P', '', NULL, NULL, '', '', 1, NULL, NULL, NULL, NULL, NULL, 'mount carmel', 'delhi', 19, NULL, NULL, NULL, NULL, NULL, '', NULL, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `cbse_template_class_sections`
--

CREATE TABLE `cbse_template_class_sections` (
  `id` int(11) NOT NULL,
  `cbse_template_id` int(11) NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_template_class_sections`
--

INSERT INTO `cbse_template_class_sections` (`id`, `cbse_template_id`, `class_section_id`, `created_at`) VALUES
(5, 1, 1, '2024-12-09 10:30:33'),
(6, 1, 2, '2024-12-09 10:30:33'),
(7, 1, 3, '2024-12-09 10:30:33'),
(8, 1, 4, '2024-12-09 10:30:33'),
(9, 2, 6, '2024-12-09 10:31:52'),
(10, 2, 7, '2024-12-09 10:31:52'),
(11, 2, 8, '2024-12-09 10:31:52'),
(12, 2, 9, '2024-12-09 10:31:52'),
(13, 3, 1, '2024-12-09 10:32:43'),
(14, 3, 2, '2024-12-09 10:32:43'),
(15, 4, 6, '2024-12-09 10:33:40'),
(16, 4, 7, '2024-12-09 10:33:40'),
(17, 4, 8, '2024-12-09 10:33:40'),
(18, 4, 9, '2024-12-09 10:33:40'),
(19, 5, 1, '2024-12-09 10:34:32'),
(20, 5, 2, '2024-12-09 10:34:32'),
(21, 5, 3, '2024-12-09 10:34:32'),
(22, 5, 4, '2024-12-09 10:34:32'),
(23, 5, 6, '2024-12-09 10:34:32'),
(24, 5, 7, '2024-12-09 10:34:32'),
(25, 5, 8, '2024-12-09 10:34:32'),
(26, 5, 9, '2024-12-09 10:34:32'),
(27, 6, 1, '2024-12-09 10:35:28'),
(28, 6, 2, '2024-12-09 10:35:28'),
(29, 6, 3, '2024-12-09 10:35:28'),
(30, 6, 4, '2024-12-09 10:35:28'),
(31, 6, 6, '2024-12-09 10:35:28'),
(32, 6, 7, '2024-12-09 10:35:28'),
(33, 6, 8, '2024-12-09 10:35:28'),
(34, 6, 9, '2024-12-09 10:35:28'),
(35, 6, 11, '2024-12-09 10:35:28'),
(36, 6, 12, '2024-12-09 10:35:28'),
(37, 6, 13, '2024-12-09 10:35:28'),
(38, 6, 14, '2024-12-09 10:35:28');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_template_terms`
--

CREATE TABLE `cbse_template_terms` (
  `id` int(11) NOT NULL,
  `cbse_template_id` int(11) NOT NULL,
  `cbse_term_id` int(11) NOT NULL,
  `weightage` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_template_terms`
--

INSERT INTO `cbse_template_terms` (`id`, `cbse_template_id`, `cbse_term_id`, `weightage`, `created_at`) VALUES
(1, 1, 2, 100, '2025-02-11 13:35:35');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_template_term_exams`
--

CREATE TABLE `cbse_template_term_exams` (
  `id` int(11) NOT NULL,
  `cbse_template_term_id` int(11) DEFAULT NULL,
  `cbse_exam_id` int(11) NOT NULL,
  `cbse_template_id` int(11) NOT NULL,
  `weightage` float NOT NULL DEFAULT 100,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_template_term_exams`
--

INSERT INTO `cbse_template_term_exams` (`id`, `cbse_template_term_id`, `cbse_exam_id`, `cbse_template_id`, `weightage`, `created_at`) VALUES
(1, 1, 3, 1, 100, '2025-02-11 13:35:35'),
(2, 1, 4, 1, 100, '2025-02-11 13:35:35'),
(3, 1, 5, 1, 100, '2025-02-11 13:35:35');

-- --------------------------------------------------------

--
-- Table structure for table `cbse_terms`
--

CREATE TABLE `cbse_terms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `term_code` varchar(100) NOT NULL,
  `description` mediumtext NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `cbse_terms`
--

INSERT INTO `cbse_terms` (`id`, `name`, `term_code`, `description`, `created_by`, `created_at`) VALUES
(1, 'Term 3', 'T003', '', 1, '2024-12-09 10:33:02'),
(2, 'Term 2', 'T015', 'An examination is a formal test that you take to show your knowledge or ability in a particular subject, or to obtain a qualification. If you have a medical examination, a doctor looks at your body, feels it, or does simple tests in order to check how healthy you are. ', 1, '2024-12-09 10:33:18'),
(3, 'Term 1', 'T021', 'An examination is a formal test that you take to show your knowledge or ability in a particular subject, or to obtain a qualification. If you have a medical examination, a doctor looks at your body, feels it, or does simple tests in order to check how healthy you are. ', 1, '2024-12-09 10:33:54');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `certificate_name` varchar(100) NOT NULL,
  `certificate_text` text NOT NULL,
  `left_header` varchar(100) NOT NULL,
  `center_header` varchar(100) NOT NULL,
  `right_header` varchar(100) NOT NULL,
  `left_footer` varchar(100) NOT NULL,
  `right_footer` varchar(100) NOT NULL,
  `center_footer` varchar(100) NOT NULL,
  `background_image` varchar(100) DEFAULT NULL,
  `created_for` tinyint(1) NOT NULL COMMENT '1 = staff, 2 = students',
  `status` tinyint(1) NOT NULL,
  `header_height` int(11) NOT NULL,
  `content_height` int(11) NOT NULL,
  `footer_height` int(11) NOT NULL,
  `content_width` int(11) NOT NULL,
  `enable_student_image` tinyint(1) NOT NULL COMMENT '0=no,1=yes',
  `enable_image_height` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `certificate_name`, `certificate_text`, `left_header`, `center_header`, `right_header`, `left_footer`, `right_footer`, `center_footer`, `background_image`, `created_for`, `status`, `header_height`, `content_height`, `footer_height`, `content_width`, `enable_student_image`, `enable_image_height`, `created_at`, `updated_at`) VALUES
(1, 'Sample Transfer Certificate', 'This is certify that <b>[name]</b> has born on [dob]  <br> and have following details [present_address] [guardian] [created_at] [admission_no] [roll_no] [class] [section] [gender] [admission_date] [category] [cast] [father_name] [mother_name] [religion] [email] [phone] .<br>We wish best of luck for future endeavors.', 'Reff. No.....1111111.........', 'To Whomever It May Concern', 'Date: _10__/_10__/__2019__', '.................................<br>admin', '.................................<br>principal', '.................................<br>admin', 'sampletc121.png', 2, 1, 360, 400, 480, 810, 1, 230, '2019-12-21 15:14:34', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `chat_connections`
--

CREATE TABLE `chat_connections` (
  `id` int(11) NOT NULL,
  `chat_user_one` int(11) NOT NULL,
  `chat_user_two` int(11) NOT NULL,
  `ip` varchar(30) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `chat_user_id` int(11) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `time` int(11) NOT NULL,
  `is_first` int(1) DEFAULT 0,
  `is_read` int(1) NOT NULL DEFAULT 0,
  `chat_connection_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_users`
--

CREATE TABLE `chat_users` (
  `id` int(11) NOT NULL,
  `user_type` varchar(20) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `create_staff_id` int(11) DEFAULT NULL,
  `create_student_id` int(11) DEFAULT NULL,
  `is_active` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class` varchar(60) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Class 1', 'no', '2024-11-25 09:57:05', NULL),
(2, 'Class 2', 'no', '2024-11-25 09:57:19', NULL),
(3, 'Class 3', 'no', '2024-11-25 09:57:31', NULL),
(4, 'Class 4', 'no', '2024-11-25 09:57:45', NULL),
(6, 'ICT technician level 5 249', 'no', '2024-12-09 10:44:30', NULL),
(7, 'ICT technician level 6 249', 'no', '2024-12-09 10:44:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `class_sections`
--

CREATE TABLE `class_sections` (
  `id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `class_sections`
--

INSERT INTO `class_sections` (`id`, `class_id`, `section_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'no', '2024-11-25 09:57:05', NULL),
(2, 1, 2, 'no', '2024-11-25 09:57:05', NULL),
(3, 1, 3, 'no', '2024-11-25 09:57:05', NULL),
(4, 1, 4, 'no', '2024-11-25 09:57:05', NULL),
(6, 2, 1, 'no', '2024-11-25 09:57:19', NULL),
(7, 2, 2, 'no', '2024-11-25 09:57:19', NULL),
(8, 2, 3, 'no', '2024-11-25 09:57:19', NULL),
(9, 2, 4, 'no', '2024-11-25 09:57:19', NULL),
(11, 3, 1, 'no', '2024-11-25 09:57:31', NULL),
(12, 3, 2, 'no', '2024-11-25 09:57:31', NULL),
(13, 3, 3, 'no', '2024-11-25 09:57:31', NULL),
(14, 3, 4, 'no', '2024-11-25 09:57:31', NULL),
(16, 4, 1, 'no', '2024-11-25 09:57:45', NULL),
(17, 4, 2, 'no', '2024-11-25 09:57:45', NULL),
(18, 4, 3, 'no', '2024-11-25 09:57:45', NULL),
(19, 4, 4, 'no', '2024-11-25 09:57:45', NULL),
(26, 6, 6, 'no', '2024-12-09 10:44:30', NULL),
(27, 7, 7, 'no', '2024-12-09 10:44:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `class_section_times`
--

CREATE TABLE `class_section_times` (
  `id` int(11) NOT NULL,
  `class_section_id` int(11) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_teacher`
--

CREATE TABLE `class_teacher` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `class_teacher`
--

INSERT INTO `class_teacher` (`id`, `session_id`, `class_id`, `section_id`, `staff_id`) VALUES
(2, 19, 2, 1, 2),
(3, 19, 2, 1, 3),
(4, 19, 2, 1, 5),
(5, 19, 2, 1, 7),
(6, 19, 1, 2, 2),
(7, 19, 1, 2, 3),
(8, 19, 1, 2, 5),
(9, 19, 1, 2, 7),
(10, 19, 3, 1, 2),
(11, 19, 3, 1, 3),
(12, 19, 3, 1, 5),
(13, 19, 3, 1, 7),
(14, 19, 4, 1, 2),
(15, 19, 4, 1, 3),
(16, 19, 4, 1, 5),
(17, 19, 4, 1, 7),
(18, 19, 2, 2, 2),
(19, 19, 3, 3, 5);

-- --------------------------------------------------------

--
-- Table structure for table `complaint`
--

CREATE TABLE `complaint` (
  `id` int(11) NOT NULL,
  `complaint_type` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `description` text NOT NULL,
  `action_taken` varchar(200) NOT NULL,
  `assigned` varchar(50) NOT NULL,
  `note` text NOT NULL,
  `image` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `complaint`
--

INSERT INTO `complaint` (`id`, `complaint_type`, `source`, `name`, `contact`, `email`, `date`, `description`, `action_taken`, `assigned`, `note`, `image`) VALUES
(1, 'Fees', 'Advertisement', 'front officce', '9876543', '', '2024-11-25', '', '', '', '', NULL),
(2, 'Sports	', 'Advertisement', 'Isabelle Madsen	', '987654', '', '2024-11-25', '', '', '', '', NULL),
(3, 'Teacher	', 'Advertisement', 'jemis', '768676788', '', '2024-11-26', '', '', '', '', NULL),
(4, 'Transport', 'Advertisement', '-', '', '', '2024-11-26', '', '', '', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `complaint_type`
--

CREATE TABLE `complaint_type` (
  `id` int(11) NOT NULL,
  `complaint_type` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `complaint_type`
--

INSERT INTO `complaint_type` (`id`, `complaint_type`, `description`, `created_at`) VALUES
(1, 'Fees', '', '2024-11-25 10:17:40'),
(2, 'Study', '', '2024-11-25 10:17:45'),
(3, 'Teacher	', '', '2024-11-25 10:17:52'),
(4, 'Sports	', '', '2024-11-25 10:17:59'),
(5, 'Transport', '', '2024-11-25 10:18:06');

-- --------------------------------------------------------

--
-- Table structure for table `conferences`
--

CREATE TABLE `conferences` (
  `id` int(11) NOT NULL,
  `purpose` varchar(20) NOT NULL DEFAULT 'class',
  `staff_id` int(11) DEFAULT NULL,
  `created_id` int(10) NOT NULL,
  `title` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `class_id` int(10) DEFAULT NULL,
  `section_id` int(10) DEFAULT NULL,
  `session_id` int(10) NOT NULL,
  `host_video` int(1) NOT NULL DEFAULT 1,
  `client_video` int(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `timezone` varchar(100) DEFAULT NULL,
  `return_response` text DEFAULT NULL,
  `api_type` varchar(30) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conferences_history`
--

CREATE TABLE `conferences_history` (
  `id` int(11) NOT NULL,
  `conference_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `total_hit` int(10) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conference_sections`
--

CREATE TABLE `conference_sections` (
  `id` int(11) NOT NULL,
  `conference_id` int(11) DEFAULT NULL,
  `cls_section_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conference_staff`
--

CREATE TABLE `conference_staff` (
  `id` int(11) NOT NULL,
  `conference_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contents`
--

CREATE TABLE `contents` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_public` varchar(10) DEFAULT 'No',
  `class_id` int(11) DEFAULT NULL,
  `cls_sec_id` int(10) DEFAULT NULL,
  `file` varchar(250) DEFAULT NULL,
  `date` date NOT NULL,
  `note` text DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content_for`
--

CREATE TABLE `content_for` (
  `id` int(11) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content_types`
--

CREATE TABLE `content_types` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` int(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `content_types`
--

INSERT INTO `content_types` (`id`, `name`, `description`, `is_active`, `created_at`) VALUES
(1, 'Exam material', '', 1, '2024-11-25 11:37:03'),
(2, 'Other Downloads', '', 1, '2024-11-25 11:37:08'),
(3, 'Study Material', '', 1, '2024-11-25 11:37:15'),
(4, 'Syllabus', '', 1, '2024-11-25 11:37:20'),
(5, 'Assignment', '', 1, '2024-11-25 11:37:25');

-- --------------------------------------------------------

--
-- Table structure for table `course_category`
--

CREATE TABLE `course_category` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `is_active` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `course_category`
--

INSERT INTO `course_category` (`id`, `category_name`, `slug`, `is_active`) VALUES
(1, 'Personal Development	', NULL, NULL),
(2, 'Health & Fitness Courses	', NULL, NULL),
(3, 'Network & Security Course	', NULL, NULL),
(4, 'Lifestyle course	', NULL, NULL),
(5, 'UPGRADE SKILL	', NULL, NULL),
(6, 'Business Marketing	', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_lesson_quiz_order`
--

CREATE TABLE `course_lesson_quiz_order` (
  `id` int(11) NOT NULL,
  `type` varchar(10) DEFAULT NULL,
  `course_section_id` int(11) DEFAULT NULL,
  `lesson_quiz_id` int(11) DEFAULT NULL,
  `order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `course_lesson_quiz_order`
--

INSERT INTO `course_lesson_quiz_order` (`id`, `type`, `course_section_id`, `lesson_quiz_id`, `order`) VALUES
(1, 'lesson', 1, 1, NULL),
(2, 'lesson', 1, 2, NULL),
(3, 'quiz', 1, 1, NULL),
(4, 'lesson', 3, 3, NULL),
(5, 'lesson', 3, 4, NULL),
(6, 'quiz', 3, 2, NULL),
(7, 'lesson', 4, 5, NULL),
(8, 'lesson', 4, 6, NULL),
(9, 'quiz', 4, 3, NULL),
(10, 'lesson', 5, 7, NULL),
(11, 'lesson', 5, 8, NULL),
(12, 'quiz', 5, 4, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_progress`
--

CREATE TABLE `course_progress` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `guest_id` int(1) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `course_section_id` int(11) DEFAULT NULL,
  `lesson_quiz_id` int(11) DEFAULT NULL,
  `lesson_quiz_type` int(11) DEFAULT NULL COMMENT '1 lesson, 2 quiz'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_quiz_answer`
--

CREATE TABLE `course_quiz_answer` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `course_quiz_id` int(11) DEFAULT NULL,
  `course_quiz_question_id` int(11) DEFAULT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_quiz_question`
--

CREATE TABLE `course_quiz_question` (
  `id` int(11) NOT NULL,
  `course_quiz_id` int(11) DEFAULT NULL,
  `question` text DEFAULT NULL,
  `option_1` varchar(255) DEFAULT NULL,
  `option_2` varchar(255) DEFAULT NULL,
  `option_3` varchar(255) DEFAULT NULL,
  `option_4` varchar(255) DEFAULT NULL,
  `option_5` varchar(255) DEFAULT NULL,
  `correct_answer` varchar(255) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `course_quiz_question`
--

INSERT INTO `course_quiz_question` (`id`, `course_quiz_id`, `question`, `option_1`, `option_2`, `option_3`, `option_4`, `option_5`, `correct_answer`, `created_date`) VALUES
(1, 1, 'Who discovered the rotating magnetic field?', 'Nikola Tesla', 'Paul Langevin', 'Pierre Curie', 'All', 'None', 'option_1', '2024-12-09 09:57:30'),
(2, 1, 'What is the change in the direction of a wave with a change in the medium called?', 'refraction', 'reflection', 'resonance', 'rotation', 'None', 'option_1', '2024-12-09 09:58:22'),
(3, 2, 'What is the sum of 130+125+191?', '335', '446', '456', '426', '152', 'option_2', '2024-12-09 10:06:28'),
(4, 2, '50 times of 8 is equal to', '80', '400', '4000', '200', '2000', 'option_2', '2024-12-09 10:06:28'),
(5, 3, 'What is the sum of 555 + 555 + 123?', '1123', '1120', '115', '1233', '1102', 'option_4', '2024-12-09 10:13:29'),
(6, 3, 'What is 15 times 12?', '164', '180', '162', '182', '172', 'option_2', '2024-12-09 10:13:29'),
(7, 4, 'To whom should a presentation be aimed?', 'The entire audience', 'The highest authority in the room, regardless of where they are', 'The people in the closest rows', 'Your best friend in the room', 'All', 'option_2', '2024-12-09 10:19:04'),
(8, 4, 'Where should you look while presenting?', 'At the board -- that\'s where the audience is looking', 'At your notes so you get the info correct', 'In the eyes of random people in your audience', 'At the chins of individuals in your audience', 'None', 'option_2', '2024-12-09 10:19:04');

-- --------------------------------------------------------

--
-- Table structure for table `course_rating`
--

CREATE TABLE `course_rating` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `course_id` int(11) NOT NULL,
  `rating` varchar(200) NOT NULL,
  `review` text NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `symbol` varchar(10) DEFAULT NULL,
  `base_price` varchar(10) NOT NULL DEFAULT '1',
  `is_active` int(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `short_name`, `symbol`, `base_price`, `is_active`, `created_at`) VALUES
(1, 'AED', 'AED', 'AEDf', '1', 0, '2022-12-30 06:19:15'),
(2, 'AFN', 'AFN', '؋', '140', 0, '2024-11-25 07:32:21'),
(3, 'ALL', 'ALL', 'ALL', '1', 0, '2022-12-30 06:19:22'),
(4, 'AMD', 'AMD', 'AMD', '1', 0, '2022-07-22 10:55:15'),
(5, 'ANG', 'ANG', 'ANG', '1', 0, '2022-07-22 10:55:15'),
(6, 'AOA', 'AOA', 'AOA', '1', 0, '2022-07-22 10:55:15'),
(7, 'ARS', 'ARS', 'ARS', '1', 0, '2022-07-22 10:55:15'),
(8, 'AUD', 'AUD', 'AUD', '1', 0, '2022-07-22 10:55:15'),
(9, 'AWG', 'AWG', 'AWG', '1', 0, '2022-07-22 10:55:15'),
(10, 'AZN', 'AZN', 'AZN', '1', 0, '2022-07-22 10:55:15'),
(11, 'BAM', 'BAM', 'BAM', '1', 0, '2022-07-22 10:55:15'),
(12, 'BAM', 'BAM', 'BAM', '1', 0, '2022-07-22 10:55:15'),
(13, 'BDT', 'BDT', 'BDT', '1', 0, '2022-07-22 10:55:15'),
(14, 'BGN', 'BGN', 'BGN', '1', 0, '2022-07-22 10:55:15'),
(15, 'BHD', 'BHD', 'BHD', '1', 0, '2022-07-22 10:55:15'),
(16, 'BIF', 'BIF', 'BIF', '1', 0, '2022-07-22 10:55:15'),
(17, 'BMD', 'BMD', 'BMD', '1', 0, '2022-07-22 10:55:15'),
(18, 'BND', 'BND', 'BND', '1', 0, '2022-07-22 10:55:15'),
(19, 'BOB', 'BOB', 'BOB', '1', 0, '2022-12-30 06:19:29'),
(20, 'BOV', 'BOV', 'BOV', '1500', 0, '2024-11-25 07:33:02'),
(21, 'BRL', 'BRL', 'BRL', '1', 0, '2022-07-22 10:55:15'),
(22, 'BSD', 'BSD', 'BSD', '640ee', 0, '2024-11-25 07:33:08'),
(23, 'BTN', 'BTN', 'BTN', '4', 0, '2024-11-25 07:33:14'),
(24, 'BWP', 'BWP', 'BWP', '1', 0, '2022-07-22 10:55:15'),
(25, 'BYN', 'BYN', 'BYN', '1', 0, '2022-07-22 10:55:15'),
(26, 'BYR', 'BYR', 'BYR', '1', 0, '2022-07-22 10:55:15'),
(27, 'BZD', 'BZD', 'BZD', '1', 0, '2022-07-22 10:55:15'),
(28, 'CAD', 'CAD', 'CAD', '1', 0, '2022-07-22 10:55:15'),
(29, 'CDF', 'CDF', 'CDF', '1', 0, '2022-07-22 10:55:15'),
(30, 'CHE', 'CHE', 'CHE', '1', 0, '2022-07-22 10:55:15'),
(31, 'CHF', 'CHF', 'CHF', '1', 0, '2022-07-22 10:55:15'),
(32, 'CHW', 'CHW', 'CHW', '1', 0, '2022-07-22 10:55:15'),
(33, 'CLF', 'CLF', 'CLF', '1', 0, '2022-07-22 10:55:15'),
(34, 'CLP', 'CLP', 'CLP', '1', 0, '2022-07-22 10:55:15'),
(35, 'CNY', 'CNY', 'CNY', '1', 0, '2022-07-22 10:55:15'),
(36, 'COP', 'COP', 'COP', '1', 0, '2022-07-22 10:55:15'),
(37, 'COU', 'COU', 'COU', '1', 0, '2022-07-22 10:55:15'),
(38, 'CRC', 'CRC', 'CRC', '1', 0, '2022-07-22 10:55:15'),
(39, 'CUC', 'CUC', 'CUC', '1', 0, '2022-07-22 10:55:15'),
(40, 'CUP', 'CUP', 'CUP', '1', 0, '2022-07-22 10:55:15'),
(41, 'CVE', 'CVE', 'CVE', '1', 0, '2022-07-22 10:55:15'),
(42, 'CZK', 'CZK', 'CZK', '1', 0, '2022-07-22 10:55:15'),
(43, 'DJF', 'DJF', 'DJF', '1', 0, '2022-07-22 10:55:15'),
(44, 'DKK', 'DKK', 'DKK', '1', 0, '2022-07-22 10:55:15'),
(45, 'DOP', 'DOP', 'DOP', '1', 0, '2022-07-22 10:55:15'),
(46, 'DZD', 'DZD', 'DZD', '1', 0, '2022-07-22 10:55:15'),
(47, 'EGP', 'EGP', 'EGP', '1', 0, '2022-07-22 10:55:15'),
(48, 'ERN', 'ERN', 'ERN', '1', 0, '2022-07-22 10:55:15'),
(49, 'ETB', 'ETB', 'ETB', '1', 0, '2022-07-22 10:55:15'),
(50, 'EUR', 'EUR', '€', '90', 1, '2024-11-25 07:33:32'),
(51, 'FJD', 'FJD', 'FJD', '1', 0, '2022-07-22 10:55:15'),
(52, 'FKP', 'FKP', 'FKP', '1', 0, '2022-07-22 10:55:15'),
(53, 'GBP', 'GBP', '£', '1', 0, '2022-12-30 06:20:29'),
(54, 'GEL', 'GEL', 'GEL', '1', 0, '2022-07-22 10:55:15'),
(55, 'GHS', 'GHS', 'GHS', '1', 0, '2022-07-22 10:55:15'),
(56, 'GIP', 'GIP', 'GIP', '1', 0, '2022-07-22 10:55:15'),
(57, 'GMD', 'GMD', 'GMD', '1', 0, '2022-07-22 10:55:15'),
(58, 'GNF', 'GNF', 'GNF', '1', 0, '2022-07-22 10:55:15'),
(59, 'GTQ', 'GTQ', 'GTQ', '1', 0, '2022-07-22 10:55:15'),
(60, 'GYD', 'GYD', 'GYD', '1', 0, '2022-07-22 10:55:15'),
(61, 'HKD', 'HKD', 'HKD', '1', 0, '2022-07-22 10:55:15'),
(62, 'HNL', 'HNL', 'HNL', '1', 0, '2022-07-22 10:55:15'),
(63, 'HRK', 'HRK', 'HRK', '1', 0, '2022-07-22 10:55:15'),
(64, 'HTG', 'HTG', 'HTG', '1', 0, '2022-07-22 10:55:15'),
(65, 'HUF', 'HUF', 'HUF', '1', 0, '2022-07-22 10:55:15'),
(66, 'IDR', 'IDR', 'IDR', '1', 0, '2022-12-30 06:20:34'),
(67, 'ILS', 'ILS', 'ILS', '1', 0, '2022-07-22 10:55:15'),
(68, 'INR', 'INR', '₹', '70', 1, '2024-11-25 07:33:51'),
(69, 'IQD', 'IQD', 'IQD', '1', 0, '2022-07-22 10:55:15'),
(70, 'IRR', 'IRR', 'IRR', '1', 0, '2022-07-22 10:55:15'),
(71, 'ISK', 'ISK', 'ISK', '1', 0, '2022-07-22 10:55:15'),
(72, 'JMD', 'JMD', 'JMD', '1', 0, '2022-07-22 10:55:15'),
(73, 'JOD', 'JOD', 'JOD', '1', 0, '2022-07-22 10:55:15'),
(74, 'JPY', 'JPY', 'JPY', '1', 0, '2022-12-30 06:19:56'),
(75, 'KES', 'KES', 'KES', '1', 0, '2022-07-22 10:55:15'),
(76, 'KGS', 'KGS', 'KGS', '1', 0, '2022-07-22 10:55:15'),
(77, 'KHR', 'KHR', 'KHR', '1', 0, '2022-07-22 10:55:15'),
(78, 'KMF', 'KMF', 'KMF', '1', 0, '2022-07-22 10:55:15'),
(79, 'KPW', 'KPW', 'KPW', '1', 0, '2022-07-22 10:55:15'),
(80, 'KRW', 'KRW', 'KRW', '1', 0, '2022-07-22 10:55:15'),
(81, 'KWD', 'KWD', 'KWD', '1', 0, '2022-07-22 10:55:15'),
(82, 'KYD', 'KYD', 'KYD', '1', 0, '2022-07-22 10:55:15'),
(83, 'KZT', 'KZT', 'KZT', '1', 0, '2022-07-22 10:55:15'),
(84, 'LAK', 'LAK', 'LAK', '1', 0, '2022-07-22 10:55:15'),
(85, 'LBP', 'LBP', 'LBP', '1', 0, '2022-07-22 10:55:15'),
(86, 'LKR', 'LKR', 'LKR', '1', 0, '2022-07-22 10:55:15'),
(87, 'LRD', 'LRD', 'LRD', '1', 0, '2022-07-22 10:55:15'),
(88, 'LSL', 'LSL', 'LSL', '1', 0, '2022-07-22 10:55:15'),
(89, 'LYD', 'LYD', 'LYD', '1', 0, '2022-07-22 10:55:15'),
(90, 'MAD', 'MAD', 'MAD', '1', 0, '2022-07-22 10:55:15'),
(91, 'MDL', 'MDL', 'MDL', '1', 0, '2022-07-22 10:55:15'),
(92, 'MGA', 'MGA', 'MGA', '1', 0, '2022-07-22 10:55:15'),
(93, 'MKD', 'MKD', 'MKD', '1', 0, '2022-07-22 10:55:15'),
(94, 'MMK', 'MMK', 'MMK', '1', 0, '2022-07-22 10:55:15'),
(95, 'MNT', 'MNT', 'MNT', '1', 0, '2022-07-22 10:55:15'),
(96, 'MOP', 'MOP', 'MOP', '1', 0, '2022-07-22 10:55:15'),
(97, 'MRO', 'MRO', 'MRO', '1', 0, '2022-07-22 10:55:15'),
(98, 'MUR', 'MUR', 'MUR', '1', 0, '2022-07-22 10:55:15'),
(99, 'MVR', 'MVR', 'MVR', '1', 0, '2022-07-22 10:55:15'),
(100, 'MWK', 'MWK', 'MWK', '1', 0, '2022-07-22 10:55:15'),
(101, 'MXN', 'MXN', 'MXN', '1', 0, '2022-07-22 10:55:15'),
(102, 'MXV', 'MXV', 'MXV', '1', 0, '2022-07-22 10:55:15'),
(103, 'MYR', 'MYR', 'MYR', '1', 0, '2022-07-22 10:55:15'),
(104, 'MZN', 'MZN', 'MZN', '1', 0, '2022-07-22 10:55:15'),
(105, 'NAD', 'NAD', 'NAD', '1', 0, '2022-07-30 09:32:37'),
(106, 'NGN', 'NGN', 'NGN', '1', 1, '2022-12-30 06:20:42'),
(107, 'NIO', 'NIO', 'NIO', '1', 0, '2022-07-22 10:55:15'),
(108, 'NOK', 'NOK', 'NOK', '1', 0, '2022-07-22 10:55:15'),
(109, 'NPR', 'NPR', 'NPR', '1', 0, '2022-07-22 10:55:15'),
(110, 'NZD', 'NZD', 'NZD', '1', 0, '2022-07-22 10:55:15'),
(111, 'OMR', 'OMR', 'OMR', '1', 0, '2022-07-22 10:55:15'),
(112, 'PAB', 'PAB', 'PAB', '1', 0, '2022-07-22 10:55:15'),
(113, 'PEN', 'PEN', 'PEN', '1', 0, '2022-07-22 10:55:15'),
(114, 'PGK', 'PGK', 'PGK', '1', 0, '2022-07-22 10:55:15'),
(115, 'PHP', 'PHP', 'PHP', '1', 0, '2022-07-22 10:55:15'),
(116, 'PKR', 'PKR', 'PKR', '1', 0, '2022-12-30 06:20:19'),
(117, 'PLN', 'PLN', 'PLN', '1', 0, '2022-07-22 10:55:15'),
(118, 'PYG', 'PYG', 'PYG', '1', 0, '2022-07-22 10:55:15'),
(119, 'QAR', 'QAR', 'QAR', '1', 0, '2022-07-22 10:55:15'),
(120, 'RON', 'RON', 'RON', '1', 0, '2022-07-22 10:55:15'),
(121, 'RSD', 'RSD', 'RSD', '1', 0, '2022-07-22 10:55:15'),
(122, 'RUB', 'RUB', 'RUB', '60', 1, '2024-11-25 07:34:14'),
(123, 'RWF', 'RWF', 'RWF', '1', 0, '2022-07-22 10:55:15'),
(124, 'SAR', 'SAR', 'SAR', '1', 0, '2022-07-22 10:55:15'),
(125, 'SBD', 'SBD', 'SBD', '1', 0, '2022-07-22 10:55:15'),
(126, 'SCR', 'SCR', 'SCR', '1', 0, '2022-07-22 10:55:15'),
(127, 'SDG', 'SDG', 'SDG', '1', 0, '2022-07-22 10:55:15'),
(128, 'SEK', 'SEK', 'SEK', '1', 0, '2022-07-22 10:55:15'),
(129, 'SGD', 'SGD', 'SGD', '1', 0, '2022-07-22 10:55:15'),
(130, 'SHP', 'SHP', 'SHP', '1', 0, '2022-07-22 10:55:15'),
(131, 'SLL', 'SLL', 'SLL', '1', 0, '2022-07-22 10:55:15'),
(132, 'SOS', 'SOS', 'SOS', '1', 0, '2022-07-22 10:55:15'),
(133, 'SRD', 'SRD', 'SRD', '1', 0, '2022-07-22 10:55:15'),
(134, 'SSP', 'SSP', 'SSP', '1', 0, '2022-07-22 10:55:15'),
(135, 'STD', 'STD', 'STD', '1', 0, '2022-07-22 10:55:15'),
(136, 'SVC', 'SVC', 'SVC', '1', 0, '2022-07-22 10:55:15'),
(137, 'SYP', 'SYP', 'SYP', '1', 0, '2022-07-22 10:55:15'),
(138, 'SZL', 'SZL', 'SZL', '1', 0, '2022-07-22 10:55:15'),
(139, 'THB', 'THB', 'THB', '1', 0, '2022-07-22 10:55:15'),
(140, 'TJS', 'TJS', 'TJS', '1', 0, '2022-07-22 10:55:15'),
(141, 'TMT', 'TMT', 'TMT', '1', 0, '2022-07-22 10:55:15'),
(142, 'TND', 'TND', 'TND', '1', 0, '2022-07-22 10:55:15'),
(143, 'TOP', 'TOP', 'TOP', '1', 0, '2022-07-22 10:55:15'),
(144, 'TRY', 'TRY', 'TRY', '1', 0, '2022-07-22 10:55:15'),
(145, 'TTD', 'TTD', 'TTD', '1', 0, '2022-07-22 10:55:15'),
(146, 'TWD', 'TWD', 'TWD', '1', 0, '2022-07-22 10:55:15'),
(147, 'TZS', 'TZS', 'TZS', '1', 0, '2022-07-22 10:55:15'),
(148, 'UAH', 'UAH', 'UAH', '1', 0, '2022-07-22 10:55:15'),
(149, 'UGX', 'UGX', 'UGX', '1', 0, '2022-07-22 10:55:15'),
(150, 'USD', 'USD', '$', '1', 1, '2022-07-22 10:55:15'),
(151, 'USN', 'USN', 'USN', '10', 0, '2024-11-25 07:34:29'),
(152, 'UYI', 'UYI', 'UYI', '1', 0, '2022-07-22 10:55:15'),
(153, 'UYU', 'UYU', 'UYU', '1', 0, '2022-07-22 10:55:15'),
(154, 'UZS', 'UZS', 'UZS', '1', 0, '2022-07-22 10:55:15'),
(155, 'VEF', 'VEF', 'VEF', '1', 0, '2022-07-22 10:55:15'),
(156, 'VND', 'VND', 'VND', '1', 0, '2022-07-22 10:55:15'),
(157, 'VUV', 'VUV', 'VUV', '1', 0, '2022-07-22 10:55:15'),
(158, 'WST', 'WST', 'WST', '1', 0, '2022-07-22 10:55:15'),
(159, 'XAF', 'XAF', 'XAF', '1', 0, '2022-07-22 10:55:15'),
(160, 'XAG', 'XAG', 'XAG', '1', 0, '2022-07-22 10:55:15'),
(161, 'XAU', 'XAU', 'XAU', '1', 0, '2022-07-22 10:55:15'),
(162, 'XBA', 'XBA', 'XBA', '1', 0, '2022-07-22 10:55:15'),
(163, 'XBB', 'XBB', 'XBB', '1', 0, '2022-07-22 10:55:15'),
(164, 'XBC', 'XBC', 'XBC', '1', 0, '2022-07-22 10:55:15'),
(165, 'XBD', 'XBD', 'XBD', '1', 0, '2022-07-22 10:55:15'),
(166, 'XCD', 'XCD', 'XCD', '1', 0, '2022-07-22 10:55:15'),
(167, 'XDR', 'XDR', 'XDR', '1', 0, '2022-07-22 10:55:15'),
(168, 'XOF', 'XOF', 'XOF', '1', 0, '2022-07-22 10:55:15'),
(169, 'XPD', 'XPD', 'XPD', '1', 0, '2022-07-22 10:55:15'),
(170, 'XPF', 'XPF', 'XPF', '1', 0, '2022-07-22 10:55:15'),
(171, 'XPT', 'XPT', 'XPT', '1', 0, '2022-07-22 10:55:15'),
(172, 'XSU', 'XSU', 'XSU', '1', 0, '2022-07-22 10:55:15'),
(173, 'XTS', 'XTS', 'XTS', '1', 0, '2022-07-22 10:55:15'),
(174, 'XUA', 'XUA', 'XUA', '1', 0, '2022-07-22 10:55:15'),
(175, 'XXX', 'XXX', 'XXX', '1', 0, '2022-07-22 10:55:15'),
(176, 'YER', 'YER', 'YER', '1', 0, '2022-07-22 10:55:15'),
(177, 'ZAR', 'ZAR', 'ZAR', '1', 0, '2022-12-30 06:20:07'),
(178, 'ZMW', 'ZMW', 'ZMW', '1', 0, '2022-07-30 07:34:00'),
(179, 'ZWL', 'ZWL', 'ZWL', '1', 0, '2022-07-22 10:55:15');

-- --------------------------------------------------------

--
-- Table structure for table `custom_fields`
--

CREATE TABLE `custom_fields` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `belong_to` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `bs_column` int(10) DEFAULT NULL,
  `validation` int(11) DEFAULT 0,
  `field_values` text DEFAULT NULL,
  `show_table` varchar(100) DEFAULT NULL,
  `visible_on_table` int(11) NOT NULL,
  `weight` int(11) DEFAULT NULL,
  `is_active` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `custom_fields`
--

INSERT INTO `custom_fields` (`id`, `name`, `belong_to`, `type`, `bs_column`, `validation`, `field_values`, `show_table`, `visible_on_table`, `weight`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Medical History', 'students', 'textarea', 12, 0, '', NULL, 0, NULL, 0, '2024-11-25 07:38:48', NULL),
(2, 'NIN', 'staff', 'input', 12, 1, '', NULL, 1, NULL, 0, '2024-11-25 07:39:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `custom_field_values`
--

CREATE TABLE `custom_field_values` (
  `id` int(11) NOT NULL,
  `belong_table_id` int(11) DEFAULT NULL,
  `custom_field_id` int(11) DEFAULT NULL,
  `field_value` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `custom_field_values`
--

INSERT INTO `custom_field_values` (`id`, `belong_table_id`, `custom_field_id`, `field_value`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'No History', '2024-12-21 08:11:15', NULL),
(2, 2, 1, '', '2024-11-25 10:11:26', NULL),
(3, 2, 2, '34567898765', '2024-11-25 11:22:19', NULL),
(4, 3, 2, '34567856', '2024-11-25 11:24:02', NULL),
(5, 4, 2, 'ALWPG5809L', '2024-11-25 11:25:47', NULL),
(6, 5, 2, 'AMB14ER', '2024-11-25 11:28:41', NULL),
(7, 3, 1, '', '2024-11-26 07:51:11', NULL),
(8, 4, 1, '', '2024-11-26 07:54:34', NULL),
(9, 5, 1, '', '2024-11-26 08:00:57', NULL),
(10, 6, 1, '', '2024-11-26 08:01:34', NULL),
(11, 7, 1, '', '2024-11-26 08:04:16', NULL),
(12, 8, 1, '', '2024-11-26 08:07:02', NULL),
(13, 9, 1, '', '2024-11-26 08:07:41', NULL),
(14, 10, 1, '', '2024-11-26 08:08:08', NULL),
(15, 11, 1, '', '2024-11-26 08:11:40', NULL),
(16, 12, 1, '', '2024-11-26 08:13:14', NULL),
(17, 13, 1, '', '2024-11-26 08:13:18', NULL),
(18, 14, 1, '', '2024-11-26 08:15:39', NULL),
(19, 15, 1, '', '2024-11-26 08:16:00', NULL),
(20, 16, 1, '', '2024-11-26 08:18:27', NULL),
(21, 17, 1, '', '2024-11-26 08:20:48', NULL),
(22, 18, 1, '', '2024-11-26 08:22:14', NULL),
(23, 19, 1, '', '2024-11-26 08:23:35', NULL),
(24, 20, 1, '', '2024-11-26 08:23:37', NULL),
(25, 21, 1, '', '2024-11-26 08:24:46', NULL),
(26, 22, 1, '', '2024-11-26 08:24:56', NULL),
(27, 23, 1, '', '2024-11-26 08:25:59', NULL),
(28, 24, 1, '', '2024-11-26 08:26:01', NULL),
(29, 25, 1, '', '2024-11-26 08:28:30', NULL),
(30, 26, 1, '', '2024-11-26 08:30:42', NULL),
(31, 27, 1, '', '2024-11-26 08:30:54', NULL),
(32, 28, 1, '', '2024-11-26 08:31:43', NULL),
(33, 29, 1, '', '2024-11-26 08:32:11', NULL),
(34, 30, 1, '', '2024-11-26 08:32:37', NULL),
(35, 31, 1, '', '2024-11-26 08:33:20', NULL),
(36, 32, 1, '', '2024-11-26 08:34:17', NULL),
(37, 33, 1, '', '2024-11-26 08:34:31', NULL),
(38, 34, 1, '', '2024-11-26 08:35:49', NULL),
(39, 35, 1, '', '2024-11-26 08:35:57', NULL),
(40, 36, 1, '', '2024-11-26 08:37:03', NULL),
(41, 37, 1, '', '2024-11-26 08:37:21', NULL),
(42, 38, 1, '', '2024-11-26 08:38:47', NULL),
(43, 39, 1, '', '2024-11-26 08:39:00', NULL),
(44, 40, 1, '', '2024-11-26 08:39:55', NULL),
(45, 41, 1, '', '2024-11-26 08:39:58', NULL),
(46, 42, 1, '', '2024-11-26 08:41:31', NULL),
(47, 43, 1, '', '2024-11-26 08:42:34', NULL),
(48, 44, 1, '', '2024-11-26 08:42:38', NULL),
(49, 45, 1, '', '2024-11-26 08:43:50', NULL),
(50, 46, 1, '', '2024-11-26 08:44:01', NULL),
(51, 47, 1, '', '2024-11-26 08:45:15', NULL),
(52, 48, 1, '', '2024-11-26 08:46:09', NULL),
(53, 6, 2, 'ALWPG5809L', '2024-12-09 09:24:12', NULL),
(54, 1, 2, 'AWP9098', '2024-12-09 09:28:29', NULL),
(55, 7, 2, 'RLWEG5809L', '2024-12-09 09:30:42', NULL),
(56, 8, 2, 'ALWPG5825H', '2024-12-09 09:34:30', NULL),
(57, 9, 2, 'OLUOPG5809', '2024-12-09 09:37:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `daily_assignment`
--

CREATE TABLE `daily_assignment` (
  `id` int(11) NOT NULL,
  `student_session_id` int(11) NOT NULL,
  `subject_group_subject_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `evaluated_by` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `evaluation_date` date DEFAULT NULL,
  `remark` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `daily_assignment`
--

INSERT INTO `daily_assignment` (`id`, `student_session_id`, `subject_group_subject_id`, `title`, `description`, `attachment`, `evaluated_by`, `date`, `evaluation_date`, `remark`, `created_at`) VALUES
(1, 1, 1, 'test', 'tesy', '1.jpg', NULL, '2024-12-23', NULL, '', '2024-12-23 11:33:26'),
(2, 1, 2, 'bbvv', 'g h. t .h trd d ', '2.jpg', NULL, '2024-12-24', NULL, '', '2024-12-23 11:34:20');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `department_name` varchar(200) NOT NULL,
  `is_active` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `department_name`, `is_active`) VALUES
(1, 'Academic', 'yes'),
(2, 'Library', 'yes'),
(3, 'Sports', 'yes'),
(4, 'Science', 'yes'),
(5, 'Commerce', 'yes'),
(6, 'Arts', 'yes'),
(7, 'Exam', 'yes'),
(8, 'Admin', 'yes'),
(9, 'Finance', 'yes'),
(10, 'Maths', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `disable_reason`
--

CREATE TABLE `disable_reason` (
  `id` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `disable_reason`
--

INSERT INTO `disable_reason` (`id`, `reason`, `created_at`) VALUES
(1, 'Regular Absent', '2024-11-25 10:08:55'),
(2, 'Fees Not Paid', '2024-11-25 10:09:03');

-- --------------------------------------------------------

--
-- Table structure for table `dispatch_receive`
--

CREATE TABLE `dispatch_receive` (
  `id` int(11) NOT NULL,
  `reference_no` varchar(50) NOT NULL,
  `to_title` varchar(100) NOT NULL,
  `type` varchar(10) NOT NULL,
  `address` varchar(500) NOT NULL,
  `note` varchar(500) NOT NULL,
  `from_title` varchar(200) NOT NULL,
  `date` date DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `dispatch_receive`
--

INSERT INTO `dispatch_receive` (`id`, `reference_no`, `to_title`, `type`, `address`, `note`, `from_title`, `date`, `image`, `created_at`) VALUES
(1, '9876', '', 'receive', '', '', 'The road', '2024-11-25', NULL, '2024-11-25 10:35:28'),
(2, '654', '', 'receive', 'F-303', '', 'The fish', '2024-11-25', NULL, '2024-11-25 10:35:45'),
(3, '6766', 'CBSE New Syllabus	', 'receive', '', '', 'CBSE New Syllabus	', '2024-11-25', NULL, '2024-11-25 10:36:43'),
(4, '', 'Government school', 'receive', '', '', 'Student Health Checkup	', '2024-11-25', NULL, '2024-11-25 10:37:08'),
(5, '6766', 'CBSE New Syllabus	', 'dispatch', '', '', '', '2024-11-25', NULL, '2024-11-25 10:37:39'),
(6, '456', 'Exam Center Office	', 'dispatch', '', '', '', '2024-11-25', NULL, '2024-11-25 10:37:53'),
(7, '567', 'School Board Office	', 'dispatch', '', '', '', '2024-11-25', NULL, '2024-11-25 10:38:10');

-- --------------------------------------------------------

--
-- Table structure for table `email_attachments`
--

CREATE TABLE `email_attachments` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `directory` varchar(255) NOT NULL,
  `attachment` varchar(255) NOT NULL,
  `attachment_name` varchar(200) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_config`
--

CREATE TABLE `email_config` (
  `id` int(11) UNSIGNED NOT NULL,
  `email_type` varchar(100) DEFAULT NULL,
  `smtp_server` varchar(100) DEFAULT NULL,
  `smtp_port` varchar(100) DEFAULT NULL,
  `smtp_username` varchar(100) DEFAULT NULL,
  `smtp_password` varchar(100) DEFAULT NULL,
  `ssl_tls` varchar(100) DEFAULT NULL,
  `smtp_auth` varchar(10) NOT NULL,
  `api_key` varchar(255) DEFAULT NULL,
  `api_secret` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `email_config`
--

INSERT INTO `email_config` (`id`, `email_type`, `smtp_server`, `smtp_port`, `smtp_username`, `smtp_password`, `ssl_tls`, `smtp_auth`, `api_key`, `api_secret`, `region`, `is_active`, `created_at`) VALUES
(1, 'sendmail', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, 'yes', '2020-02-28 13:46:03');

-- --------------------------------------------------------

--
-- Table structure for table `email_template`
--

CREATE TABLE `email_template` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `email_template`
--

INSERT INTO `email_template` (`id`, `title`, `message`, `created_at`) VALUES
(1, 'Sports Day Events', '<p>Games that are played on school sports days can be wide and varied. They can include straightforward sprints and longer races for all age groups as well as egg and spoon races. </p>', '2024-11-25'),
(2, 'National Republic Day', '<p>India celebrated its 73rd National Republic Day on the 26th of January, 2022. The constitution of India was adopted on the 26th of November, 1949 with Dr. B. R. Ambedkar as the Chairman of the Drafting Committee.</p>', '2024-11-25'),
(3, 'Annual Day Celebration', '<p>A day in School – In this theme the program can showcase, what all goes in school. The ringing of bell, the class, the love of teachers, happy-go-lucky punishments, all pranks, PTM, sports etc.</p>', '2024-11-25'),
(4, 'New Academic Session(2023-24)', '<p>Dear Parents,</p>\r\n\r\n<p>         Registration for Admission from class I to IX is open from 05 Apr 2023. Parents are requested to register their ward(s) in online mode only for admission. Please note that admissions are subject to availability of seats in the respective class. Registration through online application for admission in the classes I to IX is not the guarantee for admission in APS Delhi. Admissions will be granted as per admission policy decided by the AWES Management.</p>\r\n\r\n<p> </p>\r\n\r\n<p>Principal</p>\r\n\r\n<p>Ayasha Goyal</p>', '2024-11-25'),
(5, 'Independence Day Celebration!!!!!! Notification', '<p>All the students of our school are hereby informed that our school is going to celebrate the Independence Day like previous years in the school premises. Students are requested to note that on the 15th August they will be assembled on the school ground at 7:30 a.m. positively.</p>', '2024-11-25');

-- --------------------------------------------------------

--
-- Table structure for table `email_template_attachment`
--

CREATE TABLE `email_template_attachment` (
  `id` int(11) NOT NULL,
  `email_template_id` int(11) NOT NULL,
  `attachment` varchar(100) NOT NULL,
  `attachment_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enquiry`
--

CREATE TABLE `enquiry` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `reference` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(500) NOT NULL,
  `follow_up_date` date NOT NULL,
  `note` text NOT NULL,
  `source` varchar(50) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `assigned` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `no_of_child` varchar(11) DEFAULT NULL,
  `status` varchar(100) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `enquiry`
--

INSERT INTO `enquiry` (`id`, `name`, `contact`, `address`, `reference`, `date`, `description`, `follow_up_date`, `note`, `source`, `email`, `assigned`, `class_id`, `no_of_child`, `status`, `created_by`, `created_at`) VALUES
(1, 'Rachin sinha', '8009077889', '', 'parent', '2024-11-18', '', '2024-11-22', '', 'Online Front Site', '', 1, 1, '5', 'active', 1, '2024-11-25 10:20:53'),
(2, 'Olie Martin', '8006767889', '', 'staff', '2024-11-20', '', '2024-11-25', '', 'Online Front Site', '', 1, 1, '5', 'dead', 1, '2024-11-25 10:22:31'),
(3, 'Ottneil Baartman', '8896567566', '', 'student', '2024-11-10', '', '2024-11-12', '', 'Advertisement', '', 1, 2, '4', 'active', 1, '2024-11-25 10:23:21'),
(5, 'Ford Hoeger', 'Sao Tome and Princip', '72914 Doyle Trail', '', '2024-11-26', 'Commodi quo quod nam.', '2024-11-26', '187', 'Online Front Site', 'ford@gmail.com', 2, 4, '2', 'active', 1, '2024-11-26 10:55:51');

-- --------------------------------------------------------

--
-- Table structure for table `enquiry_type`
--

CREATE TABLE `enquiry_type` (
  `id` int(11) NOT NULL,
  `enquiry_type` varchar(100) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `event_title` varchar(200) NOT NULL,
  `event_description` text NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `event_type` varchar(100) NOT NULL,
  `event_color` varchar(200) NOT NULL,
  `event_for` varchar(100) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `is_active` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `event_title`, `event_description`, `start_date`, `end_date`, `event_type`, `event_color`, `event_for`, `role_id`, `is_active`) VALUES
(1, 'hcb', '', '2024-12-31 00:00:00', '2024-12-31 00:00:00', 'task', '#000', '1', NULL, 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `sesion_id` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_groups`
--

CREATE TABLE `exam_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `exam_type` varchar(250) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `exam_groups`
--

INSERT INTO `exam_groups` (`id`, `name`, `exam_type`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Average Passing Exam', 'basic_system', '', 0, '2024-11-25 11:02:25', NULL),
(2, 'Class 4 (GPA Grading System)', 'gpa', 'Grade Point Average (GPA) is the average of all the grades obtained by a student in his/ her academic studies.\r\n', 0, '2024-11-25 11:02:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exam_group_class_batch_exams`
--

CREATE TABLE `exam_group_class_batch_exams` (
  `id` int(11) NOT NULL,
  `exam` varchar(250) DEFAULT NULL,
  `passing_percentage` float(10,2) DEFAULT NULL,
  `session_id` int(10) NOT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `exam_group_id` int(11) DEFAULT NULL,
  `use_exam_roll_no` int(1) NOT NULL DEFAULT 1,
  `is_publish` int(1) DEFAULT 0,
  `is_rank_generated` int(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `is_active` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `exam_group_class_batch_exams`
--

INSERT INTO `exam_group_class_batch_exams` (`id`, `exam`, `passing_percentage`, `session_id`, `date_from`, `date_to`, `exam_group_id`, `use_exam_roll_no`, `is_publish`, `is_rank_generated`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Monthly Test  JULY(2024)', NULL, 20, NULL, NULL, 1, 1, 1, 0, 'Monthly Test  JULY(2024)', 1, '2024-12-10 10:49:31', NULL),
(2, 'Monthly Test (February -2024)', NULL, 19, NULL, NULL, 2, 1, 1, 0, 'These are the best model question papers issued by CBSE that will certainly help students. ', 1, '2024-12-10 10:51:02', NULL),
(3, 'Chapter Wise Weekly Test(April-2024)', NULL, 20, NULL, NULL, 2, 1, 1, 0, 'Chapter Wise Weekly Test(April-2024)', 1, '2024-12-10 10:55:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exam_group_class_batch_exam_students`
--

CREATE TABLE `exam_group_class_batch_exam_students` (
  `id` int(11) NOT NULL,
  `exam_group_class_batch_exam_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `student_session_id` int(11) NOT NULL,
  `roll_no` int(6) DEFAULT NULL,
  `teacher_remark` text DEFAULT NULL,
  `rank` int(20) NOT NULL DEFAULT 0,
  `is_active` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `exam_group_class_batch_exam_students`
--

INSERT INTO `exam_group_class_batch_exam_students` (`id`, `exam_group_class_batch_exam_id`, `student_id`, `student_session_id`, `roll_no`, `teacher_remark`, `rank`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NULL, NULL, 0, 0, '2024-12-10 10:49:46', NULL),
(2, 1, 4, 4, NULL, NULL, 0, 0, '2024-12-10 10:49:46', NULL),
(3, 1, 3, 3, NULL, NULL, 0, 0, '2024-12-10 10:49:46', NULL),
(4, 3, 1, 1, NULL, NULL, 0, 0, '2024-12-10 10:55:55', NULL),
(5, 3, 4, 4, NULL, NULL, 0, 0, '2024-12-10 10:55:55', NULL),
(6, 3, 3, 3, NULL, NULL, 0, 0, '2024-12-10 10:55:55', NULL),
(7, 3, 6, 6, NULL, NULL, 0, 0, '2024-12-10 10:56:11', NULL),
(8, 3, 8, 8, NULL, NULL, 0, 0, '2024-12-10 10:56:11', NULL),
(9, 3, 10, 10, NULL, NULL, 0, 0, '2024-12-10 10:56:11', NULL),
(10, 3, 27, 27, NULL, NULL, 0, 0, '2024-12-10 10:56:23', NULL),
(11, 3, 29, 29, NULL, NULL, 0, 0, '2024-12-10 10:56:23', NULL),
(12, 3, 31, 31, NULL, NULL, 0, 0, '2024-12-10 10:56:23', NULL),
(13, 3, 25, 25, NULL, NULL, 0, 0, '2024-12-10 10:56:34', NULL),
(14, 3, 26, 26, NULL, NULL, 0, 0, '2024-12-10 10:56:34', NULL),
(15, 3, 28, 28, NULL, NULL, 0, 0, '2024-12-10 10:56:34', NULL),
(16, 2, 1, 1, NULL, NULL, 0, 0, '2024-12-10 10:56:47', NULL),
(17, 2, 4, 4, NULL, NULL, 0, 0, '2024-12-10 10:56:47', NULL),
(18, 2, 3, 3, NULL, NULL, 0, 0, '2024-12-10 10:56:47', NULL),
(19, 2, 6, 6, NULL, NULL, 0, 0, '2024-12-10 10:57:01', NULL),
(20, 2, 8, 8, NULL, NULL, 0, 0, '2024-12-10 10:57:01', NULL),
(21, 2, 10, 10, NULL, NULL, 0, 0, '2024-12-10 10:57:01', NULL),
(22, 2, 27, 27, NULL, NULL, 0, 0, '2024-12-10 10:57:14', NULL),
(23, 2, 29, 29, NULL, NULL, 0, 0, '2024-12-10 10:57:14', NULL),
(24, 2, 31, 31, NULL, NULL, 0, 0, '2024-12-10 10:57:14', NULL),
(25, 2, 25, 25, NULL, NULL, 0, 0, '2024-12-10 10:57:26', NULL),
(26, 2, 26, 26, NULL, NULL, 0, 0, '2024-12-10 10:57:26', NULL),
(27, 2, 28, 28, NULL, NULL, 0, 0, '2024-12-10 10:57:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exam_group_class_batch_exam_subjects`
--

CREATE TABLE `exam_group_class_batch_exam_subjects` (
  `id` int(11) NOT NULL,
  `exam_group_class_batch_exams_id` int(11) DEFAULT NULL,
  `subject_id` int(10) NOT NULL,
  `date_from` date NOT NULL,
  `time_from` time NOT NULL,
  `duration` varchar(50) NOT NULL,
  `room_no` varchar(100) DEFAULT NULL,
  `max_marks` float(10,2) DEFAULT NULL,
  `min_marks` float(10,2) DEFAULT NULL,
  `credit_hours` float(10,2) DEFAULT 0.00,
  `date_to` datetime DEFAULT NULL,
  `is_active` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `exam_group_class_batch_exam_subjects`
--

INSERT INTO `exam_group_class_batch_exam_subjects` (`id`, `exam_group_class_batch_exams_id`, `subject_id`, `date_from`, `time_from`, `duration`, `room_no`, `max_marks`, `min_marks`, `credit_hours`, `date_to`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 2, 2, '2024-02-01', '11:00:00', '1', '122', 100.00, 35.00, 0.00, NULL, 0, '2024-12-10 10:54:17', NULL),
(2, 2, 1, '2024-02-03', '11:00:00', '1', '342', 100.00, 35.00, 0.00, NULL, 0, '2024-12-10 10:54:17', NULL),
(3, 2, 5, '2024-02-05', '11:00:00', '1', '124', 100.00, 35.00, 0.00, NULL, 0, '2024-12-10 10:54:17', NULL),
(4, 2, 4, '2024-02-08', '11:00:00', '1', '122', 100.00, 35.00, 0.00, NULL, 0, '2024-12-10 10:54:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exam_group_exam_connections`
--

CREATE TABLE `exam_group_exam_connections` (
  `id` int(11) NOT NULL,
  `exam_group_id` int(11) DEFAULT NULL,
  `exam_group_class_batch_exams_id` int(11) DEFAULT NULL,
  `exam_weightage` float(10,2) DEFAULT 0.00,
  `is_active` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_group_exam_results`
--

CREATE TABLE `exam_group_exam_results` (
  `id` int(11) NOT NULL,
  `exam_group_class_batch_exam_student_id` int(11) NOT NULL,
  `exam_group_class_batch_exam_subject_id` int(11) DEFAULT NULL,
  `exam_group_student_id` int(11) DEFAULT NULL,
  `attendence` varchar(10) DEFAULT NULL,
  `get_marks` float(10,2) DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `is_active` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_group_students`
--

CREATE TABLE `exam_group_students` (
  `id` int(11) NOT NULL,
  `exam_group_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `student_session_id` int(10) DEFAULT NULL,
  `is_active` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_schedules`
--

CREATE TABLE `exam_schedules` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `teacher_subject_id` int(11) DEFAULT NULL,
  `date_of_exam` date DEFAULT NULL,
  `start_to` varchar(50) DEFAULT NULL,
  `end_from` varchar(50) DEFAULT NULL,
  `room_no` varchar(50) DEFAULT NULL,
  `full_marks` int(11) DEFAULT NULL,
  `passing_marks` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `exp_head_id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `invoice_no` varchar(200) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `documents` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'yes',
  `is_deleted` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `exp_head_id`, `name`, `invoice_no`, `date`, `amount`, `documents`, `note`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 1, 'Power house center', '3243', '2024-11-20', 100.00, NULL, 'Electricity Bill means the invoice sent every month to customers or consumers stating in detail all components, charges', 'yes', 'no', '2024-11-25 10:55:35', NULL),
(2, 3, 'EDUCATIONAL TRIP', '5464', '2024-11-10', 200.00, NULL, 'As informed before our school has organized Educational trip to NEHRU PLANETARIUM & NEHRU SCIENCE CENTR.', 'yes', 'no', '2024-11-25 10:56:24', NULL),
(3, 4, 'NCRT BOOKS', '564', '2024-11-01', 150.00, NULL, 'NCRT Books are essential materials for students of all classes.', 'yes', 'no', '2024-11-25 10:57:33', NULL),
(4, 4, 'Deepak Books Publication', '76855', '2024-10-15', 150.00, NULL, 'Sales book is a book of original entry or a subsidiary book that is used to record the credit sales of the goods', 'yes', 'no', '2024-11-25 10:58:07', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expense_head`
--

CREATE TABLE `expense_head` (
  `id` int(11) NOT NULL,
  `exp_category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'yes',
  `is_deleted` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `expense_head`
--

INSERT INTO `expense_head` (`id`, `exp_category`, `description`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 'Electricity bill', '', 'yes', 'no', '2024-11-25 10:54:16', NULL),
(2, 'Telephone Bill', '', 'yes', 'no', '2024-11-25 10:54:23', NULL),
(3, 'Flower', '', 'yes', 'no', '2024-11-25 10:54:26', NULL),
(4, 'Stationary Purchase', '', 'yes', 'no', '2024-11-25 10:56:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feemasters`
--

CREATE TABLE `feemasters` (
  `id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `feetype_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fees_discounts`
--

CREATE TABLE `fees_discounts` (
  `id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `percentage` float(10,2) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `fees_discounts`
--

INSERT INTO `fees_discounts` (`id`, `session_id`, `name`, `code`, `type`, `percentage`, `amount`, `description`, `is_active`, `created_at`) VALUES
(1, 19, 'Sibling Discount', 'sibling-disc', 'fix', NULL, 500.00, '', 'no', '2024-11-25 10:40:29'),
(2, 19, 'Handicapped Discount', 'handicap-disc', 'fix', NULL, 1000.00, '', 'no', '2024-11-25 10:40:51'),
(3, 19, 'Class Topper Discount', 'cls-top-disc', 'fix', NULL, 100.00, '', 'no', '2024-11-25 10:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `fees_reminder`
--

CREATE TABLE `fees_reminder` (
  `id` int(11) NOT NULL,
  `reminder_type` varchar(10) DEFAULT NULL,
  `day` int(2) DEFAULT NULL,
  `is_active` int(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `fees_reminder`
--

INSERT INTO `fees_reminder` (`id`, `reminder_type`, `day`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'before', 2, 1, '2024-11-26 11:03:35', NULL),
(2, 'before', 5, 0, '2020-02-28 13:38:36', NULL),
(3, 'after', 2, 0, '2020-02-28 13:38:40', NULL),
(4, 'after', 5, 0, '2020-02-28 13:38:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feetype`
--

CREATE TABLE `feetype` (
  `id` int(11) NOT NULL,
  `is_system` int(1) NOT NULL DEFAULT 0,
  `feecategory_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `code` varchar(100) NOT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `feetype`
--

INSERT INTO `feetype` (`id`, `is_system`, `feecategory_id`, `type`, `code`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(1, 0, NULL, 'Admission Fees', '1-installment-fees	', 'no', '', '2024-11-25 10:40:15', NULL),
(2, 0, NULL, 'Bus Fees', 'Bus fees', 'no', '', '2024-11-25 10:40:39', NULL),
(3, 0, NULL, 'exam-fees	', 'exam-fees	', 'no', '', '2024-11-25 10:41:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fee_groups`
--

CREATE TABLE `fee_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `is_system` int(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `fee_groups`
--

INSERT INTO `fee_groups` (`id`, `name`, `is_system`, `description`, `is_active`, `created_at`) VALUES
(1, 'Class 1 General	', 0, '', 'no', '2024-11-25 10:41:18'),
(2, 'Class 1 Lump Sum	', 0, '', 'no', '2024-11-25 10:41:22'),
(3, 'Class 2 Lump Sum	', 0, '', 'no', '2024-11-25 10:41:27'),
(4, 'Discount', 0, '', 'no', '2024-11-25 10:41:42'),
(5, 'March Fees	', 0, '', 'no', '2024-11-25 10:42:11'),
(6, 'Class 4 General	', 0, '', 'no', '2024-11-25 10:42:24');

-- --------------------------------------------------------

--
-- Table structure for table `fee_groups_feetype`
--

CREATE TABLE `fee_groups_feetype` (
  `id` int(11) NOT NULL,
  `fee_session_group_id` int(11) DEFAULT NULL,
  `fee_groups_id` int(11) DEFAULT NULL,
  `feetype_id` int(11) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `fine_type` varchar(50) NOT NULL DEFAULT 'none',
  `due_date` date DEFAULT NULL,
  `fine_percentage` float(10,2) NOT NULL DEFAULT 0.00,
  `fine_amount` float(10,2) NOT NULL DEFAULT 0.00,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `fee_groups_feetype`
--

INSERT INTO `fee_groups_feetype` (`id`, `fee_session_group_id`, `fee_groups_id`, `feetype_id`, `session_id`, `amount`, `fine_type`, `due_date`, `fine_percentage`, `fine_amount`, `is_active`, `created_at`) VALUES
(1, 1, 1, 1, 19, 2000.00, 'none', '2024-04-10', 0.00, 0.00, 'no', '2024-11-25 10:43:48'),
(2, 1, 1, 2, 19, 300.00, 'none', '2024-04-10', 0.00, 0.00, 'no', '2024-11-25 10:45:11'),
(3, 2, 6, 3, 19, 250.00, 'none', '2024-09-10', 0.00, 0.00, 'no', '2024-11-25 10:47:32'),
(4, 3, 3, 1, 19, 3000.00, 'none', '2024-04-10', 0.00, 0.00, 'no', '2024-11-25 10:50:05');

-- --------------------------------------------------------

--
-- Table structure for table `fee_receipt_no`
--

CREATE TABLE `fee_receipt_no` (
  `id` int(11) NOT NULL,
  `payment` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fee_session_groups`
--

CREATE TABLE `fee_session_groups` (
  `id` int(11) NOT NULL,
  `fee_groups_id` int(11) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `fee_session_groups`
--

INSERT INTO `fee_session_groups` (`id`, `fee_groups_id`, `session_id`, `is_active`, `created_at`) VALUES
(1, 1, 19, 'no', '2024-11-25 10:43:48'),
(2, 6, 19, 'no', '2024-11-25 10:47:32'),
(3, 3, 19, 'no', '2024-11-25 10:50:05');

-- --------------------------------------------------------

--
-- Table structure for table `filetypes`
--

CREATE TABLE `filetypes` (
  `id` int(11) NOT NULL,
  `file_extension` text DEFAULT NULL,
  `file_mime` text DEFAULT NULL,
  `file_size` int(11) NOT NULL,
  `image_extension` text DEFAULT NULL,
  `image_mime` text DEFAULT NULL,
  `image_size` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `filetypes`
--

INSERT INTO `filetypes` (`id`, `file_extension`, `file_mime`, `file_size`, `image_extension`, `image_mime`, `image_size`, `created_at`) VALUES
(1, 'pdf, zip, jpg, jpeg, png, txt, 7z, gif, csv, docx, mp3, mp4, accdb, odt, ods, ppt, pptx, xlsx, wmv, jfif, apk, ppt, bmp, jpe, mdb, rar, xls, svg', 'application/pdf, image/zip, image/jpg, image/png, image/jpeg, text/plain, application/x-zip-compressed, application/zip, image/gif, text/csv, application/vnd.openxmlformats-officedocument.wordprocessingml.document, audio/mpeg, application/msaccess, application/vnd.oasis.opendocument.text, application/vnd.oasis.opendocument.spreadsheet, application/vnd.ms-powerpoint, application/vnd.openxmlformats-officedocument.presentationml.presentation, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, video/x-ms-wmv, video/mp4, image/jpeg, application/vnd.android.package-archive, application/x-msdownload, application/vnd.ms-powerpoint, image/bmp, image/jpeg, application/msaccess, application/vnd.ms-excel, image/svg+xml', 100048576, 'jfif, png, jpe, jpeg, jpg, bmp, gif, svg', 'image/jpeg, image/png, image/jpeg, image/jpeg, image/bmp, image/gif, image/x-ms-bmp, image/svg+xml', 10048576, '2021-01-30 13:03:03');

-- --------------------------------------------------------

--
-- Table structure for table `follow_up`
--

CREATE TABLE `follow_up` (
  `id` int(11) NOT NULL,
  `enquiry_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `next_date` date NOT NULL,
  `response` text NOT NULL,
  `note` text NOT NULL,
  `followup_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_media_gallery`
--

CREATE TABLE `front_cms_media_gallery` (
  `id` int(11) NOT NULL,
  `image` varchar(300) DEFAULT NULL,
  `thumb_path` varchar(300) DEFAULT NULL,
  `dir_path` varchar(300) DEFAULT NULL,
  `img_name` varchar(300) DEFAULT NULL,
  `thumb_name` varchar(300) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_type` varchar(100) NOT NULL,
  `file_size` varchar(100) NOT NULL,
  `vid_url` text NOT NULL,
  `vid_title` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `front_cms_media_gallery`
--

INSERT INTO `front_cms_media_gallery` (`id`, `image`, `thumb_path`, `dir_path`, `img_name`, `thumb_name`, `created_at`, `file_type`, `file_size`, `vid_url`, `vid_title`) VALUES
(11, 'original-6731833-1.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732605886-380649231674577be79351!original-6731833-1.jpg', '1732605886-380649231674577be79351!thumb_original-6731833-1.jpg', '2024-11-26 19:54:46', 'image/jpeg', '114292', '', ''),
(12, 'children-.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732605940-391833211674577f47e754!children-.jpg', '1732605940-391833211674577f47e754!thumb_children-.jpg', '2024-11-26 19:55:40', 'image/jpeg', '30433', '', ''),
(13, 'IMG-20.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732605962-2688226386745780a0635b!IMG-20.jpg', '1732605962-2688226386745780a0635b!thumb_IMG-20.jpg', '2024-11-26 19:56:02', 'image/jpeg', '50603', '', ''),
(14, 'images.jpeg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732605980-14530336016745781c7e354!images.jpeg', '1732605980-14530336016745781c7e354!thumb_images.jpeg', '2024-11-26 19:56:20', 'image/jpeg', '8167', '', ''),
(15, 'celebrate-teachers-day-.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606011-3086059836745783bae0a5!celebrate-teachers-day-.jpg', '1732606011-3086059836745783bae0a5!thumb_celebrate-teachers-day-.jpg', '2024-11-26 19:56:51', 'image/jpeg', '53962', '', ''),
(16, 'bann.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606030-11043733296745784e8698f!bann.jpg', '1732606030-11043733296745784e8698f!thumb_bann.jpg', '2024-11-26 19:57:10', 'image/jpeg', '36431', '', ''),
(17, 'iSto.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606053-18024583646745786535f31!iSto.jpg', '1732606053-18024583646745786535f31!thumb_iSto.jpg', '2024-11-26 19:57:33', 'image/jpeg', '412980', '', ''),
(18, '!8.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606077-5897284936745787d40cae!!8.jpg', '1732606077-5897284936745787d40cae!thumb_!8.jpg', '2024-11-26 19:57:57', 'image/jpeg', '58250', '', ''),
(19, 'dd.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606096-155398927167457890c32ad!dd.jpg', '1732606096-155398927167457890c32ad!thumb_dd.jpg', '2024-11-26 19:58:16', 'image/jpeg', '18895', '', ''),
(20, '84A1827.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606112-1166068328674578a06b3ab!84A1827.jpg', '1732606112-1166068328674578a06b3ab!thumb_84A1827.jpg', '2024-11-26 19:58:32', 'image/jpeg', '26496', '', ''),
(21, '16.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606131-779386174674578b38cd91!16.jpg', '1732606131-779386174674578b38cd91!thumb_16.jpg', '2024-11-26 19:58:51', 'image/jpeg', '25705', '', ''),
(22, 'iuu.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606146-545668323674578c21bcc7!iuu.jpg', '1732606146-545668323674578c21bcc7!thumb_iuu.jpg', '2024-11-26 19:59:06', 'image/jpeg', '30720', '', ''),
(23, '!t.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606170-367305608674578dac9460!!t.jpg', '1732606170-367305608674578dac9460!thumb_!t.jpg', '2024-11-26 19:59:30', 'image/jpeg', '42384', '', ''),
(24, 'k.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606192-1813030109674578f02c66a!k.jpg', '1732606192-1813030109674578f02c66a!thumb_k.jpg', '2024-11-26 19:59:52', 'image/jpeg', '33467', '', ''),
(25, '1237-3.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606235-20467455776745791bbaf4d!1237-3.jpg', '1732606235-20467455776745791bbaf4d!thumb_1237-3.jpg', '2024-11-26 20:00:35', 'image/jpeg', '110806', '', ''),
(26, '1235-3.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606259-1514876047674579338ad13!1235-3.jpg', '1732606259-1514876047674579338ad13!thumb_1235-3.jpg', '2024-11-26 20:00:59', 'image/jpeg', '293365', '', ''),
(27, 'image.png', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606285-17479809246745794d680ea!image.png', '1732606285-17479809246745794d680ea!thumb_image.png', '2024-11-26 20:01:25', 'image/png', '858923', '', ''),
(28, 'v-4banner3-3.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606298-9925366996745795a87387!v-4banner3-3.jpg', '1732606298-9925366996745795a87387!thumb_v-4banner3-3.jpg', '2024-11-26 20:01:38', 'image/jpeg', '227581', '', ''),
(29, 'top-banner2-2.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606318-9632988516745796e55800!top-banner2-2.jpg', '1732606318-9632988516745796e55800!thumb_top-banner2-2.jpg', '2024-11-26 20:01:58', 'image/jpeg', '330384', '', ''),
(30, '1695468824-759901127650ecd18c2cf7!ogin_bg3.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606343-2023902069674579876fc53!1695468824-759901127650ecd18c2cf7!ogin_bg3.jpg', '1732606343-2023902069674579876fc53!thumb_1695468824-759901127650ecd18c2cf7!ogin_bg3.jpg', '2024-11-26 20:02:23', 'image/jpeg', '362276', '', ''),
(31, '!1695468736-1930404604650eccc039627!user.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606415-1843532615674579cf2125f!!1695468736-1930404604650eccc039627!user.jpg', '1732606415-1843532615674579cf2125f!thumb_!1695468736-1930404604650eccc039627!user.jpg', '2024-11-26 20:03:35', 'image/jpeg', '585645', '', ''),
(32, '!1674040621-203862736463c7d52d312c6!DCC Sports Day (3).jpeg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606433-1999832186674579e1c2242!!1674040621-203862736463c7d52d312c6!DCC Sports Day (3).jpeg', '1732606433-1999832186674579e1c2242!thumb_!1674040621-203862736463c7d52d312c6!DCC Sports Day (3).jpeg', '2024-11-26 20:03:54', 'image/jpeg', '908547', '', ''),
(33, 'download.png', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606442-537967653674579ea9b905!download.png', '1732606442-537967653674579ea9b905!thumb_download.png', '2024-11-26 20:04:02', 'image/png', '7305', '', ''),
(34, 'maxresdefault.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606459-863306839674579fb682ba!maxresdefault.jpg', '1732606459-863306839674579fb682ba!thumb_maxresdefault.jpg', '2024-11-26 20:04:19', 'image/jpeg', '133595', '', ''),
(35, 'y.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606480-134584854967457a1081681!y.jpg', '1732606480-134584854967457a1081681!thumb_y.jpg', '2024-11-26 20:04:40', 'image/jpeg', '429122', '', ''),
(36, 'annual-day.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606495-7363596567457a1f044f2!annual-day.jpg', '1732606495-7363596567457a1f044f2!thumb_annual-day.jpg', '2024-11-26 20:04:55', 'image/jpeg', '165470', '', ''),
(37, '2024-CBSE.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606543-14676115767457a4f597c0!2024-CBSE.jpg', '1732606543-14676115767457a4f597c0!thumb_2024-CBSE.jpg', '2024-11-26 20:05:43', 'image/jpeg', '132035', '', ''),
(38, '6_334.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606558-19785468567457a5e0ff53!6_334.jpg', '1732606558-19785468567457a5e0ff53!thumb_6_334.jpg', '2024-11-26 20:05:58', 'image/jpeg', '145054', '', ''),
(39, 'Christmas-Day.png', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606578-72123118867457a72455ea!Christmas-Day.png', '1732606578-72123118867457a72455ea!thumb_Christmas-Day.png', '2024-11-26 20:06:18', 'image/png', '210642', '', ''),
(40, 'Happy-Diwali-to-My-Students.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606589-138106762167457a7da9c8a!Happy-Diwali-to-My-Students.jpg', '1732606589-138106762167457a7da9c8a!thumb_Happy-Diwali-to-My-Students.jpg', '2024-11-26 20:06:29', 'image/jpeg', '171827', '', ''),
(41, 'Celebrations-Ideas.jpeg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606655-115849677067457abf66db9!Celebrations-Ideas.jpeg', '1732606655-115849677067457abf66db9!thumb_Celebrations-Ideas.jpeg', '2024-11-26 20:07:35', 'image/jpeg', '79466', '', ''),
(42, 'fun games.png', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732606739-8871931267457b13dd38c!fun games.png', '1732606739-8871931267457b13dd38c!thumb_fun games.png', '2024-11-26 20:08:59', 'image/png', '125617', '', ''),
(43, 'v-4banner1.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732607077-90743727967457c657cff0!v-4banner1.jpg', '1732607077-90743727967457c657cff0!thumb_v-4banner1.jpg', '2024-11-26 20:14:37', 'image/jpeg', '253646', '', ''),
(44, 'v-4banner4.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732607117-11307269967457c8dbb12d!v-4banner4.jpg', '1732607117-11307269967457c8dbb12d!thumb_v-4banner4.jpg', '2024-11-26 20:15:17', 'image/jpeg', '190592', '', ''),
(45, 'ncc.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732616356-3797938846745a0a412928!ncc.jpg', '1732616356-3797938846745a0a412928!thumb_ncc.jpg', '2024-11-26 22:49:16', 'image/jpeg', '202985', '', ''),
(47, 'gal6.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732616603-671463256745a19b7ec0b!gal6.jpg', '1732616603-671463256745a19b7ec0b!thumb_gal6.jpg', '2024-11-26 22:53:23', 'image/jpeg', '206671', '', ''),
(48, 'gal5.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732616615-15404612746745a1a7d5f72!gal5.jpg', '1732616615-15404612746745a1a7d5f72!thumb_gal5.jpg', '2024-11-26 22:53:35', 'image/jpeg', '157400', '', ''),
(49, 'gal77.jpg', 'uploads/gallery/media/thumb/', 'uploads/gallery/media/', '1732616723-13118517526745a21391ff2!gal77.jpg', '1732616723-13118517526745a21391ff2!thumb_gal77.jpg', '2024-11-26 22:55:23', 'image/jpeg', '103873', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_menus`
--

CREATE TABLE `front_cms_menus` (
  `id` int(11) NOT NULL,
  `menu` varchar(100) DEFAULT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `open_new_tab` int(10) NOT NULL DEFAULT 0,
  `ext_url` text NOT NULL,
  `ext_url_link` text NOT NULL,
  `publish` int(11) NOT NULL DEFAULT 0,
  `content_type` varchar(10) NOT NULL DEFAULT 'manual',
  `is_active` varchar(10) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `front_cms_menus`
--

INSERT INTO `front_cms_menus` (`id`, `menu`, `slug`, `description`, `open_new_tab`, `ext_url`, `ext_url_link`, `publish`, `content_type`, `is_active`, `created_at`) VALUES
(1, 'Main Menu', 'main-menu', 'Main menu', 0, '', '', 0, 'default', 'no', '2018-04-20 14:54:49'),
(2, 'Bottom Menu', 'bottom-menu', 'Bottom Menu', 0, '', '', 0, 'default', 'no', '2018-04-20 14:54:55');

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_menu_items`
--

CREATE TABLE `front_cms_menu_items` (
  `id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `menu` varchar(100) DEFAULT NULL,
  `page_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `ext_url` text DEFAULT NULL,
  `open_new_tab` int(11) DEFAULT 0,
  `ext_url_link` text DEFAULT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `publish` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `is_active` varchar(10) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `front_cms_menu_items`
--

INSERT INTO `front_cms_menu_items` (`id`, `menu_id`, `menu`, `page_id`, `parent_id`, `ext_url`, `open_new_tab`, `ext_url_link`, `slug`, `weight`, `publish`, `description`, `is_active`, `created_at`) VALUES
(1, 1, 'Home', 1, 0, NULL, NULL, NULL, 'home', 1, 0, NULL, 'no', '2024-11-26 07:39:57'),
(2, 1, 'Contact', 4, 0, NULL, NULL, NULL, 'contact', 25, 0, NULL, 'no', '2024-11-26 07:37:04'),
(4, 1, 'Online Admission', 0, 0, '1', NULL, '{{BASEURL}}online_admission', 'online-admission', 3, 0, NULL, 'no', '2025-02-11 12:38:00'),
(5, 1, 'Online Course', 10, 0, '1', NULL, '{{BASEURL}}online_course', 'online-course', 2, 0, NULL, 'no', '2025-02-11 12:38:09'),
(6, 1, 'Academics', 0, 0, NULL, NULL, NULL, 'academics', 7, 0, NULL, 'no', '2024-11-26 07:39:57'),
(7, 1, 'Gallery', 12, 0, NULL, NULL, NULL, 'gallery', 22, 0, NULL, 'no', '2024-11-26 10:15:07'),
(8, 1, 'Cbse Exam Result', 0, 0, '1', NULL, '{{BASEURL}}cbseexam', 'cbse-exam-result', 4, 0, NULL, 'no', '2025-02-11 12:38:19'),
(9, 1, 'Exam Result', 0, 0, '1', NULL, '{{BASEURL}}examresult', 'exam-result', 5, 0, NULL, 'no', '2025-02-11 12:38:32'),
(10, 1, 'About Us', 7, 0, NULL, NULL, NULL, 'about-us', 6, 0, NULL, 'no', '2024-11-26 07:40:55'),
(11, 1, 'Annual Sports Day', 29, 6, NULL, NULL, NULL, 'annual-sports-day', 8, 0, NULL, 'no', '2024-11-26 07:39:57'),
(12, 1, 'Facilities', 18, 6, NULL, NULL, NULL, 'facilities', 9, 0, NULL, 'no', '2024-11-26 07:39:57'),
(13, 1, 'Course', 10, 6, NULL, NULL, NULL, 'course', 10, 0, NULL, 'no', '2024-11-26 07:39:57'),
(14, 1, 'School Uniform', 11, 6, NULL, NULL, NULL, 'school-uniform', 11, 0, NULL, 'no', '2024-11-26 07:39:57'),
(15, 1, 'Principal Message', 19, 6, NULL, NULL, NULL, 'principal-message', 12, 0, NULL, 'no', '2024-11-26 07:39:57'),
(16, 1, 'School Management', 20, 6, NULL, NULL, NULL, 'school-management', 13, 0, NULL, 'no', '2024-11-26 07:39:57'),
(17, 1, 'Know Us', 21, 6, NULL, NULL, NULL, 'know-us', 14, 0, NULL, 'no', '2024-11-26 07:39:57'),
(18, 1, 'Approach', 22, 6, NULL, NULL, NULL, 'approach', 15, 0, NULL, 'no', '2024-11-26 07:39:57'),
(19, 1, 'Pre Primary', 23, 6, NULL, NULL, NULL, 'pre-primary', 16, 0, NULL, 'no', '2024-11-26 07:39:57'),
(20, 1, 'Teacher', 15, 6, NULL, NULL, NULL, 'teacher', 17, 0, NULL, 'no', '2024-11-26 07:39:57'),
(21, 1, 'Houses & Mentoring', 27, 6, NULL, NULL, NULL, 'houses-mentoring', 18, 0, NULL, 'no', '2024-11-26 07:39:57'),
(22, 1, 'Student Council', 26, 6, NULL, NULL, NULL, 'student-council', 19, 0, NULL, 'no', '2024-11-26 07:39:57'),
(23, 1, 'Career Counselling', 28, 6, NULL, NULL, NULL, 'career-counselling', 20, 0, NULL, 'no', '2024-11-26 07:39:57'),
(24, 1, 'Events', 14, 0, NULL, NULL, NULL, 'events', 23, 0, NULL, 'no', '2024-11-26 07:37:13'),
(25, 1, 'News', 13, 0, NULL, NULL, NULL, 'news', 24, 0, NULL, 'no', '2024-11-26 07:37:13'),
(26, 2, 'Home', 1, 0, NULL, NULL, NULL, 'home-1', NULL, 0, NULL, 'no', '2024-11-26 09:11:31'),
(27, 2, 'About Us', 7, 0, NULL, NULL, NULL, 'about-us-1', NULL, 0, NULL, 'no', '2024-11-26 09:11:45'),
(28, 2, 'Course', 10, 0, NULL, NULL, NULL, 'course-1', NULL, 0, NULL, 'no', '2024-11-26 09:12:00'),
(29, 2, 'Gallery', 12, 0, NULL, NULL, NULL, 'gallery-1', NULL, 0, NULL, 'no', '2024-11-26 09:12:14'),
(30, 2, 'Events', 14, 0, NULL, NULL, NULL, 'events-1', NULL, 0, NULL, 'no', '2024-11-26 09:12:22'),
(31, 2, 'News', 13, 0, NULL, NULL, NULL, 'news-1', NULL, 0, NULL, 'no', '2024-11-26 09:12:54'),
(32, 2, 'Contact Us', 4, 0, NULL, NULL, NULL, 'contact-us', NULL, 0, NULL, 'no', '2024-11-26 09:13:04'),
(33, 2, 'Privacy Policy', 32, 0, NULL, NULL, NULL, 'privacy-policy', NULL, 0, NULL, 'no', '2024-12-24 09:55:29');

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_pages`
--

CREATE TABLE `front_cms_pages` (
  `id` int(11) NOT NULL,
  `page_type` varchar(10) NOT NULL DEFAULT 'manual',
  `is_homepage` int(1) DEFAULT 0,
  `title` varchar(250) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `meta_title` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keyword` text DEFAULT NULL,
  `feature_image` varchar(200) NOT NULL,
  `description` longtext DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `publish` int(10) DEFAULT 0,
  `sidebar` int(10) DEFAULT 0,
  `is_active` varchar(10) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `front_cms_pages`
--

INSERT INTO `front_cms_pages` (`id`, `page_type`, `is_homepage`, `title`, `url`, `type`, `slug`, `meta_title`, `meta_description`, `meta_keyword`, `feature_image`, `description`, `publish_date`, `publish`, `sidebar`, `is_active`, `created_at`) VALUES
(1, 'default', 1, 'Home', 'page/home', 'page', 'home', '', '', '', '', '<section class=\"services\">\r\n<div class=\"service-inner\">\r\n<div class=\"container\">\r\n<div class=\"row\">\r\n<div class=\"col-md-4 col-sm-4 service-box\">\r\n<div class=\"service-box-content\">\r\n<h3><a href=\"#\">Scholarship Facility</a></h3>\r\n\r\n<p>Eimply dummy text printing ypese tting industry.</p>\r\n\r\n<div class=\"service-box-icon\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/scholarship-icon.png\" /></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-4 col-sm-4 service-box\">\r\n<div class=\"service-box-content\">\r\n<h3><a href=\"#\">Books & Liberary</a></h3>\r\n\r\n<p>Eimply dummy text printing ypese tting industry.</p>\r\n\r\n<div class=\"service-box-icon\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/book-icon.png\" /></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-4 col-sm-4 service-box\">\r\n<div class=\"service-box-content\">\r\n<h3><a href=\"#\">Certified Teachers</a></h3>\r\n\r\n<p>Eimply dummy text printing ypese tting industry.</p>\r\n\r\n<div class=\"service-box-icon\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/teacher-icon.png\" /></div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</section>\r\n\r\n<div class=\"container spacet40\">\r\n<div class=\"row\">\r\n<div class=\"col-md-8 col-sm-12 col-md-offset-2 text-center\">\r\n<h2 class=\"head-title\">Mount Carmel Residential School</h2>\r\n\r\n<p class=\"pb40\">Tmply dummy text of the printing and typesetting industry. Lorem Ipsum has been theindustry\'s standard dummy text ever since the 1500s, when an unknown printer took.</p>\r\n<img class=\"img-responsive center-block\" src=\"https://demo.smart-school.in/uploads/gallery/media/about_bg.jpg\" /></div>\r\n</div>\r\n</div>\r\n\r\n<section class=\"bg-gray fullwidth spaceb40 spacet40\">\r\n<div class=\"container\">\r\n<div class=\"row\">\r\n<div class=\"col-md-8 col-sm-12 col-md-offset-2 text-center\">\r\n<h2 class=\"head-title\">ABOUT US</h2>\r\n\r\n<p>Fusce sem dolor, interdum in fficitur at, faucibus nec lorem. Sed nec molestie justo.</p>\r\n\r\n<div class=\"divider\">&nbsp;</div>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-sm-6\">\r\n<div class=\"about_img\"><img class=\"img-responsive img-rounded\" src=\"https://demo.smart-school.in/uploads/gallery/media/about.jpg\" /></div>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-sm-6\">\r\n<div class=\"about-right\">\r\n<h3>WELCOME TO Mount Carmel</h3>\r\n\r\n<p class=\"pt10 pb10\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>\r\n</div>\r\n\r\n<div class=\"panel-group accrodion2\" id=\"accordion\">\r\n<div class=\"panel panel-default\">\r\n<div class=\"panel-heading\">\r\n<h4 class=\"panel-title\"><a class=\"accordion-toggle\" data-parent=\"#accordion\" data-toggle=\"collapse\" href=\"#collapseOne\">Collapsible Group Item #1</a></h4>\r\n</div>\r\n\r\n<div class=\"panel-collapse collapse in\" id=\"collapseOne\">\r\n<div class=\"panel-body\">Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.</div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"panel panel-default\">\r\n<div class=\"panel-heading\">\r\n<h4 class=\"panel-title\"><a class=\"accordion-toggle\" data-parent=\"#accordion\" data-toggle=\"collapse\" href=\"#collapseTwo\">Collapsible Group Item #2</a></h4>\r\n</div>\r\n\r\n<div class=\"panel-collapse collapse\" id=\"collapseTwo\">\r\n<div class=\"panel-body\">Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.</div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"panel panel-default\">\r\n<div class=\"panel-heading\">\r\n<h4 class=\"panel-title\"><a class=\"accordion-toggle\" data-parent=\"#accordion\" data-toggle=\"collapse\" href=\"#collapseThree\">Collapsible Group Item #3</a></h4>\r\n</div>\r\n\r\n<div class=\"panel-collapse collapse\" id=\"collapseThree\">\r\n<div class=\"panel-body\">Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident.</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</section>\r\n\r\n<section class=\"spaceb40 spacet40\">\r\n<div class=\"container\">\r\n<div class=\"row\">\r\n<div class=\"col-md-8 col-sm-12 col-md-offset-2 text-center pb30\">\r\n<h2 class=\"head-title\">OUR MAIN COURSES</h2>\r\n\r\n<p>Fusce sem dolor, interdum in fficitur at</p>\r\n\r\n<div class=\"divider\">&nbsp;</div>\r\n</div>\r\n\r\n<div class=\"row\">\r\n<div class=\"owl-carousel courses-carousel\">\r\n<div class=\"col-md-12 col-sm-12\">\r\n<div class=\"courses-box\">\r\n<div class=\"courses-box-img\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/courseimg1.jpg\" /></div>\r\n\r\n<div class=\"course-inner\"><a class=\"course-subject\" href=\"#\">Science</a>\r\n\r\n<h4>Electrical Engineering</h4>\r\n\r\n<p>All over the world, human beings create an immense and ever-increasing volume of data, with new kinds of data regularly...</p>\r\n<a class=\"btn-read\" href=\"#\">apply now</a></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-12 col-sm-12\">\r\n<div class=\"courses-box\">\r\n<div class=\"courses-box-img\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/courseimg2.jpg\" /></div>\r\n\r\n<div class=\"course-inner\"><a class=\"course-subject\" href=\"#\">Science</a>\r\n\r\n<h4>Electrical Engineering</h4>\r\n\r\n<p>All over the world, human beings create an immense and ever-increasing volume of data, with new kinds of data regularly...</p>\r\n<a class=\"btn-read\" href=\"#\">apply now</a></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-12 col-sm-12\">\r\n<div class=\"courses-box\">\r\n<div class=\"courses-box-img\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/courseimg3.jpg\" /></div>\r\n\r\n<div class=\"course-inner\"><a class=\"course-subject\" href=\"#\">Science</a>\r\n\r\n<h4>Electrical Engineering</h4>\r\n\r\n<p>All over the world, human beings create an immense and ever-increasing volume of data, with new kinds of data regularly...</p>\r\n<a class=\"btn-read\" href=\"#\">apply now</a></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-12 col-sm-12\">\r\n<div class=\"courses-box\">\r\n<div class=\"courses-box-img\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/courseimg4.jpg\" /></div>\r\n\r\n<div class=\"course-inner\"><a class=\"course-subject\" href=\"#\">Science</a>\r\n\r\n<h4>Electrical Engineering</h4>\r\n\r\n<p>All over the world, human beings create an immense and ever-increasing volume of data, with new kinds of data regularly...</p>\r\n<a class=\"btn-read\" href=\"#\">apply now</a></div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</section>\r\n\r\n<section class=\"countdown_bg fullwidth counter\">\r\n<div class=\"container\">\r\n<div class=\"row\">\r\n<div class=\"col-md-6 col-lg-6 col-sm-6\">\r\n<div class=\"counter-content\">\r\n<h2 class=\"counter-title mt0\">ACHEIVEMENTS</h2>\r\n\r\n<div class=\"counter-text\">\r\n<p>A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart like mine.</p>\r\n</div>\r\n\r\n<div class=\"counter-img\">\r\n<div class=\"about_img\"><img class=\"img-responsive img-rounded\" src=\"https://demo.smart-school.in/uploads/gallery/media/achivement.jpg\" /></div>\r\n</div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-lg-6 col-sm-6\">\r\n<div class=\"row\">\r\n<div class=\"col-md-6 col-lg-6 col-sm-6\">\r\n<div class=\"counter-main\"><img class=\"svg\" src=\"https://demo.smart-school.in/uploads/gallery/media/cap.svg\" />\r\n<h3 class=\"counter-value\" data-count=\"1000\">1000</h3>\r\nGraduates</div>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-lg-6 col-sm-6\">\r\n<div class=\"counter-main\"><img class=\"svg\" src=\"https://demo.smart-school.in/uploads/gallery/media/award.svg\" />\r\n<h3 class=\"counter-value\" data-count=\"500\">500</h3>\r\nCertified Teachers</div>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-lg-6 col-sm-6\">\r\n<div class=\"counter-main\"><img class=\"svg\" src=\"https://demo.smart-school.in/uploads/gallery/media/building-o.svg\" />\r\n<h3 class=\"counter-value\" data-count=\"100\">100</h3>\r\nStudent Campuses</div>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-lg-6 col-sm-6\">\r\n<div class=\"counter-main\"><img class=\"svg\" src=\"https://demo.smart-school.in/uploads/gallery/media/people.svg\" />\r\n<h3 class=\"counter-value\" data-count=\"6285\">6285</h3>\r\nStudents</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</section>\r\n\r\n<section class=\"spaceb40 spacet40\">\r\n<div class=\"container\">\r\n<div class=\"row\">\r\n<div class=\"col-md-8 col-sm-12 col-md-offset-2 text-center\">\r\n<h2 class=\"head-title\">OUR EXPERIENCED STAFFS</h2>\r\n\r\n<p>Considering desire as primary motivation for the generation of narratives is a useful concept.</p>\r\n\r\n<div class=\"divider\">&nbsp;</div>\r\n</div>\r\n\r\n<div class=\"teamstaff\">\r\n<div class=\"row\">\r\n<div class=\"owl-carousel staff-carousel\">\r\n<div class=\"col-md-12 col-sm-12\">\r\n<div class=\"staffteam\">\r\n<div class=\"staffteamimg\"><img alt=\"\" src=\"https://demo.smart-school.in/uploads/gallery/media/teacher2.jpg\" />\r\n<ul class=\"social-links\">\r\n	<li>Facebook</li>\r\n	<li>Twitter</li>\r\n	<li>Linkedin</li>\r\n	<li>Google Plus</li>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"staff-content\">\r\n<h3>Stella Roffin</h3>\r\n<span class=\"post\">Drawing Teacher</span></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-12 col-sm-12\">\r\n<div class=\"staffteam\">\r\n<div class=\"staffteamimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/teacher1-1556236800.jpg\" />\r\n<ul class=\"social-links\">\r\n	<li>Facebook</li>\r\n	<li>Twitter</li>\r\n	<li>Linkedin</li>\r\n	<li>Google Plus</li>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"staff-content\">\r\n<h3>Princy Flora</h3>\r\n<span class=\"post\">English Tutor</span></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-12 col-sm-12\">\r\n<div class=\"staffteam\">\r\n<div class=\"staffteamimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/teacher2-1556236800.jpg\" />\r\n<ul class=\"social-links\">\r\n	<li>Facebook</li>\r\n	<li>Twitter</li>\r\n	<li>Linkedin</li>\r\n	<li>Google Plus</li>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"staff-content\">\r\n<h3 class=\"title\">Jesica Matt</h3>\r\n<span class=\"post\">Art Teacher</span></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-12 col-sm-12\">\r\n<div class=\"staffteam\">\r\n<div class=\"staffteamimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/teacher3.jpg\" />\r\n<ul class=\"social-links\">\r\n	<li>Facebook</li>\r\n	<li>Twitter</li>\r\n	<li>Linkedin</li>\r\n	<li>Google Plus</li>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"staff-content\">\r\n<h3 class=\"title\">Janaton Doe</h3>\r\n<span class=\"post\">Math Teacher</span></div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</section>\r\n\r\n<section class=\"spaceb40 spacet40 testimonial_bg fullwidth text-white\">\r\n<div class=\"container\">\r\n<div class=\"row\">\r\n<div class=\"col-md-8 col-sm-12 col-md-offset-2 text-center\">\r\n<h2 class=\"text-white\">WHAT PEOPLE SAYS</h2>\r\n\r\n<p>Fusce sem dolor, interdum in efficitur at, faucibus nec lorem. Sed nec molestie justo.</p>\r\n\r\n<div class=\"divider\">&nbsp;</div>\r\n</div>\r\n\r\n<div class=\"owl-carousel testimonial-carousel\">\r\n<div class=\"col-md-12 col-sm-12\">\r\n<div class=\"testimonial\">\r\n<div class=\"testimonialimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/testim-img2.jpg\" /></div>\r\n\r\n<div class=\"testi_description\">\r\n<h4>Sidney W. Yarber</h4>\r\n\r\n<h5>Manager</h5>\r\n\r\n<p>Etiam non elit nec augue tempor gravida et sed velit. Aliquam tempus eget lorem ut malesuada. Phasellus dictum est sed libero posuere dignissim.</p>\r\n</div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-12 col-sm-12\">\r\n<div class=\"testimonial\">\r\n<div class=\"testimonialimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/testim-img1.jpg\" /></div>\r\n\r\n<div class=\"testi_description\">\r\n<h4>Kayla H. Seaman</h4>\r\n\r\n<h5>Co & Founder</h5>\r\n\r\n<p>Etiam non elit nec augue tempor gravida et sed velit. Aliquam tempus eget lorem ut malesuada. Phasellus dictum est sed libero posuere dignissim.</p>\r\n</div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-12 col-sm-12\">\r\n<div class=\"testimonial\">\r\n<div class=\"testimonialimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/testim-img4.jpg\" /></div>\r\n\r\n<div class=\"testi_description\">\r\n<h4>Terence M. Witzel</h4>\r\n\r\n<h5>Businessman</h5>\r\n\r\n<p>Etiam non elit nec augue tempor gravida et sed velit. Aliquam tempus eget lorem ut malesuada. Phasellus dictum est sed libero posuere dignissim.</p>\r\n</div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-12 col-sm-12\">\r\n<div class=\"testimonial\">\r\n<div class=\"testimonialimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/testim-img3.jpg\" /></div>\r\n\r\n<div class=\"testi_description\">\r\n<h4>Williamson</h4>\r\n\r\n<h5>Manager</h5>\r\n\r\n<p>Etiam non elit nec augue tempor gravida et sed velit. Aliquam tempus eget lorem ut malesuada. Phasellus dictum est sed libero posuere dignissim.</p>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</section>', '0000-00-00', 1, NULL, 'no', '2024-11-26 10:02:23'),
(2, 'default', 0, 'Complain', 'page/complain', 'page', 'complain', 'Complain form', '                                                                                                                                                                                    complain form                                                                                                                                                                                                                                ', 'complain form', '', '<p>[form-builder:complain]</p>', '0000-00-00', 1, NULL, 'no', '2019-11-13 10:16:36'),
(3, 'default', 0, '404 page', 'page/404-page', 'page', '404-page', '', '                                ', '', '', '<title></title>\r\n<p>404 page found</p>', '0000-00-00', 0, NULL, 'no', '2024-11-26 07:40:43'),
(4, 'default', 0, 'Contact us', 'page/contact-us', 'page', 'contact-us', '', '', '', '', '<section class=\"contact\">\r\n<div class=\"container\">\r\n<div class=\"row\">\r\n<section class=\"contact\">\r\n<div class=\"container\">\r\n<div class=\"row\">\r\n<h2 class=\"col-md-12 col-sm-12\">Send In Your Query</h2>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<div class=\"col-md-12 col-sm-12\">[form-builder:contact_us]</div>\r\n</div>\r\n</div>\r\n</section>\r\n\r\n<div class=\"col-md-4 col-sm-4\">\r\n<div class=\"contact-item\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/pin.svg\" />\r\n<h3>Our Location</h3>\r\n\r\n<p>350 Fifth Avenue, 34th floor New York NY 10118-3299 USA</p>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-4 col-sm-4\">\r\n<div class=\"contact-item\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/phone.svg\" />\r\n<h3>CALL US</h3>\r\n\r\n<p>E-mail : info@abcschool.com</p>\r\n\r\n<p>Mobile : +91-9009987654</p>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-4 col-sm-4\">\r\n<div class=\"contact-item\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/clock.svg\" />\r\n<h3>Working Hours</h3>\r\n\r\n<p>Mon-Fri : 9 am to 5 pm</p>\r\n\r\n<p>Sat : 9 am to 3 pm</p>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-12 col-sm-12\">\r\n<div class=\"mapWrapper fullwidth\"><iframe frameborder=\"0\" height=\"500\" marginheight=\"0\" marginwidth=\"0\" scrolling=\"no\" src=\"http://maps.google.com/maps?f=q&source=s_q&hl=EN&q=time+square&aq=&sll=40.716558,-73.931122&sspn=0.40438,1.056747&ie=UTF8&rq=1&ev=p&split=1&radius=33.22&hq=time+square&hnear=&ll=37.061753,-95.677185&spn=0.438347,0.769043&z=9&output=embed\" width=\"917.6px\"></iframe></div>\r\n</div>\r\n</div>\r\n</div>\r\n</section>', '0000-00-00', 0, NULL, 'no', '2024-11-26 07:41:10'),
(6, 'manual', 0, 'Complain', 'page/complain-1', 'page', 'complain-1', '', '', '', '', '<h2 class=\"text-center\">Complain</h2>\r\n\r\n<p class=\"text-center\">Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy<br />\r\nnibh euismod tincidunt ut laoreet dolore magna.</p>\r\n\r\n<p class=\"text-center\">&nbsp;</p>\r\n\r\n<p class=\"text-center\">[form-builder:complain]</p>', NULL, 0, NULL, 'no', '2024-11-25 08:27:50'),
(7, 'manual', 0, 'About Us', 'page/about-us', 'page', 'about-us', '', '', '', '', '<div class=\"container spaceb60 spacet60\">\r\n<div class=\"row\">\r\n<div class=\"col-md-6 col-sm-6\">\r\n<h2>Who We Are</h2>\r\n\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>\r\n\r\n<h2>What We Do</h2>\r\n\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>\r\n\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</p>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-sm-6\"><img class=\"img-rounded img-responsive\" src=\"https://demo.smart-school.in/uploads/gallery/media/whatwedo.jpg\" /></div>\r\n</div>\r\n</div>\r\n\r\n<section class=\"bggray fullwidth\">\r\n<div class=\"container spaceb60 spacet60\">\r\n<div class=\"row\">\r\n<div class=\"col-md-8 col-lg-offset-2 col-sm-12 text-center\">\r\n<h2>Why Choose Our Institution?</h2>\r\n\r\n<p>Tmply dummy text of the printing and typesetting industry. Lorem Ipsum has been theindustry\'s standard dummy text ever since the 1500s, when an unknown printer took.</p>\r\n</div>\r\n\r\n<div class=\"col-lg-4 col-md-4 col-sm-4\">\r\n<div class=\"whychooses\">\r\n<div class=\"whychooses-icon\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/cap.svg\" /></div>\r\n\r\n<h3><a href=\"#\">Scholarship Facility</a></h3>\r\n\r\n<p>Dorem Ipsum has been the industry\'s standard dummy text ever since the en an unknown printer galley dear.</p>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-lg-4 col-md-4 col-sm-4\">\r\n<div class=\"whychooses\">\r\n<div class=\"whychooses-icon\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/people.svg\" /></div>\r\n\r\n<h3><a href=\"#\">Skilled Lecturers</a></h3>\r\n\r\n<p>Dorem Ipsum has been the industry\'s standard dummy text ever since the en an unknown printer galley dear.</p>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-lg-4 col-md-4 col-sm-4\">\r\n<div class=\"whychooses\">\r\n<div class=\"whychooses-icon\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/building-o.svg\" /></div>\r\n\r\n<h3><a href=\"#\">Book Library & Store</a></h3>\r\n\r\n<p>Dorem Ipsum has been the industry\'s standard dummy text ever since the en an unknown printer galley dear.</p>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</section>\r\n\r\n<div class=\"container spaceb60 spacet60\">\r\n<div class=\"row\">\r\n<div class=\"col-lg-4 col-md-4 col-sm-4\">\r\n<h2 class=\"head-title\">Our History 2018</h2>\r\n\r\n<p>Tmply dummy text of the printing and typesetting indust Lorem Ipsum has been theitry\'s snce simply dummy text of the printing.Phasellus enim libero, blandit vel sapien vita their.Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>\r\n</div>\r\n\r\n<div class=\"col-lg-4 col-md-4 col-sm-4\">\r\n<h2 class=\"head-title\">Over 100 Facalties</h2>\r\n\r\n<p>Tmply dummy text of the printing and typesetting indust Lorem Ipsum has been theitry\'s snce simply dummy text of the printing.Phasellus enim libero, blandit vel sapien vita their.Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>\r\n</div>\r\n\r\n<div class=\"col-lg-4 col-md-4 col-sm-4\">\r\n<h2 class=\"head-title\">We Have 15,000 Students</h2>\r\n\r\n<p>Tmply dummy text of the printing and typesetting indust Lorem Ipsum has been theitry\'s snce simply dummy text of the printing.Phasellus enim libero, blandit vel sapien vita their.Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>\r\n</div>\r\n</div>\r\n</div>', NULL, 0, NULL, 'no', '2024-11-25 08:28:25'),
(10, 'manual', 0, 'Course', 'page/course', 'page', 'course', '', '', '', '', '<div class=\"col-md-4 col-sm-4\">\r\n<div class=\"courses-box\">\r\n<div class=\"courses-box-img\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/courseimg1.jpg\" /></div>\r\n\r\n<div class=\"course-inner\"><a class=\"course-subject\" href=\"#\">Science</a>\r\n\r\n<h4>Electrical Engineering</h4>\r\n\r\n<p>All over the world, human beings create an immense and ever-increasing volume of data, with new kinds of data regularly...</p>\r\n<a class=\"btn-read\" href=\"#\">apply now</a></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-4 col-sm-4\">\r\n<div class=\"courses-box\">\r\n<div class=\"courses-box-img\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/courseimg2.jpg\" /></div>\r\n\r\n<div class=\"course-inner\"><a class=\"course-subject\" href=\"#\">Science</a>\r\n\r\n<h4>Electrical Engineering</h4>\r\n\r\n<p>All over the world, human beings create an immense and ever-increasing volume of data, with new kinds of data regularly...</p>\r\n<a class=\"btn-read\" href=\"#\">apply now</a></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-4 col-sm-4\">\r\n<div class=\"courses-box\">\r\n<div class=\"courses-box-img\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/courseimg3.jpg\" /></div>\r\n\r\n<div class=\"course-inner\"><a class=\"course-subject\" href=\"#\">Science</a>\r\n\r\n<h4>Electrical Engineering</h4>\r\n\r\n<p>All over the world, human beings create an immense and ever-increasing volume of data, with new kinds of data regularly...</p>\r\n<a class=\"btn-read\" href=\"#\">apply now</a></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-4 col-sm-4\">\r\n<div class=\"courses-box\">\r\n<div class=\"courses-box-img\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/courseimg4.jpg\" /></div>\r\n\r\n<div class=\"course-inner\"><a class=\"course-subject\" href=\"#\">Science</a>\r\n\r\n<h4>Electrical Engineering</h4>\r\n\r\n<p>All over the world, human beings create an immense and ever-increasing volume of data, with new kinds of data regularly...</p>\r\n<a class=\"btn-read\" href=\"#\">apply now</a></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-4 col-sm-4\">\r\n<div class=\"courses-box\">\r\n<div class=\"courses-box-img\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/courseimg6.jpg\" /></div>\r\n\r\n<div class=\"course-inner\"><a class=\"course-subject\" href=\"#\">Science</a>\r\n\r\n<h4>Electrical Engineering</h4>\r\n\r\n<p>All over the world, human beings create an immense and ever-increasing volume of data, with new kinds of data regularly...</p>\r\n<a class=\"btn-read\" href=\"#\">apply now</a></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-4 col-sm-4\">\r\n<div class=\"courses-box\">\r\n<div class=\"courses-box-img\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/courseimg5.jpg\" /></div>\r\n\r\n<div class=\"course-inner\"><a class=\"course-subject\" href=\"#\">Science</a>\r\n\r\n<h4>Electrical Engineering</h4>\r\n\r\n<p>All over the world, human beings create an immense and ever-increasing volume of data, with new kinds of data regularly...</p>\r\n<a class=\"btn-read\" href=\"#\">apply now</a></div>\r\n</div>\r\n</div>', NULL, 0, NULL, 'no', '2024-11-26 07:19:33'),
(11, 'manual', 0, 'School Uniform', 'page/school-uniform', 'page', 'school-uniform', '', '', '', '', '<h2>School Uniform</h2>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4>SCHOOL UNIFORM</h4>\r\n\r\n<p>The school uniform is to be worn as specified here below during the week; and each item must be labelled with the students Name & Class. All students must wear the School uniform daily. A pupil who is not dressed neatly and tidily in the full school uniform may not be permitted to sit in the class. The newly admitted students must start wearing the school uniform within two weeks of their admission.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4>GENERAL UNIFORM (MON, TUE, THU & FRI)</h4>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4>BOYS – CLASS KG-I, KG-II & I - V</h4>\r\n\r\n<p>Bottle green short pants, white & green striped shirt, white socks with two green stripes and black lace-up Velcro shoes.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4>BOYS CLASS VI- XII</h4>\r\n\r\n<p>Bottle green full pants, white and green striped shirt ,tie and school belt, white socks with two green stripes black lace-up shoes.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4>GIRLS CLASS KG-I, KG-II & I - VII</h4>\r\n\r\n<p>Bottle green frock attached with short sleeved shirt with green stripes, box pleats till knee , green bloomer, green striped white stockings, green hair band and school Tie & Belt.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4>GIRLS CLASS VIII–XII</h4>\r\n\r\n<p>Bottle green salwar, white kurta with green stripes & checkered jacket (pattern given by the school ) white socks and buckled black shoes.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4>SPORTS UNIFORM [WEDNESDAY & SATURDAY]</h4>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4>BOYS & GIRLS CLASS KG-I & KG-II :</h4>\r\n\r\n<p>Green track suit with school logo, beige T-shirt with green collar and shoulder stripes, black sports shoes and white socks with two green stripes.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4>BOYS & GIRLS [CLASS I-XII]</h4>\r\n\r\n<p>Green track suit with school logo, beige T-shirt with house coloured collar and shoulder stripes, black sports shoes and white socks with two green stripes. House colour ribbons for girls.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4>WINTER UNIFORM</h4>\r\n\r\n<h4>&nbsp;</h4>\r\n\r\n<h4>BOYS & GIRLS – CLASSES KG-I, KG-II & I - V</h4>\r\n\r\n<p>Long pants, white stockings with two green stripes & Sweaters.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h4>BOYS & GIRLS - CLASS VI–XII</h4>\r\n\r\n<p>Along with school uniform, bottle green blazer and socks/Stockings with two green stripes to be worn by both boys and girls during winter.</p>', NULL, 0, NULL, 'no', '2024-11-26 07:20:12'),
(12, 'manual', 0, 'Gallery', 'page/gallery', 'page', 'gallery', '', '', '', '', '<h2>Gallery</h2>', NULL, 0, NULL, 'no', '2024-11-26 10:15:56'),
(13, 'manual', 0, 'News', 'page/news', 'page', 'news', '', '', '', '', 'News', NULL, 0, NULL, 'no', '2024-11-26 07:22:02'),
(14, 'manual', 0, 'Events', 'page/events', 'page', 'events', '', '', '', '', '<h2>Events</h2>', NULL, 0, NULL, 'no', '2024-11-26 07:22:30'),
(15, 'manual', 0, 'Teacher', 'page/teacher', 'page', 'teacher', '', '', '', '', '<div class=\"col-md-3 col-sm-3\">\r\n<div class=\"staffteam\">\r\n<div class=\"staffteamimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/teacher1-1556236800.jpg\" />\r\n<ul class=\"social-links\">\r\n	<li>Facebook</li>\r\n	<li>Twitter</li>\r\n	<li>Linkedin</li>\r\n	<li>Google Plus</li>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"staff-content\">\r\n<h3>Princy Flora</h3>\r\n<span class=\"post\">English Tutor</span></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-3 col-sm-3\">\r\n<div class=\"staffteam\">\r\n<div class=\"staffteamimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/teacher2-1556236800.jpg\" />\r\n<ul class=\"social-links\">\r\n	<li>Facebook</li>\r\n	<li>Twitter</li>\r\n	<li>Linkedin</li>\r\n	<li>Google Plus</li>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"staff-content\">\r\n<h3>Jesica Matt</h3>\r\n<span class=\"post\">Art Teacher</span></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-3 col-sm-3\">\r\n<div class=\"staffteam\">\r\n<div class=\"staffteamimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/teacher3.jpg\" />\r\n<ul class=\"social-links\">\r\n	<li>Facebook</li>\r\n	<li>Twitter</li>\r\n	<li>Linkedin</li>\r\n	<li>Google Plus</li>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"staff-content\">\r\n<h3>Janaton Doe</h3>\r\n<span class=\"post\">Math Teacher</span></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-3 col-sm-3\">\r\n<div class=\"staffteam\">\r\n<div class=\"staffteamimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/teacher4.jpg\" />\r\n<ul class=\"social-links\">\r\n	<li>Facebook</li>\r\n	<li>Twitter</li>\r\n	<li>Linkedin</li>\r\n	<li>Google Plus</li>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"staff-content\">\r\n<h3>Janaton Doe</h3>\r\n<span class=\"post\">English Teacher</span></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-3 col-sm-3\">\r\n<div class=\"staffteam\">\r\n<div class=\"staffteamimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/teacher5.jpg\" />\r\n<ul class=\"social-links\">\r\n	<li>Facebook</li>\r\n	<li>Twitter</li>\r\n	<li>Linkedin</li>\r\n	<li>Google Plus</li>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"staff-content\">\r\n<h3>Broklyn Doel</h3>\r\n<span class=\"post\">History</span></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-3 col-sm-3\">\r\n<div class=\"staffteam\">\r\n<div class=\"staffteamimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/teacher7.jpg\" />\r\n<ul class=\"social-links\">\r\n	<li>Facebook</li>\r\n	<li>Twitter</li>\r\n	<li>Linkedin</li>\r\n	<li>Google Plus</li>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"staff-content\">\r\n<h3>Williamson</h3>\r\n<span class=\"post\">web developer</span></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-3 col-sm-3\">\r\n<div class=\"staffteam\">\r\n<div class=\"staffteamimg\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/teacher6.jpg\" />\r\n<ul class=\"social-links\">\r\n	<li>Facebook</li>\r\n	<li>Twitter</li>\r\n	<li>Linkedin</li>\r\n	<li>Google Plus</li>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"staff-content\">\r\n<h3>Steve Alia</h3>\r\n<span class=\"post\">Science Teacher</span></div>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-3 col-sm-3\">\r\n<div class=\"staffteam\">\r\n<div class=\"staffteamimg\"><img alt=\"\" src=\"https://demo.smart-school.in/uploads/gallery/media/teacher2.jpg\" />\r\n<ul class=\"social-links\">\r\n	<li>Facebook</li>\r\n	<li>Twitter</li>\r\n	<li>Linkedin</li>\r\n	<li>Google Plus</li>\r\n</ul>\r\n</div>\r\n\r\n<div class=\"staff-content\">\r\n<h3>Williamson</h3>\r\n<span class=\"post\">web developer</span></div>\r\n</div>\r\n</div>', NULL, 0, NULL, 'no', '2024-11-26 07:22:50'),
(16, 'manual', 0, 'Introduction', 'page/introduction', 'page', 'introduction', '', '', '', '', '<h2>Introduction</h2>', NULL, 0, NULL, 'no', '2024-11-26 07:23:19'),
(17, 'manual', 0, 'School History', 'page/school-history', 'page', 'school-history', '', '', '', '', '<h2>School History</h2>', NULL, 0, NULL, 'no', '2024-11-26 07:23:42'),
(18, 'manual', 0, 'Facilities', 'page/facilities', 'page', 'facilities', '', '', '', '', '<div class=\"container spaceb60\">\r\n<div class=\"row\">\r\n<div class=\"col-md-12 col-sm-12\">\r\n<h2 class=\"counter-title text-center pb20\">Outreach,Exposure & International Exchange Program</h2>\r\n\r\n<p><span background-color:=\"\" font-size:=\"\" helvetica=\"\" style=\"color: rgb(51, 51, 51);\">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</span></p>\r\n\r\n<p><span background-color:=\"\" font-size:=\"\" helvetica=\"\" style=\"color: rgb(51, 51, 51);\">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</span></p>\r\n</div>\r\n</div>\r\n</div>\r\n\r\n<section class=\"fullwidth facility_bg\">\r\n<div class=\"container\">\r\n<div class=\"row\">\r\n<div class=\"col-md-12 col-sm-12 mb20\">\r\n<h2 class=\"counter-title text-center\">Bus Transportation</h2>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-sm-6\">\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</p>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-sm-6\"><img class=\"img-responsive img-rounded\" src=\"https://demo.smart-school.in/uploads/gallery/media/bus.jpg\" /></div>\r\n</div>\r\n</div>\r\n</section>\r\n\r\n<div class=\"container spaceb60 spacet60\">\r\n<div class=\"row\">\r\n<div class=\"col-md-12 col-sm-12 mb20\">\r\n<h2 class=\"counter-title text-center\">AC Smart Classrooms</h2>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-sm-6\"><img class=\"img-responsive img-rounded\" src=\"https://demo.smart-school.in/uploads/gallery/media/classrooms.jpg\" /></div>\r\n\r\n<div class=\"col-md-6 col-sm-6\">\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</p>\r\n\r\n<ul class=\"list-group\" style=\"margin-left: 15px; margin-bottom: 15px;\">\r\n	<li>Visual learning, animated multimedia lessons</li>\r\n	<li>Quick and immersive learning</li>\r\n	<li>Progressive improvement in core student learning</li>\r\n	<li>High Overall School performance</li>\r\n	<li>Student assessment and evaluation</li>\r\n	<li>Report cards</li>\r\n</ul>\r\n\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</p>\r\n</div>\r\n</div>\r\n</div>\r\n\r\n<section class=\"fullwidth facility_bg\">\r\n<div class=\"container\">\r\n<div class=\"row\">\r\n<div class=\"col-md-12 col-sm-12 mb20\">\r\n<h2 class=\"counter-title text-center\">Laboratories</h2>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-sm-6\">\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</p>\r\n\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</p>\r\n\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</p>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-sm-6\"><img class=\"img-responsive img-rounded\" src=\"https://demo.smart-school.in/uploads/gallery/media/laboratories.jpg\" /></div>\r\n</div>\r\n</div>\r\n</section>\r\n\r\n<div class=\"container spaceb60 spacet60\">\r\n<div class=\"row\">\r\n<div class=\"col-md-12 col-sm-12 mb20\">\r\n<h2 class=\"counter-title text-center\">Library</h2>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-sm-6\"><img class=\"img-responsive img-rounded\" src=\"https://demo.smart-school.in/uploads/gallery/media/library.jpg\" /></div>\r\n\r\n<div class=\"col-md-6 col-sm-6\">\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</p>\r\n\r\n<ul class=\"list-group\" style=\"margin-left: 15px; margin-bottom: 15px;\">\r\n	<li>Visual learning, animated multimedia lessons</li>\r\n	<li>Quick and immersive learning</li>\r\n	<li>Progressive improvement in core student learning</li>\r\n	<li>High Overall School performance</li>\r\n	<li>Student assessment and evaluation</li>\r\n	<li>Report cards</li>\r\n</ul>\r\n\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</p>\r\n</div>\r\n</div>\r\n</div>', NULL, 0, NULL, 'no', '2024-11-26 07:24:07'),
(19, 'manual', 0, 'Principal Message', 'page/principal-message', 'page', 'principal-message', '', '', '', '', '<div class=\"col-md-6 col-sm-6\">\r\n<div class=\"col-md-6 col-sm-6\">\r\n<div class=\"col-md-6 col-sm-6\">\r\n<div class=\"col-md-6 col-sm-6\"><img class=\"img-responsive img-rounded\" src=\"https://demo.smart-school.in/uploads/gallery/media/principal.jpg\" /></div>\r\n\r\n<div class=\"col-md-6 col-sm-6\">\r\n<h2 class=\"mt0\">Message From Principal</h2>\r\n\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>\r\n\r\n<p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable.</p>\r\n</div>\r\n</div>\r\n</div>\r\n</div>', NULL, 0, NULL, 'no', '2024-12-09 11:58:30'),
(20, 'manual', 0, 'School Management', 'page/school-management', 'page', 'school-management', '', '', '', '', '<h4>School Management</h4>\r\n\r\n<p>School Management System is a multipurpose school management software which is used for all administration, management and learning related activities. Use School management information system to manage students, teachers, employees, courses and all the system and process related to running your institute efficiently. Online school management system delivers premier online fee payment, registration and data management tools for schools, administrators, finance personnel and students. It\'s simple, web-based technologies help improve efficiency and reduce costs associated with managing school registration, budgeting, fund accounting, fundraising, after-school care, and more. Online school management solution streamlines school registration with automatic back-office administration, campus recreation and intramurals, facilities, student data, school accounting, web site content management and enables educational institutions to focus on teaching and enriching the lives of students.</p>\r\n\r\n<ol type=\"1\">\r\n	<li>User friendly Interface</li>\r\n	<li>School Detail Management</li>\r\n	<li>Registration /Admission Management</li>\r\n	<li>Fee Category Management</li>\r\n	<li>Student Fee Management (Fee Detail, Fee Receipt etc.)</li>\r\n	<li>Student Administration</li>\r\n	<li>Class/div Management</li>\r\n	<li>ID Cards Printing</li>\r\n	<li>Student Information Management</li>\r\n	<li>Leave & Attendance Management</li>\r\n	<li>Advance Search for Student Information</li>\r\n	<li>Lesson Planning</li>\r\n	<li>Time Table</li>\r\n	<li>Examination & Evaluation Process</li>\r\n	<li>Online Exam</li>\r\n	<li>SMS Alerts to Students</li>\r\n	<li>Transfer Certificate Management</li>\r\n	<li>Reports Management (Daily Collection, Fee Outstanding, Scroll Sheet etc.)</li>\r\n	<li>Transport Management</li>\r\n	<li>Transport Fee Collection Management</li>\r\n	<li>Visitor Management</li>\r\n	<li>Compliant Management</li>\r\n	<li>Employee Registration Management</li>\r\n	<li>Employee Attendance Management</li>\r\n	<li>Holiday Management</li>\r\n	<li>Salary Structure Management</li>\r\n	<li>Employee Salary Management</li>\r\n	<li>Employee Attendance and Salary Reports</li>\r\n	<li>Secure and Reliable</li>\r\n	<li>Many more Feature Available</li>\r\n</ol>', NULL, 0, NULL, 'no', '2024-11-26 07:24:57'),
(21, 'manual', 0, 'Know Us', 'page/know-us', 'page', 'know-us', '', '', '', '', '<div class=\"container spaceb60 spacet60\">\r\n<div class=\"row\">\r\n<div class=\"col-md-6 col-sm-6\">\r\n<h2 class=\"pb20\">Introduction</h2>\r\n\r\n<p>Mount Carmel School , Baghmugalia, Bhopal is a recognized unaided Christian minority institution founded in the year 1999, affiliated to the Council for the Indian School Certificate Examination , New Delhi (School Code Mp023). The School prepares its students for the ICSE and the ISC Examination at X and XII Level respectively.</p>\r\n\r\n<p>The school is managed by the Sisters of the Congregation of the Mother of Carmel . The Congregation is a religious and charitable organization and its members offer their dedicated and selfless service for the intellectual , moral and aesthetic development of the people in the vicinity.</p>\r\n</div>\r\n\r\n<div class=\"col-md-6 col-sm-6\">\r\n<div class=\"visionbg text-center\">\r\n<h2 class=\"pb20\">Our Vision</h2>\r\n\r\n<p>To give to the world \"Icons\" who can win the hearts and minds of others, put service before self, and retain their humility and values even as they soar high.</p>\r\n\r\n<p>Tmply dummy text of the printing and typesetting indust Lorem Ipsum has been theitry\'s snce simply dummy text of the printing.Phasellus enim libero, blandit vel sapien vita their.Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n\r\n<section class=\"bggray fullwidth\">\r\n<div class=\"container spaceb60 spacet60\">\r\n<div class=\"row\">\r\n<div class=\"col-md-8 col-lg-offset-2 col-sm-12 text-center pb20\">\r\n<h2>Why Choose Our Institution?</h2>\r\n\r\n<p>Tmply dummy text of the printing and typesetting industry. Lorem Ipsum has been theindustry\'s standard dummy text ever since the 1500s, when an unknown printer took.</p>\r\n</div>\r\n\r\n<div class=\"col-lg-4 col-md-4 col-sm-4\">\r\n<div class=\"whychooses misssion\">\r\n<div class=\"whychooses-icon\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/cap.svg\" /></div>\r\n\r\n<h3>Admissions</h3>\r\n\r\n<p>To develop an understanding & appreciation of the global dimension of our world; making our children appreciative of their history, culture & traditions, whilst being open to other cultures and alternative views of the world.</p>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-lg-4 col-md-4 col-sm-4\">\r\n<div class=\"whychooses misssion\">\r\n<div class=\"whychooses-icon\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/people.svg\" /></div>\r\n\r\n<h3>Skilled Lecturers</h3>\r\n\r\n<p>Dorem Ipsum has been the industry\'s standard dummy text ever since the en an unknown printer galley dear.</p>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-lg-4 col-md-4 col-sm-4\">\r\n<div class=\"whychooses misssion\">\r\n<div class=\"whychooses-icon\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/building-o.svg\" /></div>\r\n\r\n<h3>Book Library & Store</h3>\r\n\r\n<p>Dorem Ipsum has been the industry\'s standard dummy text ever since the en an unknown printer galley dear.</p>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-lg-4 col-md-4 col-sm-4\">\r\n<div class=\"whychooses misssion\">\r\n<div class=\"whychooses-icon\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/iconic-f14.png\" /></div>\r\n\r\n<h3>Admissions</h3>\r\n\r\n<p>To develop an understanding & appreciation of the global dimension of our world; making our children appreciative of their history, culture & traditions, whilst being open to other cultures and alternative views of the world.</p>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-lg-4 col-md-4 col-sm-4\">\r\n<div class=\"whychooses misssion\">\r\n<div class=\"whychooses-icon\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/iconic-f15.png\" /></div>\r\n\r\n<h3>Skilled Lecturers</h3>\r\n\r\n<p>Dorem Ipsum has been the industry\'s standard dummy text ever since the en an unknown printer galley dear.</p>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-lg-4 col-md-4 col-sm-4\">\r\n<div class=\"whychooses misssion\">\r\n<div class=\"whychooses-icon\"><img src=\"https://demo.smart-school.in/uploads/gallery/media/iconic-f16.png\" /></div>\r\n\r\n<h3>Book Library & Store</h3>\r\n\r\n<p>Dorem Ipsum has been the industry\'s standard dummy text ever since the en an unknown printer galley dear.</p>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</section>', NULL, 0, NULL, 'no', '2024-11-26 07:25:24');
INSERT INTO `front_cms_pages` (`id`, `page_type`, `is_homepage`, `title`, `url`, `type`, `slug`, `meta_title`, `meta_description`, `meta_keyword`, `feature_image`, `description`, `publish_date`, `publish`, `sidebar`, `is_active`, `created_at`) VALUES
(22, 'manual', 0, 'Approach', 'page/approach', 'page', 'approach', '', '', '', '', '<div class=\"container spaceb60 spacet60\">\r\n<div class=\"row\">\r\n<div class=\"col-md-8 col-sm-8\">\r\n<h2 class=\"pb20\">Singular Focus; Multiple Intelligences</h2>\r\n\r\n<p>Mount Carmel School , Baghmugalia, Bhopal is a recognized unaided Christian minority institution founded in the year 1999, affiliated to the Council for the Indian School Certificate Examination , New Delhi (School Code Mp023). The School prepares its students for the ICSE and the ISC Examination at X and XII Level respectively.</p>\r\n\r\n<p>The school is managed by the Sisters of the Congregation of the Mother of Carmel . The Congregation is a religious and charitable organization and its members offer their dedicated and selfless service for the intellectual , moral and aesthetic development of the people in the vicinity.</p>\r\n</div>\r\n\r\n<div class=\"col-md-4 col-sm-4\">\r\n<div class=\"about_img\"><img class=\"img-responsive img-rounded\" src=\"https://demo.smart-school.in/uploads/gallery/media/about.jpg\" /></div>\r\n</div>\r\n</div>\r\n</div>\r\n\r\n<section class=\"bggray fullwidth\">\r\n<div class=\"container spaceb60 spacet60\">\r\n<div class=\"row\">\r\n<div class=\"col-md-12 whitebox\">\r\n<div class=\"col-lg-3 col-md-3 col-sm-3\"><img class=\"img-responsive img-rounded\" src=\"https://demo.smart-school.in/uploads/gallery/media/classrooms.jpg\" /></div>\r\n\r\n<div class=\"col-lg-9 col-md-9 col-sm-9\">\r\n<h2>Teaching Methodology</h2>\r\n\r\n<p class=\"text-justify\">The school is managed by the Sisters of the Congregation of the Mother of Carmel . The Congregation is a religious and charitable organization and its members offer their dedicated and selfless service for the intellectual , moral and aesthetic development of the people in the vicinity. The school is managed by the Sisters of the Congregation of the Mother of Carmel . The Congregation is a religious and charitable organization and its members offer their dedicated and selfless service for the intellectual , moral and aesthetic development of the people in the vicinity.</p>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</section>\r\n\r\n<div class=\"container spaceb60 spacet60 text-center\">\r\n<div class=\"row\">\r\n<div class=\"col-md-8 col-md-offset-2\">\r\n<h2>Professional Learning Program</h2>\r\n\r\n<p class=\"text-justify\">The School has partnered with Learning Forward India, whereby Educators undertake structured Training sessions and self-directed professional learning.</p>\r\n\r\n<p class=\"text-justify\">The Professional Learning Program (PLP) aligns with the mission of Learning Forward (www.learningforward.org), the world\'s largest body of individual educators, with a vision of excellent teaching and learning every day. The Iconic School, in collaboration with The Learning Forward India Academy promotes building the capacity of leaders to establish and sustain highly effective professional learning. Professional learning means a comprehensive, sustained and intensive approach to improving teachers’ and principals’ effectiveness in raising student achievement.</p>\r\n</div>\r\n</div>\r\n</div>', NULL, 0, NULL, 'no', '2024-11-26 07:25:49'),
(23, 'manual', 0, 'Pre Primary', 'page/pre-primary', 'page', 'pre-primary', '', '', '', '', '<div class=\"col-md-4 col-sm-6\">\r\n<div class=\"around40\">\r\n<div class=\"col-md-4 col-sm-6\">\r\n<div class=\"around40\">\r\n<div class=\"col-md-4 col-sm-6\">\r\n<div class=\"around40\">\r\n<div class=\"col-md-4 col-sm-6\">\r\n<div class=\"around40\">\r\n<div class=\"col-md-4 col-sm-6\">\r\n<div class=\"around40\">\r\n<div class=\"col-md-4 col-sm-6\">\r\n<div class=\"around40\">\r\n<div class=\"col-md-4 col-sm-6\">\r\n<div class=\"around40\"><img alt=\"\" height=\"75\" src=\"https://demo.smart-school.in/uploads/gallery/media/iconic-f17-1.png\" width=\"75\" />\r\n<h2>Involve</h2>\r\n\r\n<p class=\"blue-text\">Pre-Primary</p>\r\n\r\n<p>Mount Carmel School , Baghmugalia, Bhopal is a recognized unaided Christian minority institution founded in the year 1999, affiliated to the Council for the Indian School Certificate Examination , New Delhi (School Code Mp023). The School prepares its students for the ICSE and the ISC Examination at X and XII Level respectively. The school is managed by the Sisters of the Congregation of the Mother of Carmel</p>\r\n</div>\r\n</div>\r\n\r\n<div class=\"col-md-8 col-sm-6\"><img class=\"img-responsive\" src=\"https://demo.smart-school.in/uploads/gallery/media//classrooms.jpg\" /></div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n</div>', NULL, 0, NULL, 'no', '2024-12-09 11:53:42'),
(24, 'manual', 0, 'Primary', 'page/primary', 'page', 'primary', '', '', '', '', '<div class=\"col-md-8 col-sm-6\"><img class=\"img-responsive\" src=\"https://demo.smart-school.in/uploads/gallery/media//classrooms.jpg\" /></div>\r\n\r\n<div class=\"col-md-4 col-sm-6\">\r\n<div class=\"around40\"><img alt=\"\" height=\"75\" src=\"https://demo.smart-school.in/uploads/gallery/media/iconic-f17-1.png\" width=\"75\" />\r\n<h2>Instill</h2>\r\n\r\n<p class=\"blue-text\">Pre-Primary</p>\r\n\r\n<p>Mount Carmel School, Baghmugalia, Bhopal is a recognized unaided Christian minority institution founded in the year 1999, affiliated to the Council for the Indian School Certificate Examination , New Delhi (School Code Mp023). The School prepares its students for the ICSE and the ISC Examination at X and XII Level respectively. The school is managed by the Sisters of the Congregation of the Mother of Carmel</p>\r\n</div>\r\n</div>', NULL, 0, NULL, 'no', '2024-11-26 07:26:42'),
(25, 'manual', 0, 'Sports', 'page/sports', 'page', 'sports', '', '', '', '', '<div class=\"col-md-5 col-sm-5\"><img class=\"img-responsive\" src=\"https://demo.smart-school.in/uploads/gallery/media//classrooms.jpg\" /></div>\r\n\r\n<div class=\"col-md-7 col-sm-7\">\r\n<h2>Sports</h2>\r\n\r\n<p>Mount Carmel School , Baghmugalia, Bhopal is a recognized unaided Christian minority institution founded in the year 1999, affiliated to the Council for the Indian School Certificate Examination , New Delhi (School Code Mp023). The School prepares its students for the ICSE and the ISC Examination at X and XII Level respectively. The school is managed by the Sisters of the Congregation of the Mother of Carmel</p>\r\n\r\n<p>Mount Carmel School , Baghmugalia, Bhopal is a recognized unaided Christian minority institution founded in the year 1999, affiliated to the Council for the Indian School Certificate Examination , New Delhi (School Code Mp023). The School prepares its students for the ICSE and the ISC Examination at X and XII Level respectively. The school is managed by the Sisters of the Congregation of the Mother of Carmel</p>\r\n</div>\r\n\r\n<div class=\"col-sm-12 col-md-12 text-justify sports pt20\">\r\n<h2>Sports for Icons -&nbsp;<b>\"Right to play & play for all\"</b></h2>\r\n\r\n<p>A healthy mind resides in a healthy body. Games and sports education are not only significant for triumph in the student life but it is a key to achievement in every stride of life. The Mount School believes in \"Right to play & play for all\". The school sports structure is developed to cater to various needs of the child. If sports are inculcated from a very young age, it helps build character and discipline. It gives students the confidence to trust their own individuality.</p>\r\n\r\n<h2>For Kinder Garten – SPORTS SPROUTS</h2>\r\n\r\n<p>In this, natural curiosity and interaction with the environment stimulate the growth of motor activity which helps students to make a strong foundation for the sports. The body movement and coordination of the child is tapped through various locomotor, non-locomotor and manipulative activities. For the little icons, skill based sports like skating and gymnastic are offered.</p>\r\n\r\n<h2>For Primary - ALL-ROUNDER</h2>\r\n\r\n<p>The School provides opportunities to students to have the benefit of maximum sports with enjoyment. Education on fundamentals of every sports activity is imparted wherein the students get an experience of various sports like football, cricket, skating, swimming, athletics, etc. This varied experience enables students to identify a sport of their choice and excel in the same.</p>\r\n\r\n<h2>For Middle– SPECIALISATION</h2>\r\n\r\n<p>At this stage, sports programme encourages the students to excel in one sport, so that they are able to chip in and execute well in Intramural and Extramural competitions. Sports offered for the specialization include basketball, cricket and football.</p>\r\n\r\n<p>We, at Mount, believe that as long as the icons are involved in sports activities, all their senses, the locomotor system and intellectual capacities are engaged. By playing sports, child becomes stronger, physically as well as mentally. Above all, they learn to overcome obstacles and challenges in real life situations.</p>\r\n</div>', NULL, 0, NULL, 'no', '2024-11-26 07:27:25'),
(26, 'manual', 0, 'Student Council', 'page/student-council', 'page', 'student-council', '', '', '', '', '<h2>Student Council</h2>\r\n\r\n<p>Mount Carmel School , Baghmugalia, Bhopal is a recognized unaided Christian minority institution founded in the year 1999, affiliated to the Council for the Indian School Certificate Examination , New Delhi (School Code Mp023). The School prepares its students for the ICSE and the ISC Examination at X and XII Level respectively.</p>\r\n\r\n<ol class=\"list-1\">\r\n	<li>School&nbsp; diary&nbsp; is to be brought to school daily without fail.</li>\r\n	<li>Students should come to school in complete school uniform, on all school days ( including PTM).</li>\r\n	<li>Punctuality &nbsp;a must and late comers will be suitably checked with corrective measures.</li>\r\n	<li>No mobile phone is to be brought to school for what so ever reason. The set will be confiscated and very strict action will be taken against the user. The set will be returned only at the end of the<br />\r\n	&nbsp;&nbsp;session .</li>\r\n	<li>Students are discouraged to come to school by private vans using gas kits or over loaded.</li>\r\n	<li>85 % attendance is a must for every student.</li>\r\n	<li>A pupil who uses unfair means during examinations will be given zero in the subject and repetition of the same shall lead to dismissal.</li>\r\n	<li>Parents / guardians can meet the principal in her office from 9:00 am to 10:00 am. on all school<br />\r\n	working days.</li>\r\n	<li>Parents / guardians should meet teachers only on PTM or with appointment in special cases.</li>\r\n	<li>It is compulsory for all students to participate in sports, games and co-curricular&nbsp; activities of<br />\r\n	the school .</li>\r\n	<li>In case of accidents, school will provide only first aid; other related expenses will be borne by&nbsp;<br />\r\n	Parents / guardians.</li>\r\n</ol>\r\n\r\n<div class=\"col-md-12\">\r\n<h2>Student Council 2018-19</h2>\r\n\r\n<table class=\"table table-striped table-bordered\">\r\n	<tbody>\r\n		<tr>\r\n			<td>School Captain</td>\r\n			<td>Yashi Agrawal</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Sports Captain</td>\r\n			<td>Krishna Soni</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Editor-in-Chief</td>\r\n			<td>Tanisha Agrawal</td>\r\n		</tr>\r\n		<tr>\r\n			<th colspan=\"12\">\r\n			<center>\r\n			<h3>Houses</h3>\r\n			</center>\r\n			</th>\r\n		</tr>\r\n		<tr>\r\n			<td>Jade House Captain</td>\r\n			<td>Naman Sinha</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Jade Vice-House Captain</td>\r\n			<td>Kartik Marale</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Topaz House Captain</td>\r\n			<td>Ansh Budhwani</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Topaz Vice-House Captain</td>\r\n			<td>Aparna Kushwaha</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Sapphire House Captain</td>\r\n			<td>Alankriti Dube</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Sapphire Vice-House Captain</td>\r\n			<td>Prakhar Verma</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Ruby House Captain</td>\r\n			<td>Niyati Sinha</td>\r\n		</tr>\r\n		<tr>\r\n			<td>Ruby Vice-House Captain</td>\r\n			<td>Hiren Jain</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', NULL, 0, NULL, 'no', '2024-11-26 07:27:40'),
(27, 'manual', 0, 'Houses & Mentoring', 'page/houses-mentoring', 'page', 'houses-mentoring', '', '', '', '', '<h3>School House System and Mentoring System</h3>\r\n\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h3>Grade 1 to 4 – Mentoring System</h3>\r\n\r\n<p>The school is managed by the Sisters of the Congregation of the Mother of Carmel . The Congregation is a religious and charitable organization and its members offer their dedicated and selfless service for the intellectual , moral and aesthetic development of the people in the vicinity</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<h3>Grade 5 onwards – House System</h3>\r\n\r\n<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book</p>', NULL, 0, NULL, 'no', '2024-11-26 07:28:18'),
(28, 'manual', 0, 'Career Counselling', 'page/career-counselling', 'page', 'career-counselling', '', '', '', '', '<div class=\"col-lg-6 col-md-6 col-sm-12\"><img class=\"img-responsive img-rounded\" src=\"https://demo.smart-school.in/uploads/gallery/media/classrooms.jpg\" /></div>\r\n\r\n<div class=\"col-lg-6 col-md-6 col-sm-12\">\r\n<h2>Career Counselling</h2>\r\n\r\n<p class=\"text-justify\">The school is managed by the Sisters of the Congregation of the Mother of Carmel . The Congregation is a religious and charitable organization and its members offer their dedicated and selfless service for the intellectual , moral and aesthetic development of the people in the vicinity. The school is managed by the Sisters of the Congregation of the Mother of Carmel . The Congregation is a religious and charitable organization and its members offer their dedicated and selfless service for the intellectual , moral and aesthetic development of the people in the vicinity.</p>\r\n\r\n<p>Counselling activities encompass the following :</p>\r\n\r\n<ul class=\"square\">\r\n	<li>Helping students to identify their areas of interest and aptitude.</li>\r\n	<li>Guiding students about application processes in India and abroad.</li>\r\n	<li>Providing updated information about various institutions, courses and careers.</li>\r\n	<li>Giving sustained support in the admission process that includes collating and dispatching transcripts, writing out recommendation letters, predicted grades and school profiles and helping with the Personal Statements to be submitted.</li>\r\n</ul>\r\n</div>', NULL, 0, NULL, 'no', '2024-11-26 07:28:38'),
(29, 'manual', 0, 'Annual Sports Day', 'page/annual-sports-day', 'page', 'annual-sports-day', '', '', '', '', '<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<span style=\"font-size: 36px;\"><strong><span style=\"font-family: arial, helvetica, sans-serif;\">&nbsp;Annual Sports Day</span></strong></span><span style=\"font-family: arial, helvetica, sans-serif;\"></span></p>\r\n\r\n<hr /><span style=\"font-size: 18px;\"><span background-color:=\"\" google=\"\" style=\"color: rgb(77, 81, 86);\"><span background-color:=\"\" google=\"\"><br />\r\n<span style=\"font-size: 18px;\"><span background-color:=\"\" google=\"\" style=\"color: rgb(77, 81, 86);\">Sports Day&nbsp;</span><span google=\"\" style=\"background-color: rgb(211, 227, 253); color: rgb(4, 12, 40);\">encourages active participation in sports, promoting physical health and combating issues like obesity and sedentary habits</span><span background-color:=\"\" google=\"\" style=\"color: rgb(77, 81, 86);\">. Involvement in team sports teaches students teamwork, cooperation, and unity, skills transferrable to academics and future careers.<span background-color:=\"\" google=\"\">On this day, students participate in various sporting activities such as running, jumping, throwing, and team games. The day begins with a parade, where all the students march in their respective houses or classes, displaying their house colors and flags.<br />\r\n<br />\r\n<img src=\"https://demo.smart-school.in/uploads/gallery/media/1709637621-62224244865e6fff58972d!image.png?1709639995\" /></span></span></span></span></span></span>', NULL, 0, NULL, 'no', '2024-11-26 10:28:36'),
(30, 'manual', 0, 'new page test', 'page/new-page-test', 'page', 'new-page-test', '', '', '', '', 'test<img src=\"https://demo.smart-school.in/uploads/gallery/media/1709636432-190217141365e6fb507857b!1695468824-759901127650ecd18c2cf7!ogin_bg3.jpg?1709646851\" />', NULL, 0, NULL, 'no', '2024-11-26 07:29:26'),
(32, 'manual', 0, 'Privacy Policy', 'page/privacy-policy', 'page', 'privacy-policy', '', '', '', '', '<!DOCTYPE html>\r\n    <html>\r\n    <head>\r\n      <meta charset=\'utf-8\'>\r\n      <meta name=\'viewport\' content=\'width=device-width\'>\r\n      <title>Privacy Policy</title>\r\n      <style> body { font-family: \'Helvetica Neue\', Helvetica, Arial, sans-serif; padding:1em; } </style>\r\n    </head>\r\n    <body>\r\n    <strong>Privacy Policy</strong><p>This privacy policy applies to the Edupixel Schools - Learning Something app (hereby referred to as \"Application\") for mobile devices that was created by Muneshkumar Chodavadiya (hereby referred to as \"Service Provider\") as a Free service. This service is intended for use \"AS IS\".</p><br><strong>Information Collection and Use</strong><p>The Application collects information when you download and use it. This information may include information such as </p><ul><li>Your device\'s Internet Protocol address (e.g. IP address)</li><li>The pages of the Application that you visit, the time and date of your visit, the time spent on those pages</li><li>The time spent on the Application</li><li>The operating system you use on your mobile device</li></ul><p></p><br><p style=\"\">The Application does not gather precise information about the location of your mobile device.</p><div style=\"display: none;\"><p>The Application collects your device\'s location, which helps the Service Provider determine your approximate geographical location and make use of in below ways:</p><ul><li>Geolocation Services: The Service Provider utilizes location data to provide features such as personalized content, relevant recommendations, and location-based services.</li><li>Analytics and Improvements: Aggregated and anonymized location data helps the Service Provider to analyze user behavior, identify trends, and improve the overall performance and functionality of the Application.</li><li>Third-Party Services: Periodically, the Service Provider may transmit anonymized location data to external services. These services assist them in enhancing the Application and optimizing their offerings.</li></ul></div><br><p>The Service Provider may use the information you provided to contact you from time to time to provide you with important information, required notices and marketing promotions.</p><br><p>For a better experience, while using the Application, the Service Provider may require you to provide us with certain personally identifiable information, including but not limited to innovationhub2022@gmail.com. The information that the Service Provider request will be retained by them and used as described in this privacy policy.</p><br><strong>Third Party Access</strong><p>Only aggregated, anonymized data is periodically transmitted to external services to aid the Service Provider in improving the Application and their service. The Service Provider may share your information with third parties in the ways that are described in this privacy statement.</p><div><br><p>Please note that the Application utilizes third-party services that have their own Privacy Policy about handling data. Below are the links to the Privacy Policy of the third-party service providers used by the Application:</p><ul><li><a href=\"https://www.google.com/policies/privacy/\" target=\"_blank\" rel=\"noopener noreferrer\">Google Play Services</a></li><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----></ul></div><br><p>The Service Provider may disclose User Provided and Automatically Collected Information:</p><ul><li>as required by law, such as to comply with a subpoena, or similar legal process;</li><li>when they believe in good faith that disclosure is necessary to protect their rights, protect your safety or the safety of others, investigate fraud, or respond to a government request;</li><li>with their trusted services providers who work on their behalf, do not have an independent use of the information we disclose to them, and have agreed to adhere to the rules set forth in this privacy statement.</li></ul><p></p><br><strong>Opt-Out Rights</strong><p>You can stop all collection of information by the Application easily by uninstalling it. You may use the standard uninstall processes as may be available as part of your mobile device or via the mobile application marketplace or network.</p><br><strong>Data Retention Policy</strong><p>The Service Provider will retain User Provided data for as long as you use the Application and for a reasonable time thereafter. If you\'d like them to delete User Provided Data that you have provided via the Application, please contact them at innovationhub2022@gmail.com and they will respond in a reasonable time.</p><br><strong>Children</strong><p>The Service Provider does not use the Application to knowingly solicit data from or market to children under the age of 13.</p><div><br><p>The Application does not address anyone under the age of 13.\r\nThe Service Provider does not knowingly collect personally\r\nidentifiable information from children under 13 years of age. In the case\r\nthe Service Provider discover that a child under 13 has provided\r\npersonal information, the Service Provider will immediately\r\ndelete this from their servers. If you are a parent or guardian\r\nand you are aware that your child has provided us with\r\npersonal information, please contact the Service Provider (innovationhub2022@gmail.com) so that\r\nthey will be able to take the necessary actions.</p></div><!----><br><strong>Security</strong><p>The Service Provider is concerned about safeguarding the confidentiality of your information. The Service Provider provides physical, electronic, and procedural safeguards to protect information the Service Provider processes and maintains.</p><br><strong>Changes</strong><p>This Privacy Policy may be updated from time to time for any reason. The Service Provider will notify you of any changes to the Privacy Policy by updating this page with the new Privacy Policy. You are advised to consult this Privacy Policy regularly for any changes, as continued use is deemed approval of all changes.</p><br><p>This privacy policy is effective as of 2024-12-24</p><br><strong>Your Consent</strong><p>By using the Application, you are consenting to the processing of your information as set forth in this Privacy Policy now and as amended by us.</p><br><strong>Contact Us</strong><p>If you have any questions regarding privacy while using the Application, or have questions about the practices, please contact the Service Provider via email at innovationhub2022@gmail.com.</p><hr><p>This privacy policy page was generated by <a href=\"https://app-privacy-policy-generator.nisrulz.com/\" target=\"_blank\" rel=\"noopener noreferrer\">App Privacy Policy Generator</a></p>\r\n    </body>\r\n    </html>', NULL, 0, NULL, 'no', '2024-12-24 10:04:01');

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_page_contents`
--

CREATE TABLE `front_cms_page_contents` (
  `id` int(11) NOT NULL,
  `page_id` int(11) DEFAULT NULL,
  `content_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `front_cms_page_contents`
--

INSERT INTO `front_cms_page_contents` (`id`, `page_id`, `content_type`, `created_at`) VALUES
(3, 12, 'gallery', '2024-11-26 07:20:48'),
(4, 13, 'notice', '2024-11-26 07:22:02'),
(5, 14, 'events', '2024-11-26 07:22:30');

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_programs`
--

CREATE TABLE `front_cms_programs` (
  `id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `url` text DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `event_start` date DEFAULT NULL,
  `event_end` date DEFAULT NULL,
  `event_venue` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` varchar(10) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `meta_title` text NOT NULL,
  `meta_description` text NOT NULL,
  `meta_keyword` text NOT NULL,
  `feature_image` text NOT NULL,
  `publish_date` date DEFAULT NULL,
  `publish` varchar(10) DEFAULT '0',
  `sidebar` int(10) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `front_cms_programs`
--

INSERT INTO `front_cms_programs` (`id`, `type`, `slug`, `url`, `title`, `date`, `event_start`, `event_end`, `event_venue`, `description`, `is_active`, `created_at`, `meta_title`, `meta_description`, `meta_keyword`, `feature_image`, `publish_date`, `publish`, `sidebar`) VALUES
(1, 'events', 'nursery-my-shapes-rocket-extra-activities-1', 'read/nursery-my-shapes-rocket-extra-activities-1', 'NURSERY - My Shapes Rocket Extra Activities', NULL, '2024-11-18', '2024-11-22', 'School hall ', 'Nursery students had an exciting activity of creating their very own shapes rockets! This activity was a perfect blend of creativity, learning and fun. The students constructed their unique rockets using various shapes like circles, triangles, squares, and rectangles. They learnt about different shapes and colours while developing their creativity and fine motor skills.&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<br />\r\n&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<img src=\"https://demo.smart-school.in/uploads/gallery/media/1730789815-4563453586729c1b7d2c59!original-6731833-1.jpg?1730789825\" />', 'no', '2024-11-25 08:01:55', '', '', '', '', NULL, '0', NULL),
(2, 'events', 'childrens-day-celebrations', 'read/childrens-day-celebrations', 'Children’s Day Celebrations', NULL, '2024-11-12', '2024-11-14', 'School Hall Room', '“Every child is a different kind of flower that altogether make this world a beautiful garden.”<br />\r\nChildren’s Day was celebrated with vigour and enthusiasm by the children and teachers of&nbsp; MCS on 14 November. The teachers made the occasion memorable for the children with a special assembly. The children were treated to a melodious medley of songs followed by a colourful dance performance by their teachers.<br />\r\n<br />\r\n<br />\r\n&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<br />\r\n<img src=\"https://demo.smart-school.in/uploads/gallery/media/1730789616-16099025346729c0f06507f!children-.jpg?1730789619\" />', 'no', '2024-11-25 08:02:48', '', '', '', '', NULL, '0', NULL),
(3, 'events', 'mahatma-gandhi-jayanti-celebration', 'read/mahatma-gandhi-jayanti-celebration', 'Mahatma Gandhi Jayanti celebration', NULL, '2024-10-01', '2024-10-02', 'School hall Room', 'स्वच्छता ही सेवा (SHS)<br />\r\n<br />\r\nUnder this initiative, our school is running campaign of, \"Maha Shram Daan\" on 2nd October 2024 during school hours interested students can participate in it.&nbsp; For which they can also voluntarily contribute things like duster to wipe, broom, detergent powder, Phenyl etc. to the School.<br />\r\n<br />\r\nStudents can make chart also related to the above topic.<br />\r\n&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<img src=\"https://demo.smart-school.in/uploads/gallery/media/1727760453-149412512866fb884535d1d!IMG-20.jpg?1727760460\" />', 'no', '2024-11-25 08:04:26', '', '', '', '', NULL, '0', NULL),
(4, 'events', 'teachers-day-celebration-2024', 'read/teachers-day-celebration-2024', 'Teacher\'s Day Celebration-2024', NULL, '2024-09-03', '2024-09-05', 'School Hall Room', 'Mount Carmel School celebrated Teachers Day with great enthusiasm. Students gave beautiful performances for the teachers and also came dressed as their favourite teacher.<br />\r\n&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<img src=\"https://demo.smart-school.in/uploads/gallery/media/1725276147-40548166366d59ff3bc080!celebrate-teachers-day-.jpg?1725276155\" />', 'no', '2024-11-25 08:05:03', '', '', '', '', NULL, '0', NULL),
(5, 'events', 'independence-day-celebration-special-assembly-and-class-activities', 'read/independence-day-celebration-special-assembly-and-class-activities', 'Independence Day Celebration- Special Assembly and Class Activities', NULL, '2024-08-12', '2024-08-15', 'School Hall Room', 'A special assembly will be held on Independence Day by classes VI-VIII with performances and speeches to celebrate India\'s freedom and honour its freedom fighters. Additionally, art and craft activities will be conducted in the classes, where students can create patriotic-themed artworks.', 'no', '2024-11-25 08:06:04', '', '', '', '', NULL, '0', NULL),
(6, 'events', 'world-environment-day', 'read/world-environment-day', 'World Environment Day', NULL, '2024-06-01', '2024-06-06', 'School Play Ground', 'Dear Teacher\'s/Students,<br />\r\n<br />\r\nWorld Environment Day, established by the Mount Carmel&nbsp; School, is celebrated annually on June 5th. It provides an opportunity to focus global attention on pressing environmental challenges and encourages individuals and communities to take proactive steps towards environmental conservation.&nbsp;<br />\r\n<br />\r\nRegards,<br />\r\nPrincipal<br />\r\nMount Carmel&nbsp; School<br />\r\n<img src=\"https://demo.smart-school.in/uploads/gallery/media/1717589707-109619089666056cbe4ab9!iSto.jpg?1717589843\" />', 'no', '2024-11-25 08:07:01', '', '', '', '', NULL, '0', NULL),
(7, 'gallery', 'bhajan-sandhya-good', 'read/bhajan-sandhya-good', 'bhajan sandhya good', NULL, NULL, NULL, NULL, '<p><span font-size:=\"\" open=\"\">Fusce semper, nibh eu sollicitudin imperdiet, dolor magna vestibulum mi, vel tincidunt augue ipsum nec erat. Vestibulum congue leo elementum ullamcorper commodo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</span></p>\r\n\r\n<p><span font-size:=\"\" open=\"\">Fusce semper, nibh eu sollicitudin imperdiet, dolor magna vestibulum mi, vel tincidunt augue ipsum nec erat. Vestibulum congue leo elementum ullamcorper commodo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</span></p>', 'no', '2025-02-11 12:39:29', '', '', '', '{{BASEURL}}uploads/gallery/media/1732616603-671463256745a19b7ec0b!gal6.jpg?1732616633', NULL, '0', NULL),
(8, 'gallery', 'sports-events', 'read/sports-events', 'Sports Events', NULL, NULL, NULL, NULL, 'Sports Events', 'no', '2025-02-11 12:39:41', '', '', '', '{{BASEURL}}uploads/gallery/media/1732606433-1999832186674579e1c2242!!1674040621-203862736463c7d52d312c6!DCC Sports Day (3).jpeg?1732615675', NULL, '0', NULL),
(9, 'gallery', 'recreation-centre', 'read/recreation-centre', 'Recreation Centre', NULL, NULL, NULL, NULL, 'School&nbsp;Recreation Centre', 'no', '2025-02-11 12:39:56', '', '', '', '{{BASEURL}}uploads/gallery/media/1732616723-13118517526745a21391ff2!gal77.jpg?1732616738', NULL, '0', NULL),
(10, 'gallery', 'celebration', 'read/celebration', 'Celebration', NULL, NULL, NULL, NULL, 'School Celebration', 'no', '2025-02-11 12:40:03', '', '', '', '{{BASEURL}}uploads/gallery/media/1732616615-15404612746745a1a7d5f72!gal5.jpg?1732616657', NULL, '0', NULL),
(11, 'gallery', 'ncc-bands', 'read/ncc-bands', 'NCC & Bands', NULL, NULL, NULL, NULL, '<p><span xss=\"removed\">Fusce semper, nibh eu sollicitudin imperdiet, dolor magna vestibulum mi, vel tincidunt augue ipsum nec erat. Vestibulum congue leo elementum ullamcorper commodo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</span></p>\r\n\r\n<p><span xss=\"removed\">Fusce semper, nibh eu sollicitudin imperdiet, dolor magna vestibulum mi, vel tincidunt augue ipsum nec erat. Vestibulum congue leo elementum ullamcorper commodo. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</span></p>', 'no', '2025-02-11 12:40:10', '', '', '', '{{BASEURL}}uploads/gallery/media/1732616356-3797938846745a0a412928!ncc.jpg?1732616388', NULL, '0', NULL),
(12, 'notice', 'fit-india-week', 'read/fit-india-week', 'Fit India Week', '2024-11-15', NULL, NULL, NULL, '“It is only in a healthy body that a healthy mind can reside”. With this perspective in view, the entire student, teacher and parent fraternity at MCS School , celebrated Fit India Week with great zeal and enthusiasm.<br />\r\n<br />\r\n&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;The Week saw a plethora of activities, in which the entire school participated. A series of sporting events, right from basics to nurturing special talents, were organized and the all students were encouraged to participate in the same throughout the week.Various fitness events like races, marathons and skill-based matches, such as basketball, football were organised for each day. The idea was to help the students imbibe the benefits of fitness through sports and all the other advantages that come along the way.', 'no', '2024-11-25 08:16:03', '', '', '', '', NULL, '0', NULL),
(13, 'notice', 'general-parents-meeting-with-class-teacher', 'read/general-parents-meeting-with-class-teacher', 'General Parents Meeting with class teacher', '2024-10-22', NULL, NULL, NULL, 'Dear parents and guardians,&nbsp;<br />\r\n<br />\r\n&nbsp;There will be a General parents meeting with class teachers std 1 to 5 .8.00 a.m.<br />\r\nstd 6to10 : 9:30 a.m.<br />\r\nAll parents are requested to attend this meeting on time.', 'no', '2024-11-25 08:16:41', '', '', '', '', NULL, '0', NULL),
(14, 'notice', 'dussehra-holiday', 'read/dussehra-holiday', 'Dussehra Holiday', '2024-10-01', NULL, NULL, NULL, 'Dear parents and guardians,&nbsp;<br />\r\n<br />\r\nPlease note that students will have a holiday on 11.10.24 and 13.10.24.&nbsp; Dussehra is a reminder that good will always triumph. Wishing you a day of positivity and hope.<br />\r\n<br />\r\nThankyou!<br />\r\n&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<img src=\"https://demo.smart-school.in/uploads/gallery/media/1727759449-69995780966fb8459d4d7e!images.jfif?1727759458\" />', 'no', '2024-11-25 08:17:29', '', '', '', '', NULL, '0', NULL),
(15, 'notice', 'teachers-day-celebration', 'read/teachers-day-celebration', 'Teachers\' Day Celebration', '2024-09-05', NULL, NULL, NULL, 'Teachers’ Day was celebrated in school with great enthusiasm on Thursday, 5th September. The students of Class IV- XII put up an entertaining cultural morning with music, dance, videos and fun rounds of questions and answers.', 'no', '2024-11-25 08:18:05', '', '', '', '', NULL, '0', NULL),
(16, 'notice', 'the-opening-ceremony-of-computer-science-month', 'read/the-opening-ceremony-of-computer-science-month', 'The Opening Ceremony of Computer Science Month', '2024-08-20', NULL, NULL, NULL, '<br />\r\n&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Computer science is a rapidly growing field that is transforming the world around us. From smartphones to self-driven cars, from social media to space exploration, computer science is everywhere. With this thought, today on 3rd May the school witnessed the opening ceremony of Computer Science Month Techno कृति. The ceremony began with lighting the lamp followed by a song on the fame of technology.<br />\r\n<br />\r\n<br />\r\n<br />\r\nRegards,<br />\r\nPrincipal<br />\r\nMount Carmel School', 'no', '2024-11-25 08:18:28', '', '', '', '', NULL, '0', NULL),
(17, 'banner', NULL, NULL, 'Banner Images', NULL, NULL, NULL, NULL, NULL, 'no', '2024-11-25 08:33:40', '', '', '', '', NULL, '0', 0),
(18, 'notice', 'half-yearly-examination', 'read/half-yearly-examination', 'Half Yearly Examination', '2024-11-26', NULL, NULL, NULL, 'The Half Yearly Exam happens in the middle of the annual&nbsp;conduct 11th December&nbsp;school year. They are generally conducted after the first round of exams and before final exams.&nbsp;', 'no', '2024-11-26 10:46:07', '', '', '', '', NULL, '0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_program_photos`
--

CREATE TABLE `front_cms_program_photos` (
  `id` int(11) NOT NULL,
  `program_id` int(11) DEFAULT NULL,
  `media_gallery_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `front_cms_program_photos`
--

INSERT INTO `front_cms_program_photos` (`id`, `program_id`, `media_gallery_id`, `created_at`) VALUES
(1, 10, 1, '2024-11-25 08:26:47'),
(2, 17, 2, '2024-11-25 08:33:40'),
(3, 17, 3, '2024-11-25 08:33:47'),
(4, 17, 1, '2024-11-25 08:33:52'),
(5, 17, 4, '2024-11-25 08:33:58'),
(6, 17, 5, '2024-11-25 08:34:02'),
(7, 17, 32, '2024-11-26 07:43:18'),
(8, 17, 28, '2024-11-26 07:43:27'),
(9, 17, 29, '2024-11-26 07:43:37'),
(10, 17, 44, '2024-11-26 07:45:38'),
(11, 17, 43, '2024-11-26 07:45:56');

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_settings`
--

CREATE TABLE `front_cms_settings` (
  `id` int(11) NOT NULL,
  `theme` varchar(50) DEFAULT NULL,
  `is_active_rtl` int(10) DEFAULT 0,
  `is_active_front_cms` int(11) DEFAULT 0,
  `is_active_sidebar` int(1) DEFAULT 0,
  `logo` varchar(200) DEFAULT NULL,
  `contact_us_email` varchar(100) DEFAULT NULL,
  `complain_form_email` varchar(100) DEFAULT NULL,
  `sidebar_options` text NOT NULL,
  `whatsapp_url` varchar(255) NOT NULL,
  `fb_url` varchar(200) NOT NULL,
  `twitter_url` varchar(200) NOT NULL,
  `youtube_url` varchar(200) NOT NULL,
  `google_plus` varchar(200) NOT NULL,
  `instagram_url` varchar(200) NOT NULL,
  `pinterest_url` varchar(200) NOT NULL,
  `linkedin_url` varchar(200) NOT NULL,
  `google_analytics` text DEFAULT NULL,
  `footer_text` varchar(500) DEFAULT NULL,
  `cookie_consent` varchar(255) NOT NULL,
  `fav_icon` varchar(250) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `front_cms_settings`
--

INSERT INTO `front_cms_settings` (`id`, `theme`, `is_active_rtl`, `is_active_front_cms`, `is_active_sidebar`, `logo`, `contact_us_email`, `complain_form_email`, `sidebar_options`, `whatsapp_url`, `fb_url`, `twitter_url`, `youtube_url`, `google_plus`, `instagram_url`, `pinterest_url`, `linkedin_url`, `google_analytics`, `footer_text`, `cookie_consent`, `fav_icon`, `created_at`) VALUES
(1, 'material_pink', NULL, 1, NULL, '/uploads/school_content/logo/1.jpg', '', '', '[\"news\",\"complain\"]', 'https://www.whatsapp.com/a', 'https://www.facebook.com/a', 'https://twitter.com/a', 'https://www.youtube.com/a', 'https://plus.google.com/a', 'https://www.instagram.com/a', 'https://in.pinterest.com/a', 'https://www.linkedin.com/a', ' ', '© Mount Carmel School 2023 All rights reserved', '', './uploads/school_content/logo/1672900975-63781378463b6716fc2886!front_fav_icon-608ff44a5fdb33.94953981.png', '2024-12-14 13:44:51');

-- --------------------------------------------------------

--
-- Table structure for table `gateway_ins`
--

CREATE TABLE `gateway_ins` (
  `id` int(11) NOT NULL,
  `online_admission_id` int(11) DEFAULT NULL,
  `gateway_name` varchar(50) NOT NULL,
  `module_type` varchar(255) NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `parameter_details` mediumtext NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gateway_ins_response`
--

CREATE TABLE `gateway_ins_response` (
  `id` int(11) NOT NULL,
  `gateway_ins_id` int(11) DEFAULT NULL,
  `posted_data` text DEFAULT NULL,
  `response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_calls`
--

CREATE TABLE `general_calls` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(12) NOT NULL,
  `date` date NOT NULL,
  `description` varchar(500) NOT NULL,
  `follow_up_date` date NOT NULL,
  `call_duration` varchar(50) NOT NULL,
  `note` text NOT NULL,
  `call_type` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `general_calls`
--

INSERT INTO `general_calls` (`id`, `name`, `contact`, `date`, `description`, `follow_up_date`, `call_duration`, `note`, `call_type`, `created_at`) VALUES
(1, 'Exam Center Office	', '789890676', '2024-11-26', '', '2024-11-30', '45', '', 'Outgoing', '2024-11-25 10:37:03'),
(2, 'Telephone Office	', '907897867', '2024-11-25', '', '2024-11-28', '54', '', 'Outgoing', '2024-11-25 10:37:39'),
(3, 'Marketing Manager	', '8908977896', '2024-11-20', '', '2024-11-22', '45', '', 'Incoming', '2024-11-25 10:38:03'),
(4, 'admin', '98765434567', '2024-11-26', '', '2024-11-07', '', '', 'Incoming', '2024-11-26 10:56:52'),
(5, 'teacher', '345678987', '2024-11-26', '', '2024-11-17', '', '', 'Outgoing', '2024-11-26 10:57:15');

-- --------------------------------------------------------

--
-- Table structure for table `gmeet`
--

CREATE TABLE `gmeet` (
  `id` int(11) NOT NULL,
  `purpose` varchar(20) NOT NULL DEFAULT 'class',
  `staff_id` int(11) DEFAULT NULL,
  `created_id` int(10) NOT NULL,
  `title` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'manual',
  `api_data` text DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `url` text NOT NULL,
  `session_id` int(10) NOT NULL,
  `description` text DEFAULT NULL,
  `timezone` varchar(100) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `gmeet`
--

INSERT INTO `gmeet` (`id`, `purpose`, `staff_id`, `created_id`, `title`, `date`, `type`, `api_data`, `duration`, `subject`, `url`, `session_id`, `description`, `timezone`, `status`, `created_at`) VALUES
(1, 'meeting', NULL, 1, 'Salary Discussion', '2024-12-06 15:32:00', 'manual', NULL, 45, NULL, 'https://meet.google.com/vfs-ndjp-xno', 19, 'Salary Discussion	', 'Asia/Kolkata', 1, '2024-12-09 10:07:36'),
(2, 'meeting', NULL, 1, 'Syllabus Complete Before date', '2024-11-25 15:35:00', 'manual', NULL, 12, NULL, 'https://meet.google.com/vfs-ndjp-xno', 19, 'Syllabus Complete Before date	', 'Asia/Kolkata', 2, '2024-12-09 10:06:28'),
(3, 'meeting', NULL, 1, 'Book Stock Discussion', '2024-11-29 15:36:00', 'manual', NULL, 45, NULL, 'https://meet.google.com/vfs-ndjp-xno', 19, 'Book Stock Discussion	', 'Asia/Kolkata', 2, '2024-12-09 10:21:26'),
(4, 'meeting', NULL, 1, 'Student Health Serve Mission', '2024-11-28 15:38:00', 'manual', NULL, 35, NULL, 'https://meet.google.com/vfs-ndjp-xno', 19, 'Student Health Serve Mission	', 'Asia/Kolkata', 0, '2024-12-09 10:08:14'),
(5, 'meeting', NULL, 1, 'Finance Report Discussion', '2024-11-01 15:38:00', 'manual', NULL, 20, NULL, 'https://meet.google.com/vfs-ndjp-xno', 19, 'Finance Report Discussion\r\n', 'Asia/Kolkata', 2, '2024-12-09 10:21:29'),
(6, 'meeting', NULL, 1, 'Urgent Meeting', '2024-12-09 15:40:00', 'manual', NULL, 23, NULL, 'https://meet.google.com/vfs-ndjp-xno', 19, 'Urgent Meeting	', 'Asia/Kolkata', 0, '2024-12-09 10:10:28'),
(7, 'class', 2, 1, 'Science Extra Classes', '2024-11-30 15:41:00', 'manual', NULL, 60, NULL, 'https://meet.google.com/vfs-ndjp-xno', 19, 'Science Extra Classes	', 'Asia/Kolkata', 0, '2024-12-09 10:12:02'),
(8, 'class', 9, 1, 'Social Studies Classes', '2024-12-27 15:43:00', 'manual', NULL, 60, NULL, 'https://meet.google.com/vfs-ndjp-xno', 19, 'Social Studies Classes	', 'Asia/Kolkata', 1, '2024-12-09 10:14:23'),
(9, 'class', 8, 1, 'Fees Collected Discussion', '2024-11-27 15:45:00', 'manual', NULL, 45, NULL, 'https://meet.google.com/vfs-ndjp-xno', 19, 'Fees Collected Discussion', 'Asia/Kolkata', 0, '2024-12-09 10:15:13'),
(10, 'class', 4, 1, 'Online Course Class', '2024-12-14 15:46:00', 'manual', NULL, 45, NULL, 'https://meet.google.com/vfs-ndjp-xno', 19, 'Online Course Class	', 'Asia/Kolkata', 0, '2024-12-09 10:16:38'),
(11, 'class', 7, 1, 'Mathematics Classes', '2024-11-29 15:47:00', 'manual', NULL, 60, NULL, 'https://meet.google.com/vfs-ndjp-xno', 19, 'Mathematics Classes	', 'Asia/Kolkata', 0, '2024-12-09 10:17:59'),
(12, 'meeting', NULL, 1, 'Live Meeting For Staff Council', '2024-11-23 15:49:00', 'manual', NULL, 30, NULL, 'https://meet.google.com/vfs-ndjp-xno', 19, 'Live Meeting For Staff Council	', 'Asia/Kolkata', 0, '2024-12-09 10:20:11'),
(13, 'class', 6, 1, 'Exam Class', '2024-12-21 13:46:00', 'manual', NULL, 30, NULL, 'https://meet.google.com/qyd-kuze-ehn', 19, 'Exam Class', 'Asia/Kolkata', 0, '2024-12-21 08:17:08');

-- --------------------------------------------------------

--
-- Table structure for table `gmeet_history`
--

CREATE TABLE `gmeet_history` (
  `id` int(11) NOT NULL,
  `gmeet_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `total_hit` int(10) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `gmeet_history`
--

INSERT INTO `gmeet_history` (`id`, `gmeet_id`, `staff_id`, `student_id`, `total_hit`, `created_at`) VALUES
(1, 13, NULL, 1, 2, '2024-12-23 11:35:11');

-- --------------------------------------------------------

--
-- Table structure for table `gmeet_sections`
--

CREATE TABLE `gmeet_sections` (
  `id` int(11) NOT NULL,
  `gmeet_id` int(11) NOT NULL,
  `cls_section_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `gmeet_sections`
--

INSERT INTO `gmeet_sections` (`id`, `gmeet_id`, `cls_section_id`, `created_at`) VALUES
(1, 7, 6, '2024-12-09 10:12:02'),
(2, 7, 7, '2024-12-09 10:12:02'),
(3, 7, 8, '2024-12-09 10:12:02'),
(4, 8, 11, '2024-12-09 10:13:33'),
(5, 8, 12, '2024-12-09 10:13:33'),
(6, 8, 14, '2024-12-09 10:13:33'),
(7, 9, 16, '2024-12-09 10:15:13'),
(8, 9, 17, '2024-12-09 10:15:13'),
(9, 10, 17, '2024-12-09 10:16:38'),
(10, 10, 19, '2024-12-09 10:16:38'),
(11, 11, 8, '2024-12-09 10:17:59'),
(12, 13, 1, '2024-12-21 08:17:08'),
(13, 13, 2, '2024-12-21 08:17:08'),
(14, 13, 3, '2024-12-21 08:17:08'),
(15, 13, 4, '2024-12-21 08:17:08');

-- --------------------------------------------------------

--
-- Table structure for table `gmeet_settings`
--

CREATE TABLE `gmeet_settings` (
  `id` int(11) NOT NULL,
  `api_key` varchar(200) DEFAULT NULL,
  `api_secret` varchar(200) DEFAULT NULL,
  `use_api` int(1) DEFAULT 1,
  `parent_live_class` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gmeet_staff`
--

CREATE TABLE `gmeet_staff` (
  `id` int(11) NOT NULL,
  `gmeet_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `gmeet_staff`
--

INSERT INTO `gmeet_staff` (`id`, `gmeet_id`, `staff_id`, `created_at`) VALUES
(1, 1, 2, '2024-12-09 10:03:29'),
(2, 1, 3, '2024-12-09 10:03:29'),
(3, 1, 4, '2024-12-09 10:03:29'),
(4, 1, 5, '2024-12-09 10:03:29'),
(5, 1, 7, '2024-12-09 10:03:29'),
(6, 2, 2, '2024-12-09 10:06:05'),
(7, 2, 3, '2024-12-09 10:06:05'),
(8, 2, 5, '2024-12-09 10:06:05'),
(9, 2, 7, '2024-12-09 10:06:05'),
(10, 3, 8, '2024-12-09 10:07:24'),
(11, 4, 2, '2024-12-09 10:08:14'),
(12, 4, 3, '2024-12-09 10:08:14'),
(13, 4, 7, '2024-12-09 10:08:14'),
(14, 5, 4, '2024-12-09 10:09:05'),
(15, 5, 5, '2024-12-09 10:09:05'),
(16, 5, 6, '2024-12-09 10:09:05'),
(17, 6, 3, '2024-12-09 10:10:28'),
(18, 6, 4, '2024-12-09 10:10:28'),
(19, 12, 5, '2024-12-09 10:20:11'),
(20, 12, 6, '2024-12-09 10:20:11');

-- --------------------------------------------------------

--
-- Table structure for table `google_authenticator`
--

CREATE TABLE `google_authenticator` (
  `id` int(11) NOT NULL,
  `use_authenticator` int(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `google_authenticator`
--

INSERT INTO `google_authenticator` (`id`, `use_authenticator`, `created_at`) VALUES
(1, 1, '2024-11-25 07:47:56');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `exam_type` varchar(250) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `point` float(10,1) DEFAULT NULL,
  `mark_from` float(10,2) DEFAULT NULL,
  `mark_upto` float(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `exam_type`, `name`, `point`, `mark_from`, `mark_upto`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'school_grade_system', 'B+', 0.0, 0.00, 33.00, '', 'no', '2024-11-25 10:57:36', NULL),
(2, 'school_grade_system', 'B', 0.0, 33.00, 50.00, '\r\n', 'no', '2024-11-25 10:58:09', NULL),
(3, 'school_grade_system', 'A+', 0.0, 51.00, 75.00, '', 'no', '2024-11-25 10:59:00', NULL),
(4, 'school_grade_system', 'A', 0.0, 76.00, 100.00, '', 'no', '2024-11-25 10:59:19', NULL),
(5, 'basic_system', 'B+', 0.0, 0.00, 33.00, '', 'no', '2024-11-25 10:59:52', NULL),
(6, 'basic_system', 'B', 0.0, 33.00, 50.00, '', 'no', '2024-11-25 11:00:11', NULL),
(7, 'basic_system', 'A', 0.0, 51.00, 75.00, '', 'no', '2024-11-25 11:00:38', NULL),
(8, 'basic_system', 'A+', 0.0, 76.00, 100.00, '', 'no', '2024-11-25 11:00:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `guest`
--

CREATE TABLE `guest` (
  `id` int(11) NOT NULL,
  `guest_name` varchar(200) NOT NULL,
  `guest_unique_id` varchar(200) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `currency_id` int(11) NOT NULL,
  `email` varchar(200) NOT NULL,
  `mobileno` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `dob` varchar(200) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `note` text NOT NULL,
  `address` varchar(200) NOT NULL,
  `guest_image` varchar(200) NOT NULL,
  `verification_code` varchar(200) NOT NULL,
  `created_at` date NOT NULL,
  `is_active` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `guest`
--

INSERT INTO `guest` (`id`, `guest_name`, `guest_unique_id`, `lang_id`, `currency_id`, `email`, `mobileno`, `password`, `dob`, `gender`, `note`, `address`, `guest_image`, `verification_code`, `created_at`, `is_active`) VALUES
(1, 'yug', 'Guest100', 4, 0, 'yugs@gmail.com', '', '$2y$10$PwAvbIMCfBgHOVKoKUyut.Sc7m8yWpcafWodfH716nLj9I8RizkaC', '', NULL, '', '', '', '', '2024-12-09', 'yes'),
(2, 'Munesh', 'Guest101', 4, 150, 'muneshpatel93@gmail.com', '', '$2y$10$jgi9sEcA5GqQ.EoTbO866.MCM4GEG6h45VHqiKzdmLqwZZcjR/J6C', '', NULL, '', '', '', '', '2024-12-10', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `homework`
--

CREATE TABLE `homework` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `session_id` int(10) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `subject_group_subject_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `homework_date` date NOT NULL,
  `submit_date` date NOT NULL,
  `marks` float(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `create_date` date NOT NULL,
  `evaluation_date` date DEFAULT NULL,
  `document` varchar(200) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `evaluated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homework_evaluation`
--

CREATE TABLE `homework_evaluation` (
  `id` int(11) NOT NULL,
  `homework_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `marks` float(10,2) DEFAULT NULL,
  `note` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hostel`
--

CREATE TABLE `hostel` (
  `id` int(11) NOT NULL,
  `hostel_name` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `intake` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `hostel`
--

INSERT INTO `hostel` (`id`, `hostel_name`, `type`, `address`, `intake`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Boys Hostel 101', 'Boys', 'School Campus', 200, '', 'no', '2024-11-25 08:43:28', NULL),
(2, 'Boys Hostel 102', 'Boys', 'School Campus', 200, '', 'no', '2024-11-25 08:43:58', NULL),
(3, 'Girls Hostel 103', 'Girls', 'School Campus ', 150, '', 'no', '2024-11-25 08:44:21', NULL),
(4, 'Girls Hostel 104', 'Girls', 'School Campus', 150, '', 'no', '2024-11-25 08:44:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hostel_rooms`
--

CREATE TABLE `hostel_rooms` (
  `id` int(11) NOT NULL,
  `hostel_id` int(11) DEFAULT NULL,
  `room_type_id` int(11) DEFAULT NULL,
  `room_no` varchar(200) DEFAULT NULL,
  `no_of_bed` int(11) DEFAULT NULL,
  `cost_per_bed` float(10,2) DEFAULT 0.00,
  `title` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `hostel_rooms`
--

INSERT INTO `hostel_rooms` (`id`, `hostel_id`, `room_type_id`, `room_no`, `no_of_bed`, `cost_per_bed`, `title`, `description`, `created_at`) VALUES
(1, 1, 1, 'B1', 1, 300.00, NULL, '', '2024-11-25 08:46:46'),
(2, 1, 1, 'B3', 1, 500.00, NULL, '', '2024-11-25 08:47:20'),
(3, 2, 2, 'B2', 2, 1000.00, NULL, '', '2024-11-25 08:47:59'),
(4, 3, 2, 'G3', 2, 500.00, NULL, '', '2024-11-25 08:49:01'),
(5, 4, 3, 'G4', 2, 300.00, NULL, '', '2024-11-25 08:49:33');

-- --------------------------------------------------------

--
-- Table structure for table `id_card`
--

CREATE TABLE `id_card` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `school_name` varchar(100) NOT NULL,
  `school_address` varchar(500) NOT NULL,
  `background` varchar(100) NOT NULL,
  `logo` varchar(100) NOT NULL,
  `sign_image` varchar(100) NOT NULL,
  `enable_vertical_card` int(11) NOT NULL DEFAULT 0,
  `header_color` varchar(100) NOT NULL,
  `enable_admission_no` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_student_name` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_class` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_fathers_name` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_mothers_name` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_address` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_phone` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_dob` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_blood_group` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_student_barcode` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0=disable,1=enable',
  `status` tinyint(1) NOT NULL COMMENT '0=disable,1=enable'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `id_card`
--

INSERT INTO `id_card` (`id`, `title`, `school_name`, `school_address`, `background`, `logo`, `sign_image`, `enable_vertical_card`, `header_color`, `enable_admission_no`, `enable_student_name`, `enable_class`, `enable_fathers_name`, `enable_mothers_name`, `enable_address`, `enable_phone`, `enable_dob`, `enable_blood_group`, `enable_student_barcode`, `status`) VALUES
(1, 'Sample Student Identity Card Horizontal', 'Mount Carmel School', '110 Kings Street, CA  Phone: 456542 Email: mount@gmail.com', 'samplebackground12.png', 'samplelogo12.png', 'samplesign12.png', 0, '#595959', 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1),
(2, 'Sample Student Identity Card Vertical', 'Mount Carmel School', '110 Kings Street, CA  Phone: 456542 Email: mount@gmail.com', 'samplebackground12.png', 'samplelogo12.png', 'samplesign12.png', 1, '#595959', 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1),
(3, 'New Sample Student Identity Card', 'Mount Carmel School', '110 Kings Street, CA  Phone: 456542 Email: mount@gmail.com', '', '', '', 0, '#000000', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `id` int(11) NOT NULL,
  `income_head_id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `invoice_no` varchar(200) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `amount` float(10,2) DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'yes',
  `documents` varchar(255) DEFAULT NULL,
  `is_deleted` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `income`
--

INSERT INTO `income` (`id`, `income_head_id`, `name`, `invoice_no`, `date`, `amount`, `note`, `is_active`, `documents`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 1, 'Donation fee is', '678', '2024-11-20', 113.00, 'Donation fee is fee which you have to give to make you eligible for student,	', 'yes', NULL, 'no', '2024-11-25 10:51:31', NULL),
(2, 3, 'NCC camp', '56765', '2024-11-13', 455.00, 'The National School Lunch Program (NSLP) is typically the Nation\'s second largest food and nutrition.	', 'yes', NULL, 'no', '2024-11-25 10:53:01', NULL),
(3, 4, 'New Books collection', '546', '2024-11-29', 566.00, 'NCRT Books are essential materials for students of all classes.	', 'yes', NULL, 'no', '2024-11-25 10:53:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `income_head`
--

CREATE TABLE `income_head` (
  `id` int(255) NOT NULL,
  `income_category` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` varchar(255) NOT NULL DEFAULT 'yes',
  `is_deleted` varchar(255) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `income_head`
--

INSERT INTO `income_head` (`id`, `income_category`, `description`, `is_active`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 'Donation', '', 'yes', 'no', '2024-11-25 10:44:02', NULL),
(2, 'Rent', '', 'yes', 'no', '2024-11-25 10:44:05', NULL),
(3, 'Miscellaneous', '', 'yes', 'no', '2024-11-25 10:44:10', NULL),
(4, 'Book Sale', '', 'yes', 'no', '2024-11-25 10:44:15', NULL),
(5, 'Uniform Sale', '', 'yes', 'no', '2024-11-25 10:44:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `id` int(11) NOT NULL,
  `item_category_id` int(11) DEFAULT NULL,
  `item_store_id` int(11) DEFAULT NULL,
  `item_supplier_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `item_photo` varchar(225) DEFAULT NULL,
  `description` text NOT NULL,
  `quantity` int(100) NOT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`id`, `item_category_id`, `item_store_id`, `item_supplier_id`, `name`, `unit`, `item_photo`, `description`, `quantity`, `date`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, 'Cricket Bat', '119', NULL, '', 0, NULL, '2024-11-25 09:47:23', NULL),
(2, 3, NULL, NULL, 'Table chair', '56', NULL, '', 0, NULL, '2024-11-25 09:47:36', NULL),
(3, 4, NULL, NULL, 'Lab Equipment', '45', NULL, '', 0, NULL, '2024-11-25 09:47:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `item_category`
--

CREATE TABLE `item_category` (
  `id` int(255) NOT NULL,
  `item_category` varchar(255) NOT NULL,
  `is_active` varchar(255) NOT NULL DEFAULT 'yes',
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `item_category`
--

INSERT INTO `item_category` (`id`, `item_category`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Sports', 'yes', '', '2024-11-25 09:46:41', NULL),
(2, 'Staff Dress', 'yes', '', '2024-11-25 09:46:48', NULL),
(3, 'Furniture', 'yes', '', '2024-11-25 09:46:51', NULL),
(4, 'Chemistry Lab Apparatus', 'yes', '', '2024-11-25 09:46:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `item_issue`
--

CREATE TABLE `item_issue` (
  `id` int(11) NOT NULL,
  `issue_type` varchar(15) DEFAULT NULL,
  `issue_to` int(11) NOT NULL,
  `issue_by` int(11) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `item_category_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(10) NOT NULL,
  `note` text NOT NULL,
  `is_returned` int(2) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` varchar(10) DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `item_issue`
--

INSERT INTO `item_issue` (`id`, `issue_type`, `issue_to`, `issue_by`, `issue_date`, `return_date`, `item_category_id`, `item_id`, `quantity`, `note`, `is_returned`, `created_at`, `is_active`) VALUES
(1, '7', 1, 1, '2024-11-02', '2024-11-07', 3, 2, 4, '', 1, '2024-11-25 09:49:50', 'no'),
(2, '7', 1, 1, '2024-11-01', '2024-11-25', 4, 3, 0, '', 0, '2024-11-25 09:50:22', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `item_stock`
--

CREATE TABLE `item_stock` (
  `id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `store_id` int(11) DEFAULT NULL,
  `symbol` varchar(10) NOT NULL DEFAULT '+',
  `quantity` int(11) DEFAULT NULL,
  `purchase_price` float(10,2) NOT NULL,
  `date` date NOT NULL,
  `attachment` varchar(250) DEFAULT NULL,
  `description` text NOT NULL,
  `is_active` varchar(10) DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `item_stock`
--

INSERT INTO `item_stock` (`id`, `item_id`, `supplier_id`, `store_id`, `symbol`, `quantity`, `purchase_price`, `date`, `attachment`, `description`, `is_active`, `created_at`) VALUES
(1, 1, 1, 1, '+', 67, 67.00, '2024-11-20', NULL, '', 'yes', '2024-11-25 09:48:33'),
(2, 2, 1, 1, '+', 6, 9.00, '2024-11-09', NULL, '', 'yes', '2024-11-25 09:48:56');

-- --------------------------------------------------------

--
-- Table structure for table `item_store`
--

CREATE TABLE `item_store` (
  `id` int(255) NOT NULL,
  `item_store` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `item_store`
--

INSERT INTO `item_store` (`id`, `item_store`, `code`, `description`, `created_at`) VALUES
(1, 'Libraray Store', 'LB2	', '', '2024-11-25 09:45:41'),
(2, 'Science Store', 'SC2', 'The basic idea about the proper and necessary chemistry lab apparatus should be cleared among the students.	', '2024-11-25 09:45:57'),
(3, 'Uniform Dress Store', 'UND23	', '', '2024-11-25 09:46:07'),
(4, 'Sports', '', '', '2024-11-25 09:46:32');

-- --------------------------------------------------------

--
-- Table structure for table `item_supplier`
--

CREATE TABLE `item_supplier` (
  `id` int(255) NOT NULL,
  `item_supplier` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_person_name` varchar(255) NOT NULL,
  `contact_person_phone` varchar(255) NOT NULL,
  `contact_person_email` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `item_supplier`
--

INSERT INTO `item_supplier` (`id`, `item_supplier`, `phone`, `email`, `address`, `contact_person_name`, `contact_person_phone`, `contact_person_email`, `description`) VALUES
(1, 'Camlin Stationers', '8458436583', 'camlin@gmail.com', '22 Cristal Way. CA', 'Bruce Stark', '847487932', 'bruce@gmail.com', ''),
(2, 'Jhonson Uniform Dress', '8796787856', 'Jhon@gmail.com', '22 Cristal Way. CA', 'David', '87686785678', 'david@gmail.com', '');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `short_code` varchar(255) NOT NULL,
  `country_code` varchar(255) NOT NULL,
  `is_rtl` int(1) NOT NULL,
  `is_deleted` varchar(10) NOT NULL DEFAULT 'yes',
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `language`, `short_code`, `country_code`, `is_rtl`, `is_deleted`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Azerbaijan', 'az', 'az', 0, 'no', 'no', '2019-11-20 11:23:12', '0000-00-00'),
(2, 'Albanian', 'sq', 'al', 0, 'no', 'no', '2019-11-20 11:42:42', '0000-00-00'),
(3, 'Amharic', 'am', 'am', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(4, 'English', 'en', 'us', 0, 'no', 'no', '2019-11-20 11:38:50', '0000-00-00'),
(5, 'Arabic', 'ar', 'sa', 0, 'no', 'no', '2019-11-20 11:47:28', '0000-00-00'),
(7, 'Afrikaans', 'af', 'af', 1, 'no', 'no', '2024-11-25 07:30:37', '0000-00-00'),
(8, 'Basque', 'eu', 'es', 0, 'no', 'no', '2019-11-20 11:54:10', '0000-00-00'),
(11, 'Bengali', 'bn', 'in', 0, 'no', 'no', '2019-11-20 11:41:53', '0000-00-00'),
(13, 'Bosnian', 'bs', 'bs', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(14, 'Welsh', 'cy', 'cy', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(15, 'Hungarian', 'hu', 'hu', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(16, 'Vietnamese', 'vi', 'vi', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(17, 'Haitian', 'ht', 'ht', 0, 'no', 'no', '2021-01-23 07:09:32', '0000-00-00'),
(18, 'Galician', 'gl', 'gl', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(19, 'Dutch', 'nl', 'nl', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(21, 'Greek', 'el', 'gr', 0, 'no', 'no', '2019-11-20 12:12:08', '0000-00-00'),
(22, 'Georgian', 'ka', 'ge', 0, 'no', 'no', '2019-11-20 12:11:40', '0000-00-00'),
(23, 'Gujarati', 'gu', 'in', 0, 'no', 'no', '2019-11-20 11:39:16', '0000-00-00'),
(24, 'Danish', 'da', 'dk', 0, 'no', 'no', '2019-11-20 12:03:25', '0000-00-00'),
(25, 'Hebrew', 'he', 'il', 0, 'no', 'no', '2019-11-20 12:13:50', '0000-00-00'),
(26, 'Yiddish', 'yi', 'il', 0, 'no', 'no', '2019-11-20 12:25:33', '0000-00-00'),
(27, 'Indonesian', 'id', 'id', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(28, 'Irish', 'ga', 'ga', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(29, 'Italian', 'it', 'it', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(30, 'Icelandic', 'is', 'is', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(31, 'Spanish', 'es', 'es', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(33, 'Kannada', 'kn', 'kn', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(34, 'Catalan', 'ca', 'ca', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(36, 'Chinese', 'zh', 'cn', 0, 'no', 'no', '2019-11-20 12:01:48', '0000-00-00'),
(37, 'Korean', 'ko', 'kr', 0, 'no', 'no', '2019-11-20 12:19:09', '0000-00-00'),
(38, 'Xhosa', 'xh', 'ls', 0, 'no', 'no', '2019-11-20 12:24:39', '0000-00-00'),
(39, 'Latin', 'la', 'it', 0, 'no', 'no', '2021-01-23 07:09:32', '0000-00-00'),
(40, 'Latvian', 'lv', 'lv', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(41, 'Lithuanian', 'lt', 'lt', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(43, 'Malagasy', 'mg', 'mg', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(44, 'Malay', 'ms', 'ms', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(45, 'Malayalam', 'ml', 'ml', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(46, 'Maltese', 'mt', 'mt', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(47, 'Macedonian', 'mk', 'mk', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(48, 'Maori', 'mi', 'nz', 0, 'no', 'no', '2019-11-20 12:20:27', '0000-00-00'),
(49, 'Marathi', 'mr', 'in', 0, 'no', 'no', '2019-11-20 11:39:51', '0000-00-00'),
(51, 'Mongolian', 'mn', 'mn', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(52, 'German', 'de', 'de', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(53, 'Nepali', 'ne', 'ne', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(54, 'Norwegian', 'no', 'no', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(55, 'Punjabi', 'pa', 'in', 0, 'no', 'no', '2019-11-20 11:40:16', '0000-00-00'),
(57, 'Persian', 'fa', 'ir', 0, 'no', 'no', '2019-11-20 12:21:17', '0000-00-00'),
(59, 'Portuguese', 'pt', 'pt', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(60, 'Romanian', 'ro', 'ro', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(61, 'Russian', 'ru', 'ru', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(62, 'Cebuano', 'ceb', 'ph', 0, 'no', 'no', '2019-11-20 11:59:12', '0000-00-00'),
(64, 'Sinhala', 'si', 'lk ', 0, 'no', 'no', '2021-01-23 07:09:32', '0000-00-00'),
(65, 'Slovakian', 'sk', 'sk', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(66, 'Slovenian', 'sl', 'sl', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(67, 'Swahili', 'sw', 'ke', 0, 'no', 'no', '2019-11-20 12:21:57', '0000-00-00'),
(68, 'Sundanese', 'su', 'sd', 0, 'no', 'no', '2019-12-03 11:06:57', '0000-00-00'),
(70, 'Thai', 'th', 'th', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(71, 'Tagalog', 'tl', 'tl', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(72, 'Tamil', 'ta', 'in', 0, 'no', 'no', '2019-11-20 11:40:53', '0000-00-00'),
(74, 'Telugu', 'te', 'in', 0, 'no', 'no', '2019-11-20 11:41:15', '0000-00-00'),
(75, 'Turkish', 'tr', 'tr', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(77, 'Uzbek', 'uz', 'uz', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(79, 'Urdu', 'ur', 'pk', 0, 'no', 'no', '2019-11-20 12:23:57', '0000-00-00'),
(80, 'Finnish', 'fi', 'fi', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(81, 'French', 'fr', 'fr', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(82, 'Hindi', 'hi', 'in', 0, 'no', 'no', '2019-11-20 11:36:34', '0000-00-00'),
(84, 'Czech', 'cs', 'cz', 0, 'no', 'no', '2019-11-20 12:02:36', '0000-00-00'),
(85, 'Swedish', 'sv', 'sv', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(86, 'Scottish', 'gd', 'gd', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(87, 'Estonian', 'et', 'et', 0, 'no', 'no', '2019-11-20 11:24:23', '0000-00-00'),
(88, 'Esperanto', 'eo', 'br', 0, 'no', 'no', '2019-11-21 04:49:18', '0000-00-00'),
(89, 'Javanese', 'jv', 'id', 0, 'no', 'no', '2019-11-20 12:18:29', '0000-00-00'),
(90, 'Japanese', 'ja', 'jp', 0, 'no', 'no', '2019-11-20 12:14:39', '0000-00-00'),
(91, 'Polish', 'pl', 'pl', 0, 'no', 'no', '2020-06-15 03:25:27', '0000-00-00'),
(92, 'Kurdish', 'ku', 'iq', 0, 'no', 'no', '2020-12-21 00:15:31', '0000-00-00'),
(93, 'Lao', 'lo', 'la', 0, 'no', 'no', '2020-12-21 00:15:36', '0000-00-00'),
(94, 'Croatia', 'hr', 'hr', 0, 'no', 'no', '2022-06-07 11:48:21', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` int(11) NOT NULL,
  `type` varchar(200) NOT NULL,
  `is_active` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `type`, `is_active`) VALUES
(1, 'Medical Leave', 'yes'),
(2, 'Casual Leave	', 'yes'),
(3, 'Maternity Leave	', 'yes'),
(4, 'Sick Leave	', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `lesson`
--

CREATE TABLE `lesson` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `subject_group_subject_id` int(11) NOT NULL,
  `subject_group_class_sections_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `lesson`
--

INSERT INTO `lesson` (`id`, `session_id`, `subject_group_subject_id`, `subject_group_class_sections_id`, `name`, `created_at`) VALUES
(1, 19, 2, 1, 'A Happy Child', '2024-11-25 11:07:21'),
(2, 19, 2, 1, 'A Little Turtle ', '2024-11-25 11:07:21'),
(3, 19, 2, 1, 'Flying Man ', '2024-11-25 11:07:21'),
(4, 19, 2, 1, 'The Bubble, the Straw, and the Shoe.', '2024-11-25 11:07:21'),
(5, 19, 2, 1, 'A Kite (Poem)', '2024-11-25 11:07:21'),
(6, 19, 5, 6, 'First Day at School', '2024-11-25 11:08:59'),
(7, 19, 5, 6, 'The Wind and the Sun', '2024-11-25 11:08:59'),
(8, 19, 5, 6, 'Storm in the Garden', '2024-11-25 11:08:59'),
(9, 19, 5, 6, 'The Grasshopper and the Ant', '2024-11-25 11:08:59'),
(10, 19, 5, 6, 'Rain', '2024-11-25 11:08:59'),
(11, 19, 5, 6, 'Rain', '2024-11-25 11:08:59'),
(12, 19, 10, 11, 'Nina and the Baby Sparrows', '2024-11-25 11:09:59'),
(13, 19, 10, 11, 'Sea Song', '2024-11-25 11:09:59'),
(14, 19, 10, 11, 'The Balloon Man', '2024-11-25 11:09:59'),
(15, 19, 10, 11, 'Trains', '2024-11-25 11:09:59'),
(16, 19, 10, 11, 'What’s in the Mailbox?', '2024-11-25 11:09:59'),
(20, 19, 1, 1, 'A Happy Child A Little Turtle Flying Man The Bubble, the Straw, and the Shoe.', '2024-12-10 10:31:38'),
(21, 19, 3, 1, 'A Happy Child A Little Turtle Flying Man The Bubble, the Straw, and the Shoe.', '2024-12-10 10:31:56');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_plan_forum`
--

CREATE TABLE `lesson_plan_forum` (
  `id` int(11) NOT NULL,
  `subject_syllabus_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL COMMENT 'staff,student',
  `staff_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `libarary_members`
--

CREATE TABLE `libarary_members` (
  `id` int(11) NOT NULL,
  `library_card_no` varchar(50) DEFAULT NULL,
  `member_type` varchar(50) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `record_id` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `platform` varchar(50) DEFAULT NULL,
  `agent` varchar(50) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `message`, `record_id`, `user_id`, `action`, `ip_address`, `platform`, `agent`, `time`, `created_at`) VALUES
(1, 'Record updated On QR Attendance settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-23 18:48:07', NULL),
(2, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 14:04:32', NULL),
(3, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:35:36', NULL),
(4, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:36:42', NULL),
(5, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:37:11', NULL),
(6, 'Record updated On notification setting id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(7, 'Record updated On notification setting id 2', '2', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(8, 'Record updated On notification setting id 3', '3', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(9, 'Record updated On notification setting id 4', '4', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(10, 'Record updated On notification setting id 6', '6', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(11, 'Record updated On notification setting id 7', '7', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(12, 'Record updated On notification setting id 8', '8', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(13, 'Record updated On notification setting id 9', '9', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(14, 'Record updated On notification setting id 10', '10', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(15, 'Record updated On notification setting id 11', '11', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(16, 'Record updated On notification setting id 12', '12', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(17, 'Record updated On notification setting id 13', '13', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(18, 'Record updated On notification setting id 14', '14', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(19, 'Record updated On notification setting id 15', '15', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(20, 'Record updated On notification setting id 16', '16', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(21, 'Record updated On notification setting id 17', '17', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(22, 'Record updated On notification setting id 18', '18', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(23, 'Record updated On notification setting id 19', '19', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(24, 'Record updated On notification setting id 20', '20', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(25, 'Record updated On notification setting id 21', '21', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(26, 'Record updated On notification setting id 22', '22', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(27, 'Record updated On notification setting id 23', '23', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(28, 'Record updated On notification setting id 24', '24', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(29, 'Record updated On notification setting id 25', '25', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(30, 'Record updated On notification setting id 26', '26', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(31, 'Record updated On notification setting id 27', '27', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(32, 'Record updated On notification setting id 28', '28', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(33, 'Record updated On notification setting id 29', '29', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(34, 'Record updated On notification setting id 30', '30', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(35, 'Record updated On notification setting id 31', '31', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(36, 'Record updated On notification setting id 32', '32', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(37, 'Record updated On notification setting id 33', '33', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:49:31', NULL),
(38, 'Record updated On Front CMS Setting id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:55:00', NULL),
(39, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 19:55:00', NULL),
(40, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:00:15', NULL),
(41, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:00:21', NULL),
(42, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:00:48', NULL),
(43, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:01:00', NULL),
(44, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:01:13', NULL),
(45, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:01:22', NULL),
(46, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:01:29', NULL),
(47, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:01:39', NULL),
(48, 'New Record inserted On custom fields id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:08:48', NULL),
(49, 'New Record inserted On custom fields id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:09:29', NULL),
(50, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:12:19', NULL),
(51, 'Record updated On  online_admission_fields id 34', '34', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:12:39', NULL),
(52, 'Record updated On  online_admission_fields id 32', '32', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:12:42', NULL),
(53, 'Record updated On  online_admission_fields id 31', '31', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:12:44', NULL),
(54, 'Record updated On  online_admission_fields id 40', '40', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:12:50', NULL),
(55, 'Record updated On  online_admission_fields id 39', '39', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:12:51', NULL),
(56, 'Record updated On  online_admission_fields id 17', '17', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:13:08', NULL),
(57, 'New Record inserted On Alumni Event id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:25:57', NULL),
(58, 'New Record inserted On Alumni Event id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:27:05', NULL),
(59, 'New Record inserted On Alumni Event id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:27:39', NULL),
(60, 'New Record inserted On Alumni Event id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:28:18', NULL),
(61, 'New Record inserted On Alumni Event id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:29:07', NULL),
(62, 'New Record inserted On event id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:31:39', NULL),
(63, 'Record updated On  event id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:31:55', NULL),
(64, 'New Record inserted On event id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:32:48', NULL),
(65, 'New Record inserted On event id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:34:26', NULL),
(66, 'New Record inserted On event id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:35:03', NULL),
(67, 'New Record inserted On event id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:36:04', NULL),
(68, 'New Record inserted On event id 6', '6', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:37:01', NULL),
(69, 'New Record inserted On event id 12', '12', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:46:03', NULL),
(70, 'New Record inserted On event id 13', '13', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:46:41', NULL),
(71, 'New Record inserted On event id 14', '14', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:47:29', NULL),
(72, 'New Record inserted On event id 15', '15', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:48:05', NULL),
(73, 'New Record inserted On event id 16', '16', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:48:28', NULL),
(74, 'New Record inserted On Page List id 6', '6', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:57:50', NULL),
(75, 'New Record inserted On Page List id 7', '7', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:58:25', NULL),
(76, 'New Record inserted On Page List id 8', '8', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:58:57', NULL),
(77, 'New Record inserted On Page List id 9', '9', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 20:59:21', NULL),
(78, 'New Record inserted On Menu id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:00:33', NULL),
(79, 'New Record inserted On Menu id 6', '6', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:00:56', NULL),
(80, 'New Record inserted On Menu id 7', '7', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:02:06', NULL),
(81, 'New Record inserted On event id 17', '17', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:03:40', NULL),
(82, 'New Record inserted On id card id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:09:18', NULL),
(83, 'New Record inserted On id card id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:10:44', NULL),
(84, 'New Record inserted On id card id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:10:45', NULL),
(85, 'Record deleted On id card id 4', '4', 1, 'Delete', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:10:54', NULL),
(86, 'Record updated On  id card id 3', '3', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:11:30', NULL),
(87, 'New Record inserted On hostel id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:13:28', NULL),
(88, 'New Record inserted On hostel id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:13:58', NULL),
(89, 'New Record inserted On hostel id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:14:21', NULL),
(90, 'New Record inserted On hostel id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:14:40', NULL),
(91, 'New Record inserted On room types id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:15:02', NULL),
(92, 'New Record inserted On room types id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:15:08', NULL),
(93, 'Record updated On  room types id 2', '2', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:15:20', NULL),
(94, 'New Record inserted On room types id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:15:28', NULL),
(95, 'New Record inserted On room types id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:15:36', NULL),
(96, 'New Record inserted On hostel rooms id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:16:46', NULL),
(97, 'New Record inserted On hostel rooms id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:17:20', NULL),
(98, 'New Record inserted On hostel rooms id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:17:59', NULL),
(99, 'New Record inserted On hostel rooms id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:19:01', NULL),
(100, 'New Record inserted On hostel rooms id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:19:33', NULL),
(101, 'New Record inserted On transport route id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:25:19', NULL),
(102, 'New Record inserted On transport route id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:25:44', NULL),
(103, 'New Record inserted On transport route id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:26:15', NULL),
(104, 'New Record inserted On transport route id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:26:33', NULL),
(105, 'New Record inserted On transport route id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:27:04', NULL),
(106, 'New Record inserted On transport route id 6', '6', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:27:31', NULL),
(107, 'New Record inserted On transport route id 7', '7', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:27:53', NULL),
(108, 'New Record inserted On transport route id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:28:17', NULL),
(109, 'New Record inserted On transport route id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:28:22', NULL),
(110, 'New Record inserted On transport route id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:28:27', NULL),
(111, 'New Record inserted On transport route id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:28:31', NULL),
(112, 'New Record inserted On transport route id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:28:36', NULL),
(113, 'New Record inserted On transport route id 6', '6', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:28:40', NULL),
(114, 'New Record inserted On transport route id 7', '7', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:28:46', NULL),
(115, 'New Record inserted On transport route id 8', '8', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:28:54', NULL),
(116, 'New Record inserted On transport route id 9', '9', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:28:59', NULL),
(117, 'New Record inserted On transport route id 10', '10', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:29:03', NULL),
(118, 'New Record inserted On transport route id 11', '11', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:29:08', NULL),
(119, 'New Record inserted On vehicles id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:30:09', NULL),
(120, 'New Record inserted On vehicles id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:30:57', NULL),
(121, 'New Record inserted On vehicles id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 21:33:36', NULL),
(122, 'New Record inserted On vehicle routes id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:13:31', NULL),
(123, 'New Record inserted On vehicle routes id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:13:45', NULL),
(124, 'New Record inserted On vehicle routes id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:14:05', NULL),
(125, 'New Record inserted On vehicle routes id 6', '6', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:14:18', NULL),
(126, 'New Record inserted On item supplier id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:14:21', NULL),
(127, 'New Record inserted On vehicle routes id 7', '7', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:14:25', NULL),
(128, 'New Record inserted On item supplier id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:15:13', NULL),
(129, 'New Record inserted On item store id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:15:41', NULL),
(130, 'New Record inserted On item store id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:15:57', NULL),
(131, 'New Record inserted On item store id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:16:07', NULL),
(132, 'New Record inserted On item store id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:16:32', NULL),
(133, 'New Record inserted On item category id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:16:41', NULL),
(134, 'New Record inserted On item category id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:16:48', NULL),
(135, 'New Record inserted On item category id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:16:51', NULL),
(136, 'New Record inserted On item category id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:16:56', NULL),
(137, 'New Record inserted On item id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:17:23', NULL),
(138, 'New Record inserted On item id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:17:36', NULL),
(139, 'New Record inserted On transport route id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:17:45', NULL),
(140, 'New Record inserted On transport route id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:17:45', NULL),
(141, 'New Record inserted On transport route id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:17:45', NULL),
(142, 'New Record inserted On transport route id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:17:45', NULL),
(143, 'New Record inserted On item id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:17:48', NULL),
(144, 'New Record inserted On item stock id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:18:33', NULL),
(145, 'New Record inserted On item stock id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:18:57', NULL),
(146, 'New Record inserted On item issue id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:19:50', NULL),
(147, 'New Record inserted On transport route id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:20:05', NULL),
(148, 'New Record inserted On transport route id 6', '6', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:20:05', NULL),
(149, 'New Record inserted On transport route id 7', '7', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:20:05', NULL),
(150, 'New Record inserted On transport route id 8', '8', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:20:05', NULL),
(151, 'New Record inserted On item issue id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:20:14', NULL),
(152, 'Record updated On  item issue id   2  ', '  2  ', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:20:22', NULL),
(153, 'Record updated On  transport route id 5', '5', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:20:32', NULL),
(154, 'Record updated On  transport route id 8', '8', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:20:32', NULL),
(155, 'Record updated On  transport route id 6', '6', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:20:32', NULL),
(156, 'Record updated On  transport route id 7', '7', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:20:32', NULL),
(157, 'New Record inserted On books id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:22:01', NULL),
(158, 'New Record inserted On books id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:22:13', NULL),
(159, 'Record updated On books id 2', '2', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:22:48', NULL),
(160, 'New Record inserted On transport route id 9', '9', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:22:49', NULL),
(161, 'New Record inserted On transport route id 10', '10', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:22:49', NULL),
(162, 'New Record inserted On transport route id 11', '11', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:22:49', NULL),
(163, 'New Record inserted On transport route id 12', '12', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:22:49', NULL),
(164, 'New Record inserted On transport route id 13', '13', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:24:49', NULL),
(165, 'New Record inserted On transport route id 14', '14', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:24:49', NULL),
(166, 'New Record inserted On transport route id 15', '15', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:24:49', NULL),
(167, 'New Record inserted On transport route id 16', '16', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:24:49', NULL),
(168, 'New Record inserted On sections id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:26:21', NULL),
(169, 'New Record inserted On sections id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:26:26', NULL),
(170, 'New Record inserted On sections id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:26:29', NULL),
(171, 'New Record inserted On sections id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:26:34', NULL),
(172, 'New Record inserted On sections id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:26:38', NULL),
(173, 'New Record inserted On subject groups id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:27:05', NULL),
(174, 'New Record inserted On subject groups id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:27:19', NULL),
(175, 'New Record inserted On subjects id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:27:25', NULL),
(176, 'New Record inserted On subject groups id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:27:31', NULL),
(177, 'New Record inserted On subjects id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:27:41', NULL),
(178, 'New Record inserted On subject groups id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:27:45', NULL),
(179, 'New Record inserted On subjects id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:27:50', NULL),
(180, 'New Record inserted On subject groups id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:27:58', NULL),
(181, 'New Record inserted On subjects id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:28:04', NULL),
(182, 'New Record inserted On subjects id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:28:43', NULL),
(183, 'New Record inserted On subject groups id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:29:41', NULL),
(184, 'New Record inserted On subject groups id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:30:12', NULL),
(185, 'New Record inserted On subject groups id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:30:33', NULL),
(186, 'New Record inserted On subject groups id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:30:53', NULL),
(187, 'New Record inserted On subject groups id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:32:02', NULL),
(188, 'New Record inserted On  categories id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:33:35', NULL),
(189, 'New Record inserted On  categories id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:33:37', NULL),
(190, 'New Record inserted On  categories id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:33:49', NULL),
(191, 'New Record inserted On  categories id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:33:55', NULL),
(192, 'New Record inserted On school houses id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:37:53', NULL),
(193, 'New Record inserted On school houses id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:37:54', NULL),
(194, 'Record updated On  school houses id 2', '2', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:38:03', NULL),
(195, 'New Record inserted On school houses id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:38:06', NULL),
(196, 'New Record inserted On school houses id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:38:11', NULL),
(197, 'New Record inserted On disable reason id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:38:55', NULL),
(198, 'New Record inserted On disable reason id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:39:03', NULL),
(199, 'New Record inserted On students id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:39:48', NULL),
(200, 'New Record inserted On  student session id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:39:48', NULL),
(201, 'New Record inserted On users id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:39:48', NULL),
(202, 'New Record inserted On users id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:39:48', NULL),
(203, 'Record updated On students id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:39:48', NULL),
(204, 'New Record inserted On students id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:41:26', NULL),
(205, 'New Record inserted On  student session id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:41:26', NULL),
(206, 'New Record inserted On users id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:41:26', NULL),
(207, 'New Record inserted On users id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:41:26', NULL),
(208, 'Record updated On students id 2', '2', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:41:26', NULL),
(209, 'New Record inserted On reference id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:43:31', NULL),
(210, 'New Record inserted On reference id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:43:38', NULL),
(211, 'New Record inserted On reference id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:43:51', NULL),
(212, 'New Record inserted On reference id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:43:56', NULL),
(213, 'New Record inserted On reference id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:43:56', NULL),
(214, 'New Record inserted On source id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:46:58', NULL),
(215, 'New Record inserted On source id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:47:03', NULL),
(216, 'New Record inserted On source id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:47:11', NULL),
(217, 'New Record inserted On source id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:47:17', NULL),
(218, 'New Record inserted On  complaint_type id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:47:40', NULL),
(219, 'New Record inserted On  complaint_type id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:47:45', NULL),
(220, 'New Record inserted On  complaint_type id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:47:52', NULL),
(221, 'New Record inserted On  complaint_type id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:47:59', NULL),
(222, 'New Record inserted On  complaint_type id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:48:06', NULL),
(223, 'New Record inserted On visitors purpose id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:48:25', NULL),
(224, 'New Record inserted On visitors purpose id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:48:33', NULL),
(225, 'New Record inserted On visitors purpose id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:48:39', NULL),
(226, 'New Record inserted On visitors purpose id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:48:44', NULL),
(227, 'New Record inserted On visitors purpose id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:49:14', NULL),
(228, 'New Record inserted On  enquiry id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:50:53', NULL),
(229, 'New Record inserted On  Complain id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:51:16', NULL),
(230, 'New Record inserted On  Complain id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:51:35', NULL),
(231, 'New Record inserted On  enquiry id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:52:31', NULL),
(232, 'New Record inserted On  visitors  id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:52:41', NULL),
(233, 'New Record inserted On  visitors  id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:53:00', NULL),
(234, 'New Record inserted On  enquiry id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:53:21', NULL),
(235, 'New Record inserted On  visitors  id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:53:34', NULL),
(236, 'New Record inserted On  enquiry id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 22:54:32', NULL),
(237, 'New Record inserted On  Admission Enquiry  Postal Receive id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:05:28', NULL),
(238, 'New Record inserted On  Admission Enquiry  Postal Receive id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:05:45', NULL),
(239, 'New Record inserted On  Admission Enquiry  Postal Receive id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:06:43', NULL),
(240, 'New Record inserted On  Phone Call Log id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:07:03', NULL),
(241, 'New Record inserted On  Admission Enquiry  Postal Receive id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:07:08', NULL),
(242, 'New Record inserted On  Phone Call Log id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:07:39', NULL),
(243, 'New Record inserted On  Admission Enquiry  Postal Dispatch id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:07:39', NULL),
(244, 'New Record inserted On  Admission Enquiry  Postal Dispatch id 6', '6', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:07:53', NULL),
(245, 'New Record inserted On  Phone Call Log id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:08:03', NULL),
(246, 'New Record inserted On  Admission Enquiry  Postal Dispatch id 7', '7', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:08:10', NULL),
(247, 'New Record inserted On  fee type id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:10:15', NULL),
(248, 'New Record inserted On  fees discounts id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:10:29', NULL),
(249, 'New Record inserted On  fee type id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:10:39', NULL),
(250, 'New Record inserted On  fees discounts id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:10:51', NULL),
(251, 'New Record inserted On  fee type id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:11:02', NULL),
(252, 'New Record inserted On  fees discounts id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:11:09', NULL),
(253, 'New Record inserted On  fee group id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:11:18', NULL),
(254, 'New Record inserted On  fee group id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:11:22', NULL),
(255, 'New Record inserted On  fee group id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:11:27', NULL),
(256, 'New Record inserted On  fee group id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:11:42', NULL),
(257, 'New Record inserted On  fee group id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:12:11', NULL),
(258, 'New Record inserted On  fee group id 6', '6', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:12:24', NULL),
(259, 'New Record inserted On  fee groups feetype id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:13:48', NULL),
(260, 'New Record inserted On  income head   id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:14:02', NULL),
(261, 'New Record inserted On  income head   id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:14:05', NULL),
(262, 'New Record inserted On  income head   id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:14:10', NULL),
(263, 'New Record inserted On  income head   id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:14:15', NULL),
(264, 'New Record inserted On  income head   id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:14:20', NULL),
(265, 'New Record inserted On  fee groups feetype id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:15:11', NULL),
(266, 'New Record inserted On  fee groups feetype id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:17:32', NULL),
(267, 'New Record inserted On  fee groups feetype id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:20:05', NULL),
(268, 'New Record inserted On  Income   id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:21:06', NULL),
(269, 'Record updated On  Income   id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:21:31', NULL),
(270, 'New Record inserted On  Income   id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:23:01', NULL),
(271, 'New Record inserted On  Income   id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:23:45', NULL),
(272, 'New Record inserted On  expense head id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:24:16', NULL),
(273, 'New Record inserted On  expense head id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:24:23', NULL),
(274, 'New Record inserted On  expense head id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:24:26', NULL),
(275, 'New Record inserted On  expense head id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:24:39', NULL),
(276, 'New Record inserted On  expenses   id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:25:35', NULL),
(277, 'New Record inserted On mark_divisions id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:25:42', NULL),
(278, 'New Record inserted On mark_divisions id 2', '2', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:25:57', NULL),
(279, 'New Record inserted On mark_divisions id 3', '3', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:26:04', NULL),
(280, 'New Record inserted On  expenses   id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:26:24', NULL),
(281, 'Record updated On  expense head id 4', '4', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:26:51', NULL),
(282, 'New Record inserted On  expenses   id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:27:33', NULL),
(283, 'New Record inserted On grades id 1', '1', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:27:36', NULL),
(284, 'New Record inserted On  expenses   id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:28:07', NULL),
(285, 'New Record inserted On grades id 2', '2', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:28:09', NULL),
(286, 'New Record inserted On grades id 3', '3', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:29:00', NULL),
(287, 'New Record inserted On grades id 4', '4', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:29:19', NULL),
(288, 'New Record inserted On grades id 5', '5', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:29:52', NULL),
(289, 'New Record inserted On grades id 6', '6', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:30:11', NULL),
(290, 'New Record inserted On grades id 7', '7', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:30:38', NULL),
(291, 'New Record inserted On grades id 8', '8', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:30:56', NULL),
(292, 'New Record inserted On admit cards id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:31:58', NULL),
(293, 'New Record inserted On exam groups id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:32:25', NULL),
(294, 'New Record inserted On exam groups id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:32:59', NULL),
(295, 'New Record inserted On  questions id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:34:18', NULL),
(296, 'New Record inserted On  questions id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:34:53', NULL),
(297, 'New Record inserted On  online exam id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:35:19', NULL),
(298, 'New Record inserted On lesson id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:37:21', NULL),
(299, 'New Record inserted On lesson id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:37:21', NULL),
(300, 'New Record inserted On lesson id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:37:21', NULL),
(301, 'New Record inserted On lesson id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:37:21', NULL),
(302, 'New Record inserted On lesson id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:37:21', NULL),
(303, 'New Record inserted On lesson id 6', '6', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:38:59', NULL),
(304, 'New Record inserted On lesson id 7', '7', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:38:59', NULL),
(305, 'New Record inserted On lesson id 8', '8', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:38:59', NULL),
(306, 'New Record inserted On lesson id 9', '9', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:38:59', NULL),
(307, 'New Record inserted On lesson id 10', '10', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:38:59', NULL),
(308, 'New Record inserted On lesson id 11', '11', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:38:59', NULL),
(309, 'New Record inserted On lesson id 12', '12', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:39:59', NULL),
(310, 'New Record inserted On lesson id 13', '13', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:39:59', NULL),
(311, 'New Record inserted On lesson id 14', '14', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:39:59', NULL),
(312, 'New Record inserted On lesson id 15', '15', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:39:59', NULL),
(313, 'New Record inserted On lesson id 16', '16', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:39:59', NULL),
(314, 'New Record inserted On topic id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:40:23', NULL),
(315, 'New Record inserted On topic id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:40:23', NULL),
(316, 'New Record inserted On lesson id 17', '17', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:41:06', NULL),
(317, 'New Record inserted On lesson id 18', '18', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:41:06', NULL),
(318, 'New Record inserted On lesson id 19', '19', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:41:06', NULL),
(319, 'New Record inserted On topic id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:41:12', NULL),
(320, 'New Record inserted On  staff designation id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:41:52', NULL),
(321, 'New Record inserted On  staff designation id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:41:58', NULL),
(322, 'New Record inserted On  staff designation id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:03', NULL),
(323, 'New Record inserted On  staff designation id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:07', NULL),
(324, 'New Record inserted On  staff designation id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:11', NULL),
(325, 'New Record inserted On  staff designation id 6', '6', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:15', NULL),
(326, 'New Record inserted On  staff designation id 7', '7', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:19', NULL),
(327, 'New Record inserted On department id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:23', NULL),
(328, 'New Record inserted On  staff designation id 8', '8', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:23', NULL),
(329, 'New Record inserted On department id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:27', NULL),
(330, 'New Record inserted On department id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:32', NULL),
(331, 'New Record inserted On department id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:36', NULL),
(332, 'New Record inserted On department id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:40', NULL),
(333, 'New Record inserted On department id 6', '6', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:47', NULL),
(334, 'New Record inserted On department id 7', '7', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:51', NULL),
(335, 'New Record inserted On leave types id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:54', NULL),
(336, 'New Record inserted On department id 8', '8', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:56', NULL),
(337, 'New Record inserted On leave types id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:42:59', NULL),
(338, 'New Record inserted On department id 9', '9', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:43:00', NULL),
(339, 'New Record inserted On leave types id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:43:02', NULL),
(340, 'New Record inserted On department id 10', '10', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:43:04', NULL);
INSERT INTO `logs` (`id`, `message`, `record_id`, `user_id`, `action`, `ip_address`, `platform`, `agent`, `time`, `created_at`) VALUES
(341, 'New Record inserted On leave types id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:43:05', NULL),
(342, 'Record updated On staff id 2', '2', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:52:19', NULL),
(343, 'Record updated On staff id 2', '2', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:52:46', NULL),
(344, 'Record updated On staff id 2', '2', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:52:46', NULL),
(345, 'Record updated On staff id 3', '3', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:54:02', NULL),
(346, 'Record updated On staff id 3', '3', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:54:29', NULL),
(347, 'Record updated On staff id 3', '3', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:54:29', NULL),
(348, 'Record updated On staff id 4', '4', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:55:47', NULL),
(349, 'Record updated On staff id 5', '5', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:58:41', NULL),
(350, 'Record updated On staff id 5', '5', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:59:15', NULL),
(351, 'Record updated On staff id 5', '5', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-25 23:59:15', NULL),
(352, 'New Record inserted On books id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:01:03', NULL),
(353, 'New Record inserted On send notification id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:02:13', NULL),
(354, 'New Record inserted On send notification id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:03:09', NULL),
(355, 'New Record inserted On video tutorial id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:03:36', NULL),
(356, 'New Record inserted On Video Tutorial class sections id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:03:36', NULL),
(357, 'New Record inserted On send notification id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:03:50', NULL),
(358, 'New Record inserted On video tutorial id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:04:01', NULL),
(359, 'New Record inserted On Video Tutorial class sections id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:04:01', NULL),
(360, 'New Record inserted On video tutorial id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:04:02', NULL),
(361, 'New Record inserted On Video Tutorial class sections id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:04:02', NULL),
(362, 'New Record inserted On send notification id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:04:31', NULL),
(363, 'New Record inserted On video tutorial id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:04:35', NULL),
(364, 'New Record inserted On Video Tutorial class sections id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:04:35', NULL),
(365, 'New Record inserted On send notification id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:05:08', NULL),
(366, 'New Record inserted On  content_types id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:07:03', NULL),
(367, 'New Record inserted On  content_types id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:07:08', NULL),
(368, 'New Record inserted On  content_types id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:07:15', NULL),
(369, 'New Record inserted On  content_types id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:07:20', NULL),
(370, 'New Record inserted On  content_types id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:07:25', NULL),
(371, 'New Record inserted On  upload_contents id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:10:16', NULL),
(372, 'New Record inserted On email template id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:11:32', NULL),
(373, 'New Record inserted On email template id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:12:29', NULL),
(374, 'Record deleted On email template attachment id 2', '2', 1, 'Delete', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:12:50', NULL),
(375, 'Record updated On  email template id 2', '2', 1, 'Update', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:12:50', NULL),
(376, 'New Record inserted On  upload_contents id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:12:53', NULL),
(377, 'New Record inserted On email template id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:13:14', NULL),
(378, 'New Record inserted On email template id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:14:35', NULL),
(379, 'New Record inserted On sms template id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:15:17', NULL),
(380, 'New Record inserted On email template id 5', '5', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:15:22', NULL),
(381, 'New Record inserted On sms template id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:15:29', NULL),
(382, 'New Record inserted On sms template id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:15:43', NULL),
(383, 'New Record inserted On sms template id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:16:00', NULL),
(384, 'New Record inserted On messages id 1', '1', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:16:52', NULL),
(385, 'New Record inserted On messages id 2', '2', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:17:41', NULL),
(386, 'New Record inserted On messages id 3', '3', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:18:10', NULL),
(387, 'New Record inserted On messages id 4', '4', 1, 'Insert', '27.57.176.115', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 00:18:22', NULL),
(388, 'Record updated On  Page List id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:32:33', NULL),
(389, 'New Record inserted On Menu id 8', '8', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:44:53', NULL),
(390, 'New Record inserted On Menu id 9', '9', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:45:19', NULL),
(391, 'New Record inserted On Menu id 10', '10', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:45:40', NULL),
(392, 'Record deleted On Page List id gallery', 'gallery', 1, 'Delete', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:48:42', NULL),
(393, 'Record deleted On Page List id events', 'events', 1, 'Delete', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:48:46', NULL),
(394, 'New Record inserted On Page List id 10', '10', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:49:33', NULL),
(395, 'New Record inserted On Page List id 11', '11', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:50:12', NULL),
(396, 'New Record inserted On Page List id 12', '12', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:50:48', NULL),
(397, 'New Record inserted On Page List id 13', '13', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:52:02', NULL),
(398, 'New Record inserted On Page List id 14', '14', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:52:30', NULL),
(399, 'New Record inserted On Page List id 15', '15', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:52:50', NULL),
(400, 'New Record inserted On Page List id 16', '16', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:53:19', NULL),
(401, 'New Record inserted On Page List id 17', '17', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:53:42', NULL),
(402, 'New Record inserted On Page List id 18', '18', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:54:07', NULL),
(403, 'New Record inserted On Page List id 19', '19', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:54:33', NULL),
(404, 'New Record inserted On Page List id 20', '20', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:54:57', NULL),
(405, 'New Record inserted On Page List id 21', '21', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:55:24', NULL),
(406, 'New Record inserted On Page List id 22', '22', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:55:49', NULL),
(407, 'New Record inserted On Page List id 23', '23', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:56:20', NULL),
(408, 'New Record inserted On Page List id 24', '24', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:56:42', NULL),
(409, 'New Record inserted On Page List id 25', '25', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:57:25', NULL),
(410, 'New Record inserted On Page List id 26', '26', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:57:40', NULL),
(411, 'New Record inserted On Page List id 27', '27', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:58:18', NULL),
(412, 'New Record inserted On Page List id 28', '28', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:58:38', NULL),
(413, 'New Record inserted On Page List id 29', '29', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:59:05', NULL),
(414, 'New Record inserted On Page List id 30', '30', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 19:59:26', NULL),
(415, 'New Record inserted On Menu id 11', '11', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:00:50', NULL),
(416, 'New Record inserted On Menu id 12', '12', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:01:24', NULL),
(417, 'New Record inserted On Menu id 13', '13', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:01:41', NULL),
(418, 'New Record inserted On Menu id 14', '14', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:01:57', NULL),
(419, 'New Record inserted On Menu id 15', '15', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:02:10', NULL),
(420, 'New Record inserted On Menu id 16', '16', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:02:28', NULL),
(421, 'New Record inserted On Menu id 17', '17', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:02:45', NULL),
(422, 'New Record inserted On Menu id 18', '18', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:03:00', NULL),
(423, 'New Record inserted On Menu id 19', '19', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:03:15', NULL),
(424, 'New Record inserted On Menu id 20', '20', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:03:51', NULL),
(425, 'New Record inserted On Menu id 21', '21', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:04:38', NULL),
(426, 'New Record inserted On Menu id 22', '22', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:04:59', NULL),
(427, 'New Record inserted On Menu id 23', '23', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:05:11', NULL),
(428, 'Record deleted On menu id 3', '3', 1, 'Delete', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:05:37', NULL),
(429, 'New Record inserted On Menu id 24', '24', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:06:06', NULL),
(430, 'New Record inserted On Menu id 25', '25', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:06:24', NULL),
(431, 'Record updated On Menu id 2', '2', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:06:47', NULL),
(432, 'Record updated On  Page List id 2', '2', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:10:00', NULL),
(433, 'Record updated On  Page List id 30', '30', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:10:27', NULL),
(434, 'Record updated On  Page List id 3', '3', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:10:43', NULL),
(435, 'Record updated On  Page List id 4', '4', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:10:47', NULL),
(436, 'Record updated On Menu id 10', '10', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:10:55', NULL),
(437, 'Record updated On  Page List id 4', '4', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:11:10', NULL),
(438, 'New Record inserted On students id 3', '3', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:21:11', NULL),
(439, 'New Record inserted On  student session id 3', '3', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:21:11', NULL),
(440, 'New Record inserted On users id 5', '5', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:21:11', NULL),
(441, 'New Record inserted On users id 6', '6', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:21:11', NULL),
(442, 'Record updated On students id 3', '3', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:21:11', NULL),
(443, 'New Record inserted On students id 4', '4', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:24:34', NULL),
(444, 'New Record inserted On  student session id 4', '4', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:24:34', NULL),
(445, 'New Record inserted On users id 7', '7', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:24:34', NULL),
(446, 'New Record inserted On users id 8', '8', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:24:34', NULL),
(447, 'Record updated On students id 4', '4', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:24:34', NULL),
(448, 'Record updated On students id 3', '3', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:26:15', NULL),
(449, 'Record updated On  student session id 3', '3', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:26:15', NULL),
(450, 'Record updated On students id 3', '3', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:27:28', NULL),
(451, 'Record updated On  student session id 3', '3', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:27:28', NULL),
(452, 'Record updated On students id 2', '2', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:28:08', NULL),
(453, 'Record updated On  student session id 2', '2', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:28:08', NULL),
(454, 'Record updated On students id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:28:41', NULL),
(455, 'Record updated On  student session id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:28:41', NULL),
(456, 'Record updated On students id 3', '3', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:29:12', NULL),
(457, 'Record updated On  student session id 3', '3', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:29:12', NULL),
(458, 'New Record inserted On students id 5', '5', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:30:57', NULL),
(459, 'New Record inserted On  student session id 5', '5', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:30:57', NULL),
(460, 'New Record inserted On users id 9', '9', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:30:57', NULL),
(461, 'New Record inserted On users id 10', '10', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:30:57', NULL),
(462, 'Record updated On students id 5', '5', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:30:57', NULL),
(463, 'New Record inserted On students id 6', '6', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:31:34', NULL),
(464, 'New Record inserted On  student session id 6', '6', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:31:34', NULL),
(465, 'New Record inserted On users id 11', '11', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:31:34', NULL),
(466, 'New Record inserted On users id 12', '12', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:31:34', NULL),
(467, 'Record updated On students id 6', '6', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:31:34', NULL),
(468, 'Record updated On students id 2', '2', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:32:52', NULL),
(469, 'Record updated On  student session id 2', '2', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:32:52', NULL),
(470, 'Record updated On students id 6', '6', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:33:30', NULL),
(471, 'Record updated On  student session id 6', '6', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:33:30', NULL),
(472, 'Record updated On students id 2', '2', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:34:10', NULL),
(473, 'Record updated On  student session id 2', '2', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:34:10', NULL),
(474, 'New Record inserted On students id 7', '7', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:34:16', NULL),
(475, 'New Record inserted On  student session id 7', '7', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:34:16', NULL),
(476, 'New Record inserted On users id 13', '13', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:34:16', NULL),
(477, 'New Record inserted On users id 14', '14', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:34:16', NULL),
(478, 'Record updated On students id 7', '7', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:34:16', NULL),
(479, 'New Record inserted On students id 8', '8', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:37:02', NULL),
(480, 'New Record inserted On  student session id 8', '8', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:37:02', NULL),
(481, 'New Record inserted On users id 15', '15', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:37:02', NULL),
(482, 'New Record inserted On users id 16', '16', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:37:02', NULL),
(483, 'Record updated On students id 8', '8', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:37:02', NULL),
(484, 'New Record inserted On students id 9', '9', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:37:41', NULL),
(485, 'New Record inserted On  student session id 9', '9', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:37:41', NULL),
(486, 'New Record inserted On users id 17', '17', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:37:41', NULL),
(487, 'New Record inserted On users id 18', '18', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:37:41', NULL),
(488, 'Record updated On students id 9', '9', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:37:41', NULL),
(489, 'New Record inserted On students id 10', '10', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:38:08', NULL),
(490, 'New Record inserted On  student session id 10', '10', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:38:08', NULL),
(491, 'New Record inserted On users id 19', '19', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:38:08', NULL),
(492, 'New Record inserted On users id 20', '20', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:38:08', NULL),
(493, 'Record updated On students id 10', '10', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:38:08', NULL),
(494, 'Record updated On students id 7', '7', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:38:27', NULL),
(495, 'Record updated On  student session id 7', '7', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:38:27', NULL),
(496, 'Record deleted On classes id 5', '5', 1, 'Delete', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:40:08', NULL),
(497, 'Record deleted On sections id 5', '5', 1, 'Delete', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:40:48', NULL),
(498, 'New Record inserted On students id 11', '11', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:41:40', NULL),
(499, 'New Record inserted On  student session id 11', '11', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:41:40', NULL),
(500, 'New Record inserted On users id 21', '21', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:41:40', NULL),
(501, 'New Record inserted On users id 22', '22', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:41:40', NULL),
(502, 'Record updated On students id 11', '11', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:41:40', NULL),
(503, 'New Record inserted On students id 12', '12', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:43:14', NULL),
(504, 'New Record inserted On  student session id 12', '12', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:43:14', NULL),
(505, 'New Record inserted On users id 23', '23', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:43:14', NULL),
(506, 'New Record inserted On users id 24', '24', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:43:14', NULL),
(507, 'Record updated On students id 12', '12', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:43:14', NULL),
(508, 'New Record inserted On students id 13', '13', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:43:18', NULL),
(509, 'New Record inserted On  student session id 13', '13', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:43:18', NULL),
(510, 'New Record inserted On users id 25', '25', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:43:18', NULL),
(511, 'New Record inserted On users id 26', '26', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:43:18', NULL),
(512, 'Record updated On students id 13', '13', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:43:18', NULL),
(513, 'New Record inserted On students id 14', '14', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:45:39', NULL),
(514, 'New Record inserted On  student session id 14', '14', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:45:39', NULL),
(515, 'New Record inserted On users id 27', '27', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:45:39', NULL),
(516, 'New Record inserted On users id 28', '28', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:45:39', NULL),
(517, 'Record updated On students id 14', '14', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:45:39', NULL),
(518, 'New Record inserted On students id 15', '15', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:46:00', NULL),
(519, 'New Record inserted On  student session id 15', '15', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:46:00', NULL),
(520, 'New Record inserted On users id 29', '29', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:46:00', NULL),
(521, 'New Record inserted On users id 30', '30', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:46:00', NULL),
(522, 'Record updated On students id 15', '15', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:46:00', NULL),
(523, 'Record updated On students id 14', '14', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:48:24', NULL),
(524, 'Record updated On  student session id 14', '14', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:48:24', NULL),
(525, 'New Record inserted On students id 16', '16', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:48:27', NULL),
(526, 'New Record inserted On  student session id 16', '16', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:48:27', NULL),
(527, 'New Record inserted On users id 31', '31', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:48:27', NULL),
(528, 'New Record inserted On users id 32', '32', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:48:27', NULL),
(529, 'Record updated On students id 16', '16', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:48:27', NULL),
(530, 'Record updated On students id 4', '4', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:48:56', NULL),
(531, 'Record updated On  student session id 4', '4', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:48:56', NULL),
(532, 'Record updated On students id 3', '3', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:49:17', NULL),
(533, 'Record updated On  student session id 3', '3', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:49:17', NULL),
(534, 'Record updated On students id 5', '5', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:49:45', NULL),
(535, 'Record updated On  student session id 5', '5', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:49:45', NULL),
(536, 'Record updated On students id 7', '7', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:50:02', NULL),
(537, 'Record updated On  student session id 7', '7', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:50:02', NULL),
(538, 'Record updated On students id 9', '9', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:50:27', NULL),
(539, 'Record updated On  student session id 9', '9', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:50:27', NULL),
(540, 'New Record inserted On students id 17', '17', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:50:48', NULL),
(541, 'New Record inserted On  student session id 17', '17', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:50:48', NULL),
(542, 'New Record inserted On users id 33', '33', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:50:48', NULL),
(543, 'New Record inserted On users id 34', '34', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:50:48', NULL),
(544, 'Record updated On students id 17', '17', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:50:48', NULL),
(545, 'Record updated On students id 14', '14', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:50:56', NULL),
(546, 'Record updated On  student session id 14', '14', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:50:56', NULL),
(547, 'Record updated On students id 12', '12', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:51:22', NULL),
(548, 'Record updated On  student session id 12', '12', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:51:22', NULL),
(549, 'Record updated On students id 11', '11', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:51:49', NULL),
(550, 'Record updated On  student session id 11', '11', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:51:49', NULL),
(551, 'Record updated On students id 11', '11', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:52:09', NULL),
(552, 'Record updated On  student session id 11', '11', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:52:09', NULL),
(553, 'New Record inserted On students id 18', '18', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:52:14', NULL),
(554, 'New Record inserted On  student session id 18', '18', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:52:14', NULL),
(555, 'New Record inserted On users id 35', '35', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:52:14', NULL),
(556, 'New Record inserted On users id 36', '36', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:52:14', NULL),
(557, 'Record updated On students id 18', '18', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:52:14', NULL),
(558, 'New Record inserted On students id 19', '19', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:53:35', NULL),
(559, 'New Record inserted On  student session id 19', '19', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:53:35', NULL),
(560, 'New Record inserted On users id 37', '37', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:53:35', NULL),
(561, 'New Record inserted On users id 38', '38', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:53:35', NULL),
(562, 'Record updated On students id 19', '19', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:53:35', NULL),
(563, 'New Record inserted On students id 20', '20', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:53:37', NULL),
(564, 'New Record inserted On  student session id 20', '20', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:53:37', NULL),
(565, 'New Record inserted On users id 39', '39', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:53:37', NULL),
(566, 'New Record inserted On users id 40', '40', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:53:37', NULL),
(567, 'Record updated On students id 20', '20', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:53:37', NULL),
(568, 'New Record inserted On students id 21', '21', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:54:46', NULL),
(569, 'New Record inserted On  student session id 21', '21', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:54:46', NULL),
(570, 'New Record inserted On users id 41', '41', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:54:46', NULL),
(571, 'New Record inserted On users id 42', '42', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:54:46', NULL),
(572, 'Record updated On students id 21', '21', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:54:46', NULL),
(573, 'New Record inserted On students id 22', '22', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:54:56', NULL),
(574, 'New Record inserted On  student session id 22', '22', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:54:56', NULL),
(575, 'New Record inserted On users id 43', '43', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:54:56', NULL),
(576, 'New Record inserted On users id 44', '44', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:54:56', NULL),
(577, 'Record updated On students id 22', '22', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:54:56', NULL),
(578, 'New Record inserted On students id 23', '23', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:55:59', NULL),
(579, 'New Record inserted On  student session id 23', '23', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:55:59', NULL),
(580, 'New Record inserted On users id 45', '45', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:55:59', NULL),
(581, 'New Record inserted On users id 46', '46', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:55:59', NULL),
(582, 'Record updated On students id 23', '23', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:55:59', NULL),
(583, 'New Record inserted On students id 24', '24', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:56:01', NULL),
(584, 'New Record inserted On  student session id 24', '24', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:56:01', NULL),
(585, 'New Record inserted On users id 47', '47', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:56:01', NULL),
(586, 'New Record inserted On users id 48', '48', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:56:01', NULL),
(587, 'Record updated On students id 24', '24', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:56:01', NULL),
(588, 'New Record inserted On students id 25', '25', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:58:30', NULL),
(589, 'New Record inserted On  student session id 25', '25', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:58:30', NULL),
(590, 'New Record inserted On users id 49', '49', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:58:30', NULL),
(591, 'New Record inserted On users id 50', '50', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:58:30', NULL),
(592, 'Record updated On students id 25', '25', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 20:58:30', NULL),
(593, 'New Record inserted On students id 26', '26', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:00:42', NULL),
(594, 'New Record inserted On  student session id 26', '26', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:00:42', NULL),
(595, 'New Record inserted On users id 51', '51', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:00:42', NULL),
(596, 'New Record inserted On users id 52', '52', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:00:42', NULL),
(597, 'Record updated On students id 26', '26', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:00:42', NULL),
(598, 'New Record inserted On students id 27', '27', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:00:54', NULL),
(599, 'New Record inserted On  student session id 27', '27', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:00:54', NULL),
(600, 'New Record inserted On users id 53', '53', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:00:54', NULL),
(601, 'New Record inserted On users id 54', '54', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:00:54', NULL),
(602, 'Record updated On students id 27', '27', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:00:54', NULL),
(603, 'New Record inserted On students id 28', '28', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:01:43', NULL),
(604, 'New Record inserted On  student session id 28', '28', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:01:43', NULL),
(605, 'New Record inserted On users id 55', '55', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:01:43', NULL),
(606, 'New Record inserted On users id 56', '56', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:01:43', NULL),
(607, 'Record updated On students id 28', '28', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:01:43', NULL),
(608, 'New Record inserted On students id 29', '29', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:02:11', NULL),
(609, 'New Record inserted On  student session id 29', '29', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:02:11', NULL),
(610, 'New Record inserted On users id 57', '57', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:02:11', NULL),
(611, 'New Record inserted On users id 58', '58', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:02:11', NULL),
(612, 'Record updated On students id 29', '29', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:02:11', NULL),
(613, 'New Record inserted On students id 30', '30', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:02:37', NULL),
(614, 'New Record inserted On  student session id 30', '30', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:02:37', NULL),
(615, 'New Record inserted On users id 59', '59', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:02:37', NULL),
(616, 'New Record inserted On users id 60', '60', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:02:37', NULL),
(617, 'Record updated On students id 30', '30', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:02:37', NULL),
(618, 'New Record inserted On students id 31', '31', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:03:20', NULL),
(619, 'New Record inserted On  student session id 31', '31', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:03:20', NULL),
(620, 'New Record inserted On users id 61', '61', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:03:20', NULL),
(621, 'New Record inserted On users id 62', '62', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:03:20', NULL),
(622, 'Record updated On students id 31', '31', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:03:20', NULL),
(623, 'New Record inserted On students id 32', '32', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:04:17', NULL),
(624, 'New Record inserted On  student session id 32', '32', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:04:17', NULL),
(625, 'New Record inserted On users id 63', '63', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:04:17', NULL),
(626, 'New Record inserted On users id 64', '64', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:04:17', NULL),
(627, 'Record updated On students id 32', '32', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:04:17', NULL),
(628, 'New Record inserted On students id 33', '33', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:04:31', NULL),
(629, 'New Record inserted On  student session id 33', '33', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:04:31', NULL),
(630, 'New Record inserted On users id 65', '65', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:04:31', NULL),
(631, 'New Record inserted On users id 66', '66', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:04:31', NULL),
(632, 'Record updated On students id 33', '33', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:04:31', NULL),
(633, 'New Record inserted On students id 34', '34', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:05:49', NULL),
(634, 'New Record inserted On  student session id 34', '34', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:05:49', NULL),
(635, 'New Record inserted On users id 67', '67', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:05:49', NULL),
(636, 'New Record inserted On users id 68', '68', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:05:49', NULL),
(637, 'Record updated On students id 34', '34', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:05:49', NULL),
(638, 'New Record inserted On students id 35', '35', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:05:57', NULL),
(639, 'New Record inserted On  student session id 35', '35', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:05:57', NULL),
(640, 'New Record inserted On users id 69', '69', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:05:57', NULL),
(641, 'New Record inserted On users id 70', '70', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:05:57', NULL),
(642, 'Record updated On students id 35', '35', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:05:57', NULL),
(643, 'New Record inserted On students id 36', '36', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:07:03', NULL),
(644, 'New Record inserted On  student session id 36', '36', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:07:03', NULL),
(645, 'New Record inserted On users id 71', '71', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:07:03', NULL),
(646, 'New Record inserted On users id 72', '72', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:07:03', NULL),
(647, 'Record updated On students id 36', '36', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:07:04', NULL),
(648, 'New Record inserted On students id 37', '37', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:07:21', NULL),
(649, 'New Record inserted On  student session id 37', '37', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:07:21', NULL),
(650, 'New Record inserted On users id 73', '73', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:07:21', NULL),
(651, 'New Record inserted On users id 74', '74', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:07:21', NULL),
(652, 'Record updated On students id 37', '37', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:07:21', NULL),
(653, 'New Record inserted On students id 38', '38', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:08:47', NULL),
(654, 'New Record inserted On  student session id 38', '38', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:08:47', NULL),
(655, 'New Record inserted On users id 75', '75', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:08:47', NULL),
(656, 'New Record inserted On users id 76', '76', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:08:47', NULL),
(657, 'Record updated On students id 38', '38', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:08:47', NULL),
(658, 'New Record inserted On students id 39', '39', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:00', NULL),
(659, 'New Record inserted On  student session id 39', '39', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:00', NULL),
(660, 'New Record inserted On users id 77', '77', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:00', NULL),
(661, 'New Record inserted On users id 78', '78', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:00', NULL),
(662, 'Record updated On students id 39', '39', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:00', NULL),
(663, 'New Record inserted On students id 40', '40', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:55', NULL),
(664, 'New Record inserted On  student session id 40', '40', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:55', NULL),
(665, 'New Record inserted On users id 79', '79', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:55', NULL),
(666, 'New Record inserted On users id 80', '80', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:55', NULL),
(667, 'Record updated On students id 40', '40', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:55', NULL),
(668, 'New Record inserted On students id 41', '41', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:58', NULL),
(669, 'New Record inserted On  student session id 41', '41', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:58', NULL),
(670, 'New Record inserted On users id 81', '81', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:58', NULL),
(671, 'New Record inserted On users id 82', '82', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:58', NULL),
(672, 'Record updated On students id 41', '41', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:09:58', NULL),
(673, 'New Record inserted On students id 42', '42', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:11:31', NULL),
(674, 'New Record inserted On  student session id 42', '42', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:11:31', NULL),
(675, 'New Record inserted On users id 83', '83', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:11:31', NULL),
(676, 'New Record inserted On users id 84', '84', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:11:31', NULL),
(677, 'Record updated On students id 42', '42', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:11:31', NULL),
(678, 'New Record inserted On students id 43', '43', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:12:34', NULL),
(679, 'New Record inserted On  student session id 43', '43', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:12:34', NULL),
(680, 'New Record inserted On users id 85', '85', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:12:34', NULL),
(681, 'New Record inserted On users id 86', '86', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:12:34', NULL),
(682, 'Record updated On students id 43', '43', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:12:34', NULL),
(683, 'New Record inserted On students id 44', '44', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:12:38', NULL),
(684, 'New Record inserted On  student session id 44', '44', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:12:38', NULL),
(685, 'New Record inserted On users id 87', '87', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:12:38', NULL);
INSERT INTO `logs` (`id`, `message`, `record_id`, `user_id`, `action`, `ip_address`, `platform`, `agent`, `time`, `created_at`) VALUES
(686, 'New Record inserted On users id 88', '88', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:12:38', NULL),
(687, 'Record updated On students id 44', '44', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:12:38', NULL),
(688, 'New Record inserted On students id 45', '45', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:13:50', NULL),
(689, 'New Record inserted On  student session id 45', '45', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:13:50', NULL),
(690, 'New Record inserted On users id 89', '89', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:13:50', NULL),
(691, 'New Record inserted On users id 90', '90', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:13:50', NULL),
(692, 'Record updated On students id 45', '45', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:13:50', NULL),
(693, 'New Record inserted On students id 46', '46', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:14:01', NULL),
(694, 'New Record inserted On  student session id 46', '46', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:14:01', NULL),
(695, 'New Record inserted On users id 91', '91', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:14:01', NULL),
(696, 'New Record inserted On users id 92', '92', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:14:01', NULL),
(697, 'Record updated On students id 46', '46', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:14:01', NULL),
(698, 'New Record inserted On students id 47', '47', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:15:15', NULL),
(699, 'New Record inserted On  student session id 47', '47', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:15:15', NULL),
(700, 'New Record inserted On users id 93', '93', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:15:15', NULL),
(701, 'New Record inserted On users id 94', '94', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:15:15', NULL),
(702, 'Record updated On students id 47', '47', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:15:15', NULL),
(703, 'Record updated On students id 43', '43', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:16:08', NULL),
(704, 'Record updated On  student session id 43', '43', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:16:08', NULL),
(705, 'New Record inserted On students id 48', '48', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:16:09', NULL),
(706, 'New Record inserted On  student session id 48', '48', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:16:09', NULL),
(707, 'New Record inserted On users id 95', '95', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:16:09', NULL),
(708, 'New Record inserted On users id 96', '96', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:16:09', NULL),
(709, 'Record updated On students id 48', '48', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:16:09', NULL),
(710, 'Record updated On students id 40', '40', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:16:47', NULL),
(711, 'Record updated On  student session id 40', '40', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:16:47', NULL),
(712, 'Record updated On students id 37', '37', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:17:22', NULL),
(713, 'Record updated On  student session id 37', '37', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:17:22', NULL),
(714, 'Record updated On students id 32', '32', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:17:58', NULL),
(715, 'Record updated On  student session id 32', '32', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:17:58', NULL),
(716, 'Record updated On students id 28', '28', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:18:28', NULL),
(717, 'Record updated On  student session id 28', '28', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:18:28', NULL),
(718, 'Record updated On students id 19', '19', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:19:21', NULL),
(719, 'Record updated On  student session id 19', '19', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:19:21', NULL),
(720, 'Record updated On students id 26', '26', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:19:32', NULL),
(721, 'Record updated On  student session id 26', '26', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:19:32', NULL),
(722, 'New Record inserted On Menu id 26', '26', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:41:31', NULL),
(723, 'New Record inserted On Menu id 27', '27', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:41:45', NULL),
(724, 'New Record inserted On Menu id 28', '28', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:42:00', NULL),
(725, 'New Record inserted On Menu id 29', '29', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:42:14', NULL),
(726, 'New Record inserted On Menu id 30', '30', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:42:22', NULL),
(727, 'New Record inserted On Menu id 31', '31', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:42:54', NULL),
(728, 'New Record inserted On Menu id 32', '32', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 21:43:04', NULL),
(729, 'Record updated On Menu id 9', '9', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:05:35', NULL),
(730, 'Record updated On Menu id 5', '5', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:06:06', NULL),
(731, 'Record updated On Menu id 4', '4', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:06:40', NULL),
(732, 'Record updated On Menu id 8', '8', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:06:52', NULL),
(733, 'Record updated On Menu id 9', '9', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:08:09', NULL),
(734, 'Record updated On  Page List id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:29:36', NULL),
(735, 'Record updated On  Page List id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:30:26', NULL),
(736, 'Record updated On  Page List id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:32:23', NULL),
(737, 'Record updated On Front CMS Setting id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:35:49', NULL),
(738, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:35:49', NULL),
(739, 'Record updated On Front CMS Setting id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:36:24', NULL),
(740, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:36:24', NULL),
(741, 'Record updated On Front CMS Setting id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:37:06', NULL),
(742, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:37:06', NULL),
(743, 'Record updated On Front CMS Setting id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:37:15', NULL),
(744, 'Record updated On settings id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:37:15', NULL),
(745, 'New Record inserted On student edit fields id 1', '1', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:41:54', NULL),
(746, 'New Record inserted On student edit fields id 2', '2', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:41:56', NULL),
(747, 'New Record inserted On student edit fields id 3', '3', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:00', NULL),
(748, 'New Record inserted On student edit fields id 4', '4', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:02', NULL),
(749, 'New Record inserted On student edit fields id 5', '5', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:04', NULL),
(750, 'New Record inserted On student edit fields id 6', '6', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:06', NULL),
(751, 'New Record inserted On student edit fields id 7', '7', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:08', NULL),
(752, 'New Record inserted On student edit fields id 8', '8', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:10', NULL),
(753, 'New Record inserted On student edit fields id 9', '9', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:12', NULL),
(754, 'New Record inserted On student edit fields id 10', '10', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:14', NULL),
(755, 'New Record inserted On student edit fields id 11', '11', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:16', NULL),
(756, 'Record updated On  Page List id 12', '12', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:18', NULL),
(757, 'New Record inserted On student edit fields id 12', '12', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:18', NULL),
(758, 'New Record inserted On student edit fields id 13', '13', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:20', NULL),
(759, 'New Record inserted On student edit fields id 14', '14', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:23', NULL),
(760, 'New Record inserted On student edit fields id 15', '15', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:25', NULL),
(761, 'New Record inserted On student edit fields id 16', '16', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:27', NULL),
(762, 'New Record inserted On student edit fields id 17', '17', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:29', NULL),
(763, 'New Record inserted On student edit fields id 18', '18', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:31', NULL),
(764, 'New Record inserted On student edit fields id 19', '19', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:33', NULL),
(765, 'New Record inserted On student edit fields id 20', '20', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:36', NULL),
(766, 'New Record inserted On student edit fields id 21', '21', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:39', NULL),
(767, 'New Record inserted On student edit fields id 22', '22', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:40', NULL),
(768, 'New Record inserted On student edit fields id 23', '23', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:44', NULL),
(769, 'New Record inserted On student edit fields id 24', '24', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:46', NULL),
(770, 'New Record inserted On student edit fields id 25', '25', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:48', NULL),
(771, 'New Record inserted On student edit fields id 26', '26', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:50', NULL),
(772, 'New Record inserted On student edit fields id 27', '27', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:51', NULL),
(773, 'New Record inserted On student edit fields id 28', '28', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:53', NULL),
(774, 'New Record inserted On student edit fields id 29', '29', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:55', NULL),
(775, 'New Record inserted On student edit fields id 30', '30', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:57', NULL),
(776, 'New Record inserted On student edit fields id 31', '31', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:42:59', NULL),
(777, 'New Record inserted On student edit fields id 32', '32', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:43:01', NULL),
(778, 'New Record inserted On student edit fields id 33', '33', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:43:02', NULL),
(779, 'New Record inserted On student edit fields id 34', '34', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:43:04', NULL),
(780, 'New Record inserted On student edit fields id 35', '35', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:43:06', NULL),
(781, 'New Record inserted On student edit fields id 36', '36', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:43:09', NULL),
(782, 'New Record inserted On student edit fields id 37', '37', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:43:12', NULL),
(783, 'New Record inserted On student edit fields id 38', '38', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:43:13', NULL),
(784, 'New Record inserted On student edit fields id 39', '39', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:43:15', NULL),
(785, 'New Record inserted On student edit fields id 40', '40', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:43:18', NULL),
(786, 'New Record inserted On student edit fields id 41', '41', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:43:20', NULL),
(787, 'Record updated On Menu id 7', '7', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:45:07', NULL),
(788, 'Record updated On  Page List id 12', '12', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:45:56', NULL),
(789, 'Record updated On students id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:49:32', NULL),
(790, 'Record updated On  student session id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:49:32', NULL),
(791, 'Record updated On students id 14', '14', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:50:17', NULL),
(792, 'Record updated On  student session id 14', '14', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:50:17', NULL),
(793, 'Record updated On students id 19', '19', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:50:55', NULL),
(794, 'Record updated On  student session id 19', '19', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:50:55', NULL),
(795, 'Record updated On students id 14', '14', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:51:50', NULL),
(796, 'Record updated On  student session id 14', '14', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:51:50', NULL),
(797, 'Record updated On students id 21', '21', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:52:20', NULL),
(798, 'Record updated On  student session id 21', '21', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:52:20', NULL),
(799, 'Record updated On students id 24', '24', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:53:26', NULL),
(800, 'Record updated On  student session id 24', '24', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:53:26', NULL),
(801, 'Record updated On students id 4', '4', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:53:53', NULL),
(802, 'Record updated On  student session id 4', '4', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:53:53', NULL),
(803, 'Record updated On students id 3', '3', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:54:20', NULL),
(804, 'Record updated On  student session id 3', '3', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:54:20', NULL),
(805, 'Record updated On students id 5', '5', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:54:50', NULL),
(806, 'Record updated On  student session id 5', '5', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:54:50', NULL),
(807, 'Record updated On  Page List id 29', '29', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 22:58:36', NULL),
(808, 'Record updated On  Page List id 19', '19', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:01:13', NULL),
(809, 'Record updated On  Page List id 19', '19', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:01:34', NULL),
(810, 'Record updated On  Page List id 19', '19', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:02:37', NULL),
(811, 'Record updated On students id 5', '5', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:04:14', NULL),
(812, 'Record updated On  student session id 5', '5', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:04:14', NULL),
(813, 'Record updated On  Page List id 23', '23', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:04:16', NULL),
(814, 'Record updated On students id 7', '7', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:06:01', NULL),
(815, 'Record updated On  student session id 7', '7', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:06:01', NULL),
(816, 'Record updated On students id 9', '9', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:06:28', NULL),
(817, 'Record updated On  student session id 9', '9', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:06:28', NULL),
(818, 'Record updated On students id 9', '9', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:06:56', NULL),
(819, 'Record updated On  student session id 9', '9', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:06:56', NULL),
(820, 'Record updated On students id 11', '11', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:07:44', NULL),
(821, 'Record updated On  student session id 11', '11', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:07:44', NULL),
(822, 'Record updated On students id 12', '12', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:08:12', NULL),
(823, 'Record updated On  student session id 12', '12', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:08:12', NULL),
(824, 'Record updated On students id 6', '6', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:08:35', NULL),
(825, 'Record updated On  student session id 6', '6', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:08:35', NULL),
(826, 'Record updated On  Page List id 23', '23', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:08:42', NULL),
(827, 'Record updated On students id 8', '8', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:08:55', NULL),
(828, 'Record updated On  student session id 8', '8', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:08:55', NULL),
(829, 'Record updated On students id 10', '10', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:09:22', NULL),
(830, 'Record updated On  student session id 10', '10', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:09:22', NULL),
(831, 'Record updated On students id 2', '2', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:10:20', NULL),
(832, 'Record updated On  student session id 2', '2', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:10:20', NULL),
(833, 'Record updated On students id 13', '13', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:10:48', NULL),
(834, 'Record updated On  student session id 13', '13', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:10:48', NULL),
(835, 'Record updated On students id 15', '15', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:11:06', NULL),
(836, 'Record updated On  student session id 15', '15', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:11:06', NULL),
(837, 'Record updated On students id 16', '16', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:11:29', NULL),
(838, 'Record updated On  student session id 16', '16', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:11:29', NULL),
(839, 'Record updated On students id 17', '17', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:11:48', NULL),
(840, 'Record updated On  student session id 17', '17', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:11:48', NULL),
(841, 'Record updated On students id 18', '18', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:12:11', NULL),
(842, 'Record updated On  student session id 18', '18', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:12:11', NULL),
(843, 'Record updated On students id 20', '20', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:12:41', NULL),
(844, 'Record updated On  student session id 20', '20', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:12:41', NULL),
(845, 'Record updated On students id 22', '22', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:13:21', NULL),
(846, 'Record updated On  student session id 22', '22', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:13:21', NULL),
(847, 'Record updated On students id 23', '23', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:13:40', NULL),
(848, 'Record updated On  student session id 23', '23', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:13:40', NULL),
(849, 'Record updated On students id 27', '27', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:14:06', NULL),
(850, 'Record updated On  student session id 27', '27', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:14:06', NULL),
(851, 'Record updated On students id 29', '29', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:14:31', NULL),
(852, 'Record updated On  student session id 29', '29', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:14:31', NULL),
(853, 'Record updated On students id 31', '31', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:14:51', NULL),
(854, 'Record updated On  student session id 31', '31', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:14:51', NULL),
(855, 'Record updated On students id 33', '33', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:15:14', NULL),
(856, 'Record updated On  student session id 33', '33', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:15:14', NULL),
(857, 'Record updated On students id 34', '34', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:15:30', NULL),
(858, 'Record updated On  student session id 34', '34', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:15:30', NULL),
(859, 'Record updated On students id 36', '36', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:15:51', NULL),
(860, 'Record updated On  student session id 36', '36', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:15:51', NULL),
(861, 'New Record inserted On event id 18', '18', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:16:07', NULL),
(862, 'Record updated On students id 39', '39', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:16:11', NULL),
(863, 'Record updated On  student session id 39', '39', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:16:11', NULL),
(864, 'Record updated On students id 41', '41', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:16:31', NULL),
(865, 'Record updated On  student session id 41', '41', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:16:31', NULL),
(866, 'Record updated On students id 42', '42', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:17:00', NULL),
(867, 'Record updated On  student session id 42', '42', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:17:00', NULL),
(868, 'Record updated On students id 42', '42', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:17:18', NULL),
(869, 'Record updated On  student session id 42', '42', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:17:18', NULL),
(870, 'Record updated On students id 44', '44', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:17:36', NULL),
(871, 'Record updated On  student session id 44', '44', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:17:36', NULL),
(872, 'Record updated On students id 46', '46', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:17:55', NULL),
(873, 'Record updated On  student session id 46', '46', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:17:55', NULL),
(874, 'Record updated On students id 48', '48', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:18:25', NULL),
(875, 'Record updated On  student session id 48', '48', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:18:25', NULL),
(876, 'Record updated On students id 25', '25', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:19:13', NULL),
(877, 'Record updated On  student session id 25', '25', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:19:13', NULL),
(878, 'Record updated On students id 26', '26', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:19:38', NULL),
(879, 'Record updated On  student session id 26', '26', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:19:38', NULL),
(880, 'Record updated On students id 28', '28', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:20:03', NULL),
(881, 'Record updated On  student session id 28', '28', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:20:03', NULL),
(882, 'Record updated On students id 30', '30', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:20:46', NULL),
(883, 'Record updated On  student session id 30', '30', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:20:46', NULL),
(884, 'Record updated On students id 32', '32', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:21:13', NULL),
(885, 'Record updated On  student session id 32', '32', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:21:13', NULL),
(886, 'Record updated On students id 35', '35', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:21:42', NULL),
(887, 'Record updated On  student session id 35', '35', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:21:42', NULL),
(888, 'Record updated On students id 37', '37', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:22:09', NULL),
(889, 'Record updated On  student session id 37', '37', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:22:09', NULL),
(890, 'Record updated On students id 38', '38', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:22:34', NULL),
(891, 'Record updated On  student session id 38', '38', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:22:34', NULL),
(892, 'Record updated On students id 40', '40', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:23:30', NULL),
(893, 'Record updated On  student session id 40', '40', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:23:30', NULL),
(894, 'Record updated On students id 43', '43', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:24:06', NULL),
(895, 'Record updated On  student session id 43', '43', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:24:06', NULL),
(896, 'Record updated On QR Attendance settings id 1', '1', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:24:14', NULL),
(897, 'Record updated On students id 45', '45', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:24:29', NULL),
(898, 'Record updated On  student session id 45', '45', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:24:29', NULL),
(899, 'Record updated On students id 47', '47', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:24:50', NULL),
(900, 'Record updated On  student session id 47', '47', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:24:50', NULL),
(901, 'New Record inserted On  enquiry id 5', '5', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:25:51', NULL),
(902, 'New Record inserted On  Phone Call Log id 4', '4', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:26:52', NULL),
(903, 'New Record inserted On  Phone Call Log id 5', '5', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:27:15', NULL),
(904, 'New Record inserted On  Complain id 3', '3', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:28:00', NULL),
(905, 'New Record inserted On  Complain id 4', '4', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:28:29', NULL),
(906, 'Record updated On  reference id 5', '5', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:28:50', NULL),
(907, 'Record updated On  reference id 5', '5', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:29:26', NULL),
(908, 'New Record inserted On reference id 6', '6', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:29:37', NULL),
(909, 'New Record inserted On id card id 5', '5', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-26 23:32:19', NULL),
(910, 'Record updated On  Page List id 23', '23', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-27 00:08:49', NULL),
(911, 'Record updated On  Page List id 23', '23', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-27 00:11:22', NULL),
(912, 'Record updated On  Page List id 23', '23', 1, 'Update', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-27 00:17:58', NULL),
(913, 'New Record inserted On Page List id 31', '31', 1, 'Insert', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-27 00:20:32', NULL),
(914, 'Record deleted On Page List id pre-primary-1', 'pre-primary-1', 1, 'Delete', '27.57.181.131', 'Windows 10', 'Chrome 131.0.0.0', '2024-11-27 00:24:16', NULL),
(915, 'Record updated On staff id 6', '6', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:54:12', NULL),
(916, 'New Record inserted On course category id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:56:20', NULL),
(917, 'New Record inserted On course category id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:56:25', NULL),
(918, 'New Record inserted On course category id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:56:29', NULL),
(919, 'New Record inserted On course category id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:56:32', NULL),
(920, 'New Record inserted On course category id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:56:36', NULL),
(921, 'New Record inserted On course category id 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:56:41', NULL),
(922, 'Record updated On staff id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:58:29', NULL),
(923, 'Record updated On staff id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:58:29', NULL),
(924, 'Record updated On staff id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:58:42', NULL),
(925, 'Record updated On staff id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:58:42', NULL),
(926, 'New Record inserted On online courses id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:59:15', NULL),
(927, 'New Record inserted On online course class sections id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:59:15', NULL),
(928, 'New Record inserted On online course class sections id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:59:15', NULL),
(929, 'New Record inserted On online course class sections id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:59:15', NULL),
(930, 'New Record inserted On online course class sections id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:59:15', NULL),
(931, 'Record updated On online c1ourses id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 21:59:15', NULL),
(932, 'Record updated On staff id 7', '7', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:00:42', NULL),
(933, 'Record updated On online c1ourses id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:01:11', NULL),
(934, 'New Record inserted On online course section id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:02:09', NULL),
(935, 'Record updated On online c1ourses id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:02:09', NULL),
(936, 'Record updated On staff id 8', '8', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:04:30', NULL),
(937, 'Record updated On staff id 9', '9', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:07:30', NULL),
(938, 'New Record inserted On online course lesson id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:11:41', NULL),
(939, 'New Record inserted On lesson quiz id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:11:41', NULL),
(940, 'Record updated On online c1ourses id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:11:41', NULL),
(941, 'Record updated On  online course lesson id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:11:41', NULL),
(942, 'Record updated On  online course lesson id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:11:41', NULL),
(943, 'Record updated On online c1ourses id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:12:39', NULL),
(944, 'Record updated On  online course lesson id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:12:39', NULL),
(945, 'Record updated On  online course lesson id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:12:39', NULL),
(946, 'Record updated On  online course lesson id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:12:39', NULL),
(947, 'Record updated On online c1ourses id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:12:54', NULL),
(948, 'Record updated On  online course lesson id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:12:54', NULL),
(949, 'Record updated On  online course lesson id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:12:54', NULL),
(950, 'Record updated On  online course lesson id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:12:54', NULL),
(951, 'New Record inserted On online course lesson id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:15:45', NULL),
(952, 'New Record inserted On lesson quiz id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:15:45', NULL),
(953, 'Record updated On online c1ourses id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:15:45', NULL),
(954, 'Record updated On  online course lesson id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:15:45', NULL),
(955, 'Record updated On  online course lesson id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:15:45', NULL),
(956, 'New Record inserted On online course section id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:18:45', NULL),
(957, 'Record updated On online c1ourses id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:18:45', NULL),
(958, 'Record updated On online course section id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:19:31', NULL),
(959, 'Record updated On online c1ourses id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:19:31', NULL),
(960, 'New Record inserted On course quiz id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:26:26', NULL),
(961, 'New Record inserted On lesson quiz id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:26:26', NULL),
(962, 'Record updated On online c1ourses id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:26:26', NULL),
(963, 'New Record inserted On course quiz question 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:27:30', NULL),
(964, 'Record updated On online c1ourses id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:27:30', NULL),
(965, 'New Record inserted On student behaviour id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:27:55', NULL),
(966, 'Record updated On course quiz question 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:28:22', NULL),
(967, 'New Record inserted On course quiz question 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:28:22', NULL),
(968, 'Record deleted On course quiz question id ', '', 1, 'Delete', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:28:22', NULL),
(969, 'Record updated On online c1ourses id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:28:22', NULL),
(970, 'New Record inserted On student behaviour id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:28:23', NULL),
(971, 'New Record inserted On student behaviour id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:28:46', NULL),
(972, 'New Record inserted On student behaviour id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:29:28', NULL),
(973, 'New Record inserted On student behaviour id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:30:18', NULL),
(974, 'New Record inserted On online courses id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:31:37', NULL),
(975, 'New Record inserted On online course class sections id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:31:37', NULL),
(976, 'New Record inserted On online course class sections id 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:31:37', NULL),
(977, 'New Record inserted On online course class sections id 7', '7', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:31:37', NULL),
(978, 'New Record inserted On online course class sections id 8', '8', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:31:37', NULL),
(979, 'Record updated On online c1ourses id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:31:37', NULL),
(980, 'New Record inserted On online course section id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:32:09', NULL),
(981, 'Record updated On online c1ourses id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:32:09', NULL),
(982, 'New Record inserted On online course lesson id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:33:18', NULL),
(983, 'New Record inserted On lesson quiz id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:33:18', NULL),
(984, 'Record updated On online c1ourses id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:33:18', NULL),
(985, 'Record updated On  online course lesson id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:33:18', NULL),
(986, 'Record updated On  online course lesson id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:33:18', NULL),
(987, 'New Record inserted On gmeet id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:33:29', NULL),
(988, 'New Record inserted On online course lesson id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:34:11', NULL),
(989, 'New Record inserted On lesson quiz id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:34:11', NULL),
(990, 'Record updated On online c1ourses id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:34:11', NULL),
(991, 'Record updated On  online course lesson id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:34:11', NULL),
(992, 'Record updated On  online course lesson id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:34:11', NULL),
(993, 'New Record inserted On course quiz id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:34:50', NULL),
(994, 'New Record inserted On lesson quiz id 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:34:50', NULL),
(995, 'Record updated On online c1ourses id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:34:50', NULL),
(996, 'New Record inserted On gmeet id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:36:05', NULL),
(997, 'New Record inserted On course quiz question 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:36:28', NULL),
(998, 'New Record inserted On course quiz question 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:36:28', NULL),
(999, 'Record updated On online c1ourses id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:36:28', NULL),
(1000, 'Record updated On gmeet id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:36:28', NULL),
(1001, 'New Record inserted On gmeet id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:37:24', NULL),
(1002, 'Record updated On gmeet id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:37:36', NULL),
(1003, 'New Record inserted On gmeet id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:38:14', NULL),
(1004, 'New Record inserted On online courses id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:38:57', NULL),
(1005, 'New Record inserted On online course class sections id 9', '9', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:38:57', NULL),
(1006, 'New Record inserted On online course class sections id 10', '10', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:38:57', NULL),
(1007, 'New Record inserted On online course class sections id 11', '11', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:38:57', NULL),
(1008, 'New Record inserted On online course class sections id 12', '12', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:38:57', NULL),
(1009, 'Record updated On online c1ourses id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:38:57', NULL),
(1010, 'New Record inserted On gmeet id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:39:05', NULL),
(1011, 'New Record inserted On online course section id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:40:00', NULL),
(1012, 'Record updated On online c1ourses id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:40:00', NULL),
(1013, 'New Record inserted On gmeet id 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:40:28', NULL),
(1014, 'New Record inserted On online course lesson id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:41:00', NULL),
(1015, 'New Record inserted On lesson quiz id 7', '7', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:41:00', NULL),
(1016, 'Record updated On online c1ourses id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:41:00', NULL),
(1017, 'Record updated On  online course lesson id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:41:00', NULL),
(1018, 'Record updated On  online course lesson id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:41:00', NULL),
(1019, 'Record updated On Menu id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:41:31', NULL),
(1020, 'New Record inserted On online course lesson id 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:41:51', NULL),
(1021, 'New Record inserted On lesson quiz id 8', '8', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:41:51', NULL),
(1022, 'Record updated On online c1ourses id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:41:51', NULL),
(1023, 'Record updated On  online course lesson id 6', '6', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:41:51', NULL);
INSERT INTO `logs` (`id`, `message`, `record_id`, `user_id`, `action`, `ip_address`, `platform`, `agent`, `time`, `created_at`) VALUES
(1024, 'Record updated On  online course lesson id 6', '6', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:41:51', NULL),
(1025, 'New Record inserted On gmeet id 7', '7', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:42:02', NULL),
(1026, 'New Record inserted On course quiz id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:42:12', NULL),
(1027, 'New Record inserted On lesson quiz id 9', '9', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:42:12', NULL),
(1028, 'Record updated On online c1ourses id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:42:12', NULL),
(1029, 'Record updated On Menu id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:42:16', NULL),
(1030, 'Record updated On Menu id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:43:04', NULL),
(1031, 'New Record inserted On course quiz question 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:43:29', NULL),
(1032, 'New Record inserted On course quiz question 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:43:29', NULL),
(1033, 'Record updated On online c1ourses id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:43:29', NULL),
(1034, 'New Record inserted On gmeet id 8', '8', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:43:33', NULL),
(1035, 'Record updated On online c1ourses id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:44:14', NULL),
(1036, 'Record updated On gmeet id 8', '8', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:44:23', NULL),
(1037, 'New Record inserted On gmeet id 9', '9', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:45:13', NULL),
(1038, 'New Record inserted On online courses id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:45:42', NULL),
(1039, 'New Record inserted On online course class sections id 13', '13', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:45:42', NULL),
(1040, 'New Record inserted On online course class sections id 14', '14', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:45:42', NULL),
(1041, 'New Record inserted On online course class sections id 15', '15', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:45:42', NULL),
(1042, 'New Record inserted On online course class sections id 16', '16', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:45:42', NULL),
(1043, 'Record updated On online c1ourses id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:45:42', NULL),
(1044, 'New Record inserted On online course section id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:46:11', NULL),
(1045, 'Record updated On online c1ourses id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:46:11', NULL),
(1046, 'New Record inserted On gmeet id 10', '10', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:46:38', NULL),
(1047, 'New Record inserted On online course lesson id 7', '7', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:46:45', NULL),
(1048, 'New Record inserted On lesson quiz id 10', '10', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:46:45', NULL),
(1049, 'Record updated On online c1ourses id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:46:45', NULL),
(1050, 'Record updated On  online course lesson id 7', '7', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:46:45', NULL),
(1051, 'Record updated On  online course lesson id 7', '7', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:46:45', NULL),
(1052, 'New Record inserted On online course lesson id 8', '8', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:47:28', NULL),
(1053, 'New Record inserted On lesson quiz id 11', '11', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:47:28', NULL),
(1054, 'Record updated On online c1ourses id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:47:28', NULL),
(1055, 'Record updated On  online course lesson id 8', '8', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:47:28', NULL),
(1056, 'Record updated On  online course lesson id 8', '8', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:47:28', NULL),
(1057, 'New Record inserted On gmeet id 11', '11', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:47:59', NULL),
(1058, 'New Record inserted On course quiz id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:48:01', NULL),
(1059, 'New Record inserted On lesson quiz id 12', '12', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:48:01', NULL),
(1060, 'Record updated On online c1ourses id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:48:01', NULL),
(1061, 'New Record inserted On course quiz question 7', '7', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:49:04', NULL),
(1062, 'New Record inserted On course quiz question 8', '8', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:49:04', NULL),
(1063, 'Record updated On online c1ourses id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:49:04', NULL),
(1064, 'New Record inserted On gmeet id 12', '12', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:50:11', NULL),
(1065, 'Record updated On gmeet id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:51:26', NULL),
(1066, 'Record updated On gmeet id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:51:29', NULL),
(1067, 'New Record inserted On cbse terms id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:59:57', NULL),
(1068, 'New Record inserted On cbse template class sections id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:59:57', NULL),
(1069, 'New Record inserted On cbse template class sections id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:59:57', NULL),
(1070, 'New Record inserted On cbse template class sections id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:59:57', NULL),
(1071, 'New Record inserted On cbse template class sections id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 22:59:57', NULL),
(1072, 'Record updated On cbse terms id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:00:33', NULL),
(1073, 'Record deleted On cbse template class sections id 1', '1', 1, 'Delete', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:00:33', NULL),
(1074, 'New Record inserted On cbse template class sections id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:00:33', NULL),
(1075, 'New Record inserted On cbse template class sections id 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:00:33', NULL),
(1076, 'New Record inserted On cbse template class sections id 7', '7', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:00:33', NULL),
(1077, 'New Record inserted On cbse template class sections id 8', '8', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:00:33', NULL),
(1078, 'New Record inserted On cbse terms id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:01:52', NULL),
(1079, 'New Record inserted On cbse template class sections id 9', '9', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:01:52', NULL),
(1080, 'New Record inserted On cbse template class sections id 10', '10', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:01:52', NULL),
(1081, 'New Record inserted On cbse template class sections id 11', '11', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:01:52', NULL),
(1082, 'New Record inserted On cbse template class sections id 12', '12', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:01:52', NULL),
(1083, 'New Record inserted On cbse terms id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:02:43', NULL),
(1084, 'New Record inserted On cbse template class sections id 13', '13', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:02:43', NULL),
(1085, 'New Record inserted On cbse template class sections id 14', '14', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:02:43', NULL),
(1086, 'New Record inserted On cbse terms id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:03:02', NULL),
(1087, 'New Record inserted On cbse terms id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:03:18', NULL),
(1088, 'New Record inserted On cbse terms id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:03:40', NULL),
(1089, 'New Record inserted On cbse template class sections id 15', '15', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:03:40', NULL),
(1090, 'New Record inserted On cbse template class sections id 16', '16', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:03:40', NULL),
(1091, 'New Record inserted On cbse template class sections id 17', '17', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:03:40', NULL),
(1092, 'New Record inserted On cbse template class sections id 18', '18', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:03:40', NULL),
(1093, 'New Record inserted On cbse terms id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:03:54', NULL),
(1094, 'New Record inserted On cbse terms id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:04:32', NULL),
(1095, 'New Record inserted On cbse template class sections id 19', '19', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:04:32', NULL),
(1096, 'New Record inserted On cbse template class sections id 20', '20', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:04:32', NULL),
(1097, 'New Record inserted On cbse template class sections id 21', '21', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:04:32', NULL),
(1098, 'New Record inserted On cbse template class sections id 22', '22', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:04:32', NULL),
(1099, 'New Record inserted On cbse template class sections id 23', '23', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:04:32', NULL),
(1100, 'New Record inserted On cbse template class sections id 24', '24', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:04:32', NULL),
(1101, 'New Record inserted On cbse template class sections id 25', '25', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:04:32', NULL),
(1102, 'New Record inserted On cbse template class sections id 26', '26', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:04:32', NULL),
(1103, 'New Record inserted On cbse terms id 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:28', NULL),
(1104, 'New Record inserted On cbse template class sections id 27', '27', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:28', NULL),
(1105, 'New Record inserted On cbse template class sections id 28', '28', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:28', NULL),
(1106, 'New Record inserted On cbse template class sections id 29', '29', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:28', NULL),
(1107, 'New Record inserted On cbse template class sections id 30', '30', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:28', NULL),
(1108, 'New Record inserted On cbse template class sections id 31', '31', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:28', NULL),
(1109, 'New Record inserted On cbse template class sections id 32', '32', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:28', NULL),
(1110, 'New Record inserted On cbse template class sections id 33', '33', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:28', NULL),
(1111, 'New Record inserted On cbse template class sections id 34', '34', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:28', NULL),
(1112, 'New Record inserted On cbse template class sections id 35', '35', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:28', NULL),
(1113, 'New Record inserted On cbse template class sections id 36', '36', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:28', NULL),
(1114, 'New Record inserted On cbse template class sections id 37', '37', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:28', NULL),
(1115, 'New Record inserted On cbse template class sections id 38', '38', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:28', NULL),
(1116, 'New Record inserted On cbse observation parameters id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:44', NULL),
(1117, 'New Record inserted On cbse observation parameters id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:48', NULL),
(1118, 'New Record inserted On cbse observation parameters id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:53', NULL),
(1119, 'New Record inserted On cbse observation parameters id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:05:59', NULL),
(1120, 'New Record inserted On cbse observation terms id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:07:41', NULL),
(1121, 'New Record inserted On cbse observation terms id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:07:57', NULL),
(1122, 'New Record inserted On sections id 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:14:06', NULL),
(1123, 'New Record inserted On sections id 7', '7', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:14:11', NULL),
(1124, 'New Record inserted On subject groups id 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:14:30', NULL),
(1125, 'New Record inserted On subject groups id 7', '7', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:14:45', NULL),
(1126, 'New Record inserted On cbse exams id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:16:29', NULL),
(1127, 'New Record inserted On cbse exam class sections id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:16:29', NULL),
(1128, 'New Record inserted On cbse exams id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:19:08', NULL),
(1129, 'New Record inserted On cbse exam class sections id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:19:08', NULL),
(1130, 'New Record inserted On cbse exams id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:20:04', NULL),
(1131, 'New Record inserted On cbse exam class sections id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:20:04', NULL),
(1132, 'New Record inserted On cbse exam class sections id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:20:04', NULL),
(1133, 'New Record inserted On cbse exam class sections id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:20:04', NULL),
(1134, 'New Record inserted On cbse exam class sections id 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:20:04', NULL),
(1135, 'New Record inserted On cbse exams id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:20:45', NULL),
(1136, 'New Record inserted On cbse exam class sections id 7', '7', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:20:45', NULL),
(1137, 'New Record inserted On cbse exam class sections id 8', '8', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:20:45', NULL),
(1138, 'New Record inserted On cbse exam class sections id 9', '9', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:20:45', NULL),
(1139, 'New Record inserted On cbse exam class sections id 10', '10', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:20:45', NULL),
(1140, 'New Record inserted On cbse exams id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:21:35', NULL),
(1141, 'New Record inserted On cbse exam class sections id 11', '11', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:21:35', NULL),
(1142, 'New Record inserted On cbse exam class sections id 12', '12', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:21:35', NULL),
(1143, 'New Record inserted On cbse exam class sections id 13', '13', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:21:35', NULL),
(1144, 'New Record inserted On cbse exam class sections id 14', '14', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:21:35', NULL),
(1145, 'New Record inserted On  online exam id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:57:44', NULL),
(1146, 'New Record inserted On  online exam id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:58:54', NULL),
(1147, 'New Record inserted On  online exam id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-09 23:59:58', NULL),
(1148, 'New Record inserted On  online exam id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:01:14', NULL),
(1149, 'New Record inserted On  online exam id 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:02:02', NULL),
(1150, 'New Record inserted On  questions id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:03:31', NULL),
(1151, 'Record updated On Menu id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:10:52', NULL),
(1152, 'Record updated On Menu id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:12:36', NULL),
(1153, 'Record updated On online c1ourses id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:15:46', NULL),
(1154, 'Record updated On online c1ourses id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:15:46', NULL),
(1155, 'Record updated On online c1ourses id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:15:53', NULL),
(1156, 'Record updated On online c1ourses id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:16:51', NULL),
(1157, 'Record updated On online c1ourses id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:18:04', NULL),
(1158, 'Record updated On  Page List id 23', '23', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:23:42', NULL),
(1159, 'Record updated On  Page List id 19', '19', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:26:03', NULL),
(1160, 'Record updated On  Page List id 19', '19', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:26:46', NULL),
(1161, 'Record updated On  Page List id 19', '19', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:27:22', NULL),
(1162, 'Record updated On  Page List id 19', '19', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:28:30', NULL),
(1163, 'New Record inserted On  visitors  id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 00:46:13', NULL),
(1164, 'New Record inserted On payment settings id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:42:19', NULL),
(1165, 'New Record inserted On online course payment id1', '1', 0, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:43:14', NULL),
(1166, 'Record updated On cbse exams id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:28', NULL),
(1167, 'Record deleted On cbse exam class sections id 5', '5', 1, 'Delete', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:29', NULL),
(1168, 'New Record inserted On cbse exam class sections id 15', '15', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:29', NULL),
(1169, 'New Record inserted On cbse exam class sections id 16', '16', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:29', NULL),
(1170, 'New Record inserted On cbse exam class sections id 17', '17', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:29', NULL),
(1171, 'New Record inserted On cbse exam class sections id 18', '18', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:29', NULL),
(1172, 'Record updated On cbse exams id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:48', NULL),
(1173, 'Record deleted On cbse exam class sections id 4', '4', 1, 'Delete', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:48', NULL),
(1174, 'New Record inserted On cbse exam class sections id 19', '19', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:48', NULL),
(1175, 'New Record inserted On cbse exam class sections id 20', '20', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:48', NULL),
(1176, 'New Record inserted On cbse exam class sections id 21', '21', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:48', NULL),
(1177, 'New Record inserted On cbse exam class sections id 22', '22', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:48', NULL),
(1178, 'Record updated On cbse exams id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:57', NULL),
(1179, 'Record deleted On cbse exam class sections id 3', '3', 1, 'Delete', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:57', NULL),
(1180, 'New Record inserted On cbse exam class sections id 23', '23', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:57', NULL),
(1181, 'New Record inserted On cbse exam class sections id 24', '24', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:57', NULL),
(1182, 'New Record inserted On cbse exam class sections id 25', '25', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:57', NULL),
(1183, 'New Record inserted On cbse exam class sections id 26', '26', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:52:57', NULL),
(1184, 'Record updated On cbse exams id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:53:04', NULL),
(1185, 'Record deleted On cbse exam class sections id 2', '2', 1, 'Delete', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:53:04', NULL),
(1186, 'New Record inserted On cbse exam class sections id 27', '27', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:53:04', NULL),
(1187, 'Record updated On cbse exams id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:53:10', NULL),
(1188, 'Record deleted On cbse exam class sections id 1', '1', 1, 'Delete', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:53:10', NULL),
(1189, 'New Record inserted On cbse exam class sections id 28', '28', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:53:10', NULL),
(1190, 'Record updated On students id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:54:23', NULL),
(1191, 'Record updated On  student session id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:54:23', NULL),
(1192, 'Record updated On students id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:55:37', NULL),
(1193, 'Record updated On  student session id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 20:55:37', NULL),
(1194, 'Record updated On students id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:15:11', NULL),
(1195, 'Record updated On  student session id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:15:11', NULL),
(1196, 'New Record inserted On student apply leave id 1', '1', 0, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:19:04', NULL),
(1197, 'New Record inserted On class teacher id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:20:57', NULL),
(1198, 'New Record inserted On class teacher id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:21:49', NULL),
(1199, 'New Record inserted On class teacher id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:21:49', NULL),
(1200, 'New Record inserted On class teacher id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:21:49', NULL),
(1201, 'New Record inserted On class teacher id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:21:49', NULL),
(1202, 'New Record inserted On class teacher id 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:22:21', NULL),
(1203, 'New Record inserted On class teacher id 7', '7', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:22:21', NULL),
(1204, 'New Record inserted On class teacher id 8', '8', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:22:21', NULL),
(1205, 'New Record inserted On class teacher id 9', '9', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:22:21', NULL),
(1206, 'New Record inserted On class teacher id 10', '10', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:22:45', NULL),
(1207, 'New Record inserted On class teacher id 11', '11', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:22:45', NULL),
(1208, 'New Record inserted On class teacher id 12', '12', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:22:45', NULL),
(1209, 'New Record inserted On class teacher id 13', '13', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:22:45', NULL),
(1210, 'New Record inserted On class teacher id 14', '14', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:22:57', NULL),
(1211, 'New Record inserted On class teacher id 15', '15', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:22:57', NULL),
(1212, 'New Record inserted On class teacher id 16', '16', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:22:57', NULL),
(1213, 'New Record inserted On class teacher id 17', '17', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:22:57', NULL),
(1214, 'Record updated On settings id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:23:06', NULL),
(1215, 'Record updated On settings id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:28:51', NULL),
(1216, 'Record updated On settings id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:47:36', NULL),
(1217, 'Record updated On settings id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 21:48:05', NULL),
(1218, 'Record updated On  online exam id 6', '6', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:05:17', NULL),
(1219, 'Record updated On  online exam id 6', '6', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:05:17', NULL),
(1220, 'Record updated On  online exam id 6', '6', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:05:17', NULL),
(1221, 'Record updated On  online exam id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:05:37', NULL),
(1222, 'Record updated On  online exam id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:05:37', NULL),
(1223, 'Record updated On  online exam id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:05:37', NULL),
(1224, 'Record updated On  online exam id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:05:47', NULL),
(1225, 'Record updated On  online exam id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:05:47', NULL),
(1226, 'Record updated On  online exam id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:05:47', NULL),
(1227, 'Record updated On  online exam id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:07:05', NULL),
(1228, 'Record updated On  online exam id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:07:05', NULL),
(1229, 'Record updated On  online exam id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:07:32', NULL),
(1230, 'Record updated On  online exam id 6', '6', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:07:48', NULL),
(1231, 'Record updated On students id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:22:42', NULL),
(1232, 'Record updated On  student session id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:22:42', NULL),
(1233, 'New Record inserted On class teacher id 18', '18', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:25:52', NULL),
(1234, 'New Record inserted On class teacher id 19', '19', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:26:08', NULL),
(1235, 'New Record inserted On  onlineexam questions id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:28:05', NULL),
(1236, 'Record deleted On  onlineexam questions id 1', '1', 1, 'Delete', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:28:08', NULL),
(1237, 'Record updated On cbse exam students id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:34:13', NULL),
(1238, 'Record updated On cbse exam students id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:34:13', NULL),
(1239, 'Record updated On cbse exam students id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:34:13', NULL),
(1240, 'Record updated On cbse exam students id 11', '11', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:34:13', NULL),
(1241, 'Record updated On cbse exam students id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:34:13', NULL),
(1242, 'Record updated On cbse exam students id 9', '9', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:34:13', NULL),
(1243, 'Record updated On cbse exam students id 12', '12', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:34:13', NULL),
(1244, 'Record updated On cbse exam students id 8', '8', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:34:13', NULL),
(1245, 'Record updated On cbse exam students id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:34:13', NULL),
(1246, 'Record updated On cbse exam students id 7', '7', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:34:13', NULL),
(1247, 'Record updated On cbse exam students id 6', '6', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:34:13', NULL),
(1248, 'Record updated On cbse exam students id 10', '10', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:34:13', NULL),
(1249, 'Record updated On cbse exams id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:37:40', NULL),
(1250, 'Record deleted On cbse exam class sections id 5', '5', 1, 'Delete', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:37:40', NULL),
(1251, 'New Record inserted On cbse exam class sections id 29', '29', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:37:40', NULL),
(1252, 'New Record inserted On cbse exam class sections id 30', '30', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:37:40', NULL),
(1253, 'New Record inserted On cbse exam class sections id 31', '31', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:37:40', NULL),
(1254, 'New Record inserted On cbse exam class sections id 32', '32', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:37:40', NULL),
(1255, 'Record updated On cbse exams id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:02', NULL),
(1256, 'Record updated On cbse exam students id 25', '25', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:02', NULL),
(1257, 'Record updated On cbse exam students id 26', '26', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:02', NULL),
(1258, 'Record updated On cbse exam students id 27', '27', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:02', NULL),
(1259, 'Record updated On cbse exam students id 28', '28', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:02', NULL),
(1260, 'Record updated On cbse exam students id 29', '29', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:02', NULL),
(1261, 'Record updated On cbse exam students id 30', '30', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:02', NULL),
(1262, 'Record updated On cbse exam students id 31', '31', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:02', NULL),
(1263, 'Record updated On cbse exam students id 32', '32', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:02', NULL),
(1264, 'Record updated On cbse exam students id 33', '33', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:02', NULL),
(1265, 'Record updated On cbse exam students id 34', '34', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:02', NULL),
(1266, 'Record updated On cbse exam students id 35', '35', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:02', NULL),
(1267, 'Record updated On cbse exam students id 36', '36', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:02', NULL),
(1268, 'New Record inserted On  subject timetable id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:05', NULL),
(1269, 'New Record inserted On  subject timetable id 0', '0', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:05', NULL),
(1270, 'New Record inserted On  subject timetable id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:50:19', NULL),
(1271, 'New Record inserted On  subject timetable id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:51:31', NULL),
(1272, 'New Record inserted On  subject timetable id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:51:31', NULL),
(1273, 'New Record inserted On  subject timetable id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:51:31', NULL),
(1274, 'New Record inserted On  subject timetable id 7', '7', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:26', NULL),
(1275, 'New Record inserted On  subject timetable id 6', '6', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:26', NULL),
(1276, 'New Record inserted On  subject timetable id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:26', NULL),
(1277, 'Record updated On cbse exams id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:52', NULL),
(1278, 'Record updated On cbse exam students id 37', '37', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:52', NULL),
(1279, 'Record updated On cbse exam students id 38', '38', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:52', NULL),
(1280, 'Record updated On cbse exam students id 39', '39', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:52', NULL),
(1281, 'Record updated On cbse exam students id 40', '40', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:52', NULL),
(1282, 'Record updated On cbse exam students id 41', '41', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:52', NULL),
(1283, 'Record updated On cbse exam students id 42', '42', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:52', NULL),
(1284, 'Record updated On cbse exam students id 43', '43', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:52', NULL),
(1285, 'Record updated On cbse exam students id 44', '44', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:52', NULL),
(1286, 'Record updated On cbse exam students id 45', '45', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:52', NULL),
(1287, 'Record updated On cbse exam students id 46', '46', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:52', NULL),
(1288, 'Record updated On cbse exam students id 47', '47', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:52', NULL),
(1289, 'Record updated On cbse exam students id 48', '48', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:53:52', NULL),
(1290, 'New Record inserted On  subject timetable id 10', '10', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:54:13', NULL),
(1291, 'New Record inserted On  subject timetable id 9', '9', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:54:13', NULL),
(1292, 'New Record inserted On  subject timetable id 8', '8', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:54:13', NULL),
(1293, 'Record updated On cbse exam students id 47', '47', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:05', NULL),
(1294, 'Record updated On cbse exam students id 41', '41', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:05', NULL),
(1295, 'Record updated On cbse exam students id 37', '37', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:05', NULL),
(1296, 'Record updated On cbse exam students id 46', '46', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:05', NULL),
(1297, 'Record updated On cbse exam students id 48', '48', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:05', NULL),
(1298, 'Record updated On cbse exam students id 43', '43', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:05', NULL),
(1299, 'Record updated On cbse exam students id 40', '40', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:05', NULL),
(1300, 'Record updated On cbse exam students id 39', '39', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:05', NULL),
(1301, 'Record updated On cbse exam students id 38', '38', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:05', NULL),
(1302, 'Record updated On cbse exam students id 44', '44', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:05', NULL),
(1303, 'Record updated On cbse exam students id 42', '42', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:05', NULL),
(1304, 'Record updated On cbse exam students id 45', '45', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:05', NULL),
(1305, 'New Record inserted On  subject timetable id 13', '13', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:26', NULL),
(1306, 'New Record inserted On  subject timetable id 12', '12', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:26', NULL),
(1307, 'New Record inserted On  subject timetable id 15', '15', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:49', NULL),
(1308, 'New Record inserted On  subject timetable id 14', '14', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:55:49', NULL),
(1309, 'Record updated On cbse exams id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:57:58', NULL),
(1310, 'Record updated On cbse exam students id 13', '13', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:57:58', NULL),
(1311, 'Record updated On cbse exam students id 14', '14', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:57:58', NULL),
(1312, 'Record updated On cbse exam students id 15', '15', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:57:58', NULL),
(1313, 'Record updated On cbse exam students id 16', '16', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:57:58', NULL),
(1314, 'Record updated On cbse exam students id 17', '17', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:57:58', NULL),
(1315, 'Record updated On cbse exam students id 18', '18', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:57:58', NULL),
(1316, 'Record updated On cbse exam students id 19', '19', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:57:58', NULL),
(1317, 'Record updated On cbse exam students id 20', '20', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:57:58', NULL),
(1318, 'Record updated On cbse exam students id 21', '21', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:57:58', NULL),
(1319, 'Record updated On cbse exam students id 22', '22', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:57:58', NULL),
(1320, 'Record updated On cbse exam students id 23', '23', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:57:58', NULL),
(1321, 'Record updated On cbse exam students id 24', '24', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:57:58', NULL),
(1322, 'Record updated On cbse exam students id 13', '13', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:59:00', NULL),
(1323, 'Record updated On cbse exam students id 14', '14', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:59:00', NULL),
(1324, 'Record updated On cbse exam students id 17', '17', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:59:00', NULL),
(1325, 'Record updated On cbse exam students id 23', '23', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:59:00', NULL),
(1326, 'Record updated On cbse exam students id 16', '16', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:59:00', NULL),
(1327, 'Record updated On cbse exam students id 21', '21', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:59:00', NULL),
(1328, 'Record updated On cbse exam students id 24', '24', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:59:00', NULL),
(1329, 'Record updated On cbse exam students id 20', '20', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:59:00', NULL),
(1330, 'Record updated On cbse exam students id 15', '15', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:59:00', NULL),
(1331, 'Record updated On cbse exam students id 19', '19', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:59:00', NULL),
(1332, 'Record updated On cbse exam students id 18', '18', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:59:00', NULL),
(1333, 'Record updated On cbse exam students id 22', '22', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 22:59:00', NULL),
(1334, 'Record updated On  topic id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:01', NULL),
(1335, 'Record updated On  topic id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:08', NULL),
(1336, 'Record updated On  topic id 3', '3', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:17', NULL),
(1337, 'Record updated On cbse exam students id 25', '25', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:46', NULL),
(1338, 'Record updated On cbse exam students id 26', '26', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:46', NULL),
(1339, 'Record updated On cbse exam students id 29', '29', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:46', NULL),
(1340, 'Record updated On cbse exam students id 35', '35', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:46', NULL),
(1341, 'Record updated On cbse exam students id 28', '28', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:46', NULL),
(1342, 'Record updated On cbse exam students id 33', '33', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:46', NULL),
(1343, 'Record updated On cbse exam students id 36', '36', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:46', NULL),
(1344, 'Record updated On cbse exam students id 32', '32', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:46', NULL),
(1345, 'Record updated On cbse exam students id 27', '27', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:46', NULL),
(1346, 'Record updated On cbse exam students id 31', '31', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:46', NULL);
INSERT INTO `logs` (`id`, `message`, `record_id`, `user_id`, `action`, `ip_address`, `platform`, `agent`, `time`, `created_at`) VALUES
(1347, 'Record updated On cbse exam students id 30', '30', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:46', NULL),
(1348, 'Record updated On cbse exam students id 34', '34', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:00:46', NULL),
(1349, 'New Record inserted On lesson id 20', '20', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:01:38', NULL),
(1350, 'New Record inserted On lesson id 21', '21', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:01:56', NULL),
(1351, 'New Record inserted On topic id 4', '4', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:03:00', NULL),
(1352, 'New Record inserted On topic id 5', '5', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:03:19', NULL),
(1353, 'Record updated On  topic id 4', '4', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:03:42', NULL),
(1354, 'Record updated On  topic id 5', '5', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:04:10', NULL),
(1355, 'Record updated On Front CMS Setting id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:07:56', NULL),
(1356, 'Record updated On settings id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:07:56', NULL),
(1357, 'Record updated On Front CMS Setting id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:08:35', NULL),
(1358, 'Record updated On settings id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:08:35', NULL),
(1359, 'Record updated On Front CMS Setting id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:08:49', NULL),
(1360, 'Record updated On settings id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:08:49', NULL),
(1361, 'New Record inserted On exam group exams name id 1', '1', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:19:31', NULL),
(1362, 'New Record inserted On exam group exams name id 2', '2', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:21:02', NULL),
(1363, 'Record updated On  exam group exams name id 2', '2', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:24:38', NULL),
(1364, 'New Record inserted On exam group exams name id 3', '3', 1, 'Insert', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:25:30', NULL),
(1365, 'Record updated On  student apply leave id 1', '1', 1, 'Update', '110.227.244.250', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-10 23:30:39', NULL),
(1366, 'Record updated On  enquiry id 5', '5', 1, 'Update', '49.36.90.42', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-15 00:48:05', NULL),
(1367, 'Record updated On Front CMS Setting id 1', '1', 1, 'Update', '223.235.175.65', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-15 02:01:17', NULL),
(1368, 'Record updated On settings id 1', '1', 1, 'Update', '223.235.175.65', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-15 02:01:17', NULL),
(1369, 'Record updated On Front CMS Setting id 1', '1', 1, 'Update', '223.235.175.65', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-15 02:05:25', NULL),
(1370, 'Record updated On settings id 1', '1', 1, 'Update', '223.235.175.65', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-15 02:05:25', NULL),
(1371, 'Record updated On Front CMS Setting id 1', '1', 1, 'Update', '223.235.175.65', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-15 02:05:28', NULL),
(1372, 'Record updated On settings id 1', '1', 1, 'Update', '223.235.175.65', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-15 02:05:28', NULL),
(1373, 'Record updated On Front CMS Setting id 1', '1', 1, 'Update', '223.235.175.65', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-15 02:14:51', NULL),
(1374, 'Record updated On settings id 1', '1', 1, 'Update', '223.235.175.65', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-15 02:14:51', NULL),
(1375, 'Record updated On settings id 1', '1', 1, 'Update', '182.65.198.203', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-19 21:58:45', NULL),
(1376, 'Record updated On online c1ourses id 4', '4', 0, 'Update', '49.36.90.42', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-20 22:35:29', NULL),
(1377, 'Record updated On students id 1', '1', 1, 'Update', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-21 20:41:15', NULL),
(1378, 'Record updated On  student session id 1', '1', 1, 'Update', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-21 20:41:15', NULL),
(1379, 'New Record inserted On gmeet id 13', '13', 1, 'Insert', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-21 20:47:08', NULL),
(1380, 'Record updated On settings id 1', '1', 1, 'Update', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 00:19:45', NULL),
(1381, 'Record deleted On  Multi Branch   id 1', '1', 1, 'Delete', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 01:57:45', NULL),
(1382, 'Record deleted On  Multi Branch   id 2', '2', 1, 'Delete', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 01:57:48', NULL),
(1383, 'Record deleted On  Multi Branch   id 3', '3', 1, 'Delete', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 01:57:50', NULL),
(1384, 'Record deleted On  Multi Branch   id 4', '4', 1, 'Delete', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 01:57:52', NULL),
(1385, 'Record deleted On  Multi Branch   id 5', '5', 1, 'Delete', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 01:57:54', NULL),
(1386, 'Record deleted On  Multi Branch   id 6', '6', 1, 'Delete', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 01:57:56', NULL),
(1387, 'Record deleted On  Multi Branch   id 7', '7', 1, 'Delete', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 01:57:59', NULL),
(1388, 'Record deleted On  Multi Branch   id 8', '8', 1, 'Delete', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 02:04:34', NULL),
(1389, 'Record deleted On  Multi Branch   id 10', '10', 1, 'Delete', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 02:05:43', NULL),
(1390, 'Record deleted On  Multi Branch   id 9', '9', 1, 'Delete', '117.98.158.130', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 02:05:46', NULL),
(1391, 'New Record inserted On Page List id 32', '32', 1, 'Insert', '122.175.87.141', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 22:25:07', NULL),
(1392, 'New Record inserted On Menu id 33', '33', 1, 'Insert', '122.175.87.141', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 22:25:29', NULL),
(1393, 'Record updated On  Page List id 32', '32', 1, 'Update', '122.175.87.141', 'Windows 10', 'Chrome 131.0.0.0', '2024-12-24 22:34:01', NULL),
(1394, 'New Record inserted On student incidents id 1', '1', 1, 'Insert', '2401:4900:1f3e:647:bdf0:4865:80f0:7dd1', 'Windows 10', 'Chrome 133.0.0.0', '2025-02-11 19:02:39', NULL),
(1395, 'New Record inserted On student incidents id 2', '2', 1, 'Insert', '2401:4900:1f3e:647:bdf0:4865:80f0:7dd1', 'Windows 10', 'Chrome 133.0.0.0', '2025-02-11 19:02:39', NULL),
(1396, 'New Record inserted On student incidents id 3', '3', 1, 'Insert', '2401:4900:1f3e:647:bdf0:4865:80f0:7dd1', 'Windows 10', 'Chrome 133.0.0.0', '2025-02-11 19:02:40', NULL),
(1397, 'New Record inserted On student incidents id 4', '4', 1, 'Insert', '2401:4900:1f3e:647:bdf0:4865:80f0:7dd1', 'Windows 10', 'Chrome 133.0.0.0', '2025-02-11 19:02:40', NULL),
(1398, 'New Record inserted On student incidents id 5', '5', 1, 'Insert', '2401:4900:1f3e:647:bdf0:4865:80f0:7dd1', 'Windows 10', 'Chrome 133.0.0.0', '2025-02-11 19:02:41', NULL),
(1399, 'Record updated On cbse terms id 1', '1', 1, 'Update', '2401:4900:1f3e:647:bdf0:4865:80f0:7dd1', 'Windows 10', 'Chrome 133.0.0.0', '2025-02-11 19:05:35', NULL),
(1400, 'Record deleted On cbse template term exams and cbse template terms where cbse template id 1', '1', 1, 'Delete', '2401:4900:1f3e:647:bdf0:4865:80f0:7dd1', 'Windows 10', 'Chrome 133.0.0.0', '2025-02-11 19:05:35', NULL),
(1401, 'New Record inserted On cbse template terms id 1', '1', 1, 'Insert', '2401:4900:1f3e:647:bdf0:4865:80f0:7dd1', 'Windows 10', 'Chrome 133.0.0.0', '2025-02-11 19:05:35', NULL),
(1402, 'New Record inserted On cbse template term exams id 1', '1', 1, 'Insert', '2401:4900:1f3e:647:bdf0:4865:80f0:7dd1', 'Windows 10', 'Chrome 133.0.0.0', '2025-02-11 19:05:35', NULL),
(1403, 'New Record inserted On cbse template term exams id 2', '2', 1, 'Insert', '2401:4900:1f3e:647:bdf0:4865:80f0:7dd1', 'Windows 10', 'Chrome 133.0.0.0', '2025-02-11 19:05:35', NULL),
(1404, 'New Record inserted On cbse template term exams id 3', '3', 1, 'Insert', '2401:4900:1f3e:647:bdf0:4865:80f0:7dd1', 'Windows 10', 'Chrome 133.0.0.0', '2025-02-11 19:05:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mark_divisions`
--

CREATE TABLE `mark_divisions` (
  `id` int(11) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `percentage_from` float(10,2) DEFAULT NULL,
  `percentage_to` float(10,2) DEFAULT NULL,
  `is_active` int(10) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `mark_divisions`
--

INSERT INTO `mark_divisions` (`id`, `name`, `percentage_from`, `percentage_to`, `is_active`, `created_at`) VALUES
(1, 'First', 100.00, 60.00, 1, '2024-11-25 10:55:42'),
(2, 'Second', 345.00, 65.00, 1, '2024-11-25 10:55:57'),
(3, 'Third', 578.00, 89.00, 1, '2024-11-25 10:56:04');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `template_id` varchar(100) DEFAULT NULL,
  `email_template_id` int(11) DEFAULT NULL,
  `sms_template_id` int(11) DEFAULT NULL,
  `send_through` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `send_mail` varchar(10) DEFAULT '0',
  `send_sms` varchar(10) DEFAULT '0',
  `is_group` varchar(10) DEFAULT '0',
  `is_individual` varchar(10) DEFAULT '0',
  `is_class` int(10) NOT NULL DEFAULT 0,
  `is_schedule` int(1) NOT NULL,
  `sent` int(11) DEFAULT NULL,
  `schedule_date_time` datetime DEFAULT NULL,
  `group_list` text DEFAULT NULL,
  `user_list` text DEFAULT NULL,
  `schedule_class` int(11) DEFAULT NULL,
  `schedule_section` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `title`, `template_id`, `email_template_id`, `sms_template_id`, `send_through`, `message`, `send_mail`, `send_sms`, `is_group`, `is_individual`, `is_class`, `is_schedule`, `sent`, `schedule_date_time`, `group_list`, `user_list`, `schedule_class`, `schedule_section`, `created_at`, `updated_at`) VALUES
(1, 'National Republic Day', '', NULL, 1, '[\"sms\"]', 'India celebrated its 73rd National Republic Day on the 26th of January, 2022. The constitution of India was adopted on the 26th of November, 1949 with Dr. B. R. Ambedkar as the Chairman of the Drafting Committee.', '0', '1', '1', '0', 0, 0, NULL, NULL, '[\"student\"]', '[{\"user_id\":\"2\",\"email\":\"vish@gmailc.com\",\"mobileno\":\"23456534\",\"app_key\":null,\"role\":\"student\"},{\"user_id\":\"1\",\"email\":\"Jinal@gmail.com\",\"mobileno\":\"987654567\",\"app_key\":null,\"role\":\"student\"}]', NULL, NULL, '2024-11-26 00:16:52', NULL),
(2, 'Sports Day Events', '', NULL, 2, '[\"sms\"]', 'Games that are played on school sports days can be wide and varied. They can include straightforward sprints and longer races for all age groups as well as egg and spoon races.', '0', '1', '1', '0', 0, 0, NULL, NULL, '[\"2\",\"6\"]', '[{\"user_id\":\"2\",\"email\":\"gita@gmai.com\",\"mobileno\":\"2345678\",\"app_key\":\"\",\"role\":\"staff\"},{\"user_id\":\"3\",\"email\":\"phynes@gmail.com\",\"mobileno\":\"\",\"app_key\":\"\",\"role\":\"staff\"},{\"user_id\":\"5\",\"email\":\"Alberti12@gmail.com\",\"mobileno\":\"\",\"app_key\":\"\",\"role\":\"staff\"}]', NULL, NULL, '2024-11-26 00:17:41', NULL),
(3, 'Sports Day Events', NULL, 1, NULL, NULL, '<p>Games that are played on school sports days can be wide and varied. They can include straightforward sprints and longer races for all age groups as well as egg and spoon races.&nbsp;</p>\r\n', '1', '0', '1', '0', 0, 0, NULL, NULL, '[\"student\",\"parent\"]', '[{\"user_id\":\"1\",\"email\":\"Jinal@gmail.com\",\"mobileno\":\"987654567\",\"role\":\"student\"},{\"user_id\":\"2\",\"email\":\"vish@gmailc.com\",\"mobileno\":\"23456534\",\"role\":\"student\"},{\"user_id\":\"1\",\"email\":\"\",\"mobileno\":\"9876545678\",\"role\":\"parent\"},{\"user_id\":\"2\",\"email\":\"\",\"mobileno\":\"345678765\",\"role\":\"parent\"}]', NULL, NULL, '2024-11-26 00:18:00', NULL),
(4, 'Annual Day Celebration', NULL, 3, NULL, NULL, '<p>A day in School &ndash; In this theme the program can showcase, what all goes in school. The ringing of bell, the class, the love of teachers, happy-go-lucky punishments, all pranks, PTM, sports etc.</p>\r\n', '1', '0', '1', '0', 0, 0, NULL, NULL, '[\"1\",\"3\",\"4\"]', '[]', NULL, NULL, '2024-11-26 00:18:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `version` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `multi_branch`
--

CREATE TABLE `multi_branch` (
  `id` int(11) NOT NULL,
  `branch_name` varchar(200) DEFAULT NULL,
  `branch_url` varchar(500) NOT NULL,
  `hostname` varchar(200) DEFAULT NULL,
  `username` varchar(200) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `database_name` varchar(200) DEFAULT NULL,
  `directory_path` varchar(500) DEFAULT NULL,
  `is_verified` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `multi_branch`
--

INSERT INTO `multi_branch` (`id`, `branch_name`, `branch_url`, `hostname`, `username`, `password`, `database_name`, `directory_path`, `is_verified`, `created_at`) VALUES
(11, 'Mount Carmel School', '{{BASEURL}}', '132.148.183.109', 'school-new-1', 'school-new-1', 'school-new-1', NULL, 1, '2025-02-11 12:40:27');

-- --------------------------------------------------------

--
-- Table structure for table `notification_roles`
--

CREATE TABLE `notification_roles` (
  `id` int(11) NOT NULL,
  `send_notification_id` int(11) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `is_active` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `notification_roles`
--

INSERT INTO `notification_roles` (`id`, `send_notification_id`, `role_id`, `is_active`, `created_at`) VALUES
(1, 1, 1, 0, '2024-11-25 11:32:13'),
(2, 1, 2, 0, '2024-11-25 11:32:13'),
(3, 1, 3, 0, '2024-11-25 11:32:13'),
(4, 1, 4, 0, '2024-11-25 11:32:13'),
(5, 1, 6, 0, '2024-11-25 11:32:13'),
(6, 1, 7, 0, '2024-11-25 11:32:13'),
(7, 2, 1, 0, '2024-11-25 11:33:09'),
(8, 2, 2, 0, '2024-11-25 11:33:09'),
(9, 2, 7, 0, '2024-11-25 11:33:09'),
(10, 3, 1, 0, '2024-11-25 11:33:50'),
(11, 3, 2, 0, '2024-11-25 11:33:50'),
(12, 3, 3, 0, '2024-11-25 11:33:50'),
(13, 3, 4, 0, '2024-11-25 11:33:50'),
(14, 3, 6, 0, '2024-11-25 11:33:50'),
(15, 3, 7, 0, '2024-11-25 11:33:50'),
(16, 4, 1, 0, '2024-11-25 11:34:31'),
(17, 4, 2, 0, '2024-11-25 11:34:31'),
(18, 4, 3, 0, '2024-11-25 11:34:31'),
(19, 4, 4, 0, '2024-11-25 11:34:31'),
(20, 4, 6, 0, '2024-11-25 11:34:31'),
(21, 4, 7, 0, '2024-11-25 11:34:31'),
(22, 5, 1, 0, '2024-11-25 11:35:08'),
(23, 5, 2, 0, '2024-11-25 11:35:08'),
(24, 5, 7, 0, '2024-11-25 11:35:08');

-- --------------------------------------------------------

--
-- Table structure for table `notification_setting`
--

CREATE TABLE `notification_setting` (
  `id` int(11) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `is_mail` varchar(10) DEFAULT '0',
  `is_sms` varchar(10) DEFAULT '0',
  `is_notification` int(11) NOT NULL DEFAULT 0,
  `display_notification` int(11) NOT NULL DEFAULT 0,
  `display_sms` int(11) NOT NULL DEFAULT 1,
  `is_student_recipient` int(1) DEFAULT NULL,
  `is_guardian_recipient` int(1) DEFAULT NULL,
  `is_staff_recipient` int(1) DEFAULT NULL,
  `display_student_recipient` int(1) DEFAULT NULL,
  `display_guardian_recipient` int(1) DEFAULT NULL,
  `display_staff_recipient` int(1) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `template_id` varchar(100) NOT NULL,
  `template` longtext NOT NULL,
  `variables` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `notification_setting`
--

INSERT INTO `notification_setting` (`id`, `type`, `is_mail`, `is_sms`, `is_notification`, `display_notification`, `display_sms`, `is_student_recipient`, `is_guardian_recipient`, `is_staff_recipient`, `display_student_recipient`, `display_guardian_recipient`, `display_staff_recipient`, `subject`, `template_id`, `template`, `variables`, `created_at`) VALUES
(1, 'student_admission', '1', '0', 0, 0, 1, 1, 1, 0, 1, 1, NULL, 'Student Admission', '', 'Dear {{student_name}} your admission is confirm in Class: {{class}} Section:  {{section}} for Session: {{current_session_name}} for more \r\ndetail\r\n contact\r\n System\r\n Admin\r\n {{class}} {{section}} {{admission_no}} {{roll_no}} {{admission_date}} {{mobileno}} {{email}} {{dob}} {{guardian_name}} {{guardian_relation}} {{guardian_phone}} {{father_name}} {{father_phone}} {{blood_group}} {{mother_name}} {{gender}} {{guardian_email}}', '{{student_name}} {{class}}  {{section}}  {{admission_no}}  {{roll_no}}  {{admission_date}}   {{mobileno}}  {{email}}  {{dob}}  {{guardian_name}}  {{guardian_relation}}  {{guardian_phone}}  {{father_name}}  {{father_phone}}  {{blood_group}}  {{mother_name}}  {{gender}} {{guardian_email}} {{current_session_name}} ', '2024-11-25 07:19:31'),
(2, 'exam_result', '1', '0', 0, 1, 1, 1, 0, 0, 1, 1, NULL, 'Exam Result', '', 'Dear {{student_name}} - {{exam_roll_no}}, your {{exam}} result has been published.', '{{student_name}} {{exam_roll_no}} {{exam}}', '2024-11-25 07:19:31'),
(3, 'fee_submission', '1', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Fee Submission', '', 'Dear parents, we have received Fees Amount {{fee_amount}} for  {{student_name}}  by Your School Name \r\n{{class}} {{section}} {{fine_type}} {{fine_percentage}} {{fine_amount}} {{fee_group_name}} {{type}} {{code}} {{email}} {{contact_no}} {{invoice_id}} {{sub_invoice_id}} {{due_date}} {{amount}} {{fee_amount}}', '{{student_name}} {{class}} {{section}} {{fine_type}} {{fine_percentage}} {{fine_amount}} {{fee_group_name}} {{type}} {{code}} {{email}} {{contact_no}} {{invoice_id}} {{sub_invoice_id}} {{due_date}} {{amount}} {{fee_amount}}', '2024-11-25 07:19:31'),
(4, 'absent_attendence', '1', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Absent Attendence', '', 'Absent Notice :{{student_name}}  was absent on date {{date}} in period {{subject_name}} {{subject_code}} {{subject_type}} from Your School Name', '{{student_name}} {{mobileno}} {{email}} {{father_name}} {{father_phone}} {{father_occupation}} {{mother_name}} {{mother_phone}} {{guardian_name}} {{guardian_phone}} {{guardian_occupation}} {{guardian_email}} {{date}} {{current_session_name}} {{time_from}} {{time_to}} {{subject_name}} {{subject_code}} {{subject_type}}  ', '2024-11-25 07:19:31'),
(6, 'homework', '1', '0', 0, 1, 1, 1, 0, 0, 1, 1, NULL, 'Homework', '', 'New Homework has been created for \r\n{{student_name}} at\r\n\r\n\r\n\r\n{{homework_date}} for the class {{class}} {{section}} {{subject}}. kindly submit your\r\n\r\n\r\n homework before {{submit_date}} .Thank you', '{{homework_date}} {{submit_date}} {{class}} {{section}} {{subject}} {{student_name}} {{admission_no}} ', '2024-11-25 07:19:31'),
(7, 'fees_reminder', '1', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Fees Reminder', '', 'Dear parents, please pay fee amount Rs.{{due_amount}} of {{fee_type}} before {{due_date}} for {{student_name}}  from smart school (ignore if you already paid)', '{{fee_type}}{{fee_code}}{{due_date}}{{student_name}}{{school_name}}{{fee_amount}}{{due_amount}}{{deposit_amount}} ', '2024-11-25 07:19:31'),
(8, 'forgot_password', '1', '0', 0, 0, 0, 1, 1, 1, 1, 1, 1, 'Forgot Password', '', 'Dear  {{name}} , \r\n    Recently a request was submitted to reset password for your account. If you didn\'t make the request, just ignore this email. Otherwise you can reset your password using this link <a href=\'{{resetPassLink}}\'>Click here to reset your password</a>,\r\nif you\'re having trouble clicking the password reset button, copy and paste the URL below into your web browser. your username {{username}}\r\n{{resetPassLink}}\r\n Regards,\r\n {{school_name}}', '{{school_name}}{{name}}{{username}}{{resetPassLink}} ', '2022-12-28 09:52:24'),
(9, 'online_examination_publish_exam', '1', '0', 0, 1, 1, 1, 0, 0, 1, 1, NULL, 'Online Examination Publish Exam', '', 'A new exam {{exam_title}} has been created for  duration: {{time_duration}} min, which will be available from:  {{exam_from}} to  {{exam_to}}.', '{{exam_title}} {{exam_from}} {{exam_to}} {{time_duration}} {{attempt}} {{passing_percentage}}', '2024-11-25 07:19:31'),
(10, 'online_examination_publish_result', '1', '0', 0, 1, 1, 1, 0, 0, 1, 1, NULL, 'Online Examination Publish Result', '', 'Exam {{exam_title}} result has been declared which was conducted between  {{exam_from}} to   {{exam_to}}, for more details, please check your student portal.', '{{exam_title}} {{exam_from}} {{exam_to}} {{time_duration}} {{attempt}} {{passing_percentage}}', '2024-11-25 07:19:31'),
(11, 'online_admission_form_submission', '1', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Online Admission Form Submission', '', 'Dear {{firstname}}  {{lastname}} your online admission form is Submitted successfully  on date {{date}}. Your Reference number is {{reference_no}}. Please remember your reference number for further process.', ' {{firstname}} {{lastname}} {{date}} {{reference_no}}', '2024-11-25 07:19:31'),
(12, 'online_admission_fees_submission', '0', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Online Admission Fees Submission', '', 'Dear {{firstname}}  {{lastname}} your online admission form is Submitted successfully and the payment of {{paid_amount}} has recieved successfully on date {{date}}. Your Reference number is {{reference_no}}. Please remember your reference number for further process.', ' {{firstname}} {{lastname}} {{date}} {{paid_amount}} {{reference_no}}', '2024-11-25 07:19:31'),
(13, 'student_login_credential', '1', '1', 0, 0, 1, 1, 1, 0, 1, 1, NULL, 'Student Login Credential', '1707163291685208209', 'Hello {{display_name}} your login details for Url: {{url}} Username: {{username}}  Password: {{password}} admission No: {{admission_no}}', '{{url}} {{display_name}} {{username}} {{password}} {{admission_no}}', '2022-08-06 05:34:41'),
(14, 'staff_login_credential', '1', '1', 0, 0, 1, 0, 0, 1, NULL, NULL, 1, 'Staff Login Credential', '1707163291685208209', 'Hello {{first_name}} {{last_name}} your login details for Url: {{url}} Username: {{username}}  Password: {{password}} Employee ID: {{employee_id}}', '{{url}} {{first_name}} {{last_name}} {{username}} {{password}} {{employee_id}}', '2021-12-23 11:59:13'),
(15, 'fee_processing', '1', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Fee processing', '1707163291301326898', 'Dear parents, we have received Fees Amount {{fee_amount}} for  {{student_name}}  by Your School Name \r\n{{class}} {{section}} {{email}} {{contact_no}}\r\n\r\n{{student_name}} {{class}} {{section}} {{email}} {{contact_no}} transaction_id :{{transaction_id}} {{fee_amount}}', '{{student_name}} {{class}} {{section}} {{email}} {{contact_no}} {{transaction_id}} {{fee_amount}}', '2024-11-25 07:19:31'),
(16, 'online_admission_fees_processing', '1', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Online Admission Fees Processing', '', 'Dear {{firstname}}  {{lastname}} your online admission form is Submitted successfully and the payment of {{paid_amount}} has processing on date {{date}}. Your Reference number is {{reference_no}} and your transaction id {{transaction_id}}. Please remember your reference number for further process.', ' {{firstname}} {{lastname}} {{date}} {{paid_amount}} {{reference_no}} {{transaction_id}}', '2024-11-25 07:19:31'),
(17, 'student_apply_leave', '1', '1', 0, 0, 1, 0, 1, 1, NULL, 1, 1, 'Student Apply Leave ( {{student_name}} - {{admission_no}} )', '', 'My Name is {{student_name}} Class {{class}} section {{section}}. I have to apply leave on {{apply_date}}and from {{from_date}} to {{to_date}}. {{message}} please provide.', '{{message}} {{apply_date}} {{from_date}} {{to_date}} {{student_name}} {{class}} {{section}}', '2022-03-12 11:58:37'),
(18, 'email_pdf_exam_marksheet', '1', '0', 0, 0, 0, 1, 1, 0, 1, 1, NULL, 'Email PDF Exam Marksheet ( {{student_name}} - {{admission_no}} )', '', 'Dear {{student_name}} ({{admission_no}}) {{class}} Section {{section}}. We have mailed you the marksheet of Exam {{exam}} Roll no.{{roll_no}}', '{{student_name}} {{class}}  {{section}}  {{admission_no}}  {{roll_no}} {{exam}} {{admit_card_roll_no}} ', '2022-03-12 12:24:42'),
(19, 'gmeet_online_classes', '1', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Gmeet Live Classes', '', 'Dear student, your live class {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute, please do not share the link to any body.', '{{title}} {{date}} {{duration}}', '2024-11-25 07:19:31'),
(20, 'gmeet_online_meeting', '1', '1', 0, 0, 1, 0, 0, 1, NULL, NULL, 1, 'Gmeet Live Meeting', '', 'Dear staff, your live meeting {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute, please do not share the link to any body.', '{{title}} {{date}} {{duration}} {{employee_id}} {{department}} {{designation}} {{name}} {{contact_no}} {{email}}', '2022-07-13 12:25:28'),
(21, 'gmeet_online_classes_start', '1', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Gmeet  Live Classes Start', '', 'Dear student, your live class {{title}} has been started  for the duration of {{duration}} minute.', '{{title}} {{date}} {{duration}}', '2024-11-25 07:19:31'),
(22, 'gmeet_online_meeting_start', '1', '1', 0, 0, 1, 0, 0, 1, NULL, NULL, 1, 'Gmeet Live Meeting Start', '', 'Dear {{name}},  your live meeting {{title}} has been started  for the duration of {{duration}} minute.', '{{title}} {{date}} {{duration}} {{employee_id}} {{department}} {{designation}} {{name}} {{contact_no}} {{email}}', '2022-07-13 12:25:28'),
(23, 'online_classes', '1', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Zoom Online Classes', '', 'Dear student, your live class {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute, please do not share the link to any body.', '{{title}} {{date}} {{duration}}', '2024-11-25 07:19:31'),
(24, 'online_meeting', '1', '1', 0, 0, 1, 0, 0, 1, NULL, NULL, 1, 'Zoom Online Meeting', '', 'Dear staff, your live meeting {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute, please do not share the link to any body.', '{{title}} {{date}} {{duration}} {{employee_id}} {{department}} {{designation}} {{name}} {{contact_no}} {{email}}', '2024-11-25 07:19:31'),
(25, 'zoom_online_classes_start', '1', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Zoom Online  Classes Start', '', 'Dear student, your live class {{title}} has been started  for the duration of {{duration}} minute.', '{{title}} {{date}} {{duration}}', '2024-11-25 07:19:31'),
(26, 'zoom_online_meeting_start', '1', '1', 0, 0, 1, 0, 0, 1, NULL, NULL, 1, 'Zoom Live Meeting Start', '', 'Dear {{name}},  your live meeting {{title}}  has been started  for the duration of {{duration}} minute.', '{{title}} {{date}} {{duration}} {{employee_id}} {{department}} {{designation}} {{name}} {{contact_no}} {{email}}', '2024-11-25 07:19:31'),
(27, 'online_course_publish', '1', '1', 0, 1, 1, 1, 0, 0, 1, 1, NULL, 'Online Course Publish', '', 'Dear student, a new online course {{title}} and price {{price}} with discount {{discount}}% for {{class}} {{section}} is {{paid_free}} now available and assign to {{assign_teacher}}.', '{{title}} {{class}} {{section}} {{price}} {{discount}} {{paid_free}} {{assign_teacher}}\r\n ', '2024-11-25 07:19:31'),
(28, 'online_course_purchase', '1', '1', 0, 1, 1, 1, 0, 0, 1, 1, NULL, 'Online Course Purchase', '', 'Thanks for purchasing course {{title}} amount {{price}} purchase date {{purchase_date}} class {{class}} section {{section}} and assign for {{assign_teacher}}  discount {{discount}}%', '{{title}} {{class}} {{section}} {{price}} {{discount}}  \r\n{{purchase_date}}', '2024-11-25 07:19:31'),
(29, 'online_course_purchase_for_guest_user', '1', '0', 0, 0, 1, 1, 0, 0, 1, NULL, NULL, 'Online Course Purchase For Guest', '', 'Thanks for purchasing course {{title}} discount {{discount}} amount {{price}} purchase date {{purchase_date}}', '{{title}} {{price}} {{discount}} {{purchase_date}}', '2024-11-25 07:19:31'),
(30, 'online_course_guest_user_sign_up', '1', '0', 0, 0, 0, 1, 0, 0, 1, NULL, NULL, 'Online Course Guest User Sign Up', '', 'Dear {{guest_user_name}} you have successfully sign up with Email: {{email}} Url {{url}}', '{{guest_user_name}} {{email}} {{url}}', '2024-11-25 07:19:31'),
(31, 'behaviour_incident_assigned', '1', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Behaviour Incident Assigned', '', 'A new {{incident_title}}  behaviour incident with {{incident_point}} point is assigned on you. {{student_name}} {{class}} {{section}} {{admission_no}} {{mobileno}} {{email}} {{guardian_name}} {{guardian_phone}} {{guardian_email}}', '{{incident_title}} {{incident_point}} {{student_name}} {{class}} {{section}} {{admission_no}} {{mobileno}} {{email}} {{guardian_name}} {{guardian_phone}} {{guardian_email}}', '2024-11-25 07:19:31'),
(32, 'cbse_email_pdf_exam_marksheet', '1', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'CBSE Exam Marksheet PDF ( {{student_name}} - {{admission_no}} )', '', 'Dear {{student_name}} ({{admission_no}}) {{class}} Section {{section}}. We have mailed you the marksheet with Roll no.{{roll_no}}', '{{student_name}} {{class}} {{section}} {{admission_no}} {{roll_no}}', '2024-11-25 07:19:31'),
(33, 'cbse_exam_result', '1', '0', 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'CBSE Exam Result', '', 'Dear {{student_name}} - {{roll_no}}, your {{exam}} result has been published.', '{{student_name}} {{roll_no}} {{exam}}', '2024-11-25 07:19:31');

-- --------------------------------------------------------

--
-- Table structure for table `offline_fees_payments`
--

CREATE TABLE `offline_fees_payments` (
  `id` int(11) NOT NULL,
  `invoice_id` varchar(50) DEFAULT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `student_fees_master_id` int(11) DEFAULT NULL,
  `fee_groups_feetype_id` int(11) DEFAULT NULL,
  `student_transport_fee_id` int(11) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `bank_from` varchar(200) DEFAULT NULL,
  `bank_account_transferred` varchar(200) DEFAULT NULL,
  `reference` varchar(200) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `submit_date` datetime DEFAULT NULL,
  `approve_date` datetime DEFAULT NULL,
  `attachment` text DEFAULT NULL,
  `reply` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `is_active` varchar(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `onlineexam`
--

CREATE TABLE `onlineexam` (
  `id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `exam` text DEFAULT NULL,
  `attempt` int(11) NOT NULL,
  `exam_from` datetime DEFAULT NULL,
  `exam_to` datetime DEFAULT NULL,
  `is_quiz` int(11) NOT NULL DEFAULT 0,
  `auto_publish_date` datetime DEFAULT NULL,
  `time_from` time DEFAULT NULL,
  `time_to` time DEFAULT NULL,
  `duration` time NOT NULL,
  `passing_percentage` float NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `publish_result` int(11) NOT NULL DEFAULT 0,
  `answer_word_count` int(11) NOT NULL DEFAULT -1,
  `is_active` varchar(1) DEFAULT '0',
  `is_marks_display` int(11) NOT NULL DEFAULT 0,
  `is_neg_marking` int(11) NOT NULL DEFAULT 0,
  `is_random_question` int(11) NOT NULL DEFAULT 0,
  `is_rank_generated` int(1) NOT NULL DEFAULT 0,
  `publish_exam_notification` int(1) NOT NULL,
  `publish_result_notification` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `onlineexam`
--

INSERT INTO `onlineexam` (`id`, `session_id`, `exam`, `attempt`, `exam_from`, `exam_to`, `is_quiz`, `auto_publish_date`, `time_from`, `time_to`, `duration`, `passing_percentage`, `description`, `publish_result`, `answer_word_count`, `is_active`, `is_marks_display`, `is_neg_marking`, `is_random_question`, `is_rank_generated`, `publish_exam_notification`, `publish_result_notification`, `created_at`, `updated_at`) VALUES
(1, 19, 'Weekly Test  Quiz', 10, '2024-11-26 10:30:00', '2024-11-30 10:30:00', 1, NULL, NULL, NULL, '01:30:00', 35, '<span style=\"color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 13.3333px; background-color: rgb(245, 245, 245);\">1.The Subjects or topics covered in the exam will be as per the Syllabus.</span><br style=\"box-sizing: border-box; color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 13.3333px; background-color: rgb(245, 245, 245);\" />\r\n<span style=\"color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 13.3333px; background-color: rgb(245, 245, 245);\">2.Every student will take the examination on a Laptop/Desktop/Smart Phone.</span>', 0, 50, '1', 1, 0, 0, 0, 1, 0, '2024-12-10 09:37:05', NULL),
(2, 19, 'Online Science Exam', 10, '2024-12-25 00:30:00', '2024-12-31 17:30:00', 0, NULL, NULL, NULL, '02:30:00', 50, '<p style=\"box-sizing: border-box; margin: 0px 0px 10px; color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 13.3333px; background-color: rgb(245, 245, 245);\">1.The examination will comprise of Objective type Multiple Choice Questions (MCQs) .</p>\r\n\r\n<p style=\"box-sizing: border-box; margin: 0px 0px 10px; color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 13.3333px; background-color: rgb(245, 245, 245);\">2.The Subjects or topics covered in the exam will be as per the Syllabus.</p>', 1, 50, '1', 0, 0, 0, 0, 1, 1, '2024-12-10 09:35:47', NULL),
(3, 19, 'Mathematics Quiz', 10, '2024-11-25 00:30:00', '2024-11-28 17:30:00', 0, NULL, NULL, NULL, '02:30:00', 50, '<p style=\"box-sizing: border-box; margin: 0px 0px 10px; color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 13.3333px; background-color: rgb(245, 245, 245);\">1.The examination will comprise of Objective type Multiple Choice Questions (MCQs) .</p>\r\n\r\n<p style=\"box-sizing: border-box; margin: 0px 0px 10px; color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 13.3333px; background-color: rgb(245, 245, 245);\">2.The Subjects or topics covered in the exam will be as per the Syllabus.</p>', 0, 50, '1', 0, 0, 0, 0, 0, 0, '2024-12-09 11:28:54', NULL),
(4, 19, 'Mathematics Quiz', 10, '2024-11-25 16:59:00', '2024-11-29 16:59:00', 0, NULL, NULL, NULL, '02:06:00', 50, '<p style=\"box-sizing: border-box; margin: 0px 0px 10px; color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 13.3333px; background-color: rgb(245, 245, 245);\">1.The examination will comprise of Objective type Multiple Choice Questions (MCQs) .</p>\r\n\r\n<p style=\"box-sizing: border-box; margin: 0px 0px 10px; color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 13.3333px; background-color: rgb(245, 245, 245);\">2.The Subjects or topics covered in the exam will be as per the Syllabus.</p>', 0, 50, '1', 0, 0, 0, 0, 0, 0, '2024-12-09 11:29:58', NULL),
(5, 19, 'English Exam', 1, '2024-12-29 17:00:00', '2025-01-01 17:00:00', 1, NULL, NULL, NULL, '03:00:00', 70, '<p style=\"box-sizing: border-box; margin: 0px 0px 10px; color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 13.3333px; background-color: rgb(245, 245, 245);\">1.The Subjects or topics covered in the exam will be as per the Syllabus.</p>\r\n\r\n<p style=\"box-sizing: border-box; margin: 0px 0px 10px; color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 13.3333px; background-color: rgb(245, 245, 245);\">2.Every student will take the examination on a Laptop/Desktop/Smart Phone</p>', 0, 70, '1', 0, 0, 0, 0, 1, 1, '2024-12-10 09:37:32', NULL),
(6, 19, 'Hindi Exam', 10, '2024-12-29 17:01:00', '2025-01-04 17:01:00', 1, NULL, NULL, NULL, '02:00:00', 20, '<p style=\"box-sizing: border-box; margin: 0px 0px 10px; color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 13.3333px; background-color: rgb(245, 245, 245);\">1.The Subjects or topics covered in the exam will be as per the Syllabus.</p>\r\n\r\n<p style=\"box-sizing: border-box; margin: 0px 0px 10px; color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 13.3333px; background-color: rgb(245, 245, 245);\">2.Every student will take the examination on a Laptop/Desktop/Smart Phone</p>', 0, 20, '1', 0, 0, 0, 0, 1, 1, '2024-12-10 09:37:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `onlineexam_attempts`
--

CREATE TABLE `onlineexam_attempts` (
  `id` int(11) NOT NULL,
  `onlineexam_student_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `onlineexam_questions`
--

CREATE TABLE `onlineexam_questions` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `onlineexam_id` int(11) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `marks` float(10,2) NOT NULL DEFAULT 0.00,
  `neg_marks` float(10,2) DEFAULT 0.00,
  `is_active` varchar(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `onlineexam_students`
--

CREATE TABLE `onlineexam_students` (
  `id` int(11) NOT NULL,
  `onlineexam_id` int(11) DEFAULT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `is_attempted` int(1) NOT NULL DEFAULT 0,
  `rank` int(1) DEFAULT 0,
  `quiz_attempted` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `onlineexam_students`
--

INSERT INTO `onlineexam_students` (`id`, `onlineexam_id`, `student_session_id`, `is_attempted`, `rank`, `quiz_attempted`, `created_at`, `updated_at`) VALUES
(1, 6, 1, 0, 0, 0, '2024-12-10 10:04:26', NULL),
(2, 6, 3, 0, 0, 0, '2024-12-10 10:04:26', NULL),
(3, 6, 4, 0, 0, 0, '2024-12-10 10:04:26', NULL),
(4, 6, 5, 0, 0, 0, '2024-12-10 10:04:39', NULL),
(5, 6, 7, 0, 0, 0, '2024-12-10 10:04:39', NULL),
(6, 6, 9, 0, 0, 0, '2024-12-10 10:04:39', NULL),
(7, 6, 11, 0, 0, 0, '2024-12-10 10:04:49', NULL),
(8, 6, 12, 0, 0, 0, '2024-12-10 10:04:49', NULL),
(9, 6, 14, 0, 0, 0, '2024-12-10 10:04:49', NULL),
(10, 6, 19, 0, 0, 0, '2024-12-10 10:04:58', NULL),
(11, 6, 21, 0, 0, 0, '2024-12-10 10:04:58', NULL),
(12, 6, 24, 0, 0, 0, '2024-12-10 10:04:58', NULL),
(13, 6, 6, 0, 0, 0, '2024-12-10 10:05:10', NULL),
(14, 6, 8, 0, 0, 0, '2024-12-10 10:05:10', NULL),
(15, 6, 10, 0, 0, 0, '2024-12-10 10:05:10', NULL),
(16, 6, 2, 0, 0, 0, '2024-12-10 10:05:19', NULL),
(17, 6, 13, 0, 0, 0, '2024-12-10 10:05:19', NULL),
(18, 6, 15, 0, 0, 0, '2024-12-10 10:05:19', NULL),
(19, 6, 16, 0, 0, 0, '2024-12-10 10:05:29', NULL),
(20, 6, 17, 0, 0, 0, '2024-12-10 10:05:29', NULL),
(21, 6, 18, 0, 0, 0, '2024-12-10 10:05:29', NULL),
(22, 6, 20, 0, 0, 0, '2024-12-10 10:05:48', NULL),
(23, 6, 22, 0, 0, 0, '2024-12-10 10:05:48', NULL),
(24, 6, 23, 0, 0, 0, '2024-12-10 10:05:48', NULL),
(25, 6, 33, 0, 0, 0, '2024-12-10 10:06:13', NULL),
(26, 6, 34, 0, 0, 0, '2024-12-10 10:06:13', NULL),
(27, 6, 36, 0, 0, 0, '2024-12-10 10:06:13', NULL),
(28, 6, 27, 0, 0, 0, '2024-12-10 10:06:20', NULL),
(29, 6, 29, 0, 0, 0, '2024-12-10 10:06:20', NULL),
(30, 6, 31, 0, 0, 0, '2024-12-10 10:06:20', NULL),
(31, 6, 39, 0, 0, 0, '2024-12-10 10:06:37', NULL),
(32, 6, 41, 0, 0, 0, '2024-12-10 10:06:37', NULL),
(33, 6, 42, 0, 0, 0, '2024-12-10 10:06:37', NULL),
(34, 6, 44, 0, 0, 0, '2024-12-10 10:06:45', NULL),
(35, 6, 46, 0, 0, 0, '2024-12-10 10:06:45', NULL),
(36, 6, 48, 0, 0, 0, '2024-12-10 10:06:45', NULL),
(37, 6, 25, 0, 0, 0, '2024-12-10 10:07:00', NULL),
(38, 6, 26, 0, 0, 0, '2024-12-10 10:07:00', NULL),
(39, 6, 28, 0, 0, 0, '2024-12-10 10:07:00', NULL),
(40, 6, 30, 0, 0, 0, '2024-12-10 10:07:09', NULL),
(41, 6, 32, 0, 0, 0, '2024-12-10 10:07:09', NULL),
(42, 6, 35, 0, 0, 0, '2024-12-10 10:07:09', NULL),
(43, 6, 37, 0, 0, 0, '2024-12-10 10:07:18', NULL),
(44, 6, 38, 0, 0, 0, '2024-12-10 10:07:18', NULL),
(45, 6, 40, 0, 0, 0, '2024-12-10 10:07:18', NULL),
(46, 6, 43, 0, 0, 0, '2024-12-10 10:07:26', NULL),
(47, 6, 45, 0, 0, 0, '2024-12-10 10:07:26', NULL),
(48, 6, 47, 0, 0, 0, '2024-12-10 10:07:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `onlineexam_student_results`
--

CREATE TABLE `onlineexam_student_results` (
  `id` int(11) NOT NULL,
  `onlineexam_student_id` int(11) NOT NULL,
  `onlineexam_question_id` int(11) NOT NULL,
  `select_option` longtext DEFAULT NULL,
  `marks` float(10,2) NOT NULL DEFAULT 0.00,
  `remark` text DEFAULT NULL,
  `attachment_name` text DEFAULT NULL,
  `attachment_upload_name` varchar(250) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_admissions`
--

CREATE TABLE `online_admissions` (
  `id` int(11) NOT NULL,
  `admission_no` varchar(100) DEFAULT NULL,
  `roll_no` varchar(100) DEFAULT NULL,
  `reference_no` varchar(50) NOT NULL,
  `admission_date` date DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(255) NOT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `rte` varchar(20) NOT NULL DEFAULT 'No',
  `image` varchar(255) DEFAULT NULL,
  `mobileno` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `pincode` varchar(100) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `cast` varchar(50) NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(100) DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `class_section_id` int(11) DEFAULT NULL,
  `route_id` int(11) NOT NULL,
  `school_house_id` int(11) DEFAULT NULL,
  `blood_group` varchar(200) NOT NULL,
  `vehroute_id` int(11) NOT NULL,
  `hostel_room_id` int(11) DEFAULT NULL,
  `adhar_no` varchar(100) DEFAULT NULL,
  `samagra_id` varchar(100) DEFAULT NULL,
  `bank_account_no` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `ifsc_code` varchar(100) DEFAULT NULL,
  `guardian_is` varchar(100) NOT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `father_phone` varchar(100) DEFAULT NULL,
  `father_occupation` varchar(100) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `mother_phone` varchar(100) DEFAULT NULL,
  `mother_occupation` varchar(100) DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `guardian_relation` varchar(100) DEFAULT NULL,
  `guardian_phone` varchar(100) DEFAULT NULL,
  `guardian_occupation` varchar(150) NOT NULL,
  `guardian_address` text DEFAULT NULL,
  `guardian_email` varchar(100) NOT NULL,
  `father_pic` varchar(255) NOT NULL,
  `mother_pic` varchar(255) NOT NULL,
  `guardian_pic` varchar(255) NOT NULL,
  `is_enroll` int(255) DEFAULT 0,
  `previous_school` text DEFAULT NULL,
  `height` varchar(100) NOT NULL,
  `weight` varchar(100) NOT NULL,
  `note` text NOT NULL,
  `form_status` int(11) NOT NULL,
  `paid_status` int(11) NOT NULL,
  `measurement_date` date DEFAULT NULL,
  `app_key` text DEFAULT NULL,
  `document` text DEFAULT NULL,
  `submit_date` date DEFAULT NULL,
  `disable_at` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_admission_custom_field_value`
--

CREATE TABLE `online_admission_custom_field_value` (
  `id` int(11) NOT NULL,
  `belong_table_id` int(11) DEFAULT NULL,
  `custom_field_id` int(11) DEFAULT NULL,
  `field_value` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_admission_fields`
--

CREATE TABLE `online_admission_fields` (
  `id` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `online_admission_fields`
--

INSERT INTO `online_admission_fields` (`id`, `name`, `status`, `created_at`) VALUES
(1, 'middlename', 0, '2021-05-28 10:29:23'),
(2, 'lastname', 1, '2021-06-02 04:49:19'),
(3, 'category', 0, '2021-06-02 04:48:35'),
(4, 'religion', 0, '2021-06-02 04:48:35'),
(5, 'cast', 0, '2021-06-02 04:48:35'),
(6, 'mobile_no', 1, '2021-06-02 04:50:24'),
(7, 'admission_date', 0, '2021-06-02 04:48:35'),
(8, 'student_photo', 0, '2021-06-02 04:48:35'),
(9, 'is_student_house', 0, '2021-05-29 13:22:53'),
(10, 'is_blood_group', 0, '2021-06-02 04:48:35'),
(11, 'student_height', 0, '2021-06-02 04:48:35'),
(12, 'student_weight', 0, '2021-06-02 04:48:35'),
(13, 'father_name', 0, '2021-06-02 04:48:35'),
(14, 'father_phone', 0, '2021-06-02 04:48:35'),
(15, 'father_occupation', 0, '2021-06-02 04:48:35'),
(16, 'father_pic', 0, '2021-06-02 04:48:35'),
(17, 'mother_name', 1, '2024-11-25 07:43:08'),
(18, 'mother_phone', 0, '2021-06-02 04:48:35'),
(19, 'mother_occupation', 0, '2021-06-02 04:48:35'),
(20, 'mother_pic', 0, '2021-06-02 04:48:35'),
(21, 'guardian_name', 1, '2021-06-02 04:50:54'),
(22, 'guardian_phone', 1, '2021-06-02 04:50:54'),
(23, 'if_guardian_is', 1, '2021-06-02 04:50:54'),
(24, 'guardian_relation', 1, '2021-06-02 04:50:54'),
(25, 'guardian_email', 1, '2021-06-02 04:51:35'),
(26, 'guardian_occupation', 1, '2021-06-02 04:51:26'),
(27, 'guardian_address', 1, '2021-06-02 04:51:31'),
(28, 'bank_account_no', 0, '2021-06-02 04:48:35'),
(29, 'bank_name', 0, '2021-06-02 04:48:35'),
(30, 'ifsc_code', 0, '2021-06-02 04:48:35'),
(31, 'national_identification_no', 1, '2024-11-25 07:42:44'),
(32, 'local_identification_no', 1, '2024-11-25 07:42:42'),
(33, 'rte', 0, '2021-06-02 04:48:35'),
(34, 'previous_school_details', 1, '2024-11-25 07:42:39'),
(35, 'guardian_photo', 1, '2021-06-02 04:51:29'),
(36, 'student_note', 0, '2021-06-02 04:55:08'),
(37, 'measurement_date', 0, '2021-06-02 04:48:35'),
(38, 'student_email', 1, '2021-06-02 04:49:38'),
(39, 'current_address', 1, '2024-11-25 07:42:51'),
(40, 'permanent_address', 1, '2024-11-25 07:42:50'),
(41, 'upload_documents', 1, '2022-09-20 08:00:32');

-- --------------------------------------------------------

--
-- Table structure for table `online_admission_payment`
--

CREATE TABLE `online_admission_payment` (
  `id` int(11) NOT NULL,
  `online_admission_id` int(11) NOT NULL,
  `paid_amount` float(10,2) NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `payment_type` varchar(100) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `note` varchar(100) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_courses`
--

CREATE TABLE `online_courses` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `slug` varchar(200) NOT NULL,
  `url` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `outcomes` text DEFAULT NULL,
  `course_thumbnail` varchar(100) DEFAULT NULL,
  `course_provider` varchar(100) DEFAULT NULL,
  `course_url` varchar(255) DEFAULT NULL,
  `video_id` text DEFAULT NULL,
  `price` float(10,2) NOT NULL DEFAULT 0.00,
  `discount` float(10,2) NOT NULL DEFAULT 0.00,
  `free_course` tinyint(1) DEFAULT NULL COMMENT '0=paid,1=free',
  `view_count` int(11) DEFAULT NULL,
  `front_side_visibility` varchar(10) NOT NULL DEFAULT 'yes',
  `status` tinyint(1) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `online_courses`
--

INSERT INTO `online_courses` (`id`, `title`, `slug`, `url`, `description`, `teacher_id`, `category_id`, `outcomes`, `course_thumbnail`, `course_provider`, `course_url`, `video_id`, `price`, `discount`, `free_course`, `view_count`, `front_side_visibility`, `status`, `created_by`, `created_date`, `updated_date`) VALUES
(1, 'Science - Beginning With Physics!', 'science-beginning-with-physics', 'course/coursedetail/science-beginning-with-physics', 'Physics is a branch of fundamental science (also called basic science). Physics is also called \"the fundamental science\" because all branches of natural science including chemistry, astronomy, geology, and biology are constrained by laws of physics.Physics could be considered the mother science, as the study of motion, matter, and energy came from previous observations in Ancient Greece. Philosophers were the first physicians because of the study of surroundings and the explanation of environmental', 5, 3, '[\"This course will introduce you to some fundamental topics in Physics, including Forces and Energy.\",\"Understand more about Forces, including speed, gravity, contact forces and pressure\",\"Understand more about Waves, including sound, light, wave effects.\"]', 'course_thumbnail1.jpg', 'youtube', 'https://www.youtube.com/watch?v=i_CijGuk7fw', 'i_CijGuk7fw', 250.00, 10.00, 0, NULL, 'yes', 1, 1, '2024-12-09 02:59:15', '2024-12-09 03:28:22'),
(2, 'Math Courses', 'math-courses', 'course/coursedetail/math-courses', 'Math courses teach the abstract science of number, quantity, and space. Learn fundamentals like algebra, calculus, and statistics to strengthen problem-solving and analytical skills, crucial for students, engineers, and professionals in various fields.“Even the most motivated and intelligent student will advance more quickly under the tutelage of someone who knows the best order in which to learn things, who understands and can demonstrate the proper way to perform various skills.', 2, 5, '[\"Whole Numbers, Integers, Fractions and Decimals\",\"Rational and Irrational Numbers\",\"Exponents, Squares, Square roots, Cubes, Cube roots\"]', 'course_thumbnail2.png', 'youtube', 'https://www.youtube.com/watch?v=ljdUHWVg2AY', 'ljdUHWVg2AY', 200.00, 10.00, 0, NULL, 'yes', 1, 1, '2024-12-09 03:31:37', '2024-12-09 03:36:28'),
(3, 'Mathematical  course', 'mathematical-course', 'course/coursedetail/mathematical-course', 'Mathematical practice comprises the working practices of professional mathematicians: selecting theorems to prove, using informal notations to persuade themselves and others that various steps in the final proof are convincing, and seeking peer review and publication, as opposed to the end result of proven and published theorem .Welcome to the Grade 1 Mathematics course, a comprehensive program designed to empower parents and aspiring teachers in nurturing strong mathematical skills.', 3, 5, '[\"Understanding differentiation and integration with related applications.\",\"Grooming your child in Grade 1 English as per the global curriculum of the Mathematics for Grade 1.\",\"To create a smooth transition for Upper Kindergarten to Grade 1, especially for the Mathematics.\"]', 'course_thumbnail3.png', 'youtube', 'https://www.youtube.com/watch?v=LN7cCW1rSsI', 'LN7cCW1rSsI', 210.00, 15.00, 0, NULL, 'yes', 1, 1, '2024-12-09 03:38:57', '2024-12-09 03:43:29'),
(4, 'Presentation Skills for Beginners', 'presentation-skills-for-beginners-1', 'course/coursedetail/presentation-skills-for-beginners-1', 'Presentation skills are the most important skills most people can ever develop during their career. The higher you advance in your career, within any organization, the more time you will spend using your presentation skills to communicate with colleagues, customers, clients, investors, and the public.But wait! You are a beginner, but that doesn\'t mean can\'t start learning presentation skills basics right now. And it doesn\'t mean you have to spend years or even decades getting comfortable.', 5, 1, '[\"Deliver competent presentations.\",\"Display confidence when giving presentations\",\"Be understood by audiences\",\"Make messages memorable to audiences\"]', 'course_thumbnail4.jpg', 'youtube', 'https://www.youtube.com/watch?v=Ns_z4wEtdRM', 'Ns_z4wEtdRM', 250.00, 10.00, 0, 1, 'yes', 1, 1, '2024-12-09 03:45:42', '2024-12-09 05:15:45');

-- --------------------------------------------------------

--
-- Table structure for table `online_course_class_sections`
--

CREATE TABLE `online_course_class_sections` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `class_section_id` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `online_course_class_sections`
--

INSERT INTO `online_course_class_sections` (`id`, `course_id`, `class_section_id`, `created_date`) VALUES
(1, 1, 16, '2024-12-09 02:59:15'),
(2, 1, 17, '2024-12-09 02:59:15'),
(3, 1, 18, '2024-12-09 02:59:15'),
(4, 1, 19, '2024-12-09 02:59:15'),
(5, 2, 16, '2024-12-09 03:31:37'),
(6, 2, 17, '2024-12-09 03:31:37'),
(7, 2, 18, '2024-12-09 03:31:37'),
(8, 2, 19, '2024-12-09 03:31:37'),
(9, 3, 16, '2024-12-09 03:38:57'),
(10, 3, 17, '2024-12-09 03:38:57'),
(11, 3, 18, '2024-12-09 03:38:57'),
(12, 3, 19, '2024-12-09 03:38:57'),
(13, 4, 16, '2024-12-09 03:45:42'),
(14, 4, 17, '2024-12-09 03:45:42'),
(15, 4, 18, '2024-12-09 03:45:42'),
(16, 4, 19, '2024-12-09 03:45:42');

-- --------------------------------------------------------

--
-- Table structure for table `online_course_lesson`
--

CREATE TABLE `online_course_lesson` (
  `id` int(11) NOT NULL,
  `course_section_id` int(11) DEFAULT NULL,
  `lesson_title` varchar(255) DEFAULT NULL,
  `lesson_type` varchar(20) DEFAULT NULL,
  `thumbnail` varchar(100) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `attachment` varchar(200) DEFAULT NULL,
  `video_provider` varchar(20) DEFAULT NULL,
  `video_url` text DEFAULT NULL,
  `video_id` varchar(50) DEFAULT NULL,
  `duration` time DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `online_course_lesson`
--

INSERT INTO `online_course_lesson` (`id`, `course_section_id`, `lesson_title`, `lesson_type`, `thumbnail`, `summary`, `attachment`, `video_provider`, `video_url`, `video_id`, `duration`, `created_date`) VALUES
(1, 1, 'science', 'video', '1.jpg', 'This comprehensive course covers kinetic energy, work, potential energy and conservation of energy. The course combines lectures that summarize the important concepts and tutorials that will guide you and help you develop a problem solving strategy. You\'ll learn how to apply conservation of energy principles to study the dynamics of systems.', NULL, 'youtube', 'https://www.youtube.com/watch?v=i_CijGuk7fw', 'i_CijGuk7fw', '02:11:01', '2024-12-09 03:11:41'),
(2, 1, 'Mastering Work, Energy, and Power', 'video', '2.jpg', 'This course consists of 49 individual lessons including videos and quizzes to help you understand the fundamentals of Physics.  Science - Beginning With Physics is arranged into four topic sections:Forces,Electromagnets,Energy,Waves.\r\n', NULL, 'youtube', 'https://www.youtube.com/watch?v=giP5YGe7tjI', 'giP5YGe7tjI', '01:00:30', '2024-12-09 03:15:45'),
(3, 3, 'Math Courses', 'video', '3.png', '“Even the most motivated and intelligent student will advance more quickly under the tutelage of someone who knows the best order in which to learn things, who understands and can demonstrate the proper way to perform various skills, who can provide useful feedback, and who can devise practice activities designed to overcome particular weaknesses.”', NULL, 'youtube', 'https://www.youtube.com/watch?v=GLMZc2EzsaI&list=PLi5WqFHu_OJMRXrzLaCr94bAEAP-8f7un', 'GLMZc2EzsaI', '02:30:30', '2024-12-09 03:33:18'),
(4, 3, 'Common factors and multiples HCF/LCM', 'video', '4.png', 'This course aims at building a solid understanding of Math fundamentals in an easy and fun way. Each video is designed to build upon previous lectures to help you understand basic math and prepare you to achieve your goals. The road map diagrams will help you understand the path you are taking. We also frequently revise what we have learned before so that you develop interlinkages between concepts.', NULL, 'youtube', 'https://www.youtube.com/watch?v=N_S_wrN8ue8', 'N_S_wrN8ue8', '01:30:30', '2024-12-09 03:34:11'),
(5, 4, 'Mathematical course', 'video', '5.png', 'Probability Theory course is actually an introduction to the study of statistics and probability, but based upon the usage of Calculus to study both discrete and continuous aspects of the subject. The main difference is in how these topics are approached. For example, in a basic stats class, confidence intervals are introduced as a way to describe the distribution of parameters (i.e. how spread out your estimated results are). The focus is on how to make and interpret these intervals. With a calculus based statistics approach, functions are derived for these intervals .', NULL, 'youtube', 'https://www.youtube.com/watch?v=LN7cCW1rSsI', 'LN7cCW1rSsI', '01:30:30', '2024-12-09 03:41:00'),
(6, 4, 'Mathematical course Chapter 2', 'video', '6.png', 'Mathematics, being a foundation for many subjects and critical for analytical skills, requires your support in fostering a love for numbers and problem-solving.\r\nThroughout this journey, we will introduce and reinforce essential mathematical skills and concepts, ensuring that your child develops a solid foundation in mathematical understanding.No need to worry about additional resources, as this course includes a wealth of attractive videos and well-designed downloadable worksheets that complement your efforts to groom your child or students.', NULL, 'youtube', 'https://www.youtube.com/watch?v=UwYJUKVc-Hs', 'UwYJUKVc-Hs', '02:30:30', '2024-12-09 03:41:51'),
(7, 5, 'Presentation Skills for Beginners', 'video', '7.jpg', 'Presentation Skills. Imagine yourself speaking in front of a large audience for an hour, and you appear to be speaking purely from the heart, without reading from notes. You can break away from the lectern. Never be a boring speech reader again! .Presentation Skills course you will learn to speak directly to your audience. You will no longer be tempted to read word-for-word from a script or to read bullet points from a PowerPoint. You will learn to speak with confidence to audiences because you will know that the audience perceives you as authentic and knowledgeable. You will learn how to speak in such a way that your audience will perceive you as not speaking from notes.  ', NULL, 'youtube', 'https://www.youtube.com/watch?v=Ns_z4wEtdRM', 'Ns_z4wEtdRM', '02:30:30', '2024-12-09 03:46:45'),
(8, 5, 'Speak without reading PowerPoint bullet points', 'video', '8.jpg', 'This course was born from frustration! Frustration that no one seems to be able to do what they’re teaching when it comes to speaking and communication. Everyone is either trying to teach you how to be a motivational speaker or some BS on overcoming your fears. In other words, it’s all self-help.  If you have ever taken a public speaking course, chances are you’ve been bored through most of it and it probably didn\'t change anything.   This course will introduce a completely different way to learn communication skills and public speaking.  We are not going to talk about ideas that sound great, but do nothing to change behaviour, we are going to introduce physical.', NULL, 'youtube', 'https://www.youtube.com/watch?v=i5mYphUoOCs', 'i5mYphUoOCs', '01:30:30', '2024-12-09 03:47:28');

-- --------------------------------------------------------

--
-- Table structure for table `online_course_payment`
--

CREATE TABLE `online_course_payment` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `online_courses_id` int(11) DEFAULT NULL,
  `course_name` varchar(255) DEFAULT NULL,
  `actual_price` float(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` float(10,2) NOT NULL DEFAULT 0.00,
  `payment_mode` varchar(50) DEFAULT NULL,
  `payment_type` varchar(100) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `online_course_payment`
--

INSERT INTO `online_course_payment` (`id`, `student_id`, `guest_id`, `online_courses_id`, `course_name`, `actual_price`, `paid_amount`, `payment_mode`, `payment_type`, `transaction_id`, `note`, `date`) VALUES
(1, NULL, 2, 4, 'Presentation Skills for Beginners', 250.00, 225.00, 'Stripe', 'Online', 'ch_3QUOZCGbC4Q2i5M71sAE878k', 'Online course fees deposit through Stripe Txn ID: ch_3QUOZCGbC4Q2i5M71sAE878k', '2024-12-10 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `online_course_processing_payment`
--

CREATE TABLE `online_course_processing_payment` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `online_courses_id` int(11) DEFAULT NULL,
  `course_name` varchar(255) DEFAULT NULL,
  `actual_price` float(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` float(10,2) NOT NULL DEFAULT 0.00,
  `payment_mode` varchar(50) DEFAULT NULL,
  `payment_type` varchar(100) DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `gateway_ins_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_course_quiz`
--

CREATE TABLE `online_course_quiz` (
  `id` int(11) NOT NULL,
  `course_section_id` int(11) DEFAULT NULL,
  `quiz_title` varchar(255) DEFAULT NULL,
  `quiz_instruction` text DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `online_course_quiz`
--

INSERT INTO `online_course_quiz` (`id`, `course_section_id`, `quiz_title`, `quiz_instruction`, `created_by`, `created_date`) VALUES
(1, 1, 'Science Quizzes!!', '1.Read each question carefully, and click on the button next to your response that is based on the information covered on the topic in the module.\r\n2.Each question in the quiz is of multiple-choice or \"true or false\" format.\r\n3.If you select an incorrect response for a question, you can try again until you get the correct response.\r\n 4.All  information covered on the topic in the module.', NULL, '2024-12-09 03:26:26'),
(2, 3, 'Basics of Mathematics quiz', '1.Read each question carefully, and click on the button next to your response that is based on the information covered on the topic in the module.\r\n2.Each question in the quiz is of multiple-choice or \"true or false\" format.\r\n3.If you select an incorrect response for a question, you can try again until you get the correct response.\r\n 4.All  information covered on the topic in the module.\r\n', NULL, '2024-12-09 03:34:50'),
(3, 4, 'Mathematic Quiz', '1.Read each question carefully, and click on the button next to your response that is based on the information covered on the topic in the module.\r\n2.Each question in the quiz is of multiple-choice or \"true or false\" format.\r\n3.If you select an incorrect response for a question, you can try again until you get the correct response.\r\n 4.All  information covered on the topic in the module.', NULL, '2024-12-09 03:42:12'),
(4, 5, 'Presentation Skills for Beginners Quiz', '1.Read each question carefully, and click on the button next to your response that is based on the information covered on the topic in the module.\r\n2.Each question in the quiz is of multiple-choice or \"true or false\" format.\r\n3.If you select an incorrect response for a question, you can try again until you get the correct response.\r\n 4.All  information covered on the topic in the module.', NULL, '2024-12-09 03:48:01');

-- --------------------------------------------------------

--
-- Table structure for table `online_course_section`
--

CREATE TABLE `online_course_section` (
  `id` int(11) NOT NULL,
  `online_course_id` int(11) DEFAULT NULL,
  `section_title` varchar(255) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `online_course_section`
--

INSERT INTO `online_course_section` (`id`, `online_course_id`, `section_title`, `order`, `is_active`) VALUES
(1, 1, 'Science - Beginning With Physics!', NULL, NULL),
(3, 2, 'Math Courses', NULL, NULL),
(4, 3, 'Mathematical course', NULL, NULL),
(5, 4, 'Presentation Skills for Beginners', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `online_course_settings`
--

CREATE TABLE `online_course_settings` (
  `id` int(11) NOT NULL,
  `guest_prefix` varchar(50) NOT NULL,
  `guest_id_start_from` int(11) NOT NULL,
  `guest_login` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `online_course_settings`
--

INSERT INTO `online_course_settings` (`id`, `guest_prefix`, `guest_id_start_from`, `guest_login`) VALUES
(1, 'Guest', 100, 1);

-- --------------------------------------------------------

--
-- Table structure for table `payment_settings`
--

CREATE TABLE `payment_settings` (
  `id` int(11) NOT NULL,
  `payment_type` varchar(200) NOT NULL,
  `api_username` varchar(200) DEFAULT NULL,
  `api_secret_key` varchar(200) NOT NULL,
  `salt` varchar(200) NOT NULL,
  `api_publishable_key` varchar(200) NOT NULL,
  `api_password` varchar(200) DEFAULT NULL,
  `api_signature` varchar(200) DEFAULT NULL,
  `api_email` varchar(200) DEFAULT NULL,
  `paypal_demo` varchar(100) NOT NULL,
  `account_no` varchar(200) NOT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `gateway_mode` int(11) NOT NULL COMMENT '0 Testing, 1 live',
  `paytm_website` varchar(255) NOT NULL,
  `paytm_industrytype` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `payment_settings`
--

INSERT INTO `payment_settings` (`id`, `payment_type`, `api_username`, `api_secret_key`, `salt`, `api_publishable_key`, `api_password`, `api_signature`, `api_email`, `paypal_demo`, `account_no`, `is_active`, `gateway_mode`, `paytm_website`, `paytm_industrytype`, `created_at`, `updated_at`) VALUES
(1, 'stripe', NULL, 'sk_test_51Q4QU0GbC4Q2i5M73tjBHLH8nfRo4uvwdRlrcE8DENErmGCuAXRy8OQMQTgPtPzeSZruO1KKr4jm78ozKgxla10g00CPLSInu6', '', 'pk_test_51Q4QU0GbC4Q2i5M7v8OodlZqSV8FdS9ptOZLtQtdD8jYs9GSxfERdzdyHYLMo5UcmrKDPzZSKqDizWOjRlqOu7jy00AECqYKwZ', NULL, NULL, NULL, '', '', 'yes', 0, '', '', '2024-12-10 08:12:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payslip_allowance`
--

CREATE TABLE `payslip_allowance` (
  `id` int(11) NOT NULL,
  `payslip_id` int(11) NOT NULL,
  `allowance_type` varchar(200) NOT NULL,
  `amount` float NOT NULL,
  `staff_id` int(11) NOT NULL,
  `cal_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permission_category`
--

CREATE TABLE `permission_category` (
  `id` int(11) NOT NULL,
  `perm_group_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `short_code` varchar(100) DEFAULT NULL,
  `enable_view` int(11) DEFAULT 0,
  `enable_add` int(11) DEFAULT 0,
  `enable_edit` int(11) DEFAULT 0,
  `enable_delete` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `permission_category`
--

INSERT INTO `permission_category` (`id`, `perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`, `created_at`) VALUES
(1, 1, 'Student', 'student', 1, 1, 1, 1, '2019-10-24 05:42:03'),
(2, 1, 'Import Student', 'import_student', 1, 0, 0, 0, '2018-06-22 10:17:19'),
(3, 1, 'Student Categories', 'student_categories', 1, 1, 1, 1, '2018-06-22 10:17:36'),
(4, 1, 'Student Houses', 'student_houses', 1, 1, 1, 1, '2018-06-22 10:17:53'),
(5, 2, 'Collect Fees', 'collect_fees', 1, 1, 0, 1, '2018-06-22 10:21:03'),
(6, 2, 'Fees Carry Forward', 'fees_carry_forward', 1, 0, 0, 0, '2018-06-27 00:18:15'),
(7, 2, 'Fees Master', 'fees_master', 1, 1, 1, 1, '2018-06-27 00:18:57'),
(8, 2, 'Fees Group', 'fees_group', 1, 1, 1, 1, '2018-06-22 10:21:46'),
(9, 3, 'Income', 'income', 1, 1, 1, 1, '2018-06-22 10:23:21'),
(10, 3, 'Income Head', 'income_head', 1, 1, 1, 1, '2018-06-22 10:22:44'),
(11, 3, 'Search Income', 'search_income', 1, 0, 0, 0, '2018-06-22 10:23:00'),
(12, 4, 'Expense', 'expense', 1, 1, 1, 1, '2018-06-22 10:24:06'),
(13, 4, 'Expense Head', 'expense_head', 1, 1, 1, 1, '2018-06-22 10:23:47'),
(14, 4, 'Search Expense', 'search_expense', 1, 0, 0, 0, '2018-06-22 10:24:13'),
(15, 5, 'Student / Period Attendance', 'student_attendance', 1, 1, 1, 0, '2019-11-29 01:19:05'),
(20, 6, 'Marks Grade', 'marks_grade', 1, 1, 1, 1, '2018-06-22 10:25:25'),
(21, 7, 'Class Timetable', 'class_timetable', 1, 0, 1, 0, '2019-11-24 03:05:17'),
(23, 7, 'Subject', 'subject', 1, 1, 1, 1, '2018-06-22 10:32:17'),
(24, 7, 'Class', 'class', 1, 1, 1, 1, '2018-06-22 10:32:35'),
(25, 7, 'Section', 'section', 1, 1, 1, 1, '2018-06-22 10:31:10'),
(26, 7, 'Promote Student', 'promote_student', 1, 0, 0, 0, '2018-06-22 10:32:47'),
(27, 8, 'Upload Content', 'upload_content', 1, 1, 0, 1, '2018-06-22 10:33:19'),
(28, 9, 'Books List', 'books', 1, 1, 1, 1, '2019-11-24 00:37:12'),
(29, 9, 'Issue Return', 'issue_return', 1, 0, 0, 0, '2019-11-24 00:37:18'),
(30, 9, 'Add Staff Member', 'add_staff_member', 1, 0, 0, 0, '2018-07-02 11:37:00'),
(31, 10, 'Issue Item', 'issue_item', 1, 1, 1, 1, '2019-11-29 06:39:27'),
(32, 10, 'Add Item Stock', 'item_stock', 1, 1, 1, 1, '2019-11-24 00:39:17'),
(33, 10, 'Add Item', 'item', 1, 1, 1, 1, '2019-11-24 00:39:39'),
(34, 10, 'Item Store', 'store', 1, 1, 1, 1, '2019-11-24 00:40:41'),
(35, 10, 'Item Supplier', 'supplier', 1, 1, 1, 1, '2019-11-24 00:40:49'),
(37, 11, 'Routes', 'routes', 1, 1, 1, 1, '2018-06-22 10:39:17'),
(38, 11, 'Vehicle', 'vehicle', 1, 1, 1, 1, '2018-06-22 10:39:36'),
(39, 11, 'Assign Vehicle', 'assign_vehicle', 1, 1, 1, 1, '2018-06-27 04:39:20'),
(40, 12, 'Hostel', 'hostel', 1, 1, 1, 1, '2018-06-22 10:40:49'),
(41, 12, 'Room Type', 'room_type', 1, 1, 1, 1, '2018-06-22 10:40:27'),
(42, 12, 'Hostel Rooms', 'hostel_rooms', 1, 1, 1, 1, '2018-06-25 06:23:03'),
(43, 13, 'Notice Board', 'notice_board', 1, 1, 1, 1, '2018-06-22 10:41:17'),
(44, 13, 'Email', 'email', 1, 0, 0, 0, '2019-11-26 05:20:37'),
(46, 13, 'Email / SMS Log', 'email_sms_log', 1, 0, 0, 0, '2018-06-22 10:41:23'),
(53, 15, 'Languages', 'languages', 0, 1, 0, 1, '2021-01-23 07:09:32'),
(54, 15, 'General Setting', 'general_setting', 1, 0, 1, 0, '2018-07-05 09:08:35'),
(55, 15, 'Session Setting', 'session_setting', 1, 1, 1, 1, '2018-06-22 10:44:15'),
(56, 15, 'Notification Setting', 'notification_setting', 1, 0, 1, 0, '2018-07-05 09:08:41'),
(57, 15, 'SMS Setting', 'sms_setting', 1, 0, 1, 0, '2018-07-05 09:08:47'),
(58, 15, 'Email Setting', 'email_setting', 1, 0, 1, 0, '2018-07-05 09:08:51'),
(59, 15, 'Front CMS Setting', 'front_cms_setting', 1, 0, 1, 0, '2018-07-05 09:08:55'),
(60, 15, 'Payment Methods', 'payment_methods', 1, 0, 1, 0, '2018-07-05 09:08:59'),
(61, 16, 'Menus', 'menus', 1, 1, 0, 1, '2018-07-09 03:50:06'),
(62, 16, 'Media Manager', 'media_manager', 1, 1, 0, 1, '2018-07-09 03:50:26'),
(63, 16, 'Banner Images', 'banner_images', 1, 1, 0, 1, '2018-06-22 10:46:02'),
(64, 16, 'Pages', 'pages', 1, 1, 1, 1, '2018-06-22 10:46:21'),
(65, 16, 'Gallery', 'gallery', 1, 1, 1, 1, '2018-06-22 10:47:02'),
(66, 16, 'Event', 'event', 1, 1, 1, 1, '2018-06-22 10:47:20'),
(67, 16, 'News', 'notice', 1, 1, 1, 1, '2018-07-03 08:39:34'),
(68, 2, 'Fees Group Assign', 'fees_group_assign', 1, 0, 0, 0, '2018-06-22 10:20:42'),
(69, 2, 'Fees Type', 'fees_type', 1, 1, 1, 1, '2018-06-22 10:19:34'),
(70, 2, 'Fees Discount', 'fees_discount', 1, 1, 1, 1, '2018-06-22 10:20:10'),
(71, 2, 'Fees Discount Assign', 'fees_discount_assign', 1, 0, 0, 0, '2018-06-22 10:20:17'),
(73, 2, 'Search Fees Payment', 'search_fees_payment', 1, 0, 0, 0, '2018-06-22 10:20:27'),
(74, 2, 'Search Due Fees', 'search_due_fees', 1, 0, 0, 0, '2018-06-22 10:20:35'),
(77, 7, 'Assign Class Teacher', 'assign_class_teacher', 1, 1, 1, 1, '2018-06-22 10:30:52'),
(78, 17, 'Admission Enquiry', 'admission_enquiry', 1, 1, 1, 1, '2018-06-22 10:51:24'),
(79, 17, 'Follow Up Admission Enquiry', 'follow_up_admission_enquiry', 1, 1, 0, 1, '2018-06-22 10:51:39'),
(80, 17, 'Visitor Book', 'visitor_book', 1, 1, 1, 1, '2018-06-22 10:48:58'),
(81, 17, 'Phone Call Log', 'phone_call_log', 1, 1, 1, 1, '2018-06-22 10:50:57'),
(82, 17, 'Postal Dispatch', 'postal_dispatch', 1, 1, 1, 1, '2018-06-22 10:50:21'),
(83, 17, 'Postal Receive', 'postal_receive', 1, 1, 1, 1, '2018-06-22 10:50:04'),
(84, 17, 'Complain', 'complaint', 1, 1, 1, 1, '2018-07-03 08:40:55'),
(85, 17, 'Setup Font Office', 'setup_font_office', 1, 1, 1, 1, '2018-06-22 10:49:24'),
(86, 18, 'Staff', 'staff', 1, 1, 1, 1, '2018-06-22 10:53:31'),
(87, 18, 'Disable Staff', 'disable_staff', 1, 0, 0, 0, '2018-06-22 10:53:12'),
(88, 18, 'Staff Attendance', 'staff_attendance', 1, 1, 1, 0, '2018-06-22 10:53:10'),
(90, 18, 'Staff Payroll', 'staff_payroll', 1, 1, 0, 1, '2018-06-22 10:52:51'),
(93, 19, 'Homework', 'homework', 1, 1, 1, 1, '2018-06-22 10:53:50'),
(94, 19, 'Homework Evaluation', 'homework_evaluation', 1, 1, 0, 0, '2018-06-27 03:07:21'),
(96, 20, 'Student Certificate', 'student_certificate', 1, 1, 1, 1, '2018-07-06 10:41:07'),
(97, 20, 'Generate Certificate', 'generate_certificate', 1, 0, 0, 0, '2018-07-06 10:37:16'),
(98, 20, 'Student ID Card', 'student_id_card', 1, 1, 1, 1, '2018-07-06 10:41:28'),
(99, 20, 'Generate ID Card', 'generate_id_card', 1, 0, 0, 0, '2018-07-06 10:41:49'),
(102, 21, 'Calendar To Do List', 'calendar_to_do_list', 1, 1, 1, 1, '2018-06-22 10:54:41'),
(104, 10, 'Item Category', 'item_category', 1, 1, 1, 1, '2018-06-22 10:34:33'),
(106, 22, 'Quick Session Change', 'quick_session_change', 1, 0, 0, 0, '2018-06-22 10:54:45'),
(107, 1, 'Disable Student', 'disable_student', 1, 0, 0, 0, '2018-06-25 06:21:34'),
(108, 18, ' Approve Leave Request', 'approve_leave_request', 1, 0, 1, 1, '2020-10-05 08:56:27'),
(109, 18, 'Apply Leave', 'apply_leave', 1, 1, 0, 0, '2019-11-28 23:47:46'),
(110, 18, 'Leave Types ', 'leave_types', 1, 1, 1, 1, '2018-07-02 10:17:56'),
(111, 18, 'Department', 'department', 1, 1, 1, 1, '2018-06-26 03:57:07'),
(112, 18, 'Designation', 'designation', 1, 1, 1, 1, '2018-06-26 03:57:07'),
(113, 22, 'Fees Collection And Expense Monthly Chart', 'fees_collection_and_expense_monthly_chart', 1, 0, 0, 0, '2018-07-03 07:08:15'),
(114, 22, 'Fees Collection And Expense Yearly Chart', 'fees_collection_and_expense_yearly_chart', 1, 0, 0, 0, '2018-07-03 07:08:15'),
(115, 22, 'Monthly Fees Collection Widget', 'Monthly fees_collection_widget', 1, 0, 0, 0, '2018-07-03 07:13:35'),
(116, 22, 'Monthly Expense Widget', 'monthly_expense_widget', 1, 0, 0, 0, '2018-07-03 07:13:35'),
(117, 22, 'Student Count Widget', 'student_count_widget', 1, 0, 0, 0, '2018-07-03 07:13:35'),
(118, 22, 'Staff Role Count Widget', 'staff_role_count_widget', 1, 0, 0, 0, '2018-07-03 07:13:35'),
(122, 5, 'Attendance By Date', 'attendance_by_date', 1, 0, 0, 0, '2018-07-03 08:42:29'),
(123, 9, 'Add Student', 'add_student', 1, 0, 0, 0, '2018-07-03 08:42:29'),
(126, 15, 'User Status', 'user_status', 1, 0, 0, 0, '2018-07-03 08:42:29'),
(127, 18, 'Can See Other Users Profile', 'can_see_other_users_profile', 1, 0, 0, 0, '2018-07-03 08:42:29'),
(128, 1, 'Student Timeline', 'student_timeline', 1, 1, 1, 1, '2022-12-28 09:52:24'),
(129, 18, 'Staff Timeline', 'staff_timeline', 1, 1, 1, 1, '2022-12-28 09:52:24'),
(130, 15, 'Backup', 'backup', 1, 1, 0, 1, '2018-07-09 04:17:17'),
(131, 15, 'Restore', 'restore', 1, 0, 0, 0, '2018-07-09 04:17:17'),
(134, 1, 'Disable Reason', 'disable_reason', 1, 1, 1, 1, '2019-11-27 06:39:21'),
(135, 2, 'Fees Reminder', 'fees_reminder', 1, 0, 1, 0, '2019-10-25 00:39:49'),
(136, 5, 'Approve Leave', 'approve_leave', 1, 1, 1, 1, '2022-12-28 09:52:24'),
(137, 6, 'Exam Group', 'exam_group', 1, 1, 1, 1, '2019-10-25 01:02:34'),
(141, 6, 'Design Admit Card', 'design_admit_card', 1, 1, 1, 1, '2019-10-25 01:06:59'),
(142, 6, 'Print Admit Card', 'print_admit_card', 1, 0, 0, 0, '2019-11-23 23:57:51'),
(143, 6, 'Design Marksheet', 'design_marksheet', 1, 1, 1, 1, '2019-10-25 01:10:25'),
(144, 6, 'Print Marksheet', 'print_marksheet', 1, 0, 0, 0, '2019-10-25 01:11:02'),
(145, 7, 'Teachers Timetable', 'teachers_time_table', 1, 0, 0, 0, '2019-11-30 02:52:21'),
(146, 14, 'Student Report', 'student_report', 1, 0, 0, 0, '2019-10-25 01:27:00'),
(147, 14, 'Guardian Report', 'guardian_report', 1, 0, 0, 0, '2019-10-25 01:30:27'),
(148, 14, 'Student History', 'student_history', 1, 0, 0, 0, '2019-10-25 01:39:07'),
(149, 14, 'Student Login Credential Report', 'student_login_credential_report', 1, 0, 0, 0, '2019-10-25 01:39:07'),
(150, 14, 'Class Subject Report', 'class_subject_report', 1, 0, 0, 0, '2019-10-25 01:39:07'),
(151, 14, 'Admission Report', 'admission_report', 1, 0, 0, 0, '2019-10-25 01:39:07'),
(152, 14, 'Sibling Report', 'sibling_report', 1, 0, 0, 0, '2019-10-25 01:39:07'),
(153, 14, 'Homework Evaluation Report', 'homehork_evaluation_report', 1, 0, 0, 0, '2019-11-24 01:04:24'),
(154, 14, 'Student Profile', 'student_profile', 1, 0, 0, 0, '2019-10-25 01:39:07'),
(155, 14, 'Fees Statement', 'fees_statement', 1, 0, 0, 0, '2019-10-25 01:55:52'),
(156, 14, 'Balance Fees Report', 'balance_fees_report', 1, 0, 0, 0, '2019-10-25 01:55:52'),
(157, 14, 'Fees Collection Report', 'fees_collection_report', 1, 0, 0, 0, '2019-10-25 01:55:52'),
(158, 14, 'Online Fees Collection Report', 'online_fees_collection_report', 1, 0, 0, 0, '2019-10-25 01:55:52'),
(159, 14, 'Income Report', 'income_report', 1, 0, 0, 0, '2019-10-25 01:55:52'),
(160, 14, 'Expense Report', 'expense_report', 1, 0, 0, 0, '2019-10-25 01:55:52'),
(161, 14, 'PayRoll Report', 'payroll_report', 1, 0, 0, 0, '2019-10-31 00:23:22'),
(162, 14, 'Income Group Report', 'income_group_report', 1, 0, 0, 0, '2019-10-25 01:55:52'),
(163, 14, 'Expense Group Report', 'expense_group_report', 1, 0, 0, 0, '2019-10-25 01:55:52'),
(164, 14, 'Attendance Report', 'attendance_report', 1, 0, 0, 0, '2019-10-25 02:08:06'),
(165, 14, 'Staff Attendance Report', 'staff_attendance_report', 1, 0, 0, 0, '2019-10-25 02:08:06'),
(174, 14, 'Transport Report', 'transport_report', 1, 0, 0, 0, '2019-10-25 02:13:56'),
(175, 14, 'Hostel Report', 'hostel_report', 1, 0, 0, 0, '2019-11-27 06:51:53'),
(176, 14, 'Audit Trail Report', 'audit_trail_report', 1, 0, 0, 0, '2019-10-25 02:16:39'),
(177, 14, 'User Log', 'user_log', 1, 0, 0, 0, '2019-10-25 02:19:27'),
(178, 14, 'Book Issue Report', 'book_issue_report', 1, 0, 0, 0, '2019-10-25 02:29:04'),
(179, 14, 'Book Due Report', 'book_due_report', 1, 0, 0, 0, '2019-10-25 02:29:04'),
(180, 14, 'Book Inventory Report', 'book_inventory_report', 1, 0, 0, 0, '2019-10-25 02:29:04'),
(181, 14, 'Stock Report', 'stock_report', 1, 0, 0, 0, '2019-10-25 02:31:28'),
(182, 14, 'Add Item Report', 'add_item_report', 1, 0, 0, 0, '2019-10-25 02:31:28'),
(183, 14, 'Issue Item Report', 'issue_item_report', 1, 0, 0, 0, '2019-11-29 03:48:06'),
(185, 23, 'Online Examination', 'online_examination', 1, 1, 1, 1, '2019-11-23 23:54:50'),
(186, 23, 'Question Bank', 'question_bank', 1, 1, 1, 1, '2019-11-23 23:55:18'),
(187, 6, 'Exam Result', 'exam_result', 1, 0, 0, 0, '2019-11-23 23:58:50'),
(188, 7, 'Subject Group', 'subject_group', 1, 1, 1, 1, '2019-11-24 00:34:32'),
(189, 18, 'Teachers Rating', 'teachers_rating', 1, 0, 1, 1, '2019-11-24 03:12:54'),
(190, 22, 'Fees Awaiting Payment Widegts', 'fees_awaiting_payment_widegts', 1, 0, 0, 0, '2019-11-24 00:52:51'),
(191, 22, 'Conveted Leads Widegts', 'conveted_leads_widegts', 1, 0, 0, 0, '2019-11-24 00:58:24'),
(192, 22, 'Fees Overview Widegts', 'fees_overview_widegts', 1, 0, 0, 0, '2019-11-24 00:57:41'),
(193, 22, 'Enquiry Overview Widegts', 'enquiry_overview_widegts', 1, 0, 0, 0, '2019-12-02 05:06:09'),
(194, 22, 'Library Overview Widegts', 'book_overview_widegts', 1, 0, 0, 0, '2019-12-01 01:13:04'),
(195, 22, 'Student Today Attendance Widegts', 'today_attendance_widegts', 1, 0, 0, 0, '2019-12-03 04:57:45'),
(196, 6, 'Marks Import', 'marks_import', 1, 0, 0, 0, '2019-11-24 01:02:11'),
(197, 14, 'Student Attendance Type Report', 'student_attendance_type_report', 1, 0, 0, 0, '2019-11-24 01:06:32'),
(198, 14, 'Exam Marks Report', 'exam_marks_report', 1, 0, 0, 0, '2019-11-24 01:11:15'),
(200, 14, 'Online Exam Wise Report', 'online_exam_wise_report', 1, 0, 0, 0, '2019-11-24 01:18:14'),
(201, 14, 'Online Exams Report', 'online_exams_report', 1, 0, 0, 0, '2019-11-29 02:48:05'),
(202, 14, 'Online Exams Attempt Report', 'online_exams_attempt_report', 1, 0, 0, 0, '2019-11-29 02:46:24'),
(203, 14, 'Online Exams Rank Report', 'online_exams_rank_report', 1, 0, 0, 0, '2019-11-24 01:22:25'),
(204, 14, 'Staff Report', 'staff_report', 1, 0, 0, 0, '2019-11-24 01:25:27'),
(205, 6, 'Exam', 'exam', 1, 1, 1, 1, '2019-11-24 04:55:48'),
(207, 6, 'Exam Publish', 'exam_publish', 1, 0, 0, 0, '2019-11-24 05:15:04'),
(208, 6, 'Link Exam', 'link_exam', 1, 0, 1, 0, '2019-11-24 05:15:04'),
(210, 6, 'Assign / View student', 'exam_assign_view_student', 1, 0, 1, 0, '2019-11-24 05:15:04'),
(211, 6, 'Exam Subject', 'exam_subject', 1, 0, 1, 0, '2019-11-24 05:15:04'),
(212, 6, 'Exam Marks', 'exam_marks', 1, 0, 1, 0, '2019-11-24 05:15:04'),
(213, 15, 'Language Switcher', 'language_switcher', 1, 0, 0, 0, '2019-11-24 05:17:11'),
(214, 23, 'Add Questions in Exam ', 'add_questions_in_exam', 1, 0, 1, 0, '2019-11-28 01:38:57'),
(215, 15, 'Custom Fields', 'custom_fields', 1, 0, 0, 0, '2019-11-29 04:08:35'),
(216, 15, 'System Fields', 'system_fields', 1, 0, 0, 0, '2019-11-25 00:15:01'),
(217, 13, 'SMS', 'sms', 1, 0, 0, 0, '2018-06-22 10:40:54'),
(219, 14, 'Student / Period Attendance Report', 'student_period_attendance_report', 1, 0, 0, 0, '2019-11-29 02:19:31'),
(220, 14, 'Biometric Attendance Log', 'biometric_attendance_log', 1, 0, 0, 0, '2019-11-27 05:59:16'),
(221, 14, 'Book Issue Return Report', 'book_issue_return_report', 1, 0, 0, 0, '2019-11-27 06:30:23'),
(222, 23, 'Assign / View Student', 'online_assign_view_student', 1, 0, 1, 0, '2019-11-28 04:20:22'),
(223, 14, 'Rank Report', 'rank_report', 1, 0, 0, 0, '2019-11-29 02:30:21'),
(224, 25, 'Chat', 'chat', 1, 0, 0, 0, '2019-11-29 04:10:28'),
(226, 22, 'Income Donut Graph', 'income_donut_graph', 1, 0, 0, 0, '2019-11-29 05:00:33'),
(227, 22, 'Expense Donut Graph', 'expense_donut_graph', 1, 0, 0, 0, '2019-11-29 05:01:10'),
(228, 9, 'Import Book', 'import_book', 1, 0, 0, 0, '2019-11-29 06:21:01'),
(229, 22, 'Staff Present Today Widegts', 'staff_present_today_widegts', 1, 0, 0, 0, '2019-11-29 06:48:00'),
(230, 22, 'Student Present Today Widegts', 'student_present_today_widegts', 1, 0, 0, 0, '2019-11-29 06:47:42'),
(231, 26, 'Multi Class Student', 'multi_class_student', 1, 1, 1, 1, '2020-10-05 08:56:27'),
(232, 27, 'Online Admission', 'online_admission', 1, 0, 1, 1, '2019-12-02 06:11:10'),
(233, 15, 'Print Header Footer', 'print_header_footer', 1, 0, 0, 0, '2020-02-12 02:02:02'),
(234, 28, 'Manage Alumni', 'manage_alumni', 1, 1, 1, 1, '2020-06-02 03:15:46'),
(235, 28, 'Events', 'events', 1, 1, 1, 1, '2020-05-28 21:48:52'),
(236, 29, 'Manage Lesson Plan', 'manage_lesson_plan', 1, 1, 1, 0, '2020-05-28 22:17:37'),
(237, 29, 'Manage Syllabus Status', 'manage_syllabus_status', 1, 0, 1, 0, '2020-05-28 22:20:11'),
(238, 29, 'Lesson', 'lesson', 1, 1, 1, 1, '2020-05-28 22:20:11'),
(239, 29, 'Topic', 'topic', 1, 1, 1, 1, '2020-05-28 22:20:11'),
(240, 14, 'Syllabus Status Report', 'syllabus_status_report', 1, 0, 0, 0, '2020-05-28 23:17:54'),
(241, 14, 'Teacher Syllabus Status Report', 'teacher_syllabus_status_report', 1, 0, 0, 0, '2020-05-28 23:17:54'),
(242, 14, 'Alumni Report', 'alumni_report', 1, 0, 0, 0, '2020-06-07 23:59:54'),
(243, 15, 'Student Profile Update', 'student_profile_update', 1, 0, 0, 0, '2020-08-21 05:36:33'),
(244, 14, 'Student Gender Ratio Report', 'student_gender_ratio_report', 1, 0, 0, 0, '2020-08-22 12:37:51'),
(245, 14, 'Student Teacher Ratio Report', 'student_teacher_ratio_report', 1, 0, 0, 0, '2020-08-22 12:42:27'),
(246, 14, 'Daily Attendance Report', 'daily_attendance_report', 1, 0, 0, 0, '2020-08-22 12:43:16'),
(247, 23, 'Import Question', 'import_question', 1, 0, 0, 0, '2019-11-23 18:25:18'),
(248, 20, 'Staff ID Card', 'staff_id_card', 1, 1, 1, 1, '2018-07-06 10:41:28'),
(249, 20, 'Generate Staff ID Card', 'generate_staff_id_card', 1, 0, 0, 0, '2018-07-06 10:41:49'),
(250, 19, 'Daily Assignment', 'daily_assignment', 1, 0, 0, 0, '2022-03-02 07:28:23'),
(251, 6, 'Marks Division', 'marks_division', 1, 1, 1, 1, '2022-07-01 15:24:16'),
(252, 13, 'Schedule Email SMS Log', 'schedule_email_sms_log', 1, 0, 1, 0, '2022-07-09 11:25:16'),
(253, 13, 'Login Credentials Send', 'login_credentials_send', 1, 0, 0, 0, '2022-07-01 15:46:10'),
(254, 13, 'Email Template', 'email_template', 1, 1, 1, 1, '2022-07-01 15:46:10'),
(255, 13, 'SMS Template', 'sms_template', 1, 1, 1, 1, '2022-07-01 15:46:10'),
(256, 14, 'Balance Fees Report With Remark', 'balance_fees_report_with_remark', 1, 0, 0, 0, '2019-10-25 01:55:52'),
(257, 14, 'Balance Fees Statement', 'balance_fees_statement', 1, 0, 0, 0, '2019-10-25 01:55:52'),
(258, 14, 'Daily Collection Report', 'daily_collection_report', 1, 0, 0, 0, '2019-10-25 01:55:52'),
(259, 11, 'Fees Master', 'transport_fees_master', 1, 0, 1, 0, '2022-07-05 09:29:19'),
(260, 11, 'Pickup Point', 'pickup_point', 1, 1, 1, 1, '2022-07-04 09:50:08'),
(261, 11, 'Route Pickup Point', 'route_pickup_point', 1, 1, 1, 1, '2022-07-04 09:50:08'),
(262, 11, 'Student Transport Fees', 'student_transport_fees', 1, 1, 1, 0, '2022-07-05 10:15:55'),
(263, 29, 'Comments', 'lesson_plan_comments', 1, 1, 0, 1, '2020-05-28 22:20:11'),
(264, 15, 'Sidebar Menu', 'sidebar_menu', 1, 0, 0, 0, '2022-07-11 12:01:17'),
(265, 15, 'Currency', 'currency', 1, 0, 0, 0, '2020-08-21 05:36:33'),
(266, 6, 'Exam Schedule', 'exam_schedule', 1, 0, 0, 0, '2019-11-23 23:58:50'),
(267, 6, 'Generate Rank', 'generate_rank', 1, 0, 0, 0, '2019-11-24 05:15:04'),
(268, 8, 'Content Type', 'content_type', 1, 1, 1, 1, '2022-07-08 05:18:54'),
(269, 8, 'Content Share List', 'content_share_list', 1, 0, 0, 1, '2022-07-08 05:18:58'),
(270, 8, 'Video Tutorial', 'video_tutorial', 1, 1, 1, 1, '2022-07-08 05:19:01'),
(271, 15, 'Currency Switcher', 'currency_switcher', 1, 0, 0, 0, '2019-11-24 05:17:11'),
(272, 2, 'Offline Bank Payments', 'offline_bank_payments', 1, 0, 0, 0, '2018-06-27 00:18:15'),
(273, 29, 'Copy Old Lessons', 'copy_old_lesson', 1, 0, 0, 0, '2020-05-28 22:20:11'),
(5001, 500, 'Setting', 'setting', 1, 0, 1, 0, '2020-06-10 20:39:04'),
(5002, 500, 'Live Classes', 'live_classes', 1, 1, 0, 1, '2020-05-31 22:41:32'),
(5003, 500, 'Live Meeting', 'live_meeting', 1, 1, 0, 1, '2020-06-01 19:41:41'),
(5004, 500, 'Live Meeting Report', 'live_meeting_report', 1, 0, 0, 0, '2020-06-10 12:07:40'),
(5005, 500, 'Live Classes Report', 'live_classes_report', 1, 0, 0, 0, '2020-06-10 13:29:53'),
(6001, 600, 'Live Classes', 'gmeet_live_classes', 1, 1, 0, 1, '2020-09-22 17:03:29'),
(6002, 600, 'Live Meeting', 'gmeet_live_meeting', 1, 1, 0, 1, '2020-09-22 17:03:44'),
(6003, 600, 'Live Meeting Report', 'gmeet_live_meeting_report', 1, 0, 0, 0, '2020-09-22 17:03:57'),
(6004, 600, 'Live Classes Report', 'gmeet_live_classes_report', 1, 0, 0, 0, '2020-09-22 17:04:08'),
(6005, 600, 'Setting', 'gmeet_setting', 1, 0, 1, 0, '2020-09-22 17:04:08'),
(7001, 700, 'Online Course', 'online_course', 1, 1, 1, 1, '2019-11-24 01:25:18'),
(7002, 700, 'Course Publish', 'course_publish', 1, 0, 0, 0, '2019-11-24 01:25:18'),
(7003, 700, 'Course Section', 'online_course_section', 1, 1, 1, 1, '2021-05-17 12:26:33'),
(7004, 700, 'Course Lesson', 'online_course_lesson', 1, 1, 1, 1, '2021-05-17 12:26:24'),
(7005, 700, 'Course Quiz', 'online_course_quiz', 1, 1, 1, 1, '2021-05-17 12:26:20'),
(7006, 700, 'Offline Payment', 'online_course_offline_payment', 1, 1, 0, 0, '2021-05-17 12:26:17'),
(7007, 700, 'Student Course Purchase Report', 'student_course_purchase_report', 1, 0, 0, 0, '2021-05-17 12:25:56'),
(7008, 700, 'Course Sell Count Report', 'course_sell_count_report', 1, 0, 0, 0, '2021-05-17 12:25:52'),
(7009, 700, 'Course Trending Report', 'course_trending_report', 1, 0, 0, 0, '2021-05-17 12:25:49'),
(7010, 700, 'Course Complete Report', 'course_complete_report', 1, 0, 0, 0, '2021-05-17 12:25:42'),
(7011, 700, 'Setting', 'online_course_setting', 1, 0, 0, 0, '2021-05-17 12:25:37'),
(7012, 700, 'Course Rating Report', 'course_rating_report', 1, 0, 0, 1, '2022-06-14 18:24:57'),
(7013, 700, 'Guest Report', 'guest_report', 1, 0, 1, 1, '2022-06-14 18:33:09'),
(7014, 700, 'Course Category', 'course_category', 1, 1, 1, 1, '2019-11-24 01:25:18'),
(8001, 800, 'Behaviour Records Assign Incident', 'behaviour_records_assign_incident', 1, 1, 0, 1, '2022-05-03 15:56:15'),
(8002, 800, 'Behaviour Records Incident', 'behaviour_records_incident', 1, 1, 1, 1, '2022-05-03 14:25:10'),
(8003, 800, 'Student Incident Report', 'student_incident_report', 1, 0, 0, 0, '2022-05-03 14:24:48'),
(8004, 800, 'Student Behaviour Rank Report', 'student_behaviour_rank_report', 1, 0, 0, 0, '2022-05-03 14:24:45'),
(8005, 800, 'Class Wise Rank Report', 'class_wise_rank_report', 1, 0, 0, 0, '2022-05-03 14:24:37'),
(8006, 800, 'Class Section Wise Rank Report', 'class_section_wise_rank_report', 1, 0, 0, 0, '2022-05-03 14:24:33'),
(8007, 800, 'House Wise Rank Report', 'house_wise_rank_report', 1, 0, 0, 0, '2022-05-03 14:24:30'),
(8008, 800, 'Incident Wise Report', 'incident_wise_report', 1, 0, 0, 0, '2022-05-03 14:24:26'),
(8009, 800, 'Behaviour Records Setting', 'behaviour_records_setting', 1, 0, 1, 0, '2022-05-03 14:24:05'),
(9001, 900, 'CBSE Exam', 'cbse_exam', 1, 1, 1, 1, '2022-11-03 15:58:30'),
(9002, 900, 'CBSE Exam Schedule', 'cbse_exam_schedule', 1, 0, 0, 0, '2023-05-09 18:01:34'),
(9003, 900, 'CBSE Exam Assign / View Student', 'cbse_exam_assign_view_student', 1, 0, 1, 0, '2022-11-03 16:18:15'),
(9004, 900, 'CBSE Exam Subjects', 'cbse_exam_subjects', 1, 0, 1, 0, '2022-11-04 15:01:41'),
(9005, 900, 'CBSE Exam Marks', 'cbse_exam_marks', 1, 0, 1, 0, '2022-11-03 16:18:24'),
(9006, 900, 'CBSE Exam Attendance', 'cbse_exam_attendance', 1, 0, 1, 0, '2022-11-03 16:18:28'),
(9007, 900, 'CBSE Exam Teacher Remark', 'cbse_exam_teacher_remark', 1, 0, 1, 0, '2022-11-03 16:18:32'),
(9008, 900, 'CBSE Exam Print Marksheet', 'cbse_exam_print_marksheet', 1, 0, 0, 0, '2022-11-03 16:18:43'),
(9009, 900, 'CBSE Exam Grade', 'cbse_exam_grade', 1, 1, 1, 1, '2022-11-03 16:18:46'),
(9010, 900, 'CBSE Exam Assign Observation', 'cbse_exam_assign_observation', 1, 1, 1, 1, '2023-05-08 19:33:23'),
(9011, 900, 'CBSE Exam Observation', 'cbse_exam_observation', 1, 1, 1, 1, '2023-05-09 17:57:16'),
(9012, 900, 'CBSE Exam Observation Parameter', 'cbse_exam_observation_parameter', 1, 1, 1, 1, '2023-05-09 18:01:54'),
(9013, 900, 'CBSE Exam Assessment', 'cbse_exam_assessment', 1, 1, 1, 1, '2023-05-09 18:01:51'),
(9014, 900, 'CBSE Exam Term', 'cbse_exam_term', 1, 1, 1, 1, '2023-05-09 18:01:47'),
(9015, 900, 'CBSE Exam Template', 'cbse_exam_template', 1, 1, 1, 1, '2023-05-09 18:01:43'),
(9016, 900, 'CBSE Exam Link Exam', 'cbse_exam_link_exam', 1, 0, 0, 0, '2023-05-09 18:01:40'),
(9017, 900, 'CBSE Exam Subject Marks Report', 'subject_marks_report', 1, 0, 0, 0, '2023-05-09 18:01:38'),
(9018, 900, 'CBSE Exam Template Marks Report', 'template_marks_report', 1, 0, 0, 0, '2023-05-09 18:01:34'),
(9019, 900, 'CBSE Exam Setting', 'cbse_exam_setting', 1, 0, 0, 0, '2023-07-03 12:24:57'),
(9020, 900, 'CBSE Exam Generate Rank', 'cbse_exam_generate_rank', 1, 0, 0, 0, '2023-07-03 12:24:57'),
(11001, 1100, 'Setting', 'google_authenticate_setting', 1, 0, 0, 0, '2018-07-06 17:41:49'),
(11002, 1100, 'Setup 2FA', 'google_authenticate_setup_two_fa', 1, 0, 0, 0, '2018-07-06 17:41:49'),
(12001, 1200, 'Attendance', 'qr_code_attendance', 1, 0, 0, 0, '2023-12-11 19:34:11'),
(12002, 1200, 'Setting', 'qr_code_setting', 1, 0, 0, 0, '2023-12-11 19:34:25');

-- --------------------------------------------------------

--
-- Table structure for table `permission_group`
--

CREATE TABLE `permission_group` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `short_code` varchar(100) NOT NULL,
  `is_active` int(11) DEFAULT 0,
  `system` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `permission_group`
--

INSERT INTO `permission_group` (`id`, `name`, `short_code`, `is_active`, `system`, `created_at`) VALUES
(1, 'Student Information', 'student_information', 1, 1, '2019-03-15 09:30:22'),
(2, 'Fees Collection', 'fees_collection', 1, 0, '2020-06-11 00:51:35'),
(3, 'Income', 'income', 1, 0, '2020-06-01 01:57:39'),
(4, 'Expense', 'expense', 1, 0, '2019-03-15 09:06:22'),
(5, 'Student Attendance', 'student_attendance', 1, 0, '2018-07-02 07:48:08'),
(6, 'Examination', 'examination', 1, 0, '2018-07-11 02:49:08'),
(7, 'Academics', 'academics', 1, 1, '2018-07-02 07:25:43'),
(8, 'Download Center', 'download_center', 1, 0, '2018-07-02 07:49:29'),
(9, 'Library', 'library', 1, 0, '2018-06-28 11:13:14'),
(10, 'Inventory', 'inventory', 1, 0, '2018-06-27 00:48:58'),
(11, 'Transport', 'transport', 1, 0, '2018-06-27 07:51:26'),
(12, 'Hostel', 'hostel', 1, 0, '2018-07-02 07:49:32'),
(13, 'Communicate', 'communicate', 1, 0, '2018-07-02 07:50:00'),
(14, 'Reports', 'reports', 1, 1, '2018-06-27 03:40:22'),
(15, 'System Settings', 'system_settings', 1, 1, '2018-06-27 03:40:28'),
(16, 'Front CMS', 'front_cms', 1, 0, '2018-07-10 05:16:54'),
(17, 'Front Office', 'front_office', 1, 0, '2018-06-27 03:45:30'),
(18, 'Human Resource', 'human_resource', 1, 1, '2018-06-27 03:41:02'),
(19, 'Homework', 'homework', 1, 0, '2018-06-27 00:49:38'),
(20, 'Certificate', 'certificate', 1, 0, '2018-06-27 07:51:29'),
(21, 'Calendar To Do List', 'calendar_to_do_list', 1, 0, '2019-03-15 09:06:25'),
(22, 'Dashboard and Widgets', 'dashboard_and_widgets', 1, 1, '2018-06-27 03:41:17'),
(23, 'Online Examination', 'online_examination', 1, 0, '2020-06-01 02:25:36'),
(25, 'Chat', 'chat', 1, 0, '2019-11-23 23:54:04'),
(26, 'Multi Class', 'multi_class', 1, 0, '2019-11-27 12:14:14'),
(27, 'Online Admission', 'online_admission', 1, 0, '2019-11-27 02:42:13'),
(28, 'Alumni', 'alumni', 1, 0, '2020-05-29 00:26:38'),
(29, 'Lesson Plan', 'lesson_plan', 1, 0, '2020-06-07 05:38:30'),
(500, 'Zoom Live Classes', 'zoom_live_classes', 1, 0, '2020-06-10 20:37:23'),
(600, 'Gmeet Live Classes', 'gmeet_live_classes', 1, 0, '2020-11-12 20:37:03'),
(700, 'Online Course', 'online_course', 1, 0, '2021-05-15 18:35:53'),
(800, 'Behaviour Records', 'behaviour_records', 1, 0, '2022-05-03 17:20:33'),
(900, 'CBSE Examination', 'cbseexam', 1, 0, '2023-05-25 19:04:56'),
(1100, 'Two Factor Authenticator', 'google_authenticator', 1, 0, '2025-02-11 12:54:30'),
(1200, 'QR Code Attendence', 'qr_code_attendence', 1, 0, '2023-12-13 16:21:57');

-- --------------------------------------------------------

--
-- Table structure for table `permission_student`
--

CREATE TABLE `permission_student` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `short_code` varchar(100) NOT NULL,
  `system` int(11) NOT NULL,
  `student` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `permission_student`
--

INSERT INTO `permission_student` (`id`, `name`, `short_code`, `system`, `student`, `parent`, `group_id`, `created_at`) VALUES
(1, 'Fees', 'fees', 0, 1, 1, 2, '2020-06-11 00:51:35'),
(2, 'Class Timetable', 'class_timetable', 1, 1, 1, 7, '2020-05-30 19:57:50'),
(3, 'Homework', 'homework', 0, 1, 1, 19, '2020-06-01 02:49:14'),
(4, 'Download Center', 'download_center', 0, 1, 1, 8, '2020-06-01 02:52:49'),
(5, 'Attendance', 'attendance', 0, 1, 1, 5, '2020-06-01 02:57:18'),
(7, 'Examinations', 'examinations', 0, 1, 1, 6, '2020-06-01 02:59:50'),
(8, 'Notice Board', 'notice_board', 0, 1, 1, 13, '2020-06-01 03:00:35'),
(11, 'Library', 'library', 0, 1, 1, 9, '2020-06-01 03:02:37'),
(12, 'Transport Routes', 'transport_routes', 0, 1, 1, 11, '2020-06-01 03:51:30'),
(13, 'Hostel Rooms', 'hostel_rooms', 0, 1, 1, 12, '2020-06-01 03:52:27'),
(14, 'Calendar To Do List', 'calendar_to_do_list', 0, 1, 1, 21, '2020-06-01 03:53:18'),
(15, 'Online Examination', 'online_examination', 0, 1, 1, 23, '2020-06-11 05:20:01'),
(16, 'Teachers Rating', 'teachers_rating', 0, 1, 1, NULL, '2022-12-28 09:52:28'),
(17, 'Chat', 'chat', 0, 1, 1, 25, '2020-06-01 04:53:06'),
(18, 'Multi Class', 'multi_class', 1, 1, 1, 26, '2020-05-30 19:56:52'),
(19, 'Lesson Plan', 'lesson_plan', 0, 1, 1, 29, '2020-06-07 05:38:30'),
(20, 'Syllabus Status', 'syllabus_status', 0, 1, 1, 29, '2020-06-07 05:38:30'),
(23, 'Apply Leave', 'apply_leave', 0, 1, 1, NULL, '2022-12-28 09:52:28'),
(24, 'Visitor Book', 'visitor_book', 0, 1, 1, NULL, '2022-10-10 11:45:18'),
(25, 'Student Timeline', 'student_timeline', 0, 1, 1, NULL, '2022-10-11 04:50:29'),
(500, 'Zoom Live Classes', 'live_classes', 0, 1, 1, 500, '2024-11-23 11:51:53'),
(600, 'Gmeet Live Classes ', 'gmeet_live_classes', 0, 1, 1, 600, '2020-11-12 20:37:03'),
(700, 'Online Course', 'online_course', 0, 1, 1, 700, '2021-05-15 18:35:53'),
(800, 'Behaviour Records', 'behaviour_records', 0, 1, 1, 800, '2022-05-03 17:20:33'),
(900, 'CBSE Examination', 'cbseexam', 0, 1, 1, 900, '2023-05-25 19:07:15');

-- --------------------------------------------------------

--
-- Table structure for table `pickup_point`
--

CREATE TABLE `pickup_point` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `latitude` varchar(100) DEFAULT NULL,
  `longitude` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `pickup_point`
--

INSERT INTO `pickup_point` (`id`, `name`, `latitude`, `longitude`, `created_at`) VALUES
(1, 'Brooklyn North', '23.21953720694318,', '79.92068396109676', '2024-11-25 08:55:19'),
(2, 'Brooklyn South', '23.204781722973813', '79.89751486729702', '2024-11-25 08:55:44'),
(3, 'Brooklyn West', '23.19324172886614', '79.91536320113687', '2024-11-25 08:56:15'),
(4, 'Brooklyn East', '23.193952567195506', '79.9243812546212', '2024-11-25 08:56:33'),
(5, 'Manhattan', '23.2066336875236', '80.00451042401824', '2024-11-25 08:57:04'),
(6, 'civil Line', '23.166120045559563', '79.95531910260692', '2024-11-25 08:57:31'),
(7, 'Ranital Chowk', '23.170504563243085', '79.92385377983044', '2024-11-25 08:57:53');

-- --------------------------------------------------------

--
-- Table structure for table `print_headerfooter`
--

CREATE TABLE `print_headerfooter` (
  `id` int(11) NOT NULL,
  `print_type` varchar(255) NOT NULL,
  `header_image` varchar(255) NOT NULL,
  `footer_content` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `entry_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `print_headerfooter`
--

INSERT INTO `print_headerfooter` (`id`, `print_type`, `header_image`, `footer_content`, `created_by`, `entry_date`, `created_at`) VALUES
(1, 'staff_payslip', 'header_image.jpg', 'This payslip is computer generated hence no signature is required.', 1, '2020-02-28 15:41:08', '2022-12-28 09:52:24'),
(2, 'student_receipt', 'header_image.jpg', 'This receipt is computer generated hence no signature is required.', 1, '2020-02-28 15:40:58', '2022-12-28 09:52:24'),
(3, 'online_admission_receipt', 'header_image.jpg', 'This receipt is for online admission  computer ffffffff generated hence no signature is required.', 1, '2021-05-27 12:50:24', '2022-12-28 09:52:24'),
(4, 'online_exam', '1655913577-198504634062b33c698fde1!online-exam.jpg', 'This receipt is for online exam computer  generated hence no signature is required.', 1, '2022-08-30 12:58:46', '2022-09-08 17:28:34');

-- --------------------------------------------------------

--
-- Table structure for table `QR_code_settings`
--

CREATE TABLE `QR_code_settings` (
  `id` int(11) NOT NULL,
  `camera_type` varchar(15) DEFAULT NULL,
  `auto_attendance` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `QR_code_settings`
--

INSERT INTO `QR_code_settings` (`id`, `camera_type`, `auto_attendance`, `created_at`) VALUES
(1, 'user', 1, '2024-11-23 11:48:07');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `question_type` varchar(100) NOT NULL,
  `level` varchar(10) NOT NULL,
  `class_id` int(11) NOT NULL,
  `section_id` int(11) DEFAULT NULL,
  `class_section_id` int(11) DEFAULT NULL,
  `question` text DEFAULT NULL,
  `opt_a` text DEFAULT NULL,
  `opt_b` text DEFAULT NULL,
  `opt_c` text DEFAULT NULL,
  `opt_d` text DEFAULT NULL,
  `opt_e` text DEFAULT NULL,
  `correct` text DEFAULT NULL,
  `descriptive_word_limit` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `staff_id`, `subject_id`, `question_type`, `level`, `class_id`, `section_id`, `class_section_id`, `question`, `opt_a`, `opt_b`, `opt_c`, `opt_d`, `opt_e`, `correct`, `descriptive_word_limit`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'singlechoice', 'low', 1, 1, NULL, 'question testttt 2', 'question testttt 2', 'question testttt 2', '', '', '', 'opt_a', 0, '2024-11-25 11:04:18', NULL),
(2, 1, 2, 'descriptive', 'low', 1, 4, NULL, 'question testttt 2&nbsp;question testttt 2question testttt 2&nbsp;', '', '', '', '', '', '', 0, '2024-11-25 11:04:53', NULL),
(3, 1, 3, 'singlechoice', 'medium', 2, 3, NULL, '1.Computer quiz', '2.computer quiz', '3.computer quiz', '4.computer quiz', '', '', 'opt_b', 0, '2024-12-09 11:33:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `read_notification`
--

CREATE TABLE `read_notification` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `parent_id` int(10) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `notification_id` int(11) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `read_notification`
--

INSERT INTO `read_notification` (`id`, `student_id`, `parent_id`, `staff_id`, `notification_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, 1, 'no', '2024-12-10 08:45:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reference`
--

CREATE TABLE `reference` (
  `id` int(11) NOT NULL,
  `reference` varchar(100) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `reference`
--

INSERT INTO `reference` (`id`, `reference`, `description`) VALUES
(1, 'staff', ''),
(2, 'parent', ''),
(3, 'student', ''),
(4, 'self', ''),
(5, 'Lower Wing	', ''),
(6, 'Partner School	', '');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `is_active` int(11) DEFAULT 0,
  `is_system` int(1) NOT NULL DEFAULT 0,
  `is_superadmin` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `is_active`, `is_system`, `is_superadmin`, `created_at`, `updated_at`) VALUES
(1, 'Admin', NULL, 0, 1, 0, '2018-06-30 15:39:11', '0000-00-00'),
(2, 'Teacher', NULL, 0, 1, 0, '2018-06-30 15:39:14', '0000-00-00'),
(3, 'Accountant', NULL, 0, 1, 0, '2018-06-30 15:39:17', '0000-00-00'),
(4, 'Librarian', NULL, 0, 1, 0, '2018-06-30 15:39:21', '0000-00-00'),
(6, 'Receptionist', NULL, 0, 1, 0, '2018-07-02 05:39:03', '0000-00-00'),
(7, 'Super Admin', NULL, 0, 1, 1, '2018-07-11 14:11:29', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `roles_permissions`
--

CREATE TABLE `roles_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `perm_cat_id` int(11) DEFAULT NULL,
  `can_view` int(11) DEFAULT NULL,
  `can_add` int(11) DEFAULT NULL,
  `can_edit` int(11) DEFAULT NULL,
  `can_delete` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `roles_permissions`
--

INSERT INTO `roles_permissions` (`id`, `role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`, `created_at`) VALUES
(11, 1, 78, 1, 1, 1, 1, '2018-07-03 00:49:43'),
(23, 1, 12, 1, 1, 1, 1, '2018-07-06 09:45:38'),
(24, 1, 13, 1, 1, 1, 1, '2018-07-06 09:48:28'),
(26, 1, 15, 1, 1, 1, 0, '2019-11-27 23:47:28'),
(31, 1, 21, 1, 0, 1, 0, '2019-11-26 04:51:15'),
(34, 1, 24, 1, 1, 1, 1, '2019-11-28 06:35:20'),
(43, 1, 32, 1, 1, 1, 1, '2018-07-06 10:22:05'),
(44, 1, 33, 1, 1, 1, 1, '2018-07-06 10:22:29'),
(45, 1, 34, 1, 1, 1, 1, '2018-07-06 10:23:59'),
(46, 1, 35, 1, 1, 1, 1, '2018-07-06 10:24:34'),
(47, 1, 104, 1, 1, 1, 1, '2018-07-06 10:23:08'),
(48, 1, 37, 1, 1, 1, 1, '2018-07-06 10:25:30'),
(49, 1, 38, 1, 1, 1, 1, '2018-07-09 05:15:27'),
(61, 1, 55, 1, 1, 1, 1, '2018-07-02 09:24:16'),
(67, 1, 61, 1, 1, 0, 1, '2018-07-09 05:59:19'),
(68, 1, 62, 1, 1, 0, 1, '2018-07-09 05:59:19'),
(69, 1, 63, 1, 1, 0, 1, '2018-07-09 03:51:38'),
(70, 1, 64, 1, 1, 1, 1, '2018-07-09 03:02:19'),
(71, 1, 65, 1, 1, 1, 1, '2018-07-09 03:11:21'),
(72, 1, 66, 1, 1, 1, 1, '2018-07-09 03:13:09'),
(73, 1, 67, 1, 1, 1, 1, '2018-07-09 03:14:47'),
(74, 1, 79, 1, 1, 0, 1, '2019-11-30 01:32:51'),
(75, 1, 80, 1, 1, 1, 1, '2018-07-06 09:41:23'),
(76, 1, 81, 1, 1, 1, 1, '2018-07-06 09:41:23'),
(78, 1, 83, 1, 1, 1, 1, '2018-07-06 09:41:23'),
(79, 1, 84, 1, 1, 1, 1, '2018-07-06 09:41:23'),
(80, 1, 85, 1, 1, 1, 1, '2018-07-12 00:16:00'),
(94, 1, 82, 1, 1, 1, 1, '2018-07-06 09:41:23'),
(120, 1, 39, 1, 1, 1, 1, '2018-07-06 10:26:28'),
(156, 1, 9, 1, 1, 1, 1, '2019-11-27 23:45:46'),
(157, 1, 10, 1, 1, 1, 1, '2019-11-27 23:45:46'),
(159, 1, 40, 1, 1, 1, 1, '2019-11-30 00:49:39'),
(160, 1, 41, 1, 1, 1, 1, '2019-12-02 05:43:41'),
(161, 1, 42, 1, 1, 1, 1, '2019-11-30 00:49:39'),
(169, 1, 27, 1, 1, 0, 1, '2019-11-29 06:15:37'),
(178, 1, 54, 1, 0, 1, 0, '2018-07-05 09:09:22'),
(179, 1, 56, 1, 0, 1, 0, '2019-11-30 00:49:54'),
(180, 1, 57, 1, 0, 1, 0, '2019-11-30 01:32:51'),
(181, 1, 58, 1, 0, 1, 0, '2019-11-30 01:32:51'),
(182, 1, 59, 1, 0, 1, 0, '2019-11-30 01:32:51'),
(183, 1, 60, 1, 0, 1, 0, '2019-11-30 00:59:57'),
(201, 1, 14, 1, 0, 0, 0, '2018-07-02 11:22:03'),
(204, 1, 26, 1, 0, 0, 0, '2018-07-02 11:32:05'),
(206, 1, 29, 1, 0, 0, 0, '2018-07-02 11:43:54'),
(207, 1, 30, 1, 0, 0, 0, '2018-07-02 11:43:54'),
(208, 1, 31, 1, 1, 1, 1, '2019-11-30 01:32:51'),
(222, 1, 1, 1, 1, 1, 1, '2019-11-27 22:55:06'),
(307, 1, 126, 1, 0, 0, 0, '2018-07-03 09:26:13'),
(315, 1, 123, 1, 0, 0, 0, '2018-07-03 10:27:03'),
(369, 1, 102, 1, 1, 1, 1, '2019-12-02 05:02:15'),
(435, 1, 96, 1, 1, 1, 1, '2018-07-09 01:03:54'),
(461, 1, 97, 1, 0, 0, 0, '2018-07-09 01:00:16'),
(464, 1, 86, 1, 1, 1, 1, '2019-11-28 06:39:19'),
(474, 1, 130, 1, 1, 0, 1, '2018-07-09 10:56:36'),
(476, 1, 131, 1, 0, 0, 0, '2018-07-09 04:53:32'),
(557, 6, 82, 1, 1, 1, 1, '2019-12-01 01:48:28'),
(558, 6, 83, 1, 1, 1, 1, '2019-12-01 01:49:08'),
(559, 6, 84, 1, 1, 1, 1, '2019-12-01 01:49:59'),
(575, 6, 44, 1, 0, 0, 0, '2018-07-10 07:35:33'),
(576, 6, 46, 1, 0, 0, 0, '2018-07-10 07:35:33'),
(578, 6, 102, 1, 1, 1, 1, '2019-12-01 01:52:27'),
(625, 1, 28, 1, 1, 1, 1, '2019-11-29 06:19:18'),
(634, 4, 102, 1, 1, 1, 1, '2019-12-01 01:03:00'),
(669, 1, 145, 1, 0, 0, 0, '2019-11-26 04:51:15'),
(677, 1, 153, 1, 0, 0, 0, '2019-11-01 02:28:24'),
(720, 1, 216, 1, 0, 0, 0, '2019-11-26 05:24:12'),
(728, 1, 185, 1, 1, 1, 1, '2019-11-28 02:50:33'),
(729, 1, 186, 1, 1, 1, 1, '2019-11-28 02:49:07'),
(730, 1, 214, 1, 0, 1, 0, '2019-11-28 01:47:53'),
(732, 1, 198, 1, 0, 0, 0, '2019-11-26 05:24:30'),
(734, 1, 200, 1, 0, 0, 0, '2019-11-26 05:24:30'),
(735, 1, 201, 1, 0, 0, 0, '2019-11-26 05:24:30'),
(736, 1, 202, 1, 0, 0, 0, '2019-11-26 05:24:30'),
(737, 1, 203, 1, 0, 0, 0, '2019-11-26 05:24:30'),
(747, 1, 2, 1, 0, 0, 0, '2019-11-27 22:56:08'),
(748, 1, 3, 1, 1, 1, 1, '2019-11-27 22:56:32'),
(749, 1, 4, 1, 1, 1, 1, '2019-11-27 22:56:48'),
(751, 1, 128, 0, 1, 0, 1, '2019-11-27 22:57:01'),
(754, 1, 134, 1, 1, 1, 1, '2019-11-27 23:18:21'),
(755, 1, 5, 1, 1, 0, 1, '2019-11-27 23:35:07'),
(756, 1, 6, 1, 0, 0, 0, '2019-11-27 23:35:25'),
(757, 1, 7, 1, 1, 1, 1, '2019-11-27 23:36:35'),
(758, 1, 8, 1, 1, 1, 1, '2019-11-27 23:37:27'),
(760, 1, 68, 1, 0, 0, 0, '2019-11-27 23:38:06'),
(761, 1, 69, 1, 1, 1, 1, '2019-11-27 23:39:06'),
(762, 1, 70, 1, 1, 1, 1, '2019-11-27 23:39:41'),
(763, 1, 71, 1, 0, 0, 0, '2019-11-27 23:39:59'),
(765, 1, 73, 1, 0, 0, 0, '2019-11-27 23:43:15'),
(766, 1, 74, 1, 0, 0, 0, '2019-11-27 23:43:55'),
(768, 1, 11, 1, 0, 0, 0, '2019-11-27 23:45:46'),
(769, 1, 122, 1, 0, 0, 0, '2019-11-27 23:52:43'),
(771, 1, 136, 1, 0, 0, 0, '2019-11-27 23:55:36'),
(772, 1, 20, 1, 1, 1, 1, '2019-11-28 04:06:44'),
(773, 1, 137, 1, 1, 1, 1, '2019-11-28 00:46:14'),
(774, 1, 141, 1, 1, 1, 1, '2019-11-28 00:59:42'),
(775, 1, 142, 1, 0, 0, 0, '2019-11-27 23:56:12'),
(776, 1, 143, 1, 1, 1, 1, '2019-11-28 00:59:42'),
(777, 1, 144, 1, 0, 0, 0, '2019-11-27 23:56:12'),
(778, 1, 187, 1, 0, 0, 0, '2019-11-27 23:56:12'),
(779, 1, 196, 1, 0, 0, 0, '2019-11-27 23:56:12'),
(781, 1, 207, 1, 0, 0, 0, '2019-11-27 23:56:12'),
(782, 1, 208, 1, 0, 1, 0, '2019-11-28 00:10:22'),
(783, 1, 210, 1, 0, 1, 0, '2019-11-28 00:34:40'),
(784, 1, 211, 1, 0, 1, 0, '2019-11-28 00:38:23'),
(785, 1, 212, 1, 0, 1, 0, '2019-11-28 00:42:15'),
(786, 1, 205, 1, 1, 1, 1, '2019-11-28 00:42:15'),
(787, 1, 222, 1, 0, 1, 0, '2019-11-28 01:36:36'),
(788, 1, 77, 1, 1, 1, 1, '2019-11-28 06:22:10'),
(789, 1, 188, 1, 1, 1, 1, '2019-11-28 06:26:16'),
(790, 1, 23, 1, 1, 1, 1, '2019-11-28 06:34:20'),
(791, 1, 25, 1, 1, 1, 1, '2019-11-28 06:36:20'),
(792, 1, 127, 1, 0, 0, 0, '2019-11-28 06:41:25'),
(794, 1, 88, 1, 1, 1, 0, '2019-11-28 06:43:04'),
(795, 1, 90, 1, 1, 0, 1, '2019-11-28 06:46:22'),
(796, 1, 108, 1, 0, 1, 1, '2021-01-23 07:09:32'),
(797, 1, 109, 1, 1, 0, 0, '2019-11-28 23:38:11'),
(798, 1, 110, 1, 1, 1, 1, '2019-11-28 23:49:29'),
(799, 1, 111, 1, 1, 1, 1, '2019-11-28 23:49:57'),
(800, 1, 112, 1, 1, 1, 1, '2019-11-28 23:49:57'),
(801, 1, 129, 0, 1, 0, 1, '2019-11-28 23:49:57'),
(802, 1, 189, 1, 0, 1, 1, '2019-11-28 23:59:22'),
(810, 2, 1, 1, 1, 1, 1, '2019-11-30 02:54:16'),
(817, 1, 93, 1, 1, 1, 1, '2019-11-29 00:56:14'),
(825, 1, 87, 1, 0, 0, 0, '2019-11-29 00:56:14'),
(829, 1, 94, 1, 1, 0, 0, '2019-11-29 00:57:57'),
(836, 1, 146, 1, 0, 0, 0, '2019-11-29 01:13:28'),
(837, 1, 147, 1, 0, 0, 0, '2019-11-29 01:13:28'),
(838, 1, 148, 1, 0, 0, 0, '2019-11-29 01:13:28'),
(839, 1, 149, 1, 0, 0, 0, '2019-11-29 01:13:28'),
(840, 1, 150, 1, 0, 0, 0, '2019-11-29 01:13:28'),
(841, 1, 151, 1, 0, 0, 0, '2019-11-29 01:13:28'),
(842, 1, 152, 1, 0, 0, 0, '2019-11-29 01:13:28'),
(843, 1, 154, 1, 0, 0, 0, '2019-11-29 01:13:28'),
(862, 1, 155, 1, 0, 0, 0, '2019-11-29 02:07:30'),
(863, 1, 156, 1, 0, 0, 0, '2019-11-29 02:07:52'),
(864, 1, 157, 1, 0, 0, 0, '2019-11-29 02:08:05'),
(874, 1, 158, 1, 0, 0, 0, '2019-11-29 02:14:03'),
(875, 1, 159, 1, 0, 0, 0, '2019-11-29 02:14:31'),
(876, 1, 160, 1, 0, 0, 0, '2019-11-29 02:14:44'),
(878, 1, 162, 1, 0, 0, 0, '2019-11-29 02:15:58'),
(879, 1, 163, 1, 0, 0, 0, '2019-11-29 02:16:19'),
(882, 1, 164, 1, 0, 0, 0, '2019-11-29 02:25:17'),
(884, 1, 165, 1, 0, 0, 0, '2019-11-29 02:25:30'),
(886, 1, 197, 1, 0, 0, 0, '2019-11-29 02:25:48'),
(887, 1, 219, 1, 0, 0, 0, '2019-11-29 02:26:05'),
(889, 1, 220, 1, 0, 0, 0, '2019-11-29 02:26:22'),
(932, 1, 204, 1, 0, 0, 0, '2019-11-29 03:43:27'),
(933, 1, 221, 1, 0, 0, 0, '2019-11-29 03:45:04'),
(934, 1, 178, 1, 0, 0, 0, '2019-11-29 03:45:16'),
(935, 1, 179, 1, 0, 0, 0, '2019-11-29 03:45:33'),
(936, 1, 161, 1, 0, 0, 0, '2019-11-29 03:45:48'),
(937, 1, 180, 1, 0, 0, 0, '2019-11-29 03:45:48'),
(938, 1, 181, 1, 0, 0, 0, '2019-11-29 03:49:33'),
(939, 1, 182, 1, 0, 0, 0, '2019-11-29 03:49:45'),
(940, 1, 183, 1, 0, 0, 0, '2019-11-29 03:49:56'),
(941, 1, 174, 1, 0, 0, 0, '2019-11-29 03:50:53'),
(943, 1, 176, 1, 0, 0, 0, '2019-11-29 03:52:10'),
(944, 1, 177, 1, 0, 0, 0, '2019-11-29 03:52:22'),
(945, 1, 53, 0, 1, 0, 1, '2021-01-23 07:09:32'),
(946, 1, 215, 1, 0, 0, 0, '2019-11-29 04:01:37'),
(947, 1, 213, 1, 0, 0, 0, '2019-11-29 04:07:45'),
(974, 1, 224, 1, 0, 0, 0, '2019-11-29 04:32:52'),
(1026, 1, 135, 1, 0, 1, 0, '2019-11-29 06:02:12'),
(1031, 1, 228, 1, 0, 0, 0, '2019-11-29 06:21:16'),
(1083, 1, 175, 1, 0, 0, 0, '2019-11-30 00:37:24'),
(1086, 1, 43, 1, 1, 1, 1, '2019-11-30 00:49:39'),
(1087, 1, 44, 1, 0, 0, 0, '2019-11-30 00:49:39'),
(1088, 1, 46, 1, 0, 0, 0, '2019-11-30 00:49:39'),
(1089, 1, 217, 1, 0, 0, 0, '2019-11-30 00:49:39'),
(1090, 1, 98, 1, 1, 1, 1, '2019-11-30 01:32:51'),
(1091, 1, 99, 1, 0, 0, 0, '2019-11-30 01:30:18'),
(1092, 1, 223, 1, 0, 0, 0, '2019-11-30 01:32:51'),
(1103, 2, 205, 1, 1, 1, 1, '2019-11-30 01:56:04'),
(1105, 2, 23, 1, 0, 0, 0, '2019-11-30 01:56:04'),
(1106, 2, 24, 1, 0, 0, 0, '2019-11-30 01:56:04'),
(1107, 2, 25, 1, 0, 0, 0, '2019-11-30 01:56:04'),
(1108, 2, 77, 1, 0, 0, 0, '2019-11-30 01:56:04'),
(1119, 2, 117, 1, 0, 0, 0, '2019-11-30 01:56:04'),
(1123, 3, 8, 1, 1, 1, 1, '2019-11-30 06:46:18'),
(1125, 3, 69, 1, 1, 1, 1, '2019-11-30 07:00:49'),
(1126, 3, 70, 1, 1, 1, 1, '2019-11-30 07:04:46'),
(1130, 3, 9, 1, 1, 1, 1, '2019-11-30 07:14:54'),
(1131, 3, 10, 1, 1, 1, 1, '2019-11-30 07:16:02'),
(1134, 3, 35, 1, 1, 1, 1, '2019-11-30 07:25:04'),
(1135, 3, 104, 1, 1, 1, 1, '2019-11-30 07:25:53'),
(1140, 3, 41, 1, 1, 1, 1, '2019-11-30 07:37:13'),
(1141, 3, 42, 1, 1, 1, 1, '2019-11-30 07:37:46'),
(1142, 3, 43, 1, 1, 1, 1, '2019-11-30 07:42:06'),
(1151, 3, 87, 1, 0, 0, 0, '2019-11-30 02:23:13'),
(1152, 3, 88, 1, 1, 1, 0, '2019-11-30 02:23:13'),
(1153, 3, 90, 1, 1, 0, 1, '2019-11-30 02:23:13'),
(1154, 3, 108, 1, 0, 1, 0, '2019-11-30 02:23:13'),
(1155, 3, 109, 1, 1, 0, 0, '2019-11-30 02:23:13'),
(1156, 3, 110, 1, 1, 1, 1, '2019-11-30 02:23:13'),
(1157, 3, 111, 1, 1, 1, 1, '2019-11-30 02:23:13'),
(1158, 3, 112, 1, 1, 1, 1, '2019-11-30 02:23:13'),
(1159, 3, 127, 1, 0, 0, 0, '2019-11-30 02:23:13'),
(1160, 3, 129, 0, 1, 0, 1, '2019-11-30 02:23:13'),
(1161, 3, 102, 1, 1, 1, 1, '2019-11-30 02:23:13'),
(1162, 3, 106, 1, 0, 0, 0, '2019-11-30 02:23:13'),
(1163, 3, 113, 1, 0, 0, 0, '2019-11-30 02:23:13'),
(1164, 3, 114, 1, 0, 0, 0, '2019-11-30 02:23:13'),
(1165, 3, 115, 1, 0, 0, 0, '2019-11-30 02:23:13'),
(1166, 3, 116, 1, 0, 0, 0, '2019-11-30 02:23:13'),
(1167, 3, 117, 1, 0, 0, 0, '2019-11-30 02:23:13'),
(1168, 3, 118, 1, 0, 0, 0, '2019-11-30 02:23:13'),
(1171, 2, 142, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1172, 2, 144, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1179, 2, 212, 1, 0, 1, 0, '2019-11-30 02:36:17'),
(1183, 2, 148, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1184, 2, 149, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1185, 2, 150, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1186, 2, 151, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1187, 2, 152, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1188, 2, 153, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1189, 2, 154, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1190, 2, 197, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1191, 2, 198, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1193, 2, 200, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1194, 2, 201, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1195, 2, 202, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1196, 2, 203, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1197, 2, 219, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1198, 2, 223, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1199, 2, 213, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1201, 2, 230, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1204, 2, 214, 1, 0, 1, 0, '2019-11-30 02:36:17'),
(1206, 2, 224, 1, 0, 0, 0, '2019-11-30 02:36:17'),
(1208, 2, 2, 1, 0, 0, 0, '2019-11-30 02:55:45'),
(1210, 2, 143, 1, 1, 1, 1, '2019-11-30 02:57:28'),
(1211, 2, 145, 1, 0, 0, 0, '2019-11-30 02:57:28'),
(1214, 2, 3, 1, 1, 1, 1, '2019-11-30 03:03:18'),
(1216, 2, 4, 1, 1, 1, 1, '2019-11-30 03:32:56'),
(1218, 2, 128, 0, 1, 0, 1, '2019-11-30 03:37:44'),
(1220, 3, 135, 1, 0, 1, 0, '2019-11-30 07:08:56'),
(1231, 3, 190, 1, 0, 0, 0, '2019-11-30 03:44:02'),
(1232, 3, 192, 1, 0, 0, 0, '2019-11-30 03:44:02'),
(1233, 3, 226, 1, 0, 0, 0, '2019-11-30 03:44:02'),
(1234, 3, 227, 1, 0, 0, 0, '2019-11-30 03:44:02'),
(1235, 3, 224, 1, 0, 0, 0, '2019-11-30 03:44:02'),
(1236, 2, 15, 1, 1, 1, 0, '2019-11-30 03:54:25'),
(1239, 2, 122, 1, 0, 0, 0, '2019-11-30 03:57:48'),
(1240, 2, 136, 1, 0, 0, 0, '2019-11-30 03:57:48'),
(1242, 6, 217, 1, 0, 0, 0, '2019-11-30 04:00:13'),
(1243, 6, 224, 1, 0, 0, 0, '2019-11-30 04:00:13'),
(1245, 2, 20, 1, 1, 1, 1, '2019-11-30 04:01:28'),
(1246, 2, 137, 1, 1, 1, 1, '2019-11-30 04:02:40'),
(1248, 2, 141, 1, 1, 1, 1, '2019-11-30 04:04:04'),
(1250, 2, 187, 1, 0, 0, 0, '2019-11-30 04:11:19'),
(1252, 2, 207, 1, 0, 0, 0, '2019-11-30 04:21:21'),
(1253, 2, 208, 1, 0, 1, 0, '2019-11-30 04:22:00'),
(1255, 2, 210, 1, 0, 1, 0, '2019-11-30 04:22:58'),
(1256, 2, 211, 1, 0, 1, 0, '2019-11-30 04:24:03'),
(1257, 2, 21, 1, 0, 0, 0, '2019-11-30 04:32:59'),
(1259, 2, 188, 1, 0, 0, 0, '2019-11-30 04:34:35'),
(1260, 2, 27, 1, 0, 0, 0, '2019-11-30 04:36:13'),
(1262, 2, 43, 1, 1, 1, 1, '2019-11-30 04:39:42'),
(1263, 2, 44, 1, 0, 0, 0, '2019-11-30 04:41:43'),
(1264, 2, 46, 1, 0, 0, 0, '2019-11-30 04:41:43'),
(1265, 2, 217, 1, 0, 0, 0, '2019-11-30 04:41:43'),
(1266, 2, 146, 1, 0, 0, 0, '2019-11-30 04:46:35'),
(1267, 2, 147, 1, 0, 0, 0, '2019-11-30 04:47:37'),
(1269, 2, 164, 1, 0, 0, 0, '2019-11-30 04:51:04'),
(1271, 2, 109, 1, 1, 0, 0, '2019-11-30 05:03:37'),
(1272, 2, 93, 1, 1, 1, 1, '2019-11-30 05:07:25'),
(1273, 2, 94, 1, 1, 0, 0, '2019-11-30 05:07:42'),
(1275, 2, 102, 1, 1, 1, 1, '2019-11-30 05:11:22'),
(1277, 2, 196, 1, 0, 0, 0, '2019-11-30 05:15:01'),
(1278, 2, 195, 1, 0, 0, 0, '2019-11-30 05:19:08'),
(1279, 2, 185, 1, 1, 1, 1, '2019-11-30 05:21:44'),
(1280, 2, 186, 1, 1, 1, 1, '2019-11-30 05:22:43'),
(1281, 2, 222, 1, 0, 1, 0, '2019-11-30 05:24:30'),
(1283, 3, 5, 1, 1, 0, 1, '2019-11-30 06:43:04'),
(1284, 3, 6, 1, 0, 0, 0, '2019-11-30 06:43:29'),
(1285, 3, 7, 1, 1, 1, 1, '2019-11-30 06:44:39'),
(1286, 3, 68, 1, 0, 0, 0, '2019-11-30 06:46:58'),
(1287, 3, 71, 1, 0, 0, 0, '2019-11-30 07:05:41'),
(1288, 3, 73, 1, 0, 0, 0, '2019-11-30 07:05:59'),
(1289, 3, 74, 1, 0, 0, 0, '2019-11-30 07:06:08'),
(1290, 3, 11, 1, 0, 0, 0, '2019-11-30 07:16:37'),
(1291, 3, 12, 1, 1, 1, 1, '2019-11-30 07:19:29'),
(1292, 3, 13, 1, 1, 1, 1, '2019-11-30 07:22:27'),
(1294, 3, 14, 1, 0, 0, 0, '2019-11-30 07:22:55'),
(1295, 3, 31, 1, 1, 1, 1, '2019-12-02 06:30:37'),
(1297, 3, 37, 1, 1, 1, 1, '2019-11-30 07:28:09'),
(1298, 3, 38, 1, 1, 1, 1, '2019-11-30 07:29:02'),
(1299, 3, 39, 1, 1, 1, 1, '2019-11-30 07:30:07'),
(1300, 3, 40, 1, 1, 1, 1, '2019-11-30 07:32:43'),
(1301, 3, 44, 1, 0, 0, 0, '2019-11-30 07:44:09'),
(1302, 3, 46, 1, 0, 0, 0, '2019-11-30 07:44:09'),
(1303, 3, 217, 1, 0, 0, 0, '2019-11-30 07:44:09'),
(1304, 3, 155, 1, 0, 0, 0, '2019-11-30 07:44:32'),
(1305, 3, 156, 1, 0, 0, 0, '2019-11-30 07:45:18'),
(1306, 3, 157, 1, 0, 0, 0, '2019-11-30 07:45:42'),
(1307, 3, 158, 1, 0, 0, 0, '2019-11-30 07:46:07'),
(1308, 3, 159, 1, 0, 0, 0, '2019-11-30 07:46:21'),
(1309, 3, 160, 1, 0, 0, 0, '2019-11-30 07:46:33'),
(1313, 3, 161, 1, 0, 0, 0, '2019-11-30 07:48:26'),
(1314, 3, 162, 1, 0, 0, 0, '2019-11-30 07:48:48'),
(1315, 3, 163, 1, 0, 0, 0, '2019-11-30 07:48:48'),
(1316, 3, 164, 1, 0, 0, 0, '2019-11-30 07:49:47'),
(1317, 3, 165, 1, 0, 0, 0, '2019-11-30 07:49:47'),
(1318, 3, 174, 1, 0, 0, 0, '2019-11-30 07:49:47'),
(1319, 3, 175, 1, 0, 0, 0, '2019-11-30 07:49:59'),
(1320, 3, 181, 1, 0, 0, 0, '2019-11-30 07:50:08'),
(1321, 3, 86, 1, 1, 1, 1, '2019-11-30 07:54:08'),
(1322, 4, 28, 1, 1, 1, 1, '2019-12-01 00:52:39'),
(1324, 4, 29, 1, 0, 0, 0, '2019-12-01 00:53:46'),
(1325, 4, 30, 1, 0, 0, 0, '2019-12-01 00:53:59'),
(1326, 4, 123, 1, 0, 0, 0, '2019-12-01 00:54:26'),
(1327, 4, 228, 1, 0, 0, 0, '2019-12-01 00:54:39'),
(1328, 4, 43, 1, 1, 1, 1, '2019-12-01 00:58:05'),
(1332, 4, 44, 1, 0, 0, 0, '2019-12-01 00:59:16'),
(1333, 4, 46, 1, 0, 0, 0, '2019-12-01 00:59:16'),
(1334, 4, 217, 1, 0, 0, 0, '2019-12-01 00:59:16'),
(1335, 4, 178, 1, 0, 0, 0, '2019-12-01 00:59:59'),
(1336, 4, 179, 1, 0, 0, 0, '2019-12-01 01:00:11'),
(1337, 4, 180, 1, 0, 0, 0, '2019-12-01 01:00:29'),
(1338, 4, 221, 1, 0, 0, 0, '2019-12-01 01:00:46'),
(1339, 4, 86, 1, 0, 0, 0, '2019-12-01 01:01:02'),
(1341, 4, 106, 1, 0, 0, 0, '2019-12-01 01:05:21'),
(1342, 1, 107, 1, 0, 0, 0, '2019-12-01 01:06:44'),
(1343, 4, 117, 1, 0, 0, 0, '2019-12-01 01:10:20'),
(1344, 4, 194, 1, 0, 0, 0, '2019-12-01 01:11:35'),
(1348, 4, 230, 1, 0, 0, 0, '2019-12-01 01:19:15'),
(1350, 6, 1, 1, 0, 0, 0, '2019-12-01 01:35:32'),
(1351, 6, 21, 1, 0, 0, 0, '2019-12-01 01:36:29'),
(1352, 6, 23, 1, 0, 0, 0, '2019-12-01 01:36:45'),
(1353, 6, 24, 1, 0, 0, 0, '2019-12-01 01:37:05'),
(1354, 6, 25, 1, 0, 0, 0, '2019-12-01 01:37:34'),
(1355, 6, 77, 1, 0, 0, 0, '2019-12-01 01:38:08'),
(1356, 6, 188, 1, 0, 0, 0, '2019-12-01 01:38:45'),
(1357, 6, 43, 1, 1, 1, 1, '2019-12-01 01:40:44'),
(1358, 6, 78, 1, 1, 1, 1, '2019-12-01 01:43:04'),
(1360, 6, 79, 1, 1, 0, 1, '2019-12-01 01:44:39'),
(1361, 6, 80, 1, 1, 1, 1, '2019-12-01 01:45:08'),
(1362, 6, 81, 1, 1, 1, 1, '2019-12-01 01:47:50'),
(1363, 6, 85, 1, 1, 1, 1, '2019-12-01 01:50:43'),
(1364, 6, 86, 1, 0, 0, 0, '2019-12-01 01:51:10'),
(1365, 6, 106, 1, 0, 0, 0, '2019-12-01 01:52:55'),
(1366, 6, 117, 1, 0, 0, 0, '2019-12-01 01:53:08'),
(1394, 1, 106, 1, 0, 0, 0, '2019-12-02 05:20:33'),
(1395, 1, 113, 1, 0, 0, 0, '2019-12-02 05:20:59'),
(1396, 1, 114, 1, 0, 0, 0, '2019-12-02 05:21:34'),
(1397, 1, 115, 1, 0, 0, 0, '2019-12-02 05:21:34'),
(1398, 1, 116, 1, 0, 0, 0, '2019-12-02 05:21:54'),
(1399, 1, 117, 1, 0, 0, 0, '2019-12-02 05:22:04'),
(1400, 1, 118, 1, 0, 0, 0, '2019-12-02 05:22:20'),
(1402, 1, 191, 1, 0, 0, 0, '2019-12-02 05:23:34'),
(1403, 1, 192, 1, 0, 0, 0, '2019-12-02 05:23:47'),
(1404, 1, 193, 1, 0, 0, 0, '2019-12-02 05:23:58'),
(1405, 1, 194, 1, 0, 0, 0, '2019-12-02 05:24:11'),
(1406, 1, 195, 1, 0, 0, 0, '2019-12-02 05:24:20'),
(1408, 1, 227, 1, 0, 0, 0, '2019-12-02 05:25:47'),
(1410, 1, 226, 1, 0, 0, 0, '2019-12-02 05:31:41'),
(1411, 1, 229, 1, 0, 0, 0, '2019-12-02 05:32:57'),
(1412, 1, 230, 1, 0, 0, 0, '2019-12-02 05:32:57'),
(1413, 1, 190, 1, 0, 0, 0, '2019-12-02 05:43:41'),
(1414, 2, 174, 1, 0, 0, 0, '2019-12-02 05:54:37'),
(1415, 2, 175, 1, 0, 0, 0, '2019-12-02 05:54:37'),
(1418, 2, 232, 1, 0, 1, 1, '2019-12-02 06:11:27'),
(1419, 2, 231, 1, 0, 0, 0, '2019-12-02 06:12:28'),
(1420, 1, 231, 1, 1, 1, 1, '2021-01-23 07:09:32'),
(1421, 1, 232, 1, 0, 1, 1, '2019-12-02 06:19:32'),
(1422, 3, 32, 1, 1, 1, 1, '2019-12-02 06:30:37'),
(1423, 3, 33, 1, 1, 1, 1, '2019-12-02 06:30:37'),
(1424, 3, 34, 1, 1, 1, 1, '2019-12-02 06:30:37'),
(1425, 3, 182, 1, 0, 0, 0, '2019-12-02 06:30:37'),
(1426, 3, 183, 1, 0, 0, 0, '2019-12-02 06:30:37'),
(1427, 3, 189, 1, 0, 1, 1, '2019-12-02 06:30:37'),
(1428, 3, 229, 1, 0, 0, 0, '2019-12-02 06:30:37'),
(1429, 3, 230, 1, 0, 0, 0, '2019-12-02 06:30:37'),
(1430, 4, 213, 1, 0, 0, 0, '2019-12-02 06:32:14'),
(1432, 4, 224, 1, 0, 0, 0, '2019-12-02 06:32:14'),
(1433, 4, 195, 1, 0, 0, 0, '2019-12-03 04:57:53'),
(1434, 4, 229, 1, 0, 0, 0, '2019-12-03 04:58:19'),
(1436, 6, 213, 1, 0, 0, 0, '2019-12-03 05:10:11'),
(1437, 6, 191, 1, 0, 0, 0, '2019-12-03 05:10:11'),
(1438, 6, 193, 1, 0, 0, 0, '2019-12-03 05:10:11'),
(1439, 6, 230, 1, 0, 0, 0, '2019-12-03 05:10:11'),
(1440, 2, 106, 1, 0, 0, 0, '2020-01-25 04:21:36'),
(1441, 2, 107, 1, 0, 0, 0, '2020-02-12 02:10:13'),
(1442, 2, 134, 1, 1, 1, 1, '2020-02-12 02:12:36'),
(1443, 1, 233, 1, 0, 0, 0, '2020-02-12 02:21:57'),
(1444, 2, 86, 1, 0, 0, 0, '2020-02-12 02:22:33'),
(1445, 3, 233, 1, 0, 0, 0, '2020-02-12 03:51:17'),
(1446, 1, 234, 1, 1, 1, 1, '2020-06-01 21:51:09'),
(1447, 1, 235, 1, 1, 1, 1, '2020-05-29 23:17:01'),
(1448, 1, 236, 1, 1, 1, 0, '2020-05-29 23:17:52'),
(1449, 1, 237, 1, 0, 1, 0, '2020-05-29 23:18:18'),
(1450, 1, 238, 1, 1, 1, 1, '2020-05-29 23:19:52'),
(1451, 1, 239, 1, 1, 1, 1, '2020-05-29 23:22:10'),
(1452, 2, 236, 1, 1, 1, 0, '2020-05-29 23:40:33'),
(1453, 2, 237, 1, 0, 1, 0, '2020-05-29 23:40:33'),
(1454, 2, 238, 1, 1, 1, 1, '2020-05-29 23:40:33'),
(1455, 2, 239, 1, 1, 1, 1, '2020-05-29 23:40:33'),
(1456, 2, 240, 1, 0, 0, 0, '2020-05-28 20:51:18'),
(1457, 2, 241, 1, 0, 0, 0, '2020-05-28 20:51:18'),
(1458, 1, 240, 1, 0, 0, 0, '2020-06-07 18:30:42'),
(1459, 1, 241, 1, 0, 0, 0, '2020-06-07 18:30:42'),
(1460, 1, 242, 1, 0, 0, 0, '2020-06-07 18:30:42'),
(1461, 2, 242, 1, 0, 0, 0, '2020-06-11 22:45:24'),
(1462, 3, 242, 1, 0, 0, 0, '2020-06-14 22:46:54'),
(1463, 6, 242, 1, 0, 0, 0, '2020-06-14 22:48:14'),
(1464, 1, 243, 1, 0, 0, 0, '2020-09-12 06:05:45'),
(1465, 1, 109, 1, 1, 0, 0, '2020-09-21 06:33:50'),
(1466, 1, 108, 1, 0, 1, 1, '2023-11-04 12:52:08'),
(1467, 1, 244, 1, 0, 0, 0, '2020-09-21 06:59:54'),
(1468, 1, 245, 1, 0, 0, 0, '2020-09-21 06:59:54'),
(1469, 1, 246, 1, 0, 0, 0, '2020-09-21 06:59:54'),
(1470, 1, 247, 1, 0, 0, 0, '2021-01-07 06:12:14'),
(1472, 2, 247, 1, 0, 0, 0, '2021-01-21 12:46:40'),
(1473, 1, 248, 1, 1, 1, 1, '2021-05-19 12:52:49'),
(1474, 1, 249, 1, 0, 0, 0, '2021-05-19 12:52:49'),
(1475, 2, 248, 1, 1, 1, 1, '2021-05-28 13:11:52'),
(1476, 3, 248, 1, 1, 1, 1, '2021-05-28 09:36:16'),
(1477, 3, 249, 1, 0, 0, 0, '2021-05-28 09:36:16'),
(1478, 6, 248, 1, 0, 0, 0, '2021-05-28 09:56:14'),
(1479, 6, 249, 1, 0, 0, 0, '2021-05-28 09:56:14'),
(1480, 2, 249, 1, 0, 0, 0, '2021-05-28 13:11:52'),
(1481, 1, 269, 1, 0, 0, 1, '2023-11-04 12:52:08'),
(1482, 2, 269, 1, 0, 0, 1, '2023-11-04 12:52:28'),
(1483, 3, 269, 1, 0, 0, 1, '2023-11-04 12:53:22'),
(1484, 4, 269, 1, 0, 0, 1, '2023-11-04 12:53:34'),
(1485, 6, 269, 1, 0, 0, 1, '2023-11-04 12:53:52'),
(1486, 2, 6001, 1, 1, 0, 1, '2021-01-29 16:31:16'),
(1487, 1, 6001, 1, 1, 0, 1, '2020-09-23 19:59:22'),
(1488, 1, 6002, 1, 1, 0, 1, '2020-09-23 20:01:52'),
(1489, 1, 6003, 1, 0, 0, 0, '2020-09-23 20:02:06'),
(1490, 1, 6004, 1, 0, 0, 0, '2020-09-23 20:02:33'),
(1491, 1, 6005, 1, 0, 1, 0, '2021-01-29 14:45:05'),
(1492, 2, 6005, 1, 0, 1, 0, '2021-01-29 18:08:20'),
(1493, 2, 6002, 1, 1, 0, 1, '2021-01-29 14:58:48'),
(1494, 3, 6002, 1, 1, 0, 1, '2021-01-29 14:26:08'),
(1495, 3, 6003, 1, 0, 0, 0, '2021-01-29 14:26:08'),
(1496, 3, 6005, 1, 0, 1, 0, '2021-01-29 14:26:08'),
(1497, 4, 6001, 1, 1, 0, 1, '2021-01-29 14:26:53'),
(1498, 4, 6002, 1, 1, 0, 1, '2021-01-29 14:26:53'),
(1499, 6, 6001, 1, 1, 0, 1, '2021-01-29 14:27:32'),
(1500, 6, 6002, 1, 1, 0, 1, '2021-01-29 14:27:32'),
(1501, 6, 6005, 1, 0, 1, 0, '2021-01-29 14:27:32'),
(1502, 2, 6003, 1, 0, 0, 0, '2021-01-29 16:31:16'),
(1503, 2, 6004, 1, 0, 0, 0, '2021-01-29 16:31:16'),
(1504, 4, 6005, 1, 0, 1, 0, '2022-07-13 11:28:08'),
(1505, 3, 5005, 1, 0, 0, 0, '2022-07-13 16:54:15'),
(1506, 2, 5005, 1, 0, 0, 0, '2022-07-13 17:20:33'),
(1507, 6, 5005, 1, 0, 0, 0, '2022-07-13 17:29:49'),
(1508, 1, 5005, 1, 0, 0, 0, '2022-07-13 17:57:22'),
(1509, 4, 5005, 1, 0, 0, 0, '2022-07-13 18:23:43'),
(1510, 3, 5004, 1, 0, 0, 0, '2020-06-14 20:03:50'),
(1511, 6, 5004, 1, 0, 0, 0, '2022-07-13 17:29:49'),
(1512, 2, 5004, 1, 0, 0, 0, '2022-07-13 17:20:19'),
(1513, 1, 5004, 1, 0, 0, 0, '2022-07-13 17:57:22'),
(1514, 4, 5004, 1, 0, 0, 0, '2022-07-13 18:23:43'),
(1515, 2, 5003, 1, 1, 0, 0, '2022-07-13 17:21:13'),
(1516, 6, 5003, 1, 0, 0, 1, '2022-07-13 17:33:55'),
(1517, 1, 5003, 1, 1, 0, 1, '2022-07-13 18:00:28'),
(1518, 4, 5003, 1, 1, 0, 0, '2022-07-13 18:38:07'),
(1519, 3, 5003, 1, 1, 0, 1, '2020-06-14 20:03:50'),
(1520, 2, 5002, 1, 1, 0, 0, '2022-07-13 17:25:20'),
(1521, 6, 5002, 1, 1, 0, 1, '2022-07-13 17:52:36'),
(1522, 1, 5002, 1, 1, 0, 1, '2022-07-13 18:00:28'),
(1523, 4, 5002, 1, 1, 0, 0, '2022-07-13 18:38:07'),
(1524, 2, 5001, 1, 0, 1, 0, '2022-07-13 17:28:26'),
(1525, 1, 5001, 1, 0, 1, 0, '2022-07-13 18:00:36'),
(1526, 6, 5001, 1, 0, 1, 0, '2022-07-13 17:52:36'),
(1527, 4, 5001, 1, 0, 0, 0, '2022-07-13 18:49:49'),
(1528, 1, 7001, 1, 1, 1, 1, '2021-05-11 14:21:33'),
(1529, 1, 7002, 1, 0, 0, 0, '2021-05-17 12:28:47'),
(1530, 1, 7003, 1, 1, 1, 1, '2021-05-11 15:29:37'),
(1531, 1, 7005, 1, 1, 1, 1, '2021-05-17 12:28:47'),
(1532, 1, 7004, 1, 1, 1, 1, '2021-05-11 17:00:50'),
(1533, 1, 7006, 1, 1, 0, 0, '2021-05-17 12:28:47'),
(1534, 1, 7007, 1, 0, 0, 0, '2021-05-11 17:00:50'),
(1535, 1, 7008, 1, 0, 0, 0, '2021-05-11 17:00:50'),
(1536, 1, 7009, 1, 0, 0, 0, '2021-05-11 17:00:50'),
(1537, 1, 7010, 1, 0, 0, 0, '2021-05-11 17:00:50'),
(1538, 2, 7001, 1, 1, 1, 1, '2021-05-15 18:07:28'),
(1539, 2, 7002, 1, 0, 0, 0, '2021-05-17 17:51:44'),
(1540, 2, 7003, 1, 1, 1, 1, '2021-05-15 17:28:38'),
(1541, 2, 7004, 1, 1, 1, 1, '2021-05-15 17:28:38'),
(1542, 3, 7006, 1, 1, 0, 0, '2021-05-17 17:52:19'),
(1543, 3, 7007, 1, 0, 0, 0, '2021-05-17 11:32:06'),
(1544, 1, 7011, 1, 0, 0, 0, '2021-05-17 12:28:47'),
(1545, 2, 7005, 1, 1, 1, 1, '2021-05-17 17:51:44'),
(1546, 2, 7010, 1, 0, 0, 0, '2021-05-17 17:51:44'),
(1547, 1, 7012, 1, 0, 0, 1, '2022-06-20 11:20:01'),
(1548, 1, 7013, 1, 0, 1, 1, '2022-06-18 16:52:16'),
(1549, 1, 7014, 1, 1, 1, 1, '2021-05-15 17:28:38'),
(1550, 6, 7014, 1, 1, 1, 1, '2022-06-18 16:09:15'),
(1551, 6, 7013, 1, 0, 0, 0, '2022-06-18 16:13:07'),
(1552, 4, 7012, 1, 0, 0, 0, '2022-07-15 11:22:20'),
(1553, 4, 7013, 1, 0, 1, 1, '2022-07-15 11:26:29'),
(1554, 4, 7014, 1, 1, 1, 1, '2022-07-15 11:24:48'),
(1555, 2, 7012, 1, 0, 0, 1, '2022-07-15 12:01:12'),
(1556, 2, 7013, 1, 0, 0, 1, '2022-07-15 12:01:12'),
(1557, 2, 7014, 1, 0, 0, 1, '2022-07-15 12:01:12'),
(1558, 3, 7012, 1, 0, 0, 0, '2022-07-15 12:31:18'),
(1559, 3, 7013, 1, 0, 0, 0, '2022-07-15 12:31:18'),
(1560, 3, 7014, 1, 0, 0, 0, '2022-07-15 12:31:18'),
(1561, 1, 8001, 1, 1, 0, 1, '2022-05-05 14:00:06'),
(1562, 2, 8003, 1, 0, 0, 0, '2022-05-05 13:50:12'),
(1563, 2, 8004, 1, 0, 0, 0, '2022-05-05 13:50:42'),
(1564, 2, 8005, 1, 0, 0, 0, '2022-05-05 13:50:59'),
(1565, 2, 8006, 1, 0, 0, 0, '2022-05-05 13:51:17'),
(1566, 1, 8002, 1, 1, 1, 1, '2022-05-05 14:01:15'),
(1567, 1, 8003, 1, 0, 0, 0, '2022-05-05 14:01:49'),
(1568, 1, 8004, 1, 0, 0, 0, '2022-05-05 14:05:12'),
(1569, 1, 8005, 1, 0, 0, 0, '2022-05-05 14:08:53'),
(1570, 1, 8006, 1, 0, 0, 0, '2022-05-05 14:10:42'),
(1571, 2, 8008, 1, 0, 0, 0, '2022-05-05 14:11:34'),
(1572, 1, 8007, 1, 0, 0, 0, '2022-05-05 14:12:37'),
(1573, 1, 8008, 1, 0, 0, 0, '2022-05-05 14:15:07'),
(1574, 1, 8009, 1, 0, 1, 0, '2022-05-05 15:19:18'),
(1575, 2, 8007, 1, 0, 0, 0, '2022-05-05 14:26:02'),
(1576, 2, 8002, 1, 1, 1, 1, '2022-05-05 16:47:39'),
(1577, 2, 8009, 1, 0, 1, 0, '2022-05-05 15:25:01'),
(1578, 2, 8001, 1, 1, 0, 1, '2022-05-05 16:47:39'),
(1579, 4, 8001, 1, 1, 0, 1, '2022-05-05 15:37:50'),
(1580, 4, 8002, 1, 1, 1, 1, '2022-05-05 15:37:50'),
(1581, 4, 8003, 1, 0, 0, 0, '2022-05-05 15:32:25'),
(1582, 4, 8004, 1, 0, 0, 0, '2022-05-05 15:32:25'),
(1583, 4, 8005, 1, 0, 0, 0, '2022-05-05 15:32:25'),
(1584, 4, 8006, 1, 0, 0, 0, '2022-05-05 15:32:25'),
(1585, 4, 8007, 1, 0, 0, 0, '2022-05-05 15:32:25'),
(1586, 4, 8008, 1, 0, 0, 0, '2022-05-05 15:32:25'),
(1587, 4, 8009, 1, 0, 0, 0, '2022-05-05 15:32:25'),
(1588, 3, 8001, 1, 1, 0, 1, '2022-05-05 15:36:56'),
(1589, 3, 8002, 1, 1, 1, 1, '2022-05-05 15:42:21'),
(1590, 3, 8003, 1, 0, 0, 0, '2022-05-05 15:44:16'),
(1591, 3, 8004, 1, 0, 0, 0, '2022-05-05 15:49:05'),
(1592, 3, 8005, 1, 0, 0, 0, '2022-05-05 15:54:09'),
(1593, 3, 8006, 1, 0, 0, 0, '2022-05-05 15:57:04'),
(1594, 3, 8007, 1, 0, 0, 0, '2022-05-05 16:00:02'),
(1595, 3, 8008, 1, 0, 0, 0, '2022-05-05 16:02:30'),
(1596, 3, 8009, 1, 0, 1, 0, '2022-05-05 16:09:34'),
(1597, 1, 11001, 1, 0, 0, 0, '2022-05-05 14:00:06'),
(1598, 1, 11002, 1, 0, 0, 0, '2022-05-05 13:50:12'),
(1599, 1, 10001, 1, 0, 0, 0, '2022-05-05 14:00:06'),
(1600, 1, 10002, 1, 0, 0, 0, '2022-05-05 13:50:12'),
(1601, 1, 10003, 1, 0, 0, 0, '2022-05-05 13:50:12'),
(1602, 1, 10004, 1, 0, 0, 0, '2022-05-05 13:50:12'),
(1603, 1, 10005, 1, 0, 0, 0, '2022-05-05 13:50:12'),
(1604, 1, 10006, 1, 0, 0, 0, '2022-05-05 13:50:12'),
(1605, 1, 10007, 1, 0, 0, 0, '2022-05-05 13:50:12');

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` int(11) NOT NULL,
  `room_type` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `room_type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'One Bed', '', '2024-11-25 08:45:02', NULL),
(2, 'Two Bed AC', '', '2024-11-25 08:45:20', NULL),
(3, 'Two Bed', '', '2024-11-25 08:45:28', NULL),
(4, 'One Bed AC', '', '2024-11-25 08:45:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `route_pickup_point`
--

CREATE TABLE `route_pickup_point` (
  `id` int(11) NOT NULL,
  `transport_route_id` int(11) NOT NULL,
  `pickup_point_id` int(11) NOT NULL,
  `fees` float(10,2) DEFAULT 0.00,
  `destination_distance` float(10,1) DEFAULT 0.0,
  `pickup_time` time DEFAULT NULL,
  `order_number` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `route_pickup_point`
--

INSERT INTO `route_pickup_point` (`id`, `transport_route_id`, `pickup_point_id`, `fees`, `destination_distance`, `pickup_time`, `order_number`, `created_at`) VALUES
(1, 1, 4, 50.00, 5.0, '16:30:00', 1, '2024-11-25 09:47:45'),
(2, 1, 1, 50.00, 10.0, '12:30:00', 2, '2024-11-25 09:47:45'),
(3, 1, 2, 50.00, 15.0, '13:30:00', 3, '2024-11-25 09:47:45'),
(4, 1, 3, 100.00, 10.0, '14:00:00', 4, '2024-11-25 09:47:45'),
(5, 2, 6, 100.00, 10.0, '10:30:00', 1, '2024-11-25 09:50:32'),
(6, 2, 2, 20.00, 15.0, '12:30:00', 2, '2024-11-25 09:50:05'),
(7, 2, 7, 20.00, 5.0, '13:30:00', 3, '2024-11-25 09:50:05'),
(8, 2, 1, 10.00, 10.0, '13:00:00', 4, '2024-11-25 09:50:05'),
(9, 5, 4, 100.00, 10.0, '13:45:00', 1, '2024-11-25 09:52:49'),
(10, 5, 1, 200.00, 10.0, '11:30:00', 2, '2024-11-25 09:52:49'),
(11, 5, 2, 50.00, 5.0, '12:30:00', 3, '2024-11-25 09:52:49'),
(12, 5, 7, 10.00, 10.0, '16:13:00', 4, '2024-11-25 09:52:49'),
(13, 3, 3, 20.00, 10.0, '12:30:00', 1, '2024-11-25 09:54:49'),
(14, 3, 5, 50.00, 15.0, '13:00:00', 2, '2024-11-25 09:54:49'),
(15, 3, 6, 50.00, 20.0, '14:30:00', 3, '2024-11-25 09:54:49'),
(16, 3, 2, 100.00, 5.0, '15:30:00', 4, '2024-11-25 09:54:49');

-- --------------------------------------------------------

--
-- Table structure for table `school_houses`
--

CREATE TABLE `school_houses` (
  `id` int(11) NOT NULL,
  `house_name` varchar(200) NOT NULL,
  `description` varchar(400) NOT NULL,
  `is_active` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `school_houses`
--

INSERT INTO `school_houses` (`id`, `house_name`, `description`, `is_active`) VALUES
(1, 'Blue', '', 'yes'),
(2, 'Red', '', 'yes'),
(3, 'Green', '', 'yes'),
(4, 'Yellow', '', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `sch_settings`
--

CREATE TABLE `sch_settings` (
  `id` int(11) NOT NULL,
  `base_url` varchar(500) DEFAULT NULL,
  `folder_path` text DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `biometric` int(11) DEFAULT 0,
  `biometric_device` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `lang_id` int(11) DEFAULT NULL,
  `languages` varchar(500) NOT NULL,
  `dise_code` varchar(50) DEFAULT NULL,
  `date_format` varchar(50) NOT NULL,
  `time_format` varchar(255) NOT NULL,
  `currency` varchar(50) NOT NULL,
  `currency_symbol` varchar(50) NOT NULL,
  `is_rtl` varchar(10) DEFAULT 'disabled',
  `is_duplicate_fees_invoice` varchar(100) DEFAULT '0',
  `collect_back_date_fees` int(11) NOT NULL,
  `single_page_print` int(1) DEFAULT 0,
  `timezone` varchar(30) DEFAULT 'UTC',
  `session_id` int(11) DEFAULT NULL,
  `cron_secret_key` varchar(100) NOT NULL,
  `currency_place` varchar(50) NOT NULL DEFAULT 'before_number',
  `currency_format` varchar(20) DEFAULT NULL,
  `class_teacher` varchar(100) NOT NULL,
  `start_month` varchar(40) NOT NULL,
  `attendence_type` int(10) NOT NULL DEFAULT 0,
  `low_attendance_limit` float(10,2) NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `admin_logo` varchar(255) NOT NULL,
  `admin_small_logo` varchar(255) NOT NULL,
  `admin_login_page_background` varchar(255) NOT NULL,
  `user_login_page_background` varchar(255) NOT NULL,
  `theme` varchar(200) NOT NULL DEFAULT 'default.jpg',
  `fee_due_days` int(3) DEFAULT 0,
  `adm_auto_insert` int(1) NOT NULL DEFAULT 1,
  `adm_prefix` varchar(50) NOT NULL DEFAULT 'ssadm19/20',
  `adm_start_from` varchar(11) NOT NULL,
  `adm_no_digit` int(10) NOT NULL DEFAULT 6,
  `adm_update_status` int(11) NOT NULL DEFAULT 0,
  `staffid_auto_insert` int(11) NOT NULL DEFAULT 1,
  `staffid_prefix` varchar(100) NOT NULL DEFAULT 'staffss/19/20',
  `staffid_start_from` varchar(50) NOT NULL,
  `staffid_no_digit` int(11) NOT NULL DEFAULT 6,
  `staffid_update_status` int(11) NOT NULL DEFAULT 0,
  `is_active` varchar(255) DEFAULT 'no',
  `online_admission` int(1) DEFAULT 0,
  `online_admission_payment` varchar(50) NOT NULL,
  `online_admission_amount` float NOT NULL,
  `online_admission_instruction` text NOT NULL,
  `online_admission_conditions` text NOT NULL,
  `online_admission_application_form` varchar(255) DEFAULT NULL,
  `exam_result` int(11) NOT NULL,
  `is_blood_group` int(10) NOT NULL DEFAULT 1,
  `is_student_house` int(10) NOT NULL DEFAULT 1,
  `roll_no` int(11) NOT NULL DEFAULT 1,
  `category` int(11) NOT NULL,
  `religion` int(11) NOT NULL DEFAULT 1,
  `cast` int(11) NOT NULL DEFAULT 1,
  `mobile_no` int(11) NOT NULL DEFAULT 1,
  `student_email` int(11) NOT NULL DEFAULT 1,
  `admission_date` int(11) NOT NULL DEFAULT 1,
  `lastname` int(11) NOT NULL,
  `middlename` int(11) NOT NULL DEFAULT 1,
  `student_photo` int(11) NOT NULL DEFAULT 1,
  `student_height` int(11) NOT NULL DEFAULT 1,
  `student_weight` int(11) NOT NULL DEFAULT 1,
  `measurement_date` int(11) NOT NULL DEFAULT 1,
  `father_name` int(11) NOT NULL DEFAULT 1,
  `father_phone` int(11) NOT NULL DEFAULT 1,
  `father_occupation` int(11) NOT NULL DEFAULT 1,
  `father_pic` int(11) NOT NULL DEFAULT 1,
  `mother_name` int(11) NOT NULL DEFAULT 1,
  `mother_phone` int(11) NOT NULL DEFAULT 1,
  `mother_occupation` int(11) NOT NULL DEFAULT 1,
  `mother_pic` int(11) NOT NULL DEFAULT 1,
  `guardian_name` int(1) NOT NULL,
  `guardian_relation` int(11) NOT NULL DEFAULT 1,
  `guardian_phone` int(1) NOT NULL,
  `guardian_email` int(11) NOT NULL DEFAULT 1,
  `guardian_pic` int(11) NOT NULL DEFAULT 1,
  `guardian_occupation` int(1) NOT NULL,
  `guardian_address` int(11) NOT NULL DEFAULT 1,
  `current_address` int(11) NOT NULL DEFAULT 1,
  `permanent_address` int(11) NOT NULL DEFAULT 1,
  `route_list` int(11) NOT NULL DEFAULT 1,
  `hostel_id` int(11) NOT NULL DEFAULT 1,
  `bank_account_no` int(11) NOT NULL DEFAULT 1,
  `ifsc_code` int(1) NOT NULL,
  `bank_name` int(1) NOT NULL,
  `national_identification_no` int(11) NOT NULL DEFAULT 1,
  `local_identification_no` int(11) NOT NULL DEFAULT 1,
  `rte` int(11) NOT NULL DEFAULT 1,
  `previous_school_details` int(11) NOT NULL DEFAULT 1,
  `student_note` int(11) NOT NULL DEFAULT 1,
  `upload_documents` int(11) NOT NULL DEFAULT 1,
  `student_barcode` int(11) NOT NULL DEFAULT 1,
  `staff_designation` int(11) NOT NULL DEFAULT 1,
  `staff_department` int(11) NOT NULL DEFAULT 1,
  `staff_last_name` int(11) NOT NULL DEFAULT 1,
  `staff_father_name` int(11) NOT NULL DEFAULT 1,
  `staff_mother_name` int(11) NOT NULL DEFAULT 1,
  `staff_date_of_joining` int(11) NOT NULL DEFAULT 1,
  `staff_phone` int(11) NOT NULL DEFAULT 1,
  `staff_emergency_contact` int(11) NOT NULL DEFAULT 1,
  `staff_marital_status` int(11) NOT NULL DEFAULT 1,
  `staff_photo` int(11) NOT NULL DEFAULT 1,
  `staff_current_address` int(11) NOT NULL DEFAULT 1,
  `staff_permanent_address` int(11) NOT NULL DEFAULT 1,
  `staff_qualification` int(11) NOT NULL DEFAULT 1,
  `staff_work_experience` int(11) NOT NULL DEFAULT 1,
  `staff_note` int(11) NOT NULL DEFAULT 1,
  `staff_epf_no` int(11) NOT NULL DEFAULT 1,
  `staff_basic_salary` int(11) NOT NULL DEFAULT 1,
  `staff_contract_type` int(11) NOT NULL DEFAULT 1,
  `staff_work_shift` int(11) NOT NULL DEFAULT 1,
  `staff_work_location` int(11) NOT NULL DEFAULT 1,
  `staff_leaves` int(11) NOT NULL DEFAULT 1,
  `staff_account_details` int(11) NOT NULL DEFAULT 1,
  `staff_social_media` int(11) NOT NULL DEFAULT 1,
  `staff_upload_documents` int(11) NOT NULL DEFAULT 1,
  `staff_barcode` int(11) NOT NULL DEFAULT 1,
  `staff_notification_email` varchar(50) NOT NULL,
  `mobile_api_url` tinytext NOT NULL,
  `app_primary_color_code` varchar(20) DEFAULT NULL,
  `app_secondary_color_code` varchar(20) DEFAULT NULL,
  `admin_mobile_api_url` tinytext NOT NULL,
  `admin_app_primary_color_code` varchar(20) NOT NULL,
  `admin_app_secondary_color_code` varchar(20) NOT NULL,
  `app_logo` varchar(250) DEFAULT NULL,
  `zoom_api_key` varchar(100) DEFAULT NULL,
  `zoom_api_secret` varchar(100) DEFAULT NULL,
  `student_profile_edit` int(1) NOT NULL DEFAULT 0,
  `start_week` varchar(10) NOT NULL,
  `my_question` int(1) NOT NULL,
  `superadmin_restriction` varchar(20) NOT NULL,
  `student_timeline` varchar(20) NOT NULL,
  `calendar_event_reminder` int(2) DEFAULT NULL,
  `event_reminder` varchar(20) NOT NULL,
  `student_login` varchar(100) DEFAULT NULL,
  `parent_login` varchar(100) DEFAULT NULL,
  `student_panel_login` int(1) NOT NULL DEFAULT 1,
  `parent_panel_login` int(1) NOT NULL DEFAULT 1,
  `is_student_feature_lock` int(1) NOT NULL DEFAULT 0,
  `maintenance_mode` int(1) NOT NULL DEFAULT 0,
  `lock_grace_period` int(10) NOT NULL DEFAULT 0,
  `is_offline_fee_payment` int(1) NOT NULL DEFAULT 0,
  `offline_bank_payment_instruction` text NOT NULL,
  `scan_code_type` varchar(50) NOT NULL DEFAULT 'barcode',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `sch_settings`
--

INSERT INTO `sch_settings` (`id`, `base_url`, `folder_path`, `name`, `biometric`, `biometric_device`, `email`, `phone`, `address`, `lang_id`, `languages`, `dise_code`, `date_format`, `time_format`, `currency`, `currency_symbol`, `is_rtl`, `is_duplicate_fees_invoice`, `collect_back_date_fees`, `single_page_print`, `timezone`, `session_id`, `cron_secret_key`, `currency_place`, `currency_format`, `class_teacher`, `start_month`, `attendence_type`, `low_attendance_limit`, `image`, `admin_logo`, `admin_small_logo`, `admin_login_page_background`, `user_login_page_background`, `theme`, `fee_due_days`, `adm_auto_insert`, `adm_prefix`, `adm_start_from`, `adm_no_digit`, `adm_update_status`, `staffid_auto_insert`, `staffid_prefix`, `staffid_start_from`, `staffid_no_digit`, `staffid_update_status`, `is_active`, `online_admission`, `online_admission_payment`, `online_admission_amount`, `online_admission_instruction`, `online_admission_conditions`, `online_admission_application_form`, `exam_result`, `is_blood_group`, `is_student_house`, `roll_no`, `category`, `religion`, `cast`, `mobile_no`, `student_email`, `admission_date`, `lastname`, `middlename`, `student_photo`, `student_height`, `student_weight`, `measurement_date`, `father_name`, `father_phone`, `father_occupation`, `father_pic`, `mother_name`, `mother_phone`, `mother_occupation`, `mother_pic`, `guardian_name`, `guardian_relation`, `guardian_phone`, `guardian_email`, `guardian_pic`, `guardian_occupation`, `guardian_address`, `current_address`, `permanent_address`, `route_list`, `hostel_id`, `bank_account_no`, `ifsc_code`, `bank_name`, `national_identification_no`, `local_identification_no`, `rte`, `previous_school_details`, `student_note`, `upload_documents`, `student_barcode`, `staff_designation`, `staff_department`, `staff_last_name`, `staff_father_name`, `staff_mother_name`, `staff_date_of_joining`, `staff_phone`, `staff_emergency_contact`, `staff_marital_status`, `staff_photo`, `staff_current_address`, `staff_permanent_address`, `staff_qualification`, `staff_work_experience`, `staff_note`, `staff_epf_no`, `staff_basic_salary`, `staff_contract_type`, `staff_work_shift`, `staff_work_location`, `staff_leaves`, `staff_account_details`, `staff_social_media`, `staff_upload_documents`, `staff_barcode`, `staff_notification_email`, `mobile_api_url`, `app_primary_color_code`, `app_secondary_color_code`, `admin_mobile_api_url`, `admin_app_primary_color_code`, `admin_app_secondary_color_code`, `app_logo`, `zoom_api_key`, `zoom_api_secret`, `student_profile_edit`, `start_week`, `my_question`, `superadmin_restriction`, `student_timeline`, `calendar_event_reminder`, `event_reminder`, `student_login`, `parent_login`, `student_panel_login`, `parent_panel_login`, `is_student_feature_lock`, `maintenance_mode`, `lock_grace_period`, `is_offline_fee_payment`, `offline_bank_payment_instruction`, `scan_code_type`, `created_at`, `updated_at`) VALUES
(1, '{{BASEURL}}', '/home/fe0a2bw9kdpv/public_html/school.innovationcode.in/', 'Mount Carmel School', 0, '', 'yourschoolemail@domain.com', '89562423934', '25 Kings Street, CA', 4, '[\"4\",\"7\",\"2\",\"81\",\"82\",\"59\",\"61\",\"31\",\"75\"]', 'ACT-487438', 'm/d/Y', '12-hour', '106', 'NGN', 'disabled', '0', 0, 0, 'Asia/Kolkata', 19, '', 'after_number', '#,##,###.##', 'no', '4', 0, 75.00, '1.jpg', '1.jpg', '1.jpg', '1663064530-1070210809632059d2b8b0b!1662796232-1721792380631c41c80d038!login_bg3.jpg', '1663065284-93117584263205cc49769c!1662964519-2099955753631ed327d0ffa!login_bg5.jpg', 'default.jpg', 60, 0, '', '', 0, 1, 0, '', '', 0, 1, 'no', 1, 'yes', 100, '<p>General Instruction:-&nbsp;These instructions pertain to online application for admission to Mount Carmel School&nbsp;for the academic year 2024-2025. In the remainder of these instructions, a &quot;Mount Carmel School&quot;.<br />\r\n1.<span style=\"font-family: Roboto, Helvetica, Arial, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\">To fill online admission form,&nbsp;<b>Basic Details like as&nbsp;</b>( Class, First Name, Last Name, Gender, Date of Birth, Mobile Number, Email) etc.</span><br />\r\n2.&nbsp;Filling in admission application form and uploading documents&nbsp;<span style=\"font-family: Roboto, Helvetica, Arial, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\">and then click on the&nbsp;</span><strong style=\"box-sizing: border-box; -webkit-tap-highlight-color: transparent; font-family: Roboto, Helvetica, Arial, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\">Submit</strong><span style=\"font-family: Roboto, Helvetica, Arial, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\">&nbsp;button.</span><br />\r\n3.&nbsp;<span style=\"font-family: Roboto, Helvetica, Arial, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\">After submitting form, this will redirect you in&nbsp;</span><strong style=\"box-sizing: border-box; -webkit-tap-highlight-color: transparent; font-family: Roboto, Helvetica, Arial, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\">Online Admission Review Details</strong><span style=\"font-family: Roboto, Helvetica, Arial, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);\">&nbsp;page where you can check your details what you have filled previously.</span></p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n', '<p>General Terms &amp; Conditions for Students:-<br />\r\n<br />\r\n1. The User declares that the content of the Portal shall be accessed and used only for the purpose of online application for admission to schools administered&nbsp;by&nbsp;MCS.<br />\r\n2. Check term &amp; condition before pay online payment,&nbsp;Reviewing form, checking declaration and submitting form I certify that all the information provided is true to the best of my knowledge.</p>\r\n', NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '', 'https://school.innovationcode.in/api/', '#424242', '#eeeeee', '', '', '', '1.jpg', NULL, NULL, 1, 'Monday', 0, 'enabled', 'disabled', 0, 'disabled', 'null', 'null', 1, 1, 0, 0, 0, 0, '', 'qrcode', '2025-02-11 12:40:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `section` varchar(60) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `section`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'A', 'no', '2024-11-25 09:56:21', NULL),
(2, 'B', 'no', '2024-11-25 09:56:26', NULL),
(3, 'C', 'no', '2024-11-25 09:56:29', NULL),
(4, 'D', 'no', '2024-11-25 09:56:34', NULL),
(6, 'level 5', 'no', '2024-12-09 10:44:06', NULL),
(7, 'level 6', 'no', '2024-12-09 10:44:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `send_notification`
--

CREATE TABLE `send_notification` (
  `id` int(11) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `date` date DEFAULT NULL,
  `attachment` varchar(500) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `visible_student` varchar(10) NOT NULL DEFAULT 'no',
  `visible_staff` varchar(10) NOT NULL DEFAULT 'no',
  `visible_parent` varchar(10) NOT NULL DEFAULT 'no',
  `created_by` varchar(60) DEFAULT NULL,
  `created_id` int(11) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `send_notification`
--

INSERT INTO `send_notification` (`id`, `title`, `publish_date`, `date`, `attachment`, `message`, `visible_student`, `visible_staff`, `visible_parent`, `created_by`, `created_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Staff Meeting', '2024-11-30', '2024-11-26', NULL, '<p>\r\n\r\n</p><p>Dear All staff,</p><p>A meeting attended by the members of staff of a company, school, etc, to discuss issues relating to the running of the company, school, etc.</p>\r\n\r\n\r\n\r\n<br><p></p>', 'Yes', 'Yes', 'Yes', 'Super Admin', 1, 'no', '2024-11-25 11:32:13', NULL),
(2, 'PTM Meeting', '2024-11-15', '2024-11-15', NULL, '<p>\r\n\r\n</p><p>Dear Parents/Guardians,</p><div>We would like to invite you to attend Individual Parent-Teacher Conferences. These meetings are a valuable chance to meet with your child’s class teacher to discuss how your child has settled in since the start of year.</div>\r\n\r\n\r\n\r\n<br><p></p>', 'Yes', 'Yes', 'Yes', 'Super Admin', 1, 'no', '2024-11-25 11:33:09', NULL),
(3, 'Children’s Day Celebrations', '2024-11-14', '2024-11-11', NULL, '<p>\r\n\r\nChildren’s day on the 14th November, 2024, with great festive fervour, to commemorate the 131st birth anniversary of Pandit Jawaharlal Nehru. The programme began with a special assembly conducted by the teachers, followed by informative and inspirational speeches by the Director, the CEO and the Principal about the great leader, thinker, visionary and a true statesman – Pandit Jawaharlal Nehru.\r\n\r\n<br></p>', 'Yes', 'Yes', 'Yes', 'Super Admin', 1, 'no', '2024-11-25 11:33:50', NULL),
(4, 'EMPLOYMENT ASSISTANCE', '2024-09-04', '2024-09-01', NULL, '<p>\r\n\r\nAssists in staffing, position management, training, timekeeping and payroll, and employee retention issues as directed and in accordance to the organization\'s human resource policies and procedures.\r\n\r\n<br></p>', 'No', 'Yes', 'No', 'Super Admin', 1, 'no', '2024-11-25 11:34:31', NULL),
(5, 'PTM!!!!!!!!!!!!!!!!', '2024-02-16', '2024-02-12', NULL, '<p>\r\n\r\n</p><p>Dear Parents, </p><p>               Gladly to inform you that Parents Teacher Meeting will be organized in 20th February 2024. </p>\r\n\r\n\r\n\r\n<br><p></p>', 'Yes', 'Yes', 'Yes', 'Super Admin', 1, 'no', '2024-11-25 11:35:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `session` varchar(60) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `session`, `is_active`, `created_at`, `updated_at`) VALUES
(7, '2016-17', 'no', '2017-04-20 06:42:19', '0000-00-00'),
(11, '2017-18', 'no', '2017-04-20 06:41:37', '0000-00-00'),
(13, '2018-19', 'no', '2016-08-24 19:26:44', '0000-00-00'),
(14, '2019-20', 'no', '2016-08-24 19:26:55', '0000-00-00'),
(15, '2020-21', 'no', '2016-10-01 05:28:08', '0000-00-00'),
(16, '2021-22', 'no', '2016-10-01 05:28:20', '0000-00-00'),
(18, '2022-23', 'no', '2016-10-01 05:29:02', '0000-00-00'),
(19, '2023-24', 'no', '2016-10-01 05:29:10', '0000-00-00'),
(20, '2024-25', 'no', '2016-10-01 05:29:18', '0000-00-00'),
(21, '2025-26', 'no', '2016-10-01 05:30:10', '0000-00-00'),
(22, '2026-27', 'no', '2016-10-01 05:30:18', '0000-00-00'),
(23, '2027-28', 'no', '2016-10-01 05:30:24', '0000-00-00'),
(24, '2028-29', 'no', '2016-10-01 05:30:30', '0000-00-00'),
(25, '2029-30', 'no', '2016-10-01 05:30:37', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `share_contents`
--

CREATE TABLE `share_contents` (
  `id` int(11) NOT NULL,
  `send_to` varchar(50) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `share_date` date DEFAULT NULL,
  `valid_upto` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `share_content_for`
--

CREATE TABLE `share_content_for` (
  `id` int(11) NOT NULL,
  `group_id` varchar(20) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `user_parent_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `class_section_id` int(11) DEFAULT NULL,
  `share_content_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `share_upload_contents`
--

CREATE TABLE `share_upload_contents` (
  `id` int(11) NOT NULL,
  `upload_content_id` int(11) DEFAULT NULL,
  `share_content_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sidebar_menus`
--

CREATE TABLE `sidebar_menus` (
  `id` int(11) NOT NULL,
  `permission_group_id` int(10) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `menu` varchar(500) DEFAULT NULL,
  `activate_menu` varchar(100) DEFAULT NULL,
  `lang_key` varchar(250) NOT NULL,
  `system_level` int(3) DEFAULT 0,
  `level` int(5) DEFAULT NULL,
  `sidebar_display` int(1) DEFAULT 0,
  `access_permissions` text DEFAULT NULL,
  `is_active` int(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `sidebar_menus`
--

INSERT INTO `sidebar_menus` (`id`, `permission_group_id`, `icon`, `menu`, `activate_menu`, `lang_key`, `system_level`, `level`, `sidebar_display`, `access_permissions`, `is_active`, `created_at`) VALUES
(1, 17, 'fa fa-ioxhost ftlayer', 'Front Office', 'front_office', 'front_office', 10, 1, 1, '(\'admission_enquiry\', \'can_view\') || (\'visitor_book\', \'can_view\') ||       (\'phon_call_log\', \'can_view\') ||  (\'postal_dispatch\', \'can_view\') ||       (\'postal_receive\', \'can_view\') || (\'complaint\', \'can_view\') ||(\'setup_font_office\', \'can_view\')', 1, '2023-01-10 12:49:51'),
(2, 1, 'fa fa-user-plus ftlayer', 'Student Information', 'student_information', 'student_information', 20, 2, 1, '(\'student\', \'can_view\') || (\'student\', \'can_add\') || (\'student_history\', \'can_view\') || (\'student_categories\', \'can_view\') || (\'student_houses\', \'can_view\') || (\'disable_student\', \'can_view\') || (\'disable_reason\', \'can_view\') || (\'online_admission\', \'can_view\') || (\'multiclass_student\', \'can_view\') || (\'disable_reason\', \'can_view\')', 1, '2023-01-10 12:49:51'),
(3, 2, 'fa fa-money ftlayer', 'Fees Collection', 'fees_collection', 'fees_collection', 30, 3, 1, '(\'collect_fees\', \'can_view\') || (\'search_fees_payment\', \'can_view\') || (\'search_due_fees\', \'can_view\') || (\'fees_statement\', \'can_view\') || (\'fees_carry_forward\', \'can_view\') || (\'fees_master\', \'can_view\') || (\'fees_group\', \'can_view\') || (\'fees_type\', \'can_view\') || (\'fees_discount\', \'can_view\') || (\'accountants\', \'can_view\')', 1, '2023-01-10 12:49:51'),
(4, 3, 'fa fa-usd ftlayer', 'Income', 'income', 'income', 40, 8, 1, '(\'income\', \'can_view\') || (\'search_income\', \'can_view\') || (\'income_head\', \'can_view\')', 1, '2024-11-25 07:48:24'),
(7, 4, 'fa fa-credit-card ftlayer', 'Expense', 'expense', 'expenses', 50, 9, 1, '(\'expense\', \'can_view\') || (\'search_expense\', \'can_view\') || (\'expense_head\', \'can_view\')', 1, '2024-11-25 07:48:24'),
(10, 5, 'fa fa-calendar-check-o ftlayer', 'Attendance', 'attendance', 'attendance', 60, 12, 1, '(\'student_attendance\', \'can_view\')', 1, '2024-11-25 07:48:24'),
(11, 6, 'fa fa-map-o ftlayer', 'Examinations', 'examinations', 'examinations', 70, 11, 1, '(\'exam_group\', \'can_view\') || (\'exam_result\', \'can_view\') || (\'design_admit_card\', \'can_view\') || (\'print_admit_card\', \'can_view\') || (\'design_marksheet\', \'can_view\') || (\'print_marksheet\', \'can_view\') || (\'marks_grade\', \'can_view\')', 1, '2024-11-25 07:48:24'),
(12, 23, 'fa fa-rss ftlayer', 'Online Examinations', 'online_examinations', 'online_examinations', 80, 14, 1, '(\'online_examination\', \'can_view\') ||  (\'question_bank\', \'can_view\'', 1, '2023-01-10 12:49:37'),
(13, 29, 'fa fa-list-alt ftlayer', 'Lesson Plan', 'lesson_plan', 'lesson_plan', 90, 16, 1, '(\'manage_lesson_plan\', \'can_view\') || (\'manage_syllabus_status\', \'can_view\') || (\'lesson\', \'can_view\') ||  (\'topic\', \'can_view\')||  (\'copy_old_lesson\', \'can_view\')', 1, '2023-01-10 12:49:37'),
(14, 7, 'fa fa-mortar-board ftlayer', 'Academics', 'academics', 'academics', 100, 15, 1, '(\'class_timetable\', \'can_view\') || (\'teachers_timetable\', \'can_view\') || (\'assign_class_teacher\', \'can_view\') || (\'promote_student\', \'can_view\') || (\'subject_group\', \'can_view\') || (\'section\', \'can_view\') || (\'subject\', \'can_view\') || (\'class\', \'can_view\') || (\'section\', \'can_view\')', 1, '2023-01-10 12:49:37'),
(15, 18, 'fa fa-sitemap ftlayer', 'Human Resource', 'human_resource', 'human_resource', 110, 17, 1, '(\'staff\', \'can_view\') || (\'approve_leave_request\', \'can_view\') || (\'apply_leave\', \'can_view\') || (\'leave_types\', \'can_view\') || (\'teachers_rating\', \'can_view\') || (\'department\', \'can_view\') || (\'designation\', \'can_view\') || (\'disable_staff\', \'can_view\')', 1, '2023-01-10 12:49:37'),
(16, 13, 'fa fa-bullhorn ftlayer', 'Communicate', 'communicate', 'communicate', 120, 18, 1, '(\'notice_board\', \'can_view\') || (\'email\', \'can_view\') || (\'sms\', \'can_view\') || (\'email_sms_log\', \'can_view\')', 1, '2023-01-10 12:49:37'),
(17, 8, 'fa fa-download ftlayer', 'Download Center', 'download_center', 'download_center', 130, 19, 1, '(\'upload_content\', \'can_view\') || (\'video_tutorial\', \'can_view\') || (\'content_type\', \'can_view\') || (\'content_share_list\', \'can_view\')', 1, '2023-01-10 12:49:37'),
(18, 19, 'fa fa-flask ftlayer', 'Homework', 'homework', 'homework', 140, 20, 1, '(\'homework\', \'can_view\') || (\'homework\', \'can_view\')', 1, '2023-01-10 12:49:37'),
(19, 9, 'fa fa-book ftlayer', 'Library', 'library', 'library', 150, 21, 1, '(\'books\', \'can_view\') || (\'issue_return\', \'can_view\') || (\'add_staff_member\', \'can_view\') || (\'add_student\', \'can_view\')', 1, '2023-01-10 12:49:37'),
(20, 10, 'fa fa-object-group ftlayer', 'Inventory', 'inventory', 'inventory', 160, 22, 1, '(\'issue_item\', \'can_view\') || (\'item_stock\', \'can_view\') || (\'item\', \'can_view\') || (\'item_category\', \'can_view\') || (\'item_category\', \'can_view\') || (\'store\', \'can_view\') || (\'supplier\', \'can_view\')', 1, '2023-01-10 12:49:37'),
(21, 11, 'fa fa-bus ftlayer', 'Transport', 'transport', 'transport', 170, 23, 1, '(\'routes\', \'can_view\') || (\'vehicle\', \'can_view\') || (\'assign_vehicle\', \'can_view\') || (\'transport_fees_master\', \'can_view\') || (\'pickup_point\', \'can_view\') || (\'route_pickup_point\', \'can_view\') || (\'student_transport_fees\', \'can_view\')      ', 1, '2023-01-10 12:49:37'),
(22, 12, 'fa fa-building-o ftlayer', 'Hostel', 'hostel', 'hostel', 180, 24, 1, '(\'hostel_rooms\', \'can_view\') || (\'room_type\', \'can_view\') || (\'hostel\', \'can_view\')', 1, '2023-01-10 12:49:37'),
(23, 20, 'fa fa-newspaper-o ftlayer', 'Certificate', 'certificate', 'certificate', 190, 25, 1, '(\'student_certificate\', \'can_view\') || (\'generate_certificate\', \'can_view\') || (\'student_id_card\', \'can_view\') || (\'generate_id_card\', \'can_view\') || (\'staff_id_card\', \'can_view\') || (\'generate_staff_id_card\', \'can_view\')', 1, '2023-01-10 12:49:37'),
(24, 16, 'fa fa-empire ftlayer', 'Front CMS', 'front_cms', 'front_cms', 200, 26, 1, '(\'event\', \'can_view\') || (\'gallery\', \'can_view\') || (\'notice\', \'can_view\') || (\'media_manager\', \'can_view\') || (\'pages\', \'can_view\') || (\'menus\', \'can_view\') || (\'banner_images\', \'can_view\')', 1, '2023-01-10 12:49:37'),
(25, 28, 'fa fa-universal-access ftlayer', 'Alumni', 'alumni', 'alumni', 210, 27, 1, '(\'manage_alumni\', \'can_view\') || (\'events\', \'can_view\')', 1, '2023-01-10 12:49:37'),
(26, 14, 'fa fa-line-chart ftlayer', 'Reports', 'reports', 'reports', 220, 28, 1, '(\'student_report\', \'can_view\') || (\'guardian_report\', \'can_view\') || (\'student_history\', \'can_view\') || (\'student_login_credential_report\', \'can_view\') || (\'class_subject_report\', \'can_view\') || (\'admission_report\', \'can_view\') || (\'sibling_report\', \'can_view\') || (\'evaluation_report\', \'can_view\') || (\'student_profile\', \'can_view\') || (\'fees_statement\', \'can_view\') || (\'balance_fees_report\', \'can_view\') || (\'fees_collection_report\', \'can_view\') || (\'online_fees_collection_report\', \'can_view\') || (\'income_report\', \'can_view\') || (\'expense_report\', \'can_view\') || (\'payroll_report\', \'can_view\') || (\'income_group_report\', \'can_view\') || (\'expense_group_report\', \'can_view\') || (\'attendance_report\', \'can_view\') || (\'staff_attendance_report\', \'can_view\') || (\'exam_marks_report\', \'can_view\') ||        (\'online_exam_wise_report\', \'can_view\') || (\'online_exams_report\', \'can_view\') || (\'online_exams_attempt_report\', \'can_view\') || (\'online_exams_rank_report\', \'can_view\') || (\'payroll_report\', \'can_view\') || (\'transport_report\', \'can_view\') || (\'hostel_report\', \'can_view\') || (\'audit_trail_report\', \'can_view\') || (\'user_log\', \'can_view\') || (\'book_issue_report\', \'can_view\') || (\'book_due_report\', \'can_view\') || (\'book_inventory_report\', \'can_view\') || (\'stock_report\', \'can_view\') ||      (\'add_item_report\', \'can_view\') || (\'issue_inventory_report\', \'can_view\') || (\'syllabus_status_report\', \'can_view\') ||    (\'teacher_syllabus_status_report\', \'can_view\') || (\'daily_collection_report\', \'can_view\') || (\'balance_fees_statement\', \'can_view\') || (\'balance_fees_report_with_remark\', \'can_view\')', 1, '2023-01-10 12:49:37'),
(27, 15, 'fa fa-gears ftlayer', 'System Settings', 'system_settings', 'system_setting', 230, 29, 1, '(\'general_setting\', \'can_view\') || (\'session_setting\', \'can_view\') || (\'notification_setting\', \'can_view\') || (\'sms_setting\', \'can_view\') || (\'email_setting\', \'can_view\') || (\'payment_methods\', \'can_view\') || (\'languages\', \'can_view\') || (\'user_status\', \'can_view\') || (\'backup_restore\', \'can_view\') || (\'print_header_footer\', \'can_view\') || (\'backup\', \'can_view\') || (\'front_cms_setting\', \'can_view\') || (\'custom_fields\', \'can_view\') || (\'system_fields\', \'can_view\') || (\'student_profile_update\', \'can_view\') || (\'currency\', \'can_view\') || (\'language_switcher\', \'can_view\') || (\'sidebar_menu\', \'can_view\') || (\'online_admission\', \'can_view\')\r\n', 1, '2023-01-10 12:49:37'),
(28, 700, 'fa fa-file-video-o ftlayer', 'Online Course', 'online_course', 'online_course', 0, 4, 1, '(\'online_course\', \'can_view\') || (\'online_course_offline_payment\', \'can_view\') || (\'student_course_purchase_report\', \'can_view\') || (\'course_sell_count_report\', \'can_view\') || (\'online_course_setting\', \'can_view\')|| (\'course_category\', \'can_view\') || (\'guest_report\', \'can_view\')|| (\'course_rating_report\', \'can_view\')', 1, '2023-01-10 19:49:51'),
(29, 600, 'fa fa-video-camera ftlayer', 'Gmeet Live Classes', 'gmeet_live_classes', 'gmeet_live_classes', 0, 6, 1, '(\'gmeet_live_classes\', \'can_view\')) || (\'gmeet_live_meeting\', \'can_view\') || (\'gmeet_live_meeting_report\', \'can_view\') || (\'gmeet_live_classes_report\', \'can_view\')', 1, '2024-11-25 07:48:24'),
(30, 500, 'fa fa-video-camera ftlayer', 'Zoom Live Classes', 'zoom_live_classes', 'zoom_live_classes', 0, 7, 1, '(\'setting\', \'can_view\') || (\'live_classes\', \'can_view\') || (\'live_meeting\', \'can_view\') || (\'live_classes_report\', \'can_view\') || (\'live_meeting_report\', \'can_view\')', 1, '2024-11-25 07:48:24'),
(31, 800, 'fa fa-map-signs ftlayer', 'Behaviour Records', 'behaviour_records', 'behaviour_records', 0, 5, 1, '(\'student_incident_report\', \'can_view\') || (\'student_behaviour_rank_report\', \'can_view\') || (\'class_wise_rank_report\', \'can_view\') || (\'class_section_wise_rank_report\', \'can_view\') || (\'house_wise_rank_report\', \'can_view\') || (\'incident_wise_report\', \'can_view\') || (\'behaviour_records_assign_incident\', \'can_view\') || (\'behaviour_records_incident\', \'can_view\') || (\'behaviour_records_setting\', \'can_view\')', 1, '2024-11-25 07:48:24'),
(32, 1100, 'fa fa-lock ftlayer', 'Two Factor Authenticator', 'two_factor_authentication', 'two_factor_authentication', 0, 4, 1, '(\'google_authenticate_setting\', \'can_view\') || (\'google_authenticate_setup_two_fa\', \'can_view\')', 1, '2025-02-11 13:00:59'),
(34, 900, 'fa fa-file-text-o', 'CBSE Examination', 'cbse_exam', 'cbse_exam', 69, 10, 1, '(\'subject_marks_report\', \'can_view\') || (\'template_marks_report\', \'can_view\') || (\'cbse_exam\', \'can_view\') ||  (\'cbse_exam_print_marksheet\', \'can_view\') ||  (\'cbse_exam_grade\', \'can_view\') ||  (\'cbse_exam_assign_observation\', \'can_view\') || (\'cbse_exam_observation\', \'can_view\') || (\'cbse_exam_observation_parameter\', \'can_view\') || (\'cbse_exam_assessment\', \'can_view\') || (\'cbse_exam_term\', \'can_view\') || (\'cbse_exam_template\', \'can_view\') || (\'cbse_exam_schedule\', \'can_view\') ', 1, '2024-11-25 07:48:24'),
(35, 1200, 'fa fa-qrcode', 'QR Code Attendance', 'qr_code_attendance', 'qr_code_attendance', 230, 13, 1, '(\'qr_code_attendance\', \'can_view\') || (\'qr_code_setting\', \'can_view\')\n\n', 1, '2023-12-11 19:28:29');

-- --------------------------------------------------------

--
-- Table structure for table `sidebar_sub_menus`
--

CREATE TABLE `sidebar_sub_menus` (
  `id` int(11) NOT NULL,
  `sidebar_menu_id` int(10) DEFAULT NULL,
  `menu` varchar(500) DEFAULT NULL,
  `key` varchar(500) DEFAULT NULL,
  `lang_key` varchar(250) DEFAULT NULL,
  `url` text DEFAULT NULL,
  `level` int(5) DEFAULT NULL,
  `access_permissions` varchar(500) DEFAULT NULL,
  `permission_group_id` int(11) DEFAULT NULL,
  `activate_controller` varchar(100) DEFAULT NULL COMMENT 'income',
  `activate_methods` varchar(500) DEFAULT NULL COMMENT 'index,edit',
  `addon_permission` varchar(100) DEFAULT NULL,
  `is_active` int(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `sidebar_sub_menus`
--

INSERT INTO `sidebar_sub_menus` (`id`, `sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `addon_permission`, `is_active`, `created_at`) VALUES
(1, 1, 'admission_enquiry', NULL, 'admission_enquiry', 'admin/enquiry', 1, '(\'admission_enquiry\', \'can_view\')', NULL, 'enquiry', 'index', NULL, 1, '2022-07-11 12:04:46'),
(2, 1, 'visitor_book', NULL, 'visitor_book', 'admin/visitors', 2, '(\'visitor_book\', \'can_view\')', NULL, 'visitors', 'index', NULL, 1, '2022-07-11 12:04:46'),
(3, 1, 'phone_call_log', NULL, 'phone_call_log', 'admin/generalcall', 3, '(\'phone_call_log\', \'can_view\')', NULL, 'generalcall', 'index,edit', NULL, 1, '2022-05-10 11:40:07'),
(4, 1, 'postal_dispatch', NULL, 'postal_dispatch', 'admin/dispatch', 4, '(\'postal_dispatch\', \'can_view\')', NULL, 'dispatch', 'index,editdispatch', NULL, 1, '2022-05-10 11:40:09'),
(5, 1, 'postal_receive', NULL, 'postal_receive', 'admin/receive', 5, '(\'postal_receive\', \'can_view\')', NULL, 'receive', 'index,editreceive', NULL, 1, '2022-05-10 11:40:09'),
(6, 1, 'complain', NULL, 'complain', 'admin/complaint', 6, '(\'complaint\', \'can_view\')', NULL, 'complaint', 'index,edit', NULL, 1, '2022-05-10 11:40:00'),
(7, 1, 'setup_front_office', NULL, 'setup_front_office', 'admin/visitorspurpose', 7, '(\'setup_font_office\', \'can_view\')', NULL, 'visitorspurpose', 'index,edit', NULL, 1, '2022-04-18 15:43:15'),
(9, 2, 'student_admission', NULL, 'student_admission', 'student/create', 2, '(\'student\', \'can_add\')', NULL, 'student', 'create,import', NULL, 1, '2022-08-30 07:51:02'),
(10, 2, 'online_admission', NULL, 'online_admission', 'admin/onlinestudent', 3, '(\'online_admission\', \'can_view\')', 27, 'onlinestudent', 'index,edit', NULL, 1, '2022-05-10 11:40:30'),
(11, 2, 'disable_student', NULL, 'disabled_students', 'student/disablestudentslist', 4, '(\'disable_student\', \'can_view\')', NULL, 'student', 'disablestudentslist', '', 1, '2022-07-23 06:49:00'),
(12, 3, 'collect_fees', NULL, 'collect_fees', 'studentfee', 1, '(\'collect_fees\', \'can_view\')', NULL, 'studentfee', 'index,addfee', NULL, 1, '2022-07-23 06:53:34'),
(13, 3, 'search_fees_payment', NULL, 'search_fees_payment', 'studentfee/searchpayment', 3, '(\'search_fees_payment\', \'can_view\')', NULL, 'studentfee', 'searchpayment', NULL, 1, '2022-08-08 06:03:40'),
(14, 3, 'search_due_fees', NULL, 'search_due_fees', 'studentfee/feesearch', 4, '(\'search_due_fees\', \'can_view\')', NULL, 'studentfee', 'feesearch', NULL, 1, '2022-08-08 06:03:38'),
(15, 3, 'fees_master', NULL, 'fees_master', 'admin/feemaster', 5, '(\'fees_master\', \'can_view\')', NULL, 'feemaster', 'index,assign,edit', NULL, 1, '2022-09-24 07:35:55'),
(16, 3, 'fees_group', NULL, 'fees_group', 'admin/feegroup', 6, '(\'fees_group\', \'can_view\')', NULL, 'feegroup', 'index,edit', NULL, 1, '2022-08-08 06:03:32'),
(17, 4, 'add_income', NULL, 'add_income', 'admin/income', 1, '(\'income\', \'can_view\')', NULL, 'income', 'index,edit', NULL, 1, '2022-07-23 07:03:40'),
(18, 4, 'search_income', NULL, 'search_income', 'admin/income/incomesearch', 2, '(\'search_income\', \'can_view\')', NULL, 'income', 'incomesearch', NULL, 1, '2022-07-23 07:10:13'),
(19, 4, 'income_head', NULL, 'income_head', 'admin/incomehead', 3, '(\'income_head\', \'can_view\')', NULL, 'incomehead', 'index,edit', NULL, 1, '2022-07-23 07:11:11'),
(20, 2, 'student_details', NULL, 'student_details', 'student/search', 1, '(\'student\', \'can_view\')', NULL, 'student', 'search,view,edit', NULL, 1, '2022-08-30 07:51:05'),
(21, 2, 'multi_class_student', NULL, 'multi_class_student', 'student/multiclass', 5, '(\'multi_class_student\', \'can_view\')', 26, 'student', 'multiclass', NULL, 1, '2022-07-23 06:48:37'),
(22, 2, 'bulk_delete', NULL, 'bulk_delete', 'student/bulkdelete', 6, '(\'student\', \'can_delete\')', NULL, 'student', 'bulkdelete', NULL, 1, '2022-07-23 06:48:11'),
(23, 2, 'student_categories', NULL, 'student_categories', 'category', 7, '(\'student_categories\', \'can_view\')', NULL, 'category', 'index,edit', NULL, 1, '2022-07-23 06:47:24'),
(24, 2, 'student_house', NULL, 'student_house', 'admin/schoolhouse', 8, '(\'student_houses\', \'can_view\')', NULL, 'schoolhouse', 'index,edit', NULL, 1, '2022-07-23 06:49:59'),
(25, 2, 'disable_reason', NULL, 'disable_reason', 'admin/disable_reason', 9, '(\'disable_reason\', \'can_view\')', NULL, 'disable_reason', 'index,edit', NULL, 1, '2022-07-23 06:50:41'),
(29, 7, 'add_expense', NULL, 'add_expense', 'admin/expense', 1, '(\'expense\', \'can_view\')', NULL, 'expense', 'index,edit', '', 1, '2022-07-23 07:12:25'),
(32, 3, 'fees_type', NULL, 'fees_type', 'admin/feetype', 7, '(\'fees_type\', \'can_view\')', NULL, 'feetype', 'index,edit', '', 1, '2022-08-08 06:03:29'),
(33, 10, 'attendance_by_date', 'attendance_by_date', 'attendance_by_date', 'admin/stuattendence/attendencereport', 3, '(\'attendance_by_date\', \'can_view\')', NULL, 'stuattendence', 'attendencereport', '', 1, '2022-10-20 05:50:25'),
(34, 10, 'student_attendance', 'student_attendance', 'student_attendance', 'admin/stuattendence', 1, '(\'student_attendance\', \'can_view\')', NULL, 'stuattendence', 'index', '', 1, '2022-10-20 05:50:25'),
(35, 10, 'approve_leave', 'approve_leave', 'approve_leave', 'admin/approve_leave', 2, '(\'approve_leave\', \'can_view\')', NULL, 'approve_leave', 'index', '', 1, '2022-10-14 16:16:44'),
(36, 11, 'exam_group', NULL, 'exam_group', 'admin/examgroup', 1, '(\'exam_group\', \'can_view\')', NULL, 'examgroup', 'index,addexam,edit', '', 1, '2022-07-23 07:23:01'),
(37, 11, 'exam_schedule', NULL, 'exam_schedule', 'admin/exam_schedule', 2, '(\'student_attendance\', \'can_view\')', NULL, 'exam_schedule', 'index', '', 1, '2022-05-16 07:01:34'),
(38, 11, 'exam_result', NULL, 'exam_result', 'admin/examresult', 3, '(\'exam_result\', \'can_view\')', NULL, 'examresult', 'index', '', 1, '2022-05-16 07:01:34'),
(39, 11, 'design_admit_card', NULL, 'design_admit_card', 'admin/admitcard', 4, '(\'design_admit_card\', \'can_view\')', NULL, 'admitcard', 'index,edit', '', 1, '2022-07-23 07:28:02'),
(40, 11, 'print_admit_card', NULL, 'print_admit_card', 'admin/examresult/admitcard', 5, '(\'print_admit_card\', \'can_view\')', NULL, 'examresult', 'admitcard', '', 1, '2022-05-16 07:01:34'),
(41, 11, 'design_marksheet', NULL, 'design_marksheet', 'admin/marksheet', 6, '(\'design_marksheet\', \'can_view\')', NULL, 'marksheet', 'index,edit', '', 1, '2022-07-23 07:35:35'),
(42, 11, 'print_marksheet', NULL, 'print_marksheet', 'admin/examresult/marksheet', 7, '(\'print_marksheet\', \'can_view\')', NULL, 'examresult', 'marksheet', '', 1, '2022-05-16 07:01:38'),
(43, 11, 'marks_grade', NULL, 'marks_grade', 'admin/grade', 8, '(\'marks_grade\', \'can_view\')', NULL, 'grade', 'index,edit', '', 1, '2022-07-23 07:37:15'),
(44, 11, 'marks_division', NULL, 'marks_division', 'admin/marksdivision', 9, '(\'marks_division\', \'can_view\')', NULL, 'marksdivision', 'index,edit', '', 1, '2022-08-25 06:04:26'),
(45, 12, 'online_exam', NULL, 'online_exam', 'admin/onlineexam', 1, '(\'online_examination\', \'can_view\')', NULL, 'onlineexam', 'index,evalution,assign', '', 1, '2022-08-30 13:03:45'),
(46, 12, 'question_bank', NULL, 'question_bank', 'admin/question', 1, '(\'question_bank\', \'can_view\')', NULL, 'question', 'index,read', '', 1, '2022-08-30 11:03:13'),
(47, 13, 'manage_lesson_plan', NULL, 'manage_lesson_plan', 'admin/syllabus', 2, '(\'manage_lesson_plan\', \'can_view\')', NULL, 'syllabus', 'index', '', 1, '2022-09-03 16:59:31'),
(48, 13, 'manage_syllabus_status', NULL, 'manage_syllabus_status', 'admin/syllabus/status', 3, '(\'manage_syllabus_status\', \'can_view\')', NULL, 'syllabus', 'status', '', 1, '2022-09-03 16:59:35'),
(49, 13, 'lesson', NULL, 'lesson', 'admin/lessonplan/lesson', 4, '(\'lesson\', \'can_view\')', NULL, 'lessonplan', 'lesson,editlesson', '', 1, '2022-09-15 11:30:55'),
(50, 13, 'topic', NULL, 'topic', 'admin/lessonplan/topic', 5, '(\'topic\', \'can_view\')', NULL, 'lessonplan', 'topic,edittopic', '', 1, '2022-09-15 11:30:24'),
(51, 14, 'class_timetable', NULL, 'class_timetable', 'admin/timetable/classreport', 1, '(\'class_timetable\', \'can_view\')', NULL, 'timetable', 'classreport,create', '', 1, '2022-07-23 09:01:22'),
(52, 14, 'teachers_timetable', NULL, 'teachers_timetable', 'admin/timetable/mytimetable', 2, '(\'teachers_time_table\', \'can_view\')', NULL, 'timetable', 'mytimetable', '', 1, '2022-07-20 12:22:59'),
(53, 14, 'assign_class_teacher', NULL, 'assign_class_teacher', 'admin/teacher/assign_class_teacher', 3, '(\'assign_class_teacher\', \'can_view\')', NULL, 'teacher', 'assign_class_teacher,update_class_teacher', '', 1, '2022-07-23 09:00:19'),
(54, 14, 'promote_students', NULL, 'promote_students', 'admin/stdtransfer', 4, '(\'promote_student\', \'can_view\')', NULL, 'stdtransfer', 'index', '', 1, '2022-07-20 12:22:54'),
(55, 14, 'subject_group', NULL, 'subject_group', 'admin/subjectgroup', 5, '(\'subject_group\', \'can_view\')', NULL, 'subjectgroup', 'index,edit', '', 1, '2022-07-23 08:59:42'),
(56, 14, 'subjects', NULL, 'subjects', 'admin/subject', 6, '(\'subject\', \'can_view\')', NULL, 'subject', 'index,edit', '', 1, '2022-07-23 08:59:20'),
(57, 14, 'class', NULL, 'class', 'classes', 7, '(\'class\', \'can_view\')', NULL, 'classes', 'index,edit', '', 1, '2022-07-23 08:58:49'),
(58, 14, 'sections', NULL, 'sections', 'sections', 8, '(\'section\', \'can_view\')', NULL, 'sections', 'index,edit', '', 1, '2022-07-23 08:58:21'),
(59, 15, 'staff_directory', NULL, 'staff_directory', 'admin/staff', 1, '(\'staff\', \'can_view\')', NULL, 'staff', 'index,edit,profile,create', '', 1, '2022-10-12 09:13:24'),
(60, 15, 'staff_attendance', NULL, 'staff_attendance', 'admin/staffattendance', 1, '(\'staff_attendance\', \'can_view\')', NULL, 'staffattendance', 'index', '', 1, '2022-09-07 12:04:15'),
(61, 15, 'payroll', NULL, 'payroll', 'admin/payroll', 1, '(\'staff_payroll\', \'can_view\')', NULL, 'payroll', 'index,edit,create', '', 1, '2022-08-16 11:58:44'),
(62, 15, 'approve_leave_request', NULL, 'approve_leave_request', 'admin/leaverequest/leaverequest', 1, '(\'approve_leave_request\', \'can_view\')', NULL, 'leaverequest', 'leaverequest', '', 1, '2022-05-16 09:04:33'),
(74, 15, 'apply_leave', NULL, 'apply_leave', 'admin/staff/leaverequest', 1, '(\'apply_leave\', \'can_view\')', NULL, 'staff', 'leaverequest', '', 1, '2022-05-16 09:11:41'),
(75, 15, 'leave_type', NULL, 'leave_type', 'admin/leavetypes', 1, '(\'leave_types\', \'can_view\')', NULL, 'leavetypes', 'index,leaveedit,createleavetype', '', 1, '2022-10-18 11:19:22'),
(76, 15, 'teachers_rating', NULL, 'teachers_rating', 'admin/staff/rating', 1, '(\'teachers_rating\', \'can_view\')', NULL, 'staff', 'rating', '', 1, '2022-05-16 09:15:31'),
(77, 15, 'department', NULL, 'department', 'admin/department/department', 1, '(\'department\', \'can_view\')', NULL, 'department', 'department,departmentedit', '', 1, '2022-07-23 09:14:20'),
(78, 15, 'designation', NULL, 'designation', 'admin/designation/designation', 1, '(\'designation\', \'can_view\')', NULL, 'designation', 'designation,designationedit', '', 1, '2022-07-23 09:15:04'),
(79, 15, 'disabled_staff', NULL, 'disabled_staff', 'admin/staff/disablestafflist', 1, '(\'disable_staff\', \'can_view\')', NULL, 'staff', 'disablestafflist', '', 1, '2022-09-13 07:46:56'),
(80, 16, 'notice_board', NULL, 'notice_board', 'admin/notification', 1, '(\'notice_board\', \'can_view\')', NULL, 'notification', 'index,edit,add', '', 1, '2022-07-23 09:17:24'),
(81, 16, 'send_email', NULL, 'send_email', 'admin/mailsms/compose', 2, '(\'email\', \'can_view\')', NULL, 'mailsms', 'compose', '', 1, '2022-09-02 16:52:46'),
(82, 16, 'send_sms', NULL, 'send_sms', 'admin/mailsms/compose_sms', 3, '(\'sms\', \'can_view\')', NULL, 'mailsms', 'compose_sms', '', 1, '2022-09-02 16:52:46'),
(83, 16, 'email_sms_log', NULL, 'email_sms_log', 'admin/mailsms/index', 4, '(\'email_sms_log\', \'can_view\')', NULL, 'mailsms', 'index', '', 1, '2022-09-02 16:52:50'),
(84, 16, 'schedule_email_sms_log', NULL, 'schedule_email_sms_log', 'admin/mailsms/schedule', 5, '(\'schedule_email_sms_log\', \'can_view\')', NULL, 'mailsms', 'schedule,edit_schedule', '', 1, '2022-09-13 07:07:38'),
(85, 16, 'login_credentials_send', NULL, 'login_credentials_send', 'student/bulkmail', 6, '(\'login_credentials_send\', \'can_view\')', NULL, 'student', 'bulkmail', '', 1, '2022-09-02 16:52:46'),
(86, 16, 'email_template', NULL, 'email_template', 'admin/mailsms/email_template', 7, '(\'email_template\', \'can_view\')', NULL, 'mailsms', 'email_template', '', 1, '2022-09-02 16:52:46'),
(87, 16, 'sms_template', NULL, 'sms_template', 'admin/mailsms/sms_template', 8, '(\'sms_template\', \'can_view\')', NULL, 'mailsms', 'sms_template', '', 1, '2022-09-02 16:52:46'),
(88, 17, 'content_type', NULL, 'content_type', 'admin/contenttype', 1, '(\'content_type\', \'can_view\')', NULL, 'contenttype', 'index,edit', '', 1, '2022-07-23 09:24:45'),
(89, 17, 'content_share_list', NULL, 'content_share_list', 'admin/content/list', 1, '(\'content_share_list\', \'can_view\')', NULL, 'content', 'list', '', 1, '2022-07-22 10:07:17'),
(90, 17, 'upload_content', NULL, 'upload_content', 'admin/content/upload', 1, '(\'upload_content\', \'can_view\')', NULL, 'content', 'upload', '', 1, '2022-07-22 10:07:17'),
(91, 17, 'video_tutorial', NULL, 'video_tutorial', 'admin/video_tutorial', 1, '(\'video_tutorial\', \'can_view\')', NULL, 'video_tutorial', 'index', '', 1, '2022-07-22 10:07:17'),
(92, 18, 'add_homework', NULL, 'add_homework', 'homework', 1, '(\'homework\', \'can_view\')', NULL, 'homework', 'index', '', 1, '2022-06-25 09:50:01'),
(93, 18, 'daily_assignment', NULL, 'daily_assignment', 'homework/dailyassignment', 2, '(\'daily_assignment\', \'can_view\')', NULL, 'homework', 'dailyassignment', '', 1, '2022-07-23 09:27:23'),
(94, 19, 'book_list', NULL, 'book_list', 'admin/book/getall', 1, '(\'books\', \'can_view\')', NULL, 'book', 'getall,index,edit,import,issue_returnreport', '', 1, '2022-09-07 11:45:50'),
(95, 19, 'issue_return', NULL, 'issue_return', 'admin/member', 1, '(\'issue_return\', \'can_view\')', NULL, 'member', 'index,issue', '', 1, '2022-07-23 09:32:48'),
(96, 19, 'add_student', NULL, 'add_student', 'admin/member/student', 1, '(\'add_student\', \'can_view\')', NULL, 'member', 'student', '', 1, '2022-05-16 11:22:54'),
(97, 19, 'add_staff_member', NULL, 'add_staff_member', 'admin/member/teacher', 1, '(\'add_staff_member\', \'can_view\')', NULL, 'member', 'teacher', '', 1, '2022-05-16 11:31:43'),
(98, 7, 'search_expense', NULL, 'search_expense', 'admin/expense/expensesearch', 1, '(\'search_expense\', \'can_view\')', NULL, 'expense', 'expensesearch', '', 1, '2022-05-16 11:36:09'),
(99, 7, 'expense_head', NULL, 'expense_head', 'admin/expensehead', 1, '(\'expense_head\', \'can_view\')', NULL, 'expensehead', 'index,edit', '', 1, '2022-07-23 07:16:17'),
(100, 20, 'issue_item', NULL, 'issue_item', 'admin/issueitem', 1, '(\'issue_item\', \'can_view\')', NULL, 'issueitem', 'index,create', '', 1, '2022-07-23 09:35:03'),
(101, 20, 'add_item_stock', NULL, 'add_item_stock', 'admin/itemstock', 1, '(\'item_stock\', \'can_view\')', NULL, 'itemstock', 'index,edit', '', 1, '2022-07-23 09:36:17'),
(102, 20, 'add_item', NULL, 'add_item', 'admin/item', 1, '(\'item\', \'can_view\')', NULL, 'item', 'index,edit', '', 1, '2022-07-23 09:36:56'),
(103, 20, 'item_category', NULL, 'item_category', 'admin/itemcategory', 1, '(\'item_category\', \'can_view\')', NULL, 'itemcategory', 'index,edit', '', 1, '2022-07-23 09:37:12'),
(104, 20, 'item_store', NULL, 'item_store', 'admin/itemstore', 1, '(\'store\', \'can_view\')', NULL, 'itemstore', 'index,edit,create', '', 1, '2022-09-16 11:49:03'),
(105, 20, 'item_supplier', NULL, 'item_supplier', 'admin/itemsupplier', 1, '(\'supplier\', \'can_view\')', NULL, 'itemsupplier', 'index,edit,create', '', 1, '2022-07-23 09:38:22'),
(106, 21, 'fees_master', NULL, 'fees_master', 'admin/transport/feemaster', 1, '(\'transport_fees_master\', \'can_view\')', NULL, 'transport', 'feemaster', '', 1, '2023-03-31 05:33:14'),
(107, 21, 'pickup_point', NULL, 'pickup_point', 'admin/pickuppoint', 1, '(\'pickup_point\', \'can_view\')', NULL, 'pickuppoint', 'index', '', 1, '2023-03-31 05:24:24'),
(108, 21, 'routes', NULL, 'routes', 'admin/route', 1, '(\'routes\', \'can_view\')', NULL, 'route', 'index,edit', '', 1, '2022-09-17 06:21:23'),
(109, 21, 'vehicles', NULL, 'vehicles', 'admin/vehicle', 1, '(\'vehicle\', \'can_view\')', NULL, 'vehicle', 'index', '', 1, '2022-05-16 12:29:35'),
(110, 21, 'assign_vehicle', NULL, 'assign_vehicle', 'admin/vehroute', 1, '(\'assign_vehicle\',\'can_view\')', NULL, 'vehroute', 'index,edit', '', 1, '2022-10-19 07:06:08'),
(111, 21, 'route_pickup_point', NULL, 'route_pickup_point', 'admin/pickuppoint/assign', 1, '(\'route_pickup_point\', \'can_view\')', NULL, 'pickuppoint', 'assign', '', 1, '2023-03-31 05:25:08'),
(112, 21, 'student_transport_fees', NULL, 'student_transport_fees', 'admin/pickuppoint/student_fees', 1, '(\'student_transport_fees\', \'can_view\')', NULL, 'pickuppoint', 'student_fees', '', 1, '2023-03-31 05:25:43'),
(113, 22, 'hostel_rooms', NULL, 'hostel_rooms', 'admin/hostelroom', 1, '(\'hostel_rooms\', \'can_view\')', NULL, 'hostelroom', 'index,edit', '', 1, '2022-07-23 10:27:48'),
(114, 22, 'room_type', NULL, 'room_type', 'admin/roomtype', 2, '(\'room_type\', \'can_view\')', NULL, 'roomtype', 'index,edit', '', 1, '2022-07-23 10:32:14'),
(115, 22, 'hostel', NULL, 'hostel', 'admin/hostel', 3, '(\'hostel\', \'can_view\')', NULL, 'hostel', 'index,edit', '', 1, '2022-07-23 10:32:39'),
(116, 23, 'student_certificate', NULL, 'student_certificate', 'admin/certificate', 1, '(\'student_certificate\', \'can_view\')', NULL, 'certificate', 'index,edit', '', 1, '2022-07-23 10:44:30'),
(117, 23, 'generate_certificate', NULL, 'generate_certificate', 'admin/generatecertificate', 1, '(\'generate_certificate\', \'can_view\')', NULL, 'generatecertificate', 'index,search', '', 1, '2022-07-23 10:46:16'),
(118, 23, 'student_id_card', NULL, 'student_id_card', 'admin/studentidcard', 1, '(\'student_id_card\', \'can_view\')', NULL, 'studentidcard', 'index,edit', '', 1, '2022-07-23 10:47:01'),
(119, 23, 'generate_id_card', NULL, 'generate_id_card', 'admin/generateidcard/search', 1, '(\'generate_id_card\', \'can_view\')', NULL, 'generateidcard', 'search', '', 1, '2022-05-18 05:35:13'),
(120, 23, 'staff_id_card', NULL, 'staff_id_card', 'admin/staffidcard', 1, '(\'staff_id_card\', \'can_view\')', NULL, 'staffidcard', 'index,edit', '', 1, '2022-07-23 10:48:13'),
(121, 23, 'generate_staff_id_card', NULL, 'generate_staff_id_card', 'admin/generatestaffidcard', 1, '(\'generate_staff_id_card\', \'can_view\')', NULL, 'generatestaffidcard', 'index,search', '', 1, '2022-07-23 10:49:06'),
(122, 24, 'event', NULL, 'event', 'admin/front/events', 1, '(\'event\', \'can_view\')', NULL, 'events', 'index,edit,create', '', 1, '2022-07-23 10:51:51'),
(123, 24, 'gallery', NULL, 'gallery', 'admin/front/gallery', 1, '(\'gallery\', \'can_view\')', NULL, 'gallery', 'index,edit,create', '', 1, '2022-07-23 10:52:22'),
(124, 24, 'news', NULL, 'news', 'admin/front/notice', 1, '(\'notice\', \'can_view\')', NULL, 'notice', 'index,edit,create', '', 1, '2022-07-23 10:54:23'),
(125, 24, 'media_manager', NULL, 'media_manager', 'admin/front/media', 1, '(\'media_manager\', \'can_view\')', NULL, 'media', 'index', '', 1, '2022-05-18 06:03:32'),
(126, 24, 'pages', NULL, 'pages', 'admin/front/page', 1, '(\'pages\', \'can_view\')', NULL, 'page', 'index,edit,create', '', 1, '2022-07-23 10:55:28'),
(127, 24, 'menus', NULL, 'menus', 'admin/front/menus', 1, '(\'menus\', \'can_view\')', NULL, 'menus', 'index,additem', '', 1, '2022-07-23 10:56:31'),
(128, 24, 'banner_images', NULL, 'banner_images', 'admin/front/banner', 1, '(\'banner_images\', \'can_view\')', NULL, 'banner', 'index', '', 1, '2022-05-18 06:10:53'),
(129, 25, 'manage_alumini', NULL, 'manage_alumini', 'admin/alumni/alumnilist', 1, '(\'manage_alumni\', \'can_view\')', NULL, 'alumni', 'alumnilist', '', 1, '2022-07-23 10:58:36'),
(130, 25, 'events', NULL, 'events', 'admin/alumni/events', 1, '(\'events\', \'can_view\')', NULL, 'alumni', 'events', '', 1, '2022-07-23 10:59:09'),
(131, 26, 'student_information', NULL, 'student_information', 'report/studentinformation', 1, '(\'student_report\', \'can_view\') || (\'guardian_report\', \'can_view\') || (\'student_history\', \'can_view\') || (\'student_login_credential_report\', \'can_view\') || (\'class_subject_report\', \'can_view\') || (\'admission_report\', \'can_view\') || (\'sibling_report\', \'can_view\') || (\'homehork_evaluation_report\', \'can_view\') || (\'student_profile\', \'can_view\') || (\'student_gender_ratio_report\', \'can_view\') || (\'student_teacher_ratio_report\', \'can_view\')', NULL, 'report', 'studentinformation,studentreport,online_admission_report,student_teacher_ratio,boys_girls_ratio,student_profile,sibling_report,admission_report,class_subject,classsectionreport,guardianreport,admissionreport,logindetailreport,parentlogindetailreport', '', 1, '2022-09-26 05:26:53'),
(132, 26, 'finance', NULL, 'finance', 'financereports/finance', 2, '(\'fees_statement\', \'can_view\') || (\'balance_fees_report\', \'can_view\') || (\'fees_collection_report\', \'can_view\') || (\'online_fees_collection_report\', \'can_view\') || (\'income_report\', \'can_view\') || (\'expense_report\', \'can_view\') || (\'payroll_report\', \'can_view\') || (\'income_group_report\', \'can_view\') || (\'expense_group_report\', \'can_view\') || (\'online_admission\', \'can_view\')', NULL, 'financereports', 'finance,reportduefees,reportdailycollection,reportbyname,studentacademicreport,collection_report,onlinefees_report,duefeesremark,income,expense,payroll,incomegroup,expensegroup,onlineadmission', '', 1, '2022-09-24 12:20:32'),
(133, 26, 'attendance', NULL, 'attendance', 'attendencereports/attendance', 3, '(\'attendance_report\', \'can_view\') || (\'student_attendance_type_report\', \'can_view\') || (\'daily_attendance_report\', \'can_view\') || (\'staff_attendance_report\', \'can_view\')', NULL, 'attendencereports', 'attendance,classattendencereport,attendancereport,daily_attendance_report,staffattendancereport,biometric_attlog,reportbymonthstudent,reportbymonth', '', 1, '2022-09-26 11:36:08'),
(134, 26, 'examinations', NULL, 'examinations', 'admin/examresult/examinations', 4, '(\'rank_report\', \'can_view\')', NULL, 'examresult', 'rankreport,examinations', '', 1, '2022-09-20 08:34:13'),
(135, 26, 'lesson_plan', NULL, 'lesson_plan', 'report/lesson_plan', 6, '(\'syllabus_status_report\', \'can_view\') || (\'teacher_syllabus_status_report\', \'can_view\')', NULL, 'report', 'lesson_plan,teachersyllabusstatus', '', 1, '2022-07-25 11:39:17'),
(136, 26, 'human_resource', NULL, 'human_resource', 'report/human_resource', 7, '(\'staff_report\', \'can_view\') || (\'payroll_report\', \'can_view\')', NULL, 'report', 'human_resource,staff_report,payrollreport', '', 1, '2022-07-25 11:38:20'),
(137, 26, 'library', NULL, 'library', 'report/library', 9, '(\'book_issue_report\', \'can_view\') || (\'book_due_report\', \'can_view\') || (\'book_issue_return_report\', \'can_view\') || (\'book_inventory_report\', \'can_view\')', NULL, 'report', 'library,studentbookissuereport,bookduereport,bookinventory', '', 1, '2022-09-07 11:53:15'),
(138, 26, 'inventory', NULL, 'inventory', 'report/inventory', 10, '(\'stock_report\', \'can_view\') || (\'add_item_report\', \'can_view\') || (\'issue_item_report\', \'can_view\')', NULL, 'report', 'inventory,inventorystock,additem,issueinventory', '', 1, '2022-07-25 11:30:57'),
(139, 26, 'hostel', NULL, 'hostel', 'admin/hostelroom/studenthosteldetails', 12, '(\'hostel_report\', \'can_view\')', NULL, 'hostelroom', 'studenthosteldetails', '', 1, '2022-07-20 12:30:07'),
(140, 26, 'alumni', NULL, 'alumni', 'report/alumnireport', 13, '(\'alumni_report\', \'can_view\')', NULL, 'report', 'alumnireport', '', 1, '2022-07-20 12:30:07'),
(141, 26, 'user_log', NULL, 'user_log', 'admin/userlog', 14, '(\'user_log\', \'can_view\')', NULL, 'userlog', 'index', '', 1, '2022-07-20 12:30:07'),
(142, 26, 'audit_trail_report', NULL, 'audit_trail_report', 'admin/audit', 15, '(\'audit_trail_report\', \'can_view\')', NULL, 'audit', 'index', '', 1, '2022-07-20 12:30:07'),
(143, 26, 'online_examinations', NULL, 'online_examinations', 'admin/onlineexam/report', 5, '(\'online_exam_wise_report\', \'can_view\') || (\'online_exams_report\', \'can_view\') || (\'online_exams_attempt_report\', \'can_view\') || (\'online_exams_rank_report\', \'can_view\')', NULL, 'onlineexam', 'report,onlineexams', '', 1, '2022-07-25 11:48:23'),
(144, 26, 'homework', NULL, 'homework', 'homework/homeworkordailyassignmentreport', 8, '(\'homework\', \'can_view\') || (\'daily_assignment\', \'can_view\')', NULL, 'homework', 'homeworkordailyassignmentreport,homeworkreport,evaluation_report,dailyassignmentreport', '', 1, '2022-09-21 09:28:47'),
(145, 26, 'transport', NULL, 'transport', 'admin/route/studenttransportdetails', 11, '(\'transport_report\', \'can_view\')', NULL, 'route', 'studenttransportdetails', '', 1, '2022-07-20 12:30:07'),
(146, 27, 'general_setting', NULL, 'general_setting', 'schsettings', 1, '(\'general_setting\', \'can_view\')', NULL, 'schsettings', 'index,logo,miscellaneous,backendtheme,mobileapp,studentguardianpanel,fees,idautogeneration,attendancetype,maintenance', '', 1, '2022-07-25 11:14:13'),
(147, 27, 'session_setting', NULL, 'session_setting', 'sessions', 2, '(\'session_setting\', \'can_view\')', NULL, 'sessions', 'index,edit', '', 1, '2022-07-23 11:57:16'),
(148, 27, 'notification_setting', NULL, 'notification_setting', 'admin/notification/setting', 3, '(\'notification_setting\', \'can_view\')', NULL, 'notification', 'setting', '', 1, '2022-07-08 08:12:28'),
(149, 27, 'sms_setting', NULL, 'sms_setting', 'smsconfig', 4, '(\'sms_setting\', \'can_view\')', NULL, 'smsconfig', 'index', '', 1, '2022-07-08 08:12:28'),
(150, 27, 'email_setting', NULL, 'email_setting', 'emailconfig', 5, '(\'email_setting\', \'can_view\')', NULL, 'emailconfig', 'index', '', 1, '2022-07-08 08:12:28'),
(151, 27, 'payment_methods', NULL, 'payment_methods', 'admin/paymentsettings', 6, '(\'payment_methods\', \'can_view\')', NULL, 'paymentsettings', 'index', '', 1, '2022-07-08 08:12:28'),
(152, 27, 'print_headerfooter', NULL, 'print_headerfooter', 'admin/print_headerfooter', 7, '(\'print_header_footer\', \'can_view\')', NULL, 'print_headerfooter', 'index', '', 1, '2022-07-08 08:12:28'),
(153, 27, 'front_cms_setting', NULL, 'front_cms_setting', 'admin/frontcms', 8, '(\'front_cms_setting\', \'can_view\')', NULL, 'frontcms', 'index', '', 1, '2022-07-08 08:12:28'),
(154, 27, 'roles_permissions', NULL, 'roles_permissions', 'admin/roles', 9, '(\'superadmin\', \'can_view\')', NULL, 'roles', 'index,permission', '', 1, '2022-09-09 11:03:34'),
(155, 27, 'backup_restore', NULL, 'backup_restore', 'admin/admin/backup', 10, '(\'backup\', \'can_view\')', NULL, 'admin', 'backup', '', 1, '2022-07-08 08:12:28'),
(156, 27, 'users', NULL, 'users', 'admin/users', 13, '(\'user_status\', \'can_view\')', NULL, 'users', 'index', '', 1, '2022-07-20 12:34:09'),
(157, 27, 'languages', NULL, 'languages', 'admin/language', 11, '(\'languages\', \'can_view\')', NULL, 'language', 'index,create', '', 1, '2022-09-10 09:14:52'),
(158, 27, 'modules', NULL, 'modules', 'admin/module', 14, '(\'superadmin\', \'can_view\')', NULL, 'module', 'index', '', 1, '2022-07-20 12:34:06'),
(159, 27, 'custom_fields', NULL, 'custom_fields', 'admin/customfield', 15, '(\'custom_fields\', \'can_view\')', NULL, 'customfield', 'index,edit', '', 1, '2022-07-23 12:02:14'),
(160, 27, 'captcha_setting', NULL, 'captcha_setting', 'admin/captcha', 16, '(\'superadmin\', \'can_view\')', NULL, 'captcha', 'index', '', 1, '2022-07-20 12:34:06'),
(161, 27, 'system_fields', NULL, 'system_fields', 'admin/systemfield', 17, '(\'system_fields\', \'can_view\')', NULL, 'systemfield', 'index', '', 1, '2022-07-22 06:07:38'),
(162, 27, 'student_profile_update', NULL, 'student_profile_update', 'student/profilesetting', 18, '(\'student_profile_update\', \'can_view\')', NULL, 'student', 'profilesetting', '', 1, '2022-07-20 12:34:06'),
(163, 27, 'online_admission', NULL, 'online_admission', 'admin/onlineadmission/admissionsetting', 19, '(\'online_admission\', \'can_view\')', NULL, 'onlineadmission', 'admissionsetting', '', 1, '2022-07-20 12:34:06'),
(164, 27, 'file_types', NULL, 'file_types', 'admin/admin/filetype', 20, '(\'superadmin\', \'can_view\')', NULL, 'admin', 'filetype', '', 1, '2022-07-20 12:34:30'),
(165, 27, 'system_update', NULL, 'system_update', 'admin/updater', 22, '(\'superadmin\', \'can_view\')', NULL, 'updater', 'index', '', 0, '2022-10-13 11:49:51'),
(166, 27, 'sidebar_menu', NULL, 'sidebar_menu', 'admin/sidemenu', 21, '(\'sidebar_menu\', \'can_view\')', NULL, 'sidemenu', 'index', '', 1, '2022-10-13 11:49:51'),
(167, 28, 'online_course', NULL, 'online_course', 'onlinecourse/course/index', 1, '(\'online_course\', \'can_view\')', NULL, 'course', 'index', NULL, 1, '2024-11-28 07:02:03'),
(168, 28, 'offline_payment', NULL, 'offline_payment', 'onlinecourse/offlinepayment/payment', 2, '(\'online_course_offline_payment\', \'can_view\')', NULL, 'offlinepayment', 'payment', NULL, 1, '2024-11-28 07:02:05'),
(169, 28, 'course_category', NULL, 'course_category', 'onlinecourse/coursecategory/categoryadd', 3, '(\'course_category\', \'can_view\') || (\'course_category\', \'can_add\') || (\'course_category\', \'can_edit\') || (\'course_category\', \'can_delete\')', NULL, 'coursecategory', 'categoryadd,categoryedit', NULL, 1, '2024-11-28 07:03:10'),
(170, 29, 'live_class', NULL, 'live_class', 'admin/gmeet/timetable', 1, '(\'gmeet_live_classes\', \'can_view\')', NULL, 'gmeet', 'timetable', NULL, 1, '2024-11-28 07:00:51'),
(171, 29, 'live_meeting', NULL, 'live_meeting', 'admin/gmeet/meeting', 2, '(\'gmeet_live_meeting\', \'can_view\')', NULL, 'gmeet', 'meeting', NULL, 1, '2024-11-28 07:00:54'),
(172, 29, 'live_class_report', NULL, 'live_class_report', 'admin/gmeet/class_report', 3, '(\'gmeet_live_classes_report\', \'can_view\')', NULL, 'gmeet', 'class_report', NULL, 1, '2024-11-28 07:00:57'),
(173, 29, 'live_meeting_report', NULL, 'live_meeting_report', 'admin/gmeet/meeting_report', 4, '(\'gmeet_live_meeting_report\', \'can_view\')', NULL, 'gmeet', 'meeting_report', NULL, 1, '2024-11-28 07:00:59'),
(174, 29, 'setting', NULL, 'setting', 'admin/gmeet/index', 5, '(\'gmeet_setting\', \'can_view\')', NULL, 'gmeet', 'index', '', 1, '2022-07-21 18:28:45'),
(175, 30, 'live_class', NULL, 'live_class', 'admin/conference/timetable', 2, '(\'live_classes\', \'can_view\')', NULL, 'conference', 'timetable', NULL, 1, '2024-11-28 06:59:38'),
(176, 30, 'live_meeting', NULL, 'live_meeting', 'admin/conference/meeting', 1, '(\'live_meeting\', \'can_view\')', NULL, 'conference', 'meeting', NULL, 1, '2024-11-28 06:59:44'),
(177, 30, 'live_class_report', NULL, 'live_class_report', 'admin/conference/class_report', 3, '(\'live_classes_report\', \'can_view\')', NULL, 'conference', 'class_report', NULL, 1, '2024-11-28 06:59:46'),
(178, 30, 'live_meeting_report', NULL, 'live_meeting_report', 'admin/conference/meeting_report', 4, '(\'live_meeting_report\', \'can_view\')', NULL, 'conference', 'meeting_report', NULL, 1, '2024-11-28 06:59:50'),
(179, 30, 'setting', NULL, 'setting', 'admin/conference', 5, '(\'setting\', \'can_view\')', NULL, 'conference', 'index', '', 1, '2022-07-11 18:38:41'),
(180, 28, 'online_course_report', NULL, 'online_course_report', 'onlinecourse/coursereport/report', 4, '(\'student_course_purchase_report\', \'can_view\') || (\'course_sell_count_report\', \'can_view\') || (\'course_trending_report\', \'can_view\') || (\'course_complete_report\', \'can_view\')  || (\'course_rating_report\', \'can_view\')  || (\'guest_report\', \'can_view\')', NULL, 'coursereport', 'report,coursepurchase,coursesellreport,trendingreport,completereport,courseratingreport,guestlist,quizperformance', NULL, 1, '2024-11-28 07:03:04'),
(181, 3, 'fees_discount', NULL, 'fees_discount', 'admin/feediscount', 8, '(\'fees_discount\', \'can_view\')', NULL, 'feediscount', 'index,edit,assign', '', 1, '2022-08-08 06:03:27'),
(182, 3, 'fees_carry_forward', NULL, 'fees_carry_forward', 'admin/feesforward', 9, '(\'fees_carry_forward\', \'can_view\')', NULL, 'feesforward', 'index', '', 1, '2022-08-08 06:03:24'),
(183, 3, 'fees_reminder', NULL, 'fees_reminder', 'admin/feereminder/setting', 10, '(\'fees_reminder\', \'can_view\')', NULL, 'feereminder', 'setting', '', 1, '2022-08-08 06:03:21'),
(184, 27, 'currency', NULL, 'currency', 'admin/currency', 12, '(\'currency\', \'can_view\')', NULL, 'currency', 'index', '', 1, '2022-07-20 12:34:09'),
(186, 31, 'assign_incident', NULL, 'assign_incident', 'behaviour/studentincidents', 1, '(\'behaviour_records_assign_incident\', \'can_view\')', NULL, 'studentincidents', 'index', NULL, 1, '2024-11-28 07:02:58'),
(187, 31, 'incidents', NULL, 'incidents', 'behaviour/incidents', 1, '(\'behaviour_records_incident\', \'can_view\')', NULL, 'incidents', 'index', NULL, 1, '2024-11-28 07:02:54'),
(188, 31, 'reports', NULL, 'reports', 'behaviour/report', 1, '(\'student_incident_report\', \'can_view\') || (\'student_behaviour_rank_report\', \'can_view\') || (\'class_wise_rank_report\', \'can_view\') || (\'class_section_wise_rank_report\', \'can_view\') || (\'house_wise_rank_report\', \'can_view\') || (\'incident_wise_report\', \'can_view\')', NULL, 'report', 'index,studentincidentreport,studentbehaviorsrankreport,classwiserankreport,classsectionwiserank,housewiserank,incidentwisereport', NULL, 1, '2024-11-28 07:02:50'),
(189, 31, 'setting', NULL, 'setting', 'behaviour/setting', 1, '(\'behaviour_records_setting\', \'can_view\')', NULL, 'setting', 'index', '', 1, '2022-09-06 20:02:20'),
(190, 3, 'offline_bank_payments', NULL, 'offline_bank_payments', 'admin/offlinepayment', 2, '(\'offline_bank_payments\', \'can_view\')', NULL, 'offlinepayment', 'index', '', 1, '2022-08-08 06:05:29'),
(191, 13, 'Copy Old Lessons', NULL, 'copy_old_lesson', 'admin/lessonplan/copylesson', 1, '(\'copy_old_lesson\', \'can_view\')', NULL, 'lessonplan', 'copylesson', NULL, 1, '2022-09-09 10:20:37'),
(192, 10, 'Period Attendance', 'period_attendance', 'period_attendance', 'admin/subjectattendence/index', 4, '(\'student_attendance\',\'can_view\')', NULL, 'subjectattendence', 'index', NULL, 0, '2022-10-20 05:50:25'),
(193, 10, 'Period Attendance By Date', 'period_attendance_by_date', 'period_attendance_by_date', 'admin/subjectattendence/reportbydate', 5, '(\'attendance_by_date\', \'can_view\')', NULL, 'subjectattendence', 'reportbydate', NULL, 0, '2022-10-20 05:50:25'),
(194, 32, 'setup_two_fa', NULL, 'setup_two_fa', 'admin/gauthenticate/admin/setup', 1, '(\'google_authenticate_setup_two_fa\', \'can_view\')', NULL, 'admin', 'setup', NULL, 1, '2024-11-28 07:02:46'),
(195, 32, 'setting', NULL, 'setting', 'admin/gauthenticate/admin', 1, '(\'google_authenticate_setting\', \'can_view\')', NULL, 'admin', 'index', '', 1, '2022-11-21 16:01:34'),
(197, 28, 'setting', NULL, 'setting', 'onlinecourse/course/setting', 5, '(\'online_course_setting\', \'can_view\')', NULL, 'course', 'setting', '', 1, '2022-07-22 08:13:30'),
(198, 33, 'overview', NULL, 'overview', 'admin/multibranch/branch/overview', 1, '(\'multi_branch_overview\', \'can_view\')', NULL, 'branch', 'overview', NULL, 1, '2025-02-11 13:06:49'),
(199, 33, 'report', NULL, 'report', 'admin/multibranch/finance/index', 1, '(\'multi_branch_daily_collection_report\', \'can_view\') || (\'multi_branch_payroll\', \'can_view\') || (\'multi_branch_income_report\', \'can_view\') || (\'multi_branch_expense_report\', \'can_view\') || (\'multi_branch_user_log_report\', \'can_view\')', NULL, 'finance', 'dailycollectionreport,payroll,incomelist,expenselist,incomereport,expensereport,userlogreport,index', NULL, 1, '2025-02-11 12:58:29'),
(200, 33, 'setting', NULL, 'setting', 'admin/multibranch/branch', 1, '(\'multi_branch_setting\', \'can_view\')', NULL, 'branch', 'index', '', 1, '2022-11-15 12:45:32'),
(201, 34, 'exam', NULL, 'exam', 'cbseexam/exam', 1, '(\'cbse_exam\', \'can_view\')', NULL, 'exam', 'index,examwiserank', NULL, 1, '2024-11-28 07:02:42'),
(202, 34, 'exam_schedule', NULL, 'exam_schedule', 'cbseexam/exam/examtimetable', 1, '(\'cbse_exam_schedule\', \'can_view\')', NULL, 'exam', 'examtimetable', NULL, 1, '2024-11-28 07:02:38'),
(203, 34, 'print_marksheet', NULL, 'print_marksheet', 'cbseexam/result/marksheet', 1, '(\'cbse_exam_print_marksheet\', \'can_view\')', NULL, 'result', 'marksheet', NULL, 1, '2024-11-28 07:02:35'),
(204, 34, 'exam_grade', NULL, 'exam_grade', 'cbseexam/grade/gradelist', 1, '(\'cbse_exam_grade\', \'can_view\')', NULL, 'grade', 'gradelist', NULL, 1, '2024-11-28 07:02:32'),
(205, 34, 'assign_observation', NULL, 'assign_observation', 'cbseexam/observation/assign', 1, '(\'cbse_exam_assign_observation\', \'can_view\')', NULL, 'observation', 'assign', NULL, 1, '2024-11-28 07:02:28'),
(206, 34, 'observation', NULL, 'observation', 'cbseexam/observation', 1, '(\'cbse_exam_observation\', \'can_view\')', NULL, 'observation', 'index', NULL, 1, '2024-11-28 07:02:26'),
(207, 34, 'observation_parameter', NULL, 'observation_parameter', 'cbseexam/observationparameter', 1, '(\'cbse_exam_observation_parameter\', \'can_view\')', NULL, 'observationparameter', 'index,edit', NULL, 1, '2024-11-28 07:02:24'),
(208, 34, 'assessment', NULL, 'assessment', 'cbseexam/assessment', 1, '(\'cbse_exam_assessment\', \'can_view\')', NULL, 'assessment', 'index', NULL, 1, '2024-11-28 07:02:21'),
(209, 34, 'term', NULL, 'term', 'cbseexam/term', 1, '(\'cbse_exam_term\', \'can_view\')', NULL, 'term', 'index', NULL, 1, '2024-11-28 07:02:18'),
(210, 34, 'template', NULL, 'template', 'cbseexam/template', 1, '(\'cbse_exam_template\', \'can_view\')', NULL, 'template', 'index,templatewiserank', NULL, 1, '2024-11-28 07:02:16'),
(211, 34, 'reports', NULL, 'reports', 'cbseexam/report/index', 1, '(\'subject_marks_report\', \'can_view\') || (\'template_marks_report\', \'can_view\')', NULL, 'report', 'index,templatewise,examsubject', NULL, 1, '2024-11-28 07:02:14'),
(212, 34, 'setting', NULL, 'setting', 'cbseexam/setting/index', 1, '(\'cbse_exam_setting\', \'can_view\')', NULL, 'setting', 'index', '', 1, '2023-07-03 12:26:03'),
(213, 35, 'attendance', NULL, 'attendance', 'admin/qrattendance/attendance/index', 1, '(\'qr_code_attendance\', \'can_view\')', NULL, 'attendance', 'index', NULL, 1, '2024-11-28 07:02:11'),
(214, 35, 'setting', NULL, 'setting', 'admin/qrattendance/setting/index', 2, '(\'qr_code_setting\', \'can_view\')', NULL, 'setting', 'index', NULL, 1, '2023-12-11 19:38:27');

-- --------------------------------------------------------

--
-- Table structure for table `sms_config`
--

CREATE TABLE `sms_config` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `api_id` varchar(100) NOT NULL,
  `authkey` varchar(100) NOT NULL,
  `senderid` varchar(100) NOT NULL,
  `contact` text DEFAULT NULL,
  `username` varchar(150) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'disabled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sms_template`
--

CREATE TABLE `sms_template` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `sms_template`
--

INSERT INTO `sms_template` (`id`, `title`, `message`, `created_at`) VALUES
(1, 'National Republic Day', 'India celebrated its 73rd National Republic Day on the 26th of January, 2022. The constitution of India was adopted on the 26th of November, 1949 with Dr. B. R. Ambedkar as the Chairman of the Drafting Committee.', '2024-11-25'),
(2, 'Sports Day Events', 'Games that are played on school sports days can be wide and varied. They can include straightforward sprints and longer races for all age groups as well as egg and spoon races.', '2024-11-25'),
(3, 'Independence Day', 'I cordially invite you to the 76th independence day celebrations in our school on 15th August. The program will have cultural activities by our students and flag hoisting.', '2024-11-25'),
(4, 'New Academic Session(2023-24)', 'The Central Board of Secondary Education (CBSE) will begin the new academic session of 2023-24 from April 1, 2023.', '2024-11-25');

-- --------------------------------------------------------

--
-- Table structure for table `source`
--

CREATE TABLE `source` (
  `id` int(11) NOT NULL,
  `source` varchar(100) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `source`
--

INSERT INTO `source` (`id`, `source`, `description`) VALUES
(1, 'Advertisement', ''),
(2, 'Online Front Site', ''),
(3, 'Google Ads', ''),
(4, 'Admission Campaign', '');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(200) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `currency_id` int(11) DEFAULT 0,
  `department` int(11) DEFAULT NULL,
  `designation` int(11) DEFAULT NULL,
  `qualification` varchar(200) NOT NULL,
  `work_exp` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `surname` varchar(200) NOT NULL,
  `father_name` varchar(200) NOT NULL,
  `mother_name` varchar(200) NOT NULL,
  `contact_no` varchar(200) NOT NULL,
  `emergency_contact_no` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `dob` date NOT NULL,
  `marital_status` varchar(100) NOT NULL,
  `date_of_joining` date DEFAULT NULL,
  `date_of_leaving` date DEFAULT NULL,
  `local_address` varchar(300) NOT NULL,
  `permanent_address` varchar(200) NOT NULL,
  `note` varchar(200) NOT NULL,
  `image` varchar(200) NOT NULL,
  `password` varchar(250) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `account_title` varchar(200) NOT NULL,
  `bank_account_no` varchar(200) NOT NULL,
  `bank_name` varchar(200) NOT NULL,
  `ifsc_code` varchar(200) NOT NULL,
  `bank_branch` varchar(100) NOT NULL,
  `payscale` varchar(200) NOT NULL,
  `basic_salary` int(11) DEFAULT NULL,
  `epf_no` varchar(200) NOT NULL,
  `contract_type` varchar(100) NOT NULL,
  `shift` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `facebook` varchar(200) NOT NULL,
  `twitter` varchar(200) NOT NULL,
  `linkedin` varchar(200) NOT NULL,
  `instagram` varchar(200) NOT NULL,
  `resume` varchar(200) NOT NULL,
  `joining_letter` varchar(200) NOT NULL,
  `resignation_letter` varchar(200) NOT NULL,
  `other_document_name` varchar(200) NOT NULL,
  `other_document_file` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_active` int(11) NOT NULL,
  `verification_code` varchar(100) NOT NULL,
  `zoom_api_key` varchar(100) DEFAULT NULL,
  `zoom_api_secret` varchar(100) DEFAULT NULL,
  `disable_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `employee_id`, `lang_id`, `currency_id`, `department`, `designation`, `qualification`, `work_exp`, `name`, `surname`, `father_name`, `mother_name`, `contact_no`, `emergency_contact_no`, `email`, `dob`, `marital_status`, `date_of_joining`, `date_of_leaving`, `local_address`, `permanent_address`, `note`, `image`, `password`, `gender`, `account_title`, `bank_account_no`, `bank_name`, `ifsc_code`, `bank_branch`, `payscale`, `basic_salary`, `epf_no`, `contract_type`, `shift`, `location`, `facebook`, `twitter`, `linkedin`, `instagram`, `resume`, `joining_letter`, `resignation_letter`, `other_document_name`, `other_document_file`, `user_id`, `is_active`, `verification_code`, `zoom_api_key`, `zoom_api_secret`, `disable_at`) VALUES
(1, '9000', 0, 0, NULL, NULL, '', '', 'Super Admin', '', '', '', '', '', 'superadmin@gmail.com', '2020-01-01', '', NULL, NULL, '', '', '', '1733736522-6087058426756b84ae7bc4!12.jpg', '$2y$10$Nn1z4HXhKdq37/86uppKgOOSAqdbZMITVV.nlydEWeXRzYrVjJmdu', 'Male', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', 'Other Document', '', 0, 1, '', NULL, NULL, NULL),
(2, '1234', 0, 0, 4, 1, '', '', 'Hyphen', 'Ptael', '', '', '2345678', '0987654345', 'gita@gmai.com', '2024-11-20', 'Single', '2024-11-05', NULL, '', '', '', '1732533739-114541252667445deb98b08!5.jpg', '$2y$10$FdBNkrRtUyiAzDtUKmWbCe.LRaKbWV1L5lhLJu.O0dOcf8TxP6vDW', 'Male', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', 'Other Document', '', 0, 1, '', NULL, NULL, NULL),
(3, '23', 0, 0, 6, 2, '', '', 'Phynes', '', '', '', '', '', 'phynes@gmail.com', '2024-11-20', '', '2024-11-28', NULL, '', '', '', '1732533869-137536927067445e6d74fd5!13.jpg', '$2y$10$qvbQ3IkpMcANBiJtgT.jVOUWlg1zWVhs7gxN0PExBP.lcmCyxHH9K', 'Male', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', 'Other Document', '', 0, 1, '', NULL, NULL, NULL),
(4, '9005', 0, 0, 8, 8, 'MS', '15 Yrs', 'Jensi', 'Black', 'Will Black', 'Mini Black', '6545645645', '54654644', 'maria.ford@gmail.com', '1988-01-01', 'Married', '2010-03-11', NULL, '9837 Temple Apartment', '9837 Temple Apartment', '', '1732533947-120641652067445ebb639bb!41.jpg', '$2y$10$X8WLhLDGihsnBkPYFTH5UO95MZQDcHxGbivPbgmo99L2Bjqhuiluq', 'Female', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 1, '', NULL, NULL, NULL),
(5, '54545454', 0, 0, 10, 2, 'Graduate', '5', 'Vishwa', 'Patel', '', '', '', '', 'Alberti12@gmail.com', '2000-02-09', '', NULL, NULL, '', '', '', '1732534155-76219281767445f8b9f6d3!38.jpg', '$2y$10$G2d6x0EcaQU7d1HtaUFmqe68yd40kFdfbeDfoSGsYE5pB2/LMAd4G', 'Female', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', 'Other Document', '', 0, 1, '', NULL, NULL, NULL),
(6, '2000', 0, 0, 8, 8, 'MS', '15 Yrs', 'Joe', 'Black', '', '', '6545645645', '54654644', 'superadmin1@gmail.com', '2010-01-11', 'Married', '2010-03-11', NULL, '9837 Temple Apartment', '9837 Temple Apartment', '', '', '$2y$10$VSbIIJmdviH0pg1BwQJrqeXGs./3a57.j1GgXLBRX0ZNCYaUgdbwG', 'Male', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 1, '', NULL, NULL, NULL),
(7, '9002', 0, 0, 1, 1, 'Msc ', '12 yrs', 'Shivam', 'Verma', 'Pulkit Verma', '', '9552654564', '544454747', 'manisha@gmail.com', '2024-12-03', 'Married', '2024-12-17', NULL, '097 Avon Apartment', '097 Avon Apartment', '', '1733736642-21255502856756b8c20b903!19.jpg', '$2y$10$lFtydD17egXtMK0d/HeOGenSHfYqBStcrLP4O/uVfBRf36FXvkXeC', 'Male', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 1, '', NULL, NULL, NULL),
(8, '9006', 0, 0, 2, 7, 'B.Lib.', '6 year ', 'Brandon', 'Heart', 'Joseph Heart', 'Maxy Heart', '34564654', '456456456', 'brandon@gmail.com', '1988-03-04', 'Married', '2011-09-09', NULL, 'Boys Hostel', '323 Dox Street', '', '1733736870-2082270426756b9a63e101!22.jpg', '$2y$10$NeaPgUgIeeG4C0Zd6ULpxeTloY4E8tzHWDtlhR3IWr1WgPDdPdzS6', 'Male', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 1, '', NULL, NULL, NULL),
(9, '9004', 0, 0, 9, 2, 'B.Com.', '5 Yrs', 'James', 'Deckar', 'Manu Deckar', '', '79786546463', '', 'james.deckar@gmail.com', '2024-12-02', 'Not Specified', '2024-12-24', NULL, '232, Saint Street, Boston, CA', '232, Saint Street, Boston, CA', '', '1733737050-8574890516756ba5a9ec9f!42.jpg', '$2y$10$wjLl8A2jTErQMIcLIcdx7Obfq57ouNjOwCWShSCy3710FGYdf6OV2', 'Male', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 1, '', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff_attendance`
--

CREATE TABLE `staff_attendance` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `staff_id` int(11) NOT NULL,
  `staff_attendance_type_id` int(11) NOT NULL,
  `biometric_attendence` int(11) DEFAULT 0,
  `qrcode_attendance` int(11) NOT NULL DEFAULT 0,
  `biometric_device_data` text DEFAULT NULL,
  `user_agent` varchar(250) DEFAULT NULL,
  `remark` varchar(200) NOT NULL,
  `is_active` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_attendance_type`
--

CREATE TABLE `staff_attendance_type` (
  `id` int(11) NOT NULL,
  `type` varchar(200) NOT NULL,
  `key_value` varchar(200) NOT NULL,
  `is_active` varchar(50) NOT NULL,
  `for_qr_attendance` int(11) NOT NULL DEFAULT 1,
  `long_lang_name` varchar(250) DEFAULT NULL,
  `long_name_style` varchar(250) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `staff_attendance_type`
--

INSERT INTO `staff_attendance_type` (`id`, `type`, `key_value`, `is_active`, `for_qr_attendance`, `long_lang_name`, `long_name_style`, `created_at`, `updated_at`) VALUES
(1, 'Present', '<b class=\"text text-success\">P</b>', 'yes', 1, 'present', 'label label-success', '2023-12-13 10:15:57', '2023-11-10'),
(2, 'Late', '<b class=\"text text-warning\">L</b>', 'yes', 1, 'late', 'label label-warning', '2023-12-13 10:16:01', '2023-11-10'),
(3, 'Absent', '<b class=\"text text-danger\">A</b>', 'yes', 0, 'absent', 'label label-danger', '2023-12-13 10:16:06', '2023-11-10'),
(4, 'Half Day', '<b class=\"text text-warning\">F</b>', 'yes', 1, 'half_day', 'label label-info', '2023-12-14 12:57:07', '2023-11-10'),
(5, 'Holiday', 'H', 'yes', 0, 'holiday', 'label label-warning text-dark', '2023-12-13 10:16:17', '2023-11-10');

-- --------------------------------------------------------

--
-- Table structure for table `staff_designation`
--

CREATE TABLE `staff_designation` (
  `id` int(11) NOT NULL,
  `designation` varchar(200) NOT NULL,
  `is_active` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `staff_designation`
--

INSERT INTO `staff_designation` (`id`, `designation`, `is_active`) VALUES
(1, 'Faculty', 'yes'),
(2, 'Accountant	', 'yes'),
(3, 'Admin', 'yes'),
(4, 'Receptionist', 'yes'),
(5, 'Principal	', 'yes'),
(6, 'Director	', 'yes'),
(7, 'Librarian', 'yes'),
(8, 'Technical Head	', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `staff_id_card`
--

CREATE TABLE `staff_id_card` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `school_address` varchar(255) NOT NULL,
  `background` varchar(100) NOT NULL,
  `logo` varchar(100) NOT NULL,
  `sign_image` varchar(100) NOT NULL,
  `header_color` varchar(100) NOT NULL,
  `enable_vertical_card` int(11) NOT NULL DEFAULT 0,
  `enable_staff_role` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_staff_id` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_staff_department` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_designation` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_name` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_fathers_name` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_mothers_name` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_date_of_joining` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_permanent_address` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_staff_dob` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_staff_phone` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_staff_barcode` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `status` tinyint(1) NOT NULL COMMENT '0=disable,1=enable'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `staff_id_card`
--

INSERT INTO `staff_id_card` (`id`, `title`, `school_name`, `school_address`, `background`, `logo`, `sign_image`, `header_color`, `enable_vertical_card`, `enable_staff_role`, `enable_staff_id`, `enable_staff_department`, `enable_designation`, `enable_name`, `enable_fathers_name`, `enable_mothers_name`, `enable_date_of_joining`, `enable_permanent_address`, `enable_staff_dob`, `enable_staff_phone`, `enable_staff_barcode`, `status`) VALUES
(1, 'Sample Staff ID Card Horizontal', 'Mount Carmel School', '110 Kings Street, CA', 'background1.png', 'logo1.png', 'sign1.png', '#9b1818', 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 1),
(2, 'Sample Staff ID Card Vertical', 'Mount Carmel School', '110 Kings Street, CA', 'background1.png', 'logo1.png', 'sign1.png', '#9b1818', 1, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 1),
(3, 'New Sample Staff ID Card', 'Mount Carmel School', '110 Kings Street, CA  Phone: 456542 Email: mount@gmail.com', '', '', '', '#000000', 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(5, 'sample', 'your school', '987654678', '', '', '', '#734545', 0, 0, 1, 1, 0, 1, 0, 0, 1, 0, 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `staff_leave_details`
--

CREATE TABLE `staff_leave_details` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `alloted_leave` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `staff_leave_details`
--

INSERT INTO `staff_leave_details` (`id`, `staff_id`, `leave_type_id`, `alloted_leave`) VALUES
(1, 2, 1, ''),
(2, 2, 2, ''),
(3, 2, 3, ''),
(4, 2, 4, ''),
(5, 3, 1, ''),
(6, 3, 2, ''),
(7, 3, 3, ''),
(8, 3, 4, ''),
(9, 4, 1, ''),
(10, 4, 2, ''),
(11, 4, 3, ''),
(12, 4, 4, ''),
(13, 5, 3, ''),
(14, 5, 1, ''),
(15, 5, 2, ''),
(16, 5, 4, ''),
(17, 6, 1, ''),
(18, 6, 2, ''),
(19, 6, 3, ''),
(20, 6, 4, ''),
(21, 1, 3, ''),
(22, 1, 1, ''),
(23, 1, 2, ''),
(24, 1, 4, ''),
(25, 7, 1, ''),
(26, 7, 2, ''),
(27, 7, 3, ''),
(28, 7, 4, ''),
(29, 8, 1, ''),
(30, 8, 2, ''),
(31, 8, 3, ''),
(32, 8, 4, ''),
(33, 9, 1, ''),
(34, 9, 2, ''),
(35, 9, 3, ''),
(36, 9, 4, '');

-- --------------------------------------------------------

--
-- Table structure for table `staff_leave_request`
--

CREATE TABLE `staff_leave_request` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `leave_from` date NOT NULL,
  `leave_to` date NOT NULL,
  `leave_days` int(11) NOT NULL,
  `employee_remark` varchar(200) NOT NULL,
  `admin_remark` varchar(200) NOT NULL,
  `status` varchar(50) NOT NULL,
  `applied_by` int(11) DEFAULT NULL,
  `document_file` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_payroll`
--

CREATE TABLE `staff_payroll` (
  `id` int(11) NOT NULL,
  `basic_salary` int(11) NOT NULL,
  `pay_scale` varchar(200) NOT NULL,
  `grade` varchar(50) NOT NULL,
  `is_active` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_payslip`
--

CREATE TABLE `staff_payslip` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `basic` float(10,2) NOT NULL,
  `total_allowance` float(10,2) NOT NULL,
  `total_deduction` float(10,2) NOT NULL,
  `leave_deduction` int(11) NOT NULL,
  `tax` varchar(200) NOT NULL,
  `net_salary` float(10,2) NOT NULL,
  `status` varchar(100) NOT NULL,
  `month` varchar(200) NOT NULL,
  `year` varchar(200) NOT NULL,
  `payment_mode` varchar(200) NOT NULL,
  `payment_date` date NOT NULL,
  `remark` varchar(200) NOT NULL,
  `generated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff_rating`
--

CREATE TABLE `staff_rating` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `rate` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(255) NOT NULL,
  `status` int(11) NOT NULL COMMENT '0 decline, 1 Approve',
  `entrydt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `staff_rating`
--

INSERT INTO `staff_rating` (`id`, `staff_id`, `comment`, `rate`, `user_id`, `role`, `status`, `entrydt`) VALUES
(1, 2, 'great', 5, 1, 'student', 0, '2024-12-23 11:32:30'),
(2, 3, 'good', 5, 1, 'student', 0, '2024-12-23 11:32:43');

-- --------------------------------------------------------

--
-- Table structure for table `staff_roles`
--

CREATE TABLE `staff_roles` (
  `id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `is_active` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `staff_roles`
--

INSERT INTO `staff_roles` (`id`, `role_id`, `staff_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 7, 1, 0, '2024-11-23 11:42:27', NULL),
(2, 2, 2, 0, '2024-11-25 11:22:19', NULL),
(3, 2, 3, 0, '2024-11-25 11:24:02', NULL),
(4, 7, 4, 0, '2024-11-25 11:25:47', NULL),
(5, 2, 5, 0, '2024-11-25 11:28:41', NULL),
(6, 7, 6, 0, '2024-12-09 09:24:12', NULL),
(7, 2, 7, 0, '2024-12-09 09:30:42', NULL),
(8, 4, 8, 0, '2024-12-09 09:34:30', NULL),
(9, 3, 9, 0, '2024-12-09 09:37:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff_timeline`
--

CREATE TABLE `staff_timeline` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `timeline_date` date NOT NULL,
  `description` varchar(300) NOT NULL,
  `document` varchar(200) NOT NULL,
  `status` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `admission_no` varchar(100) DEFAULT NULL,
  `roll_no` varchar(100) DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `rte` varchar(20) DEFAULT NULL,
  `image` varchar(100) DEFAULT NULL,
  `mobileno` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `pincode` varchar(100) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `cast` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(100) DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `category_id` varchar(100) DEFAULT NULL,
  `school_house_id` int(11) DEFAULT NULL,
  `blood_group` varchar(200) NOT NULL,
  `hostel_room_id` int(11) DEFAULT NULL,
  `adhar_no` varchar(100) DEFAULT NULL,
  `samagra_id` varchar(100) DEFAULT NULL,
  `bank_account_no` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `ifsc_code` varchar(100) DEFAULT NULL,
  `guardian_is` varchar(100) NOT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `father_phone` varchar(100) DEFAULT NULL,
  `father_occupation` varchar(100) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `mother_phone` varchar(100) DEFAULT NULL,
  `mother_occupation` varchar(100) DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `guardian_relation` varchar(100) DEFAULT NULL,
  `guardian_phone` varchar(100) DEFAULT NULL,
  `guardian_occupation` varchar(150) NOT NULL,
  `guardian_address` text DEFAULT NULL,
  `guardian_email` varchar(100) DEFAULT NULL,
  `father_pic` varchar(200) NOT NULL,
  `mother_pic` varchar(200) NOT NULL,
  `guardian_pic` varchar(200) NOT NULL,
  `is_active` varchar(255) DEFAULT 'yes',
  `previous_school` text DEFAULT NULL,
  `height` varchar(100) NOT NULL,
  `weight` varchar(100) NOT NULL,
  `measurement_date` date DEFAULT NULL,
  `dis_reason` int(11) NOT NULL,
  `note` varchar(200) DEFAULT NULL,
  `dis_note` text NOT NULL,
  `app_key` text DEFAULT NULL,
  `parent_app_key` text DEFAULT NULL,
  `disable_at` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `parent_id`, `admission_no`, `roll_no`, `admission_date`, `firstname`, `middlename`, `lastname`, `rte`, `image`, `mobileno`, `email`, `state`, `city`, `pincode`, `religion`, `cast`, `dob`, `gender`, `current_address`, `permanent_address`, `category_id`, `school_house_id`, `blood_group`, `hostel_room_id`, `adhar_no`, `samagra_id`, `bank_account_no`, `bank_name`, `ifsc_code`, `guardian_is`, `father_name`, `father_phone`, `father_occupation`, `mother_name`, `mother_phone`, `mother_occupation`, `guardian_name`, `guardian_relation`, `guardian_phone`, `guardian_occupation`, `guardian_address`, `guardian_email`, `father_pic`, `mother_pic`, `guardian_pic`, `is_active`, `previous_school`, `height`, `weight`, `measurement_date`, `dis_reason`, `note`, `dis_note`, `app_key`, `parent_app_key`, `disable_at`, `created_at`, `updated_at`) VALUES
(1, 2, '1', '1', '2024-11-25', 'Jinal', NULL, 'Patel', 'No', 'uploads/student_images/1734768675-1319872020676678235b81a!images.jpeg', '987654567', 'jinal@gmail.com', NULL, NULL, NULL, 'hindu', '', '2024-10-31', 'Female', 'Atlantis Shyamdham Road', 'Atlantis Shyamdham Road', '2', 1, 'O+', 5, 'AUJSPF52', 'AUJSPF52', '8454784587', 'SBI', 'SBI1234', 'father', 'Ramesh bhai', '8140781993', 'Diamond', 'Rinkal Ben', '8140781993', 'House Wife', 'Jemin Bhai', 'Father', '9876545678', '', '', 'ramesh@gmail.com', 'uploads/student_images/1734768675-456180073676678235bb20!istockphoto-1447126543-612x612.jpg', 'uploads/student_images/1734768675-1499364413676678235bcb8!istockphoto-1446885495-612x612.jpg', 'uploads/student_images/1734768675-932978554676678235be40!istockphoto-1446885495-612x612.jpg', 'yes', 'Navchetan', '5,4\'', '70', '2024-11-25', 0, 'No comments', '', NULL, 'device_token', NULL, '2024-12-26 11:47:54', NULL),
(2, 4, '2011', '2', '2024-11-25', 'Vishwa', NULL, 'Patel', 'No', 'uploads/student_images/1732608250-1518358583674580fabc660!49.jpg', '', 'vish@gmail.com', NULL, NULL, NULL, '', '', '2024-11-21', 'Female', '', '', '1', 0, '', 5, '', '', '', '', '', 'father', '', '', '', '', '', '', 'Gmeis Bhai', 'Father', '345678765', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-25', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:40:20', NULL),
(3, 6, '4', '3', '2024-11-26', 'Lewis', NULL, 'Pfannerstill', 'No', 'uploads/student_images/1732607952-40960970867457fd02778e!88aa3c703827f00d38124e6a06a94133.jpg', '98765423456', 'lewis2005@gmail.com', NULL, NULL, NULL, '', '', '2024-11-13', 'Male', '', '', '2', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'Frieda Hansen', '', '746-106-9603', '', '', 'your.email+fakedata64725@gmail.com', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:24:20', NULL),
(4, 8, '3', '2', '2024-11-26', 'vishwa', NULL, 'patel', 'No', 'uploads/student_images/1732607674-210898241067457eba3e219!1.jpg', '9876543222', 'vishwa@gmail.com', NULL, NULL, NULL, '', '', '2024-11-26', 'Female', '', '', '1', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'lewis bhai', 'Father', '987654356', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:23:53', NULL),
(5, 10, '5', '11', '2024-11-26', 'Priyal', NULL, 'Patel', 'No', 'uploads/student_images/1732608057-25214607067458039cb84a!images.jpeg', '9875678876', 'priyal@gmail.com', NULL, NULL, NULL, '', '', '2024-12-03', 'Female', '', '', '1', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'rama ben', 'Mother', '09876545678', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:24:50', NULL),
(6, 12, '2001', '21', '2024-11-26', 'Hetvi', NULL, '', 'No', 'uploads/student_images/1732608210-1187150512674580d26679f!37.jpg', '', 'hetvi@gmail.com', NULL, NULL, NULL, '', '', '2024-11-09', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Rita', 'Mother', '9876456780', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:38:35', NULL),
(7, 14, '6', '12', '2024-11-26', 'Yug', NULL, 'Shah', 'No', 'uploads/student_images/1732608507-1678709555674581fb7176a!36.jpg', '', 'yug@gmail.com', NULL, NULL, NULL, '', '', '2024-12-10', 'Male', '', '', '2', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Sneha ben', 'Mother', '3456787654', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:36:01', NULL),
(8, 16, '2002', '202', '2024-11-26', 'Tanay', NULL, '', 'No', 'uploads/student_images/1732608422-761715986674581a66c616!boy1.jpg', '', 'tanay@gmail.com', NULL, NULL, NULL, '', '', '2024-11-16', 'Male', '', '', '', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'Hiteshbhai', 'Father', '9852074100', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:38:55', NULL),
(9, 18, '7', '13', '2024-11-26', 'khushi', NULL, 'shah', 'No', 'uploads/student_images/1732608461-1384850798674581cd5cea0!images (1).jpeg', '', 'khushi@gmail.com', NULL, NULL, NULL, '', '', '2024-11-06', 'Female', '', '', '1', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'snhha ben', 'Mother', '456789567', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:36:56', NULL),
(10, 20, '2003', '', '2024-11-26', 'Teena', NULL, '', 'No', 'uploads/student_images/1732608488-314799722674581e81048d!50.jpg', '', 'teena@gmail.com', NULL, NULL, NULL, '', '', '2024-11-29', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Meenaben', 'Mother', '9874105206', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:39:22', NULL),
(11, 22, '8', '23', '2024-11-26', 'Hardik', NULL, 'desai', 'No', 'uploads/student_images/1732609309-17357095246745851d7288d!16.jpg', '98765445678', 'hardik@gmail.com', NULL, NULL, NULL, '', '', '2024-11-06', 'Male', '', '', '3', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'kishor bhai', 'Father', '987654321', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:37:44', NULL),
(12, 24, '9', '22', '2024-11-26', 'kunj', NULL, 'desai', 'No', 'uploads/student_images/1732608794-9684559036745831aaf1f2!39.jpg', '87654321345', 'kunj@gmail.com', NULL, NULL, NULL, '', '', '2024-10-29', 'Male', '', '', '1', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'neeta ben', 'Mother', '3456784567', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:38:12', NULL),
(13, 26, '2012', '212', '2024-11-26', 'Aesvi', NULL, '', 'No', 'uploads/student_images/1732608798-4003276866745831e40d12!32.jpg', '', 'aesvi@gmail.com', NULL, NULL, NULL, '', '', '2024-11-04', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Nilamben', 'Mother', '9852741209', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:40:48', NULL),
(14, 28, '10', '21', '2024-11-26', 'pinki', NULL, 'patel', 'No', 'uploads/student_images/1732616510-8691284226745a13e54c6f!47.jpg', '987654321', 'pinki@gmail.com', NULL, NULL, NULL, '', '', '2024-11-12', 'Female', '', '', '2', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'ujal bhai', 'Father', '98765112', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:21:50', NULL),
(15, 30, '2013', '213', '2024-11-26', 'Shivam', NULL, '', 'No', 'uploads/student_images/1732608960-404233268674583c043a90!boy2.jpg', '', 'shivam@gmail.com', NULL, NULL, NULL, '', '', '2024-11-17', 'Male', '', '', '', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'Bharatbhai', 'Father', '9632741025', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:41:06', NULL),
(16, 32, '2021', '221', '2024-11-26', 'Rita', NULL, '', 'No', 'uploads/student_images/1732609107-25634452967458453d58d4!26.jpg', '', 'rita@gmail.com', NULL, NULL, NULL, '', '', '2024-11-10', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Tinaben', 'Mother', '9520854107', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:41:29', NULL),
(17, 34, '2022', '222', '2024-11-26', 'Ritesh', NULL, '', 'No', 'uploads/student_images/1732609248-323081327674584e0512c0!58364b97bba4c044562b44d6df4010ae.jpg', '', 'ritesh@gmail.com', NULL, NULL, NULL, '', '', '2024-11-01', 'Male', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Twinkalben', 'Mother', '9852078563', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:41:48', NULL),
(18, 36, '2023', '223', '2024-11-26', 'Heer', NULL, '', 'No', 'uploads/student_images/1732609334-13667878776745853626c3b!41.jpg', '', 'heer@gmail.com', NULL, NULL, NULL, '', '', '2024-11-17', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Mitalben', 'Mother', '9875678087', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:42:11', NULL),
(19, 38, '12', '31', '2024-11-26', 'Moons', NULL, 'sheth', 'No', 'uploads/student_images/1732610961-85490490767458b911e4a5!46.jpg', '98765456787', 'moons@gmail.com', NULL, NULL, NULL, '', '', '2024-11-26', 'Male', '', '', '1', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'ashokbhai', 'Father', '8765432345', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:20:55', NULL),
(20, 40, '2031', '231', '2024-11-26', 'Vishakha', NULL, '', 'No', 'uploads/student_images/1732609417-515753968674585896c154!44.jpg', '', 'vishakha@gmail.com', NULL, NULL, NULL, '', '', '2024-10-17', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'Tarangbhai', 'Father', '9657456852', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:42:41', NULL),
(21, 42, '13', '32', '2024-11-26', 'Mit', NULL, 'rakholiya', 'No', 'uploads/student_images/1732609486-1198878612674585ce99e93!25.jpg', '876543234', 'mit@gmail.com', NULL, NULL, NULL, '', '', '2024-11-09', 'Male', '', '', '3', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'ashokbhai', 'Father', '654322467', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:22:20', NULL),
(22, 44, '2032', '232', '2024-11-26', 'Ram', NULL, '', 'No', 'uploads/student_images/1732609496-2093098997674585d801d2d!9b9362e4b998a73745da74cdc3adb4a9.jpg', '', 'ram@gmail.com', NULL, NULL, NULL, '', '', '2024-11-11', 'Male', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Sitaben', 'Mother', '9085246210', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:43:21', NULL),
(23, 46, '2033', '233', '2024-11-26', 'Krina', NULL, '', 'No', 'uploads/student_images/1732609559-9269946266745861765bc5!30.jpg', '', 'krina@gmail.com', NULL, NULL, NULL, '', '', '2024-11-12', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Manishaben', 'Mother', '9878900987', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:43:40', NULL),
(24, 48, '14', '33', '2024-11-26', 'Jimmy', NULL, 'jenny', 'No', 'uploads/student_images/1732609561-1406754208674586192d54d!images (2).jpeg', '765432123', 'jimmy@gmail.com', NULL, NULL, NULL, '', '', '2024-11-22', 'Female', '', '', '4', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'rita ben', 'Mother', '765434655', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:23:26', NULL),
(25, 50, '17', '41', '2024-11-26', 'Dhairy', NULL, 'shah', 'No', 'uploads/student_images/1732609710-1419391131674586ae2e0de!34.jpg', '456745678', 'dhairy@gmail.com', NULL, NULL, NULL, '', '', '2024-11-16', 'Male', '', '', '3', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'moons', 'Father', '9876543234', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:49:13', NULL),
(26, 52, '19', '42', '2024-11-26', 'Kiara', NULL, 'advani', 'No', 'uploads/student_images/1732610972-47445431067458b9cdf30c!29.jpg', '34567894567', 'kiara@gmail.com', NULL, NULL, NULL, '', '', '2024-11-22', 'Female', '', '', '2', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'anshu bhai', 'Father', '345678567', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:49:38', NULL),
(27, 54, '2111', '211', '2024-11-26', 'Veena', NULL, '', 'No', 'uploads/student_images/1732609854-2776428356745873ea500e!32.jpg', '', 'veena@gmail.com', NULL, NULL, NULL, '', '', '2024-08-14', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'Ritenbhai', 'Father', '9627412630', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:44:06', NULL),
(28, 56, '21', '43', '2024-11-26', 'vivan', NULL, 'tank', 'No', 'uploads/student_images/1732610908-122575096167458b5ce9ebb!21.jpg', '9876542345677', 'vivan@gmail.com', NULL, NULL, NULL, '', '', '2024-11-13', 'Male', '', '', '3', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'trupesh', 'Father', '3456784567', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:50:03', NULL),
(29, 58, '2112', '212', '2024-11-26', 'Titansh', NULL, '', 'No', 'uploads/student_images/1732609931-21263224626745878b3a822!159d3e5351fc81667acf41618657faff.jpg', '', 'titansh@gmail.com', NULL, NULL, NULL, '', '', '2024-11-04', 'Male', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Bhavnaben', 'Mother', '9087656789', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:44:31', NULL),
(30, 60, '23', '51', '2024-11-26', 'Rajmit', NULL, 'tank', 'No', 'uploads/student_images/1732609957-1173898885674587a52d07c!43.jpg', '98765434567', 'rajmit@gmail.com', NULL, NULL, NULL, '', '', '2024-11-16', 'Male', '', '', '3', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'ghanshyambhai', 'Father', '987654345678', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:50:46', NULL),
(31, 62, '2113', '213', '2024-11-26', 'Tinku', NULL, '', 'No', 'uploads/student_images/1732610000-160302207674587d016c44!34.jpg', '', 'tinku@gmail.com', NULL, NULL, NULL, '', '', '2024-11-16', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Jinaben', 'Mother', '9674895202', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:44:51', NULL),
(32, 64, '24', '52', '2024-11-26', 'Krishna', NULL, 'tank', 'No', 'uploads/student_images/1732610878-119142532967458b3e75b9c!images.jpeg', '98765434556', 'krishna@gmail.com', NULL, NULL, NULL, '', '', '2024-11-15', 'Female', '', '', '2', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'yansembhai', 'Father', '98765456787', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:51:13', NULL),
(33, 66, '2221', '221', '2024-11-26', 'Hina', NULL, '', 'No', 'uploads/student_images/1732610071-93983023967458817b965f!33.jpg', '', 'hina@gmail.com', NULL, NULL, NULL, '', '', '2024-11-19', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'Tilenbhai', 'Father', '9870968077', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:45:14', NULL),
(34, 68, '2222', '222', '2024-11-26', 'Bheem', NULL, '', 'No', 'uploads/student_images/1732610149-123101397667458865670cd!618f42236250ae1166cb2e98652906f4.jpg', '', 'bheem@gmail.com', NULL, NULL, NULL, '', '', '2024-11-19', 'Male', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Kinaben', 'Mother', '9520256256', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:45:30', NULL),
(35, 70, '26', '53', '2024-11-26', 'Yatin', NULL, '', 'No', 'uploads/student_images/1732610157-7584385736745886d32af3!10.jpg', '456789678', 'yatin@gmail.com', NULL, NULL, NULL, '', '', '2024-11-23', 'Male', '', '', '3', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'gita ben', 'Mother', '9876543456', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:51:42', NULL),
(36, 72, '2223', '223', '2024-11-26', 'Pinal', NULL, '', 'No', 'uploads/student_images/1732610223-482350478674588aff08a6!30.jpg', '', 'pinal@gmail.com', NULL, NULL, NULL, '', '', '2024-11-16', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Pinkalben', 'Mother', '9876567090', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:45:51', NULL),
(37, 74, '28', '61', '2024-11-26', 'Yansi', NULL, 'thummar', 'No', 'uploads/student_images/1732610842-211543619467458b1a05453!images (1).jpeg', '976545678', 'yansi@gmail.com', NULL, NULL, NULL, '', '', '2024-11-29', 'Female', '', '', '3', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'rajmit bhai', 'Father', '98765345678', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:52:09', NULL),
(38, 76, '29', '62', '2024-11-26', 'Tanveer', NULL, 'rank', 'No', 'uploads/student_images/1732610327-155125496867458917664d8!37.jpg', '7654345678', 'tanveer@gmail.com', NULL, NULL, NULL, '', '', '2024-11-14', 'Male', '', '', '3', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'rita sanyal', 'Mother', '98765456', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:52:34', NULL),
(39, 78, '2231', '231', '2024-11-26', 'Tisha', NULL, '', 'No', 'uploads/student_images/1732610340-77113517767458924bdd81!42.jpg', '', 'tisha@gmail.com', NULL, NULL, NULL, '', '', '2024-11-17', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'Tusharbhai', 'Father', '9875122320', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:46:11', NULL),
(40, 80, '30', '63', '2024-11-26', 'Reeta', NULL, 'sanyal', 'No', 'uploads/student_images/1732610807-45857315667458af7d57cd!images (2).jpeg', '9876544567', 'reeta@gmail.com', NULL, NULL, NULL, '', '', '2024-11-22', 'Female', '', '', '4', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'jayesh bhai', 'Father', '34567899876', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:53:30', NULL),
(41, 82, '2232', '232', '2024-11-26', 'Hitesh', NULL, '', 'No', 'uploads/student_images/1732610398-2753747506745895e760a5!2acf9a571bfc454d37877e2c5a136bda.jpg', '', 'hitesh@gmail.com', NULL, NULL, NULL, '', '', '2024-11-05', 'Male', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Yaminiben', 'Mother', '9876789876', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:46:31', NULL),
(42, 84, '2233', '233', '2024-11-26', 'Ishita', NULL, '', 'No', 'uploads/student_images/1732610491-1323908177674589bbca82a!28.jpg', '', 'ishita@gmail.com', NULL, NULL, NULL, '', '', '2024-11-08', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'Vijaybhai', 'Father', '9878888907', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:47:00', NULL),
(43, 86, '61', '71', '2024-11-26', 'Yogita', NULL, 'desai', 'No', 'uploads/student_images/1732610768-161238828867458ad0c540a!12.jpg', '987654345678', 'yogita@gmail.com', NULL, NULL, NULL, '', '', '2024-11-09', 'Female', '', '', '4', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'rita', 'Mother', '876545678', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:54:06', NULL),
(44, 88, '2241', '241', '2024-11-26', 'Trisha', NULL, '', 'No', 'uploads/student_images/1732610558-916430856674589febab32!27.jpg', '', 'trisha@gmail.com', NULL, NULL, NULL, '', '', '2024-11-22', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Trushaben', 'Mother', '9658820018', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:47:36', NULL),
(45, 90, '62', '72', '2024-11-26', 'Raj', NULL, 'vora', 'No', 'uploads/student_images/1732610630-52294907167458a4647f85!8.jpg', '98743456788', 'raj@gmail.com', NULL, NULL, NULL, '', '', '2024-11-20', 'Male', '', '', '3', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'saniya', 'Mother', '5678567867', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:54:29', NULL),
(46, 92, '2242', '242', '2024-11-26', 'Sumit', NULL, '', 'No', 'uploads/student_images/1732610641-82634549567458a516ef7a!682d13fed1b7f3e52d6cd4a2e1d3dbdc.jpg', '', 'sumit@gmail.com', NULL, NULL, NULL, '', '', '2024-08-07', 'Male', '', '', '', 0, '', 0, '', '', '', '', '', 'father', '', '', '', '', '', '', 'Sanjaybhai', 'Father', '9971025875', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:47:55', NULL),
(47, 94, '63', '73', '2024-11-26', 'Soniya', NULL, 'vora', 'No', 'uploads/student_images/1732610715-108853160167458a9b1c17e!1.jpg', '98765456778', 'soniya@gmail.com', NULL, NULL, NULL, '', '', '2024-11-22', 'Female', '', '', '3', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'rita', 'Mother', '9876534567', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:54:50', NULL),
(48, 96, '2243', '243', '2024-11-26', 'Khushi', NULL, '', 'No', 'uploads/student_images/1732610769-164050389267458ad141a10!39.jpg', '', 'khushi123@gmail.com', NULL, NULL, NULL, '', '', '2024-11-20', 'Female', '', '', '', 0, '', 0, '', '', '', '', '', 'mother', '', '', '', '', '', '', 'Geetaben', 'Mother', '9520474150', '', '', '', '', '', '', 'yes', '', '', '', '2024-11-26', 0, '', '', NULL, NULL, NULL, '2024-11-26 10:48:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_applyleave`
--

CREATE TABLE `student_applyleave` (
  `id` int(11) NOT NULL,
  `student_session_id` int(11) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `apply_date` date NOT NULL,
  `status` int(1) NOT NULL,
  `docs` varchar(200) DEFAULT NULL,
  `reason` text NOT NULL,
  `approve_by` int(11) DEFAULT NULL,
  `approve_date` date DEFAULT NULL,
  `request_type` int(11) NOT NULL COMMENT '0 student,1 staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `student_applyleave`
--

INSERT INTO `student_applyleave` (`id`, `student_session_id`, `from_date`, `to_date`, `apply_date`, `status`, `docs`, `reason`, `approve_by`, `approve_date`, `request_type`, `created_at`) VALUES
(2, 1, '2024-12-24', '2024-12-24', '2024-12-24', 0, NULL, 'tfg', NULL, NULL, 0, '2024-12-24 09:41:18');

-- --------------------------------------------------------

--
-- Table structure for table `student_attendences`
--

CREATE TABLE `student_attendences` (
  `id` int(11) NOT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `biometric_attendence` int(11) NOT NULL DEFAULT 0,
  `qrcode_attendance` int(11) NOT NULL DEFAULT 0,
  `date` date DEFAULT NULL,
  `attendence_type_id` int(11) DEFAULT NULL,
  `remark` varchar(200) NOT NULL,
  `biometric_device_data` text DEFAULT NULL,
  `user_agent` varchar(250) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_behaviour`
--

CREATE TABLE `student_behaviour` (
  `id` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  `description` text NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `student_behaviour`
--

INSERT INTO `student_behaviour` (`id`, `point`, `description`, `title`, `created_at`) VALUES
(1, -10, 'It’s important to report cases of theft on campus so that the university or school can increase security where needed. They could also consider other options to combat incidents of theft, such as lockers.', 'Thefts', '2024-12-09 09:57:55'),
(2, -10, 'Respect others/property.', 'Respect others/property.', '2024-12-09 09:58:23'),
(3, 10, 'Smile & have a good attitude and good behaviour.', 'Student Good Behaviour', '2024-12-09 09:58:46'),
(4, -29, 'If students report this type of behaviour, institutions will be able to monitor the individuals involved. They can then try to resolve the situation.', 'Harassment and bullying', '2024-12-09 09:59:28'),
(5, -20, 'If the university, or school, is aware of these cases, they can monitor the spaces where threats and violence occur and improve security. This ensures that the individuals implicated can face consequences for their actions.', 'Threats and violence', '2024-12-09 10:00:18');

-- --------------------------------------------------------

--
-- Table structure for table `student_doc`
--

CREATE TABLE `student_doc` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `doc` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_edit_fields`
--

CREATE TABLE `student_edit_fields` (
  `id` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `student_edit_fields`
--

INSERT INTO `student_edit_fields` (`id`, `name`, `status`, `created_at`) VALUES
(1, 'firstname', 1, '2024-11-26 10:11:54'),
(2, 'admission_date', 1, '2024-11-26 10:11:56'),
(3, 'lastname', 1, '2024-11-26 10:12:00'),
(4, 'rte', 1, '2024-11-26 10:12:02'),
(5, 'student_photo', 1, '2024-11-26 10:12:04'),
(6, 'mobile_no', 1, '2024-11-26 10:12:06'),
(7, 'student_email', 1, '2024-11-26 10:12:08'),
(8, 'religion', 1, '2024-11-26 10:12:10'),
(9, 'cast', 1, '2024-11-26 10:12:12'),
(10, 'dob', 1, '2024-11-26 10:12:14'),
(11, 'is_blood_group', 1, '2024-11-26 10:12:16'),
(12, 'if_guardian_is', 1, '2024-11-26 10:12:18'),
(13, 'gender', 1, '2024-11-26 10:12:20'),
(14, 'current_address', 1, '2024-11-26 10:12:23'),
(15, 'permanent_address', 1, '2024-11-26 10:12:25'),
(16, 'category', 1, '2024-11-26 10:12:27'),
(17, 'bank_account_no', 1, '2024-11-26 10:12:29'),
(18, 'bank_name', 1, '2024-11-26 10:12:31'),
(19, 'ifsc_code', 1, '2024-11-26 10:12:33'),
(20, 'father_name', 1, '2024-11-26 10:12:36'),
(21, 'father_phone', 1, '2024-11-26 10:12:39'),
(22, 'father_occupation', 1, '2024-11-26 10:12:40'),
(23, 'mother_name', 1, '2024-11-26 10:12:44'),
(24, 'mother_phone', 1, '2024-11-26 10:12:46'),
(25, 'is_student_house', 1, '2024-11-26 10:12:48'),
(26, 'mother_occupation', 1, '2024-11-26 10:12:50'),
(27, 'guardian_name', 1, '2024-11-26 10:12:51'),
(28, 'guardian_relation', 1, '2024-11-26 10:12:53'),
(29, 'guardian_phone', 1, '2024-11-26 10:12:55'),
(30, 'guardian_occupation', 1, '2024-11-26 10:12:57'),
(31, 'guardian_address', 1, '2024-11-26 10:12:59'),
(32, 'guardian_email', 1, '2024-11-26 10:13:01'),
(33, 'national_identification_no', 1, '2024-11-26 10:13:02'),
(34, 'local_identification_no', 1, '2024-11-26 10:13:04'),
(35, 'father_pic', 1, '2024-11-26 10:13:06'),
(36, 'mother_pic', 1, '2024-11-26 10:13:09'),
(37, 'guardian_pic', 1, '2024-11-26 10:13:12'),
(38, 'student_height', 1, '2024-11-26 10:13:13'),
(39, 'student_weight', 1, '2024-11-26 10:13:15'),
(40, 'previous_school_details', 1, '2024-11-26 10:13:18'),
(41, 'Medical History', 1, '2024-11-26 10:13:20');

-- --------------------------------------------------------

--
-- Table structure for table `student_fees`
--

CREATE TABLE `student_fees` (
  `id` int(11) NOT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `feemaster_id` int(11) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `amount_discount` float(10,2) NOT NULL,
  `amount_fine` float(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_fees_deposite`
--

CREATE TABLE `student_fees_deposite` (
  `id` int(11) NOT NULL,
  `student_fees_master_id` int(11) DEFAULT NULL,
  `fee_groups_feetype_id` int(11) DEFAULT NULL,
  `student_transport_fee_id` int(11) DEFAULT NULL,
  `amount_detail` text DEFAULT NULL,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `student_fees_deposite`
--

INSERT INTO `student_fees_deposite` (`id`, `student_fees_master_id`, `fee_groups_feetype_id`, `student_transport_fee_id`, `amount_detail`, `is_active`, `created_at`) VALUES
(1, NULL, NULL, 1, '{\"1\":{\"amount\":50,\"date\":\"2024-12-10\",\"amount_discount\":0,\"amount_fine\":50,\"description\":\"ch_3QUP68GbC4Q2i5M70jLpN5oJ\",\"received_by\":\"\",\"payment_mode\":\"Stripe\",\"inv_no\":1},\"2\":{\"amount\":50,\"date\":\"2024-12-10\",\"amount_discount\":0,\"amount_fine\":50,\"description\":\"ch_3QUP6YGbC4Q2i5M70Qznqtgn\",\"received_by\":\"\",\"payment_mode\":\"Stripe\",\"inv_no\":2}}', 'no', '2024-12-10 08:47:43'),
(2, 16, 3, NULL, '{\"1\":{\"amount\":\"250.00\",\"date\":\"2024-12-10\",\"description\":\"\",\"amount_discount\":0,\"collected_by\":\"Super Admin(9000)\",\"amount_fine\":\"0\",\"payment_mode\":\"Cash\",\"received_by\":\"1\",\"inv_no\":1}}', 'no', '2024-12-10 09:59:42'),
(3, 17, 1, NULL, '{\"1\":{\"amount\":\"2000.00\",\"date\":\"2024-12-10\",\"description\":\"\",\"amount_discount\":0,\"collected_by\":\"Super Admin(9000)\",\"amount_fine\":\"0\",\"payment_mode\":\"Cash\",\"received_by\":\"1\",\"inv_no\":1}}', 'no', '2024-12-10 09:59:42'),
(4, 17, 2, NULL, '{\"1\":{\"amount\":\"300.00\",\"date\":\"2024-12-10\",\"description\":\"\",\"amount_discount\":0,\"collected_by\":\"Super Admin(9000)\",\"amount_fine\":\"0\",\"payment_mode\":\"Cash\",\"received_by\":\"1\",\"inv_no\":1}}', 'no', '2024-12-10 09:59:42'),
(5, 18, 4, NULL, '{\"1\":{\"amount\":\"3000.00\",\"date\":\"2024-12-10\",\"description\":\"\",\"amount_discount\":0,\"collected_by\":\"Super Admin(9000)\",\"amount_fine\":\"0\",\"payment_mode\":\"Cash\",\"received_by\":\"1\",\"inv_no\":1}}', 'no', '2024-12-10 09:59:42');

-- --------------------------------------------------------

--
-- Table structure for table `student_fees_discounts`
--

CREATE TABLE `student_fees_discounts` (
  `id` int(11) NOT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `fees_discount_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'assigned',
  `payment_id` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_fees_master`
--

CREATE TABLE `student_fees_master` (
  `id` int(11) NOT NULL,
  `is_system` int(1) NOT NULL DEFAULT 0,
  `student_session_id` int(11) DEFAULT NULL,
  `fee_session_group_id` int(11) DEFAULT NULL,
  `amount` float(10,2) DEFAULT 0.00,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `student_fees_master`
--

INSERT INTO `student_fees_master` (`id`, `is_system`, `student_session_id`, `fee_session_group_id`, `amount`, `is_active`, `created_at`) VALUES
(1, 0, 3, 3, 0.00, 'no', '2024-11-26 07:51:11'),
(2, 0, 3, 2, 0.00, 'no', '2024-11-26 07:51:11'),
(3, 0, 4, 1, 0.00, 'no', '2024-11-26 07:54:34'),
(4, 0, 5, 1, 0.00, 'no', '2024-11-26 08:00:57'),
(5, 0, 25, 3, 0.00, 'no', '2024-11-26 08:28:30'),
(6, 0, 26, 1, 0.00, 'no', '2024-11-26 08:30:42'),
(7, 0, 28, 1, 0.00, 'no', '2024-11-26 08:31:43'),
(8, 0, 30, 1, 0.00, 'no', '2024-11-26 08:32:37'),
(9, 0, 32, 2, 0.00, 'no', '2024-11-26 08:34:17'),
(10, 0, 35, 2, 0.00, 'no', '2024-11-26 08:35:57'),
(11, 0, 37, 2, 0.00, 'no', '2024-11-26 08:37:21'),
(12, 0, 38, 2, 0.00, 'no', '2024-11-26 08:38:47'),
(13, 0, 40, 2, 0.00, 'no', '2024-11-26 08:39:55'),
(14, 0, 43, 2, 0.00, 'no', '2024-11-26 08:42:34'),
(15, 0, 47, 2, 0.00, 'no', '2024-11-26 08:45:15'),
(16, 0, 1, 2, 0.00, 'no', '2024-12-10 09:52:42'),
(17, 0, 1, 1, 0.00, 'no', '2024-12-10 09:52:42'),
(18, 0, 1, 3, 0.00, 'no', '2024-12-10 09:52:42');

-- --------------------------------------------------------

--
-- Table structure for table `student_fees_processing`
--

CREATE TABLE `student_fees_processing` (
  `id` int(11) NOT NULL,
  `gateway_ins_id` int(11) NOT NULL,
  `fee_category` varchar(255) NOT NULL,
  `student_fees_master_id` int(11) DEFAULT NULL,
  `fee_groups_feetype_id` int(11) DEFAULT NULL,
  `student_transport_fee_id` int(11) DEFAULT NULL,
  `amount_detail` text DEFAULT NULL,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_incidents`
--

CREATE TABLE `student_incidents` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `assign_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `student_incidents`
--

INSERT INTO `student_incidents` (`id`, `session_id`, `student_id`, `incident_id`, `assign_by`, `created_at`) VALUES
(1, 19, 1, 1, 1, '2025-02-11 13:32:39'),
(2, 19, 1, 2, 1, '2025-02-11 13:32:39'),
(3, 19, 1, 3, 1, '2025-02-11 13:32:40'),
(4, 19, 1, 4, 1, '2025-02-11 13:32:40'),
(5, 19, 1, 5, 1, '2025-02-11 13:32:41');

-- --------------------------------------------------------

--
-- Table structure for table `student_incident_comments`
--

CREATE TABLE `student_incident_comments` (
  `id` int(11) NOT NULL,
  `student_incident_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_quiz_status`
--

CREATE TABLE `student_quiz_status` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `guest_id` int(1) DEFAULT NULL,
  `course_quiz_id` int(11) DEFAULT NULL,
  `total_question` int(11) DEFAULT NULL,
  `correct_answer` int(11) DEFAULT NULL,
  `wrong_answer` int(11) DEFAULT NULL,
  `not_answer` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL COMMENT '1-completed,0-incomplete	',
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `updated_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_session`
--

CREATE TABLE `student_session` (
  `id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `hostel_room_id` int(11) DEFAULT NULL,
  `vehroute_id` int(10) DEFAULT NULL,
  `route_pickup_point_id` int(11) DEFAULT NULL,
  `transport_fees` float(10,2) NOT NULL DEFAULT 0.00,
  `fees_discount` float(10,2) NOT NULL DEFAULT 0.00,
  `is_leave` int(1) NOT NULL DEFAULT 0,
  `is_active` varchar(255) DEFAULT 'no',
  `is_alumni` int(11) NOT NULL,
  `default_login` int(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `student_session`
--

INSERT INTO `student_session` (`id`, `session_id`, `student_id`, `class_id`, `section_id`, `hostel_room_id`, `vehroute_id`, `route_pickup_point_id`, `transport_fees`, `fees_discount`, `is_leave`, `is_active`, `is_alumni`, `default_login`, `created_at`, `updated_at`) VALUES
(1, 19, 1, 1, 1, NULL, 1, 3, 0.00, 0.00, 0, 'no', 0, 1, '2024-12-09 09:16:33', NULL),
(2, 19, 2, 2, 2, NULL, 5, 14, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-25 10:11:26', NULL),
(3, 19, 3, 1, 1, NULL, 1, 1, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 07:51:11', NULL),
(4, 19, 4, 1, 1, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 07:54:34', NULL),
(5, 19, 5, 1, 2, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:00:57', NULL),
(6, 19, 6, 2, 1, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:01:34', NULL),
(7, 19, 7, 1, 2, NULL, 3, 5, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:04:16', NULL),
(8, 19, 8, 2, 1, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:07:02', NULL),
(9, 19, 9, 1, 2, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:07:41', NULL),
(10, 19, 10, 2, 1, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:08:08', NULL),
(11, 19, 11, 1, 3, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:22:09', NULL),
(12, 19, 12, 1, 3, NULL, 1, 2, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:13:14', NULL),
(13, 19, 13, 2, 2, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:13:18', NULL),
(14, 19, 14, 1, 3, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:15:39', NULL),
(15, 19, 15, 2, 2, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:16:00', NULL),
(16, 19, 16, 2, 3, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:18:27', NULL),
(17, 19, 17, 2, 3, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:20:48', NULL),
(18, 19, 18, 2, 3, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:22:14', NULL),
(19, 19, 19, 1, 4, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:23:35', NULL),
(20, 19, 20, 2, 4, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:23:37', NULL),
(21, 19, 21, 1, 4, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:24:46', NULL),
(22, 19, 22, 2, 4, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:24:56', NULL),
(23, 19, 23, 2, 4, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:25:59', NULL),
(24, 19, 24, 1, 4, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:26:01', NULL),
(25, 19, 25, 4, 1, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:28:30', NULL),
(26, 19, 26, 4, 1, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:30:42', NULL),
(27, 19, 27, 3, 1, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:30:54', NULL),
(28, 19, 28, 4, 1, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:31:43', NULL),
(29, 19, 29, 3, 1, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:32:11', NULL),
(30, 19, 30, 4, 2, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:32:37', NULL),
(31, 19, 31, 3, 1, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:33:20', NULL),
(32, 19, 32, 4, 2, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:34:17', NULL),
(33, 19, 33, 3, 2, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:34:31', NULL),
(34, 19, 34, 3, 2, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:35:49', NULL),
(35, 19, 35, 4, 2, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:35:57', NULL),
(36, 19, 36, 3, 2, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:37:03', NULL),
(37, 19, 37, 4, 3, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:37:21', NULL),
(38, 19, 38, 4, 3, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:38:47', NULL),
(39, 19, 39, 3, 3, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:39:00', NULL),
(40, 19, 40, 4, 3, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:39:55', NULL),
(41, 19, 41, 3, 3, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:39:58', NULL),
(42, 19, 42, 3, 3, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:41:31', NULL),
(43, 19, 43, 4, 4, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:42:34', NULL),
(44, 19, 44, 3, 4, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:42:38', NULL),
(45, 19, 45, 4, 4, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:43:50', NULL),
(46, 19, 46, 3, 4, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:44:01', NULL),
(47, 19, 47, 4, 4, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:45:15', NULL),
(48, 19, 48, 3, 4, NULL, NULL, NULL, 0.00, 0.00, 0, 'no', 0, 0, '2024-11-26 08:46:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_subject_attendances`
--

CREATE TABLE `student_subject_attendances` (
  `id` int(11) NOT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `subject_timetable_id` int(11) DEFAULT NULL,
  `attendence_type_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_timeline`
--

CREATE TABLE `student_timeline` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `timeline_date` date NOT NULL,
  `description` text NOT NULL,
  `document` varchar(200) DEFAULT NULL,
  `status` varchar(200) NOT NULL,
  `created_student_id` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `student_timeline`
--

INSERT INTO `student_timeline` (`id`, `student_id`, `title`, `timeline_date`, `description`, `document`, `status`, `created_student_id`, `date`) VALUES
(1, 1, 'jinal timeline', '2024-12-10', 'about us', '', 'yes', 0, '2024-12-10');

-- --------------------------------------------------------

--
-- Table structure for table `student_transport_fees`
--

CREATE TABLE `student_transport_fees` (
  `id` int(11) NOT NULL,
  `transport_feemaster_id` int(10) NOT NULL,
  `student_session_id` int(11) NOT NULL,
  `route_pickup_point_id` int(11) NOT NULL,
  `generated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `student_transport_fees`
--

INSERT INTO `student_transport_fees` (`id`, `transport_feemaster_id`, `student_session_id`, `route_pickup_point_id`, `generated_by`, `created_at`) VALUES
(1, 1, 1, 3, NULL, '2024-11-25 10:09:48'),
(2, 2, 2, 14, NULL, '2024-11-25 10:11:26'),
(3, 1, 3, 1, NULL, '2024-11-26 07:51:11'),
(4, 1, 7, 5, NULL, '2024-11-26 08:04:16');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `code` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `code`, `type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Hindi', 'HI89', 'theory', 'no', '2024-11-25 09:57:25', NULL),
(2, 'English', 'EN45', 'theory', 'no', '2024-11-25 09:57:41', NULL),
(3, 'Computer', 'COM34', 'practical', 'no', '2024-11-25 09:57:50', NULL),
(4, 'Science', 'SI78', 'theory', 'no', '2024-11-25 09:58:04', NULL),
(5, 'Mathematics', 'MT34', 'theory', 'no', '2024-11-25 09:58:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subject_groups`
--

CREATE TABLE `subject_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `subject_groups`
--

INSERT INTO `subject_groups` (`id`, `name`, `description`, `session_id`, `created_at`) VALUES
(1, 'Class 1st Subject Group', '', 19, '2024-11-25 09:59:41'),
(2, 'Class 2st Subject Group', '', 19, '2024-11-25 10:00:12'),
(3, 'Class 3st Subject Group', '', 19, '2024-11-25 10:00:33'),
(4, 'Class 4st Subject Group', '', 19, '2024-11-25 10:00:53'),
(5, 'Class 5st Subject Group', '', 19, '2024-11-25 10:02:02');

-- --------------------------------------------------------

--
-- Table structure for table `subject_group_class_sections`
--

CREATE TABLE `subject_group_class_sections` (
  `id` int(11) NOT NULL,
  `subject_group_id` int(11) DEFAULT NULL,
  `class_section_id` int(11) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `subject_group_class_sections`
--

INSERT INTO `subject_group_class_sections` (`id`, `subject_group_id`, `class_section_id`, `session_id`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 19, NULL, 0, '2024-11-25 09:59:41', NULL),
(2, 1, 2, 19, NULL, 0, '2024-11-25 09:59:41', NULL),
(3, 1, 3, 19, NULL, 0, '2024-11-25 09:59:41', NULL),
(4, 1, 4, 19, NULL, 0, '2024-11-25 09:59:41', NULL),
(6, 2, 6, 19, NULL, 0, '2024-11-25 10:00:12', NULL),
(7, 2, 7, 19, NULL, 0, '2024-11-25 10:00:12', NULL),
(8, 2, 8, 19, NULL, 0, '2024-11-25 10:00:12', NULL),
(9, 2, 9, 19, NULL, 0, '2024-11-25 10:00:12', NULL),
(11, 3, 11, 19, NULL, 0, '2024-11-25 10:00:33', NULL),
(12, 3, 12, 19, NULL, 0, '2024-11-25 10:00:33', NULL),
(13, 3, 13, 19, NULL, 0, '2024-11-25 10:00:33', NULL),
(14, 3, 14, 19, NULL, 0, '2024-11-25 10:00:33', NULL),
(16, 4, 16, 19, NULL, 0, '2024-11-25 10:00:53', NULL),
(17, 4, 17, 19, NULL, 0, '2024-11-25 10:00:53', NULL),
(18, 4, 18, 19, NULL, 0, '2024-11-25 10:00:53', NULL),
(19, 4, 19, 19, NULL, 0, '2024-11-25 10:00:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subject_group_subjects`
--

CREATE TABLE `subject_group_subjects` (
  `id` int(11) NOT NULL,
  `subject_group_id` int(11) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `subject_group_subjects`
--

INSERT INTO `subject_group_subjects` (`id`, `subject_group_id`, `session_id`, `subject_id`, `created_at`) VALUES
(1, 1, 19, 1, '2024-11-25 09:59:41'),
(2, 1, 19, 2, '2024-11-25 09:59:41'),
(3, 1, 19, 3, '2024-11-25 09:59:41'),
(4, 2, 19, 1, '2024-11-25 10:00:12'),
(5, 2, 19, 2, '2024-11-25 10:00:12'),
(6, 2, 19, 3, '2024-11-25 10:00:12'),
(7, 2, 19, 5, '2024-11-25 10:00:12'),
(8, 3, 19, 1, '2024-11-25 10:00:33'),
(9, 3, 19, 3, '2024-11-25 10:00:33'),
(10, 3, 19, 4, '2024-11-25 10:00:33'),
(11, 4, 19, 1, '2024-11-25 10:00:53'),
(12, 4, 19, 2, '2024-11-25 10:00:53'),
(13, 4, 19, 3, '2024-11-25 10:00:53'),
(14, 4, 19, 4, '2024-11-25 10:00:53'),
(15, 5, 19, 1, '2024-11-25 10:02:02'),
(16, 5, 19, 3, '2024-11-25 10:02:02'),
(17, 5, 19, 4, '2024-11-25 10:02:02'),
(18, 5, 19, 5, '2024-11-25 10:02:02');

-- --------------------------------------------------------

--
-- Table structure for table `subject_syllabus`
--

CREATE TABLE `subject_syllabus` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_for` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_from` varchar(255) NOT NULL,
  `time_to` varchar(255) NOT NULL,
  `presentation` text NOT NULL,
  `attachment` text NOT NULL,
  `lacture_youtube_url` varchar(255) NOT NULL,
  `lacture_video` varchar(255) NOT NULL,
  `sub_topic` text NOT NULL,
  `teaching_method` text NOT NULL,
  `general_objectives` text NOT NULL,
  `previous_knowledge` text NOT NULL,
  `comprehensive_questions` text NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject_timetable`
--

CREATE TABLE `subject_timetable` (
  `id` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `subject_group_id` int(11) DEFAULT NULL,
  `subject_group_subject_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `day` varchar(20) DEFAULT NULL,
  `time_from` varchar(20) DEFAULT NULL,
  `time_to` varchar(20) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `room_no` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `subject_timetable`
--

INSERT INTO `subject_timetable` (`id`, `session_id`, `class_id`, `section_id`, `subject_group_id`, `subject_group_subject_id`, `staff_id`, `day`, `time_from`, `time_to`, `start_time`, `end_time`, `room_no`, `created_at`) VALUES
(1, 19, 1, 1, 1, 1, 5, 'Monday', '3:49 PM', '04:19 PM', '15:49:00', '16:19:00', '345', '2024-12-10 10:20:05'),
(2, 19, 1, 1, 1, 2, 3, 'Monday', '3:49 PM', '3:49 PM', '15:49:00', '15:49:00', '543', '2024-12-10 10:20:05'),
(3, 19, 1, 1, 1, 3, 7, 'Monday', '3:50 PM', '3:50 PM', '15:50:00', '15:50:00', '543', '2024-12-10 10:20:19'),
(4, 19, 1, 1, 1, 1, 2, 'Tuesday', '3:50 PM', '3:50 PM', '15:50:00', '15:50:00', '678', '2024-12-10 10:21:31'),
(5, 19, 1, 1, 1, 2, 2, 'Tuesday', '3:51 PM', '3:51 PM', '15:51:00', '15:51:00', '567', '2024-12-10 10:21:31'),
(6, 19, 1, 1, 1, 3, 7, 'Tuesday', '3:51 PM', '3:51 PM', '15:51:00', '15:51:00', '789', '2024-12-10 10:21:31'),
(7, 19, 1, 1, 1, 1, 2, 'Wednesday', '3:52 PM', '3:52 PM', '15:52:00', '15:52:00', '56', '2024-12-10 10:23:26'),
(8, 19, 1, 1, 1, 2, 5, 'Wednesday', '3:53 PM', '3:53 PM', '15:53:00', '15:53:00', '90', '2024-12-10 10:23:26'),
(9, 19, 1, 1, 1, 3, 7, 'Wednesday', '3:53 PM', '3:53 PM', '15:53:00', '15:53:00', '786', '2024-12-10 10:23:26'),
(10, 19, 1, 1, 1, 1, 3, 'Thursday', '3:53 PM', '3:53 PM', '15:53:00', '15:53:00', '54', '2024-12-10 10:24:13'),
(11, 19, 1, 1, 1, 2, 5, 'Thursday', '3:53 PM', '3:53 PM', '15:53:00', '15:53:00', '65', '2024-12-10 10:24:13'),
(12, 19, 1, 1, 1, 3, 7, 'Thursday', '3:54 PM', '3:54 PM', '15:54:00', '15:54:00', '78', '2024-12-10 10:24:13'),
(13, 19, 1, 1, 1, 1, 3, 'Friday', '3:55 PM', '3:55 PM', '15:55:00', '15:55:00', '87', '2024-12-10 10:25:26'),
(14, 19, 1, 1, 1, 2, 5, 'Friday', '3:55 PM', '3:55 PM', '15:55:00', '15:55:00', '09', '2024-12-10 10:25:26'),
(15, 19, 1, 1, 1, 1, 5, 'Saturday', '3:55 PM', '3:55 PM', '15:55:00', '15:55:00', '76', '2024-12-10 10:25:49'),
(16, 19, 1, 1, 1, 3, 3, 'Saturday', '3:55 PM', '3:55 PM', '15:55:00', '15:55:00', '87', '2024-12-10 10:25:49');

-- --------------------------------------------------------

--
-- Table structure for table `submit_assignment`
--

CREATE TABLE `submit_assignment` (
  `id` int(11) NOT NULL,
  `homework_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `docs` varchar(225) NOT NULL,
  `file_name` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `template_admitcards`
--

CREATE TABLE `template_admitcards` (
  `id` int(11) NOT NULL,
  `template` varchar(250) DEFAULT NULL,
  `heading` text DEFAULT NULL,
  `title` text DEFAULT NULL,
  `left_logo` varchar(200) DEFAULT NULL,
  `right_logo` varchar(200) DEFAULT NULL,
  `exam_name` varchar(200) DEFAULT NULL,
  `school_name` varchar(200) DEFAULT NULL,
  `exam_center` varchar(200) DEFAULT NULL,
  `sign` varchar(200) DEFAULT NULL,
  `background_img` varchar(200) DEFAULT NULL,
  `is_name` int(1) NOT NULL DEFAULT 1,
  `is_father_name` int(1) NOT NULL DEFAULT 1,
  `is_mother_name` int(1) NOT NULL DEFAULT 1,
  `is_dob` int(1) NOT NULL DEFAULT 1,
  `is_admission_no` int(1) NOT NULL DEFAULT 1,
  `is_roll_no` int(1) NOT NULL DEFAULT 1,
  `is_address` int(1) NOT NULL DEFAULT 1,
  `is_gender` int(1) NOT NULL DEFAULT 1,
  `is_photo` int(11) NOT NULL,
  `is_class` int(11) NOT NULL DEFAULT 0,
  `is_section` int(11) NOT NULL DEFAULT 0,
  `content_footer` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `template_admitcards`
--

INSERT INTO `template_admitcards` (`id`, `template`, `heading`, `title`, `left_logo`, `right_logo`, `exam_name`, `school_name`, `exam_center`, `sign`, `background_img`, `is_name`, `is_father_name`, `is_mother_name`, `is_dob`, `is_admission_no`, `is_roll_no`, `is_address`, `is_gender`, `is_photo`, `is_class`, `is_section`, `content_footer`, `created_at`, `updated_at`) VALUES
(1, 'Sample Admit Card', '', '', 'ab12c4b65f53ee621dcf84370a7c5be4.png', '0910482bf79df5fd103e8383d61b387a.png', 'Test', 'Mount Carmel School', 'test dmit card2', 'aa9c7087e68c5af1d2c04946de1d3bd3.png', '782a71f53ea6bca213012d49e9d46d98.jpg', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, '2020-02-28 14:26:15', NULL),
(2, 'AMPULA', 'AMPULA HIGH SCHOOL', 'www.ampulaschool.ac.tz', '', '', 'MOCK EXAMINATION', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '2024-11-25 11:01:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `template_marksheets`
--

CREATE TABLE `template_marksheets` (
  `id` int(11) NOT NULL,
  `header_image` varchar(200) DEFAULT NULL,
  `template` varchar(200) DEFAULT NULL,
  `heading` text DEFAULT NULL,
  `title` text DEFAULT NULL,
  `left_logo` varchar(200) DEFAULT NULL,
  `right_logo` varchar(200) DEFAULT NULL,
  `exam_name` varchar(200) DEFAULT NULL,
  `school_name` varchar(200) DEFAULT NULL,
  `exam_center` varchar(200) DEFAULT NULL,
  `left_sign` varchar(200) DEFAULT NULL,
  `middle_sign` varchar(200) DEFAULT NULL,
  `right_sign` varchar(200) DEFAULT NULL,
  `exam_session` int(1) DEFAULT 1,
  `is_name` int(1) DEFAULT 1,
  `is_father_name` int(1) DEFAULT 1,
  `is_mother_name` int(1) DEFAULT 1,
  `is_dob` int(1) DEFAULT 1,
  `is_admission_no` int(1) DEFAULT 1,
  `is_roll_no` int(1) DEFAULT 1,
  `is_photo` int(11) DEFAULT 1,
  `is_division` int(1) NOT NULL DEFAULT 1,
  `is_rank` int(1) NOT NULL DEFAULT 0,
  `is_customfield` int(1) NOT NULL,
  `background_img` varchar(200) DEFAULT NULL,
  `date` varchar(20) DEFAULT NULL,
  `is_class` int(11) NOT NULL DEFAULT 0,
  `is_teacher_remark` int(11) NOT NULL DEFAULT 1,
  `is_section` int(11) NOT NULL DEFAULT 0,
  `content` text DEFAULT NULL,
  `content_footer` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `template_marksheets`
--

INSERT INTO `template_marksheets` (`id`, `header_image`, `template`, `heading`, `title`, `left_logo`, `right_logo`, `exam_name`, `school_name`, `exam_center`, `left_sign`, `middle_sign`, `right_sign`, `exam_session`, `is_name`, `is_father_name`, `is_mother_name`, `is_dob`, `is_admission_no`, `is_roll_no`, `is_photo`, `is_division`, `is_rank`, `is_customfield`, `background_img`, `date`, `is_class`, `is_teacher_remark`, `is_section`, `content`, `content_footer`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Sample Marksheet', 'BOARD OF SECONDARY EDUCATION, MADHYA PRADESH, BHOPAL', 'BOARD OF SECONDARY EDUCATION, MADHYA PRADESH, BHOPAL', 'f314cec3f688771ccaeddbcee6e52f7c.png', 'e824b2df53266266be2dbfd2001168b8.png', 'HIGHER SECONDARY SCHOOL CERTIFICATE EXAMINATION', 'Mount Carmel School', 'GOVT GIRLS H S SCHOOL', '331e0690e50f8c6b7a219a0a2b9667f7.png', '351f513d79ee5c0f642c2d36514a1ff4.png', 'fb79d2c0d163357d1706b78550a05e2c.png', 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, '', NULL, 0, 1, 0, NULL, NULL, '2020-02-28 14:26:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `topic`
--

CREATE TABLE `topic` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `complete_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `topic`
--

INSERT INTO `topic` (`id`, `session_id`, `lesson_id`, `name`, `status`, `complete_date`, `created_at`) VALUES
(1, 19, 1, '	Speak modulation ', 1, '2024-12-10', '2024-12-10 10:30:01'),
(2, 19, 1, 'Basic Communication with child', 1, '2024-12-06', '2024-12-10 10:30:08'),
(3, 19, 2, 'Chapter-1 Turtle (Poem)', 1, '2024-11-25', '2024-12-10 10:30:17'),
(4, 19, 20, 'Spoken', 1, '2024-12-01', '2024-12-10 10:33:42'),
(5, 19, 21, 'theory', 1, '2024-12-04', '2024-12-10 10:34:10');

-- --------------------------------------------------------

--
-- Table structure for table `transport_feemaster`
--

CREATE TABLE `transport_feemaster` (
  `id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `month` varchar(50) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `fine_amount` float(10,2) DEFAULT 0.00,
  `fine_type` varchar(50) DEFAULT NULL,
  `fine_percentage` float(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `transport_feemaster`
--

INSERT INTO `transport_feemaster` (`id`, `session_id`, `month`, `due_date`, `fine_amount`, `fine_type`, `fine_percentage`, `created_at`) VALUES
(1, 19, 'April', '2024-04-10', 50.00, 'fix', NULL, '2024-11-25 08:54:32'),
(2, 19, 'May', '2024-05-10', 50.00, 'fix', NULL, '2024-11-25 08:54:32'),
(3, 19, 'June', '2024-06-10', 50.00, 'fix', NULL, '2024-11-25 08:54:32'),
(4, 19, 'July', '2024-07-10', NULL, 'percentage', 10.00, '2024-11-25 08:54:32'),
(5, 19, 'August', '2024-08-10', NULL, 'percentage', 10.00, '2024-11-25 08:54:32'),
(6, 19, 'September', '2024-09-10', NULL, 'percentage', 10.00, '2024-11-25 08:54:32'),
(7, 19, 'October', '2024-10-10', NULL, 'percentage', 10.00, '2024-11-25 08:54:32'),
(8, 19, 'November', '2024-11-10', 50.00, 'fix', NULL, '2024-11-25 08:54:32'),
(9, 19, 'December', '2024-12-10', 50.00, 'fix', NULL, '2024-11-25 08:54:32'),
(10, 19, 'January', '2025-01-10', 50.00, 'fix', NULL, '2024-11-25 08:54:32'),
(11, 19, 'February', '2025-02-10', 50.00, 'fix', NULL, '2024-11-25 08:54:32'),
(12, 19, 'March', '2025-03-10', 50.00, 'fix', NULL, '2024-11-25 08:54:32');

-- --------------------------------------------------------

--
-- Table structure for table `transport_route`
--

CREATE TABLE `transport_route` (
  `id` int(11) NOT NULL,
  `route_title` varchar(100) DEFAULT NULL,
  `no_of_vehicle` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `transport_route`
--

INSERT INTO `transport_route` (`id`, `route_title`, `no_of_vehicle`, `note`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Brooklyn Central', NULL, NULL, 'no', '2024-11-25 08:58:17', NULL),
(2, 'Brooklyn East', NULL, NULL, 'no', '2024-11-25 08:58:22', NULL),
(3, 'Brooklyn West', NULL, NULL, 'no', '2024-11-25 08:58:27', NULL),
(4, 'Brooklyn South', NULL, NULL, 'no', '2024-11-25 08:58:31', NULL),
(5, 'Brooklyn North', NULL, NULL, 'no', '2024-11-25 08:58:36', NULL),
(6, 'Railway station', NULL, NULL, 'no', '2024-11-25 08:58:40', NULL),
(7, 'High Court', NULL, NULL, 'no', '2024-11-25 08:58:46', NULL),
(8, 'Vijay Nagar', NULL, NULL, 'no', '2024-11-25 08:58:54', NULL),
(9, 'Civil Line', NULL, NULL, 'no', '2024-11-25 08:58:59', NULL),
(10, 'Dindayal Chowk', NULL, NULL, 'no', '2024-11-25 08:59:03', NULL),
(11, 'Ranitaal', NULL, NULL, 'no', '2024-11-25 08:59:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `upload_contents`
--

CREATE TABLE `upload_contents` (
  `id` int(11) NOT NULL,
  `content_type_id` int(10) NOT NULL,
  `image` varchar(300) DEFAULT NULL,
  `thumb_path` varchar(300) DEFAULT NULL,
  `dir_path` varchar(300) DEFAULT NULL,
  `real_name` text NOT NULL,
  `img_name` varchar(300) DEFAULT NULL,
  `thumb_name` varchar(300) DEFAULT NULL,
  `file_type` varchar(100) NOT NULL,
  `mime_type` text NOT NULL,
  `file_size` varchar(100) NOT NULL,
  `vid_url` text NOT NULL,
  `vid_title` varchar(250) NOT NULL,
  `upload_by` int(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `upload_contents`
--

INSERT INTO `upload_contents` (`id`, `content_type_id`, `image`, `thumb_path`, `dir_path`, `real_name`, `img_name`, `thumb_name`, `file_type`, `mime_type`, `file_size`, `vid_url`, `vid_title`, `upload_by`, `created_at`) VALUES
(1, 1, NULL, 'uploads/school_content/material/media/thumb/', 'uploads/school_content/material/media/', '1727870598.jpg', '1732534816-17690749567446220e627a!1727870598.jpg', '1732534816-17690749567446220e627a!thumb_1727870598.jpg', 'jpeg', 'image/jpeg', '42039', '', '', 1, '2024-11-26 00:10:16'),
(2, 2, NULL, 'uploads/school_content/material/media/thumb/', 'uploads/school_content/material/media/', 'fee-structure- (1).jpg', '1732534973-1292048850674462bd55664!fee-structure- (1).jpg', '1732534973-1292048850674462bd55664!thumb_fee-structure- (1).jpg', 'jpeg', 'image/jpeg', '59541', '', '', 1, '2024-11-26 00:12:53');

-- --------------------------------------------------------

--
-- Table structure for table `userlog`
--

CREATE TABLE `userlog` (
  `id` int(11) NOT NULL,
  `user` varchar(100) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `class_section_id` int(11) DEFAULT NULL,
  `ipaddress` varchar(100) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `login_datetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `userlog`
--

INSERT INTO `userlog` (`id`, `user`, `role`, `class_section_id`, `ipaddress`, `user_agent`, `login_datetime`) VALUES
(1, 'superadmin@gmail.com', 'Super Admin', NULL, '27.57.176.115', 'Chrome 131.0.0.0, Windows 10', '2024-11-23 18:46:38'),
(2, 'superadmin@gmail.com', 'Super Admin', NULL, '27.57.176.115', 'Chrome 131.0.0.0, Windows 10', '2024-11-25 13:50:26'),
(3, 'superadmin@gmail.com', 'Super Admin', NULL, '27.57.176.115', 'Chrome 131.0.0.0, Windows 10', '2024-11-25 21:33:15'),
(4, 'superadmin@gmail.com', 'Super Admin', NULL, '27.57.176.115', 'Chrome 131.0.0.0, Windows 10', '2024-11-25 21:38:45'),
(5, 'superadmin@gmail.com', 'Super Admin', NULL, '27.57.176.115', 'Chrome 131.0.0.0, Windows 10', '2024-11-25 21:39:34'),
(6, 'superadmin@gmail.com', 'Super Admin', NULL, '27.57.176.115', 'Chrome 131.0.0.0, Windows 10', '2024-11-26 00:05:25'),
(7, 'superadmin@gmail.com', 'Super Admin', NULL, '27.57.181.131', 'Chrome 131.0.0.0, Windows 10', '2024-11-26 19:32:05'),
(8, 'superadmin@gmail.com', 'Super Admin', NULL, '27.57.181.131', 'Chrome 131.0.0.0, Windows 10', '2024-11-26 19:39:51'),
(9, 'superadmin@gmail.com', 'Super Admin', NULL, '27.57.181.131', 'Chrome 131.0.0.0, Windows 10', '2024-11-26 19:40:38'),
(10, 'superadmin@gmail.com', 'Super Admin', NULL, '27.57.181.131', 'Chrome 131.0.0.0, Windows 10', '2024-11-26 22:31:05'),
(11, 'superadmin@gmail.com', 'Super Admin', NULL, '27.57.181.131', 'Chrome 131.0.0.0, Windows 10', '2024-11-28 19:01:44'),
(12, 'superadmin@gmail.com', 'Super Admin', NULL, '122.172.98.83', 'Chrome 131.0.0.0, Windows 10', '2024-12-02 23:04:52'),
(13, 'superadmin@gmail.com', 'Super Admin', NULL, '49.36.90.104', 'Chrome 131.0.0.0, Windows 10', '2024-12-09 18:34:59'),
(14, 'superadmin@gmail.com', 'Super Admin', NULL, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-09 21:44:42'),
(15, 'superadmin@gmail.com', 'Super Admin', NULL, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-09 21:45:16'),
(16, 'superadmin@gmail.com', 'Super Admin', NULL, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-09 21:46:02'),
(17, 'parent1', 'parent', NULL, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-09 21:46:30'),
(18, 'superadmin@gmail.com', 'Super Admin', NULL, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-09 21:47:32'),
(19, 'superadmin@gmail.com', 'Super Admin', NULL, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-10 20:41:23'),
(20, 'superadmin@gmail.com', 'Super Admin', NULL, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-10 20:47:27'),
(21, 'superadmin@gmail.com', 'Super Admin', NULL, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-10 20:48:17'),
(22, 'superadmin@gmail.com', 'Super Admin', NULL, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-10 21:02:55'),
(23, 'std1', 'student', 1, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-10 21:03:05'),
(24, 'parent1', 'parent', NULL, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-10 21:04:03'),
(25, 'std1', 'student', 1, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-10 21:07:16'),
(26, 'parent1', 'parent', NULL, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-10 21:07:27'),
(27, 'superadmin@gmail.com', 'Super Admin', NULL, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-10 21:14:13'),
(28, 'superadmin@gmail.com', 'Super Admin', NULL, '110.227.244.250', 'Chrome 131.0.0.0, Windows 10', '2024-12-10 21:21:56'),
(29, 'superadmin@gmail.com', 'Super Admin', NULL, '117.97.160.83', 'Chrome 131.0.0.0, Windows 10', '2024-12-12 22:29:43'),
(30, 'superadmin@gmail.com', 'Super Admin', NULL, '223.235.175.65', 'Chrome 131.0.0.0, Windows 10', '2024-12-14 22:25:15'),
(31, 'Superadmin@gmail.com', 'Super Admin', NULL, '49.36.90.42', 'Chrome 131.0.0.0, Windows 10', '2024-12-14 22:58:29'),
(32, 'Superadmin@gmail.com', 'Super Admin', NULL, '49.36.90.42', 'Chrome 131.0.0.0, Windows 10', '2024-12-15 00:16:47'),
(33, 'Superadmin@gmail.com', 'Super Admin', NULL, '49.36.90.42', 'Chrome 131.0.0.0, Windows 10', '2024-12-15 00:42:55'),
(34, 'superadmin@gmail.com', 'Super Admin', NULL, '223.235.175.65', 'Chrome 131.0.0.0, Windows 10', '2024-12-15 01:37:03'),
(35, 'superadmin@gmail.com', 'Super Admin', NULL, '182.65.198.203', 'Chrome 131.0.0.0, Windows 10', '2024-12-19 21:58:17'),
(36, 'superadmin@gmail.com', 'Super Admin', NULL, '182.65.198.203', 'Chrome 131.0.0.0, Windows 10', '2024-12-19 22:01:30'),
(37, 'superadmin@gmail.com', 'Super Admin', NULL, '182.65.198.203', 'Chrome 131.0.0.0, Windows 10', '2024-12-21 01:14:32'),
(38, 'superadmin@gmail.com', 'Super Admin', NULL, '117.98.158.130', 'Chrome 131.0.0.0, Windows 10', '2024-12-21 18:26:06'),
(39, 'std1', 'student', 1, '117.98.158.130', 'Chrome 131.0.0.0, Windows 10', '2024-12-21 20:35:32'),
(40, 'superadmin@gmail.com', 'Super Admin', NULL, '117.98.158.130', 'Chrome 131.0.0.0, Windows 10', '2024-12-21 20:36:33'),
(41, 'superadmin@gmail.com', 'Super Admin', NULL, '117.98.158.130', 'Chrome 131.0.0.0, Windows 10', '2024-12-24 00:18:15'),
(42, 'superadmin@gmail.com', 'Super Admin', NULL, '122.175.87.141', 'Chrome 131.0.0.0, Windows 10', '2024-12-24 22:22:09'),
(43, 'superadmin@gmail.com', 'Super Admin', NULL, '27.57.165.171', 'Chrome 131.0.0.0, Windows 10', '2024-12-27 00:18:28'),
(44, 'std1', 'student', 1, '2401:4900:1f3e:647:bdf0:4865:80f0:7dd1', 'Chrome 133.0.0.0, Windows 10', '2025-02-11 18:18:59'),
(45, 'superadmin@gmail.com', 'Super Admin', NULL, '2401:4900:1f3e:647:bdf0:4865:80f0:7dd1', 'Chrome 133.0.0.0, Windows 10', '2025-02-11 18:23:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `user_id` int(10) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `childs` text NOT NULL,
  `role` varchar(30) NOT NULL,
  `lang_id` int(11) NOT NULL,
  `currency_id` int(1) DEFAULT 0,
  `verification_code` varchar(200) NOT NULL,
  `is_active` varchar(255) DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `username`, `password`, `childs`, `role`, `lang_id`, `currency_id`, `verification_code`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'std1', 'jinal123', '', 'student', 4, 150, 'bGE1K0tlOVliTHl0bExub28rRnVCUFcrZlBBcXNPMTFhMGlwRkxKN2U1cz0=', 'yes', '2024-12-23 11:39:06', NULL),
(2, 0, 'parent1', 'bx9h7z', '1', 'parent', 0, 0, '', 'yes', '2024-11-25 10:09:48', NULL),
(3, 2, 'std2', '3ea233', '', 'student', 4, 0, '', 'yes', '2024-11-25 10:11:26', NULL),
(4, 0, 'parent2', 'mqu2dp', '2', 'parent', 0, 0, '', 'yes', '2024-11-25 10:11:26', NULL),
(5, 3, 'std3', 'f4zj07', '', 'student', 4, 0, '', 'yes', '2024-11-26 07:51:11', NULL),
(6, 0, 'parent3', 'nhmn3x', '3', 'parent', 0, 0, '', 'yes', '2024-11-26 07:51:11', NULL),
(7, 4, 'std4', 'v9y51k', '', 'student', 4, 0, '', 'yes', '2024-11-26 07:54:34', NULL),
(8, 0, 'parent4', '0ypmnj', '4', 'parent', 0, 0, '', 'yes', '2024-11-26 07:54:34', NULL),
(9, 5, 'std5', 'eyd6wr', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:00:57', NULL),
(10, 0, 'parent5', 'f9n3d6', '5', 'parent', 0, 0, '', 'yes', '2024-11-26 08:00:57', NULL),
(11, 6, 'std6', 'np51x4', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:01:34', NULL),
(12, 0, 'parent6', 'xzs875', '6', 'parent', 0, 0, '', 'yes', '2024-11-26 08:01:34', NULL),
(13, 7, 'std7', 'kdcfh5', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:04:16', NULL),
(14, 0, 'parent7', '7an8cw', '7', 'parent', 0, 0, '', 'yes', '2024-11-26 08:04:16', NULL),
(15, 8, 'std8', '5rsx76', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:07:02', NULL),
(16, 0, 'parent8', 'z9uqvi', '8', 'parent', 0, 0, '', 'yes', '2024-11-26 08:07:02', NULL),
(17, 9, 'std9', '1cs5qt', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:07:41', NULL),
(18, 0, 'parent9', 'xhdp63', '9', 'parent', 0, 0, '', 'yes', '2024-11-26 08:07:41', NULL),
(19, 10, 'std10', 'y38g6w', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:08:08', NULL),
(20, 0, 'parent10', '8gzfsb', '10', 'parent', 0, 0, '', 'yes', '2024-11-26 08:08:08', NULL),
(21, 11, 'std11', 'rbyc9h', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:11:40', NULL),
(22, 0, 'parent11', '7fvvnr', '11', 'parent', 0, 0, '', 'yes', '2024-11-26 08:11:40', NULL),
(23, 12, 'std12', 'crinmx', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:13:14', NULL),
(24, 0, 'parent12', 'ximzde', '12', 'parent', 0, 0, '', 'yes', '2024-11-26 08:13:14', NULL),
(25, 13, 'std13', 'a6zgfd', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:13:18', NULL),
(26, 0, 'parent13', 'y5awfe', '13', 'parent', 0, 0, '', 'yes', '2024-11-26 08:13:18', NULL),
(27, 14, 'std14', 'n31b1h', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:15:39', NULL),
(28, 0, 'parent14', 'sk2ef3', '14', 'parent', 0, 0, '', 'yes', '2024-11-26 08:15:39', NULL),
(29, 15, 'std15', '4klgeh', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:16:00', NULL),
(30, 0, 'parent15', 'c7cqyl', '15', 'parent', 0, 0, '', 'yes', '2024-11-26 08:16:00', NULL),
(31, 16, 'std16', 'pb3p9n', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:18:27', NULL),
(32, 0, 'parent16', 'tzrgbd', '16', 'parent', 0, 0, '', 'yes', '2024-11-26 08:18:27', NULL),
(33, 17, 'std17', '5fu8dc', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:20:48', NULL),
(34, 0, 'parent17', 'dyaxcw', '17', 'parent', 0, 0, '', 'yes', '2024-11-26 08:20:48', NULL),
(35, 18, 'std18', 'y1wvmd', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:22:14', NULL),
(36, 0, 'parent18', 'cikub3', '18', 'parent', 0, 0, '', 'yes', '2024-11-26 08:22:14', NULL),
(37, 19, 'std19', 'nu1xqi', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:23:35', NULL),
(38, 0, 'parent19', '9ov483', '19', 'parent', 0, 0, '', 'yes', '2024-11-26 08:23:35', NULL),
(39, 20, 'std20', '8br60c', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:23:37', NULL),
(40, 0, 'parent20', 'b2eby3', '20', 'parent', 0, 0, '', 'yes', '2024-11-26 08:23:37', NULL),
(41, 21, 'std21', 'vcs020', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:24:46', NULL),
(42, 0, 'parent21', 'wj490s', '21', 'parent', 0, 0, '', 'yes', '2024-11-26 08:24:46', NULL),
(43, 22, 'std22', 'd89na4', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:24:56', NULL),
(44, 0, 'parent22', 'assmdb', '22', 'parent', 0, 0, '', 'yes', '2024-11-26 08:24:56', NULL),
(45, 23, 'std23', 'c5pebf', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:25:59', NULL),
(46, 0, 'parent23', 'jxm7t3', '23', 'parent', 0, 0, '', 'yes', '2024-11-26 08:25:59', NULL),
(47, 24, 'std24', 'izbuti', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:26:01', NULL),
(48, 0, 'parent24', 'trrug8', '24', 'parent', 0, 0, '', 'yes', '2024-11-26 08:26:01', NULL),
(49, 25, 'std25', 'sxh7mh', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:28:30', NULL),
(50, 0, 'parent25', 'ek6pot', '25', 'parent', 0, 0, '', 'yes', '2024-11-26 08:28:30', NULL),
(51, 26, 'std26', '1p6fnr', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:30:42', NULL),
(52, 0, 'parent26', '8qpos2', '26', 'parent', 0, 0, '', 'yes', '2024-11-26 08:30:42', NULL),
(53, 27, 'std27', 'iwlrwh', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:30:54', NULL),
(54, 0, 'parent27', 'eju8pk', '27', 'parent', 0, 0, '', 'yes', '2024-11-26 08:30:54', NULL),
(55, 28, 'std28', 'qppmt1', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:31:43', NULL),
(56, 0, 'parent28', '3vota1', '28', 'parent', 0, 0, '', 'yes', '2024-11-26 08:31:43', NULL),
(57, 29, 'std29', '8zzokr', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:32:11', NULL),
(58, 0, 'parent29', '2j1yb0', '29', 'parent', 0, 0, '', 'yes', '2024-11-26 08:32:11', NULL),
(59, 30, 'std30', 'h0d03l', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:32:37', NULL),
(60, 0, 'parent30', 'd1xksn', '30', 'parent', 0, 0, '', 'yes', '2024-11-26 08:32:37', NULL),
(61, 31, 'std31', '1j7ue8', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:33:20', NULL),
(62, 0, 'parent31', 'atx6a8', '31', 'parent', 0, 0, '', 'yes', '2024-11-26 08:33:20', NULL),
(63, 32, 'std32', '9riv6w', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:34:17', NULL),
(64, 0, 'parent32', 'p3wz4u', '32', 'parent', 0, 0, '', 'yes', '2024-11-26 08:34:17', NULL),
(65, 33, 'std33', 'dxpil1', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:34:31', NULL),
(66, 0, 'parent33', 'fmu22u', '33', 'parent', 0, 0, '', 'yes', '2024-11-26 08:34:31', NULL),
(67, 34, 'std34', '09a7np', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:35:49', NULL),
(68, 0, 'parent34', 'sikyzv', '34', 'parent', 0, 0, '', 'yes', '2024-11-26 08:35:49', NULL),
(69, 35, 'std35', 'o1fvdc', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:35:57', NULL),
(70, 0, 'parent35', '5fmfy7', '35', 'parent', 0, 0, '', 'yes', '2024-11-26 08:35:57', NULL),
(71, 36, 'std36', 'gp832k', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:37:03', NULL),
(72, 0, 'parent36', '9btqq9', '36', 'parent', 0, 0, '', 'yes', '2024-11-26 08:37:03', NULL),
(73, 37, 'std37', 'm48l4h', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:37:21', NULL),
(74, 0, 'parent37', '3rwnbx', '37', 'parent', 0, 0, '', 'yes', '2024-11-26 08:37:21', NULL),
(75, 38, 'std38', 'pr20tq', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:38:47', NULL),
(76, 0, 'parent38', '8v3ae9', '38', 'parent', 0, 0, '', 'yes', '2024-11-26 08:38:47', NULL),
(77, 39, 'std39', 'yosywn', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:39:00', NULL),
(78, 0, 'parent39', '2lxjnm', '39', 'parent', 0, 0, '', 'yes', '2024-11-26 08:39:00', NULL),
(79, 40, 'std40', 'xufx2r', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:39:55', NULL),
(80, 0, 'parent40', 'tuvun0', '40', 'parent', 0, 0, '', 'yes', '2024-11-26 08:39:55', NULL),
(81, 41, 'std41', 'fk7no1', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:39:58', NULL),
(82, 0, 'parent41', 'pct9zk', '41', 'parent', 0, 0, '', 'yes', '2024-11-26 08:39:58', NULL),
(83, 42, 'std42', 'ndzhry', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:41:31', NULL),
(84, 0, 'parent42', 'ibltg7', '42', 'parent', 0, 0, '', 'yes', '2024-11-26 08:41:31', NULL),
(85, 43, 'std43', 'bbh50f', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:42:34', NULL),
(86, 0, 'parent43', 'sguzao', '43', 'parent', 0, 0, '', 'yes', '2024-11-26 08:42:34', NULL),
(87, 44, 'std44', '7uc22e', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:42:38', NULL),
(88, 0, 'parent44', '4o0tr4', '44', 'parent', 0, 0, '', 'yes', '2024-11-26 08:42:38', NULL),
(89, 45, 'std45', 'm696ok', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:43:50', NULL),
(90, 0, 'parent45', 'xe58rr', '45', 'parent', 0, 0, '', 'yes', '2024-11-26 08:43:50', NULL),
(91, 46, 'std46', '1dvlza', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:44:01', NULL),
(92, 0, 'parent46', 'ymvogk', '46', 'parent', 0, 0, '', 'yes', '2024-11-26 08:44:01', NULL),
(93, 47, 'std47', 'c3snjj', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:45:15', NULL),
(94, 0, 'parent47', 'rhnj8j', '47', 'parent', 0, 0, '', 'yes', '2024-11-26 08:45:15', NULL),
(95, 48, 'std48', 'n4t52p', '', 'student', 4, 0, '', 'yes', '2024-11-26 08:46:09', NULL),
(96, 0, 'parent48', 'nrk724', '48', 'parent', 0, 0, '', 'yes', '2024-11-26 08:46:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_authentication`
--

CREATE TABLE `users_authentication` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `expired_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users_authentication`
--

INSERT INTO `users_authentication` (`id`, `users_id`, `token`, `staff_id`, `expired_at`, `created_at`, `updated_at`) VALUES
(4, 1, 'OAMwMTANgMQ', NULL, '2025-12-21 13:48:44', NULL, NULL),
(5, 1, 'MQOAMQNwMQ', NULL, '2025-12-21 15:22:59', NULL, NULL),
(6, 1, 'OQNANgMwMTA', NULL, '2025-12-21 15:35:59', NULL, NULL),
(7, 1, 'MQNAMwMTANg', NULL, '2025-12-21 16:56:07', NULL, NULL),
(10, 2, 'OAMQNgNAMw', NULL, '2025-12-23 18:51:48', NULL, NULL),
(11, 1, 'OANgNwMgMw', NULL, '2025-12-24 16:03:50', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_google_authenticate_codes`
--

CREATE TABLE `user_google_authenticate_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_id` int(11) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `secret_code` varchar(100) DEFAULT NULL,
  `is_active` int(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `vehicle_no` varchar(20) DEFAULT NULL,
  `vehicle_model` varchar(100) NOT NULL DEFAULT 'None',
  `vehicle_photo` varchar(255) DEFAULT NULL,
  `manufacture_year` varchar(4) DEFAULT NULL,
  `registration_number` varchar(50) NOT NULL,
  `chasis_number` varchar(100) NOT NULL,
  `max_seating_capacity` varchar(255) NOT NULL,
  `driver_name` varchar(50) DEFAULT NULL,
  `driver_licence` varchar(50) NOT NULL DEFAULT 'None',
  `driver_contact` varchar(20) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_no`, `vehicle_model`, `vehicle_photo`, `manufacture_year`, `registration_number`, `chasis_number`, `max_seating_capacity`, `driver_name`, `driver_licence`, `driver_contact`, `note`, `created_at`) VALUES
(1, 'VH4584', 'Ford CAB', NULL, '2015', 'FFG-76575676787', '523422', '50', 'Jasper', '258714545', '8521479630', '', '2024-11-25 09:00:09'),
(2, 'VH5645', 'Volvo Bus', NULL, '2018', 'BGBFDF787987956', '45433', '50', 'Maximus', '545645666776', '885456456', '', '2024-11-25 09:00:57'),
(3, 'VH1001', 'Volvo Bus', NULL, '2017', 'FVFF-08797865', '45453', '50', 'Michel', 'R534534', '8667777869', '', '2024-11-25 09:03:36');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_routes`
--

CREATE TABLE `vehicle_routes` (
  `id` int(11) NOT NULL,
  `route_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `vehicle_routes`
--

INSERT INTO `vehicle_routes` (`id`, `route_id`, `vehicle_id`, `created_at`) VALUES
(1, 1, 3, '2024-11-25 09:43:31'),
(2, 2, 3, '2024-11-25 09:43:45'),
(3, 2, 1, '2024-11-25 09:43:45'),
(4, 3, 2, '2024-11-25 09:44:05'),
(5, 3, 1, '2024-11-25 09:44:05'),
(6, 5, 2, '2024-11-25 09:44:18'),
(7, 4, 2, '2024-11-25 09:44:25');

-- --------------------------------------------------------

--
-- Table structure for table `video_tutorial`
--

CREATE TABLE `video_tutorial` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `vid_title` text DEFAULT NULL,
  `description` text NOT NULL,
  `thumb_path` varchar(500) DEFAULT NULL,
  `dir_path` varchar(500) DEFAULT NULL,
  `img_name` varchar(300) NOT NULL,
  `thumb_name` varchar(300) NOT NULL,
  `video_link` varchar(100) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `video_tutorial`
--

INSERT INTO `video_tutorial` (`id`, `title`, `vid_title`, `description`, `thumb_path`, `dir_path`, `img_name`, `thumb_name`, `video_link`, `created_by`, `created_at`) VALUES
(1, 'Mathematics Study material', 'Fun with shapes class 3 maths new book maths mela chapter 5 solution with explanation part 1', 'Mathematics Study material\r\n', 'uploads/video_tutorial/youtube_video/thumb/', 'uploads/video_tutorial/youtube_video/', '1732534416-9294702476744609063795!1732534416.jpg', '1732534416-9294702476744609063795!thumb_1732534416.jpg', 'https://youtu.be/HS0Vhv7xzyo?si=2maIlN-2lQi4Wjxp', 1, '2024-11-26 00:03:36'),
(2, 'Life Of A Tree', 'Life Of A Tree I Class 4 I Learn With BYJU\'S', 'Life Of A Tree', 'uploads/video_tutorial/youtube_video/thumb/', 'uploads/video_tutorial/youtube_video/', '1732534441-2117123471674460a933d06!1732534441.jpg', '1732534441-2117123471674460a933d06!thumb_1732534441.jpg', 'https://www.youtube.com/watch?v=TaClGzxamgc', 1, '2024-11-26 00:04:01'),
(3, 'Life Of A Tree', 'Life Of A Tree I Class 4 I Learn With BYJU\'S', 'Life Of A Tree', 'uploads/video_tutorial/youtube_video/thumb/', 'uploads/video_tutorial/youtube_video/', '1732534442-569410490674460aa35c59!1732534442.jpg', '1732534442-569410490674460aa35c59!thumb_1732534442.jpg', 'https://www.youtube.com/watch?v=TaClGzxamgc', 1, '2024-11-26 00:04:02'),
(4, 'Planets of Our Solar System', 'Planets of Our Solar System | Science Grade 3 | Periwinkle', 'Planets of Our Solar System', 'uploads/video_tutorial/youtube_video/thumb/', 'uploads/video_tutorial/youtube_video/', '1732534475-586944995674460cba23da!1732534475.jpg', '1732534475-586944995674460cba23da!thumb_1732534475.jpg', 'https://www.youtube.com/watch?v=jpMyjPJ9HFg', 1, '2024-11-26 00:04:35');

-- --------------------------------------------------------

--
-- Table structure for table `video_tutorial_class_sections`
--

CREATE TABLE `video_tutorial_class_sections` (
  `id` int(11) NOT NULL,
  `video_tutorial_id` int(11) NOT NULL,
  `class_section_id` int(11) NOT NULL,
  `created_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `video_tutorial_class_sections`
--

INSERT INTO `video_tutorial_class_sections` (`id`, `video_tutorial_id`, `class_section_id`, `created_date`) VALUES
(1, 1, 1, '2024-11-25'),
(2, 2, 12, '2024-11-25'),
(3, 3, 12, '2024-11-25');

-- --------------------------------------------------------

--
-- Table structure for table `visitors_book`
--

CREATE TABLE `visitors_book` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `student_session_id` int(11) DEFAULT NULL,
  `source` varchar(100) DEFAULT NULL,
  `purpose` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(12) NOT NULL,
  `id_proof` varchar(50) NOT NULL,
  `no_of_people` int(11) NOT NULL,
  `date` date NOT NULL,
  `in_time` varchar(20) NOT NULL,
  `out_time` varchar(20) NOT NULL,
  `note` text NOT NULL,
  `image` varchar(100) DEFAULT NULL,
  `meeting_with` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `visitors_book`
--

INSERT INTO `visitors_book` (`id`, `staff_id`, `student_session_id`, `source`, `purpose`, `name`, `email`, `contact`, `id_proof`, `no_of_people`, `date`, `in_time`, `out_time`, `note`, `image`, `meeting_with`, `created_at`) VALUES
(1, 1, NULL, NULL, 'Marketing', 'Royston Dias	', NULL, '9876543', '65', 4, '2024-11-25', '03:51 PM', '03:51 PM', '', NULL, 'staff', '2024-11-25 10:22:41'),
(2, 1, NULL, NULL, 'Parent Teacher Meeting	', 'Royston Dias	', NULL, '987654', '65', 5, '2024-11-25', '03:52 PM', '03:52 PM', '', NULL, 'staff', '2024-11-25 10:23:00'),
(3, NULL, 1, NULL, 'Principal Meeting	', 'Alex Diesendorf	', NULL, '9876543', '44', 4, '2024-11-25', '03:53 PM', '03:53 PM', '', NULL, 'student', '2024-11-25 10:23:34'),
(4, NULL, NULL, 'Online', 'courses', 'yug', 'yugs@gmail.com', '', '', 0, '2024-12-09', '', '', 'about your courses (Sent from online front site)', NULL, '', '2024-12-09 12:16:13');

-- --------------------------------------------------------

--
-- Table structure for table `visitors_purpose`
--

CREATE TABLE `visitors_purpose` (
  `id` int(11) NOT NULL,
  `visitors_purpose` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `visitors_purpose`
--

INSERT INTO `visitors_purpose` (`id`, `visitors_purpose`, `description`, `created_at`) VALUES
(1, 'Marketing', '', '2024-11-25 10:18:25'),
(2, 'Parent Teacher Meeting	', '', '2024-11-25 10:18:33'),
(3, 'Student Meeting	', '', '2024-11-25 10:18:39'),
(4, 'Principal Meeting	', '', '2024-11-25 10:18:44'),
(5, 'School Events	', '', '2024-11-25 10:19:14');

-- --------------------------------------------------------

--
-- Table structure for table `zoom_settings`
--

CREATE TABLE `zoom_settings` (
  `id` int(11) NOT NULL,
  `zoom_api_key` varchar(200) DEFAULT NULL,
  `zoom_api_secret` varchar(200) DEFAULT NULL,
  `use_teacher_api` int(1) DEFAULT 1,
  `use_zoom_app` int(1) DEFAULT 1,
  `use_zoom_app_user` int(1) DEFAULT 1,
  `parent_live_class` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alumni_events`
--
ALTER TABLE `alumni_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `alumni_students`
--
ALTER TABLE `alumni_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `attendence_type`
--
ALTER TABLE `attendence_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `aws_s3_settings`
--
ALTER TABLE `aws_s3_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `behaviour_settings`
--
ALTER TABLE `behaviour_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_issues`
--
ALTER TABLE `book_issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `captcha`
--
ALTER TABLE `captcha`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbse_exams`
--
ALTER TABLE `cbse_exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_term_id` (`cbse_term_id`),
  ADD KEY `cbse_exam_grade_id` (`cbse_exam_grade_id`),
  ADD KEY `cbse_exam_assessment_id` (`cbse_exam_assessment_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_exam_code` (`exam_code`);

--
-- Indexes for table `cbse_exam_assessments`
--
ALTER TABLE `cbse_exam_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `cbse_exam_assessment_types`
--
ALTER TABLE `cbse_exam_assessment_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_assessment_id` (`cbse_exam_assessment_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_code` (`code`);

--
-- Indexes for table `cbse_exam_class_sections`
--
ALTER TABLE `cbse_exam_class_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_section_id` (`class_section_id`),
  ADD KEY `cbse_exam_id` (`cbse_exam_id`);

--
-- Indexes for table `cbse_exam_grades`
--
ALTER TABLE `cbse_exam_grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `cbse_exam_grades_range`
--
ALTER TABLE `cbse_exam_grades_range`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_grade_id` (`cbse_exam_grade_id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `cbse_exam_observations`
--
ALTER TABLE `cbse_exam_observations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `cbse_exam_students`
--
ALTER TABLE `cbse_exam_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_id` (`cbse_exam_id`),
  ADD KEY `student_session_id` (`student_session_id`);

--
-- Indexes for table `cbse_exam_student_subject_rank`
--
ALTER TABLE `cbse_exam_student_subject_rank`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_template_id` (`cbse_template_id`),
  ADD KEY `student_session_id` (`student_session_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `idx_rank` (`rank`);

--
-- Indexes for table `cbse_exam_timetable`
--
ALTER TABLE `cbse_exam_timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_id` (`cbse_exam_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `cbse_exam_timetable_assessment_types`
--
ALTER TABLE `cbse_exam_timetable_assessment_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_timetable_id` (`cbse_exam_timetable_id`),
  ADD KEY `cbse_exam_assessment_type_id` (`cbse_exam_assessment_type_id`);

--
-- Indexes for table `cbse_marksheet_type`
--
ALTER TABLE `cbse_marksheet_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_short_code` (`short_code`);

--
-- Indexes for table `cbse_observation_class_section`
--
ALTER TABLE `cbse_observation_class_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbse_observation_parameters`
--
ALTER TABLE `cbse_observation_parameters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- Indexes for table `cbse_observation_subparameter`
--
ALTER TABLE `cbse_observation_subparameter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_observation_parameter_id_ibfk_1` (`cbse_observation_parameter_id`),
  ADD KEY `cbse_exam_observation_id_ibfk_1` (`cbse_exam_observation_id`);

--
-- Indexes for table `cbse_observation_terms`
--
ALTER TABLE `cbse_observation_terms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_term_id` (`cbse_term_id`),
  ADD KEY `cbse_ovservation_terms_ibfk_3` (`session_id`),
  ADD KEY `cbse_exam_observations_ibfk_1` (`cbse_exam_observation_id`);

--
-- Indexes for table `cbse_observation_term_student_subparameter`
--
ALTER TABLE `cbse_observation_term_student_subparameter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_observation_term_student_subparameter_ibfk_1` (`cbse_ovservation_term_id`),
  ADD KEY `cbse_observation_subparameter_id` (`cbse_observation_subparameter_id`),
  ADD KEY `student_session_id` (`student_session_id`);

--
-- Indexes for table `cbse_student_exam_ranks`
--
ALTER TABLE `cbse_student_exam_ranks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_id` (`cbse_exam_id`),
  ADD KEY `student_session_id` (`student_session_id`),
  ADD KEY `idx_rank` (`rank`),
  ADD KEY `idx_rank_percentage` (`rank_percentage`);

--
-- Indexes for table `cbse_student_subject_marks`
--
ALTER TABLE `cbse_student_subject_marks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_exam_timetable_id` (`cbse_exam_timetable_id`),
  ADD KEY `cbse_exam_student_id` (`cbse_exam_student_id`),
  ADD KEY `cbse_exam_assessment_type_id` (`cbse_exam_assessment_type_id`),
  ADD KEY `cbse_exam_timetable_assessment_type_ibfk_4` (`cbse_exam_timetable_assessment_type_id`);

--
-- Indexes for table `cbse_student_subject_result`
--
ALTER TABLE `cbse_student_subject_result`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cbse_student_template_rank`
--
ALTER TABLE `cbse_student_template_rank`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_session_id` (`student_session_id`),
  ADD KEY `cbse_template_id` (`cbse_template_id`),
  ADD KEY `idx_rank` (`rank`),
  ADD KEY `idx_rank_percentage` (`rank_percentage`);

--
-- Indexes for table `cbse_template`
--
ALTER TABLE `cbse_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_template_ibfk_3` (`session_id`),
  ADD KEY `cbse_template_ibfk_1` (`gradeexam_id`),
  ADD KEY `cbse_template_ibfk_2` (`remarkexam_id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_marksheet_type` (`marksheet_type`),
  ADD KEY `idx_exam_name` (`exam_name`),
  ADD KEY `idx_school_name` (`school_name`);

--
-- Indexes for table `cbse_template_class_sections`
--
ALTER TABLE `cbse_template_class_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_template_id` (`cbse_template_id`),
  ADD KEY `class_section_id` (`class_section_id`);

--
-- Indexes for table `cbse_template_terms`
--
ALTER TABLE `cbse_template_terms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_template_id` (`cbse_template_id`),
  ADD KEY `cbse_term_id` (`cbse_term_id`),
  ADD KEY `idx_weightage` (`weightage`);

--
-- Indexes for table `cbse_template_term_exams`
--
ALTER TABLE `cbse_template_term_exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cbse_template_term_id` (`cbse_template_term_id`),
  ADD KEY `cbse_template_term_exams_ibfk_3` (`cbse_exam_id`),
  ADD KEY `cbse_template_term_exams_ibfk_4` (`cbse_template_id`),
  ADD KEY `idx_weightage` (`weightage`);

--
-- Indexes for table `cbse_terms`
--
ALTER TABLE `cbse_terms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_term_code` (`term_code`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_connections`
--
ALTER TABLE `chat_connections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_user_one` (`chat_user_one`),
  ADD KEY `chat_user_two` (`chat_user_two`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_user_id` (`chat_user_id`),
  ADD KEY `chat_connection_id` (`chat_connection_id`);

--
-- Indexes for table `chat_users`
--
ALTER TABLE `chat_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `create_staff_id` (`create_staff_id`),
  ADD KEY `create_student_id` (`create_student_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_sections`
--
ALTER TABLE `class_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `class_section_times`
--
ALTER TABLE `class_section_times`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_section_id` (`class_section_id`);

--
-- Indexes for table `class_teacher`
--
ALTER TABLE `class_teacher`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `complaint`
--
ALTER TABLE `complaint`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complaint_type`
--
ALTER TABLE `complaint_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conferences`
--
ALTER TABLE `conferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conferences_ibfk_1` (`staff_id`),
  ADD KEY `conferences_ibfk_2` (`created_id`),
  ADD KEY `idx_class_id` (`class_id`);

--
-- Indexes for table `conferences_history`
--
ALTER TABLE `conferences_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conferences_history_ibfk_1` (`conference_id`),
  ADD KEY `conferences_history_ibfk_2` (`staff_id`),
  ADD KEY `conferences_history_ibfk_3` (`student_id`);

--
-- Indexes for table `conference_sections`
--
ALTER TABLE `conference_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conference_sections_ibfk_1` (`conference_id`),
  ADD KEY `conference_sections_ibfk_2` (`cls_section_id`);

--
-- Indexes for table `conference_staff`
--
ALTER TABLE `conference_staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conference_staff_ibfk_1` (`conference_id`),
  ADD KEY `conference_staff_ibfk_2` (`staff_id`);

--
-- Indexes for table `contents`
--
ALTER TABLE `contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `cls_sec_id` (`cls_sec_id`);

--
-- Indexes for table `content_for`
--
ALTER TABLE `content_for`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `content_types`
--
ALTER TABLE `content_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_category`
--
ALTER TABLE `course_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_lesson_quiz_order`
--
ALTER TABLE `course_lesson_quiz_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`course_section_id`);

--
-- Indexes for table `course_progress`
--
ALTER TABLE `course_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_section_id` (`course_section_id`);

--
-- Indexes for table `course_quiz_answer`
--
ALTER TABLE `course_quiz_answer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `quiz_id` (`course_quiz_id`),
  ADD KEY `question_id` (`course_quiz_question_id`);

--
-- Indexes for table `course_quiz_question`
--
ALTER TABLE `course_quiz_question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_quiz_id` (`course_quiz_id`);

--
-- Indexes for table `course_rating`
--
ALTER TABLE `course_rating`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_course_id` (`course_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `custom_fields`
--
ALTER TABLE `custom_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_belong_to` (`belong_to`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_visible_on_table` (`visible_on_table`),
  ADD KEY `idx_weight` (`weight`);
ALTER TABLE `custom_fields` ADD FULLTEXT KEY `idx_field_values` (`field_values`);

--
-- Indexes for table `custom_field_values`
--
ALTER TABLE `custom_field_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_field_id` (`custom_field_id`),
  ADD KEY `idx_belong_table_id` (`belong_table_id`),
  ADD KEY `idx_field_value` (`field_value`);

--
-- Indexes for table `daily_assignment`
--
ALTER TABLE `daily_assignment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_session_id` (`student_session_id`),
  ADD KEY `evaluated_by` (`evaluated_by`),
  ADD KEY `subject_group_subject_id` (`subject_group_subject_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `disable_reason`
--
ALTER TABLE `disable_reason`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dispatch_receive`
--
ALTER TABLE `dispatch_receive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_attachments`
--
ALTER TABLE `email_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `email_config`
--
ALTER TABLE `email_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_template`
--
ALTER TABLE `email_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_template_attachment`
--
ALTER TABLE `email_template_attachment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enquiry`
--
ALTER TABLE `enquiry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `assigned` (`assigned`),
  ADD KEY `enquiry_ibfk_4` (`class_id`);

--
-- Indexes for table `enquiry_type`
--
ALTER TABLE `enquiry_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sesion_id` (`sesion_id`);

--
-- Indexes for table `exam_groups`
--
ALTER TABLE `exam_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_group_class_batch_exams`
--
ALTER TABLE `exam_group_class_batch_exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_group_id` (`exam_group_id`),
  ADD KEY `exam_group_class_batch_exams_ibfk_2` (`session_id`);

--
-- Indexes for table `exam_group_class_batch_exam_students`
--
ALTER TABLE `exam_group_class_batch_exam_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_group_class_batch_exam_id` (`exam_group_class_batch_exam_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `student_session_id` (`student_session_id`);

--
-- Indexes for table `exam_group_class_batch_exam_subjects`
--
ALTER TABLE `exam_group_class_batch_exam_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_group_class_batch_exams_id` (`exam_group_class_batch_exams_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `exam_group_exam_connections`
--
ALTER TABLE `exam_group_exam_connections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_group_id` (`exam_group_id`),
  ADD KEY `exam_group_class_batch_exams_id` (`exam_group_class_batch_exams_id`);

--
-- Indexes for table `exam_group_exam_results`
--
ALTER TABLE `exam_group_exam_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_group_class_batch_exam_subject_id` (`exam_group_class_batch_exam_subject_id`),
  ADD KEY `exam_group_student_id` (`exam_group_student_id`),
  ADD KEY `exam_group_class_batch_exam_student_id` (`exam_group_class_batch_exam_student_id`);

--
-- Indexes for table `exam_group_students`
--
ALTER TABLE `exam_group_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_group_id` (`exam_group_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `student_session_id` (`student_session_id`);

--
-- Indexes for table `exam_schedules`
--
ALTER TABLE `exam_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_subject_id` (`teacher_subject_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exp_head_id` (`exp_head_id`);

--
-- Indexes for table `expense_head`
--
ALTER TABLE `expense_head`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feemasters`
--
ALTER TABLE `feemasters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `feetype_id` (`feetype_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `fees_discounts`
--
ALTER TABLE `fees_discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `fees_reminder`
--
ALTER TABLE `fees_reminder`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feetype`
--
ALTER TABLE `feetype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fee_groups`
--
ALTER TABLE `fee_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fee_groups_feetype`
--
ALTER TABLE `fee_groups_feetype`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_session_group_id` (`fee_session_group_id`),
  ADD KEY `fee_groups_id` (`fee_groups_id`),
  ADD KEY `feetype_id` (`feetype_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `fee_receipt_no`
--
ALTER TABLE `fee_receipt_no`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fee_session_groups`
--
ALTER TABLE `fee_session_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_groups_id` (`fee_groups_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `filetypes`
--
ALTER TABLE `filetypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `follow_up`
--
ALTER TABLE `follow_up`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enquiry_id` (`enquiry_id`),
  ADD KEY `followup_by` (`followup_by`);

--
-- Indexes for table `front_cms_media_gallery`
--
ALTER TABLE `front_cms_media_gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `front_cms_menus`
--
ALTER TABLE `front_cms_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `front_cms_menu_items`
--
ALTER TABLE `front_cms_menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `front_cms_pages`
--
ALTER TABLE `front_cms_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `front_cms_page_contents`
--
ALTER TABLE `front_cms_page_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`);

--
-- Indexes for table `front_cms_programs`
--
ALTER TABLE `front_cms_programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `front_cms_program_photos`
--
ALTER TABLE `front_cms_program_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `front_cms_settings`
--
ALTER TABLE `front_cms_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gateway_ins`
--
ALTER TABLE `gateway_ins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_admission_id` (`online_admission_id`);

--
-- Indexes for table `gateway_ins_response`
--
ALTER TABLE `gateway_ins_response`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gateway_ins_id` (`gateway_ins_id`);

--
-- Indexes for table `general_calls`
--
ALTER TABLE `general_calls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gmeet`
--
ALTER TABLE `gmeet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `gmeet_history`
--
ALTER TABLE `gmeet_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gmeet_id` (`gmeet_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `gmeet_sections`
--
ALTER TABLE `gmeet_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cls_section_id` (`cls_section_id`),
  ADD KEY `gmeet_id` (`gmeet_id`);

--
-- Indexes for table `gmeet_settings`
--
ALTER TABLE `gmeet_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gmeet_staff`
--
ALTER TABLE `gmeet_staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gmeet_id` (`gmeet_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `google_authenticator`
--
ALTER TABLE `google_authenticator`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guest`
--
ALTER TABLE `guest`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `idx_lang_id` (`lang_id`),
  ADD KEY `idx_currency_id` (`currency_id`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `homework`
--
ALTER TABLE `homework`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_group_subject_id` (`subject_group_subject_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `evaluated_by` (`evaluated_by`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `homework_evaluation`
--
ALTER TABLE `homework_evaluation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `homework_id` (`homework_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `student_session_id` (`student_session_id`);

--
-- Indexes for table `hostel`
--
ALTER TABLE `hostel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hostel_rooms`
--
ALTER TABLE `hostel_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hostel_id` (`hostel_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `id_card`
--
ALTER TABLE `id_card`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`id`),
  ADD KEY `income_head_id` (`income_head_id`);

--
-- Indexes for table `income_head`
--
ALTER TABLE `income_head`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_category_id` (`item_category_id`),
  ADD KEY `item_store_id` (`item_store_id`),
  ADD KEY `item_supplier_id` (`item_supplier_id`);

--
-- Indexes for table `item_category`
--
ALTER TABLE `item_category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_issue`
--
ALTER TABLE `item_issue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `item_category_id` (`item_category_id`),
  ADD KEY `issue_to` (`issue_to`),
  ADD KEY `issue_by` (`issue_by`);

--
-- Indexes for table `item_stock`
--
ALTER TABLE `item_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `store_id` (`store_id`);

--
-- Indexes for table `item_store`
--
ALTER TABLE `item_store`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_supplier`
--
ALTER TABLE `item_supplier`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type` (`type`);

--
-- Indexes for table `lesson`
--
ALTER TABLE `lesson`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `subject_group_subject_id` (`subject_group_subject_id`),
  ADD KEY `subject_group_class_sections_id` (`subject_group_class_sections_id`);

--
-- Indexes for table `lesson_plan_forum`
--
ALTER TABLE `lesson_plan_forum`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_syllabus_id` (`subject_syllabus_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `libarary_members`
--
ALTER TABLE `libarary_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mark_divisions`
--
ALTER TABLE `mark_divisions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `multi_branch`
--
ALTER TABLE `multi_branch`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification_roles`
--
ALTER TABLE `notification_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `send_notification_id` (`send_notification_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `notification_setting`
--
ALTER TABLE `notification_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offline_fees_payments`
--
ALTER TABLE `offline_fees_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_fees_master_id` (`student_fees_master_id`),
  ADD KEY `fee_groups_feetype_id` (`fee_groups_feetype_id`),
  ADD KEY `student_transport_fee_id` (`student_transport_fee_id`),
  ADD KEY `offline_fees_payments_ibfk_4` (`approved_by`),
  ADD KEY `student_session_id` (`student_session_id`);

--
-- Indexes for table `onlineexam`
--
ALTER TABLE `onlineexam`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `onlineexam_attempts`
--
ALTER TABLE `onlineexam_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `onlineexam_student_id` (`onlineexam_student_id`);

--
-- Indexes for table `onlineexam_questions`
--
ALTER TABLE `onlineexam_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `onlineexam_id` (`onlineexam_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `onlineexam_students`
--
ALTER TABLE `onlineexam_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `onlineexam_id` (`onlineexam_id`),
  ADD KEY `student_session_id` (`student_session_id`);

--
-- Indexes for table `onlineexam_student_results`
--
ALTER TABLE `onlineexam_student_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `onlineexam_student_id` (`onlineexam_student_id`),
  ADD KEY `onlineexam_question_id` (`onlineexam_question_id`);

--
-- Indexes for table `online_admissions`
--
ALTER TABLE `online_admissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_section_id` (`class_section_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `hostel_room_id` (`hostel_room_id`),
  ADD KEY `school_house_id` (`school_house_id`),
  ADD KEY `idx_reference_no` (`reference_no`),
  ADD KEY `idx_mobileno` (`mobileno`);

--
-- Indexes for table `online_admission_custom_field_value`
--
ALTER TABLE `online_admission_custom_field_value`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_field_id` (`custom_field_id`),
  ADD KEY `idx_belong_table_id` (`belong_table_id`),
  ADD KEY `idx_field_value` (`field_value`(200));

--
-- Indexes for table `online_admission_fields`
--
ALTER TABLE `online_admission_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_admission_payment`
--
ALTER TABLE `online_admission_payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_admission_id` (`online_admission_id`);

--
-- Indexes for table `online_courses`
--
ALTER TABLE `online_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_category_id` (`category_id`);

--
-- Indexes for table `online_course_class_sections`
--
ALTER TABLE `online_course_class_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `class_section_id` (`class_section_id`);

--
-- Indexes for table `online_course_lesson`
--
ALTER TABLE `online_course_lesson`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_section_id` (`course_section_id`) USING BTREE;

--
-- Indexes for table `online_course_payment`
--
ALTER TABLE `online_course_payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_online_courses_id` (`online_courses_id`);

--
-- Indexes for table `online_course_processing_payment`
--
ALTER TABLE `online_course_processing_payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `online_courses_id` (`online_courses_id`);

--
-- Indexes for table `online_course_quiz`
--
ALTER TABLE `online_course_quiz`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `course_section_id` (`course_section_id`) USING BTREE;

--
-- Indexes for table `online_course_section`
--
ALTER TABLE `online_course_section`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_course_id` (`online_course_id`);

--
-- Indexes for table `online_course_settings`
--
ALTER TABLE `online_course_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_settings`
--
ALTER TABLE `payment_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payslip_allowance`
--
ALTER TABLE `payslip_allowance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `payslip_id` (`payslip_id`);

--
-- Indexes for table `permission_category`
--
ALTER TABLE `permission_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_short_code` (`short_code`),
  ADD KEY `perm_group_id` (`perm_group_id`);

--
-- Indexes for table `permission_group`
--
ALTER TABLE `permission_group`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permission_student`
--
ALTER TABLE `permission_student`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `pickup_point`
--
ALTER TABLE `pickup_point`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `print_headerfooter`
--
ALTER TABLE `print_headerfooter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `QR_code_settings`
--
ALTER TABLE `QR_code_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `class_section_id` (`class_section_id`);

--
-- Indexes for table `read_notification`
--
ALTER TABLE `read_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_id` (`notification_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `reference`
--
ALTER TABLE `reference`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles_permissions`
--
ALTER TABLE `roles_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `perm_cat_id` (`perm_cat_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `route_pickup_point`
--
ALTER TABLE `route_pickup_point`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transport_route_id` (`transport_route_id`),
  ADD KEY `pickup_point_id` (`pickup_point_id`);

--
-- Indexes for table `school_houses`
--
ALTER TABLE `school_houses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sch_settings`
--
ALTER TABLE `sch_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lang_id` (`lang_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `send_notification`
--
ALTER TABLE `send_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_id` (`created_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `share_contents`
--
ALTER TABLE `share_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `share_content_for`
--
ALTER TABLE `share_content_for`
  ADD PRIMARY KEY (`id`),
  ADD KEY `upload_content_id` (`share_content_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `class_section_id` (`class_section_id`),
  ADD KEY `user_parent_id` (`user_parent_id`);

--
-- Indexes for table `share_upload_contents`
--
ALTER TABLE `share_upload_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `upload_content_id` (`upload_content_id`),
  ADD KEY `share_content_id` (`share_content_id`);

--
-- Indexes for table `sidebar_menus`
--
ALTER TABLE `sidebar_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permission_group_id` (`permission_group_id`);

--
-- Indexes for table `sidebar_sub_menus`
--
ALTER TABLE `sidebar_sub_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sidebar_menu_id` (`sidebar_menu_id`),
  ADD KEY `permission_group_id` (`permission_group_id`);

--
-- Indexes for table `sms_config`
--
ALTER TABLE `sms_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sms_template`
--
ALTER TABLE `sms_template`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `source`
--
ALTER TABLE `source`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `designation` (`designation`),
  ADD KEY `department` (`department`);

--
-- Indexes for table `staff_attendance`
--
ALTER TABLE `staff_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_staff_attendance_staff` (`staff_id`),
  ADD KEY `FK_staff_attendance_staff_attendance_type` (`staff_attendance_type_id`);

--
-- Indexes for table `staff_attendance_type`
--
ALTER TABLE `staff_attendance_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_designation`
--
ALTER TABLE `staff_designation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_id_card`
--
ALTER TABLE `staff_id_card`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_leave_details`
--
ALTER TABLE `staff_leave_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_staff_leave_details_staff` (`staff_id`),
  ADD KEY `FK_staff_leave_details_leave_types` (`leave_type_id`);

--
-- Indexes for table `staff_leave_request`
--
ALTER TABLE `staff_leave_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_staff_leave_request_staff` (`staff_id`),
  ADD KEY `FK_staff_leave_request_leave_types` (`leave_type_id`),
  ADD KEY `applied_by` (`applied_by`);

--
-- Indexes for table `staff_payroll`
--
ALTER TABLE `staff_payroll`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_payslip`
--
ALTER TABLE `staff_payslip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_staff_payslip_staff` (`staff_id`);

--
-- Indexes for table `staff_rating`
--
ALTER TABLE `staff_rating`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_staff_rating_staff` (`staff_id`);

--
-- Indexes for table `staff_roles`
--
ALTER TABLE `staff_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `staff_timeline`
--
ALTER TABLE `staff_timeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_staff_timeline_staff` (`staff_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admission_no` (`admission_no`),
  ADD KEY `idx_roll_no` (`roll_no`),
  ADD KEY `idx_mobileno` (`mobileno`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_firstname` (`firstname`);

--
-- Indexes for table `student_applyleave`
--
ALTER TABLE `student_applyleave`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_session_id` (`student_session_id`),
  ADD KEY `approve_by` (`approve_by`);

--
-- Indexes for table `student_attendences`
--
ALTER TABLE `student_attendences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_session_id` (`student_session_id`),
  ADD KEY `attendence_type_id` (`attendence_type_id`);

--
-- Indexes for table `student_behaviour`
--
ALTER TABLE `student_behaviour`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_title` (`title`),
  ADD KEY `idx_description` (`description`(200));

--
-- Indexes for table `student_doc`
--
ALTER TABLE `student_doc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_id` (`student_id`);

--
-- Indexes for table `student_edit_fields`
--
ALTER TABLE `student_edit_fields`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_fees`
--
ALTER TABLE `student_fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feemaster_id` (`feemaster_id`),
  ADD KEY `student_session_id` (`student_session_id`);

--
-- Indexes for table `student_fees_deposite`
--
ALTER TABLE `student_fees_deposite`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_fees_master_id` (`student_fees_master_id`),
  ADD KEY `fee_groups_feetype_id` (`fee_groups_feetype_id`),
  ADD KEY `student_transport_fee_id` (`student_transport_fee_id`);

--
-- Indexes for table `student_fees_discounts`
--
ALTER TABLE `student_fees_discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_session_id` (`student_session_id`),
  ADD KEY `fees_discount_id` (`fees_discount_id`);

--
-- Indexes for table `student_fees_master`
--
ALTER TABLE `student_fees_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_session_id` (`student_session_id`),
  ADD KEY `fee_session_group_id` (`fee_session_group_id`);

--
-- Indexes for table `student_fees_processing`
--
ALTER TABLE `student_fees_processing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_fees_master_id` (`student_fees_master_id`),
  ADD KEY `fee_groups_feetype_id` (`fee_groups_feetype_id`),
  ADD KEY `student_transport_fee_id` (`student_transport_fee_id`),
  ADD KEY `gateway_ins_id` (`gateway_ins_id`);

--
-- Indexes for table `student_incidents`
--
ALTER TABLE `student_incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_incidents_ibfk_1` (`student_id`),
  ADD KEY `student_incidents_ibfk_2` (`incident_id`);

--
-- Indexes for table `student_incident_comments`
--
ALTER TABLE `student_incident_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_incident_comments_ibfk_1` (`student_incident_id`);

--
-- Indexes for table `student_quiz_status`
--
ALTER TABLE `student_quiz_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_quiz_id` (`course_quiz_id`) USING BTREE;

--
-- Indexes for table `student_session`
--
ALTER TABLE `student_session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `student_session_ibfk_5` (`vehroute_id`),
  ADD KEY `hostel_room_id` (`hostel_room_id`),
  ADD KEY `student_session_ibfk_6` (`route_pickup_point_id`);

--
-- Indexes for table `student_subject_attendances`
--
ALTER TABLE `student_subject_attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendence_type_id` (`attendence_type_id`),
  ADD KEY `student_session_id` (`student_session_id`),
  ADD KEY `subject_timetable_id` (`subject_timetable_id`);

--
-- Indexes for table `student_timeline`
--
ALTER TABLE `student_timeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `student_transport_fees`
--
ALTER TABLE `student_transport_fees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_session_id` (`student_session_id`),
  ADD KEY `route_pickup_point_id` (`route_pickup_point_id`),
  ADD KEY `transport_feemaster_id` (`transport_feemaster_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_code` (`code`);

--
-- Indexes for table `subject_groups`
--
ALTER TABLE `subject_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `subject_group_class_sections`
--
ALTER TABLE `subject_group_class_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_section_id` (`class_section_id`),
  ADD KEY `subject_group_id` (`subject_group_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `subject_group_subjects`
--
ALTER TABLE `subject_group_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_group_id` (`subject_group_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `subject_syllabus`
--
ALTER TABLE `subject_syllabus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `created_for` (`created_for`);

--
-- Indexes for table `subject_timetable`
--
ALTER TABLE `subject_timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `section_id` (`section_id`),
  ADD KEY `subject_group_id` (`subject_group_id`),
  ADD KEY `subject_group_subject_id` (`subject_group_subject_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `submit_assignment`
--
ALTER TABLE `submit_assignment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `homework_id` (`homework_id`);

--
-- Indexes for table `template_admitcards`
--
ALTER TABLE `template_admitcards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `template_marksheets`
--
ALTER TABLE `template_marksheets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `topic`
--
ALTER TABLE `topic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `transport_feemaster`
--
ALTER TABLE `transport_feemaster`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `transport_route`
--
ALTER TABLE `transport_route`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `upload_contents`
--
ALTER TABLE `upload_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `upload_by` (`upload_by`),
  ADD KEY `upload_contents_ibfk_2` (`content_type_id`),
  ADD KEY `idx_file_type` (`file_type`);

--
-- Indexes for table `userlog`
--
ALTER TABLE `userlog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_section_id` (`class_section_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_authentication`
--
ALTER TABLE `users_authentication`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_google_authenticate_codes`
--
ALTER TABLE `user_google_authenticate_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vehicle_no` (`vehicle_no`);

--
-- Indexes for table `vehicle_routes`
--
ALTER TABLE `vehicle_routes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `route_id` (`route_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `video_tutorial`
--
ALTER TABLE `video_tutorial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_title` (`title`);

--
-- Indexes for table `video_tutorial_class_sections`
--
ALTER TABLE `video_tutorial_class_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_section_id` (`class_section_id`),
  ADD KEY `video_tutorial_id` (`video_tutorial_id`);

--
-- Indexes for table `visitors_book`
--
ALTER TABLE `visitors_book`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `student_session_id` (`student_session_id`);

--
-- Indexes for table `visitors_purpose`
--
ALTER TABLE `visitors_purpose`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `zoom_settings`
--
ALTER TABLE `zoom_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alumni_events`
--
ALTER TABLE `alumni_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `alumni_students`
--
ALTER TABLE `alumni_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendence_type`
--
ALTER TABLE `attendence_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `aws_s3_settings`
--
ALTER TABLE `aws_s3_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `behaviour_settings`
--
ALTER TABLE `behaviour_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `book_issues`
--
ALTER TABLE `book_issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `captcha`
--
ALTER TABLE `captcha`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cbse_exams`
--
ALTER TABLE `cbse_exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cbse_exam_assessments`
--
ALTER TABLE `cbse_exam_assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cbse_exam_assessment_types`
--
ALTER TABLE `cbse_exam_assessment_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cbse_exam_class_sections`
--
ALTER TABLE `cbse_exam_class_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `cbse_exam_grades`
--
ALTER TABLE `cbse_exam_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cbse_exam_grades_range`
--
ALTER TABLE `cbse_exam_grades_range`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `cbse_exam_observations`
--
ALTER TABLE `cbse_exam_observations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cbse_exam_students`
--
ALTER TABLE `cbse_exam_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `cbse_exam_student_subject_rank`
--
ALTER TABLE `cbse_exam_student_subject_rank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `cbse_exam_timetable`
--
ALTER TABLE `cbse_exam_timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cbse_exam_timetable_assessment_types`
--
ALTER TABLE `cbse_exam_timetable_assessment_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `cbse_marksheet_type`
--
ALTER TABLE `cbse_marksheet_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cbse_observation_class_section`
--
ALTER TABLE `cbse_observation_class_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_observation_parameters`
--
ALTER TABLE `cbse_observation_parameters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cbse_observation_subparameter`
--
ALTER TABLE `cbse_observation_subparameter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cbse_observation_terms`
--
ALTER TABLE `cbse_observation_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cbse_observation_term_student_subparameter`
--
ALTER TABLE `cbse_observation_term_student_subparameter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_student_exam_ranks`
--
ALTER TABLE `cbse_student_exam_ranks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `cbse_student_subject_marks`
--
ALTER TABLE `cbse_student_subject_marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `cbse_student_subject_result`
--
ALTER TABLE `cbse_student_subject_result`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbse_student_template_rank`
--
ALTER TABLE `cbse_student_template_rank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `cbse_template`
--
ALTER TABLE `cbse_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cbse_template_class_sections`
--
ALTER TABLE `cbse_template_class_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `cbse_template_terms`
--
ALTER TABLE `cbse_template_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cbse_template_term_exams`
--
ALTER TABLE `cbse_template_term_exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cbse_terms`
--
ALTER TABLE `cbse_terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chat_connections`
--
ALTER TABLE `chat_connections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_users`
--
ALTER TABLE `chat_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `class_sections`
--
ALTER TABLE `class_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `class_section_times`
--
ALTER TABLE `class_section_times`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_teacher`
--
ALTER TABLE `class_teacher`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `complaint`
--
ALTER TABLE `complaint`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `complaint_type`
--
ALTER TABLE `complaint_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `conferences`
--
ALTER TABLE `conferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conferences_history`
--
ALTER TABLE `conferences_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conference_sections`
--
ALTER TABLE `conference_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conference_staff`
--
ALTER TABLE `conference_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contents`
--
ALTER TABLE `contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_for`
--
ALTER TABLE `content_for`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_types`
--
ALTER TABLE `content_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `course_category`
--
ALTER TABLE `course_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `course_lesson_quiz_order`
--
ALTER TABLE `course_lesson_quiz_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `course_progress`
--
ALTER TABLE `course_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_quiz_answer`
--
ALTER TABLE `course_quiz_answer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_quiz_question`
--
ALTER TABLE `course_quiz_question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `course_rating`
--
ALTER TABLE `course_rating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- AUTO_INCREMENT for table `custom_fields`
--
ALTER TABLE `custom_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `custom_field_values`
--
ALTER TABLE `custom_field_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `daily_assignment`
--
ALTER TABLE `daily_assignment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `disable_reason`
--
ALTER TABLE `disable_reason`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dispatch_receive`
--
ALTER TABLE `dispatch_receive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `email_attachments`
--
ALTER TABLE `email_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_config`
--
ALTER TABLE `email_config`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `email_template`
--
ALTER TABLE `email_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `email_template_attachment`
--
ALTER TABLE `email_template_attachment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enquiry`
--
ALTER TABLE `enquiry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `enquiry_type`
--
ALTER TABLE `enquiry_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_groups`
--
ALTER TABLE `exam_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `exam_group_class_batch_exams`
--
ALTER TABLE `exam_group_class_batch_exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exam_group_class_batch_exam_students`
--
ALTER TABLE `exam_group_class_batch_exam_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `exam_group_class_batch_exam_subjects`
--
ALTER TABLE `exam_group_class_batch_exam_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `exam_group_exam_connections`
--
ALTER TABLE `exam_group_exam_connections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_group_exam_results`
--
ALTER TABLE `exam_group_exam_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_group_students`
--
ALTER TABLE `exam_group_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_schedules`
--
ALTER TABLE `exam_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `expense_head`
--
ALTER TABLE `expense_head`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `feemasters`
--
ALTER TABLE `feemasters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fees_discounts`
--
ALTER TABLE `fees_discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fees_reminder`
--
ALTER TABLE `fees_reminder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `feetype`
--
ALTER TABLE `feetype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fee_groups`
--
ALTER TABLE `fee_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `fee_groups_feetype`
--
ALTER TABLE `fee_groups_feetype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `fee_receipt_no`
--
ALTER TABLE `fee_receipt_no`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_session_groups`
--
ALTER TABLE `fee_session_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `filetypes`
--
ALTER TABLE `filetypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `follow_up`
--
ALTER TABLE `follow_up`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `front_cms_media_gallery`
--
ALTER TABLE `front_cms_media_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `front_cms_menus`
--
ALTER TABLE `front_cms_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `front_cms_menu_items`
--
ALTER TABLE `front_cms_menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `front_cms_pages`
--
ALTER TABLE `front_cms_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `front_cms_page_contents`
--
ALTER TABLE `front_cms_page_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `front_cms_programs`
--
ALTER TABLE `front_cms_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `front_cms_program_photos`
--
ALTER TABLE `front_cms_program_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `front_cms_settings`
--
ALTER TABLE `front_cms_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gateway_ins`
--
ALTER TABLE `gateway_ins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gateway_ins_response`
--
ALTER TABLE `gateway_ins_response`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_calls`
--
ALTER TABLE `general_calls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gmeet`
--
ALTER TABLE `gmeet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `gmeet_history`
--
ALTER TABLE `gmeet_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gmeet_sections`
--
ALTER TABLE `gmeet_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `gmeet_settings`
--
ALTER TABLE `gmeet_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gmeet_staff`
--
ALTER TABLE `gmeet_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `google_authenticator`
--
ALTER TABLE `google_authenticator`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `guest`
--
ALTER TABLE `guest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `homework`
--
ALTER TABLE `homework`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `homework_evaluation`
--
ALTER TABLE `homework_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hostel`
--
ALTER TABLE `hostel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hostel_rooms`
--
ALTER TABLE `hostel_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `id_card`
--
ALTER TABLE `id_card`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `income_head`
--
ALTER TABLE `income_head`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `item_category`
--
ALTER TABLE `item_category`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `item_issue`
--
ALTER TABLE `item_issue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `item_stock`
--
ALTER TABLE `item_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `item_store`
--
ALTER TABLE `item_store`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `item_supplier`
--
ALTER TABLE `item_supplier`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lesson`
--
ALTER TABLE `lesson`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `lesson_plan_forum`
--
ALTER TABLE `lesson_plan_forum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `libarary_members`
--
ALTER TABLE `libarary_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1405;

--
-- AUTO_INCREMENT for table `mark_divisions`
--
ALTER TABLE `mark_divisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `multi_branch`
--
ALTER TABLE `multi_branch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `notification_roles`
--
ALTER TABLE `notification_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `notification_setting`
--
ALTER TABLE `notification_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `offline_fees_payments`
--
ALTER TABLE `offline_fees_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `onlineexam`
--
ALTER TABLE `onlineexam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `onlineexam_attempts`
--
ALTER TABLE `onlineexam_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `onlineexam_questions`
--
ALTER TABLE `onlineexam_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `onlineexam_students`
--
ALTER TABLE `onlineexam_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `onlineexam_student_results`
--
ALTER TABLE `onlineexam_student_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_admissions`
--
ALTER TABLE `online_admissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_admission_custom_field_value`
--
ALTER TABLE `online_admission_custom_field_value`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_admission_fields`
--
ALTER TABLE `online_admission_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `online_admission_payment`
--
ALTER TABLE `online_admission_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_courses`
--
ALTER TABLE `online_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `online_course_class_sections`
--
ALTER TABLE `online_course_class_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `online_course_lesson`
--
ALTER TABLE `online_course_lesson`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `online_course_payment`
--
ALTER TABLE `online_course_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `online_course_processing_payment`
--
ALTER TABLE `online_course_processing_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_course_quiz`
--
ALTER TABLE `online_course_quiz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `online_course_section`
--
ALTER TABLE `online_course_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `online_course_settings`
--
ALTER TABLE `online_course_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_settings`
--
ALTER TABLE `payment_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payslip_allowance`
--
ALTER TABLE `payslip_allowance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permission_category`
--
ALTER TABLE `permission_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12003;

--
-- AUTO_INCREMENT for table `permission_group`
--
ALTER TABLE `permission_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1201;

--
-- AUTO_INCREMENT for table `permission_student`
--
ALTER TABLE `permission_student`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=901;

--
-- AUTO_INCREMENT for table `pickup_point`
--
ALTER TABLE `pickup_point`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `print_headerfooter`
--
ALTER TABLE `print_headerfooter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `read_notification`
--
ALTER TABLE `read_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reference`
--
ALTER TABLE `reference`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roles_permissions`
--
ALTER TABLE `roles_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1606;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `route_pickup_point`
--
ALTER TABLE `route_pickup_point`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `school_houses`
--
ALTER TABLE `school_houses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `send_notification`
--
ALTER TABLE `send_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `share_contents`
--
ALTER TABLE `share_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `share_content_for`
--
ALTER TABLE `share_content_for`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `share_upload_contents`
--
ALTER TABLE `share_upload_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sidebar_menus`
--
ALTER TABLE `sidebar_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `sidebar_sub_menus`
--
ALTER TABLE `sidebar_sub_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;

--
-- AUTO_INCREMENT for table `sms_config`
--
ALTER TABLE `sms_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_template`
--
ALTER TABLE `sms_template`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `source`
--
ALTER TABLE `source`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `staff_attendance`
--
ALTER TABLE `staff_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_attendance_type`
--
ALTER TABLE `staff_attendance_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `staff_designation`
--
ALTER TABLE `staff_designation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `staff_id_card`
--
ALTER TABLE `staff_id_card`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `staff_leave_details`
--
ALTER TABLE `staff_leave_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `staff_leave_request`
--
ALTER TABLE `staff_leave_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_payroll`
--
ALTER TABLE `staff_payroll`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_payslip`
--
ALTER TABLE `staff_payslip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_rating`
--
ALTER TABLE `staff_rating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff_roles`
--
ALTER TABLE `staff_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `staff_timeline`
--
ALTER TABLE `staff_timeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `student_applyleave`
--
ALTER TABLE `student_applyleave`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_attendences`
--
ALTER TABLE `student_attendences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_behaviour`
--
ALTER TABLE `student_behaviour`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_doc`
--
ALTER TABLE `student_doc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_edit_fields`
--
ALTER TABLE `student_edit_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `student_fees`
--
ALTER TABLE `student_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_fees_deposite`
--
ALTER TABLE `student_fees_deposite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_fees_discounts`
--
ALTER TABLE `student_fees_discounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_fees_master`
--
ALTER TABLE `student_fees_master`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `student_fees_processing`
--
ALTER TABLE `student_fees_processing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_incidents`
--
ALTER TABLE `student_incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_incident_comments`
--
ALTER TABLE `student_incident_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_quiz_status`
--
ALTER TABLE `student_quiz_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_session`
--
ALTER TABLE `student_session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `student_subject_attendances`
--
ALTER TABLE `student_subject_attendances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_timeline`
--
ALTER TABLE `student_timeline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_transport_fees`
--
ALTER TABLE `student_transport_fees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subject_groups`
--
ALTER TABLE `subject_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subject_group_class_sections`
--
ALTER TABLE `subject_group_class_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `subject_group_subjects`
--
ALTER TABLE `subject_group_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `subject_syllabus`
--
ALTER TABLE `subject_syllabus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subject_timetable`
--
ALTER TABLE `subject_timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `submit_assignment`
--
ALTER TABLE `submit_assignment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `template_admitcards`
--
ALTER TABLE `template_admitcards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `template_marksheets`
--
ALTER TABLE `template_marksheets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `topic`
--
ALTER TABLE `topic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transport_feemaster`
--
ALTER TABLE `transport_feemaster`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `transport_route`
--
ALTER TABLE `transport_route`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `upload_contents`
--
ALTER TABLE `upload_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `userlog`
--
ALTER TABLE `userlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `users_authentication`
--
ALTER TABLE `users_authentication`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_google_authenticate_codes`
--
ALTER TABLE `user_google_authenticate_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vehicle_routes`
--
ALTER TABLE `vehicle_routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `video_tutorial`
--
ALTER TABLE `video_tutorial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `video_tutorial_class_sections`
--
ALTER TABLE `video_tutorial_class_sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `visitors_book`
--
ALTER TABLE `visitors_book`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `visitors_purpose`
--
ALTER TABLE `visitors_purpose`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `zoom_settings`
--
ALTER TABLE `zoom_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alumni_events`
--
ALTER TABLE `alumni_events`
  ADD CONSTRAINT `alumni_events_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `alumni_events_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `alumni_students`
--
ALTER TABLE `alumni_students`
  ADD CONSTRAINT `alumni_students_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `book_issues`
--
ALTER TABLE `book_issues`
  ADD CONSTRAINT `book_issues_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `book_issues_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `libarary_members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_exams`
--
ALTER TABLE `cbse_exams`
  ADD CONSTRAINT `cbse_exams_ibfk_1` FOREIGN KEY (`cbse_term_id`) REFERENCES `cbse_terms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exams_ibfk_2` FOREIGN KEY (`cbse_exam_grade_id`) REFERENCES `cbse_exam_grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exams_ibfk_3` FOREIGN KEY (`cbse_exam_assessment_id`) REFERENCES `cbse_exam_assessments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exams_ibfk_4` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_exam_assessment_types`
--
ALTER TABLE `cbse_exam_assessment_types`
  ADD CONSTRAINT `cbse_exam_assessment_types_ibfk_1` FOREIGN KEY (`cbse_exam_assessment_id`) REFERENCES `cbse_exam_assessments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_exam_class_sections`
--
ALTER TABLE `cbse_exam_class_sections`
  ADD CONSTRAINT `cbse_exam_class_sections_ibfk_1` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exam_class_sections_ibfk_2` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_exam_grades_range`
--
ALTER TABLE `cbse_exam_grades_range`
  ADD CONSTRAINT `cbse_exam_grades_range_ibfk_1` FOREIGN KEY (`cbse_exam_grade_id`) REFERENCES `cbse_exam_grades` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_exam_students`
--
ALTER TABLE `cbse_exam_students`
  ADD CONSTRAINT `cbse_exam_students_ibfk_1` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exam_students_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_exam_student_subject_rank`
--
ALTER TABLE `cbse_exam_student_subject_rank`
  ADD CONSTRAINT `cbse_exam_student_subject_rank_ibfk_1` FOREIGN KEY (`cbse_template_id`) REFERENCES `cbse_template` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exam_student_subject_rank_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exam_student_subject_rank_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_exam_timetable`
--
ALTER TABLE `cbse_exam_timetable`
  ADD CONSTRAINT `cbse_exam_timetable_ibfk_1` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exam_timetable_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_exam_timetable_assessment_types`
--
ALTER TABLE `cbse_exam_timetable_assessment_types`
  ADD CONSTRAINT `cbse_exam_timetable_assessment_types_ibfk_1` FOREIGN KEY (`cbse_exam_timetable_id`) REFERENCES `cbse_exam_timetable` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_exam_timetable_assessment_types_ibfk_2` FOREIGN KEY (`cbse_exam_assessment_type_id`) REFERENCES `cbse_exam_assessment_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_observation_subparameter`
--
ALTER TABLE `cbse_observation_subparameter`
  ADD CONSTRAINT `cbse_exam_observation_id_ibfk_1` FOREIGN KEY (`cbse_exam_observation_id`) REFERENCES `cbse_exam_observations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_observation_parameter_id_ibfk_1` FOREIGN KEY (`cbse_observation_parameter_id`) REFERENCES `cbse_observation_parameters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_observation_terms`
--
ALTER TABLE `cbse_observation_terms`
  ADD CONSTRAINT `cbse_exam_observations_ibfk_1` FOREIGN KEY (`cbse_exam_observation_id`) REFERENCES `cbse_exam_observations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_observation_terms_ibfk_2` FOREIGN KEY (`cbse_term_id`) REFERENCES `cbse_terms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_observation_terms_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_observation_term_student_subparameter`
--
ALTER TABLE `cbse_observation_term_student_subparameter`
  ADD CONSTRAINT `cbse_observation_term_student_subparameter_ibfk_1` FOREIGN KEY (`cbse_ovservation_term_id`) REFERENCES `cbse_observation_terms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_observation_term_student_subparameter_ibfk_2` FOREIGN KEY (`cbse_observation_subparameter_id`) REFERENCES `cbse_observation_subparameter` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_observation_term_student_subparameter_ibfk_3` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_student_exam_ranks`
--
ALTER TABLE `cbse_student_exam_ranks`
  ADD CONSTRAINT `cbse_student_exam_ranks_ibfk_1` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_student_exam_ranks_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_student_subject_marks`
--
ALTER TABLE `cbse_student_subject_marks`
  ADD CONSTRAINT `cbse_exam_timetable_assessment_type_ibfk_4` FOREIGN KEY (`cbse_exam_timetable_assessment_type_id`) REFERENCES `cbse_exam_timetable_assessment_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_student_subject_marks_ibfk_1` FOREIGN KEY (`cbse_exam_timetable_id`) REFERENCES `cbse_exam_timetable` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_student_subject_marks_ibfk_2` FOREIGN KEY (`cbse_exam_student_id`) REFERENCES `cbse_exam_students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_student_subject_marks_ibfk_3` FOREIGN KEY (`cbse_exam_assessment_type_id`) REFERENCES `cbse_exam_assessment_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_student_template_rank`
--
ALTER TABLE `cbse_student_template_rank`
  ADD CONSTRAINT `cbse_student_template_rank_ibfk_1` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_student_template_rank_ibfk_2` FOREIGN KEY (`cbse_template_id`) REFERENCES `cbse_template` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_template`
--
ALTER TABLE `cbse_template`
  ADD CONSTRAINT `cbse_template_ibfk_1` FOREIGN KEY (`gradeexam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cbse_template_ibfk_2` FOREIGN KEY (`remarkexam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cbse_template_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_template_class_sections`
--
ALTER TABLE `cbse_template_class_sections`
  ADD CONSTRAINT `cbse_template_class_sections_ibfk_1` FOREIGN KEY (`cbse_template_id`) REFERENCES `cbse_template` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_template_class_sections_ibfk_2` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_template_terms`
--
ALTER TABLE `cbse_template_terms`
  ADD CONSTRAINT `cbse_template_terms_ibfk_1` FOREIGN KEY (`cbse_template_id`) REFERENCES `cbse_template` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_template_terms_ibfk_2` FOREIGN KEY (`cbse_term_id`) REFERENCES `cbse_terms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cbse_template_term_exams`
--
ALTER TABLE `cbse_template_term_exams`
  ADD CONSTRAINT `cbse_template_term_exams_ibfk_1` FOREIGN KEY (`cbse_exam_id`) REFERENCES `cbse_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_template_term_exams_ibfk_2` FOREIGN KEY (`cbse_template_term_id`) REFERENCES `cbse_template_terms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cbse_template_term_exams_ibfk_4` FOREIGN KEY (`cbse_template_id`) REFERENCES `cbse_template` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_connections`
--
ALTER TABLE `chat_connections`
  ADD CONSTRAINT `chat_connections_ibfk_1` FOREIGN KEY (`chat_user_one`) REFERENCES `chat_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_connections_ibfk_2` FOREIGN KEY (`chat_user_two`) REFERENCES `chat_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`chat_user_id`) REFERENCES `chat_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`chat_connection_id`) REFERENCES `chat_connections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_users`
--
ALTER TABLE `chat_users`
  ADD CONSTRAINT `chat_users_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_users_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_users_ibfk_3` FOREIGN KEY (`create_staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_users_ibfk_4` FOREIGN KEY (`create_student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_sections`
--
ALTER TABLE `class_sections`
  ADD CONSTRAINT `class_sections_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_sections_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_section_times`
--
ALTER TABLE `class_section_times`
  ADD CONSTRAINT `class_section_times_ibfk_1` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_teacher`
--
ALTER TABLE `class_teacher`
  ADD CONSTRAINT `class_teacher_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_teacher_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_teacher_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_teacher_ibfk_4` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conferences`
--
ALTER TABLE `conferences`
  ADD CONSTRAINT `conferences_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conferences_ibfk_2` FOREIGN KEY (`created_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conferences_history`
--
ALTER TABLE `conferences_history`
  ADD CONSTRAINT `conferences_history_ibfk_1` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conferences_history_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conferences_history_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conference_sections`
--
ALTER TABLE `conference_sections`
  ADD CONSTRAINT `conference_sections_ibfk_1` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conference_sections_ibfk_2` FOREIGN KEY (`cls_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conference_staff`
--
ALTER TABLE `conference_staff`
  ADD CONSTRAINT `conference_staff_ibfk_1` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conference_staff_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contents`
--
ALTER TABLE `contents`
  ADD CONSTRAINT `contents_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contents_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contents_ibfk_3` FOREIGN KEY (`cls_sec_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `content_for`
--
ALTER TABLE `content_for`
  ADD CONSTRAINT `content_for_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `content_for_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_lesson_quiz_order`
--
ALTER TABLE `course_lesson_quiz_order`
  ADD CONSTRAINT `course_lesson_quiz_order_ibfk_1` FOREIGN KEY (`course_section_id`) REFERENCES `online_course_section` (`id`);

--
-- Constraints for table `course_progress`
--
ALTER TABLE `course_progress`
  ADD CONSTRAINT `course_progress_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `online_courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_progress_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_progress_ibfk_3` FOREIGN KEY (`course_section_id`) REFERENCES `online_course_section` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_quiz_answer`
--
ALTER TABLE `course_quiz_answer`
  ADD CONSTRAINT `course_quiz_answer_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_quiz_answer_ibfk_2` FOREIGN KEY (`course_quiz_id`) REFERENCES `online_course_quiz` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_quiz_answer_ibfk_3` FOREIGN KEY (`course_quiz_question_id`) REFERENCES `course_quiz_question` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_quiz_question`
--
ALTER TABLE `course_quiz_question`
  ADD CONSTRAINT `course_quiz_question_ibfk_1` FOREIGN KEY (`course_quiz_id`) REFERENCES `online_course_quiz` (`id`);

--
-- Constraints for table `custom_field_values`
--
ALTER TABLE `custom_field_values`
  ADD CONSTRAINT `custom_field_values_ibfk_1` FOREIGN KEY (`custom_field_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daily_assignment`
--
ALTER TABLE `daily_assignment`
  ADD CONSTRAINT `daily_assignment_ibfk_1` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `daily_assignment_ibfk_2` FOREIGN KEY (`evaluated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `daily_assignment_ibfk_3` FOREIGN KEY (`subject_group_subject_id`) REFERENCES `subject_group_subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `email_attachments`
--
ALTER TABLE `email_attachments`
  ADD CONSTRAINT `email_attachments_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enquiry`
--
ALTER TABLE `enquiry`
  ADD CONSTRAINT `enquiry_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enquiry_ibfk_3` FOREIGN KEY (`assigned`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enquiry_ibfk_4` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`sesion_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_group_class_batch_exams`
--
ALTER TABLE `exam_group_class_batch_exams`
  ADD CONSTRAINT `exam_group_class_batch_exams_ibfk_1` FOREIGN KEY (`exam_group_id`) REFERENCES `exam_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_group_class_batch_exams_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_group_class_batch_exam_students`
--
ALTER TABLE `exam_group_class_batch_exam_students`
  ADD CONSTRAINT `exam_group_class_batch_exam_students_ibfk_1` FOREIGN KEY (`exam_group_class_batch_exam_id`) REFERENCES `exam_group_class_batch_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_group_class_batch_exam_students_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_group_class_batch_exam_students_ibfk_3` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_group_class_batch_exam_subjects`
--
ALTER TABLE `exam_group_class_batch_exam_subjects`
  ADD CONSTRAINT `exam_group_class_batch_exam_subjects_ibfk_1` FOREIGN KEY (`exam_group_class_batch_exams_id`) REFERENCES `exam_group_class_batch_exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_group_class_batch_exam_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_group_exam_connections`
--
ALTER TABLE `exam_group_exam_connections`
  ADD CONSTRAINT `exam_group_exam_connections_ibfk_1` FOREIGN KEY (`exam_group_id`) REFERENCES `exam_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_group_exam_connections_ibfk_2` FOREIGN KEY (`exam_group_class_batch_exams_id`) REFERENCES `exam_group_class_batch_exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_group_exam_results`
--
ALTER TABLE `exam_group_exam_results`
  ADD CONSTRAINT `exam_group_exam_results_ibfk_1` FOREIGN KEY (`exam_group_class_batch_exam_subject_id`) REFERENCES `exam_group_class_batch_exam_subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_group_exam_results_ibfk_2` FOREIGN KEY (`exam_group_student_id`) REFERENCES `exam_group_students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_group_exam_results_ibfk_3` FOREIGN KEY (`exam_group_class_batch_exam_student_id`) REFERENCES `exam_group_class_batch_exam_students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_group_students`
--
ALTER TABLE `exam_group_students`
  ADD CONSTRAINT `exam_group_students_ibfk_1` FOREIGN KEY (`exam_group_id`) REFERENCES `exam_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_group_students_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_group_students_ibfk_3` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_schedules`
--
ALTER TABLE `exam_schedules`
  ADD CONSTRAINT `exam_schedules_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_schedules_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`exp_head_id`) REFERENCES `expense_head` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feemasters`
--
ALTER TABLE `feemasters`
  ADD CONSTRAINT `feemasters_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feemasters_ibfk_2` FOREIGN KEY (`feetype_id`) REFERENCES `feetype` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feemasters_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fees_discounts`
--
ALTER TABLE `fees_discounts`
  ADD CONSTRAINT `fees_discounts_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_groups_feetype`
--
ALTER TABLE `fee_groups_feetype`
  ADD CONSTRAINT `fee_groups_feetype_ibfk_1` FOREIGN KEY (`fee_session_group_id`) REFERENCES `fee_session_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_groups_feetype_ibfk_2` FOREIGN KEY (`fee_groups_id`) REFERENCES `fee_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_groups_feetype_ibfk_3` FOREIGN KEY (`feetype_id`) REFERENCES `feetype` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_groups_feetype_ibfk_4` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fee_session_groups`
--
ALTER TABLE `fee_session_groups`
  ADD CONSTRAINT `fee_session_groups_ibfk_1` FOREIGN KEY (`fee_groups_id`) REFERENCES `fee_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fee_session_groups_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `follow_up`
--
ALTER TABLE `follow_up`
  ADD CONSTRAINT `follow_up_ibfk_1` FOREIGN KEY (`enquiry_id`) REFERENCES `enquiry` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `follow_up_ibfk_2` FOREIGN KEY (`followup_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `front_cms_menu_items`
--
ALTER TABLE `front_cms_menu_items`
  ADD CONSTRAINT `front_cms_menu_items_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `front_cms_menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `front_cms_page_contents`
--
ALTER TABLE `front_cms_page_contents`
  ADD CONSTRAINT `front_cms_page_contents_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `front_cms_pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `front_cms_program_photos`
--
ALTER TABLE `front_cms_program_photos`
  ADD CONSTRAINT `front_cms_program_photos_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `front_cms_programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gateway_ins`
--
ALTER TABLE `gateway_ins`
  ADD CONSTRAINT `gateway_ins_ibfk_1` FOREIGN KEY (`online_admission_id`) REFERENCES `online_admissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gateway_ins_response`
--
ALTER TABLE `gateway_ins_response`
  ADD CONSTRAINT `gateway_ins_response_ibfk_1` FOREIGN KEY (`gateway_ins_id`) REFERENCES `gateway_ins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gmeet`
--
ALTER TABLE `gmeet`
  ADD CONSTRAINT `gmeet_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeet_ibfk_2` FOREIGN KEY (`created_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeet_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gmeet_history`
--
ALTER TABLE `gmeet_history`
  ADD CONSTRAINT `gmeet_history_ibfk_1` FOREIGN KEY (`gmeet_id`) REFERENCES `gmeet` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeet_history_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeet_history_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gmeet_sections`
--
ALTER TABLE `gmeet_sections`
  ADD CONSTRAINT `gmeet_sections_ibfk_1` FOREIGN KEY (`cls_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeet_sections_ibfk_2` FOREIGN KEY (`gmeet_id`) REFERENCES `gmeet` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gmeet_staff`
--
ALTER TABLE `gmeet_staff`
  ADD CONSTRAINT `gmeet_staff_ibfk_1` FOREIGN KEY (`gmeet_id`) REFERENCES `gmeet` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeet_staff_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `homework`
--
ALTER TABLE `homework`
  ADD CONSTRAINT `homework_ibfk_1` FOREIGN KEY (`subject_group_subject_id`) REFERENCES `subject_group_subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_ibfk_3` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_ibfk_4` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_ibfk_5` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_ibfk_6` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_ibfk_7` FOREIGN KEY (`evaluated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_ibfk_8` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `homework_evaluation`
--
ALTER TABLE `homework_evaluation`
  ADD CONSTRAINT `homework_evaluation_ibfk_1` FOREIGN KEY (`homework_id`) REFERENCES `homework` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_evaluation_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `homework_evaluation_ibfk_3` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hostel_rooms`
--
ALTER TABLE `hostel_rooms`
  ADD CONSTRAINT `hostel_rooms_ibfk_1` FOREIGN KEY (`hostel_id`) REFERENCES `hostel` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hostel_rooms_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `income_ibfk_1` FOREIGN KEY (`income_head_id`) REFERENCES `income_head` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`item_category_id`) REFERENCES `item_category` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_ibfk_2` FOREIGN KEY (`item_store_id`) REFERENCES `item_store` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_ibfk_3` FOREIGN KEY (`item_supplier_id`) REFERENCES `item_supplier` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_issue`
--
ALTER TABLE `item_issue`
  ADD CONSTRAINT `item_issue_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_issue_ibfk_2` FOREIGN KEY (`item_category_id`) REFERENCES `item_category` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_issue_ibfk_3` FOREIGN KEY (`issue_to`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_issue_ibfk_4` FOREIGN KEY (`issue_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_stock`
--
ALTER TABLE `item_stock`
  ADD CONSTRAINT `item_stock_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_stock_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `item_supplier` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_stock_ibfk_3` FOREIGN KEY (`store_id`) REFERENCES `item_store` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson`
--
ALTER TABLE `lesson`
  ADD CONSTRAINT `lesson_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_ibfk_2` FOREIGN KEY (`subject_group_subject_id`) REFERENCES `subject_group_subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_ibfk_3` FOREIGN KEY (`subject_group_class_sections_id`) REFERENCES `subject_group_class_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_plan_forum`
--
ALTER TABLE `lesson_plan_forum`
  ADD CONSTRAINT `lesson_plan_forum_ibfk_1` FOREIGN KEY (`subject_syllabus_id`) REFERENCES `subject_syllabus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_plan_forum_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_plan_forum_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_roles`
--
ALTER TABLE `notification_roles`
  ADD CONSTRAINT `notification_roles_ibfk_1` FOREIGN KEY (`send_notification_id`) REFERENCES `send_notification` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `offline_fees_payments`
--
ALTER TABLE `offline_fees_payments`
  ADD CONSTRAINT `offline_fees_payments_ibfk_1` FOREIGN KEY (`student_fees_master_id`) REFERENCES `student_fees_master` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offline_fees_payments_ibfk_2` FOREIGN KEY (`fee_groups_feetype_id`) REFERENCES `fee_groups_feetype` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offline_fees_payments_ibfk_3` FOREIGN KEY (`student_transport_fee_id`) REFERENCES `student_transport_fees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offline_fees_payments_ibfk_4` FOREIGN KEY (`approved_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offline_fees_payments_ibfk_5` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `onlineexam`
--
ALTER TABLE `onlineexam`
  ADD CONSTRAINT `onlineexam_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `onlineexam_attempts`
--
ALTER TABLE `onlineexam_attempts`
  ADD CONSTRAINT `onlineexam_attempts_ibfk_1` FOREIGN KEY (`onlineexam_student_id`) REFERENCES `onlineexam_students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `onlineexam_questions`
--
ALTER TABLE `onlineexam_questions`
  ADD CONSTRAINT `onlineexam_questions_ibfk_1` FOREIGN KEY (`onlineexam_id`) REFERENCES `onlineexam` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `onlineexam_questions_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `onlineexam_questions_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `onlineexam_students`
--
ALTER TABLE `onlineexam_students`
  ADD CONSTRAINT `onlineexam_students_ibfk_1` FOREIGN KEY (`onlineexam_id`) REFERENCES `onlineexam` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `onlineexam_students_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `onlineexam_student_results`
--
ALTER TABLE `onlineexam_student_results`
  ADD CONSTRAINT `onlineexam_student_results_ibfk_1` FOREIGN KEY (`onlineexam_student_id`) REFERENCES `onlineexam_students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `onlineexam_student_results_ibfk_2` FOREIGN KEY (`onlineexam_question_id`) REFERENCES `onlineexam_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_admissions`
--
ALTER TABLE `online_admissions`
  ADD CONSTRAINT `online_admissions_ibfk_1` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_ibfk_3` FOREIGN KEY (`hostel_room_id`) REFERENCES `hostel_rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_admissions_ibfk_4` FOREIGN KEY (`school_house_id`) REFERENCES `school_houses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_admission_custom_field_value`
--
ALTER TABLE `online_admission_custom_field_value`
  ADD CONSTRAINT `online_admission_custom_field_value_ibfk_1` FOREIGN KEY (`custom_field_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_admission_payment`
--
ALTER TABLE `online_admission_payment`
  ADD CONSTRAINT `online_admission_payment_ibfk_1` FOREIGN KEY (`online_admission_id`) REFERENCES `online_admissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_courses`
--
ALTER TABLE `online_courses`
  ADD CONSTRAINT `online_courses_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_course_class_sections`
--
ALTER TABLE `online_course_class_sections`
  ADD CONSTRAINT `online_course_class_sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `online_courses` (`id`),
  ADD CONSTRAINT `online_course_class_sections_ibfk_2` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`);

--
-- Constraints for table `online_course_lesson`
--
ALTER TABLE `online_course_lesson`
  ADD CONSTRAINT `online_course_lesson_ibfk_1` FOREIGN KEY (`course_section_id`) REFERENCES `online_course_section` (`id`);

--
-- Constraints for table `online_course_quiz`
--
ALTER TABLE `online_course_quiz`
  ADD CONSTRAINT `online_course_quiz_ibfk_1` FOREIGN KEY (`course_section_id`) REFERENCES `online_course_section` (`id`),
  ADD CONSTRAINT `online_course_quiz_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_course_section`
--
ALTER TABLE `online_course_section`
  ADD CONSTRAINT `online_course_section_ibfk_1` FOREIGN KEY (`online_course_id`) REFERENCES `online_courses` (`id`);

--
-- Constraints for table `payslip_allowance`
--
ALTER TABLE `payslip_allowance`
  ADD CONSTRAINT `payslip_allowance_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payslip_allowance_ibfk_2` FOREIGN KEY (`payslip_id`) REFERENCES `staff_payslip` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permission_category`
--
ALTER TABLE `permission_category`
  ADD CONSTRAINT `permission_category_ibfk_1` FOREIGN KEY (`perm_group_id`) REFERENCES `permission_group` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permission_student`
--
ALTER TABLE `permission_student`
  ADD CONSTRAINT `permission_student_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `permission_group` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_ibfk_4` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_ibfk_5` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_ibfk_6` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `read_notification`
--
ALTER TABLE `read_notification`
  ADD CONSTRAINT `read_notification_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `send_notification` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `read_notification_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `read_notification_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roles_permissions`
--
ALTER TABLE `roles_permissions`
  ADD CONSTRAINT `roles_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_permissions_ibfk_2` FOREIGN KEY (`perm_cat_id`) REFERENCES `permission_category` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `route_pickup_point`
--
ALTER TABLE `route_pickup_point`
  ADD CONSTRAINT `route_pickup_point_ibfk_1` FOREIGN KEY (`transport_route_id`) REFERENCES `transport_route` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `route_pickup_point_ibfk_2` FOREIGN KEY (`pickup_point_id`) REFERENCES `pickup_point` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `send_notification`
--
ALTER TABLE `send_notification`
  ADD CONSTRAINT `send_notification_ibfk_1` FOREIGN KEY (`created_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `share_contents`
--
ALTER TABLE `share_contents`
  ADD CONSTRAINT `share_contents_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `share_content_for`
--
ALTER TABLE `share_content_for`
  ADD CONSTRAINT `share_content_for_ibfk_1` FOREIGN KEY (`share_content_id`) REFERENCES `share_contents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `share_content_for_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `share_content_for_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`),
  ADD CONSTRAINT `share_content_for_ibfk_4` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`),
  ADD CONSTRAINT `share_content_for_ibfk_5` FOREIGN KEY (`user_parent_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `share_upload_contents`
--
ALTER TABLE `share_upload_contents`
  ADD CONSTRAINT `share_upload_contents_ibfk_1` FOREIGN KEY (`upload_content_id`) REFERENCES `upload_contents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `share_upload_contents_ibfk_2` FOREIGN KEY (`share_content_id`) REFERENCES `share_contents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sidebar_menus`
--
ALTER TABLE `sidebar_menus`
  ADD CONSTRAINT `sidebar_menus_ibfk_1` FOREIGN KEY (`permission_group_id`) REFERENCES `permission_group` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sidebar_sub_menus`
--
ALTER TABLE `sidebar_sub_menus`
  ADD CONSTRAINT `sidebar_sub_menus_ibfk_1` FOREIGN KEY (`sidebar_menu_id`) REFERENCES `sidebar_menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sidebar_sub_menus_ibfk_2` FOREIGN KEY (`permission_group_id`) REFERENCES `permission_group` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`designation`) REFERENCES `staff_designation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`department`) REFERENCES `department` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_attendance`
--
ALTER TABLE `staff_attendance`
  ADD CONSTRAINT `FK_staff_attendance_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_staff_attendance_staff_attendance_type` FOREIGN KEY (`staff_attendance_type_id`) REFERENCES `staff_attendance_type` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_leave_details`
--
ALTER TABLE `staff_leave_details`
  ADD CONSTRAINT `FK_staff_leave_details_leave_types` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_staff_leave_details_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_leave_request`
--
ALTER TABLE `staff_leave_request`
  ADD CONSTRAINT `FK_staff_leave_request_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_leave_request_ibfk_1` FOREIGN KEY (`applied_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_leave_request_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_payslip`
--
ALTER TABLE `staff_payslip`
  ADD CONSTRAINT `FK_staff_payslip_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_rating`
--
ALTER TABLE `staff_rating`
  ADD CONSTRAINT `FK_staff_rating_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_roles`
--
ALTER TABLE `staff_roles`
  ADD CONSTRAINT `FK_staff_roles_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_staff_roles_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_timeline`
--
ALTER TABLE `staff_timeline`
  ADD CONSTRAINT `FK_staff_timeline_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_applyleave`
--
ALTER TABLE `student_applyleave`
  ADD CONSTRAINT `student_applyleave_ibfk_1` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_applyleave_ibfk_2` FOREIGN KEY (`approve_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_attendences`
--
ALTER TABLE `student_attendences`
  ADD CONSTRAINT `student_attendences_ibfk_1` FOREIGN KEY (`attendence_type_id`) REFERENCES `attendence_type` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_attendences_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_fees`
--
ALTER TABLE `student_fees`
  ADD CONSTRAINT `student_fees_ibfk_1` FOREIGN KEY (`feemaster_id`) REFERENCES `feemasters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_fees_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_fees_deposite`
--
ALTER TABLE `student_fees_deposite`
  ADD CONSTRAINT `student_fees_deposite_ibfk_1` FOREIGN KEY (`student_transport_fee_id`) REFERENCES `student_transport_fees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_fees_deposite_ibfk_2` FOREIGN KEY (`student_fees_master_id`) REFERENCES `student_fees_master` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_fees_deposite_ibfk_3` FOREIGN KEY (`fee_groups_feetype_id`) REFERENCES `fee_groups_feetype` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_fees_discounts`
--
ALTER TABLE `student_fees_discounts`
  ADD CONSTRAINT `student_fees_discounts_ibfk_1` FOREIGN KEY (`fees_discount_id`) REFERENCES `fees_discounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_fees_discounts_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_fees_master`
--
ALTER TABLE `student_fees_master`
  ADD CONSTRAINT `student_fees_master_ibfk_1` FOREIGN KEY (`fee_session_group_id`) REFERENCES `fee_session_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_fees_master_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_fees_processing`
--
ALTER TABLE `student_fees_processing`
  ADD CONSTRAINT `student_fees_processing_ibfk_1` FOREIGN KEY (`student_fees_master_id`) REFERENCES `student_fees_master` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_fees_processing_ibfk_2` FOREIGN KEY (`student_transport_fee_id`) REFERENCES `student_transport_fees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_fees_processing_ibfk_3` FOREIGN KEY (`fee_groups_feetype_id`) REFERENCES `fee_groups_feetype` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_fees_processing_ibfk_4` FOREIGN KEY (`gateway_ins_id`) REFERENCES `gateway_ins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_incidents`
--
ALTER TABLE `student_incidents`
  ADD CONSTRAINT `student_incidents_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_incidents_ibfk_2` FOREIGN KEY (`incident_id`) REFERENCES `student_behaviour` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_incident_comments`
--
ALTER TABLE `student_incident_comments`
  ADD CONSTRAINT `student_incident_comments_ibfk_1` FOREIGN KEY (`student_incident_id`) REFERENCES `student_incidents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_quiz_status`
--
ALTER TABLE `student_quiz_status`
  ADD CONSTRAINT `student_quiz_status_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_quiz_status_ibfk_2` FOREIGN KEY (`course_quiz_id`) REFERENCES `online_course_quiz` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_session`
--
ALTER TABLE `student_session`
  ADD CONSTRAINT `student_session_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_session_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_session_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_session_ibfk_4` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_session_ibfk_5` FOREIGN KEY (`vehroute_id`) REFERENCES `vehicle_routes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_session_ibfk_6` FOREIGN KEY (`route_pickup_point_id`) REFERENCES `route_pickup_point` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `student_session_ibfk_7` FOREIGN KEY (`hostel_room_id`) REFERENCES `hostel_rooms` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_subject_attendances`
--
ALTER TABLE `student_subject_attendances`
  ADD CONSTRAINT `student_subject_attendances_ibfk_1` FOREIGN KEY (`attendence_type_id`) REFERENCES `attendence_type` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_subject_attendances_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_subject_attendances_ibfk_3` FOREIGN KEY (`subject_timetable_id`) REFERENCES `subject_timetable` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_timeline`
--
ALTER TABLE `student_timeline`
  ADD CONSTRAINT `student_timeline_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_transport_fees`
--
ALTER TABLE `student_transport_fees`
  ADD CONSTRAINT `student_transport_fees_ibfk_1` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_transport_fees_ibfk_2` FOREIGN KEY (`route_pickup_point_id`) REFERENCES `route_pickup_point` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_transport_fees_ibfk_3` FOREIGN KEY (`transport_feemaster_id`) REFERENCES `transport_feemaster` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_groups`
--
ALTER TABLE `subject_groups`
  ADD CONSTRAINT `subject_groups_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_group_class_sections`
--
ALTER TABLE `subject_group_class_sections`
  ADD CONSTRAINT `subject_group_class_sections_ibfk_1` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_group_class_sections_ibfk_2` FOREIGN KEY (`subject_group_id`) REFERENCES `subject_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_group_class_sections_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_group_subjects`
--
ALTER TABLE `subject_group_subjects`
  ADD CONSTRAINT `subject_group_subjects_ibfk_1` FOREIGN KEY (`subject_group_id`) REFERENCES `subject_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_group_subjects_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_group_subjects_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_syllabus`
--
ALTER TABLE `subject_syllabus`
  ADD CONSTRAINT `subject_syllabus_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topic` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_syllabus_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_syllabus_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_syllabus_ibfk_4` FOREIGN KEY (`created_for`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_timetable`
--
ALTER TABLE `subject_timetable`
  ADD CONSTRAINT `subject_timetable_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_timetable_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_timetable_ibfk_3` FOREIGN KEY (`subject_group_id`) REFERENCES `subject_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_timetable_ibfk_4` FOREIGN KEY (`subject_group_subject_id`) REFERENCES `subject_group_subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_timetable_ibfk_5` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_timetable_ibfk_6` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `submit_assignment`
--
ALTER TABLE `submit_assignment`
  ADD CONSTRAINT `submit_assignment_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submit_assignment_ibfk_2` FOREIGN KEY (`homework_id`) REFERENCES `homework` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `topic`
--
ALTER TABLE `topic`
  ADD CONSTRAINT `topic_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `topic_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lesson` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transport_feemaster`
--
ALTER TABLE `transport_feemaster`
  ADD CONSTRAINT `transport_feemaster_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `upload_contents`
--
ALTER TABLE `upload_contents`
  ADD CONSTRAINT `upload_contents_ibfk_1` FOREIGN KEY (`upload_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `upload_contents_ibfk_2` FOREIGN KEY (`content_type_id`) REFERENCES `content_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `userlog`
--
ALTER TABLE `userlog`
  ADD CONSTRAINT `userlog_ibfk_1` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_google_authenticate_codes`
--
ALTER TABLE `user_google_authenticate_codes`
  ADD CONSTRAINT `user_google_authenticate_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_google_authenticate_codes_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_routes`
--
ALTER TABLE `vehicle_routes`
  ADD CONSTRAINT `vehicle_routes_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `transport_route` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vehicle_routes_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `video_tutorial`
--
ALTER TABLE `video_tutorial`
  ADD CONSTRAINT `video_tutorial_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `video_tutorial_class_sections`
--
ALTER TABLE `video_tutorial_class_sections`
  ADD CONSTRAINT `video_tutorial_class_sections_ibfk_1` FOREIGN KEY (`class_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `video_tutorial_class_sections_ibfk_2` FOREIGN KEY (`video_tutorial_id`) REFERENCES `video_tutorial` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visitors_book`
--
ALTER TABLE `visitors_book`
  ADD CONSTRAINT `visitors_book_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visitors_book_ibfk_2` FOREIGN KEY (`student_session_id`) REFERENCES `student_session` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
