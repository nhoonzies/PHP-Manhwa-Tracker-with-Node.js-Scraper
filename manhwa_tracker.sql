-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2026 at 04:25 AM
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
-- Database: `manhwa_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `chapter_number` decimal(6,2) NOT NULL,
  `chapter_title` varchar(255) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `series_id`, `chapter_number`, `chapter_title`, `release_date`, `created_at`) VALUES
(6, 2, 0.17, NULL, NULL, '2026-05-04 00:09:34'),
(7, 3, 35.00, NULL, NULL, '2026-05-04 00:17:41'),
(9, 5, 0.00, NULL, NULL, '2026-05-04 01:25:16'),
(10, 6, 0.50, NULL, NULL, '2026-05-04 01:45:47'),
(11, 7, 0.12, NULL, NULL, '2026-05-04 01:47:00'),
(12, 8, 0.00, NULL, NULL, '2026-05-04 01:57:48');

-- --------------------------------------------------------

--
-- Table structure for table `series`
--

CREATE TABLE `series` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cover_image_url` varchar(500) DEFAULT NULL,
  `scrape_url` varchar(500) NOT NULL,
  `status` enum('Ongoing','Completed','Hiatus') DEFAULT 'Ongoing',
  `last_checked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `series`
--

INSERT INTO `series` (`id`, `title`, `author`, `description`, `cover_image_url`, `scrape_url`, `status`, `last_checked_at`, `created_at`) VALUES
(2, 'Absolute Sword Sense', NULL, 'For as long as he can remember, Woonhwi So has been the black sheep of his family and clan. Because his spirit core has been destroyed, he has no choice but to serve as a third-rate spy for the Blood Cult.\nOne day, during his mission to find the legendary Blade Scroll, he is killed and sent 10 years into the past. When Woonhwi regains consciousness, he realizes that he has been sent back to the day he was kidnapped by the Blood Cult, and that he has gained a mysterious ability to hear the voice of a dagger.\n', 'https://swebtoon-phinf.pstatic.net/20230207_233/1675728041036wlSaC_JPEG/5AbsoluteSwordSense_landingpage_mobile.jpg?type=crop540_540', 'https://www.webtoons.com/en/action/absolute-sword-sense/list?title_no=5100', 'Ongoing', NULL, '2026-05-04 00:09:34'),
(3, 'System Universe', NULL, 'Derek lives by one creed. Fight, adapt, and rely only on himself. In the years after the System arrived on Earth and warped it into a monster-ridden wasteland, those rules forged him into one of Earth’s most powerful warriors. They let him thrive in the face of the Apocalypse — until the day he broke them. Failed by his teammates and sent through the void into an entirely new world, fate seems to have wanted Derek to start all the way back from the beginning at level 1. It’s a good thing he brought his old System with him.', 'https://swebtoon-phinf.pstatic.net/20250912_163/1757634438744IFYxm_JPEG/System Universe_Landing Page.jpg?type=crop540_540', 'https://www.webtoons.com/en/action/system-universe/list?title_no=8178', 'Ongoing', NULL, '2026-05-04 00:17:41'),
(5, 'Solo Leveling', NULL, 'Read the official series of Solo Leveling. [Epilogue now available] In a world where awakened beings called “Hunters” must battle deadly monsters to protect humanity, Sung Jinwoo, nicknamed “the weakest hunter of all mankind,” finds himself in a constant struggle for survival. One day, after a brutal encounter in an overpowered dungeon wipes out his party and threatens to end his life, a mysterious System chooses him as its sole player: Jinwoo has been granted the rare opportunity to level up his abilities, possibly beyond any known limits. Follow Jinwoo’s journey as he takes on ever-stronger enemies, both human and monster, to discover the secrets deep within the dungeons and the ultimate extent of his powers.\n\nBased on the action-fantasy novel that has become a global phenomenon, the highly anticipated comic adaptation arrives with a brand-new official English translation produced by Tappytoon.\n\nSolo Leveling ⓒ DUBU(REDICE), Chugong, h-goon / D&C\nSolo Leveling Epilogue ⓒ DISCIPLES(REDICE), Chugong, h-goon / D&C\nAll rights reserved. Published by Tappytoon under license from partners.', 'https://image-repository-cdn.tappytoon.com/series/20/bf3b8372-e290-4c2c-9870-268b9b1f9d62.jpg', 'https://www.tappytoon.com/en/book/solo-leveling-official', 'Ongoing', NULL, '2026-05-04 01:25:16'),
(6, 'Infinite Leveling: Murim', NULL, 'Killed on the battlefield without glory to his name, Yuseong  Dan receives an opportunity to grow stronger with a strange quest and level system. But each quest he’s forced to fight only seems to be getting harder and harder. Can he level up enough to avoid the same miserable ending and become the powerful hero he wishes to be?\n', 'https://swebtoon-phinf.pstatic.net/20260429_11/17773977283028bb2D_JPEG/6Infinite_Leveling-_Murim_Episode_List_Mobile.jpg?type=crop540_540', 'https://www.webtoons.com/en/action/infinite-leveling-murim/list?title_no=2676&page=21', 'Ongoing', NULL, '2026-05-04 01:45:47'),
(7, 'One Step Closer to the Demon King', NULL, 'When the infamy of the Demon King echoes to the ocean\'s depths, Yuria, the king of the sea, decides to venture onto land and challenge them. However, the very act of gaining two legs robs her of all her power, transforming her into a helpless child. Now faced with this unexpected twist of fate, can Yuria rise above this setback and defeat the Demon King to claim her place as the most powerful being in the world?', 'https://swebtoon-phinf.pstatic.net/20240123_299/1705959295072vfO4w_JPEG/8EpisodeList_Mobile.jpg?type=crop540_540', 'https://www.webtoons.com/en/fantasy/one-step-closer-to-the-demon-king/list?title_no=6064', 'Ongoing', NULL, '2026-05-04 01:47:00'),
(8, 'Tyrant of the Tower Defense Game', NULL, 'Read the official series of Tyrant of the Tower Defense Game. \"Protect the Empire\" was considered unbeatable for over a decade until streamer extraordinaire \"Mr. Gamer Geek\" comes along and defeats the game on its hardest mode. But just when he\'s about to rest on his laurels, he\'s sucked into the world of the game by some mysterious figure and thrust into Prince Ash\'s body! Ash now realizes that every click and command he had mindlessly sent out had real, gruesome costs - including his teammates\' lives that he sacrificed for the sake of victory. To make up for his previous actions, Ash promises to keep his whole team alive this time while using his wits and knowledge to survive the hellish onslaught of monsters. But who brought him to this world in the first place, and why? Ash may soon find the answers to these questions - if he can survive the bloody battlefield first!\n\nⓒ RyuMo, Gyong, Ha Jung 2022\nAll rights reserved. Published by Tappytoon under license from partners.', 'https://image-repository-cdn.tappytoon.com/series/54/50b724c1-ae01-4bc5-96f9-8e5649cefaef.jpg', 'https://www.tappytoon.com/en/book/tyrant-of-the-tower-defense-game', 'Ongoing', NULL, '2026-05-04 01:57:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', 'placeholder_password', 'user', '2026-05-03 21:57:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_library`
--

