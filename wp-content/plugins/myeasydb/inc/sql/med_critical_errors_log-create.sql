CREATE TABLE `med_critical_errors_log` (
  `RRN` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `DATE` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tomail` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`RRN`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8