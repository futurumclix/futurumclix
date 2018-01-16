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
 * BlockioGateway
 *
 */
class BlockioGateway implements GatewayInterface {
	protected $settings;
	protected $data;

	public function __construct($settings = array()) {
		$this->settings = $settings['api_settings'];
		$this->settings['currency_code'] = $settings['currency_code'];

		if(!isset($this->settings['confirmations']) || (empty($this->settings['confirmations']) && $this->settings['confirmations'] != 0)) {
			$this->settings['confirmations'] = 3;
		}
	}

	public static function needsSettings() {
		return true;
	}

	public static function validateSettings(array $check = array()) {
		if(!extension_loaded('gmp')) {
			throw new \Exception('GMP extension seems not to be installed');
		}
		if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off'){
			throw new \Exception('HTTPS connections seems to be disabled');
		}
		return true;
	}

	public static function getAccountValidationRules() {
		return true;
	}

	public static function getSupportedCurrencies($direction) {
		switch($direction) {
			case 'Deposit':
				return array('XBT');
			case 'Cashout':
				return array('XBT');
		}
	}

	public function pay(array $params = array()) {
		App::uses('BlockIo', 'Vendor');
		$blockio = new BlockIo($this->settings['api_key'], $this->settings['pin'], 2);
		$res = $blockio->get_new_address(array('label' => $params['id']));

		if($res->status === 'success') {
			$notify = $blockio->create_notification(array(
				'type' => 'address',
				'address' => $res->data->address,
				'url' => urlencode(Router::url(array('plugin' => null, 'controller' => 'payments', 'action' => 'index', 'Blockio', $params['id']), true)),
			));

			if($notify->status == 'success') {
				CakeSession::write('XBT', array('amount' => $params['amount'], 'addr' => $res->data->address, 'label' => $params['title']));
				return array('plugin' => null, 'controller' => 'payments', 'action' => 'xbt');
			}
		}
		throw new PaymentGatewayException(__d('exception', 'Block.io error %s', print_r($res, true)));
	}

	public function getOperationName(array $data = array()) {
		$json = file_get_contents('php://input');
		$this->data = json_decode($json, true);

		if($this->data['type'] == 'ping') {
			exit;
		}

		return 'deposit';
	}

	public function getDepositGatewayId(array $data = array()) {
		return $this->data['data']['address'];
	}

	public function depositCallback(array $data = array()) {
		$req = Router::getRequest();

		$ip = $req->clientIp();

		if($ip !== gethostbyname('n.block.io')) {
			throw new NotFoundException(__d('exception', 'Invalid source %s', $ip));
		}

		$status = PaymentStatus::FAILED;

		if($this->data['data']['confirmations'] >= $this->settings['confirmations']) {
			$status = PaymentsStatus::SUCCESS;
		} else {
			$status = PaymentStatus::PENDING;
		}


		if(!isset($req->params['pass'][1]) || empty($req->params['pass'][1])) {
			throw new NotFoundException(__d('exception', 'Missing pass arg'));
		}

		$id = $req->params['pass'][1];

		return array($status, $id, $this->data['data']['balance_change'], $this->data['data']['address']);
	}

	public function cashout($cashout) {
		App::uses('BlockIo', 'Vendor');
		$blockio = new BlockIo($this->settings['api_key'], $this->settings['pin'], 2);

		try{
			$res = $blockio->withdraw(array('amounts' => $cashout['Cashout']['amount'], 'to_addresses' => $cashout['Cashout']['payment_account'], 'priority' => $this->settings['cashout_priority']));
		} catch(Exception $e) {
			throw $e;
		}

		if($res->status == 'success') {
			return 'Pending';
		}

		return false;
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
