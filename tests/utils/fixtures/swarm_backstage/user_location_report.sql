DROP TABLE IF EXISTS `user_location_report`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_location_report` VALUES (1,1,3,1,1,1,1,'2014-09-11 21:32:21');
