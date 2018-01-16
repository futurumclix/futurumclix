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

/**
 * Memberships Controller
 *
 * @property Membership $Membership
 * @property PaginatorComponent $Paginator
 */
class MembershipsController extends AppController {
/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'Membership',
		'MembershipsUser',
		'User',
		'Settings'
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		'Payments',
		'UserPanel'
	);

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		if($this->request->params['action'] == 'admin_edit') {
			if(isset($this->request->data['Membership']['id'])) {
				$start = $this->Membership->RentedReferralsPrice->find('count', array(
					'conditions' => array(
						'RentedReferralsPrice.membership_id' => $this->request->data['Membership']['id'],
					),
				));
			} else {
				$start = 0;
			}

			if(isset($this->request->data['RentedReferralsPrice'])) {
				$stop = count($this->request->data['RentedReferralsPrice']);
			} else {
				$stop = 150;
			}

			for(; $start < $stop; $start++) {
				$this->Security->unlockedFields[] = 'RentedReferralsPrice.'.$start.'.autopay_price';
				$this->Security->unlockedFields[] = 'RentedReferralsPrice.'.$start.'.min';
				$this->Security->unlockedFields[] = 'RentedReferralsPrice.'.$start.'.max';
				$this->Security->unlockedFields[] = 'RentedReferralsPrice.'.$start.'.price';
			}
		}
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Membership->recursive = -1;

		$memberships = $this->Membership->find('all', array(
			'conditions' => array(
				'OR' => array(
					array('Membership.status' => 'Active'),
					array('Membership.status' => 'Default'),
				),
			),
		));

		foreach($memberships as &$membership) {
			if($membership['Membership']['1_month_active']) {
				$membership['Membership']['duration_select_data']['1_month'] = __('1 month - ').CurrencyFormatter::format($membership['Membership']['1_month_price']);
			}
			for($i = 2; $i <= 12; ++$i) {
				if($membership['Membership']["{$i}_months_active"]) {
					$membership['Membership']['duration_select_data']["{$i}_months"] = __('%d months - ', $i).CurrencyFormatter::format($membership['Membership']["{$i}_months_price"]);
				}
			}
			if(strpos($membership['Membership']['minimum_cashout'], ',') !== false) {
				$d = explode(',', $membership['Membership']['minimum_cashout']);
				$d2 = array();

				foreach($d as $v) {
					$d2[] = CurrencyFormatter::format($v);
				}

				$membership['Membership']['minimum_cashout'] = implode(', ', $d2);
			} elseif(!empty($membership['Membership']['minimum_cashout'])) {
				$membership['Membership']['minimum_cashout'] = CurrencyFormatter::format($membership['Membership']['minimum_cashout']);
			} else {
				$membership['Membership']['minimum_cashout'] = __('Unlimited');
			}
		}

		$this->set('memberships', $memberships);

		$this->User->id = $this->Auth->user('id');
		$this->User->contain(array(
			'ActiveMembership.Membership',
		));
		$this->User->read();
		$this->set('user', $this->User->data);
		$this->set('breadcrumbTitle', __('Upgrade your account'));
	}

/**
 * buy method
 *
 * @return void
 */
	public function buy($id = null, $duration = null) {
		$allowPBalance = $this->Settings->fetchOne('allowUpgradeFromPBalance', false);
		$months = explode('_', $duration)[0];

		if(!$this->Membership->durationExists($duration)) {
			throw new NotFoundException(__d('exception', 'Invalid duration'));
		}
		if(!$this->Membership->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid membership'));
		}
		$toBuy = $this->Membership->find('first', array(
			'fields' => array('id', 'name', $duration.'_active as active', $duration.'_price as price', 'status'),
			'conditions' => array('Membership.id' => $id),
			'recursive' => -1,
		));
		if($toBuy['Membership']['status'] != 'Active') {
			throw new NotFoundException(__d('exception', 'Membership not available'));
		}
		if($toBuy['Membership']['active'] != true) {
			throw new NotFoundException(__d('exception', 'Duration not available'));
		}

		if($this->request->is('post') && isset($this->request->data['gateway'])) {
			if(!$this->Payments->checkMinimumDepositAmount($this->request->data['gateway'], $toBuy['Membership']['price'])) {
				$this->Notice->error(__('Minimum deposit amount not exceeded'));
			} else {
				if(!$allowPBalance && $this->request->data['gateway'] == 'PurchaseBalance') {
					/* cheater? */
					$this->Notice->error(__('Upgrading from Purchase Balance is not allowed'));
				} else {
					$this->Payments->pay('membership', $this->request->data['gateway'], $toBuy['Membership']['price'], $this->Auth->user('id'), array(
						'membership_id' => $toBuy['Membership']['id'],
						'membership_name' => $toBuy['Membership']['name'],
						'duration' => $months,
					), array('plugin' => null, 'controller' => 'users', 'action' => 'dashboard'));
				}
			}
		}

		$activeGateways = $this->Payments->getActiveDepositsHumanized();

		if(!$allowPBalance) {
			unset($activeGateways['PurchaseBalance']);
		}

		$prices = array();
		foreach($activeGateways as $k => $v) {
			$price = bcadd($toBuy['Membership']['price'], $this->Payments->getDepositFee($toBuy['Membership']['price'], $k));

			if($this->Payments->checkMinimumDepositAmount($k, $price)) {
				$prices[$k] = $price;
			} else {
				unset($activeGateways[$k]);
			}
		}

		$this->set('user', $this->UserPanel->getData());
		$this->set('breadcrumbTitle', __('Buy membership'));
		$this->set(compact('activeGateways', 'prices', 'toBuy', 'duration'));
	}

