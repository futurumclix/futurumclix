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
App::uses('AdGridAppModel', 'AdGrid.Model');
/**
 * AdGridAd Model
 *
 */
class AdGridAd extends AdGridAppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'url';

/**
 * actAs
 *
 * @var array
 */
	public $actsAs = array(
		'Ads',
		'Containable',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'url' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 512),
				'message' => 'URL cannot be longer than 512 characters',
				'allowEmpty' => false,
			),
			'url' => array(
				'rule' => array('url', true),
				'message' => 'Please enter a valid URL',
			),
		),
		'clicks' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Number of clicks should be a natural number or 0',
				'allowEmpty' => false,
			),
		),
		'total_clicks' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Number of clicks should be a natural number or 0',
				'allowEmpty' => false,
			),
		),
		'expiry' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Expiry value should be a natural number or 0',
				'allowEmpty' => false,
			),
		),
		'package_type' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 20),
				'message' => 'Package type cannot be longer than 20 characters',
				'allowEmpty' => false,
			),
			'inList' => array(
				'rule' => array('inList', array('Clicks', 'Days')),
				'message' => 'Please select a valid package type',
				'allowEmpty' => false,
			),
		),
		'status' => array(
			'inList' => array(
				'rule' => array('inList', array('Inactive', 'Pending', 'Active', 'Unpaid')),
				'message' => 'Please select a valid status',
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
		'Advertiser' => array(
			'className' => 'User',
			'foreignKey' => 'advertiser_id',
		),
		'AnonymousAdvertiser' => array(
			'classname' => 'AnonymousAdvertiser',
			'foreignKey' => 'anonymous_advertiser_id',
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ClickHistory' => array(
			'className' => 'ClickHistory',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array(
				'ClickHistory.model' => 'AdGridAd',
			),
			'order' => 'ClickHistory.created',
		),
	);

/**
 * reportActions
 *
 * @var array
 */
	public $reportActions = array(
		'delete' => 'delete',
	);

/**
 * reportViewURL
 *
 * @var array
 */
	public $reportViewURL = array('plugin' => 'ad_grid', 'controller' => 'ad_grid', 'action' => 'edit');

/**
 * assignPack
 *
 * @return boolean
 */
	public function assignPack($packData, $ad_id = null) {
		$this->clear();

		if($ad_id) {
			$this->id = $ad_id; 
		}

		$this->set('package_type', $packData['AdGridAdsPackage']['type']);
		$this->set('expiry', $packData['AdGridAdsPackage']['amount']);
		$this->set('clicks', 0);
		$this->set('impressions', 0);
		$this->set('start', date('Y-m-d H:i:s'));

		if($this->save()) {
			return ClassRegistry::init('BoughtItem')->delete($packData['BoughtItem']['id']);
		}

		return false;
	}

/**
 * addPack
 *
 * @return boolean
 */
	public function addPack($packData, $ad_id = null) {
		if($ad_id === null) {
			$ad_id = $this->id; 
		}

		$this->recursive = -1;
		$res = $this->updateAll(array(
			'AdGridAd.expiry' => '`AdGridAd`.`expiry` + '.$packData['AdGridAdsPackage']['amount'],
		), array(
			'AdGridAd.id' => $ad_id,
			'AdGridAd.package_type' => $packData['AdGridAdsPackage']['type'],
		));

		if($res && $this->getAffectedRows() > 0) {
			return ClassRegistry::init('BoughtItem')->delete($packData['BoughtItem']['id']);
		}

		return false;
	}

/**
 * buy method
 *
 * @return boolean
 */
	public function buy($ad_id, $package_id) {
		$this->id = $ad_id;

		if(!$this->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid ad id'));
		}

		$packModel = ClassRegistry::init('AdGrid.AdGridAdsPackage');

		$packModel->recursive = -1;
		$packData = $packModel->findById($package_id);

		if(empty($packData)) {
			throw new NotFoundException(__d('exception', 'Invalid package id'));
		}

		$settings = ClassRegistry::init('AdGrid.AdGridSettings')->fetchOne('adGrid');

		if(!empty($settings) && $settings['autoApprove']) {
			$this->set('status', 'Active');
		} else {
			$this->set('status', 'Pending');
		}

		$this->set('package_type', $packData['AdGridAdsPackage']['type']);
		$this->set('expiry', $packData['AdGridAdsPackage']['amount']);
		$this->set('clicks', 0);
		$this->set('impressions', 0);
		$this->set('start', date('Y-m-d H:i:s'));

		return $this->save();
	}

/**
 * findAdForUser method
 *
 * @return array
 */
	public function findAdForUser($user_id = 0, $alreadySeen = array()) {
		$alias = $this->alias;
		$ad = $this->find('first', array(
			'conditions' => array(
				$alias.'.advertiser_id !=' => $user_id,
				$alias.'.id !=' => $alreadySeen,
				$alias.'.status' => 'Active',
				$alias.'.package_type !=' => '', 
				"CASE WHEN $alias.package_type = 'Clicks' THEN $alias.clicks < $alias.expiry
				 ELSE ($alias.start IS NULL OR DATEDIFF(NOW(), $alias.start) < $alias.expiry) END",
			),
			'order' => 'RAND()',
		));

		if(!empty($ad)) {
			return $ad;
		}

		$ad = $this->find('first', array(
			'conditions' => array(
				$alias.'.status' => 'Active',
				$alias.'.package_type !=' => '',
				"CASE WHEN $alias.package_type = 'Clicks' THEN $alias.clicks < $alias.expiry
				 ELSE ($alias.start IS NULL OR DATEDIFF(NOW(), $alias.start) < $alias.expiry) END",
			),
			'order' => 'RAND()',
		));

		return $ad;
	}

/**
 * credit method
 *
 * @return array
 */
	public function credit($ad, $user_id, $x, $y) {
		$userModel = ClassRegistry::init('User');
		$userModel->bindModel(array(
			'hasOne' => array(
				'AdGridUserClick' => array(
					'className' => 'AdGrid.AdGridUserClick',
				),
			),
		));

		$userModel->contain(array(
			'ActiveMembership' => array('Membership' => 'points_enabled'),
			'AdGridUserClick',
		));
		$user = $userModel->find('first', array(
			'conditions' => array(
				'User.id' => $user_id,
			),
			'fields' => array(
				'User.id',
				'User.username',
				'ActiveMembership.membership_id',
				'AdGridUserClick.clicks',
				'AdGridUserClick.fields',
				'AdGridUserClick.ads',
			),
		));
		$adGridMembershipsOption = ClassRegistry::init('AdGrid.AdGridMembershipsOption');
		$adGridMembershipsOption->recursive = -1;
		$options = $adGridMembershipsOption->findByMembershipId($user['ActiveMembership']['membership_id']);

		if($user['AdGridUserClick']['clicks'] !== null && $user['AdGridUserClick']['clicks'] >= $options['AdGridMembershipsOption']['clicks_per_day']) {
			return array(false, __d('ad_grid', 'You have reached maximum clicks today. Try again tomorrow.'));
		}

		$coords = "$x:$y";

		if($user['AdGridUserClick']['fields'] !== null && in_array($coords, $user['AdGridUserClick']['fields'])) {
			return array(false, __d('ad_grid', 'Sorry, but you already clicked this field.'));
		}

		$data = array('AdGridUserClick' => &$user['AdGridUserClick']);

		if($user['AdGridUserClick']['clicks'] === null) {
			$data['AdGridUserClick']['clicks'] = 0;
		}
		$data['AdGridUserClick']['user_id'] = $user_id;
		$data['AdGridUserClick']['clicks']++;
		$data['AdGridUserClick']['fields'][] = $coords;
		$data['AdGridUserClick']['ads'][] = $ad['AdGridAd']['id'];

		if(!$userModel->AdGridUserClick->save($data)) {
			throw new InternalErrorException(__d('ad_grid', 'Failed to save click data'));
		}

		$this->ClickHistory->addClick('AdGrid.AdGridAd', $ad['AdGridAd']['id']);

		$this->recursive = -1;
		$res = $this->updateAll(array(
			$this->alias.'.clicks' => '`'.$this->alias.'`.`clicks` + 1',
			$this->alias.'.total_clicks' => '`'.$this->alias.'`.`total_clicks` + 1',
		), array(
			$this->alias.'.id' => $ad['AdGridAd']['id'],
		));
		if(!$res) {
			throw new InternalErrorException(__d('ad_grid', 'Failed to increment ad clicks'));
		}

		if($user['ActiveMembership']['Membership']['points_enabled']) {
			$userModel->pointsAdd($options['AdGridMembershipsOption']['points_per_click'], $user_id);
		}

		$chance = mt_rand(1, 1000);

		if($chance <= intval($options['AdGridMembershipsOption']['win_probability'])) {
			$userPrize = 0;
			$probmax = 0;
			$prizes = array();

			foreach($options['AdGridMembershipsOption']['prizes'] as $prize) {
				$probmax += $prize['probability'];
				$prizes[$probmax] = $prize;
			}

			$chance = mt_rand(0, $probmax - 1);

			foreach($prizes as $prob => $prize) {
				if($chance <= $prob) {
					$userPrize = $prize;
					break;
				}
			}

			if(bccomp($userPrize['prize'], 0) > 0 || (isset($userPrize['points']) && $userPrize['points'] > 0)) {
				if(!$userModel->accountBalanceAdd($userPrize['prize'], $user_id)) {
					throw new InternalErrorException(__d('ad_grid_exception', 'Failed to save user account balance'));
				}
				if(isset($userPrize['points'])) {
					if(!$userModel->pointsAdd($userPrize['points'], $user_id)) {
						throw new InternalErrorException(__d('ad_grid_exception', 'Failed to save user points balance'));
					}
				}
				ClassRegistry::init('AdGrid.AdGridWinHistory')->addNew($user_id, $user['User']['username'], $userPrize['prize']);
				return array(true, $userPrize['prize']);
			} else {
				/* TODO: notify admin, prizes not set! */
			}
		}
		return array(false, __d('ad_grid', 'Sorry, you did not win this time. Please, try again.'));
	}

/**
 * deleteInactive method
 *
 * @return boolean
 */
	public function deleteInactive($days) {
		$date = date('Y-m-d H:i:s', strtotime("-$days days"));

		$this->recurisve = -1;

		return $this->deleteAll(
			array(
				$this->alias.'.modified <=' => $date,
				$this->alias.'.status !=' => 'Active',
			), true, true
		);
	}

/**
 * getStatistics method
 *
 * @return array
 */
	public function getStatistics($adId) {
		$this->contain();
		$res = $this->find('first', array(
			'conditions' => array(
				$this->alias.'.id' => $adId,
				$this->alias.'.advertiser_id' => null,
				$this->alias.'.anonymous_advertiser_id !=' => null,
			),
		));

		$data = $this->ClickHistory->find('all', array(
			'fields' => array('SUM(clicks) as sum', 'created'),
			'conditions' => array(
				'model' => 'AdGrid.AdGridAd',
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));
		$res['chart'][__('Clicks')] = Hash::combine($data, '{n}.ClickHistory.created', '{n}.0.sum');

		return $res;
	}
}
