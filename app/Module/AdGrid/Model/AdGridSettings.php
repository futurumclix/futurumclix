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
App::uses('Settings', 'Model');
/**
 * AdGridSettings Model
 *
 */
class AdGridSettings extends Settings {
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'adGrid' => array(
			'rule' => array('settingsArray', array(
				'autoApprove' => 'boolean',
				'size' => array(
					'rule' => array('settingsArray', array(
						'width' => array(
							'rule' => array('range', 0, 101),
							'message' => 'Width should be between 1 and 100.',
							'allowEmpty' => false,
						),
						'height' => array(
							'rule' => array('range', 0, 101),
							'message' => 'Height should be between 1 and 100.',
							'allowEmpty' => false,
						),
					)),
				),
				'time' => array(
					'rule' => array('naturalNumber', true),
					'message' => 'Time should be a valid natural number.',
					'allowEmpty' => false,
				),
				'focus' => 'boolean',
				'delay' => array(
					'natural' => array(
						'rule' => array('naturalNumber', true),
						'message' => 'Time should be a natural number.',
						'allowEmpty' => false,
					),
				),
				'timeMode' => array(
					'list' => array(
						'rule' => array('inList', array('dual', 'immediately', 'afterLoad')),
						'message' => 'Wrong value for start timer.',
						'allowEmpty' => false,
					),
				),
				'payMode' => array(
					'list' => array(
						'rule' => array('inList', array('account', 'purchase')),
						'message' => 'Wrong value for pay mode.',
						'allowEmpty' => false,
					),
				),
			)),
		),
	);
}
