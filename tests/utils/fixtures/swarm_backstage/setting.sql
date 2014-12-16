DROP TABLE IF EXISTS `setting`;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `setting` VALUES (1,'Adress Line 1','address1','Address line 1 of the location',2,'','2014-07-03 20:26:54','2014-07-11 01:47:06');
INSERT INTO `setting` VALUES (2,'Address Line 2','address2','Address line 2 of the location',2,'','2014-07-03 20:26:54','2014-07-11 01:47:15');
INSERT INTO `setting` VALUES (3,'City','city','City of the location',2,'','2014-07-03 20:26:54','2014-07-11 01:47:46');
INSERT INTO `setting` VALUES (4,'State','state','State of the location',2,'','2014-07-03 20:26:54','2014-07-11 01:47:54');
INSERT INTO `setting` VALUES (5,'Zip Code','zipcode','Zipcode of the location',2,'','2014-07-03 20:26:54','2014-07-11 01:48:01');
INSERT INTO `setting` VALUES (6,'Network ID','network_id','Presence device network id',6,'','2014-07-03 20:26:54','2014-07-11 01:48:39');
INSERT INTO `setting` VALUES (7,'Timezone','timezone','Timezone in which all the location data is presented to the user',3,'America/Los_Angeles','2014-07-03 20:26:54','2014-07-11 01:49:48');
INSERT INTO `setting` VALUES (8,'Email Gate Wifi Access?','email_gate_wifi','Have 10% Off email on Instant App?',1,'','2014-07-03 20:26:54','2014-07-11 02:16:07');
INSERT INTO `setting` VALUES (9,'Agree to the terms?','terms_disagree','Customer agrees to Swarms terms',6,'yes','2014-07-03 20:26:54','2014-07-11 01:50:23');
INSERT INTO `setting` VALUES (10,'Stripe Customer ID','stripe_customer_id','Customers stripe id',4,'','2014-07-03 20:26:54','2014-07-11 02:05:23');
INSERT INTO `setting` VALUES (11,'POS Store ID','pos_store_id','POS store ID',5,'','2014-07-03 20:26:54','2014-07-11 02:05:23');
INSERT INTO `setting` VALUES (12,'Monday Open','monday_open','Time the location opens the business on Monday',3,'09:00','2014-07-03 20:26:54','2014-07-11 01:51:54');
INSERT INTO `setting` VALUES (13,'Monday Close','monday_close','Time the location closes the business on Monday',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:23');
INSERT INTO `setting` VALUES (14,'Tuesday Open','tuesday_open','Time the location opens the business on Tuesday',3,'09:00','2014-07-03 20:26:54','2014-07-11 02:05:23');
INSERT INTO `setting` VALUES (15,'Tuesday Close','tuesday_close','Time the location closes the business on Tuesdays',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:23');
INSERT INTO `setting` VALUES (16,'Wednesday Open','wednesday_open','Time the location opens the business on Wednesdays',3,'09:00','2014-07-03 20:26:54','2014-07-11 02:05:23');
INSERT INTO `setting` VALUES (17,'Wednesday Close','wednesday_close','Time the location closes the business on Wednesdays',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:23');
INSERT INTO `setting` VALUES (18,'Thursday Open','thursday_open','Time the location opens the business on Thursdays',3,'09:00','2014-07-03 20:26:54','2014-07-11 02:05:24');
INSERT INTO `setting` VALUES (19,'Thursday Close','thursday_close','Time the location closes the business on Thursdays',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:24');
INSERT INTO `setting` VALUES (20,'Friday Open','friday_open','Time the location opens the business on Fridays',3,'09:00','2014-07-03 20:26:54','2014-07-11 02:05:24');
INSERT INTO `setting` VALUES (21,'Friday Close','friday_close','Time the location closes the business on Fridays',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:24');
INSERT INTO `setting` VALUES (22,'Saturday Open','saturday_open','Time the location opens the business on Saturdays',3,'09:00','2014-07-03 20:26:54','2014-07-11 02:05:24');
INSERT INTO `setting` VALUES (23,'Saturday Close','saturday_close','Time the location closes the business on Saturdays',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:24');
INSERT INTO `setting` VALUES (24,'Sunday Open','sunday_open','Time the location opens the business on Sundays',3,'09:00','2014-07-03 20:26:54','2014-07-11 02:05:24');
INSERT INTO `setting` VALUES (25,'Sunday Close','sunday_close','Time the location closes the business on Sundays',3,'21:00','2014-07-03 20:26:54','2014-07-11 02:05:24');
INSERT INTO `setting` VALUES (26,'Industry','industry','Location Industry Type (Name)',1,'Retail','2014-07-03 20:26:54','2014-07-11 02:05:24');
INSERT INTO `setting` VALUES (27,'Currency','currency','Location currency',1,'$','2014-07-03 20:26:54','2014-07-11 02:05:24');
INSERT INTO `setting` VALUES (28,'Customers Terms of Service','customer_terms','Location customer terms of service',6,'','2014-07-03 20:26:54','2014-07-11 02:05:25');
INSERT INTO `setting` VALUES (29,'Network Provider','network_provider','Location wifi network provider',6,'','2014-07-03 20:26:54','2014-07-11 02:05:25');
INSERT INTO `setting` VALUES (30,'Register Filter','register_filter','Location register_id for POS',5,'','2014-07-03 20:26:54','2014-07-11 02:05:25');
INSERT INTO `setting` VALUES (31,'Outlet Filter','outlet_filter','Location outlet_id for POS',5,'','2014-07-03 20:26:54','2014-07-11 02:05:25');
INSERT INTO `setting` VALUES (32,'Country','country','Location Country (Address)',2,'','2014-07-03 20:26:54','2014-07-11 02:05:25');
INSERT INTO `setting` VALUES (33,'Nightclub Hours?','nightclub_hours','Is a nightclub?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:25');
INSERT INTO `setting` VALUES (34,'Correction Percentage','correction_percentage','Percentage of correction (in base 1)',6,'','2014-07-03 20:26:54','2014-07-11 02:05:25');
INSERT INTO `setting` VALUES (35,'Email Gate Text','email_gate_text','Text received on location wifi access',1,'','2014-07-03 20:26:54','2014-07-11 02:05:25');
INSERT INTO `setting` VALUES (36,'Unit of Measurement','unit_of_measurement','Location size unit of measurement (ft, m2)',1,'feet','2014-07-03 20:26:54','2014-07-11 02:05:25');
INSERT INTO `setting` VALUES (37,'Size of Store','size_of_store','Location size (number)',6,'','2014-07-03 20:26:54','2014-07-11 02:05:25');
INSERT INTO `setting` VALUES (38,'Distance To Front Door','distance_to_front_door','Distance from Presence to Front door',6,'','2014-07-03 20:26:54','2014-07-11 02:05:26');
INSERT INTO `setting` VALUES (39,'Distance To Left Wall','distance_to_left_wall','Distance from Presence to Left Wall',6,'','2014-07-03 20:26:54','2014-07-11 02:05:26');
INSERT INTO `setting` VALUES (40,'Distance To Right Wall','distance_to_right_wall','Distance from Presence to Right Wall',6,'','2014-07-03 20:26:54','2014-07-11 02:05:26');
INSERT INTO `setting` VALUES (41,'Distance To Back Wall','distance_to_back_wall','Distance from Presence to Back Wall',6,'','2014-07-03 20:26:54','2014-07-11 02:05:26');
INSERT INTO `setting` VALUES (42,'Shape of Store','shape_of_store','Shape of the store (Rectangular, Square, L)',6,'','2014-07-03 20:26:54','2014-07-11 02:05:26');
INSERT INTO `setting` VALUES (43,'Estimated Daily Foot Traffic','estimated_daily_foot_traffic','Estimated number of people in store per day',6,'','2014-07-03 20:26:54','2014-07-11 02:05:26');
INSERT INTO `setting` VALUES (44,'POS Provider','pos_provider','POS provider name',5,'','2014-07-03 20:26:54','2014-07-11 02:05:26');
INSERT INTO `setting` VALUES (45,'POS Api Key','pos_api_key','POS API key',5,'','2014-07-03 20:26:54','2014-07-11 02:05:26');
INSERT INTO `setting` VALUES (46,'Guest Wifi Off/On?','guest_wifi','Location have Guest Wi-Fi?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:26');
INSERT INTO `setting` VALUES (47,'Wifi Network Name','wifi_name','Guest Wi-Fi name',1,'','2014-07-03 20:26:54','2014-07-11 02:05:27');
INSERT INTO `setting` VALUES (48,'Wifi Password Yes/No?','wifi_password','Guest Wi-Fi password',1,'','2014-07-03 20:26:54','2014-07-11 02:05:27');
INSERT INTO `setting` VALUES (49,'The Wifi Password (optional)','actual_wifi_password','Guest Wi-Fi password',1,'','2014-07-03 20:26:54','2014-07-11 02:05:27');
INSERT INTO `setting` VALUES (50,'Use Instant App?','use_instant_app','Location uses InstantApp',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:27');
INSERT INTO `setting` VALUES (51,'InstantApp Redirect URL','instant_app_redirect_url','Redirect URI after use InstantApp',6,'','2014-07-03 20:26:54','2014-07-11 02:05:27');
INSERT INTO `setting` VALUES (52,'Meraki Org Validator','meraki_org_validator','Meraki ORG Validator',4,'','2014-07-03 20:26:54','2014-07-11 02:05:27');
INSERT INTO `setting` VALUES (53,'Meraki Wifi Network Name','meraki_wifi_network_name','Meraki Wi-Fi name',4,'','2014-07-03 20:26:54','2014-07-11 02:05:27');
INSERT INTO `setting` VALUES (54,'Setup Account Information Complete','setup_account_complete','Is the step Account complete on Setup?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:27');
INSERT INTO `setting` VALUES (55,'Setup Store Hours Complete','setup_hours_complete','Is the step Store Hours complete on Setup?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:27');
INSERT INTO `setting` VALUES (56,'Setup Calibration Information Complete','setup_calibration_complete','Is the step Calibration complete on Setup?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:27');
INSERT INTO `setting` VALUES (57,'Setup POS Complete','setup_pos_complete','Is the step POS complete on Setup?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:28');
INSERT INTO `setting` VALUES (58,'Setup Wireless Complete','setup_wireless_complete','Is the step Wireless complete on Setup?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:28');
INSERT INTO `setting` VALUES (59,'Phone Number','phone','Location Phone',2,'','2014-07-03 20:26:54','2014-07-11 02:05:28');
INSERT INTO `setting` VALUES (60,'POS Uplink Installed?','pos_uplink_installed','Location POS UpLink Installed',5,'no','2014-07-03 20:26:54','2014-07-11 02:05:28');
INSERT INTO `setting` VALUES (61,'POS Login','pos_login','POS username login',5,'','2014-07-03 20:26:54','2014-07-11 02:05:28');
INSERT INTO `setting` VALUES (62,'POS Password','pos_password','POS password login',5,'','2014-07-03 20:26:54','2014-07-11 02:05:28');
INSERT INTO `setting` VALUES (63,'POS Login URL','pos_url','POS URL',5,'','2014-07-03 20:26:54','2014-07-11 02:05:28');
INSERT INTO `setting` VALUES (64,'Connect Screen Variant','connect_screen_variant','InstantApp variant',1,'open_access','2014-07-03 20:26:54','2014-07-11 02:16:07');
INSERT INTO `setting` VALUES (65,'Email Gate Subject','email_gate_subject','InstantApp emails subject',1,'','2014-07-03 20:26:54','2014-07-11 02:05:28');
INSERT INTO `setting` VALUES (66,'Email Gate Content','email_gate_content','InstantApp emails content',1,'','2014-07-03 20:26:54','2014-07-11 02:05:28');
INSERT INTO `setting` VALUES (67,'No Rollups?','no_rollups','Use rollups for fill metrics?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:29');
INSERT INTO `setting` VALUES (68,'No Cache?','no_cache','Use cache for fil metrics?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:29');
INSERT INTO `setting` VALUES (69,'Nighclub Hours Location','nightclub_hours_location','Nightclub Timezone Conversion',6,'eastcoast_time','2014-07-03 20:26:54','2014-07-11 02:05:29');
INSERT INTO `setting` VALUES (70,'NAICS Code','naics_code','Locations NAICS code',1,'','2014-07-03 20:26:54','2014-07-11 02:05:29');
INSERT INTO `setting` VALUES (71,'IAB Tier 1 Category','iab_tier_1_category','Locations IAB Tier 1',1,'','2014-07-03 20:26:54','2014-07-11 02:05:29');
INSERT INTO `setting` VALUES (72,'IAB Tier 2 Category','iab_tier_2_category','Locations IAB Tier 2',1,'','2014-07-03 20:26:54','2014-07-11 02:05:29');
INSERT INTO `setting` VALUES (73,'Brand 1','brand_1','Locations Brand 1',1,'','2014-07-03 20:26:54','2014-07-11 02:05:29');
INSERT INTO `setting` VALUES (74,'Brand 2','brand_2','Locations Brand 2',1,'','2014-07-03 20:26:54','2014-07-11 02:05:29');
INSERT INTO `setting` VALUES (75,'Brand 3','brand_3','Locations Brand 3',1,'','2014-07-03 20:26:54','2014-07-11 02:05:29');
INSERT INTO `setting` VALUES (76,'Brand 4','brand_4','Locations Brand 4',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30');
INSERT INTO `setting` VALUES (77,'Brand 5','brand_5','Locations Brand 5',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30');
INSERT INTO `setting` VALUES (78,'Brand 6','brand_6','Locations Brand 6',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30');
INSERT INTO `setting` VALUES (79,'Brand 7 ','brand_7','Locations Brand 7',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30');
INSERT INTO `setting` VALUES (80,'Brand 8','brand_8','Locations Brand 8',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30');
INSERT INTO `setting` VALUES (81,'Brand 9','brand_9','Locations Brand 9',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30');
INSERT INTO `setting` VALUES (82,'Brand 10','brand_10','Locations Brand 10',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30');
INSERT INTO `setting` VALUES (83,'POS Version','pos_version','POS version',5,'','2014-07-03 20:26:54','2014-07-11 02:05:30');
INSERT INTO `setting` VALUES (84,'OS Version','os_version','Locations computer OS version',1,'','2014-07-03 20:26:54','2014-07-11 02:05:30');
INSERT INTO `setting` VALUES (85,'Count out of store hours transactions?','transactions_while_closed','Display transactions made it outside open hours?',6,'yes','2014-07-03 20:26:54','2014-07-11 02:05:31');
INSERT INTO `setting` VALUES (86,'White Label Dashboard?','white_label_dashboard','Have the Locations dashboard custom logo?',6,'no','2014-07-03 20:26:54','2014-07-11 02:05:31');
INSERT INTO `setting` VALUES (87,'Slug','slug','InstantApp URLs slug identificator',6,'','2014-07-03 20:26:54','2014-07-11 02:05:31');
INSERT INTO `setting` VALUES (88,'Avatar Filename','avatar_filename','InstantApp Custom Background image',6,'','2014-07-03 20:26:54','2014-07-11 02:05:31');
INSERT INTO `setting` VALUES (89,'Logo Filename','logo_filename','White Label Dashboard Logo',6,'','2014-07-03 20:26:54','2014-07-11 02:05:31');
INSERT INTO `setting` VALUES (90,'Constant Contact Access Token','ctct_access_token','Constant Contact access_token',4,'','2014-07-03 20:26:54','2014-07-11 02:05:31');
INSERT INTO `setting` VALUES (91,'Constant Contact Expires In','ctct_expires_in','Constant Contact token expires',4,'','2014-07-03 20:26:54','2014-07-11 02:05:31');
INSERT INTO `setting` VALUES (92,'Constant Contact Token Type','ctct_token_type','Constant Contact token type',4,'','2014-07-03 20:26:54','2014-07-11 02:05:31');
INSERT INTO `setting` VALUES (93,'Constant Contact Username','ctct_username','Constant Contact username',4,'','2014-07-03 20:26:54','2014-07-11 02:05:31');
INSERT INTO `setting` VALUES (94,'Default device for Foot Traffic metric','footTraffic_default_device','Which device data to use to calculate foot traffic',6,'portal','0000-00-00 00:00:00','2014-08-15 19:44:39');
INSERT INTO `setting` VALUES (95,'Default device for Conversion Rate metric','conversionRate_default_device','Which device data to use to calculate Conversion Rate',6,'portal','0000-00-00 00:00:00','2014-08-15 19:44:42');
INSERT INTO `setting` VALUES (96,'Business Name','business_name',NULL,7,'','0000-00-00 00:00:00','2014-09-05 21:26:56');
INSERT INTO `setting` VALUES (97,'Number of Locations','number_of_locations',NULL,7,'','0000-00-00 00:00:00','2014-09-05 21:27:06');
INSERT INTO `setting` VALUES (98,'Type of Retail','type_of_retail',NULL,7,'','0000-00-00 00:00:00','2014-09-05 21:27:48');
INSERT INTO `setting` VALUES (99,'Annual Revenue','annual_revenue',NULL,7,'','0000-00-00 00:00:00','2014-09-05 21:28:00');
INSERT INTO `setting` VALUES (100,'Number of Employees','number_of_employees',NULL,7,'','0000-00-00 00:00:00','2014-09-05 21:28:27');
INSERT INTO `setting` VALUES (101,'Subsciption Start Date','subscription_start_date','Date the location started paying for swarm services',6,'','2014-08-19 11:00:00','2014-08-19 11:00:00');
INSERT INTO `setting` VALUES (102,'Package','package','Swarm products the location is paying subscription for',6,'','2014-08-24 00:00:00','2014-08-27 00:20:20');
INSERT INTO `setting` VALUES (103,'Billing Status','billing_status','Recurring payment type, that can be quaterly, monthly or anually',6,'','0000-00-00 00:00:00','2014-08-26 22:45:36');
INSERT INTO `setting` VALUES (104,'Latitude','lat',NULL,2,'','0000-00-00 00:00:00','2014-09-10 00:44:13');
INSERT INTO `setting` VALUES (105,'Longitude','lng',NULL,2,'','0000-00-00 00:00:00','2014-09-10 00:44:26');
