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

/* This is basically the CakePHP configuration file with ini file loading added.
 * For additional options not listed here, please refer to CakePHP 2 documentation */

$configuration = parse_ini_file('core.ini.php', true, INI_SCANNER_TYPED);

if($configuration === false) {
	throw new InternalErrorException(__d('exception', 'Failed to load configuration file'));
}

Configure::write('debug', isset($configuration['debug']['level']) ? $configuration['debug']['level'] : 0);
Configure::write('Error', array(
	'handler' => 'ErrorHandler::handleError',
	'level' => E_ALL & ~E_DEPRECATED,
	'trace' => true
));

Configure::write('Exception', array(
	'handler' => 'ErrorHandler::handleException',
	'renderer' => 'ExceptionRenderer',
	'log' => true
));
Configure::write('App.encoding', 'UTF-8');
Configure::write('Routing.prefixes', array('admin'));


Configure::write('Cache.disable', isset($configuration['cache']['enabled']) ? !$configuration['cache']['enabled'] : 0);

Configure::write('Session', array(
	'defaults' => 'php',
	'cookie' => 'FUTURUMCLIX',
));

Configure::write('Acl.classname', 'DbAcl');
Configure::write('Acl.database', 'default');

date_default_timezone_set('UTC');

$engine = isset($configuration['cache']['engine']) ? $configuration['cache']['engine'] : 'File';

// In development mode, caches should expire quickly.
$duration = '+999 days';
if (Configure::read('debug') > 0) {
	$duration = '+10 seconds';
}

// Prefix each application on the same server with a different string, to avoid Memcache and APC conflicts.
$prefix = isset($configuration['cache']['prefix']) ? $configuration['cache']['prefix'] : 'futurumclix_';

Cache::config('_cake_core_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_core_',
	'path' => CACHE . 'persistent' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration
));

Cache::config('_cake_model_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_model_',
	'path' => CACHE . 'models' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration
));

Configure::write('Security.salt', isset($configuration['security']['salt']) ? $configuration['security']['salt'] : null);
Configure::write('Security.key', isset($configuration['security']['key']) ? $configuration['security']['key'] : null);
Configure::write('Security.cipherSeed', isset($configuration['security']['cipher_seed']) ? $configuration['security']['cipher_seed'] : null);

/**
 * Disables use of HTTP PUT method as it causes some troubles
 * on servers with enabled Haproxy (but still is best to fix
 * Haproxy settings than enabling this).
 */
Configure::write('Compatibility.formDisablePUT', isset($configuration['compatibility']['disable_put']) ? $configuration['compatibility']['disable_put'] : null);

unset($configuration);
