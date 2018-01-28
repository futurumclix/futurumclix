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
 * VisitedAd Model
 *
 * @property Ad $Ad
 * @property User $User
 */
class VisitedAd extends AppModel {

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Ad' => array(
			'className' => 'Ad',
			'foreignKey' => 'ad_id',
			'dependent' => false,
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'dependent' => false,
		)
	);

/**
 * clearVisitedAds
 *
 * @return boolean
 */
	public function clearVisitedAds($user_id = null) {
		$mode = ClassRegistry::init('Settings')->fetchOne('clearVisitedAds');

		ClassRegistry::init('ExpressAdsVisitedAd')->clearVisitedAds($user_id, $mode);
		ClassRegistry::init('ExplorerAdsVisitedAd')->clearVisitedAds($user_id, $mode);

		switch($mode) {
			case 'daily':
				return $this->clearVisitedAdsDaily($user_id);

			case 'first':
				return $this->clearVisitedAdsByFirst($user_id);

			case 'last':
				return $this->clearVisitedAdsByLast($user_id);

			case 'constPerUser':
				return $this->clearVisitedAdsConstPerUser($user_id);

			case 'accurate':
			default:
				return $this->clearVisitedAdsAccurate($user_id);
		}
	}

/**
 * deleteVisitedAdsByUser
 *
 * @return boolean
 */
	public function deleteVisitedAdsByUser($user_id) {
		ClassRegistry::init('ExpressAdsVisitedAd')->deleteVisitedAdsByUser($user_id);
		ClassRegistry::init('ExplorerAdsVisitedAd')->deleteVisitedAdsByUser($user_id);

		return $this->deleteAll(array(
			$this->alias.'.user_id' => $user_id,
		));
	}

/**
 * clearVisitedAdsAccurate
 *
 * @return boolean
 */
	protected function clearVisitedAdsAccurate($user_id) {
		$conditions = array(
			$this->alias.'.created <=' => date('Y-m-d H:i:s', time() - 24 * 60 * 60),
		);

		if($user_id !== null) {
			$conditions[$this->alias.'.user_id'] = $user_id;
		}

		return $this->deleteAll($conditions, false);
	}

/**
 * clearVisitedAdsDaily
 *
 * @return boolean
 */
	protected function clearVisitedAdsDaily($user_id) {
		$conditions = array(
			$this->alias.'.created <' => date('Y-m-d'),
		);

		if($user_id !== null) {
			$conditions[$this->alias.'.user_id'] = $user_id;
		}

		return $this->deleteAll($conditions, false);
	}

/**
 * clearVisitedAdsByFirst
 *
 * @return boolean
 */
	protected function clearVisitedAdsByFirst($user_id) {
		$table = $this->tablePrefix.$this->table;
		$query = "DELETE FROM `$table` WHERE user_id IN 
			(SELECT user_id FROM 
				(SELECT user_id, MIN(created) FROM `$table` WHERE DATE(created) = SUBDATE(current_date, 1) %AND% GROUP BY user_id) as c 
			WHERE created <= DATE_ADD(NOW(), INTERVAL -1 DAY))";

		if($user_id !== null) {
			$query = str_replace('%AND%', 'AND user_id = '.$user_id, $query);
		} else {
			$query = str_replace('%AND%', '', $query);
		}

		return $this->query($query);
	}

/**
 * clearVisitedAdsByLast
 *
 * @return boolean
 */
	protected function clearVisitedAdsByLast($user_id) {
		$table = $this->tablePrefix.$this->table;
		$query = "DELETE FROM `$table` WHERE user_id IN 
			(SELECT user_id FROM 
				(SELECT user_id, MAX(created) FROM `$table` WHERE DATE(created) = SUBDATE(current_date, 1) %AND% GROUP BY user_id) as c 
			WHERE created <= DATE_ADD(NOW(), INTERVAL -1 DAY))";

		if($user_id !== null) {
			$query = str_replace('%AND%', 'AND user_id = '.$user_id, $query);
		} else {
			$query = str_replace('%AND%', '', $query);
		}

		return $this->query($query);
	}

/**
 * clearVisitedAdsConstPerUser
 *
 * @return boolean
 */
	protected function clearVisitedAdsConstPerUser($user_id) {
		$userTable = $this->User->tablePrefix.$this->User->table;
		$visitedTable = $this->tablePrefix.$this->table;
		$time = date('H:i:s');
		$date = date('Y-m-d');

		$query = "DELETE v FROM `$visitedTable` as v JOIN `$userTable` as u ON v.user_id = u.id 
		 WHERE TIME(u.first_click) <= '$time' AND DATE(u.first_click) < '$date' AND TIME(u.first_click) >= TIME(v.created) %AND%";

		if($user_id !== null) {
			$query = str_replace('%AND%', 'AND v.user_id = '.$user_id, $query);
		} else {
			$query = str_replace('%AND%', '', $query);
		}

		return $this->query($query);
	}

/**
 * getDateVisitedByUser
 *
 * @return string/null
 */
	public function getDateVisitedByUser($user_id, $order = 'DESC', $include = array('ExplorerAdsVisitedAd', 'ExpressAdsVisitedAd')) {
		$this->recursive = -1;
		$res = $this->find('first', array(
			'conditions' => array(
				'user_id' => $user_id,
			),
			'fields' => array('created'),
			'order' => 'created '.$order,
		));

		if(empty($res)) {
			$result = null;
		} else {
			$result = $res[$this->alias]['created'];
		}

		foreach($include as $modelName) {
			$date = ClassRegistry::init($modelName)->getDateVisitedByUser($user_id, $order);

			if($date === null) {
				continue;
			}

			switch($order) {
				case 'DESC':
					if($result === null || $result < $date) {
						$result = $date;
					}
				break;

				case 'ASC':
					if($result === null || $result > $date) {
						$result = $date;
					}
				break;
			}
		}

		return $result;
	}
}
