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
 * RentedReferralsPrice Model
 *
 * @property Membership $Membership
 */
class RentedReferralsPrice extends AppModel {
/**
 * INFINITE
 *
 * @const int
 */
	const INFINITE = 65535;

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'min' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber'),
				'message' => 'Range start should be a natural number',
				'allowEmpty' => false,
			),
			'lessThanMax' => array(
				'rule' => array('comparisonWithField', '<', 'max'),
				'message' => 'Start value has to be lower than end value'
			),
		),
		'max' => array(
			'comparison' => array(
				'rule' => array('comparison', '>=', -1),
				'message' => 'Range end should be a natural number',
				'allowEmpty' => false,
			),
			'moreThanMin' => array(
				'rule' => array('comparisonWithField', '>', 'min'),
				'message' => 'End value has to be bigger than start value',
			),
		),
		'price' => array(
			'decimal' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Price should be a decimal value',
				'allowEmpty' => false,
			),
			'comparison' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price has to be bigger or equal to zero',
			),
		),
		'autopay_price' => array(
			'decimal' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Price should be a decimal value',
				'allowEmpty' => false,
			),
			'comparison' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Price has to be bigger or equal to zero',
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
		)
	);

	private static function compare($data1, $data2) {
		if($data1['min'] == $data2['min']) {
			return 0;
		} 
		return ($data1['min'] < $data2['min']) ? -1 : 1;
	}

	private function sortArray(&$data) {
		return usort($data, array('RentedReferralsPrice', 'compare'));
	}

/**
 * validateArray
 *
 * @return mixed boolean/string
 */
	public function validateArray($data) {
		$this->sortArray($data);

		$min = 1;
		$membership_id = $data[0]['membership_id'];

		foreach($data as $price) {
			if($price['membership_id'] != $membership_id) {
				return __d('admin', 'Mixed memberships.');
			}
			if($price['min'] != $min) {
				return __d('admin', 'Ranges are not continuous.');
			}
			$min = $price['max'] + 1;
		}

		if($data[count($data) - 1]['max'] != RentedReferralsPrice::INFINITE) {
			return __d('admin', 'Last range should end at infinity.');
		}

		return true;
	}

/**
 * getRangeByRRefsNo
 *
 * @return array
 */
	public function getRangeByRRefsNo($data, $rrefs_no) {
		$this->sortArray($data);

		foreach($data as $price) {
			if($price['min'] <= $rrefs_no && $rrefs_no <= $price['max'] || $rrefs_no <= 0) {
				return $price;
			}
		}
		return null;
	}
}
