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
App::uses('Sanitize', 'Utility');
App::uses('PaymentStatus', 'Payments');
App::uses('PaymentsInflector', 'Payments');
/**
 * Deposits Controller
 *
 * @property Deposit $Deposit
 * @property Settings $Settings
 * @property User $User
 * @property PaginatorComponent $Paginator
 * @property PaymentsComponent $Payments
 */
class DepositsController extends AppController {
/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'Deposit',
		'Settings',
		'User',
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		'Payments',
		'UserPanel',
	);

/**
 * show method
 *
 * @return void
 */
	public function show($id = null) {
		if($id === null) {
			throw new NotFoundException(__d('exception', 'Invalid id'));
		}

		$this->Deposit->bindTitle();
		$this->Deposit->recursive = 1;
		$deposit = $this->Deposit->find('first', array(
			'conditions' => array(
				'Deposit.id' => $id,
				'Deposit.user_id' => $this->Auth->user('id'),
			),
		));
		$this->Deposit->unbindTitle();

		if(empty($deposit)) {
			throw new NotFoundException(__d('exception', 'Invalid id'));
		}

		$user = $this->UserPanel->getData();
		$this->set('breadcrumbTitle', __('Payment details'));
		$this->set(compact('deposit', 'user'));
	}

/**
 * admin_deposits method
 *
 * @return void
 */
	public function admin_deposits() {
		$conditions = $this->createPaginatorConditions(array(
			'User.username',
			'Deposit.account',
			'Deposit.gateway',
			'Deposit.gatewayid',
		));

		$inCollapse = array(
			'Deposit.begins',
			'Deposit.ends',
			'Deposit.gatewayid LIKE',
		);

		$inCollapse = array_intersect_key($conditions, array_flip($inCollapse));
		if(!empty($inCollapse)) {
			$this->set('searchCollapse', 'in');
		} else {
			$this->set('searchCollapse', '');
		}

		if(isset($conditions['Deposit.begins'])) {
			$conditions['Deposit.date >='] = $conditions['Deposit.begins'];
			unset($conditions['Deposit.begins']);
		}

		if(isset($conditions['Deposit.ends'])) {
			$conditions['Deposit.date <='] = $conditions['Deposit.ends'];
			unset($conditions['Deposit.ends']);
		}

		$conditions['Deposit.item LIKE'] = 'deposit%';

		$this->Deposit->bindDepositAmount();

		$this->paginate = array(
			'order' => 'Deposit.date DESC',
			'fields' => array(
				'Deposit.id',
				'Deposit.user_id',
				'Deposit.gateway',
				'Deposit.account',
				'Deposit.amount',
				'Deposit.status',
				'Deposit.gatewayid',
				'Deposit.date',
				'Deposit.deposit_amount',
				'User.id',
				'User.username',
			),
		);
		$this->Deposit->recursive = 1;
		$deposits = $this->Paginator->paginate($conditions);
		$this->Deposit->unbindVirtualFields();

		foreach($deposits as &$d) {
			$d['Deposit']['refunds'] = $this->Payments->supportsRefunds($d['Deposit']['gateway']);
		}

		$gt = $this->Deposit->find('list', array(
			'fields' => array('Deposit.gateway', 'Deposit.gateway'),
			'group' => 'Deposit.gateway',
			'order' => 'Deposit.gateway',
		));

		$gateways = array();

		foreach($gt as $k => $v) {
			$gateways[$k] = PaymentsInflector::humanize(PaymentsInflector::underscore($v));
		}

		$this->set(compact('gateways', 'deposits'));
	}

