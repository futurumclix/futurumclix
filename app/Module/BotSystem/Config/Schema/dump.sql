

DROP TABLE IF EXISTS `futurumclix`.`futurum_acos`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_aros`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_aros_acos`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_bot_system_bots`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_bot_system_groups`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_bot_system_statistics`;
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

CREATE TABLE `futurumclix`.`futurum_bot_system_bots` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`rented_upline_id` int(10) UNSIGNED DEFAULT NULL,
	`rent_starts` datetime DEFAULT NULL,
	`rent_ends` datetime DEFAULT NULL,
	`auto_renew_attempts` int(6) DEFAULT 0 NOT NULL,
	`earned_as_rref` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`clicks_as_rref` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_0` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_1` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_2` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_3` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_4` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_5` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_6` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_0` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_1` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_2` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_3` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_4` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_5` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_6` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`last_click_as_rref` datetime DEFAULT NULL,
	`today_done` tinyint(1) DEFAULT '0' NOT NULL,
	`active` tinyint(1) DEFAULT '1' NOT NULL,
	`active_days` int(11) DEFAULT 0 NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_bot_system_groups` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`membership_id` int(10) UNSIGNED NOT NULL,
	`click_value` decimal(17,8) NOT NULL,
	`points_per_click` decimal(10,2) DEFAULT '0.00' NOT NULL,
	`min_clicks` int(3) UNSIGNED NOT NULL,
	`max_clicks` int(3) UNSIGNED NOT NULL,
	`skip_chance` int(3) UNSIGNED NOT NULL,
	`max_avg` decimal(5,2) NOT NULL,
	`min_activity_days` int(5) UNSIGNED NOT NULL,
	`max_activity_days` int(5) UNSIGNED NOT NULL,
	`stop_chance` int(3) UNSIGNED NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_bot_system_statistics` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`income` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`outcome` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`created` date NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_settings` (
	`key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`value` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`global` tinyint(1) NOT NULL,	PRIMARY KEY  (`key`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