/**
 * degrade method
 *
 * @return void
 */
	public function degrade() {
		$this->request->allowMethod('post', 'put');
		if($this->MembershipsUser->deleteLast($this->Auth->user('id'))) {
			$this->Notice->success('Last bought membership deleted.');
		} else {
			$this->Notice->error('You cannot degrade from default membership.');
		}
		$this->redirect($this->referer());
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->Membership->recursive = -1;
		$this->paginate = array('order' => 'Membership.id DESC');
		$this->set('memberships', $this->Paginator->paginate());
	}

/**
 * admin_massaction method
 *
 * @throws NotFoundException
 * @return void
 */
	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {
			$memberships = 0;
			foreach($this->request->data['Memberships'] as $id => $on) {
				if($on) {
					$memberships++;
					if(!$this->Membership->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid membership'));
					}
					if($this->Membership->isDefault($id)) {
						$this->Notice->error(__d('admin', 'Default membership cannot be modified by Mass Action.'));
						return $this->redirect($this->referer());
					}
				}
			}
			foreach($this->request->data['Memberships'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'delete':
							$this->Membership->delete($id);
						break;
						
						case 'activate':
							$this->Membership->activate($id);
						break;
						
						case 'disable':
							$this->Membership->disable($id);
						break;
					}
				}
			}
			if($memberships) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select memberships.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select an action.'));
		}
		$this->redirect($this->referer());
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if($this->request->is('post')) {
			$this->request->data['Membership']['status'] = 'Disabled';
			$this->Membership->create();
			if($this->Membership->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'The membership has been created.'));
				return $this->redirect(array('action' => 'edit', $this->Membership->id));
			} else {
				$this->Notice->error(__d('admin', 'The membership could not be saved. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->Membership->recursive = -1;
		$this->Membership->id = $id;
		if(!$this->Membership->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid membership'));
		}
		if($this->request->is(array('post', 'put'))) {
			$save = true;
			if(isset($this->request->data['RentedReferralsPrice'])) {
				foreach($this->request->data['RentedReferralsPrice'] as &$price) {
					if($price['max'] == -1) {
						$price['max'] = 65535;
					}
					$price['membership_id'] = $id;
				}
				if(($reason = $this->Membership->RentedReferralsPrice->validateArray($this->request->data['RentedReferralsPrice'])) !== true) {
					$this->Notice->error($reason);
					$save = false;
				}
			}
			if($save && $this->Membership->saveAll($this->request->data)) {
				$this->Notice->success(__d('admin', 'The membership has been saved.'));
			} else {
				$options = array('fields' => array('status'), 'conditions' => array('Membership.' . $this->Membership->primaryKey => $id));
				$this->request->data['Membership']['status'] = $this->Membership->find('first', $options)['Membership']['status'];
				$this->Notice->error(__d('admin', 'The membership could not be saved. Please, try again.'));
			}
		}
		$this->Membership->recursive = 1;
		$membership = $this->Membership->findById($id);
		$this->request->data = Hash::filter(Hash::merge($membership, $this->request->data));

		$availableItems = array();
		foreach(PaymentsComponent::$items as $k => $v) {
			if(isset($v['commission']) && $v['commission']) {
				$availableItems[$k] = __d('admin', $v['admin_title']);
			}
		}

		$this->set(compact('availableItems'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if(!$this->Membership->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid membership'));
		}
		$this->request->allowMethod('post', 'delete');
		if(!$this->Membership->isDefault($id)) {
			if($this->Membership->delete($id)) {
				$this->Notice->success(__d('admin', 'The membership has been deleted.'));
			} else {
				$this->Notice->error(__d('admin', 'The membership could not be deleted. Please, try again.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Default membership cannot be deleted'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_disable method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_disable($id = null) {
		if(!$this->Membership->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid membership'));
		}
		$this->request->allowMethod('post', 'delete');
		if(!$this->Membership->isDefault($id)) {
			if($this->Membership->disable($id)) {
				$this->Notice->success(__d('admin', 'The membership has been disabled.'));
			} else {
				$this->Notice->error(__d('admin', 'The membership could not be disabled. Please, try again.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Default membership cannot be disabled'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_activate method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_activate($id = null) {
		if(!$this->Membership->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid membership'));
		}
		$this->request->allowMethod('post', 'delete');
		if(!$this->Membership->isDefault($id)) {
			if($this->Membership->hasPeriod($id)) {
				if($this->Membership->activate($id)) {
					$this->Notice->success(__d('admin', 'The membership has been activated.'));
				} else {
					$this->Notice->error(__d('admin', 'The membership could not be activated. Please, try again.'));
				}
			} else {
				$this->Notice->error(__d('admin', 'Enable at least one period before activation of membership.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Default membership cannot be activated.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
