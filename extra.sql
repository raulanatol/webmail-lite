DROP TABLE IF EXISTS `email_blacklist`;
CREATE TABLE `email_blacklist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` char(100) NOT NULL DEFAULT '',
  UNIQUE (`id`),
  INDEX(`email`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `domain_blacklist`;
CREATE TABLE `domain_blacklist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` char(100) NOT NULL DEFAULT '',
  UNIQUE (`id`),
  INDEX(`domain`),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
