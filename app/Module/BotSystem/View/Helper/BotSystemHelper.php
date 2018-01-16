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
App::uses('AppHelper', 'View/Helper');

class BotSystemHelper extends AppHelper {
	public $helpers = array(
		'Chart',
	);

	public function statisticsChart($divOptions = array(), $jsOptions = array()) {
		$data = ClassRegistry::init('BotSystem.BotSystemStatistic')->find('all', array(
			'order' => 'created',
		));

		$data = array(
			'Income' => Hash::combine($data, '{n}.BotSystemStatistic.created', '{n}.BotSystemStatistic.income'),
			'Outcome' => Hash::combine($data, '{n}.BotSystemStatistic.created', '{n}.BotSystemStatistic.outcome'),
		);

		return $this->Chart->show($data, $divOptions + array('continous' => 'day'), $jsOptions);
	}

	public function getOverallStats() {
		return ClassRegistry::init('BotSystem.BotSystemSettings')->fetchOne('botSystemStats');
	}
}
