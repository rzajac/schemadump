CREATE TABLE `bigtable` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bt_id` int(10) unsigned NOT NULL,
  `chr` char(1) DEFAULT NULL,
  `vchr` varchar(2) DEFAULT NULL,
  `tint` tinyint(4) DEFAULT NULL,
  `sint` smallint(6) DEFAULT NULL,
  `mint` mediumint(9) DEFAULT NULL,
  `inte` int(11) DEFAULT NULL,
  `bint` bigint(20) DEFAULT NULL,
  `flt` float DEFAULT NULL,
  `doub` double DEFAULT NULL,
  `deci` decimal(10,0) DEFAULT NULL,
  `bt` bit(1) DEFAULT NULL,
  `bol` tinyint(1) DEFAULT NULL,
  `ttxt` tinytext,
  `txt` text,
  `mtxt` mediumtext,
  `ltxt` longtext,
  `tblob` tinyblob,
  `mblob` mediumblob,
  `nblob` blob,
  `lblob` longblob,
  `bin` binary(1) DEFAULT NULL,
  `vbin` varbinary(20) DEFAULT NULL,
  `d` date DEFAULT NULL,
  `dt` datetime DEFAULT NULL,
  `ts` timestamp NULL DEFAULT NULL,
  `tm` time DEFAULT NULL,
  `y` year(4) DEFAULT NULL,
  PRIMARY KEY (`id`,`bt_id`),
  UNIQUE KEY `u` (`chr`),
  UNIQUE KEY `vchr` (`vchr`),
  KEY `tint` (`tint`),
  KEY `b` (`sint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;