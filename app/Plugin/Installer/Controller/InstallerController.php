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
class InstallerController extends InstallerAppController {
	public $uses = false;
	public $components = array(
		'Notice',
	);
	public $helpers = array(
		'Notice',
		'AdminForm',
	);

/**
 * _installSchema method
 *
 * Update database with Schema object
 * 
 * @return mixed (boolean/string)
 */
	private static function _installSchema($options = null) {
		App::uses('CakeSchema', 'Model');

		if($options) {
			$moduleSchema = new CakeSchema($options);
		} else {
			$moduleSchema = new CakeSchema();
		}

		$activeSchema = new CakeSchema();
		$db = ConnectionManager::getDataSource($activeSchema->connection);

		$active = $activeSchema->read(array('models' => false));
		$module = $moduleSchema->load();

		$compare = $activeSchema->compare($active, $module);

		$contents = array();

		if(empty($table)) {
			foreach($compare as $table => $changes) {
				if(isset($compare[$table]['create'])) {
					$contents[$table] = $db->createSchema($module, $table);
				} else {
					$contents[$table] = $db->alterSchema(array($table => $compare[$table]), $table);
				}
			}
		} elseif(isset($compare[$table])) {
			if(isset($compare[$table]['create'])) {
				$contents[$table] = $db->createSchema($Schema, $table);
			} else {
				$contents[$table] = $db->alterSchema(array($table => $compare[$table]), $table);
			}
		}

		if(empty($contents)) {
			return true;
		}

		return self::_runSQL($contents, 'update', $activeSchema);
	}

/**
 * _runSQL method
 *
 * Runs sql from _installSchema()
 *
 * @param array $contents The contents to execute.
 * @param string $event The event to fire
 * @param CakeSchema $Schema The schema instance.
 * @return mixed (boolean/string)
 */
	private static function _runSQL($contents, $event, CakeSchema &$Schema) {
		if(empty($contents)) {
			return;
		}
		$db = ConnectionManager::getDataSource($Schema->connection);

		foreach($contents as $table => $sql) {
			if(!empty($sql)) {
				if(!$Schema->before(array($event => $table))) {
					return false;
				}

				$error = null;

				try {
					$db->execute($sql);
				} catch(PDOException $e) {
					$error = $table . ': ' . $e->getMessage();
				}

				$Schema->after(array($event => $table, 'errors' => $error));

				if(!empty($error)) {
					return $error;
				}
			}
		}
		return true;
	}

	/* load only components specified in this controller, so we can skip AppController ones */
	public function constructClasses() {
		if($this->uses) {
			$this->uses = (array)$this->uses;
			list(, $this->modelClass) = pluginSplit(reset($this->uses));
		}
		$this->Components->init($this);
		return true;
	}

	public function beforeFilter() {
		$anyErrors = false;

		parent::beforeFilter();
		$this->layout = 'installer';

		if(!version_compare(PHP_VERSION, '5.6', '>=')) {
			$anyErrors = true;
		}

		if(!is_writable(TMP)) {
			$anyErrors = true;
		}

		if(!is_writable(APP.'Config'.DS.'database.ini.php')) {
			$anyErrors = true;
		}
		
		if(!is_writable(APP.'Config'.DS.'core.ini.php')) {
			$anyErrors = true;
		}

		if(!is_writable(APP.'Media'.DS.'Banners')) {
			$anyErrors = true;
		}

		if(!is_writable(APP.'Media'.DS.'Banners'.DS.'Cache')) {
			$anyErrors = true;
		}

		if(!ini_get('allow_url_fopen')) {
			$this->Notice->error(__d('installer', 'Please enable "allow_url_fopen" in your php.ini file'));
			$anyErrors = true;
		} else {
			$protocol = 'http://';

			if($this->request->is('ssl')) {
				$protocol = 'https://';
			}

			$remoteFile = file_get_contents($protocol.$_SERVER['SERVER_NAME'].DS.'css'.DS.'installer.css');
			$localFile = file_get_contents(WWW_ROOT.'css'.DS.'installer.css');

			if($remoteFile !== $localFile) {
				$anyErrors = true;
			}
		}

		if(!extension_loaded('curl')) {
			$anyErrors = true;
		}

		if(!extension_loaded('bcmath')) {
			$anyErrors = true;
		}

		if(!extension_loaded('openssl')) {
			$anyErrors = true;
		}

		if($this->request->params['action'] != 'index' && $anyErrors) {
			$this->redirect(array('action' => 'index'));
		}
	}

