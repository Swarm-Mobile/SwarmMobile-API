DROP TABLE IF EXISTS `iab2`;
CREATE TABLE `iab2` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) NOT NULL,
  `iab1_id` mediumint(4) NOT NULL
);