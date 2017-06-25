-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 25, 2017 at 05:05 PM
-- Server version: 10.1.22-MariaDB
-- PHP Version: 7.1.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cascade`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(255) NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `leather_cap` int(255) NOT NULL DEFAULT '0',
  `leather_vest` int(255) NOT NULL DEFAULT '0',
  `practice_sword` int(255) NOT NULL DEFAULT '0',
  `practice_wand` int(255) NOT NULL DEFAULT '0',
  `potion` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `username`, `leather_cap`, `leather_vest`, `practice_sword`, `practice_wand`, `potion`) VALUES
(24, 'Nepex', 1, 1, 3, 5, 955),
(25, 'NewAccount', 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(255) NOT NULL,
  `sender` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receiver` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `dateOf` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timeOf` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender`, `receiver`, `seen`, `message`, `dateOf`, `timeOf`) VALUES
(9, 'Nepex', 'Nepex', 'true', 'Test', '05/14/17', '12:32 am'),
(10, 'Nepex', 'Nepex', 'false', 'thx', '05/14/17', '12:32 am'),
(11, 'Nepex', 'nepex', 'false', 'yo\n', '05/14/17', '11:40 pm');

-- --------------------------------------------------------

--
-- Table structure for table `party`
--

CREATE TABLE `party` (
  `id` int(255) NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `job` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` int(255) NOT NULL,
  `sprite` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `experience` int(255) NOT NULL,
  `experience_needed` int(255) NOT NULL,
  `strength` int(255) NOT NULL,
  `magic` int(255) NOT NULL,
  `defense` int(255) NOT NULL,
  `resistance` int(255) NOT NULL,
  `haste` int(255) NOT NULL,
  `current_hp` int(255) NOT NULL,
  `hp` int(255) NOT NULL,
  `current_mp` int(255) NOT NULL,
  `mp` int(255) NOT NULL,
  `bonus_strength` int(255) NOT NULL,
  `bonus_magic` int(255) NOT NULL,
  `bonus_defense` int(255) NOT NULL,
  `bonus_resistance` int(255) NOT NULL,
  `bonus_haste` int(255) NOT NULL,
  `bonus_hp` int(255) NOT NULL,
  `bonus_mp` int(255) NOT NULL,
  `stat_points` int(255) NOT NULL,
  `helm` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'empty',
  `chest` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'empty',
  `main_hand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'empty',
  `off_hand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'empty',
  `accessory` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'empty'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `party`
--

INSERT INTO `party` (`id`, `owner`, `name`, `job`, `level`, `sprite`, `experience`, `experience_needed`, `strength`, `magic`, `defense`, `resistance`, `haste`, `current_hp`, `hp`, `current_mp`, `mp`, `bonus_strength`, `bonus_magic`, `bonus_defense`, `bonus_resistance`, `bonus_haste`, `bonus_hp`, `bonus_mp`, `stat_points`, `helm`, `chest`, `main_hand`, `off_hand`, `accessory`) VALUES
(100, 'NewAccount', 'Mondays', 'Priest', 1, 'sprite2', 0, 500, 5, 20, 15, 15, 10, 9999, 9999, 55, 55, 0, 1, 2, 0, 0, 0, 0, 0, 'leather_cap', 'leather_vest', 'practice_wand', 'empty', 'empty'),
(106, 'Nepex', 'Nepex', 'Knight', 12, 'sprite1', 37, 500, 20, 5, 15, 15, 10, 9959, 9999, 1500, 1500, 1, 0, 2, 0, 0, 0, 0, 110, 'leather_cap', 'leather_vest', 'practice_sword', 'empty', 'empty'),
(111, 'Nepex', 'test', 'Knight', 1, 'sprite1', 87, 500, 20, 5, 15, 15, 10, 105, 125, 15, 15, 1, 0, 2, 0, 0, 0, 0, 0, 'leather_cap', 'leather_vest', 'practice_sword', 'empty', 'empty'),
(112, 'Nepex', 'test2', 'Priest', 1, 'sprite1', 87, 500, 5, 20, 15, 15, 10, 115, 115, 50, 55, 0, 1, 2, 0, 0, 0, 0, 0, 'leather_cap', 'leather_vest', 'practice_wand', 'empty', 'empty'),
(113, 'Nepex', 'test23', 'Priest', 1, 'sprite1', 87, 500, 5, 20, 15, 15, 10, 55, 115, 55, 55, 0, 1, 2, 0, 0, 0, 0, 0, 'leather_cap', 'leather_vest', 'practice_wand', 'empty', 'empty');

-- --------------------------------------------------------

--
-- Table structure for table `quests`
--

CREATE TABLE `quests` (
  `id` int(255) NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quests`
--

INSERT INTO `quests` (`id`, `username`) VALUES
(2, 'Nepex'),
(3, 'NewAccount');

-- --------------------------------------------------------

--
-- Table structure for table `spells_learned`
--

CREATE TABLE `spells_learned` (
  `id` int(255) NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `party_member` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `spell_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cost` int(255) NOT NULL,
  `base` int(255) NOT NULL,
  `spell_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `spells_learned`
--

INSERT INTO `spells_learned` (`id`, `username`, `party_member`, `spell_name`, `cost`, `base`, `spell_type`, `description`) VALUES
(37, 'NewAccount', 'Mondays', 'Cure', 5, 80, 'Heal', 'Heals a party member for a small amount.'),
(39, 'Nepex', 'test2', 'Cure', 5, 80, 'Heal', 'Heals a party member for a small amount.'),
(40, 'Nepex', 'test23', 'Cure', 5, 80, 'Heal', 'Heals a party member for a small amount.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(255) NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` int(255) NOT NULL DEFAULT '500',
  `party_slots_unlocked` int(255) NOT NULL DEFAULT '1',
  `combat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `currency`, `party_slots_unlocked`, `combat`) VALUES
(16, 'Nepex', '$2y$10$Vz38IFI5sDj2medaCm0gYeO3Wlgg4W8m0imHIB4ixi/1T10mpQzRe', 'thechump@hotmail.com', 11105, 4, 'true'),
(17, 'NewAccount', '$2y$10$zWpOUXuMFC0MrqoTKewKeubP9kcLSifRJB7Z/42Qbb0UHJKswi6.i', 'new@account.com', 500, 1, 'false');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `party`
--
ALTER TABLE `party`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quests`
--
ALTER TABLE `quests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spells_learned`
--
ALTER TABLE `spells_learned`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `party`
--
ALTER TABLE `party`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;
--
-- AUTO_INCREMENT for table `quests`
--
ALTER TABLE `quests`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `spells_learned`
--
ALTER TABLE `spells_learned`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
