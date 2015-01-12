DROP TABLE IF EXISTS `email_queue_processed`;
CREATE TABLE `email_queue_processed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_email` varchar(255) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `to_email` varchar(255) DEFAULT NULL,
  `to_name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `ts_queue` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

