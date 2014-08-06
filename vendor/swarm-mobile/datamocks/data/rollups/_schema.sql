CREATE DATABASE  IF NOT EXISTS `rollups` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `rollups`;
-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: localhost    Database: rollups
-- ------------------------------------------------------
-- Server version	5.6.17-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `avgTicket`
--

DROP TABLE IF EXISTS `avgTicket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `avgTicket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` decimal(10,2) NOT NULL,
  `total_close` decimal(10,2) NOT NULL,
  `total_total` decimal(10,2) NOT NULL,
  `h00` decimal(10,2) NOT NULL,
  `h01` decimal(10,2) NOT NULL,
  `h02` decimal(10,2) NOT NULL,
  `h03` decimal(10,2) NOT NULL,
  `h04` decimal(10,2) NOT NULL,
  `h05` decimal(10,2) NOT NULL,
  `h06` decimal(10,2) NOT NULL,
  `h07` decimal(10,2) NOT NULL,
  `h08` decimal(10,2) NOT NULL,
  `h09` decimal(10,2) NOT NULL,
  `h10` decimal(10,2) NOT NULL,
  `h11` decimal(10,2) NOT NULL,
  `h12` decimal(10,2) NOT NULL,
  `h13` decimal(10,2) NOT NULL,
  `h14` decimal(10,2) NOT NULL,
  `h15` decimal(10,2) NOT NULL,
  `h16` decimal(10,2) NOT NULL,
  `h17` decimal(10,2) NOT NULL,
  `h18` decimal(10,2) NOT NULL,
  `h19` decimal(10,2) NOT NULL,
  `h20` decimal(10,2) NOT NULL,
  `h21` decimal(10,2) NOT NULL,
  `h22` decimal(10,2) NOT NULL,
  `h23` decimal(10,2) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=10270670 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conversionRate`
--

