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
 * UserMembership Model
 *
 */
class MembershipsUser extends AppModel {
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'dependent' => false,
		),
		'Membership' => array(
			'className' => 'Membership',
			'foreignKey' => 'membership_id',
			'dependent' => false,
		),
	);

/**
 * createDefault method
 *
 * @return boolean
 */
	public function createDefault($user_id) {
		$data = array(
			'user_id' => $user_id,
			'membership_id' => $this->Membership->getDefaultId(),
			'period' => 'Default',
			'begins' => date('Y-m-d H:i:s'),
		);
		$this->create($data);
		return $this->save();
	}

/**
 * buy method
 *
 * @return boolean
 */
	public function buy($user_id, $membership_id, $period) {
		$newMembership = array(
			'user_id' => $user_id,
			'membership_id' => $membership_id,
			'period' => $period,
			'begins' => date('Y-m-d H:i:s'),
			'ends' => date('Y-m-d H:i:s', strtotime('+ '.$period.' months'))
		);

		$this->create($newMembership);
		if(!$this->save()) {
			throw new InternalErrorException(__d('exception', 'Failed to save new membership'));
		}

		return true;
	}

/**
 * expand method
 *
 * @return boolean
 */
	public function expand($membershipsUserId, $userId, $membershipId, $period) {
		$this->id = $membershipsUserId;
		$this->recursive = -1;
		$this->read();

		$newdate = strtotime($this->data['MembershipsUser']['ends'].' + '.$period.' months');

		$this->data['MembershipsUser']['ends'] = date('Y-m-d H:i:s', $newdate);

		if(!$this->save()) {
			throw new InternalErrorException(__d('exception', 'Failed to save new membership'));
		}

		return true;
	}

/**
 * deleteLast method
 *
 * @return boolean
 */
	public function deleteLast($user_id) {
		$this->recursive = -1;
		$membership = $this->findByUserId($user_id, array('id', 'period'), 'begins DESC');

		if($membership['MembershipsUser']['period'] == 'Default') {
			return false;
		}

		if(!$this->delete($membership['MembershipsUser']['id']))
			throw new InternalErrorException(__d('exception', 'Failed to delete memberships user'));

		return true;
	}

/**
 * addNew method
 *
 * @return boolean
 */
	public function addNew($userId, $membershipId, $begins, $ends, $period = 'Admin') {
		$this->create(array(
			'user_id' => $userId,
			'membership_id' => $membershipId,
			'begins' => $begins,
			'ends' => $ends,
			'period' => $period,
		));
		return $this->save();
	}

/**
 * assign method
 *
 * @return boolean
 */
	public function assign($amount, $user_id, $membership_id, $duration) {
		$res = false;
		$period = $duration == '1' ? '1_month' : $duration.'_months';

		$membershipConditions = array(
			'Membership.id' => $membership_id,
			'Membership.'.$period.'_active' => true,
		);

		if($this->Membership->hasAny($membershipConditions)) {
			$this->User->id = $user_id;
			$this->User->contain(array('ActiveMembership'));
			$this->User->read(null);

			$actMembership = &$this->User->data['ActiveMembership'];
			if($actMembership['membership_id'] == $membership_id) {
				$res = $this->expand($actMembership['id'], $user_id, $membership_id, $duration);
			} else {
				$res = $this->buy($user_id, $membership_id, $duration);
			}
		}

		if($res) {
			$membership = $this->Membership->findById($membership_id, array('points_enabled', 'points_for_upgrade'));

			if($membership['Membership']['points_enabled']) {
				$this->User->pointsAdd($membership['Membership']['points_for_upgrade'], $user_id);
			}
		}

		return $res;
	}
}