/**
 * admin_purchases method
 *
 * @return void
 */
	public function admin_purchases() {
		$this->Deposit->bindTitle();

		$conditions = $this->createPaginatorConditions(array(
			'User.username',
			'Deposit.account',
			'Deposit.gateway',
			'Deposit.gatewayid',
			'Deposit.item',
		));

		$inCollapse = array(
			'Deposit.begins',
			'Deposit.ends',
			'Deposit.gatewayid LIKE',
			'Deposit.item LIKE',
		);

		$inCollapse = array_intersect_key($conditions, array_flip($inCollapse));
		if(!empty($inCollapse)) {
			$this->set('searchCollapse', 'in');
		} else {
			$this->set('searchCollapse', '');
		}

		if(isset($conditions['Deposit.begins'])) {
			$conditions['Deposit.date >='] = $conditions['Deposit.begins'];
			unset($conditions['Deposit.begins']);
		}

		if(isset($conditions['Deposit.ends'])) {
			$conditions['Deposit.date <='] = $conditions['Deposit.ends'];
			unset($conditions['Deposit.ends']);
		}

		$conditions['Deposit.item NOT LIKE'] = 'deposit%';

		$this->Deposit->recursive = 1;
		$deposits = $this->Paginator->paginate($conditions);

		$this->Deposit->unbindTitle();

		foreach($deposits as &$d) {
			$d['Deposit']['refunds'] = $this->Payments->supportsRefunds($d['Deposit']['gateway']);
		}

		$gt = $this->Deposit->find('list', array(
			'fields' => array('Deposit.gateway', 'Deposit.gateway'),
			'group' => 'Deposit.gateway',
			'order' => 'Deposit.gateway',
		));

		$gateways = array();

		foreach($gt as $k => $v) {
			$gateways[$k] = PaymentsInflector::humanize(PaymentsInflector::underscore($v));
		}

		$availableItems = array();
		foreach(PaymentsComponent::$items as $k => $v) {
			if(isset($v['commission']) && $v['commission']) {
				$availableItems[$k] = __d('admin', $v['admin_title']);
			}
		}
		unset($availableItems['deposit']);

		$this->set(compact('gateways', 'deposits', 'availableItems'));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$gateways = $this->Payments->getActiveDepositsHumanized();

		$refl = new ReflectionClass('PaymentStatus');
		$statuses = $refl->getConstants();

		if($this->request->is('post')) {
			$this->User->contain(array());
			$user = $this->User->findByUsername($this->request->data['Deposit']['username'], array('User.id'));

			if(!empty($user)) {
				unset($this->request->data['Deposit']['username']);
				$this->request->data['Deposit']['user_id'] = $user['User']['id'];

				if(isset($this->request->data['Deposit']['status'])) {
					$this->request->data['Deposit']['status'] = $statuses[$this->request->data['Deposit']['status']];
				}

				$this->request->data['Deposit']['item'] = $this->Payments->createId('deposit', array(
					'user_id' => $this->request->data['Deposit']['user_id'],
					'amount' => $this->request->data['Deposit']['amount'],
				));

				$this->Deposit->create();
				if($this->Deposit->save($this->request->data)) {
					$this->Notice->success(__d('admin', 'Save succesfull.'));

					return $this->redirect(array('action' => 'deposits'));
				} else {
					$this->Notice->error(__d('admin', 'Error while saving. Please, try again.'));
				}
			} else {
				$this->Notice->error(__d('admin', 'Error while saving. User does not exist.'));
			}
		}
		$users = $this->Deposit->User->find('list');
		$this->set(compact('users', 'gateways', 'statuses'));
	}

