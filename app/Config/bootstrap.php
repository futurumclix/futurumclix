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
 * @copyright     Copyright (c) 2018 FuturumClix
 * @link          https://github.com/futurumclix/futurumclix
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPLv3
 */

define('USER_MODEL', 'User');
define('__VERSIONDATE__', '20171229');

Cache::config('default', array('engine' => 'File'));
Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher'
));

/* Configuration */
Configure::write('UserLogin.tries', 10);
Configure::write('Admin.aliases', array(
	'superModerator' => 'SuperModerators',
	'user' => 'Users',
));
App::uses('CakeLog', 'Log');
CakeLog::config('error', array(
	'engine' => 'File',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));

App::build(array(
	'Payments' => array(APP.'Lib'.DS.'Payments'.DS),
));

App::uses('Module', 'Lib');

try {
	CakePlugin::load(array(
		'Installer',
	));
	/* routes should be in routes.php in plugin, but they won't work that way (we need to install '/' route before standard one)... */
	Router::connect('/', array('plugin' => 'Installer', 'controller' => 'installer', 'action' => 'index'));
	Router::connect('/:action', array('plugin' => 'Installer', 'controller' => 'installer'));
	CakePlugin::loadAll(array('bootstrap' => true, 'ignoreMissing' => true));
	return true;
}	catch(MissingPluginException $e) {
	/* do nothing, go next. */
}

App::uses('ClassRegistry', 'Utility');
$settingsModel = ClassRegistry::init('Settings');

if(!Configure::read('GlobalSettings')) {

	$globals = $settingsModel->fetchGlobals();

	foreach($globals['Settings'] as $key => $value) {
		Configure::write($key, $value);
	}

	unset($globals);

	Configure::write('GlobalSettings', true);
}

if(Configure::read('App.fullBaseUrl') === null) {
	Configure::write('App.fullBaseUrl', Configure::read('siteURL'));
}

if(!bcscale(8)) {
	throw new InternalErrorException(__d('exception', 'Failed to set decimal scale'));
}

App::uses('CurrencyFormatter', 'Lib');
CurrencyFormatter::init();

Configure::write('Session', array(
	'defaults' => 'php',
	'ini' => array(
		'session.cookie_secure' => false
	)
));

/**
 * Load modules
 */
Module::loadInstalled();

if(!Module::active('BotSystem') && Configure::read('rentingOption') != 'realOnly') {
	if(!$settingsModel->store(array('Settings' => array('rentingOption' => 'realOnly')), array('rentingOption'), true)) {
		throw new InternalErrorException(__d('exception', 'Wrong renting option'));
	}
}

/**
 * Load plugins
 */

CakePlugin::load(array(
	'Evercookie' => array(
		'routes' => true,
		'bootstrap' => true,
		'ignoreMissing' => true,
	),
	'Forum' => array(
		'routes' => true,
		'bootstrap' => true,
		'ignoreMissing' => true
	),
	'Utility' => array(
		'routes' => true,
		'bootstrap' => true,
		'ignoreMissing' => true
	),
	'DebugKit' => array('routes' => true, 'bootstrap' => true, 'ignoreMissing' => true), // NOTE: need to be in one line (autoremoving in script)
	'TinyMCE',
	'Online',
	'GoogleAuthenticator',
));

/* Forum plugin configuration */
Configure::write('Forum.settings.name', Configure::read('siteName'));
Configure::write('Forum.settings.email', Configure::read('siteEmail'));
Configure::write('Forum.settings.emailSender', Configure::read('siteEmailSender'));

unset($settingsModel);
