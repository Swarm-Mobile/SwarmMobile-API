USE `swarm_backstage`;
/*
-- Query: SELECT p.* 
FROM portal p
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
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (4,1336,'Portal - CE63109597101830','Imported on 2014-07-25 20:02:29 with portals_jul_25.csv','1.07','2014-07-25 20:03:33');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (8,1340,'Portal - CE63109597111930','Imported on 2014-07-25 20:02:29 with portals_jul_25.csv','1.07','2014-07-25 20:03:33');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (9,1341,'Portal - CE63109597112230','Imported on 2014-07-25 20:02:29 with portals_jul_25.csv','1.07','2014-07-25 20:03:33');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (14,1346,'Portal - CE63109597172D36','Imported on 2014-07-25 20:02:29 with portals_jul_25.csv','1.07','2014-07-25 20:03:33');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (22,1354,'Portal - CE631095971A2632','Imported on 2014-07-25 20:02:29 with portals_jul_25.csv','1.07','2014-07-25 20:03:33');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (26,1358,'Portal - CE631095971B3532','Imported on 2014-07-25 20:02:29 with portals_jul_25.csv','1.07','2014-07-25 20:03:33');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (29,1361,'Portal - CE63109597222936','Imported on 2014-07-25 20:02:29 with portals_jul_25.csv','1.07','2014-07-25 20:03:33');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (30,1362,'Portal - CE63109597223133','Imported on 2014-07-25 20:02:29 with portals_jul_25.csv','1.07','2014-07-25 20:03:33');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (36,1368,'Portal - CE63109597283032','Imported on 2014-07-25 20:02:30 with portals_jul_25.csv','1.07','2014-07-25 20:03:34');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (56,1388,'Portal - CE63109597303B36','Imported on 2014-07-25 20:02:30 with portals_jul_25.csv','1.07','2014-07-25 20:03:34');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (59,1391,'Portal - CE63109597322535','Imported on 2014-07-25 20:02:30 with portals_jul_25.csv','1.07','2014-07-25 20:03:34');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (60,1392,'Portal - CE63109597322A35','Imported on 2014-07-25 20:02:30 with portals_jul_25.csv','1.07','2014-07-25 20:03:34');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (63,1395,'Portal - CE63109597344133','Imported on 2014-07-25 20:02:30 with portals_jul_25.csv','1.07','2014-07-25 20:03:34');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (66,1398,'Portal - CE63109597351436','Imported on 2014-07-25 20:02:30 with portals_jul_25.csv','1.07','2014-07-25 20:03:34');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (77,1409,'Portal - CE631095973B2133','Imported on 2014-07-25 20:02:30 with portals_jul_25.csv','1.07','2014-07-25 20:03:34');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (87,1419,'Portal - CE63109597431732','Imported on 2014-07-25 20:02:30 with portals_jul_25.csv','1.07','2014-07-25 20:03:34');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (98,1430,'Portal - CE63109597473C31','Imported on 2014-07-25 20:02:31 with portals_jul_25.csv','1.07','2014-07-25 20:03:35');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (110,1442,'Portal - CE631095974C3B36','Imported on 2014-07-25 20:02:31 with portals_jul_25.csv','1.07','2014-07-25 20:03:35');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (111,1443,'Portal - CE631095974D3532','Imported on 2014-07-25 20:02:31 with portals_jul_25.csv','1.07','2014-07-25 20:03:35');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (112,1444,'Portal - CE631095974F1832','Imported on 2014-07-25 20:02:31 with portals_jul_25.csv','1.07','2014-07-25 20:03:35');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (116,1448,'Portal - CE63109597541336','Imported on 2014-07-25 20:02:31 with portals_jul_25.csv','1.07','2014-07-25 20:03:35');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (138,1470,'Portal - CE63109597630730','Imported on 2014-07-25 20:02:31 with portals_jul_25.csv','1.07','2014-07-25 20:03:35');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (140,1472,'Portal - CE63109597640B30','Imported on 2014-07-25 20:02:31 with portals_jul_25.csv','1.07','2014-07-25 20:03:35');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (142,1474,'Portal - CE63109597651730','Imported on 2014-07-25 20:02:31 with portals_jul_25.csv','1.07','2014-07-25 20:03:35');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (154,1486,'Portal - CE631095976F3435','Imported on 2014-07-25 20:02:31 with portals_jul_25.csv','1.07','2014-07-25 20:03:35');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (160,1492,'Portal - CE63109597722336','Imported on 2014-07-25 20:02:31 with portals_jul_25.csv','1.07','2014-07-25 20:03:35');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (181,1513,'Portal - CE631095977E1836','Imported on 2014-07-25 20:02:31 with portals_jul_25.csv','1.07','2014-07-25 20:03:35');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (182,1514,'Portal - CE631095977E2431','Imported on 2014-07-25 20:02:31 with portals_jul_25.csv','1.07','2014-07-25 20:03:35');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (315,1655,'Portal - CE631095971A3033','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (316,1656,'Portal - CE631095975C0631','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (317,1657,'Portal - CE631095970D3533','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (318,1658,'Portal - CE63109597192636','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (319,1659,'Portal - CE631095974B4431','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (320,1660,'Portal - CE63109597141532','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (321,1661,'Portal - CE63109597462031','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (322,1662,'Portal - CE63109597193236','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (323,1663,'Portal - CE631095977D3630','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (324,1664,'Portal - CE63109597744330','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (325,1665,'Portal - CE63109597182A36','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (326,1666,'Portal - CE63109597582432','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (327,1667,'Portal - CE631095976B1B35','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (328,1668,'Portal - CE63109597242E33','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (329,1669,'Portal - CE63109597592c30','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (330,1670,'Portal - CE631095976A1332','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (331,1671,'Portal - CE63109597442732','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (332,1672,'Portal - CE631095972B0F30','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (333,1673,'Portal - CE63109597540F35','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (334,1674,'Portal - CE631095976B3C35','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (335,1675,'Portal - CE63109597352F33','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (336,1676,'Portal - CE63109597411D32','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (337,1677,'Portal - CE63109597604330','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (338,1678,'Portal - CE631095972E3633','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (339,1679,'Portal - CE63109597191532','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (340,1680,'Portal - CE631095971B3931','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (341,1681,'Portal - CE63109597510532','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (342,1682,'Portal - CE63109597141D32','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (343,1683,'Portal - CE63109597631732','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:28');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (344,1684,'Portal - CE63109597611336','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:29');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (345,1685,'Portal - CE63109597721935','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:29');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (346,1686,'Portal - CE63109597600F36','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:29');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (347,1687,'Portal - CE63109597614536','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:29');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (348,1688,'Portal - CE63109597472632','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:29');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (349,1689,'Portal - CE631095973C2236','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:29');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (350,1690,'Portal - CE63109597121530','Imported on 2014-08-05 18:32:46 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:29');
INSERT INTO `portal` (`id`,`device_id`,`name`,`description`,`version`,`ts_update`) VALUES (476,1816,'Portal - CE63109597512D36','Imported on 2014-08-05 18:32:48 with portals_Aug4_revised_comma.csv','1.07','2014-08-05 18:34:30');
