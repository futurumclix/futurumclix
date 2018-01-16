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
 * UserMembership Model
 *
 */
class Membership extends AppModel {
/**
 * status values
 *
 * @const
 */
	const TOTAL_CASHOUTS_LIMIT_NONE = 0;
	const TOTAL_CASHOUTS_LIMIT_VALUE = 1;
	const TOTAL_CASHOUTS_LIMIT_PERCENTAGE_DEPOSITS = 2;
	const TOTAL_CASHOUTS_LIMIT_PERCENTAGE_RR_INCOME = 3;

/**
 * points conversion modes
 *
 * @const
 */
	const POINTS_CONVERSION_DISABLED = 0;
	const POINTS_CONVERSION_ACCOUNT = 1;
	const POINTS_CONVERSION_PURCHASE = 2;

/**
 * actAs
 *
 * @var array
 */
	public $actsAs = array(
		'Utility.Enumerable',
	);

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
				'rule' => array('between', 1, 50),
				'message' => 'Name should be between 1 and 50 characters',
				'allowEmpty' => false,
			),
		),
		'status' => array(
			'values' => array(
				'rule' => array('inList', array('Active', 'Disabled', 'Default')),
				'allowEmpty' => false,
			),
		),
		'direct_referrals_limit' => array(
			'rule' => array('range', -2, 1000000),
			'message' => 'Limit should be lower than 1000000 (or -1 for unlimited value)',
			'allowEmpty' => false,
		),
		'rented_referrals_limit' => array(
			'rule' => array('range', -2, 1000000),
			'message' => 'Limit should be lower than 1000000 (or -1 for unlimited value)',
			'allowEmpty' => false,
		),
		'1_month_price' => array(
			'money' => array(
				'rule' => 'checkMonetary',
				'message' => 'Price should be non-negative decimal value',
			),
			'biggerOrEqualZero' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price cannot be negative value',
			),
		),
		'2_months_price' => array(
			'money' => array(
				'rule' => 'checkMonetary',
				'message' => 'Price should be non-negative decimal value',
			),
			'biggerOrEqualZero' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price cannot be negative value',
			),
		),
		'3_months_price' => array(
			'money' => array(
				'rule' => 'checkMonetary',
				'message' => 'Price should be non-negative decimal value',
			),
			'biggerOrEqualZero' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price cannot be negative value',
			),
		),
		'4_months_price' => array(
			'money' => array(
				'rule' => 'checkMonetary',
				'message' => 'Price should be non-negative decimal value',
			),
			'biggerOrEqualZero' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price cannot be negative value',
			),
		),
		'5_months_price' => array(
			'money' => array(
				'rule' => 'checkMonetary',
				'message' => 'Price should be non-negative decimal value',
			),
			'biggerOrEqualZero' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price cannot be negative value',
			),
		),
		'6_months_price' => array(
			'money' => array(
				'rule' => 'checkMonetary',
				'message' => 'Price should be non-negative decimal value',
			),
			'biggerOrEqualZero' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price cannot be negative value',
			),
		),
		'7_months_price' => array(
			'money' => array(
				'rule' => 'checkMonetary',
				'message' => 'Price should be non-negative decimal value',
			),
			'biggerOrEqualZero' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price cannot be negative value',
			),
		),
		'8_months_price' => array(
			'money' => array(
				'rule' => 'checkMonetary',
				'message' => 'Price should be non-negative decimal value',
			),
			'biggerOrEqualZero' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price cannot be negative value',
			),
		),
		'9_months_price' => array(
			'money' => array(
				'rule' => 'checkMonetary',
				'message' => 'Price should be non-negative decimal value',
			),
			'biggerOrEqualZero' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price cannot be negative value',
			),
		),
		'10_months_price' => array(
			'money' => array(
				'rule' => 'checkMonetary',
				'message' => 'Price should be non-negative decimal value',
			),
			'biggerOrEqualZero' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price cannot be negative value',
			),
		),
		'11_months_price' => array(
			'money' => array(
				'rule' => 'checkMonetary',
				'message' => 'Price should be non-negative decimal value',
			),
			'biggerOrEqualZero' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price cannot be negative value',
			),
		),
		'12_months_price' => array(
			'money' => array(
				'rule' => 'checkMonetary',
				'message' => 'Price should be a decimal value',
			),
			'biggerOrEqualZero' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price cannot be negative value',
			),
		),
		'1_month_active' =>'boolean',
		'2_month_active' =>'boolean',
		'3_month_active' =>'boolean',
		'4_month_active' =>'boolean',
		'5_month_active' =>'boolean',
		'6_month_active' =>'boolean',
		'7_month_active' =>'boolean',
		'8_month_active' =>'boolean',
		'9_month_active' =>'boolean',
		'10_month_active' =>'boolean',
		'11_month_active' =>'boolean',
		'12_month_active' =>'boolean',
		'allow_more_cashouts' => 'boolean',
		'instant_cashouts' => 'boolean',
		'direct_referrals_delete_cost' => array(
			'rule' => 'checkMonetary',
			'message' => 'Direct referrals delete cost should be a decimal value',
			'allowEmpty' => false,
		),
		'rented_referral_expiry_fee' => array(
			'rule' => 'checkMonetary',
			'message' => 'Rented referrals expiry fee should be a decimal value',
			'allowEmpty' => false,
		),
		'minimum_cashout' => array(
			'length' => array(
				'rule' => array('between', 0, 255),
				'message' => 'Minimum payout cannot be longer than 255 characters',
			),
			'commaSeparatedList' => array(
				'rule' => 'checkMonetaryList',
				'message' => 'Minimum payout should be a comma separated list of decimal values'
			),
		),
		'cashout_wait_time' => array(
			'allowEmpty' => false,
		),
		'maximum_cashout_amount' => array(
			'rule' => 'checkMonetary',
			'message' => 'Maximum cashout amount should be a decimal value',
			'allowEmpty' => false,
		),
		'upgrade_commission' => array(
			'rule' => 'checkMonetary',
			'message' => 'Upgrade commission should be a decimal value',
			'allowEmpty' => false,
		),
		'fund_commission' => array(
			'bcrange' => array(
				'rule' => array('bcrange', '0', '100'),
				'message' => 'Fund commission should be a percentage value (0-100)',
				'allowEmpty' => false,
			),
		),
		'purchase_commission' => array(
			'bcrange' => array(
				'rule' => array('bcrange', '0', '100'),
				'message' => 'Purchase commission should be a percentage value (0-100)',
				'allowEmpty' => false,
			),
		),
		'time_between_renting' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Time between renting should be a non-negative numeric value',
				'allowEmpty' => false,
			),
			'nonNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Time between renting should be a non-negative numeric value',
			)
		),
		'available_referrals_packs' => array(
			'maxLength' => array(
				'rule' => array('between', 0, 255),
				'message' => 'Available referrals packs cannot be longer than 255 characters',
			),
			'commaSeparatedList' => array(
				'rule' => '/(^[0-9]+(,[0-9]+)*$)|(^$)/',
				'message' => 'Available referrals packs should be a comma separated list of numbers'
			),
		),
		'referral_recycle_cost' => array(
			'rule' => 'checkMonetary',
			'message' => 'Referral recycle cost should be a decimal value',
			'allowEmpty' => false,
		),
		'autorecycle_time' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Autorecycle time should be a non-negative numeric value',
				'allowEmpty' => false,
			),
			'nonNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Autorecycle time should be a non-negative numeric value',
			)
		),
		'cashout_waiting_time' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Autorecycle time should be a non-negative numeric value',
				'allowEmpty' => false,
			),
			'nonNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Autorecycle time should be a non-negative numeric value',
			)
		),
		'max_purchase_commission_referral' => array(
			'rule' => 'checkMonetary',
			'message' => 'Maximum purchase commission per referral should be a decimal value',
			'allowEmpty' => false,
		),
		'max_purchase_commission_transaction' => array(
			'rule' => 'checkMonetary',
			'message' => 'Maximum purchase commission per transaction should be a decimal value',
			'allowEmpty' => false,
		),
		'commission_delay' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Commission delay should be a non-negative numeric value',
				'allowEmpty' => false,
			),
			'nonNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Commission delay should be a non-negative numeric value',
			),
			'16bit' => array(
				'rule' => array('comparison', '<=', 65535),
				'message' => 'Commission delay cannot be longer than 65535 days',
			)
		),
		'results_per_page' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Results per page should be a non-negative numeric value',
				'allowEmpty' => false,
			),
			'nonNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Results per page  should be a non-negative numeric value',
			),
			'16bit' => array(
				'rule' => array('comparison', '<=', 65535),
				'message' => 'Results per page cannot be more than 65535',
			)
		),
		'autopay_trigger_days' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'AutoPay trigger days should be a non-negative numeric value',
				'allowEmpty' => false,
			),
			'nonNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'AutoPay trigger days should be a non-negative numeric value',
			),
			'16bit' => array(
				'rule' => array('comparison', '<=', 255),
				'message' => 'AutoPay trigger days cannot be longer than 255 days',
			)
		),
		'total_cashouts_limit_mode' => array(
			'list' => array(
				'rule' => array('inList', array(self::TOTAL_CASHOUTS_LIMIT_NONE, self::TOTAL_CASHOUTS_LIMIT_VALUE, self::TOTAL_CASHOUTS_LIMIT_PERCENTAGE_DEPOSITS, self::TOTAL_CASHOUTS_LIMIT_PERCENTAGE_RR_INCOME)),
				'message' => 'Please select total cashouts limit mode',
				'allowEmpty' => false,
			),
		),
		'total_cashouts_limit_value' => array(
			'rule' => 'checkMonetary',
			'message' => 'Total cashouts limit value amount should be a decimal value',
			'allowEmpty' => false,
		),
		'total_cashouts_limit_percentage' => array(
			'bcrange' => array(
				'rule' => array('bcrange', '0.000000', '9999.999999'),
				'message' => 'Total cashouts limit percentage should be a valid percentage value (%s-%s)',
				'allowEmpty' => false,
			),
		),
		'points_enabled' => 'boolean',
		'points_per_dref' => array(
			'rule' => 'checkPoints',
			'message' => 'Points assigned per direct referral should be a valid decimal value.',
		),
		'points_per_rref' => array(
			'rule' => 'checkPoints',
			'message' => 'Points assigned per rented referral should be a valid decimal value.',
		),
		'points_per_topic' => array(
			'rule' => 'checkPoints',
			'message' => 'Points assigned per forum topic should be a valid decimal value.',
		),
		'points_per_post' => array(
			'rule' => 'checkPoints',
			'message' => 'Points assigned per forum points should be a valid decimal value.',
		),
		'points_per_paid_offer' => array(
			'rule' => 'checkPoints',
			'message' => 'Points assigned per accepted Paid Offer should be a valid decimal value.',
		),
		'points_for_upgrade' => array(
			'rule' => 'checkPoints',
			'message' => 'Points assigned for upgrading to this membership should be a valid decimal value.',
		),
		'points_per_deposit' => array(
			'rule' => 'checkPoints',
			'message' => 'Points assigned for deposit should be a valid decimal value.',
		),
		'points_conversion' => array(
			'rule' => array('inList', array(self::POINTS_CONVERSION_DISABLED, self::POINTS_CONVERSION_ACCOUNT, self::POINTS_CONVERSION_PURCHASE)),
			'message' => 'Please select points conversion mode.',
		),
		'points_value' => array(
			'rule' => 'checkMonetary',
			'message' => 'Points conversion value shoud be a valid monetary value.',
		),
		'points_min_conversion' => array(
			'rule' => 'checkPoints',
			'message' => 'Please enter minimum points amount for conversion.',
		),
	);

