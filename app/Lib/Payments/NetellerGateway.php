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
 * NetellerGateway
 *
 */
class NetellerGateway implements GatewayInterface {

	const ENDPOINT = 'https://api.neteller.com';
	const SANDBOX_ENDPOINT = 'https://test.api.neteller.com';
	const OAUTH_LOCATION = '/v1/oauth2/token';
	const ORDER_LOCATION = '/v1/orders';
	const CASHOUT_LOCATION = '/v1/transferOut';

	protected $settings;
	protected $debug;
	private $authData = null;
	private $data = null;

	public function __construct($settings = array()) {
		$this->debug = Configure::read('debug');

		$this->settings = $settings['api_settings'];

		if($this->debug > 0) {
			//debug mode, use sandbox
			$this->settings['cashoutEndpoint'] = self::SANDBOX_ENDPOINT.self::CASHOUT_LOCATION;
			$this->settings['orderEndpoint'] = self::SANDBOX_ENDPOINT.self::ORDER_LOCATION;
			$this->settings['oauthEndpoint'] = self::SANDBOX_ENDPOINT.self::OAUTH_LOCATION;
		} else {
			// production mode, use real Neteller
			$this->settings['cashoutEndpoint'] = self::ENDPOINT.self::CASHOUT_LOCATION;
			$this->settings['orderEndpoint'] = self::ENDPOINT.self::ORDER_LOCATION;
			$this->settings['oauthEndpoint'] = self::ENDPOINT.self::OAUTH_LOCATION;
		}

		$this->settings['currency_code'] = $settings['currency_code'];
		$v = explode('.', Configure::read('currency.Currency.step'));
		$places = strlen(rtrim($v[1], '0'));
		$this->settings['amountMultipler'] = '1';
		for($i = 0; $i < $places; $i++) {
			$this->settings['amountMultipler'] .= '0';
		}
	}

	private function createAmount($amount) {
		$temp = bcmul($amount, $this->settings['amountMultipler']);
		$temp = explode('.', $temp);

		return $temp[0];
	}

	private function authorize() {
		$url = $this->settings['oauthEndpoint'].'?grant_type=client_credentials';
		$header = array();

		$header[] = 'Authorization: Basic '.base64_encode($this->settings['clientId'].':'.$this->settings['clientSecret']);
		$header[] = 'Content-Type: application/json';
		$header[] = 'Cache-Control: no-cache';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

		if($this->debug > 0) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}

		$response = curl_exec($ch);

		if($response === false) {
			throw new PaymentGatewayException(__d('admin', 'Connection error: %s', curl_error($ch)));
		}

		$this->authData = json_decode($response, true);

		if($this->authData === false) {
			throw new PaymentGatewayException(__d('admin', 'Unknown response format'));
		}

		if(isset($this->authData['error'])) {
			throw new PaymentGatewayException(__d('admin', 'Neteller OAuth error: %s', $this->authData['error']));
		}

		if(!isset($this->authData['tokenType']) || $this->authData['tokenType'] != 'Bearer') {
			throw new PaymentGatewayException(__d('admin', 'Invalid Neteller access token type'));
		}

		if(!isset($this->authData['accessToken']) || empty($this->authData['accessToken'])) {
			throw new PaymentGatewayException(__d('admin', 'Invalid Neteller access token'));
		}

		$this->authData['validUntil'] = time() + $this->authData['expiresIn'];

