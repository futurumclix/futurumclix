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
App::uses('Component', 'Controller');
App::uses('GatewaysList', 'Payments');
App::uses('PaymentIdMissingArgumentException', 'Payments');
App::uses('MissingPaymentGatewayException', 'Payments');
App::uses('Security', 'Utility');
App::uses('PaymentsInflector', 'Payments');

/**
 * PaymentsComponent
 *
 *
 */
class PaymentsComponent extends Component {
	private $Settings = null;
	private $PaymentGateway = null;
	private $depositDataCache = array();
	private $cashoutDataCache = array();
	private $refundsDataCache = array();
	private $minimumDepositAmount = array();
	private $ignoreMinDeposit = false;

	protected $available = null;
	protected $activeDeposits;
	protected $activeCashouts;
	protected $active;

	public static $items = array(
		'membership' => array(
			'id' => 'membership-:amount:-:user_id:-:membership_id:-:duration:', /* duration should be in months */
			'title' => 'Membership ":membership_name:" for :duration: months',
			'admin_title' => 'Membership buy',
			'commission' => true,
		),
		'deposit' => array(
			'id' => 'deposit-:amount:-:user_id:',
			'title' => 'Purchase balance fund',
			'admin_title' => 'Purchase balance fund',
			'commission' => true,
		),
		'transfer' => array(
			'id' => 'transfer',
			'title' => 'Transfer from account balance to purchase balance',
			'admin_title' => 'Transfer from account balance to purchase balance',
		),
		'rent' => array(
			'id' => 'rent-:amount:-:user_id:-:refs_no:',
			'title' => 'Rent :refs_no: referrals',
			'admin_title' => 'Referrals rent',
			'commission' => true,
		),
		'extend' => array(
			'id' => 'extend-:amount:-:user_id:-:refs_no:',
			'title' => 'Extend rent of :refs_no: referrals',
			'admin_title' => 'Rent extend',
			'commission' => true,
		),
		'recycle' => array(
			'id' => 'recycle-:amount:-:user_id:-:refs_no:',
			'title' => 'Recycle :refs_no: rented referrals',
			'admin_title' => 'Rented referrals recycle',
			'commission' => true,
		),
		'referrals' => array(
			'id' => 'referrals-:amount:-:user_id:-:refs_no:',
			'title' => ':refs_no: direct referrals',
			'admin_title' => 'Buy direct referrals',
			'commission' => true,
		),
		'PTCPackage' => array(
			'id' => 'PTCPackage-:amount:-:user_id:-:package_id:',
			'title' => 'PTC package',
			'admin_title' => 'Buy PTC package',
			'commission' => true,
		),
		'FeaturedAdsPackage' => array(
			'id' => 'FeaturedAdsPackage-:amount:-:user_id:-:package_id:',
			'title' => 'Featured Ad package',
			'admin_title' => 'Buy Featured Ad package',
			'commission' => true,
		),
		'BannerAdsPackage' => array(
			'id' => 'BannerAdsPackage-:amount:-:user_id:-:package_id:',
			'title' => 'Banner Ad package',
			'admin_title' => 'Buy Banner Ad package',
			'commission' => true,
		),
		'AdGridAdsPackage' => array(
			'id' => 'AdGridAdsPackage-:amount:-:user_id:-:package_id:',
			'title' => 'AdGrid Ad package',
			'admin_title' => 'Buy AdGrid Ad package',
			'commission' => true,
		),
		'LoginAdsPackage' => array(
			'id' => 'LoginAdsPackage-:amount:-:user_id:-:package_id:',
			'title' => 'Login Ad package',
			'admin_title' => 'Buy Login Ad package',
			'commission' => true,
		),
		'PaidOffersPackage' => array(
			'id' => 'PaidOffersPackage-:amount:-:user_id:-:package_id:',
			'title' => 'Paid Offers package',
			'admin_title' => 'Buy Paid Offers package',
			'commission' => true,
		),
		'RevenueShareOption' => array(
			'id' => 'RevenueShareOption-:amount:-:user_id:-:option_id:-:quantity:',
			'title' => 'Revenue Share package',
			'admin_title' => 'Buy Revenue Share package',
			'commission' => true,
		),
		'ExpressAdsPackage' => array(
			'id' => 'ExpressAdsPackage-:amount:-:user_id:-:package_id:',
			'title' => 'Express Ad package',
			'admin_title' => 'Buy Express Ad package',
			'commission' => true,
		),
		'ExplorerAdsPackage' => array(
			'id' => 'ExplorerAdsPackage-:amount:-:user_id:-:package_id:',
			'title' => 'Explorer Ad package',
			'admin_title' => 'Buy Explorer Ad package',
			'commission' => true,
		),
		'Ad' => array(
			'id' => 'Ad-:amount:-:user_id:-:model:-:ad_id:-:package_id:',
			'title' => 'Advertisement',
			'admin_title' => 'Advertisement',
		),
	);

