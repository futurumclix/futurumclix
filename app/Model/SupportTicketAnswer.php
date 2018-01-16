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
App::uses('AppModel', 'Model');
/**
 * SupportTicketAnswer Model
 *
 * @property Ticket $Ticket
 */
class SupportTicketAnswer extends AppModel {
/**
 * actAs
 *
 * @var array
 */
	public $actsAs = array(
		'Utility.Enumerable',
	);

/**
 * Sender flags values
 *
 * @const
 */
	const OWNER = 0;
	const ADMIN = 1;

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'ticket_id';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'ticket_id' => array(
			'uuid' => array(
				'rule' => array('uuid'),
			),
		),
		'sender_flag' => array(
			'range' => array(
				'rule' => array('range', -1, 2),
			),
		),
		'message' => array(
			'minLength' => array(
				'rule' => array('notBlank'),
				'message' => 'Message cannot be empty',
				'allowEmpty' => false,
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Ticket' => array(
			'className' => 'SupportTicket',
			'foreignKey' => 'ticket_id',
		)
	);

/**
 * enum
 *
 * @var array
 */
	public $enum = array(
		'sender_flag' => array(
			self::OWNER => 'Owner',
			self::ADMIN => 'Admin',
		),
	);

	protected $variablesList = array(
		'%email%' => 'E-mail address',
		'%sitename%' => 'Site name',
		'%siteemail%' => 'Site contact e-mail',

		'%fullname%' => 'Full name',
		'%ticketsubject%' => 'Ticket subject',
		'%ticketurl%' => 'View ticket URL',
	);

	protected $variables = array();

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->reset();
	}

	public function getVariables() {
		$res = $this->variablesList;

		foreach($res as &$v) {
			$v = __($v);
		}

		return $res;
	}

	public function reset() {
		$this->variables = array(
			'%sitename%' => Configure::read('siteName'),
			'%siteemail%' => Configure::read('siteEmail'),
		);
	}

	public function setVariables($data = array()) {
		$this->variables = array_merge($this->variables, $data);
	}

	public function beforeSave($options = array()) {
		if($this->data[$this->alias]['sender_flag'] == self::ADMIN) {
			$ticket = $this->Ticket->findById($this->data[$this->alias]['ticket_id']);

			if(empty($ticket)) {
				throw new InternalErrorException(__d('exception', 'Invalid ticket_id'));
			}

			$this->setVariables(array(
				'%fullname%' => $ticket['Ticket']['full_name'],
				'%ticketsubject%' => $ticket['Ticket']['subject'],
				'%ticketurl%' => Router::url(array('admin' => false, 'plugin' => null, 'controller' => 'support', 'action' => 'view', $this->data[$this->alias]['ticket_id']), true),
			));

			$this->data[$this->alias]['message'] = str_replace(array_keys($this->variables), $this->variables, $this->data[$this->alias]['message']);
			$this->data[$this->alias]['message'] = str_replace(array_keys($this->variablesList), '', $this->data[$this->alias]['message']);
		}
		return true;
	}

	public function afterSave($created, $options = array()) {
		$this->Ticket->id = $this->data[$this->alias]['ticket_id'];

		$this->Ticket->saveField('modified', date('Y-m-d H:i:s'));
	}
}
