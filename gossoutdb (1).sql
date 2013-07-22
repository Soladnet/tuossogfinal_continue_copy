-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 20, 2013 at 11:43 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gossoutdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment` text NOT NULL,
  `post_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` varchar(9) NOT NULL DEFAULT 'show',
  `deleteStatus` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`post_id`,`sender_id`),
  UNIQUE KEY `id` (`id`),
  KEY `post_id` (`post_id`),
  KEY `sender_id` (`sender_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `comment`, `post_id`, `sender_id`, `time`, `status`, `deleteStatus`) VALUES
(36, 'this is ok', 1422, 34, '2013-04-16 13:59:38', 'show', 0),
(37, 'hghfjh', 1423, 34, '2013-04-16 14:01:51', 'show', 0),
(38, 'xnxdgsdgxf', 1425, 34, '2013-04-17 13:51:23', 'show', 0);

-- --------------------------------------------------------

--
-- Table structure for table `comminvitation`
--

CREATE TABLE IF NOT EXISTS `comminvitation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `receiver_email` text NOT NULL,
  `comid` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`sender_id`,`comid`),
  KEY `comid` (`comid`),
  KEY `sender_id` (`sender_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `comminvitation`
--

INSERT INTO `comminvitation` (`id`, `sender_id`, `receiver_id`, `receiver_email`, `comid`, `status`, `time`) VALUES
(7, 38, 34, '', 61, 1, '2013-04-20 01:12:25'),
(8, 38, 37, '', 61, 0, '2013-04-19 13:38:04');

-- --------------------------------------------------------

--
-- Table structure for table `community`
--

CREATE TABLE IF NOT EXISTS `community` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unique_name` varchar(90) NOT NULL,
  `name` varchar(120) NOT NULL,
  `category` text NOT NULL,
  `type` varchar(7) NOT NULL DEFAULT 'Public',
  `description` text NOT NULL,
  `pix` text NOT NULL,
  `thumbnail100` text NOT NULL,
  `thumbnail150` text NOT NULL,
  `datecreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `creator_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`creator_id`),
  UNIQUE KEY `unique_name` (`unique_name`),
  KEY `creator_id` (`creator_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=62 ;

--
-- Dumping data for table `community`
--

INSERT INTO `community` (`id`, `unique_name`, `name`, `category`, `type`, `description`, `pix`, `thumbnail100`, `thumbnail150`, `datecreated`, `creator_id`) VALUES
(48, 'abu', 'Ahmadu Bello Univeristy', '', 'Public', 'Desc of ABU', 'upload/images/community_photo/1366145482-MzQ-0.jpg', 'upload/images/community_photo/1366145482-MzQ-1_thumb.jpg', 'upload/images/community_photo/1366145482-MzQ-2_thumb.jpg', '2013-04-15 08:51:36', 34),
(52, 'abc', 'ABC Transport Company', '', 'Public', 'This is about abc transport company system', 'upload/images/community_photo/1366357026-MzQ-0.jpg', 'upload/images/community_photo/1366357026-MzQ-1_thumb.jpg', 'upload/images/community_photo/1366357026-MzQ-2_thumb.jpg', '2013-04-15 11:37:08', 34),
(53, 'soladnet', 'Soladnet Softwares', '', 'Public', 'This is soladnet softwares with a\n\nwrap', 'upload/images/community_photo/1366113655-MzQ-0.jpg', 'upload/images/community_photo/1366113655-MzQ-1_thumb.jpg', 'upload/images/community_photo/1366113655-MzQ-2_thumb.jpg', '2013-04-15 16:35:30', 34),
(54, 'who', 'World Health Organization', '', 'Public', ',bgchjkh hjkh', 'upload/images/community_photo/1366121249-MzQ-0.jpg', 'upload/images/community_photo/1366121249-MzQ-1_thumb.jpg', 'upload/images/community_photo/1366121249-MzQ-2_thumb.jpg', '2013-04-16 14:06:58', 34),
(55, 'kora', 'Kora Properties', '', 'Public', 'Estate Agency', 'upload/images/community_photo/1366121623-MzQ-0.gif', 'upload/images/community_photo/1366121623-MzQ-1_thumb.gif', 'upload/images/community_photo/1366121623-MzQ-2_thumb.gif', '2013-04-16 14:13:43', 34),
(61, 'Gbaja', 'Gbaja Street Surulere Lagos', '', 'Public', 'some description', 'upload/images/community_photo/1366196026-Mzg-0.jpg', 'upload/images/community_photo/1366196026-Mzg-1_thumb.jpg', 'upload/images/community_photo/1366196026-Mzg-2_thumb.jpg', '2013-04-17 10:53:47', 38);

