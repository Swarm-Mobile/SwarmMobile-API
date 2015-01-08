DROP TABLE IF EXISTS `device`;
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
  `developer_id` int(10) unsigned DEFAULT NULL,
  `battery_level` decimal(10,2) DEFAULT NULL,
  `lat` varchar(45) DEFAULT NULL,
  `long` varchar(45) DEFAULT NULL,
  `last_sync` datetime DEFAULT NULL,
  `store_open` varchar(45) DEFAULT NULL,
  `store_close` varchar(45) DEFAULT NULL,
  `firmware_version` varchar(45) DEFAULT NULL,
  `app_version` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reseller_id` (`reseller_id`),
  KEY `location_id` (`location_id`),
  KEY `device_type_id` (`devicetype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `device` VALUES (3509,'',NULL,2283,0,'0000-00-00 00:00:00','2014-11-18 00:53:37',3,'',NULL,NULL,NULL,NULL,0,'','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `device` VALUES (3508,'',0,2763,3,'0000-00-00 00:00:00','2014-11-13 22:26:59',1,'Ready to Trade 3 - 71420777',NULL,NULL,1,777,1,'71420777','71420777',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `device` VALUES (3507,'',0,689,3,'0000-00-00 00:00:00','2014-11-13 22:26:16',3,'Ready to Trade 2 - 71420656',NULL,NULL,1,656,1,'71420656','71420656',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `device` VALUES (3506,'',0,2761,3,'0000-00-00 00:00:00','2014-11-13 22:25:30',1,'Ready to Trade 1 - 71420650',NULL,NULL,1,650,1,'71420650','71420650',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `device` VALUES (101,'',0,367,1,'2014-07-03 20:27:33','2014-07-03 20:27:33',3,'Rapha Cycle Club San Francisco Presence',NULL,NULL,NULL,NULL,2,'123456789','123456789',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `device` VALUES (2657,'00 00 1C BA 8C 28 4C 40',0,367,2,'2014-08-20 21:08:14','2014-10-30 20:02:43',3,'Rapha SF',NULL,NULL,814,20070,2,'0000000081420070','81420070','Imported on 2014-08-20 21:11:21 with portals_Aug20_revised.csv',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `device` VALUES (3295,'',0,367,1,'0000-00-00 00:00:00','2014-10-13 21:26:26',3,'Rapha SF Presence',NULL,NULL,NULL,NULL,2,'AC:86:74:24:a2:10','AC:86:74:24:a2:10',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `device` VALUES (3296,'',0,367,1,'0000-00-00 00:00:00','2014-10-13 21:25:57',3,'Rapha SF Presence',NULL,NULL,NULL,NULL,2,'AC:86:74:24:A2:08','AC:86:74:24:A2:08',NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `device` VALUES (338,'',0,689,1,'2014-07-03 20:29:03','2014-07-03 20:29:03',3,'Cinnamon Girl - Kahala Mall Presence',NULL,NULL,NULL,NULL,2,'','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
INSERT INTO `device` VALUES (871,'',0,1494,1,'2014-07-03 20:33:36','2014-07-03 20:33:36',3,'Cheeky Presence',NULL,NULL,NULL,NULL,2,'','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
