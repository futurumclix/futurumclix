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
class RevenueShareController extends RevenueShareAppController {
	public $uses = array(
		'RevenueShare.RevenueSharePacket',
		'RevenueShare.RevenueShareLimit',
		'RevenueShare.RevenueShareOption',
	);

	public $components = array(
		'Paginator',
		'UserPanel',
		'Payments',
	);

	public $helpers = array(
		'Utility.Utility',
	);

	public function admin_index() {
		if($this->request->is(array('post', 'put'))) {
			debug($this->request->data);
			if(isset($this->request->data['RevenueShareSettings'])) {
				if($this->RevenueShareSettings->store($this->request->data, 'revenueShare')) {
					$this->Notice->success(__d('revenue_share_admin', 'Settings saved successfully.'));
				} else {
					$this->Notice->error(__d('revenue_share_admin', 'The settings could not be saved. Please, try again.'));
				}
			}

			if(isset($this->request->data['RevenueShareLimit'])) {
				if($this->RevenueShareLimit->saveMany($this->request->data['RevenueShareLimit'])) {
					$this->Notice->success(__d('revenue_share_admin', 'Settings saved successfully.'));
				} else {
					$this->Notice->error(__d('revenue_share_admin', 'The settings could not be saved. Please, try again.'));
				}
			}

			if(isset($this->request->data['RevenueShareOption'])) {
				if(isset($this->request->data['RevenueShareOption']['items']) && !empty($this->request->data['RevenueShareOption']['items'])) {
					foreach($this->request->data['RevenueShareOption']['items'] as $item) {
						list($model, $foreign_key) = explode('-', $item);
						$items[] = compact('model', 'foreign_key');
					}
					unset($this->request->data['RevenueShareOption']['items']);
				}

				$toSave = array('RevenueShareOption' => $this->request->data['RevenueShareOption'], 'Item' => $items);

				if($this->RevenueShareOption->saveAssociated($toSave)) {
					$this->Notice->success(__d('revenue_share_admin', 'Settings saved successfully.'));
					unset($this->request->data['RevenueShareOption']);
				} else {
					$this->Notice->error(__d('revenue_share_admin', 'The settings could not be saved. Please, try again.'));
				}
			}
		}

		$settings = $this->RevenueShareSettings->fetch('revenueShare');
		$this->request->data = Hash::merge($settings, $this->request->data);

		$this->RevenueShareLimit->contain();
		$limits = $this->RevenueShareLimit->find('all');
		$limits = Hash::combine($limits, '{n}.RevenueShareLimit.membership_id', '{n}.RevenueShareLimit');
		$this->request->data = Hash::merge(array('RevenueShareLimit' => $limits), $this->request->data);

		$this->RevenueShareOption->recursive = 1;
		$data = $this->RevenueShareOption->find('all');
		$options = array();
		foreach($data as &$v) {
			foreach($v['Item'] as $item) {
				$v['RevenueShareOption']['items'][] = $item['model'].'-'.$item['foreign_key'];
			}
			$options[$v['RevenueShareOption']['membership_id']][] = $v['RevenueShareOption'];
		}

		$this->paginate = array(
			'contain' => array('RevenueShareOption', 'User' => array('username', 'ActiveMembership' => array('Membership' => 'name'))),
		);
		$packets = $this->Paginator->paginate();

		$items = $this->RevenueShareOption->getAvailableItems();

		$memberships = ClassRegistry::init('Membership')->getList();

		$history = ClassRegistry::init('RevenueShare.RevenueShareHistory')->find('all', array(
			'recursive' => -1,
			'order' => 'created',
		));
		$income = Hash::combine($history, '{n}.RevenueShareHistory.created', '{n}.RevenueShareHistory.income');
		$outcome = Hash::combine($history, '{n}.RevenueShareHistory.created', '{n}.RevenueShareHistory.outcome');

		$this->set(compact('memberships', 'options', 'items', 'packets', 'income', 'outcome'));
	}

	public function admin_deleteOption($id = null) {
		$this->RevenueShareOption->id = $id;

		if(!$this->RevenueShareOption->exists()) {
			throw new NotFoundException(__d('revenue_share_admin', 'Invalid option id'));
		}

		if($this->RevenueShareOption->delete()) {
			$this->Notice->success(__d('revenue_share_admin', 'Option deleted successfully.'));
		} else {
			$this->Notice->error(__d('revenue_share_admin', 'Failed to delete option. Please, try again.'));
		}

		return $this->redirect($this->referer());
	}

