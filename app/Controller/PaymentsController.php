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
App::uses('AppController', 'Controller');
App::uses('MissingPaymentGatewayException', 'Payments');
App::uses('PaymentsInflector', 'Payments');
App::uses('PaymentStatus', 'Payments');

/**
 * PaymentsController
 *
 * 
 */
class PaymentsController extends AppController {
/**
 * Models
 *
 * @var array
 */
	public $uses = array(
		'Cashout',
		'Deposit',
		'Commission',
		'User',
		'Settings',
	);

/**
 * components
 *
 * @var array
 */
	public $components = array(
		'Payments',
		'UserPanel',
	);

	private function getShortId($shortId) {
		$shortItemIdModel = ClassRegistry::init('ShortItemId');
		$shortItemIdModel->recursive = -1;

		$data = $shortItemIdModel->findById($shortId);

		if(empty($data)) {
			throw new InternalErrorException(__d('exception', 'Failed to unshorten item id'));
		}

		return $data[$shortItemIdModel->alias];
	}

	private function deleteShortId($shortId) {
		return ClassRegistry::init('ShortItemId')->delete($shortId);
	}

	private function _depositCallback($gateway_name, &$gateway) {
		$gatewayid = $gateway->getDepositGatewayId($this->request->data);

		if(!is_string($gatewayid) || empty($gatewayid) || $gatewayid == '') {
			throw new PaymentGatewayException(__d('exception', 'Failed to get Deposit gatewayid.'));
		}

		$deposit = $this->Deposit->find('first', array(
			'fields' => array(
				'Deposit.id',
				'Deposit.status',
			),
			'conditions' => array(
				'Deposit.gatewayid' => $gatewayid,
				'Deposit.gateway' => $gateway_name,
			),
			'recursive' => -1,
		));

		if(!empty($deposit) && $deposit['Deposit']['status'] == PaymentStatus::SUCCESS) {
			return; /* this one we already processed, skip */
		}

		list($status, $shortId, $amount, $payer) = $gateway->depositCallback($this->request->data);

		if($gateway_name == 'PurchaseBalance') {
			$id = $shortId;
			$check_amount = null;
		} else {
			$short_id = $this->getShortId($shortId);
			$id = $short_id['item_id'];
			$check_amount = $short_id['check_amount'];
		}

		$data = $this->Deposit->explodeItemId($id);
		$user_id = $data[2];

		if($user_id == 'null') {
			$user_id = null;
		}

		if($user_id && !$this->User->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid User'));
		}

		/* @TODO: refactor using currency different than site currency */
		if($check_amount === null) {
			/* payment currency is same as site currency, act normal */
			$currency_places = CurrencyFormatter::realCommaPlaces();
			$toRound = bcadd($data[1], $this->Payments->getDepositFee($data[1], $gateway_name));
			$full_amount = CurrencyFormatter::round($toRound, $currency_places);

			if(bccomp($amount, $full_amount) < 0) {
				throw new InternalErrorException(__d('exception', 'Too low [%s] amount %s %s %s', $currency_places, $amount, $full_amount, $data[1]));
			}
		} else {
			/* payment currency is different than site currency, make exact check with check_amount from short_ids table (every rounding needs to be handled in gateway implementation, to check_amount should be inserted value *with* fee included) */
			if(bccomp($amount, $check_amount) < 0) {
				throw new InternalErrorException(__d('exception', 'Too low amount [check] %s %s %s', $amount, $check_amount, $data[1]));
			}
			$amount = $data[1]; /* amount in payment currency is ok, so we can turn back to requested amount in site currency */
		}

		if($status != PaymentStatus::SUCCESS && $gateway_name == 'PurchaseBalance') {
			/* prevent creating un-successful PurchaseBalance deposits */
			return;
		}

		$depositData = array(
			'gateway' => $gateway_name,
			'gatewayid' => $gatewayid,
			'status' => $status,
			'amount' => $amount,
			'account' => $payer,
			'item' => $id,
			'user_id' => $user_id,
			'date' => date('Y-m-d H:i:s'),
		);

		if(empty($deposit)) {
			$this->Deposit->create();
		} else {
			$depositData['id'] = $deposit['Deposit']['id'];
		}

		if(!$this->Deposit->save($depositData)) {
			throw new PaymentGatewayException(__d('exception', 'Failed to save deposit: %s', print_r($this->Deposit->validationErrors, true)), 500);
		}

		if($status == PaymentStatus::SUCCESS && $gateway_name != 'PurchaseBalance') {
			$this->deleteShortId($shortId);
		}
	}

