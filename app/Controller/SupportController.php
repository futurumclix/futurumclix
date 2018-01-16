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
class SupportController extends AppController {

	public $uses = array(
		'SupportTicket',
		'SupportDepartment',
		'SupportTicketAnswer',
	);

	public $components = array(
		'Paginator',
		'UserPanel',
		'Captcha',
	);

	public function beforeFilter() {
		parent::beforeFilter();

		if($this->Session->read('Support.block')) {
			if($this->Session->read('Support.block') <= time()) {
				$this->Session->delete('Support');
			} else {
				throw new InternalErrorException(__d('exception', 'Too many failed attempts.'));
			}
		}

		if($this->request->prefix != 'admin') {
			if(!Configure::read('supportEnabled')) {
				throw new NotFoundException(__d('exception', 'Support is disabled.'));
			}

			if(!$this->Settings->fetchOne('supportRequireLogin')) {
				$this->Auth->allow(array('notLoggedIn', 'index', 'add', 'view'));
			}
		} else {
			if($this->request->params['action'] == 'admin_settings') {
				$start = $this->SupportDepartment->find('count');

				if(isset($this->request->data['SupportDepartment'])) {
					$stop = count($this->request->data['SupportDepartment']);
				} else {
					$stop = 50;
				}

				for(;$start < $stop; $start++) {
					$this->Security->unlockedFields[] = 'SupportDepartment.'.$start.'.name';
				}
			}
		}

		$this->Captcha->protect('add');
	}

	private function _ticketNotFound() {
		$fails = $this->Session->read('Support.fail');

		if($fails) {
			$fails++;
		} else {
			$fails = 1;
		}

		$this->Session->write('Support.fail', $fails);

		if($fails >= 10) {
			$this->Session->write('Support.block', strtotime('+30 minutes'));
		}
	}

	public function notLoggedIn() {
		if($this->Auth->loggedIn()) {
			return $this->redirect(array('action' => 'index'));
		}

		if($this->request->is('post')) {
			$this->SupportTicket->id = $this->request->data['ticket'];

			if(!$this->SupportTicket->exists()) {
				$this->_ticketNotFound();

				return $this->Notice->error(__('Invalid ticket.'));
			}

			return $this->redirect(array('action' => 'view', $this->request->data['ticket']));
		}
	}

	public function index() {
		if(!$this->Auth->loggedIn()) {
			return $this->redirect(array('action' => 'notLoggedIn'));
		}

		$this->SupportTicket->contain(array('Owner', 'Department'));
		$this->paginate = array('order' => 'SupportTicket.created DESC');
		$tickets = $this->Paginator->paginate(array(
			'SupportTicket.user_id' => $this->Auth->user('id'),
		));

		$this->set(compact('tickets'));
	}

	public function add() {
		$settings = $this->Settings->fetch(array(
			'captchaOnSupport',
			'captchaType',
		));
		$this->set('captchaOnSupport', $settings['Settings']['captchaOnSupport'] && $settings['Settings']['captchaType'] != 'disabled');

		if($this->request->is('post')) {
			$this->SupportTicket->create();

			$this->request->data['SupportTicket']['status'] = SupportTicket::OPEN;

			if($this->SupportTicket->save($this->request->data)) {
				$email = ClassRegistry::init('Email');
				$email->setVariables(array(
					'%ticketsubject%' => $this->request->data['SupportTicket']['subject'],
					'%ticketurl%' => Router::url(array('plugin' => null, 'controller' => 'support', 'action' => 'view', $this->SupportTicket->id), true),
				));

				$email->send('Open support ticket', $this->request->data['SupportTicket']['email']);

				$this->Notice->success(__('Ticket successfully added. Please check e-mail address, you have provided, for further informations.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to add new ticket. Please, try again.'));
			}
		}

		if($this->Auth->loggedIn()) {
			$this->User->contain();
			$user = $this->User->findById($this->Auth->user('id'));
			$this->set(compact('user'));
		}

		$departments = $this->SupportDepartment->find('list');

		$this->set(compact('departments'));
	}