	public function index() {
		$anyErrors = false;

		if(!version_compare(PHP_VERSION, '5.6', '>=')) {
			$this->Notice->error(__d('installer', 'Your PHP version is too old. You need at least 5.6.'));
			$anyErrors = true;
		}

		if(!is_writable(TMP)) {
			$this->Notice->error(__d('installer', 'Temp directory is not writable by script. Please check permissions to "%s".', TMP));
			$anyErrors = true;
		}

		if(!is_writable(APP.'Config'.DS.'database.ini.php')) {
			$this->Notice->error(__d('installer', 'Database configuration file is not writable by script. Please check permissions to "%s".', APP.'Config'.DS.'database.ini.php'));
			$anyErrors = true;
		}
		
		if(!is_writable(APP.'Config'.DS.'core.ini.php')) {
			$this->Notice->error(__d('installer', 'Configuration file is not writable by script. Please check permissions to "%s".', APP.'Config'.DS.'core.ini.php'));
			$anyErrors = true;
		}

		if(!is_writable(APP.'Media'.DS.'Banners')) {
			$this->Notice->error(__d('installer', 'Banners directory is not writable by script. Please check permissions to "%s".', APP.'Media'.DS.'Banners'));
			$anyErrors = true;
		}

		if(!is_writable(APP.'Media'.DS.'Banners'.DS.'Cache')) {
			$this->Notice->error(__d('installer', 'Banners cache directory is not writable by script. Please check permissions to "%s".', APP.'Media'.DS.'Banners'.DS.'Cache'));
			$anyErrors = true;
		}

		if(!ini_get('allow_url_fopen')) {
			$this->Notice->error(__d('installer', 'Please enable "allow_url_fopen" in your php.ini file'));
			$anyErrors = true;
		} else {
			$protocol = 'http://';

			if($this->request->is('ssl')) {
				$protocol = 'https://';
			}

			$remoteFile = file_get_contents($protocol.$_SERVER['SERVER_NAME'].DS.'css'.DS.'installer.css');
			$localFile = file_get_contents(WWW_ROOT.'css'.DS.'installer.css');

			if($remoteFile !== $localFile) {
				$this->Notice->error(__d('installer', 'Sorry but you need to have URL rewriting enabled on your server in order to install FuturumClix. Please check our Installation Manual for more details.'));
				$anyErrors = true;
			}
		}

		if(!extension_loaded('gettext')) {
			$this->Notice->error(__d('installer', 'FuturumClix needs GetText extension to be installed. Please install it and refresh page.'));
			$anyErrors = true;
		}

		if(!extension_loaded('curl')) {
			$this->Notice->error(__d('installer', 'FuturumClix needs curl PHP extension to be installed. Please install it and refresh page.'));
			$anyErrors = true;
		}

		if(!extension_loaded('bcmath')) {
			$this->Notice->error(__d('installer', 'FuturumClix needs BCMath PHP extension to be installed. Please install it and refresh page.'));
			$anyErrors = true;
		}

		if(!extension_loaded('openssl')) {
			$this->Notice->error(__d('installer', 'FuturumClix needs OpenSSL PHP extension to be installed. Please install it and refresh page.'));
			$anyErrors = true;
		}

		$this->set(compact('anyErrors'));
	}

