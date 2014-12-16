DROP TABLE IF EXISTS `requested_rollups_processed`;
CREATE TABLE `requested_rollups_processed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `location_id` int(10) unsigned NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `override` tinyint(1) DEFAULT '0',
  `rebuild` tinyint(1) DEFAULT '0',
  `reporter_email` varchar(255) NOT NULL,
  `ts_queue` timestamp NOT NULL,
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
