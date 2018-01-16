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
 * PurchaseBalanceGateway
 *
 */
class PurchaseBalanceGateway implements GatewayInterface {
	public function __construct($settings = array()) {
	}

	public function pay(array $params = array()) {
		$cakeobj = new Object();

		$params['amount'] = CurrencyFormatter::round($params['amount'], CurrencyFormatter::realCommaPlaces());

		$cakeobj->requestAction(array(
			'plugin' => null,
			'controller' => 'payments',
			'action' => 'index',
			'purchase_balance'
		), array(
			'data' => $params,
		));

		if(isset($params['purchase_balance_redirect']) && !empty($params['purchase_balance_redirect'])) {
			return $params['purchase_balance_redirect'];
		}

		return false;
	}

	public function getOperationName(array $data = array()) {
		if(isset($data['Cashout'])) {
			return 'cashout';
		} else {
			return 'deposit';
		}
	}

	public function depositCallback(array $data = array()) {
		$status = PaymentStatus::FAILED;

		if(!isset($data['amount'])) {
			throw new PaymentGatewayException(__d('admin', 'Unknown amount'));
		}

		if(!isset($data['title'])) {
			throw new PaymentGatewayException(__d('admin', 'Unknown title'));
		}

		if(!isset($data['id'])) {
			throw new PaymentGatewayException(__d('admin', 'Unknown id'));
		}

		$user = ClassRegistry::init('User');
		$user->id = $data['user_data']['User']['id'];
		$user->contain();
		if(!$user->read('purchase_balance')) {
			throw new PaymentGatewayException(__d('admin', 'Unknown user'));
		}

		if(bccomp($user->data['User']['purchase_balance'], $data['amount']) >= 0) {
			$user->data['User']['purchase_balance'] = bcsub($user->data['User']['purchase_balance'], $data['amount']);
			if($user->save()) {
				$status = PaymentStatus::SUCCESS;
				$data['NoticeComponent']->success(__d('admin', '%s bought sucessfully', $data['title']));
			} else {
				throw new PaymentGatewayException(__d('admin', 'Failed to save purchase balance'));
			}
		} else {
			$data['NoticeComponent']->error(__d('admin', 'Sorry, your do not have enough funds on your Purchase Balance.'));
		}

		return array($status, $data['id'], $data['amount'], $data['user_data']['User']['email'], $data['title']);
	}

	public function getDepositGatewayId(array $data = array()) {
		return md5($data['id'].time());
	}

	public function getCashoutId(array $data = array()) {
		return $data['Cashout']['id'];
	}

	public function cashoutCallback(array $data = array()) {
		$status = PaymentStatus::FAILED;

		$user = ClassRegistry::init('User');

		if($user->purchaseBalanceAdd($data['Cashout']['amount'], $data['Cashout']['user_id'])) {
			$status = PaymentStatus::SUCCESS;
		}

		return $status;
	}

	public static function needsSettings() {
		return false;
	}

	public static function validateSettings(array $check = array()) {
		return false;
	}

	public static function getAccountValidationRules() {
		return null;
	}

	public static function getSupportedCurrencies($direction) {
		switch($direction) {
			case 'Deposit':
				return null;
			case 'Cashout':
				return array();
		}
	}

	public function generatePaymentList($cashouts) {
		return false;
	}

	public function cashout($cashout) {
		$cakeobj = new Object();

		return $cakeobj->requestAction(array(
			'plugin' => null,
			'controller' => 'payments',
			'action' => 'index',
			'admin' => false,
			'purchase_balance',
		), array(
			'data' => $cashout,
		));
	}

	public function supportsRefunds() {
		return true;
	}

	public function refund($transactionId) {
		$depositModel = ClassRegistry::init('Deposit');

		$depositModel->recursive = -1;
		$deposit = $depositModel->find('first', array(
			'fields' => array(
				'amount',
				'user_id',
			),
			'conditions' => array(
				'gatewayid' => $transactionId,
				'gateway' => 'PurchaseBalance',
			),
		));

		if(empty($deposit)) {
			return false;
		}

		$userModel = ClassRegistry::init('User');
		return $userModel->purchaseBalanceAdd($deposit['Deposit']['amount'], $deposit['Deposit']['user_id']);
	}
}
