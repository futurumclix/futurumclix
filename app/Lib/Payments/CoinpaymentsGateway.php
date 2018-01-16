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
App::uses('GatewayInterface', 'Payments');

/**
 * CoinpaymentsGateway
 *
 */
class CoinpaymentsGateway implements GatewayInterface {
	const ENDPOINT = 'https://www.coinpayments.net/api.php';

	protected $settings;
	protected $debug;

	public function __construct($settings = array()) {
		$this->settings = $settings['api_settings'];
		$this->settings['currency_code'] = $settings['currency_code'];
		$this->debug = Configure::read('debug');
	}

	private function setCheckAmount($id, $check_amount) {
		$shortItemId = ClassRegistry::init('ShortItemId');
		$shortItemId->create();
		$shortItemId->id = $id;
		$shortItemId->set('check_amount', $check_amount);

		if(!$shortItemId->save()) {
			throw new InternalErrorException(__d('exception', 'Failed to shorten item id'));
		}
	}

	private function makeCall($cmd, $req = array()) {
		$req['version'] = 1;
		$req['cmd'] = $cmd;
		$req['key'] = $this->settings['public_key'];
		$req['format'] = 'json';

		$data = http_build_query($req, '', '&');

		$hmac = hash_hmac('sha512', $data, $this->settings['private_key']);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, self::ENDPOINT);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('HMAC: '.$hmac));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		if($this->debug) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}

		$res = curl_exec($ch);

		if($res === false) {
			throw new PaymentGatewayException(__d('exception', 'Failed to connect: %s', curl_error($ch)));
		}

		$res = json_decode($res, true);

		if($res === false) {
			throw new PaymentGatewayException(__d('exception', 'Invalid response: %s', json_last_error()));
		}

		if($res['error'] != 'ok') {
			throw new PaymentGatewayException(__d('exception', 'Coinpayments Error: %s', $res['error']));
		}

		return $res;
	}

	private function checkCallback() {
		if(!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') { 
			throw new PaymentGatewayException(__d('exception', 'Invalid IPN mode (should be HMAC).'));
		} 

		if(!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
			throw new PaymentGatewayException(__d('exception', 'Invalid request, missing hash.'));
		}

		$request = file_get_contents('php://input');
		if($request === false || empty($request)) {
			throw new PaymentGatewayException(__d('exception', 'Invalid request, missing data.'));
		}

		if(!isset($_POST['merchant']) || empty($_POST['merchant']) || $this->settings['merchant_id'] != $_POST['merchant']) {
			throw new PaymentGatewayException(__d('exception', 'Invalid merchant id.'));
		}

		$hmac = hash_hmac('sha512', $request, $this->settings['secret']);

		if($hmac != $_SERVER['HTTP_HMAC']) {
			throw new PaymentGatewayException(__d('exception', 'Invalid hash.'));
		}
	}

	private function convertToXBT($amount, $currency = null) {
		if($currency === null) {
			$currency = $this->settings['currency_code'];
		}
		if($currency == 'BTC' || $currency == 'XBT') {
			return $amount;
		}

		$res = $this->makeCall('rates');

		return $res;
	}

	public function pay(array $params = array()) {
		$payment = array(
			'amount' => $params['amount'],
			'currency1' => $this->settings['currency_code'] == 'XBT' ? 'BTC' : $this->settings['currency_code'],
			'currency2' => 'BTC',
			'item_name' => $params['title'],
			'custom' => $params['id'],
			'buyer_email' => @$params['user_data']['User']['email'],
		);

		$res = $this->makeCall('create_transaction', $payment);

		if(!isset($res['result']['amount']) || empty($res['result']['amount'])) {
			throw new PaymentGatewayException(__d('exception', 'Invalid amount'));
		}

		$this->setCheckAmount($params['id'], $res['result']['amount']);

		CakeSession::write('XBT', array('amount' => $res['result']['amount'], 'addr' => $res['result']['address'], 'label' => $params['title']));
		return array('plugin' => null, 'controller' => 'payments', 'action' => 'xbt');
	}

	public function getOperationName(array $data = array()) {
		if($_POST['ipn_type'] == 'withdrawal') {
			return 'cashout';
		}
		return 'deposit';
	}

	public function depositCallback(array $data = array()) {
		$this->checkCallback();

		$status = intval($_POST['status']);

		if($status >= 100 || $status == 2) {
			$status = PaymentStatus::SUCCESS;
		} elseif($status < 0) {
			$status = PaymentStatus::FAILED;
		} else {
			$status = PaymentStatus::PENDING;
		}

		return array($status, $_POST['custom'], $_POST['amount2'], $_POST['email']);
	}

	public function getDepositGatewayId(array $data = array()) {
		return @$_POST['txn_id'];
	}

	public static function needsSettings() {
		return true;
	}

	public static function validateSettings(array $check = array()) {
		return true;
	}

	public static function getAccountValidationRules() {
		return true;
	}

	public static function getSupportedCurrencies($direction) {
		switch($direction) {
			case 'Deposit':
				return array(
					'XBT',
					'EUR',
					'GBP',
					'USD',
					'LTC',
				);
			case 'Cashout':
				return array(
					'XBT',
				);
		}
	}

	public function cashout($cashout) {
		$payment = array(
			'amount' => $cashout['Cashout']['amount'],
			'currency' => 'BTC',
			'address' => $cashout['Cashout']['payment_account'],
			'auto_confirm' => isset($this->settings['auto_confirm']) && $this->settings['auto_confirm'] ? '1' : '0',
			'ipn_url' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'index', 'Coinpayments', 'id' => $cashout['Cashout']['id']), true),
		);

		$res = $this->makeCall('create_withdrawal', $payment);

		switch($res['result']['status']) {
			case 0:
			case 1:
				return 'Pending';

			default:
				throw new PaymentGatewayException(__d('exception', 'Unknown status code %s', $res['result']['status']));
		}
	}

	public function getCashoutId(array $data = array()) {
		$url = parse_url(Router::url(null, true), PHP_URL_PATH);
		$arr = explode('/', $url);
		$named = array();

		foreach($arr as $val){
			if(strpos($val, ":") !== false) {
				$vars  = explode(":",$val);
				$named[$vars[0]] = $vars[1];
			}
		}

		return @$named['id'];
	}

	public function cashoutCallback(array $data = array()) {
		$this->checkCallback();

		switch($_POST['status']) {
			case 0:
			case 1:
				return PaymentStatus::PENDING;

			case 2:
				return PaymentStatus::SUCCESS;

			default:
				return PaymentStatus::FAILED;
		}
	}

	public function supportsRefunds() {
		return false;
	}

	public function refund($transactionId) {
		throw new PaymentGatewayException(__d('exception', 'Not supported'));
	}

	public function generatePaymentList($cashouts) {
		return false;
	}
}
