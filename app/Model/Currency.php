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
 * Currency Model
 *
 */
class Currency extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * Virtual fields
 *
 * @var array
 */
	public $virtualFields = array(
		'NameAndCode' => "CONCAT(Currency.name, ' (', Currency.code, ')')"
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'code' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Currency code cannot be empty',
				'allowEmpty' => false,
			),
			'minLength' => array(
				'rule' => array('minLength', 3),
				'message' => 'Currency code cannot be shorter than 3 characters',
				'allowEmpty' => false,
			),
			'maxLength' => array(
				'rule' => array('maxLength', 3),
				'message' => 'Currency code cannot be longer than 3 characters',
				'allowEmpty' => false,
			),
		),
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Currency name cannot be empty',
				'allowEmpty' => false,
			),
			'maxLength' => array(
				'rule' => array('maxLength', 65),
				'message' => 'Currency name cannot be longer than 32 characters',
				'allowEmpty' => false,
			),
		),
		'symbol' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Currency symbol cannot be empty',
				'allowEmpty' => false,
			),
			'maxLength' => array(
				'rule' => array('maxLength', 3),
				'message' => 'Currency symbol cannot be longer than 3 characters',
				'allowEmpty' => false,
			),
		),
		'iso_number' => array(
			'numeric' => array(
				'rule' => array('naturalNumber'),
				'message' => 'Currency ISO number should be a numeric value',
				'allowEmpty' => false,
			),
		),
		'step' => array(
			'money' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Real step value should be a valid monetary value',
				'allowEmpty' => false,
			),
		),
	);
}