	public function startup(Controller $controller) {
		parent::startup($controller);

		$this->controller = $controller;

		$this->PaymentGateway = ClassRegistry::init('PaymentGateway');
		$this->Settings = ClassRegistry::init('Settings');

		$list = new GatewaysList();
		$this->available = $list->getGatewaysList();

		$this->refreshData();
	}

	public function refreshData() {
		$this->activeDeposits = array();
		$this->activeCashouts = array();
		$this->active = array();
		$this->minimumDepositAmount = array();
		$this->ignoreMinDeposit = $this->Settings->fetchOne('ignoreMinDeposit');

		if(empty($this->ignoreMinDeposit)) {
			$this->ignoreMinDeposit = false;
		}

		$gateways = $this->PaymentGateway->find('all', array(
			'fields' => array('name', 'deposits', 'cashouts', 'minimum_deposit_amount'),
			'conditions' => array(
				'OR' => array(
					'cashouts',
					'deposits',
				),
			),
			'order' => 'name',
		));

		/* TODO: make sure that all gateways are supported at now (implementations wasn't removed) */
		foreach($gateways as $gateway) {
			if($gateway['PaymentGateway']['deposits']) {
				$this->activeDeposits[] = $gateway['PaymentGateway']['name'];
			}
			if($gateway['PaymentGateway']['cashouts']) {
				$this->activeCashouts[] = $gateway['PaymentGateway']['name'];
			}
			$this->minimumDepositAmount[$gateway['PaymentGateway']['name']] = $gateway['PaymentGateway']['minimum_deposit_amount'];
		}
		$this->active = array_unique(array_merge($this->activeDeposits, $this->activeCashouts));
		sort($this->active);
	}

	public function createGateway($gatewayName) {
		$class = PaymentsInflector::classify($gatewayName).'Gateway';
		App::uses($class, 'Payments');

		$settings = $this->PaymentGateway->findByName($gatewayName)['PaymentGateway'];
		$settings['currency_code'] = Configure::read('siteCurrency');

		if(empty($settings)) {
			throw new MissingPaymentGatewayException(__d('exception', 'Payment processor settings not found'));
		}

		$gateway = new $class($settings);
		return $gateway;
	}

	public function getDepositFee($amount, $gatewayName) {
		if(isset($this->depositDataCache[$gatewayName])) {
			$gateway = $this->depositDataCache[$gatewayName];
		} else {
			$gateway = $this->PaymentGateway->find('first', array(
				'fields' => array(
					'deposit_fee_percent',
					'deposit_fee_amount',
				),
				'conditions' => array(
					'name' => $gatewayName,
					'OR' => array(
						'deposits' => true,
						'name' => 'PurchaseBalance',
					),
				),
			));
			$this->depositDataCache[$gatewayName] = $gateway;
		}

		if(empty($gateway)) {
			throw new MissingPaymentGatewayException(__d('exception', 'Payment gateway not allowed'));
		}

		if(bccomp($amount, '0') != 1) {
			return '0';
		}

		if(bccomp($gateway['PaymentGateway']['deposit_fee_percent'], '0') != 0) {
			$fee = bcmul($amount, bcdiv($gateway['PaymentGateway']['deposit_fee_percent'], '100'));

			if(bccomp($gateway['PaymentGateway']['deposit_fee_amount'], '0') == 1) {
				return bcadd($fee, $gateway['PaymentGateway']['deposit_fee_amount']);
			}

			return $fee;
		}

		return $gateway['PaymentGateway']['deposit_fee_amount'];
	}

