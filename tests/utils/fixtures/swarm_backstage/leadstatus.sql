DROP TABLE IF EXISTS `leadstatus`;
CREATE TABLE `leadstatus` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
);
INSERT INTO `leadstatus` VALUES (1,'Qualified','Initial default status');
INSERT INTO `leadstatus` VALUES (2,'Expired','The lead passed the expiration time');
INSERT INTO `leadstatus` VALUES (3,'Pitch','Back and Forth communication');
INSERT INTO `leadstatus` VALUES (4,'Proposal','Proposal sent');
INSERT INTO `leadstatus` VALUES (5,'Closed-Won','Closed with success');
INSERT INTO `leadstatus` VALUES (6,'Closed-Lost','Closed without success');
INSERT INTO `leadstatus` VALUES (7,'In Setup','In location setup process');
