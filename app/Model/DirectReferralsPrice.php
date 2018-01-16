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
 * DirectReferralsPrice Model
 *
 */
class DirectReferralsPrice extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'amount' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Amount should be a numeric value',
				'allowEmpty' => false,
			),
			'comparison' => array(
				'rule' => array('comparison', '>=', 1),
				'message' => 'Amount should be more than 0',
			),
			'comparison' => array(
				'rule' => array('comparison', '<=', 65535),
				'message' => 'Amount should be less than 65535',
			),
		),
		'price' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Price should be a valid decimal value',
				'allowEmpty' => false,
			),
		),
	);
}
