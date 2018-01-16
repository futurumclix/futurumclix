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
class ReferralsContestController extends ReferralsContestAppController {
	public $uses = array(
		'ReferralsContest.ReferralsContest',
	);

	public $components = array(
		'Paginator',
		'UserPanel',
		'Payments',
	);

	public function beforeFilter() {
		parent::beforeFilter();
		if($this->request->params['action'] == 'admin_index' || $this->request->params['action'] == 'admin_edit') {
			$start = 0;
			$stop = 50;

			for(;$start < $stop; $start++) {
				$this->Security->unlockedFields[] = 'ReferralsContest.prizes.'.$start.'.prize';
				$this->Security->unlockedFields[] = 'ReferralsContest.prizes.'.$start.'.credit';
			}
		}
	}

	public function admin_index() {
		if($this->request->is(array('post', 'put'))) {
			if(isset($this->request->data['ReferralsContestSettings'])) {
				if($this->ReferralsContestSettings->store($this->request->data, 'referralsContest')) {
					$this->Notice->success(__d('referrals_contest_admin', 'Referrals contest settings saved successfully.'));
				} else {
					$this->Notice->error(__d('referrals_contest_admin', 'The settings could not be saved. Please, try again.'));
				}
			}
			if(isset($this->request->data['ReferralsContest'])) {
				if($this->ReferralsContest->save($this->request->data)) {
					$this->Notice->success(__d('referrals_contest_admin', 'Referrals contest successfully saved.'));
					unset($this->request->data['ReferralsContest']);
				} else {
					$this->Notice->error(__d('referrals_contest_admin', 'Failed to save new contest. Please, try again.'));
				}
			}
		}

		$contests = $this->Paginator->paginate('ReferralsContest.ReferralsContest');

		$settings = $this->ReferralsContestSettings->fetch('referralsContest');
		$this->request->data = Hash::merge($settings, $this->request->data);
		$this->set(compact('contests'));
	}

