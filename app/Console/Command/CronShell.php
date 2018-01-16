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
class CronShell extends AppShell {
	public $uses = array(
		'Settings',
	);

	public function daily() {
		$this->out(__d('cron_console', 'Daily cron run: start at %s.', date('Y-m-d H:i:s')), 1, Shell::NORMAL);

		$this->dispatchShell('Users deleteUnverified');

		$this->dispatchShell('Users refreshAutopay');
		$this->dispatchShell('Users autoRenew');
		$this->dispatchShell('Users autoRecycleReferrals');

		if(Module::installed('AdGrid')) {
			$this->dispatchShell('AdGrid.Cleanup');
			$this->dispatchShell('AdGrid.Cleanup deleteInactive');
		}

		$this->dispatchShell('App setActiveStatsNumber');

		$this->dispatchShell('Ads clearVisitedAds');

		$this->dispatchShell('Forum.Subscription');

		$this->dispatchShell('Ads deleteInactive');
		$this->dispatchShell('BannerAds deleteInactive');
		$this->dispatchShell('FeaturedAds deleteInactive');
		$this->dispatchShell('LoginAds deleteInactive');
		$this->dispatchShell('PaidOffers deleteInactive');

		if(Module::active('BotSystem')) {
			$this->dispatchShell('BotSystem.Statistics cleanup');
		}

		$this->dispatchShell('Deposits purgePending');

		$end = date('Y-m-d H:i:s');

		$this->Settings->store(array('Settings' => array('cronDailyLast' => $end)), array('cronDailyLast'));
		$this->out(__d('cron_console', 'Daily cron run: end at %s.', $end), 1, Shell::NORMAL);
	}

	public function frequent() {
		$this->out(__d('cron_console', 'Frequent cron run: start at %s.', date('Y-m-d H:i:s')), 1, Shell::NORMAL);

		$this->dispatchShell('Ads cleanupClickHistory');
		$this->dispatchShell('Commissions credit');
		$this->dispatchShell('PaidOffers autoAccept');
		$this->dispatchShell('Users suspendInactive');
		$this->dispatchShell('Users deleteInactive');
		$this->dispatchShell('Users degrade');
		$this->dispatchShell('Users removeExpiredRentedReferrals');

		if(Module::active('Offerwalls')) {
			$this->dispatchShell('Offerwalls.Offerwalls pay');
		}

		if(Module::active('ReferralsContest')) {
			$this->dispatchShell('ReferralsContest.ReferralsContest pay');
		}

		if(Module::active('RevenueShare')) {
			$this->dispatchShell('RevenueShare.RevenueShare pay');
		}

		$this->dispatchShell('Email send');

		$end = date('Y-m-d H:i:s');

		$this->Settings->store(array('Settings' => array('cronFrequentLast' => $end)), array('cronFrequentLast'));
		$this->out(__d('cron_console', 'Frequent cron run: end at %s.', $end), 1, Shell::NORMAL);
	}

	public function botSystem() {
		if(Module::active('BotSystem')) {
			$this->out(__d('cron_console', 'BotSystem cron run: start at %s.', date('Y-m-d H:i:s')), 1, Shell::NORMAL);

			$this->dispatchShell('BotSystem.ClickSimulator');

			$end = date('Y-m-d H:i:s');

			$this->Settings->store(array('Settings' => array('cronBotLast' => $end)), array('cronBotLast'));
			$this->out(__d('cron_console', 'BotSystem cron run: end at %s.', $end), 1, Shell::NORMAL);
		}
	}
}