	/* database */
	public function step1() {
		if($this->request->is(array('post', 'put'))) {
			$ok = true;

			if(!isset($this->request->data['host']) || empty($this->request->data['host'])) {
				$this->Notice->error(__d('installer', 'Please specify host'));
				$ok = false;
			}
			if(!isset($this->request->data['dlogin']) || empty($this->request->data['dlogin'])) {
				$this->Notice->error(__d('installer', 'Please specify login'));
				$ok = false;
			}
			if(!isset($this->request->data['dpassword']) || empty($this->request->data['dpassword'])) {
				$this->Notice->error(__d('installer', 'Please specify password'));
				$ok = false;
			}
			if(!isset($this->request->data['database']) || empty($this->request->data['database'])) {
				$this->Notice->error(__d('installer', 'Please specify database'));
				$ok = false;
			}
			if(!isset($this->request->data['prefix'])) {
				$this->request->data['prefix'] = '';
			}

			if($ok) {
				$test = array(
					'host' => $this->request->data['host'],
					'login' => $this->request->data['dlogin'],
					'password' => $this->request->data['dpassword'],
					'database' => $this->request->data['database'],
					'prefix' => $this->request->data['prefix'],
					'datasource' => 'Database/Mysql',
					'persistent' => false,
					'encoding' => 'utf8',
				);

				try {
					App::uses('Mysql', 'Model/Datasource/Database');
					$dbo = new Mysql($test, false);
					if($dbo->connect()) {
						$path = APP.'Config'.DS.'database.ini.php';

						unset($test['encoding']);
						unset($test['persistent']);
						unset($test['datasource']);

						$contents = ";<?php exit(0); ?>\n[default]\n";
						foreach($test as $k => $v) {
							$contents .= "$k = \"$v\"\n";
						}

						if(file_put_contents($path, $contents) === FALSE) {
							return $this->Notice->error(__d('installer', 'Failed to save database configuration. Please check if file %s is writable.', $path));
						}
						if(!@chmod($path, 0644)) {
							$this->Notice->info(__d('installer', 'Failed to automatically change permissions on %s. Please make sure that file can be accessed only by trusted users.', $path));
						}
						return $this->redirect(array('action' => 'step2'));
					}
				} catch(CakeException $e) {
					$errorMsg = '';
					if(method_exists($e, 'getAttributes')) {
						$attributes = $e->getAttributes();
						$errorMsg = __d('installer', 'Error: "%s"', $attributes['message']);
					}
					$this->Notice->error(__d('installer', 'Failed to connect to database, please check configuration. %s', $errorMsg));
				}
			}
		}
	}

	/* install schema */
	public function step2() {
		if($this->request->is(array('post', 'put'))) {
			$res = $this->_installSchema();

			if($res === true) {
				$forumRes = $this->_installSchema(array('plugin' => 'Forum', 'name' => 'Forum'));
				$onlineRes = $this->_installSchema(array('plugin' => 'Online', 'name' => 'Online'));

				if($forumRes === true && $onlineRes === true) {
					return $this->redirect(array('action' => 'step3'));
				} elseif(is_string($forumRes)) {
					$this->Notice->error($forumRes);
				} elseif(is_string($onlineRes)) {
					$this->Notice->error($onlineRes);
				} else {
					throw new InternalErrorException(__d('installer', 'Failed to install database schema. Please contact support.'));
				}
			} elseif(is_string($res)) {
				$this->Notice->error($res);
			} else {
				throw new InternalErrorException(__d('installer', 'Failed to install database schema. Please contact support.'));
			}
		}
	}

	/* site configuration */
	public function step3() {
		$this->Settings = ClassRegistry::init('Settings');
		$globalKeys = array(
			'siteName',
			'siteTitle',
			'siteURL',
			'siteEmail',
			'siteEmailSender',
		);

		if(Configure::read('App.fullBaseUrl') && empty($this->request->data['Settings']['siteURL'])) {
			$this->request->data['Settings']['siteURL'] = Configure::read('App.fullBaseUrl');
		}

		if($this->request->is(array('post', 'put'))) {
			if($this->request->data['Security']['auto']) {
				$bytes = openssl_random_pseudo_bytes(30, $cstrong);
				if(!$cstrong) {
					return $this->Notice->error(__d('installer', 'Automatic mode failed, returned data is not "cryptographically strong"! Please enter manually a real random data.'));
				}
				$this->request->data['Security']['salt'] = bin2hex($bytes);

				$bytes = openssl_random_pseudo_bytes(30, $cstrong);
				if(!$cstrong) {
					return $this->Notice->error(__d('installer', 'Automatic mode failed, returned data is not "cryptographically strong"! Please enter manually a real random data.'));
				}
				$this->request->data['Security']['key'] = bin2hex($bytes);

				$bytes = openssl_random_pseudo_bytes(60, $cstrong);
				if(!$cstrong) {
					return $this->Notice->error(__d('installer', 'Automatic mode failed, returned data is not "cryptographically strong"! Please enter manually a real random data.'));
				}
				$numbers = '0123456789';
				for($i = 0; $i < 60; $i++) {
					$this->request->data['Security']['cipherSeed'] .= $numbers[ord($bytes[$i]) % 10];
				}
			}

			if(empty($this->request->data['Security']['salt'])) {
				return $this->Notice->error(__d('installer', 'Please enter security salt.'));
			}

			if(empty($this->request->data['Security']['key'])) {
				return $this->Notice->error(__d('installer', 'Please enter security key.'));
			}

			if(empty($this->request->data['Security']['cipherSeed'])) {
				return $this->Notice->error(__d('installer', 'Please enter security cipher seed.'));
			}

			$debugLevel = intval($this->request->data['Settings']['productionInstall']) == 0 ? 2 : 0;
			unset($this->request->data['Settings']['productionInstall']);

			$toFile  = ";<?php exit(0); ?>\n[debug]\nlevel = \"$debugLevel\"\n[security]\n";
			$toFile .= "salt = \"{$this->request->data['Security']['salt']}\"\n";
			$toFile .= "key = \"{$this->request->data['Security']['key']}\"\n";
			$toFile .= "cipher_seed = \"{$this->request->data['Security']['cipherSeed']}\"\n";

			$path = APP.'Config'.DS.'core.ini.php';
			if(file_put_contents($path, $toFile) === FALSE) {
				return $this->Notice->error(__d('installer', 'Failed to save security configuration configuration. Please check if file %s is writable.', $path));
			}

			if(!@chmod($path, 0644)) {
				$this->Notice->info(__d('installer', 'Failed to automatically change permissions on %s. Please make sure that this file can be accessed only by trusted users.', $path));
			}

			if($this->Settings->store($this->request->data, $globalKeys, true)) {
				$this->Notice->success(__d('installer', 'The settings has been saved.'));
				return $this->redirect(array('action' => 'step4'));
			} else {
				$this->Notice->error(__d('installer', 'The settings could not be saved. Please, try again.'));
			}
		}
	}

