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
App::uses('Security', 'Utility');
App::uses('PaymentsInflector', 'Payments');

/**
 * PaymentGateway Model
 *
 */
class PaymentGateway extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'name';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'between' => array(
				'rule' => array('between', 0, 129),
				'message' => 'Name should be between 0 and 129 characters long',
				'allowEmpty' => false,
			),
		),
		'deposits' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'cashouts' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'minimum_deposit_amount' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Minimum deposit amount should be a decimal value',
				'allowEmpty' => false,
			),
		),
		'deposit_fee_percent' => array(
			'bcrange' => array(
				'rule' => array('bcrange', '0', '100'),
				'message' => 'Deposit fee (percent) should be a valid percentage value',
				'allowEmpty' => false,
			),
		),
		'deposit_fee' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Deposit fee should be a decimal value',
				'allowEmpty' => false,
			),
		),
		'cashout_fee_percent' => array(
			'bcrange' => array(
				'rule' => array('bcrange', '0', '100'),
				'message' => 'Cashout fee (percent) should be a valid percentage value',
				'allowEmpty' => false,
			),
		),
		'cashout_fee' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Cashout fee should be a decimal value',
				'allowEmpty' => false,
			),
		),
		'api_settings' => array(
			'gatewaySettings' => array(
				'rule' => array('validateApiSettings'),
				'message' => 'Wrong gateway settings',
			),
		),
	);

/**
 * beforeSave callback
 *
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if(isset($this->data[$this->alias]['api_settings'])) {
			$this->data[$this->alias]['api_settings'] = base64_encode(Security::encrypt(serialize($this->data[$this->alias]['api_settings']), Configure::read('Security.key')));
		}
		return true;
	}

/**
 * afterFind callback
 *
 * @return array
 */
	public function afterFind($results, $primary = false) {
		for($i = 0, $e = count($results); $i < $e; ++$i) {
			if(isset($results[$i][$this->alias]['api_settings'])) {
				$results[$i][$this->alias]['api_settings'] = unserialize(Security::decrypt(base64_decode($results[$i][$this->alias]['api_settings']), Configure::read('Security.key')));
			}
		}
		return $results;
	}

/**
 * validatePaymentGatewaySettings
 *
 * @param $check array
 * @return boolean
 */
	public function validateApiSettings(array $check) {
		$gatewayName = $this->data['PaymentGateway']['name'];
		$class = PaymentsInflector::classify($gatewayName).'Gateway';
		App::uses($class, 'Payments');

		if($class::needsSettings()) {
			return $class::validateSettings($check['api_settings']);
		}
		return false;
	}

	public function findActiveCashouts($type = 'list', $options = array()) {
		$defaults = array(
			'conditions' => array(
				'cashouts',
			),
		);

		$options = Hash::merge($defaults, $options);

		$result = $this->find($type, $options);

		if($type == 'list') {
			foreach($result as $r => &$v) {
				$v = PaymentsInflector::humanize(PaymentsInflector::underscore($v));
			}
		}
		return $result;
	}

	public function findActiveDeposits($type = 'list', $options = array()) {
		$defaults = array(
			'conditions' => array(
				'deposits',
			),
		);

		$options = Hash::merge($defaults, $options);

		$result = $this->find($type, $options);

		if($type == 'list') {
			foreach($result as $r => &$v) {
				$v = PaymentsInflector::humanize(PaymentsInflector::underscore($v));
			}
		}
		return $result;
	}

	public function findActive($type = 'list', $options = array()) {
		$defaults = array(
			'conditions' => array(
				'OR' => array(
					'cashouts',
					'deposits',
				),
			),
		);

		$options = Hash::merge($defaults, $options);

		$result = $this->find($type, $options);

		if($type == 'list') {
			foreach($result as $r => &$v) {
				$v = PaymentsInflector::humanize(PaymentsInflector::underscore($v));
			}
		}
		return $result;
	}

	public function arrayByName($data) {
		return Hash::combine($data, '{n}.PaymentGateway.name', '{n}.PaymentGateway');
	}

}
