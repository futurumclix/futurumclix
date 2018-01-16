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
App::uses('AdGridAppModel', 'AdGrid.Model');
/**
 * AdGridWinHistory Model
 *
 * @property User $User
 */
class AdGridWinHistory extends AdGridAppModel {
/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'ad_grid_win_history';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'username' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Username cannot be blank',
			),
			'length' => array(
				'rule' => array('between', 3, 50),
				'message' => 'Username cannot be longer than 50 and shorter than 3 characters',
			),
			'alphaNum' => array(
				'rule' => 'alphaNumeric',
				'message' => 'Username must only contain letters and numbers',
			),
		),
		'prize' => array(
			'decimal' => array(
				'rule' => array('checkMonetary', true),
				'message' => 'Prize should be a decimal value',
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

/**
 * addNew method
 *
 * @return boolean
 */
	public function addNew($user_id, $username, $prize) {
		$this->create();

		$this->set(compact('user_id', 'username', 'prize'));

		return $this->save();
	}

/**
 * sumForUser method
 *
 * @return decimal
 */
	public function sumForUser($user_id) {
		$this->recursive = -1;
		$res = $this->find('first', array(
			'fields' => array('COALESCE(SUM(prize), 0) as sum'),
			'conditions' => compact('user_id')
		));

		return $res[0]['sum'];
	}

/**
 * sumForUserToday method
 *
 * @return decimal
 */
	public function sumForUserToday($user_id) {
		$this->recursive = -1;
		$res = $this->find('first', array(
			'fields' => array('COALESCE(SUM(prize), 0) as sum'),
			'conditions' => array(
				'user_id' => $user_id,
				'DATEDIFF(created, NOW())' => 0,
			),
		));

		return $res[0]['sum'];
	}

/**
 * getLastWinners method
 *
 * @return array
 */
	public function getLastWinners($days = 2) {
		$this->recursive = -1;
		return $this->find('all', array(
			'fields' => array(
				'username',
				'prize',
				'created as date',
			),
			'conditions' => array(
				'DATEDIFF(NOW(), created) <=' => $days,
			),
			'order' => 'created DESC',
		));
	}

/**
 * getLastMaxWinners method
 *
 * @return array
 */
	public function getLastMaxWinners($mainPrize, $days = 2) {
		$this->recursive = -1;
		return $this->find('all', array(
			'fields' => array(
				'username',
				'prize',
				'created as date',
			),
			'conditions' => array(
				'DATEDIFF(NOW(), created) <=' => $days,
				'prize' => $mainPrize,
			),
			'order' => 'created DESC',
		));
	}
}
