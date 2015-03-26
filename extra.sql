DROP TABLE IF EXISTS `email_blacklist`;
CREATE TABLE `email_blacklist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` char(100) NOT NULL DEFAULT '',
  UNIQUE (`id`),
  UNIQUE (`email`),
  INDEX(`email`),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `domain_blacklist`;
CREATE TABLE `domain_blacklist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` char(100) NOT NULL DEFAULT '',
  UNIQUE (`id`),
  INDEX(`domain`),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `massive_email_list`;
CREATE TABLE `massive_email_list` (
  `id` int(10) unsigned not null auto_increment,
  `email` char(100) NOT NULL DEFAULT '',
  `enabled` tinyint(1) NULL DEFAULT 1,
  UNIQUE (`id`),
  UNIQUE (`email`),
  INDEX(`email`),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;