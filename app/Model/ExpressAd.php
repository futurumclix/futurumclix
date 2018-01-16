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
 * ExpressAd Model
 *
 * @property ExpressAdsPackage $ExpressAdsPackage
 * @property Advertiser $Advertiser
 */
class ExpressAd extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'title';

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
		'title' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Title cannot be blank',
				'allowEmpty' => false,
			),
			'maxLength' => array(
				'rule' => array('maxLength', 128),
				'message' => 'Title cannot be longer than 128 characters',
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
		'ExpressAdsPackage' => array(
			'className' => 'ExpressAdsPackage',
			'foreignKey' => 'express_ads_package_id',
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
			'className' => 'ExpressAdsVisitedAd',
			'foreignKey' => 'express_ad_id',
			'dependent' => true,
		),
		'TargettedLocations' => array(
			'className' => 'ExpressAdsTargettedLocation',
			'foreignKey' => 'express_ad_id',
			'dependent' => true,
		),
		'ClickHistory' => array(
			'className' => 'ClickHistory',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array(
				'ClickHistory.model' => 'ExpressAd',
			),
			'order' => 'ClickHistory.created',
		),
		'ClickValue' => array(
			'className' => 'ExpressAdsClickValue',
			'foreignKey' => false,
			'dependent' => false,
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

		$settings = ClassRegistry::init('Settings')->fetchOne('expressAds', null);

		if($settings === null) {
			return;
		}

		if($settings['titleLen'] < $this->validate['title']['maxLength']['rule'][1]) {
			$this->validate['title']['maxLength']['rule'][1] = $settings['titleLen'];
			$this->validate['title']['maxLength']['message'] = sprintf('Title cannot be longer than %d characters', $settings['titleLen']);
		}
		if($settings['descLen'] < $this->validate['description']['maxLength']['rule'][1]) {
			$this->validate['description']['maxLength']['rule'][1] = $settings['descLen'];
			$this->validate['description']['maxLength']['message'] = sprintf('Description cannot be longer than %d characters', $settings['descLen']);
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
 * expireDaysAds
 *
 * @return boolean
 */
	public function expireDaysAds($user_id = false) {
		$this->recursive = -1;

		$conditions = array(
			'ExpressAd.package_type' => 'Days',
			'ExpressAd.expiry_date < NOW()',
			'ExpressAd.expiry !=' => 0,
		);

		if($user_id !== false) {
			$conditions['ExpressAd.advertiser_id'] = $user_id;
		}

		$res = $this->updateAll(array(
			'ExpressAd.expiry' => 0,
			'ExpressAd.expiry_date' => null,
		), $conditions);

		return $res;
	}

/**
 * assignPack
 *
 * @return boolean
 */
	public function assignPack($packData, $ad_id = null, $deleteBoughtItem = true) {
		if($ad_id) {
			$this->id = $ad_id; 
		}

		$this->set('package_type', $packData['ExpressAdsPackage']['type']);
		$this->set('expiry', $packData['ExpressAdsPackage']['amount']);
		$this->set('express_ads_package_id', $packData['ExpressAdsPackage']['id']);

		if($packData['ExpressAdsPackage']['type'] == 'Days') {
			$this->set('expiry_date', date('Y-m-d H:i:s', strtotime("+ {$packData['ExpressAdsPackage']['amount']} days")));
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
 * addPack
 *
 * @return boolean
 */
	public function addPack($packData, $ad_id = null) {
		if($ad_id === null) {
			$ad_id = $this->id; 
		}

		$update = array(
			'ExpressAd.expiry' => 'ExpressAd.expiry + '.$packData['ExpressAdsPackage']['amount'],
			'ExpressAd.express_ads_package_id' => $packData['ExpressAdsPackage']['id'],
		);

		if($packData['ExpressAdsPackage']['type'] == 'Days') {
			$update['ExpressAd.expiry_date'] = 'DATE_ADD(COALESCE(`ExpressAd`.`expiry_date`, NOW()), INTERVAL '.$packData['ExpressAdsPackage']['amount'].' DAY)';
		}

		$this->recursive = -1;
		$res = $this->updateAll($update, array(
			'ExpressAd.id' => $ad_id,
			'ExpressAd.package_type' => $packData['ExpressAdsPackage']['type'],
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

		$this->ExpressAdsPackage->recursive = -1;
		$packData = $this->ExpressAdsPackage->findById($package_id);

		if(empty($packData)) {
			throw new NotFoundException(__d('exception', 'Invalid package id'));
		}

		$settings = ClassRegistry::init('Settings')->fetchOne('expressAds', false);

		if($settings && $settings['autoApprove']) {
			$this->set('status', 'Active');
		} else {
			$this->set('status', 'Pending');
		}

		return $this->assignPack($packData, $ad_id, false);
	}

/**
 * fetchAdsForUser method
 *
 * @return array
 */
	public function fetchAdsForUser($user_id = null, $membership_id = null, $locations = null) {
		$contain = array(
			'ClickValue' => array(
				'conditions' => array(
					'membership_id' => $membership_id ? $membership_id : ClassRegistry::init('Membership')->getDefaultId(),
				),
			),
		);

		$conditions = array(
			$this->alias.'.status' => 'Active',
			$this->alias.'.package_type !=' => '',
			'OR' => array(
				array(
					$this->alias.'.package_type' => 'Clicks',
					$this->alias.'.expiry >' => 0,
				),
				array(
					$this->alias.'.package_type' => 'Days',
					$this->alias.'.expiry_date >= NOW()', 
				),
			),
		);

		if($user_id) {
			$contain['VisitedAd'] = array(
				'conditions' => array(
					'VisitedAd.user_id' => $user_id,
				),
			);
		}

		if($membership_id) {
			$contain['TargettedMemberships'] = array('conditions' => array('TargettedMemberships.id' => $membership_id));
		}

		if($locations) {
			$contain['TargettedLocations'] = array(
				'conditions' => $locations,
			);
		}

		$this->contain($contain);
		return $this->find('all', compact('conditions'));
	}

/**
 * getAdForUser method
 *
 * @return array
 */
	public function getAdForUser($ad_id, $user_id = null, $membership_id = null, $locations = null) {
		$contain = array(
			'ClickValue' => array(
				'conditions' => array(
					'membership_id' => $membership_id ? $membership_id : ClassRegistry::init('Membership')->getDefaultId(),
				),
			),
		);

		$conditions = array(
			$this->alias.'.status' => 'Active',
			$this->alias.'.package_type !=' => '',
			'OR' => array(
				array(
					$this->alias.'.package_type' => 'Clicks',
					$this->alias.'.expiry >' => 0,
				),
				array(
					$this->alias.'.package_type' => 'Days',
					$this->alias.'.expiry_date >= NOW()', 
				),
			),
			$this->alias.'.id' => $ad_id,
		);

		if($user_id) {
			$contain['VisitedAd'] = array(
				'conditions' => array(
					'VisitedAd.user_id' => $user_id,
				),
			);
		}

		if($membership_id) {
			$contain['TargettedMemberships'] = array('conditions' => array('TargettedMemberships.id' => $membership_id));
		}

		if($locations) {
			$contain['TargettedLocations'] = array(
				'conditions' => $locations,
			);
		}

		$this->contain($contain);
		return $this->find('first', compact('conditions'));
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
				'model' => 'Ad',
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));
		$res['chart'][__('Clicks')] = Hash::combine($data, '{n}.ClickHistory.created', '{n}.0.sum');

		$settings = ClassRegistry::init('Settings')->fetchOne('expressAds', array());
		if(!empty($settings) && $settings['geo_targetting']) {
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

