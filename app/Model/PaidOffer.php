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
App::uses('PaidOffersApplication', 'Model');
/**
 * PaidOffer Model
 *
 */
class PaidOffer extends AppModel {
/**
 * actAs
 *
 * @var array
 */
	public $actsAs = array(
		'Containable',
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
		'url' => array(
			'maxLength' => array(
				'rule' => array('between', 9, 513),
				'message' => 'URL should be between 10 and 512 characters',
				'allowEmpty' => false,
			),
			'url' => array(
				'rule' => array('url', true),
				'message' => 'Please enter a valid URL',
			),
		),
		'description' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 1024),
				'message' => 'Description cannot be longer than 1024 characters',
				'allowEmpty' => true,
			),
		),
		'total_slots' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Number of slots should be a natural number or 0',
				'allowEmpty' => false,
			),
		),
		'taken_slots' => array(
			'naturalNumber' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Number of taken slots should be a natural number or 0',
				'allowEmpty' => false,
			),
		),
		'value' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Value should be a valid monetary value',
				'allowEmpty' => false,
			),
		),
		'status' => array(
			'inList' => array(
				'rule' => array('inList', array('Inactive', 'Pending', 'Active')),
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
		'Category' => array(
			'className' => 'PaidOffersCategory',
			'foreignKey' => 'category_id',
		)
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
		'PendingApplication' => array(
			'className' => 'PaidOffersApplication',
			'foreignKey' => 'offer_id',
			'conditions' => array('PendingApplication.status' => PaidOffersApplication::PENDING),
			'dependent' => true,
		),
		'AcceptedApplication' => array(
			'className' => 'PaidOffersApplication',
			'foreignKey' => 'offer_id',
			'conditions' => array('AcceptedApplication.status' => PaidOffersApplication::ACCEPTED),
			'dependent' => true,
		),
		'RejectedApplication' => array(
			'className' => 'PaidOffersApplication',
			'foreignKey' => 'offer_id',
			'conditions' => array('RejectedApplication.status' => PaidOffersApplication::REJECTED),
			'dependent' => true,
		),
		'IgnoringUser' => array(
			'className' => 'PaidOffersIgnoredOffer',
			'foreignKey' => 'offer_id',
			'dependent' => true,
		),
		'TargettedLocations' => array(
			'className' => 'PaidOffersTargettedLocation',
			'foreignKey' => 'offer_id',
			'dependent' => true,
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
 * virtualFields
 *
 * @var array
 */
	public $virtualFields = array(
		'slots_left' => 'total_slots - taken_slots',
	);

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
		} elseif($created) {
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
 * beforeDelete callback
 *
 * @return void
 */
	public function beforeDelete($cascade = true) {
		$this->recursive = -1;
		$data = $this->findById($this->id, array('status', 'title', 'advertiser_id'));

		if($data[$this->alias]['status'] == 'Pending') {
			$this->Advertiser->contain();
			$user = $this->Advertiser->findById($data[$this->alias]['advertiser_id'], array('username' , 'first_name', 'last_name', 'email', 'role', 'allow_emails'));

			if(!empty($user) && $user['Advertiser']['role'] == 'Active' && $user['Advertiser']['allow_emails']) {
				$email = ClassRegistry::init('Email');
				$email->setVariables(array(
					'%username%' => $user['Advertiser']['username'],
					'%firstname%' => $user['Advertiser']['first_name'],
					'%lastname%' => $user['Advertiser']['last_name'],
					'%adstatus%' => __('Deleted'),
					'%adtitle%' => $data[$this->alias]['title'],
				));

				$email->send('Ad declined', $user['Advertiser']['email']);
			}
		}
		return true;
	}

/**
 * getStatusesList
 *
 * @return array
 */
	public function getStatusesList() {
		$res = array();

		foreach($this->validate['status']['inList']['rule'][1] as $v) {
			$res[$v] = __($v);
		}

		return $res;
	}

/**
 * activate method
 *
 * @return boolean
 */
	public function activate($id = null) {
		if($id) {
			$this->id = $id;
		}

		$this->contain();
		$this->read();

		if($this->data[$this->alias]['status'] == 'Pending') {
			$this->Advertiser->contain();
			$user = $this->Advertiser->findById($this->data[$this->alias]['advertiser_id'], array('username' , 'first_name', 'last_name', 'email', 'role', 'allow_emails'));

			if(!empty($user) && $user['Advertiser']['role'] == 'Active' && $user['Advertiser']['allow_emails']) {
				$email = ClassRegistry::init('Email');
				$email->setVariables(array(
					'%username%' => $user['Advertiser']['username'],
					'%firstname%' => $user['Advertiser']['first_name'],
					'%lastname%' => $user['Advertiser']['last_name'],
					'%adstatus%' => __('Active'),
					'%adtitle%' => $this->data[$this->alias]['title'],
				));

				$email->send('Ad approve', $user['Advertiser']['email']);
			}
		}

		return $this->saveField('status', 'Active');
	}

/**
 * inactivate method
 *
 * @return boolean
 */
	public function inactivate($id = null) {
		if($id) {
			$this->id = $id;
		}

		$this->contain();
		$this->read();

		if($this->data[$this->alias]['status'] == 'Pending') {
			$this->Advertiser->contain();
			$user = $this->Advertiser->findById($this->data[$this->alias]['advertiser_id'], array('username' , 'first_name', 'last_name', 'email', 'role', 'allow_emails'));

			if(!empty($user) && $user['Advertiser']['role'] == 'Active' && $user['Advertiser']['allow_emails']) {
				$email = ClassRegistry::init('Email');
				$email->setVariables(array(
					'%username%' => $user['Advertiser']['username'],
					'%firstname%' => $user['Advertiser']['first_name'],
					'%lastname%' => $user['Advertiser']['last_name'],
					'%adstatus%' => __('Inactive'),
					'%adtitle%' => $this->data[$this->alias]['title'],
				));

				$email->send('Ad approve', $user['Advertiser']['email']);
			}
		}

		return $this->saveField('status', 'Inactive');
	}

/**
 * assignPack
 *
 * @return boolean
 */
	public function assignPack($packData, $id = null) {
		$this->clear();

		if($id) {
			$this->id = $id; 
		}

		$this->set('total_slots', $packData['PaidOffersPackage']['quantity']);
		$this->set('value', $packData['PaidOffersPackage']['value']);
		$this->set('taken_slots', 0);

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
			'PaidOffer.total_slots' => '`PaidOffer`.`total_slots` + '.$packData['PaidOffersPackage']['quantity'],
		), array(
			'PaidOffer.id' => $ad_id,
			'PaidOffer.value' => $packData['PaidOffersPackage']['value'],
		));

		if($res && $this->getAffectedRows() > 0) {
			return ClassRegistry::init('BoughtItem')->delete($packData['BoughtItem']['id']);
		}

		return false;
	}

/**
 * getSlot method
 *
 * @return boolean
 */
	public function getSlot($id = null) {
		if(!$id) {
			$id = $this->id;
		}

		$this->recursive = -1;
		$res = $this->updateAll(array(
			$this->alias.'.taken_slots' => $this->alias.'.taken_slots + 1',
		), array(
			$this->alias.'.id' => $id,
		));

		if($res && $this->getAffectedRows() >= 1) {
			return true;
		}

		return false;
	}

/**
 * freeSlot method
 *
 * @return boolean
 */
	public function freeSlot($id = null) {
		if(!$id) {
			$id = $this->id;
		}

		$this->recursive = -1;
		$res = $this->updateAll(array(
			$this->alias.'.taken_slots' => $this->alias.'.taken_slots - 1',
		), array(
			$this->alias.'.id' => $id,
		));

		if($res && $this->getAffectedRows() >= 1) {
			return true;
		}

		return false;
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
}
