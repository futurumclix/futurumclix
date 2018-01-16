

DROP TABLE IF EXISTS `futurumclix`.`acos`;
DROP TABLE IF EXISTS `futurumclix`.`aros`;
DROP TABLE IF EXISTS `futurumclix`.`aros_acos`;
DROP TABLE IF EXISTS `futurumclix`.`forum_forums`;
DROP TABLE IF EXISTS `futurumclix`.`forum_moderators`;
DROP TABLE IF EXISTS `futurumclix`.`forum_poll_options`;
DROP TABLE IF EXISTS `futurumclix`.`forum_poll_votes`;
DROP TABLE IF EXISTS `futurumclix`.`forum_polls`;
DROP TABLE IF EXISTS `futurumclix`.`forum_post_ratings`;
DROP TABLE IF EXISTS `futurumclix`.`forum_posts`;
DROP TABLE IF EXISTS `futurumclix`.`forum_subscriptions`;
DROP TABLE IF EXISTS `futurumclix`.`forum_topics`;
DROP TABLE IF EXISTS `futurumclix`.`users`;


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

CREATE TABLE `futurumclix`.`forum_forums` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`parent_id` int(11) DEFAULT NULL,
	`title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`slug` varchar(115) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`status` int(6) DEFAULT 1 NOT NULL,
	`orderNo` int(6) DEFAULT 0 NOT NULL,
	`autoLock` tinyint(1) DEFAULT '1' NOT NULL,
	`excerpts` tinyint(1) DEFAULT '0' NOT NULL,
	`topic_count` int(11) DEFAULT 0 NOT NULL,
	`post_count` int(11) DEFAULT 0 NOT NULL,
	`accessRead` int(11) DEFAULT NULL,
	`accessPost` int(11) DEFAULT NULL,
	`accessPoll` int(11) DEFAULT NULL,
	`accessReply` int(11) DEFAULT NULL,
	`lastTopic_id` int(11) DEFAULT NULL,
	`lastPost_id` int(11) DEFAULT NULL,
	`lastUser_id` int(11) DEFAULT NULL,
	`lft` int(11) DEFAULT NULL,
	`rght` int(11) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `parent_id` (`parent_id`),
	KEY `lastTopic_id` (`lastTopic_id`),
	KEY `lastPost_id` (`lastPost_id`),
	KEY `lastUser_id` (`lastUser_id`),
	KEY `accessRead` (`accessRead`),
	KEY `accessPost` (`accessPost`),
	KEY `accessPoll` (`accessPoll`),
	KEY `accessReply` (`accessReply`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`forum_moderators` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`forum_id` int(11) DEFAULT NULL,
	`user_id` int(11) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `user_id` (`user_id`),
	KEY `forum_id` (`forum_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`forum_poll_options` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`poll_id` int(11) DEFAULT NULL,
	`option` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`poll_vote_count` int(11) DEFAULT 0 NOT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `poll_id` (`poll_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`forum_poll_votes` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`poll_id` int(11) DEFAULT NULL,
	`poll_option_id` int(11) DEFAULT NULL,
	`user_id` int(11) DEFAULT NULL,
	`created` datetime DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `poll_id` (`poll_id`),
	KEY `poll_option_id` (`poll_option_id`),
	KEY `user_id` (`user_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`forum_polls` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`topic_id` int(11) DEFAULT NULL,
	`expires` datetime DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `topic_id` (`topic_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`forum_post_ratings` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) NOT NULL,
	`post_id` int(11) NOT NULL,
	`topic_id` int(11) NOT NULL,
	`type` int(6) NOT NULL,
	`created` datetime DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `user_id` (`user_id`),
	KEY `post_id` (`post_id`),
	KEY `topic_id` (`topic_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`forum_posts` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`forum_id` int(11) DEFAULT NULL,
	`topic_id` int(11) DEFAULT NULL,
	`user_id` int(11) DEFAULT NULL,
	`userIP` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`up` int(11) DEFAULT 0 NOT NULL,
	`down` int(11) DEFAULT 0 NOT NULL,
	`score` int(11) DEFAULT 0 NOT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `forum_id` (`forum_id`),
	KEY `topic_id` (`topic_id`),
	KEY `user_id` (`user_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`forum_subscriptions` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(11) DEFAULT NULL,
	`forum_id` int(11) DEFAULT NULL,
	`topic_id` int(11) DEFAULT NULL,
	`created` datetime DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `topic_id` (`topic_id`),
	KEY `forum_id` (`forum_id`),
	KEY `user_id` (`user_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`forum_topics` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`forum_id` int(11) DEFAULT NULL,
	`user_id` int(11) DEFAULT NULL,
	`title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`slug` varchar(110) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`excerpt` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`status` int(6) DEFAULT 0 NOT NULL,
	`type` int(6) DEFAULT 0 NOT NULL,
	`post_count` int(11) DEFAULT 0 NOT NULL,
	`view_count` int(11) DEFAULT 0 NOT NULL,
	`firstPost_id` int(11) DEFAULT NULL,
	`lastPost_id` int(11) DEFAULT NULL,
	`lastUser_id` int(11) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `forum_id` (`forum_id`),
	KEY `user_id` (`user_id`),
	KEY `firstPost_id` (`firstPost_id`),
	KEY `lastPost_id` (`lastPost_id`),
	KEY `lastUser_id` (`lastUser_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`users` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`username` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`password` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`role` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`email` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`signup_ip` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`last_ip` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`account_balance` decimal(17,8) DEFAULT '0.00000000',
	`purchase_balance` decimal(17,8) DEFAULT '0.00000000',
	`upline_id` int(10) UNSIGNED DEFAULT NULL,
	`upline_commission` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`dref_since` datetime DEFAULT NULL,
	`created` datetime NOT NULL,
	`modified` datetime NOT NULL,
	`remind_profile` tinyint(1) DEFAULT '1' NOT NULL,
	`first_name` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`last_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`last_log_in` datetime DEFAULT NULL,
	`refs_count` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rented_users_count` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rented_bots_count` int(11) DEFAULT 0 NOT NULL,
	`country` int(10) NOT NULL,
	`rented_upline_id` int(10) DEFAULT NULL,
	`rent_starts` datetime DEFAULT NULL,
	`rent_ends` datetime DEFAULT NULL,
	`last_rent_action` datetime DEFAULT NULL,
	`comes_from` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`cashouts` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`topic_count` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`post_count` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`signature` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`forum_status` int(5) UNSIGNED DEFAULT 1 NOT NULL,
	`avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`forum_statistics` tinyint(1) DEFAULT '0' NOT NULL,
	`autopay_enabled` tinyint(1) DEFAULT '0' NOT NULL,
	`autopay_done` tinyint(1) DEFAULT '0' NOT NULL,
	`auto_renew_days` int(6) UNSIGNED DEFAULT 0 NOT NULL,
	`auto_renew_extend` int(6) UNSIGNED DEFAULT 0 NOT NULL,
	`auto_renew_attempts` int(5) UNSIGNED DEFAULT 0 NOT NULL,
	`accepted_applications` int(11) DEFAULT 0 NOT NULL,
	`rejected_applications` int(11) DEFAULT 0 NOT NULL,
	`pending_applications` int(11) DEFAULT 0 NOT NULL,
	`evercookie` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,	PRIMARY KEY  (`id`),
	KEY `rented_upline_id` (`rented_upline_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

