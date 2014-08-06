CREATE DATABASE  IF NOT EXISTS `swarmdata` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `swarmdata`;
-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: localhost    Database: swarmdata
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
-- Table structure for table `access_log`
--

DROP TABLE IF EXISTS `access_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time_since_epoch` decimal(15,3) DEFAULT NULL,
  `response_time` int(11) DEFAULT NULL,
  `client_src_ip_addr` char(15) DEFAULT NULL,
  `squid_request_status` varchar(20) DEFAULT NULL,
  `http_status_code` varchar(10) DEFAULT NULL,
  `reply_size` int(11) DEFAULT NULL,
  `request_method` varchar(20) DEFAULT NULL,
  `request_url` varchar(1000) DEFAULT NULL,
  `username` varchar(20) DEFAULT NULL,
  `squid_hier_status` varchar(20) DEFAULT NULL,
  `server_ip_addr` char(15) DEFAULT NULL,
  `mime_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=535 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `analytics`
--

DROP TABLE IF EXISTS `analytics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `analytics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `netid` int(10) unsigned NOT NULL,
  `nodeid` int(10) unsigned NOT NULL,
  `macid` int(11) NOT NULL,
  `sessid` int(11) NOT NULL,
  `domain` varchar(100) NOT NULL,
  `url` varchar(100) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=481411 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `macid` int(11) NOT NULL,
  `netid` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mac_net` (`macid`,`netid`)
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_log`
--

DROP TABLE IF EXISTS `event_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `log_data` varchar(100) DEFAULT NULL,
  `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hourly_data_rollup`
--

DROP TABLE IF EXISTS `hourly_data_rollup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hourly_data_rollup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL,
  `network_id` int(10) unsigned NOT NULL,
  `local_date` date NOT NULL,
  `local_hour` tinyint(3) unsigned NOT NULL,
  `concurrent` int(11) DEFAULT NULL,
  `returning` int(11) DEFAULT NULL,
  `walkbys` int(11) DEFAULT NULL,
  `sensor_sessions` int(11) DEFAULT '0',
  `avg_dwell` varchar(16) DEFAULT NULL,
  `dwell_seconds` int(10) unsigned DEFAULT '0',
  `dwell_count` int(10) unsigned DEFAULT '0',
  `transactions` int(11) DEFAULT NULL,
  `revenue` decimal(9,2) DEFAULT NULL,
  `avg_ticket` decimal(9,2) DEFAULT NULL,
  `avg_item_count` decimal(9,2) DEFAULT NULL,
  `conversion_rate` decimal(5,2) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hourly_data_unique_rec_idx` (`network_id`,`member_id`,`local_date`,`local_hour`),
  KEY `hourly_data_rollup_date_idx` (`local_date`)
) ENGINE=InnoDB AUTO_INCREMENT=44824321 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hourly_data_rollup_bak`
--

DROP TABLE IF EXISTS `hourly_data_rollup_bak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hourly_data_rollup_bak` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `member_id` int(10) unsigned NOT NULL,
  `network_id` int(10) unsigned NOT NULL,
  `local_date` date NOT NULL,
  `local_hour` tinyint(3) unsigned NOT NULL,
  `concurrent` int(11) DEFAULT NULL,
  `returning` int(11) DEFAULT NULL,
  `walkbys` int(11) DEFAULT NULL,
  `avg_dwell` varchar(16) CHARACTER SET utf8 DEFAULT NULL,
  `dwell_seconds` int(10) unsigned DEFAULT '0',
  `dwell_count` int(10) unsigned DEFAULT '0',
  `transactions` int(11) DEFAULT NULL,
  `revenue` decimal(9,2) DEFAULT NULL,
  `avg_ticket` decimal(9,2) DEFAULT NULL,
  `avg_item_count` decimal(9,2) DEFAULT NULL,
  `conversion_rate` decimal(5,2) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logins`
--