	public function view($id = null) {
		if(!$this->SupportTicket->exists($id)) {
			$this->_ticketNotFound();
			throw new NotFoundException(__d('exception', 'Invalid ticket'));
		}

		if($this->request->is(array('post', 'put'))) {
			$this->SupportTicket->contain();
			$ticket = $this->SupportTicket->findById($id, array('status'));

			if($ticket['SupportTicket']['status'] == SupportTicket::CLOSED) {
				throw new NotFoundException(__d('exception', 'Ticket is closed.'));
			}

			$this->SupportTicketAnswer->create();
			$this->request->data['SupportTicketAnswer']['ticket_id'] = $id;
			$this->request->data['SupportTicketAnswer']['sender_flag'] = SupportTicketAnswer::OWNER;
			if($this->SupportTicketAnswer->save($this->request->data)) {
				$this->Notice->success(__('Answer successfully saved.'));
				unset($this->request->data);
			} else {
				$this->Notice->error(__('Failed to save your answer. Please, try again.'));
			}
		}

		$this->SupportTicket->contain(array(
			'Owner' => array('username'),
			'Answers',
			'Department',
		));
		$ticket = $this->SupportTicket->find('first', array(
			'conditions' => array(
				'SupportTicket.id' => $id,
			)
		));

		$senderFlag = $this->SupportTicketAnswer->enum('sender_flag');

		$this->set(compact('ticket', 'senderFlag'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$conditions = $this->createPaginatorConditions(array(
			'Owner.username',
			'SupportTicket.id',
		));

		$this->SupportTicket->contain(array('Owner' => array('username'), 'Department' => array('name'), 'LastAnswer' => array('sender_flag')));
		$this->paginate = array('order' => 'SupportTicket.created DESC', 'fields' => array('id', 'full_name', 'subject', 'status', 'user_id', 'created', 'modified', 'LastAnswer.sender_flag'));
		$tickets = $this->Paginator->paginate($conditions);
		$statuses = $this->SupportTicket->enum('status');
		$departments = $this->SupportDepartment->find('list');

		$this->set(compact('statuses', 'tickets', 'departments'));
	}

/**
 * admin_close
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_close($id = null) {
		if(!$this->SupportTicket->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid ticket'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->SupportTicket->close($id)) {
			$this->Notice->success(__d('admin', 'The ticket has been closed.'));
		} else {
			$this->Notice->error(__d('admin', 'The ticket could not be closed. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_open
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_open($id = null) {
		if(!$this->SupportTicket->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid ticket'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->SupportTicket->open($id)) {
			$this->Notice->success(__d('admin', 'The ticket has been opened.'));
		} else {
			$this->Notice->error(__d('admin', 'The ticket could not be opened. Please, try again.'));
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
	public function admin_delete($id = null) {
		if(!$this->SupportTicket->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid ticket'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->SupportTicket->delete($id)) {
			$this->Notice->success(__d('admin', 'The ticket has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The ticket could not be deleted. Please, try again.'));
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

			if(!isset($this->request->data['SupportTicket']) || empty($this->request->data['SupportTicket'])) {
				$this->Notice->error(__d('admin', 'Please select at least one ticket.'));
				return $this->redirect($this->referer());
			}

			$ads = 0;
			foreach($this->request->data['SupportTicket'] as $id => $on) {
				if($on) {
					$ads++;
					if(!$this->SupportTicket->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid ticket.'));
					}
				}
			}

			foreach($this->request->data['SupportTicket'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'open':
							$this->SupportTicket->open($id);
						break;

						case 'close':
							$this->SupportTicket->close($id);
						break;

						case 'delete':
							$this->SupportTicket->delete($id);
						break;
					}
				}
			}
			if($ads) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one ticket.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect($this->referer());
	}


/**
 * admin_view method
 *
 * @return void
 */
	public function admin_view($id = null) {
		if(!$this->SupportTicket->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid ticket'));
		}

		if($this->request->is(array('post', 'put'))) {
			$this->SupportTicket->contain();
			$ticket = $this->SupportTicket->findById($id);

			$this->SupportTicketAnswer->create();
			$this->request->data['SupportTicketAnswer']['ticket_id'] = $id;
			$this->request->data['SupportTicketAnswer']['sender_flag'] = SupportTicketAnswer::ADMIN;
			if($this->SupportTicketAnswer->save($this->request->data)) {
				$names = explode(' ', $ticket['SupportTicket']['full_name']);
				$email = ClassRegistry::init('Email');
				$email->setVariables(array(
					'%firstname%' => $names[0],
					'%lastname%' => $names[1],
					'%username%' => $ticket['SupportTicket']['full_name'],
					'%ticketsubject%' => $ticket['SupportTicket']['subject'],
					'%ticketurl%' => Router::url(array('admin' => false, 'plugin' => null, 'controller' => 'support', 'action' => 'view', $id), true),
				));

				$email->send('New support ticket answer', $ticket['SupportTicket']['email']);

				$this->Notice->success(__d('admin', 'Answer successfully saved.'));

				if(isset($this->request->data['close'])) {
					if($this->SupportTicket->close($id)) {
						return $this->redirect(array('action' => 'index'));
					} else {
						$this->Notice->error(__d('admin', 'Failed to close ticket. Please, try again.'));
					}
				}

				unset($this->request->data);
			} else {
				$this->Notice->error(__d('admin', 'Failed to save your answer. Please, try again.'));
			}
		}

		$this->SupportTicket->contain(array(
			'Owner' => array('username'),
			'Answers',
			'Department',
		));
		$ticket = $this->SupportTicket->findById($id);

		if(empty($ticket)) {
			throw new NotFoundException(__d('exception', 'Invalid ticket'));
		}

		$ticket['SupportTicket']['last_action'] = $ticket['SupportTicket']['modified'];

		if(!empty($ticket['Answers'])) {
			$lastAnswer = reset($ticket['Answers']);
			if($lastAnswer['created'] > $ticket['SupportTicket']['last_action']) {
				$ticket['SupportTicket']['last_action'] = $lastAnswer['created'];
			}
		}
		$senderFlag = $this->SupportTicketAnswer->enum('sender_flag');
		$variables = $this->SupportTicketAnswer->getVariables();

		$cannedAnswersData = ClassRegistry::init('SupportCannedAnswer')->find('all', array(
			'fields' => array(
				'id',
				'name',
				'message',
			),
		));
		$cannedAnswers = Hash::combine($cannedAnswersData, '{n}.SupportCannedAnswer.id', '{n}.SupportCannedAnswer.name');
		$cannedAnswersData = json_encode(Hash::combine($cannedAnswersData, '{n}.SupportCannedAnswer.id', '{n}.SupportCannedAnswer'));

		$this->set(compact('ticket', 'senderFlag', 'variables', 'cannedAnswersData', 'cannedAnswers'));
	}

/**
 * admin_settings method
 *
 * @return void
 */
	public function admin_settings() {
		$keys = array(
			'supportRequireLogin',
			'supportMinMsgLen',
		);
		$globalKeys = array(
			'supportEnabled',
		);

		if($this->request->is(array('post', 'put'))) {
			if(isset($this->request->data['Settings'])) {
				if($this->Settings->store($this->request->data, $globalKeys, true)
				 && $this->Settings->store($this->request->data, $keys)) {
					$this->Notice->success(__d('admin', 'Settings saved successfully'));
				} else {
					$this->Notice->error(__d('admin', 'The settings could not be saved. Please, try again.'));
				}
			}

			if(isset($this->request->data['SupportDepartment'])) {
				if($this->SupportDepartment->saveMany($this->request->data['SupportDepartment'])) {
					$this->Notice->success(__d('admin', 'Departments saved successfully'));
				} else {
					$this->Notice->error(__d('admin', 'Failed to save featured ads packages. Please, try again.'));
				}
			}
		}
		$settings = $this->Settings->fetch($keys);

		foreach($globalKeys as $k) {
			$settings['Settings'][$k] = Configure::read($k);
		}

		$this->SupportDepartment->recursive = -1;
		$departments = $this->SupportDepartment->find('all');
		$departments = Hash::extract($departments, '{n}.SupportDepartment');

		$this->request->data = Hash::merge($settings, $this->request->data);
		if(isset($this->request->data['SupportDepartment'])) {
			$this->request->data['SupportDepartment'] = Hash::merge($departments, $this->request->data['SupportDepartment']);
		} else {
			$this->request->data['SupportDepartment'] = $departments;
		}

		$this->set('departmentsNo', count($departments));
	}
}
