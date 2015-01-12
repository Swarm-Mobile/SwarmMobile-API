DROP TABLE IF EXISTS `deviceenvironment`;
CREATE TABLE `deviceenvironment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `deviceenvironment` VALUES (1,'E2C56DB5-DFFB-48D2-B060-D0F5A71096E0','demo');
INSERT INTO `deviceenvironment` VALUES (2,'45FB2AE1-A73B-4ECC-852D-DB5BDFCB4F1C','production');