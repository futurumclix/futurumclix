<?php
/**
 * Copyright (c) 2018 FuturumClix
 *
 * This program is free software: you can redistribute it and/or  modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Please notice this program incorporates variety of libraries or other
 * programs that may or may not have their own licenses, also they may or
 * may not be modified by FuturumClix. All modifications made by
 * FuturumClix are available under the terms of GNU Affero General Public
 * License, version 3, if original license allows that.
 *
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

require_once dirname(__DIR__) . '/Vendor/autoload.php';

App::uses('ClassRegistry', 'Utility');
App::uses('Sanitize', 'Utility');

/**
 * Forum critical constants.
 */
define('FORUM_PLUGIN', dirname(__DIR__) . '/');

// Table Prefix
if (!defined('FORUM_PREFIX')) {
    define('FORUM_PREFIX', 'forum_');
}

// Database config
if (!defined('FORUM_DATABASE')) {
    define('FORUM_DATABASE', 'default');
}

Configure::write('User.routes', array(
		'login' => array('plugin' => '', 'controller' => 'users', 'action' => 'login'),
		'logout' => array('plugin' => '', 'controller' => 'users', 'action' => 'logout'),
		'forgotPass' => array('plugin' => '', 'controller' => 'users', 'action' => 'sendPasswordRequestEmail'),
		'settings' => array('plugin' => '', 'controller' => 'userProfiles', 'action' => 'edit', ),
	)
);

Configure::write('Admin.coreName', 'Core');
Configure::write('Admin.modelDefaults', array());

/**
 * Current version.
 */
Configure::write('Forum.version', file_get_contents(dirname(__DIR__) . '/version.md'));

/**
 * Customizable layout; defaults to the plugin layout.
 */
Configure::write('Forum.viewLayout', 'forum');

/**
 * List of settings that alter the forum system.
 */
Configure::write('Forum.settings', array(
    'name' => __d('forum', 'Forum'),
    'email' => 'forum@futurumclix.com',
    'url' => Router::url('/', true),
    'titleSeparator' => ' - ',

    // Topics
    'topicsPerPage' => 20,
    'topicsPerHour' => 50,
    'topicFloodInterval' => 300,
    'topicPagesTillTruncate' => 10,
    'topicDaysTillAutolock' => 21,
    'excerptLength' => 500,

    // Posts
    'postsPerPage' => 15,
    'postsPerHour' => 15,
    'postsTillHotTopic' => 35,
    'postFloodInterval' => 60,

    // Subscriptions
    'enableTopicSubscriptions' => true,
    'enableForumSubscriptions' => true,
    'autoSubscribeSelf' => true,
    'subscriptionTemplate' => '',

    // Ratings
    'enablePostRating' => true,
    'showRatingScore' => true,
    'ratingBuryThreshold' => -25,
    'rateUpPoints' => 1,
    'rateDownPoints' => 1,

    // Misc
    'whosOnlineInterval' => '-15 minutes',
    'enableQuickReply' => true,
    'enableGravatar' => true,
    'censoredWords' => array(),
    'defaultLocale' => 'eng',
    'defaultTimezone' => '-8',
));

/**
 * Add forum specific user field mappings.
 */
Configure::write('User.fieldMap', array(
    'username'      => 'username',
    'password'      => 'password',
    'email'         => 'email',
    'avatar'        => 'avatar',
    'totalTopics'   => 'topic_count',
    'totalPosts'    => 'post_count',
    'signature'     => 'signature',
    'status'        => 'forum_status',
));

Configure::write('User.statusMap', array(
	'pending' => 0,
	'active' => 1,
	'banned' => 2
));