	public function admin_edit($id = null) {
		$this->ReferralsContest->recursive = -1;
		$contest = $this->ReferralsContest->findById($id);

		if(empty($contest)) {
			throw new NotFoundException(__d('referrals_contest_admin', 'Invalid contest'));
		}

		if($this->request->is(array('post', 'put'))) {
			$this->ReferralsContest->id = $id;

			if($this->ReferralsContest->save($this->request->data)) {
				$this->Notice->success(__d('referrals_contest_admin', 'Referrals contest saved successfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('referrals_contest_admin', 'Failed to save referrals contest. Please, try again.'));
			}
		} else {
			$this->request->data = $contest;
		}
	}

	public function admin_delete($id = null) {
		$this->request->allowMethod(array('post', 'put'));

		$this->ReferralsContest->id = $id;

		if(!$this->ReferralsContest->exists()) {
			throw new NotFoundException(__d('referrals_contest_admin', 'Invalid contest'));
		}

		if($this->ReferralsContest->delete()) {
			$this->Notice->success(__d('referrals_contest_admin', 'Referrals contest deleted successfully'));
		} else {
			$this->Notice->error(__d('referrals_contest_admin', 'Failed to delete referrals contest. Please, try again.'));
		}

		return $this->redirect($this->referer());
	}

	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {

			if(!isset($this->request->data['ReferralsContest']) || empty($this->request->data['ReferralsContest'])) {
				$this->Notice->error(__d('referrals_contest_admin', 'Please select at least one contest.'));
				return $this->redirect($this->referer());
			}

			$con = 0;
			foreach($this->request->data['ReferralsContest'] as $id => $on) {
				if($on) {
					$con++;
					if(!$this->ReferralsContest->exists($id)) {
						throw new NotFoundException(__d('referrals_contest_admin', 'Invalid contest'));
					}
				}
			}

			foreach($this->request->data['ReferralsContest'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'delete':
							$this->ReferralsContest->delete($id);
						break;
					}
				}
			}
			if($con) {
				$this->Notice->success(__d('referrals_contest_admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('referrals_contest_admin', 'Please select at least one contest.'));
			}
		} else {
			$this->Notice->error(__d('referrals_contest_admin', 'Please select an action.'));
		}
		$this->redirect($this->referer());
	}

	public function admin_view($id = null) {
		$this->ReferralsContest->recursive = 1;
		$contest = $this->ReferralsContest->findById($id);

		if(empty($contest)) {
			throw new NotFoundException(__d('referrals_contest_admin', 'Invalid contest'));
		}

		$banned = Hash::extract($contest['BannedUser'], '{n}.id');
		unset($contest['BannedUser']);

		$list = $this->ReferralsContest->getList($contest);
		$this->set(compact('contest', 'list', 'banned'));
	}

	public function admin_ban($user_id = null, $contest_id = null) {
		$this->request->allowMethod(array('post', 'put'));

		$this->ReferralsContest->recursive = -1;
		if(!$this->ReferralsContest->exists($contest_id)) {
			throw new NotFoundException(__d('referrals_contest_admin', 'Invalid contest'));
		}

		$this->ReferralsContest->BannedUser->contain();
		if(!$this->ReferralsContest->BannedUser->exists($user_id)) {
			throw new NotFoundException(__d('referrals_contest_admin', 'Invalid user'));
		}

		$data = array(
			'ReferralsContest' => array(
				'id' => $contest_id,
			),
			'BannedUser' => array(
				'id' => $user_id,
			),
		);

		if($this->ReferralsContest->save($data)) {
			$this->Notice->success(__d('referrals_contest_admin', 'User banned successfully.'));
		} else {
			$this->Notice->error(__d('referrals_contest_admin', 'Failed to ban user. Please, try again.'));
		}

		return $this->redirect($this->referer());
	}

	public function admin_unban($user_id = null, $contest_id = null) {
		$this->request->allowMethod(array('post', 'put'));

		if(!$this->ReferralsContest->exists($contest_id)) {
			throw new NotFoundException(__d('referrals_contest_admin', 'Invalid contest'));
		}

		if(!$this->ReferralsContest->BannedUser->exists($user_id)) {
			throw new NotFoundException(__d('referrals_contest_admin', 'Invalid user'));
		}

		$model = ClassRegistry::init('ReferralsContestBannedUser');

		$model->recursive = -1;
		$res = $model->deleteAll(array(
			'ReferralsContestBannedUser.referrals_contest_id' => $contest_id,
			'ReferralsContestBannedUser.user_id' => $user_id,
		));

		if($res) {
			$this->Notice->success(__d('referrals_contest_admin', 'User unbanned successfully.'));
		} else {
			$this->Notice->error(__d('referrals_contest_admin', 'Failed to unban user. Please, try again.'));
		}

		return $this->redirect($this->referer());
	}

	public function index() {
		$settings = $this->ReferralsContestSettings->fetchOne('referralsContest');

		$now = date('Y-m-d H:i:s');

		$this->ReferralsContest->recursive = -1;
		$current = $this->ReferralsContest->find('all', array(
			'conditions' => array(
				'ends >=' => $now,
				'starts <=' => $now,
			),
		));

		if($settings['showFinished']) {
			$this->ReferralsContest->recursive = -1;
			$old = $this->ReferralsContest->find('all', array(
				'conditions' => array(
					'ends <=' => $now,
					'starts <=' => $now,
				),
			));
			$this->set(compact('old'));
		}

		if($settings['showFuture']) {
			$this->ReferralsContest->recursive = -1;
			$future = $this->ReferralsContest->find('all', array(
				'conditions' => array(
					'ends >=' => $now,
					'starts >=' => $now,
				),
			));
			$this->set(compact('future'));
		}

		$this->set('breadcrumbTitle', __d('referrals_contest', 'Referral\'s Contest'));
		$this->set('user', $this->UserPanel->getData());
		$this->set(compact('current'));
	}

	public function view($id = null) {
		$this->ReferralsContest->recursive = 1;
		$contest = $this->ReferralsContest->findById($id);

		if(empty($contest)) {
			throw new NotFoundException(__d('referrals_contest', 'Invalid contest'));
		}

		$list = $this->ReferralsContest->getList($contest);
		$this->set(compact('contest', 'list'));

		$this->set('breadcrumbTitle', __d('referrals_contest', 'Referral\'s Contest'));
		$this->set('user', $this->UserPanel->getData());
	}
}
