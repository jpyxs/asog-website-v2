-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 20, 2026 at 03:05 AM
-- Server version: 11.8.8-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `asogtbi`
--
CREATE DATABASE IF NOT EXISTS `asogtbi` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `asogtbi`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `fullName` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `googleEmail` varchar(255) DEFAULT NULL,
  `googleSub` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'admin',
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `lastLoginAt` datetime DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL,
  `resetToken` varchar(255) DEFAULT NULL,
  `resetTokenExpiresAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_notifications`
--

CREATE TABLE `admin_notifications` (
  `id` int(11) UNSIGNED NOT NULL,
  `type` varchar(60) NOT NULL,
  `title` varchar(160) NOT NULL,
  `body` text DEFAULT NULL,
  `link` varchar(500) DEFAULT NULL,
  `sourceType` varchar(80) DEFAULT NULL,
  `sourceId` int(11) UNSIGNED DEFAULT NULL,
  `targetRole` varchar(30) DEFAULT NULL,
  `targetAdminId` int(11) UNSIGNED DEFAULT NULL,
  `actorAdminId` int(11) UNSIGNED DEFAULT NULL,
  `priority` varchar(20) NOT NULL DEFAULT 'normal',
  `isRead` tinyint(1) NOT NULL DEFAULT 0,
  `readAt` datetime DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_notification_reads`
--

CREATE TABLE `admin_notification_reads` (
  `id` int(11) UNSIGNED NOT NULL,
  `notificationId` int(11) UNSIGNED NOT NULL,
  `adminId` int(11) UNSIGNED NOT NULL,
  `readAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cohorts`
--

CREATE TABLE `cohorts` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `number` int(10) UNSIGNED NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `createdAt` datetime DEFAULT current_timestamp(),
  `updatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `isRead` tinyint(1) NOT NULL DEFAULT 0,
  `isArchived` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) UNSIGNED NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `sortOrder` int(11) NOT NULL DEFAULT 0,
  `isPublished` tinyint(1) NOT NULL DEFAULT 1,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_players`
--

CREATE TABLE `game_players` (
  `id` int(10) UNSIGNED NOT NULL,
  `fullName` varchar(150) NOT NULL,
  `firstName` varchar(60) DEFAULT NULL,
  `middleName` varchar(60) DEFAULT NULL,
  `lastName` varchar(60) DEFAULT NULL,
  `school` varchar(190) DEFAULT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `lastLoginAt` datetime DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_wordle_plays`
--

