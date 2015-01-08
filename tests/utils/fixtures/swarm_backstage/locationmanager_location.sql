DROP TABLE IF EXISTS `locationmanager_location`;
CREATE TABLE `locationmanager_location` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int(10) unsigned NOT NULL,
  `locationmanager_id` int(10) unsigned NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_id` (`location_id`,`locationmanager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `locationmanager_location` VALUES (385,807,385,'2014-07-03 20:29:23');
INSERT INTO `locationmanager_location` VALUES (4236,3,1,'2014-10-22 17:54:06');