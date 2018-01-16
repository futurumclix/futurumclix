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

class UsersShell extends AppShell {
	public $uses = array(
		'User',
	);

	public function suspendInactive() {
		$inactivitySuspendDays = $this->Settings->fetchOne('inactivitySuspendDays');

		if($inactivitySuspendDays == 0) {
			return;
		}

		$this->out(__d('console', 'Suspending users inactive at least %d days...', $inactivitySuspendDays), 0, Shell::NORMAL);

		if($this->User->suspendInactive($inactivitySuspendDays)) {
			$this->out(__d('console', 'done. %d users suspended.', $this->User->getAffectedRows()), 1, Shell::NORMAL);
		} else {
			$this->out(__d('console', 'error!'), 1, Shell::NORMAL);
		}
	}

	public function deleteInactive() {
		$inactivityDeleteDays = $this->Settings->fetchOne('inactivityDeleteDays');

		if($inactivityDeleteDays == 0) {
			return;
		}

		$this->out(__d('console', 'Deleting users inactive at least %d days...', $inactivityDeleteDays), 0, Shell::NORMAL);

		if($this->User->deleteInactive($inactivityDeleteDays)) {
			$this->out(__d('console', 'done. %d users deleted.', $this->User->getAffectedRows()), 1, Shell::NORMAL);
		} else {
			$this->out(__d('console', 'error!'), 1, Shell::NORMAL);
		}
	}

	public function deleteUnverified() {
		$days = $this->Settings->fetchOne('unverifiedDeleteDays', 0);

		if($days == 0) {
			return;
		}

		$this->out(__d('console', 'Deleting users un-verified at least %d days...', $days), 0, Shell::NORMAL);

		if($this->User->deleteUnverified($days)) {
			$this->out(__d('console', 'done.'), 1, Shell::NORMAL);
		} else {
			$this->out(__d('console', 'error!'), 1, Shell::NORMAL);
		}
	}

	public function degrade() {
		$removeReferralsOverflow = $this->Settings->fetchOne('removeReferralsOverflow');
		$this->out(__d('console', 'Removing expired memberships...'), 0, Shell::NORMAL);

		$conditions = array(
			'ends !=' => null, 
			'ends <=' => date('Y-m-d H:i:s'),
			'period !=' => 'Default',
		);

		if($removeReferralsOverflow) {
			$expired = $this->User->MembershipsUser->find('all', array(
				'fields' => 'user_id',
				'conditions' => $conditions,
			));

			$checkReferrals = Hash::extract($expired, '{n}.MembershipsUser.user_id');
		}

		$this->User->MembershipsUser->deleteAll($conditions, true, true);
		$removed = $this->User->MembershipsUser->getAffectedRows();

		if($removeReferralsOverflow) {
			foreach($checkReferrals as $upline_id) {
				$this->User->removeReferralsOverflow($upline_id);
			}
		}

		$this->out(__d('console', 'done. %d users degraded.', $removed), 1, Shell::NORMAL);
	}

	public function removeExpiredRentedReferrals() {
		$expireBalance = $this->Settings->fetchOne('expiryReferralsBalance');

		$this->out(__d('console', 'Removing expired rented referrals, mode: %s...', $expireBalance), 0, Shell::NORMAL);


		$this->User->contain();
		$expired = $this->User->find('all', array(
			'fields' => array(
				'COUNT(*) as expired_refs_no',
				'User.rented_upline_id',
			),
			'conditions' => array(
				'User.rented_upline_id IS NOT NULL',
				'User.rent_ends <= NOW()',
			),
			'group' => 'User.rented_upline_id',
		));

		$this->User->contain(array(
			'ActiveMembership' => array(
				'id',
				'membership_id',
				'Membership' => array(
					'rented_referral_expiry_fee'
				)
			)
		));

		$uplines = $this->User->find('all', array(
			'fields' => array(
				'User.id',
				'User.purchase_balance',
				'User.account_balance',
			),
			'conditions' => array(
				'User.id' => Hash::extract($expired, '{n}.User.rented_upline_id'),
			),
		));
		$uplines = Hash::combine($uplines, '{n}.User.id', '{n}');

		$toSave = array();

		if($expireBalance == 'account') {
			foreach($expired as $v) {
				if(bccomp($uplines[$v['User']['rented_upline_id']]['ActiveMembership']['Membership']['rented_referral_expiry_fee'], '0') != 0) {
					$cost = bcmul($v[0]['expired_refs_no'], $uplines[$v['User']['rented_upline_id']]['ActiveMembership']['Membership']['rented_referral_expiry_fee']);

					$toSave[] = array(
						'id' => $v['User']['rented_upline_id'],
						'account_balance' => bcsub($uplines[$v['User']['rented_upline_id']]['User']['account_balance'], $cost),
					);
				}
			}
		} elseif($expireBalance == 'both') {
			foreach($expired as $v) {
				if(bccomp($uplines[$v['User']['rented_upline_id']]['ActiveMembership']['Membership']['rented_referral_expiry_fee'], '0') != 0) {
					$cost = bcmul($v[0]['expired_refs_no'], $uplines[$v['User']['rented_upline_id']]['ActiveMembership']['Membership']['rented_referral_expiry_fee']);

					$purchase = $uplines[$v['User']['rented_upline_id']]['User']['purchase_balance'];
					$account = $uplines[$v['User']['rented_upline_id']]['User']['account_balance'];

					if(bccomp($purchase, $cost) >= 0) {
						$purchase = bcsub($purchase, $cost);
					} else {
						$account = bcsub($account, bcsub($cost, $purchase));
						$purchase = 0;
					}

					$toSave[] = array(
						'id' => $v['User']['rented_upline_id'],
						'account_balance' => $account,
						'purchase_balance' => $purchase,
					);
				}
			}
		}

		if(!empty($toSave)) {
			if(!$this->User->saveMany($toSave)) {
				$this->out(__d('console', 'error saving user data!'), 1, Shell::NORMAL);
				throw new InternalErrorException(__d('console', 'error saving user data!'));
			}
		}

		if($this->User->removeExpiredRentedReferrals()) {
			$this->out(__d('console', 'done.'), 1, Shell::NORMAL);
		} else {
			$this->out(__d('console', 'error!'), 1, Shell::NORMAL);
		}
	}

	public function refreshAutopay() {
		$this->out(__d('console', 'Refreshing autopay in users...'), 0, Shell::NORMAL);

		$res = $this->User->updateAll(array(
			'User.autopay_done' => false,
		));

		if($res) {
			$this->out(__d('console', 'done. %d users affected.', $this->User->getAffectedRows()), 1, Shell::NORMAL);
		} else {
			$this->out(__d('console', 'error!'), 1, Shell::NORMAL);
		}
	}

	public function autoRenew() {
		$this->out(__d('console', 'Runing AutoRenew...'), 0, Shell::NORMAL);

		$res = $this->User->autoRenew();

		if($res) {
			$this->out(__d('console', 'done.'), 1, Shell::NORMAL);
		} else {
			$this->out(__d('console', 'error!'), 1, Shell::NORMAL);
		}
	}

	public function autoRecycleReferrals() {
		$this->out(__d('console', 'Runing AutoRecycle...'), 0, Shell::NORMAL);

		try{
			$res = $this->User->autoRecycleReferrals();
			$this->out(__d('console', 'done.'), 1, Shell::NORMAL);
		} catch(Exception $e) {
			$this->out(__d('console', 'error!'), 1, Shell::NORMAL);
		}
	}
}