		curl_close($ch);
	}

	private function sendRequest($data, $endpoint) {
		if(empty($this->authData) || isset($this->authData['error']) || empty($this->authData['accessToken']) || $this->authData['validUntil'] >= time()) {
			$this->authorize();
		}
		$data = json_encode($data, JSON_PRETTY_PRINT);

		$header = array();

		$header[] = 'Authorization:Bearer '.$this->authData['accessToken'];
		$header[] = 'Content-Type:application/json';
		$header[] = 'Cache-Control:no-cache';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

		if($this->debug > 0) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}

		$response = curl_exec($ch);

		if($response === false) {
			throw new PaymentGatewayException(__d('admin', 'Connection error: %s', curl_error($ch)));
		}

		$data = json_decode($response, true);

		if($data === false) {
			throw new PaymentGatewayException(__d('admin', 'Unknown response format'));
		}

		return $data;
	}

	private function getStatus($url) {
		if(empty($this->authData) || isset($this->authData['error']) || empty($this->authData['accessToken']) || $this->authData['validUntil'] >= time()) {
			$this->authorize();
		}

		$header = array();

		$header[] = 'Authorization:Bearer '.$this->authData['accessToken'];
		$header[] = 'Content-Type:application/json';
		$header[] = 'Cache-Control:no-cache';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

		if($this->debug > 0) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}

		$response = curl_exec($ch);

		if($response === false) {
			throw new PaymentGatewayException(__d('admin', 'Connection error: %s', curl_error($ch)));
		}

		$data = json_decode($response, true);

		if($data === false) {
			throw new PaymentGatewayException(__d('admin', 'Unknown response format'));
		}

		return $data;
	}

	public function pay(array $params = array()) {
		$data = array(
			'order' => array(
				'merchantRefId' => $params['id'],
				'totalAmount' => $this->createAmount($params['amount']),
				'currency' => $this->settings['currency_code'],
				'lang' => 'en_US',
				'items' => array(
					array(
						'quantity' => 1,
						'name' => $params['title'],
						'description' => $params['title'],
						'amount' => $this->createAmount($params['amount']),
					),
				),
				'redirects' => array(
					array(
						'rel' => 'on_success',
						'uri' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'success', 'Neteller', $params['title']), true),
					),
					array(
						'rel' => 'on_cancel',
						'uri' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'cancel', 'Neteller'), true),
					),
				),
			),
		);

		$res = $this->sendRequest($data, $this->settings['orderEndpoint']);

		if(isset($res['error'])) {
			throw new PaymentGatewayException(__d('admin', 'Neteller error[%d]: "%s" %s', $res['error']['code'], $res['error']['message'], print_r($res, true)));
		}

		if(!isset($res['orderId']) || empty($res['orderId'])) {
			throw new PaymentGatewayException(__d('admin', 'Neteller order id not received'));
		}

		if(!isset($res['links']) || empty($res['links'])) {
			throw new PaymentGatewayException(__d('admin', 'No redirect links received'));
		}

		$redirect = null;
		foreach($res['links'] as $link) {
			if(isset($link['rel']) && $link['rel'] == 'hosted_payment') {
				$redirect = $link['url'];
			}
		}

		if(!$redirect) {
			throw new PaymentGatewayException(__d('admin', 'Neteller hosted payment redirect URL not found'));
		}

		return $redirect;
	}

	public function getOperationName(array $data = array()) {
		$this->data = json_decode(file_get_contents('php://input'), true);

		if($this->data === false) {
			throw new PaymentGatewayException(__d('admin', 'No data received'));
		}

		if(isset($this->settings['webhookKey']) && !empty($this->settings['webhookKey'])) {
			if($this->data['key'] != $this->settings['webhookKey']) {
				throw new PaymentGatewayError(__d('admin', 'Invalid webhook secret key'));
			}
		}

		if(!isset($this->data['eventType'])) {
			throw new PaymentGatewayException(__d('admin', 'No event type set'));
		}

		if(strstr($this->data['eventType'], 'payment') !== false) {
			return 'deposit';
		}

		throw new PaymentGatewayException(__d('admin', 'Unknown event type'));
	}

	public function depositCallback(array $data = array()) {
		if(!$this->data) {
			throw new PaymentGatewayException(__d('admin', 'No data received'));
		}

		if($this->data['mode'] == 'test') {
			die(); /* return HTTP 200 on test mode */
		} 

		if(!isset($this->data['links'][0]['url'])) {
			throw new PaymentGatewayException(__d('admin', 'No invoice url'));
		}

		$url = $this->data['links'][0]['url'];

		$res = $this->getStatus($url);

		switch($res['transaction']['status']) {
			case 'accepted':
				$status = PaymentStatus::SUCCESS;
			break;

			case 'pending':
				$status = PaymentStatus::PENDING;
			break;
 
 			case 'cancelled':
			case 'declined':
				$status = PaymentStatus::FAILED;
			break;

			default:
				throw new PaymentGatewayException(__d('admin', 'Invalid status: %s', $res['transaction']['status']));
		}

		if(!isset($res['transaction']['merchantRefId']) || empty($res['transaction']['merchantRefId'])) {
			throw new PaymentGatewayException(__d('admin', 'Invalid merchantRefId'));
		}

		$id = $res['transaction']['merchantRefId'];
		$amount = bcdiv($res['transaction']['amount'], $this->settings['amountMultipler']);

		$result = array($status, $id, $amount, $res['billingDetail']['email']);

		return $result;
	}

	public function getDepositGatewayId(array $data = array()) {
		return $this->data['id'];
	}

	public static function needsSettings() {
		return true;
	}

	public static function validateSettings(array $check = array()) {
		if(strlen($check['masspayDescription']) > 255) {
			return __d('admin', 'Neteller payment description cannot be longer than 255 characters');
		}

		return true;
	}

	public static function getAccountValidationRules() {
		return true;
	}

	public static function getSupportedCurrencies($direction) {
		return array(
			'AED',
			'AUD',
			'BRL',
			'GBP',
			'BGN',
			'CAD',
			'DKK',
			'EUR',
			'HUF',
			'INR',
			'JPY',
			'MYR',
			'MAD',
			'MXN',
			'NGN',
			'NOK',
			'PLN',
			'RON',
			'RUB',
			'SGD',
			'SEK',
			'CHF',
			'TWD',
			'TND',
			'USD',
			'ZAR',
		);
	}

	public function generatePaymentList($cashouts) {
		$res = '';

		foreach($cashouts as $c) {
			$amount = $this->createAmount($c['amount']);
			$res .= '"'.$c['payment_account'].'","'.$amount.'","'.$this->settings['currency_code'].'","'.$c['id'].'","'.str_replace('"', '""', $this->settings['masspayDescription']).'"'."\n";
		}

		return $res == '' ? false : '"recipient","amount","currencyCode","merchantRefId","message"'."\n".$res;
	}

	public function cashout($cashout) {
		if(filter_var($cashout['Cashout']['payment_account'], FILTER_VALIDATE_EMAIL)) {
			$type = 'email';
		} else {
			$type = 'accountId';
		}

		$amount = $this->createAmount($cashout['Cashout']['amount']);

		$data = array(
			'payeeProfile' => array(
				$type => $cashout['Cashout']['payment_account'],
			),
			'transaction' => array(
				'amount' => $amount,
				'currency' => $this->settings['currency_code'],
				'merchantRefId' => $cashout['Cashout']['id'],
			),
			'message' => $this->settings['masspayDescription'],
		);

		$res = $this->sendRequest($data, $this->settings['cashoutEndpoint']);

		if(isset($res['error'])) {
			throw new PaymentGatewayException(__d('admin', 'Neteller error[%d]: "%s" %s', $res['error']['code'], $res['error']['message'], print_r($res, true)));
		}

		switch($res['transaction']['status']) {
			case 'accepted':
				return 'Completed';
			case 'pending':
				return 'Pending';
 			case 'cancelled':
				return 'Cancelled';
			case 'declined':
				return 'Failed';
			default:
				throw new PaymentGatewayException(__d('admin', 'Invalid status: %s', $res['transaction']['status']));
		}
	}

	public function supportsRefunds() {
		return false;
	}

	public function refund($transactionId) {
		throw new PaymentGatewayException(__d('admin', 'Not supported'), 501);
	}

/**
 * getCashoutId() and cashoutCallback() are not needed in Neteller
 */
	public function getCashoutId(array $data = array()) {
		throw new PaymentGatewayException(__d('admin', 'Not needed'), 501);
	}

	public function cashoutCallback(array $data = array()) {
		throw new PaymentGatewayException(__d('admin', 'Not needed'), 501);
	}
}
