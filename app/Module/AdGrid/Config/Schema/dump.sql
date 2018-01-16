

DROP TABLE IF EXISTS `futurumclix`.`futurum_acos`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_ad_grid_ads`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_ad_grid_ads_packages`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_ad_grid_memberships_options`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_ad_grid_user_clicks`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_ad_grid_win_history`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_aros`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_aros_acos`;
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

CREATE TABLE `futurumclix`.`futurum_ad_grid_ads` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`anonymous_advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`url` varchar(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`clicks` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`expiry` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`package_type` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`start` datetime DEFAULT NULL,
	`total_clicks` bigint(20) UNSIGNED DEFAULT 0 NOT NULL,
	`status` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Pending' NOT NULL,
	`modified` datetime NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_ad_grid_ads_packages` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`amount` int(10) UNSIGNED NOT NULL,
	`price` decimal(17,8) NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_ad_grid_memberships_options` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`membership_id` int(10) UNSIGNED NOT NULL,
	`clicks_per_day` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`win_probability` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`prizes` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_ad_grid_user_clicks` (
	`user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`ads` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`fields` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`clicks` int(10) UNSIGNED NOT NULL,
	`created` date NOT NULL,	PRIMARY KEY  (`user_id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_ad_grid_win_history` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` int(10) UNSIGNED NOT NULL,
	`username` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`prize` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
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

CREATE TABLE `futurumclix`.`futurum_settings` (
	`key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`value` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`global` tinyint(1) NOT NULL,	PRIMARY KEY  (`key`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

