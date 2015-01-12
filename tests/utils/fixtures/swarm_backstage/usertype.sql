DROP TABLE IF EXISTS `usertype`;
CREATE TABLE `usertype` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `ts_creation` imestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO `usertype` VALUES (1,'Super Admin','Full Administrator user','AccountManager','2014-07-03 20:26:54','2014-07-03 20:26:54');
INSERT INTO `usertype` VALUES (2,'Account Manager','Swarm account managers','AccountManager','2014-07-03 20:26:54','2014-07-03 20:26:54');
INSERT INTO `usertype` VALUES (3,'Reseller','Reseller that work to increase the presence of Swarms devices','Reseller','2014-07-03 20:26:54','2014-07-03 20:26:54');
INSERT INTO `usertype` VALUES (4,'Location Manager','Locations owner or responsable','LocationManager','2014-07-03 20:26:54','2014-07-03 20:26:54');
INSERT INTO `usertype` VALUES (5,'Employee','Locations employee','Employee','2014-07-03 20:26:54','2014-07-03 20:26:54');
INSERT INTO `usertype` VALUES (6,'Developer','App Developer','Developer','2014-08-16 00:00:00','2014-08-16 00:00:00');