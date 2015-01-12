DROP TABLE IF EXISTS `metric`;
CREATE TABLE `metric` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `data` varchar(255) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `description` text
);