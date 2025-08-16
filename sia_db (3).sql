-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2025 at 07:55 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sia_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accountabilities`
--

CREATE TABLE `accountabilities` (
  `id` int(11) NOT NULL,
  `student_user_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','resolved','failed') DEFAULT 'pending',
  `category` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accountabilities`
--

INSERT INTO `accountabilities` (`id`, `student_user_id`, `description`, `amount`, `status`, `category`, `created_at`) VALUES
(1, 7, 'form137', 0.00, 'pending', 'document', '2025-08-14 11:59:13'),
(4, 7, 'form137', 0.00, 'resolved', 'document', '2025-08-15 08:15:53'),
(5, 7, 'sasa', 0.00, 'pending', 'document', '2025-08-15 08:17:19'),
(7, 7, '212', 0.00, 'failed', 'document', '2025-08-15 08:23:05');

-- --------------------------------------------------------

--
-- Table structure for table `admission_applications`
--

CREATE TABLE `admission_applications` (
  `id` int(11) NOT NULL,
  `desired_course` varchar(255) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `suffix` varchar(50) DEFAULT NULL,
  `complete_address` text DEFAULT NULL,
  `zip_code` char(4) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `landline` char(8) DEFAULT NULL,
  `mobile_no` char(11) NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `primary_school` varchar(255) DEFAULT NULL,
  `primary_year_graduated` year(4) DEFAULT NULL,
  `secondary_school` varchar(255) DEFAULT NULL,
  `secondary_year_graduated` year(4) DEFAULT NULL,
  `tertiary_school` varchar(255) DEFAULT NULL,
  `tertiary_year_graduated` year(4) DEFAULT NULL,
  `course_graduated` varchar(255) DEFAULT NULL,
  `educational_plan` varchar(255) DEFAULT NULL,
  `academic_achievement` varchar(255) DEFAULT NULL,
  `working` enum('Yes','No') DEFAULT NULL,
  `employer` varchar(255) DEFAULT NULL,
  `work_in_shifts` enum('Yes','No') DEFAULT NULL,
  `work_position` varchar(255) DEFAULT NULL,
  `family_connected_ncst` enum('Yes','No') DEFAULT NULL,
  `ncst_student` enum('Yes','No') DEFAULT NULL,
  `number_of_siblings` int(11) DEFAULT 0,
  `ncst_employee` enum('Yes','No') DEFAULT NULL,
  `relationship` varchar(100) DEFAULT NULL,
  `how_know_ncst` varchar(255) DEFAULT NULL,
  `transferee` enum('Yes','No') DEFAULT NULL,
  `als_graduate` enum('Yes','No') DEFAULT NULL,
  `returnee` enum('Yes','No') DEFAULT NULL,
  `dts_student` enum('Yes','No') DEFAULT NULL,
  `cross_enrollee` enum('Yes','No') DEFAULT NULL,
  `foreign_student` enum('Yes','No') DEFAULT NULL,
  `father_family_name` varchar(100) DEFAULT NULL,
  `father_given_name` varchar(100) DEFAULT NULL,
  `father_middle_name` varchar(100) DEFAULT NULL,
  `father_deceased` enum('Yes','No') DEFAULT NULL,
  `father_complete_address` text DEFAULT NULL,
  `father_landline` char(8) DEFAULT NULL,
  `father_mobile_no` char(11) DEFAULT NULL,
  `father_occupation` varchar(255) DEFAULT NULL,
  `mother_family_name` varchar(100) DEFAULT NULL,
  `mother_given_name` varchar(100) DEFAULT NULL,
  `mother_middle_name` varchar(100) DEFAULT NULL,
  `mother_deceased` enum('Yes','No') DEFAULT NULL,
  `mother_maiden_family_name` varchar(100) DEFAULT NULL,
  `mother_maiden_given_name` varchar(100) DEFAULT NULL,
  `mother_maiden_middle_name` varchar(100) DEFAULT NULL,
  `mother_complete_address` text DEFAULT NULL,
  `mother_landline` char(8) DEFAULT NULL,
  `mother_mobile_no` char(11) DEFAULT NULL,
  `mother_occupation` varchar(255) DEFAULT NULL,
  `guardian_family_name` varchar(100) DEFAULT NULL,
  `guardian_given_name` varchar(100) DEFAULT NULL,
  `guardian_middle_name` varchar(100) DEFAULT NULL,
  `guardian_complete_address` text DEFAULT NULL,
  `guardian_landline` char(8) DEFAULT NULL,
  `guardian_mobile_no` char(11) DEFAULT NULL,
  `guardian_occupation` varchar(255) DEFAULT NULL,
  `guardian_relationship` varchar(100) DEFAULT NULL,
  `status` enum('pending','under_review','approved','rejected') DEFAULT 'pending',
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admission_applications`
--

