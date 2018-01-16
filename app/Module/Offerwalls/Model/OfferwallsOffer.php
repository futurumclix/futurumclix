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
App::uses('OfferwallsAppModel', 'Offerwalls.Model');

class OfferwallsOffer extends OfferwallsAppModel {
	const PENDING = 0;
	const COMPLETED = 1;
	const PAID = 2;
	const FAILED = 3;

	public $actsAs = array(
		'Containable',
		'Utility.Enumerable',
	);

	public $enum = array(
		'status' => array(
			self::PENDING => 'Pending',
			self::COMPLETED => 'Completed',
			self::PAID => 'Paid',
			self::FAILED => 'Failed',
		),
	);

	public $displayField = 'offerwallid';

	public $validate = array(
		'offerwall' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 25),
				'message' => 'Gateway name must not be longer than 25 characters',
				'allowEmpty' => false,
			),
		),
		'amount' => array(
			'decimal' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Amount should be a decimal value',
			),
		),
		'status' => array(
			'inList' => array(
				'rule' => array('inList', array(self::PENDING, self::COMPLETED, self::PAID, self::FAILED)),
				'message' => 'Offer status should "pending", "completed", "paid" or "failed".',
				'allowEmpty' => false,
			),
		),
		'transactionid' => array(
			'between' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Offerwall id must not be longer than 255 characters.',
				'allowEmpty' => false,
			),
		),
		'complete_date' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'message' => 'Complete date should be a valid datetime value.',
			),
		),
		'credit_date' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'message' => 'Credit date should be a valid datetime value.',
			),
		),
	);

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

	public function beforeSave($options = array()) {
		if(!empty($this->data[$this->alias])) {
			if($this->data[$this->alias]['status'] == self::COMPLETED) {
				$user = ClassRegistry::init('User');
				$offerwallsMemberships = ClassRegistry::init('Offerwalls.OfferwallsMembership');

				$user->contain(array('ActiveMembership' => array('Membership' => array('id', 'points_enabled'))));
				$userData = $user->findById($this->data[$this->alias]['user_id'], array('id', 'ActiveMembership.membership_id'));

				if(empty($userData)) {
					throw new NotFoundException(__d('offerwalls', 'Invalid user'));
				}

				$offerwallsMemberships->recursive = -1;
				$options = $offerwallsMemberships->findByMembershipId($userData['ActiveMembership']['membership_id']);

				if(empty($options)) {
					throw new InternalErrorException(__d('offerwalls', 'Empty offers options'));
				}

				$toAdd = bcmul($this->data[$this->alias]['amount'], $options['OfferwallsMembership']['point_ratio']);

				if($options['OfferwallsMembership']['delay'] == 0 || bccomp($options['OfferwallsMembership']['instant_limit'], $toAdd) >= 0) {
					$settings = ClassRegistry::init('Offerwalls.OfferwallsSettings')->fetchOne('offerwalls');
					$this->data[$this->alias]['credit_date'] = date('Y-m-d H:i:s');
					$this->data[$this->alias]['status'] = self::PAID;

					switch($settings['credit']) {
						case 'account':
							if(!$user->accountBalanceAdd($toAdd, $this->data[$this->alias]['user_id'])) {
								throw new InternalErrorException(__d('offerwalls', 'Failed to credit offer'));
							}
						break;

						case 'purchase':
							if(!$user->purchaseBalanceAdd($toAdd, $this->data[$this->alias]['user_id'])) {
								throw new InternalErrorException(__d('offerwalls', 'Failed to credit offer'));
							}
						break;
					}

					if($userData['ActiveMembership']['Membership']['points_enabled']) {
						$user->pointsAdd($options['OfferwallsMembership']['points_per_offer'], $userData['User']['id']);
					}
				}
			}
		}
		return true;
	}

	public function pay() {
		$user = ClassRegistry::init('User');
		$settings = ClassRegistry::init('Offerwalls.OfferwallsSettings')->fetchOne('offerwalls');
		$now = date('Y-m-d H:i:s');
		$completed = array();

		$this->contain(array('User' => array('id', 'ActiveMembership' => array('Membership' => array('id', 'OfferwallsSettings', 'points_enabled')))));
		$data = $this->find('all', array(
			'conditions' => array(
				$this->alias.'.status' => self::COMPLETED,
				$this->alias.'.credit_date' => null,
			),
		));

		foreach($data as $offer) {
			$creditDate = date('Y-m-d H:i:s', strtotime($offer[$this->alias]['complete_date']." + {$offer['User']['ActiveMembership']['Membership']['OfferwallsSettings']['delay']} day"));
			$toAdd = bcmul($offer[$this->alias]['amount'], $offer['User']['ActiveMembership']['Membership']['OfferwallsSettings']['point_ratio']);

			if($creditDate > $now && bccomp($offer['User']['ActiveMembership']['Membership']['OfferwallsSettings']['instant_limit'], $toAdd) < 0) {
				continue;
			}

			switch($settings['credit']) {
				case 'account':
					if(!$user->accountBalanceAdd($toAdd, $offer[$this->alias]['user_id'])) {
						throw new InternalErrorException(__d('offerwalls', 'Failed to credit offer'));
					}
				break;

				case 'purchase':
					if(!$user->purchaseBalanceAdd($toAdd, $offer[$this->alias]['user_id'])) {
						throw new InternalErrorException(__d('offerwalls', 'Failed to credit offer'));
					}
				break;
			}

			if($offer['User']['ActiveMembership']['Membership']['points_enabled']) {
				$user->pointsAdd($offer['User']['ActiveMembership']['Membership']['OfferwallsSettings']['point_ratio'], $offer['User']['id']);
			}

			$completed[] = $offer[$this->alias]['id'];
		}

		if(empty($completed)) {
			return true;
		}

		$this->recursive = -1;
		return $this->updateAll(array(
			$this->alias.'.credit_date' => "'$now'",
			$this->alias.'.status' => self::PAID,
		), array(
			$this->alias.'.id' => $completed,
		));
	}
}
