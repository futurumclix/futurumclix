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
App::uses('Shell', 'Console');

class AppShell extends Shell {
	public $uses = array('Settings', 'UserStatistic');

	public function setActiveStatsNumber() {
		$this->out(__d('console', 'Setting active stats field number...'), 0, Shell::NORMAL);

		$todayNumber = $this->Settings->fetchOne('activeStatsNumber');

		$todayNumber = ($todayNumber + 1) % 7;

		$res = $this->UserStatistic->updateAll(array(
			'user_clicks_'.$todayNumber => '0',
			'dref_clicks_'.$todayNumber => '0',
			'dref_clicks_credited_'.$todayNumber => '0',
			'rref_clicks_'.$todayNumber => '0',
			'rref_clicks_credited_'.$todayNumber => '0',
			'clicks_as_dref_credited_'.$todayNumber => '0',
			'clicks_as_rref_credited_'.$todayNumber => '0',
		));

		if(!$res) {
			throw new InternalErrorException(__d('console', 'Failed to cleanup users statistics'));
		}

		if(CakePlugin::loaded('BotSystem')) {
			if(!ClassRegistry::init('BotSystem.BotSystemBot')->cleanupStatistics($todayNumber)) {
				throw new InternalErrorException(__d('console', 'Failed to cleanup bots statistics'));
			}
		}

		if(!$this->Settings->store(array('Settings' => array('activeStatsNumber' => $todayNumber)), array('activeStatsNumber'))) {
			throw new InternalErrorException(__d('console', 'Failed to set activeStatsNumber'));
		} else {
			$this->out(__d('console', 'done.', 1, Shell::NORMAL));
		}
	}

	protected function getLastQuery($model) {
		$dbo = $model->getDatasource();
		$logs = $dbo->getLog();
		$lastLog = end($logs['log']);
		return $lastLog['query'];
	}
}