	public function getCashoutFee($amount, $gatewayName) {
		if(isset($this->cashoutDataCache[$gatewayName])) {
			$gateway = $this->cashoutDataCache[$gatewayName];
		} else {
			$gateway = $this->PaymentGateway->find('first', array(
				'fields' => array(
					'cashout_fee_percent',
					'cashout_fee_amount',
				),
				'conditions' => array(
					'name' => $gatewayName,
					'cashouts' => true,
				),
			));
			$this->cashoutDataCache[$gatewayName] = $gateway;
		}

		if(empty($gateway)) {
			throw new MissingPaymentGatewayException(__d('exception', 'Payment gateway not allowed'));
		}

		if(bccomp($amount, '0') != 1) {
			return '0';
		}

		if(bccomp($gateway['PaymentGateway']['cashout_fee_percent'], '0') != 0) {
			$fee = bcmul($amount, bcdiv($gateway['PaymentGateway']['cashout_fee_percent'], '100'));

			if(bccomp($gateway['PaymentGateway']['cashout_fee_amount'], '0') == 1) {
				return bcadd($fee, $gateway['PaymentGateway']['cashout_fee_amount']);
			}

			return $fee;
		}

		return $gateway['PaymentGateway']['cashout_fee_amount'];
	}

	private function makeShortId($id) {
		$shortItemId = ClassRegistry::init('ShortItemId');
		$shortItemId->create();
		$shortItemId->set('item_id', $id);

		if(!$shortItemId->save()) {
			throw new InternalErrorException(__d('exception', 'Failed to shorten item id'));
		}
		return $shortItemId->id;
	}

	public function pay($type, $gateway, $amount, $user_id, $info = array(), $purchase_balance_redirect = array()) {
		if($gateway == 'Coinpayment') { // TODO: is it really needed?
			$gateway = 'Coinpayments';
		}

		if(!isset($gateway) || empty($gateway)) {
			throw new MissingPaymentGatewayException(__d('exception', 'Payment processor not selected'));
		}
		if(!in_array($gateway, $this->available)) {
			throw new MissingPaymentGatewayException(__d('exception', '%s not found!', $gateway));
		}

		$pay_pb_exceptions = array(
			'extend',
			'recycle',
			'rent',
			'referrals',
		);

		if(!($gateway === 'PurchaseBalance' && in_array($type, $pay_pb_exceptions))) {
			$active = $this->PaymentGateway->findActive();

			if(!in_array($gateway, array_flip($active))) {
				throw new MissingPaymentGatewayException(__d('exception', '%s: not allowed for deposits', $gateway));
			}
		}

		if($user_id) {
			$User = ClassRegistry::init('User');
			$User->id = $info['user_id'] = $user_id;;

			if(!$User->exists()) {
				throw new NotFoundException(__d('exception', 'Invalid user'));
			}

			$User->contain(array('UserProfile'));
			$User->read();
		} else {
			$info['user_id'] = 'null';
		}

		$info['amount'] = $amount;
		$total = bcadd($amount, $this->getDepositFee($amount, $gateway));

		$id = $this->createId($type, $info);

		if($gateway != 'PurchaseBalance') {
			$id = $this->makeShortId($id);
		}

		$params = array(
			'amount' => $total,
			'id' => $id,
			'title' => $this->createTitle($type, $info),
		);

		if($user_id) {
			$params['user_data'] = $User->data;
		}

		if($gateway == 'PurchaseBalance') {
			$params['purchase_balance_redirect'] = $purchase_balance_redirect;
		}

		$gateway = $this->createGateway($gateway);
		$url = $gateway->pay($params);

		if($url !== false) {
			$this->controller->redirect($url);
		}
	}

