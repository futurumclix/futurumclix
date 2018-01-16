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
 * UserStatistic Model
 *
 * @property User $User
 */
class UserStatistic extends AppModel {
/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'user_id';

/**
 * Primary key field
 *
 * @var string
 */
	public $virtualFields = array(
		'total_rrefs_not_credited_clicks' => 'CAST(UserStatistic.total_rrefs_clicks AS SIGNED) - CAST(UserStatistic.total_rrefs_credited_clicks as SIGNED)',
		'total_drefs_not_credited_clicks' => 'CAST(UserStatistic.total_drefs_clicks AS SIGNED) - CAST(UserStatistic.total_drefs_credited_clicks as SIGNED)',
		'total_external_deposits' => 'UserStatistic.total_deposits - UserStatistic.purchase_balance_deposits',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'total_clicks' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'total_rrefs_clicks' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
			'biggerOrEqualCredited' => array(
				'rule' => array('comparisonWithField', '>=', 'total_rrefs_credited_clicks'),
				'message' => 'Total clicks number cannot be less than credited clicks number',
			),
		),
		'total_rrefs_credited_clicks' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
			'lessThanTotal' => array(
				'rule' => array('comparisonWithField', '<=', 'total_rrefs_clicks'),
				'message' => 'Total clicks number cannot be less than credited clicks number',
			),
		),
		'total_drefs_clicks' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
			'biggerOrEqualCredited' => array(
				'rule' => array('comparisonWithField', '>=', 'total_drefs_credited_clicks'),
				'message' => 'Total clicks number cannot be less than credited clicks number',
			),
		),
		'total_drefs_credited_clicks' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
			'lessThanTotal' => array(
				'rule' => array('comparisonWithField', '<=', 'total_drefs_clicks'),
				'message' => 'Total clicks number cannot be less than credited clicks number',
			),
		),
		'user_clicks_0' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'user_clicks_1' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'user_clicks_2' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'user_clicks_3' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'user_clicks_4' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'user_clicks_5' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'required' => false,
			),
		),
		'user_clicks_6' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'dref_clicks_0' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'dref_clicks_1' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'dref_clicks_2' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'dref_clicks_3' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'dref_clicks_4' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'dref_clicks_5' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'dref_clicks_6' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'rref_clicks_0' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'rref_clicks_1' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'rref_clicks_2' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'rref_clicks_3' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'rref_clicks_4' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'rref_clicks_5' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'rref_clicks_6' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => false,
			),
		),
		'total_deposits' => array(
			'decimal' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Deposit amount should be a decimal value',
			),
			'nonNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Deposit amount cannot be a negative number',
			),
		),
		'total_cashouts' => array(
			'decimal' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Deposit amount should be a decimal value',
			),
			'nonNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Deposit amount cannot be a negative number',
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
		)
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
			$fieldName = PaymentsInflector::underscore($gateway).'_deposits';
			$this->validate[$fieldName] = $this->validate['total_deposits'];
			$fieldName = PaymentsInflector::underscore($gateway).'_cashouts';
			$this->validate[$fieldName] = $this->validate['total_cashouts'];
		}

		if($this->columnExists('manual_pay_pal_deposits')) {
			$this->virtualFields['pay_pal_deposits'] = $this->alias.'.manual_pay_pal_deposits + '.$this->alias.'.pay_pal_deposits';
		}

		if($this->columnExists('bitcoin_deposits')) {
			if(ClassRegistry::init('Settings')->fetchOne('bitcoinDepositsSumHack', 0)) {
				$columns = $this->alias.'.bitcoin_deposits';

				if($this->columnExists('blockchain_deposits')) {
					$columns .= ' + '.$this->alias.'.blockchain_deposits';
				}

				if($this->columnExists('coinpayments_deposits')) {
					$columns .= ' + '.$this->alias.'.coinpayments_deposits';
				}

				$this->virtualFields['bitcoin_deposits'] = $columns;
			}
		}

		unset($list);
	}

/**
 * createDefault method
 *
 * @return boolean
 */
	public function createDefault($user_id) {
		$data = array(
			'user_id' => $user_id,
		);
		$this->create($data);
		return $this->save();
	}

/**
 * addDepositsColumn
 *
 * @return boolean
 */
	public function addDepositsColumn($gatewayName) {
		return parent::addNewColumn(PaymentsInflector::underscore($gatewayName).'_deposits', 'decimal', false, '17,8');
	}

/**
 * newDeposit
 *
 * @return boolean
 */
	public function newDeposit($user_id, $gatewayName, $amount) {
		$fieldName = PaymentsInflector::underscore($gatewayName).'_deposits';

		$this->recursive = -1;
		return $this->updateAll(array(
			'UserStatistic.'.$fieldName => '`UserStatistic`.`'.$fieldName.'` + '.$amount,
			'UserStatistic.total_deposits' => '`UserStatistic`.`total_deposits` + '.$amount,
		), array(
			'UserStatistic.user_id' => $user_id,
		));
	}

/**
 * addCashoutsColumn
 *
 * @return boolean
 */
	public function addCashoutsColumn($gatewayName) {
		return parent::addNewColumn(PaymentsInflector::underscore($gatewayName).'_cashouts', 'decimal', false, '17,8');
	}

