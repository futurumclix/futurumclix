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
App::uses('Email', 'Model');
/**
 * PendingEmail Model
 *
 * @property User $User
 */
class PendingEmail extends Email {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'subject';

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'next_user',
			'order' => 'User.id',
			'contain' => array(),
		)
	);

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		unset($this->validate['name']);
	}

	public function send($email, $to) {
		if(empty($email[$this->alias])) {
			throw new NotFoundException(__d('exception', 'Wrong e-mail data'));
		}

		if(empty($this->variables['%email%'])) {
			$this->variables['%email%'] = $to;
		}

		$this->render($email);

		$cemail = new CakeEmail();
		$cemail->to($to)
				->from($email[$this->alias]['reply_to'], $email[$this->alias]['sender_name'])
				->emailFormat($email[$this->alias]['format'])
				->subject($email[$this->alias]['subject'])
				->send($email[$this->alias]['content']);
	}
}