DROP TABLE IF EXISTS `conversionRate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conversionRate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` decimal(10,2) NOT NULL,
  `total_close` decimal(10,2) NOT NULL,
  `total_total` decimal(10,2) NOT NULL,
  `h00` decimal(10,2) NOT NULL,
  `h01` decimal(10,2) NOT NULL,
  `h02` decimal(10,2) NOT NULL,
  `h03` decimal(10,2) NOT NULL,
  `h04` decimal(10,2) NOT NULL,
  `h05` decimal(10,2) NOT NULL,
  `h06` decimal(10,2) NOT NULL,
  `h07` decimal(10,2) NOT NULL,
  `h08` decimal(10,2) NOT NULL,
  `h09` decimal(10,2) NOT NULL,
  `h10` decimal(10,2) NOT NULL,
  `h11` decimal(10,2) NOT NULL,
  `h12` decimal(10,2) NOT NULL,
  `h13` decimal(10,2) NOT NULL,
  `h14` decimal(10,2) NOT NULL,
  `h15` decimal(10,2) NOT NULL,
  `h16` decimal(10,2) NOT NULL,
  `h17` decimal(10,2) NOT NULL,
  `h18` decimal(10,2) NOT NULL,
  `h19` decimal(10,2) NOT NULL,
  `h20` decimal(10,2) NOT NULL,
  `h21` decimal(10,2) NOT NULL,
  `h22` decimal(10,2) NOT NULL,
  `h23` decimal(10,2) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=10270806 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` int(10) NOT NULL,
  `total_close` int(10) NOT NULL,
  `total_total` int(10) NOT NULL,
  `h00` int(10) NOT NULL,
  `h01` int(10) NOT NULL,
  `h02` int(10) NOT NULL,
  `h03` int(10) NOT NULL,
  `h04` int(10) NOT NULL,
  `h05` int(10) NOT NULL,
  `h06` int(10) NOT NULL,
  `h07` int(10) NOT NULL,
  `h08` int(10) NOT NULL,
  `h09` int(10) NOT NULL,
  `h10` int(10) NOT NULL,
  `h11` int(10) NOT NULL,
  `h12` int(10) NOT NULL,
  `h13` int(10) NOT NULL,
  `h14` int(10) NOT NULL,
  `h15` int(10) NOT NULL,
  `h16` int(10) NOT NULL,
  `h17` int(10) NOT NULL,
  `h18` int(10) NOT NULL,
  `h19` int(10) NOT NULL,
  `h20` int(10) NOT NULL,
  `h21` int(10) NOT NULL,
  `h22` int(10) NOT NULL,
  `h23` int(10) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=20549884 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `dwell`
--

DROP TABLE IF EXISTS `dwell`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dwell` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` int(10) NOT NULL,
  `total_close` int(10) NOT NULL,
  `total_total` int(10) NOT NULL,
  `h00` int(10) unsigned NOT NULL,
  `h01` int(10) unsigned NOT NULL,
  `h02` int(10) unsigned NOT NULL,
  `h03` int(10) unsigned NOT NULL,
  `h04` int(10) unsigned NOT NULL,
  `h05` int(10) unsigned NOT NULL,
  `h06` int(10) unsigned NOT NULL,
  `h07` int(10) unsigned NOT NULL,
  `h08` int(10) unsigned NOT NULL,
  `h09` int(10) unsigned NOT NULL,
  `h10` int(10) unsigned NOT NULL,
  `h11` int(10) unsigned NOT NULL,
  `h12` int(10) unsigned NOT NULL,
  `h13` int(10) unsigned NOT NULL,
  `h14` int(10) unsigned NOT NULL,
  `h15` int(10) unsigned NOT NULL,
  `h16` int(10) unsigned NOT NULL,
  `h17` int(10) unsigned NOT NULL,
  `h18` int(10) unsigned NOT NULL,
  `h19` int(10) unsigned NOT NULL,
  `h20` int(10) unsigned NOT NULL,
  `h21` int(10) unsigned NOT NULL,
  `h22` int(10) unsigned NOT NULL,
  `h23` int(10) unsigned NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=10272494 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `footTraffic`
--

DROP TABLE IF EXISTS `footTraffic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `footTraffic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` int(10) unsigned NOT NULL,
  `total_close` int(10) unsigned NOT NULL,
  `total_total` int(10) unsigned NOT NULL,
  `h00` int(10) unsigned NOT NULL,
  `h01` int(10) unsigned NOT NULL,
  `h02` int(10) unsigned NOT NULL,
  `h03` int(10) unsigned NOT NULL,
  `h04` int(10) unsigned NOT NULL,
  `h05` int(10) unsigned NOT NULL,
  `h06` int(10) unsigned NOT NULL,
  `h07` int(10) unsigned NOT NULL,
  `h08` int(10) unsigned NOT NULL,
  `h09` int(10) unsigned NOT NULL,
  `h10` int(10) unsigned NOT NULL,
  `h11` int(10) unsigned NOT NULL,
  `h12` int(10) unsigned NOT NULL,
  `h13` int(10) unsigned NOT NULL,
  `h14` int(10) unsigned NOT NULL,
  `h15` int(10) unsigned NOT NULL,
  `h16` int(10) unsigned NOT NULL,
  `h17` int(10) unsigned NOT NULL,
  `h18` int(10) unsigned NOT NULL,
  `h19` int(10) unsigned NOT NULL,
  `h20` int(10) unsigned NOT NULL,
  `h21` int(10) unsigned NOT NULL,
  `h22` int(10) unsigned NOT NULL,
  `h23` int(10) unsigned NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=20559296 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `itemsPerTransaction`
--

DROP TABLE IF EXISTS `itemsPerTransaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itemsPerTransaction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` decimal(10,2) NOT NULL,
  `total_close` decimal(10,2) NOT NULL,
  `total_total` decimal(10,2) NOT NULL,
  `h00` decimal(10,2) NOT NULL,
  `h01` decimal(10,2) NOT NULL,
  `h02` decimal(10,2) NOT NULL,
  `h03` decimal(10,2) NOT NULL,
  `h04` decimal(10,2) NOT NULL,
  `h05` decimal(10,2) NOT NULL,
  `h06` decimal(10,2) NOT NULL,
  `h07` decimal(10,2) NOT NULL,
  `h08` decimal(10,2) NOT NULL,
  `h09` decimal(10,2) NOT NULL,
  `h10` decimal(10,2) NOT NULL,
  `h11` decimal(10,2) NOT NULL,
  `h12` decimal(10,2) NOT NULL,
  `h13` decimal(10,2) NOT NULL,
  `h14` decimal(10,2) NOT NULL,
  `h15` decimal(10,2) NOT NULL,
  `h16` decimal(10,2) NOT NULL,
  `h17` decimal(10,2) NOT NULL,
  `h18` decimal(10,2) NOT NULL,
  `h19` decimal(10,2) NOT NULL,
  `h20` decimal(10,2) NOT NULL,
  `h21` decimal(10,2) NOT NULL,
  `h22` decimal(10,2) NOT NULL,
  `h23` decimal(10,2) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=10270658 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `portalTraffic`
