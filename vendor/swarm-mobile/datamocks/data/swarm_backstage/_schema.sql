CREATE DATABASE  IF NOT EXISTS `swarm_backstage` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `swarm_backstage`;
-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: localhost    Database: swarm_backstage
-- ------------------------------------------------------
-- Server version	5.5.27-log

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
-- Table structure for table `accountmanager`
--

DROP TABLE IF EXISTS `accountmanager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accountmanager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `is_admin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `phone_no` varchar(255) DEFAULT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(2) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=243 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currency` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `label` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `device`
--

DROP TABLE IF EXISTS `device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mac` varchar(255) NOT NULL,
  `reseller_id` int(10) unsigned DEFAULT NULL,
  `location_id` int(10) unsigned DEFAULT NULL,
  `devicetype_id` tinyint(10) unsigned NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `devicestatus_id` smallint(5) NOT NULL DEFAULT '1',
  `alias` varchar(40) NOT NULL,
  `ship_date` timestamp NULL DEFAULT NULL,
  `return_date` timestamp NULL DEFAULT NULL,
  `major` int(10) unsigned DEFAULT NULL,
  `minor` int(10) unsigned DEFAULT NULL,
  `deviceenvironment_id` int(10) unsigned NOT NULL,
  `serial` varchar(255) NOT NULL DEFAULT '',
  `manufacturer_serial` varchar(255) NOT NULL DEFAULT '',
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `reseller_id` (`reseller_id`),
  KEY `location_id` (`location_id`),
  KEY `device_type_id` (`devicetype_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1840 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deviceenvironment`
--

DROP TABLE IF EXISTS `deviceenvironment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deviceenvironment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devicestatus`
--

DROP TABLE IF EXISTS `devicestatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devicestatus` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devicetype`
--

