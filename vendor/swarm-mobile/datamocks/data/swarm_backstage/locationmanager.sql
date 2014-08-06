USE `swarm_backstage`;
/*
-- Query: SELECT lm.* 
FROM locationmanager lm
INNER JOIN locationmanager_location lml
ON lm.id = lml.location_id
AND lm.id IN (
	3,687,688,689,690,691,692, 
	693,1288,1340,1341,1342,50,
	51,63,64,70,71,230,231,232,
	233,262,266,330,331,911,1490,
	1491,1685,1686,768,769,1349,
	7,19,714,718,349,350,351,
	272,2039,1375
)
GROUP BY lm.id
LIMIT 0, 50000

-- Date: 2014-08-06 12:00
*/
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (3,35,'ZJ Boarding House','','310.392.5646','2014-07-03 20:26:55');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (7,39,'Sports LTD','','','2014-07-03 20:26:57');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (19,51,'O\'Neill Outlet - Las Vegas South','','','2014-07-03 20:27:02');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (50,82,'Soloma Demo Store','','0620519195','2014-07-03 20:27:15');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (51,83,'Atlantis Games and Comics - Portsmouth','','','2014-07-03 20:27:15');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (63,95,'mac-fusion','','','2014-07-03 20:27:19');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (64,96,'Laguna Beach Cyclery','','949-494-1522','2014-07-03 20:27:20');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (70,102,'iStore ','Greenville','864-276-4343','2014-07-05 20:04:56');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (71,103,'Simply Macintosh','','','2014-07-03 20:27:22');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (230,262,'Muscle Coach - West End','','13 000 687253','2014-07-03 20:28:21');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (231,263,'Ubertec','','+6493583801','2014-07-03 20:28:22');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (232,264,'nextexit23','','','2014-07-03 20:28:22');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (233,265,'nextexit25','','','2014-07-03 20:28:22');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (262,294,'VM - NSW Castle Hill Kiosk','','96595411','2014-07-03 20:28:34');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (266,298,'VM - QLD Carindale Store','','0435355310','2014-07-03 20:28:36');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (272,304,'Leona Edmiston - Paddington','','93317033','2014-07-03 20:28:38');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (330,362,'Precious Earth Eco Boutique','','','2014-07-03 20:29:00');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (349,381,'Corner Bike Shop','','','2014-07-03 20:29:07');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (350,382,'Bicycle Doctor','','','2014-07-03 20:29:08');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (351,383,'Spokesman Pro Bicycle Works','','985-727-7211','2014-07-03 20:29:08');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (687,719,'nextexit257','','','2014-07-03 20:31:51');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (688,720,'nextexit258','','','2014-07-03 20:31:51');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (689,721,'nextexit259','','','2014-07-03 20:31:52');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (690,722,'nextexit260','','','2014-07-03 20:31:53');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (691,723,'nextexit261','','','2014-07-03 20:31:53');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (692,724,'nextexit262','','','2014-07-03 20:31:54');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (693,725,'nextexit263','','','2014-07-03 20:31:54');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (714,746,'nextexit284','','','2014-07-03 20:32:06');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (718,750,'nextexit288','','','2014-07-03 20:32:08');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (768,800,'goodworks27','','','2014-07-03 20:32:37');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (769,801,'goodworks28','','','2014-07-03 20:32:37');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (911,943,'Lexington Public Library - 4','','','2014-07-03 20:34:00');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (1288,1320,'Chunking Accounts','','','2014-07-03 20:36:29');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (1340,1372,'CRS Box 4','','','2014-07-03 20:36:30');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (1341,1373,'CRS Box 5','','','2014-07-03 20:36:30');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (1342,1374,'CRS Box 6','','','2014-07-03 20:36:30');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (1349,1381,'CRS Box 13','','','2014-07-03 20:36:30');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (1375,1407,'G9 Jack 2','','','2014-07-03 20:36:31');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (1490,1633,'3432','432432',NULL,'2014-07-30 09:41:45');
INSERT INTO `locationmanager` (`id`,`user_id`,`firstname`,`lastname`,`phone_no`,`ts_update`) VALUES (1491,1634,'test','user',NULL,'2014-07-30 09:42:03');
