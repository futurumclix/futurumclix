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
class AdsShell extends AppShell {
	public $uses = array(
		'Ad',
		'VisitedAd',
		'ClickHistory',
	);

	public function clearVisitedAds() {
		$this->out(__d('console', 'clearing visited ads...'), 0, Shell::NORMAL);

		if($this->VisitedAd->clearVisitedAds()) {
			$this->out(__d('console', 'done.'), 1, Shell::NORMAL);
		} else {
			$this->out(__d('console', 'failed!'), 1, Shell::NORMAL);
		}
	}

	public function deleteInactive() {
		$days = $this->Settings->fetchOne('PTCDeleteAfter');

		$this->out(__d('console', 'Deleting inactive ads modified at least %d days ago...', $days), 0, Shell::NORMAL);

		if($this->Ad->deleteInactive($days)) {
			$this->out(__d('console', 'done, removed %d ads.', $this->Ad->getAffectedRows()), 1, Shell::NORMAL);
		} else {
			$this->out(__d('console', 'failed!'), 1, Shell::NORMAL);
		}
	}

	public function cleanupClickHistory() {
		$days = $this->Settings->fetchOne('PTCStatsDays');

		$this->out(__d('console', 'Deleting old click history (created at least %d days ago)...', $days), 0, Shell::NORMAL);

		if($this->ClickHistory->deleteOld($days)) {
			$this->out(__d('console', 'done, removed %d entries.', $this->ClickHistory->getAffectedRows()), 1, Shell::NORMAL);
		} else {
			$this->out(__d('console', 'failed!'), 1, Shell::NORMAL);
		}
	}
}
