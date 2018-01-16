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
class TestsShell extends AppShell {
	public $uses = array('User', 'Ad', 'AdsCategory', 'Membership', 'Ip2NationCountry', 'Admin', 'Settings');

	private $_urls = array(
		'http://example.com',
	);

	public function createAdmin() {
		$admin['Admin']['email'] = $this->_getInput(__d('console', 'Please enter an email address for new admin:'), null, 'admin@localhost');
		while(!isset($admin['Admin']['password']) || !$admin['Admin']['password']) {
			$admin['Admin']['password'] = $this->_getInput(__d('console', 'Please enter password:'), null, null);
		}
		$admin['Admin']['allowed_ips'] = $this->_getInput(__d('console', 'Please enter a comma separated list of valid allowed IP addresses'), null, '');
		$admin['Admin']['verify_token'] = null;

		$this->Admin->create();
		if($this->Admin->save($admin)) {
			$this->out(__d('console', 'New admin account sucessfully created.'), 1, Shell::NORMAL);
		} else {
			$this->out(__d('console', 'Failed to create admin account. Please try again.'), 1, Shell::NORMAL);
		}
	}

	public function createTestUsers() {
		if(!isset($this->args[0])) {
			die('Please specify number of users.');
		}
		$usersLimit = $this->args[0];
		$ipA = 0;
		$ipB = 1;

		if($usersLimit > 255 * 255) {
			$this->out('Maxium of test users is '.(255 * 255), 1, Shell::NORMAL);
		}

		$this->out('Try to create '.$usersLimit.' users', 0, Shell::NORMAL);

		for($i = 0; $i < $usersLimit; ++$i) {
			$this->User->create();

			$data['User']['username'] = "TestUser$i";
			$data['User']['password'] = "TestPassword$i";
			$data['User']['role'] = 'Un-verified';
			$data['User']['email'] = "User$i@test-nonexists.com";
			$data['User']['first_name'] = "User$i";
			$data['User']['last_name'] = "Test";
			$data['User']['acceptTos'] = true;
			$data['User']['signup_ip'] = "192.168.$ipA.$ipB";
			$data['User']['last_ip'] = "192.168.$ipA.$ipB";
			$data['User']['account_balance'] = 0;
			$data['User']['purchase_balance'] = 0;
			$data['User']['country'] = rand(2, 246); /* 1 => private */

			if($this->User->save($data)) {
				$lastId = $this->User->getLastInsertId();

				if($this->User->UserMetadata->removeVerify($lastId) && $this->User->activate($lastId)) {
					$this->out('.', 0, Shell::NORMAL);
					if(++$ipB >= 256)
					{
						$ipB = 0;
						++$ipA;
					}
					continue;
				}
			}

			$this->out(' - ERROR!', 1, Shell::NORMAL);
			debug($data);
			debug($this->User->validationErrors);
			return;

		}

		$this->out('Done!', 1, Shell::NORMAL);

	}

	public function verifyTestUsers() {
		$this->out('Verifing test users', 0, Shell::NORMAL);

		$users = $this->User->find('list', array(
			'conditions' => array(
				'User.signup_ip LIKE' => '%192.168.%',
				'User.last_name' => 'Test',
			),
		));

		foreach($users as $id => $name) {
			if(!$this->User->UserMetadata->removeVerify($id) || !$this->User->activate($id)) {
				throw new InternalErrorException(__d('console', 'Failed to activate user'));
			} else {
				$this->out('.', 0, Shell::NORMAL);
			}
		}
		$this->out('Done', 1, Shell::NORMAL);
	}

	public function deleteTestUsers() {
		return $this->User->deleteAll(array(
			'User.signup_ip LIKE' => '%192.168.%',
			'User.last_name' => 'Test',
		), true, true);
	}

