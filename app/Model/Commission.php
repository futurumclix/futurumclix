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
 * Commission Model
 *
 * @property Deposit $Deposit
 */
class Commission extends AppModel {
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'amount' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
			),
		),
		'credit_date' => array(
			'datetime' => array(
				'rule' => array('datetime'),
			),
		),
		'status' => array(
			'inList' => array(
				'rule' => array('inList', array('Pending', 'Credited', 'Failed', 'Cancelled')),
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
			'fields' => array('id', 'gateway', 'amount', 'item', 'date', 'name'),
		),
		'Upline' => array(
			'className' => 'User',
			'foreignKey' => 'upline_id',
			'fields' => array('username'),
		),
		'Referral' => array(
			'className' => 'User',
			'foreignKey' => 'referral_id',
			'fields' => array('username'),
		),
	);

/**
 * bindTitle method
 *
 * @return void
 */
	public function bindTitle() {
		$this->Deposit->bindTitle();
		$this->bindModel(array(
			'belongsTo' => array(
				'Membership' => array(
					'fields' => 'name',
					'className' => 'Membership',
					'foreignKey' => '',
					'conditions' => array(
						'SUBSTRING_INDEX(SUBSTRING_INDEX(Deposit.item, \'-\', 4), \'-\', -1) = Membership.id',
						'SUBSTRING_INDEX(Deposit.item, \'-\', 1)' => 'membership'
					),
				),
			),
		));
		$this->belongsTo['Deposit']['fields'][] = 'title';
	}

	public function addNew($ref_id, $amount, $creditTo, $deposit_id = null, $item_name = null) {
		$this->Referral->id = $ref_id;
		$this->Referral->contain();
		$this->Referral->read(array(
			'id',
			'upline_id',
			'upline_commission',
		));

		if(empty($this->Referral->data)) {
			throw new NotFoundException(__d('exception', 'Invalid referral'));
		}

		$this->Upline->id = $this->Referral->data['Referral']['upline_id'];
		$this->Upline->contain(array(
			'ActiveMembership.Membership' => array(
				'upgrade_commission',
				'fund_commission',
				'purchase_commission',
				'commission_delay',
				'commission_items',
				'max_purchase_commission_referral',
				'max_purchase_commission_transaction',
			)
		));
		$this->Upline->read(array('id'));

		if($item_name !== null) {
			if(strpos(implode($this->Upline->data['ActiveMembership']['Membership']['commission_items']), $item_name) === false) {
				return true; /* item not added to commissions list in upline membership, ignore */
			}
		}

		if($item_name != 'membership') {
			if(bccomp($this->Upline->data['ActiveMembership']['Membership']['max_purchase_commission_transaction'], '0') == 1) { /* 0 -> disabled */
				if(bccomp($amount, $this->Upline->data['ActiveMembership']['Membership']['max_purchase_commission_transaction']) == 1) {
					$amount = $this->Upline->data['ActiveMembership']['Membership']['max_purchase_commission_transaction'];
				}
			}

			if(bccomp($this->Upline->data['ActiveMembership']['Membership']['max_purchase_commission_referral'], '0') == 1) { /* 0 -> disabled */
				if(bccomp(bcadd($amount, $this->Referral->data['Referral']['upline_commission']), $this->Upline->data['ActiveMembership']['Membership']['max_purchase_commission_referral']) == 1) {
					$amount = bcsub($this->Upline->data['ActiveMembership']['Membership']['max_purchase_commission_referral'], $this->Referral->data['Referral']['upline_commission']);
				}
			}
		}

		if(bccomp('0', $amount) >= 1) {
			return true; /* amount excedes limits, ignore */
		}

		$this->create();

		$delay = $this->Upline->data['ActiveMembership']['Membership']['commission_delay'];
		$commissionData = array(
			'deposit_id' => $deposit_id,
			'upline_id' => $this->Upline->id,
			'referral_id' => $this->Referral->id,
			'amount' => $amount,
			'status' => 'Pending',
			'credit_date' => date('Y-m-d H:i:s', strtotime("+$delay days"))
		);

		$this->Referral->recursive = -1;
		$this->Referral->updateAll(array('Referral.upline_commission' => "Referral.upline_commission + $amount"), array('Referral.id' => $this->Referral->id));

		if($delay == 0) {
			$this->Upline->recursive = -1;
			$this->Upline->updateAll(array("Upline.$creditTo" => "Upline.$creditTo + $amount"), array('Upline.id' => $this->Upline->id));
			$commissionData['status'] = 'Credited';
		}

		return $this->save($commissionData);
	}

/**
 * credit method
 *
 * @return boolean
 */
	public function credit($id, $creditTo) {
		$this->recursive = -1;
		$commission = $this->findById($id);

		if($commission[$this->alias]['status'] == 'Credited') {
			throw new InternalErrorException(__d('exception', 'Commission already credited'));
		}

		$amount = $commission[$this->alias]['amount'];

		$commission[$this->alias]['credit_date'] = date('Y-m-d H:i:s');
		$commission[$this->alias]['status'] = 'Credited';

		if(!$this->save($commission[$this->alias])) {
			return false;
		}

		$this->Referral->recursive = -1;
		if(!$this->Referral->updateAll(array('Referral.upline_commission' => "Referral.upline_commission + $amount"), array('Referral.id' => $commission['Commission']['referral_id']))) {
			return false;
		}
		$this->Upline->recursive = -1;
		return $this->Upline->updateAll(array("Upline.$creditTo" => "Upline.$creditTo + $amount"), array('Upline.id' => $commission['Commission']['upline_id']));
	}

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

/**
 * creditAll method
 *
 * @return boolean
 */
	public function creditAll($creditTo) {
		$this->recursive = -1;

		$toCredit = $this->find('all', array(
			'fields' => array('id'),
			'conditions' => array(
				'credit_date <= NOW()',
				'status' => 'Pending',
			),
		));

		foreach($toCredit as $commission) {
			if(!$this->credit($commission[$this->alias]['id'], $creditTo)) {
				return false;
			}
		}
		return true;
	}

/**
 * cancel method private
 *
 * @throws InternalErrorException
 * @param string $id
 * @return boolean
 */
	public function cancel($account_type, $id = null) {
		if($id !== null) {
			$this->id = $id;
		}
		$this->recursive = -1;
		$this->read(array('upline_id', 'amount', 'status'));

		if($this->data[$this->alias]['status'] != 'Credited') {
			return true;
		}

		switch($account_type) {
			case 'account_balance':
				if(!$this->Upline->accountBalanceSub($this->data[$this->alias]['amount'], $this->data[$this->alias]['upline_id'])) {
					return false;
				}
			break;

			case 'purchase_balance':
				if(!$this->Upline->purchaseBalanceSub($this->data[$this->alias]['amount'], $this->data[$this->alias]['upline_id'])) {
					return false;
				}
			break;
		}
		return true;
	}

}
