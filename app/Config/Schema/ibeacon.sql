CREATE TABLE `ibeacon_api_key` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`key` VARCHAR(100) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `user_id` (`user_id`),
	CONSTRAINT `FK_ibeacon_api_key_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf32_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=2;
INSERT INTO `ibeacon_api_key` (`id`, `user_id`, `key`) VALUES (1, 2, 'D57092AC-DFAA-446C-8EF3-C81AA22815B5');

CREATE TABLE `ibeacon_blacklist_ip` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`ip` VARCHAR(45) NOT NULL DEFAULT '0' COLLATE 'utf8_bin',
	`approved` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `ip` (`ip`)
)
COLLATE='utf8_bin'
ENGINE=InnoDB;

CREATE TABLE `ibeacon_campaigns` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`location_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`name` VARCHAR(256) NOT NULL DEFAULT '0',
	`total_coupons` INT(11) NOT NULL DEFAULT '0',
	`active` TINYINT(1) NOT NULL DEFAULT '0',
	`start_date` DATE NOT NULL DEFAULT '0000-00-00',
	`end_date` DATE NOT NULL DEFAULT '0000-00-00',
	`ad_partner` VARCHAR(100) NULL DEFAULT NULL,
	`product_id` VARCHAR(100) NULL DEFAULT NULL,
	`minimum_score` INT(11) NULL DEFAULT NULL,
	`ts_create` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`ts_update` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	INDEX `location_id` (`location_id`),
	CONSTRAINT `FK_ibeacon_campaigns_location` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=7;
INSERT INTO `ibeacon_campaigns` (`id`, `location_id`, `name`, `total_coupons`, `active`, `start_date`, `end_date`, `ad_partner`, `product_id`, `minimum_score`, `ts_create`, `ts_update`) VALUES (1, 8, 'test 2', 1000, 1, '2014-06-30', '2014-07-28', 'sdf', 'Product Identifier', 0, '2014-07-07 17:03:58', '2014-07-07 16:03:58');

CREATE TABLE `ibeacon_campaign_proximity_rules` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`campaign_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`type` ENUM('outbound_lost','outbound_unknown','outbound_far','outbound_near','inbound_immediate','inbound_unknown','inbound_far','inbound_near') NOT NULL DEFAULT 'outbound_lost',
	`ts_create` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`ts_update` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	INDEX `campaign_id` (`campaign_id`),
	CONSTRAINT `FK_ibeacon_campaign_proximity_rules_ibeacon_campaigns` FOREIGN KEY (`campaign_id`) REFERENCES `ibeacon_campaigns` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=73;

INSERT INTO `ibeacon_campaign_proximity_rules` (`id`, `campaign_id`, `type`, `ts_create`, `ts_update`) VALUES (72, 1, 'inbound_near', '2014-07-07 16:03:58', '0000-00-00 00:00:00');


CREATE TABLE `ibeacon_campaign_scoring _rules` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`campaign_id` INT(11) UNSIGNED NOT NULL,
	`name` VARCHAR(128) NOT NULL,
	`rule` TEXT NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `campaign_id` (`campaign_id`),
	CONSTRAINT `FK_ibeacon_campaign_rules_ibeacon_campaigns` FOREIGN KEY (`campaign_id`) REFERENCES `ibeacon_campaigns` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=7;

CREATE TABLE `ibeacon_coupons` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`customer_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`campaign_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`code` VARCHAR(100) NULL DEFAULT '0',
	`status` ENUM('new','accept','reject') NULL DEFAULT 'new',
	`ts_create` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`ts_update` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	INDEX `customer_id` (`customer_id`),
	INDEX `campaign_id` (`campaign_id`),
	INDEX `status` (`status`),
	INDEX `campaign_id_status` (`campaign_id`, `status`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=12;

CREATE TABLE `ibeacon_coupon_configuration` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`campaign_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`image_url` VARCHAR(512) NULL DEFAULT NULL,
	`external_url` VARCHAR(512) NULL DEFAULT NULL,
	`title` VARCHAR(256) NOT NULL,
	`text` VARCHAR(256) NOT NULL,
	`delivery_text` VARCHAR(256) NULL DEFAULT NULL,
	`ts_create` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`ts_update` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	INDEX `campaign_id` (`campaign_id`),
	CONSTRAINT `FK_ibeacon_coupon_configuration_ibeacon_campaigns` FOREIGN KEY (`campaign_id`) REFERENCES `ibeacon_campaigns` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=23;


INSERT INTO `ibeacon_coupon_configuration` (`id`, `campaign_id`, `image_url`, `external_url`, `title`, `text`, `delivery_text`, `ts_create`, `ts_update`) VALUES (16, 1, 'http://www.barcoding.com/images/Barcodes/code93.gif', 'dfgddddd', 'title', 'text', 'dfg', '2014-07-07 17:03:58', '2014-07-07 16:03:58');


CREATE TABLE `ibeacon_customers` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(128) NOT NULL,
	`user_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`remote_id` VARCHAR(128) NULL DEFAULT NULL,
	`vendor_id` VARCHAR(100) NULL DEFAULT NULL,
	`advertiser_id` VARCHAR(100) NULL DEFAULT NULL,
	`description` VARCHAR(256) NULL DEFAULT NULL,
	`ts_create` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`ts_update` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	INDEX `user_id` (`user_id`),
	CONSTRAINT `FK_customers_users` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=11;


CREATE TABLE `ibeacon_customer_ssv` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`customer_id` INT(11) UNSIGNED NOT NULL,
	`name` VARCHAR(128) NOT NULL,
	`value` TEXT NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `customer_id` (`customer_id`),
	CONSTRAINT `FK__customers` FOREIGN KEY (`customer_id`) REFERENCES `ibeacon_customers` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=104;

CREATE TABLE `ibeacon_device_coordinates` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`device_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`latitude` DOUBLE NOT NULL DEFAULT '0',
	`longitude` DOUBLE NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `device_id` (`device_id`),
	CONSTRAINT `FK__device` FOREIGN KEY (`device_id`) REFERENCES `device` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
COLLATE='utf32_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=7;

CREATE TABLE `ibeacon_rest_api_rate_limit` (
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`type` ENUM('ip','api_key') NOT NULL DEFAULT 'ip' COLLATE 'utf8_bin',
	`max_number_requests` INT(9) UNSIGNED NOT NULL DEFAULT '0',
	`period` VARCHAR(125) NOT NULL COLLATE 'utf8_bin',
	`message` VARCHAR(256) NULL DEFAULT NULL COLLATE 'utf8_bin',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_bin'
ENGINE=InnoDB;
