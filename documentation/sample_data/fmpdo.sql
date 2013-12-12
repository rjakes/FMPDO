# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.1.66)
# Database: fmpdo
# Generation Time: 2013-12-11 22:19:26 -0800
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table category
# ------------------------------------------------------------

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `date_created` date DEFAULT NULL,
  `timestamp_modified` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `time_created` time DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;

INSERT INTO `category` (`id`, `name`, `type`, `date_created`, `timestamp_modified`, `time_created`)
VALUES
	(1,'Belmont','city','2013-02-01','2013-12-11 22:06:19','08:09:00'),
	(2,'Palo Alto','town','2013-12-03','2013-12-11 21:47:10','08:10:01'),
	(3,'California','state','2013-12-04','2013-12-11 21:47:26','08:15:13'),
	(4,'Mexico','country','2013-12-05','2013-12-11 21:47:42','08:16:23'),
	(5,'San Mateo','city','2013-12-06','2013-12-11 21:47:48','00:00:00'),
	(6,'Austin','city','2013-12-17','2013-12-11 21:47:58','08:22:23'),
	(41,NULL,NULL,'2013-12-18','2013-12-11 21:48:02','08:23:23'),
	(37,NULL,'town','2013-12-18','2013-12-11 21:48:31','09:16:23'),
	(42,'Bristol','city','2013-12-11','2013-12-11 21:49:07','09:33:23'),
	(43,'Brazil','country','2013-12-11','2013-12-11 19:09:29','18:01:10'),
	(44,'Germany','country','2013-12-11',NULL,'19:08:25'),
	(57,'Wolcott','town','2013-12-11',NULL,'22:06:19');

/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

DELIMITER ;;
/*!50003 SET SESSION SQL_MODE="" */;;
/*!50003 CREATE */ /*!50017 DEFINER=`root`@`localhost` */ /*!50003 TRIGGER `on_create` BEFORE INSERT ON `category` FOR EACH ROW BEGIN
SET NEW.time_created = NOW();
SET NEW.date_created = CURDATE();
END */;;
DELIMITER ;
/*!50003 SET SESSION SQL_MODE=@OLD_SQL_MODE */;


# Dump of table contact
# ------------------------------------------------------------

DROP TABLE IF EXISTS `contact`;

CREATE TABLE `contact` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name_first` varchar(255) DEFAULT NULL,
  `name_last` varchar(255) DEFAULT NULL,
  `phone_home` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;

INSERT INTO `contact` (`id`, `name_first`, `name_last`, `phone_home`)
VALUES
	(1,'Fred','Flintstone','1800FLINSTO');

/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