	private function _importSQL($path) {
		App::uses('ConnectionManager', 'Model');
		$db = ConnectionManager::getDataSource('default');

		$dbConfig = new DATABASE_CONFIG();
		$prefix = $dbConfig->default['prefix'];

		$statements = file_get_contents($path);

		if($statements === false) {
			throw new InternalErrorException(__d('installer', 'Database file not found'));
		}

		if(!empty($prefix)) {
			$tables = array(
				'ads_categories',
				'aros',
				'click_values',
				'currencies',
				'direct_referrals_prices',
				'emails',
				'ip2nationCountries',
				'ip2nation',
				'memberships',
				'payment_gateways',
				'rent_extension_periods',
				'rented_referrals_prices',
				'settings',
			);
			$find = array();
			$replace = array();

			foreach($tables as $table) {
				$find[] = "`$table`";
				$replace[] = "`$prefix$table`";
			}

			$statements = str_replace($find, $replace, $statements);
		}

		$statements = explode(");\n", $statements);

		foreach($statements as $statement) {
			if(!empty(trim($statement))) {
				$db->query($statement.')');
			}
		}
		return true;
	}

	/* import database contents */
	public function step4() {
		if($this->request->is(array('post', 'put'))) {
			$res = false;
			$path = CakePlugin::path('Installer').'Config'.DS.'Databases'.DS; 
			switch($this->request->data['option']) {
				case 'empty':
					$res = $this->_importSQL($path.'empty_database.sql');
				break;

				case 'default':
					$res = $this->_importSQL($path.'default_database.sql');
				break;
			}
			
			if($res) {
				$this->Notice->success(__d('installer', 'Database sucessfully imported.'));
				return $this->redirect(array('action' => 'step5'));
			} else {
				$this->Notice->error(__d('installer', 'Failed to import database. Please, try again.'));
			}
		}
	}

	/* create admin account */
	public function step5() {
		$this->Admin = ClassRegistry::init('Admin');
		if($this->request->is(array('post', 'put'))) {
			$this->Admin->create();
			if($this->Admin->save($this->request->data)) {
				$this->Notice->success(__d('installer', 'Admin has been saved.'));
				return $this->redirect(array('action' => 'end'));
			} else {
				$this->Notice->error(__d('installer', 'Admin could not be saved. Please, try again.'));
			}
		}
	}

	/* finish */
	public function end() {
		if(!@rename(APP.'Plugin'.DS.'Installer', APP.'Plugin'.DS.'InstallerDone')) {
			$this->Notice->error(__d('installer', 'Failed to remove installer. Please remove or rename "%s" manually. Please remove those files, otherwise your script will not be working properly and you are exposing yourself for security issues!', APP.'Plugin'.DS.'Installer'));
		} else {
			$this->Notice->success(__d('installer', 'FuturumClix is sucessfully installed. Please, log in.'), 'admin_action');
			$this->redirect('/admin');
		}
	}
}
