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
 * RevenueShareHistory Model
 *
 * @property Country $Country
 */
class RevenueShareHistory extends AppModel {
/**
 * Use table
 *
 * @var table name
 */
	public $useTable = 'revenue_share_history';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'outcome' => array(
			'decimal' => array(
				'rule' => array('checkMonetary', true),
				'message' => 'Outcome should be a decimal value',
			),
		),
		'income' => array(
			'decimal' => array(
				'rule' => array('checkMonetary', true),
				'message' => 'Income should be a decimal value',
			),
		),
	);

/**
 * add method
 *
 * @return boolean
 */
	public function add($income = '0', $outcome = '0', $created = null) {
		$created = ($created === null ? date('Y-m-d') : $created);

		$this->recursive = -1;
		$res = $this->updateAll(array(
			$this->alias.'.income' => '`'.$this->alias.'`'.'.`income` + '.$income,
			$this->alias.'.outcome' => '`'.$this->alias.'`'.'.`outcome` + '.$outcome,
		), compact('created'));

		if(!$res || $this->getAffectedRows() < 1) {
			$this->create();
			$this->recursive = -1;
			return $this->save(array($this->alias => compact('income', 'outcome', 'created')));
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
				$this->alias.'.created <=' <= $date,
			), true, false
		);
	}
}
