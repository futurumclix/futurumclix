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
class AntiCheatController extends AppController {
	public $uses = array(
		'User',
		'IpLock',
		'EmailLock',
		'UsernameLock',
		'CountryLock',
	);

	public $components = array(
		'Paginator',
	);

	public function admin_blocking() {
		if($this->request->is(array('post', 'put'))) {
			if(isset($this->request->data['IpLock'])) {
				if($this->IpLock->save($this->request->data)) {
					$this->Notice->success(__d('admin', 'IP added sucessfully.'));
					unset($this->request->data['IpLock']);
				} else {
					$this->Notice->error(__d('admin', 'Failed to save IP. Please, try again.'));
				}
			}

			if(isset($this->request->data['EmailLock'])) {
				if($this->EmailLock->save($this->request->data)) {
					$this->Notice->success(__d('admin', 'Email lock added sucessfully.'));
					unset($this->request->data['EmailLock']);
				} else {
					$this->Notice->error(__d('admin', 'Failed to save email lock. Please, try again.'));
				}
			}

			if(isset($this->request->data['UsernameLock'])) {
				if($this->UsernameLock->save($this->request->data)) {
					$this->Notice->success(__d('admin', 'Username lock added sucessfully.'));
					unset($this->request->data['UsernameLock']);
				} else {
					$this->Notice->error(__d('admin', 'Failed to save username lock. Please, try again.'));
				}
			}

			if(isset($this->request->data['CountryLock'])) {
				if($this->CountryLock->save($this->request->data)) {
					$this->Notice->success(__d('admin', 'Country lock added sucessfully.'));
					unset($this->request->data['CountryLock']);
				} else {
					$this->Notice->error(__d('admin', 'Failed to save country lock. Please, try again.'));
				}
			}
		}

		$this->IpLock->recursive = -1;
		$ips = $this->IpLock->find('all');
		$this->EmailLock->recursive = -1;
		$emails = $this->EmailLock->find('all');
		$this->UsernameLock->recursive = -1;
		$usernames = $this->UsernameLock->find('all');
		$this->CountryLock->recursive = 1;
		$countrylocks = $this->CountryLock->find('all', array(
			'fields' => array(
				'Country.country',
				'CountryLock.id',
				'CountryLock.country_id',
				'CountryLock.note',
				'CountryLock.created',
			)
		));

		$this->set(compact('ips', 'emails', 'usernames', 'countrylocks'));
		$this->set('countries', ClassRegistry::init('Ip2NationCountry')->getCountriesList());
	}

