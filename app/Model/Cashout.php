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
 * Cashout Model
 *
 * @property User $User
 */
class Cashout extends AppModel {
/**
 * actAs
 *
 * @var array
 */
	public $actsAs = array(
		'Containable',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_id' => 'numeric',
		'amount' => array(
			'decimal' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Amount should be a decimal value',
			),
			'nonNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Amount cannot be a negative number',
			),
		),
		'fee' => array(
			'decimal' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Fee should be a decimal value',
			),
			'nonNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Fee cannot be a negative number',
			),
		),
		'payment_account' => array(
			'between' => array(
				'rule' => array('between', 1, 255),
				'allowEmpty' => false,
			),
		),
		'gateway' => array(
			'between' => array(
				'rule' => array('between', 1, 25),
				'allowEmpty' => false,
			),
		),
		'status' => array(
			'inList' => array(
				'rule' => array('inList', array('New', 'Pending', 'Completed', 'Failed', 'Cancelled')),
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'counterCache' => 'cashouts',
			'counterScope' => array(
				'status' => 'Completed',
			),
		)
	);

/**
 * getStatuses method
 *
 * @return array
 */
	public function getStatuses() {
		$res = array();
		foreach($this->validate['status']['inList']['rule'][1] as $v) {
			$res[$v] = __($v);
		}
		return $res;
	}
}
