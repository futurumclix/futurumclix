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

class SettingsController extends ForumAppController {

	public $components = array(
		'Paginator',
	);

	public $uses = array(
		'Forum.Forum',
		'Forum.Topic',
		'RequestObject',
	);

	public function admin_general() {
		$globalKeys = array(
			'Forum.indexStatistics',
			'Forum.newestUser',
			'Forum.active',
			'Forum.onlyLogged',
		);
		if($this->request->is(array('post', 'put'))) {
			$this->request->data['Settings'] = Hash::flatten($this->request->data['Settings']);
			$this->Settings = ClassRegistry::init('Settings');
			if($this->Settings->store($this->request->data, $globalKeys, true)) {
				$this->Notice->success(__d('forum', 'The settings has been saved.'));
			} else {
				$this->Notice->error(__d('forum', 'The settings could not be saved. Please, try again.'));
			}
		}
		$configuration = array();
		foreach($globalKeys as $k) {
			$configuration[$k] = Configure::read($k);
		}
		$this->set(compact('configuration'));
	}

	public function admin_index() {
		$this->Forum->contain(array(
			'AccessRead',
			'AccessPoll',
			'AccessPost',
			'AccessReply',
			'Parent' => array('title'),
		));
		$forums = $this->Paginator->paginate();
		$this->set(compact('forums'));
	}

