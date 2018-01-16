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
App::uses('ForumAppController', 'Forum.Controller');
/**
 * Users Controller
 *
 * @property UserProfile $UserProfile
 * @property PaginatorComponent $Paginator
 */
class UsersController extends ForumAppController {

	public $uses = array(USER_MODEL);

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function profile($id = null) {
		if($id === null) {
			throw new NotFoundException(__d('forum', 'Invalid user profile'));
		}

		$userMap = Configure::read('User.fieldMap');

		$fields = array(
			'id',
			$userMap['signature'],
			$userMap['avatar'],
			$userMap['username'],
			$userMap['totalTopics'],
			$userMap['totalPosts'],
			$userMap['email'],
			$userMap['status'],
			'refs_count',
			'rented_refs_count',
			'forum_statistics',
			'location',
			'UserStatistic.total_clicks_earned',
			'UserStatistic.total_drefs_clicks_earned',
			'UserStatistic.total_rrefs_clicks_earned',
			'UserStatistic.total_cashouts',
			'UserStatistic.purchase_balance_cashouts',
			// TODO: add autopay and autorenew
		);

		$this->User->contain(array('UserStatistic'));

		if(is_numeric($id)) {
			$profile = $this->User->findById($id, $fields);
		} else {
			$profile = $this->User->findByUsername($id, $fields);
		}

		if(empty($profile)) {
			throw new NotFoundException(__d('forum', 'Invalid user profile'));
		}

		$profile['UserStatistic']['total_earned'] = $this->User->getEarnings($profile);

		$this->set(compact('profile'));
	}

/**
 * admin_ban method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_ban($id) {
		$this->User->id = $id;
		if(!$this->User->exists()) {
			throw new NotFoundException(__d('forum_admin', 'Invalid user'));
		}

		$res = $this->User->saveField(Configure::read('User.fieldMap.status'), Configure::read('User.statusMap.banned'));

		if($res) {
			$this->Notice->success(__d('forum_admin', 'User sucessfully banned.'));
		} else {
			$this->Notice->error(__d('forum_admin', 'Failed to ban user. Please, try again'));
		}
		return $this->redirect($this->referer());
	}

/**
 * admin_unban method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_unban($id) {
		$this->User->id = $id;
		if(!$this->User->exists()) {
			throw new NotFoundException(__d('forum_admin', 'Invalid user'));
		}

		$res = $this->User->saveField(Configure::read('User.fieldMap.status'), Configure::read('User.statusMap.active'));

		if($res) {
			$this->Notice->success(__d('forum_admin', 'User sucessfully unbanned.'));
		} else {
			$this->Notice->error(__d('forum_admin', 'Failed to unban user. Please, try again'));
		}
		return $this->redirect($this->referer());
	}
}
