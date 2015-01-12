DROP TABLE IF EXISTS `totals`;
CREATE TABLE `totals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `location_id` int(10) unsigned NOT NULL DEFAULT '0',
  `walkbys` int(10) unsigned NOT NULL DEFAULT '0',
  `transactions` int(10) unsigned NOT NULL DEFAULT '0',
  `revenue` decimal(10,2) NOT NULL DEFAULT '0.00',
  `totalItems` int(10) unsigned NOT NULL DEFAULT '0',
  `presenceReturning` int(10) unsigned NOT NULL DEFAULT '0',
  `presenceTraffic` int(10) unsigned NOT NULL DEFAULT '0',
  `portalTraffic` int(10) unsigned NOT NULL DEFAULT '0',
  `timeInShop` int(10) unsigned NOT NULL DEFAULT '0',
  `traffic` int(10) unsigned NOT NULL DEFAULT '0',
  `devices` int(10) unsigned NOT NULL DEFAULT '0',
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_date` (`location_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;