	public function admin_massaction($model = null) {
		if($model === null || !in_array($model, array('IpLock'))) {
			throw new BadRequestException(__d('exception', 'Invalid model: %s', $model));
		}
		if(!empty($this->request->data['Action'])) {

			if(!isset($this->request->data['IpLock']) || empty($this->request->data['IpLock'])) {
				$this->Notice->error(__d('admin', 'Please select at least one entry.'));
				return $this->redirect($this->referer());
			}

			foreach($this->request->data['IpLock'] as $id => $on) {
				if($on && !$this->$model->exists($id)) {
					throw new NotFoundException(__d('exception', 'Invalid entry'));
				}
			}
			$deposits = 0;
			foreach($this->request->data['IpLock'] as $id => $on) {
				if($on) {
					$deposits++;
					switch($this->request->data['Action']) {
						case 'delete':
							$this->$model->delete($id);
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

	public function admin_ip_lock_delete($id = null) {
		$this->request->allowMethod(array('post', 'put'));
		$this->IpLock->id = $id;

		if($id === null || !$this->IpLock->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid id'));
		}

		if($this->IpLock->delete()) {
			$this->Notice->success(__d('admin', 'IP lock successfully deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to delete IP lock. Please, try again.'));
		}

		return $this->redirect($this->referer());
	}

	public function admin_username_lock_delete($id = null) {
		$this->request->allowMethod(array('post', 'put'));
		$this->UsernameLock->id = $id;

		if($id === null || !$this->UsernameLock->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid id'));
		}

		if($this->UsernameLock->delete()) {
			$this->Notice->success(__d('admin', 'Username lock successfully deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to delete username lock. Please, try again.'));
		}

		return $this->redirect($this->referer());
	}

	public function admin_email_lock_delete($id = null) {
		$this->request->allowMethod(array('post', 'put'));
		$this->EmailLock->id = $id;

		if($id === null || !$this->EmailLock->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid id'));
		}

		if($this->EmailLock->delete()) {
			$this->Notice->success(__d('admin', 'Email lock successfully deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to delete email lock. Please, try again.'));
		}

		return $this->redirect($this->referer());
	}

	public function admin_country_lock_delete($id = null) {
		$this->request->allowMethod(array('post', 'put'));
		$this->CountryLock->id = $id;

		if($id === null || !$this->CountryLock->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid id'));
		}

		if($this->CountryLock->delete()) {
			$this->Notice->success(__d('admin', 'Country lock successfully deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to delete country lock. Please, try again.'));
		}

		return $this->redirect($this->referer());
	}

	public function admin_evercookie() {
		if($this->request->is(array('post', 'put'))) {
			if($this->Settings->store($this->request->data, 'Evercookie', 'Evercookie')) {
				$this->Notice->success(__d('admin', 'The settings has been saved.'));
			} else {
				$this->Notice->error(__d('admin', 'The settings could not be saved. Please, try again.'));
			}
		}
		@$this->request->data['Settings']['Evercookie'] = Hash::filter(Hash::merge(Configure::read('Evercookie'), $this->request->data['Settings']['Evercookie']));
	}

	public function admin_suspicious($mode = null) {
		switch($mode) {
			case 'ip':
				$week = date('Y-m-d H:i:s', strtotime('-1 week'));
				$options = array(
					'fields' => array(
						'DISTINCT User.id',
						'User.username',
						'User.signup_ip',
						'User.last_ip',
						'User.created',
						'User.email',
						'User.location',
					),
					'conditions' => array(
						'User.signup_ip',
						'User2.signup_ip',
						'OR' => array(
							'User.created >=' => $week,
							'User.last_log_in >=' => $week,
						)
					),
					'joins' => array(
						array(
							'alias' => 'User2',
							'table' => 'users',
							'type' => 'INNER',
							'conditions' => '`User`.`id` != `User2`.`id` AND ((SUBSTRING_INDEX(`User`.`signup_ip`, ".", 1) = SUBSTRING_INDEX(`User2`.`signup_ip`, ".", 1)
							 AND SUBSTRING_INDEX(SUBSTRING_INDEX(`User`.`signup_ip`, ".", 2), ".", -1) = SUBSTRING_INDEX(SUBSTRING_INDEX(`User2`.`signup_ip`, ".", 2), ".", -1))
							 OR (SUBSTRING_INDEX(`User`.`last_ip`, ".", 1) = SUBSTRING_INDEX(`User2`.`last_ip`, ".", 1) 
							 AND SUBSTRING_INDEX(SUBSTRING_INDEX(`User`.`last_ip`, ".", 2), ".", -1) = SUBSTRING_INDEX(SUBSTRING_INDEX(`User2`.`last_ip`, ".", 2), ".", -1)))',
						),
					),
					'contain' => array(),
					'order' => 'User.id DESC',
				);
			break;

			case 'username':
				$options = array(
					'fields' => array(
						'DISTINCT User.id',
						'User.username',
						'User.signup_ip',
						'User.last_ip',
						'User.created',
						'User.email',
						'User.location',
					),
					'joins' => array(
						array(
							'alias' => 'User2',
							'table' => 'users',
							'type' => 'INNER',
							'conditions' => '`User`.`id` != `User2`.`id` AND `User`.`username` LIKE CONCAT("%", `User2`.`username`, "%")',
						),
					),
					'contain' => array(),
					'order' => 'User.id DESC',
				);
			break;

			case 'usernameemail':
				$options = array(
					'fields' => array(
						'DISTINCT User.id',
						'User.username',
						'User.signup_ip',
						'User.last_ip',
						'User.created',
						'User.email',
						'User.location',
					),
					'conditions' => array(
						'`User`.`email` LIKE CONCAT("%", `User`.`username`, "%")'
					),
					'contain' => array(),
					'order' => 'User.id DESC',
				);
			break;
		}

		if(isset($options)) {
			$this->paginate = $options;
			$data = $this->Paginator->paginate();
			$this->set(compact('data'));
		}

		$this->set(compact('mode'));
	}
}
