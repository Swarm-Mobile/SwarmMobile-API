DROP TABLE IF EXISTS `devicestatus`;
CREATE TABLE `devicestatus` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `devicestatus` VALUES (1,'inventory','Device in Swarms Inventory');
INSERT INTO `devicestatus` VALUES (2,'reseller','With Reseller');
INSERT INTO `devicestatus` VALUES (3,'deployed','Device has been deployed at a location');
INSERT INTO `devicestatus` VALUES (4,'lost','Device cannot be found');
INSERT INTO `devicestatus` VALUES (5,'returned','Device has been returned by Reseller/Location');
INSERT INTO `devicestatus` VALUES (6,'defective','Device is defective.');