/**
 * enum
 *
 * @var array
 */
	public $enum = array(
		'total_cashouts_limit_mode' => array(
			self::TOTAL_CASHOUTS_LIMIT_NONE => 'None',
			self::TOTAL_CASHOUTS_LIMIT_VALUE => 'Value',
			self::TOTAL_CASHOUTS_LIMIT_PERCENTAGE_DEPOSITS => 'Percentage of Deposits',
			self::TOTAL_CASHOUTS_LIMIT_PERCENTAGE_RR_INCOME => 'Percentage of Rented Referrals Income',
		),
		'points_conversion' => array(
			self::POINTS_CONVERSION_DISABLED => 'Not Allowed',
			self::POINTS_CONVERSION_ACCOUNT => 'Account Balance',
			self::POINTS_CONVERSION_PURCHASE => 'Purchase Balance',
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ClickValue' => array(
			'className' => 'ClickValue',
			'foreignKey' => 'membership_id',
			'dependent' => true,
		),
		'RentedReferralsPrice' => array(
			'className' => 'RentedReferralsPrice',
			'foreignKey' => 'membership_id',
			'dependent' => true,
		),
	);

/**
 * constuctor
 *
 * @return void
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		if(Module::installed('RevenueShare')) {
			$this->hasMany['RevenueShareLimit'] = array(
				'className' => 'RevenueShare.RevenueShareLimit',
				'foreignKey' => 'membership_id',
				'dependent' => true,
				'conditions' => null,
			);
		}

		if(Module::installed('Offerwalls')) {
			$this->hasOne['OfferwallsSettings'] = array(
				'className' => 'Offerwalls.OfferwallsMembership',
				'foreignKey' => 'membership_id',
				'dependent' => true,
				'conditions' => null,
			);
		}
	}

/**
 * beforeSave callback
 *
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if(isset($this->data[$this->alias]['commission_items'])) {
			if(!empty($this->data[$this->alias]['commission_items'])) {
				$this->data[$this->alias]['commission_items'] = implode(';', $this->data[$this->alias]['commission_items']);
			} else {
				$this->data[$this->alias]['commission_items'] = '';
			}
		}
		return true;
	}

/**
 * afterSave callback
 *
 * @return void
 */
	public function afterSave($created, $options = array()) {
		if($created) {
			$this->ClickValue->createDefault($this->data[$this->alias]['id']);
		}
	}

