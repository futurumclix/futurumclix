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
 * BotSystemBot Model
 *
 * @property RentedUpline $RentedUpline
 */
class BotSystemBot extends BotSystemAppModel {
/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'RentedUpline' => array(
			'className' => 'User',
			'foreignKey' => 'rented_upline_id',
			'counterCache' => 'rented_bots_count',
		),
	);

	public $virtualFields = array(
		'clicks_avg' => 'ROUND(`BotSystemBot`.`clicks_as_rref` / (COALESCE(DATEDIFF(CURDATE(), `BotSystemBot`.`rent_starts`), 0) + 1), 2)',
		'rent_ends_days' => 'DATEDIFF(`BotSystemBot`.`rent_ends`, NOW())',
	);

	public function getStatistics($id = null) {
		if($id == null) {
			$id = $this->id;
		}

		if(!$this->exists($id)) {
			throw new NotFoundException(__d('bot_system', 'RentedReferral not found'));
		}

		$this->recursive = -1;
		$d = $this->findById($id, array(
			$this->alias.'.earned_as_rref',
			$this->alias.'.clicks_as_rref_0 as user_clicks_0',
			$this->alias.'.clicks_as_rref_1 as user_clicks_1',
			$this->alias.'.clicks_as_rref_2 as user_clicks_2',
			$this->alias.'.clicks_as_rref_3 as user_clicks_3',
			$this->alias.'.clicks_as_rref_4 as user_clicks_4',
			$this->alias.'.clicks_as_rref_5 as user_clicks_5',
			$this->alias.'.clicks_as_rref_6 as user_clicks_6',
			$this->alias.'.clicks_as_rref_credited_0',
			$this->alias.'.clicks_as_rref_credited_1',
			$this->alias.'.clicks_as_rref_credited_2',
			$this->alias.'.clicks_as_rref_credited_3',
			$this->alias.'.clicks_as_rref_credited_4',
			$this->alias.'.clicks_as_rref_credited_5',
			$this->alias.'.clicks_as_rref_credited_6',
		));

		return $d[$this->alias];
	}

	public function countNotRentedBots() {
		$this->recursive = -1;
		return $this->find('count', array(
			'conditions' => array(
				$this->alias.'.rented_upline_id' => null,
			),
		));
	}

	public function assignRentedRefsBots($upline_id, $limit, $days) {
		$this->recursive = -1;
		$refs = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.rented_upline_id' => null,
			),
			'order' => 'RAND()',
			'limit' => $limit,
		));
		if(count($refs) == $limit) {
			$this->recursive = -1;
			if($this->updateAll(array(
					$this->alias.'.rented_upline_id' => $upline_id,
					$this->alias.'.rent_ends' => "NOW() + INTERVAL $days day",
					$this->alias.'.rent_starts' => 'NOW()',
				), array(
					$this->alias.'.id' => array_keys($refs),
				)
			)) {
				$this->RentedUpline->id = $upline_id;
				$this->RentedUpline->set(array('last_rent_action' => date('Y-m-d H:i:s')));
				$this->RentedUpline->save(null, array('fieldList' => array('last_rent_action')));
				$this->updateCounterCache(array('rented_upline_id' => $upline_id));
				return true;
			}
		}
		return false;
	}

	public function unhookByUplineId($upline_id) {
		$this->recursive = -1;
		$res = $this->updateAll(array(
			$this->alias.'.rented_upline_id' => null,
			$this->alias.'.rent_starts' => null,
			$this->alias.'.rent_ends' => null,
			$this->alias.'.last_click_as_rref' => null,
			$this->alias.'.today_done' => false,
			$this->alias.'.active' => true,
			$this->alias.'.active_days' => 0,
			$this->alias.'.auto_renew_attempts' => 0,
			$this->alias.'.earned_as_rref' => 0,
			$this->alias.'.clicks_as_rref' => 0,
			$this->alias.'.clicks_as_rref_credited' => 0,
			$this->alias.'.clicks_as_rref_0' => 0,
			$this->alias.'.clicks_as_rref_1' => 0,
			$this->alias.'.clicks_as_rref_2' => 0,
			$this->alias.'.clicks_as_rref_3' => 0,
			$this->alias.'.clicks_as_rref_4' => 0,
			$this->alias.'.clicks_as_rref_5' => 0,
			$this->alias.'.clicks_as_rref_6' => 0,
			$this->alias.'.clicks_as_rref_credited_0' => 0,
			$this->alias.'.clicks_as_rref_credited_1' => 0,
			$this->alias.'.clicks_as_rref_credited_2' => 0,
			$this->alias.'.clicks_as_rref_credited_3' => 0,
			$this->alias.'.clicks_as_rref_credited_4' => 0,
			$this->alias.'.clicks_as_rref_credited_5' => 0,
			$this->alias.'.clicks_as_rref_credited_6' => 0,
		), array(
			$this->alias.'.rented_upline_id' => $upline_id,
		));

		if($res) {
			$this->updateCounterCache(array('rented_upline_id' => $upline_id));
			return true;
		}

		return false;
	}

	public function removeExpiredRentedReferrals($date = null) {
		if($date === null) {
			$date = date('Y-m-d H:i:s');
		}

		$conditions = array(
			$this->alias.'.rent_ends <=' => $date,
		);

		$this->recursive = -1;
		$toUpdate = $this->find('all', array(
			'fields' => array('DISTINCT(rented_upline_id)'),
			'conditions' => $conditions,
		));

		$toUpdate = Hash::extract($toUpdate, '{n}.'.$this->alias.'.rented_upline_id');

		$this->recursive = -1;
		if(!$this->updateAll(array(
			$this->alias.'.rented_upline_id' => null,
			$this->alias.'.rent_starts' => null,
			$this->alias.'.rent_ends' => null,
			$this->alias.'.last_click_as_rref' => null,
			$this->alias.'.today_done' => false,
			$this->alias.'.active' => true,
			$this->alias.'.active_days' => 0,
			$this->alias.'.auto_renew_attempts' => 0,
			$this->alias.'.earned_as_rref' => 0,
			$this->alias.'.clicks_as_rref' => 0,
			$this->alias.'.clicks_as_rref_credited' => 0,
			$this->alias.'.clicks_as_rref_0' => 0,
			$this->alias.'.clicks_as_rref_1' => 0,
			$this->alias.'.clicks_as_rref_2' => 0,
			$this->alias.'.clicks_as_rref_3' => 0,
			$this->alias.'.clicks_as_rref_4' => 0,
			$this->alias.'.clicks_as_rref_5' => 0,
			$this->alias.'.clicks_as_rref_6' => 0,
			$this->alias.'.clicks_as_rref_credited_0' => 0,
			$this->alias.'.clicks_as_rref_credited_1' => 0,
			$this->alias.'.clicks_as_rref_credited_2' => 0,
			$this->alias.'.clicks_as_rref_credited_3' => 0,
			$this->alias.'.clicks_as_rref_credited_4' => 0,
			$this->alias.'.clicks_as_rref_credited_5' => 0,
			$this->alias.'.clicks_as_rref_credited_6' => 0,
		), $conditions)) {
			return false;
		}

		foreach($toUpdate as $uplineId) {
			$this->updateCounterCache(array('rented_upline_id' => $uplineId));
		}

		return true;
	}

	public function getRandomNotRented($not_in = array(), $limit) {
		$this->recursive = -1;
		$refs = $this->find('all', array(
			'fields' => array(
				$this->alias.'.id',
			),
			'conditions' => array(
				$this->alias.'.rented_upline_id' => null,
				$this->alias.'.id !=' => $not_in,
			),
			'limit' => $limit,
			'order' => 'RAND()',
		));

		return Hash::extract($refs, '{n}.'.$this->alias.'.id');
	}

	public function recycleReferralsBots($upline_id, $rrefs, $rrefs_no = null) {
		if($rrefs_no === null) {
			$rrefs_no = count($rrefs);
		}

		$skip = Hash::extract($rrefs, '{n}.'.$this->alias.'.id');

		$new = $this->getRandomNotRented($skip, $rrefs_no);

		if(count($new) != $rrefs_no) {
			return false;
		}

		if(!$this->removeRentedUplines($skip)) {
			return false;
		}

		$now = date('Y-m-d H:i:s');
		$data = array();
		for($i = 0; $i < $rrefs_no; $i++) {
			$data[$i] = array(
				'id' => $new[$i],
				'rented_upline_id' => $upline_id,
				'rent_starts' => $now,
				'rent_ends' => $rrefs[$i][$this->alias]['rent_ends'],
				'today_done' => $rrefs[$i][$this->alias]['today_done'],
			);
		}

		return $this->saveAll($data, array(
			'validate' => false,
			'atomic' => true,
			'counterCache' => true,
			'deep' => false,
		));
	}

	public function removeRentedUplines($botsIds, $uplineId = null) {
		if($botsIds === null || empty($botsIds)) {
			throw InternalErrorException(__d('bot_system', 'null argument'));
		}
		$this->recursive = -1;
		if($this->updateAll(array(
			$this->alias.'.rented_upline_id' => null,
			$this->alias.'.rent_starts' => null,
			$this->alias.'.rent_ends' => null,
			$this->alias.'.last_click_as_rref' => null,
			$this->alias.'.today_done' => false,
			$this->alias.'.active' => true,
			$this->alias.'.active_days' => 0,
			$this->alias.'.auto_renew_attempts' => 0,
			$this->alias.'.earned_as_rref' => 0,
			$this->alias.'.clicks_as_rref' => 0,
			$this->alias.'.clicks_as_rref_credited' => 0,
			$this->alias.'.clicks_as_rref_0' => 0,
			$this->alias.'.clicks_as_rref_1' => 0,
			$this->alias.'.clicks_as_rref_2' => 0,
			$this->alias.'.clicks_as_rref_3' => 0,
			$this->alias.'.clicks_as_rref_4' => 0,
			$this->alias.'.clicks_as_rref_5' => 0,
			$this->alias.'.clicks_as_rref_6' => 0,
			$this->alias.'.clicks_as_rref_credited_0' => 0,
			$this->alias.'.clicks_as_rref_credited_1' => 0,
			$this->alias.'.clicks_as_rref_credited_2' => 0,
			$this->alias.'.clicks_as_rref_credited_3' => 0,
			$this->alias.'.clicks_as_rref_credited_4' => 0,
			$this->alias.'.clicks_as_rref_credited_5' => 0,
			$this->alias.'.clicks_as_rref_credited_6' => 0,
		), array(
			$this->alias.'.id' => $botsIds,
		))) {
			if($uplineId !== null) {
				$this->updateCounterCache(array('rented_upline_id' => $uplineId));
			}
			return true;
		}
	}

	public function extendReferrals($uplineId, $rrefs, $days) {
		$this->recursive = -1;
		return $this->updateAll(array(
			$this->alias.'.rent_ends' => "DATE_ADD(`{$this->alias}`.`rent_ends`, INTERVAL $days DAY)",
		), array(
			$this->alias.'.id' => $rrefs,
			$this->alias.'.rented_upline_id' => $uplineId,
		));
	}

	public function getLaziestRentedReferrals($limit, $upline_id) {
		$this->recursive = -1;

		$res = $this->find('list', array(
			'fields' => 'id',
			'conditions' => array(
				$this->alias.'.rented_upline_id' => $upline_id,
			),
			'order' => $this->alias.'.clicks_as_rref',
			'limit' => $limit,
		));

		return array_keys($res);
	}

	public function removeReferralsOverflow($uplineId) {
		$this->RentedUpline->id = $uplineId;
		$this->RentedUpline->contain(array(
			'ActiveMembership' => array(
				'Membership' => array(
					'direct_referrals_limit', 
					'rented_referrals_limit',
				),
			),
		));
		$this->RentedUpline->read();

		if($this->RentedUpline->data['ActiveMembership']['Membership']['rented_referrals_limit'] != -1) {
			$limit = $this->RentedUpline->data['RentedUpline']['rented_refs_count'] - $this->RentedUpline->data['ActiveMembership']['Membership']['rented_referrals_limit'];

			if($limit > 0) {
				$toRemove = $this->getLaziestRentedReferrals($limit, $uplineId);

				$this->removeRentedUplines($toRemove, $uplineId);
			}
		}
	}

	public function autoRenew() {
		$income = '0';
		$settings = ClassRegistry::init('BotSystem.BotSystemSettings')->fetch(array('autoRenewTries', 'rentPeriod'));
		$contain = array(
			'RentedBots' => array(
				'id',
				'rent_ends',
			),
			'ActiveMembership' => array('Membership' => array('id', 'RentedReferralsPrice')),
		);

		if($settings['BotSystemSettings']['autoRenewTries'] != -1) {
			$contain[] = 'RentedBots.auto_renew_attempts < '.$settings['BotSystemSettings']['autoRenewTries'];
		}

		$this->RentedUpline->contain($contain);
		$uplines = $this->RentedUpline->find('all', array(
			'fields' => array(
				'RentedUpline.id',
				'RentedUpline.account_balance',
				'RentedUpline.purchase_balance',
				'RentedUpline.auto_renew_extend',
				'RentedUpline.auto_renew_days',
				'RentedUpline.rented_refs_count',
				'ActiveMembership.membership_id',
			),
			'conditions' => array(
				'RentedUpline.rented_refs_count >' => 0,
				'RentedUpline.auto_renew_days !=' => 0,
				'RentedUpline.auto_renew_extend !=' => 0,
			),
		));

		$RentExtensionPeriod = ClassRegistry::init('RentExtensionPeriod');
		$RentExtensionPeriod->recursive = -1;
		$extendPeriods = $RentExtensionPeriod->find('all');
		$extendPeriods = Hash::combine($extendPeriods, '{n}.RentExtensionPeriod.days', '{n}.RentExtensionPeriod');

		$AutorenewHistory = ClassRegistry::init('AutorenewHistory');

		foreach($uplines as $upline) {
			$purchase = $upline['RentedUpline']['purchase_balance'];
			$account = $upline['RentedUpline']['account_balance'];
			$range = $this->RentedUpline->MembershipsUser->Membership->RentedReferralsPrice->getRangeByRRefsNo($upline['ActiveMembership']['Membership']['RentedReferralsPrice'], $upline['RentedUpline']['rented_refs_count']);
			$extend = $extendPeriods[$upline['RentedUpline']['auto_renew_extend']];
			$historyAmount = '0';

			$price = bcmul($range['price'], bcdiv($upline['RentedUpline']['auto_renew_extend'], $settings['BotSystemSettings']['rentPeriod']));
			$price = bcsub($price, bcmul($price, bcdiv($extend['discount'], 100)));

			$now = new DateTime();
			$toExtend = array();
			$failed = array();

			foreach($upline['RentedBots'] as $ref) {
				$ends = new DateTime($ref['rent_ends']);
				$interval = $now->diff($ends);

				if($interval->format('%a') <= $upline['RentedUpline']['auto_renew_days']) {
					if(bccomp($purchase, $price) >= 0) {
						$purchase = bcsub($purchase, $price);
						$toExtend[] = $ref['id'];
						$historyAmount = bcadd($historyAmount, $price);
					} elseif(bccomp($account, $price) >= 0) {
						$account = bcsub($account, $price);
						$toExtend[] = $ref['id'];
						$historyAmount = bcadd($historyAmount, $price);
					} else {
						$failed[] = $ref['id'];
					}
				}
			}

			$this->RentedUpline->clear();
			$this->RentedUpline->contain();
			$this->RentedUpline->id = $upline['RentedUpline']['id'];
			$this->RentedUpline->set('purchase_balance', $purchase);
			$this->RentedUpline->set('account_balance', $account);
			if(!$this->RentedUpline->save()) {
				throw new InternalErrorException(__d('bot_system_console', 'Failed to save upline balances'));
			}

			if(!empty($toExtend)) {
				$this->recursive = -1;
				if(!$this->updateAll(array($this->alias.'.rent_ends' => "DATE_ADD(`{$this->alias}`.`rent_ends`, INTERVAL {$upline['RentedUpline']['auto_renew_extend']} DAY)"), array($this->alias.'.id' => $toExtend))) {
					throw new InternalErrorException(__d('bot_system_console', 'Failed to extend referals rent time'));
				}
			}

			if(!empty($failed)) {
				$this->recursive = -1;
				if(!$this->updateAll(array($this->alias.'.auto_renew_attempts' => "`{$this->alias}`.`auto_renew_attempts` + 1"), array($this->alias.'.id' => $failed))) {
					throw new InternalErrorException(__d('bot_system_console', 'Failed to save attempts number'));
				}
			}

			$AutorenewHistory->add($historyAmount, $upline['RentedUpline']['id']);
			$income = bcadd($income, $historyAmount);
		}

		ClassRegistry::init('BotSystem.BotSystemStatistic')->addData(compact('income'));

		return true;
	}

	public function findForClicksSimulation($type, $settings, $statsNumber, $limit = null) {
		$joins = array();
		$fields = array(
			$this->alias.'.id',
			$this->alias.'.rented_upline_id',
			$this->alias.'.active_days',
			$this->alias.'.clicks_avg',
			$this->alias.'.rent_ends_days',
		);
		$conditions = array(
			$this->alias.'.rented_upline_id !=' => null,
			$this->alias.'.today_done =' => false,
			$this->alias.'.active' => true,
		);

		if(isset($settings['BotSystemSettings'])) {
			$settings = $settings['BotSystemSettings'];
		}

		if($settings['botSystem']['activity']) {
			$joins[] = array(
				'table' => 'user_statistics',
				'alias' => 'UserStatistic',
				'type' => 'INNER',
				'conditions' => array(
					$this->alias.'.rented_upline_id = UserStatistic.user_id',
				),
			);

			if($settings['botSystem']['countNotCredited']) {
				$fields[] = 'UserStatistic.user_clicks_'.$statsNumber.' as activityCheck';
			} else {
				$conditions['UserStatistic.user_clicks_'.$statsNumber.' >='] = $settings['userActivityClicks'];
			}
		}

		$this->recursive = -1;
		$result = $this->find($type, array(
			'fields' => $fields,
			'joins' => $joins,
			'conditions' => $conditions,
			'limit' => $limit,
		));

		return $result;
	}

	public function simulateClicks() {
		$botStatistics = array('income' => '0', 'outcome' => '0');
		$settingsModel = ClassRegistry::init('BotSystem.BotSystemSettings');
		$settings = array(
			'botSystem',
			'botSystemCronRun',
			'userActivityClicks',
			'botSystemCronRun',
		);

		$settings = $settingsModel->fetch($settings);
		$settings = $settings['BotSystemSettings'];

		if(!isset($settings['botSystemCronRun'])) {
			$settings['botSystemCronRun'] = 0;
		}

		if($settings['botSystemCronRun'] >= 48) {
			$this->refreshClickSimulator();
			$settings['botSystemCronRun'] = 0;
		}

		$statsNumber = $settingsModel->magicStatsNumber(1);

		$count = $this->findForClicksSimulation('count', $settings, $statsNumber);

		$limit = ceil($count / (48 - $settings['botSystemCronRun']));

		if($limit > 0) {
			$groupModel = ClassRegistry::init('BotSystemGroup');
			$groupModel->recursive = -1;
			$groups = $groupModel->find('all');
			$groups = Hash::combine($groups, '{n}.BotSystemGroup.membership_id', '{n}.BotSystemGroup');

			$botsToDo = $this->findForClicksSimulation('all', $settings, $statsNumber, $limit);

			$uplines = array_unique(Hash::extract($botsToDo, '{n}.'.$this->alias.'.rented_upline_id'));

			$userStatisticModel = ClassRegistry::init('UserStatistic');
			$userModel = ClassRegistry::init('User');
			$uplines = $userModel->find('all', array(
				'fields' => array('User.id', 'User.autopay_enabled', 'ActiveMembership.membership_id', 'User.rented_refs_count', 'User.purchase_balance', 'User.account_balance'),
				'contain' => array('ActiveMembership' => array('Membership' => array('RentedReferralsPrice', 'autopay_trigger_days'))),
				'conditions' => array(
					'User.id' => $uplines,
				),
			));
			$uplines = Hash::combine($uplines, '{n}.User.id', '{n}');

			$toStop = array();
			foreach($botsToDo as $bot) {
				$todayNumber = $settingsModel->magicStatsNumber();
				$upline = $uplines[$bot[$this->alias]['rented_upline_id']];
				$group = $groups[$upline['ActiveMembership']['membership_id']];

				if($bot[$this->alias]['clicks_avg'] >= $group['max_avg']) {
					$max = intval($group['max_avg']);
				} else {
					$max = $group['max_clicks'];
				}

				$clicks = mt_rand($group['min_clicks'], $max);

				$chance = mt_rand(1, 100);

				$skipChance = $group['skip_chance'];
				$stopChance = $skipChance + $group['stop_chance'];

				if($chance <= $skipChance) {
					$clicks = 0;
				} elseif(($chance <= $stopChance && $group['min_activity_days'] <= $bot[$this->alias]['active_days']) || $bot[$this->alias]['active_days'] >= $group['max_activity_days']) {
					$toStop[] = $bot[$this->alias]['id'];
					$clicks = 0;
				}

				if($clicks > 0) {
					$autopay = false;
					$toUpdateBot = array(
						'clicks_as_rref' => "`clicks_as_rref` + $clicks",
						'last_click_as_rref' => 'NOW()',
						'today_done' => true,
						'active_days' => '`active_days` + 1',
						"clicks_as_rref_$todayNumber" => "`clicks_as_rref_$todayNumber` + $clicks",
					);
					$toUpdateUplineStats = array(
						"rref_clicks_$todayNumber" => "rref_clicks_$todayNumber + $clicks",
						'total_rrefs_clicks' => "total_rrefs_clicks + $clicks",
					);

					if(!isset($bot['UserStatistic']['activityCheck']) || $bot['UserStatistic']['activityCheck'] >= $settings['userActivityClicks']) {
						$total_amount = bcmul($group['click_value'], $clicks);
						$total_points = bcmul($group['points_per_click'], $clicks);

						if(!$userModel->accountBalanceAdd($total_amount, $bot[$this->alias]['rented_upline_id'])) {
							throw new InternalErrorException(__d('bot_system_console', 'Failed to add to account balance of %s', $bot[$this->alias]['rented_upline_id']));
						}

						if(!$userModel->pointsAdd($total_points, $bot[$this->alias]['rented_upline_id'])) {
							throw new InternalErrorException(__d('bot_system_console', 'Failed to add to points balance of %s', $bot[$this->alias]['rented_upline_id']));
						}

						$botStatistics['outcome'] = bcadd($botStatistics['outcome'], $total_amount);

						$toUpdateBot["clicks_as_rref_credited_$todayNumber"] = "`clicks_as_rref_credited_$todayNumber` + $clicks";
						$toUpdateBot['clicks_as_rref_credited'] = "`clicks_as_rref_credited` + $clicks";
						$toUpdateBot['earned_as_rref'] = "`earned_as_rref` + $total_amount";
						$toUpdateUplineStats['total_rrefs_credited_clicks'] = "total_rrefs_credited_clicks + $clicks";
						$toUpdateUplineStats["rref_clicks_credited_$todayNumber"] = "rref_clicks_credited_$todayNumber + $clicks";
						$toUpdateUplineStats['total_rrefs_clicks_earned'] = 'total_rrefs_clicks_earned + '.$total_amount;

						$autopay = true;
					}

					$this->recursive = -1;
					$res = $this->updateAll($toUpdateBot, array(
						$this->alias.'.id' => $bot[$this->alias]['id'],
					));

					if(!$res) {
						throw new InternalErrorException(__d('bot_system_console', 'Failed to save bot data: %s', $bot[$this->alias]['id']));
					}

					$userStatisticModel->recursive = -1;
					if(!$userStatisticModel->updateAll($toUpdateUplineStats, array(
						$userStatisticModel->alias.'.user_id' => $bot[$this->alias]['rented_upline_id'],
					))) {
						throw new InternalErrorException(__d('bot_system_console', 'Cannot save statistic data'));
					}

					if($autopay && $upline['User']['autopay_enabled'] && $bot[$this->alias]['rent_ends_days'] >= $upline['ActiveMembership']['Membership']['autopay_trigger_days']) {
						$done = false;
						$range = ClassRegistry::init('RentedReferralsPrice')->getRangeByRRefsNo($upline['ActiveMembership']['Membership']['RentedReferralsPrice'], $upline['User']['rented_refs_count']);

						if(bccomp($upline['User']['purchase_balance'], $range['autopay_price']) >= 0) {
							if(!$userModel->purchaseBalanceSub($range['autopay_price'], $upline['User']['id'])) {
								throw new InternalErrorException(__d('bot_system_console', 'Cannot save autopay data'));
							}

							$done = true;
						} else {
							if(bccomp($upline['User']['purchase_balance'], '0') <= 0) {
								$acc = $range['autopay_price'];
							} else {
								$acc = bcsub($range['autopay_price'], $upline['User']['purchase_balance']);
							}

							if(bccomp($upline['User']['account_balance'], $acc) >= 0) {
								$userModel->recursive = -1;
								$res = $userModel->updateAll(array(
									'User.account_balance' => "`User`.`account_balance` - '$acc'",
									'User.purchase_balance' => "0",
								), array(
									'User.id' => $upline['User']['id'],
								));

								if(!$res) {
									throw new InternalErrorException(__d('bot_system_console', 'Cannot save autopay data'));
								}

								$done = true;
							}
						}

						if($done) {
							$this->recursive = -1;
							$res = $this->updateAll(array(
								$this->alias.'.rent_ends' => 'DATE_ADD('.$this->alias.'.rent_ends, INTERVAL 1 DAY)',
							), array(
								$this->alias.'.id' => $bot[$this->alias]['id'],
							));

							if(!$res) {
								throw new InternalErrorException(__d('bot_system_console', 'Cannot save autopay data'));
							}
							ClassRegistry::init('AutopayHistory')->add($range['autopay_price'], $upline['User']['id']);

							$botStatistics['income'] = bcadd($botStatistics['income'], $range['autopay_price']);
						}
					}
				} else {
					$this->recursive = -1;
					$res = $this->updateAll(array(
						$this->alias.'.active_days' => '`active_days` + 1',
						$this->alias.'.today_done' => true,
					), array(
						$this->alias.'.id' => $bot[$this->alias]['id'],
					));

					if(!$res) {
						throw new InternalErrorException(__d('bot_system_console', 'Failed to save bot data 2: %s', $bot[$this->alias]['id']));
					}
				}
			}

			ClassRegistry::init('BotSystem.BotSystemStatistic')->addData($botStatistics);

			if(!empty($toStop)) {
				$this->recursive = -1;
				$res = $this->updateAll(array(
					$this->alias.'.active' => false,
				), array(
					$this->alias.'.id' => $toStop,
				));

				if(!$res) {
					throw new InternalErrorException(__d('bot_system_console', 'Failed to stop bots'));
				}
			}

		}

		if(!$settingsModel->store(array('BotSystemSettings' => array('botSystemCronRun' => $settings['botSystemCronRun'] + 1)), array('botSystemCronRun'))) {
			throw new InternalErrorException(__d('bot_system_console', 'Failed to save bot system runs number.'));
		}
	}

	public function refreshClickSimulator() {
		if(!ClassRegistry::init('BotSystem.BotSystemSettings')->store(array('BotSystemSettings' => array('botSystemCronRun' => 0)), array('botSystemCronRun'))) {
			throw new InternalErrorException(__d('bot_system_console', 'Failed to save bot system runs number.'));
		}

		$this->recursive = -1;
		$res = $this->updateAll(array(
			$this->alias.'.today_done' => false,
		));

		if(!$res) {
			throw new InternalErrorException(__d('bot_system_console', 'Failed to refresh today_done'));
		}

		$settings = ClassRegistry::init('BotSystem.BotSystemSettings')->fetchOne('botSystem');

		if($settings['autoAdd'] && isset($settings['autoAddMin']) && isset($settings['autoAddMax']) && $settings['autoAddMin'] <= $settings['autoAddMax']) {
			$add = mt_rand($settings['autoAddMin'], $settings['autoAddMax']);

			$toSave = array_fill(0, $add, array('created' => date('Y-m-d H:i:s')));

			if(!$this->saveMany($toSave)) {
				throw new InternalErrorException(__d('bot_system_console', 'Failed to create new bots (%d).', $add));
			}
		}
	}

	public function cleanupStatistics($magicNumber) {
		$this->recursive = -1;
		return $this->updateAll(array(
			$this->alias.'.clicks_as_rref_'.$magicNumber => 0,
			$this->alias.'.clicks_as_rref_credited_'.$magicNumber => 0,
		));
	}
}