--

DROP TABLE IF EXISTS `portalTraffic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portalTraffic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` int(10) unsigned NOT NULL,
  `total_close` int(10) unsigned NOT NULL,
  `total_total` int(10) unsigned NOT NULL,
  `h00` int(10) unsigned NOT NULL,
  `h01` int(10) unsigned NOT NULL,
  `h02` int(10) unsigned NOT NULL,
  `h03` int(10) unsigned NOT NULL,
  `h04` int(10) unsigned NOT NULL,
  `h05` int(10) unsigned NOT NULL,
  `h06` int(10) unsigned NOT NULL,
  `h07` int(10) unsigned NOT NULL,
  `h08` int(10) unsigned NOT NULL,
  `h09` int(10) unsigned NOT NULL,
  `h10` int(10) unsigned NOT NULL,
  `h11` int(10) unsigned NOT NULL,
  `h12` int(10) unsigned NOT NULL,
  `h13` int(10) unsigned NOT NULL,
  `h14` int(10) unsigned NOT NULL,
  `h15` int(10) unsigned NOT NULL,
  `h16` int(10) unsigned NOT NULL,
  `h17` int(10) unsigned NOT NULL,
  `h18` int(10) unsigned NOT NULL,
  `h19` int(10) unsigned NOT NULL,
  `h20` int(10) unsigned NOT NULL,
  `h21` int(10) unsigned NOT NULL,
  `h22` int(10) unsigned NOT NULL,
  `h23` int(10) unsigned NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=7830851 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `requested_rollups_processed`
--

DROP TABLE IF EXISTS `requested_rollups_processed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requested_rollups_processed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int(10) unsigned NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `override` tinyint(1) DEFAULT '0',
  `rebuild` tinyint(1) DEFAULT '0',
  `reporter_email` varchar(255) NOT NULL,
  `ts_queue` timestamp NOT NULL,
  `ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `requested_rollups_queue`
--

DROP TABLE IF EXISTS `requested_rollups_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requested_rollups_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int(10) unsigned NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `override` tinyint(1) DEFAULT '0',
  `rebuild` tinyint(1) DEFAULT '0',
  `reporter_email` varchar(255) NOT NULL,
  `ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `returning`
--

