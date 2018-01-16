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
 * PayeerGateway
 *
 */
class PayeerGateway implements GatewayInterface {
	const ENDPOINT = 'https://payeer.com/merchant';

	protected $settings;
	protected $allowedIPs = array(
		'185.71.65.92',
		'185.71.65.189',
		'149.202.17.210',
	);

	public function __construct($settings = array()) {
		$this->settings = $settings['api_settings'];
		$this->settings['endpoint'] = self::ENDPOINT;

		$this->settings['currency_code'] = $settings['currency_code'];
	}

	private function makeId($id) {
		return str_replace('-', '', $id);
	}

	private function revertId($id) {
		return substr($id, 0, 8).'-'.substr($id, 8, 4).'-'.substr($id, 12, 4).'-'.substr($id, 16, 4).'-'.substr($id, 20);
	}

	public function pay(array $params = array()) {
		$query = array(
			'm_shop' => $this->settings['shopNumber'],
			'm_orderid' => $this->makeId($params['id']),
			'm_amount' => number_format($params['amount'], 2, '.', ''),
			'm_curr' => $this->settings['currency_code'],
			'm_desc' => base64_encode($params['title']),
		);

		$sign = strtoupper(hash('sha256', implode(':', $query).':'.$this->settings['secretKey']));

		$query['m_sign'] = $sign;
		$query['m_process'] = 'send';
		$query['form[ps]'] = 2609;
		$query['form[curr[2609]]'] = $this->settings['currency_code'];

		$query_string = http_build_query($query);

		return $this->settings['endpoint'].'?'.$query_string;
	}

	public function getOperationName(array $data = array()) {
		if(!in_array($_SERVER['REMOTE_ADDR'], $this->allowedIPs)) {
			throw new PaymentGatewayException(__d('admin', 'Not allowed, request from %s', $_SERVER['REMOTE_ADDR']), 403);
		}

		if(isset($_POST['m_operation_id']) && isset($_POST['m_sign'])) {
			return 'deposit';
		}

		throw new PaymentGatewayException(__d('admin', 'Invalid request'));
	}

	public function depositCallback(array $data = array()) {
		$arHash = array(
			$_POST['m_operation_id'],
			$_POST['m_operation_ps'],
			$_POST['m_operation_date'],
			$_POST['m_operation_pay_date'],
			$_POST['m_shop'],
			$_POST['m_orderid'],
			$_POST['m_amount'],
			$_POST['m_curr'],
			$_POST['m_desc'],
			$_POST['m_status'],
			$this->settings['secretKey']
		);
		$sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));

		$id = $this->revertId($_POST['m_orderid']);

		if($_POST['m_sign'] == $sign_hash && $_POST['m_status'] == 'success') {
			echo $_POST['m_orderid'].'|success';
			return array(PaymentStatus::SUCCESS, $id, $_POST['m_amount'], 'unknown');
		}
		echo $_POST['m_orderid'].'|error';
		return array(PaymentStatus::FAILED, $id, $_POST['m_amount'], 'unknown');
	}

	public function getDepositGatewayId(array $data = array()) {
		if(isset($_POST['m_operation_id'])) {
			return $_POST['m_operation_id'];
		}
		throw new PaymentGatewayException(__d('admin', 'Operation id not set'));
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
			'RUB',
		);
	}

	public function cashout($cashout) {
		App::uses('CPayeer', 'Vendor');

		$payeer = new CPayeer($this->settings['accountNumber'], $this->settings['cashoutApiId'], $this->settings['cashoutApiKey']);
		if($payeer->isAuth()) {
			$arTransfer = $payeer->transfer(array(
				'curIn' => $this->settings['currency_code'],
				'sum' => number_format($cashout['Cashout']['amount'], 2, '.', ''),
				'curOut' => $this->settings['currency_code'],
				'to' => $cashout['Cashout']['payment_account'],
			));
			if(empty($arTransfer['errors'])) {
				return 'Completed';
			} else {
				throw new PaymentGatewayException(print_r($arTransfer['errors'], true));
			}
		} else {
			throw new PaymentGatewayException(print_r($payeer->getErrors(), true));
		}
		return false;
	}

	public function supportsRefunds() {
		return false;
	}

	public function refund($transactionId) {
		throw new PaymentGatewayException(__d('admin', 'Not supported'));
	}

/**
 * getCashoutId() and cashoutCallback() are not needed in Payeer
 *
 */
	public function getCashoutId(array $data = array()) {
		throw new PaymentGatewayException(__d('admin', 'Not needed'));
	}

	public function cashoutCallback(array $data = array()) {
		throw new PaymentGatewayException(__d('admin', 'Not needed'));
	}

/**
 * Payment lists are not supported in Payeer
 *
 */
	public function generatePaymentList($cashouts) {
		return false;
	}
}
