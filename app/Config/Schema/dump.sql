

DROP TABLE IF EXISTS `futurumclix`.`futurum_acos`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_admins`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_ads`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_ads_categories`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_ads_category_packages`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_ads_memberships`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_anonymous_advertiser`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_aros`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_aros_acos`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_autopay_history`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_autorenew_history`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_banner_ads`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_banner_ads_packages`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_banners`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_bought_items`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_cashouts`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_click_history`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_click_values`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_commissions`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_country_locks`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_currencies`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_deposit_bonuses`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_deposits`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_direct_referrals_prices`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_email_locks`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_emails`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_explorer_ads`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_explorer_ads_click_values`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_explorer_ads_memberships`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_explorer_ads_packages`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_explorer_ads_targetted_locations`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_explorer_ads_visited_ads`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_express_ads`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_express_ads_click_values`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_express_ads_memberships`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_express_ads_packages`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_express_ads_targetted_locations`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_express_ads_visited_ads`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_featured_ads`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_featured_ads_packages`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_ignored_offers`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_impression_history`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_ip2nation`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_ip2nationCountries`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_ip_locks`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_item_reports`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_login_ads`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_login_ads_packages`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_memberships`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_memberships_paid_offers`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_memberships_users`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_news`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_paid_offers`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_paid_offers_applications`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_paid_offers_categories`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_paid_offers_packages`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_paid_offers_targetted_locations`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_paid_offers_values`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_payment_gateways`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_pending_emails`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_rent_extension_periods`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_rented_referrals_prices`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_settings`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_short_item_ids`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_support_canned_answers`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_support_departments`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_support_ticket_answers`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_support_tickets`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_targetted_locations`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_user_metadata`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_user_profiles`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_user_statistics`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_username_locks`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_users`;
DROP TABLE IF EXISTS `futurumclix`.`futurum_visited_ads`;


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

CREATE TABLE `futurumclix`.`futurum_admins` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`password` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`allowed_ips` varchar(500) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	`last_log_in` datetime DEFAULT NULL,
	`verify_token` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_ads` (
	`id` varchar(36) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
	`title` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`url` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`clicks` bigint(10) UNSIGNED DEFAULT 0 NOT NULL,
	`outside_clicks` bigint(10) UNSIGNED DEFAULT 0 NOT NULL,
	`status` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'Active' NOT NULL,
	`package_type` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`expiry` int(10) UNSIGNED NOT NULL,
	`expiry_date` datetime DEFAULT NULL,
	`description` text CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`ads_category_id` int(10) UNSIGNED NOT NULL,
	`ads_category_package_id` int(10) UNSIGNED NOT NULL,
	`advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`anonymous_advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`hide_referer` tinyint(1) DEFAULT '0' NOT NULL,
	`modified` datetime DEFAULT NULL,
	`created` datetime DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_ads_categories` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`time` int(10) UNSIGNED NOT NULL,
	`allow_description` tinyint(1) DEFAULT '0' NOT NULL,
	`geo_targetting` tinyint(1) DEFAULT '0' NOT NULL,
	`referrals_earnings` tinyint(1) DEFAULT '0' NOT NULL,
	`status` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'Active' NOT NULL,
	`position` int(5) UNSIGNED DEFAULT 65535 NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_ads_category_packages` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`ads_category_id` int(10) UNSIGNED NOT NULL,
	`type` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'Clicks' NOT NULL,
	`amount` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_ads_memberships` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`ad_id` blob NOT NULL,
	`membership_id` int(10) UNSIGNED NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_anonymous_advertiser` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`email` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
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

CREATE TABLE `futurumclix`.`futurum_autopay_history` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` int(10) UNSIGNED NOT NULL,
	`amount` decimal(17,8) NOT NULL,
	`created` date NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_autorenew_history` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` int(10) UNSIGNED NOT NULL,
	`amount` decimal(17,8) NOT NULL,
	`created` date NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_banner_ads` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`anonymous_advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`title` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`url` varchar(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`clicks` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`impressions` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`expiry` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`package_type` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`image_url` varchar(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`start` datetime DEFAULT NULL,
	`total_clicks` bigint(20) UNSIGNED DEFAULT 0 NOT NULL,
	`status` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Pending' NOT NULL,
	`modified` datetime NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_banner_ads_packages` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`amount` int(10) UNSIGNED NOT NULL,
	`price` decimal(17,8) NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_banners` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`filename` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`statistical` tinyint(1) NOT NULL,
	`user_paid` tinyint(1) NOT NULL,
	`user_earned` tinyint(1) NOT NULL,
	`site_paid` tinyint(1) NOT NULL,
	`font_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'DroidSans' NOT NULL,
	`font_color` varchar(8) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'FFFFFF' NOT NULL,
	`font_size` int(3) UNSIGNED DEFAULT 0 NOT NULL,
	`user_paid_x` int(11) DEFAULT 0 NOT NULL,
	`user_paid_y` int(11) DEFAULT 0 NOT NULL,
	`user_earned_x` int(11) DEFAULT 0 NOT NULL,
	`user_earned_y` int(11) DEFAULT 0 NOT NULL,
	`site_paid_x` int(11) DEFAULT 0 NOT NULL,
	`site_paid_y` int(11) DEFAULT 0 NOT NULL,
	`modified` datetime DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_bought_items` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` int(10) UNSIGNED NOT NULL,
	`model` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`foreign_key` int(10) UNSIGNED NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_cashouts` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` int(10) UNSIGNED NOT NULL,
	`amount` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`fee` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`payment_account` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`gateway` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`status` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'New' NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_click_history` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`model` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`foreign_key` varchar(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`country_id` int(10) UNSIGNED DEFAULT NULL,
	`clicks` bigint(20) UNSIGNED DEFAULT 1 NOT NULL,
	`created` date NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_click_values` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`ads_category_id` int(10) UNSIGNED NOT NULL,
	`membership_id` int(10) UNSIGNED NOT NULL,
	`user_click_value` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`direct_referral_click_value` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`rented_referral_click_value` decimal(17,8) DEFAULT '0.00000000' NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_commissions` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`deposit_id` int(10) UNSIGNED DEFAULT NULL,
	`upline_id` int(10) UNSIGNED NOT NULL,
	`referral_id` int(10) UNSIGNED NOT NULL,
	`amount` decimal(17,8) NOT NULL,
	`credit_date` datetime NOT NULL,
	`status` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'Pending' NOT NULL,	PRIMARY KEY  (`id`),
	KEY `deposit_id` (`deposit_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_country_locks` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`country_id` int(10) UNSIGNED NOT NULL,
	`note` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_currencies` (
	`id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT,
	`code` varchar(3) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`name` varchar(65) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`symbol` varchar(3) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`iso_number` int(11) NOT NULL,
	`step` decimal(17,8) NOT NULL,	PRIMARY KEY  (`id`),
	UNIQUE KEY `code` (`code`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=MyISAM;

CREATE TABLE `futurumclix`.`futurum_deposit_bonuses` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`deposit_id` int(10) UNSIGNED NOT NULL,
	`user_id` int(10) UNSIGNED NOT NULL,
	`amount` decimal(17,8) NOT NULL,
	`status` int(3) NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_deposits` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` int(10) UNSIGNED DEFAULT NULL,
	`gateway` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`amount` decimal(17,8) NOT NULL,
	`account` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`status` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'Failed' NOT NULL,
	`item` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`gatewayid` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`date` datetime NOT NULL,	PRIMARY KEY  (`id`),
	KEY `user_id` (`user_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_direct_referrals_prices` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`amount` int(5) UNSIGNED NOT NULL,
	`price` decimal(17,8) NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_email_locks` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`template` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`note` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_emails` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`format` varchar(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'text' NOT NULL,
	`subject` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`content` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_explorer_ads` (
	`id` varchar(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`title` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`url` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`clicks` bigint(20) UNSIGNED DEFAULT 0 NOT NULL,
	`outside_clicks` bigint(20) UNSIGNED DEFAULT 0 NOT NULL,
	`status` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Active' NOT NULL,
	`package_type` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`expiry` int(10) UNSIGNED NOT NULL,
	`expiry_date` datetime DEFAULT NULL,
	`description` text CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`explorer_ads_package_id` int(10) UNSIGNED DEFAULT NULL,
	`advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`anonymous_advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`hide_referer` tinyint(1) DEFAULT '0' NOT NULL,
	`subpages` int(3) UNSIGNED DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_explorer_ads_click_values` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`membership_id` int(10) UNSIGNED NOT NULL,
	`subpages` int(3) UNSIGNED NOT NULL,
	`user_click_value` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`direct_referral_click_value` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`rented_referral_click_value` decimal(17,8) DEFAULT '0.00000000' NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_explorer_ads_memberships` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`explorer_ad_id` varchar(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`membership_id` int(10) UNSIGNED NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_explorer_ads_packages` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`subpages` int(3) UNSIGNED DEFAULT 1 NOT NULL,
	`type` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Clicks' NOT NULL,
	`amount` int(10) UNSIGNED DEFAULT 0,
	`price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_explorer_ads_targetted_locations` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`explorer_ad_id` varchar(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`location` varchar(768) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_explorer_ads_visited_ads` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`explorer_ad_id` varchar(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`user_id` int(10) UNSIGNED NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_express_ads` (
	`id` varchar(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`title` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`url` varchar(512) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`clicks` bigint(20) UNSIGNED DEFAULT 0 NOT NULL,
	`outside_clicks` bigint(20) UNSIGNED DEFAULT 0 NOT NULL,
	`status` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Active' NOT NULL,
	`package_type` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`expiry` int(10) UNSIGNED NOT NULL,
	`expiry_date` datetime DEFAULT NULL,
	`description` text CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`express_ads_package_id` int(10) UNSIGNED DEFAULT NULL,
	`advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`anonymous_advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`hide_referer` tinyint(1) DEFAULT '0' NOT NULL,
	`modified` datetime DEFAULT NULL,
	`created` datetime DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_express_ads_click_values` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`membership_id` int(10) UNSIGNED NOT NULL,
	`user_click_value` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`direct_referral_click_value` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`rented_referral_click_value` decimal(17,8) DEFAULT '0.00000000' NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_express_ads_memberships` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`express_ad_id` varchar(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`membership_id` int(10) UNSIGNED NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_express_ads_packages` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Clicks' NOT NULL,
	`amount` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_express_ads_targetted_locations` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`express_ad_id` varchar(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`location` varchar(768) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_express_ads_visited_ads` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`express_ad_id` varchar(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`user_id` int(10) UNSIGNED NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_featured_ads` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`anonymous_advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`title` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`url` varchar(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`clicks` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`impressions` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`expiry` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`package_type` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`description` varchar(300) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`start` datetime DEFAULT NULL,
	`total_clicks` bigint(20) UNSIGNED DEFAULT 0 NOT NULL,
	`status` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Pending' NOT NULL,
	`modified` datetime NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_featured_ads_packages` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`amount` int(10) UNSIGNED NOT NULL,
	`price` decimal(17,8) NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_ignored_offers` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` int(10) UNSIGNED NOT NULL,
	`offer_id` int(10) UNSIGNED NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_impression_history` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`model` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`foreign_key` varchar(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`impressions` bigint(20) UNSIGNED DEFAULT 1 NOT NULL,
	`created` date NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_ip2nation` (
	`ip` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`country` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,	PRIMARY KEY  (`ip`),
	KEY `ip` (`ip`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_ip2nationCountries` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`code` varchar(4) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`iso_code_2` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`iso_code_3` varchar(3) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`iso_country` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`country` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`lat` float DEFAULT 0 NOT NULL,
	`lon` float DEFAULT 0 NOT NULL,	PRIMARY KEY  (`id`),
	KEY `code` (`code`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_ip_locks` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`ip_start` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`ip_end` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`note` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_item_reports` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`reporter_id` int(11) DEFAULT NULL,
	`resolver_id` int(11) DEFAULT NULL,
	`status` int(6) DEFAULT 0 NOT NULL,
	`type` int(6) DEFAULT 0 NOT NULL,
	`model` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`foreign_key` varchar(36) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`item` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`reason` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`comment` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `reporter_id` (`reporter_id`),
	KEY `resolver_id` (`resolver_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_login_ads` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`anonymous_advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`title` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`url` varchar(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`clicks` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`expiry` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`package_type` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`image_url` varchar(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`start` datetime DEFAULT NULL,
	`total_clicks` bigint(20) UNSIGNED DEFAULT 0 NOT NULL,
	`status` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Pending' NOT NULL,
	`modified` datetime NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_login_ads_packages` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`amount` int(10) UNSIGNED NOT NULL,
	`price` decimal(17,8) NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_memberships` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`1_month_price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`2_months_price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`3_months_price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`4_months_price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`5_months_price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`6_months_price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`7_months_price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`8_months_price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`9_months_price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`10_months_price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`11_months_price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`12_months_price` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`status` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'Disabled' NOT NULL,
	`direct_referrals_limit` int(11) DEFAULT -1 NOT NULL,
	`rented_referrals_limit` int(11) DEFAULT -1 NOT NULL,
	`1_month_active` tinyint(1) DEFAULT '0' NOT NULL,
	`2_months_active` tinyint(1) DEFAULT '0' NOT NULL,
	`3_months_active` tinyint(1) DEFAULT '0' NOT NULL,
	`4_months_active` tinyint(1) DEFAULT '0' NOT NULL,
	`5_months_active` tinyint(1) DEFAULT '0' NOT NULL,
	`6_months_active` tinyint(1) DEFAULT '0' NOT NULL,
	`7_months_active` tinyint(1) DEFAULT '0' NOT NULL,
	`8_months_active` tinyint(1) DEFAULT '0' NOT NULL,
	`9_months_active` tinyint(1) DEFAULT '0' NOT NULL,
	`10_months_active` tinyint(1) DEFAULT '0' NOT NULL,
	`11_months_active` tinyint(1) DEFAULT '0' NOT NULL,
	`12_months_active` tinyint(1) DEFAULT '0' NOT NULL,
	`direct_referrals_delete_cost` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`rented_referral_expiry_fee` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`minimum_cashout` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`cashout_waiting_time` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`allow_more_cashouts` tinyint(1) DEFAULT '0' NOT NULL,
	`maximum_cashout_amount` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`instant_cashouts` tinyint(1) DEFAULT '0' NOT NULL,
	`upgrade_commission` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`fund_commission` decimal(6,3) UNSIGNED DEFAULT '0.000' NOT NULL,
	`purchase_commission` decimal(6,3) UNSIGNED DEFAULT '0.000' NOT NULL,
	`time_between_renting` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`available_referrals_packs` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`referral_recycle_cost` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`autorecycle_time` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`transfering_commission` tinyint(1) DEFAULT '0' NOT NULL,
	`max_purchase_commission_referral` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`max_purchase_commission_transaction` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`commission_delay` int(5) UNSIGNED DEFAULT 0 NOT NULL,
	`commission_items` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`results_per_page` int(6) DEFAULT 30 NOT NULL,
	`autopay_trigger_days` int(3) UNSIGNED NOT NULL,
	`total_cashouts_limit_mode` int(3) UNSIGNED DEFAULT 0 NOT NULL,
	`total_cashouts_limit_value` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`total_cashouts_limit_percentage` decimal(10,6) DEFAULT '0.000000' NOT NULL,
	`maximum_roi` decimal(10,6) DEFAULT 200.000000 NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_memberships_paid_offers` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`paid_offer_id` int(10) UNSIGNED NOT NULL,
	`membership_id` int(10) UNSIGNED NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_memberships_users` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` int(10) UNSIGNED NOT NULL,
	`membership_id` int(10) UNSIGNED NOT NULL,
	`period` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`begins` datetime NOT NULL,
	`ends` datetime DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `user_id` (`user_id`),
	KEY `membership_id` (`membership_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_news` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`title` varchar(300) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`content` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`show_in_login_ads` tinyint(1) DEFAULT '0' NOT NULL,
	`show_in_login_ads_until` datetime DEFAULT NULL,
	`created` datetime NOT NULL,
	`modified` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_paid_offers` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`advertiser_id` int(10) UNSIGNED DEFAULT NULL,
	`category_id` int(10) UNSIGNED NOT NULL,
	`title` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`url` varchar(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`total_slots` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`taken_slots` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`value` decimal(17,8) DEFAULT NULL,
	`accepted_applications` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`pending_applications` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rejected_applications` int(11) DEFAULT 0 NOT NULL,
	`status` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'Pending' NOT NULL,
	`modified` datetime NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_paid_offers_applications` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` int(10) UNSIGNED NOT NULL,
	`offer_id` int(10) UNSIGNED NOT NULL,
	`description` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`status` int(3) UNSIGNED DEFAULT 0 NOT NULL,
	`reject_reason` text CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`modified` datetime NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_paid_offers_categories` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_paid_offers_packages` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`value` decimal(17,8) NOT NULL,
	`quantity` int(10) UNSIGNED NOT NULL,
	`price` decimal(17,8) NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_paid_offers_targetted_locations` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`offer_id` int(10) UNSIGNED NOT NULL,
	`location` varchar(768) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_paid_offers_values` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`value` decimal(17,8) NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_payment_gateways` (
	`name` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`deposits` tinyint(1) DEFAULT '0' NOT NULL,
	`cashouts` tinyint(1) DEFAULT '0' NOT NULL,
	`minimum_deposit_amount` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`deposit_fee_percent` decimal(6,3) UNSIGNED DEFAULT '0.000' NOT NULL,
	`deposit_fee_amount` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`cashout_fee_percent` decimal(6,3) UNSIGNED DEFAULT '0.000' NOT NULL,
	`cashout_fee_amount` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`api_settings` text CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,	PRIMARY KEY  (`name`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_pending_emails` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`format` varchar(4) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'text' NOT NULL,
	`subject` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`content` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`sender_name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`reply_to` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`next_user` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_rent_extension_periods` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`days` int(8) UNSIGNED NOT NULL,
	`discount` int(3) UNSIGNED NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_rented_referrals_prices` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`membership_id` int(10) UNSIGNED NOT NULL,
	`min` int(5) UNSIGNED NOT NULL,
	`max` int(5) UNSIGNED NOT NULL,
	`price` decimal(17,8) NOT NULL,
	`autopay_price` decimal(17,8) NOT NULL,	PRIMARY KEY  (`id`),
	KEY `fk_membership_id` (`membership_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_settings` (
	`key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`value` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`global` tinyint(1) NOT NULL,	PRIMARY KEY  (`key`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_short_item_ids` (
	`id` varchar(36) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
	`item_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`check_amount` decimal(17,8) DEFAULT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_support_canned_answers` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`message` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`created` datetime NOT NULL,
	`modified` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_support_departments` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_support_ticket_answers` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`ticket_id` varchar(36) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
	`sender_flag` int(3) UNSIGNED NOT NULL,
	`message` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_support_tickets` (
	`id` varchar(36) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
	`user_id` int(10) UNSIGNED DEFAULT NULL,
	`full_name` varchar(71) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`email` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`department_id` int(10) UNSIGNED NOT NULL,
	`subject` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`status` int(3) UNSIGNED NOT NULL,
	`message` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`created` datetime NOT NULL,
	`modified` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_targetted_locations` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`ad_id` varchar(36) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`location` varchar(768) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_user_metadata` (
	`user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`verify_token` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`verify_expires` datetime DEFAULT NULL,
	`reset_token` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`verify_date` datetime DEFAULT NULL,
	`admin_note` text CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`next_email` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,	PRIMARY KEY  (`user_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_user_profiles` (
	`user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`gender` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`birth_day` date DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	`address` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`pay_pal` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`payza` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`pay_pal_modified` datetime DEFAULT NULL,
	`payza_modified` datetime DEFAULT NULL,
	`neteller` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`neteller_modified` datetime DEFAULT NULL,
	`payeer` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`payeer_modified` datetime DEFAULT NULL,
	`advcash` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`advcash_modified` datetime DEFAULT NULL,
	`perfect_money` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`perfect_money_modified` datetime DEFAULT NULL,
	`solid_trust_pay` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`solid_trust_pay_modified` datetime DEFAULT NULL,
	`purchase_balance` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`purchase_balance_modified` datetime DEFAULT NULL,
	`blockchain` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`blockchain_modified` datetime DEFAULT NULL,
	`coinpayments` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`coinpayments_modified` datetime DEFAULT NULL,
	`skrill` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`skrill_modified` datetime DEFAULT NULL,
	`okpay` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`okpay_modified` datetime DEFAULT NULL,
	`manual_pay_pal` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
	`manual_pay_pal_modified` datetime DEFAULT NULL,	PRIMARY KEY  (`user_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_user_statistics` (
	`user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`total_clicks` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`total_clicks_earned` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`last_click_date` datetime DEFAULT NULL,
	`total_rrefs_clicks` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`total_rrefs_credited_clicks` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`total_rrefs_clicks_earned` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`total_drefs_clicks` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`total_drefs_credited_clicks` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`total_drefs_clicks_earned` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`earned_as_dref` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`clicks_as_dref` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_dref_credited` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_dref_credited_0` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_dref_credited_1` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_dref_credited_2` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_dref_credited_3` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_dref_credited_4` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_dref_credited_5` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_dref_credited_6` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`earned_as_rref` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`clicks_as_rref` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_0` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_1` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_2` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_3` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_4` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_5` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`clicks_as_rref_credited_6` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`user_clicks_0` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`user_clicks_1` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`user_clicks_2` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`user_clicks_3` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`user_clicks_4` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`user_clicks_5` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`user_clicks_6` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_0` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_1` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_2` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_3` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_4` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_5` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_6` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_credited_0` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_credited_1` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_credited_2` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_credited_3` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_credited_4` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_credited_5` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`dref_clicks_credited_6` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_0` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_1` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_2` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_3` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_4` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_5` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_6` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_credited_0` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_credited_1` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_credited_2` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_credited_3` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_credited_4` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_credited_5` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`rref_clicks_credited_6` int(10) UNSIGNED DEFAULT 0 NOT NULL,
	`total_deposits` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`total_cashouts` decimal(17,8) DEFAULT '0.00000000' NOT NULL,
	`payeer_deposits` decimal(17,8) NOT NULL,
	`neteller_cashouts` decimal(17,8) NOT NULL,
	`payeer_cashouts` decimal(17,8) NOT NULL,
	`neteller_deposits` decimal(17,8) NOT NULL,
	`advcash_deposits` decimal(17,8) NOT NULL,
	`advcash_cashouts` decimal(17,8) NOT NULL,
	`pay_pal_deposits` decimal(17,8) NOT NULL,
	`pay_pal_cashouts` decimal(17,8) NOT NULL,
	`perfect_money_deposits` decimal(17,8) NOT NULL,
	`perfect_money_cashouts` decimal(17,8) NOT NULL,
	`solid_trust_pay_deposits` decimal(17,8) NOT NULL,
	`solid_trust_pay_cashouts` decimal(17,8) NOT NULL,
	`payza_deposits` decimal(17,8) NOT NULL,
	`payza_cashouts` decimal(17,8) NOT NULL,
	`purchase_balance_deposits` decimal(17,8) NOT NULL,
	`purchase_balance_cashouts` decimal(17,8) NOT NULL,
	`blockchain_deposits` decimal(17,8) NOT NULL,
	`blockchain_cashouts` decimal(17,8) NOT NULL,
	`coinpayments_deposits` decimal(17,8) NOT NULL,
	`coinpayments_cashouts` decimal(17,8) NOT NULL,
	`skrill_deposits` decimal(17,8) NOT NULL,
	`skrill_cashouts` decimal(17,8) NOT NULL,
	`okpay_deposits` decimal(17,8) NOT NULL,
	`okpay_cashouts` decimal(17,8) NOT NULL,
	`manual_pay_pal_deposits` decimal(17,8) NOT NULL,
	`manual_pay_pal_cashouts` decimal(17,8) NOT NULL,	PRIMARY KEY  (`user_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_username_locks` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`template` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`note` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`created` datetime NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_users` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`username` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`password` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
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
	`location` varchar(768) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '*' NOT NULL,
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
	`allow_emails` tinyint(1) DEFAULT '1' NOT NULL,
	`autopay_enabled` tinyint(1) DEFAULT '0' NOT NULL,
	`autopay_done` tinyint(1) DEFAULT '0' NOT NULL,
	`auto_renew_days` int(6) UNSIGNED DEFAULT 0 NOT NULL,
	`auto_renew_extend` int(6) UNSIGNED DEFAULT 0 NOT NULL,
	`auto_renew_attempts` int(5) UNSIGNED DEFAULT 0 NOT NULL,
	`accepted_applications` int(11) DEFAULT 0 NOT NULL,
	`rejected_applications` int(11) DEFAULT 0 NOT NULL,
	`pending_applications` int(11) DEFAULT 0 NOT NULL,
	`evercookie` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
	`first_click` datetime DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `rented_upline_id` (`rented_upline_id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

CREATE TABLE `futurumclix`.`futurum_visited_ads` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`ad_id` varchar(36) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
	`user_id` int(10) UNSIGNED NOT NULL,
	`created` datetime DEFAULT NULL,	PRIMARY KEY  (`id`),
	KEY `user_id` (`user_id`),
	KEY `ad_id` (`ad_id`),
	KEY `created` (`created`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_bin,
	ENGINE=InnoDB;

