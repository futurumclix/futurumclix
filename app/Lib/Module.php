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
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('CakeSchema', 'Model');

class Module {
/**
 * MODULES_PATH
 *
 * @const string
 */
	const MODULES_PATH = APP.'Module'.DS;

/**
 * INFO_FILE_NAME
 *
 * @const string
 */
	const INFO_FILE_NAME = 'module.json';

/**
* Cached data.
*
* @var array
*/
	private static $_available = null;
	private static $_active = null;

/**
 * inspect method
 *
 * @return mixed (boolean/array)
 */
	private static function inspect($name) {
		if(function_exists('ioncube_read_file')) {
			$data = ioncube_read_file(self::MODULES_PATH.$name.DS.self::INFO_FILE_NAME);
		} else {
			$data = file_get_contents(self::MODULES_PATH.$name.DS.self::INFO_FILE_NAME);
			if($data === false) {
				$data = 1;
			}
		}

		if(!is_int($data)) {
			$data = json_decode($data, true, 10, JSON_BIGINT_AS_STRING);
			if($data === null || empty($data)) {
				throw new InternalErrorException(__d('admin', 'Failed to read "%s" module info file', $name));
			}
		} else {
			throw new InternalErrorException(__d('exception', 'IonCube Read File Error: %d', $data));
		}
		return $data;
	}

/**
 * scanModules method
 *
 * @return array
 */
	private static function scanModules($force = false) {
		if(!self::$_available || $force) {
			self::$_available = array();

			$dir = new Folder(self::MODULES_PATH);
			$content = $dir->read();

			foreach($content[0] as $module) {
				$info = self::inspect($module);

				if($info) {
					self::$_available[$module] = $info;
				}
			}
		}
		return self::$_available;
	}

/**
 * installSchema method
 *
 * Update database with Schema object
 * 
 * @return mixed (boolean/string)
 */
	private static function installSchema($moduleName, $table = null) {
		$moduleSchema = new CakeSchema(array(
			'plugin' => $moduleName,
			'name' => $moduleName,
		));
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

		if(!empty($contents)) {
			$res = self::runSQL($contents, 'update', $activeSchema);

			if($res !== true) {
				return $res;
			}
		}

		clearCache(null, 'models');
		Cache::clear();
		$db->cacheSources = false;

		$msch = ClassRegistry::init("{$moduleName}.{$moduleName}Schema");

		if($msch && method_exists($msch, 'afterInstall')) {
			if(!$msch->afterInstall()) {
				return __d('admin', '[%s]: Failed to create default settings.', $moduleName);
			}
		}

		return true;
	}

/**
 * runSQL method
 *
 * Runs sql from installSchema()
 *
 * @param array $contents The contents to execute.
 * @param string $event The event to fire
 * @param CakeSchema $Schema The schema instance.
 * @return mixed (boolean/string)
 */
	private static function runSQL($contents, $event, CakeSchema &$Schema) {
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

/**
 * install method
 *
 * @return mixed(boolean/string)
 */
	public static function install($moduleName = null) {
		if($moduleName) {
			$available = self::getAvailable();

			if(!isset($available[$moduleName])) {
				return __d('admin', 'Module "%s" is not available.', $moduleName);
			}

			$installed = self::getInstalled();

			if(!CakePlugin::loaded($moduleName)) {
				CakePlugin::load(array(
					$moduleName => array(
						'routes' => true,
						'bootstrap' => true,
						'ignoreMissing' => true,
						'path' => self::MODULES_PATH.$moduleName.DS,
					),
				));
			}

			$res = self::installSchema($moduleName);

			if($res !== true) {
				return $res;
			}

			$installed[$moduleName] = array(
				'active' => true,
				'version' => $available[$moduleName]['version'],
			);

			return ClassRegistry::init('Settings')->store(array(
				'Settings' => array(
					'modules' => $installed,
				),
			), array('modules'), true);
		} else {
			foreach(self::getAvailableList() as $moduleName) {
				self::install($moduleName);
			}
		}
	}

/**
 * deactivate method
 *
 * @return boolean
 */
	public static function deactivate($moduleName) {
		if($moduleName) {
			$installed = self::getInstalled();

			if(!isset($installed[$moduleName])) {
				return false;
			}

			$installed[$moduleName]['active'] = false;

			return ClassRegistry::init('Settings')->store(array(
				'Settings' => array(
					'modules' => $installed,
				),
			), array('modules'), true);
		}
		return false;
	}

/**
 * getAvailable
 *
 * @return array
 */
	public static function getAvailable($force = false) {
		return self::scanModules($force);
	}

/**
 * getAvailableList
 *
 * @return array
 */
	public static function getAvailableList($force = false) {
		return array_keys(self::getAvailable($force));
	}

/**
 * getInstalled method
 *
 * @return array
 */
	public static function getInstalled() {
		$res = Configure::read('modules');
		return is_array($res) ? $res : array();
	}

/**
 * getInstalledList method
 *
 * @return array
 */
	public static function getInstalledList() {
		return array_keys(self::getInstalled());
	}

/**
 * active method
 *
 * @return boolean
 */
	public static function active($name, $force = false) {
		$active = self::getActive($force);
		return isset($active[$name]);
	}

/**
 * installed
 *
 * @return boolean
 */
	public static function installed($name, $force = false) {
		$installed = self::getInstalledList();
		return in_array($name, $installed);
	}

/**
 * getActive method
 *
 * @return array
 */
	public static function getActive($force = false) {
		if(!self::$_active || $force) {
			self::$_active = array();
			$installed = self::getInstalled();

			foreach($installed as $name => $info) {
				if(isset($info['active']) && $info['active']) {
					self::$_active[$name] = $info;
				}
			}
		}

		return self::$_active;
	}

/**
 * getActiveList method
 *
 * @return array
 */
	public static function getActiveList($force = false) {
		return array_keys(self::getActive($force));
	}

/**
 * getAll
 *
 * @return array
 */
	public static function getAll($force = false) {
		$all = self::getAvailable($force);

		foreach($all as &$v) {
			$v['status'] = 'Available';
		}

		foreach(self::getInstalledList($force) as $name) {
			if(isset($all[$name])) {
				$all[$name]['status'] = 'Installed';
			}
		}

		foreach(self::getActiveList($force) as $name) {
			if(isset($all[$name])) {
				$all[$name]['status'] = 'Active';
			}
		}

		return $all;
	}

/**
 * checkForUpdate method
 *
 * @return void
 */
	public static function checkForUpdate() {
		$installed = self::getInstalled();
		$available = self::getAvailable();

		foreach($installed as $name => $data) {
			if(isset($available[$name])) {
				if($available[$name]['version'] > $data['version']) {
					self::install($name);
				} elseif($available[$name]['version'] < $data['version']) {
					throw new InternalErrorException(__d('admin', 'Module "%s" is too old.', $name));
				}
			}
		}
	}

/**
 * loadActive method
 *
 * @return void
 */
	public static function loadInstalled() {
		self::checkForUpdate();
		$modules = array();
		foreach(self::getInstalledList() as $module) {
			$modules[$module] = array(
				'routes' => true,
				'bootstrap' => true,
				'ignoreMissing' => true,
				'path' => self::MODULES_PATH.$module.DS,
			);
		}
		CakePlugin::load($modules);
	}
}
