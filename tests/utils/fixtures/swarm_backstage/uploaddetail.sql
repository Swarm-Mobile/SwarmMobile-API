DROP TABLE IF EXISTS `uploaddetail`;
CREATE TABLE `uploaddetail` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,s
  `name` varchar(255) NOT NULL,
  `setting_id` int(10) NOT NULL,
  `width` int(10) NOT NULL,
  `height` int(10) NOT NULL,
  `size` int(10) NOT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `save_pattern` varchar(255) DEFAULT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO `uploaddetail` VALUES (1,'Avatar',88,2000,3000,5000000,'avatars/','avatar_{id}','2014-11-12 01:09:49');
INSERT INTO `uploaddetail` VALUES (2,'Logo',89,400,200,200000,'logos/','photo_{id}','2014-10-02 00:55:42');