	public function admin_deletePacket($id = null) {
		$this->RevenueSharePacket->id = $id;

		if(!$this->RevenueSharePacket->exists()) {
			throw new NotFoundException(__d('revenue_share_admin', 'Invalid packet id'));
		}

		if($this->RevenueSharePacket->delete()) {
			$this->Notice->success(__d('revenue_share_admin', 'Packet deleted successfully.'));
		} else {
			$this->Notice->error(__d('revenue_share_admin', 'Failed to delete packet. Please, try again.'));
		}

		return $this->redirect($this->referer());
	}

	public function admin_massactionPacket() {
		if(!empty($this->request->data['Action'])) {

			if(!isset($this->request->data['RevenueSharePacket']) || empty($this->request->data['RevenueSharePacket'])) {
				$this->Notice->error(__d('revenue_share_admin', 'Please select at least one packet.'));
				return $this->redirect($this->referer());
			}

			$packets = 0;
			foreach($this->request->data['RevenueSharePacket'] as $id => $on) {
				if($on) {
					$packets++;
					if(!$this->RevenueSharePacket->exists($id)) {
						throw new NotFoundException(__d('revenue_share_admin', 'Invalid packet'));
					}
				}
			}

			foreach($this->request->data['RevenueSharePacket'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'delete':
							$this->RevenueSharePacket->delete($id);
						break;
					}
				}
			}
			if($packets) {
				$this->Notice->success(__d('revenue_share_admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('revenue_share_admin', 'Please select at least one packet.'));
			}
		} else {
			$this->Notice->error(__d('revenue_share_admin', 'Please select an action.'));
		}
		$this->redirect($this->referer());
	}

	public function index() {
		$settings = $this->RevenueShareSettings->fetchOne('revenueShare');
		$user = $this->UserPanel->getData();

		$this->paginate = array(
			'contain' => array('RevenueShareOption'),
			'limit' => $user['ActiveMembership']['Membership']['results_per_page'],
		);

		$conditions = array(
			'RevenueSharePacket.user_id' => $user['User']['id'],
		);

		if(!$settings['showHistoric']) {
			$conditions[] = 'DATE_ADD(RevenueSharePacket.created, INTERVAL RevenueSharePacket.running_days_max DAY) >= NOW()';
			$conditions[] = 'RevenueSharePacket.revenued + RevenueSharePacket.failed_revenue < RevenueSharePacket.total_revenue';
		}

		$packets = $this->Paginator->paginate($conditions);

		$this->RevenueShareLimit->contain();
		$limit = $this->RevenueShareLimit->findByMembershipId($user['ActiveMembership']['membership_id']);

		$this->RevenueSharePacket->contain();
		$packets_count = $this->RevenueSharePacket->find('count', array(
			'conditions' => array(
				'RevenueSharePacket.user_id' => $user['User']['id'],
				'DATE_ADD(RevenueSharePacket.created, INTERVAL RevenueSharePacket.running_days_max DAY) >= NOW()',
				'RevenueSharePacket.revenued + RevenueSharePacket.failed_revenue < RevenueSharePacket.total_revenue',
			),
		));

		$this->RevenueSharePacket->contain();
		$last_packet = $this->RevenueSharePacket->find('all', array(
			'fields' => array(
				'COALESCE(MAX(created), 0) as last',
			),
			'conditions' => array(
				'RevenueSharePacket.user_id' => $user['User']['id'],
				'DATE_ADD(RevenueSharePacket.created, INTERVAL RevenueSharePacket.running_days_max DAY) >= NOW()',
				'RevenueSharePacket.revenued + RevenueSharePacket.failed_revenue < RevenueSharePacket.total_revenue',
			),
		));
		$next_purchase_date = date('Y-m-d H:i:s', strtotime($last_packet[0][0]['last'].' + '.$limit['RevenueShareLimit']['days_between'].' day'));

		$this->set('breadcrumbTitle', __d('revenue_share', 'Revenue Shares'));
		$this->set(compact('user', 'packets', 'limit', 'packets_count', 'next_purchase_date'));
	}

	public function buy() {
		$settings = $this->RevenueShareSettings->fetchOne('revenueShare');
		$user = $this->UserPanel->getData();

		$conditions = array(
			'RevenueSharePacket.revenued + RevenueSharePacket.failed_revenue < RevenueSharePacket.total_revenue',
			'RevenueSharePacket.user_id' => $user['User']['id'],
		);

		$this->RevenueShareLimit->contain();
		$limit = $this->RevenueShareLimit->findByMembershipId($user['ActiveMembership']['membership_id']);

		$this->RevenueSharePacket->contain();
		$packets_count = $this->RevenueSharePacket->find('count', array(
			'conditions' => $conditions,
		));

		$this->RevenueSharePacket->contain();
		$last_packet = $this->RevenueSharePacket->find('all', array(
			'fields' => array(
				'COALESCE(MAX(created), 0) as last',
			),
			'conditions' => $conditions,
		));
		$next_purchase_date = date('Y-m-d H:i:s', strtotime($last_packet[0][0]['last'].' + '.$limit['RevenueShareLimit']['days_between'].' day'));

		if($next_purchase_date > date('Y-m-d H:i:s') || $limit['RevenueShareLimit']['max_packs'] <= $packets_count && $limit['RevenueShareLimit']['max_packs'] != -1) {
			/* cheater? */
			throw new ForbiddenException(__d('revenue_share', 'Not allowed'));
		}

		$can_buy = $limit['RevenueShareLimit']['max_packs_one_purchase'];

		if($limit['RevenueShareLimit']['max_packs'] != -1) {
			if($limit['RevenueShareLimit']['max_packs'] - $packets_count <= 0) {
				throw new ForbiddenException(__d('revenue_share', 'Not allowed'));
			} else {
				$can_buy = $limit['RevenueShareLimit']['max_packs'] - $packets_count;
			}
		}

		if($can_buy < 0) {
			$can_buy = 'unlimited';
		}

		$this->RevenueShareOption->recursive = 1;
		$optionsData = $this->RevenueShareOption->find('all', array(
			'conditions' => array(
				'RevenueShareOption.membership_id' => $user['ActiveMembership']['membership_id'],
			),
		));

		App::uses('CurrencyHelper', 'View/Helper');
		$currencyHelper = new CurrencyHelper(new View()); /* lame... */

		$activeGateways = $this->Payments->getActiveDepositsHumanized();

		if(!$settings['purchaseBalance']) {
			unset($activeGateways['PurchaseBalance']);
		}

		$options = array();
		foreach($optionsData as &$v) {
			$options[$v['RevenueShareOption']['id']] = __d('revenue_share', '%s - %s', $v['RevenueShareOption']['title'], $currencyHelper->format($v['RevenueShareOption']['price']));

			$v['RevenueShareOption']['prices'] = array();

			foreach($activeGateways as $k => $g) {
				$v['RevenueShareOption']['prices'][$k] = bcadd($v['RevenueShareOption']['price'], $this->Payments->getDepositFee($v['RevenueShareOption']['price'], $k));
			}
		}
		$optionsData = Hash::combine($optionsData, '{n}.RevenueShareOption.id', '{n}');

		$minimumDeposits = $this->Payments->getMinimumDepositAmount();
		$ignoreMinDeposit = $this->Payments->getIgnoreMinDeposit();
		$items = $this->RevenueShareOption->getAvailableItems();

		foreach($optionsData as &$v) {
			if(empty($v['Item'])) {
				$v['RevenueShareOption']['items'] = array(__d('revenue_share', 'None'));
			} else {
				foreach($v['Item'] as $item) {
					$v['RevenueShareOption']['items'][] = $items[$item['model'].'-'.$item['foreign_key']];
				}
			}
			unset($v['Item']);
		}

		$this->set('breadcrumbTitle', __d('revenue_share', 'Revenue Shares'));
		$this->set('limit', $limit['RevenueShareLimit']['max_packs']);
		$this->set(compact('user', 'options', 'activeGateways', 'can_buy', 'optionsData', 'minimumDeposits', 'ignoreMinDeposit', 'items'));

		if($this->request->is(array('post', 'put'))) {
			$option_id = $this->request->data['option_id'];
			$amount = $this->request->data['amount'];

			if(empty($amount) || !is_numeric($amount)) {
				return $this->Notice->error(__d('revenue_share', 'Please enter a valid amount'));
			}

			if(!in_array($option_id, array_keys($options))) {
				/* cheater? */
				throw new NotFoundException(__d('revenue_share', 'Invalid option'));
			}

			if(!in_array($this->request->data['gateway'], array_keys($activeGateways))) {
				/* cheater? */
				throw new NotFoundException(__d('revenue_share', 'Invalid gateway'));
			}

			if($amount > $can_buy && $can_buy != 'unlimited') {
				return $this->Notice->error(__d('revenue_share', 'You can buy maximum of %d packs.', $can_buy));
			}

			$price = bcmul($amount, $optionsData[$option_id]['RevenueShareOption']['price']);

			if(!$this->Payments->checkMinimumDepositAmount($this->request->data['gateway'], $price)) {
				return $this->Notice->error(__d('revenue_share', 'Minimum deposit amount for %s is %s.', $this->request->data['gateway'], $minimumDeposits[$this->request->data['gateway']]));
			}

			$this->Payments->pay('RevenueShareOption', $this->request->data['gateway'], $price,
			 $this->Auth->user('id'), array('option_id' => $option_id, 'quantity' => $amount), array('plugin' => 'revenue_share', 'controller' => 'revenue_share', 'action' => 'index'));
		}
	}
}
