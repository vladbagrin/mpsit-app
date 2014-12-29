-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 29, 2014 at 05:51 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `birthday_thanker`
--
CREATE DATABASE IF NOT EXISTS `birthday_thanker` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `birthday_thanker`;

-- --------------------------------------------------------

--
-- Table structure for table `posts_thanked`
--

CREATE TABLE IF NOT EXISTS `posts_thanked` (
  `fb_id` varchar(50) NOT NULL,
  `post_id` varchar(128) NOT NULL,
  UNIQUE KEY `fb_id` (`fb_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