	public function createTestAds() {
		if(!isset($this->args[0])) {
			die('Please specify number of ads.');
		}
		$adsLimit = $this->args[0];
		$urlsNo = count($this->_urls);
		$this->out('Try to create '.$adsLimit.' ads', 0, Shell::NORMAL);

		$categories = $this->AdsCategory->find('list');
		$categories = array_flip($categories);
		$catKeys = array_keys($categories);
		$catsNo = count($categories);

		$memberships = $this->Membership->find('list');
		foreach($memberships as $id => $name) {
			$adBase['TargettedMemberships']['TargettedMemberships'][] = $id;
		}

		$countries = $this->Ip2NationCountry->getCountriesList();
		foreach($countries as $id => $name) {
			$adBase['TargettedCountries']['TargettedCountries'][] = $id;
		}

		for($i = 0; $i < $adsLimit; ++$i) {
			$this->Ad->create();

			$newAd = $adBase;

			$newAd['Ad'] = array(
				'title' => "TestAd$i",
				'description' => "Test Ad $i Description",
				'url' => $this->_urls[rand(0, $urlsNo - 1)],
				'ads_category_id' => $categories[$catKeys[rand(0, $catsNo - 1)]],
				'expiry' => 10 + rand(0, 20),
				'package_type' => rand(0, 1) == 1 ? 'Days' : 'Clicks',
				'advertiser_id' => null
			);

			if(!$this->Ad->save($newAd)) {
				debug($newAd);
				throw new InternalErrorException(__d('console', 'Failed to save ad'));
			} else {
				$this->out('.', 0, Shell::NORMAL);
			}
		}

		$this->out('Done', 1, Shell::NORMAL);
	}

	public function deleteTestAds() {
		return $this->Ad->deleteAll(array(
			'Ad.title LIKE' => 'TestAd%',
			'Ad.description LIKE' => 'Test Ad % Description',
			'Ad.url IN' => $this->_urls,
			'Ad.advertiser_id' => null,
		), true, true);
	}

	public function clickAllTestUsers() {
		$this->User->contain(array('ActiveMembership'));
		$users = $this->User->find('all', array(
				'conditions' => array(
					'User.signup_ip LIKE' => '%192.168.%',
					'User.last_name' => 'Test',
				),
				'order' => 'RAND()',
			)
		);
		$ads = $this->Ad->find('all', array(
			'conditions' => array(
				'Ad.status' => 'Active',
			),
		));

		$this->out('Users:'.count($users));
		$this->out('Ads: '.count($ads));
		$uno = 0;

		foreach($users as $user) {
		   $ano = 0;
			foreach($ads as $ad) {
				$this->_clickUser($user, $ad);
				$this->out("User: $uno Ad: $ano\r", 0, Shell::NORMAL);
				++$ano;
			}
			++$uno;
		}
	}

	public function clickTestUserRand() {
		$this->User->contain(array('ActiveMembership'));
		$user = $this->User->find('first', array(
				'conditions' => array(
					'User.signup_ip LIKE' => '%192.168.%',
					'User.last_name' => 'Test',
				),
				'order' => 'RAND()',
			)
		);
		$ad = $this->Ad->getRandomAdForUser($user['User']['id'], $user['ActiveMembership']['membership_id'], $user['User']['country']);

		$this->out($user['User']['username'].' is trying to click '.$ad['Ad']['title'], 0, Shell::NORMAL);

		if($this->_clickUser($user, $ad)) {
			$this->out('Successfully clicked.', 1, Shell::NORMAL);
		} else {
			$this->out('...sorry, not this time.', 1, Shell::NORMAL);
		}
	}

	private function _magicStatsNumber($daysAgo = 0) {
		$magic = $this->Settings->fetch('activeStatsNumber')['Settings']['activeStatsNumber'];

		for($i = 0; $i < $daysAgo; $i++) {
			if(--$magic < 0) {
				$magic = 6;
			}
		}

		return $magic;
	}

