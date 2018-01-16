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
App::uses('PaymentStatus', 'Payments');
/**
 * Deposit Model
 *
 * @property User $User
 */
class Deposit extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'gateway' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 50),
				'message' => 'Gateway name cannot be longer than %d characters',
				'allowEmpty' => false,
			),
		),
		'account' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 50),
				'message' => 'Account cannot be longer than 50 characters',
				'allowEmpty' => false,
			),
		),
		'status' => array(
			'inList' => array(
				'rule' => array('inList'), /* possible values are filled in __construct */
				'message' => 'Deposit status is wrong',
			),
		),
		'item' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 100),
				'message' => 'Item id cannot be longer than 100 characters',
				'allowEmpty' => false,
			),
		),
		'gatewayid' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Gateway identification cannot be longer than 255 characters',
				'allowEmpty' => false,
			),
		),
		'date' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'message' => 'Datetime should be valid datetime',
				'allowEmpty' => false,
			),
		),
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
			'fields' => array('username', 'id', 'upline_id'),
		)
	);

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'Commission' => array(
			'className' => 'Commission',
			'foreign_key' => 'deposit_id',
			'dependent' => true,
		),
		'DepositBonus' => array(
			'className' => 'DepositBonus',
			'foreign_key' => 'deposit_id',
			'dependent' => true,
		),
	);

/**
 * __construct
 *
 * 
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$refl = new ReflectionClass('PaymentStatus');
		$consts = $refl->getConstants();

		foreach($consts as $status) {
			$add[] = $status;
		}

		$this->validate['status']['inList']['rule'][] = $add;

		unset($consts);
		unset($refl);
	}

/**
 * debug method
 *
 * @return void
 */
	private function debug($message = '') {
		$now = date('Y-m-d H:i:s');
		$fp = fopen(TMP.'logs'.DS.'deposits_errors.log', 'a');

		if($fp == false) {
			return;
		}

		fwrite($fp, "\n** [$now] deposit error **\n");
		if(!empty($message)) {
			if(is_array($message)) {
				$message = print_r($message, true);
			}
			fwrite($fp, "Error message: $message\n");
		}
		fwrite($fp, print_r($this->data, true)."\n");
		fwrite($fp, "****************************************\n");

		fclose($fp);
	}

/**
 * afterSave callback
 *
 * @return void
 */
	public function afterSave($created, $options = array()) {
		$this->read(array('id', 'status', 'item', 'amount', 'user_id', 'gateway'));
		$method = 'on'.$this->data[$this->alias]['status'];

		if(method_exists($this, $method)) {
			$this->{$method}();
		}
	}

/**
 * onSuccess method
 *
 * @return void
 */
	public function onSuccess() {
		if($this->data[$this->alias]['gateway'] == 'AccountBalance') {
			return;
		}

		$item_data = $this->explodeItemId($this->data[$this->alias]['item']);

		if(!$this->assignItem($item_data, $this->data[$this->alias]['amount'])) {
			$this->debug('Failed to assign item');
		}

		if($this->data[$this->alias]['user_id']) {
			$exists = $this->Commission->hasAny(array(
				'Commission.deposit_id' => $this->data[$this->alias]['id'],
			));

			if(!$exists) {
				if(!$this->addCommission($item_data, $this->data[$this->alias]['user_id'])) {
					$this->debug('Failed to add commission');
				}
			}

			if($item_data[0] == 'deposit') {
				if(!$this->User->getDepositBonus($this->data[$this->alias])) {
					$this->debug('Failed to add deposit bonus');
				}
			}

			if(!$this->User->UserStatistic->newDeposit($this->data[$this->alias]['user_id'], $this->data[$this->alias]['gateway'], $item_data[1])) {
				$this->debug('Failed to update user statistic');
			}
		}

		if(!ClassRegistry::init('Settings')->newDeposit($this->data[$this->alias]['amount'])) {
			$this->debug('Failed to update global statistic');
		}
	}

/**
 * assignItem method
 *
 * @return void
 */
	private function assignItem($data, $amount) {
		$result = false;

		switch($data[0]) {
			case 'membership':
				$result = ClassRegistry::init('MembershipsUser')->assign(
					$data[1],
					$data[2],
					$data[3],
					$data[4]
				);
			break;

			case 'deposit':
				$result = $this->User->purchaseBalanceDeposit(
					$data[1],
					$data[2]
				);
			break;

			case 'rent':
				$result = $this->User->rentReferrals(
					$data[1],
					$data[2],
					$data[3],
					ClassRegistry::init('Settings')->fetch(array(
						'enableRentingReferrals',
						'rentMinClickDays',
						'rentFilter',
						'rentPeriod',
						'rentingOption',
					))
				);
			break;

			case 'referrals':
				$result = $this->User->buyReferrals(
					$data[1],
					$data[2],
					$data[3],
					ClassRegistry::init('Settings')->fetch(array(
						'enableBuyingReferrals',
						'directFilter',
						'directMinClickDays',
					))
				);
			break;

			case 'PTCPackage':
				$result = ClassRegistry::init('AdsCategoryPackage')->assign(
					$data[1],
					$data[2],
					$data[3]
				);
			break;

			case 'FeaturedAdsPackage':
			case 'BannerAdsPackage':
			case 'LoginAdsPackage':
			case 'PaidOffersPackage':
			case 'ExpressAdsPackage':
			case 'ExplorerAdsPackage':
				$result = ClassRegistry::init($data[0])->assign(
					$data[1],
					$data[2],
					$data[3]
				);
			break;

			case 'AdGridAdsPackage':
				$result = ClassRegistry::init('AdGrid.AdGridAdsPackage')->assign(
					$data[1],
					$data[2],
					$data[3]
				);
			break;

			case 'RevenueShareOption':
				$result = ClassRegistry::init('RevenueShare.RevenueShareOption')->assign(
					$data[1],
					$data[2],
					$data[3],
					$data[4]
				);
			break;

			case 'extend':
			case 'recycle':
				/* little bit tricky. we don't use normal payment mechanism, those are possible only with purchase balance */
				$result = true;
			break;

			case 'Ad':
				$model = ClassRegistry::init($data[3]);
				$result = $model->buy($data[4], $data[5]);
				$model->notifyBuy($data[4]);
			break;
		}

		return $result;
	}

