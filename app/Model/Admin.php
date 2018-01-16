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
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
/**
 * Admin Model
 *
 */
class Admin extends AppModel {
	const SECRET_NONE = 0;
	const SECRET_GA = 1;

	public $actsAs = array(
		'Utility.Enumerable',
	);

	public $enum = array(
		'secret' => array(
			self::SECRET_NONE => 'None',
			self::SECRET_GA => 'Google Authenticator',
		),
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please enter valid e-mail address',
				'allowEmpty' => false,
				'required' => 'create',
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'This e-mail adress is in use',
			)
		),
		'password' => array(
			'minLength' => array(
				'rule' => array('minLength', 7),
				'message' => 'Password should be at least 7 characters long',
				'allowEmpty' => false,
				'required' => 'create',
			)
		),
		'allowed_ips' => array(
			'commaSeparatedIPs' => array(
				'rule' => '/^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}(,\n|,?))*$/',
				'message' => 'Please enter a comma separated list of valid IP addresses',
				'allowEmpty' => true,
			),
		),
		'secret' => array(
			'list' => array(
				'rule' => array('inList', array(self::SECRET_NONE, self::SECRET_GA)),
				'message' => 'Please select secret mode',
			),
		),
	);

/**
 * beforeSave callback
 *
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if(isset($this->data[$this->alias]['password'])) {
			$passwordHasher = new BlowfishPasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash($this->data[$this->alias]['password']);
		}
		return true;
	}

/**
 * afterSave callback
 *
 * @return void
 */
	public function afterSave($created, $options = array()) {
		if(!$created) {
			App::uses('CakeSession', 'Model/Datasource');
			if(CakeSession::read('Auth.Admin') != null) {
				if(CakeSession::read('Auth.Admin.id') === $this->data['Admin']['id']) {
					$this->recursive = -1;
					CakeSession::write('Auth.Admin', $this->read()['Admin']);
				}
			}
		}
	}

/**
 * beforeDelete callback
 *
 * @return boolean
 */
	public function beforeDelete($cascade = true) {
		if($this->id == 1 || $this->data[$this->alias]['id'] == 1) {
			return false;
		}
		return true;
	}

/**
 * afterLogin method
 *
 * @return boolean
 */
	public function afterLogin($id, $date = null) {
		$date = $date === null ? date('Y-m-d H:i:s') : $date;
		$this->id = $id;
		$this->set(['id'=> $id, 'last_log_in' => $date, 'modified' => false]);

		if($this->save()) {
			return true;
		}

		return false;
	}

/**
 * createVerifyToken method
 *
 * @return string
 */
	public function createVerifyToken() {
		return $this->createRandomStr(20);
	}
}
