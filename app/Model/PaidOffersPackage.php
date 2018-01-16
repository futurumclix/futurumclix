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
 * PaidOffersPackage Model
 *
 */
class PaidOffersPackage extends AppModel {
/**
 * actsAs
 *
 * @var array
 */
	public $actsAs = array(
		'Packages',
	);

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'value';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'value' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Value should be a valid monetary value.',
				'allowEmpty' => false,
			),
		),
		'quantity' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Quantity should be a valid numeric value.',
				'allowEmpty' => false,
			),
			'range' => array(
				'rule' => array('range', -1, 4294967296),
				'message' => 'Quantity should be between 0 and 4294967295.',
				'allowEmpty' => false,
			),
		),
		'price' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Price should be a valid monetary value.',
				'allowEmpty' => false,
			),
		),
	);

/**
 * assign()
 *
 * @return boolean
 */
	public function assign($amount, $user_id, $packet_id) {
		$this->id = $packet_id;

		if(!$this->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid package'));
		}

		$item = array(
			'user_id' => $user_id,
			'model' => $this->alias,
			'foreign_key' => $packet_id,
		);

		return ClassRegistry::init('BoughtItem')->save(array('BoughtItem' => $item));
	}
}
