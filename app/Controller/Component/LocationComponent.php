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
App::uses('Component', 'Controller');

/**
 * LocationComponent
 *
 *
 */
class LocationComponent extends Component {
	public function beforeRender(Controller $controller) {
		if(Module::active('AccurateLocationDatabase')) {
			$controller->helpers[] = 'AccurateLocationDatabase.Locations';
		}
	}

	public function getByIp($ip) {
		if(Module::active('AccurateLocationDatabase')) {
			return ClassRegistry::init('AccurateLocationDatabase.AccurateLocationDatabaseIp')->getLocationByIp($ip);
		} else {
			return ClassRegistry::init('Ip2Nation')->getLocationByIp($ip);
		}
	}

	public function getCountriesList() {
		if(Module::active('AccurateLocationDatabase')) {
			return ClassRegistry::init('AccurateLocationDatabase.AccurateLocationDatabaseLocation')->getCountriesList();
		} else {
			return ClassRegistry::init('Ip2NationCountry')->getLocationsList();
		}
	}

	public function getConditions($location, $alias = 'TargettedLocations.location', $includeAll = true) {
		$conditions = array('OR' => array());

		if($includeAll) {
			$conditions['OR'][] = array($alias.' LIKE' => '*');
		}

		$locationsPath = explode('/', $location);

		if(is_array($locationsPath)) {
			$a = '';
			$max = count($locationsPath);
			foreach($locationsPath as $l) {
				$a .= $l.'/';
				if($locationsPath[$max - 1] == $l && $max != 1) {
					$a = trim($a, '/');
					$conditions['OR'][] = array($alias.' LIKE' => $a);
				} else {
					$conditions['OR'][] = array($alias.' LIKE' => $a.'*');
				}
			}
		} else {
			$conditions['OR'][] = array($alias.' LIKE ' => $locationsPath.'/*');
		}

		return $conditions;
	}
}

