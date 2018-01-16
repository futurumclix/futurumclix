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
App::uses('RevenueShareAppModel', 'RevenueShare.Model');
/**
 * RevenueSharePacket Model
 *
 * @property User $User
 */
class RevenueSharePacket extends RevenueShareAppModel {
	public $actsAs = array('Containable');

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'step' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber'),
				'message' => 'Step should be a natural number.',
				'allowEmpty' => false,
			),
		),
		'running_days' => array(
			'range' => array(
				'rule' => array('range', 0, 65536),
				'message' => 'Runinng days should be between 1 and 65535.',
				'allowEmpty' => false,
			),
			'lessThanMax' => array(
				'rule' => array('comparisonWithField', '<=', 'running_days_max'),
				'message' => 'Running days should be less or equal running days max.'
			),
		),
		'running_days_max' => array(
			'range' => array(
				'rule' => array('range', 0, 65536),
				'message' => 'Runinng days should be between 1 and 65535.',
				'allowEmpty' => false,
			),
		),
		'total_revenue' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Revenue should be a valid monetary value.',
				'allowEmpty' => false,
			),
		),
		'per_step_revenue' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Revenue should be a valid monetary value.',
				'allowEmpty' => false,
			),
		),
		'revenued' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Revenue should be a valid monetary value.',
				'allowEmpty' => false,
			),
		),
		'failed_revenue' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Revenue should be a valid monetary value.',
				'allowEmpty' => false,
			),
		),
		'last_revenue' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'message' => 'Last revenue should be a valid datetime value.',
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
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'RevenueShareOption' => array(
			'className' => 'RevenueShare.RevenueShareOption',
		),
	);

	public $virtualFields = array(
		'days_left' => 'DATEDIFF(DATE_ADD(RevenueSharePacket.created, INTERVAL RevenueSharePacket.running_days_max DAY), NOW())',
	);

	public function pay() {
		$history = ClassRegistry::init('RevenueShare.RevenueShareHistory');
		$userModel = ClassRegistry::init('User');
		$userStatisticModel = ClassRegistry::init('UserStatistic');
		$settingsModel = ClassRegistry::init('RevenueShare.RevenueShareSettings');
		$settings = $settingsModel->fetch(array('revenueShare', 'userActivityClicks'));
		$magicYesterday = $settingsModel->magicStatsNumber(1);
		$creditList = ClassRegistry::init('RevenueShare.RevenueShareLimit')->whereCreditList();
		$now = date('Y-m-d H:i:s');

		$this->contain();
		$toPay = $this->find('all', array(
			'conditions' => array(
				$this->alias.'.revenued + '.$this->alias.'.failed_revenue < '.$this->alias.'.total_revenue',
				'OR' => array(
					array(
						array($this->alias.'.last_revenue !=' => null),
						array('DATE_ADD('.$this->alias.'.last_revenue, INTERVAL '.$this->alias.'.step MINUTE) < '."'$now'"),
					),
					array(
						array($this->alias.'.last_revenue' => null),
						array('DATE_ADD('.$this->alias.'.created, INTERVAL '.$this->alias.'.step MINUTE) < '."'$now'"),
					),
				),
			),
		));

		if($settings['RevenueShareSettings']['revenueShare']['activity']) {
			$activity = $settings['RevenueShareSettings']['userActivityClicks'];
		} else {
			$activity = 0;
		}

		foreach($toPay as $packet) {
			if($packet[$this->alias]['last_revenue']) {
				$last_revenue = $packet[$this->alias]['last_revenue'];
			} else {
				$last_revenue = $packet[$this->alias]['created'];
			}

			$alreadyRevenued = bcadd($packet[$this->alias]['revenued'], $packet[$this->alias]['failed_revenue']);
			$left = bcsub($packet[$this->alias]['total_revenue'], $alreadyRevenued);

			$ends = new DateTime($packet[$this->alias]['created']);
			$ends = $ends->add(new DateInterval("P{$packet[$this->alias]['running_days_max']}D"));
			$ends = $ends->format('Y-m-d H:i:s');

			if($ends >= $now) {
				$last_revenue = new DateTime($last_revenue);
				$interval = $last_revenue->diff(new DateTime());
				$minutes = abs($interval->days * 24 * 60 + $interval->h * 60 + $interval->i);

				$steps = floor($minutes / $packet[$this->alias]['step']);

				$toAdd = bcmul($packet[$this->alias]['per_step_revenue'], $steps);

				if(bccomp($left, $toAdd) < 0) {
					$toAdd = $left;
				}
			} else {
				$toAdd = $left;
			}

			$wasActive = true;

			if($activity > 0) {
				$userStatisticModel->recursive = -1;
				$stats = $userStatisticModel->findByUserId($packet[$this->alias]['user_id'], array('user_clicks_'.$magicYesterday));

				if($stats['UserStatistic']['user_clicks_'.$magicYesterday] < $activity) {
					$wasActive = false;
				}
			}

			if($wasActive) {
				$userModel->contain(array('ActiveMembership' => 'membership_id'));
				$user = $userModel->findById($packet[$this->alias]['user_id'], array('id'));
				$membership_id = $user['ActiveMembership']['membership_id'];

				if(!isset($creditList[$membership_id]) || !$creditList[$membership_id]['enabled']) {
					continue;
				}

				switch($creditList[$membership_id]['credit']) {
					case RevenueShareLimit::ACCOUNT:
						if(!$userModel->accountBalanceAdd($toAdd, $packet[$this->alias]['user_id'])) {
							throw new InternalErrorException(__d('revenue_share_console', 'Failed to change account balance of user %d', $packet[$this->alias]['user_id']));
						}
					break;

					case RevenueShareLimit::PURCHASE:
						if(!$userModel->purchaseBalanceAdd($toAdd, $packet[$this->alias]['user_id'])) {
							throw new InternalErrorException(__d('revenue_share_console', 'Failed to change account balance of user %d', $packet[$this->alias]['user_id']));
						}
					break;

					default:
						throw new InternalErrorException(__d('revenue_share_console', 'Invalid credit limit setting'));
				}

				$this->recursive = -1;
				$this->updateAll(array(
					$this->alias.'.last_revenue' => "'$now'",
					$this->alias.'.revenued' => '`'.$this->alias.'`.`revenued` + '."'$toAdd'",
				), array(
					$this->alias.'.id' => $packet[$this->alias]['id'],
				));

				$history->add('0', $toAdd);
			} else {
				$this->recursive = -1;
				$this->updateAll(array(
					$this->alias.'.last_revenue' => "'$now'",
					$this->alias.'.failed_revenue' => '`'.$this->alias.'`.`failed_revenue` + '."'$toAdd'",
				), array(
					$this->alias.'.id' => $packet[$this->alias]['id'],
				));
			}
		}
	}
}
