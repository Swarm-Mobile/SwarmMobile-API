CREATE DATABASE  IF NOT EXISTS `swarm_backstage` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `swarm_backstage`;
-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: localhost    Database: swarm_backstage
-- ------------------------------------------------------
-- Server version	5.5.27-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `pos`
--

DROP TABLE IF EXISTS `pos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pos` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `label` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos`
--

LOCK TABLES `pos` WRITE;
/*!40000 ALTER TABLE `pos` DISABLE KEYS */;
INSERT INTO `pos` VALUES (1,'vend','Vend','2014-07-03 20:26:54'),(2,'mos','Merchant OS','2014-07-03 20:26:54');
/*!40000 ALTER TABLE `pos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploaddetail_fileextension`
--

DROP TABLE IF EXISTS `uploaddetail_fileextension`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploaddetail_fileextension` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uploaddetail_id` tinyint(10) unsigned NOT NULL,
  `fileextension_id` tinyint(10) unsigned NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uploaddetail_id` (`uploaddetail_id`,`fileextension_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploaddetail_fileextension`
--

LOCK TABLES `uploaddetail_fileextension` WRITE;
/*!40000 ALTER TABLE `uploaddetail_fileextension` DISABLE KEYS */;
INSERT INTO `uploaddetail_fileextension` VALUES (13,2,1,'2014-07-03 20:57:48'),(14,2,2,'2014-07-03 20:57:48'),(15,2,3,'2014-07-03 20:57:48'),(22,1,1,'2014-08-05 02:47:14'),(23,1,2,'2014-08-05 02:47:14'),(24,1,3,'2014-08-05 02:47:14');
/*!40000 ALTER TABLE `uploaddetail_fileextension` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currency` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `label` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currency`
--

LOCK TABLES `currency` WRITE;
/*!40000 ALTER TABLE `currency` DISABLE KEYS */;
INSERT INTO `currency` VALUES (1,'$','$','2014-07-03 20:26:53'),(2,'Â£','Â£','2014-07-03 20:26:53'),(3,'â‚¬','â‚¬','2014-07-03 20:26:53'),(4,'â‚«','â‚«','2014-07-03 20:26:53'),(5,'AED','AED','2014-07-03 20:26:53'),(6,'KWD','KWD','2014-07-03 20:26:53'),(7,'SEK','SEK','2014-07-03 20:26:53'),(8,'DKK','DKK','2014-07-03 20:26:53'),(9,'NOK','NOK','2014-07-03 20:26:53');
/*!40000 ALTER TABLE `currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `superadmin`
--

DROP TABLE IF EXISTS `superadmin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `superadmin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone_no` varchar(255) DEFAULT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `superadmin`
--

LOCK TABLES `superadmin` WRITE;
/*!40000 ALTER TABLE `superadmin` DISABLE KEYS */;
/*!40000 ALTER TABLE `superadmin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devicestatus`
--

DROP TABLE IF EXISTS `devicestatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devicestatus` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devicestatus`
--

LOCK TABLES `devicestatus` WRITE;
/*!40000 ALTER TABLE `devicestatus` DISABLE KEYS */;
INSERT INTO `devicestatus` VALUES (1,'inventory','Device in Swarm\'s Inventory'),(2,'reseller','With Reseller'),(3,'deployed','Device has been deployed at a location'),(4,'lost','Device cannot be found'),(5,'returned','Device has been returned by Reseller/Location'),(6,'dead','Device has died.');
/*!40000 ALTER TABLE `devicestatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `iab1`
--

DROP TABLE IF EXISTS `iab1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iab1` (
  `id` mediumint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `iab1`
--

LOCK TABLES `iab1` WRITE;
/*!40000 ALTER TABLE `iab1` DISABLE KEYS */;
INSERT INTO `iab1` VALUES (1,'Arts & Entertainment'),(2,'Automotive'),(3,'Business'),(4,'Careers'),(5,'Education'),(6,'Family & Parenting'),(7,'Health & Fitness'),(8,'Food & Drink'),(9,'Hobbies & Interests'),(10,'Home & Garden'),(11,'Law, Gov\'t & Politics'),(12,'News'),(13,'Personal Finance'),(14,'Society'),(15,'Science'),(16,'Pets'),(17,'Sports'),(18,'Style & Fashion'),(19,'Technology & Computing'),(20,'Travel'),(21,'Real Estate'),(22,'Shopping'),(23,'Religion & Spirituality'),(24,'Uncategorized');
/*!40000 ALTER TABLE `iab1` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `iab2`
--

DROP TABLE IF EXISTS `iab2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iab2` (
  `id` mediumint(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `iab1_id` mediumint(4) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `name_2` (`name`,`iab1_id`)
) ENGINE=InnoDB AUTO_INCREMENT=357 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `iab2`
--

LOCK TABLES `iab2` WRITE;
/*!40000 ALTER TABLE `iab2` DISABLE KEYS */;
INSERT INTO `iab2` VALUES (276,'3-D Graphics',19),(54,'7-12 Education',5),(79,'A.D.D.',7),(275,'Accessories',18),(69,'Adoption',6),(55,'Adult Education',5),(312,'Adventure Travel',20),(31,'Advertising',3),(313,'Africa',20),(32,'Agriculture',3),(80,'AIDS/HIV',7),(314,'Air Travel',20),(81,'Allergies',7),(82,'Alternative Medicine',7),(346,'Alternative Religions',23),(123,'American Cuisine',8),(277,'Animation',19),(278,'Antivirus Software',19),(339,'Apartments',21),(172,'Appliances',10),(219,'Aquariums',16),(340,'Architects',21),(56,'Art History',5),(141,'Art/Technology',9),(83,'Arthritis',7),(142,'Arts & Crafts',9),(84,'Asthma',7),(209,'Astrology',15),(347,'Atheism/Agnosticism',23),(315,'Australia & New Zealand',20),(85,'Autism/PDD',7),(8,'Auto Parts',2),(226,'Auto Racing',17),(9,'Auto Repair',2),(70,'Babies & Toddlers',6),(124,'Barbecues & Grilling',8),(227,'Baseball',17),(143,'Beadwork',9),(270,'Beauty',18),(316,'Bed & Breakfasts',20),(189,'Beginning Investing',13),(228,'Bicycling',17),(210,'Biology',15),(33,'Biotech/Biomedical',3),(86,'Bipolar Disorder',7),(220,'Birds',16),(144,'Birdwatching',9),(145,'Board Games/Puzzles',9),(271,'Body Art',18),(229,'Bodybuilding',17),(1,'Books & Literature',1),(217,'Botany',15),(230,'Boxing',17),(87,'Brain Tumor',7),(348,'Buddhism',23),(317,'Budget Travel',20),(34,'Business Software',3),(318,'Business Travel',20),(10,'Buying/Selling Cars',2),(341,'Buying/Selling Homes',21),(319,'By US Locale',20),(279,'C/C++',19),(125,'Cajun/Creole',8),(280,'Cameras & Camcorders',19),(320,'Camping',20),(321,'Canada',20),(88,'Cancer',7),(146,'Candle & Soap Making',9),(231,'Canoeing/Kayaking',17),(11,'Car Culture',2),(147,'Card Games',9),(53,'Career Advice',4),(43,'Career Planning',4),(322,'Caribbean',20),(349,'Catholicism',23),(221,'Cats',16),(2,'Celebrity Fan/Gossip',1),(281,'Cell Phones',19),(12,'Certified Pre-Owned',2),(232,'Cheerleading',17),(211,'Chemistry',15),(148,'Chess',9),(126,'Chinese Cuisine',8),(89,'Cholesterol',7),(350,'Christianity',23),(90,'Chronic Fatigue Syndrome',7),(91,'Chronic Pain',7),(149,'Cigars',9),(233,'Climbing',17),(274,'Clothing',18),(127,'Cocktails/Beer',8),(128,'Coffee/Tea',8),(92,'Cold & Flu',7),(150,'Collecting',9),(57,'Colledge Administration',5),(44,'College',4),(58,'College Life',5),(151,'Comic Books',9),(185,'Commentary',11),(344,'Comparison',22),(282,'Computer Certification',19),(283,'Computer Networking',19),(284,'Computer Peripherals',19),(285,'Computer Reviews',19),(35,'Construction',3),(342,'Contests & Freebies',22),(13,'Convertible',2),(14,'Coupe',2),(343,'Couponing',22),(190,'Credit/Debt & Loans',13),(234,'Cricket',17),(15,'Crossover',2),(323,'Cruises',20),(129,'Cuisine-Specific',8),(286,'Data Centers',19),(287,'Databases',19),(201,'Dating',14),(71,'Daycare/Pre School',6),(93,'Deafness',7),(94,'Dental Care',7),(95,'Depression',7),(96,'Dermatology',7),(288,'Desktop Publishing',19),(289,'Desktop Video',19),(130,'Desserts & Baking',8),(97,'Diabetes',7),(16,'Diesel',2),(131,'Dining Out',8),(59,'Distance Learning',5),(202,'Divorce Support',14),(222,'Dogs',16),(152,'Drawing/Sketching',9),(324,'Eastern Europe',20),(77,'Eldercare',6),(17,'Electric Vehicle',2),(290,'Email',19),(345,'Engines',22),(60,'English as a 2nd Language',5),(173,'Entertaining',10),(304,'Entertainment',19),(174,'Environmental Safety',10),(98,'Epilepsy',7),(208,'Ethnic Specific',14),(325,'Europe',20),(78,'Exercise',7),(72,'Family Internet',6),(272,'Fashion',18),(235,'Figure Skating',17),(45,'Financial Aid',4),(191,'Financial News',13),(192,'Financial Planning',13),(3,'Fine Art',1),(236,'Fly Fishing',17),(132,'Food Allergies',8),(237,'Football',17),(36,'Forestry',3),(326,'France',20),(153,'Freelance Writing',9),(133,'French Cuisine',8),(238,'Freshwater Fishing',17),(239,'Game & Fish',17),(175,'Gardening',10),(203,'Gay Life',14),(154,'Genealogy',9),(216,'Geography',15),(212,'Geology',15),(99,'GERD/Acid Reflux',7),(155,'Getting Published',9),(240,'Golf',17),(37,'Government',3),(62,'Graduate School',5),(291,'Graphics Software',19),(327,'Greece',20),(38,'Green Solutions',3),(156,'Guitar',9),(18,'Hatchback',2),(100,'Headaches/Migraines',7),(134,'Health/Lowfat Cooking',8),(101,'Heart Disease',7),(193,'Hedge Fund',13),(102,'Herbs for Health',7),(351,'Hinduism',23),(103,'Holistic Healing',7),(157,'Home Recording',9),(176,'Home Repair',10),(177,'Home Theater',10),(292,'Home Video/DVD',19),(63,'Homeschooling',5),(64,'Homework/Study Tips',5),(328,'Honeymoons/Getaways',20),(241,'Horse Racing',17),(242,'Horses',17),(329,'Hotels',20),(39,'Human Resources',3),(4,'Humor',1),(243,'Hunting/Shooting',17),(19,'Hybrid',2),(104,'IBS/Crohn\'s Disease',7),(181,'Immigration',11),(105,'Incest/Abuse Support',7),(106,'Incontinence',7),(107,'Infertility',7),(244,'Inline Skating',17),(194,'Insurance',13),(178,'Interior Decorating',10),(186,'International News',12),(293,'Internet Technology',19),(195,'Investing',13),(158,'Investors & Patents',9),(352,'Islam',23),(135,'Italian Cuisine',8),(330,'Italy',20),(331,'Japan',20),(136,'Japanese Cuisine',8),(294,'Java',19),(295,'JavaScript',19),(273,'Jewelry',18),(159,'Jewelry Making',9),(46,'Job Fairs',4),(47,'Job Search',4),(353,'Judaism',23),(65,'K-6 Educators',5),(179,'Landscaping',10),(61,'Language Learning',5),(223,'Large Animals',16),(354,'Latter-Day Saints',23),(182,'Legal Issues',11),(188,'Local News',12),(40,'Logistics',3),(20,'Luxury',2),(296,'Mac Support',19),(160,'Magic & Illusion',9),(41,'Marketing',3),(204,'Marriage',14),(245,'Martial Arts',17),(108,'Men\'s Health',7),(42,'Metals',3),(137,'Mexican Cuisine',8),(332,'Mexico & Central America',20),(21,'MiniVan',2),(22,'Mororcycles',2),(246,'Mountain Biking',17),(5,'Movies',1),(297,'MP3/MIDI',19),(6,'Music',1),(196,'Mutual Funds',13),(247,'NASCAR Racing',17),(187,'National News',12),(333,'National Parks',20),(161,'Needlework',9),(298,'Net Conferencing',19),(299,'Net for Beginners',19),(300,'Network Security',19),(49,'Nursing',4),(109,'Nutrition',7),(23,'Off-Road Vehicles',2),(248,'Olympics',17),(197,'Options',13),(110,'Orthopedics',7),(355,'Pagan/Wiccan',23),(249,'Paintball',17),(162,'Painting',9),(301,'Palmtops/PDAs',19),(111,'Panic/Anxiety Disorders',7),(213,'Paranormal Phenomena',15),(73,'Parenting - K-6 Kids',6),(74,'Parenting teens',6),(302,'PC Support',19),(112,'Pediatrics',7),(24,'Performance Vehicles',2),(163,'Photography',9),(113,'Physical Therapy',7),(214,'Physics',15),(25,'Pickup',2),(184,'Politics',11),(303,'Portable',19),(250,'Power & Motorcycles',17),(75,'Pregnancy',6),(66,'Private School',5),(251,'Pro Basketball',17),(252,'Pro Ice Hockey',17),(114,'Psychology/Psychiatry',7),(164,'Radio',9),(180,'Remodeling & Construction',10),(224,'Reptiles',16),(48,'Resume Writing/Advice',4),(198,'Retirement Planning',13),(26,'Road-Side Assistance',2),(253,'Rodeo',17),(165,'Roleplaying Games',9),(254,'Rugby',17),(255,'Running/Jogging',17),(256,'Sailing',17),(257,'Saltwater Fishing',17),(50,'Scholarships',4),(166,'Sci-Fi & Fantasy',9),(167,'Scrapbooking',9),(168,'Screenwriting',9),(258,'Scuba Diving',17),(27,'Sedan',2),(205,'Senior Living',14),(115,'Senor Health',7),(116,'Sexuality',7),(305,'Shareware/Freeware',19),(259,'Skateboarding',17),(260,'Skiing',17),(117,'Sleep Disorders',7),(118,'Smoking Cessation',7),(261,'Snowboarding',17),(334,'South America',20),(215,'Space/Astronomy',15),(335,'Spas',20),(67,'Special Education',5),(76,'Special Needs Kids',6),(169,'Stamps & Coins',9),(199,'Stocks',13),(68,'Studying Business',5),(119,'Substance Abuse',7),(262,'Surfing/Bodyboarding',17),(263,'Swimming',17),(264,'Table Tennis/Ping-Pong',17),(200,'Tax Planning',13),(206,'Teens',14),(51,'Telecommuting',4),(7,'Television',1),(265,'Tennis',17),(336,'Theme Parks',20),(120,'Thyroid Disease',7),(337,'Traveling with Kids',20),(28,'Trucks & Accessories',2),(183,'U.S. Government Resources',11),(52,'U.S. Military',4),(356,'Uncategorized',24),(338,'United Kingdom',20),(306,'Unix',19),(138,'Vegan',8),(139,'Vegetarian',8),(225,'Veterinary Medicine',16),(170,'Video & Computer Games',9),(29,'Vintage Cars',2),(307,'Visual Basic',19),(266,'Volleyball',17),(30,'Wagon',2),(267,'Walking',17),(268,'Waterski/Wakeboard',17),(218,'Weather',15),(308,'Web Clip Art',19),(309,'Web Design/HTML',19),(310,'Web Search',19),(207,'Weddings',14),(121,'Weight Loss',7),(311,'Windows',19),(140,'Wine',8),(122,'Women\'s Health',7),(171,'Woodworking',9),(269,'World Soccer',17);
/*!40000 ALTER TABLE `iab2` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `setting`
--

DROP TABLE IF EXISTS `setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `settinggroup_id` tinyint(10) unsigned NOT NULL,
  `default` varchar(255) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `settinggroup_id` (`settinggroup_id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `setting`
--

LOCK TABLES `setting` WRITE;
/*!40000 ALTER TABLE `setting` DISABLE KEYS */;
INSERT INTO `setting` VALUES (1,'Adress Line 1','address1','Address line 1 of the location',2,'','2014-07-03 20:26:54','2014-07-11 01:47:06'),(2,'Address Line 2','address2','Address line 2 of the location',2,'','2014-07-03 20:26:54','2014-07-11 01:47:15'),(3,'City','city','City of the location',2,'','2014-07-03 20:26:54','2014-07-11 01:47:46'),(4,'State','state','State of the location',2,'','2014-07-03 20:26:54','2014-07-11 01:47:54'),(5,'Zip Code','zipcode','Zipcode of the location',2,'','2014-07-03 20:26:54','2014-07-11 01:48:01'),(6,'Network ID','network_id','Presence device network id',6,'','2014-07-03 20:26:54','2014-07-11 01:48:39'),(7,'Timezone','timezone','Timezone in which all the location data is presented to the user',3,'America/Los_Angeles','2014-07-03 20:26:54','2014-07-11 01:49:48'),(8,'Email Gate Wifi Access?','email_gate_wifi','Have 10% Off email on Instant App?',1,'','2014-07-03 20:26:54','2014-07-11 02:16:07'),(9,'Agree to the terms?','terms_disagree','Customer agree\'s to Swarm\'s terms',6,'yes','2014-07-03 20:26:54','2014-07-11 01:50:23'),(10,'Stripe Customer ID','stripe_customer_id','Customer\'s stripe id',4,'','2014-07-03 20:26:54','2014-07-11 02:05:23'),(11,'POS Store ID','pos_store_id','POS store ID',5,'','2014-07-03 20:26:54','2014-07-11 02:05:23'),(12,'Monday Open','monday_open','Time the location opens the business on Monday',3,'09:00','2014-07-03 20:26:54','2014-07-11 01:51:54'),(13,'Monday Close','monday_close','Time the location closes the business on Monday',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:23'),(14,'Tuesday Open','tuesday_open','Time the location opens the business on Tuesday',3,'09:00','2014-07-03 20:26:54','2014-07-11 02:05:23'),(15,'Tuesday Close','tuesday_close','Time the location closes the business on Tuesdays',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:23'),(16,'Wednesday Open','wednesday_open','Time the location opens the business on Wednesdays',3,'09:00','2014-07-03 20:26:54','2014-07-11 02:05:23'),(17,'Wednesday Close','wednesday_close','Time the location closes the business on Wednesdays',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:23'),(18,'Thursday Open','thursday_open','Time the location opens the business on Thursdays',3,'09:00','2014-07-03 20:26:54','2014-07-11 02:05:24'),(19,'Thursday Close','thursday_close','Time the location closes the business on Thursdays',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:24'),(20,'Friday Open','friday_open','Time the location opens the business on Fridays',3,'09:00','2014-07-03 20:26:54','2014-07-11 02:05:24'),(21,'Friday Close','friday_close','Time the location closes the business on Fridays',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:24'),(22,'Saturday Open','saturday_open','Time the location opens the business on Saturdays',3,'09:00','2014-07-03 20:26:54','2014-07-11 02:05:24'),(23,'Saturday Close','saturday_close','Time the location closes the business on Saturdays',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:24'),(24,'Sunday Open','sunday_open','Time the location opens the business on Sundays',3,'09:00','2014-07-03 20:26:54','2014-07-11 02:05:24'),(25,'Sunday Close','sunday_close','Time the location closes the business on Sundays',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:24'),(26,'Industry','industry','Location Industry Type (Name)',1,'Retail','2014-07-03 20:26:54','2014-07-11 02:05:24'),(27,'Currency','currency','Location currency',1,'$','2014-07-03 20:26:54','2014-07-11 02:05:24'),(28,'Customer\'s Terms of Service','customer_terms','Location customer terms of service',6,'','2014-07-03 20:26:54','2014-07-11 02:05:25'),(29,'Network Provider','network_provider','Location wifi network provider',6,'','2014-07-03 20:26:54','2014-07-11 02:05:25'),(30,'Register Filter','register_filter','Location register_id for POS',5,'','2014-07-03 20:26:54','2014-07-11 02:05:25'),(31,'Outlet Filter','outlet_filter','Location outlet_id for POS',5,'','2014-07-03 20:26:54','2014-07-11 02:05:25'),(32,'Country','country','Location Country (Address)',2,'','2014-07-03 20:26:54','2014-07-11 02:05:25'),(33,'Nightclub Hours?','nightclub_hours','Is a nightclub?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:25'),(34,'Correction Percentage','correction_percentage','Percentage of correction (in base 1)',6,'','2014-07-03 20:26:54','2014-07-11 02:05:25'),(35,'Email Gate Text','email_gate_text','Text received on location wifi access',1,'','2014-07-03 20:26:54','2014-07-11 02:05:25'),(36,'Unit of Measurement','unit_of_measurement','Location size unit of measurement (ft, m2)',1,'feet','2014-07-03 20:26:54','2014-07-11 02:05:25'),(37,'Size of Store','size_of_store','Location size (number)',6,'','2014-07-03 20:26:54','2014-07-11 02:05:25'),(38,'Distance To Front Door','distance_to_front_door','Distance from Presence to Front door',6,'','2014-07-03 20:26:54','2014-07-11 02:05:26'),(39,'Distance To Left Wall','distance_to_left_wall','Distance from Presence to Left Wall',6,'','2014-07-03 20:26:54','2014-07-11 02:05:26'),(40,'Distance To Right Wall','distance_to_right_wall','Distance from Presence to Right Wall',6,'','2014-07-03 20:26:54','2014-07-11 02:05:26'),(41,'Distance To Back Wall','distance_to_back_wall','Distance from Presence to Back Wall',6,'','2014-07-03 20:26:54','2014-07-11 02:05:26'),(42,'Shape of Store','shape_of_store','Shape of the store (Rectangular, Square, L)',6,'','2014-07-03 20:26:54','2014-07-11 02:05:26'),(43,'Estimated Daily Foot Traffic','estimated_daily_foot_traffic','Estimated number of people in store per day',6,'','2014-07-03 20:26:54','2014-07-11 02:05:26'),(44,'POS Provider','pos_provider','POS provider name',5,'','2014-07-03 20:26:54','2014-07-11 02:05:26'),(45,'POS Api Key','pos_api_key','POS API key',5,'','2014-07-03 20:26:54','2014-07-11 02:05:26'),(46,'Guest Wifi Off/On?','guest_wifi','Location have Guest Wi-Fi?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:26'),(47,'Wifi Network Name','wifi_name','Guest Wi-Fi name',1,'','2014-07-03 20:26:54','2014-07-11 02:05:27'),(48,'Wifi Password Yes/No?','wifi_password','Guest Wi-Fi password',1,'','2014-07-03 20:26:54','2014-07-11 02:05:27'),(49,'The Wifi Password (optional)','actual_wifi_password','Guest Wi-Fi password',1,'','2014-07-03 20:26:54','2014-07-11 02:05:27'),(50,'Use Instant App?','use_instant_app','Location uses InstantApp',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:27'),(51,'InstantApp Redirect URL','instant_app_redirect_url','Redirect URI after use InstantApp',6,'','2014-07-03 20:26:54','2014-07-11 02:05:27'),(52,'Meraki Org Validator','meraki_org_validator','Meraki ORG Validator',4,'','2014-07-03 20:26:54','2014-07-11 02:05:27'),(53,'Meraki Wifi Network Name','meraki_wifi_network_name','Meraki Wi-Fi name',4,'','2014-07-03 20:26:54','2014-07-11 02:05:27'),(54,'Setup Account Information Complete','setup_account_complete','Is the step Account complete on Setup?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:27'),(55,'Setup Store Hours Complete','setup_hours_complete','Is the step Store Hours complete on Setup?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:27'),(56,'Setup Calibration Information Complete','setup_calibration_complete','Is the step Calibration complete on Setup?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:27'),(57,'Setup POS Complete','setup_pos_complete','Is the step POS complete on Setup?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:28'),(58,'Setup Wireless Complete','setup_wireless_complete','Is the step Wireless complete on Setup?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:28'),(59,'Phone Number','phone','Location Phone',2,'','2014-07-03 20:26:54','2014-07-11 02:05:28'),(60,'POS Uplink Installed?','pos_uplink_installed','Location POS UpLink Installed',5,'no','2014-07-03 20:26:54','2014-07-11 02:05:28'),(61,'POS Login','pos_login','POS username login',5,'','2014-07-03 20:26:54','2014-07-11 02:05:28'),(62,'POS Password','pos_password','POS password login',5,'','2014-07-03 20:26:54','2014-07-11 02:05:28'),(63,'POS Login URL','pos_url','POS URL',5,'','2014-07-03 20:26:54','2014-07-11 02:05:28'),(64,'Connect Screen Variant','connect_screen_variant','InstantApp variant',1,'open_access','2014-07-03 20:26:54','2014-07-11 02:16:07'),(65,'Email Gate Subject','email_gate_subject','InstantApp email\'s subject',1,'','2014-07-03 20:26:54','2014-07-11 02:05:28'),(66,'Email Gate Content','email_gate_content','InstantApp email\'s content',1,'','2014-07-03 20:26:54','2014-07-11 02:05:28'),(67,'No Rollups?','no_rollups','Use rollups for fill metrics?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:29'),(68,'No Cache?','no_cache','Use cache for fil metrics?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:29'),(69,'Nighclub Hours Location','nightclub_hours_location','Nightclub Timezone Conversion',6,'eastcoast_time','2014-07-03 20:26:54','2014-07-11 02:05:29'),(70,'NAICS Code','naics_code','Location\'s NAICS code',1,'','2014-07-03 20:26:54','2014-07-11 02:05:29'),(71,'IAB Tier 1 Category','iab_tier_1_category','Location\'s IAB Tier 1',1,'','2014-07-03 20:26:54','2014-07-11 02:05:29'),(72,'IAB Tier 2 Category','iab_tier_2_category','Location\'s IAB Tier 2',1,'','2014-07-03 20:26:54','2014-07-11 02:05:29'),(73,'Brand 1','brand_1','Location\'s Brand 1',1,'','2014-07-03 20:26:54','2014-07-11 02:05:29'),(74,'Brand 2','brand_2','Location\'s Brand 2',1,'','2014-07-03 20:26:54','2014-07-11 02:05:29'),(75,'Brand 3','brand_3','Location\'s Brand 3',1,'','2014-07-03 20:26:54','2014-07-11 02:05:29'),(76,'Brand 4','brand_4','Location\'s Brand 4',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30'),(77,'Brand 5','brand_5','Location\'s Brand 5',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30'),(78,'Brand 6','brand_6','Location\'s Brand 6',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30'),(79,'Brand 7 ','brand_7','Location\'s Brand 7',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30'),(80,'Brand 8','brand_8','Location\'s Brand 8',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30'),(81,'Brand 9','brand_9','Location\'s Brand 9',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30'),(82,'Brand 10','brand_10','Location\'s Brand 10',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30'),(83,'POS Version','pos_version','POS version',5,'','2014-07-03 20:26:54','2014-07-11 02:05:30'),(84,'OS Version','os_version','Location\'s computer OS version',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30'),(85,'Count out of store hours transactions?','transactions_while_closed','Display transactions made it outside open hours?',6,'yes','2014-07-03 20:26:54','2014-07-11 02:05:31'),(86,'White Label Dashboard?','white_label_dashboard','Have the Location\'s dashboard custom logo?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:31'),(87,'Slug','slug','InstantApp URL\'s slug identificator',6,'','2014-07-03 20:26:54','2014-07-11 02:05:31'),(88,'Avatar Filename','avatar_filename','InstantApp Custom Background image',6,'','2014-07-03 20:26:54','2014-07-11 02:05:31'),(89,'Logo Filename','logo_filename','White Label Dashboard Logo',6,'','2014-07-03 20:26:54','2014-07-11 02:05:31'),(90,'Constant Contact Access Token','ctct_access_token','Constant Contact access_token',4,'','2014-07-03 20:26:54','2014-07-11 02:05:31'),(91,'Constant Contact Expires In','ctct_expires_in','Constant Contact token expires',4,'','2014-07-03 20:26:54','2014-07-11 02:05:31'),(92,'Constant Contact Token Type','ctct_token_type','Constant Contact token type',4,'','2014-07-03 20:26:54','2014-07-11 02:05:31'),(93,'Constant Contact Username','ctct_username','Constant Contact username',4,'','2014-07-03 20:26:54','2014-07-11 02:05:31');
/*!40000 ALTER TABLE `setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `devicetype`
--

DROP TABLE IF EXISTS `devicetype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devicetype` (
  `id` tinyint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `devicetype`
--

LOCK TABLES `devicetype` WRITE;
/*!40000 ALTER TABLE `devicetype` DISABLE KEYS */;
INSERT INTO `devicetype` VALUES (1,'Presence','WiFi sniffer device','Presence','2014-07-03 20:26:54','2014-07-03 20:26:54'),(2,'Portal','Infra-red foot traffic sensor','Portal','2014-07-03 20:26:54','2014-07-03 20:26:54'),(3,'Ping','BLE iBeacon','Ping','2014-07-03 20:26:54','2014-07-03 20:26:54');
/*!40000 ALTER TABLE `devicetype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fileextension`
--

DROP TABLE IF EXISTS `fileextension`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fileextension` (
  `id` tinyint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `extension` varchar(255) NOT NULL,
  `mime` varchar(255) NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fileextension`
--

LOCK TABLES `fileextension` WRITE;
/*!40000 ALTER TABLE `fileextension` DISABLE KEYS */;
INSERT INTO `fileextension` VALUES (1,'JPEG','.jpeg','image/jpeg','2014-07-03 20:26:54'),(2,'GIF','.gif','image/gif','2014-07-03 20:26:54'),(3,'PNG','.png','image/png','2014-07-03 20:26:54');
/*!40000 ALTER TABLE `fileextension` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deviceenvironment`
--

DROP TABLE IF EXISTS `deviceenvironment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deviceenvironment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deviceenvironment`
--

LOCK TABLES `deviceenvironment` WRITE;
/*!40000 ALTER TABLE `deviceenvironment` DISABLE KEYS */;
INSERT INTO `deviceenvironment` VALUES (1,'E2C56DB5-DFFB-48D2-B060-D0F5A71096E0','demo'),(2,'45FB2AE1-A73B-4ECC-852D-DB5BDFCB4F1C','production');
/*!40000 ALTER TABLE `deviceenvironment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(2) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=243 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `country`
--

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;
INSERT INTO `country` VALUES (1,'US','United States'),(2,'CA','Canada'),(3,'AF','Afghanistan'),(4,'AL','Albania'),(5,'DZ','Algeria'),(6,'DS','American Samoa'),(7,'AD','Andorra'),(8,'AO','Angola'),(9,'AI','Anguilla'),(10,'AQ','Antarctica'),(11,'AG','Antigua and/or Barbuda'),(12,'AR','Argentina'),(13,'AM','Armenia'),(14,'AW','Aruba'),(15,'AU','Australia'),(16,'AT','Austria'),(17,'AZ','Azerbaijan'),(18,'BS','Bahamas'),(19,'BH','Bahrain'),(20,'BD','Bangladesh'),(21,'BB','Barbados'),(22,'BY','Belarus'),(23,'BE','Belgium'),(24,'BZ','Belize'),(25,'BJ','Benin'),(26,'BM','Bermuda'),(27,'BT','Bhutan'),(28,'BO','Bolivia'),(29,'BA','Bosnia and Herzegovina'),(30,'BW','Botswana'),(31,'BV','Bouvet Island'),(32,'BR','Brazil'),(33,'IO','British lndian Ocean Territory'),(34,'BN','Brunei Darussalam'),(35,'BG','Bulgaria'),(36,'BF','Burkina Faso'),(37,'BI','Burundi'),(38,'KH','Cambodia'),(39,'CM','Cameroon'),(40,'CV','Cape Verde'),(41,'KY','Cayman Islands'),(42,'CF','Central African Republic'),(43,'TD','Chad'),(44,'CL','Chile'),(45,'CN','China'),(46,'CX','Christmas Island'),(47,'CC','Cocos (Keeling) Islands'),(48,'CO','Colombia'),(49,'KM','Comoros'),(50,'CG','Congo'),(51,'CK','Cook Islands'),(52,'CR','Costa Rica'),(53,'HR','Croatia (Hrvatska)'),(54,'CU','Cuba'),(55,'CY','Cyprus'),(56,'CZ','Czech Republic'),(57,'DK','Denmark'),(58,'DJ','Djibouti'),(59,'DM','Dominica'),(60,'DO','Dominican Republic'),(61,'TP','East Timor'),(62,'EC','Ecuador'),(63,'EG','Egypt'),(64,'SV','El Salvador'),(65,'GQ','Equatorial Guinea'),(66,'ER','Eritrea'),(67,'EE','Estonia'),(68,'ET','Ethiopia'),(69,'FK','Falkland Islands (Malvinas)'),(70,'FO','Faroe Islands'),(71,'FJ','Fiji'),(72,'FI','Finland'),(73,'FR','France'),(74,'FX','France, Metropolitan'),(75,'GF','French Guiana'),(76,'PF','French Polynesia'),(77,'TF','French Southern Territories'),(78,'GA','Gabon'),(79,'GM','Gambia'),(80,'GE','Georgia'),(81,'DE','Germany'),(82,'GH','Ghana'),(83,'GI','Gibraltar'),(84,'GR','Greece'),(85,'GL','Greenland'),(86,'GD','Grenada'),(87,'GP','Guadeloupe'),(88,'GU','Guam'),(89,'GT','Guatemala'),(90,'GN','Guinea'),(91,'GW','Guinea-Bissau'),(92,'GY','Guyana'),(93,'HT','Haiti'),(94,'HM','Heard and Mc Donald Islands'),(95,'HN','Honduras'),(96,'HK','Hong Kong'),(97,'HU','Hungary'),(98,'IS','Iceland'),(99,'IN','India'),(100,'ID','Indonesia'),(101,'IR','Iran (Islamic Republic of)'),(102,'IQ','Iraq'),(103,'IE','Ireland'),(104,'IL','Israel'),(105,'IT','Italy'),(106,'CI','Ivory Coast'),(107,'JM','Jamaica'),(108,'JP','Japan'),(109,'JO','Jordan'),(110,'KZ','Kazakhstan'),(111,'KE','Kenya'),(112,'KI','Kiribati'),(113,'KP','Korea, Democratic People\'s Republic of'),(114,'KR','Korea, Republic of'),(115,'XK','Kosovo'),(116,'KW','Kuwait'),(117,'KG','Kyrgyzstan'),(118,'LA','Lao People\'s Democratic Republic'),(119,'LV','Latvia'),(120,'LB','Lebanon'),(121,'LS','Lesotho'),(122,'LR','Liberia'),(123,'LY','Libyan Arab Jamahiriya'),(124,'LI','Liechtenstein'),(125,'LT','Lithuania'),(126,'LU','Luxembourg'),(127,'MO','Macau'),(128,'MK','Macedonia'),(129,'MG','Madagascar'),(130,'MW','Malawi'),(131,'MY','Malaysia'),(132,'MV','Maldives'),(133,'ML','Mali'),(134,'MT','Malta'),(135,'MH','Marshall Islands'),(136,'MQ','Martinique'),(137,'MR','Mauritania'),(138,'MU','Mauritius'),(139,'TY','Mayotte'),(140,'MX','Mexico'),(141,'FM','Micronesia, Federated States of'),(142,'MD','Moldova, Republic of'),(143,'MC','Monaco'),(144,'MN','Mongolia'),(145,'ME','Montenegro'),(146,'MS','Montserrat'),(147,'MA','Morocco'),(148,'MZ','Mozambique'),(149,'MM','Myanmar'),(150,'NA','Namibia'),(151,'NR','Nauru'),(152,'NP','Nepal'),(153,'NL','Netherlands'),(154,'AN','Netherlands Antilles'),(155,'NC','New Caledonia'),(156,'NZ','New Zealand'),(157,'NI','Nicaragua'),(158,'NE','Niger'),(159,'NG','Nigeria'),(160,'NU','Niue'),(161,'NF','Norfork Island'),(162,'MP','Northern Mariana Islands'),(163,'NO','Norway'),(164,'OM','Oman'),(165,'PK','Pakistan'),(166,'PW','Palau'),(167,'PA','Panama'),(168,'PG','Papua New Guinea'),(169,'PY','Paraguay'),(170,'PE','Peru'),(171,'PH','Philippines'),(172,'PN','Pitcairn'),(173,'PL','Poland'),(174,'PT','Portugal'),(175,'PR','Puerto Rico'),(176,'QA','Qatar'),(177,'RE','Reunion'),(178,'RO','Romania'),(179,'RU','Russian Federation'),(180,'RW','Rwanda'),(181,'KN','Saint Kitts and Nevis'),(182,'LC','Saint Lucia'),(183,'VC','Saint Vincent and the Grenadines'),(184,'WS','Samoa'),(185,'SM','San Marino'),(186,'ST','Sao Tome and Principe'),(187,'SA','Saudi Arabia'),(188,'SN','Senegal'),(189,'RS','Serbia'),(190,'SC','Seychelles'),(191,'SL','Sierra Leone'),(192,'SG','Singapore'),(193,'SK','Slovakia'),(194,'SI','Slovenia'),(195,'SB','Solomon Islands'),(196,'SO','Somalia'),(197,'ZA','South Africa'),(198,'GS','South Georgia South Sandwich Islands'),(199,'ES','Spain'),(200,'LK','Sri Lanka'),(201,'SH','St. Helena'),(202,'PM','St. Pierre and Miquelon'),(203,'SD','Sudan'),(204,'SR','Suriname'),(205,'SJ','Svalbarn and Jan Mayen Islands'),(206,'SZ','Swaziland'),(207,'SE','Sweden'),(208,'CH','Switzerland'),(209,'SY','Syrian Arab Republic'),(210,'TW','Taiwan'),(211,'TJ','Tajikistan'),(212,'TZ','Tanzania, United Republic of'),(213,'TH','Thailand'),(214,'TG','Togo'),(215,'TK','Tokelau'),(216,'TO','Tonga'),(217,'TT','Trinidad and Tobago'),(218,'TN','Tunisia'),(219,'TR','Turkey'),(220,'TM','Turkmenistan'),(221,'TC','Turks and Caicos Islands'),(222,'TV','Tuvalu'),(223,'UG','Uganda'),(224,'UA','Ukraine'),(225,'AE','United Arab Emirates'),(226,'GB','United Kingdom'),(227,'UM','United States minor outlying islands'),(228,'UY','Uruguay'),(229,'UZ','Uzbekistan'),(230,'VU','Vanuatu'),(231,'VA','Vatican City State'),(232,'VE','Venezuela'),(233,'VN','Vietnam'),(234,'VG','Virigan Islands (British)'),(235,'VI','Virgin Islands (U.S.)'),(236,'WF','Wallis and Futuna Islands'),(237,'EH','Western Sahara'),(238,'YE','Yemen'),(239,'YU','Yugoslavia'),(240,'ZR','Zaire'),(241,'ZM','Zambia'),(242,'ZW','Zimbabwe');
/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `industry`
--

DROP TABLE IF EXISTS `industry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `industry` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `label` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `industry`
--

LOCK TABLES `industry` WRITE;
/*!40000 ALTER TABLE `industry` DISABLE KEYS */;
INSERT INTO `industry` VALUES (1,'retail','Retail','2014-07-03 20:26:54'),(2,'nightclub','Nightclub','2014-07-03 20:26:54'),(3,'hotel','Hotel','2014-07-03 20:26:54'),(4,'restaurant','Restaurant','2014-07-03 20:26:54'),(5,'other','Concert/Event/Other','2014-07-03 20:26:54');
/*!40000 ALTER TABLE `industry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usertype`
--

DROP TABLE IF EXISTS `usertype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usertype` (
  `id` tinyint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usertype`
--

LOCK TABLES `usertype` WRITE;
/*!40000 ALTER TABLE `usertype` DISABLE KEYS */;
INSERT INTO `usertype` VALUES (1,'Super Admin','Full Administrator user','AccountManager','2014-07-03 20:26:54','2014-07-03 20:26:54'),(2,'Account Manager','Swarm account managers','AccountManager','2014-07-03 20:26:54','2014-07-03 20:26:54'),(3,'Reseller','Reseller that work to increase the presence of Swarm\'s devices','Reseller','2014-07-03 20:26:54','2014-07-03 20:26:54'),(4,'Location Manager','Location\'s owner or responsable','LocationManager','2014-07-03 20:26:54','2014-07-03 20:26:54'),(5,'Employee','Location\'s employee','Employee','2014-07-03 20:26:54','2014-07-03 20:26:54');
/*!40000 ALTER TABLE `usertype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settinggroup`
--

DROP TABLE IF EXISTS `settinggroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settinggroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settinggroup`
--

LOCK TABLES `settinggroup` WRITE;
/*!40000 ALTER TABLE `settinggroup` DISABLE KEYS */;
INSERT INTO `settinggroup` VALUES (1,'Misc','2014-07-03 20:26:54','2014-07-03 20:26:54'),(2,'Address','2014-07-03 20:26:54','2014-07-03 20:26:54'),(3,'Schedule','2014-07-03 20:26:54','2014-07-03 20:26:54'),(4,'Third Parties','2014-07-03 20:26:54','2014-07-03 20:26:54'),(5,'POS','2014-07-03 20:26:54','2014-07-03 20:26:54'),(6,'Swarm','2014-07-03 20:26:54','2014-07-03 20:26:54');
/*!40000 ALTER TABLE `settinggroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploaddetail`
--

DROP TABLE IF EXISTS `uploaddetail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploaddetail` (
  `id` tinyint(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `setting_id` int(10) unsigned NOT NULL,
  `width` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `save_pattern` varchar(255) DEFAULT NULL,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploaddetail`
--

LOCK TABLES `uploaddetail` WRITE;
/*!40000 ALTER TABLE `uploaddetail` DISABLE KEYS */;
INSERT INTO `uploaddetail` VALUES (1,'Avatar',88,2000,3000,200000,'/images/avatars/','avatar_{id}','2014-08-05 02:47:14'),(2,'Logo',89,400,200,200000,'/images/location_photos/','photo_{id}','2014-07-03 20:57:48');
/*!40000 ALTER TABLE `uploaddetail` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-08-05 22:21:57
