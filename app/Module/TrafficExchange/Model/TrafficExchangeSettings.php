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
 * TrafficExchangeSettings Model
 *
 */
class TrafficExchangeSettings extends Settings {
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'trafficExchange' => array(
			'rule' => array('settingsArray', array(
				'autoApprove' => 'boolean',
				'checkConnection' => 'boolean',
				'checkConnectionTimeout' => array(
					'rule' => array('naturalNumber', false),
					'message' => 'Timeout should be a natural number.',
					'allowEmpty' => false,
				),
				'previewTime' => array(
					'rule' => array('naturalNumber', false),
					'message' => 'Preview time should be a natural number.',
					'allowEmpty' => false,
				),
				'activityMinimum' => array(
					'rule' => array('naturalNumber', true),
					'message' => 'Activity minimum should be a natural number (or 0 to turn off).',
					'allowEmpty' => false,
				),
			)),
		),
	);
}
