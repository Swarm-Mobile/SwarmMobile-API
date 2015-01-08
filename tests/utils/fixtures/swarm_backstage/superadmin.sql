DROP TABLE IF EXISTS `superadmin`;
CREATE TABLE `superadmin` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone_no` varchar(255) DEFAULT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);