/**
 * afterFind callback
 *
 * @return array
 */
	public function afterFind($results, $primary = false) {
		for($i = 0, $e = count($results); $i < $e; ++$i) {
			if(isset($results[$i][$this->alias]['commission_items'])) {
				if(!empty($results[$i][$this->alias]['commission_items'])) {
					$results[$i][$this->alias]['commission_items'] = explode(';', $results[$i][$this->alias]['commission_items']);
				} else {
					$results[$i][$this->alias]['commission_items'] = array();
				}
			}
		}
		return $results;
	}

/**
 * activate method
 *
 * @return boolean
 */
	public function activate($id) {
		$this->recursive = -1;
		$this->id = $id;
		$this->set(array('id'=> $id, 'status' => 'Active'));

		if($this->save()) {
			return true;
		}

		return false;
	}

/**
 * disable method
 *
 * @return boolean
 */
	public function disable($id) { 
		$this->recursive = -1;
		$this->id = $id;
		$this->set(array('id'=> $id, 'status' => 'Disabled'));

		if($this->save()) {
			return true;
		}

		return false;
	}

/**
 * isDefault method
 *
 * @return boolean
 */
	public function isDefault($id) {
		$this->recursive = -1;
		$this->id = $id;
		$status = $this->field('status');

		if($status === false) {
			throw new NotFoundException('Invalid membership');
		}

		return $status == 'Default';
	}

