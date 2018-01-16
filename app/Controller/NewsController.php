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
class NewsController extends AppController {
	public $components = array(
		'Paginator',
	);

	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow(array('index', 'view'));
	}

	public function index() {
		$this->News->recursive = -1;
		$this->Paginator->settings = array(
			'order' => array('News.modified' => 'desc'),
		);
		$allNews = $this->Paginator->paginate();
		$this->set(compact('allNews'));
	}

	public function view($id = null) {
		$this->News->id = $id;
		$news = $this->News->read();

		if(empty($news)) {
			throw new NotFoundException(__d('exception', 'Invalid news'));
		}

		$this->set(compact('news'));
	}

	public function admin_index() {
		$this->News->recursive = -1;
		$this->Paginator->settings = array(
			'fields' => array(
				'id',
				'title',
				'show_in_login_ads',
				'show_in_login_ads_until',
				'created', 
				'modified',
			),
		);
		$this->paginate = array('order' => 'News.modified DESC');
		$allNews = $this->Paginator->paginate();
		$this->set(compact('allNews'));
	}

	public function admin_add() {
		$this->helpers[] = 'TinyMCE.TinyMCE';
		if($this->request->is(array('post', 'put'))) {
			$this->News->create();
			if($this->News->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'The news has been created.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'The news could not be created. Please, try again.'));
			}
		}
	}

	public function admin_edit($id = null) {
		$this->helpers[] = 'TinyMCE.TinyMCE';
		if(!$this->News->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid news'));
		}

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['News']['id'] = $id;
			if($this->News->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'The news has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'The news could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->News->findById($id);
		}
	}

	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {
			if(!isset($this->request->data['News']) || empty($this->request->data['News'])) {
				$this->Notice->error(__d('admin', 'Please select at least one news.'));
				return $this->redirect(array('action' => 'index'));
			}
			foreach($this->request->data['News'] as $id => $on) {
				if($on && !$this->News->exists($id)) {
					throw new NotFoundException(__d('exception', 'Invalid news'));
				}
			}
			$news = 0;
			foreach($this->request->data['News'] as $id => $on) {
				if($on) {
					$news++;
					switch($this->request->data['Action']) {
						case 'delete':
							$this->News->delete($id);
						break;
					}
				}
			}
			if($news) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one news.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect(array('action' => 'index'));
	}

	public function admin_delete($id = null) {
		$this->request->allowMethod('post', 'delete');
		$this->News->id = $id;
		if(!$this->News->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid news'));
		}
		if($this->News->delete()) {
			$this->Notice->success(__d('admin', 'The news has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The news could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
