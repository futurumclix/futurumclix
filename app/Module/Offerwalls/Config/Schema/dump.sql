

DROP TABLE IF EXISTS `futurumclix`.`futurum_acos`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_aros`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_aros_acos`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_offerwalls_memberships`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_offerwalls_offers`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_offerwalls_offerwalls`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_settings`;


CREATE TABLE `futurumclix`.`futurum_acos` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`parent_id` int(10) DEFAULT NULL,
	`model` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`foreign_key` int(10) DEFAULT NULL,
	`alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`lft` int(10) DEFAULT NULL,
	`rght` int(10) DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_aros` (
	`id` int(10) NOT NULL AUTO_INCREMENT,
	`parent_id` int(10) DEFAULT NULL,
	`model` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`foreign_key` int(10) DEFAULT NULL,
	`alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`lft` int(10) DEFAULT NULL,
	`rght` int(10) DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_aros_acos` (
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

CREATE TABLE `futurumclix`.`futurum_offerwalls_memberships` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`membership_id` int(10) UNSIGNED NOT NULL,
	`point_ratio` decimal(7,4) NOT NULL,
	`delay` int(3) UNSIGNED NOT NULL,
	`instant_limit` decimal(17,8) UNSIGNED DEFAULT '0.00000000' NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_offerwalls_offers` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` int(10) UNSIGNED NOT NULL,
	`offerwall` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`amount` decimal(17,8) NOT NULL,
	`status` int(3) UNSIGNED NOT NULL,
	`transactionid` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`complete_date` datetime DEFAULT NULL,
	`credit_date` datetime DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_offerwalls_offerwalls` (
	`name` varchar(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`enabled` tinyint(1) DEFAULT '0' NOT NULL,
	`allowed_ips` varchar(8096) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`api_settings` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,	PRIMARY KEY  (`name`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_settings` (
	`key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`value` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`global` tinyint(1) NOT NULL,	PRIMARY KEY  (`key`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

