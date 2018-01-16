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
App::uses('AppModel', 'Model');
/**
 * PaidOffersApplication Model
 *
 * @property User $User
 * @property Offer $Offer
 */
class PaidOffersApplication extends AppModel {
/**
 * status values
 *
 * @const
 */
	const PENDING = 0;
	const ACCEPTED = 1;
	const REJECTED = 2;

/**
 * actAs
 *
 * @var array
 */
	public $actsAs = array(
		'Containable',
		'Utility.Enumerable',
	);

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'status';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'status' => array(
			'inList' => array(
				'rule' => array('inList', array(self::PENDING, self::ACCEPTED, self::REJECTED)),
				'message' => 'Invalid status.',
				'allowEmpty' => false,
			),
		),
		'description' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 1024),
				'message' => 'Description cannot be longer than 1024 characters.',
			),
		),
		'reject_reason' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 1024),
				'message' => 'Reject reason cannot be longer than 1024 characters.',
			),
		),
	);

/**
 * belongsTo associations
 *
 * NOTE: Overwritten in constructor!
 *
 * @var array
 */
	public $belongsTo = array(
	);

/**
 * enum
 *
 * @var array
 */
	public $enum = array(
		'status' => array(
			self::PENDING => 'Pending',
			self::ACCEPTED => 'Accepted',
			self::REJECTED => 'Rejected',
		),
	);

/**
 * constuctor
 *
 * @return void
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$this->belongsTo = array(
			'Applicant' => array(
				'className' => 'User',
				'foreignKey' => 'user_id',
				'conditions' => '',
				'counterCache' => array(
					'pending_applications' => array($this->alias.'.status' => self::PENDING),
					'accepted_applications' => array($this->alias.'.status' => self::ACCEPTED),
					'rejected_applications' => array($this->alias.'.status' => self::REJECTED),
				),
			),
			'PaidOffer' => array(
				'className' => 'PaidOffer',
				'foreignKey' => 'offer_id',
				'conditions' => '',
				'counterCache' => array(
					'pending_applications' => array($this->alias.'.status' => self::PENDING),
					'accepted_applications' => array($this->alias.'.status' => self::ACCEPTED),
					'rejected_applications' => array($this->alias.'.status' => self::REJECTED),
				),
			),
		);
	}

/**
 * accept method
 *
 * @return boolean
 */
	public function accept($id = null) {
		if($id) {
			$this->id = $id;
		}

		$this->contain(array('PaidOffer' => 'value', 'Applicant' => array('ActiveMembership' => array('Membership' => array('points_enabled', 'points_per_paid_offer')))));
		$this->read(array('user_id'));

		if($this->data['Applicant']['ActiveMembership']['Membership']['points_enabled']) {
			$this->Applicant->pointsAdd($this->data['Applicant']['ActiveMembership']['Membership']['points_per_paid_offer'], $this->data[$this->alias]['user_id']);
		}

		if($this->Applicant->accountBalanceAdd($this->data['PaidOffer']['value'], $this->data[$this->alias]['user_id'])) {
			return $this->saveField('status', self::ACCEPTED);
		}
		return false;
	}

/**
 * reject method
 *
 * @return boolean
 */
	public function reject($id = null, $reason = null, $offer_id = null) {
		if(!$id) {
			$id = $this->id;
		}

		if(!$offer_id) {
			$this->contain();
			$data = $this->findById($id, array('offer_id'));
			$offer_id = $data[$this->alias]['offer_id'];
		}

		if(!$this->PaidOffer->freeSlot($offer_id)) {
			throw new InternalErrorException(__d('exception', 'Failed to free slot in offer %d', $offer_id));
		}

		$this->id = $id;

		$this->set('status', self::REJECTED);
		$this->set('reject_reason', $reason);

		return $this->save();
	}

/**
 * autoAccept
 *
 * @return boolean
 */
	public function autoAccept($settings) {
		$this->contain();
		$data = $this->find('all', array(
			'fields' => 'id',
			'conditions' => array(
				"created < DATE_SUB(NOW(), INTERVAL {$settings['applicationAutoApproveDays']} DAY)",
				'status' => self::PENDING,
			),
		));

		$data = Hash::extract($data, '{n}.'.$this->alias.'.id');

		foreach($data as $id) {
			if(!$this->accept($id)) {
				throw new InternalErrorException(__d('exception', 'Failed to accept application %s.', $id));
			}
		}
		return true;
	}
}
