DROP TABLE IF EXISTS `password_recovery`;
CREATE TABLE `password_recovery` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` int(10) NOT NULL,
  `code` varchar(40) NOT NULL,
  `ts_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ts_expire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expired` tinyint(1) NOT NULL DEFAULT '0'
);