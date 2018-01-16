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
class EmailShell extends AppShell {
	public $uses = array('PendingEmail');

	public function send() {
		$this->out(__d('console', 'Sending queued emails...'), 0, Shell::NORMAL);

		$this->PendingEmail->recursive = -1;
		$email = $this->PendingEmail->find('first');

		if(empty($email)) {
			$this->out(__d('console', 'nothing to send, end.'), 1, Shell::NORMAL);
			return;
		}

		$this->PendingEmail->User->contain();
		$users = $this->PendingEmail->User->find('all', array(
			'conditions' => array(
				'User.id >=' => $email['PendingEmail']['next_user'],
				'User.created <' => $email['PendingEmail']['created'],
				'User.role' => 'Active',
				'User.allow_emails' => 1,
				'User.signup_ip NOT LIKE' => '192.168.%', //TODO: probably should be removed
			),
			'order' => 'User.id',
			'limit' => $this->Settings->fetchOne('emailsPerShell', 500),
		));

		if(empty($users)) {
			$this->PendingEmail->id = $email['PendingEmail']['id'];
			$this->PendingEmail->delete();
			$this->out(__d('console', 'removed old queue entry, end.'), 1, Shell::NORMAL);
			return $this->send();
		}

		foreach($users as $user) {
			$this->PendingEmail->setVariables(array(
				'%username%' => $user['User']['username'],
				'%firstname%' => $user['User']['first_name'],
				'%lastname%' => $user['User']['last_name'],
			));
			$this->PendingEmail->send($email, $user['User']['email']);
		}

		$last = array_pop($users);

		$email['PendingEmail']['next_user'] = $last['User']['id'] + 1;
		$this->PendingEmail->save($email);

		$this->out(__d('console', 'done.'), 1, Shell::NORMAL);
	}
}
