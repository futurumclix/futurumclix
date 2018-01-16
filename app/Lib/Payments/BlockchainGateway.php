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
 * BlockchainGateway
 *
 */
class BlockchainGateway implements GatewayInterface {
	const ENDPOINT = 'https://api.blockchain.info/v2/receive';
	const TOXBT_ENDPOINT = 'https://blockchain.info/tobtc';
	const ID_FIELD = 'fc_id';

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

	private function convertToXBT($amount, $currency = null) {
		if($currency === null) {
			$currency = $this->settings['currency_code'];
		}
		if($currency == 'BTC' || $currency == 'XBT') {
			return $amount;
		}
		$params = array(
			'currency' => $this->settings['currency_code'],
			'value' => $amount,
		);

		$req = http_build_query($params);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, self::TOXBT_ENDPOINT.'?'.$req);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		if($this->debug) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}

		$res = curl_exec($ch);

		if($res === false) {
			throw new PaymentGatewayException(__d('exception', 'Failed to connect %s', curl_error($ch)));
		}

		curl_close($ch);

		return $res;
	}

	public function pay(array $params = array()) {
		$payment = array(
			'xpub' => $this->settings['xPub'],
			'key' => $this->settings['key'],
			'gap_limit' => isset($this->settings['gap_limit']) ? $this->settings['gap_limit'] : 20,
			'callback' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'index', 'Blockchain', '?' => array(self::ID_FIELD => $params['id'])), true),
		);

		$amount = $this->convertToXBT($params['amount']);

		$req = http_build_query($payment);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, self::ENDPOINT.'?'.$req);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		if($this->debug) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}

		$res = curl_exec($ch);

		if($res === false) {
			throw new PaymentGatewayException(__d('exception', 'Failed to connect %s', curl_error($ch)));
		}

		curl_close($ch);

		$res = json_decode($res, true);

		if($res === false) {
			throw new PaymentGatewayException(__d('exception', 'Invalid response'));
		}

		if(!isset($res['address']) || empty($res['address'])) {
			throw new PaymentGatewayException(__d('exception', 'Error: %s', $res['message']));
		}

		$this->setCheckAmount($params['id'], $amount);

		CakeSession::write('XBT', array('amount' => $amount, 'addr' => $res['address'], 'label' => $params['title']));
		return array('plugin' => null, 'controller' => 'payments', 'action' => 'xbt');
	}

	public function getOperationName(array $data = array()) {
		return 'deposit';
	}

	public function depositCallback(array $data = array()) {
		$amount = bcdiv($_GET['value'], '100000000');

		if(!$this->debug && $_GET['test']) {
			throw new PaymentGatewayException(__d('exception', 'Test request without debug mode'));
		}

		if(!isset($_GET[self::ID_FIELD]) || empty($_GET[self::ID_FIELD])) {
			throw new PaymentGatewayException(__d('exception', 'Unknown item id'));
		}

		if($_GET['confirmations'] >= $this->settings['confirmations']) {
			echo '*ok*';
			return array(PaymentStatus::SUCCESS, $_GET[self::ID_FIELD], $amount, $_GET['address']);
		}

		return array(PaymentStatus::PENDING, $_GET[self::ID_FIELD], $amount, $_GET['address']);
	}

	public function getDepositGatewayId(array $data = array()) {
		return @$_GET['transaction_hash'];
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
					'USD',
					'ISK',
					'HKD',
					'TWD',
					'CHF',
					'EUR',
					'DKK',
					'CLP',
					'CAD',
					'CNY',
					'THB',
					'AUD',
					'SGD',
					'KRW',
					'JPY',
					'PLN',
					'GBP',
					'SEK',
					'NZD',
					'BRL',
					'RUB',
				);
			case 'Cashout':
				return array();
		}
	}

	public function getCashoutId(array $data = array()) {
		throw new PaymentGatewayException(__d('exception', 'Not supported'));
	}

	public function cashoutCallback(array $data = array()) {
		throw new PaymentGatewayException(__d('exception', 'Not supported'));
	}

	public function refund($transactionId) {
		throw new PaymentGatewayException(__d('exception', 'Not supported'));
	}

	public function cashout($cashout) {
		throw new PaymentGatewayException(__d('exception', 'Not supported'));
	}

	public function generatePaymentList($cashouts) {
		return false;
	}

	public function supportsRefunds() {
		return false;
	}

}
