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
class Ip2NationCountry extends AppModel {
	public $useTable = 'ip2nationCountries';
	public $displayField = 'country';

	public function getCountriesList() {
		$this->recursive = -1;
		return $this->find('list', array(
			'conditions' => array('code != ' => '01'),
		));
	}

	public function getLocationsList() {
		$this->recursive = -1;
		$res = $this->find('all', array(
			'fields' => array(
				'CONCAT(country, \'/*\') as location',
				'country',
			),
			'conditions' => array(
				'code != ' => '01'
			),
		));
		return Hash::combine($res, '{n}.0.location', '{n}.'.$this->alias.'.country');
	}

	public function findIdByLocation($location) {
		$location = explode('/', $location);
		if($location == '*') {
			return null;
		} elseif(is_array($location)) {
			$location = $location[0];
		}

		$this->recursive = -1;
		$res = $this->find('first', array(
			'fields' => array('id'),
			'conditions' => array(
				'country' => $location,
			),
		));
		if(empty($res)) {
			return null;
		}
		return $res[$this->alias]['id'];
	}
}
