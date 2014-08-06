USE `swarm_backstage`;
/*
-- Query: SELECT p.* 
FROM presence p
INNER JOIN device d 
ON p.device_id = d.id
AND (
	d.location_id IN (
		3,687,688,689,690,691,692, 
		693,1288,1340,1341,1342,50,
		51,63,64,70,71,230,231,232,
		233,262,266,330,331,911,1490,
		1491,1685,1686,768,769,1349,
		7,19,714,718,349,350,351,
		272,2039,1375
	) OR d.reseller_id IN(1,104,105)
)
-- Date: 2014-08-05 22:45
*/
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (1,1,'Cynergy Bicycles Presence','','',0,'2014-07-03 20:26:54');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (2,2,'Flight Club New York Presence','','',0,'2014-07-03 20:26:55');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (4,4,'Flight Club - LA Presence','','',0,'2014-07-03 20:26:56');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (12,12,'O\'Neill - Santa Monica Presence','','',0,'2014-07-03 20:26:59');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (13,13,'O\'Neill - Gardenwalk Presence','','',0,'2014-07-03 20:27:00');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (17,17,'O\'Neill Outlet - Citadel Presence','','',0,'2014-07-03 20:27:02');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (18,18,'O\'Neill Outlet - Las Vegas North Presence','','',0,'2014-07-03 20:27:02');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (19,19,'O\'Neill Outlet - Las Vegas South Presence','','',0,'2014-07-03 20:27:02');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (20,20,'O\'Neill Outlet - Lake Elsinore Presence','','',0,'2014-07-03 20:27:03');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (24,24,'O\'Neill Outlet - Las Americas Presence','','',0,'2014-07-03 20:27:04');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (25,25,'O\'Neill Outlet - Ontario Mills Presence','','',0,'2014-07-03 20:27:05');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (26,26,'O\'Neill Outlet - Orange Presence','','',0,'2014-07-03 20:27:05');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (27,27,'O\'Neill Outlet - Tulare Presence','','',0,'2014-07-03 20:27:06');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (43,43,'O\'Neill - Tempe (Arizona Mills) Presence','','',0,'2014-07-03 20:27:12');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (45,45,'O\'Neill HQ Presence','','',0,'2014-07-03 20:27:13');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (47,47,'Mac911 Presence','','',0,'2014-07-03 20:27:14');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (81,81,'O\'Neill Flagship - GardenWalk Presence','','',0,'2014-07-03 20:27:26');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (82,82,'O\'Neill Las Vegas Presence','','',0,'2014-07-03 20:27:27');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (90,90,'Superette - Ponsonby Presence','','',0,'2014-07-03 20:27:29');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (91,91,'Superette - Newmarket Presence','','',0,'2014-07-03 20:27:30');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (92,92,'Superette - Wellington Presence','','',0,'2014-07-03 20:27:30');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (336,336,'Cinnamon Girl - Ward Warehouse Presence','','',0,'2014-07-03 20:29:02');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (337,337,'Cinnamon Girl - Ala Moana Center Presence','','',0,'2014-07-03 20:29:03');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (338,338,'Cinnamon Girl - Kahala Mall Presence','','',0,'2014-07-03 20:29:03');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (339,339,'Cinnamon Girl - Pearlridge Center Presence','','',0,'2014-07-03 20:29:03');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (340,340,'Cinnamon Girl - Whalers Village Presence','','',0,'2014-07-03 20:29:04');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (341,341,'Cinnamon Girl - Kings Shops Presence','','',0,'2014-07-03 20:29:04');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (342,342,'33 Butterflies Presence','','',0,'2014-07-03 20:29:04');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (354,354,'Marquee NY Presence','','',0,'2014-07-03 20:29:09');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (357,357,'Tash Inc. Presence','','',0,'2014-07-03 20:29:11');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (375,375,'The Dog Bakery - Pasadena Presence','','',0,'2014-07-03 20:29:19');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (376,376,'The Dog Bakery - Los Angeles Presence','','',0,'2014-07-03 20:29:19');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (436,436,'O\'Neill Outlet - Sawgrass Presence','','',0,'2014-07-03 20:29:44');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (726,726,'Frye - 113 Spring St. (SoHo) Presence','','',0,'2014-07-03 20:32:13');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (773,773,'Frye - Boston Presence','','',0,'2014-07-03 20:32:39');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (774,774,'Frye - Chicago Presence','','',0,'2014-07-03 20:32:40');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (775,775,'Frye - Georgetown Presence','','',0,'2014-07-03 20:32:40');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (778,778,'The Dog Bakery - Venice Presence','','',0,'2014-07-03 20:32:42');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (792,792,'Vanguard Surf & Skate Presence','','',0,'2014-07-03 20:32:50');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (868,868,'O\'Neill - Palm Beach Presence','','',0,'2014-07-03 20:33:34');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (869,869,'O\'Neill - iDrive Presence','','',0,'2014-07-03 20:33:35');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (994,994,'O\'Neill - Grapevine Presence','','',0,'2014-07-03 20:34:49');
INSERT INTO `presence` (`id`,`device_id`,`name`,`description`,`version`,`network_id`,`ts_update`) VALUES (995,995,'O\'Neill - Milpitas Presence','','',0,'2014-07-03 20:34:49');