DROP TABLE IF EXISTS `returning`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `returning` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` int(10) unsigned NOT NULL,
  `total_close` int(10) unsigned NOT NULL,
  `total_total` int(10) unsigned NOT NULL,
  `h00` int(10) unsigned NOT NULL,
  `h01` int(10) unsigned NOT NULL,
  `h02` int(10) unsigned NOT NULL,
  `h03` int(10) unsigned NOT NULL,
  `h04` int(10) unsigned NOT NULL,
  `h05` int(10) unsigned NOT NULL,
  `h06` int(10) unsigned NOT NULL,
  `h07` int(10) unsigned NOT NULL,
  `h08` int(10) unsigned NOT NULL,
  `h09` int(10) unsigned NOT NULL,
  `h10` int(10) unsigned NOT NULL,
  `h11` int(10) unsigned NOT NULL,
  `h12` int(10) unsigned NOT NULL,
  `h13` int(10) unsigned NOT NULL,
  `h14` int(10) unsigned NOT NULL,
  `h15` int(10) unsigned NOT NULL,
  `h16` int(10) unsigned NOT NULL,
  `h17` int(10) unsigned NOT NULL,
  `h18` int(10) unsigned NOT NULL,
  `h19` int(10) unsigned NOT NULL,
  `h20` int(10) unsigned NOT NULL,
  `h21` int(10) unsigned NOT NULL,
  `h22` int(10) unsigned NOT NULL,
  `h23` int(10) unsigned NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=10287607 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `revenue`
--

DROP TABLE IF EXISTS `revenue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `revenue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` decimal(10,2) NOT NULL,
  `total_close` decimal(10,2) NOT NULL,
  `total_total` decimal(10,2) NOT NULL,
  `h00` decimal(10,2) NOT NULL,
  `h01` decimal(10,2) NOT NULL,
  `h02` decimal(10,2) NOT NULL,
  `h03` decimal(10,2) NOT NULL,
  `h04` decimal(10,2) NOT NULL,
  `h05` decimal(10,2) NOT NULL,
  `h06` decimal(10,2) NOT NULL,
  `h07` decimal(10,2) NOT NULL,
  `h08` decimal(10,2) NOT NULL,
  `h09` decimal(10,2) NOT NULL,
  `h10` decimal(10,2) NOT NULL,
  `h11` decimal(10,2) NOT NULL,
  `h12` decimal(10,2) NOT NULL,
  `h13` decimal(10,2) NOT NULL,
  `h14` decimal(10,2) NOT NULL,
  `h15` decimal(10,2) NOT NULL,
  `h16` decimal(10,2) NOT NULL,
  `h17` decimal(10,2) NOT NULL,
  `h18` decimal(10,2) NOT NULL,
  `h19` decimal(10,2) NOT NULL,
  `h20` decimal(10,2) NOT NULL,
  `h21` decimal(10,2) NOT NULL,
  `h22` decimal(10,2) NOT NULL,
  `h23` decimal(10,2) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=20554144 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sensorTraffic`
--

DROP TABLE IF EXISTS `sensorTraffic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensorTraffic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` int(10) unsigned NOT NULL,
  `total_close` int(10) unsigned NOT NULL,
  `total_total` int(10) unsigned NOT NULL,
  `h00` int(10) unsigned NOT NULL,
  `h01` int(10) unsigned NOT NULL,
  `h02` int(10) unsigned NOT NULL,
  `h03` int(10) unsigned NOT NULL,
  `h04` int(10) unsigned NOT NULL,
  `h05` int(10) unsigned NOT NULL,
  `h06` int(10) unsigned NOT NULL,
  `h07` int(10) unsigned NOT NULL,
  `h08` int(10) unsigned NOT NULL,
  `h09` int(10) unsigned NOT NULL,
  `h10` int(10) unsigned NOT NULL,
  `h11` int(10) unsigned NOT NULL,
  `h12` int(10) unsigned NOT NULL,
  `h13` int(10) unsigned NOT NULL,
  `h14` int(10) unsigned NOT NULL,
  `h15` int(10) unsigned NOT NULL,
  `h16` int(10) unsigned NOT NULL,
  `h17` int(10) unsigned NOT NULL,
  `h18` int(10) unsigned NOT NULL,
  `h19` int(10) unsigned NOT NULL,
  `h20` int(10) unsigned NOT NULL,
  `h21` int(10) unsigned NOT NULL,
  `h22` int(10) unsigned NOT NULL,
  `h23` int(10) unsigned NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=7830809 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `timeInShop`
--

DROP TABLE IF EXISTS `timeInShop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timeInShop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` int(10) unsigned NOT NULL,
  `total_close` int(10) unsigned NOT NULL,
  `total_total` int(10) unsigned NOT NULL,
  `h00` int(10) unsigned NOT NULL,
  `h01` int(10) unsigned NOT NULL,
  `h02` int(10) unsigned NOT NULL,
  `h03` int(10) unsigned NOT NULL,
  `h04` int(10) unsigned NOT NULL,
  `h05` int(10) unsigned NOT NULL,
  `h06` int(10) unsigned NOT NULL,
  `h07` int(10) unsigned NOT NULL,
  `h08` int(10) unsigned NOT NULL,
  `h09` int(10) unsigned NOT NULL,
  `h10` int(10) unsigned NOT NULL,
  `h11` int(10) unsigned NOT NULL,
  `h12` int(10) unsigned NOT NULL,
  `h13` int(10) unsigned NOT NULL,
  `h14` int(10) unsigned NOT NULL,
  `h15` int(10) unsigned NOT NULL,
  `h16` int(10) unsigned NOT NULL,
  `h17` int(10) unsigned NOT NULL,
  `h18` int(10) unsigned NOT NULL,
  `h19` int(10) unsigned NOT NULL,
  `h20` int(10) unsigned NOT NULL,
  `h21` int(10) unsigned NOT NULL,
  `h22` int(10) unsigned NOT NULL,
  `h23` int(10) unsigned NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=20557371 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `totalItems`
--

DROP TABLE IF EXISTS `totalItems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `totalItems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` int(10) unsigned NOT NULL,
  `total_close` int(10) unsigned NOT NULL,
  `total_total` int(10) unsigned NOT NULL,
  `h00` int(10) unsigned NOT NULL,
  `h01` int(10) unsigned NOT NULL,
  `h02` int(10) unsigned NOT NULL,
  `h03` int(10) unsigned NOT NULL,
  `h04` int(10) unsigned NOT NULL,
  `h05` int(10) unsigned NOT NULL,
  `h06` int(10) unsigned NOT NULL,
  `h07` int(10) unsigned NOT NULL,
  `h08` int(10) unsigned NOT NULL,
  `h09` int(10) unsigned NOT NULL,
  `h10` int(10) unsigned NOT NULL,
  `h11` int(10) unsigned NOT NULL,
  `h12` int(10) unsigned NOT NULL,
  `h13` int(10) unsigned NOT NULL,
  `h14` int(10) unsigned NOT NULL,
  `h15` int(10) unsigned NOT NULL,
  `h16` int(10) unsigned NOT NULL,
  `h17` int(10) unsigned NOT NULL,
  `h18` int(10) unsigned NOT NULL,
  `h19` int(10) unsigned NOT NULL,
  `h20` int(10) unsigned NOT NULL,
  `h21` int(10) unsigned NOT NULL,
  `h22` int(10) unsigned NOT NULL,
  `h23` int(10) unsigned NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=30817724 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `totals`
