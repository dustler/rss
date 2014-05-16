CREATE TABLE `resources` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `link` varchar(255) NOT NULL,
  `lastPubDate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `host` (`link`,`lastPubDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `items` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `resourceId` bigint(20) NOT NULL,
  `link` varchar(255) NOT NULL,
  `pubDate` datetime NOT NULL,
  `createdAt` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `resource` (`resourceId`,`pubDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;