CREATE TABLE `game_wordle_plays` (
  `id` int(10) UNSIGNED NOT NULL,
  `playerId` int(10) UNSIGNED NOT NULL,
  `playDate` date NOT NULL,
  `answerWord` varchar(5) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'in_progress',
  `attemptsUsed` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `elapsedMs` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `score` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `startedAt` datetime DEFAULT NULL,
  `finishedAt` datetime DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incubatees`
--

CREATE TABLE `incubatees` (
  `id` int(10) UNSIGNED NOT NULL,
  `companyName` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `shortDescription` varchar(500) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `sdgNumbers` varchar(120) DEFAULT NULL,
  `logoPath` varchar(500) DEFAULT NULL,
  `logoWhitePath` varchar(500) DEFAULT NULL,
  `websiteUrl` varchar(500) DEFAULT NULL,
  `facebookUrl` varchar(500) DEFAULT NULL,
  `contactDetails` varchar(255) DEFAULT NULL,
  `contactName` varchar(150) DEFAULT NULL,
  `contactNumber` varchar(50) DEFAULT NULL,
  `contactEmail` varchar(255) DEFAULT NULL,
  `cohort` varchar(50) DEFAULT NULL,
  `teamMembers` text DEFAULT NULL,
  `sortOrder` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `isPublished` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incubatee_applications`
--

CREATE TABLE `incubatee_applications` (
  `id` int(11) UNSIGNED NOT NULL,
  `startupName` varchar(255) NOT NULL,
  `startupDescription` text NOT NULL,
  `mainRisk` text DEFAULT NULL,
  `shortTermGoals` text DEFAULT NULL,
  `teamCvPath` text DEFAULT NULL,
  `leanCanvasPath` varchar(500) DEFAULT NULL,
  `videoPresentationLink` varchar(500) NOT NULL,
  `applicantName` varchar(255) NOT NULL,
  `applicantEmail` varchar(255) NOT NULL,
  `contactNumber` varchar(20) NOT NULL,
  `applicationStatus` enum('pending','for_revalidation','accepted','rejected') NOT NULL DEFAULT 'pending',
  `statusRemark` text DEFAULT NULL,
  `revalidationTokenHash` varchar(64) DEFAULT NULL,
  `revalidationTokenExpiresAt` datetime DEFAULT NULL,
  `revalidationRequestedAt` datetime DEFAULT NULL,
  `revalidatedAt` datetime DEFAULT NULL,
  `isArchived` tinyint(1) NOT NULL DEFAULT 0,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  `updatedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `landing_settings`
--

CREATE TABLE `landing_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `settingKey` varchar(100) NOT NULL,
  `settingValue` text DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organization_members`
--

CREATE TABLE `organization_members` (
  `id` int(11) UNSIGNED NOT NULL,
  `section` varchar(20) NOT NULL,
  `fullName` varchar(150) NOT NULL,
  `rolePrimary` varchar(255) DEFAULT NULL,
  `roleSecondary` varchar(255) DEFAULT NULL,
  `mentorCategory` varchar(100) DEFAULT NULL,
  `photoPath` varchar(500) DEFAULT NULL,
  `isFeatured` tinyint(1) NOT NULL DEFAULT 0,
  `sortOrder` int(11) NOT NULL DEFAULT 0,
  `isPublished` tinyint(1) NOT NULL DEFAULT 1,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `shortDescription` varchar(500) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'news',
  `sortOrder` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `imagePath` varchar(500) DEFAULT NULL,
  `isPublished` tinyint(1) NOT NULL DEFAULT 0,
  `isFeatured` tinyint(1) NOT NULL DEFAULT 0,
  `authorName` varchar(150) DEFAULT NULL,
  `publishedAt` datetime DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `updatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_slug_history`
--

CREATE TABLE `post_slug_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `postId` int(10) UNSIGNED NOT NULL,
  `oldSlug` varchar(255) NOT NULL,
  `createdAt` datetime DEFAULT current_timestamp(),
  `updatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_admins_email` (`email`),
  ADD UNIQUE KEY `admins_googleEmail_unique` (`googleEmail`),
  ADD UNIQUE KEY `admins_googleSub_unique` (`googleSub`);

--
-- Indexes for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `isRead` (`isRead`),
  ADD KEY `createdAt` (`createdAt`),
  ADD KEY `sourceType_sourceId` (`sourceType`,`sourceId`),
  ADD KEY `targetRole` (`targetRole`),
  ADD KEY `targetAdminId` (`targetAdminId`),
  ADD KEY `actorAdminId` (`actorAdminId`);

--
-- Indexes for table `admin_notification_reads`
--
ALTER TABLE `admin_notification_reads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_notification_reads_unique` (`notificationId`,`adminId`),
  ADD KEY `notificationId_adminId` (`notificationId`,`adminId`);

--
-- Indexes for table `cohorts`
--
ALTER TABLE `cohorts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_name` (`name`),
  ADD UNIQUE KEY `uq_number` (`number`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `isRead` (`isRead`),
  ADD KEY `createdAt` (`createdAt`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sortOrder` (`sortOrder`),
  ADD KEY `isPublished` (`isPublished`);

--
-- Indexes for table `game_players`
--
ALTER TABLE `game_players`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_game_players_first_last_school_unique` (`firstName`,`lastName`,`school`),
  ADD KEY `isActive` (`isActive`);

--
-- Indexes for table `game_wordle_plays`
--
ALTER TABLE `game_wordle_plays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_game_wordle_player_day_unique` (`playerId`,`playDate`),
  ADD KEY `idx_playdate_status` (`playDate`,`status`),
  ADD KEY `idx_score` (`score`),
  ADD KEY `idx_finishedAt` (`finishedAt`);

--
-- Indexes for table `incubatees`
--
ALTER TABLE `incubatees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_incubatees_slug` (`slug`);

--
-- Indexes for table `incubatee_applications`
--
ALTER TABLE `incubatee_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `applicationStatus` (`applicationStatus`),
  ADD KEY `createdAt` (`createdAt`),
  ADD KEY `idx_incubatee_applications_revalidation_token` (`revalidationTokenHash`);

--
-- Indexes for table `landing_settings`
--
ALTER TABLE `landing_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_landing_settings_key` (`settingKey`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `organization_members`
--
ALTER TABLE `organization_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_sortOrder` (`section`,`sortOrder`),
  ADD KEY `isPublished` (`isPublished`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_posts_slug` (`slug`);

--
-- Indexes for table `post_slug_history`
--
ALTER TABLE `post_slug_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_post_slug_history_oldSlug` (`oldSlug`),
  ADD UNIQUE KEY `idx_post_slug_history_oldSlug` (`oldSlug`),
  ADD KEY `idx_post_slug_history_postId` (`postId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_notifications`
--
ALTER TABLE `admin_notifications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_notification_reads`
--
ALTER TABLE `admin_notification_reads`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cohorts`
--
ALTER TABLE `cohorts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `game_players`
--
ALTER TABLE `game_players`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `game_wordle_plays`
--
ALTER TABLE `game_wordle_plays`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incubatees`
--
ALTER TABLE `incubatees`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incubatee_applications`
--
ALTER TABLE `incubatee_applications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `landing_settings`
--
ALTER TABLE `landing_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `organization_members`
--
ALTER TABLE `organization_members`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_slug_history`
--
ALTER TABLE `post_slug_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `game_wordle_plays`
--
ALTER TABLE `game_wordle_plays`
  ADD CONSTRAINT `fk_game_wordle_plays_player` FOREIGN KEY (`playerId`) REFERENCES `game_players` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `post_slug_history`
--
ALTER TABLE `post_slug_history`
  ADD CONSTRAINT `fk_post_slug_history_postId` FOREIGN KEY (`postId`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
