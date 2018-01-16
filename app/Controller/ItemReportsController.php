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
 * ItemReports Controller
 *
 * @property ItemReport $ItemReport
 * @property PaginatorComponent $Paginator
 * @property ReportComponent $Report
 * @property SessionComponent $Session
 */
class ItemReportsController extends AppController {
/**
 * Helpers
 *
 * @var array
 */
	public $helpers = array(
		'Utility.Utility'
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		'Report',
		'Session'
	);

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$conditions = $this->createPaginatorConditions(array(
			'Reporter.username',
			'Resolver.email',
			'ItemReport.reason',
			'ItemReport.comment',
		));

		$inCollapse = array(
			'ItemReport.type',
			'ItemReport.reason LIKE',
			'ItemReport.comment LIKE',
		);

		$inCollapse = array_intersect_key($conditions, array_flip($inCollapse));
		if(!empty($inCollapse)) {
			$this->set('searchCollapse', 'in');
		} else {
			$this->set('searchCollapse', '');
		}

		$this->ItemReport->contain(array('Reporter', 'Resolver'));
		$this->paginate = array('order' => 'ItemReport.created DESC');
		$itemReports = $this->Paginator->paginate($conditions);

		foreach($itemReports as $k => $v) {
			$itemReports[$k]['view_url'] = $this->Report->getModelViewURL($v['ItemReport']['model'], $v['ItemReport']['foreign_key']);
		}

		$this->set(compact('itemReports'));
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if(!$this->ItemReport->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid item report'));
		}
		$options = array('conditions' => array('ItemReport.' . $this->ItemReport->primaryKey => $id));
		$this->ItemReport->contain(array('Reporter'));
		$this->set('itemReport', $this->ItemReport->find('first', $options));
		$this->set('return', $this->referer() == '/' ? array('action' => 'index') : $this->referer());
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if(!$this->ItemReport->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid item report'));
		}
		if($this->request->is(array('post', 'put'))) {
			$this->request->data['ItemReport']['status'] = ItemReport::RESOLVED;
			$this->request->data['ItemReport']['resolver_id'] = $this->Auth->user('id');
			if($this->ItemReport->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'The item report has been saved.'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'The item report could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('ItemReport.' . $this->ItemReport->primaryKey => $id));
			$this->ItemReport->contain(array('Reporter'));
			$this->request->data = $this->ItemReport->find('first', $options);
		}

		$viewURL = $this->Report->getModelViewURL($this->request->data['ItemReport']['model'], $this->request->data['ItemReport']['foreign_key']);
		$actions = $this->Report->getModelActionsList($this->request->data['ItemReport']['model']);
		$this->set(compact('actions', 'viewURL'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->ItemReport->id = $id;
		if(!$this->ItemReport->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid item report'));
		}
		$this->request->allowMethod('post', 'delete');
		if($this->ItemReport->delete()) {
			$this->Session->setFlash(__d('admin', 'The item report has been deleted.'));
		} else {
			$this->Session->setFlash(__d('admin', 'The item report could not be deleted. Please, try again.'));
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

			if(!isset($this->request->data['ItemReport']) || empty($this->request->data['ItemReport'])) {
				$this->Notice->error(__d('admin', 'Please select at least one item.'));
				return $this->redirect($this->referer());
			}

			$ads = 0;
			foreach($this->request->data['ItemReport'] as $id => $on) {
				if($on) {
					$ads++;
					if(!$this->ItemReport->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid item.'));
					}
				}
			}

			foreach($this->request->data['ItemReport'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'delete':
							$this->ItemReport->delete($id);
						break;
					}
				}
			}
			if($ads) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one item.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect($this->referer());
	}
}