	private function _cashoutCallback($gateway_name, &$gateway) {
		$id = $gateway->getCashoutId($this->request->data);

		if(!is_numeric($id)) {
			throw new PaymentGatewayException(__d('exception', 'Failed to get Cashout id.'));
		}

		$cashout = $this->Cashout->find('first', array(
			'fields' => array(
				'Cashout.id',
				'Cashout.status',
				'Cashout.user_id',
				'Cashout.gateway',
				'Cashout.amount',
				'Cashout.fee',
			),
			'conditions' => array(
				'Cashout.id' => $id,
				'Cashout.gateway' => $gateway_name
			),
		));

		if(empty($cashout) || $cashout['Cashout']['status'] == 'Completed') {
			return; /* this one not exists or we already processed it, skip */
		}

		$result = $gateway->cashoutCallback($this->request->data);

		$newstatus = 'Failed';

		switch($result) {
			case PaymentStatus::SUCCESS:
				$newstatus = 'Completed';
			break;

			case PaymentStatus::PENDING:
				$newstatus = 'Pending';
			break;
		}

		if($cashout['Cashout']['status'] != $newstatus) {
			$cashout['Cashout']['status'] = $newstatus;

			$userid = $cashout['Cashout']['user_id'];
			$gateway = $cashout['Cashout']['gateway'];
			$amount = $cashout['Cashout']['amount'];
			$fee = $cashout['Cashout']['fee'];

			if(!($this->Cashout->save($cashout))) {
				throw new InternalErrorException(__d('exception', 'Failed to change Cashout status'));
			}

			$set = $this->Settings->fetch('autoCashoutRefund');

			if($newstatus == 'Completed') {
				if(!$this->Cashout->User->UserStatistic->newCashout($userid, $gateway, $amount)) {
					throw new InternalErrorException(__d('exception', 'Failed to update statistics'));
				}
				if(!$this->Settings->newCashout($amount)) {
					throw new InternalErrorException(__d('exception', 'Failed to update statistics'));
				}

				$this->User->contain();
				$user = $this->User->findById($userid);

				if($user['User']['role'] == 'Active' && $user['User']['allow_emails']) {
					$email = ClassRegistry::init('Email');
					$email->setVariables(array(
						'%username%' => $user['User']['username'],
						'%firstname%' => $user['User']['first_name'],
						'%lastname%' => $user['User']['last_name'],
						'%amount%' => $amount,
						'%gateway%' => PaymentsInflector::humanize($gateway),
					));

					$email->send('Success Cashout', $user['User']['email']);
				}
			} elseif($newstatus == 'Cancelled' || ($newstatus == 'Failed' && $set['Settings']['autoCashoutRefund'])) {
				$refund = bcadd($amount, $fee);

				if(!$this->Cashout->User->accountBalanceAdd($refund, $userid)) {
					throw new InternalErrorException(__d('exception', 'Failed to refund'));
				}

				if(!$this->Cashout->User->UserStatistic->cancelCashout($userid, $gateway, $amount)) {
					throw new InternalErrorException(__d('exception', 'Failed to update statistics'));
				}

				if(!$this->Settings->cancelCashout($amount)) {
					throw new InternalErrorException(__d('exception', 'Failed to update statistics'));
				}
			}
		}
	}

/**
 * Runs specified callback
 *
 * @param string What gateway should be used
 * @throws MissingPaymentGatewayException When the gateway could not be found
 * @return void
 */
	public function index() {
		$this->autoRender = false;
		$gateways = $this->Payments->getActiveDeposits();
		$path = func_get_args();

		if(count($path) < 1) {
			throw new MissingPaymentGatewayException(__d('exception', 'Processor not specified'), 404);
		}

		$gateway_name = PaymentsInflector::classify($path[0]);

		if($gateway_name == 'PurchaseBalance') {
			$this->request->data['NoticeComponent'] = $this->Notice;
		}

		if(in_array($gateway_name, $gateways) || $gateway_name == 'PurchaseBalance') {
			$gateway = $this->Payments->createGateway($gateway_name);
			switch($gateway->getOperationName($this->request->data)) {
				case 'deposit':
					$this->_depositCallback($gateway_name, $gateway);
				break;

				case 'cashout':
					$this->_cashoutCallback($gateway_name, $gateway);
				break;

				default:
					throw new NotFoundException(__d('exception', 'Operation not implemented'), 404);
				break;
			}
		} else {
			throw new MissingPaymentGatewayException(__d('exception', 'Processor not found'), 404);
		}

		if($gateway_name == 'PurchaseBalance') {
			return 'Completed';
		}
	}

/**
 * cancel method (return from payment processor without buying)
 *
 * @param string payment gateway name
 * @return void
 */
	public function cancel($gateway = null, $title = null) {
		if($this->Auth->loggedIn()) {
			$this->set('user', $this->UserPanel->getData());
		}
		$this->set('breadcrumbTitle', __('Payment Cancelled'));
		$this->set(compact('title', 'gateway'));
	}

/**
 * success method (return from payment processor)
 *
 * @param string payment gateway name
 * @return void
 */
	public function success($gateway = null, $title = null) {
		if($this->Auth->loggedIn()) {
			$this->set('user', $this->UserPanel->getData());
		}
		$this->set('breadcrumbTitle', __('Payment Successful'));
		$this->set(compact('title', 'gateway'));
	}

/**
 * paymentRedirect method
 *
 * If gateway does not support GET in payment redirection one can use
 * this method to redirect via POST form autosubmitted via JavaScript.
 * Payment data should be stored in session as array with keys:
 *  - gateway - string - gateway name;
 *  - url - string - POST URL;
 *  - data - array - form data.
 *
 * @return void
 */
	public function paymentRedirect() {
		$payment = CakeSession::read('Payment');
		CakeSession::delete('Payment');

		if(!$payment || empty($payment) || empty($payment['data'] || empty($payment['url'])) ) {
			throw new InternalErrorException(__d('exception', 'Payment data not set'));
		}

		if($this->Auth->loggedIn()) {
			$this->set('user', $this->UserPanel->getData());
		}
		$this->set('breadcrumbTitle', __('Payment redirection'));
		$this->set(compact('payment'));
	}

/**
 * manual method
 *
 * ManualPayment data should be stored in session as array with keys:
 *  - gateway - string - gateway name;
 *  - to_account - string - account name to receive payment;
 *  - amount - string - payment amount;
 *  - id - string - item short id;
 *  - internal_gateway - string - internal name of implementation class (without "Gateway" suffix);
 *  - title - string - payment title;
 *  - currency - string - currency code.
 *
 * @return void
 */
	public function manual() {
		$payment = CakeSession::read('ManualPayment');

		if(!$payment || empty($payment) || empty($payment['gateway']) || empty($payment['currency'])
		   || empty($payment['to_account']) || empty($payment['amount'])) {
			throw new InternalErrorException(__d('exception', 'Payment data not set'));
		}

		$short_id = $this->getShortId($payment['id']);

		if(empty($short_id)) {
			throw new InternalErrorException(__d('exception', 'Invalid short id'));
		}

		if($this->Auth->loggedIn()) {
			$user = $this->UserPanel->getData();
			$this->set(compact('user'));
		}

		if($this->request->is('post')) {
			$deposit = array(
				'user_id' => $this->Auth->loggedIn() ? $user['User']['id'] : null,
				'gateway' => $payment['internal_gateway'],
				'amount' => (string)$payment['amount'],
				'item' => $short_id['item_id'],
				'date' => date('Y-m-d H:i:s'),
				'status' => PaymentStatus::PENDING,
			);

			$this->Deposit->create();

			if(!$this->Deposit->save($deposit)) {
				$this->Notice->error(__('Failed to save payment. Please, try again.'));
			}

			CakeSession::delete('ManualPayment');
			return $this->redirect(array('action' => 'success', $payment['title']));
		}

		if($short_id['check_amount']) {
			$payment['amount'] = $short_id['check_amount'];
		}

		$this->set('breadcrumbTitle', __('Manual payment'));
		$this->set(compact('user', 'payment'));
	}


/**
 * message
 *
 * @return void
 */
	public function message($message = null) {
		if(!$message || empty($message)) {
			throw new InternalErrorException(__d('exception', 'PaymentMessage not set'));
		}

		$this->set('user', $this->UserPanel->getData());
		$this->set('breadcrumbTitle', __('Payment message'));
		$this->set(compact('message'));
	}

/**
 * xbt
 *
 * @return void
 */
	public function xbt() {
		$data = $this->Session->read('XBT');

		$this->Session->delete('XBT');

		if(!$data || empty($data) || !$data['addr'] || empty($data['addr'])) {
			throw new InternalErrorException(__d('exception', 'Address not set'));
		}

		$url = 'bitcoin:'.$data['addr'];

		if(isset($data['amount']) && !empty($data['amount'])) {
			$url .= '?amount='.urlencode($data['amount']);
		}

		if(isset($data['label']) && !empty($data['label'])) {
			$url .= '&label='.urlencode($data['amount']);
		}

		if($this->Auth->loggedIn()) {
			$this->set('user', $this->UserPanel->getData());
		}
		$this->set('breadcrumbTitle', __('Bitcoin Payment'));
		$this->set(compact('url', 'data'));
	}

/**
 * beforeFilter callback
 *
 * Allow not logged run 'index', 'cancel' and 'success' actions
 * 
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('index', 'success', 'cancel', 'paymentRedirect', 'manual', 'xbt'));
		$this->Security->unlockedActions = array(
			'index',
			'cancel',
			'success',
		);
	}
}
