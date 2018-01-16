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
 * UserMetadata Model
 *
 * @property User $User
 */
class UserMetadata extends AppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'user_id';

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		)
	);

/**
 * removeVerify method
 *
 * @return boolean
 */
	public function removeVerify($userId) {
		$this->id = $userId;
		$this->set(array('user_id'=> $userId, 'verify_token' => null, 'verify_expires' => null, 'verify_date' => date('Y-m-d H:i:s')));

		if($this->save()) {
			return true;
		}

		return false;
	}
 
/**
 * createWithVerify method
 *
 * @return boolean
 */
	public function createWithVerify($userId) {
		$this->id = $userId;
		$this->set(array('user_id'=> $userId, 'verify_token' => $this->createRandomStr(20), 'verify_expires' => date('Y-m-d H:i:s', strtotime('+1 week'))));

		if($this->save()) {
			return true;
		}

		return false;
	}

/**
 * createPasswordResetToken method
 *
 * @return mixed(boolean/string)
 */
	public function createPasswordResetToken($userId) {
		$newPassword = $this->createRandomStr(20);
		$this->id = $userId;
		$this->set(array('user_id'=> $userId, 'reset_token' => $newPassword));

		if($this->save()) {
			return $newPassword;
		}

		return false;
	}

/**
 * clearResetToken method
 *
 * @return boolean
 */
	public function clearResetToken($userId) {
		$this->id = $userId;
		$this->set(array('user_id'=> $userId, 'reset_token' => null));

		if($this->save()) {
			return true;
		}

		return false;
	}

/**
 * clearNextEmail method
 *
 * @return boolean
 */
	public function clearNextEmail($userId) {
		$this->id = $userId;
		$this->set(array('user_id' => $userId, 'verify_token' => null, 'verify_expires' => null, 'next_email' => null));

		if($this->save()) {
			return true;
		}

		return false;
	}

/**
 * addToAdminNote method
 *
 * @return boolean
 */
	public function addToAdminNote($note, $userId = null) {
		if(!$userId) {
			$userId = $this->id;
		}

		$oldNote = $this->findByUserId($userId, array($this->alias.'.admin_note'));

		$newNote = $oldNote[$this->alias]['admin_note']."\n".$note;

		$this->id = $userId;

		return $this->saveField('admin_note', $newNote);
	}
}
