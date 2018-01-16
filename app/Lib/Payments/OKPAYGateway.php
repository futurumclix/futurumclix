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
 * OKPAYGateway
 *
 */
class OKPAYGateway implements GatewayInterface {
	const ENDPOINT = 'https://checkout.okpay.com/';
	const VERIFY_ENDPOINT = 'https://checkout.okpay.com/ipn-verify';

	protected $settings;
	protected $debug;

	public function __construct($settings = array()) {
		$this->settings = $settings['api_settings'];
		$this->settings['currency_code'] = $settings['currency_code'];
		$this->debug = Configure::read('debug');
	}

	private function _makePostRequest($url, $data = array()) {
		$req = http_build_query($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);

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
			'ok_receiver' => $this->settings['receiver'],
			'ok_item_1_name' => $params['title'],
			'ok_item_1_price' => round($params['amount'], 2),
			'ok_item_1_article' => $params['id'],
			'ok_currency' => $this->settings['currency_code'],
			'ok_ipn' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'index', 'OKPAY'), true),
			'ok_return_success' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'success', 'OKPAY', $params['title']), true),
			'ok_return_fail' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'cancel', 'OKPAY'), true),
		);

		$res = http_build_query($payment);

		return self::ENDPOINT.'?'.$res;
	}

	public function getOperationName(array $data = array()) {
		return 'deposit';
	}

	public function depositCallback(array $data = array()) {
		$res = $this->_makePostRequest(self::VERIFY_ENDPOINT, array('ok_verify' => 'true') + $_POST);

		if($res == 'INVALID') {
			throw new PaymentGatewayException(__d('admin', 'Request INVALID.'));
		}

		if($res == 'TEST' && $this->debug <= 0) {
			throw new PaymentGatewayException(__d('admin', 'Test request without debug mode.'));
		}

		if($res != 'VERIFIED' && $res != 'TEST') {
			throw new PaymentGatewayException(__d('admin', 'Unknown verify answer: %s', print_r($res, true)));
		}

		if($_POST['ok_txn_kind'] != 'payment_link') {
			throw new PaymentGatewayException(__d('admin', 'Unknown payment kind: %s', print_r($_POST['ok_txn_kind'], true)));
		}

		if($_POST['ok_receiver_wallet'] != $this->settings['receiver']) {
			throw new PaymentGatewayException(__d('admin', 'Unknown receiver: %s vs %s', print_r($_POST['ok_receiver_wallet'], true), $this->settings['receiver']));
		}

		$status = PaymentStatus::FAILED;

		switch($_POST['ok_txn_status']) {
			case 'completed':
				$status = PaymentStatus::SUCCESS;
			break;

			case 'pending':
				$status = PaymentStatus::PENDING;
			break;

			case 'reversed':
				$status = PaymentStatus::AUTO_REFUNDED;
			break;
		}

		return array($status, $_POST['ok_item_1_article'], $_POST['ok_txn_net'], $_POST['ok_payer_id']);
	}

	public function getDepositGatewayId(array $data = array()) {
		return @$_POST['ok_ipn_id'];
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
		return array(
			'EUR',
			'USD',
			'GBP',
			'HKD',
			'CHF',
			'AUD',
			'PLN',
			'JPY',
			'SEK',
			'DKK',
			'CAD',
			'RUB',
			'CZK',
			'HRK',
			'HUF',
			'NOK',
			'NZD',
			'RON',
			'TRY',
			'ZAR',
			'PHP',
			'SGD',
			'MYR',
			'TWD',
			'ILS',
			'MXN',
			'CNY',
			'NGN',
		);
	}

	public function generatePaymentList($cashouts) {
		$res = '';

		foreach($cashouts as $c) {
			$amount = round($c['amount'], 2);
			$res .= "{$c['payment_account']}\t{$amount}\t{$this->settings['currency_code']}\t{$c['id']}\t{$this->settings['masspay_description']}";
		}

		return $res == '' ? false : $res;
	}

	public function cashout($cashout) {
		try
		{
			$secWord  = $this->settings['api_key'];
			$WalletID = $this->settings['receiver'];

			$datePart = gmdate("Ymd:H");
			$authString = $secWord.":".$datePart;

			$secToken = hash('sha256', $authString);
			$secToken = strtoupper($secToken);

			$opts = array(
				'http'=>array(
					'user_agent' => 'PHPSoapClient'
				)
			);
			$context = stream_context_create($opts);
			$client = new SoapClient("https://api.okpay.com/OkPayAPI?wsdl", array(
				'stream_context' => $context,
				'cache_wsdl' => WSDL_CACHE_NONE
			));

			$arr = array();

			$arr['WalletID'] = $WalletID;
			$arr['SecurityToken'] = $secToken;
			$arr['Currency'] = $this->settings['currency_code'];
			$arr['Receiver'] = $cashout['Cashout']['payment_account'];
			$arr['Amount'] = round($cashout['Cashout']['amount'], 2);
			$arr['Comment'] = isset($this->settings['masspay_description']) ? $this->settings['masspay_description'] : '';
			$arr['IsReceiverPaysFees'] = (boolean)$this->settings['receiver_fees'];
			$webService = $client->Send_Money($arr);
			$wsResult = $webService->Send_MoneyResult;

			switch($wsResult->Status) {
				case 'None':
				case 'Error':
				case 'Reversed':
				case 'Hold':
					return 'Failed';

				default:
					return $wsResult->Status;
			}
		}
		catch (Exception $e)
		{
			return 'Failed';
		}
	}

	public function getCashoutId(array $data = array()) {
		throw new PaymentGatewayException(__d('exception', 'Not needed'));
	}

	public function cashoutCallback(array $data = array()) {
		throw new PaymentGatewayException(__d('exception', 'Not needed'));
	}

	public function refund($transactionId) {
		throw new PaymentGatewayException(__d('exception', 'Not supported'));
	}

	public function supportsRefunds() {
		return false;
	}
}
