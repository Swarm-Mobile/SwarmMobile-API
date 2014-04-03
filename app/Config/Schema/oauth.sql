CREATE SCHEMA IF NOT EXISTS 'oauth';
USE oauth;

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

DROP TABLE IF EXISTS `access_tokens`;
CREATE TABLE IF NOT EXISTS `access_tokens` (
  `oauth_token` varchar(40) NOT NULL,
  `client_id` char(36) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `expires` int(11) NOT NULL,
  `scope` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`oauth_token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `access_tokens` (`oauth_token`, `client_id`, `user_id`, `expires`, `scope`) VALUES
('d2bb168dd2a3d5bc826199759db4258016812687', 'NTMzOGY4ZTYxOGI1YTVi', 10, 1396495514, ''),
('6ae5f5894ba007909c47df017d42136ad79fdbea', 'NTMzOGY4ZTYxOGI1YTVi', 10, 1396495543, '');

DROP TABLE IF EXISTS `auth_codes`;
CREATE TABLE IF NOT EXISTS `auth_codes` (
  `code` varchar(40) NOT NULL,
  `client_id` char(36) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `redirect_uri` varchar(200) NOT NULL,
  `expires` int(11) NOT NULL,
  `scope` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `auth_codes` (`code`, `client_id`, `user_id`, `redirect_uri`, `expires`, `scope`) VALUES
('a7bfb29bf2d20e6c03f62da20e762a996f403776', 'NTMzOGY4ZTYxOGI1YTVi', 10, 'http://localhost/cake/pages/dump', 1396464098, '');

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `client_id` char(20) NOT NULL,
  `client_secret` char(40) NOT NULL,
  `redirect_uri` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `clients` (`client_id`, `client_secret`, `redirect_uri`, `user_id`) VALUES
('NTMzOGY4ZTYxOGI1YTVi', '7cebea7b8d1db5eded8977f1888d404d0a11199b', 'http://localhost/cake/pages/dump', 10);

DROP TABLE IF EXISTS `refresh_tokens`;
CREATE TABLE IF NOT EXISTS `refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` char(36) NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `expires` int(11) NOT NULL,
  `scope` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`refresh_token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `refresh_tokens` (`refresh_token`, `client_id`, `user_id`, `expires`, `scope`) VALUES
('623eae8bb5a9b453cfe8fb307f2a57ebae8c17dc', 'NTMzOGY4ZTYxOGI1YTVi', 10, 1397701543, '');

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

INSERT INTO `users` (`id`, `username`, `password`, `ts_creation`, `ts_update`) VALUES
(10, 'swarm', '2e19bc3456708ae986287383f6f6abd7929dc257', '2014-04-01 07:00:00', '2014-04-01 19:20:51');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
