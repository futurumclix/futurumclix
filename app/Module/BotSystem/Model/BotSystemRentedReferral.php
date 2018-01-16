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
App::uses('BotSystemAppModel', 'BotSystem.Model');
/**
 * BotSystemRentedReferral Model
 *
 */
class BotSystemRentedReferral extends BotSystemAppModel {

	public $useTable = false;

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = -1, $extra = array()) {
		if(isset($conditions['rented_upline_id'])) {
			$condStr = 'rented_upline_id = '.$conditions['rented_upline_id'];
		} else {
			$condStr = '1';
		}

		$orderStr = '';
		if(!empty($order)) {
			foreach($order as $k => $ord) {
				$orderStr[] = $k . ' ' . $ord;
			}
			$orderStr = 'ORDER BY '. implode(', ', $orderStr);
		}

		if(isset($extra['sort'])) {
			if(strncmp($extra['sort'], 'UserStatistic.', 14) === 0) {
				$extra['sort'] = substr($extra['sort'], 14);
			}
			if($extra['sort'] == 'clicks_avg') {
				$extra['sort'] = 'User__clicks_avg_as_rref';
			}

			if($orderStr == '') {
				$orderStr = "ORDER BY {$extra['sort']} {$extra['direction']}";
			} else {
				$orderStr .= ' '.$extra['sort'].' '.$extra['direction'];
			}
		}

		$startFrom = ($page - 1) * $limit;

		$userStatsModel = ClassRegistry::init('UserStatistic');
		$botsModel = ClassRegistry::init('BotSystem.BotSystemBot');
		$userModel = ClassRegistry::init('User');

		$sql = 'SELECT * FROM (
					SELECT u.id, u.username, u.rent_starts, u.rent_ends, s.last_click_date, s.clicks_as_rref, s.earned_as_rref, ROUND(s.clicks_as_rref / (COALESCE(DATEDIFF(CURDATE(), u.rent_starts), 0) + 1), 2) as User__clicks_avg_as_rref, id as url_id
					FROM `'.$userModel->tablePrefix.$userModel->table.'` as u JOIN `'.$userStatsModel->tablePrefix.$userStatsModel->table.'` as s ON(u.id = s.user_id) WHERE '.$condStr.'
					UNION ALL SELECT b.id, CONCAT(\'R\', b.id), b.rent_starts, b.rent_ends, b.last_click_as_rref, b.clicks_as_rref, b.earned_as_rref, ROUND(b.clicks_as_rref / (COALESCE(DATEDIFF(CURDATE(), b.rent_starts), 0) + 1), 2) as User__clicks_avg_as_rref, CONCAT(\'R\', b.id) as url_id FROM `'.$botsModel->tablePrefix.$botsModel->table.'` as b
					WHERE '.$condStr.') as User WHERE 1 = 1 '.$orderStr.' LIMIT '.$startFrom.', '.$limit;

		$data = $this->query($sql);

		foreach($data as &$d) {
			$d['User']['clicks_avg_as_rref'] = $d[0]['User__clicks_avg_as_rref'];
			$d['UserStatistic']['last_click_date'] = $d['User']['last_click_date'];
			$d['UserStatistic']['clicks_as_rref'] = $d['User']['clicks_as_rref'];
			$d['UserStatistic']['earned_as_rref'] = $d['User']['earned_as_rref'];
			unset($d[0]);
		}

		return $data;
	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$this->recursive = $recursive;

		if(isset($conditions['rented_upline_id'])) {
			$condStr = 'rented_upline_id = '.$conditions['rented_upline_id'];
		} else {
			$condStr = '1';
		}

		$userModel = ClassRegistry::init('User');
		$botsModel = ClassRegistry::init('BotSystem.BotSystemBot');

		$sql = 'SELECT COUNT(*) as u_count FROM `'.$userModel->tablePrefix.$userModel->table.'` WHERE '.$condStr;
		$dataU = $this->query($sql);

		$sql = 'SELECT COUNT(*) as b_count FROM `'.$botsModel->tablePrefix.$botsModel->table.'` WHERE '.$condStr;
		$dataB = $this->query($sql);

		return $dataU[0][0]['u_count'] + $dataB[0][0]['b_count'];
	}
}
