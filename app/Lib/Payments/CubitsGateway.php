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
App::uses('Cubits', 'Vendor');

/**
 * CubitsGateway
 *
 */
class CubitsGateway implements GatewayInterface {
	protected $settings;
	protected $data;

	public function __construct($settings = array()) {
		$this->settings = $settings['api_settings'];
		$this->settings['currency_code'] = $settings['currency_code'];

		Cubits::configure('https://pay.cubits.com/api/v1/', true);
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
					'AUD',
					'CAD',
					'CHF',
					'CZK',
					'DKK',
					'EUR',
					'GBP',
					'HRK',
					'HUF',
					'JPY',
					'NOK',
					'PLN',
					'RSD',
					'SEK',
					'USD',
					'TRY',
					'RUB',
					'CNY',
					'BRL',
					'ARS',
					'MXN',
					'ZAR',
					'KRW',
					'XBT',
				);
			case 'Cashout':
				return array('XBT');
		}
	}

	public function pay(array $params = array()) {
		$cubits = Cubits::withApiKey($this->settings['api_key'], $this->settings['api_secret']);
		$currency = $this->settings['currency_code'] == 'XBT' ? 'BTC' : $this->settings['currency_code'];
		$response = $cubits->createInvoice($params['title'], $params['amount'], $currency, array(
			'description' => $params['title'],
			'reference' =>  $params['id'],
			'callback_url' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'index', 'Cubits'), true),
			'success_url' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'success', 'Cubits', $params['title']), true),
			'cancel_url' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'cancel', 'Cubits'), true),
		));

		return $response->invoice_url;
	}

	public function getOperationName(array $data = array()) {
		foreach(getallheaders() as $name => $value) {
			switch($name) {
				case "X-Cubits-Callback-Id":
					$cubits_callback_id = $value;
				break;

				case "X-Cubits-Key":
					$cubits_key = $value;
				break;

				case "X-Cubits-Signature":
					$cubits_signature = $value;
				break;
			}
		}

		if(!isset($cubits_callback_id) || !isset($cubits_key) || !isset($cubits_signature)) {
			throw new PaymentGatewayException('INVALID REQUEST');
		}

		if($cubits_key !== $this->settings['api_key']) {
			throw new PaymentGatewayException('INVALID KEY');
		}

		$json = file_get_contents('php://input');
		$this->data = json_decode($json, true);

		$hash = hash('sha256', utf8_encode($json), false);
		$hash_hmac = hash_hmac('sha512', $cubits_callback_id.$hash, $this->settings['api_secret']);

		if($cubits_signature !== $hash_hmac) {
			throw new PaymentGatewayException('INVALID SIGNATURE');
		}

		return 'deposit';
	}

	public function getDepositGatewayId(array $data = array()) {
		return $this->data['id'];
	}

	public function depositCallback(array $data = array()) {
		$id = $this->data['reference'];
		$payment_amount = (string)$this->data['merchant_amount'];
		$payer_email = 'Cubits';
		$status = PaymentStatus::FAILED;

		switch($this->data['status']) {
			case 'completed':
				$status = PaymentStatus::SUCCESS;
			break;

			case 'pending':
				$status = PaymentStatus::PENDING;
			break;
		}

		$result = array($status, $id, $payment_amount, $payer_email);
		return $result;
	}

	public function cashout($cashout) {
		$cubits = Cubits::withApiKey($this->settings['api_key'], $this->settings['api_secret']);

		try {
			$res = $cubits->sendMoney($cashout['Cashout']['payment_account'], $cashout['Cashout']['amount']);
			return 'Completed';
		} catch(Exception $e) {
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

	public function generatePaymentList($cashouts) {
		return false;
	}

	public function supportsRefunds() {
		return false;
	}
}