DROP TABLE IF EXISTS `logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `network_id` int(11) NOT NULL,
  `mac_id` int(11) DEFAULT NULL,
  `mac` varchar(17) NOT NULL,
  `time_login` datetime NOT NULL,
  `time_logout` datetime DEFAULT NULL,
  `data_in` bigint(20) DEFAULT NULL,
  `data_out` bigint(20) DEFAULT NULL,
  `sessionid` varchar(20) NOT NULL,
  `user_ip` varchar(20) DEFAULT NULL,
  `external_ip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `network_id` (`network_id`),
  KEY `mac_id` (`mac_id`),
  KEY `network_id_2` (`network_id`,`time_login`)
) ENGINE=InnoDB AUTO_INCREMENT=187278 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mac_address`
--

DROP TABLE IF EXISTS `mac_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mac_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'index',
  `mac` varchar(17) NOT NULL COMMENT 'mac address',
  `hostname` varchar(32) NOT NULL COMMENT 'device hostname',
  `status` enum('passive','active','login','goodbye','noise','instore','passerby') DEFAULT NULL,
  `time_first` datetime NOT NULL COMMENT 'first time seen',
  `time_last` datetime NOT NULL COMMENT 'last time seen',
  `net_first` int(10) unsigned NOT NULL COMMENT 'first seen at this network',
  `net_last` int(10) unsigned NOT NULL COMMENT 'last seen at this network',
  `rssi` int(10) DEFAULT NULL,
  `probes` int(11) DEFAULT '0',
  `ssid` varchar(64) DEFAULT NULL,
  `noise` tinyint(1) DEFAULT '0',
  `srev` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mac_address` (`mac`)
) ENGINE=InnoDB AUTO_INCREMENT=1851692785 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`swarmdata`@`%`*/ /*!50003 TRIGGER `before_new_mac` BEFORE INSERT ON `mac_address` 
    FOR EACH ROW BEGIN
	DECLARE x INT;
	SET x = (SELECT noise FROM maclist WHERE SUBSTR(NEW.mac,1,8) = maclist.mac);
	IF x = 1 THEN
	    SET NEW.noise=1;
	    SET NEW.status='noise';
	END IF;
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`swarmdata`@`%`*/ /*!50003 TRIGGER `after_new_mac` AFTER INSERT ON `mac_address` 
    FOR EACH ROW BEGIN
	IF NEW.noise = 0 THEN
		INSERT INTO sessions (`network_id`,`mac_id`,`mac`,`time_login`,`time_logout`,`sessionid`) VALUES (NEW.net_last,NEW.id,NEW.mac,NEW.time_last,NEW.time_last,NEW.status);
	END IF;
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`swarmdata`@`%`*/ /*!50003 TRIGGER `check_noise` BEFORE UPDATE ON `mac_address` 
    FOR EACH ROW BEGIN
        IF NEW.status = 'noise' OR OLD.noise = 1 THEN
		SET NEW.noise = 1;
		SET NEW.status = 'noise';
        END IF;
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`swarmdata`@`%`*/ /*!50003 TRIGGER `updated_mac` AFTER UPDATE ON `mac_address` 
    FOR EACH ROW BEGIN
        IF NEW.noise = 0 THEN
	    IF NEW.probes = 0 THEN
	        INSERT INTO sessions (`network_id`,`mac_id`,`mac`,`time_login`,`time_logout`,`sessionid`) VALUES (NEW.net_last,NEW.id,NEW.mac,NEW.time_last,NEW.time_last,NEW.status);
            ELSE
                UPDATE sessions SET `time_logout` = NEW.time_last, sessionid = NEW.status WHERE `mac_id` = NEW.id AND sessionid = OLD.status ORDER BY id DESC LIMIT 1;     
            END IF;
        END IF;
        
        #DECLARE x INT;
        #SET x = (OLD.status * 10) + NEW.status; #-- this is to make a 2 digit value
        #-- passive, active, login, goodbye, noise
	#CASE
                               -- WHEN x = 0-10 do nothing
			       -- WHEN x = 11 passive->passive do nothing
	#    WHEN x = 16 THEN   -- passive to instore, open an instore session
        #        INSERT INTO sessions (`network_id`,`mac_id`,`mac`,`time_login`,`time_logout`,`sessionid`) VALUES (NEW.net_last,NEW.id,NEW.mac,NEW.time_last,NEW.time_last,'instore');
        #    WHEN x = 17 THEN   -- passive to passerby, open a passerby session
        #        INSERT INTO sessions (`network_id`,`mac_id`,`mac`,`time_login`,`time_logout`,`sessionid`) VALUES (NEW.net_last,NEW.id,NEW.mac,NEW.time_last,NEW.time_last,'passerby');
                               -- WHEN x = 21 active to passive; do nothing
                               -- WHEN x = 22 active to active; do nothing
                               -- WHEN x = 23 active to login; do nothing
                               -- WHEN x = 24 active to goodbye; do nothing
                               -- WHEN x = 25-30 active to noise; do nothing
        #    WHEN x = 26 THEN   -- passive to instore, open an instore session
        #        INSERT INTO sessions (`network_id`,`mac_id`,`mac`,`time_login`,`time_logout`,`sessionid`) VALUES (NEW.net_last,NEW.id,NEW.mac,NEW.time_last,NEW.time_last,'instore');
        #    WHEN x = 27 THEN   -- passive to passerby, open a passerby session
        #        INSERT INTO sessions (`network_id`,`mac_id`,`mac`,`time_login`,`time_logout`,`sessionid`) VALUES (NEW.net_last,NEW.id,NEW.mac,NEW.time_last,NEW.time_last,'passerby');    
                               -- WHEN x = 31 login to passive; do nothing
                               -- WHEN x = 32 login to active; do nothing
                               -- WHEN x = 33 login to login; do nothing
                               -- WHEN x = 34 login to goodbye; do nothing
                               -- WHEN x = 35-40 login to noise; do nothing
			       -- WHEN x = 41 goodbye to passive; do nothing
			       -- WHEN x = 42 goodbye to active; do nothing
	#    WHEN x = 46 THEN  -- goodbye->instore start a new session if NEW.probes=0
	#	IF ( NEW.probes = 0 ) THEN
        #            INSERT INTO sessions (`network_id`,`mac_id`,`mac`,`time_login`,`time_logout`,`sessionid`) VALUES (NEW.net_last,NEW.id,NEW.mac,NEW.time_first,NEW.time_last,NEW.status);
	#	ELSE
        #            UPDATE sessions SET `time_logout` = NEW.time_last WHERE `mac_id` = NEW.id AND sessionid = 'instore' ORDER BY id DESC LIMIT 1;
        #        END IF;
        #    WHEN x = 47 THEN  -- goodbye->passerby start a new session if NEW.probes=0
	#	IF ( NEW.probes = 0 ) THEN
        #            INSERT INTO sessions (`network_id`,`mac_id`,`mac`,`time_login`,`time_logout`,`sessionid`) VALUES (NEW.net_last,NEW.id,NEW.mac,NEW.time_first,NEW.time_last,NEW.status);
	#	ELSE
        #            UPDATE sessions SET `time_logout` = NEW.time_last WHERE `mac_id` = NEW.id AND sessionid = 'instore' ORDER BY id DESC LIMIT 1;
        #        END IF;
                              -- WHEN x = 52 noise to active; do nothing
                              -- WHEN x = 52 noise to login, do nothing radius starts the session
                              -- WHEN x = 53 do nothing
                              -- WHEN x = 54 do nothing
                              -- WHEN x = 55 do nothing
                              -- WHEN x = 61 instore to passive; do nothing
                 	      -- WHEN x = 64 instore->goodbye do nothing
                              -- WHEN x = 65 instore->noise do nothing
        #    WHEN x = 66 THEN  -- instore to instore, start new session if NEW.probes=0
        #        IF ( NEW.probes = 0 ) THEN
        #            INSERT INTO sessions (`network_id`,`mac_id`,`mac`,`time_login`,`time_logout`,`sessionid`) VALUES (NEW.net_last,NEW.id,NEW.mac,NEW.time_first,NEW.time_last,NEW.status);
	#	ELSE
        #            UPDATE sessions SET `time_logout` = NEW.time_last WHERE `mac_id` = NEW.id AND sessionid = 'instore' ORDER BY id DESC LIMIT 1;
        #        END IF;
			      -- WHEN x = 67 instore to passerby, create new session
                              -- WHEN x = 71 passerby to passive; do nothing
	    #                 -- WHEN x = 74 passerby->goodbye do nothing
            #                 -- WHEN x = 75 passerby->noise do nothing
        #    WHEN x = 76 THEN  -- passerby to instore, change sessionid to instore
        #        IF ( NEW.probes = 0 ) THEN
        #            INSERT INTO sessions (`network_id`,`mac_id`,`mac`,`time_login`,`time_logout`,`sessionid`) VALUES (NEW.net_last,NEW.id,NEW.mac,NEW.time_first,NEW.time_last,NEW.status);
        #        ELSE    
        #            UPDATE sessions SET sessionid = 'instore', `time_login` = OLD.time_last, `time_logout` = NEW.time_last WHERE `mac_id` = NEW.id AND sessionid = 'passerby' ORDER BY id DESC LIMIT 1;
        #        END IF;
        #    WHEN x = 77 THEN  -- passerby to passerby, creat new session if probes=0, else update existing session
        #        IF ( NEW.probes = 0 ) THEN
        #            INSERT INTO sessions (`network_id`,`mac_id`,`mac`,`time_login`,`time_logout`,`sessionid`) VALUES (NEW.net_last,NEW.id,NEW.mac,NEW.time_first,NEW.time_last,NEW.status);
	#	ELSE
        #            UPDATE sessions SET `time_logout` = NEW.time_last WHERE `mac_id` = NEW.id AND sessionid = 'passerby' ORDER BY id DESC LIMIT 1;
        #        END IF;
	#    ELSE
        #        BEGIN
        #        END;
	#	INSERT INTO event_log (`type`,`log_data`) VALUES ('trigger_update',CONCAT("x=",x," old: ",OLD.status," new: ",NEW.status, " network: ", NEW.net_last, " mac: ", NEW.mac));
        #END CASE;
        #END IF;      
	#INSERT INTO event_log (`type`,`log_data`) VALUES ('trigger_update',CONCAT(" old: ",OLD.status," new: ",NEW.status, " network: ", NEW.net_last, " mac: ", NEW.mac, " old probes: ", OLD.probes, " new probes: ", NEW.probes));
	#INSERT INTO event_log (`type`,`log_data`) VALUES ('trigger_update',CONCAT("x=",x," old: ",OLD.status," new: ",NEW.status, " network: ", NEW.net_last, " mac: ", NEW.mac, " old probes: ", OLD.probes, " new probes: ", NEW.probes));
	
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `maclist`
--

DROP TABLE IF EXISTS `maclist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maclist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mac` char(8) NOT NULL,
  `name` varchar(30) NOT NULL,
  `noise` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `macaddr` (`mac`)
) ENGINE=InnoDB AUTO_INCREMENT=39566 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `network`
--

DROP TABLE IF EXISTS `network`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `network` (
  `id` int(11) NOT NULL,
  `realm` int(6) NOT NULL,
  `net_name` varchar(30) NOT NULL,
  `display_name` varchar(200) NOT NULL,
  `net_location` varchar(100) NOT NULL,
  `gmt_offset` int(2) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `network_mac_logins`
--

DROP TABLE IF EXISTS `network_mac_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `network_mac_logins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `network_id` int(10) unsigned NOT NULL,
  `mac_id` int(10) unsigned NOT NULL,
  `first_login` datetime NOT NULL,
  `first_logout` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_logout` datetime DEFAULT NULL,
  `login_count` int(10) unsigned DEFAULT '1',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `network_id` (`network_id`,`mac_id`),
  KEY `first_login` (`first_login`)
) ENGINE=InnoDB AUTO_INCREMENT=1945587495 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `network_mac_logins_new1`
--

