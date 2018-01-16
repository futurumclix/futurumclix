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
 * BotSystemSettings Model
 *
 */
class BotSystemSettings extends Settings {
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'botSystem' => array(
			'rule' => array('settingsArray', array(
				'activity' => 'boolean',
				'countNotCredited' => 'boolean',
				'autoAdd' => 'boolean',
				'autoAddMin' => array(
					'rule' => array('naturalNumber', true),
					'message' => 'Auto add minimum should be a valid natural number',
					'allowEmpty' => true,
				),
				'autoAddMax' => array(
					'moreThanAutoAddMin' => array(
						'rule' => array('comparisonWithField', '>', 'autoAddMin'),
						'message' => 'Auto add maximum should be more than auto add minimum',
						'allowEmpty' => true,
					),
					'natural' => array(
						'rule' => array('naturalNumber', true),
						'message' => 'Auto add maximum should be a valid natural number',
						'allowEmpty' => true,
					),
				),
				'statsCleanupDays' => array(
					'numeric' => array(
						'rule' => array('naturalNumber'),
						'message' => 'Bot System statistics cleanup days should be a valid natural number',
						'allowEmpty' => false,
					),
				),
			)),
		),
		'botSystemCronRun' => 'numeric',
		'botSystemStats' => array(
			'rule' => array('settingsArray', array(
				'income' => array('checkMonetary'),
				'outcome' => array('checkMonetary'),
			)),
		),
	);
}
