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
 * AutopayHistory Model
 *
 * @property Country $Country
 */
class AutopayHistory extends AppModel {
/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'autopay_history';

/**
 * add method
 *
 * @return boolean
 */
	public function add($amount, $user_id, $created = null) {
		if(bccomp($amount, '0') == 0) {
			return true; /* ignore data with amount == 0 */
		}

		$created = ($created === null ? date('Y-m-d') : $created);
		$conditions = compact('user_id', 'created');

		$this->recursive = -1;
		$res = $this->updateAll(array(
			'amount' => '`'.$this->alias.'`'.'.`amount` + "'.$amount.'"'
		), $conditions);

		if(!$res || $this->getAffectedRows() < 1) {
			$this->create();

			return $this->save(array($this->alias => $conditions + compact('amount')));
		}

		return true;
	}

/**
 * deleteOld method
 *
 * @return boolean
 */
	public function deleteOld($days) {
		$date = date('Y-m-d H:i:s', strtotime("-$days days"));

		$this->recursive = -1;
		return $this->deleteAll(
			array(
				'created <=' <= $date,
			), true, false
		);
	}
}
