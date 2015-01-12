DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `usertype_id` tinyint(10) unsigned NOT NULL,
  `ts_creation` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email` varchar(255) NOT NULL,
  `is_demo` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `user_type_id` (`usertype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` VALUES (13,'53b5bcbc9b2f4','gpepe13','1d255920ada6ac787656b393e9e0b5845aa1d975f1db4015f80904da4c232786444e6dc87e719981bd4d8596b4d0f937d6e464cbd7424a3a0aff6059fa0caee1','w.eU-HP?ij>1^\mb%r8l-t"v#a!#Ye2.E7_~qb<KfnW},#VO*5x]uL>uKGj%~#+D%v\fM>5S+Mv(kQ6:P19\'XCd\'qFWT;ATMu+NG#1tI^,>)w./Km0Z%"1SQQ0KClOz?',1,'2014-07-03 20:26:54','2014-09-02 19:13:04','gian@swarm-mobile.com',0);
INSERT INTO `user` VALUES (1,'53b5bcbc87b9f','admin','1d255920ada6ac787656b393e9e0b5845aa1d975f1db4015f80904da4c232786444e6dc87e719981bd4d8596b4d0f937d6e464cbd7424a3a0aff6059fa0caee1','w.eU-HP?ij>1^\mb%r8l-t"v#a!#Ye2.E7_~qb<KfnW},#VO*5x]uL>uKGj%~#+D%v\fM>5S+Mv(kQ6:P19\'XCd\'qFWT;ATMu+NG#1tI^,>)w./Km0Z%"1SQQ0KClOz?',2,'2014-07-03 20:26:54','2014-07-03 21:07:54','stevebeyatte@gmail.com',0);
INSERT INTO `user` VALUES (417,'53b5bd519a96f','neishnetworks','1d255920ada6ac787656b393e9e0b5845aa1d975f1db4015f80904da4c232786444e6dc87e719981bd4d8596b4d0f937d6e464cbd7424a3a0aff6059fa0caee1','w.eU-HP?ij>1^\mb%r8l-t"v#a!#Ye2.E7_~qb<KfnW},#VO*5x]uL>uKGj%~#+D%v\fM>5S+Mv(kQ6:P19\'XCd\'qFWT;ATMu+NG#1tI^,>)w./Km0Z%"1SQQ0KClOz?',3,'2014-07-03 20:29:23','2014-07-21 17:20:09','blake@neishnetworks.com',0);
INSERT INTO `user` VALUES (33,'53b5bcbcd1efc','Cynergy','1d255920ada6ac787656b393e9e0b5845aa1d975f1db4015f80904da4c232786444e6dc87e719981bd4d8596b4d0f937d6e464cbd7424a3a0aff6059fa0caee1','w.eU-HP?ij>1^\mb%r8l-t"v#a!#Ye2.E7_~qb<KfnW},#VO*5x]uL>uKGj%~#+D%v\fM>5S+Mv(kQ6:P19\'XCd\'qFWT;ATMu+NG#1tI^,>)w./Km0Z%"1SQQ0KClOz?',4,'2014-07-03 20:26:54','2014-10-27 17:25:02','jim@cynergycycles.com.au',0);
INSERT INTO `user` VALUES (1759,'53e54336ceba9','hdasman','1d255920ada6ac787656b393e9e0b5845aa1d975f1db4015f80904da4c232786444e6dc87e719981bd4d8596b4d0f937d6e464cbd7424a3a0aff6059fa0caee1','w.eU-HP?ij>1^\mb%r8l-t"v#a!#Ye2.E7_~qb<KfnW},#VO*5x]uL>uKGj%~#+D%v\fM>5S+Mv(kQ6:P19\'XCd\'qFWT;ATMu+NG#1tI^,>)w./Km0Z%"1SQQ0KClOz?',5,'0000-00-00 00:00:00','2014-08-08 21:35:27','Hale@nativogroup.eu',0);
INSERT INTO `user` VALUES (1477,'53c8642c68e09','axia','1d255920ada6ac787656b393e9e0b5845aa1d975f1db4015f80904da4c232786444e6dc87e719981bd4d8596b4d0f937d6e464cbd7424a3a0aff6059fa0caee1','w.eU-HP?ij>1^\mb%r8l-t"v#a!#Ye2.E7_~qb<KfnW},#VO*5x]uL>uKGj%~#+D%v\fM>5S+Mv(kQ6:P19\'XCd\'qFWT;ATMu+NG#1tI^,>)w./Km0Z%"1SQQ0KClOz?',6,'2014-07-18 00:02:52','2014-09-19 00:13:09','msheffey@axiapayments.com',0);
