DROP TABLE IF EXISTS `settinggroup`;
CREATE TABLE `settinggroup` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,s
  `name` varchar(255) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO `settinggroup` VALUES (1,'Misc','2014-07-03 20:26:54','2014-07-03 20:26:54');
INSERT INTO `settinggroup` VALUES (2,'Address','2014-07-03 20:26:54','2014-07-03 20:26:54');
INSERT INTO `settinggroup` VALUES (3,'Schedule','2014-07-03 20:26:54','2014-07-03 20:26:54');
INSERT INTO `settinggroup` VALUES (4,'Third Parties','2014-07-03 20:26:54','2014-07-03 20:26:54');
INSERT INTO `settinggroup` VALUES (5,'POS','2014-07-03 20:26:54','2014-07-03 20:26:54');
INSERT INTO `settinggroup` VALUES (6,'Swarm','2014-07-03 20:26:54','2014-07-03 20:26:54');
INSERT INTO `settinggroup` VALUES (7,'AppConfig','0000-00-00 00:00:00','2014-09-05 21:26:49');
