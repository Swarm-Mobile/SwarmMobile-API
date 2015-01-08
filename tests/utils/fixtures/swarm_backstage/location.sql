DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountmanager_id` int(10) unsigned DEFAULT NULL,
  `reseller_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lead_id` int(10) unsigned DEFAULT NULL,
  `developer_id` int(10) unsigned DEFAULT NULL,
  `cda_imported` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accountmanager_id` (`accountmanager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `location` VALUES (689,14,0,'Cinnamon Girl - Kahala Mall','','2014-08-07 10:08:40','2014-10-02 20:43:44',NULL,0,'2014-10-02 20:43:44');
INSERT INTO `location` VALUES (367,14,NULL,'Rapha Cycle Club San Francisco','','2014-08-29 07:08:13','2014-10-02 20:39:30',NULL,NULL,'2014-10-02 20:39:30');
INSERT INTO `location` VALUES (1494,15,0,'Cheeky','','2014-07-03 20:33:36','2014-10-02 20:53:37',NULL,0,'2014-10-02 20:53:37');
INSERT INTO `location` VALUES (385,15,4,'Expert Soest','','2014-07-29 05:07:07','2014-10-02 20:39:43',NULL,0,'2014-10-02 20:39:43');
INSERT INTO `location` VALUES (2191,NULL,63,'Nativo - IZMIR','','2014-08-08 09:08:43','2014-08-08 21:33:11',NULL,NULL,NULL);
INSERT INTO `location` VALUES (2238,33,NULL,'Axia','','2014-08-16 09:08:05','2014-09-16 22:01:34',NULL,8,NULL);
