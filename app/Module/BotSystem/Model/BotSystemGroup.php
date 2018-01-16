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
App::uses('BotSystemAppModel', 'BotSystem.Model');
/**
 * BotSystemGroup Model
 *
 * @property Membership $Membership
 */
class BotSystemGroup extends BotSystemAppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'click_value' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Click value should be a valid monetary value.',
				'allowEmpty' => false,
			),
		),
		'min_clicks' => array(
			'range' => array(
				'rule' => array('range', -1, 256),
				'message' => 'Minimum clicks should be between 0 and 255.',
				'allowEmpty' => false,
			),
		),
		'max_clicks' => array(
			'range' => array(
				'rule' => array('range', -1, 256),
				'message' => 'Maximum clicks should be between 0 and 255.',
				'allowEmpty' => false,
			),
			'moreThanMin' => array(
				'rule' => array('comparisonWithField', '>', 'min_clicks'),
				'message' => 'Maximum clicks must be more than minimum clicks.'
			),
		),
		'skip_chance' => array(
			'range' => array(
				'rule' => array('range', -1, 101),
				'message' => 'Skip chance should be a valid percentage value (between 0 and 100).',
				'allowEmpty' => false,
			),
		),
		'max_avg' => array(
			'range' => array(
				'rule' => array('range', -1, 256),
				'message' => 'Maximum average should be between 0 and 255.',
				'allowEmpty' => false,
			),
		),
		'min_activity_days' => array(
			'range' => array(
				'rule' => array('range', -1, 65536),
				'message' => 'Minimum activity days should be between 0 and 65535',
				'allowEmpty' => false,
			),
		),
		'max_activity_days' => array(
			'range' => array(
				'rule' => array('range', -1, 65536),
				'message' => 'Maximum activity days should be between 0 and 65535',
				'allowEmpty' => false,
			),
			'moreThanMin' => array(
				'rule' => array('comparisonWithField', '>', 'min_activity_days'),
				'message' => 'Maximum activity days must be more than minimum activity days.'
			),
		),
		'stop_chance' => array(
			'range' => array(
				'rule' => array('range', -1, 101),
				'message' => 'Stop chance should be a valid percentage value (between 0 and 100).',
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
		)
	);
}