CREATE TABLE `user_library` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `series_id` int(11) NOT NULL,
  `current_chapter` decimal(6,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT 'Reading',
  `rating` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_library`
--

INSERT INTO `user_library` (`id`, `user_id`, `series_id`, `current_chapter`, `status`, `rating`, `updated_at`) VALUES
(5, 1, 2, 0.00, 'Dropped', NULL, '2026-05-04 00:13:47'),
(7, 1, 3, 0.00, 'Plan to Read', 4, '2026-05-04 00:18:24'),
(11, 1, 5, 0.00, 'Dropped', NULL, '2026-05-04 01:25:46'),
(12, 1, 6, 0.00, 'Reading', NULL, '2026-05-04 01:46:02'),
(14, 1, 7, 0.00, 'Favorites', 5, '2026-05-04 01:52:14'),
(15, 1, 8, 0.00, 'Favorites', 5, '2026-05-04 01:58:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `series_id` (`series_id`,`chapter_number`);

--
-- Indexes for table `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_library`
--
ALTER TABLE `user_library`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`series_id`),
  ADD KEY `series_id` (`series_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `series`
--
ALTER TABLE `series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_library`
--
ALTER TABLE `user_library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chapters`
--
ALTER TABLE `chapters`
  ADD CONSTRAINT `chapters_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_library`
--
ALTER TABLE `user_library`
  ADD CONSTRAINT `user_library_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_library_ibfk_2` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
