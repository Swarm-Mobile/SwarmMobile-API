DROP TABLE IF EXISTS `uploaddetail_fileextension`;
CREATE TABLE `uploaddetail_fileextension` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uploaddetail_id` tinyint(10) NOT NULL,
  `fileextension_id` tinyint(10) NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO `uploaddetail_fileextension` VALUES (13,2,1,'2014-07-03 20:57:48');
INSERT INTO `uploaddetail_fileextension` VALUES (14,2,2,'2014-07-03 20:57:48');
INSERT INTO `uploaddetail_fileextension` VALUES (15,2,3,'2014-07-03 20:57:48');
INSERT INTO `uploaddetail_fileextension` VALUES (25,1,1,'2014-11-12 01:09:50');
INSERT INTO `uploaddetail_fileextension` VALUES (26,1,2,'2014-11-12 01:09:50');
INSERT INTO `uploaddetail_fileextension` VALUES (27,1,3,'2014-11-12 01:09:50');
