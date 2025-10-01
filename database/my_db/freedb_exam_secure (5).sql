-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: sql.freedb.tech
-- Generation Time: Sep 26, 2025 at 07:35 PM
-- Server version: 8.0.43-0ubuntu0.22.04.1
-- PHP Version: 8.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `freedb_exam_secure`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int NOT NULL,
  `unique_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `unique_id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `date_created`) VALUES
(1, '76c7490c-a0c3-48c1-9b45-75b73fbda25b', 'john', 'j', 'doe', 'john123@gmail.com', '$2y$10$4PAJ9kwqadUbGi9UVcVrqu/eAYIYDQPV6tynh0hqHoLaO1vheqcKm', '2025-05-14 02:17:29'),
(2, '080e91fd-80eb-4eb4-b188-4b67681d859c', 'Moises', 'R.', 'Urbano', 'moi@gmail.com', '$2y$10$SNCsjj.l4OYtl8vmD8JDWOzmIQKIb3T3mvsFzBuSDVMxSC9id5oGC', '2025-06-14 00:43:35'),
(3, 'c8bf0ab3-7d50-433f-9910-f5f933e1ef8f', 'Veronica', 'G', 'Zaldivar', 'veronica@gmail.comm', '$2y$10$IQt5N1EYxEOJfGzt9vf/EuUR9SVHzsoyo05TvyS3BBC2kxrlZyZjK', '2025-08-28 21:58:22'),
(4, '31e342e9-f05c-462d-b28a-30cea10fd247', 'Moi', 'Moi', 'Moi', 'm@gmail.com', '$2y$10$pWTGIr1E6xMqo3T5T42j2ey08M2YZyTU6722.SCwulcHtEaSvR30S', '2025-09-02 22:00:18'),
(5, '817f429c-6bba-45df-99c6-6d5b9695d648', 'Lukas', '', 'Urbs', 'lukas@gmail.com', '$2y$10$GTYWNTRlhBK7VyXnECD6Pex0tN4mcdgZJdoz2X7PhiOUiROs885SO', '2025-09-04 23:09:10'),
(6, '278d6153-bde4-41a9-bdb1-c3970b85df88', 'Robert', '', 'Tony', 'robert@gmail.com', '$2y$10$LHRRoKzGD/K9x0hRFY.QSOxteFm5s97jS7xGyS5mLOC834FutO4LW', '2025-09-07 16:58:41');

-- --------------------------------------------------------

--
-- Table structure for table `assessment_sessions`
--