--

DROP TABLE IF EXISTS `totals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `totals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `walkbys` int(10) NOT NULL,
  `sensorTraffic` int(10) NOT NULL,
  `transactions` int(10) NOT NULL,
  `revenue` decimal(10,2) NOT NULL,
  `totalItems` int(10) NOT NULL,
  `returning` int(10) NOT NULL,
  `footTraffic` int(10) NOT NULL,
  `timeInShop` int(10) NOT NULL,
  `traffic` int(10) NOT NULL,
  `devices` int(10) NOT NULL,
  `itemsPerTransaction` decimal(10,2) NOT NULL,
  `windowConversion` decimal(10,2) NOT NULL,
  `avgTicket` decimal(10,2) NOT NULL,
  `conversionRate` decimal(10,2) NOT NULL,
  `dwell` int(10) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `portalTraffic` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=10588098 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `traffic`
--

DROP TABLE IF EXISTS `traffic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traffic` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` int(10) unsigned NOT NULL,
  `total_close` int(10) unsigned NOT NULL,
  `total_total` int(10) unsigned NOT NULL,
  `h00` int(10) unsigned NOT NULL,
  `h01` int(10) unsigned NOT NULL,
  `h02` int(10) unsigned NOT NULL,
  `h03` int(10) unsigned NOT NULL,
  `h04` int(10) unsigned NOT NULL,
  `h05` int(10) unsigned NOT NULL,
  `h06` int(10) unsigned NOT NULL,
  `h07` int(10) unsigned NOT NULL,
  `h08` int(10) unsigned NOT NULL,
  `h09` int(10) unsigned NOT NULL,
  `h10` int(10) unsigned NOT NULL,
  `h11` int(10) unsigned NOT NULL,
  `h12` int(10) unsigned NOT NULL,
  `h13` int(10) unsigned NOT NULL,
  `h14` int(10) unsigned NOT NULL,
  `h15` int(10) unsigned NOT NULL,
  `h16` int(10) unsigned NOT NULL,
  `h17` int(10) unsigned NOT NULL,
  `h18` int(10) unsigned NOT NULL,
  `h19` int(10) unsigned NOT NULL,
  `h20` int(10) unsigned NOT NULL,
  `h21` int(10) unsigned NOT NULL,
  `h22` int(10) unsigned NOT NULL,
  `h23` int(10) unsigned NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=30828793 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` int(10) unsigned NOT NULL,
  `total_close` int(10) unsigned NOT NULL,
  `total_total` int(10) unsigned NOT NULL,
  `h00` int(10) unsigned NOT NULL,
  `h01` int(10) unsigned NOT NULL,
  `h02` int(10) unsigned NOT NULL,
  `h03` int(10) unsigned NOT NULL,
  `h04` int(10) unsigned NOT NULL,
  `h05` int(10) unsigned NOT NULL,
  `h06` int(10) unsigned NOT NULL,
  `h07` int(10) unsigned NOT NULL,
  `h08` int(10) unsigned NOT NULL,
  `h09` int(10) unsigned NOT NULL,
  `h10` int(10) unsigned NOT NULL,
  `h11` int(10) unsigned NOT NULL,
  `h12` int(10) unsigned NOT NULL,
  `h13` int(10) unsigned NOT NULL,
  `h14` int(10) unsigned NOT NULL,
  `h15` int(10) unsigned NOT NULL,
  `h16` int(10) unsigned NOT NULL,
  `h17` int(10) unsigned NOT NULL,
  `h18` int(10) unsigned NOT NULL,
  `h19` int(10) unsigned NOT NULL,
  `h20` int(10) unsigned NOT NULL,
  `h21` int(10) unsigned NOT NULL,
  `h22` int(10) unsigned NOT NULL,
  `h23` int(10) unsigned NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=41106168 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `walkbys`