	public function getSupported($direction, $currency = null) {
		if($currency === null) {
			$currency = Configure::read('siteCurrency');
		}
		$res = array();

		foreach($this->available as $gateway) {
			if($this->checkSupportedCurrency($currency, $gateway, $direction)) {
				$res[] = $gateway;
			}
		}

		return $res;
	}

	public function getSupportedHumanized($direction, $currency = null) {
		$supported = $this->getSupported($direction, $currency);
		$res = array();

		foreach($supported as $gateway) {
			$res[$gateway] = PaymentsInflector::humanize(PaymentsInflector::underscore($gateway));
		}

		return $res;
	}

	public function getActiveWithUserSettingHumanized($type = 'all') {
		$res = array();
		switch($type) {
			case 'cashouts':
				$active = $this->getActiveCashouts();
			break;

			case 'deposits':
				$active = $this->getActiveDeposits();
			break;

			case 'all':
			default:
				$active = $this->getActive();
		}

		foreach($active as $v) {
			$class = PaymentsInflector::classify($v).'Gateway';
			App::uses($class, 'Payments');
			$validationRules = $class::getAccountValidationRules();

			if(is_array($validationRules) || $validationRules === true) {
				$res[$v] = PaymentsInflector::humanize(PaymentsInflector::underscore($v));
			}
		}
		return $res;
	}

	public function getActiveDeposits() {
		return $this->activeDeposits;
	}

	public function getActiveDepositsHumanized($includePurchaseBalance = true) {
		$res = array();

		foreach($this->activeDeposits as $gateway) {
			$res[$gateway] = PaymentsInflector::humanize(PaymentsInflector::underscore($gateway));
		}

		if(!$includePurchaseBalance) {
			unset($res['PurchaseBalance']);
		}

		return $res;
	}

	public function getActiveCashouts() {
		return $this->activeCashouts;
	}

	public function getActiveCashoutsHumanized() {
		$res = array();

		foreach($this->activeCashouts as $gateway) {
			$res[$gateway] = PaymentsInflector::humanize(PaymentsInflector::underscore($gateway));
		}

		return $res;
	}

	public function getActive() {
		return $this->active;
	}

	public function getActiveHumanized() {
		$res = array();
		$active = $this->getActive();

		foreach($active as $gateway) {
			$res[$gateway] = PaymentsInflector::humanize(PaymentsInflector::underscore($gateway));
		}

		return $res;
	}

	private function needsSettings($gatewayName) {
		$class = PaymentsInflector::classify($gatewayName).'Gateway';
		App::uses($class, 'Payments');
		return $class::needsSettings();
	}

	public function getActiveWithSettings() {
		$res = array();
		$active = $this->getActive();

		foreach($active as $v) {
			if($this->needsSettings($v)) {
				$res[] = $v;
			}
		}
		return $res;
	}

	public function getActiveWithSettingsHumanized() {
		$res = array();
		$active = $this->getActive();

		foreach($active as $v) {
			if($this->needsSettings($v)) {
				$res[$v] = PaymentsInflector::humanize(PaymentsInflector::underscore($v));
			}
		}
		return $res;
	}

	public function getSupportedDepositCurrencies($gatewayName) {
		$class = PaymentsInflector::classify($gatewayName).'Gateway';
		App::uses($class, 'Payments');
		return $class::getSupportedCurrencies('Deposit');
	}

	public function getSupportedCashoutCurrencies($gatewayName) {
		$class = PaymentsInflector::classify($gatewayName).'Gateway';
		App::uses($class, 'Payments');
		return $class::getSupportedCurrencies('Cashout');
	}

	public function checkSupportedCurrency($currency, $gatewayName, $direction) {
		switch($direction) {
			case 'Deposit':
				$currencies = $this->getSupportedDepositCurrencies($gatewayName);
			break;

			case 'Cashout':
				$currencies = $this->getSupportedCashoutCurrencies($gatewayName);
			break;

			default:
				throw new InvalidArgumentException(__d('exception', 'Invalid direction (can be only "Deposit" or "Cashout"): %s', $direction));
		}

		if($currencies === null) {
			return true;
		}

		if(in_array($currency, $currencies)) {
			return true;
		}

		return false;
	}

