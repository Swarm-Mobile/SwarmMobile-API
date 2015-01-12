DROP TABLE IF EXISTS `package`;
CREATE TABLE `package` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO `package` VALUES (1,'unknown','Unknown','2014-09-08 22:37:58');
INSERT INTO `package` VALUES (2,'sdk','SDK','2014-09-08 22:37:58');
INSERT INTO `package` VALUES (3,'demo','Demo','2014-09-08 22:37:58');
INSERT INTO `package` VALUES (4,'portal','Portal','2014-09-08 22:37:58');
INSERT INTO `package` VALUES (5,'portal_pos','Portal & POS','2014-09-08 22:37:58');
INSERT INTO `package` VALUES (6,'portal_presence_pos','Portal, Presence & POS','2014-09-08 22:37:58');
INSERT INTO `package` VALUES (7,'portal_presence','Portal, Presence','2014-09-08 22:37:58');
INSERT INTO `package` VALUES (8,'presence','Presence','2014-09-08 22:37:58');
INSERT INTO `package` VALUES (9,'presence_pos','Presence & POS','2014-09-08 22:37:58');
