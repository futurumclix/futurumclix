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
 * SupportTicket Model
 *
 * @property User $User
 * @property Department $Department
 */
class SupportTicket extends AppModel {
/**
 * status values
 *
 * @const
 */
	const CLOSED = 0;
	const OPEN = 1;

/**
 * actAs
 *
 * @var array
 */
	public $actsAs = array(
		'Containable',
		'Utility.Enumerable',
	);

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'subject';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'id' => array(
			'uuid' => array(
				'rule' => array('uuid'),
				'allowEmpty' => false,
			),
		),
		'department_id' => array(
			'notEmpty' => array(
				'rule' => array('numeric'),
				'message' => 'Please select department.',
				'allowEmpty' => false,
			),
		),
		'full_name' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 71),
				'message' => 'Full name cannot be longer than 71 characters.',
				'allowEmpty' => false,
			),
		),
		'email' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 50),
				'message' => 'E-mail cannot be longer than 50 characters.',
				'allowEmpty' => false,
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please enter a valid e-mail address.',
				'allowEmpty' => false,
			),
		),
		'subject' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 100),
				'message' => 'Subject cannot be longer than 100 characters.',
				'allowEmpty' => false,
			),
		),
		'status' => array(
			'list' => array(
				'rule' => array('inList', array(self::OPEN, self::CLOSED)),
				'message' => 'Status can be only "Open" or "Closed".',
			),
		),
		'message' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Message cannot be empty.',
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
		'Owner' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'Department' => array(
			'className' => 'SupportDepartment',
			'foreignKey' => 'department_id',
		)
	);

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'LastAnswer' => array(
			'className' => 'SupportTicketAnswer',
			'foreignKey' => 'ticket_id',
			'order' => 'created',
			'limit' => 1,
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Answers' => array(
			'className' => 'SupportTicketAnswer',
			'foreignKey' => 'ticket_id',
			'order' => 'created',
			'dependent' => true,
		),
	);

/**
 * enum
 *
 * @var array
 */
	public $enum = array(
		'status' => array(
			self::OPEN => 'Open',
			self::CLOSED => 'Closed',
		),
	);

/**
 * constuctor
 *
 * @return void
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$minLen = ClassRegistry::init('Settings')->fetchOne('supportMinMsgLen');

		$this->validate['message']['minLen'] = array(
			'rule' => array('minLength', $minLen),
			'message' => __('Message should be at least %d character length.', $minLen),
		);
	}


/**
 * open method
 *
 * @return boolean
 */
	public function open($id = null) {
		if($id) {
			$this->id = $id;
		}

		$this->contain(array('Owner'));
		$this->read(array('email', 'subject', 'full_name', 'status'));

		if($this->data[$this->alias]['status'] != self::OPEN) {
			if(empty($this->data['Owner']) || $this->data['Owner']['role'] == 'Active') {
				$names = explode(' ', $this->data[$this->alias]['full_name']);
				$email = ClassRegistry::init('Email');
				$email->setVariables(array(
					'%firstname%' => $names[0],
					'%lastname%' => $names[1],
					'%username%' => $this->data[$this->alias]['full_name'],
					'%ticketsubject%' => $this->data[$this->alias]['subject'],
					'%ticketurl%' => Router::url(array('admin' => false, 'plugin' => null, 'controller' => 'support', 'action' => 'view', $id), true),
				));

				$email->send('Open support ticket', $this->data[$this->alias]['email']);
			}
		}

		return $this->saveField('status', self::OPEN);
	}

/**
 * close method
 *
 * @return boolean
 */
	public function close($id = null) {
		if($id) {
			$this->id = $id;
		}
		return $this->saveField('status', self::CLOSED);
	}
}