/**
 * addCommission method
 *
 * @return boolean
 */
	private function addCommission($data, $ref_id) {
		$commissionTo = ClassRegistry::init('Settings')->fetchOne('commissionTo', null);

		if($commissionTo === null) {
			throw new InternalErrorException(__d('exception', 'Missing settings in "commissionTo" key'));
		}

		$this->User->contain();
		$this->User->id = $ref_id;
		$this->User->read(array('upline_id'));
		$upline_id = $this->User->data[$this->User->alias]['upline_id'];

		$this->User->contain(array(
			'ActiveMembership.Membership' => array(
				'upgrade_commission',
				'purchase_commission',
				'fund_commission',
			),
		));
		$upline = $this->User->findById($upline_id, array('id'));

		if(empty($upline)) {
			return true;
		}

		switch($data[0]) {
			case 'membership':
				$amount = $upline['ActiveMembership']['Membership']['upgrade_commission'];
			break;

			case 'deposit':
				$amount = bcmul(bcdiv($upline['ActiveMembership']['Membership']['fund_commission'], '100'), $data[1]);
			break;

			default:
				$amount = bcmul(bcdiv($upline['ActiveMembership']['Membership']['purchase_commission'], '100'), $data[1]);
		}

		if(bccomp($amount, '0') == 1) {
			return $this->Commission->addNew($ref_id, $amount, $commissionTo, $this->data[$this->alias]['id'], $data[0]);
		}

		return false;
	}

/**
 * bindTitle
 *
 * @return void
 */
	public function bindTitle() {
		App::uses('PaymentsComponent', 'Controller'.DS.'Component');
		$this->virtualFields['name'] = "SUBSTRING_INDEX({$this->alias}.item, '-', 1)";

		$items = &PaymentsComponent::$items;

		$this->bindModel(array(
			'belongsTo' => array(
				'Membership' => array(
					'fields' => 'name',
					'className' => 'Membership',
					'foreignKey' => '',
					'conditions' => array(
						'SUBSTRING_INDEX(SUBSTRING_INDEX('.$this->alias.'.item, \'-\', 4), \'-\', -1) = Membership.id',
						'name' => 'membership'
					),
				),
			),
		));

		$virtualstr = "CASE SUBSTRING_INDEX({$this->alias}.item, '-', 1)";

		foreach($items as $k => $v) {
			$v = __($v['title']);

			switch($k) {
				case 'membership':
					$v = "REPLACE('$v', ':membership_name:', IF(Membership.name IS NULL, '".__('DELETED')."', Membership.name))";
					$v = "REPLACE($v, ':duration:', SUBSTRING_INDEX(SUBSTRING_INDEX({$this->alias}.item, '-', 5), '-', -1))";
				break;

				case 'extend':
				case 'rent':
				case 'recycle':
				case 'referrals':
					$v = "REPLACE('$v', ':refs_no:', SUBSTRING_INDEX(SUBSTRING_INDEX({$this->alias}.item, '-', 4), '-', -1))";
				break;

				default:
					$v = "'$v'";
			}

			$virtualstr .= " WHEN '$k' THEN $v";
		}

		$virtualstr .= " END";
		$this->virtualFields['title'] = $virtualstr;
	}

/**
 * bindDepositAmount method
 *
 * @return void
 */
	public function bindDepositAmount() {
		$this->virtualFields['deposit_amount'] = "SUBSTRING_INDEX(SUBSTRING_INDEX(Deposit.item, '-', 2), '-', -1)";
	}

/**
 * unbindTitle
 *
 * @return void
 */
	public function unbindTitle() {
		$this->unbindModel(array(
			'belongsTo' => array(
				'Membership',
			)
		));
		$this->unbindVirtualFields();
	}

/**
 * unbindVirtualFields
 *
 * @return void
 */
	public function unbindVirtualFields() {
		unset($this->virtualFields);
	}

/**
 * purgePendingByHours
 *
 * @return boolean
 */
	public function purgePendingByHours($hours) {
		if(!is_numeric($hours)) {
			throw new InternalErrorException(__d('exception', 'Invalid arg'));
		}
		$date = date('Y-m-d H:i:s', strtotime("-$hours hour"));

		$this->recursive = -1;
		return $this->updateAll(array(
			$this->alias.'.status' => "'Failed'",
		), array(
			$this->alias.'.status' => 'Pending',
			$this->alias.'.date <=' => $date, 
		));
	}

/**
 * explodeItemId method
 *
 * @return array
 */
	public function explodeItemId($id) {
		$data = explode('-', $id);

		foreach($data as &$v) {
			$v = str_replace('._.', '-', $v);
		}

		return $data;
	}
}
