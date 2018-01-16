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
class BotSystemController extends BotSystemAppController {
	public $uses = array(
		'BotSystem.BotSystemGroup',
		'BotSystem.BotSystemBot',
		'BotSystem.BotSystemStatistic',
	);

	public function admin_settings() {
		if($this->request->is(array('post', 'put'))) {
			if(isset($this->request->data['BotSystemSettings'])) {
				if($this->BotSystemSettings->store($this->request->data, 'botSystem')) {
					$this->Notice->success(__d('bot_system_admin', 'Bot system settings saved sucessfully.'));
				} else {
					$this->Notice->error(__d('bot_system_admin', 'The settings could not be saved. Please, try again.'));
				}
			}

			if(isset($this->request->data['BotSystemGroup'])) {
				if($this->BotSystemGroup->saveMany($this->request->data['BotSystemGroup'])) {
					$this->Notice->success(__d('bot_system_admin', 'Bot system settings saved sucessfully.'));
				} else {
					$this->Notice->error(__d('bot_system_admin', 'The settings could not be saved. Please, try again.'));
				}
			}
		}

		$settings = $this->BotSystemSettings->fetch('botSystem');
		$this->request->data = Hash::merge($settings, $this->request->data);

		$groups = $this->BotSystemGroup->find('all');
		$groups = Hash::combine($groups, '{n}.BotSystemGroup.membership_id', '{n}.BotSystemGroup');
		if(isset($this->request->data['BotSystemGroup'])) {
			$this->request->data['BotSystemGroup'] = Hash::merge($groups, $this->request->data['BotSystemGroup']);
		} else {
			$this->request->data['BotSystemGroup'] = $groups;
		}

		$memberships = ClassRegistry::init('Membership')->getList();
		$this->BotSystemBot->recursive = -1;
		$botsNumber = $this->BotSystemBot->find('count');
		$this->BotSystemBot->recursive = -1;
		$botsAvailable = $this->BotSystemBot->find('count', array(
			'conditions' => array(
				'rented_upline_id' => null,
			),
		));

		$this->set(compact('memberships', 'botsNumber', 'botsAvailable'));
	}

	public function admin_create() {
		$this->request->allowMethod(array('post', 'put'));

		if(!isset($this->request->data['no']) || empty($this->request->data['no'] || $this->request->data['no'] == 0)) {
			throw new InternalErrorException(__d('bot_system_admin', 'Wrong number of bots'));
		}

		$toSave = array_fill(0, $this->request->data['no'], array('created' => date('Y-m-d H:i:s')));

		if($this->BotSystemBot->saveMany($toSave)) {
			$this->Notice->success(__d('bot_system_admin', '%d bots created successfully.', $this->request->data['no']));
		} else {
			$this->Notice->error(__d('bot_system_admin', 'Failed to create bots. Please, try again.'));
		}

		return $this->redirect($this->referer());
	}
}
