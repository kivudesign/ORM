-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 04, 2021 at 07:52 AM
-- Server version: 5.7.21
-- PHP Version: 7.3.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wepesi_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `actions` varchar(200) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `datecreated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `userid`, `actions`, `description`, `datecreated`) VALUES
(1, 1, 'login', 'admin: tantative de connection ,host:::1,situation:connection avec success', '2021-01-01 20:18:29'),
(2, 1, 'login', 'admin: tantative de connection ,host:::1,situation:connection avec success', '2021-01-01 20:18:52'),
(3, 1, 'login', 'Array', '2021-01-01 20:29:57'),
(4, 1, 'login', '{\"username\":\"admin\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":\"connection avec success\"}}', '2021-01-01 20:33:17'),
(5, 0, 'login', '{\"username\":\"alfa\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":{\"situation\":\"echec de connection\",\"source\":\"user is not registered\"}}}', '2021-01-01 20:34:00'),
(6, 0, 'login', '{\"username\":\"alfa\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":{\"situation\":\"echec de connection\",\"source\":\"user is not registered\"}}}', '2021-01-01 20:39:07'),
(7, 0, 'login', '{\"username\":\"ibmussafb@gmail.com\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":{\"situation\":\"echec de connection\",\"source\":\"user is not registered\"}}}', '2021-01-01 20:43:44'),
(8, 0, 'login', '{\"username\":\"ibmussafb@gmail.com\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":{\"situation\":\"echec de connection\",\"source\":\"user is not registered\"}}}', '2021-01-01 20:44:58'),
(9, 1, 'login', '{\"username\":\"admin\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":\"connection avec success\"}}', '2021-01-01 20:45:56'),
(10, 0, 'register', '{\"username\":\"admin\",\"host\":\"::1\",\"action_start\":\"tantatice de creation de compte\",\"0\":{\"result\":{\"situation\":\"echec de connection\",\"source\":[\"username already exist.\"]}}}', '2021-01-01 21:08:58'),
(11, 2, 'register', 'Array', '2021-01-01 21:10:08'),
(12, 0, 'register', '{\"username\":\"alfa\",\"host\":\"::1\",\"action_start\":\"tantatice de creation de compte\",\"0\":{\"result\":{\"situation\":\"echec de connection\",\"source\":[\"username already exist.\"]}}}', '2021-01-01 21:11:02'),
(13, 2, 'login', '{\"username\":\"alfa\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":\"connection avec success\"}}', '2021-01-01 21:11:08'),
(14, 3, 'register', '{\"username\":\"alfa\",\"host\":\"::1\",\"action_start\":\"tantatice de creation de compte\",\"0\":{\"result\":\"creation avec succes\"}}', '2021-01-01 21:11:54'),
(15, 3, 'login', '{\"username\":\"alfa\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":\"connection avec success\"}}', '2021-01-01 21:12:34'),
(16, 1, 'login', '{\"username\":\"admin\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":\"connection avec success\"}}', '2021-01-01 21:12:51'),
(17, 1, 'login', '{\"username\":\"admin\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":\"connection avec success\"}}', '2021-01-02 00:20:25'),
(18, 1, 'logout', '{\"username\":\"admin\",\"host\":\"::1\",\"action_start\":\"tentative de deconnexion\",\"result\":\"logout success full\"}', '2021-01-02 00:31:00'),
(19, 1, 'login', '{\"username\":\"admin\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":\"connection avec success\"}}', '2021-01-02 01:16:45'),
(20, 1, 'post message', '{\"action_start\":\"tentative post message\",\"host\":\"::1\",\"0\":{\"result\":{\"situation\":\"echec post message\",\"source\":\"SQLSTATE[42S22]: Column not found: 1054 Unknown column \'username\' in \'field list\'\"}}}', '2021-01-02 01:16:52'),
(21, 1, 'post message', '{\"action_start\":\"tentative post message\",\"host\":\"::1\",\"0\":{\"result\":\"post message avec success\"}}', '2021-01-02 01:24:13'),
(22, 1, 'post message', '{\"action_start\":\"tentative post message\",\"host\":\"::1\",\"0\":{\"result\":\"post message avec success\"}}', '2021-01-02 01:27:32'),
(23, 1, 'logout', '{\"username\":\"admin\",\"host\":\"::1\",\"action_start\":\"tentative de deconnexion\",\"result\":\"logout success full\"}', '2021-01-02 01:27:40'),
(24, 3, 'login', '{\"username\":\"alfa\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":\"connection avec success\"}}', '2021-01-02 01:27:45'),
(25, 3, 'post message', '{\"action_start\":\"tentative post message\",\"host\":\"::1\",\"0\":{\"result\":\"post message avec success\"}}', '2021-01-02 01:28:05'),
(26, 3, 'logout', '{\"username\":\"alfa\",\"host\":\"::1\",\"action_start\":\"tentative de deconnexion\",\"result\":\"logout success full\"}', '2021-01-02 01:28:08'),
(27, 1, 'login', '{\"username\":\"admin\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":\"connection avec success\"}}', '2021-01-02 01:28:12'),
(28, 1, 'logout', '{\"username\":\"admin\",\"host\":\"::1\",\"action_start\":\"tentative de deconnexion\",\"result\":\"logout success full\"}', '2021-01-02 01:29:30'),
(29, 1, 'login', '{\"username\":\"admin\",\"action_start\":\"tantative de connection\",\"host\":\"::1\",\"0\":{\"result\":\"connection avec success\"}}', '2021-01-02 01:54:20'),
(30, 1, 'post message', '{\"action_start\":\"tentative post message\",\"host\":\"::1\",\"0\":{\"result\":\"post message avec success\"}}', '2021-01-02 01:54:24'),
(31, 1, 'logout', '{\"username\":\"admin\",\"host\":\"::1\",\"action_start\":\"tentative de deconnexion\",\"result\":\"logout success full\"}', '2021-01-02 01:54:28');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `message` varchar(200) COLLATE utf8_bin NOT NULL,
  `datecreated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `userid`, `message`, `datecreated`) VALUES
(1, 1, 'dgdfgdfg', '2021-01-02 01:01:13'),
(2, 1, 'hello le monde, comment vous allez??', '2021-01-02 01:01:32'),
(3, 3, 'je vais bien, comment ca se pass la journee d hier', '2021-01-02 01:01:05'),
(4, 1, 'sdfsegserg', '2021-01-02 01:01:24'),
(5, 2, 'bonjour wepesi', '2021-01-04 03:49:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname` varchar(50) COLLATE utf8_bin NOT NULL,
  `username` varchar(50) COLLATE utf8_bin NOT NULL,
  `password` varchar(200) COLLATE utf8_bin NOT NULL,
  `datecreated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `datecreated`) VALUES
(1, 'ibrahim 2', 'admin', '123456', '2021-01-01 16:40:06'),
(3, 'alfa ', 'alfa', '123456', '2021-01-01 21:11:54');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
