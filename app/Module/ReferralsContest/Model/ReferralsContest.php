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
App::uses('ReferralsContestAppModel', 'ReferralsContest.Model');
/**
 * ReferralsContest Model
 *
 */
class ReferralsContest extends ReferralsContestAppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'title';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'title' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 120),
				'message' => 'Title cannot be longer than 120 characters.',
				'allowEmpty' => false,
			),
		),
		'description' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 1024),
				'message' => 'Description cannot be longer than 1024 characters.',
			),
		),
		'starts' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'message' => 'Start date should be a valid datetime value.',
				'allowEmpty' => false,
			),
		),
		'ends' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'message' => 'End date should be a valid datetime value.',
				'allowEmpty' => false,
			),
		),
		'activity' => array(
			'range' => array(
				'rule' => array('range', -1, 5001),
				'message' => 'Activity should be between 0 and 5000.',
				'allowEmpty' => false,
			),
		),
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'BannedUser' => array(
			'className' => 'User',
			'unique' => true,
			'joinTable' => 'referrals_contest_banned_users',
			'fields' => array('BannedUser.id'),
			'unique' => false,
		),
	);

/**
 * beforeSave callback
 *
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if(!parent::beforeSave($options)) {
			return false;
		}

		if(isset($this->data[$this->alias]['prizes'])) {
			$this->data[$this->alias]['prizes'] = serialize($this->data[$this->alias]['prizes']);
		}
		return true;
	}

/**
 * afterFind callback
 *
 * @return array
 */
	public function afterFind($results, $primary = false) {
		$results = parent::afterFind($results, $primary);

		if(isset($results['prizes'])) {
			$results['prizes'] = unserialize($results['ads']);
		} else {
			for($i = 0, $e = count($results); $i < $e; ++$i) {
				if(isset($results[$i][$this->alias]['prizes'])) {
					$results[$i][$this->alias]['prizes'] = unserialize($results[$i][$this->alias]['prizes']);
				}
			}
		}
		return $results;
	}

/**
 * markAsPaid method
 *
 * @return boolean
 */
	public function markAsPaid($id) {
		$this->recursive = -1;
		return $this->updateAll(array(
			$this->alias.'.paid' => true
		), array(
			$this->alias.'.id' => $id,
		));
	}

/**
 * getList method
 *
 * @return array
 */
	public function getList($data = null, $limit = 30) {
		if($data) {
			$this->data = $data;
		}

		if(isset($this->data['BannedUser']) && !empty($this->data['BannedUser'])) {
			$banned = Hash::extract($this->data['BannedUser'], '{n}.id');
			$banned = 'AND upline_id NOT IN ('.implode(',', $banned).')';
		} else {
			$banned = '';
		}

		$userModel = ClassRegistry::init('User');
		$userStatsModel = ClassRegistry::init('UserStatistic');

		$qs = 'SELECT (SELECT @row_num := @row_num + 1) AS rank, IF(@score = User.refs_no, @rownum2 := @rownum2, @rownum2 := @row_num) `rank2`, @score:=User.refs_no `score`, Upline.username, Upline.id
		FROM (SELECT @row_num := 0, @score:=0, @rownum2:=0) AS r, (SELECT COUNT(*) as refs_no, upline_id FROM `'.$userModel->tablePrefix.$userModel->table.'` JOIN `'.$userStatsModel->tablePrefix.$userStatsModel->table.'` ON id = user_id WHERE upline_id IS NOT NULL AND dref_since >= "'.$this->data[$this->alias]['starts'].'" AND dref_since <= "'.$this->data[$this->alias]['ends'].'" AND `'.$userStatsModel->tablePrefix.$userStatsModel->table.'`.`total_clicks` >= '.$this->data[$this->alias]['activity'].' '.$banned.' GROUP BY upline_id ORDER BY refs_no DESC) AS User
		JOIN `'.$userModel->tablePrefix.$userModel->table.'` as Upline ON User.upline_id = Upline.id ORDER BY rank LIMIT '.$limit;

		return $userModel->query($qs);
	}

	public function pay() {
		$userModel = ClassRegistry::init('User');
		$now = date('Y-m-d H:i:s');

		$this->recursive = 1;
		$contests = $this->find('all', array(
			'conditions' => array(
				'starts <=' => $now,
				'ends <=' => $now,
				'paid' => false,
			),
		));

		foreach($contests as $contest) {
			$list = $this->getList($contest, count($contest[$this->alias]['prizes']));

			$this->markAsPaid($contest[$this->alias]['id']);

			foreach($list as $user) {
				$prize = $contest[$this->alias]['prizes'][$user[0]['rank2'] - 1];

				switch($prize['credit']) {
					case 'account':
						if(!$userModel->accountBalanceAdd($prize['prize'], $user['Upline']['id'])) {
							throw new InternalErrorException('Failed to add prize to %d account balance', $user['Upline']['id']);
						}
					break;

					case 'purchase':
						if(!$userModel->purchaseBalanceAdd($prize['prize'], $user['Upline']['id'])) {
							throw new InternalErrorException('Failed to add prize to %d purchase balance', $user['Upline']['id']);
						}
					break;

					default:
						throw new InternalErrorException('Unknown credit value!');
					break;
				}
			}
		}

		$this->recursive = -1;
		$this->updateAll(array(
			$this->alias.'.paid' => true
		), array(
			$this->alias.'.starts <=' => $now,
			$this->alias.'.ends <=' => $now,
			$this->alias.'.paid' => false
		));

		return true;
	}
}
