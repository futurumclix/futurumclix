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
 * AdGridMembershipsOption Model
 *
 * @property Membership $Membership
 */
class AdGridMembershipsOption extends AdGridAppModel {
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'clicks_per_day' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Clicks per day should be a valid natural number.',
				'allowEmpty' => false,
			),
		),
		'win_probability' => array(
			'between' => array(
				'rule' => array('between', -1, 1001),
				'message' => 'Probability should be between 0 and 1000.',
				'allowEmpty' => false,
			),
		),
		'prizes' => array(
			'prizesArray' => array(
				'rule' => array('checkPrizesArray'),
				'message' => 'You have to specify at least one prize',
				'allowEmpty' => false,
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Membership' => array(
			'className' => 'Membership',
			'foreignKey' => 'membership_id',
			'dependent' => false,
		),
	);

/**
 * beforeSave callback
 *
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if(isset($this->data[$this->alias]['prizes'])) {
			$this->data[$this->alias]['prizes'] = serialize(array_values($this->data[$this->alias]['prizes']));
		}
		return true;
	}

/**
 * afterFind callback
 *
 * @return array
 */
	public function afterFind($results, $primary = false) {
		if(isset($results['prizes'])) {
			$results['prizes'] = unserialize($results['prizes']);
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
 * checkPrizesArray method
 *
 * @return mixed (boolean/string)
 */
	public function checkPrizesArray($check = array()) {
		$val = array_values($check)[0];
		$errors = array();

		if(empty($val) || !is_array($val)) {
			return false;
		}

		foreach($val as $k => $v) {
			if(!isset($v['prize']) || (intval($v['prize']) !== 0 && empty($v['prize']))) {
				$errors[$k]['prize'] = __d('ad_grid_admin', 'Please enter a prize.');
			}

			if(!isset($v['points']) || (intval($v['points']) !== 0 && empty($v['points']))) {
				$errors[$k]['points'] = __d('ad_grid_admin', 'Please enter a points.');
			}

			if(!isset($v['probability']) || (intval($v['probability']) !== 0 && empty($v['probability']))) {
				$errors[$k]['probability'] = __d('ad_grid_admin', 'Please enter a probability.');
			}

			if(!$this->bcrange(array($v['probability']), '0', '100')) {
				$errors[$k]['probability'] = __d('ad_grid_admin', 'Probability should be between 0 and 100.');
			}

			if(!$this->checkMonetary(array($v['prize']))) {
				$errors[$k]['prize'] = __d('ad_grid_admin', 'Prize should be a valid monetary value');
			}

			if(!$this->checkPoints(array($v['points']))) {
				$errors[$k]['points'] = __d('ad_grid_admin', 'Points should be a valid decimal value');
			}
		}

		if(!empty($errors)) {
			$this->validationErrors['prizes'] = $errors;
			return false;
		}

		return true;
	}
}
