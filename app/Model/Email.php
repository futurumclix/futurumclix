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
App::uses('CakeEmail', 'Network/Email');
/**
 * Email Model
 *
 */
class Email extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 50),
				'message' => 'E-mail name cannot be longer than 50 characters',
				'allowEmpty' => false,
			),
		),
		'format' => array(
			'inList' => array(
				'rule' => array('inList', array(CakeEmail::MESSAGE_TEXT, CakeEmail::MESSAGE_HTML)),
				'message' => 'Format can only be "text" or "html"',
				'allowEmpty' => false,
			),
		),
		'subject' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 100),
				'message' => 'Subject cannot be longer than 50 characters',
			),
		),
	);

	protected $variablesList = array(
		'%email%' => 'E-mail address',
		'%sitename%' => 'Site name',
		'%siteemail%' => 'Site contact e-mail',

		'%username%' => 'Username',
		'%firstname%' => 'First name',
		'%lastname%' => 'Last name',
		'%verifyurl%' => 'Verify link',
		'%confirmationurl%' => 'Confirmation link',
		'%password%' => 'New password',
		'%amount%' => 'Amount',
		'%gateways%' => 'Gateways list',
		'%gateway%' => 'Gateway name',
		'%adstatus%' => 'Ad status',
		'%adtitle%' => 'Ad title',
		'%ticketsubject%' => 'Ticket subject',
		'%ticketurl%' => 'View ticket URL',
		'%cheatersData%' => 'Cheaters data',
		'%comesFrom%' => 'Comes from',
		'%uplineusername%' => 'Upline username',
		'%outsidestats%' => 'Ad statistics URL',
	);

	protected $variables = array();

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->reset();
	}

	public function getFormats() {
		$types = $this->validate['format']['inList']['rule'][1];
		$res = array();

		foreach($types as $v) {
			$res[$v] = __(ucfirst($v));
		}

		return $res;
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
		if(isset($data['%amount%'])) {
			$data['%amount%'] = CurrencyFormatter::format($data['%amount%']);
		}
		$this->variables = array_merge($this->variables, $data);
	}

	protected function render(&$email) {
		$email[$this->alias]['content'] = str_replace(array_keys($this->variables), $this->variables, $email[$this->alias]['content']);
		$email[$this->alias]['content'] = str_replace(array_keys($this->variablesList), '', $email[$this->alias]['content']);
		$email[$this->alias]['subject'] = str_replace(array_keys($this->variables), $this->variables, $email[$this->alias]['subject']);
		$email[$this->alias]['subject'] = str_replace(array_keys($this->variablesList), '', $email[$this->alias]['subject']);
	}

	public function send($name, $to) {
		$this->recursive = -1;
		$email = $this->findByName($name);

		if(empty($email)) {
			throw new NotFoundException(__d('exception', 'Wrong e-mail data'));
		}

		if(empty($this->variables['%email%'])) {
			$this->variables['%email%'] = $to;
		}

		if($email[$this->alias]['format'] == 'html') {
			foreach($this->variables as $name => &$data) {
				$data = nl2br($data);
			}
		}

		$this->render($email);

		$cemail = new CakeEmail();
		$cemail->to($to)
				->from(Configure::read('siteEmail'), Configure::read('siteEmailSender'))
				->emailFormat($email[$this->alias]['format'])
				->subject($email[$this->alias]['subject'])
				->send($email[$this->alias]['content']);
	}
}