INSERT INTO `admission_applications` (`id`, `desired_course`, `last_name`, `first_name`, `middle_name`, `suffix`, `complete_address`, `zip_code`, `region`, `province`, `city`, `barangay`, `landline`, `mobile_no`, `gender`, `civil_status`, `nationality`, `date_of_birth`, `place_of_birth`, `email`, `religion`, `primary_school`, `primary_year_graduated`, `secondary_school`, `secondary_year_graduated`, `tertiary_school`, `tertiary_year_graduated`, `course_graduated`, `educational_plan`, `academic_achievement`, `working`, `employer`, `work_in_shifts`, `work_position`, `family_connected_ncst`, `ncst_student`, `number_of_siblings`, `ncst_employee`, `relationship`, `how_know_ncst`, `transferee`, `als_graduate`, `returnee`, `dts_student`, `cross_enrollee`, `foreign_student`, `father_family_name`, `father_given_name`, `father_middle_name`, `father_deceased`, `father_complete_address`, `father_landline`, `father_mobile_no`, `father_occupation`, `mother_family_name`, `mother_given_name`, `mother_middle_name`, `mother_deceased`, `mother_maiden_family_name`, `mother_maiden_given_name`, `mother_maiden_middle_name`, `mother_complete_address`, `mother_landline`, `mother_mobile_no`, `mother_occupation`, `guardian_family_name`, `guardian_given_name`, `guardian_middle_name`, `guardian_complete_address`, `guardian_landline`, `guardian_mobile_no`, `guardian_occupation`, `guardian_relationship`, `status`, `submitted_at`) VALUES
(53, 'BSIT', 'sasa', 'sasa', 'sa', 'sa', 'sasa', '1111', 'Region IV-A', 'Cavite', 'Imus', 'Anabu 1', '11111111', '11111111111', 'Male', 'Single', 'Filipino', '2025-08-16', 'sasasa', 'azivrix@gmail.com', 'Catholic', 'sasa', '0000', 'sasa', '2121', 'sa', '2121', 'sa', 'Partial Scholar', 'Honor Student', 'No', 'sasa', 'No', 'sa', '', '', 3, '', 'Parent', 'Friends', '', '', '', '', '', '', 'sa', 'sa', 'sas', 'No', 'sasa', '11111111', '11111111111', 'sas', 'sa', 'sa', 'sa', 'No', 'sa', 'sa', 'sa', 'sasa', '11111111', '11111111111', 'sasa', 'sasa', 'sasa', 'sas', 'asa', '11111111', '11111111111', '1sasa', 'sasa', '', '2025-08-16 10:28:38'),
(54, 'BSIT', 'sas', 'as', 'asa', 's', 'sasa', '1222', 'Region IV-A', 'Cavite', 'Imus', 'Anabu 1', '11111111', '11111111111', 'Male', 'Single', 'Filipino', '2025-08-07', 'sas', 'stevegramatica2@gmail.com', 'Christian', 'sas', '0000', 'asa', '2121', 'sas', '2121', 'asa', 'Full Scholar', 'Honor Student', 'No', 'sasa', 'No', 'sas', '', '', 3, '', 'Parent', 'Friends', '', '', '', '', '', '', 'sa', 'sa', 'sasa', 'No', 'sa', '11111111', '11111111111', 'sas', 'asa', 'sa', 'sasa', 'No', 'sa', 'sasasa', 'sasa', 'sasa', '11111111', '11111111111', 'sasa', 'sasa', 'sasa', 'sas', 'asas', '11111111', '11111111111', '1sasa', 'sasa', 'pending', '2025-08-16 14:36:55'),
(55, 'BSIT', 'sas', 'as', 'as', 'as', 'asa', '1111', 'Region IV-A', 'Laguna', 'Calamba', 'Parian', '11111111', '11111111111', 'Male', 'Single', 'Filipino', '2025-08-12', 'sasa', 'azivrix@gmail.com', 'Catholic', 'sas', '0000', 'as', '2121', 'asa', '2121', 'sasa', 'Full Scholar', 'Honor Student', 'No', 'asasa', 'No', 'sa', '', '', 3, '', 'Parent', 'Friends', '', '', '', '', '', '', 'asa', 'sas', 'sas', 'No', 'sasa', '11111111', '11111111111', '1sasasa', 'sas', 'as', 'asa', 'No', 'sa', 'sasasa', 'asa', 'sas', '11111111', '11111111111', 'sasa', 'sas', 'as', 'sa', 'sa', '11111111', '11111111111', 'sasa', 'sasasa', 'approved', '2025-08-16 15:05:45'),
(56, 'BSIT', 'sasa', 'sas', 'as', 'a', 'sasa', '1212', 'Region IV-A', 'Cavite', 'Imus', 'Anabu 1', '11111111', '11111111111', 'Male', 'Single', 'Filipino', '2025-03-16', 'sasa', 'azivrix@gmail.com', 'Catholic', 'sasa', '0000', 'sa', '2121', 'sas', '2121', 'asa', 'Full Scholar', 'Honor Student', 'No', 'sasa', 'No', 'sa', '', '', 2, '', 'Parent', 'Social Media', '', '', '', '', '', '', 'sas', 'as', 'sa', 'No', 'sasa', '11111111', '11111111111', 'sasa', 'sas', 'asa', 'sas', 'No', 'asa', 'sas', 'sa', 'sasa', '11111111', '11111111111', 'sasa', 'asa', 'sa', 'as', 'sasa', '11111111', '11111111111', '111sas', 'asa', '', '2025-08-16 15:12:38'),
(57, 'BSIT', 'sasa', 'asa', 'sasa', 'sa', 'sas', '1111', 'Region IV-A', 'Cavite', 'Imus', 'Anabu 1', '11111111', '11111111111', 'Male', 'Single', 'Filipino', '2025-08-22', 'sasas', 'stevegramatica2@gmail.com', 'Catholic', 'sa', '0000', 'sas', '2121', 'as', '0000', 'sa', 'Full Scholar', 'Honor Student', 'No', 'sas', 'No', 'sa', '', '', 2, '', 'Parent', 'Friends', '', '', '', '', '', '', 'sasas', 'asa', 'sa', 'No', 'sasa', '11111111', '11111111111', '11sasasasa', 'sasa', 'sa', 'sas', 'No', 'sasa', 'sasa', 'sa', 'sasa', '11111111', '11111111111', 'sasa', 'sasa', 'asa', 'sas', 'sasasa', '11111111', '11111111111', '1sasasa', 'sasa', 'approved', '2025-08-16 15:16:15'),
(58, 'BSIT', 'gramatica', 'sas', 'asa', 'sasa', 'sasa', '1212', 'Region IV-A', 'Cavite', 'Imus', 'Anabu 1', '11111111', '11111111111', 'Male', 'Widowed', 'Filipino', '2025-09-17', 'sasa', 'stevegramatica@gmail.com', 'Catholic', 'sas', '0000', 'asa', '0000', 'sas', '2121', 'a', 'Full Scholar', 'Honor Student', 'No', 'sasa', 'No', 'sa', '', '', 4, '', 'Sibling', 'Friends', '', '', '', '', '', '', 'sas', 'as', 'sasa', 'Yes', 'sasa', '11111111', '11111111111', '1sasa', 'sa', 'sa', 'sas', 'No', 'sa', 'sasa', 'sa', 'sasa', '11111111', '11111111111', 'sasa', 'sasa', 'sa', 'sa', 'sasa', '11111111', '11111111111', 'sasa', 'asa', '', '2025-08-16 15:22:51'),
(59, 'BSIT', 'gramatica', 'sasa', 'sasa', 'sa', 'sas', '1111', 'Region IV-A', 'Cavite', 'Imus', 'Anabu 1', '11111111', '11111111111', 'Male', 'Married', 'Filipino', '2025-08-08', 'sasa', 'azivrix@gmail.com', 'Catholic', 'sas', '0000', 'asa', '0000', 'sas', '0000', 'asa', 'Full Scholar', 'Honor Student', 'No', 'sasa', 'No', 'sas', '', '', 3, '', 'Parent', 'Friends', '', '', '', '', '', '', 'sas', 'as', 'asa', 'No', 'sasa', '11111111', '11111111111', 'sasa', 'sa', 'asa', 'sasa', 'No', 'sas', 'sasa', 'asa', 'sasa', '11111111', '11111111111', 'sasasa', 'sa', 'asa', 'sas', 'sa', '11111111', '11111111111', '1sasasa', 'sasasa', 'approved', '2025-08-16 15:25:58'),
(60, 'BSIT', 'gramatica', 'sasa', 'sa', 'sas', 'asa', '1111', 'Region IV-A', 'Cavite', 'Imus', 'Anabu 1', '11111111', '11111111111', 'Male', 'Single', 'Filipino', '2025-08-14', 'sasa', 'azivrix@gmail.com', 'Catholic', 'sas', '0000', 'as', '0000', 'asa', '0000', 'sasa', 'Full Scholar', 'Dean\'s Lister', 'No', 'sasa', 'No', 'sas', '', '', 2, '', 'Parent', 'Friends', '', '', '', '', '', '', 'sas', 'as', 'as', 'No', 'sasa', '11111111', '11111111111', 'sasa', 'sa', 'sa', 'sasa', 'No', 'sa', 'sas', 'sa', 'sasa', '11111111', '11111111111', 'sasa', 'sasa', 'sas', 'asa', 'sa', '11111111', '11111111111', 'sas', 'asasa', 'approved', '2025-08-16 15:33:57'),
(61, 'BSIT', 'gr', 'sas', 'as', 'asa', 'sasa', '1111', 'Region IV-A', 'Cavite', 'Imus', 'Anabu 1', '11111111', '11111111111', 'Male', 'Single', 'Filipino', '2025-07-28', 'sasasa', 'stevegramatica2@gmail.com', 'Catholic', 'sas', '0000', 'asa', '0000', 'sa', '2121', 'sasa', 'Full Scholar', 'Honor Student', 'No', 'sasa', 'No', 'sas', '', '', 4, '', 'Parent', 'Social Media', '', '', '', '', '', '', 'sasa', 'sas', 'asa', 'No', 'sasa', '11111111', '11111111111', '1sasa', 'sa', 'sas', 'asa', 'No', 'sas', 'sasa', 'asas', 'asasa', '11111111', '11111111111', '1sas', 'asa', 'sa', 'sasa', 'sas', '11111111', '11111111111', '111sas', 'asasa', 'approved', '2025-08-16 16:24:15'),
(62, 'BSIT', 'gramatica', 'asa', 'sa', 'as', 'asa', '1111', 'Region IV-A', 'Cavite', 'Bacoor', 'San Nicolas II', '11111111', '11111111111', 'Male', 'Single', 'Filipino', '2025-08-23', 'sasas', 'stevegramatica2@gmail.com', 'Catholic', 'sas', '0000', 'asa', '0000', 'sa', '2121', 'sa', 'Partial Scholar', 'Honor Student', 'No', 'sasa', 'No', 'sasa', '', '', 3, '', 'Parent', 'Friends', '', '', '', '', '', '', 'sa', 'asa', 'sas', 'No', 'sasa', '11111111', '11111111111', 'sasa', 'sas', 'sa', 'sa', 'No', 'sa', 'asas', 'sa', 'sasa', '11111111', '11111111111', 'sas', 'as', 'asa', 'sa', 'sasa', '11111111', '11111111111', 'sas', 'asa', 'approved', '2025-08-16 16:35:27'),
(63, 'BSIT', 'sas', 'as', 'as', 'as', 'asa', '1111', 'Region IV-A', 'Cavite', 'Imus', 'Anabu 1', '11111111', '11111111111', 'Male', 'Single', 'Filipino', '2025-08-22', 'sasa', 'stevegramatica2@gmail.com', 'Catholic', 'sa', '0000', 'sas', '2122', 'sas', '0000', 'a', 'Full Scholar', 'None', 'No', 'sasa', 'No', 'sa', '', '', 3, '', 'Parent', 'Friends', '', '', '', '', '', '', 'sas', 'sasa', 'asas', 'No', 'sasa', '11111111', '11111111111', 'sasa', 'sa', 'sasa', 'asa', 'No', 'sa', 'sasa', 'sasa', 'sas', '11111111', '11111111111', '1sas', 'asa', 'sa', 'sas', 'sas', '11111111', '11111111111', 'sas', 'asa', '', '2025-08-16 16:58:39'),
(64, 'BSIT', 'sasa', 'sa', 'sas', 'asa', 'sasa', '1111', 'Region IV-A', 'Cavite', 'Imus', 'Anabu 1', '11111111', '11111111111', 'Male', 'Single', 'Filipino', '2025-02-28', 'sas', 'stevegramatica2@gmail.com', 'Catholic', 'sas', '0000', 'as', '0000', 'asa', '0000', 'sa', 'Full Scholar', 'Honor Student', 'No', 'sas', 'No', 'asa', '', '', 3, '', 'Parent', 'Friends', '', '', '', '', '', '', 'sas', 'sas', 'asa', 'No', 'sasa', '11111111', '11111111111', '1sasa', 'sasa', 'sas', 'asa', 'No', 'sa', 'sasa', 'sas', 'asas', '11111111', '11111111111', 'sas', 'as', 'asa', 'sa', 'sa', '11111111', '11111111111', '11sa', 'sasa', '', '2025-08-16 17:26:06'),
(65, 'BSIT', 'sas', 'sas', 'as', 'as', 'asasa', '1111', 'Region IV-A', 'Cavite', 'Imus', 'Anabu 1', '11111111', '11111111111', 'Male', 'Single', 'Filipino', '2025-08-05', 'sasa', 'stevegramatica2@gmail.com', 'Catholic', 'sas', '2121', 'asa', '2121', 'sasa', '0000', 'sa', 'Full Scholar', 'Dean\'s Lister', 'No', 'sasa', 'No', 'sa', '', '', 2, '', 'Sibling', 'Social Media', '', '', '', '', '', '', 'sa', 'sa', 'sa', 'Yes', 'sasasa', '11111111', '11111111111', 'sasa', 'sssssss', 'sssas', 'asa', 'Yes', 'sasa', 'sasa', 'sas', 'asa', '11111111', '11111111111', '111sas', 'asa', 'sas', 'asa', 'sasa', '11111111', '11111111111', 'sasa', 'sasa', '', '2025-08-16 17:32:19');

