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
 * BotSystemStatistic Model
 *
 */
class BotSystemStatistic extends BotSystemAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'income' => array(
			'decimal' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Income should be a decimal value',
			),
		),
		'outcome' => array(
			'decimal' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Outcome should be a decimal value',
			),
		),
	);

/**
 * addOverallData method
 *
 * @return boolean
 */
	public function addOverallData($stats) {
		$do = false;
		$settings = ClassRegistry::init('BotSystem.BotSystemSettings');
		$bSStats = $settings->fetchOne('botSystemStats');

		if(isset($stats['income']) && bccomp($stats['income'], 0) > 0) {
			$bSStats['income'] = bcadd($stats['income'], $bSStats['income']);
			$do = true;
		}

		if(isset($stats['outcome']) && bccomp($stats['outcome'], 0) > 0) {
			$bSStats['outcome'] = bcadd($stats['outcome'], $bSStats['outcome']);
			$do = true;
		}

		if($do) {
			return $settings->store(array($settings->alias => array('botSystemStats' => $bSStats)), 'botSystemStats');
		}

		return true;
	}

/**
 * addData method
 *
 * @return boolean
 */
	public function addData($stats, $created = null) {
		$do = false;
		$created = ($created === null ? date('Y-m-d') : $created);

		$conditions = compact('created');

		$update = array();

		if(isset($stats['income']) && bccomp($stats['income'], 0) > 0) {
			$update['income'] = '`'.$this->alias.'`'.'.`income` + '.$stats['income'];
			$do = true;
		}

		if(isset($stats['outcome']) && bccomp($stats['outcome'], 0) > 0) {
			$update['outcome'] = '`'.$this->alias.'`'.'.`outcome` + '.$stats['outcome'];
			$do = true;
		}

		if(!$do) {
			return true;
		}

		$this->recursive = -1;
		$res = $this->updateAll($update, $conditions);

		if(!$res || $this->getAffectedRows() < 1) {
			$this->create();

			$this->recursive = -1;
			if(!$this->save(array($this->alias => $conditions + $stats))) {
				return false;
			}
		}

		return $this->addOverallData($stats);
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
				'created <=' => $date,
			), true, false
		);
	}
}
