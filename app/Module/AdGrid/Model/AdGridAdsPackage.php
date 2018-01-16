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
 * AdGridAdsPackage Model
 *
 */
class AdGridAdsPackage extends AdGridAppModel {
/**
 * actsAs
 *
 * @var array
 */
	public $actsAs = array(
		'Packages',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'type' => array(
			'inList' => array(
				'rule' => array('inList', array('Clicks', 'Days')),
				'message' => 'Type have to be "%s" or "%s"',
			),
		),
		'amount' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Amount should be a numeric value',
				'allowEmpty' => false,
			),
			'nonNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Amount cannot be a negative number',
			),
		),
		'price' => array(
			'money' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Price should be non-negative decimal with max two places after dot',
			),
		),
	);


/**
 * getTypesList method
 *
 * @return array
 */
	public function getTypesList() {
		$res = array();

		foreach($this->validate['type']['inList']['rule'][1] as $v) {
			$res[$v] = __d('ad_grid', $v);
		}

		return $res;
	}

/**
 * assign()
 *
 * @return boolean
 */
	public function assign($amount, $user_id, $packet_id) {
		$this->id = $packet_id;

		if(!$this->exists()) {
			throw new NotFoundException(__d('ad_grid', 'Invalid package'));
		}

		$item = array(
			'user_id' => $user_id,
			'model' => 'AdGrid.AdGridAdsPackage',
			'foreign_key' => $packet_id,
		);

		if(ClassRegistry::init('BoughtItem')->save(array('BoughtItem' => $item))) {
			return true;
		}

		return false;
	}

/**
 * sumBoughtPackages method
 *
 * @return array
 */
	public function sumBoughtPackages() {
		return ClassRegistry::init('BoughtItem')->sumPackages('AdGrid.'.$this->alias);
	}
}
