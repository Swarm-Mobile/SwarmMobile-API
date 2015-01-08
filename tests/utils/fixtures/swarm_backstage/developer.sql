DROP TABLE IF EXISTS `developer`;
CREATE TABLE `developer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `accountmanager_id` int(10) unsigned DEFAULT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `company` varchar(255) NOT NULL DEFAULT '',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `accountmanager_id` (`accountmanager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `developer` VALUES (8,1477,NULL,'Axia','',NULL,'Axia','2014-09-16 22:01:06');