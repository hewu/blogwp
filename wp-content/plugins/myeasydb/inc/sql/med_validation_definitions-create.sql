CREATE TABLE `med_validation_definitions` (
  `RRN` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `desc` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isDATE` int(1) unsigned DEFAULT NULL,
  `isCHECK` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `isRADIO` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`RRN`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='myEASYdb: definitions of the validation to apply'