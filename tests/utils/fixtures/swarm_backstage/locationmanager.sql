DROP TABLE IF EXISTS `locationmanager`;
CREATE TABLE `locationmanager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `phone_no` varchar(255) DEFAULT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `locationmanager` VALUES (1,33,'Cynergy Bicycles','-','','2014-10-22 17:54:06');
INSERT INTO `locationmanager` VALUES (385,1759,'Neishnetworks','','','2014-07-03 20:29:23');