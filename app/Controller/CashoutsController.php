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
App::uses('PaymentsInflector', 'Payments');

/**
 * Cashouts Controller
 *
 * @property Cashout $Cashout
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class CashoutsController extends AppController {
/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		'Session',
		'Payments',
		'UserPanel'
	);

/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'Cashout',
		'Settings',
	);

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('payment_proofs'));
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$user = $this->UserPanel->getData();

		$this->Paginator->settings = array(
			'limit' => $user['ActiveMembership']['Membership']['results_per_page'],
			'order' => 'Cashout.created DESC',
		);

		$this->Cashout->recursive = 1;
		$cashouts = $this->Paginator->paginate(array(
			'Cashout.user_id' => $this->Auth->User('id'),
		));

		$this->set(compact('cashouts', 'user'));
		$this->set('breadcrumbTitle', __('Cashout History'));
	}

/**
 * payment_proofs method
 *
 * @return void
 */
	public function payment_proofs() {
		if($this->Auth->loggedIn()) {
			$user = $this->UserPanel->getData();
			$limit = $user['ActiveMembership']['Membership']['results_per_page'];
		} else {
			$limit = 100;
		}

		$this->Paginator->settings = array(
			'limit' => $limit,
			'order' => 'Cashout.created DESC',
		);

		$this->Cashout->recursive = 1;
		$cashouts = $this->Paginator->paginate(array(
			'status' => 'Completed',
		));

		$this->set(compact('cashouts', 'user'));
		$this->set('breadcrumbTitle', __('Cashout History'));
	}


