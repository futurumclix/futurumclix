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
App::import('Lib', 'FAdmin');

class GroupsController extends AppController {

	public $uses = array(
		'RequestObject',
	);

	public $components = array(
		'Paginator',
	);

	public function admin_index() {
		$this->RequestObject->contain();
		$this->paginate = array('order' => 'id DESC');
		$groups = $this->Paginator->paginate(array(
			'RequestObject.model' => null,
		));
		$this->set(compact('groups'));
	}

	public function admin_edit($id = null) {
		if(!$this->RequestObject->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid group'));
		}

		$group = $this->RequestObject->findById($id);

		if(in_array($group['RequestObject']['alias'], Configure::read('Admin.aliases'))) {
			throw new InternalErrorException(__d('exception', 'Forbidden group edit'));
		}

		if($this->request->is(array('post', 'put'))) {
			if($this->RequestObject->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'The group has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'The group could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $group;
		}

		$parents = $this->RequestObject->find('list');
		unset($parents[$id]);

		$aros = $this->RequestObject->find('list');
		$statuses = $this->RequestObject->enum['status'];
		$this->set(compact('parents', 'aros', 'statuses'));
	}

	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {
			if(!isset($this->request->data['Group']) || empty($this->request->data['Group'])) {
				$this->Notice->error(__d('admin', 'Please select at least one group.'));
				return $this->redirect(array('action' => 'index'));
			}
			foreach($this->request->data['Group'] as $id => $on) {
				if($on && !$this->RequestObject->exists($id)) {
					throw new NotFoundException(__d('exception', 'Invalid group'));
				}
			}
			$groups = 0;
			foreach($this->request->data['Group'] as $id => $on) {
				if($on) {
					$groups++;
					switch($this->request->data['Action']) {
						case 'delete':
							$this->RequestObject->delete($id);
						break;
					}
				}
			}
			if($groups) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one group.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect(array('action' => 'index'));
	}

	public function admin_add() {
		if($this->request->is(array('post', 'put'))) {
			$this->RequestObject->create();
			if($this->RequestObject->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'The group has been created.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'The group could not be created. Please, try again.'));
			}
		}
	}

	public function admin_delete($id = null) {
		$this->RequestObject->id = $id;
		if(!$this->RequestObject->read()) {
			throw new NotFoundException(__d('exception', 'Invalid group'));
		}

		$this->request->allowMethod('post', 'delete');
		if($this->RequestObject->delete()) {
			$this->Notice->success(__d('admin', 'The group has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The group could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
