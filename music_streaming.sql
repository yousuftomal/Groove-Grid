-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2024 at 08:53 AM
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
-- Database: `music_streaming`
--

-- --------------------------------------------------------

--
-- Table structure for table `adminapproval`
--

CREATE TABLE `adminapproval` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `song_id` int(11) DEFAULT NULL,
  `approval` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `artist`
--

CREATE TABLE `artist` (
  `id` int(11) NOT NULL,
  `Channel_name` varchar(30) DEFAULT NULL,
  `song_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE `genre` (
  `id` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genre`
--

INSERT INTO `genre` (`id`, `Name`) VALUES
(1, 'Pop'),
(2, 'Rock');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `liked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `song_id`, `liked_at`) VALUES
(2, 6, 4, '2024-09-20 15:58:33'),
(6, 4, 5, '2024-09-20 18:37:12');

-- --------------------------------------------------------

--
-- Table structure for table `song`
--

CREATE TABLE `song` (
  `id` int(11) NOT NULL,
  `URL` varchar(500) NOT NULL,
  `Release_date` date NOT NULL,
  `language` varchar(50) NOT NULL,
  `details` varchar(250) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `approval` tinyint(1) DEFAULT 0,
  `artist_id` int(11) DEFAULT NULL,
  `genre` varchar(10) NOT NULL,
  `cover_photo_url` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `song`
--

INSERT INTO `song` (`id`, `URL`, `Release_date`, `language`, `details`, `title`, `approval`, `artist_id`, `genre`, `cover_photo_url`) VALUES
(4, 'uploads/test.mp3', '2024-09-20', 'Bangla', 'Shikkhar Jobanbondi || শিক্ষার জবানবন্দি || Kaaktaal', 'Shikkhar Jobanbondi || শিক্ষার জবানবন্দি || Kaaktaal', 1, 5, '', ''),
(5, 'uploads\\Bob Dylan - Knocking on Heaven\'s Door (Original 1973).mp3', '2024-09-20', 'English', 'Knockin\' on Heaven\'s Door&quot; is a song by American singer-songwriter Bob Dylan, written for the soundtrack of the 1973 film Pat Garrett and Billy the Kid.', 'Bob Dylan - knocking on heaven\'s door (original 1973)', 1, 5, 'Pop', 'uploads/cover.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `songgenre`
--

CREATE TABLE `songgenre` (
  `song_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE `subscription` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscription`
--

INSERT INTO `subscription` (`id`, `user_id`, `type`, `start_date`, `end_date`) VALUES
(1, 1, 'Monthly', '2024-01-01', '2024-01-31'),
(2, 2, 'Yearly', '2024-02-01', '2025-01-31'),
(4, NULL, '6-Months', '2024-09-19', '2025-03-19');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `F_name` varchar(25) NOT NULL,
  `L_name` varchar(25) NOT NULL,
  `img_url` varchar(505) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `User_Type` enum('Listener','Admin','Artist') NOT NULL,
  `bio` varchar(300) NOT NULL,
  `genre` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `F_name`, `L_name`, `img_url`, `password`, `User_Type`, `bio`, `genre`) VALUES
(1, 'John', 'Doe', 'http://example.com/john.jpg', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Listener', '', ''),
(2, 'Jane', 'Smith', 'http://example.com/jane.jpg', 'fbb4a8a163ffa958b4f02bf9cabb30cfefb40de803f2c4c346a9d39b3be1b544', 'Admin', '', ''),
(3, 'Emily', 'Jones', 'http://example.com/emily.jpg', '89e01536ac207279409d4de1e5253e01f4a1769e696db0d6062ca9b8f56767c8', 'Artist', '', ''),
(4, 'Afrida', 'Afafafafa', NULL, '$2y$10$UkWxYA9BqZZ5IacGUPf..epuSW9f8NCt./4XjeLR/QgsuM.tPKdGm', 'Listener', '', ''),
(5, 'Afrida', 'Afafafafafafafafaf', NULL, '$2y$10$abgQgSiuB.o3gVhQ5D8lC.1aaYJB0Jy37gfkBlfysXnyZqR6c2ZmS', 'Artist', 'yoyo', 'Rap'),
(6, 'Afrida', 'Afafafafafafafafaf', NULL, '$2y$10$87cZXr0ZELmxVW/1BZl9Aut63p.YGuIzgcoVJfTYJYqojNCW4cJwG', 'Admin', '', ''),
(7, 'Admin', 'admin', NULL, '$2y$10$u/mEYjKlf4xPV/naU0dyj.VQoOOWhi3pUSvtks6iiQeX/T6/m7ZaO', 'Admin', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `useremail`
--

CREATE TABLE `useremail` (
  `id` int(11) NOT NULL,
  `Email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `useremail`
--

INSERT INTO `useremail` (`id`, `Email`) VALUES
(1, 'john.doe@example.com'),
(2, 'jane.smith@example.com'),
(3, 'emily.jones@example.com'),
(4, 'abc@xyz.com'),
(5, 'abc@xyz.artist'),
(6, 'abc@xyz.admin'),
(7, 'admin@admin.admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminapproval`
--
ALTER TABLE `adminapproval`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `song_id` (`song_id`);

--
-- Indexes for table `artist`
--
ALTER TABLE `artist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `song_id` (`song_id`);

--
-- Indexes for table `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `song_id` (`song_id`);

--
-- Indexes for table `song`
--
ALTER TABLE `song`
  ADD PRIMARY KEY (`id`),
  ADD KEY `artist_id` (`artist_id`);

--
-- Indexes for table `songgenre`
--
ALTER TABLE `songgenre`
  ADD PRIMARY KEY (`song_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `subscription`
--
ALTER TABLE `subscription`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `useremail`
--
ALTER TABLE `useremail`
  ADD PRIMARY KEY (`id`,`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adminapproval`
--
ALTER TABLE `adminapproval`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `genre`
--
ALTER TABLE `genre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `song`
--
ALTER TABLE `song`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subscription`
--
ALTER TABLE `subscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adminapproval`
--
ALTER TABLE `adminapproval`
  ADD CONSTRAINT `adminapproval_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `adminapproval_ibfk_2` FOREIGN KEY (`song_id`) REFERENCES `song` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `artist`
--
ALTER TABLE `artist`
  ADD CONSTRAINT `artist_ibfk_1` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `artist_ibfk_2` FOREIGN KEY (`song_id`) REFERENCES `song` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`song_id`) REFERENCES `song` (`id`);

--
-- Constraints for table `song`
--
ALTER TABLE `song`
  ADD CONSTRAINT `song_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `user` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `songgenre`
--
ALTER TABLE `songgenre`
  ADD CONSTRAINT `songgenre_ibfk_1` FOREIGN KEY (`song_id`) REFERENCES `song` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `songgenre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscription`
--
ALTER TABLE `subscription`
  ADD CONSTRAINT `subscription_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `useremail`
--
ALTER TABLE `useremail`
  ADD CONSTRAINT `useremail_ibfk_1` FOREIGN KEY (`id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
