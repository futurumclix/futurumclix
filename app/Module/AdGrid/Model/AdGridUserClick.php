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
 * AdGridUserClick Model
 *
 * @property User $User
 */
class AdGridUserClick extends AdGridAppModel {

/**
 * Primary key field
 *
 * @var string
 */
	public $primaryKey = 'user_id';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'clicks' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
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
 * beforeSave callback
 *
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if(!parent::beforeSave($options)) {
			return false;
		}

		if(isset($this->data[$this->alias]['ads'])) {
			$this->data[$this->alias]['ads'] = implode('-', array_unique($this->data[$this->alias]['ads']));
		}
		if(isset($this->data[$this->alias]['fields'])) {
			$this->data[$this->alias]['fields'] = implode('-', array_unique($this->data[$this->alias]['fields']));
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

		if(isset($results['ads'])) {
			$results['ads'] = explode('-', $results['ads']);
		} else {
			for($i = 0, $e = count($results); $i < $e; ++$i) {
				if(isset($results[$i][$this->alias]['ads'])) {
					$results[$i][$this->alias]['ads'] = explode('-', $results[$i][$this->alias]['ads']);
				}
			}
		}
		if(isset($results['fields'])) {
			$results['fields'] = explode('-', $results['ads']);
		} else {
			for($i = 0, $e = count($results); $i < $e; ++$i) {
				if(isset($results[$i][$this->alias]['fields'])) {
					$results[$i][$this->alias]['fields'] = explode('-', $results[$i][$this->alias]['fields']);
				}
			}
		}
		return $results;
	}

/**
 * deleteOld method
 *
 * @return boolean
 */
	public function deleteOld() {
		return $this->deleteAll(array(
			$this->alias.'.created <' => date('Y-m-d'),
		));
	}
}