/**
 * newCashout
 *
 * @return boolean
 */
	public function newCashout($user_id, $gatewayName, $amount) {
		$fieldName = PaymentsInflector::underscore($gatewayName).'_cashouts';

		$this->recursive = -1;
		return $this->updateAll(array(
			'UserStatistic.'.$fieldName => '`UserStatistic`.`'.$fieldName.'` + '.$amount,
			'UserStatistic.total_cashouts' => '`UserStatistic`.`total_cashouts` + '.$amount,
		), array(
			'UserStatistic.user_id' => $user_id,
		));
	}

/**
 * cancelCashout
 *
 * @return boolean
 */
	public function cancelCashout($user_id, $gatewayName, $amount) {
		$fieldName = PaymentsInflector::underscore($gatewayName).'_cashouts';

		$this->recursive = -1;
		return $this->updateAll(array(
			'UserStatistic.'.$fieldName => '`UserStatistic`.`'.$fieldName.'` - '.$amount,
			'UserStatistic.total_cashouts' => '`UserStatistic`.`total_cashouts` - '.$amount,
		), array(
			'UserStatistic.user_id' => $user_id,
		));
	}

/**
 * newTransfer
 *
 * @return boolean
 */
	public function newTransfer($user_id, $amount) {
		$this->recursive = -1;
		return $this->updateAll(array(
			$this->alias.'.purchase_balance_cashouts' => '`'.$this->alias.'`.`purchase_balance_cashouts` + '.$amount,
		), array(
			$this->alias.'.user_id' => $user_id,
		));
	}

/**
 * clearClicksAsDRef()
 *
 * @return boolean
 */
	public function clearClicksAsDRef($user_id) {
		$data = array(
			'user_id' => $user_id,
			'clicks_as_dref' => 0,
		);
		return $this->save($data, true, array('clicks_as_dref'));
	}

/**
 * clearClicksAsRRef()
 *
 * @return boolean
 */
	public function clearClicksAsRRef($user_id) {
		$data = array(
			'user_id' => $user_id,
			'clicks_as_rref' => 0,
		);
		return $this->save($data, true, array('clicks_as_rref'));
	}

/**
 * getSiteCashouts()
 *
 * @return string
 */
	public function getSiteCashouts() {
		return $this->find('first', array(
			'fields' => array(
				"SUM({$this->alias}.total_cashouts) as sum",
			),
		))[0]['sum'];
	}

/**
 * getUserEarnings()
 *
 * @return string
 */
	public function getUserEarnings($data = null, $user_id = null, $userAlias = 'User') {
		if($data === null) {
			$data = $this->data;
		}

		if($user_id === null) {
			$user_id = $data[$userAlias]['id'];

			if(!$user_id) {
				throw new InternalErrorException(__d('exception', 'Wrong user id'));
			}
		}

		$keys = array(
			'total_clicks_earned',
			'total_drefs_clicks_earned',
			'total_rrefs_clicks_earned',
			// TODO: add autopay and autorenew
		);

		if(count(array_intersect_key($data[$this->alias], array_flip($keys))) != count($keys)) {
			$this->find('first', array(
				'fields' => $keys,
				'condtions' => array($this->alias.'.user_id' => $user_id),
			));
			$data = $this->data;
		}

		$res = $this->User->getCreditedCommissionsAmount($user_id);

		foreach($keys as $k) {
			$res = bcadd($res, $data[$this->alias][$k]);
		}

		if(Module::active('AdGrid')) {
			$res = bcadd($res, ClassRegistry::init('AdGrid.AdGridWinHistory')->sumForUser($user_id));
		}

		return $res;
	}

/**
 * getROI
 *
 * @return string (decimal,percentage)
 */
	public function getROI($user_id, $statsData = array()) {
		if(empty($statsData) || !isset($statsData[$this->alias])) {
			$this->recursive = -1;
			$statsData = $this->findByUserId($user_id, array(
				'total_rrefs_clicks_earned',
				'total_external_deposits',
				'total_cashouts',
			));
		}

		if(bccomp($statsData[$this->alias]['total_rrefs_clicks_earned'], '0') <= 0 && bccomp($statsData[$this->alias]['total_external_deposits'], '0') <= 0) {
			return 0;
		}

		if(bccomp($statsData[$this->alias]['total_external_deposits'], '0') <= 0) {
			if(bccomp($statsData[$this->alias]['total_cashouts'], '0') <= 0) {
				return 0;
			}
			return bcdiv(bcmul($statsData[$this->alias]['total_cashouts'], 100), $statsData[$this->alias]['total_rrefs_clicks_earned']);
		}

		if(bccomp($statsData[$this->alias]['total_rrefs_clicks_earned'], '0') <= 0) {
			if(bccomp($statsData[$this->alias]['total_cashouts'], '0') <= 0) {
				return 0;
			}
			return bcdiv(bcmul($statsData[$this->alias]['total_cashouts'], 100), $statsData[$this->alias]['total_external_deposits']);
		}

		return bcdiv(bcmul($statsData[$this->alias]['total_rrefs_clicks_earned'], 100), $statsData[$this->alias]['total_external_deposits']);
	}
}
