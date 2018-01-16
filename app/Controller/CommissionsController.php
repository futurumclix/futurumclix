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
/**
 * Commissions Controller
 *
 */
class CommissionsController extends AppController {
/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		'Session',
		'Payments',
		'UserPanel',
	);

/**
 * Models
 *
 * @var array
 */
	public $uses = array(
		'Commission',
		'Settings',
	);

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$user = $this->UserPanel->getData();
		$this->Paginator->settings = array('limit' => $user['ActiveMembership']['Membership']['results_per_page']);
		$this->Commission->bindTitle();
		$this->Commission->recursive = 1;
		$this->paginate = array('order' => 'Deposit.date DESC');
		$this->set('commissions', $this->Paginator->paginate(array(
			'Commission.upline_id' => $this->Auth->user('id'),
		)));
		$this->set(compact('user'));
		$this->set('breadcrumbTitle', __('Referral\'s commissions'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$conditions = $this->createPaginatorConditions(array(
			'Upline.username',
			'Referral.username',
		));

		if(isset($conditions['Deposit.name'])) {
			$conditions['(SUBSTRING_INDEX(`Deposit`.`item`, \'-\', 1))'] = $conditions['Deposit.name'];
			unset($conditions['Deposit.name']);
		}

		$statuses = $this->Commission->getStatuses();
		$availableItems = array();
		foreach(PaymentsComponent::$items as $k => $v) {
			$availableItems[$k] = __d('admin', $v['admin_title']);
		}
		$this->Commission->bindTitle();

		$inCollapse = array(
			'Deposit.date >=',
			'Deposit.date <=',
			'Commission.status',
		);

		$inCollapse = array_intersect_key($conditions, array_flip($inCollapse));
		if(!empty($inCollapse)) {
			$this->set('searchCollapse', 'in');
		} else {
			$this->set('searchCollapse', '');
		}

		$this->paginate = array('order' => 'Deposit.date DESC');
		$this->Commission->recursive = 1;
		$this->set('commissions', $this->Paginator->paginate($conditions));
		$this->set(compact('availableItems', 'statuses'));
	}

/**
 * admin_credit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_credit($id = null) {
		$this->Commission->id = $id;
		if(!$this->Commission->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid commission'));
		}
		$this->request->allowMethod('post', 'delete');
		$settings = $this->Settings->fetch('commissionTo');
		if($this->Commission->credit($id, $settings['Settings']['commissionTo'])) {
			$this->Notice->success(__d('admin', 'The commission has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The commission could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null, $cancel = false) {
		$this->request->allowMethod('post', 'delete');

		$this->Commission->id = $id;
		if(!$this->Commission->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid commission'));
		}

		if($cancel) {
			$commissionTo = $this->Settings->fetchOne('commissionTo');
			if(!$this->Commission->cancel($commissionTo)) {
				$this->Notice->error(__d('admin', 'Failed to cancel commission. Please, try again.'));
				return $this->redirect(array('action' => 'index'));
			}
		}

		if($this->Commission->delete()) {
			$this->Notice->success(__d('admin', 'The commission has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The commission could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_massaction method
 *
 * @throws NotFoundException
 * @return void
 */
	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {
			if(!isset($this->request->data['Commission']) || empty($this->request->data['Commission'])) {
				$this->Notice->error(__d('admin', 'Please select at least one commission.'));
				return $this->redirect(array('action' => 'index'));
			}

			foreach($this->request->data['Commission'] as $id => $on) {
				if($on && !$this->Commission->exists($id)) {
					throw new NotFoundException(__d('exception', 'Invalid commission'));
				}
			}
			$commissions = 0;
			$settings = $this->Settings->fetch('commissionTo');
			foreach($this->request->data['Commission'] as $id => $on) {
				if($on) {
					$commissions++;
					switch($this->request->data['Action']) {
						case 'credit':
							$this->Commission->credit($id, $settings['Settings']['commissionTo']);
						break;

						case 'delete':
							$this->Commission->delete($id);
						break;

						case 'deleteAndCancel':
							$this->Commission->id = $id;
							$this->Commission->cancel($settings['Settings']['commissionTo']);
							$this->Commission->delete();
						break;
					}
				}
			}
			if($commissions) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one commission.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect(array('action' => 'index'));
	}
}