CREATE TABLE `assessment_sessions` (
  `session_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assessment_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_code` char(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_section` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_activity_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` timestamp NULL DEFAULT NULL,
  `status` enum('ongoing','completed','abandoned','kicked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ongoing',
  `tab_switch_count` int NOT NULL DEFAULT '0',
  `face_left_count` int NOT NULL DEFAULT '0',
  `face_right_count` int NOT NULL DEFAULT '0',
  `suspicious_count` int NOT NULL DEFAULT '0',
  `screenshot_count` int NOT NULL DEFAULT '0',
  `cheating_flag` tinyint(1) NOT NULL DEFAULT '0',
  `cheating_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assessment_sessions`
--

INSERT INTO `assessment_sessions` (`session_id`, `assessment_id`, `student_id`, `access_code`, `student_name`, `year_section`, `started_at`, `last_activity_at`, `completed_at`, `status`, `tab_switch_count`, `face_left_count`, `face_right_count`, `suspicious_count`, `screenshot_count`, `cheating_flag`, `cheating_reason`) VALUES
('sess_1995735fc92_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - B', '2025-09-17 10:26:22', '2025-09-17 10:26:22', NULL, 'ongoing', 0, 0, 0, 0, 0, 0, NULL),
('sess_199574d7e5c_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - B', '2025-09-17 10:52:02', '2025-09-17 10:52:02', NULL, 'ongoing', 0, 0, 0, 0, 0, 0, NULL),
('sess_199575a2848_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - B', '2025-09-17 11:05:52', '2025-09-17 11:05:52', NULL, 'ongoing', 0, 0, 0, 0, 0, 0, NULL),
('sess_19957638359_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - B', '2025-09-17 11:16:06', '2025-09-17 11:16:06', NULL, 'ongoing', 0, 0, 0, 0, 0, 0, NULL),
('sess_199576e821c_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - B', '2025-09-17 11:28:06', '2025-09-17 11:28:06', NULL, 'ongoing', 0, 0, 0, 0, 0, 0, NULL),
('sess_199577aa54b_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - B', '2025-09-17 11:41:21', '2025-09-17 11:41:21', NULL, 'ongoing', 0, 0, 0, 0, 0, 0, NULL),
('sess_19957870ab4_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - C', '2025-09-17 11:54:54', '2025-09-17 11:54:54', NULL, 'ongoing', 0, 0, 0, 0, 0, 0, NULL),
('sess_199578bc2ed_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - C', '2025-09-17 12:00:03', '2025-09-17 12:00:03', NULL, 'ongoing', 0, 0, 0, 0, 0, 0, NULL),
('sess_1996cfbcc3d_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - C', '2025-09-21 15:54:27', '2025-09-21 15:54:27', NULL, 'ongoing', 0, 0, 0, 0, 0, 0, NULL),
('sess_1996d0ee231_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - C', '2025-09-21 16:15:18', '2025-09-21 16:15:57', '2025-09-21 16:15:57', 'completed', 0, 0, 0, 0, 0, 0, NULL),
('sess_1996d1473f4_d3c42a', '1038fa38-90ce-4d31-bcee-081241333f2a', '#vwiHmKWT', '5Q1WAW', 'MOISES URBANO', 'I BSIT - C', '2025-09-21 16:21:23', '2025-09-22 00:21:53', '2025-09-22 00:21:53', 'completed', 0, 0, 0, 0, 0, 0, NULL),
('sess_1996d17a3c0_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - A', '2025-09-22 00:24:49', '2025-09-22 00:25:15', '2025-09-22 00:25:15', 'completed', 0, 0, 0, 0, 0, 0, NULL),
('sess_1996d1eb919_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - A', '2025-09-22 00:32:34', '2025-09-22 00:32:34', NULL, 'ongoing', 0, 0, 0, 0, 0, 0, NULL),
('sess_1996d38cafb_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - C', '2025-09-22 01:01:02', '2025-09-22 01:01:02', NULL, 'ongoing', 0, 0, 0, 0, 0, 0, NULL),
('sess_1996d460faf_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - C', '2025-09-22 01:15:32', '2025-09-22 02:08:27', NULL, 'ongoing', 5, 0, 0, 0, 0, 0, NULL),
('sess_1997abaad14_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - C', '2025-09-24 15:57:58', '2025-09-24 16:02:00', '2025-09-24 16:02:00', 'completed', 6, 0, 0, 0, 2, 0, NULL),
('sess_1997ac0f3b2_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - C', '2025-09-24 16:04:49', '2025-09-24 16:07:22', NULL, 'ongoing', 4, 0, 0, 0, 1, 0, NULL),
('sess_1997acc11fa_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - C', '2025-09-24 16:16:58', '2025-09-24 16:25:48', NULL, 'ongoing', 9, 0, 0, 0, 1, 0, NULL),
('sess_1997ad63ccb_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - C', '2025-09-24 16:28:04', '2025-09-24 16:33:50', NULL, 'ongoing', 6, 0, 0, 0, 3, 0, NULL),
('sess_1997adfb599_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - B', '2025-09-24 16:38:25', '2025-09-24 16:49:32', NULL, 'ongoing', 5, 0, 0, 0, 5, 0, NULL),
('sess_199819f4d5b_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - B', '2025-09-26 00:05:24', '2025-09-26 00:06:49', NULL, 'ongoing', 1, 0, 0, 0, 0, 0, NULL),
('sess_19981a25337_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - B', '2025-09-26 00:08:43', '2025-09-26 00:10:02', NULL, 'ongoing', 4, 0, 0, 0, 0, 0, NULL),
('sess_19981a41255_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - B', '2025-09-26 00:10:37', '2025-09-26 00:11:25', NULL, 'ongoing', 3, 0, 0, 0, 0, 0, NULL),
('sess_19981b12d46_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - B', '2025-09-26 00:24:56', '2025-09-26 00:37:59', NULL, 'ongoing', 2, 0, 0, 0, 0, 0, NULL),
('sess_19986678018_d3c42a', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', '#vwiHmKWT', 'DEPRHK', 'MOISES URBANO', 'IV BSIT - B', '2025-09-26 22:22:34', '2025-09-26 23:06:07', NULL, 'ongoing', 3, 0, 0, 0, 3, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cheating_events`
--

CREATE TABLE `cheating_events` (
  `id` bigint NOT NULL,
  `session_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` enum('TAB_SWITCH','APP_BACKGROUNDED','FACE_LEFT','FACE_RIGHT','SUSPICIOUS','SCREENSHOT','CUSTOM') COLLATE utf8mb4_unicode_ci NOT NULL,
  `severity` enum('LOW','MEDIUM','HIGH') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'LOW',
  `event_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `meta` json DEFAULT NULL,
  `screenshot_url` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail_url` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checksum` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cheating_events`
--

INSERT INTO `cheating_events` (`id`, `session_id`, `event_type`, `severity`, `event_time`, `meta`, `screenshot_url`, `thumbnail_url`, `checksum`) VALUES
(1, 'sess_1996d460faf_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-22 01:56:50', '{\"timestamp\": \"2025-09-22T01:56:50.320072\", \"access_code\": \"unknown\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '1034290447'),
(2, 'sess_1996d460faf_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-22 02:02:03', '{\"timestamp\": \"2025-09-22T02:02:02.260613\", \"access_code\": \"unknown\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '51438346'),
(3, 'sess_1996d460faf_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-22 02:02:06', '{\"timestamp\": \"2025-09-22T02:02:06.313208\", \"access_code\": \"unknown\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '377752056'),
(4, 'sess_1996d460faf_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-22 02:07:13', '{\"timestamp\": \"2025-09-22T02:07:12.701532\", \"access_code\": \"unknown\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '361587627'),
(5, 'sess_1996d460faf_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-22 02:08:26', '{\"timestamp\": \"2025-09-22T02:08:26.277775\", \"access_code\": \"unknown\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '772722686'),
(6, 'sess_1997abaad14_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 15:58:57', '{\"timestamp\": \"2025-09-24T15:58:56.604347\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_1997abaad14_d3c42a%2Ftab_switch%2Fscreenshot_1758700732052.jpg?alt=media&token=8e450c72-947e-402d-89fe-b7dd0fd705ed', NULL, '161221717'),
(7, 'sess_1997abaad14_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 15:59:03', '{\"timestamp\": \"2025-09-24T15:59:02.572332\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_1997abaad14_d3c42a%2Ftab_switch%2Fscreenshot_1758700739125.jpg?alt=media&token=b2d4545d-1bf6-4f38-a72c-81fe50761277', NULL, '372362257'),
(8, 'sess_1997abaad14_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 15:59:17', '{\"timestamp\": \"2025-09-24T15:59:16.669843\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '572326995'),
(9, 'sess_1997abaad14_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:00:15', '{\"timestamp\": \"2025-09-24T16:00:15.326690\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '929813849'),
(10, 'sess_1997abaad14_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:00:27', '{\"timestamp\": \"2025-09-24T16:00:27.354070\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '55766095'),
(11, 'sess_1997abaad14_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:00:49', '{\"timestamp\": \"2025-09-24T16:00:48.988222\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '730352681'),
(12, 'sess_1997ac0f3b2_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:05:19', '{\"timestamp\": \"2025-09-24T16:05:18.760655\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '895763715'),
(13, 'sess_1997ac0f3b2_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:05:31', '{\"timestamp\": \"2025-09-24T16:05:30.755307\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '218291044'),
(14, 'sess_1997ac0f3b2_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:06:32', '{\"timestamp\": \"2025-09-24T16:06:31.555368\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '982084601'),
(15, 'sess_1997ac0f3b2_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:07:21', '{\"timestamp\": \"2025-09-24T16:07:20.973087\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_1997ac0f3b2_d3c42a%2Ftab_switch%2Fscreenshot_1758701235352.jpg?alt=media&token=26a5a6b4-15d1-4dc3-b4a2-3b49dbe8a0e0', NULL, '834726991'),
(16, 'sess_1997acc11fa_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:17:19', '{\"timestamp\": \"2025-09-24T16:17:18.974365\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_1997acc11fa_d3c42a%2Ftab_switch%2Fscreenshot_1758701834686.jpg?alt=media&token=3b435104-0152-46ca-a46c-9fc8518c6d8b', NULL, '567868819'),
(17, 'sess_1997acc11fa_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:18:12', '{\"timestamp\": \"2025-09-24T16:18:11.410744\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '855722929'),
(18, 'sess_1997acc11fa_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:18:20', '{\"timestamp\": \"2025-09-24T16:18:19.606716\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '570137674'),
(19, 'sess_1997acc11fa_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:18:31', '{\"timestamp\": \"2025-09-24T16:18:31.108938\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '836681071'),
(20, 'sess_1997acc11fa_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:20:08', '{\"timestamp\": \"2025-09-24T16:20:07.348175\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '1047261139'),
(21, 'sess_1997acc11fa_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:22:44', '{\"timestamp\": \"2025-09-24T16:22:44.285029\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '806606863'),
(22, 'sess_1997acc11fa_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:25:24', '{\"timestamp\": \"2025-09-24T16:25:23.911151\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '544960084'),
(23, 'sess_1997acc11fa_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:25:30', '{\"timestamp\": \"2025-09-24T16:25:30.283736\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '285298471'),
(24, 'sess_1997acc11fa_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:25:47', '{\"timestamp\": \"2025-09-24T16:25:46.680739\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '784371212'),
(25, 'sess_1997ad63ccb_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:28:24', '{\"timestamp\": \"2025-09-24T16:28:24.306576\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_1997ad63ccb_d3c42a%2Ftab_switch%2Fscreenshot_1758702499570.jpg?alt=media&token=cfab884d-6b06-4d18-ab7d-13606d5e6840', NULL, '848315319'),
(26, 'sess_1997ad63ccb_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:29:18', '{\"timestamp\": \"2025-09-24T16:29:17.513714\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '606151374'),
(27, 'sess_1997ad63ccb_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:29:29', '{\"timestamp\": \"2025-09-24T16:29:29.226331\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '417348507'),
(28, 'sess_1997ad63ccb_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:29:49', '{\"timestamp\": \"2025-09-24T16:29:49.012561\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_1997ad63ccb_d3c42a%2Ftab_switch%2Fscreenshot_1758702584920.jpg?alt=media&token=9c7e0ff0-84fd-4f68-9d6d-451e8d161f2a', NULL, '330675095'),
(29, 'sess_1997ad63ccb_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:31:10', '{\"timestamp\": \"2025-09-24T16:31:09.453691\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_1997ad63ccb_d3c42a%2Ftab_switch%2Fscreenshot_1758702663802.jpg?alt=media&token=6cefe465-7955-4575-b041-0216d5df3193', NULL, '223645507'),
(30, 'sess_1997ad63ccb_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:33:49', '{\"timestamp\": \"2025-09-24T16:33:49.110090\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '737270758'),
(31, 'sess_1997adfb599_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:38:42', '{\"timestamp\": \"2025-09-24T16:38:41.492295\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_1997adfb599_d3c42a%2Ftab_switch%2Fscreenshot_1758703117713.jpg?alt=media&token=ef352e7e-798b-4763-a9f1-ea9cb17bd89e', NULL, '533977766'),
(32, 'sess_1997adfb599_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:39:29', '{\"timestamp\": \"2025-09-24T16:39:28.611025\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_1997adfb599_d3c42a%2Ftab_switch%2Fscreenshot_1758703164522.jpg?alt=media&token=b8395ab9-5b1f-4cf8-b019-40f66823b607', NULL, '858067277'),
(33, 'sess_1997adfb599_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:40:40', '{\"timestamp\": \"2025-09-24T16:40:40.292622\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_1997adfb599_d3c42a%2Ftab_switch%2Fscreenshot_1758703237209.jpg?alt=media&token=260ec285-3d6a-454c-b794-94d9d7849ac0', NULL, '219921367'),
(34, 'sess_1997adfb599_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:46:18', '{\"timestamp\": \"2025-09-24T16:46:17.498614\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_1997adfb599_d3c42a%2Ftab_switch%2Fscreenshot_1758703573627.jpg?alt=media&token=5574a149-1cb2-492e-b264-52788c582d3c', NULL, '738252490'),
(35, 'sess_1997adfb599_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-24 16:49:32', '{\"timestamp\": \"2025-09-24T16:49:31.452365\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_1997adfb599_d3c42a%2Ftab_switch%2Fscreenshot_1758703767991.jpg?alt=media&token=a336b927-52fa-40ad-a451-2cdeeb48cde1', NULL, '529983862'),
(36, 'sess_199819f4d5b_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-26 00:06:48', '{\"timestamp\": \"2025-09-26T00:06:47.555523\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '1061766381'),
(37, 'sess_19981a25337_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-26 00:09:15', '{\"timestamp\": \"2025-09-26T00:09:14.800318\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '473624682'),
(38, 'sess_19981a25337_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-26 00:09:45', '{\"timestamp\": \"2025-09-26T00:09:44.713392\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '378855274'),
(39, 'sess_19981a25337_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-26 00:09:54', '{\"timestamp\": \"2025-09-26T00:09:53.484830\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '21280310'),
(40, 'sess_19981a25337_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-26 00:10:02', '{\"timestamp\": \"2025-09-26T00:10:01.556923\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '1044816604'),
(41, 'sess_19981a41255_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-26 00:11:14', '{\"timestamp\": \"2025-09-26T00:11:14.063475\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '304020463'),
(42, 'sess_19981a41255_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-26 00:11:18', '{\"timestamp\": \"2025-09-26T00:11:17.636719\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '608510016'),
(43, 'sess_19981a41255_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-26 00:11:25', '{\"timestamp\": \"2025-09-26T00:11:24.447697\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '952815941'),
(44, 'sess_19981b12d46_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-26 00:37:49', '{\"timestamp\": \"2025-09-26T00:37:49.061819\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '89572127'),
(45, 'sess_19981b12d46_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-26 00:37:58', '{\"timestamp\": \"2025-09-26T00:37:58.297810\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', NULL, NULL, '17708223'),
(46, 'sess_19986678018_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-26 22:23:46', '{\"timestamp\": \"2025-09-26T22:23:46.320705\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_19986678018_d3c42a%2Ftab_switch%2Fscreenshot_1758896619304.jpg?alt=media&token=01ec380e-21f7-44d6-954e-bed5cccd1232', NULL, '229953033'),
(47, 'sess_19986678018_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-26 22:26:33', '{\"timestamp\": \"2025-09-26T22:26:32.911503\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_19986678018_d3c42a%2Ftab_switch%2Fscreenshot_1758896787847.jpg?alt=media&token=9a551a79-76a6-48cc-adeb-d90d63008a25', NULL, '1028721734'),
(48, 'sess_19986678018_d3c42a', 'TAB_SWITCH', 'MEDIUM', '2025-09-26 23:06:06', '{\"timestamp\": \"2025-09-26T23:06:05.144180\", \"access_code\": \"DEPRHK\", \"student_name\": \"MOISES URBANO\", \"assessment_id\": \"712cb7fa-8726-4b39-8fd5-84b93dc1769d\"}', 'https://firebasestorage.googleapis.com/v0/b/exam-c29b9.firebasestorage.app/o/cheating_events%2F712cb7fa-8726-4b39-8fd5-84b93dc1769d%2Fsess_19986678018_d3c42a%2Ftab_switch%2Fscreenshot_1758899160800.jpg?alt=media&token=e2a2d970-d12f-40f3-b5c1-dec41611a017', NULL, '971142072');

-- --------------------------------------------------------

--
-- Table structure for table `created_assessments`
--

CREATE TABLE `created_assessments` (
  `unique_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_code` char(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year_course` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sections` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timer` int NOT NULL DEFAULT '0',
  `status` enum('active','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `school_year` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `schedule` datetime NOT NULL,
  `closing_time` datetime NOT NULL,
  `shuffle_mcq` tinyint(1) NOT NULL DEFAULT '0',
  `shuffle_identification` tinyint(1) NOT NULL DEFAULT '0',
  `shuffle_true_false` tinyint(1) NOT NULL DEFAULT '0',
  `ai_check_identification` tinyint(1) NOT NULL DEFAULT '0',
  `owner_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `created_assessments`
--

INSERT INTO `created_assessments` (`unique_id`, `access_code`, `title`, `year_course`, `sections`, `course_code`, `timer`, `status`, `school_year`, `schedule`, `closing_time`, `shuffle_mcq`, `shuffle_identification`, `shuffle_true_false`, `ai_check_identification`, `owner_id`, `created_at`, `updated_at`) VALUES
('1038fa38-90ce-4d31-bcee-081241333f2a', '5Q1WAW', 'Quiz # 2', 'I BSIT', 'C,B', 'LTOM 101', 30, 'active', '2025-2026', '2025-09-13 13:00:00', '2025-10-02 15:00:00', 1, 1, 1, 1, '278d6153-bde4-41a9-bdb1-c3970b85df88', '2025-09-14 05:42:00', '2025-09-14 05:42:00'),
('114c4cd0-09d4-4270-bfac-b6be68cbd10a', '145647', 'Quiz #1', 'III BIT', 'A.D', 'CC101', 30, 'active', '2024-2025', '2025-05-14 05:00:00', '2025-05-16 01:00:00', 1, 1, 1, 1, '76c7490c-a0c3-48c1-9b45-75b73fbda25b', '2025-05-13 23:45:16', '2025-05-13 23:45:16'),
('2e622f72-44d7-40ef-85d3-391e51b70d63', 'BDOBXX', 'Quiz nilukas', 'I CS', 'B,D', 'CC101', 90, 'active', '2025-2026', '2025-09-06 08:00:00', '2025-09-10 11:00:00', 1, 1, 0, 1, '817f429c-6bba-45df-99c6-6d5b9695d648', '2025-09-04 15:11:34', '2025-09-07 16:37:24'),
('47c7cfba-9a94-46bf-8bbe-77a161ae0b0b', '967413', 'Midterm Exam', 'III BSIT', 'B,D', 'ELEC1', 60, 'active', '2024-2025', '2025-05-14 11:00:00', '2025-05-14 03:00:00', 1, 1, 1, 1, '76c7490c-a0c3-48c1-9b45-75b73fbda25b', '2025-05-13 23:53:14', '2025-05-13 23:53:14'),
('4cd1f700-2a19-4e36-9cdd-51ed8ef7e289', '478178', 'wetwwewh', 'wehwehwehw', 'hwhew', 'ehhweh', 60, 'active', 'wehwh', '2025-05-14 11:00:00', '2025-05-30 10:00:00', 1, 1, 1, 1, '76c7490c-a0c3-48c1-9b45-75b73fbda25b', '2025-05-13 23:57:38', '2025-05-13 23:57:38'),
('67e767b3-5fa8-47df-bb95-8b2e388cc7a8', '135664', 'test', 'test', 'terst', 'test', 60, 'active', 'testt', '2025-05-14 10:00:00', '2025-05-22 09:00:00', 1, 1, 1, 1, '76c7490c-a0c3-48c1-9b45-75b73fbda25b', '2025-05-13 23:33:14', '2025-05-13 23:33:14'),
('70ad3b9a-977c-4806-b78a-ed545abd73a4', '767713', '123', '123', '123', '123', 60, 'active', '123', '2025-05-14 08:00:00', '2025-05-16 12:00:00', 1, 1, 1, 1, '76c7490c-a0c3-48c1-9b45-75b73fbda25b', '2025-05-13 23:38:59', '2025-05-13 23:38:59'),
('712cb7fa-8726-4b39-8fd5-84b93dc1769d', 'DEPRHK', 'Quiz #1', 'IV BSIT', 'A,B,C', 'SP101', 0, 'active', '2025-2026', '2025-09-07 08:00:00', '2025-09-30 11:00:00', 1, 1, 1, 1, '278d6153-bde4-41a9-bdb1-c3970b85df88', '2025-09-07 17:06:05', '2025-09-24 07:57:48'),
('77f89ca1-04c7-4e4f-86ae-b7ac6ee2be40', 'ZP8XC8', 'Summary Test', 'III HUMMS', 'A,D', '101HUMSS', 0, 'closed', '2024-2025', '2025-09-02 03:00:00', '2025-09-02 05:00:00', 1, 0, 1, 0, 'c8bf0ab3-7d50-433f-9910-f5f933e1ef8f', '2025-09-02 15:15:31', '2025-09-02 15:15:31'),
('96a4b71a-a8d9-42cb-a6c6-80cafe04f3a3', '394055', 'uytityity', 'iitiityi', 'tiytityiti', 'tityi', 60, 'active', 'tyitiiyti', '2025-05-14 09:00:00', '2025-05-22 02:00:00', 1, 1, 1, 1, '76c7490c-a0c3-48c1-9b45-75b73fbda25b', '2025-05-13 23:55:17', '2025-05-13 23:55:17'),
('a48d57f9-da54-48d7-a7c2-97aebd96fb2b', 'EVO5RM', 'Test1', 'test', 'test', 'test', 60, 'closed', '2025-2027', '2025-09-01 08:00:00', '2025-09-04 10:00:00', 1, 1, 1, 1, 'c8bf0ab3-7d50-433f-9910-f5f933e1ef8f', '2025-09-01 16:49:18', '2025-09-02 14:09:13'),
('c2428ad3-5b4f-4aa5-8582-e3162fecc3c5', 'JTZ03D', 'Quiz #1', 'II HM', 'A,B', 'LS 101', 0, 'active', '2025-2026', '2025-09-01 08:00:00', '2025-09-02 10:00:00', 0, 1, 0, 1, 'c8bf0ab3-7d50-433f-9910-f5f933e1ef8f', '2025-09-01 15:25:29', '2025-09-02 15:36:39'),
('c7edf064-2449-49a1-8760-5e2af2c531ba', 'ZFTY9X', 'Test #2', 'II HM', 'A. B', 'SAD101', 0, 'active', '2025-2026', '2025-09-12 08:00:00', '2025-09-23 15:00:00', 1, 1, 1, 1, '278d6153-bde4-41a9-bdb1-c3970b85df88', '2025-09-12 16:41:13', '2025-09-14 05:00:55'),
('d241df9a-7144-463e-8869-336f12e43592', '563388', 'wetwwewh', 'wehwehwehw', 'hwhew', 'ehhweh', 0, 'active', 'wehwh', '2025-05-14 10:00:00', '2025-05-30 02:00:00', 1, 1, 1, 1, '76c7490c-a0c3-48c1-9b45-75b73fbda25b', '2025-05-16 19:13:08', '2025-05-16 19:13:08'),
('fe6c8eea-15a1-4e61-a556-e818a8195231', 'RSEBKF', 'Test ni babi', 'IV BSIT', 'C,D', 'SP101', 0, 'active', '2025-2026', '2025-09-12 11:00:00', '2025-09-15 14:00:00', 1, 1, 1, 1, '278d6153-bde4-41a9-bdb1-c3970b85df88', '2025-09-14 14:52:14', '2025-09-14 14:54:30');

-- --------------------------------------------------------

--
-- Table structure for table `identification_questions`
--

CREATE TABLE `identification_questions` (
  `id` int NOT NULL,
  `question_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assessment_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `correct_answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `identification_questions`
--

INSERT INTO `identification_questions` (`id`, `question_id`, `assessment_id`, `question_text`, `correct_answer`, `points`, `created_at`) VALUES
(2, '9c21a528-bed9-4a58-b37d-d358ac9d1aa0', 'd241df9a-7144-463e-8869-336f12e43592', '90[9[9[7', '[79[79[', 5, '2025-05-16 19:13:09'),
(6, '079a29f6-460a-4338-8fe7-d6c1dd3138eb', 'c2428ad3-5b4f-4aa5-8582-e3162fecc3c5', 'What is the latest version of android?', '16', 5, '2025-09-01 15:25:29'),
(7, '08a883364468dd4a85c4b5ff97aa74db', 'a48d57f9-da54-48d7-a7c2-97aebd96fb2b', 'This is an identification question', 'this is the answer', 2, '2025-09-02 13:25:56'),
(8, '922c8e74db195474b8d955715f9755a2', 'a48d57f9-da54-48d7-a7c2-97aebd96fb2b', 'This is the second identification question', 'This is the second answer', 4, '2025-09-02 13:32:47'),
(9, 'b36652e1ecd8295e633a54bc4c5fca7f', 'a48d57f9-da54-48d7-a7c2-97aebd96fb2b', 'This is the third question', 'this is the third', 5, '2025-09-02 13:55:39'),
(10, '23090cbe-3f38-47e0-ad10-f596ec55deb3', '77f89ca1-04c7-4e4f-86ae-b7ac6ee2be40', 'wHAT THE BEST COOKIE?', 'Cookie run', 1, '2025-09-02 15:15:32'),
(11, '5de24d27-f3ad-4dc5-8093-9a84244b01d2', '77f89ca1-04c7-4e4f-86ae-b7ac6ee2be40', 'The best chocolate?', 'Feastables', 4, '2025-09-02 15:15:32'),
(13, '361c7a5a-8eb4-48b8-9210-147e72edbe3a', '2e622f72-44d7-40ef-85d3-391e51b70d63', 'Lukas test question identification', 'Lukas test question identification', 1, '2025-09-04 15:11:34'),
(14, '10d804ef-fc12-44f1-8fda-db76b5f15c16', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', 'Who is your instructor in capstone?', 'sir robert', 5, '2025-09-07 17:06:06'),
(15, '32621040-86ff-49d2-bd4f-13aa7f826e87', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', 'Who is our national hero?', 'Jose Rizal', 1, '2025-09-07 17:06:06'),
(16, '957cffdd-adf8-4e99-99aa-32dbfd915f7e', 'c7edf064-2449-49a1-8760-5e2af2c531ba', 'wHAT IS THIS', 'TESTEST', 1, '2025-09-12 16:41:14'),
(17, 'baf4586d-3333-4f6a-b592-881e78093a71', '1038fa38-90ce-4d31-bcee-081241333f2a', 'testtest', 'testtest', 1, '2025-09-14 05:42:01');

-- --------------------------------------------------------

--
-- Table structure for table `multiple_choice_questions`
--

CREATE TABLE `multiple_choice_questions` (
  `id` int NOT NULL,
  `question_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assessment_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_a` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_b` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_c` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_d` text COLLATE utf8mb4_unicode_ci,
  `option_e` text COLLATE utf8mb4_unicode_ci,
  `correct_answer` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `multiple_choice_questions`
--

INSERT INTO `multiple_choice_questions` (`id`, `question_id`, `assessment_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `option_e`, `correct_answer`, `points`, `created_at`) VALUES
(3, 'f04a660c-057a-4cc8-8587-9f6a190ba306', '67e767b3-5fa8-47df-bb95-8b2e388cc7a8', 'tetuu', 'ttuu', 'u', 'y', NULL, NULL, 'A', 1, '2025-05-13 23:33:14'),
(4, '7bbcdb14-11a8-40fc-8533-3dc9971def3d', '70ad3b9a-977c-4806-b78a-ed545abd73a4', '123', '1231', '231', '23', NULL, NULL, 'B', 1, '2025-05-13 23:38:59'),
(5, '30fd01d0-fe92-43a9-8456-10a69ae3f3b4', '114c4cd0-09d4-4270-bfac-b6be68cbd10a', 'What is 1+1', '1', '4', '2', NULL, NULL, 'C', 1, '2025-05-13 23:45:16'),
(6, 'a8f15500-390c-4ce0-a143-2e003262b41c', '47c7cfba-9a94-46bf-8bbe-77a161ae0b0b', 'Which of the following is used to define a route with a parameter?', 'Route::get(\'user\', \'UserController@show\');', 'Route::get(\'user/{id}\', \'UserController@show\');', 'Route::get(\'user:id\', \'UserController@show\');', NULL, NULL, 'B', 1, '2025-05-13 23:53:14'),
(7, '5abad110-268c-43ac-9b3c-8aed39b58483', '96a4b71a-a8d9-42cb-a6c6-80cafe04f3a3', 'ititi', 'tyity', 'itityi', 'tyi', NULL, NULL, 'A', 1, '2025-05-13 23:55:17'),
(8, '092bd056-2396-4145-9c8b-13b2829021c9', '4cd1f700-2a19-4e36-9cdd-51ed8ef7e289', 'wehwh', 'whe', 'hewhw', 'whweh', NULL, NULL, 'A', 1, '2025-05-13 23:57:38'),
(9, '50216251-b025-4d20-8a7a-8a1deaade023', 'd241df9a-7144-463e-8869-336f12e43592', '90p[90[', '0[90', '[9[', '9[90[[', NULL, NULL, 'B', 1, '2025-05-16 19:13:08'),
(16, '985a9157-0d13-4736-9d74-51d226cf34ff', 'c2428ad3-5b4f-4aa5-8582-e3162fecc3c5', 'What is the largest planet?s', 'Pluto', 'Jupiter', 'Mars', 'eartg', 'staurn', 'B', 3, '2025-09-01 15:25:29'),
(17, 'e2023d09-9f62-4c27-9c7b-1fd0071ff128', 'c2428ad3-5b4f-4aa5-8582-e3162fecc3c5', 'What is the fastest animal?', 'whale', 'cheetah', 'fly', 'penguin', '', 'B', 4, '2025-09-01 15:25:29'),
(18, 'f64ca335-25c0-4ec0-84a6-b397f52eb4ce', 'a48d57f9-da54-48d7-a7c2-97aebd96fb2b', 'test', 'test', 'tes', 'test', '', '', 'B', 1, '2025-09-01 16:49:18'),
(19, 'b92843ac-d26e-4aba-84c8-a51f15c82716', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', 'What is the largest planet?', 'Earth', 'Jupiter', 'Mars', 'Pluto', '', 'B', 3, '2025-09-07 17:06:05'),
(20, '8e6679f8-9ec3-473e-97db-6ee584fcefca', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', 'Who discovered gravity?', 'Newton', 'Einstein', 'Aristotle', '', '', 'A', 2, '2025-09-07 17:06:05'),
(21, 'f75bf065-b1ac-435d-8d1a-f6b98fca345a', 'c7edf064-2449-49a1-8760-5e2af2c531ba', 'TEST', 'TETES', 'TES', '35R3', NULL, NULL, 'A', 1, '2025-09-12 16:41:14'),
(22, 'da126874-e327-458b-b848-39b8a70fbc79', '1038fa38-90ce-4d31-bcee-081241333f2a', 'this is mcq', 'a', 'a', 'a', NULL, NULL, 'A', 1, '2025-09-14 05:42:00'),
(23, '5ac46bd6-6b88-4bcb-bc0e-f20d24844726', 'fe6c8eea-15a1-4e61-a556-e818a8195231', 'TEST NI BABI', 'TEST 1', 'TEST 2', 'TEST 2', NULL, NULL, 'A', 1, '2025-09-14 14:52:14'),
(24, 'c8a8b91f-ab51-470f-8d8c-942257a758ee', 'fe6c8eea-15a1-4e61-a556-e818a8195231', 'MOI MOI', 'MOI MOI 4', 'MOI MOI 1', 'MOI MOI 2', NULL, NULL, 'B', 4, '2025-09-14 14:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `student_responses`
--

CREATE TABLE `student_responses` (
  `id` int NOT NULL,
  `response_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assessment_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_type` enum('multiple_choice','identification','true_false') COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `correct_answer` text COLLATE utf8mb4_unicode_ci,
  `question_text` text COLLATE utf8mb4_unicode_ci,
  `is_correct` tinyint(1) NOT NULL,
  `points_earned` int NOT NULL DEFAULT '0',
  `question_points` int DEFAULT '1',
  `time_spent_seconds` int DEFAULT '0',
  `attempt_number` int DEFAULT '1',
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_responses`
--

INSERT INTO `student_responses` (`id`, `response_id`, `assessment_id`, `session_id`, `student_id`, `question_id`, `question_type`, `student_answer`, `correct_answer`, `question_text`, `is_correct`, `points_earned`, `question_points`, `time_spent_seconds`, `attempt_number`, `submitted_at`) VALUES
(19, 'resp_1757842590257_488', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#0KWGqWGi', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'B', 'A', 'Who discovered gravity?', 0, 0, 2, 6, 1, '2025-09-14 09:36:31'),
(20, 'resp_1757842590715_448', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#0KWGqWGi', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'D', 'B', 'What is the largest planet?', 0, 0, 3, 8, 1, '2025-09-14 09:36:32'),
(21, 'resp_1757842591243_132', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#0KWGqWGi', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'B', 'True', 'Are whales the largest animal on our planet?', 0, 0, 1, 8, 1, '2025-09-14 09:36:32'),
(22, 'resp_1757842591782_602', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#0KWGqWGi', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'B', 'True', 'Is our body made up of water?', 0, 0, 4, 9, 1, '2025-09-14 09:36:33'),
(23, 'resp_1757842592251_82', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#0KWGqWGi', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'test edin', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 36, 1, '2025-09-14 09:36:33'),
(24, 'resp_1757842592800_372', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#0KWGqWGi', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'test', 'Jose Rizal', 'Who is our national hero?', 0, 0, 1, 38, 1, '2025-09-14 09:36:34'),
(25, 'resp_1757842870071_518', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'B', 'B', 'What is the largest planet?', 1, 3, 3, 10, 1, '2025-09-14 09:41:11'),
(26, 'resp_1757842870577_157', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'A', 'A', 'Who discovered gravity?', 1, 2, 2, 12, 1, '2025-09-14 09:41:12'),
(27, 'resp_1757842871013_269', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'True', 'Is our body made up of water?', 1, 4, 4, 13, 1, '2025-09-14 09:41:12'),
(28, 'resp_1757842871529_698', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'True', 'Are whales the largest animal on our planet?', 1, 1, 1, 14, 1, '2025-09-14 09:41:13'),
(29, 'resp_1757842872031_915', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'sir', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 17, 1, '2025-09-14 09:41:13'),
(30, 'resp_1757842872517_164', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'jose', 'Jose Rizal', 'Who is our national hero?', 0, 0, 1, 20, 1, '2025-09-14 09:41:14'),
(31, 'resp_1757843131459_490', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'B', 'B', 'What is the largest planet?', 1, 3, 3, 2, 1, '2025-09-14 09:45:33'),
(32, 'resp_1757843131952_954', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'A', 'A', 'Who discovered gravity?', 1, 2, 2, 3, 1, '2025-09-14 09:45:33'),
(33, 'resp_1757843132376_132', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 5, 1, '2025-09-14 09:45:33'),
(34, 'resp_1757843132812_888', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 5, 1, '2025-09-14 09:45:34'),
(35, 'resp_1757843133319_17', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'sir robert', 'Jose Rizal', 'Who is our national hero?', 0, 0, 1, 9, 1, '2025-09-14 09:45:34'),
(36, 'resp_1757843133858_559', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'sir robert', 'sir robert', 'Who is your instructor in capstone?', 1, 5, 5, 15, 1, '2025-09-14 09:45:35'),
(37, 'resp_1757843470159_170', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'B', 'B', 'What is the largest planet?', 1, 3, 3, 1, 1, '2025-09-14 09:51:11'),
(38, 'resp_1757843470632_718', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'A', 'A', 'Who discovered gravity?', 1, 2, 2, 3, 1, '2025-09-14 09:51:12'),
(39, 'resp_1757843471151_435', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 4, 1, '2025-09-14 09:51:12'),
(40, 'resp_1757843471675_107', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 4, 1, '2025-09-14 09:51:13'),
(41, 'resp_1757843472110_719', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'jose rizal', 'Jose Rizal', 'Who is our national hero?', 1, 1, 1, 14, 1, '2025-09-14 09:51:13'),
(42, 'resp_1757843472641_91', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'sir robert', 'sir robert', 'Who is your instructor in capstone?', 1, 5, 5, 23, 1, '2025-09-14 09:51:14'),
(43, 'resp_1757843674406_538', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'C', 'A', 'Who discovered gravity?', 0, 0, 2, 0, 1, '2025-09-14 09:54:35'),
(44, 'resp_1757843674825_829', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'C', 'B', 'What is the largest planet?', 0, 0, 3, 1, 1, '2025-09-14 09:54:36'),
(45, 'resp_1757843675316_770', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'B', 'A', 'Is our body made up of water?', 0, 0, 4, 3, 1, '2025-09-14 09:54:36'),
(46, 'resp_1757843675745_840', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 3, 1, '2025-09-14 09:54:37'),
(47, 'resp_1757844138102_344', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'B', 'B', 'What is the largest planet?', 1, 3, 3, 4, 1, '2025-09-14 10:02:19'),
(48, 'resp_1757844138655_99', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'A', 'A', 'Who discovered gravity?', 1, 2, 2, 1, 1, '2025-09-14 10:02:20'),
(49, 'resp_1757844139157_353', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 5, 1, '2025-09-14 10:02:20'),
(50, 'resp_1757844139631_579', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 6, 1, '2025-09-14 10:02:21'),
(51, 'resp_1757844140136_362', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'djshs', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 8, 1, '2025-09-14 10:02:21'),
(52, 'resp_1757844140670_620', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'jsjsjs', 'Jose Rizal', 'Who is our national hero?', 0, 0, 1, 11, 1, '2025-09-14 10:02:22'),
(65, '19947d48-021f-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'A', 'A', 'Who discovered gravity?', 1, 2, 2, 2, 1, '2025-09-14 10:45:37'),
(66, '19947d48-021f-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'B', 'B', 'What is the largest planet?', 1, 3, 3, 3, 1, '2025-09-14 10:45:37'),
(67, '19947d48-021f-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 5, 1, '2025-09-14 10:45:38'),
(68, '19947d48-021f-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 6, 1, '2025-09-14 10:45:38'),
(69, '19947d48-021f-d3c4_q5', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'sir robert', 'sir robert', 'Who is your instructor in capstone?', 1, 5, 5, 10, 1, '2025-09-14 10:45:39'),
(70, '19947d48-021f-d3c4_q6', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'jose rizal', 'Jose Rizal', 'Who is our national hero?', 1, 1, 1, 13, 1, '2025-09-14 10:45:39'),
(71, '19947e60-03e3-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'A', 'A', 'Who discovered gravity?', 1, 2, 2, 1, 1, '2025-09-14 11:04:41'),
(72, '19947e60-03e3-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'B', 'B', 'What is the largest planet?', 1, 3, 3, 2, 1, '2025-09-14 11:04:42'),
(73, '19947e60-03e3-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'B', 'A', 'Is our body made up of water?', 0, 0, 4, 3, 1, '2025-09-14 11:04:42'),
(74, '19947e60-03e3-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 4, 1, '2025-09-14 11:04:43'),
(75, '19947e74-0218-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'B', 'B', 'What is the largest planet?', 1, 3, 3, 2, 1, '2025-09-14 11:06:06'),
(76, '19947e74-0218-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'A', 'A', 'Who discovered gravity?', 1, 2, 2, 3, 1, '2025-09-14 11:06:07'),
(77, '19947e74-0218-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 4, 1, '2025-09-14 11:06:07'),
(78, '19947e74-0218-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 4, 1, '2025-09-14 11:06:08'),
(79, '19947e8b-0185-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'B', 'B', 'What is the largest planet?', 1, 3, 3, 3, 1, '2025-09-14 11:07:41'),
(80, '19947e8b-0185-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'B', 'A', 'Who discovered gravity?', 0, 0, 2, 4, 1, '2025-09-14 11:07:42'),
(81, '19947e8b-0185-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 5, 1, '2025-09-14 11:07:42'),
(82, '19947e8b-0185-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'B', 'A', 'Is our body made up of water?', 0, 0, 4, 5, 1, '2025-09-14 11:07:42'),
(83, '19947e98-00df-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'C', 'A', 'Who discovered gravity?', 0, 0, 2, 1, 1, '2025-09-14 11:08:34'),
(84, '19947e98-00df-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'A', 'B', 'What is the largest planet?', 0, 0, 3, 1, 1, '2025-09-14 11:08:35'),
(85, '19947e98-00df-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 3, 1, '2025-09-14 11:08:36'),
(86, '19947e98-00df-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 3, 1, '2025-09-14 11:08:36'),
(87, '19947e98-00df-d3c4_q5', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'dkdkd', 'Jose Rizal', 'Who is our national hero?', 0, 0, 1, 6, 1, '2025-09-14 11:08:37'),
(88, '19947e98-00df-d3c4_q6', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'ejeiie', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 7, 1, '2025-09-14 11:08:37'),
(89, '1994824f-004c-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'A', 'A', 'Who discovered gravity?', 1, 2, 2, 8, 1, '2025-09-14 12:13:29'),
(90, '1994824f-004c-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'A', 'B', 'What is the largest planet?', 0, 0, 3, 9, 1, '2025-09-14 12:13:29'),
(91, '1994824f-004c-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'B', 'A', 'Are whales the largest animal on our planet?', 0, 0, 1, 39, 1, '2025-09-14 12:13:30'),
(92, '1994824f-004c-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 33, 1, '2025-09-14 12:13:30'),
(93, '1994824f-004c-d3c4_q5', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'dd', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 25, 1, '2025-09-14 12:13:31'),
(94, '1994824f-004c-d3c4_q6', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'jsjs', 'Jose Rizal', 'Who is our national hero?', 0, 0, 1, 38, 1, '2025-09-14 12:13:31'),
(95, '19948343-0167-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'D', 'B', 'What is the largest planet?', 0, 0, 3, 2, 1, '2025-09-14 12:30:06'),
(96, '19948343-0167-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'A', 'A', 'Who discovered gravity?', 1, 2, 2, 2, 1, '2025-09-14 12:30:07'),
(97, '19948343-0167-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 4, 1, '2025-09-14 12:30:07'),
(98, '19948343-0167-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 4, 1, '2025-09-14 12:30:08'),
(99, '19948343-0167-d3c4_q5', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'sir robs', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 11, 1, '2025-09-14 12:30:08'),
(100, '19948343-0167-d3c4_q6', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'jose rizal', 'Jose Rizal', 'Who is our national hero?', 1, 1, 1, 17, 1, '2025-09-14 12:30:09'),
(101, '19948356-02fe-d3c4_q1', 'c7edf064-2449-49a1-8760-5e2af2c531ba', NULL, '#vwiHmKWT', 'f75bf065-b1ac-435d-8d1a-f6b98fca345a', 'multiple_choice', 'A', 'A', 'TEST', 1, 1, 1, 3, 1, '2025-09-14 12:31:24'),
(102, '19948356-02fe-d3c4_q2', 'c7edf064-2449-49a1-8760-5e2af2c531ba', NULL, '#vwiHmKWT', 'fedd8de5-d91e-42ba-bd2e-6cb239257f93', 'true_false', 'A', 'A', 'IS THAT THE READ', 1, 1, 1, 4, 1, '2025-09-14 12:31:24'),
(103, '19948356-02fe-d3c4_q3', 'c7edf064-2449-49a1-8760-5e2af2c531ba', NULL, '#vwiHmKWT', '957cffdd-adf8-4e99-99aa-32dbfd915f7e', 'identification', 'ttt', 'TESTEST', 'wHAT IS THIS', 0, 0, 1, 9, 1, '2025-09-14 12:31:25'),
(104, '199483e1-02a6-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'A', 'B', 'What is the largest planet?', 0, 0, 3, 1, 1, '2025-09-14 12:40:54'),
(105, '199483e1-02a6-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'B', 'A', 'Who discovered gravity?', 0, 0, 2, 2, 1, '2025-09-14 12:40:54'),
(106, '199483e1-02a6-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 5, 1, '2025-09-14 12:40:54'),
(107, '199483e1-02a6-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 4, 1, '2025-09-14 12:40:55'),
(108, '199483e1-02a6-d3c4_q5', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'jose rizal', 'Jose Rizal', 'Who is our national hero?', 1, 1, 1, 11, 1, '2025-09-14 12:40:56'),
(109, '199483e1-02a6-d3c4_q6', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'sir robert ', 'sir robert', 'Who is your instructor in capstone?', 1, 5, 5, 8, 1, '2025-09-14 12:40:56'),
(110, '19948bdf-028b-d3c4_q1', 'fe6c8eea-15a1-4e61-a556-e818a8195231', NULL, '#vwiHmKWT', 'c8a8b91f-ab51-470f-8d8c-942257a758ee', 'multiple_choice', 'B', 'B', 'MOI MOI', 1, 4, 4, 36, 1, '2025-09-14 15:00:36'),
(111, '19948bdf-028b-d3c4_q2', 'fe6c8eea-15a1-4e61-a556-e818a8195231', NULL, '#vwiHmKWT', '5ac46bd6-6b88-4bcb-bc0e-f20d24844726', 'multiple_choice', 'C', 'A', 'TEST NI BABI', 0, 0, 1, 31, 1, '2025-09-14 15:00:36'),
(112, '199501a0-0126-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'A', 'A', 'Who discovered gravity?', 1, 2, 2, 10, 1, '2025-09-16 01:18:31'),
(113, '199501a0-0126-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'A', 'B', 'What is the largest planet?', 0, 0, 3, 12, 1, '2025-09-16 01:18:33'),
(114, '199501a0-0126-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 13, 1, '2025-09-16 01:18:34'),
(115, '199501a0-0126-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 13, 1, '2025-09-16 01:18:35'),
(116, '199501a0-0126-d3c4_q5', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'fgf', 'Jose Rizal', 'Who is our national hero?', 0, 0, 1, 41, 1, '2025-09-16 01:18:36'),
(117, '199501a0-0126-d3c4_q6', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'hhh', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 42, 1, '2025-09-16 01:18:37'),
(118, '199509b4-032b-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'A', 'B', 'What is the largest planet?', 0, 0, 3, 185, 1, '2025-09-16 03:39:41'),
(119, '199509b4-032b-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'C', 'A', 'Who discovered gravity?', 0, 0, 2, 184, 1, '2025-09-16 03:39:41'),
(120, '199509b4-032b-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'hddhhs', 'Jose Rizal', 'Who is our national hero?', 0, 0, 1, 183, 1, '2025-09-16 03:39:42'),
(121, '199509b4-032b-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'jud', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 181, 1, '2025-09-16 03:39:43'),
(122, '1995763e-03b3-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'mock-mc-1', 'multiple_choice', 'B', 'C', 'What is the correct syntax of Text widget in Flutter?', 0, 0, 1, 18, 1, '2025-09-17 11:16:31'),
(123, '1995763e-03b3-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'mock-id-1', 'identification', 'rf', 'Dependencies and project configuration', 'What does the pubspec.yaml file manage?', 0, 0, 2, 16, 1, '2025-09-17 11:16:32'),
(124, '1995763e-03b3-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'mock-tf-1', 'true_false', 'A', 'A', 'Flutter is developed by Google.', 1, 1, 1, 17, 1, '2025-09-17 11:16:32'),
(125, '19957818-0249-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'B', 'A', 'Who discovered gravity?', 0, 0, 2, 434, 1, '2025-09-17 11:48:53'),
(126, '19957818-0249-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'B', 'B', 'What is the largest planet?', 1, 3, 3, 435, 1, '2025-09-17 11:48:54'),
(127, '19957818-0249-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 436, 1, '2025-09-17 11:48:54'),
(128, '19957818-0249-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 436, 1, '2025-09-17 11:48:55'),
(129, '19957818-0249-d3c4_q5', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'se', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 441, 1, '2025-09-17 11:48:55'),
(130, '19957818-0249-d3c4_q6', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'ff', 'Jose Rizal', 'Who is our national hero?', 0, 0, 1, 439, 1, '2025-09-17 11:48:56'),
(131, '1996cfc2-02cd-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'D', 'B', 'What is the largest planet?', 0, 0, 3, 3, 1, '2025-09-21 15:54:52'),
(132, '1996cfc2-02cd-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'B', 'A', 'Who discovered gravity?', 0, 0, 2, 2, 1, '2025-09-21 15:54:53'),
(133, '1996cfc2-02cd-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 5, 1, '2025-09-21 15:54:53'),
(134, '1996cfc2-02cd-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 6, 1, '2025-09-21 15:54:54'),
(135, '1996cfc2-02cd-d3c4_q5', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'dfffffff', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 12, 1, '2025-09-21 15:54:54'),
(136, '1996cfc2-02cd-d3c4_q6', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'dddddddd', 'Jose Rizal', 'Who is our national hero?', 0, 0, 1, 16, 1, '2025-09-21 15:54:55'),
(137, '1996d0f7-0394-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'B', 'A', 'Who discovered gravity?', 0, 0, 2, 7, 1, '2025-09-21 16:15:56'),
(138, '1996d0f7-0394-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'A', 'B', 'What is the largest planet?', 0, 0, 3, 8, 1, '2025-09-21 16:15:56'),
(139, '1996d0f7-0394-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'B', 'A', 'Is our body made up of water?', 0, 0, 4, 10, 1, '2025-09-21 16:15:57'),
(140, '1996d0f7-0394-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'B', 'A', 'Are whales the largest animal on our planet?', 0, 0, 1, 10, 1, '2025-09-21 16:15:57'),
(141, '1996d0f7-0394-d3c4_q5', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'jose rizal', 'Jose Rizal', 'Who is our national hero?', 1, 1, 1, 19, 1, '2025-09-21 16:15:58'),
(142, '1996d0f7-0394-d3c4_q6', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'sir robs', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 28, 1, '2025-09-21 16:15:58'),
(143, '1996d14e-00a9-d3c4_q1', '1038fa38-90ce-4d31-bcee-081241333f2a', NULL, '#vwiHmKWT', 'da126874-e327-458b-b848-39b8a70fbc79', 'multiple_choice', 'A', 'A', 'this is mcq', 1, 1, 1, 1, 1, '2025-09-21 16:21:54'),
(144, '1996d14e-00a9-d3c4_q2', '1038fa38-90ce-4d31-bcee-081241333f2a', NULL, '#vwiHmKWT', '19e51725-8724-4864-99e1-80eb8723d6f5', 'true_false', 'A', 'A', 'erherhehr', 1, 1, 1, 2, 1, '2025-09-21 16:21:54'),
(145, '1996d14e-00a9-d3c4_q3', '1038fa38-90ce-4d31-bcee-081241333f2a', NULL, '#vwiHmKWT', 'baf4586d-3333-4f6a-b592-881e78093a71', 'identification', 'quiz 2', 'testtest', 'testtest', 0, 0, 1, 7, 1, '2025-09-21 16:21:55'),
(146, '1996d17f-0331-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'B', 'A', 'Who discovered gravity?', 0, 0, 2, 1, 1, '2025-09-21 16:25:15'),
(147, '1996d17f-0331-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'A', 'B', 'What is the largest planet?', 0, 0, 3, 2, 1, '2025-09-21 16:25:15'),
(148, '1996d17f-0331-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'B', 'A', 'Is our body made up of water?', 0, 0, 4, 6, 1, '2025-09-21 16:25:15'),
(149, '1996d17f-0331-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'B', 'A', 'Are whales the largest animal on our planet?', 0, 0, 1, 5, 1, '2025-09-21 16:25:16'),
(150, '1996d17f-0331-d3c4_q5', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'uu', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 9, 1, '2025-09-21 16:25:16'),
(151, '1996d17f-0331-d3c4_q6', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'uu', 'Jose Rizal', 'Who is our national hero?', 0, 0, 1, 12, 1, '2025-09-21 16:25:17'),
(152, '1997abe5-0083-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'D', 'B', 'What is the largest planet?', 0, 0, 3, 4, 1, '2025-09-24 08:01:59'),
(153, '1997abe5-0083-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'A', 'A', 'Who discovered gravity?', 1, 2, 2, 4, 1, '2025-09-24 08:02:00'),
(154, '1997abe5-0083-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'B', 'A', 'Are whales the largest animal on our planet?', 0, 0, 1, 184, 1, '2025-09-24 08:02:00'),
(155, '1997abe5-0083-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 183, 1, '2025-09-24 08:02:01'),
(156, '1997abe5-0083-d3c4_q5', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'ecve', 'Jose Rizal', 'Who is our national hero?', 0, 0, 1, 188, 1, '2025-09-24 08:02:01'),
(157, '1997abe5-0083-d3c4_q6', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'eveb', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 190, 1, '2025-09-24 08:02:01'),
(158, '19981a39-00a3-d3c4_q1', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '8e6679f8-9ec3-473e-97db-6ee584fcefca', 'multiple_choice', 'B', 'A', 'Who discovered gravity?', 0, 0, 2, 72, 1, '2025-09-25 16:10:08'),
(159, '19981a39-00a3-d3c4_q2', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'b92843ac-d26e-4aba-84c8-a51f15c82716', 'multiple_choice', 'C', 'B', 'What is the largest planet?', 0, 0, 3, 72, 1, '2025-09-25 16:10:08'),
(160, '19981a39-00a3-d3c4_q3', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', 'a260db62-fc6e-4039-81ea-4cb2440f2775', 'true_false', 'A', 'A', 'Is our body made up of water?', 1, 4, 4, 71, 1, '2025-09-25 16:10:09'),
(161, '19981a39-00a3-d3c4_q4', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', 'true_false', 'A', 'A', 'Are whales the largest animal on our planet?', 1, 1, 1, 71, 1, '2025-09-25 16:10:09'),
(162, '19981a39-00a3-d3c4_q5', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '32621040-86ff-49d2-bd4f-13aa7f826e87', 'identification', 'ruruuer', 'Jose Rizal', 'Who is our national hero?', 0, 0, 1, 76, 1, '2025-09-25 16:10:10'),
(163, '19981a39-00a3-d3c4_q6', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', NULL, '#vwiHmKWT', '10d804ef-fc12-44f1-8fda-db76b5f15c16', 'identification', 'uweuue', 'sir robert', 'Who is your instructor in capstone?', 0, 0, 5, 74, 1, '2025-09-25 16:10:10');

-- --------------------------------------------------------

--
-- Table structure for table `true_false_questions`
--

CREATE TABLE `true_false_questions` (
  `id` int NOT NULL,
  `question_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assessment_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `correct_answer` tinyint(1) NOT NULL,
  `points` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `true_false_questions`
--

INSERT INTO `true_false_questions` (`id`, `question_id`, `assessment_id`, `question_text`, `correct_answer`, `points`, `created_at`) VALUES
(4, '0687bc38-9c21-4958-b421-c18653f34671', '67e767b3-5fa8-47df-bb95-8b2e388cc7a8', 'yyry', 1, 1, '2025-05-13 23:33:14'),
(5, 'a1b623bd-76cb-4300-8194-81c769f4c171', '67e767b3-5fa8-47df-bb95-8b2e388cc7a8', 'i787', 0, 1, '2025-05-13 23:33:14'),
(8, 'bec3b0ee-60ac-4855-a16e-39e95a8803d5', 'c2428ad3-5b4f-4aa5-8582-e3162fecc3c5', 'Dragons are real', 0, 3, '2025-09-01 15:25:30'),
(13, '20346149-b941-444b-bc44-84f2f3b43eee', '2e622f72-44d7-40ef-85d3-391e51b70d63', 'This is lukas t or f', 1, 3, '2025-09-04 15:11:34'),
(14, 'a260db62-fc6e-4039-81ea-4cb2440f2775', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', 'Is our body made up of water?', 1, 4, '2025-09-07 17:06:07'),
(15, '7d007b5c-f047-4a34-a9fa-25a39f4dc9d7', '712cb7fa-8726-4b39-8fd5-84b93dc1769d', 'Are whales the largest animal on our planet?', 1, 1, '2025-09-07 17:06:07'),
(16, 'fedd8de5-d91e-42ba-bd2e-6cb239257f93', 'c7edf064-2449-49a1-8760-5e2af2c531ba', 'IS THAT THE READ', 1, 1, '2025-09-12 16:41:14'),
(17, '19e51725-8724-4864-99e1-80eb8723d6f5', '1038fa38-90ce-4d31-bcee-081241333f2a', 'erherhehr', 1, 1, '2025-09-14 05:42:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `assessment_sessions`
--
ALTER TABLE `assessment_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_assessment_status` (`assessment_id`,`status`),
  ADD KEY `idx_assessment_student` (`assessment_id`,`student_id`),
  ADD KEY `idx_last_activity` (`last_activity_at`);

--
-- Indexes for table `cheating_events`
--
ALTER TABLE `cheating_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session_time` (`session_id`,`event_time`),
  ADD KEY `idx_type_time` (`event_type`,`event_time`);

--
-- Indexes for table `created_assessments`
--
ALTER TABLE `created_assessments`
  ADD PRIMARY KEY (`unique_id`),
  ADD UNIQUE KEY `access_code` (`access_code`),
  ADD KEY `idx_access_code` (`access_code`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_schedule` (`schedule`),
  ADD KEY `idx_owner` (`owner_id`),
  ADD KEY `idx_course_code` (`course_code`),
  ADD KEY `idx_school_year` (`school_year`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_created_assessments_status_time` (`status`,`schedule`,`closing_time`);

--
-- Indexes for table `identification_questions`
--
ALTER TABLE `identification_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `question_id` (`question_id`),
  ADD KEY `idx_assessment` (`assessment_id`);

--
-- Indexes for table `multiple_choice_questions`
--
ALTER TABLE `multiple_choice_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `question_id` (`question_id`),
  ADD KEY `idx_assessment` (`assessment_id`);

--
-- Indexes for table `student_responses`
--
ALTER TABLE `student_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `idx_assessment_student` (`assessment_id`,`student_id`),
  ADD KEY `idx_question` (`question_id`),
  ADD KEY `idx_session` (`session_id`);

--
-- Indexes for table `true_false_questions`
--
ALTER TABLE `true_false_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `question_id` (`question_id`),
  ADD KEY `idx_assessment` (`assessment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cheating_events`
--
ALTER TABLE `cheating_events`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `identification_questions`
--
ALTER TABLE `identification_questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `multiple_choice_questions`
--
ALTER TABLE `multiple_choice_questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_responses`
--
ALTER TABLE `student_responses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `true_false_questions`
--
ALTER TABLE `true_false_questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessment_sessions`
--
ALTER TABLE `assessment_sessions`
  ADD CONSTRAINT `fk_sessions_assessment` FOREIGN KEY (`assessment_id`) REFERENCES `created_assessments` (`unique_id`) ON DELETE CASCADE;

--
-- Constraints for table `cheating_events`
--
ALTER TABLE `cheating_events`
  ADD CONSTRAINT `fk_events_session` FOREIGN KEY (`session_id`) REFERENCES `assessment_sessions` (`session_id`) ON DELETE CASCADE;

--
-- Constraints for table `identification_questions`
--
ALTER TABLE `identification_questions`
  ADD CONSTRAINT `identification_questions_ibfk_1` FOREIGN KEY (`assessment_id`) REFERENCES `created_assessments` (`unique_id`) ON DELETE CASCADE;

--
-- Constraints for table `multiple_choice_questions`
--
ALTER TABLE `multiple_choice_questions`
  ADD CONSTRAINT `multiple_choice_questions_ibfk_1` FOREIGN KEY (`assessment_id`) REFERENCES `created_assessments` (`unique_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_responses`
--
ALTER TABLE `student_responses`
  ADD CONSTRAINT `student_responses_ibfk_1` FOREIGN KEY (`assessment_id`) REFERENCES `created_assessments` (`unique_id`) ON DELETE CASCADE;

--
-- Constraints for table `true_false_questions`
--
ALTER TABLE `true_false_questions`
  ADD CONSTRAINT `true_false_questions_ibfk_1` FOREIGN KEY (`assessment_id`) REFERENCES `created_assessments` (`unique_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
