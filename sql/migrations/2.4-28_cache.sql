SET NAMES 'utf8';

/* Creates caching table structure in business database */
CREATE TABLE `cache` (
  `ID_CACHE` varchar(250) NOT NULL,
  `VALUE_CACHE` text,
  `EXPIRE_CACHE` int,
  PRIMARY KEY (`ID_CACHE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;