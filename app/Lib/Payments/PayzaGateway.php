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
 * PayzaGateway
 *
 */
class PayzaGateway implements GatewayInterface {

	const SANDBOX_PAY_ENDPOINT = 'https://sandbox.payza.com/sandbox/payprocess.aspx';
	const SANDBOX_MASSPAY_ENDPOINT = 'https://sandbox.payza.com/api/api.svc/executemasspay';
	const PAY_ENDPOINT = 'https://secure.payza.com/checkout/payprocess.aspx';
	const MASSPAY_ENDPOINT = 'https://api.payza.com/svc/api.svc/executemasspay';
	const SANDBOX_IPN_V2_HANDLER = 'https://sandbox.payza.com/sandbox/ipn2.ashx';
	const IPN_V2_HANDLER = 'https://secure.payza.com/ipn2.ashx';
	const REFUND_ENDPOINT = 'https://api.payza.com/svc/api.svc/RefundTransaction';
	const SANDBOX_REFUND_ENDPOINT = 'https://sandbox.payza.com/svc/api.svc/RefundTransaction';

	protected $settings;
	protected $debug;
	private $data;

	public function __construct($settings = array()) {
		$this->debug = Configure::read('debug');

		$this->settings = $settings['api_settings'];

		if($this->debug > 0) {
			//debug mode, use sandbox
			$this->settings['payEndpoint'] = self::SANDBOX_PAY_ENDPOINT;
			$this->settings['masspayEndpoint'] = self::SANDBOX_MASSPAY_ENDPOINT;
			$this->settings['ipnHandler'] = self::SANDBOX_IPN_V2_HANDLER;
			$this->settings['refundEndpoint'] = self::SANDBOX_REFUND_ENDPOINT;
		} else {
			// production mode, use real Payza
			$this->settings['payEndpoint'] = self::PAY_ENDPOINT;
			$this->settings['masspayEndpoint'] = self::MASSPAY_ENDPOINT;
			$this->settings['ipnHandler'] = self::IPN_V2_HANDLER;
			$this->settings['refundEndpoint'] = self::REFUND_ENDPOINT;
		}

		$this->settings['currency_code'] = $settings['currency_code'];
	}

	public function pay(array $params = array()) {
		$query = array(
			'ap_purchasetype' => 'item',
			'ap_currency' => $this->settings['currency_code'],
			'ap_amount' => round($params['amount'], 2),
			'ap_itemname' => $params['title'],
			'ap_merchant' => $this->settings['account_id'],
			'ap_quantity' => 1,
			'ap_description' => $params['title'],
			'ap_returnurl' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'success', 'Payza', $params['title']), true),
			'ap_cancelurl' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'cancel', 'Payza'), true),
			'ap_alerturl' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'index', 'Payza'), true),
			'ap_ipnversion' => 2,
			'apc_1' => $params['id'],
		);

		$query_string = http_build_query($query);

		return $this->settings['payEndpoint'].'?'.$query_string;
	}

	public function getOperationName(array $data = array()) {
		$token = 'token='.urlencode($_POST['token']);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->settings['ipnHandler']);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $token);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($ch);

		curl_close($ch);

		if(strlen($response) > 0) {
			if(urldecode($response) != 'INVALID TOKEN') {
				$response = urldecode($response);
				$aps = explode('&', $response);

				$this->data = array();

				foreach($aps as $ap) {
					$ele = explode('=', $ap);
					$this->data[$ele[0]] = $ele[1];
				}

				if($this->data['ap_transactiontype'] == 'masspay') {
					return 'cashout';
				}
				else {
					return 'deposit';
				}

			} else {
				throw new PaymentGatewayException('INVALID');
			}
		}
		throw new PaymentGatewayException('No response from Payza');
	}

	public function getCashoutId(array $data = array()) {
		return $this->data['ap_mpcustom'];
	}

	public function cashoutCallback(array $data = array()) {
		$info = &$this->data;

		if($info['ap_returncode'] == '100') {
			if($info['ap_currency'] == $this->settings['currency_code']) {
				return PaymentStatus::SUCCESS;
			}
		}
		return PaymentStatus::FAILED;
	}

	public function depositCallback(array $data = array()) {
		$info = &$this->data;

		if($info['ap_merchant'] == $this->settings['account_id']) {
			if($info['ap_status'] == 'Success') {
				if($info['ap_currency'] == $this->settings['currency_code']) {
					if(!empty($info['apc_1'])) {
						if($info['ap_totalamount'] > 0) {
							return array(PaymentStatus::SUCCESS, $info['apc_1'], $info['ap_totalamount'], $info['ap_custemailaddress']);
						}
					}
				}
			}
		}

		throw new PaymentGatewayException('Wrong data');
	}

	public function getDepositGatewayId(array $data = array()) {
		return $this->data['ap_referencenumber'];
	}

	public static function needsSettings() {
		return true;
	}

	public static function validateSettings(array $check = array()) {
		if(empty($check['account_id']) || !filter_var($check['account_id'], FILTER_VALIDATE_EMAIL)) {
			return __d('admin', 'Payza account id should be a valid e-mail address.');
		}

		return true;
	}

	public static function getAccountValidationRules() {
		return array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Payza account id should be a valid e-mail address',
			),
			'length' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Payza account id cannot be longer than 255 characters',
			),
		);
	}

	public static function getSupportedCurrencies($direction) {
		return array(
			'AUD',
			'BGN',
			'CAD',
			'CHF',
			'CZK',
			'DKK',
			'EEK',
			'EUR',
			'GBP',
			'HKD',
			'HUF',
			'LTL',
			'MYR',
			'MKD',
			'NOK',
			'NZD',
			'PLN',
			'RON',
			'SEK',
			'SGD',
			'USD',
			'ZAR'
		);
	}

	public function generatePaymentList($cashouts) {
		$res = '';

		foreach($cashouts as $c) {
			$amount = round($c['amount'], 2);
			$res .= "{$c['payment_account']},{$amount},{$this->settings['masspay_description']}";
		}

		return $res == '' ? false : $res;
	}

	public function cashout($cashout) {
		$data = array(
			'USER' => $this->settings['account_id'],
			'PASSWORD' => $this->settings['api_password'],
			'CURRENCY' => $this->settings['currency_code'],
			'SENDEREMAIL' => $this->settings['account_id'],
			'RECEIVEREMAIL_1' => $cashout['Cashout']['payment_account'],
			'AMOUNT_1' => round($cashout['Cashout']['amount'], 2),
			'NOTE_1' => $this->settings['masspay_description'],
			'MPCUSTOM_1' => $cashout['Cashout']['id'],
		);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->settings['masspayEndpoint']);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($ch);

		curl_close($ch);

		if(!empty($response)) {
			parse_str($response, $parsed);

			if(isset($parsed['RETURNCODE']) && $parsed['RETURNCODE'] == 100) {
				return 'Pending';
			}
		}
		return 'Failed';
	}

	public function supportsRefunds() {
		return true;
	}

	public function refund($transactionId) {
		$result = false;

		$data = array(
			'USER' => $this->settings['account_id'],
			'PASSWORD' => $this->settings['api_password'],
			'TRANSACTIONREFERENCE' => $transactionId,
		);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $this->settings['refundEndpoint']);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($ch);

		curl_close($ch);

		if(!empty($response)) {
			parse_str($response, $parsed);

			if($parsed['RETURNCODE'] == 100) {
				$result = true;
			}
		}
		return $result;
	}
}
