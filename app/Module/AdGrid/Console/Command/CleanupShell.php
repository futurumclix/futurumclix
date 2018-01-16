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
class CleanupShell extends AppShell {
	public $uses = array(
		'AdGrid.AdGridUserClick',
		'AdGrid.AdGridSettings',
		'AdGrid.AdGridAd',
	);

	public function main() {
		if(!CakePlugin::loaded('AdGrid')) {
			$this->err('<error>AdGrid module is not installed, aborting!</error>');
			return;
		}

		$this->out(__d('ad_grid_console', 'Deleting old AdGrid data...'), 0, Shell::NORMAL);

		if($this->AdGridUserClick->deleteOld()) {
			$this->out(__d('ad_grid_console', 'done, removed %d entries.', $this->AdGridUserClick->getAffectedRows()), 1, Shell::NORMAL);
		} else {
			$this->out(__d('ad_grid_console', 'failed!'), 1, Shell::NORMAL);
		}
	}

	public function deleteInactive() {
		$days = $this->AdGridSettings->fetchOne('AdGridDeleteAfter');

		if(!is_numeric($days)) {
			$this->err('<error>AdGrid module is not setup correctlly, aborting!</error>');
			return;
		}

		$this->out(__d('ad_grid_console', 'Deleting inactive ads modified at least %d days ago...', $days), 0, Shell::NORMAL);

		if($this->AdGridAd->deleteInactive($days)) {
			$this->out(__d('ad_grid_console', 'done, removed %d ads.', $this->AdGridAd->getAffectedRows()), 1, Shell::NORMAL);
		} else {
			$this->out(__d('ad_grid_console', 'failed!'), 1, Shell::NORMAL);
		}
	}
}