/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$conditions = $this->createPaginatorConditions(array(
			'User.username'
		));

		$inCollapse = array(
			'Cashout.created <=',
			'Cashout.created >=',
			'Cashout.gateway',
		);

		if(count(array_intersect_key($conditions, array_flip($inCollapse))) != 0) {
			$this->set('searchCollapse', 'in');
		} else {
			$this->set('searchCollapse', '');
		}

		$gt = $this->Cashout->find('list', array(
			'fields' => 'Cashout.gateway',
			'group' => 'Cashout.gateway',
			'order' => 'Cashout.gateway',
		));

		$gateways = array();

		foreach($gt as $v) {
			$gateways[$v] = PaymentsInflector::humanize(PaymentsInflector::underscore($v));
		}

		$limit = $this->Cashout->find('count');
		$limit = $limit <= 0 ? 1 : $limit;

		$this->paginate = array(
			'limit' => $limit,
			'conditions' => $conditions,
			'order' => 'id DESC',
		);

		$this->Cashout->contain(array(
			'User' => array(
				'username',
				'location',
			)
		));
		$this->set('cashouts', $this->Paginator->paginate());
		$this->set('memberships', $this->Cashout->User->MembershipsUser->Membership->getList());
		$this->set('statuses', $this->Cashout->getStatuses());
		$this->set(compact('gateways'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null, $redirect = true) {
		$this->Cashout->id = $id;
		if(!$this->Cashout->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid cashout'));
		}
		$this->request->allowMethod('post', 'delete');
		if($this->Cashout->delete()) {
			$this->Notice->success(__d('admin', 'The cashout has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The cashout could not be deleted. Please, try again.'));
		}
		if($redirect) {
			return $this->redirect($this->referer());
		}
	}


/**
 * admin_mark method
 *
 * @return void
 */
	public function admin_mark($id = null, $status = null, $redirect = true, $onlyStatus = false) {
		$statuses = array_flip($this->Cashout->getStatuses());
		if(!in_array($status, $statuses)) {
			throw new NotFoundException(__d('exception', 'Invalid status'));
		}

		$this->Cashout->id = $id;

		if(!$this->Cashout->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid cashout'));
		}

		$this->Cashout->contain();
		$this->Cashout->read();

		if($this->Cashout->data['Cashout']['status'] != $status) {
			$cashout = $this->Cashout->data;
			$this->Cashout->data['Cashout']['status'] = $status;

			if($this->Cashout->save()) {
				if(!$onlyStatus) {
					if($status == 'Cancelled') {
						$refund = bcadd($cashout['Cashout']['amount'], $cashout['Cashout']['fee']);

						if(!$this->Cashout->User->accountBalanceAdd($refund, $cashout['Cashout']['user_id'])) {
							throw new InternalErrorException(__d('exception', 'Failed to refund'));
						}

						if($cashout['Cashout']['status'] == 'Completed') {
							if(!$this->Cashout->User->UserStatistic->cancelCashout($cashout['Cashout']['user_id'],
							 $cashout['Cashout']['gateway'], $cashout['Cashout']['amount'])) {
								throw new InternalErrorException(__d('exception', 'Failed to update statistics'));
							}

							if(!$this->Settings->cancelCashout($cashout['Cashout']['amount'])) {
								throw new InternalErrorException(__d('exception', 'Failed to update statistics'));
							}
						}

						$this->Cashout->User->contain();
						$user = $this->Cashout->User->findById($cashout['Cashout']['user_id']);

						if($user['User']['role'] == 'Active' && $user['User']['allow_emails']) {
							$email = ClassRegistry::init('Email');
							$email->setVariables(array(
								'%username%' => $user['User']['username'],
								'%firstname%' => $user['User']['first_name'],
								'%lastname%' => $user['User']['last_name'],
								'%amount%' => $cashout['Cashout']['amount'],
								'%gateway%' => PaymentsInflector::humanize($cashout['Cashout']['gateway']),
							));

							$email->send('Cancelled cashout', $user['User']['email']);
						}
					} else if($status == 'Completed') {
						if(!$this->Cashout->User->UserStatistic->newCashout($cashout['Cashout']['user_id'],
						 $cashout['Cashout']['gateway'], $cashout['Cashout']['amount'])) {
							throw new InternalErrorException(__d('exception', 'Failed to update statistics'));
						}
						if(!$this->Settings->newCashout($cashout['Cashout']['amount'])) {
							throw new InternalErrorException(__d('exception', 'Failed to update statistics'));
						}
					}
				}
				$this->Notice->success(__d('admin', 'Cashout status changed'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to change cashout status'));
			}
		}

		if($redirect) {
			return $this->redirect($this->referer());
		}
	}

/**
 * admin_cashout method
 *
 * @return void
 */
	public function admin_cashout($id = null, $redirect = true) {
		$this->Cashout->id = $id;

		if(!$this->Cashout->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid cashout'));
		}

		$this->Cashout->contain();
		$this->Cashout->read();

		$cashout = $this->Cashout->data;

		if($this->Cashout->data['Cashout']['status'] != 'Completed') {
			$cashout['Cashout']['status'] = $this->Cashout->data['Cashout']['status'] = 'Pending';
			if(!$this->Cashout->save()) {
				throw new InternalErrorException(__d('exception', 'Failed to change cashout status'));
			}
			try
			{
				$status = $this->Payments->cashout($cashout);

				if($status === false || $status == 'Failed') {
					throw new InternalErrorException(__d('exception', 'Cashout call result == false or "Failed"'));
				}

				$this->Cashout->id = $cashout['Cashout']['id'];

				if(!$this->Cashout->saveField('status', $status)) {
					throw new InternalErrorException(__d('exception', 'Failed to change cashout status'));
				}

				$this->Notice->success(__d('admin', 'Cashout request successfully send to gateway.'));
			} catch(Exception $e) {
				$msg = __d('admin', 'Error while sending cashout request to gateway.');

				$set = $this->Settings->fetchOne('autoCashoutFail', 'failed');

				switch($set) {
					case 'failed':
						$this->Cashout->data['Cashout']['status'] = 'Failed';
					break;

					case 'new':
						$this->Cashout->data['Cashout']['status'] = 'New';
					break;

					case 'cancelled':
						if($this->Cashout->User->accountBalanceAdd(bcadd($cashout['Cashout']['amount'], $cashout['Cashout']['fee']), $cashout['Cashout']['user_id'])) {
							$msg .= ' '.__d('admin', 'Funds refunded to user.');
						} else {
							$msg .= ' '.__d('admin', 'Failed to refund funds to user.');
						}
						$this->Cashout->data['Cashout']['status'] = 'Cancelled';
					break;
				}

				if(!$this->Cashout->save()) {
					throw new InternalErrorException(__d('exception', 'Error while sending request to gateway and failed to change cashout status'));
				}

				$this->Notice->error($msg);
			}
		} else {
			$this->Notice->error(__d('admin', 'Cashout already completed.'));
		}

		if($redirect) {
			$this->redirect($this->referer());
		}
	}

/**
 * _admin_createList method
 *
 * @return CakeResponse
 */
	private function _admin_createList() {
		$cashouts = array();
		foreach($this->request->data['Cashout'] as $id => $on) {
			if($on) {
				if(!$this->Cashout->exists($id)) {
					throw new NotFoundException(__d('exception', 'Invalid cashout'));
				}
				$cashouts[] = $id;
			}
		}

		$this->Cashout->contain();
		$cashouts = $this->Cashout->find('all', array(
			'conditions' => array(
				'Cashout.id' => $cashouts,
				'Cashout.status' => 'New',
			),
			'order' => 'Cashout.gateway',
		));

		$cashouts = Hash::combine($cashouts, '{n}.Cashout.id', '{n}.Cashout', '{n}.Cashout.gateway');

		$list = $this->Payments->generatePaymentsLists($cashouts);

		if(count($list) > 1) {
			$zip = new ZipArchive();
			if($zip->open(TMP.'masspay.zip', ZipArchive::CREATE || ZipArchive::OVERWRITE) !== true) {
				throw new InternalErrorException(__d('exception', 'Failed to create zip file'));
			}
			foreach($list as $k => $v) {
				$zip->addFromString($k.'.txt', $v);
			}
			$zip->close();
			$this->response->file(TMP.'masspay.zip');
			$this->response->type('zip');
			$this->response->download('masspay-'.date('Y-m-d').'.zip');
		} else {
			$this->response->body(reset($list));
			$this->response->type('txt');
			$this->response->download(key($list).date('Y-m-d').'.txt');
		}
		return $this->response;
	}

/**
 * admin_massaction method
 *
 * @throws NotFoundException
 * @return void
 */
	public function admin_massaction() {
		if(isset($this->request->data['Action']) && !empty($this->request->data['Action'])) {
			if(!isset($this->request->data['Cashout']) || empty($this->request->data['Cashout'])) {
				$this->Notice->error(__d('admin', 'Please select at least one cashout.'));
				return $this->redirect($this->referer());
			}
			$cashouts = 0;
			if($this->request->data['Action'] == 'list') {
				return $this->_admin_createList();
			} else {
				foreach($this->request->data['Cashout'] as $id => $on) {
					if($on) {
						$cashouts++;
						switch($this->request->data['Action']) {
							case 'delete':
								$this->Cashout->delete($id, false);
							break;

							case 'cancel':
								$this->admin_mark($id, 'Cancelled', false);
							break;

							case 'markPaid':
								$this->admin_mark($id, 'Completed', false);
							break;

							case 'markUnpaid':
								$this->admin_mark($id, 'New', false);
							break;

							case 'cashout':
								$this->admin_cashout($id, false);
							break;
						}
					}
				}
			}
			if($cashouts) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one cashout.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect($this->referer());
	}
}