	private function idSprintf($str = '', $args = array(), $magic = ':') {
		if($str == '' || !$str || !is_string($str)) {
			throw new PaymentIdMissingArgumentException(__d('exception', 'Invalid template string'));
		}

		$toReplace = substr_count($str, $magic);

		if($toReplace % 2 != 0 || count($args) < $toReplace / 2) {
			throw new PaymentIdMissingArgumentException();
		}

		foreach($args as $k => $v) {
			$v = str_replace('-', '._.', $v);
			$str = str_replace($magic.$k.$magic, $v, $str, $replaces);
		}

		return $str;
	}

	public function createId($name, array $args) {
		return $this->idSprintf($this::$items[$name]['id'], $args);
	}

	private function createTitle($name, array $args) {
		return $this->idSprintf(__($this::$items[$name]['title']), $args);
	}

	public function generatePaymentsLists($data) {
		$lists = array();

		foreach($data as $gatewayName => $cashouts) {
			$gateway = $this->createGateway($gatewayName);

			$lists[$gatewayName] = $gateway->generatePaymentList($cashouts);
		}

		return $lists;
	}

	public function cashout($cashout) {
		$gateway = $cashout['Cashout']['gateway'];
		if(!isset($gateway) || empty($gateway)) {
			throw new MissingPaymentGatewayException(__d('exception', 'Payment processor not selected'));
		}
		if(!in_array($gateway, $this->available)) {
			throw new MissingPaymentGatewayException(__d('exception', '%s not found!', $gateway));
		}
		$gateway = $this->createGateway($gateway);

		$res = $gateway->cashout($cashout);

		if($res == 'Completed') {
			if(!ClassRegistry::init('UserStatistic')->newCashout($cashout['Cashout']['user_id'],
			 $cashout['Cashout']['gateway'], $cashout['Cashout']['amount'])) {
				throw new InternalErrorException(__d('exception', 'Failed to update statistics'));
			}
			if(!ClassRegistry::init('Settings')->newCashout($cashout['Cashout']['amount'])) {
				throw new InternalErrorException(__d('exception', 'Failed to update statistics'));
			}
		}
		return $res;
	}

	public function refund($deposit) {
		$gateway = $deposit['Deposit']['gateway'];
		if(!isset($gateway) || empty($gateway)) {
			throw new MissingPaymentGatewayException(__d('exception', 'Payment processor not selected'));
		}
		if(!in_array($gateway, $this->available)) {
			throw new MissingPaymentGatewayException(__d('exception', '%s not found!', $gateway));
		}
		$gateway = $this->createGateway($gateway);

		if($gateway->supportsRefunds()) {
			return $gateway->refund($deposit['Deposit']['gatewayid']);
		}
		return false;
	}

	public function supportsRefunds($gatewayName) {
		if($gatewayName == 'AccountBalance') {
			return false;
		}
		if(isset($this->refundsDataCache[$gatewayName])) {
			return $this->refundsDataCache[$gatewayName];
		}
		$gateway = $this->createGateway($gatewayName);

		$this->refundsDataCache[$gatewayName] = $gateway->supportsRefunds();

		return $this->refundsDataCache[$gatewayName];
	}

	public function checkMinimumDepositAmount($gatewayName, $amount) {
		if($this->ignoreMinDeposit) {
			return true;
		}

		if(!isset($this->minimumDepositAmount[PaymentsInflector::classify($gatewayName)])) {
			throw new InternalErrorException(__d('exception', 'Invalid gateway settings'));
		}

		return bccomp($amount, $this->minimumDepositAmount[$gatewayName]) >= 0;
	}

	public function getMinimumDepositAmount($gatewayName = null) {
		if($gatewayName === null) {
			return $this->minimumDepositAmount;
		} else {
			return $this->minimumDepositAmount[$gatewayName];
		}
	}

	public function getIgnoreMinDeposit() {
		return $this->ignoreMinDeposit;
	}
}
