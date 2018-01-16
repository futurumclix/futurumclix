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
App::uses('AppModel', 'Model');
/**
 * IpLock Model
 *
 */
class IpLock extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'ip_start' => array(
			'ip' => array(
				'rule' => 'ip',
				'message' => 'Please supply a valid IP address.',
				'allowEmpty' => false,
			),
		),
		'ip_end' => array(
			'ip' => array(
				'rule' => 'ip',
				'message' => 'Please supply a valid IP address.',
				'allowEmpty' => false,
			),
		),
		'note' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Note cannot be longer than 255 characters.',
			),
		),
	);

/**
 * beforeSave callback
 *
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if(isset($this->data[$this->alias]['ip'])) {
			$ip = explode('-', $this->data[$this->alias]['ip']);

			$this->data[$this->alias]['ip_start'] = $ip[0];

			if(!empty($ip[1])) {
				$this->data[$this->alias]['ip_end'] = $ip[1];
			} else {
				$this->data[$this->alias]['ip_end'] = $ip[0];
			}

			unset($this->data[$this->alias]['ip']);
		}

		return true;
	}

	public function isLocked($clientIp) {
		$this->recursive = -1;
		$data = $this->find('first', array(
			'fields' => array('id'),
			'conditions' => array(
				"INET_ATON(?) BETWEEN INET_ATON(`{$this->alias}`.`ip_start`) AND INET_ATON(`{$this->alias}`.`ip_end`)" => $clientIp,
			),
		));

		return !empty($data);
	}
}
