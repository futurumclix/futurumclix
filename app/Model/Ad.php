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
 * Ad Model
 *
 * @property AdCategory $AdCategory
 * @property AdCategoryPackage $AdCategoryPackage
 * @property Advertiser $Advertiser
 */
class Ad extends AppModel {
/**
 * actsAs
 *
 * @var array
 */
	public $actsAs = array(
		'Ads',
	);

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'title';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Title cannot be blank',
				'allowEmpty' => false,
			),
			'maxLength' => array(
				'rule' => array('maxLength', 128),
				'message' => 'Title cannot be longer than 128 characters',
				'allowEmpty' => false,
			),
			'characters' => array(
				'rule' => array('custom', '/^[\s\p{L}\d\-_\!\.\@\#\$\%\^\&\*\(\)\-\+\"\|]+$/iu'),
				'message' => 'Title can contain only alphanumerical characters, "-", "_", ".", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "-", "+", """ or "|"',
				'allowEmpty' => false,
			),
		),
		'description' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 1024),
				'message' => 'Description cannot be longer than 1024 characters',
				'allowEmpty' => true,
			),
		),
		'url' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'URL cannot be blank',
				'allowEmpty' => false,
			),
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
		'outside_clicks' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Number of clicks should be a natural number or 0',
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
		'expiry' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Expiry should be a natural number or 0',
				'allowEmpty' => false,
			),
		),
		'expiry_date' => array(
			'date' => array(
				'rule' => array('datetime'),
				'message' => 'Expiry date should be a valid date and time value',
				'allowEmpty' => true,
			),
		),
		'hide_referer' => 'boolean',
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'AdsCategory' => array(
			'className' => 'AdsCategory',
			'foreignKey' => 'ads_category_id',
			'limit' => 1,
		),
		'AdsCategoryPackage' => array(
			'className' => 'AdsCategoryPackage',
			'foreignKey' => 'ads_category_package_id',
			'limit' => 1,
		),
		'Advertiser' => array(
			'className' => 'User',
			'foreignKey' => 'advertiser_id',
			'fields' => array('username', 'id'),
			'limit' => 1,
		),
		'AnonymousAdvertiser' => array(
			'className' => 'AnonymousAdvertiser',
			'foreignKey' => 'anonymous_advertiser_id',
			'fields' => array('email', 'id'),
			'limit' => 1,
		),
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'TargettedMemberships' => array(
			'className' => 'Membership',
			'unique' => true,
			'fields' => array('id', 'name'),
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'VisitedAd' => array(
			'className' => 'VisitedAd',
			'foreignKey' => 'ad_id',
			'dependent' => true,
		),
		'TargettedLocations' => array(
			'className' => 'TargettedLocation',
			'foreignKey' => 'ad_id',
			'dependent' => true,
		),
		'ClickHistory' => array(
			'className' => 'ClickHistory',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array(
				'ClickHistory.model' => 'Ad',
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
		'deactivate' => 'inactivate',
		'activate' => 'activate',
	);

/**
 * __construct
 *
 * @constructor
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$settings = ClassRegistry::init('Settings')->fetch(array('PTCTitleLength', 'PTCDescLength'));

		if($settings['Settings']['PTCTitleLength'] < $this->validate['title']['maxLength']['rule'][1]) {
			$this->validate['title']['maxLength']['rule'][1] = $settings['Settings']['PTCTitleLength'];
			$this->validate['title']['maxLength']['message'] = sprintf('Title cannot be longer than %d characters', $settings['Settings']['PTCTitleLength']);
		}
		if($settings['Settings']['PTCDescLength'] < $this->validate['description']['maxLength']['rule'][1]) {
			$this->validate['description']['maxLength']['rule'][1] = $settings['Settings']['PTCDescLength'];
			$this->validate['description']['maxLength']['message'] = sprintf('Description cannot be longer than %d characters', $settings['Settings']['PTCDescLength']);
		}
	}

/**
 * afterSave callback
 *
 * @return void
 */
	public function afterSave($created, $options = array()) {
		if(isset($this->data[$this->alias]['TargettedLocations']) && !empty($this->data[$this->alias]['TargettedLocations'])) {
			$this->TargettedLocations->saveFromList($this->data[$this->alias]['TargettedLocations'], isset($this->data[$this->alias]['id']) ? $this->data[$this->alias]['id'] : $this->id);
		} else if(isset($this->data[$this->alias]['AccurateTargettedLocations']) && !empty($this->data[$this->alias]['AccurateTargettedLocations'])) {
			$data = array();
			foreach($this->data[$this->alias]['AccurateTargettedLocations'] as $l) {
				if($l['region'] != '*') {
					$data[] = $l['country'].'/'.$l['region'].'/'.$l['city'];
				} elseif($l['country'] != '*') {
					$data[] = $l['country'].'/*';
				} else  {
					$data = array('*');
					break;
				}
			}
			$this->TargettedLocations->saveFromList($data, isset($this->data[$this->alias]['id']) ? $this->data[$this->alias]['id'] : $this->id);
		} else if(isset($this->data['AccurateTargettedLocations']) && !empty($this->data['AccurateTargettedLocations'])) {
			$data = array();
			foreach($this->data['AccurateTargettedLocations'] as $l) {
				if($l['region'] != '*') {
					$data[] = $l['country'].'/'.$l['region'].'/'.$l['city'];
				} elseif($l['country'] != '*') {
					$data[] = $l['country'].'/*';
				} else  {
					$data = array('*');
					break;
				}
			}
			$this->TargettedLocations->saveFromList($data, isset($this->data[$this->alias]['id']) ? $this->data[$this->alias]['id'] : $this->id);
		} else if($created) {
			$this->TargettedLocations->saveFromList(array('*'), isset($this->data[$this->alias]['id']) ? $this->data[$this->alias]['id'] : $this->id);
		}
	}

/**
 * afterFind callback
 *
 * @return array
 */
	public function afterFind($results, $primary = false) {
		$all = null;
		foreach($results as &$r) {
			if(isset($r['TargettedLocations'])) {
				$locations = Hash::extract($r['TargettedLocations'], '{n}.location');
				if(!Module::active('AccurateLocationDatabase')) {
					if(in_array('*', $locations)) {
						if($all === null) {
							$all = array_keys(ClassRegistry::init('Ip2NationCountry')->getLocationsList());
						} 
						$locations = $all;
					}
				} else {
					foreach($locations as $l) {
						$loc = explode('/', $l);
						if(is_array($loc)) {
							$r['AccurateTargettedLocations'][] = array(
								'country' => $loc[0],
								'region' => isset($loc[1]) ? $loc[1] : '*',
								'city' => isset($loc[2]) ? $loc[2] : '*',
							);
						} else {
							$r['AccurateTargettedLocations'][] = array(
								'country' => $loc,
								'region' => '*',
								'city' => '*',
							);
						}
					}
				}
				$r['TargettedLocations'] = $locations;
			}
		}
		return $results;
	}

/**
 * getAdForUser method
 *
 * @return array
 */
	public function getAdForUser($adId, $userId, $membershipId, $locationConditions, $recursive = 0) {
		$this->recursive = $recursive;
		$ad = $this->find('first', array(
			'joins' => array(
				array(
					'table' => 'ads_memberships',
					'alias' => 'TargettedMemberships',
					'type' => 'INNER',
					'conditions' => array(
						'TargettedMemberships.ad_id = Ad.id',
					),
				),
				array(
					'table' => 'targetted_locations',
					'alias' => 'TargettedLocations',
					'type' => 'INNER',
					'conditions' => array(
						'TargettedLocations.ad_id = Ad.id',
					),
				),
				array(
					'table' => 'visited_ads',
					'alias' => 'VisitedAds',
					'type' => 'LEFT',
					'conditions' => array(
						'Ad.id = VisitedAds.ad_id',
						'VisitedAds.user_id' => $userId,
					),
				),
			),
			'conditions' => array(
				'Ad.id' => $adId,
				'Ad.status' => 'Active',
				'Ad.package_type !=' => '',
				'OR' => array(
					array(
						'Ad.package_type' => 'Clicks',
						'Ad.expiry >' => 0,
					),
					array(
						'Ad.package_type' => 'Days',
						'Ad.expiry_date IS NOT NULL', 
						'Ad.expiry_date >= NOW()', 
					),
				),
				'TargettedMemberships.membership_id' => $membershipId,
				'OR' => array(
					$locationConditions,
					'AdsCategory.geo_targetting' => false,
				),
				'VisitedAds.ad_id IS NULL',
			),
		));
		return $ad;
	}

/**
 * getAdActive method
 *
 * @return array
 */
	public function getActiveAd($adId, $recursive = 0) {
		$this->recursive = 1;
		$ad = $this->find('first', array(
			'conditions' => array(
				'Ad.id' => $adId,
				'Ad.status' => 'Active',
			),
		));
		return $ad;
	}

/**
 * getRandomAdForUser method
 *
 * @return array
 */
	public function getRandomAdForUser($userId, $membershipId, $locationConditions, $recursive = 0) {
		$this->recursive = $recursive;
		$ad = $this->find('first', array(
			'joins' => array(
				array(
					'table' => 'ads_memberships',
					'alias' => 'TargettedMemberships',
					'type' => 'INNER',
					'conditions' => array(
						'TargettedMemberships.ad_id = Ad.id',
					),
				),
				array(
					'table' => 'targetted_locations',
					'alias' => 'TargettedLocations',
					'type' => 'INNER',
					'conditions' => array(
						'TargettedLocations.ad_id = Ad.id',
					),
				),
				array(
					'table' => 'visited_ads',
					'alias' => 'VisitedAds',
					'type' => 'LEFT',
					'conditions' => array(
						'Ad.id = VisitedAds.ad_id',
						'VisitedAds.user_id' => $userId,
					),
				),
			),
			'conditions' => array(
				'Ad.status' => 'Active',
				'Ad.package_type !=' => '',
				'OR' => array(
					array(
						'Ad.package_type' => 'Clicks',
						'Ad.expiry >' => 0,
					),
					array(
						'Ad.package_type' => 'Days',
						'Ad.expiry_date IS NOT NULL', 
						'Ad.expiry_date >= NOW()', 
					),
				),
				'TargettedMemberships.membership_id' => $membershipId,
				$locationConditions,
				'VisitedAds.ad_id IS NULL',
			),
			'order' => 'RAND()',
		));
		return $ad;
	}

/**
 * assignPack method
 *
 * @return boolean
 */
	public function assignPack($packData, $ad_id = null, $deleteBoughtItem = true) {
		if($ad_id) {
			$this->id = $ad_id; 
		}

		if(isset($packData['AdsCategoryPackage']['ads_category_id'])) {
			$cat_id = $packData['AdsCategoryPackage']['ads_category_id'];
		} else {
			$cat_id = $packData['AdsCategory']['id'];
		}

		$this->set('package_type', $packData['AdsCategoryPackage']['type']);
		$this->set('expiry', $packData['AdsCategoryPackage']['amount']);
		$this->set('ads_category_id', $cat_id);
		$this->set('ads_category_package_id', $packData['AdsCategoryPackage']['id']);

		if($packData['AdsCategoryPackage']['type'] == 'Days') {
			$this->set('expiry_date', date('Y-m-d H:i:s', strtotime("+ {$packData['AdsCategoryPackage']['amount']} days")));
		} else {
			$this->set('expiry_date', null);
		}

		if($this->save()) {
			if($deleteBoughtItem) {
				return ClassRegistry::init('BoughtItem')->delete($packData['BoughtItem']['id']);
			}
			return true;
		}

		return false;
	}

/**
 * addPack method
 *
 * @return boolean
 */
	public function addPack($packData, $ad_id = null) {
		if($ad_id === null) {
			$ad_id = $this->id; 
		}

		$update = array(
			'Ad.expiry' => 'Ad.expiry + '.$packData['AdsCategoryPackage']['amount'],
			'Ad.ads_category_package_id' => $packData['AdsCategoryPackage']['id'],
		);

		if($packData['AdsCategoryPackage']['type'] == 'Days') {
			$update['Ad.expiry_date'] = 'DATE_ADD(COALESCE(`Ad`.`expiry_date`, NOW()), INTERVAL '.$packData['AdsCategoryPackage']['amount'].' DAY)';
		}

		$this->recursive = -1;
		$res = $this->updateAll($update, array(
			'Ad.id' => $ad_id,
			'Ad.package_type' => $packData['AdsCategoryPackage']['type'],
			'Ad.ads_category_id' => $packData['AdsCategory']['id'],
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

		$this->AdsCategoryPackage->recursive = -1;
		$packData = $this->AdsCategoryPackage->findById($package_id);

		if(empty($packData)) {
			throw new NotFoundException(__d('exception', 'Invalid package id'));
		}

		$auto_approve = ClassRegistry::init('Settings')->fetchOne('PTCAutoApprove', 0);

		$this->set('status', $auto_approve ? 'Active' : 'Pending');

		return $this->assignPack($packData, $ad_id, false);
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
				'Ad.modified <=' => $date,
				'OR' => array(
					'Ad.package_type' => '',
					array(
						'Ad.package_type' => 'Clicks',
						'Ad.expiry' => 0,
					),
					array(
						'Ad.package_type' => 'Days',
						'OR' => array(
							'Ad.expiry_date IS NULL',
							'Ad.expiry_date < NOW()',
						),
					),
				),
				'Ad.status !=' => 'Active',
			), true, true
		);
	}

/**
 * expireDaysAds
 *
 * @return boolean
 */
	public function expireDaysAds($user_id = false) {
		$this->recursive = -1;

		$conditions = array(
			'Ad.package_type' => 'Days',
			'Ad.expiry_date < NOW()',
			'Ad.expiry !=' => 0,
		);

		if($user_id !== false) {
			$conditions['Ad.advertiser_id'] = $user_id;
		}

		$res = $this->updateAll(array(
			'Ad.expiry' => 0,
			'Ad.expiry_date' => null,
		), $conditions);

		return $res;
	}

/**
 * getStatistics method
 *
 * @return array
 */
	public function getStatistics($adId) {
		$rem = array(
			'hasMany' => 'VisitedAd',
		);

		$old = array();

		foreach($rem as $rel => $alias) {
			$old[$rel][$alias] = $this->{$rel}[$alias];
			unset($this->{$rel}[$alias]);
		}

		$this->recursive = -1;
		$res = $this->find('first', array(
			'conditions' => array(
				$this->alias.'.id' => $adId,
				$this->alias.'.advertiser_id' => null,
				$this->alias.'.anonymous_advertiser_id !=' => null,
			),
		));

		foreach($rem as $rel => $alias) {
			$this->{$rel}[$alias] = $old[$rel][$alias];
		}

		$data = $this->ClickHistory->find('all', array(
			'fields' => array('SUM(clicks) as sum', 'created'),
			'conditions' => array(
				'model' => 'Ad',
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));
		$res['chart'][__('Clicks')] = Hash::combine($data, '{n}.ClickHistory.created', '{n}.0.sum');

		$geo_active = $this->AdsCategory->findById($res[$this->alias]['ads_category_id'], array('geo_targetting'));
		if(!empty($geo_active) && $geo_active['AdsCategory']['geo_targetting']) {
			$data = $this->ClickHistory->find('all', array(
				'fields' => array('SUM(clicks) as sum', 'Country.code as code'),
				'conditions' => array(
					'model' => 'Ad',
					'foreign_key' => $adId,
				),
				'recursive' => 1,
				'order' => 'Country.country',
				'group' => 'country_id',
			));
			$res['geo'] = Hash::combine($data, '{n}.Country.code', '{n}.0.sum');
		}

		return $res;
	}
}