/**
 * admin_edit method
 *
 * @return void
 */
	public function admin_edit($id = null) {
		$this->Deposit->recurisve = -1;
		$deposit = $this->Deposit->findById($id);

		if(empty($deposit)) {
			throw new NotFoundException(__d('exception', 'Invalid id'));
		}

		if($deposit['Deposit']['user_id']) {
			$this->User->contain();
			$user = $this->User->findById($deposit['Deposit']['user_id'], array('username'));
			$deposit['User']['username'] = $user['User']['username'];
		}

		$gateways = $this->Payments->getActiveDepositsHumanized();

		$refl = new ReflectionClass('PaymentStatus');
		$statuses = $refl->getConstants();
		$statuses = array_combine($statuses, $statuses);

		if($this->request->is(array('post', 'put'))) {
			$save = false;

			if(!empty($this->request->data['User']['username'])) {
				$this->User->contain(array());
				$user = $this->User->findByUsername($this->request->data['User']['username'], array('User.id'));

				if(!empty($user)) {
					unset($this->request->data['Deposit']['username']);
					$this->request->data['Deposit']['user_id'] = $user['User']['id'];
					$save = true;
				}
			} else {
				$this->request->data['Deposit']['user_id'] = null;
				$save = true;
			}
			$this->Deposit->id = $id;
			if($save && $this->Deposit->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'Deposit saved successfully'));
				return $this->redirect($this->referer());
			} else {
				$this->Notice->error(__d('admin', 'Failed to save deposit. Please, try again.'));
			}
		} else {
			$this->request->data = $deposit;
		}

		$this->set(compact('gateways', 'statuses'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->request->allowMethod('post', 'delete');

		$this->Deposit->id = $id;
		if(!$this->Deposit->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid deposit'));
		}

		if($this->Deposit->delete()) {
			$this->Notice->success(__d('admin', 'The deposit has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The deposit could not be deleted. Please, try again.'));
		}
		return $this->redirect($this->referer());
	}

/**
 * admin_cancel method
 *
 * @throws InternalErrorException
 * @param string $id
 * @return void
 */
	public function admin_cancel($id = null, $delete = true) {
		$this->Deposit->id = $id;

		if(!$this->Deposit->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid deposit'));
		}

		$this->Deposit->read(array('item', 'amount', 'user_id'));
		$item = explode('-', $this->Deposit->data['Deposit']['item']);

		if($item[0] != 'deposit') {
			throw new InternalErrorException(__d('exception', 'Purchases cannot be cancelled.'));
		} else {
			$this->User->contain();
			$data = $this->User->findById($this->Deposit->data['Deposit']['user_id'], array('purchase_balance'));

			if(bccomp($data['User']['purchase_balance'], $item[1]) >= 0) {
				if(!$this->User->PurchaseBalanceSub($item[1], $this->Deposit->data['Deposit']['user_id'])) {
					throw new InternalErrorException(__d('exception', 'Failed to cancel deposit'));
				}
			} else {
				$this->Notice->error(__d('admin', 'User has insufficient purchase balance, failed to cancel deposit.'));
				return $this->redirect($this->referer());
			}

			$this->Deposit->Commission->recursive = -1;
			$this->Deposit->Commission->deleteAll(array(
				'Commission.status' => 'Pending',
				'Commission.deposit_id' => $id,
			));

			$this->Deposit->DepositBonus->recursive = -1;
			$bonus = $this->Deposit->DepositBonus->find('first', array(
				'conditions' => array(
					'deposit_id' => $id,
				),
			));

			if(!empty($bonus)) {
				if(bccomp($data['User']['purchase_balance'], $bonus['DepositBonus']['amount']) >= 0) {
					if(!$this->Deposit->DepositBonus->cancel($bonus['DepositBonus']['id'], $bonus['DepositBonus']['user_id'], $bonus['DepositBonus']['amount'])) {
						throw new InternalErrorException(__d('exception', 'Failed to cancel deposit bonus'));
					}
					$this->Deposit->DepositBonus->delete($bonus['DepositBonus']['id']);
				} else {
					$this->Notice->info(__d('admin', 'Failed to cancel deposit bonus, user has insufficient purchase balance.'));
				}
			}

			$this->Settings->cancelDeposit($this->Deposit->data['Deposit']['amount']);
			if($delete) {
				if($this->Deposit->delete($id)) {
					$this->Notice->success(__d('admin', 'Deposit deleted successfully.'));
				} else {
					$this->Notice->error(__d('admin', 'Failed to delete deposit.'));
				}
			} else {
				$this->Notice->success(__d('admin', 'Deposit cancelled successfully.'));
			}
		}
		return $this->redirect($this->referer());
	}

/**
 * admin_refund method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_refund($id = null) {
		$this->request->allowMethod('post', 'delete');

		$this->Deposit->recursive = -1;
		$deposit = $this->Deposit->findById($id, array('gateway', 'gatewayid', 'item', 'amount', 'user_id', 'status'));
		if(empty($deposit)) {
			throw new NotFoundException(__d('exception', 'Invalid Deposit id'));
		}

		if($deposit['Deposit']['status'] == 'Refunded') {
			return $this->redirect($this->referer());
		}

		$this->User->contain();
		$data = $this->User->findById($deposit['Deposit']['user_id'], array('purchase_balance'));

		if(empty($data)) {
			return false;
		}

		$item = explode('-', $deposit['Deposit']['item']);
		if($item[0] == 'deposit') {
			if(bccomp($data['User']['purchase_balance'], $item[1]) >= 0) {
				if(!$this->User->PurchaseBalanceSub($item[1], $deposit['Deposit']['user_id'])) {
					throw new InternalErrorException(__d('exception', 'Failed to cancel deposit'));
				} else {
					$this->Notice->success(__d('admin', 'Deposit has been removed from user\'s account.'));
				}
			} else {
				$this->Notice->error(__d('admin', 'Sorry, user has insufficient purchase balance, refund cannot be done.'));
				return $this->redirect($this->referer());
			}
		}

		$this->Deposit->Commission->recursive = -1;
		$this->Deposit->Commission->updateAll(array(
			'Commission.status' => '"Cancelled"',
		), array(
			'Commission.status' => 'Pending',
			'Commission.deposit_id' => $id,
		));

		$this->Deposit->DepositBonus->recursive = -1;
		$bonus = $this->Deposit->DepositBonus->find('first', array(
			'conditions' => array(
				'deposit_id' => $id,
			),
		));

		if(!empty($bonus)) {
			if(bccomp($data['User']['purchase_balance'], $bonus['DepositBonus']['amount']) >= 0) {
				if(!$this->Deposit->DepositBonus->cancel($bonus['DepositBonus']['id'], $bonus['DepositBonus']['user_id'], $bonus['DepositBonus']['amount'])) {
					throw new InternalErrorException(__d('exception', 'Failed co cancel deposit bonus'));
				} else {
					$this->Notice->success(__d('admin', 'Deposit bonus has been removed from user\'s account.'));
				}
			} else {
				$this->Notice->info(__d('admin', 'Failed to cancel deposit bonus, user has insufficient purchase balance.'));
			}
		}

		$this->Settings->cancelDeposit($deposit['Deposit']['amount']);
		$this->Deposit->id = $id;
		$this->Deposit->saveField('status', PaymentStatus::REFUNDED);

		if($this->Payments->refund($deposit)) {
			$this->Notice->success(__d('admin', 'Refund request has been sent to gateway.'));
		} else {
			$this->Notice->error(__d('admin', 'There was an error when refund was requested from gateway. Please use gateway website to refund payment to user manually.'));
		}
		return $this->redirect($this->referer());
	}

/**
 * admin_accept method
 *
 * @return void
 */
	public function admin_accept($id = null) {
		$this->request->allowMethod(array('post', 'put'));

		$this->Deposit->recursive = -1;
		$deposit = $this->Deposit->findById($id, array('id', 'gateway', 'gatewayid', 'item', 'amount', 'user_id', 'status'));

		if(empty($deposit)) {
			throw new NotFoundException(__d('exception', 'Invalid Deposit id'));
		}

		if($deposit['Deposit']['status'] != PaymentStatus::PENDING) {
			throw new InternalErrorException(__d('exception', 'Only pending deposits are allowed'));
		}

		$this->Deposit->id = $id;
		$this->Deposit->set('status', PaymentStatus::SUCCESS);

		if(!$this->Deposit->save()) {
			$this->Notice->error(__d('admin', 'Failed to accept deposit.'));
			return $this->redirect($this->referer());
		}

		$this->Notice->success(__d('admin', 'Deposit accepted sucessfully.'));
		return $this->redirect($this->referer());
	}

/**
 * admin_massaction method
 *
 * @throws NotFoundException
 * @return void
 */
	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {

			if(!isset($this->request->data['Deposit']) || empty($this->request->data['Deposit'])) {
				$this->Notice->error(__d('admin', 'Please select at least one deposit.'));
				return $this->redirect($this->referer());
			}

			foreach($this->request->data['Deposit'] as $id => $on) {
				if($on && !$this->Deposit->exists($id)) {
					throw new NotFoundException(__d('exception', 'Invalid deposit'));
				}
			}
			$deposits = 0;
			foreach($this->request->data['Deposit'] as $id => $on) {
				if($on) {
					$deposits++;
					switch($this->request->data['Action']) {
						case 'delete':
							$this->Deposit->delete($id);
						break;

						case 'deleteAndCancel':
							$this->admin_cancel($id);
						break;

						case 'refund':
							$this->admin_refund($id);
						break;
					}
				}
			}
			if($deposits) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one entry.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect($this->referer());
	}
}
