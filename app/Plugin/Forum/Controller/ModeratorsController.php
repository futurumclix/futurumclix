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
App::uses('ForumAppController', 'Forum.Controller');

class ModeratorsController extends ForumAppController {

	public $components = array(
		'Paginator',
	);

	public $uses = array(
		'Forum.Moderator',
	);

	public function admin_index() {
		$this->Moderator->contain(array('Forum', 'User' => array('username')));
		$moderators = $this->Paginator->paginate();
		$this->set(compact('moderators'));
	}

	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {
			if(!isset($this->request->data['Moderator']) || empty($this->request->data['Moderator'])) {
				$this->Notice->error(__d('forum', 'Please select at least one moderator.'));
				return $this->redirect(array('action' => 'index'));
			}
			foreach($this->request->data['Moderator'] as $id => $on) {
				if($on && !$this->Moderator->exists($id)) {
					throw new NotFoundException(__d('forum', 'Invalid moderator'));
				}
			}
			$moderators = 0;
			foreach($this->request->data['Moderator'] as $id => $on) {
				if($on) {
					$moderators++;
					switch($this->request->data['Action']) {
						case 'delete':
							$this->Moderator->delete($id);
						break;
					}
				}
			}
			if($moderators) {
				$this->Notice->success(__d('forum', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('forum', 'Please select at least one moderator.'));
			}
		} else {
			$this->Notice->error(__d('forum', 'Please select an action.'));
		}
		$this->redirect(array('action' => 'index'));
	}

	public function admin_add() {
		$this->set('forums', $this->Moderator->Forum->getHierarchy(true, true));

		if($this->request->is(array('post', 'put'))) {
			if(!isset($this->request->data['User']) || !isset($this->request->data['User']['username']) || empty($this->request->data['User']['username'])) {
				$this->Notice->error(__d('forum', 'Please enter an username.'));
				return;
			}

			$this->Moderator->User->contain();
			$user = $this->Moderator->User->findByUsername($this->request->data['User']['username'], array('User.id'));

			if(empty($user)) {
				return $this->Notice->error(__d('forum', 'User not found'));
			}

			unset($this->request->data['User']);
			$this->request->data['Moderator']['user_id'] = $user['User']['id'];

			$this->Moderator->create();
			if($this->Moderator->save($this->request->data)) {
				$this->Notice->success(__d('forum', 'The moderator has been created.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('forum', 'The moderator could not be created. Please, try again.'));
			}
		}
	}

	public function admin_edit($id = null) {
		$this->Moderator->id = $id;

		if(!$this->Moderator->exists()) {
			throw new NotFoundException(__d('forum', 'Invalid moderator'));
		}

		$this->set('forums', $this->Moderator->Forum->getHierarchy(true, true));

		if($this->request->is(array('post', 'put'))) {
			if(!isset($this->request->data['User']) || !isset($this->request->data['User']['username']) || empty($this->request->data['User']['username'])) {
				$this->Notice->error(__d('forum', 'Please enter an username.'));
				return;
			}

			$this->Moderator->User->contain();
			$user = $this->Moderator->User->findByUsername($this->request->data['User']['username'], array('User.id'));

			if(empty($user)) {
				return $this->Notice->error(__d('forum', 'User not found'));
			}

			unset($this->request->data['User']);
			$this->request->data['Moderator']['user_id'] = $user['User']['id'];

			if($this->Moderator->save($this->request->data)) {
				$this->Notice->success(__d('forum', 'The moderator has been saved sucessfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('forum', 'The moderator could not be saved. Please, try again.'));
			}
		} else {
			$this->Moderator->read();
			$this->request->data = $this->Moderator->data;

			$this->Moderator->User->contain();
			$user = $this->Moderator->User->findById($this->request->data['Moderator']['user_id'], array('User.username'));
			$this->request->data['User']['username'] = $user['User']['username'];
		}
	}

	public function admin_delete($id = null) {
		$this->Moderator->id = $id;
		if(!$this->Moderator->exists()) {
			throw new NotFoundException(__d('forum', 'Invalid moderator'));
		}
		$this->request->allowMethod('post', 'delete');
		if($this->Moderator->delete()) {
			$this->Notice->success(__d('forum', 'The moderator has been deleted.'));
		} else {
			$this->Notice->error(__d('forum', 'The moderator could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
