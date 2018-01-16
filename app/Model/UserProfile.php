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
App::uses('GatewaysList', 'Payments');
App::uses('PaymentsInflector', 'Payments');

/**
 * UserProfile Model
 *
 * @property User $User
 */
class UserProfile extends AppModel {

	public $actsAs = array(
		'Containable',
	);

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'user_id';

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'UserMetadata' => array(
			'className' => 'UserMetadata',
			'foreignKey' => 'user_id',
		),
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'gender' => array(
			'inList' => array(
				'rule' => array('inList', array('', 'Male', 'Female')),
				'message' => 'Please select Male or Female',
			),
		),
		'birth_day' => array(
			'datetime' => array(
				'rule' => array('date'),
				'allowEmpty' => true,
			),
			'past' => array(
				'rule' => array('checkPastDate'),
				'message' => 'Birthday cannot be in the future',
				'allowEmpty' => true,
			),
		),
		'address' => array(
			'length' => array(
				'rule' => array('maxLength', 200),
				'message' => 'Address cannot be longer than 200 characters',
			),
		),
	);

/**
 * __construct
 *
 * 
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$list = new GatewaysList();
		$this->gateways = $list->getGatewaysList();

		foreach($this->gateways as $gateway) {
			$class = PaymentsInflector::classify($gateway).'Gateway';
			$fieldName = PaymentsInflector::underscore($gateway);
			App::uses($class, 'Payments');
			$validationRules = $class::getAccountValidationRules();

			$this->validate[$fieldName]['unique'] = array(
				'rule' => 'isUnique',
				'message' => __('Sorry, but this %s account id is already taken', $gateway),
				'allowEmpty' => true,
			);
			$this->validate[$fieldName]['maxLength'] = array(
				'rule' => array('maxLength', 255),
				'message' => __('%s account id cannot be longer than 255 characters', $gateway),
				'allowEmpty' => true,
			);

			if(is_array($validationRules)) {
				foreach($validationRules as &$v) {
					$v['allowEmpty'] = true;
				}
				$this->validate[$fieldName] = $validationRules;
			}
			$this->validate[$fieldName.'_modified'] = array();
		}

		unset($list);
	}

/**
 * createEmpty method
 *
 * @return void
 */
	public function createEmpty($userId) {
		$this->set(array('user_id' => $userId, 'modified' => false));

		if($this->save()) {
			return true;
		}

		return false;
	}

/**
 * beforeSave callback
 *
 * @return boolean
 */
	public function beforeSave($options = array()) {
		foreach($this->data[$this->alias] as &$v) {
			$v = trim($v);
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
			$userId = $this->data['UserProfile']['user_id'];

			$this->User->set(array('id' => $userId, 'remind_profile' => false));

			if(!$this->User->save()) {
				throw new InternalErrorException();
			}
		}
	}

/**
 * getGendersList method
 *
 * @return array
 */
	public function getGendersList() {
		return array(
			'Male' => __('Male'),
			'Female' => __('Female'),
		);
	}

/**
 * addAccountIdColumn
 *
 * @return boolean
 */
	public function addAccountIdColumn($gatewayName) {
		if(!parent::addNewColumn(PaymentsInflector::underscore($gatewayName), 'string', true, 255)) {
			return false;
		}
		return parent::addNewColumn(PaymentsInflector::underscore($gatewayName).'_modified', 'datetime', true);
	}
}
