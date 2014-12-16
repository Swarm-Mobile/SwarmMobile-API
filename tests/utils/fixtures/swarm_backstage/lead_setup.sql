DROP TABLE IF EXISTS `lead_setup`;
CREATE TABLE `lead_setup` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `lead_id` int(10) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `package` varchar(255) DEFAULT NULL,
  `presence_from_inventory` tinyint(1) DEFAULT NULL,
  `presence_direct_to_location` tinyint(1) DEFAULT NULL,
  `presence_id` int(10) DEFAULT NULL,
  `portal_from_inventory` tinyint(1) DEFAULT NULL,
  `portal_direct_to_location` tinyint(1) DEFAULT NULL,
  `portal_id` int(10) DEFAULT '0',
  `pos_type` varchar(255) DEFAULT NULL,
  `ts_creation` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `completed` tinyint(1) DEFAULT '0'
);