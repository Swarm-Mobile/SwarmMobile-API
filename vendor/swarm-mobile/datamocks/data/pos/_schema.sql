CREATE DATABASE  IF NOT EXISTS `pos_production` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `pos_production`;
-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: localhost    Database: pos_production
-- ------------------------------------------------------
-- Server version	5.5.31-log

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
-- Table structure for table `BATCH_JOB_EXECUTION`
--

DROP TABLE IF EXISTS `BATCH_JOB_EXECUTION`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BATCH_JOB_EXECUTION` (
  `JOB_EXECUTION_ID` bigint(20) NOT NULL,
  `VERSION` bigint(20) DEFAULT NULL,
  `JOB_INSTANCE_ID` bigint(20) NOT NULL,
  `CREATE_TIME` datetime NOT NULL,
  `START_TIME` datetime DEFAULT NULL,
  `END_TIME` datetime DEFAULT NULL,
  `STATUS` varchar(10) DEFAULT NULL,
  `EXIT_CODE` varchar(100) DEFAULT NULL,
  `EXIT_MESSAGE` varchar(2500) DEFAULT NULL,
  `LAST_UPDATED` datetime DEFAULT NULL,
  PRIMARY KEY (`JOB_EXECUTION_ID`),
  KEY `JOB_INST_EXEC_FK` (`JOB_INSTANCE_ID`),
  CONSTRAINT `JOB_INST_EXEC_FK` FOREIGN KEY (`JOB_INSTANCE_ID`) REFERENCES `BATCH_JOB_INSTANCE` (`JOB_INSTANCE_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BATCH_JOB_EXECUTION_CONTEXT`
--

DROP TABLE IF EXISTS `BATCH_JOB_EXECUTION_CONTEXT`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BATCH_JOB_EXECUTION_CONTEXT` (
  `JOB_EXECUTION_ID` bigint(20) NOT NULL,
  `SHORT_CONTEXT` varchar(2500) NOT NULL,
  `SERIALIZED_CONTEXT` text,
  PRIMARY KEY (`JOB_EXECUTION_ID`),
  CONSTRAINT `JOB_EXEC_CTX_FK` FOREIGN KEY (`JOB_EXECUTION_ID`) REFERENCES `BATCH_JOB_EXECUTION` (`JOB_EXECUTION_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BATCH_JOB_EXECUTION_PARAMS`
--

DROP TABLE IF EXISTS `BATCH_JOB_EXECUTION_PARAMS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BATCH_JOB_EXECUTION_PARAMS` (
  `JOB_EXECUTION_ID` bigint(20) NOT NULL,
  `TYPE_CD` varchar(6) NOT NULL,
  `KEY_NAME` varchar(100) NOT NULL,
  `STRING_VAL` varchar(250) DEFAULT NULL,
  `DATE_VAL` datetime DEFAULT NULL,
  `LONG_VAL` bigint(20) DEFAULT NULL,
  `DOUBLE_VAL` double DEFAULT NULL,
  `IDENTIFYING` char(1) NOT NULL,
  KEY `JOB_EXEC_PARAMS_FK` (`JOB_EXECUTION_ID`),
  CONSTRAINT `JOB_EXEC_PARAMS_FK` FOREIGN KEY (`JOB_EXECUTION_ID`) REFERENCES `BATCH_JOB_EXECUTION` (`JOB_EXECUTION_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BATCH_JOB_EXECUTION_SEQ`
--

DROP TABLE IF EXISTS `BATCH_JOB_EXECUTION_SEQ`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BATCH_JOB_EXECUTION_SEQ` (
  `ID` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BATCH_JOB_INSTANCE`
--

DROP TABLE IF EXISTS `BATCH_JOB_INSTANCE`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BATCH_JOB_INSTANCE` (
  `JOB_INSTANCE_ID` bigint(20) NOT NULL,
  `VERSION` bigint(20) DEFAULT NULL,
  `JOB_NAME` varchar(100) NOT NULL,
  `JOB_KEY` varchar(32) NOT NULL,
  PRIMARY KEY (`JOB_INSTANCE_ID`),
  UNIQUE KEY `JOB_INST_UN` (`JOB_NAME`,`JOB_KEY`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BATCH_JOB_SEQ`
--

DROP TABLE IF EXISTS `BATCH_JOB_SEQ`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BATCH_JOB_SEQ` (
  `ID` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BATCH_STEP_EXECUTION`
--

DROP TABLE IF EXISTS `BATCH_STEP_EXECUTION`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BATCH_STEP_EXECUTION` (
  `STEP_EXECUTION_ID` bigint(20) NOT NULL,
  `VERSION` bigint(20) NOT NULL,
  `STEP_NAME` varchar(100) NOT NULL,
  `JOB_EXECUTION_ID` bigint(20) NOT NULL,
  `START_TIME` datetime NOT NULL,
  `END_TIME` datetime DEFAULT NULL,
  `STATUS` varchar(10) DEFAULT NULL,
  `COMMIT_COUNT` bigint(20) DEFAULT NULL,
  `READ_COUNT` bigint(20) DEFAULT NULL,
  `FILTER_COUNT` bigint(20) DEFAULT NULL,
  `WRITE_COUNT` bigint(20) DEFAULT NULL,
  `READ_SKIP_COUNT` bigint(20) DEFAULT NULL,
  `WRITE_SKIP_COUNT` bigint(20) DEFAULT NULL,
  `PROCESS_SKIP_COUNT` bigint(20) DEFAULT NULL,
  `ROLLBACK_COUNT` bigint(20) DEFAULT NULL,
  `EXIT_CODE` varchar(100) DEFAULT NULL,
  `EXIT_MESSAGE` varchar(2500) DEFAULT NULL,
  `LAST_UPDATED` datetime DEFAULT NULL,
  PRIMARY KEY (`STEP_EXECUTION_ID`),
  KEY `JOB_EXEC_STEP_FK` (`JOB_EXECUTION_ID`),
  CONSTRAINT `JOB_EXEC_STEP_FK` FOREIGN KEY (`JOB_EXECUTION_ID`) REFERENCES `BATCH_JOB_EXECUTION` (`JOB_EXECUTION_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BATCH_STEP_EXECUTION_CONTEXT`
--

DROP TABLE IF EXISTS `BATCH_STEP_EXECUTION_CONTEXT`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BATCH_STEP_EXECUTION_CONTEXT` (
  `STEP_EXECUTION_ID` bigint(20) NOT NULL,
  `SHORT_CONTEXT` varchar(2500) NOT NULL,
  `SERIALIZED_CONTEXT` text,
  PRIMARY KEY (`STEP_EXECUTION_ID`),
  CONSTRAINT `STEP_EXEC_CTX_FK` FOREIGN KEY (`STEP_EXECUTION_ID`) REFERENCES `BATCH_STEP_EXECUTION` (`STEP_EXECUTION_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `BATCH_STEP_EXECUTION_SEQ`
--

DROP TABLE IF EXISTS `BATCH_STEP_EXECUTION_SEQ`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BATCH_STEP_EXECUTION_SEQ` (
  `ID` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `DATABASECHANGELOG`
--

DROP TABLE IF EXISTS `DATABASECHANGELOG`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DATABASECHANGELOG` (
  `ID` varchar(63) NOT NULL,
  `AUTHOR` varchar(63) NOT NULL,
  `FILENAME` varchar(200) NOT NULL,
  `DATEEXECUTED` datetime NOT NULL,
  `ORDEREXECUTED` int(11) NOT NULL,
  `EXECTYPE` varchar(10) NOT NULL,
  `MD5SUM` varchar(35) DEFAULT NULL,
  `DESCRIPTION` varchar(255) DEFAULT NULL,
  `COMMENTS` varchar(255) DEFAULT NULL,
  `TAG` varchar(255) DEFAULT NULL,
  `LIQUIBASE` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ID`,`AUTHOR`,`FILENAME`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `DATABASECHANGELOGLOCK`
--

DROP TABLE IF EXISTS `DATABASECHANGELOGLOCK`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DATABASECHANGELOGLOCK` (
  `ID` int(11) NOT NULL,
  `LOCKED` tinyint(1) NOT NULL,
  `LOCKGRANTED` datetime DEFAULT NULL,
  `LOCKEDBY` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `apis`
--

DROP TABLE IF EXISTS `apis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `apis` (
  `api_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `notes` text,
  UNIQUE KEY `id` (`api_id`),
  UNIQUE KEY `api_id` (`api_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `category_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint(20) unsigned NOT NULL,
  `ls_category_id` bigint(64) DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `lft` bigint(20) unsigned DEFAULT NULL,
  `rgt` bigint(20) unsigned DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `category_id` (`category_id`),
  UNIQUE KEY `category_id_2` (`category_id`),
  KEY `categoriess_storeid_lscategoryid_idx` (`store_id`,`ls_category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17016 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `customer_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ls_customer_id` bigint(64) DEFAULT NULL,
  `store_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `postcode` varchar(15) DEFAULT NULL,
  `notes` text,
  `uuid` char(36) DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  UNIQUE KEY `customer_id` (`customer_id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `customers_storeid_lscustomerid_idx` (`store_id`,`ls_customer_id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9201149 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `error_log`
--

DROP TABLE IF EXISTS `error_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `error_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint(20) unsigned DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `error` varchar(100) DEFAULT NULL,
  `details` text,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=965476 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `g9_import_logs`
--

DROP TABLE IF EXISTS `g9_import_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `g9_import_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `logfile` varchar(63) NOT NULL,
  `completed` tinyint(1) DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=432 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice_lines`
--

DROP TABLE IF EXISTS `invoice_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_lines` (
  `invoice_line_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `ls_line_id` bigint(64) DEFAULT NULL,
  `ls_product_id` bigint(64) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `class` varchar(100) DEFAULT NULL,
  `family` varchar(100) DEFAULT NULL,
  `quantity` smallint(5) unsigned DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `tax` decimal(8,2) DEFAULT NULL,
  `ts` timestamp NULL DEFAULT NULL,
  `store_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uuid` char(36) DEFAULT NULL,
  UNIQUE KEY `invoice_line_id` (`invoice_line_id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `invoice_id` (`invoice_id`),
  KEY `idx_storeid_lslineid` (`store_id`,`ls_line_id`)
) ENGINE=InnoDB AUTO_INCREMENT=111037665 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `invoice_lines_deleted_103`
--

DROP TABLE IF EXISTS `invoice_lines_deleted_103`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_lines_deleted_103` (
  `invoice_line_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `invoice_id` bigint(20) unsigned NOT NULL,
  `ls_line_id` bigint(20) unsigned NOT NULL,
  `ls_product_id` bigint(20) unsigned DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `class` varchar(100) DEFAULT NULL,
  `family` varchar(100) DEFAULT NULL,
  `quantity` smallint(5) unsigned DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `tax` decimal(8,2) DEFAULT NULL,
  `ts` timestamp NULL DEFAULT NULL,
  `store_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uuid` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `invoice_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint(20) unsigned NOT NULL,
  `ls_invoice_id` bigint(64) DEFAULT NULL,
  `customer_id` bigint(64) DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tsfraction` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `mainphone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `total` decimal(8,2) DEFAULT NULL,
  `completed` tinyint(1) DEFAULT '0',
  `lines_processed` tinyint(1) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `outlet_id` bigint(20) unsigned DEFAULT NULL,
  `register_id` bigint(20) unsigned DEFAULT NULL,
  `ls_outlet_id` bigint(20) unsigned DEFAULT NULL,
  `ls_register_id` bigint(20) unsigned DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  `test_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified` datetime DEFAULT NULL,
  UNIQUE KEY `invoice_id` (`invoice_id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `store_id` (`store_id`),
  KEY `idx_ls_invoice_id` (`ls_invoice_id`)  
) ENGINE=InnoDB AUTO_INCREMENT=7459807 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `invoices_deleted_103`
--

DROP TABLE IF EXISTS `invoices_deleted_103`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices_deleted_103` (
  `invoice_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `store_id` bigint(20) unsigned NOT NULL,
  `ls_invoice_id` bigint(20) unsigned NOT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tsfraction` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `mainphone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `total` decimal(8,2) DEFAULT NULL,
  `completed` tinyint(1) DEFAULT '0',
  `lines_processed` tinyint(1) DEFAULT NULL,
  `test_ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `outlet_id` bigint(20) unsigned DEFAULT NULL,
  `register_id` bigint(20) unsigned DEFAULT NULL,
  `ls_outlet_id` bigint(20) unsigned DEFAULT NULL,
  `ls_register_id` bigint(20) unsigned DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturers`
--

DROP TABLE IF EXISTS `manufacturers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manufacturers` (
  `manufacturer_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ls_manufacturer_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  UNIQUE KEY `id` (`manufacturer_id`),
  UNIQUE KEY `manufacturer_id` (`manufacturer_id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=7477 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `outlets`
--

DROP TABLE IF EXISTS `outlets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `outlets` (
  `outlet_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ls_outlet_id` bigint(20) unsigned DEFAULT NULL,
  `store_id` bigint(20) unsigned NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `address1` varchar(50) DEFAULT NULL,
  `address2` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `postcode` varchar(15) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  UNIQUE KEY `outlet_id` (`outlet_id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=273 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `product_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ls_product_id` bigint(64) DEFAULT NULL,
  `store_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `sku` varchar(50) DEFAULT NULL,
  `store_sku` varchar(50) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `manufacturer` varchar(50) DEFAULT NULL,
  `upc` varchar(14) DEFAULT NULL,
  `ean` varchar(14) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  UNIQUE KEY `product_id` (`product_id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `products_storeid_lsproductid_idx` (`store_id`,`ls_product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7153456 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `registers`
--

DROP TABLE IF EXISTS `registers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registers` (
  `register_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint(20) unsigned DEFAULT NULL,
  `ls_register_id` bigint(20) unsigned DEFAULT NULL,
  `outlet_id` bigint(20) unsigned NOT NULL,
  `ls_outlet_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  UNIQUE KEY `register_id` (`register_id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=431 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `retailpro_client`
--

DROP TABLE IF EXISTS `retailpro_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retailpro_client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `swarm_id` varchar(255) DEFAULT NULL,
  `component_id` varchar(20) DEFAULT NULL,
  `component_type` varchar(20) DEFAULT NULL,
  `rp_version` varchar(20) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `install_date` datetime DEFAULT NULL,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_RETAILPRO_CLIENT_SWARM_ID_AND_COMPONENT_ID` (`swarm_id`,`component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `retailpro_configuration`
--

DROP TABLE IF EXISTS `retailpro_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retailpro_configuration` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `swarm_id` varchar(255) DEFAULT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `invoice_date` datetime DEFAULT NULL,
  `store_date` datetime DEFAULT NULL,
  `version_date` datetime DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `retailpro_plugin`
--

DROP TABLE IF EXISTS `retailpro_plugin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retailpro_plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `swarm_id` varchar(255) DEFAULT NULL,
  `heartbeat` datetime DEFAULT NULL,
  `plugin_version` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_RETAILPRO_PLUGIN_SWARM_ID` (`swarm_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `staging_categories`
--

DROP TABLE IF EXISTS `staging_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staging_categories` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ls_category_id` varchar(100) DEFAULT NULL,
  `stage_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `swarm_id` varchar(255) DEFAULT NULL,
  `store_id` varchar(100) DEFAULT NULL,
  `parent_id` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `ls_lft` varchar(100) DEFAULT NULL,
  `ls_rgt` varchar(100) DEFAULT NULL,
  `last_modified` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=106012 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `staging_customers`
--

DROP TABLE IF EXISTS `staging_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staging_customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ls_store_no` varchar(100) DEFAULT NULL,
  `ls_sbs_no` varchar(100) DEFAULT NULL,
  `swarm_id` varchar(255) DEFAULT NULL,
  `ls_customer_id` varchar(100) DEFAULT NULL,
  `stage_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(100) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address1` varchar(100) DEFAULT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `notes` text,
  `store_id` bigint(20) unsigned DEFAULT NULL,
  `last_modified` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3908197 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `staging_invoice_lines`
--

DROP TABLE IF EXISTS `staging_invoice_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staging_invoice_lines` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ls_invoice_id` varchar(100) DEFAULT NULL,
  `ls_line_id` varchar(100) DEFAULT NULL,
  `ls_product_id` varchar(100) DEFAULT NULL,
  `swarm_id` varchar(255) DEFAULT NULL,
  `stage_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `quantity` varchar(10) DEFAULT NULL,
  `price` varchar(20) DEFAULT NULL,
  `tax` varchar(10) DEFAULT NULL,
  `store_id` bigint(20) unsigned DEFAULT NULL,
  `ls_store_no` varchar(100) DEFAULT NULL,
  `ls_sbs_no` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41893380 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `staging_invoices`
--

DROP TABLE IF EXISTS `staging_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staging_invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ls_store_no` varchar(100) DEFAULT NULL,
  `ls_sbs_no` varchar(100) DEFAULT NULL,
  `swarm_id` varchar(255) DEFAULT NULL,
  `ls_invoice_id` varchar(100) DEFAULT NULL,
  `ls_customer_id` varchar(100) DEFAULT NULL,
  `stage_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `invoice_no` varchar(50) DEFAULT NULL,
  `ts` varchar(40) DEFAULT NULL,
  `total` varchar(20) DEFAULT NULL,
  `store_id` bigint(20) unsigned DEFAULT NULL,
  `completed` varchar(31) DEFAULT NULL,
  `so_number` varchar(100) DEFAULT NULL,
  `receipt_type` varchar(20) DEFAULT NULL,
  `receipt_status` varchar(20) DEFAULT NULL,
  `tender` varchar(20) DEFAULT NULL,
  `last_modified` varchar(255) DEFAULT NULL,
  `lines_processed` varchar(31) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14710933 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `staging_manufacturers`
--

DROP TABLE IF EXISTS `staging_manufacturers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staging_manufacturers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `swarm_id` varchar(255) DEFAULT NULL,
  `store_id` bigint(20) DEFAULT NULL,
  `ls_manufacturer_id` varchar(100) DEFAULT NULL,
  `stage_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` varchar(100) DEFAULT NULL,
  `last_modified` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `staging_products`
--

DROP TABLE IF EXISTS `staging_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staging_products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ls_product_id` varchar(100) DEFAULT NULL,
  `ls_store_no` varchar(100) DEFAULT NULL,
  `ls_sbs_no` varchar(100) DEFAULT NULL,
  `swarm_id` varchar(255) DEFAULT NULL,
  `stage_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sku` varchar(50) DEFAULT NULL,
  `store_sku` varchar(50) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `manufacturer` varchar(50) DEFAULT NULL,
  `upc` varchar(14) DEFAULT NULL,
  `ean` varchar(14) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `price` varchar(20) DEFAULT NULL,
  `last_modified` varchar(40) DEFAULT NULL,
  `store_id` bigint(20) unsigned DEFAULT NULL,
  `ls_manufacturer_id` bigint(20) unsigned DEFAULT NULL,
  `ls_category_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21247119 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stores`
--

DROP TABLE IF EXISTS `stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stores` (
  `store_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `api_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `api_url` varbinary(256) DEFAULT NULL,
  `api_key` varbinary(112) DEFAULT NULL,
  `username` varbinary(112) DEFAULT NULL,
  `password` varbinary(256) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT NULL,
  `notes` text,
  `tz` char(64) DEFAULT NULL,
  `account_id` bigint(20) unsigned DEFAULT NULL,
  `store_filter` varchar(100) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  `oauth_token` varbinary(256) DEFAULT NULL,
  UNIQUE KEY `store_id` (`store_id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=1324 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `stores_rp`
--

DROP TABLE IF EXISTS `stores_rp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stores_rp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ls_store_no` varchar(100) DEFAULT NULL,
  `ls_sbs_no` varchar(100) DEFAULT NULL,
  `swarm_id` varchar(255) DEFAULT NULL,
  `store_name` varchar(255) DEFAULT NULL,
  `store_id` bigint(20) DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `time_offset` bigint(20) DEFAULT NULL,
  `pos_software` varchar(255) DEFAULT NULL,
  `pos_timezone` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `state` varchar(255) DEFAULT 'NORMAL',
  PRIMARY KEY (`id`),
  KEY `RP_STORE_IDENTIFIERS` (`swarm_id`,`ls_store_no`,`ls_sbs_no`)
) ENGINE=InnoDB AUTO_INCREMENT=337 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `updates`
--

DROP TABLE IF EXISTS `updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `updates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint(20) unsigned DEFAULT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` timestamp NULL DEFAULT NULL,
  `imported` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `id` (`id`),
  KEY `store_id` (`store_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1358721 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vend_ids`
--

DROP TABLE IF EXISTS `vend_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vend_ids` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL,
  `type` varchar(15) DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=8840601 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-05 23:34:24