DROP TABLE IF EXISTS `devicetype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devicetype` (
  `id` tinyint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_queue`
--

DROP TABLE IF EXISTS `email_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_email` varchar(255) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `to_email` varchar(255) DEFAULT NULL,
  `to_name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_queue_processed`
--

DROP TABLE IF EXISTS `email_queue_processed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_queue_processed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_email` varchar(255) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `to_email` varchar(255) DEFAULT NULL,
  `to_name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `ts_queue` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fileextension`
--

DROP TABLE IF EXISTS `fileextension`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fileextension` (
  `id` tinyint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `extension` varchar(255) NOT NULL,
  `mime` varchar(255) NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iab1`
--

DROP TABLE IF EXISTS `iab1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iab1` (
  `id` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iab2`
--

DROP TABLE IF EXISTS `iab2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iab2` (
  `id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `iab1_id` mediumint(4) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `name_2` (`name`,`iab1_id`)
) ENGINE=InnoDB AUTO_INCREMENT=357 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibeacon_api_key`
--

DROP TABLE IF EXISTS `ibeacon_api_key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ibeacon_api_key` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `key` varchar(100) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf32;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibeacon_blacklist_ip`
--

DROP TABLE IF EXISTS `ibeacon_blacklist_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ibeacon_blacklist_ip` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibeacon_campaign_proximity_rules`
--

DROP TABLE IF EXISTS `ibeacon_campaign_proximity_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ibeacon_campaign_proximity_rules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) unsigned NOT NULL DEFAULT '0',
  `type` enum('outbound_lost','outbound_unknown','outbound_far','outbound_near','inbound_immediate','inbound_unknown','inbound_far','inbound_near') NOT NULL DEFAULT 'outbound_lost',
  `ts_create` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibeacon_campaign_scoring _rules`
--

DROP TABLE IF EXISTS `ibeacon_campaign_scoring _rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ibeacon_campaign_scoring _rules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `rule` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibeacon_campaigns`
--

DROP TABLE IF EXISTS `ibeacon_campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ibeacon_campaigns` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(256) NOT NULL DEFAULT '0',
  `total_coupons` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `ad_partner` varchar(100) DEFAULT NULL,
  `product_id` varchar(100) DEFAULT NULL,
  `minimum_score` int(11) DEFAULT NULL,
  `ts_create` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `location_id` (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibeacon_coupon_configuration`
--

DROP TABLE IF EXISTS `ibeacon_coupon_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ibeacon_coupon_configuration` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` int(11) unsigned NOT NULL DEFAULT '0',
  `image_url` varchar(512) DEFAULT NULL,
  `external_url` varchar(512) DEFAULT NULL,
  `title` varchar(256) NOT NULL,
  `text` varchar(256) NOT NULL,
  `delivery_text` varchar(256) DEFAULT NULL,
  `ts_create` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibeacon_coupons`
--

DROP TABLE IF EXISTS `ibeacon_coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ibeacon_coupons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) unsigned NOT NULL DEFAULT '0',
  `campaign_id` int(11) unsigned NOT NULL DEFAULT '0',
  `code` varchar(100) DEFAULT '0',
  `status` enum('new','accept','reject') DEFAULT 'new',
  `ts_create` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`),
  KEY `campaign_id` (`campaign_id`),
  KEY `status` (`status`),
  KEY `campaign_id_status` (`campaign_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibeacon_customer_ssv`
--

DROP TABLE IF EXISTS `ibeacon_customer_ssv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ibeacon_customer_ssv` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibeacon_customers`
--

DROP TABLE IF EXISTS `ibeacon_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ibeacon_customers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `remote_id` varchar(128) DEFAULT NULL,
  `vendor_id` varchar(100) DEFAULT NULL,
  `advertiser_id` varchar(100) DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `ts_create` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibeacon_device_coordinates`
--

DROP TABLE IF EXISTS `ibeacon_device_coordinates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ibeacon_device_coordinates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` int(11) unsigned NOT NULL DEFAULT '0',
  `latitude` double NOT NULL DEFAULT '0',
  `longitude` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `device_id` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ibeacon_rest_api_rate_limit`
--

DROP TABLE IF EXISTS `ibeacon_rest_api_rate_limit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ibeacon_rest_api_rate_limit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `type` enum('ip','api_key') COLLATE utf8_bin NOT NULL DEFAULT 'ip',
  `max_number_requests` int(9) unsigned NOT NULL DEFAULT '0',
  `period` varchar(125) COLLATE utf8_bin NOT NULL,
  `message` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `industry`
--

DROP TABLE IF EXISTS `industry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `industry` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `label` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountmanager_id` int(10) unsigned DEFAULT NULL,
  `reseller_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `accountmanager_id` (`accountmanager_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2150 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `location_employee`
--

DROP TABLE IF EXISTS `location_employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location_employee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_id` (`location_id`,`employee_id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `location_setting`
--

DROP TABLE IF EXISTS `location_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int(10) unsigned NOT NULL,
  `setting_id` int(10) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=132517 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `locationmanager`
--

DROP TABLE IF EXISTS `locationmanager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locationmanager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `phone_no` varchar(255) DEFAULT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1562 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `locationmanager_location`
--

DROP TABLE IF EXISTS `locationmanager_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locationmanager_location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int(10) unsigned NOT NULL,
  `locationmanager_id` int(10) unsigned NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_id` (`location_id`,`locationmanager_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3097 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_recovery`
--

DROP TABLE IF EXISTS `password_recovery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_recovery` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `code` varchar(40) NOT NULL,
  `ts_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ts_expire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expired` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ping`
--

DROP TABLE IF EXISTS `ping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ping` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_id` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `portal`
--

DROP TABLE IF EXISTS `portal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_id` (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=500 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pos`
--

DROP TABLE IF EXISTS `pos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `label` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presence`
--

DROP TABLE IF EXISTS `presence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `presence` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `version` varchar(255) NOT NULL,
  `network_id` int(10) unsigned NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_id` (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1160 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reseller`
--

DROP TABLE IF EXISTS `reseller`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reseller` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `accountmanager_id` int(10) unsigned DEFAULT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `address1` varchar(255) NOT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(10) NOT NULL,
  `company` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `accountmanager_id` (`accountmanager_id`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `setting`
--

DROP TABLE IF EXISTS `setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `settinggroup_id` tinyint(10) unsigned NOT NULL,
  `default` varchar(255) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `settinggroup_id` (`settinggroup_id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settinggroup`
--

DROP TABLE IF EXISTS `settinggroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settinggroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `superadmin`
--

DROP TABLE IF EXISTS `superadmin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `superadmin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone_no` varchar(255) DEFAULT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uploaddetail`
--

DROP TABLE IF EXISTS `uploaddetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploaddetail` (
  `id` tinyint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `setting_id` int(10) unsigned NOT NULL,
  `width` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `save_pattern` varchar(255) DEFAULT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uploaddetail_fileextension`
--

DROP TABLE IF EXISTS `uploaddetail_fileextension`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploaddetail_fileextension` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uploaddetail_id` tinyint(10) unsigned NOT NULL,
  `fileextension_id` tinyint(10) unsigned NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uploaddetail_id` (`uploaddetail_id`,`fileextension_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `usertype_id` tinyint(10) unsigned NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email` varchar(255) NOT NULL,
  `is_demo` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `user_type_id` (`usertype_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1711 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_location_report`
--

DROP TABLE IF EXISTS `user_location_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_location_report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `daily` tinyint(1) NOT NULL DEFAULT '0',
  `weekly` tinyint(1) NOT NULL DEFAULT '0',
  `monthly` tinyint(1) NOT NULL DEFAULT '0',
  `zero_highlights` tinyint(1) NOT NULL DEFAULT '0',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1202 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usertype`
--

DROP TABLE IF EXISTS `usertype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usertype` (
  `id` tinyint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-05 23:33:25
