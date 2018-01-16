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
 * SkrillGateway
 *
 */
class SkrillGateway implements GatewayInterface {
	const ENDPOINT = 'https://pay.skrill.com';
	const PAY_ENDPOINT = 'https://www.skrill.com/app/pay.pl';

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
		$amount = CurrencyFormatter::round($params['amount'], CurrencyFormatter::realCommaPlaces());
		$amount = CurrencyFormatter::cutTrailingZeros($amount);

		$payment = array(
			'prepare_only' => 1,
			'pay_to_email' => $this->settings['email'],
			'transaction_id' => $params['id'],
			'return_url' =>  Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'success', 'Skrill', $params['title']), true),
			'cancel_url' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'cancel', 'Skrill'), true),
			'status_url' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'index', 'Skrill'), true),
			'pay_from_email' => @$params['user_data']['UserProfile']['skrill'],
			'firstname' => @$params['user_data']['User']['first_name'],
			'lastname' => @$params['user_data']['User']['last_name'],
			'date_of_birth' => @date('dmY', strtotime($params['user_data']['UserProfile']['birth_day'])),
			'amount' => $amount,
			'currency' => $this->settings['currency_code'],
			'detail1_description' => $params['title'],
		);

		if($this->settings['notify']) {
			$payment['status_url2'] = 'mailto:'.Configure::read('siteEmail');
		}

		$res = $this->_makePostRequest(self::ENDPOINT, $payment);

		$error = json_decode($res, true);
		if($error != false) {
			throw new PaymentGatewayException(__d('exception', 'Gateway error code: %s message: %s', $error['code'], $error['message']));
		}

		return self::ENDPOINT.'?sid='.$res;
	}

	public function getOperationName(array $data = array()) {
		return 'deposit';
	}

	public function depositCallback(array $data = array()) {
		$sig = strtoupper(md5($_POST['merchant_id'].$_POST['transaction_id'].strtoupper(md5($this->settings['secret'])).$_POST['mb_amount'].$_POST['mb_currency'].$_POST['status']));

		if($sig != $_POST['md5sig']) {
			throw new PaymentGatewayException(__d('exception', 'Invalid signature: %s vs %s', $sig, $_POST['md5sig']));
		}

		if($_POST['currency'] != $this->settings['currency_code']) {
			throw new PaymentGatewayException(__d('exception', 'Invalid currency: %s vs %s', $this->settings['currency_code'], $_POST['currency']));
		}

		switch($_POST['status']) {
			case 2:
				return array(PaymentStatus::SUCCESS, $_POST['transaction_id'], $_POST['amount'], $_POST['pay_from_email']);

			case 0:
				return array(PaymentStatus::PENDING, $_POST['transaction_id'], $_POST['amount'], $_POST['pay_from_email']);

			default:
				return array(PaymentStatus::FAILED, $_POST['transaction_id'], $_POST['amount'], $_POST['pay_from_email']);
		}
	}

	public function getDepositGatewayId(array $data = array()) {
		return @$_POST['mb_transaction_id'];
	}

	public function cashout($cashout) {
		$result = 'Failed';

		/* in Skrill subject is required */
		if(!isset($this->settings['masspay_subject']) || empty($this->settings['masspay_subject'])) {
			$subject = __('%s, your payment has been processed', Configure::read('siteName'));
		} else {
			$subject = $this->settings['masspay_subject'];
		}
		/* in Skrill description is required */
		if(!isset($this->settings['masspay_description']) || empty($this->settings['masspay_description'])) {
			$note = __('We are pleased to inform you, that your payment from %s has been processed', Configure::read('siteName'));
		} else {
			$note = $this->settings['masspay_description'];
		}

		$data = array(
			'action' => 'prepare',
			'email' => $this->settings['email'],
			'password' => md5($this->settings['api_password']),
			'amount' => rtrim(rtrim(round($cashout['Cashout']['amount'], 2), '0'), '.'),
			'currency' => $this->settings['currency_code'],
			'bnf_email' => $cashout['Cashout']['payment_account'],
			'subject' => $subject,
			'note' => $note,
			'frn_trn_id' => $cashout['Cashout']['id'],
		);
		$res = $this->_makePostRequest(self::PAY_ENDPOINT, $data);

		$xml = simplexml_load_string($res);
		if($xml === false) {
			throw new PaymentGatewayException(__d('exception', 'Unrecognized answer'));
		}

		if($xml->error) {
			throw new PaymentGatewayException(__d('exception', 'Gateway error A: %s', $xml->error->error_msg));
		}

		$xml_array = (array)$xml;
		$data = array(
			'action' => 'transfer',
			'sid' => $xml_array['sid'],
		);

		$res = $this->_makePostRequest(self::PAY_ENDPOINT, $data);

		$xml = simplexml_load_string($res);
		if($xml === false) {
			throw new PaymentGatewayException(__d('exception', 'Unrecognized answer'));
		}

		if($xml->error) {
			throw new PaymentGatewayException(__d('exception', 'Gateway error B: %s', $xml->error->error_msg));
		}

		if($xml->Status == 1) {
			$result = 'Pending';
		} elseif($xml->Status == 2) {
			$result = 'Completed';
		}

		return $result;
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
			case 'Cashout':
				return array(
					'EUR',
					'USD',
					'GBP',
					'HKD',
					'SGD',
					'JPY',
					'CAD',
					'AUD',
					'CHF',
					'DKK',
					'SEK',
					'NOK',
					'ILS',
					'MYR',
					'NZD',
					'TRY',
					'AED',
					'MAD',
					'QAR',
					'SAR',
					'TWD',
					'THB',
					'CZK',
					'HUF',
					'BGN',
					'PLN',
					'ISK',
					'INR',
					'KRW',
					'ZAR',
					'RON',
					'HRK',
					'JOD',
					'OMR',
					'RSD',
					'TND',
					'BHD',
					'KWD',
				);
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

	public function generatePaymentList($cashouts) {
		return false;
	}

	public function supportsRefunds() {
		return false;
	}
}
