CREATE TABLE `med_tables_definitions` (
  `RRN` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `table` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `referenceField` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `referencedTable` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `referencedID` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `referencedDesc` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `validateType` int(3) DEFAULT NULL,
  PRIMARY KEY (`RRN`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='myEASYdb: tables definitions'