	private function _clickUser($user, $ad) {
		if(!bcscale(8)) {
			throw new InternalErrorException(__d('console', 'Failed to set decimal scale'));
		}

		$days = rand(0, 7);

		$todayNumber = $this->_magicStatsNumber($days);

		if(!empty($ad)) {
			$this->Ad->AdsCategory->ClickValue->recursive = -1;
			$clickValues = $this->Ad->AdsCategory->ClickValue->find('first', array(
				'conditions' => array(
					'ClickValue.ads_category_id' => $ad['AdsCategory']['id'],
					'ClickValue.membership_id' => $user['ActiveMembership']['membership_id'],
				),
			));
			if(empty($clickValues)) {
				throw new InternalErrorException(__d('console', 'ClickValues not found'));
			}

			if(!$this->User->UserStatistic->updateAll(array(
				"user_clicks_$todayNumber" => "user_clicks_$todayNumber + 1",
				'total_clicks' => 'total_clicks + 1',
				'last_click_date' => 'NOW()',
			), array(
				'user_id' => $user['User']['id'],
			))) {
				throw new InternalErrorException(__d('console', 'Cannot save statistic data'));
			}

			$this->User->id = $user['User']['id'];
			$data = array(
				'User' => array(
					'account_balance' => bcadd($user['User']['account_balance'], $clickValues['ClickValue']['user_click_value']),
				),
			);
			if(!$this->User->save($data, true, ['account_balance'])) {
				throw new InternalErrorException(__d('console', 'Cannot save user data'));
			}
			if(($this->User->Upline->id = $user['User']['upline_id']) != null) {
				if($this->User->Upline->exists()) {
					if(!$this->User->UserStatistic->updateAll(array(
						"dref_clicks_$todayNumber" => "user_clicks_$todayNumber + 1",
						'total_drefs_clicks' => 'total_drefs_clicks + 1',
					), array(
						'user_id' => $this->User->Upline->id,
					))) {
						throw new InternalErrorException(__d('console', 'Cannot save statistic data'));
					}
					$this->User->Upline->contain('ActiveMembership.Membership.ClickValue.ads_category_id = '.$ad['AdsCategory']['id']);
					$this->User->Upline->read();
					$upline_account_balance = bcadd($this->User->Upline->data['User']['account_balance'], $this->User->Upline->data['ActiveMembership']['Membership']['ClickValue'][0]['direct_referral_click_value']);
					if(!$this->User->Upline->saveField('account_balance', $upline_account_balance)) {
						throw new InternalErrorException(__d('console', 'Cannot save upline data'));
					}
				}
			}

			if(($this->User->RentedUpline->id = $user['User']['rented_upline_id']) != null) {
				if($this->User->RentedUpline->exists()) {
					if(!$this->User->UserStatistic->updateAll(array(
						"rref_clicks_$todayNumber" => "user_clicks_$todayNumber + 1",
						'total_rrefs_clicks' => 'total_rrefs_clicks + 1',
					), array(
						'user_id' => $this->User->RentedUpline->id,
					))) {
						throw new InternalErrorException(__d('console', 'Cannot save statistic data'));
					}
					$this->User->Upline->contain('ActiveMembership.Membership.ClickValue.ads_category_id = '.$ad['AdsCategory']['id']);
					$this->User->RentedUpline->read();
					$rented_upline_account_balance = bcadd($this->User->RentedUpline->data['User']['account_balance'], $this->User->RentedUpline->data['ActiveMembership']['Membership']['ClickValue'][0]['rented_referral_click_value']);
					if(!$this->User->RentedUpline->saveField('account_balance', $rented_upline_account_balance)) {
						throw new InternalErrorException(__d('console', 'Cannot save upline data'));
					}
				}
			}

			

			$this->User->VisitedAds->create();
			$data = array(
				'VisitedAds' => array(
					'user_id' => $user['User']['id'],
					'ad_id' => $ad['Ad']['id'],
					'created' => date("Y-m-d H:i:s", strtotime("- $days days")),
				),
			);
			if(!$this->User->VisitedAds->save($data)) {
				throw new InternalErrorException(__d('console', 'Failed to mark ad visted'));
			}

			$this->Ad->id = $ad['Ad']['id'];
			$adData = array(
				'clicks' => $ad['Ad']['clicks'] + 1,
			);

			if(($ad['Ad']['package_type'] === 'Clicks')) {
				$adData['expiry'] = $ad['Ad']['expiry'] - 1;

				if($ad['Ad']['expiry'] - 1 <= 0) {
					$adData['status'] = 'Inactive';
				}
			}

			if(!$this->Ad->save(['Ad' => $adData], true, ['clicks', 'expiry', 'status'])) {
				throw new InternalErrorException(__d('console', 'Cannot save ad data'));
			}
			return true;
		} else {
			return false;
		}
	}
}
