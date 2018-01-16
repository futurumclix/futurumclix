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
 * DepositBonus Model
 *
 * @property Deposit $Deposit
 * @property User $User
 */
class DepositBonus extends AppModel {
/**
 * status values
 *
 * @const
 */
	const CREDITED = 1;
	const CANCELLED = 2;

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'amount' => array(
			'decimal' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Purchase balance should be a decimal value',
				'allowEmpty' => false,
			),
		),
		'status' => array(
			'inList' => array(
				'rule' => array('inList', array(self::CREDITED, self::CANCELLED)),
				'message' => 'Status can be only "Credited" or "Cancelled".',
				'allowEmpty' => false,
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Deposit' => array(
			'className' => 'Deposit',
			'foreignKey' => 'deposit_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		)
	);

/**
 * enum
 *
 * @var array
 */
	public $enum = array(
		'status' => array(
			self::CREDITED => 'Credited',
			self::CANCELLED => 'Cancelled',
		),
	);

/**
 * cancel
 *
 * @return boolean
 */
	public function cancel($id = null, $userId = null, $amount = null) {
		if($id === null) {
			$id = $this->id;
		}

		if($userId === null || $amount === null) {
			$data = $this->findById($id, array('user_id', 'amount'));

			if(empty($data)) {
				throw new InternalErrorException(__d('exception', 'DepositBonus does not exists'));
			}

			$userId = $data[$this->alias]['user_id'];
			$amount = $data[$this->alias]['amount'];
		}

		if(!$this->User->purchaseBalanceSub($amount, $userId)) {
			throw new InternalErrorException(__d('exception', 'Failed to subtract purchase balance'));
		}

		$this->id = $id;
		return $this->saveField('status', self::CANCELLED);
	}
}
