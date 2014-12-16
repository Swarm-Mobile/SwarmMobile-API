DROP TABLE IF EXISTS `accountmanager`;
CREATE TABLE `accountmanager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `is_admin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `phone_no` varchar(255) DEFAULT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `accountmanager` VALUES (1,1,0,'Admin','','','2014-07-03 20:26:54');
INSERT INTO `accountmanager` VALUES (13,13,1,'Gian','Pepe','','2014-09-02 19:13:04');