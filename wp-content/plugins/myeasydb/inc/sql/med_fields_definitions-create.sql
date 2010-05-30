CREATE TABLE `med_fields_definitions` (
  `RRN` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `table` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isFILTER` tinyint(1) unsigned DEFAULT '0',
  /* `hasEndDate` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL, *//*0.0.6 */
  PRIMARY KEY (`RRN`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='myEASYdb: fields definitions'