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
 * SupportCannedAnswers Controller
 *
 * @property SupportCannedAnswer $SupportCannedAnswer
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class SupportCannedAnswersController extends AppController {
	public $uses = array(
		'SupportCannedAnswer',
		'SupportTicketAnswer',
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		'Session',
	);

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->SupportCannedAnswer->recursive = -1;
		$this->paginate = array('order' => 'SupportCannedAnswer.created DESC');
		$this->set('supportCannedAnswers', $this->Paginator->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if(!$this->SupportCannedAnswer->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid support canned answer.'));
		}
		$options = array('conditions' => array('SupportCannedAnswer.' . $this->SupportCannedAnswer->primaryKey => $id));
		$this->set('supportCannedAnswer', $this->SupportCannedAnswer->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if($this->request->is('post')) {
			$this->SupportCannedAnswer->create();
			if($this->SupportCannedAnswer->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'The support canned answer has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'The support canned answer could not be saved. Please, try again.'));
			}
		}
		$variables = $this->SupportTicketAnswer->getVariables();
		$this->set(compact('variables'));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if(!$this->SupportCannedAnswer->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid support canned answer'));
		}
		if($this->request->is(array('post', 'put'))) {
			$this->request->data['SupportCannedAnswer']['id'] = $id;
			if($this->SupportCannedAnswer->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'The support canned answer has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'The support canned answer could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('SupportCannedAnswer.' . $this->SupportCannedAnswer->primaryKey => $id));
			$this->request->data = $this->SupportCannedAnswer->find('first', $options);
		}
		$variables = $this->SupportTicketAnswer->getVariables();
		$this->set(compact('variables'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->SupportCannedAnswer->id = $id;
		if(!$this->SupportCannedAnswer->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid support canned answer.'));
		}
		$this->request->allowMethod('post', 'delete');
		if($this->SupportCannedAnswer->delete()) {
			$this->Notice->success(__d('admin', 'The support canned answer has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The support canned answer could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
