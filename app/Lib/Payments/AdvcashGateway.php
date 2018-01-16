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
 * AdvcashGateway
 *
 */
class AdvcashGateway implements GatewayInterface {
	const ENDPOINT = 'https://wallet.advcash.com/sci';
	const WSDL_URL = 'https://wallet.advcash.com:8443/wsm/merchantWebService?wsdl';

	protected $settings;
	protected $soapOptions = array(
		'location' => 'https://wallet.advcash.com:8443/wsm/merchantWebService',
	);
	protected $soapClient = null;

	public function createAuthToken() {
		$date = new DateTime('now', new DateTimeZone('UTC'));
		$string = implode(':', array(
			$this->settings['password'],
			$date->format('Ymd'),
			$date->format('H')
		));
		return strtoupper(hash('sha256', $string));
	}

	private function createSoapClient() {
		try {
			$this->soapClient = new SoapClient(self::WSDL_URL, $this->soapOptions);
		} catch(SoapFault $e) {
			throw new PaymentGatewayException(__d('admin', 'Failed to create SoapClient: %s', $e->getMessage()));
		}
	}

	private function sendSoap($method, $params = null) {
		if(!$this->soapClient) {
			$this->createSoapClient();
		}

		try {
			$result = $this->soapClient->{$method}(array(
				'arg0' => array(
					'apiName' => $this->settings['name'],
					'accountEmail' => $this->settings['email'],
					'authenticationToken' => $this->createAuthToken(),
				),
				'arg1' => $params,
			));
		} catch(SoapFault $e) {
			throw new PaymentGatewayException(__d('admin', 'Failed to send Soap request: %s', $e->getMessage()));
		}
		return json_decode(json_encode($result), true);
	}

	public function __construct($settings = array()) {
		$this->settings = $settings['api_settings'];
		$this->settings['currency_code'] = $settings['currency_code'];
	}

	public function pay(array $params = array()) {
		$data = array(
			'ac_account_email' => $this->settings['accountEmail'],
			'ac_sci_name' => $this->settings['sciName'],
			'ac_amount' => round($params['amount'], 2),
			'ac_currency' => $this->settings['currency_code'],
			'ac_order_id' => $params['id'],
			'ac_comments' => $params['title'],
			'ac_success_url' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'success', 'Advcash', $params['title']), true),
			'ac_success_url_method' => 'POST',
			'ac_fail_url' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'cancel', 'Advcash'), true),
			'ac_fail_url_method' => 'POST',
			'ac_status_url' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'index', 'Advcash'), true),
			'ac_status_url_method' => 'POST',
		);

		$string = $data['ac_account_email'].':'.$data['ac_sci_name'].':'.$data['ac_amount'].':'.$data['ac_currency'].':'.$this->settings['secret'].':'.$data['ac_order_id'];

		$data['ac_sign'] = hash('sha256', $string);

		$query_string = http_build_query($data);

		return self::ENDPOINT.'?'.$query_string;
	}

	public function getOperationName(array $data = array()) {
		return 'deposit';
	}

	public function depositCallback(array $data = array()) {
		$string = $_POST['ac_transfer'].':'.$_POST['ac_start_date'].':'.$_POST['ac_sci_name']
		 .':'.$_POST['ac_src_wallet'].':'.$_POST['ac_dest_wallet'].':'.$_POST['ac_order_id']
		 .':'.$_POST['ac_amount'].':'.$_POST['ac_merchant_currency'].':'.$this->settings['secret'];

		$hash = hash('sha256', $string);

		if($_POST['ac_hash'] != $hash) {
			throw new PaymentGatewayException(__d('admin', 'Invalid request hash'));
		}

		if($_POST['ac_dest_wallet'] != $this->settings['accountNumber']) {
			throw new PaymentGatewayException(__d('admin', 'Invalid account number'));
		}

		if($_POST['ac_amount'] <= 0) {
			throw new PaymentGatewayException(__d('admin', 'Invalid amount'));
		}

		if($_POST['ac_merchant_currency'] != $this->settings['currency_code']) {
			throw new PaymentGatewayException(__d('admin', 'Invalid currency'));
		}

		if(empty($_POST['ac_order_id'])) {
			throw new PaymentGatewayException(__d('admin', 'Order ID is empty'));
		}

		switch($_POST['ac_transaction_status']) {
			case 'PENDING':
			case 'PROCESS':
			case 'CONFIRMED':
				$status = PaymentStatus::PENDING;
			break;

			case 'COMPLETED':
				$status = PaymentStatus::SUCCESS;
			break;

			case 'CANCELED':
				$status = PaymentStatus::FAILED;
			break;

			default:
				throw new PaymentGatewayException(__d('admin', 'Invalid status: %s', $_POST['ac_transaction_status']));
		}

		return array($status, $_POST['ac_order_id'], $_POST['ac_amount'], $_POST['ac_src_wallet']);
	}

	public function getDepositGatewayId(array $data = array()) {
		return $_POST['ac_transfer'];
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
			'RUR',
			'GBP',
		);
	}

	public function cashout($cashout) {
		if(filter_var($cashout['Cashout']['payment_account'], FILTER_VALIDATE_EMAIL)) {
			$type = 'email';
		} else {
			$type = 'walletId';
		}

		$result = $this->sendSoap('sendMoney', array(
			'amount' => round($cashout['Cashout']['amount'], 2),
			'currency' => $this->settings['currency_code'],
			$type => $cashout['Cashout']['payment_account'],
			'note' => $this->settings['cashoutNote'],
			'savePaymentTemplate' => false,
		));

		if(isset($result['error'])) {
			return 'Failed';
		}

		return 'Completed';
	}

	public function supportsRefunds() {
		return false;
	}

	public function refund($transactionId) {
		throw new PaymentGatewayException(__d('admin', 'Not supported'));
	}

/**
 * Payment lists are not supported in Advcash
 *
 */
	public function generatePaymentList($cashouts) {
		return false;
	}

/**
 * getCashoutId() and cashoutCallback() are not needed in Advcash
 *
 */
	public function getCashoutId(array $data = array()) {
		throw new PaymentGatewayException(__d('admin', 'Not needed'));
	}

	public function cashoutCallback(array $data = array()) {
		throw new PaymentGatewayException(__d('admin', 'Not needed'));
	}
}
