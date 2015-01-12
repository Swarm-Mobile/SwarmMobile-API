DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `product_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ls_product_id` bigint(64) DEFAULT NULL,
  `store_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `sku` varchar(50) DEFAULT NULL,
  `store_sku` varchar(50) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `manufacturer` varchar(50) DEFAULT NULL,
  `upc` varchar(14) DEFAULT NULL,
  `ean` varchar(14) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  `uuid` char(36) DEFAULT NULL,
  UNIQUE KEY `product_id` (`product_id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `products_storeid_lsproductid_idx` (`store_id`,`ls_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
