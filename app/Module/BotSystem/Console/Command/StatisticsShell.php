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
class StatisticsShell extends AppShell {
	public $uses = array(
		'BotSystem.BotSystemStatistic',
		'BotSystem.BotSystemSettings',
	);

	public function startup() {
		if(!CakePlugin::loaded('BotSystem')) {
			$this->err('<error>BotSystem module is not installed, aborting!</error>');
			return;
		}

		return parent::startup();
	}

	public function cleanup() {
		$settings = $this->BotSystemSettings->fetchOne('botSystem');

		if(!isset($settings['statsCleanupDays'])) {
			$days = 14;
		} else {
			$days = $settings['statsCleanupDays'];
		}

		$this->out(__d('bot_system_console', 'Removing statistics data older than %d days...', $days), 0, Shell::NORMAL);

		$this->BotSystemStatistic->deleteOld($days);

		$this->out(__d('bot_system_console', 'done.'), 1, Shell::NORMAL);
	}
}
