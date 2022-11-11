-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 02, 2021 at 08:54 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `demo_student`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `addStudent` (IN `s_name` VARCHAR(255), IN `s_dept` VARCHAR(255), IN `s_mob` VARCHAR(20), OUT `is_done` TINYINT(4), OUT `s_id` BIGINT(20))  NO SQL
BEGIN
set s_id = 0;
set is_done = 0;
INSERT INTO `student`(`name`,`dept`,`mob`)
VALUES(s_name,s_dept,s_mob);
IF Row_Count () > 0 THEN
	set s_id = last_insert_id();
    set is_done=1;
end IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteStudent` (IN `s_id` BIGINT(20), OUT `is_done` TINYINT(4))  NO SQL
BEGIN
set is_done = 0;
DELETE from `student` WHERE id = s_id;
IF Row_Count () > 0 THEN
	set is_done = 1;
end IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetchAllStudents` ()  NO SQL
SELECT  * from student$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getStudent` (IN `s_id` BIGINT(20))  NO SQL
SELECT * FROM student WHERE id = s_id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateStudent` (IN `s_id` BIGINT(20), IN `s_name` VARCHAR(255), OUT `is_done` TINYINT(4))  NO SQL
BEGIN
set is_done=0;
UPDATE `student` SET name = s_name WHERE id = s_id;
IF Row_Count() > 0 THEN
	set is_done=1;
end IF;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateStudPhoto` (IN `s_id` BIGINT(20), IN `imagename` VARCHAR(255), OUT `is_done` TINYINT(4))  NO SQL
BEGIN
set is_done=0;
UPDATE `student` SET image = imagename WHERE id = s_id;
set is_done=1;


END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `Id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dept` varchar(255) NOT NULL,
  `mob` varchar(20) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`Id`, `name`, `dept`, `mob`, `image`) VALUES
(3, 'Dhvanil', 'Computer', '4567890123', '3.png'),
(5, 'Ankita', 'Computer', '0987654321', '5.png'),
(6, 'Harshita', 'Computer', '1234567890', ''),
(7, 'rushi', 'Computer', '2345678901', '7.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `Id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
