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
App::uses('OfferwallsAppModel', 'Offerwalls.Model');
/**
 * OfferwallsMembership Model
 *
 * @property Membership $Membership
 */
class OfferwallsMembership extends OfferwallsAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'point_ratio' => array(
			'bcrange' => array(
				'rule' => array('bcrange', '0.0000', '999.9999'),
				'message' => 'Point ratio should be between %s and %s.',
				'allowEmpty' => false,
			),
		),
		'delay' => array(
			'8bit' => array(
				'rule' => array('comparison', '<=', 255),
				'message' => 'Delay cannot be longer than 255 days.',
				'allowEmpty' => false,
			),
		),
		'instant_limit' => array(
			'decimal' => array(
				'rule' => 'checkMonetary',
				'message' => 'Instant Limit should be a valid monetary value.',
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
		),
	);
}