--

DROP TABLE IF EXISTS `walkbys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `walkbys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` int(10) unsigned NOT NULL,
  `total_close` int(10) unsigned NOT NULL,
  `total_total` int(10) unsigned NOT NULL,
  `h00` int(10) unsigned NOT NULL,
  `h01` int(10) unsigned NOT NULL,
  `h02` int(10) unsigned NOT NULL,
  `h03` int(10) unsigned NOT NULL,
  `h04` int(10) unsigned NOT NULL,
  `h05` int(10) unsigned NOT NULL,
  `h06` int(10) unsigned NOT NULL,
  `h07` int(10) unsigned NOT NULL,
  `h08` int(10) unsigned NOT NULL,
  `h09` int(10) unsigned NOT NULL,
  `h10` int(10) unsigned NOT NULL,
  `h11` int(10) unsigned NOT NULL,
  `h12` int(10) unsigned NOT NULL,
  `h13` int(10) unsigned NOT NULL,
  `h14` int(10) unsigned NOT NULL,
  `h15` int(10) unsigned NOT NULL,
  `h16` int(10) unsigned NOT NULL,
  `h17` int(10) unsigned NOT NULL,
  `h18` int(10) unsigned NOT NULL,
  `h19` int(10) unsigned NOT NULL,
  `h20` int(10) unsigned NOT NULL,
  `h21` int(10) unsigned NOT NULL,
  `h22` int(10) unsigned NOT NULL,
  `h23` int(10) unsigned NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=10287518 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `windowConversion`
--

DROP TABLE IF EXISTS `windowConversion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `windowConversion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `total_open` decimal(10,2) NOT NULL,
  `total_close` decimal(10,2) NOT NULL,
  `total_total` decimal(10,2) NOT NULL,
  `h00` decimal(10,2) NOT NULL,
  `h01` decimal(10,2) NOT NULL,
  `h02` decimal(10,2) NOT NULL,
  `h03` decimal(10,2) NOT NULL,
  `h04` decimal(10,2) NOT NULL,
  `h05` decimal(10,2) NOT NULL,
  `h06` decimal(10,2) NOT NULL,
  `h07` decimal(10,2) NOT NULL,
  `h08` decimal(10,2) NOT NULL,
  `h09` decimal(10,2) NOT NULL,
  `h10` decimal(10,2) NOT NULL,
  `h11` decimal(10,2) NOT NULL,
  `h12` decimal(10,2) NOT NULL,
  `h13` decimal(10,2) NOT NULL,
  `h14` decimal(10,2) NOT NULL,
  `h15` decimal(10,2) NOT NULL,
  `h16` decimal(10,2) NOT NULL,
  `h17` decimal(10,2) NOT NULL,
  `h18` decimal(10,2) NOT NULL,
  `h19` decimal(10,2) NOT NULL,
  `h20` decimal(10,2) NOT NULL,
  `h21` decimal(10,2) NOT NULL,
  `h22` decimal(10,2) NOT NULL,
  `h23` decimal(10,2) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB AUTO_INCREMENT=10272088 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-05 23:30:59