/**
 * hasPeriod method
 *
 * @return boolean
 */
	public function hasPeriod($id) {
		$this->recursive = -1;
		$this->id = $id;
		$this->read(array(
			'1_month_active',
			'2_months_active',
			'3_months_active',
			'4_months_active',
			'5_months_active',
			'6_months_active',
			'7_months_active',
			'8_months_active',
			'9_months_active',
			'10_months_active',
			'11_months_active',
			'12_months_active'
		));
		foreach($this->data['Membership'] as $period) {
			if($period === true)
				return true;
		}
		return false;
	}

/**
 * getDefaultId method
 *
 * @return id integer
 */
	public function getDefaultId() {
		$conditions = array('status' => 'Default');

		$id = $this->find('first', array('fields' => 'id', 'conditions' => $conditions));

		if(empty($id)) {
			throw new InternalErrorException(__d('exception', 'Could not find default membership'));
		}

		return $id['Membership']['id'];
	}

/**
 * durationExists method
 *
 * @return boolean
 */
	public function durationExists($duration) {
		switch($duration) {
			case '1_month':
			case '2_months':
			case '3_months':
			case '4_months':
			case '5_months':
			case '6_months':
			case '7_months':
			case '8_months':
			case '9_months':
			case '10_months':
			case '11_months':
			case '12_months':
				return true;
		}
		return false;
	}

/**
 * getList method
 *
 * @return array
 */
	public function getList($withDefault = true) {
		$list = array();
		$this->recursive = -1;
		$options = array(
			'fields' => array('id', 'name'),
		);
		if(!$withDefault) {
			$options = array_merge($options, array('conditions' => array('Membership.status !=' => 'Default')));
		}
		$memberships = $this->find('all', $options);
		foreach($memberships as $membership) {
			$list[$membership['Membership']['id']] = $membership['Membership']['name'];
		}
		return $list;
	}
}
