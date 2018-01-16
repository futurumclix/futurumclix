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
class OfferwallsController extends OfferwallsAppController {
	public $components = array(
		'UserPanel',
		'Paginator',
		'Offerwalls.Offerwalls',
	);
	public $uses = array(
		'Offerwalls.OfferwallsOffer',
		'Offerwalls.Offerwall',
		'Offerwalls.OfferwallsMembership',
	);

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('offerCallback');
		$this->Security->unlockedActions = array(
			'offerCallback',
		);
	}

	public function admin_index() {
		if($this->request->is(array('post', 'put'))) {
			$success = true;
			$this->Offerwall->recursive = -1;
			$offerwallsData = $this->Offerwall->find('all');
			$offerwallsData = Hash::combine($offerwallsData, '{n}.Offerwall.name', '{n}.Offerwall');

			if(!empty($this->request->data['Offerwall'])) {
				$offerwalls = &$this->request->data['Offerwall'];
				foreach($offerwalls as $k => &$o) {
					$class = $k.'Offerwall';
					App::uses($class, 'Offerwalls.Lib/Offerwalls');

					$o['name'] = $k;
					if(!isset($offerwallsData[$k])) {
						$o['allowed_ips'] = implode(',', $class::getAllowedIPs());
					}
				}
				if($this->Offerwall->saveMany($offerwalls)) {
					$this->Offerwalls->refreshData();
					$success = true;
				} else {
					$success = false;
				}
			}

			if(!empty($this->request->data['OfferwallsSettings']) && $success) {
				$success = $this->OfferwallsSettings->store($this->request->data, array('offerwalls'));
			}

			if(!empty($this->request->data['OfferwallsMembership']) && $success) {
				$success = $this->OfferwallsMembership->saveMany($this->request->data['OfferwallsMembership']);
			}

			if($success) {
				$this->Notice->success(__d('offerwalls_admin', 'The settings has been saved.'));
			} else {
				$this->Notice->error(__d('offerwalls_admin', 'Error occurred when saving settings. Please, try again.'));
			}
		}

		$available = $this->Offerwalls->getAvailableHumanized();
		$active = $this->Offerwalls->getActiveHumanized();

		$settings = $this->OfferwallsSettings->fetch('offerwalls');
		$this->request->data = Hash::merge($settings, $this->request->data);

		$this->OfferwallsMembership->recursive = -1;
		$options = $this->OfferwallsMembership->find('all');
		$options = Hash::combine($options, '{n}.OfferwallsMembership.membership_id', '{n}.OfferwallsMembership');
		if(isset($this->request->data['OfferwallsMembership'])) {
			$this->request->data['OfferwallsMembership'] = Hash::merge($options, $this->request->data['OfferwallsMembership']);
		} else {
			$this->request->data['OfferwallsMembership'] = $options;
		}

		foreach($active as $k => $n) {
			if(!isset($this->request->data['Offerwall'][$k])) {
				$this->request->data['Offerwall'][$k] = array('enabled' => true);
			}
		}

		$this->Offerwall->recursive = -1;
		$offerwallsData = $this->Offerwall->find('all');
		$offerwallsData = Hash::combine($offerwallsData, '{n}.Offerwall.name', '{n}.Offerwall');
		if(isset($this->request->data['Offerwall'])) {
			$this->request->data['Offerwall'] = Hash::merge($offerwallsData, $this->request->data['Offerwall']);
		} else {
			$this->request->data['Offerwall'] = $offerwallsData;
		}

		$memberships = ClassRegistry::init('Membership')->getList();

		$this->set(compact('available', 'active', 'memberships'));
	}

	public function admin_delete($id = null) {
		$this->OfferwallsOffer->id = $id;

		if(!$this->OfferwallsOffer->exists()) {
			throw new NotFoundException(__d('offerwalls_admin', 'Invalid id'));
		}

		if($this->OfferwallsOffer->delete()) {
			$this->Notice->success(__d('offerwalls_admin', 'Offer successfully deleted'));
		} else {
			$this->Notice->errro(__d('offerwalls_admin', 'Failed to delete offer, please try again.'));
		}

		return $this->redirect($this->referer());
	}

	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {

			if(!isset($this->request->data['OfferwallsOffer']) || empty($this->request->data['OfferwallsOffer'])) {
				$this->Notice->error(__d('offerwalls_admin', 'Please select at least one offer.'));
				return $this->redirect($this->referer());
			}

			foreach($this->request->data['OfferwallsOffer'] as $id => $on) {
				if($on && !$this->OfferwallsOffer->exists($id)) {
					throw new NotFoundException(__d('offerwalls_admin', 'Invalid offer'));
				}
			}
			$offers = 0;
			foreach($this->request->data['OfferwallsOffer'] as $id => $on) {
				if($on) {
					$offers++;
					switch($this->request->data['Action']) {
						case 'delete':
							$this->OfferwallsOffer->delete($id);
						break;
					}
				}
			}
			if($offers) {
				$this->Notice->success(__d('offerwalls_admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('offerwalls_admin', 'Please select at least one offer.'));
			}
		} else {
			$this->Notice->error(__d('offerwalls_admin', 'Please select action.'));
		}
		$this->redirect($this->referer());
	}

	public function admin_postbackLog() {
		$conditions = $this->createPaginatorConditions(array(
			'User.username',
			'OfferwallsOffer.transactionid',
		));

		$this->paginate = array(
			'contain' => array(
				'User' => 'username',
			),
		);

		$offers = $this->Paginator->paginate($conditions);
		$offerwalls = $this->Offerwalls->getActiveHumanized();

		$this->set(compact('offers', 'offerwalls'));
	}

	public function index() {
		$this->set('breadcrumbTitle', __d('offerwalls', 'Offerwalls'));

		$user = $this->UserPanel->getData(array('UserProfile'));
		$offers = $this->Offerwalls->getOffers($user);

		$this->set(compact('offers', 'user'));
	}

	public function postbackLog() {
		$this->set('breadcrumbTitle', __d('offerwalls', 'Offerwall\'s Log'));
		$user = $this->UserPanel->getData();
		$this->Paginator->settings['limit'] = $user['ActiveMembership']['Membership']['results_per_page'];

		$this->OfferwallsMembership->recursive = -1;
		$options = $this->OfferwallsMembership->findByMembershipId($user['ActiveMembership']['membership_id']);

		$offers = $this->Paginator->paginate(array(
			'OfferwallsOffer.user_id' => $this->Auth->user('id'),
		));

		$this->set(compact('offers', 'user', 'options'));
	}

	private function _offerCallback($offerwall_name, &$offerwall) {
		$trid = $offerwall->getTransactionId();

		if(!is_string($trid) || empty($trid) || $trid == '') {
			throw new BadRequestException(__d('offerwalls', 'Failed to get transaction id.'));
		}

		$this->OfferwallsOffer->contain();
		$transaction = $this->OfferwallsOffer->find('first', array(
			'fields' => array(
				'OfferwallsOffer.id',
				'OfferwallsOffer.status',
			),
			'conditions' => array(
				'OfferwallsOffer.transactionid' => $trid,
				'OfferwallsOffer.offerwall' => $offerwall_name,
			),
			'recursive' => -1,
		));

		if(!empty($transaction) && $transaction['OfferwallsOffer']['status'] != OfferwallsOffer::PENDING) {
			if($offerwall_name == 'SuperRewards') { /* TODO: probably should be implemented in SuperRewardsOfferwall::doubleRecive() or smth like that. */
				header('Content-type: text/plain');
				die("1\n");
			}

			exit(); /* this one we already processed, skip */
		}

		list($status, $user_id, $points) = $offerwall->offerCallback();

		$data = array(
			'OfferwallsOffer' => array(
				'user_id' => $user_id,
				'offerwall' => $offerwall_name,
				'amount' => $points,
				'status' => $status,
				'transactionid' => $trid,
				'complete_date' => date('Y-m-d H:i:s'),
			),
		);

		if(!empty($transaction)) {
			$data['OfferwallsOffer']['id'] = $transaction['OfferwallsOffer']['id'];
		}

		if(!$this->OfferwallsOffer->save($data)) {
			throw new InternalErrorException(__d('offerwalls', 'Failed to save offer'));
		}
		exit(); /* make sure we will not render anything. */
	}

	public function offerCallback($offerwallName = null) {
		$this->autoRender = false;
		$offerwalls = $this->Offerwalls->getActive();

		if(!$offerwallName) {
			throw new NotFoundException(__d('offerwalls', 'Offerwall not specified'));
		}

		if(in_array($offerwallName, $offerwalls)) {
			$offerwall = $this->Offerwalls->createOfferwall($offerwallName);

			$this->_offerCallback($offerwallName, $offerwall);
		} else {
			throw new NotFoundException(__d('offerwalls', 'Offerwall not found: %s', $offerwallName));
		}
	}
}
