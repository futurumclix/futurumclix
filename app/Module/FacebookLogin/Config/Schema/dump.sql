

DROP TABLE IF EXISTS `futurumclix`.`acos`;
DROP TABLE IF EXISTS `futurumclix`.`aros`;
DROP TABLE IF EXISTS `futurumclix`.`aros_acos`;
DROP TABLE IF EXISTS `futurumclix`.`external_logins`;
DROP TABLE IF EXISTS `futurumclix`.`settings`;


CREATE TABLE `futurumclix`.`acos` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`parent_id` int(10) DEFAULT NULL,
	`model` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`foreign_key` int(10) DEFAULT NULL,
	`alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`lft` int(10) DEFAULT NULL,
	`rght` int(10) DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`aros` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`parent_id` int(10) DEFAULT NULL,
	`model` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`foreign_key` int(10) DEFAULT NULL,
	`alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`lft` int(10) DEFAULT NULL,
	`rght` int(10) DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`aros_acos` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`aro_id` int(10) NOT NULL,
	`aco_id` int(10) NOT NULL,
	`_create` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '0' NOT NULL,
	`_read` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '0' NOT NULL,
	`_update` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '0' NOT NULL,
	`_delete` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '0' NOT NULL,	PRIMARY KEY  (`id`),
	UNIQUE KEY `ARO_ACO_KEY` (`aro_id`, `aco_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`external_logins` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` int(10) UNSIGNED NOT NULL,
	`facebook` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`settings` (
	`key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`value` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`global` tinyint(1) NOT NULL,	PRIMARY KEY  (`key`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

