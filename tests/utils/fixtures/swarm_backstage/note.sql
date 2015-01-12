DROP TABLE IF EXISTS `note`;
CREATE TABLE `note` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` int(10) DEFAULT NULL,
  `message` text,
  `resource_type` varchar(45) DEFAULT NULL,
  `resource_id` int(10) DEFAULT NULL,
  `ts_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP
);