	public function admin_edit($id = null) {
		if(!$this->Forum->exists($id)) {
			throw new NotFoundException(__d('forum', 'Invalid forum'));
		}
		if($this->request->is(array('post', 'put'))) {
			if($this->Forum->save($this->request->data)) {
				$this->Notice->success(__d('forum', 'The forum has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('forum', 'The forum could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Forum->findById($id);
		}

		$parents = $this->Forum->find('list');
		unset($parents[$id]);

		$aros = $this->RequestObject->find('list', array('conditions' => array('RequestObject.model' => null)));
		$statuses = $this->Forum->enum['status'];
		$this->set(compact('parents', 'aros', 'statuses'));
	}

	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {
			if(!isset($this->request->data['Forum']) || empty($this->request->data['Forum'])) {
				$this->Notice->error(__d('forum', 'Please select at least one forum.'));
				return $this->redirect(array('action' => 'index'));
			}
			foreach($this->request->data['Forum'] as $id => $on) {
				if($on && !$this->Forum->exists($id)) {
					throw new NotFoundException(__d('forum', 'Invalid forum'));
				}
			}
			$forums = 0;
			foreach($this->request->data['Forum'] as $id => $on) {
				if($on) {
					$forums++;
					switch($this->request->data['Action']) {
						case 'delete':
							$this->Forum->delete($id);
						break;
					}
				}
			}
			if($forums) {
				$this->Notice->success(__d('forum', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('forum', 'Please select at least one forum.'));
			}
		} else {
			$this->Notice->error(__d('forum', 'Please select an action.'));
		}
		$this->redirect(array('action' => 'index'));
	}

	public function admin_add() {
		if($this->request->is(array('post', 'put'))) {
			$this->Forum->create();
			$this->request->data['Forum']['status'] = Forum::CLOSED;
			if($this->Forum->save($this->request->data)) {
				$this->Notice->success(__d('forum', 'The forum has been created.'));
				return $this->redirect(array('action' => 'edit', $this->Forum->id));
			} else {
				$this->Notice->error(__d('forum', 'The forum could not be created. Please, try again.'));
			}
		}
	}

	public function admin_delete($id = null) {
		$this->Forum->id = $id;
		if(!$this->Forum->exists()) {
			throw new NotFoundException(__d('forum', 'Invalid forum'));
		}
		$this->request->allowMethod('post', 'delete');
		if($this->Forum->delete()) {
			$this->Notice->success(__d('forum', 'The forum has been deleted.'));
		} else {
			$this->Notice->error(__d('forum', 'The forum could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function admin_removeIcon($id = null) {
		$this->Forum->id = $id;
		if(!$this->Forum->exists()) {
			throw new NotFoundException(__d('forum', 'Invalid forum'));
		}
		$this->request->allowMethod('post', 'delete');
		if($this->Forum->deleteIcon()) {
			$this->Notice->success(__d('forum', 'The forum icon has been deleted.'));
		} else {
			$this->Notice->error(__d('forum', 'The forum icon could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	private function _sort_children(&$parent, &$forums) {
		if(isset($parent['children'])) {
			usort($parent['children'], function ($a, $b) use ($forums) {
				if($forums[$a]['orderNo'] == $forums[$b]['orderNo']) {
					return 0;
				}
				return $forums[$a]['orderNo'] < $forums[$b]['orderNo'] ? -1 : 1;
			});
			foreach($parent['children'] as $child_id) {
				$this->_sort_children($forums[$child_id], $forums);
			}
		}
	}

	public function admin_order() {
		if($this->request->is(array('post', 'put'))) {
			$this->request->data = array_values($this->request->data['Forum']);
			if($this->Forum->saveMany($this->request->data)) {
				$this->Notice->success(__d('forum', 'Successfully saved new forums order'));
			} else {
				$this->Notice->error(__d('forum', 'Failed to save forums order. Please, try again.'));
			}
		}
		$forums = $this->Forum->find('all', array(
			'fields' => array('id', 'title', 'orderNo', 'parent_id'),
			'order' => array('Forum.lft' => 'ASC'),
		));
		$forums = Hash::combine($forums, '{n}.Forum.id', '{n}.Forum');
		$parents = $this->Forum->generateTreeList(null, null, '{n}.Forum.parent_id', null);

		foreach($parents as $k => $v) {
			$forums[$v]['children'][] = $k;
		}

		$this->_sort_children($forums[''], $forums);

		foreach($forums['']['children'] as $child_id) {
			$this->_sort_children($forums[$child_id], $forums);
		}

		$this->set(compact('forums'));
	}

	public function admin_tos() {
		$this->helpers[] = 'TinyMCE.TinyMCE';
		$this->Settings = ClassRegistry::init('Settings');

		if($this->request->is(array('post', 'put'))) {
			$data = array(
				'Settings' => array(
					'Forum.ToS' => $this->request->data['text'],
					'Forum.ToSActive' => $this->request->data['enable'],
				),
			);
			if($this->Settings->store($data, array('Forum.ToS')) && $this->Settings->store($data, array('Forum.ToSActive'), true)) {
				$this->Notice->success(__d('forum', 'ToS saved successfully.'));
			} else {
				$this->Notice->error(__d('forum', 'Failed to save, please try again.'));
			}
		} else {
			$settings = $this->Settings->fetch('Forum.ToS');

			$this->request->data['text'] = $settings['Settings']['Forum.ToS'];
			$this->request->data['enable'] = Configure::read('Forum.ToSActive');
		}
	}

	public function admin_help() {
		$this->helpers[] = 'TinyMCE.TinyMCE';
		$this->Settings = ClassRegistry::init('Settings');

		if($this->request->is(array('post', 'put'))) {
			$data = array(
				'Settings' => array(
					'Forum.help' => $this->request->data['text'],
					'Forum.helpActive' => $this->request->data['enable'],
				),
			);
			if($this->Settings->store($data, array('Forum.help')) && $this->Settings->store($data, array('Forum.helpActive'), true)) {
				$this->Notice->success(__d('forum', 'Help saved successfully.'));
			} else {
				$this->Notice->error(__d('forum', 'Failed to save, please try again.'));
			}
		} else {
			$settings = $this->Settings->fetch('Forum.help');

			$this->request->data['text'] = $settings['Settings']['Forum.help'];
			$this->request->data['enable'] = Configure::read('Forum.helpActive');
		}
	}
}
