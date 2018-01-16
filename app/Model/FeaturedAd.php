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
 * FeaturedAd Model
 *
 * @property Advertiser $Advertiser
 */
class FeaturedAd extends AppModel {
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
			'maxLength' => array(
				'rule' => array('maxLength', 128),
				'message' => 'Title cannot be longer than 128 charcters',
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
				'rule' => array('maxLength', 300),
				'message' => 'Description cannot be longer than 300 characters',
				'allowEmpty' => true,
			),
		),
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
		'impressions' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Number of impressions should be a natural number or 0',
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
				'rule' => array('inList', array('Clicks', 'Days', 'Impressions')),
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
			'className' => 'AnonymousAdvertiser',
			'foreignKey' => 'anonymous_advertiser_id',
			'fields' => array('email', 'id'),
			'limit' => 1,
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
				'ClickHistory.model' => 'FeaturedAd',
			),
			'order' => 'ClickHistory.created',
		),
		'ImpressionHistory' => array(
			'className' => 'ImpressionHistory',
			'foreignKey' => 'foreign_key',
			'dependent' => true,
			'conditions' => array(
				'ImpressionHistory.model' => 'FeaturedAd',
			),
			'order' => 'ImpressionHistory.created',
		),
	);

/**
 * assignPack
 *
 * @return boolean
 */
	public function assignPack($packData, $ad_id = null, $reset_stats = true) {
		$this->clear();

		if($ad_id) {
			$this->id = $ad_id; 
		}

		if($reset_stats) {
			$this->set('clicks', 0);
			$this->set('impressions', 0);
		}

		$this->set('package_type', $packData['FeaturedAdsPackage']['type']);
		$this->set('expiry', $packData['FeaturedAdsPackage']['amount']);
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
		$ad = $this->findById($ad_id, array(
			'DATE_ADD(FeaturedAd.start, INTERVAL FeaturedAd.expiry DAY) as end',
			'FeaturedAd.package_type',
		));

		if(empty($ad)) {
			return false;
		}

		if($ad['FeaturedAd']['package_type'] == 'Days' && $ad[0]['end'] < date('Y-m-d H:i:s')) {
			return $this->assignPack($packData, $ad_id, false);
		}

		$this->recursive = -1;
		$res = $this->updateAll(array(
			'FeaturedAd.expiry' => '`FeaturedAd`.`expiry` + '.$packData['FeaturedAdsPackage']['amount'],
		), array(
			'FeaturedAd.id' => $ad_id,
			'FeaturedAd.package_type' => $packData['FeaturedAdsPackage']['type'],
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

		$packModel = ClassRegistry::init('FeaturedAdsPackage');

		$packModel->recursive = -1;
		$packData = $packModel->findById($package_id);

		if(empty($packData)) {
			throw new NotFoundException(__d('exception', 'Invalid package id'));
		}

		$auto_approve = ClassRegistry::init('Settings')->fetchOne('featuredAdsAutoApprove', 0);

		$this->set('status', $auto_approve ? 'Active' : 'Pending');
		$this->set('package_type', $packData['FeaturedAdsPackage']['type']);
		$this->set('expiry', $packData['FeaturedAdsPackage']['amount']);
		$this->set('start', date('Y-m-d H:i:s'));

		return $this->save();
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
				'model' => $this->alias,
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));
		$res['chart'][__('Clicks')] = Hash::combine($data, '{n}.ClickHistory.created', '{n}.0.sum');

		$data = $this->ImpressionHistory->find('all', array(
			'fields' => array('SUM(impressions) as sum', 'created'),
			'conditions' => array(
				'model' => $this->alias,
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));
		$res['chart'][__('Impressions')] = Hash::combine($data, '{n}.ImpressionHistory.created', '{n}.0.sum');

		return $res;
	}
}
