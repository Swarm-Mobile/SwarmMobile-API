DROP TABLE IF EXISTS `location_employee`;
CREATE TABLE `location_employee` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int(10) unsigned NOT NULL,
  `employee_id` int(10) unsigned NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_id` (`location_id`,`employee_id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `location_employee` VALUES (1,2191,17,'2014-08-08 21:35:27');