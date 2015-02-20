# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host:testdb (MySQL 5.5.34-log)
# Database: twaFramework DB
# Generation Time: 2013-10-19 22:52:34 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table #__global_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__global_settings`;

CREATE TABLE `#__global_settings` (
  `twa_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting` varchar(255) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`twa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table #__group_access
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__group_access`;

CREATE TABLE `#__group_access` (
  `twa_id` int(11) NOT NULL AUTO_INCREMENT,
  `access_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`twa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table #__user_accesslvl
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__user_accesslvl`;

CREATE TABLE `#__user_accesslvl` (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `access_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`access_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table #__user_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__user_group`;

CREATE TABLE `#__user_group` (
  `twa_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`twa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table #__user_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__user_groups`;

CREATE TABLE `#__user_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) DEFAULT NULL,
  `redirect_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table #__users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__users`;

CREATE TABLE `#__users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(500) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_logged_in` datetime DEFAULT NULL,
  `approved` int(11) DEFAULT NULL,
  `blocked` int(11) DEFAULT NULL,
  `cookie` varchar(255) DEFAULT NULL,
  `last_updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Dump of table #__basic_auth
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__basic_auth`;

CREATE TABLE `#__basic_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `api_key` varchar(255) DEFAULT NULL,
  `secret` varchar(500) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_logged_in` datetime DEFAULT NULL,
  `approved` int(11) DEFAULT NULL,
  `blocked` int(11) DEFAULT NULL,
  `last_updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table #__blocked_users_list
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__blocked_users_list`;

CREATE TABLE `#__blocked_users_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

# Dump of table #__user_social
# ------------------------------------------------------------

DROP TABLE IF EXISTS `#__user_social`;

CREATE TABLE `#__user_social` (
  `user_id` int(11) unsigned NOT NULL,
  `fb_id` varchar(255) DEFAULT NULL,
  `gplus_id` varchar(255) DEFAULT NULL,
  `twitter_id` varchar(255) DEFAULT NULL,
  `linkedin_id` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `last_updated_on` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
