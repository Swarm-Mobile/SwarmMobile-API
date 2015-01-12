DROP TABLE IF EXISTS `reseller`;
CREATE TABLE `reseller` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `accountmanager_id` int(10) unsigned DEFAULT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `address1` varchar(255) NOT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `country` varchar(10) NOT NULL,
  `company` varchar(255) NOT NULL DEFAULT '',
  `portal_committment` int(10) unsigned DEFAULT NULL,
  `presence_committment` int(10) unsigned DEFAULT NULL,
  `commitment_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `accountmanager_id` (`accountmanager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `reseller` VALUES (64,417,14,'Benn','Besharah','#200-375 Water Street','','V6B 5C6','(778) 882-2437','2014-07-21 17:17:21','Vancouver','','CA','Neish Networks',NULL,NULL,NULL);
INSERT INTO `reseller` VALUES (17,1477,14,'Mike','Sheffey','820 State St.',NULL,'93101','(877) 875-6114 x 3','2014-07-18 00:01:26','Santa Barbara','California','US','Axia Payments',NULL,NULL,NULL);