-- --------------------------------------------------------

--
-- Table structure for table `admission_requirements`
--

CREATE TABLE `admission_requirements` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `requirement_name` varchar(255) NOT NULL,
  `status` enum('pending','resolved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admission_requirements`
--

INSERT INTO `admission_requirements` (`id`, `application_id`, `requirement_name`, `status`) VALUES
(11, 65, 'Good Moral Certificate', 'resolved'),
(12, 65, '2x2 Photo', 'resolved'),
(13, 53, 'Birth Certificate', 'resolved'),
(14, 53, 'Transcript of Records', 'resolved'),
(15, 53, 'Good Moral Certificate', 'resolved'),
(16, 53, '2x2 Photo', 'resolved'),
(17, 58, 'Birth Certificate', 'resolved'),
(18, 58, 'Transcript of Records', 'resolved'),
(19, 58, 'Good Moral Certificate', 'resolved'),
(20, 58, '2x2 Photo', 'resolved'),
(21, 56, 'Birth Certificate', 'resolved'),
(22, 56, 'Transcript of Records', 'resolved'),
(23, 56, 'Good Moral Certificate', 'resolved'),
(24, 56, '2x2 Photo', 'resolved'),
(25, 53, 'Birth Certificate', 'resolved'),
(26, 53, 'Transcript of Records', 'resolved'),
(27, 53, 'Good Moral Certificate', 'resolved'),
(28, 53, '2x2 Photo', 'resolved'),
(29, 53, 'Birth Certificate', 'pending'),
(30, 53, 'Transcript of Records', 'pending'),
(31, 53, 'Good Moral Certificate', 'pending'),
(32, 53, '2x2 Photo', 'pending'),
(33, 53, 'Birth Certificate', 'pending'),
(34, 53, 'Transcript of Records', 'pending'),
(35, 53, 'Good Moral Certificate', 'pending'),
(36, 53, '2x2 Photo', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `applicant_requirements`
--

CREATE TABLE `applicant_requirements` (
  `id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `birth_certificate` tinyint(1) DEFAULT 0,
  `tor` tinyint(1) DEFAULT 0,
  `picture` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicant_requirements`
--

INSERT INTO `applicant_requirements` (`id`, `applicant_id`, `birth_certificate`, `tor`, `picture`) VALUES
(1, 63, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `section` varchar(10) NOT NULL,
  `status` enum('pending','approved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `section`, `status`, `created_at`, `updated_at`) VALUES
(1, 7, 'A1', 'approved', '2025-08-14 14:44:38', '2025-08-14 14:47:07');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `semester` enum('1st','2nd','Summer') NOT NULL,
  `subject` varchar(100) NOT NULL,
  `prelims` decimal(5,2) NOT NULL,
  `midterms` decimal(5,2) NOT NULL,
  `prefinals` decimal(5,2) NOT NULL,
  `finals` decimal(5,2) NOT NULL,
  `final_grade` decimal(5,2) NOT NULL,
  `dept_head_id` int(11) DEFAULT NULL,
  `dept_head_approved` tinyint(1) DEFAULT 0,
  `dept_head_approved_at` datetime DEFAULT NULL,
  `registrar_id` int(11) DEFAULT NULL,
  `registrar_finalized` tinyint(1) DEFAULT 0,
  `registrar_finalized_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `units` int(11) DEFAULT 3,
  `code` varchar(100) DEFAULT NULL,
  `equivalent` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `teacher_id`, `semester`, `subject`, `prelims`, `midterms`, `prefinals`, `finals`, `final_grade`, `dept_head_id`, `dept_head_approved`, `dept_head_approved_at`, `registrar_id`, `registrar_finalized`, `registrar_finalized_at`, `created_at`, `updated_at`, `units`, `code`, `equivalent`) VALUES
(1, 7, 3, '1st', 'Science', 99.00, 99.00, 99.00, 99.00, 99.00, 6, 1, '2025-08-14 05:17:14', 4, 1, '2025-08-14 05:19:37', '2025-08-14 12:14:36', '2025-08-14 12:19:37', 3, NULL, NULL),
(2, 7, 3, '1st', 'Mathematics', 88.00, 98.00, 89.00, 89.00, 91.00, 6, 1, '2025-08-14 05:43:54', 4, 1, '2025-08-14 05:45:51', '2025-08-14 12:14:59', '2025-08-14 12:45:51', 3, NULL, NULL),
(3, 7, 3, '1st', 'Mathematics', 75.00, 75.00, 75.00, 75.00, 75.00, 6, 1, '2025-08-14 05:35:56', 4, 1, '2025-08-14 05:36:14', '2025-08-14 12:35:40', '2025-08-14 12:36:14', 3, NULL, NULL),
(4, 7, 3, '1st', 'Mathematics', 58.00, 58.00, 58.00, 58.00, 58.00, 6, 1, '2025-08-14 05:43:51', 4, 1, '2025-08-14 05:45:03', '2025-08-14 12:43:26', '2025-08-14 12:45:03', 3, NULL, NULL),
(5, 7, 3, '1st', 'Mathematics', 45.00, 100.00, 45.00, 54.00, 61.00, 6, 1, '2025-08-14 05:53:35', 4, 1, '2025-08-14 05:53:43', '2025-08-14 12:53:22', '2025-08-14 12:53:43', 2, 'Ge-001', '47');

-- --------------------------------------------------------

--
-- Table structure for table `student_subjects`
--

CREATE TABLE `student_subjects` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `year_level` varchar(20) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL,
  `units` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `prereq` varchar(255) DEFAULT NULL,
  `can_enroll` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_subjects`
--

INSERT INTO `student_subjects` (`id`, `student_id`, `year_level`, `semester`, `subject_code`, `description`, `units`, `status`, `prereq`, `can_enroll`, `created_at`) VALUES
(1, 7, '1st Year', '1st Semester', 'Ge-001', 'Math', 1, '0', 'IT-202', 1, '2025-08-14 06:11:25'),
(2, 26, '1st Year', '1st Semester', 'Ge-001', 'sasa', 3, '0', '', 1, '2025-08-16 08:18:42'),
(3, 39, '1st Year', '1st Semester', 'Ge-001', 'Math', 3, '0', 'IT-202', 0, '2025-08-16 08:38:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','teacher','treasury','registrar','department-head','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `student_id` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `course` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `student_id`, `email`, `course`) VALUES
(2, 'ad', '$2y$10$.W7qKgr0hBHhwydc1ifr0uqIN6GAgOvlT2LwwoR2nAavuQ5jzF9lq', 'admin', '2025-08-14 08:33:29', NULL, '', NULL),
(3, 'tc', '$2y$10$L9E.fYVyTPdSSJXMgUPTz.Zrc1SG4tl6V5DIAdwSlkQ06mAufRpP6', 'teacher', '2025-08-14 08:34:05', NULL, '', NULL),
(4, 'reg', '$2y$10$5mlg43fCqUm6jYzlhENjzODp3qHxCILV9HvVJRMbmXSk3chvHM.XW', 'registrar', '2025-08-14 08:34:18', NULL, '', NULL),
(5, 'tr', '$2y$10$aEmqD65ov6YU40bd66XBReaQbw2VJLE2VPF9Fku6gojt1UuqZgG4q', 'treasury', '2025-08-14 08:34:24', NULL, '', NULL),
(6, 'dep', '$2y$10$KilZk4EnAVcrCijoZNoOtecRw1feND0svX6VGKOmy8wwvWE/l0pEu', 'department-head', '2025-08-14 08:34:39', NULL, '', NULL),
(7, '2025-1', '$2y$10$3NPEtOt7VWcP/pdUqze/hOLawhSZjQD.P8j3geANGPLMTWeaYNf12', 'student', '2025-08-14 08:34:51', NULL, '', NULL),
(12, 'sia', '$2y$10$nvv0EsEwYPabmV6j7QQOJe6r16wVg0uKFwgK50Olx7q/4Y4jKOts2', 'teacher', '2025-08-15 07:31:20', NULL, '', NULL),
(13, 'azivrix@gmail.com', '$2y$10$yJDV2cG6j0e1dE0Y2NPADOVZPGzAXZozREVBWM2bIn3ZEkdWfrYfW', 'student', '2025-08-16 09:55:06', NULL, '', NULL),
(16, 'stevegramatica2@gmail.com', '$2y$10$ROo.NfjPB/HeSXyBn5rPqOlELj0Nt.wV8XHi.lWi85GA.TXIDTN6W', 'student', '2025-08-16 10:00:09', '20250001', '', NULL),
(26, '2025-6', '$2y$10$4.AsiTDM9lD2TDNbzom4FeUbP8rGSbH4WdGh10M.PfJVlfq5iyGOi', 'student', '2025-08-16 14:40:47', NULL, 'stevegramatica2@gmail.com', 'BSHM'),
(27, '2025-7', '$2y$10$lD9TopllGaL2vI.oRtYgc.i2kN4y0Bko9zZdakct9yCCBlZ9JQFOS', 'student', '2025-08-16 14:57:51', NULL, 'stevegramatica2@gmail.com', 'BSIT'),
(29, '2025-4', '$2y$10$Hr4UvAmQsjzd4SXN7Vmc5eK2NslMUbg9C4eyPRGcdKaH1ZRBiH8hO', 'student', '2025-08-16 15:06:44', NULL, 'azivrix@gmail.com', 'BSIT'),
(37, '2025-5', '$2y$10$CimDXVWPSoCI0VFEJWQxoumASX4EyU4sixCvyAX/aYnoR/YqRzZCe', 'student', '2025-08-16 15:26:04', NULL, 'stevegramatica@gmail.com', 'BSIT'),
(39, '2025-8', '$2y$10$iWcfIvjqAWdZRf59c3badeOrrwFEyp.Ix8/m2VXiMjJ4x.PCdsZ/.', 'student', '2025-08-16 15:37:31', NULL, 'azivrix@gmail.com', 'BSIT');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accountabilities`
--
ALTER TABLE `accountabilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_user_id` (`student_user_id`);

--
-- Indexes for table `admission_applications`
--
ALTER TABLE `admission_applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admission_requirements`
--
ALTER TABLE `admission_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `applicant_requirements`
--
ALTER TABLE `applicant_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `dept_head_id` (`dept_head_id`),
  ADD KEY `registrar_id` (`registrar_id`);

--
-- Indexes for table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accountabilities`
--
ALTER TABLE `accountabilities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `admission_applications`
--
ALTER TABLE `admission_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `admission_requirements`
--
ALTER TABLE `admission_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `applicant_requirements`
--
ALTER TABLE `applicant_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_subjects`
--
ALTER TABLE `student_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accountabilities`
--
ALTER TABLE `accountabilities`
  ADD CONSTRAINT `accountabilities_ibfk_1` FOREIGN KEY (`student_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `admission_requirements`
--
ALTER TABLE `admission_requirements`
  ADD CONSTRAINT `admission_requirements_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `admission_applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `applicant_requirements`
--
ALTER TABLE `applicant_requirements`
  ADD CONSTRAINT `applicant_requirements_ibfk_1` FOREIGN KEY (`applicant_id`) REFERENCES `admission_applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `grades_ibfk_3` FOREIGN KEY (`dept_head_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `grades_ibfk_4` FOREIGN KEY (`registrar_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD CONSTRAINT `student_subjects_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
