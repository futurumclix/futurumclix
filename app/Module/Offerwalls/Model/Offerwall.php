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
App::uses('OfferwallsAppModel', 'Offerwalls.Model');
App::uses('Security', 'Utility');
/**
 * Offerwall Model
 *
 */
class Offerwall extends OfferwallsAppModel {
	public $useTable = 'offerwalls_offerwalls';

	public $primaryKey = 'name';

	public $displayField = 'name';

	public $validate = array(
		'name' => array(
			'between' => array(
				'rule' => array('between', 0, 129),
				'message' => 'Name should be between 0 and 129 characters long',
				'allowEmpty' => false,
			),
		),
		'enabled' => array('boolean'),
		'allowed_ips' => array(
			'commaSeparatedIPs' => array(
				'rule' => '/^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}(,\n|,?))*$/',
				'message' => 'Please enter a comma separated list of valid IP addresses',
				'allowEmpty' => true,
			),
		),
	);

	public function beforeSave($options = array()) {
		if(isset($this->data[$this->alias]['api_settings'])) {
			$this->data[$this->alias]['api_settings'] = base64_encode(Security::encrypt(serialize($this->data[$this->alias]['api_settings']), Configure::read('Security.key')));
		}
		return true;
	}

	public function afterFind($results, $primary = false) {
		for($i = 0, $e = count($results); $i < $e; ++$i) {
			if(isset($results[$i][$this->alias]['api_settings'])) {
				$results[$i][$this->alias]['api_settings'] = unserialize(Security::decrypt(base64_decode($results[$i][$this->alias]['api_settings']), Configure::read('Security.key')));
			}
		}
		return $results;
	}

	public function arrayByName($data) {
		return Hash::combine($data, '{n}.'.$this->alias.'.name', '{n}.'.$this->alias);
	}
}
