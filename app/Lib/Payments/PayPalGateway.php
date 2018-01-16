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
App::uses('ModelValidator', 'Model');

/**
 * PayPalGateway
 *
 */
class PayPalGateway implements GatewayInterface {

	protected $settings;
	protected $debug;

	public function __construct($settings = array()) {
		$this->debug = Configure::read('debug');

		$this->settings = $settings['api_settings'];
		$this->settings['currency_code'] = $settings['currency_code'];
	}

	public function pay(array $params = array()) {
		$query = array(
			'notify_url' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'index', 'PayPal'), true),
			'cancel_return' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'cancel', 'PayPal'), true),
			'return' => Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'success', 'PayPal', $params['title']), true),
			'cmd' => '_xclick', /* tell paypal that we want to do "buy now" action */
			'business' => $this->settings['account_id'],
			'first_name' => @$params['user_data']['User']['first_name'],
			'last_name' => @$params['user_data']['User']['last_name'],
			'email' => @$params['user_data']['UserProfile']['pay_pal'],
			'item_name' => $params['title'],
			'quantity' => 1,
			'amount' => round($params['amount'], 2),
			'currency_code' => $this->settings['currency_code'],
			'custom' => $params['id'],
			'no_shipping' => 1,
		);

		$query_string = http_build_query($query);

		if($this->debug > 0) {
			return 'https://www.sandbox.paypal.com/cgi-bin/webscr?'.$query_string;
		} else {
			return 'https://www.paypal.com/cgi-bin/webscr?'.$query_string;
		}
	}

	public function cashout($cashout) {
		$url = $this->debug > 0 ? 'https://api-3t.sandbox.paypal.com' : 'https://api-3t.paypal.com';
		$url .= '/nvp';
		$result = 'Failed';

		$data = array(
			'METHOD' => 'MassPay',
			'VERSION' => '51.0',
			'RECEIVERTYPE' => 'EmailAddress',
			'USER' => $this->settings['api_username'],
			'PWD' => $this->settings['api_password'],
			'SIGNATURE' => $this->settings['api_signature'],
			'EMAILSUBJECT' => $this->settings['masspay_subject'],
			'CURRENCYCODE' => $this->settings['currency_code'],
			'L_EMAIL0' => $cashout['Cashout']['payment_account'],
			'L_AMT0' => round($cashout['Cashout']['amount'], 2),
			'L_NOTE0' => $this->settings['masspay_description'],
			'L_UNIQUEID0' => $cashout['Cashout']['id'],
		);

		$req = http_build_query($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_VERBOSE, true);

		if($this->debug > 0) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}

		if(($res = curl_exec($ch))) {
			parse_str($res, $parsed);

			if($parsed['ACK'] == 'Success') {
				$result = 'Pending';
			}
		}

		curl_close($ch);

		return $result;
	}

	public function getOperationName(array $data = array()) {
		if($_POST['txn_type'] == 'web_accept') {
			return 'deposit';
		}

		if($_POST['txn_type'] == 'masspay') {
			return 'cashout';
		}

		return false;
	}

	public function getCashoutId(array $data = array()) {
		return $_POST['unique_id_1'];
	}

	public function cashoutCallback(array $data = array()) {
		$verifyURL = $this->debug > 0 ? 'https://sandbox.paypal.com' : 'https://paypal.com';
		$verifyURL .= '/cgi-bin/webscr';
		$result = PaymentStatus::FAILED;

		$payer_email      = &$_POST['payer_email'];
		$payment_status   = &$_POST['payment_status'];
		$status           = &$_POST['status_1'];
		$payment_amount   = &$_POST['mc_gross_1'];
		$payment_currency = &$_POST['mc_currency_1'];

		$req = 'cmd=_notify-validate&'.http_build_query($_POST);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $verifyURL);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_VERBOSE, true);

		if($this->debug > 0) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}

		if(($res = curl_exec($ch))) {
			if(strcmp(trim($res), 'VERIFIED') == 0) {
				if($payer_email == $this->settings['account_id']) {
					if($payment_currency == $this->settings['currency_code']) {
						if($payment_amount > 0) {
							switch($status) {
								case 'Completed':
									$result = PaymentStatus::SUCCESS;
								break;

								case 'Processed':
									$status = PaymentStatus::PENDING;
								break;

								default:
									$result = PaymentStatus::FAILED;
							}
						}
					}
				}
			}
		}

		curl_close($ch);

		return $result;
	}

	public function getDepositGatewayId(array $data = array()) {
		return $_POST['txn_id'];
	}

	public function supportsRefunds() {
		return true;
	}

	public function refund($transactionId) {
		$url = $this->debug > 0 ? 'https://api-3t.sandbox.paypal.com' : 'https://api-3t.paypal.com';
		$url .= '/nvp';
		$result = false;

		$data = array(
			'METHOD' => 'RefundTransaction',
			'VERSION' => '94',
			'USER' => $this->settings['api_username'],
			'PWD' => $this->settings['api_password'],
			'SIGNATURE' => $this->settings['api_signature'],
			'REFUNDTYPE' => 'Full',
			'CURRENCYCODE' => $this->settings['currency_code'],
			'TRANSACTIONID' => $transactionId,
		);

		$req = http_build_query($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_VERBOSE, true);

		if($this->debug > 0) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}

		if(($res = curl_exec($ch))) {
			parse_str($res, $parsed);

			if($parsed['ACK'] == 'Success') {
				$result = true;
			}
		}

		curl_close($ch);

		return $result;
	}

	public function depositCallback(array $data = array()) {
		$verifyURL = $this->debug > 0 ? 'https://sandbox.paypal.com' : 'https://ipnpb.paypal.com';
		$verifyURL .= '/cgi-bin/webscr';

		$payment_status   = &$_POST['payment_status'];
		$payment_amount   = &$_POST['mc_gross'];
		$payment_currency = &$_POST['mc_currency'];
		$receiver_email   = &$_POST['receiver_email'];
		$id               = &$_POST['custom'];
		$payer_email      = &$_POST['payer_email'];
		$payer_status     = &$_POST['payer_status'];

		$result = array(PaymentStatus::FAILED, $id, $payment_amount, $payer_email);

		$req = 'cmd=_notify-validate&'.http_build_query($_POST);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $verifyURL);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_PORT, 443);
		curl_setopt($ch, CURLOPT_VERBOSE, true);

		if($this->debug > 0) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}

		if(($res = curl_exec($ch))) {
			if(strcmp(trim($res), 'VERIFIED') === 0) {
				if($receiver_email == $this->settings['account_id']) {
					if($payment_currency == $this->settings['currency_code']) {
						if($payment_amount > 0) {

							if(!isset($this->settings['allow_unverified']) || !$this->settings['allow_unverified']) {
								if($payer_status != 'verified') {
									if($this->refund($this->getDepositGatewayId())) {
										return array(PaymentStatus::AUTO_REFUNDED, $id, $payment_amount, $payer_email);
									} else {
										throw new InternalErrorException(__d('admin', 'Failed to refund'));
									}
								}
							}

							switch($payment_status) {
								case 'Completed':
									$status = PaymentStatus::SUCCESS;
								break;

								case 'Processed':
								case 'Pending':
								case 'Created':
									$status = PaymentStatus::PENDING;
								break;

								default:
									$status = PaymentStatus::FAILED;
							}
							$result = array($status, $id, $payment_amount, $payer_email);
						}
					}
				}
			}
		}

		curl_close($ch);

		if($result === false)
			throw new PaymentGatewayException('INVALID');

		return $result;
	}

	public static function needsSettings() {
		return true;
	}

	public static function validateSettings(array $check = array()) {
		if(empty($check['account_id']) || !filter_var($check['account_id'], FILTER_VALIDATE_EMAIL)) {
			return __d('admin', 'PayPal account id should be a valid e-mail address.');
		}

		if(strlen($check['masspay_description']) > 255) {
			return __d('admin', 'PayPal payment description cannot be longer than 255 characters');
		}

		return true;
	}

	public static function getAccountValidationRules() {
		return array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'PayPal account id should be a valid e-mail address',
			),
			'length' => array(
				'rule' => array('maxLength', 255),
				'message' => 'PayPal account id cannot be longer than 255 characters',
			),
		);
	}

	public static function getSupportedCurrencies($direction) {
		return array(
			'AUD',
			'BRL',
			'CAD',
			'CZK',
			'DKK',
			'EUR',
			'HKD',
			'HUF',
			'ILS',
			'JPY',
			'MYR',
			'MXN',
			'NOK',
			'NZD',
			'PHP',
			'PLN',
			'GBP',
			'RUB',
			'SGD',
			'SEK',
			'CHF',
			'TWD',
			'THB',
			'TRY',
			'USD'
		);
	}

	public function generatePaymentList($cashouts) {
		$res = '';

		foreach($cashouts as $c) {
			$amount = round($c['amount'], 2);
			$res .= "{$c['payment_account']},{$amount},{$this->settings['currency_code']},,{$this->settings['masspay_description']}";
		}

		return $res == '' ? false : $res;
	}
}
