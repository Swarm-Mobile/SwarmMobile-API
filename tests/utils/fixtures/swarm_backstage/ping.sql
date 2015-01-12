DROP TABLE IF EXISTS `ping`;
CREATE TABLE `ping` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `device_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);