DROP TABLE IF EXISTS `network_mac_logins_new1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `network_mac_logins_new1` (
  `id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `network_id` int(10) unsigned NOT NULL,
  `mac_id` int(10) unsigned NOT NULL,
  `first_login` datetime NOT NULL,
  `first_logout` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_logout` datetime DEFAULT NULL,
  `login_count` int(10) unsigned DEFAULT '1',
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `network_mac_logins_old`
--

DROP TABLE IF EXISTS `network_mac_logins_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `network_mac_logins_old` (
  `id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `network_id` int(10) unsigned NOT NULL,
  `mac_id` int(10) unsigned NOT NULL,
  `first_login` datetime NOT NULL,
  `first_logout` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_logout` datetime DEFAULT NULL,
  `login_count` int(10) unsigned DEFAULT '1',
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `node`
--

DROP TABLE IF EXISTS `node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `node` (
  `id` int(11) NOT NULL,
  `time` varchar(20) NOT NULL,
  `mac` varchar(20) NOT NULL,
  `netid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mac` (`mac`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `passerby_sessions`
--

DROP TABLE IF EXISTS `passerby_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `passerby_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `network_id` int(11) NOT NULL,
  `mac_id` int(11) DEFAULT NULL,
  `mac` varchar(17) NOT NULL,
  `time_login` datetime NOT NULL,
  `time_logout` datetime DEFAULT NULL,
  `data_in` bigint(20) DEFAULT NULL,
  `data_out` bigint(20) DEFAULT NULL,
  `sessionid` varchar(20) NOT NULL,
  `user_ip` varchar(20) DEFAULT NULL,
  `external_ip` varchar(20) DEFAULT NULL,
  `opfield1` varchar(255) DEFAULT NULL,
  `opfield2` varchar(255) DEFAULT NULL,
  `opfield3` varchar(255) DEFAULT NULL,
  `txtfield4` text,
  PRIMARY KEY (`id`),
  KEY `idx_network_id_login` (`network_id`,`time_login`)
) ENGINE=InnoDB AUTO_INCREMENT=1030500285 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `public_ip`
--

DROP TABLE IF EXISTS `public_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `public_ip` (
  `netid` int(10) NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`netid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `realm`
--

DROP TABLE IF EXISTS `realm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `realm` (
  `id` int(11) NOT NULL,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `display` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sensor_sessions`
--

DROP TABLE IF EXISTS `sensor_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sensor_sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL,
  `collector_id` int(10) unsigned NOT NULL,
  `packet_id` varchar(20) DEFAULT NULL,
  `sensor` enum('temp','accl','gyro') DEFAULT NULL,
  `ts` datetime DEFAULT NULL,
  `val` varchar(10) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_member_id_packet_id` (`member_id`,`packet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `network_id` int(11) NOT NULL,
  `mac_id` int(11) DEFAULT NULL,
  `mac` varchar(17) NOT NULL,
  `time_login` datetime NOT NULL,
  `time_logout` datetime DEFAULT NULL,
  `data_in` bigint(20) DEFAULT NULL,
  `data_out` bigint(20) DEFAULT NULL,
  `sessionid` enum('unknown','instore','passerby','passive') NOT NULL DEFAULT 'unknown',
  `user_ip` varchar(20) DEFAULT NULL,
  `external_ip` varchar(20) DEFAULT NULL,
  `opfield1` varchar(255) DEFAULT NULL,
  `opfield2` varchar(255) DEFAULT NULL,
  `opfield3` varchar(255) DEFAULT NULL,
  `txtfield4` text,
  PRIMARY KEY (`id`,`time_login`),
  KEY `mac_id` (`mac_id`),
  KEY `network_id_2` (`network_id`,`time_login`)
) ENGINE=InnoDB AUTO_INCREMENT=1460591601 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC
/*!50100 PARTITION BY RANGE (TO_DAYS(time_login))
(PARTITION p_2013_09 VALUES LESS THAN (735477) ENGINE = InnoDB,
 PARTITION p_2013_10 VALUES LESS THAN (735507) ENGINE = InnoDB,
 PARTITION p_2013_11 VALUES LESS THAN (735538) ENGINE = InnoDB,
 PARTITION p_2013_12 VALUES LESS THAN (735568) ENGINE = InnoDB,
 PARTITION p_2014_01 VALUES LESS THAN (735599) ENGINE = InnoDB,
 PARTITION p_2014_02 VALUES LESS THAN (735630) ENGINE = InnoDB,
 PARTITION p_2014_03 VALUES LESS THAN (735658) ENGINE = InnoDB,
 PARTITION p_2014_04 VALUES LESS THAN (735689) ENGINE = InnoDB,
 PARTITION p_2014_05 VALUES LESS THAN (735719) ENGINE = InnoDB,
 PARTITION p_2014_06 VALUES LESS THAN (735750) ENGINE = InnoDB,
 PARTITION p_2014_07 VALUES LESS THAN (735780) ENGINE = InnoDB,
 PARTITION p_2014_08 VALUES LESS THAN (735811) ENGINE = InnoDB,
 PARTITION p_2014_09 VALUES LESS THAN (735842) ENGINE = InnoDB,
 PARTITION p_2014_10 VALUES LESS THAN (735872) ENGINE = InnoDB,
 PARTITION p_2014_11 VALUES LESS THAN (735903) ENGINE = InnoDB,
 PARTITION p_2014_12 VALUES LESS THAN (735933) ENGINE = InnoDB,
 PARTITION p_2015_01 VALUES LESS THAN (735964) ENGINE = InnoDB,
 PARTITION p_2015_02 VALUES LESS THAN (735995) ENGINE = InnoDB,
 PARTITION p_2015_03 VALUES LESS THAN (736023) ENGINE = InnoDB,
 PARTITION p_2015_04 VALUES LESS THAN (736054) ENGINE = InnoDB,
 PARTITION p_2015_05 VALUES LESS THAN (736084) ENGINE = InnoDB,
 PARTITION p_2015_06 VALUES LESS THAN (736115) ENGINE = InnoDB,
 PARTITION p_2015_07 VALUES LESS THAN (736145) ENGINE = InnoDB,
 PARTITION p_2015_08 VALUES LESS THAN (736176) ENGINE = InnoDB,
 PARTITION p_2015_09 VALUES LESS THAN (736207) ENGINE = InnoDB,
 PARTITION p_2015_10 VALUES LESS THAN (736237) ENGINE = InnoDB,
 PARTITION p_2015_11 VALUES LESS THAN (736268) ENGINE = InnoDB,
 PARTITION p_2015_12 VALUES LESS THAN (736298) ENGINE = InnoDB,
 PARTITION pmax_2016 VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions_archive`
--

DROP TABLE IF EXISTS `sessions_archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions_archive` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `network_id` int(11) NOT NULL,
  `mac_id` int(11) DEFAULT NULL,
  `mac` varchar(17) NOT NULL,
  `time_login` datetime NOT NULL,
  `time_logout` datetime DEFAULT NULL,
  `data_in` bigint(20) DEFAULT NULL,
  `data_out` bigint(20) DEFAULT NULL,
  `sessionid` enum('unknown','instore','passerby','passive') NOT NULL DEFAULT 'unknown',
  `user_ip` varchar(20) DEFAULT NULL,
  `external_ip` varchar(20) DEFAULT NULL,
  `opfield1` varchar(255) DEFAULT NULL,
  `opfield2` varchar(255) DEFAULT NULL,
  `opfield3` varchar(255) DEFAULT NULL,
  `txtfield4` text,
  PRIMARY KEY (`id`,`time_login`),
  KEY `mac_id` (`mac_id`),
  KEY `network_id_2` (`network_id`,`time_login`)
) ENGINE=InnoDB AUTO_INCREMENT=1180487861 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC
/*!50100 PARTITION BY RANGE ( TO_DAYS(time_login))
(PARTITION p_2013_03 VALUES LESS THAN (735293) ENGINE = InnoDB,
 PARTITION p_2013_04 VALUES LESS THAN (735324) ENGINE = InnoDB,
 PARTITION p_2013_05 VALUES LESS THAN (735354) ENGINE = InnoDB,
 PARTITION p_2013_06 VALUES LESS THAN (735385) ENGINE = InnoDB,
 PARTITION p_2013_07 VALUES LESS THAN (735415) ENGINE = InnoDB,
 PARTITION p_2013_08 VALUES LESS THAN (735446) ENGINE = InnoDB,
 PARTITION p_2013_09 VALUES LESS THAN (735477) ENGINE = InnoDB,
 PARTITION p_2013_10 VALUES LESS THAN (735507) ENGINE = InnoDB,
 PARTITION p_2013_11 VALUES LESS THAN (735538) ENGINE = InnoDB,
 PARTITION p_2013_12 VALUES LESS THAN (735568) ENGINE = InnoDB,
 PARTITION p_2014_01 VALUES LESS THAN (735599) ENGINE = InnoDB,
 PARTITION p_2014_02 VALUES LESS THAN (735630) ENGINE = InnoDB,
 PARTITION p_2014_03 VALUES LESS THAN (735658) ENGINE = InnoDB,
 PARTITION p_2014_04 VALUES LESS THAN (735689) ENGINE = InnoDB,
 PARTITION p_2014_05 VALUES LESS THAN (735719) ENGINE = InnoDB,
 PARTITION p_2014_06 VALUES LESS THAN (735750) ENGINE = InnoDB,
 PARTITION p_2014_07 VALUES LESS THAN (735780) ENGINE = InnoDB,
 PARTITION p_2014_08 VALUES LESS THAN (735811) ENGINE = InnoDB,
 PARTITION p_2014_09 VALUES LESS THAN (735842) ENGINE = InnoDB,
 PARTITION p_2014_10 VALUES LESS THAN (735872) ENGINE = InnoDB,
 PARTITION p_2014_11 VALUES LESS THAN (735903) ENGINE = InnoDB,
 PARTITION p_2014_12 VALUES LESS THAN (735933) ENGINE = InnoDB,
 PARTITION p_2015_01 VALUES LESS THAN (735964) ENGINE = InnoDB,
 PARTITION p_2015_02 VALUES LESS THAN (735995) ENGINE = InnoDB,
 PARTITION p_2015_03 VALUES LESS THAN (736023) ENGINE = InnoDB,
 PARTITION p_2015_04 VALUES LESS THAN (736054) ENGINE = InnoDB,
 PARTITION p_2015_05 VALUES LESS THAN (736084) ENGINE = InnoDB,
 PARTITION p_2015_06 VALUES LESS THAN (736115) ENGINE = InnoDB,
 PARTITION p_2015_07 VALUES LESS THAN (736145) ENGINE = InnoDB,
 PARTITION p_2015_08 VALUES LESS THAN (736176) ENGINE = InnoDB,
 PARTITION p_2015_09 VALUES LESS THAN (736207) ENGINE = InnoDB,
 PARTITION p_2015_10 VALUES LESS THAN (736237) ENGINE = InnoDB,
 PARTITION p_2015_11 VALUES LESS THAN (736268) ENGINE = InnoDB,
 PARTITION p_2015_12 VALUES LESS THAN (736298) ENGINE = InnoDB,
 PARTITION pmax_2016 VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions_noise`
--

DROP TABLE IF EXISTS `sessions_noise`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions_noise` (
  `id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `network_id` int(11) NOT NULL,
  `mac_id` int(11) DEFAULT NULL,
  `mac` varchar(17) NOT NULL,
  `time_login` datetime NOT NULL,
  `time_logout` datetime DEFAULT NULL,
  `data_in` bigint(20) DEFAULT NULL,
  `data_out` bigint(20) DEFAULT NULL,
  `sessionid` varchar(20) NOT NULL,
  `user_ip` varchar(20) DEFAULT NULL,
  `external_ip` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions_temp`
--

DROP TABLE IF EXISTS `sessions_temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions_temp` (
  `id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `network_id` int(11) NOT NULL,
  `mac_id` int(11) DEFAULT NULL,
  `mac` varchar(17) NOT NULL,
  `time_login` datetime NOT NULL,
  `time_logout` datetime DEFAULT NULL,
  `data_in` bigint(20) DEFAULT NULL,
  `data_out` bigint(20) DEFAULT NULL,
  `sessionid` varchar(20) NOT NULL,
  `user_ip` varchar(20) DEFAULT NULL,
  `external_ip` varchar(20) DEFAULT NULL,
  `opfield1` varchar(255) DEFAULT NULL,
  `opfield2` varchar(255) DEFAULT NULL,
  `opfield3` varchar(255) DEFAULT NULL,
  `txtfield4` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-06 11:16:51