-- --------------------------------------------------------

--
-- Table structure for table `community_subscribers`
--

CREATE TABLE IF NOT EXISTS `community_subscribers` (
  `user` int(11) NOT NULL,
  `community_id` int(11) NOT NULL,
  `emailNotif` varchar(3) NOT NULL DEFAULT 'YES',
  `datejoined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `leave_status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user`,`community_id`),
  KEY `community_id` (`community_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `community_subscribers`
--

INSERT INTO `community_subscribers` (`user`, `community_id`, `emailNotif`, `datejoined`, `leave_status`) VALUES
(34, 48, 'YES', '2013-04-16 14:03:19', 0),
(34, 52, 'YES', '2013-04-16 14:00:22', 0),
(34, 53, 'YES', '2013-04-16 14:03:10', 0),
(34, 54, 'YES', '2013-04-16 14:06:58', 0),
(34, 55, 'YES', '2013-04-17 10:40:59', 0),
(34, 61, 'YES', '2013-04-20 01:26:57', 0),
(37, 48, 'YES', '2013-04-15 09:38:45', 0),
(37, 52, 'YES', '2013-04-19 08:03:20', 0),
(38, 52, 'YES', '2013-04-17 09:54:01', 0),
(38, 55, 'YES', '2013-04-17 10:07:55', 0),
(38, 61, 'YES', '2013-04-17 10:53:47', 0);

-- --------------------------------------------------------

--
-- Table structure for table `password_recovery`
--

CREATE TABLE IF NOT EXISTS `password_recovery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(120) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responded` varchar(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `pictureuploads`
--

CREATE TABLE IF NOT EXISTS `pictureuploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `original` text NOT NULL,
  `thumbnail45` text NOT NULL,
  `thumbnail50` text NOT NULL,
  `thumbnail75` text NOT NULL,
  `thumbnail150` text NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `id` (`id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=81 ;

--
-- Dumping data for table `pictureuploads`
--

INSERT INTO `pictureuploads` (`id`, `user_id`, `original`, `thumbnail45`, `thumbnail50`, `thumbnail75`, `thumbnail150`, `date_added`) VALUES
(77, 34, 'upload/images/1365962301MzQ.jpg', 'upload/images/1365962301MzQ-1.jpg', 'upload/images/1365962301MzQ-2.jpg', 'upload/images/1365962301MzQ-3.jpg', 'upload/images/1365962301MzQ-4.jpg', '2013-04-14 17:58:21'),
(79, 37, 'upload/images/1365968745Mzc.jpg', 'upload/images/1365968745Mzc-1.jpg', 'upload/images/1365968745Mzc-2.jpg', 'upload/images/1365968745Mzc-3.jpg', 'upload/images/1365968745Mzc-4.jpg', '2013-04-14 19:45:45'),
(80, 38, 'upload/images/1366190697Mzg.jpg', 'upload/images/1366190697Mzg-1.jpg', 'upload/images/1366190697Mzg-2.jpg', 'upload/images/1366190697Mzg-3.jpg', 'upload/images/1366190697Mzg-4.jpg', '2013-04-17 09:24:57');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post` text NOT NULL,
  `community_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(10) NOT NULL DEFAULT 'Show',
  `deleteStatus` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`),
  KEY `community_id` (`community_id`),
  KEY `sender_id` (`sender_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1427 ;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `post`, `community_id`, `sender_id`, `time`, `status`, `deleteStatus`) VALUES
(1419, 'where are you', 48, 37, '2013-04-15 09:38:59', 'Show', 0),
(1422, 'dsfsdf', 52, 34, '2013-04-15 15:45:57', 'Show', 0),
(1423, 'Type anything', 52, 34, '2013-04-16 14:01:23', 'Show', 0),
(1424, 'cbxcxcbcv', 48, 34, '2013-04-17 13:51:04', 'Show', 0),
(1425, 'xdv xxcgxdg', 48, 34, '2013-04-17 13:51:12', 'Show', 0),
(1426, 'man', 61, 34, '2013-04-19 16:59:52', 'Show', 0);

-- --------------------------------------------------------

--
-- Table structure for table `post_image`
--

CREATE TABLE IF NOT EXISTS `post_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `community_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `original` text NOT NULL,
  `thumbnail100` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleteStatus` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`post_id`,`community_id`,`sender_id`),
  KEY `post_id` (`post_id`),
  KEY `community_id` (`community_id`),
  KEY `sender_id` (`sender_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=218 ;

--
-- Dumping data for table `post_image`
--

INSERT INTO `post_image` (`id`, `post_id`, `community_id`, `sender_id`, `original`, `thumbnail100`, `time`, `deleteStatus`) VALUES
(207, 1423, 52, 34, 'upload/images/community/1366120883-52-0.jpg', 'upload/images/community/1366120883-52-0_thumb.jpg', '2013-04-16 14:01:23', 0),
(208, 1423, 52, 34, 'upload/images/community/1366120883-52-1.jpg', 'upload/images/community/1366120883-52-1_thumb.jpg', '2013-04-16 14:01:23', 0),
(209, 1425, 48, 34, 'upload/images/community/1366206673-48-0.jpg', 'upload/images/community/1366206673-48-0_thumb.jpg', '2013-04-17 13:51:13', 0),
(210, 1425, 48, 34, 'upload/images/community/1366206673-48-1.jpg', 'upload/images/community/1366206673-48-1_thumb.jpg', '2013-04-17 13:51:13', 0),
(211, 1425, 48, 34, 'upload/images/community/1366206673-48-2.jpg', 'upload/images/community/1366206673-48-2_thumb.jpg', '2013-04-17 13:51:13', 0),
(212, 1425, 48, 34, 'upload/images/community/1366206673-48-3.jpg', 'upload/images/community/1366206673-48-3_thumb.jpg', '2013-04-17 13:51:13', 0),
(213, 1426, 61, 34, 'upload/images/community/1366390792-61-0.jpg', 'upload/images/community/1366390792-61-0_thumb.jpg', '2013-04-19 16:59:53', 0),
(214, 1426, 61, 34, 'upload/images/community/1366390793-61-1.jpg', 'upload/images/community/1366390793-61-1_thumb.jpg', '2013-04-19 16:59:53', 0),
(215, 1426, 61, 34, 'upload/images/community/1366390793-61-2.jpg', 'upload/images/community/1366390793-61-2_thumb.jpg', '2013-04-19 16:59:53', 0),
(216, 1426, 61, 34, 'upload/images/community/1366390793-61-3.jpg', 'upload/images/community/1366390793-61-3_thumb.jpg', '2013-04-19 16:59:53', 0),
(217, 1426, 61, 34, 'upload/images/community/1366390793-61-4.png', 'upload/images/community/1366390793-61-4_thumb.png', '2013-04-19 16:59:53', 0);

-- --------------------------------------------------------

--
-- Table structure for table `privatemessae`
--

CREATE TABLE IF NOT EXISTS `privatemessae` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`,`sender_id`,`receiver_id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `profile_pix`
--

CREATE TABLE IF NOT EXISTS `profile_pix` (
  `pix_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `pix_id` (`pix_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tweakwink`
--

CREATE TABLE IF NOT EXISTS `tweakwink` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `type` varchar(1) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`,`sender_id`,`receiver_id`),
  UNIQUE KEY `id` (`id`),
  KEY `receiver_id` (`receiver_id`),
  KEY `sender_id` (`sender_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Dumping data for table `tweakwink`
--

INSERT INTO `tweakwink` (`id`, `sender_id`, `receiver_id`, `type`, `time`, `status`) VALUES
(44, 37, 34, 'W', '2013-04-15 09:39:23', 'R'),
(45, 34, 37, 'W', '2013-04-16 21:18:18', 'R'),
(46, 37, 34, 'W', '2013-04-17 07:43:51', 'I');

-- --------------------------------------------------------

--
-- Table structure for table `usercontacts`
--

CREATE TABLE IF NOT EXISTS `usercontacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username1` int(11) NOT NULL,
  `username2` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'N',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`username1`,`username2`,`sender_id`),
  KEY `username1` (`username1`),
  KEY `username2` (`username2`),
  KEY `sender_id` (`sender_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Dumping data for table `usercontacts`
--

INSERT INTO `usercontacts` (`id`, `username1`, `username2`, `sender_id`, `status`, `time`) VALUES
(43, 34, 37, 34, 'Y', '2013-04-17 07:43:40'),
(44, 34, 38, 34, 'R', '2013-04-17 10:44:08'),
(45, 34, 38, 34, 'Y', '2013-04-17 10:46:24'),
(46, 37, 38, 37, 'Y', '2013-04-17 10:46:58');

-- --------------------------------------------------------

--
-- Table structure for table `user_login_details`
--

CREATE TABLE IF NOT EXISTS `user_login_details` (
  `id` int(11) NOT NULL,
  `password` varchar(100) NOT NULL,
  `activated` varchar(1) NOT NULL DEFAULT 'N',
  `token` text NOT NULL,
  `theme_id` int(11) NOT NULL DEFAULT '1',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_login_details`
--

INSERT INTO `user_login_details` (`id`, `password`, `activated`, `token`, `theme_id`) VALUES
(34, 'c59f6373c60b995970fd4acdc6ea9cb4', 'N', 'b8b0e32f4b987ce54f3b61b25fc1e244', 1),
(37, 'c59f6373c60b995970fd4acdc6ea9cb4', 'N', '8c58c6b275049b64bb8695321448b8c3', 1),
(38, 'c59f6373c60b995970fd4acdc6ea9cb4', 'N', 'c94d28e257156b77dd08facd12e72b36', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_personal_info`
--

CREATE TABLE IF NOT EXISTS `user_personal_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(70) NOT NULL,
  `username` varchar(40) NOT NULL,
  `gender` varchar(1) NOT NULL,
  `dob` date NOT NULL,
  `dateJoined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `phone` varchar(30) NOT NULL,
  `url` text NOT NULL,
  `relationship_status` varchar(50) NOT NULL,
  `bio` text NOT NULL,
  `favquote` text NOT NULL,
  `location` text NOT NULL,
  `likes` text NOT NULL,
  `dislikes` text NOT NULL,
  `works` text NOT NULL,
  `agreement` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`email`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `user_personal_info`
--

INSERT INTO `user_personal_info` (`id`, `firstname`, `lastname`, `email`, `username`, `gender`, `dob`, `dateJoined`, `phone`, `url`, `relationship_status`, `bio`, `favquote`, `location`, `likes`, `dislikes`, `works`, `agreement`) VALUES
(34, 'Soladoye', 'Abdulrasheed', 'soladnet@gmail.com', 'soladnet', 'M', '1961-05-05', '2013-04-14 17:58:00', '', '', '', '', '', '', '', '', '', 0),
(37, 'Abdulkareem', 'Rukayyat', 'a.ruki07@yahoo.com', 'aruki07', 'F', '1988-06-30', '2013-04-14 19:44:59', '', '', '', '', '', '', '', '', '', 0),
(38, 'Ross', 'Madweke', 'ross@gmail.com', 'ross', 'M', '1962-06-03', '2013-04-17 09:24:46', '', '', '', '', '', '', '', '', '', 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `user_login_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comminvitation`
--
ALTER TABLE `comminvitation`
  ADD CONSTRAINT `comminvitation_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `user_personal_info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comminvitation_ibfk_1` FOREIGN KEY (`comid`) REFERENCES `community` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `community`
--
ALTER TABLE `community`
  ADD CONSTRAINT `community_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `user_personal_info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `community_subscribers`
--
ALTER TABLE `community_subscribers`
  ADD CONSTRAINT `community_subscribers_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user_personal_info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `community_subscribers_ibfk_2` FOREIGN KEY (`community_id`) REFERENCES `community` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_recovery`
--
ALTER TABLE `password_recovery`
  ADD CONSTRAINT `password_recovery_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_login_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pictureuploads`
--
ALTER TABLE `pictureuploads`
  ADD CONSTRAINT `pictureuploads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_personal_info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`community_id`) REFERENCES `community` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_ibfk_3` FOREIGN KEY (`sender_id`) REFERENCES `user_login_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `post_image`
--
ALTER TABLE `post_image`
  ADD CONSTRAINT `post_image_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_image_ibfk_2` FOREIGN KEY (`community_id`) REFERENCES `community` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_image_ibfk_3` FOREIGN KEY (`sender_id`) REFERENCES `user_personal_info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `privatemessae`
--
ALTER TABLE `privatemessae`
  ADD CONSTRAINT `privatemessae_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `user_login_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `privatemessae_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `user_login_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `profile_pix`
--
ALTER TABLE `profile_pix`
  ADD CONSTRAINT `profile_pix_ibfk_1` FOREIGN KEY (`pix_id`) REFERENCES `pictureuploads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `profile_pix_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_personal_info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tweakwink`
--
ALTER TABLE `tweakwink`
  ADD CONSTRAINT `tweakwink_ibfk_1` FOREIGN KEY (`receiver_id`) REFERENCES `user_login_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tweakwink_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `user_login_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `usercontacts`
--
ALTER TABLE `usercontacts`
  ADD CONSTRAINT `usercontacts_ibfk_1` FOREIGN KEY (`username1`) REFERENCES `user_personal_info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usercontacts_ibfk_2` FOREIGN KEY (`username2`) REFERENCES `user_personal_info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usercontacts_ibfk_3` FOREIGN KEY (`sender_id`) REFERENCES `user_personal_info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_login_details`
--
ALTER TABLE `user_login_details`
  ADD CONSTRAINT `user_login_details_ibfk_1` FOREIGN KEY (`id`) REFERENCES `user_personal_info` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
