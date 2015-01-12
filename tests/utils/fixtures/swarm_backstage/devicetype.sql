DROP TABLE IF EXISTS `devicetype`;
CREATE TABLE `devicetype` (
  `id` tinyint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `devicetype` VALUES (1,'Presence','WiFi sniffer device','Presence','2014-07-03 20:26:54','2014-07-03 20:26:54');
INSERT INTO `devicetype` VALUES (2,'Portal','Infra-red foot traffic sensor','Portal','2014-07-03 20:26:54','2014-07-03 20:26:54');
INSERT INTO `devicetype` VALUES (3,'Ping','BLE iBeacon','Ping','2014-07-03 20:26:54','2014-07-03 20:26:54');
