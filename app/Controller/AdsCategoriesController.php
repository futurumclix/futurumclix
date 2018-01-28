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
App::uses('AppController', 'Controller');
/**
 * AdsCategories Controller
 *
 * @property AdsCategory $AdsCategory
 * @property PaginatorComponent $Paginator
 */
class AdsCategoriesController extends AppController {
/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'AdsCategory',
		'AdsCategoryPackage',
		'VisitedAd',
		'Membership',
		'User',
		'Settings',
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		'Location',
	);

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('index'));
		if($this->request->params['action'] == 'admin_edit') {
			if(isset($this->request->data['AdsCategory']['id'])) {
				$start = $this->AdsCategoryPackage->find('count', array(
					'conditions' => array(
						'ads_category_id' => $this->request->data['AdsCategory']['id'],
					),
				));
			} else {
				$start = 0;
			}

			if(isset($this->request->data['AdsCategoryPackage'])) {
				$stop = count($this->request->data['AdsCategoryPackage']);
			} else {
				$stop = 150;
			}

			for(;$start < $stop; $start++) {
				$this->Security->unlockedFields[] = 'AdsCategoryPackage.'.$start.'.type';
				$this->Security->unlockedFields[] = 'AdsCategoryPackage.'.$start.'.amount';
				$this->Security->unlockedFields[] = 'AdsCategoryPackage.'.$start.'.price';
			}
		}
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		if($this->Auth->loggedIn()) {
			$user_id = $this->User->id = $this->Auth->user('id');
			$this->VisitedAd->clearVisitedAds($this->User->id);
			$this->User->contain(array(
				'ActiveMembership.Membership',
				'VisitedAds.ad_id',
			));
			$this->User->read();
			$membership_id = $this->User->data['ActiveMembership']['Membership']['id'];
			$locations = $this->Location->getConditions($this->User->data['User']['location']);
			$categoryContain = array(
				'ClickValue' => array(
					'conditions' => array(
						'membership_id' => $membership_id,
					),
				),
				'Ads' => array(
					'TargettedMemberships' => array(
						'conditions' => array(
							'TargettedMemberships.id' => $membership_id,
						),
					),
					'TargettedLocations' => array(
						'conditions' => array(
							$locations,
						),
					),
					'conditions' => array(
						'Ads.status' => 'Active',
						'Ads.package_type !=' => '',
						'OR' => array(
							array(
								'Ads.package_type' => 'Clicks',
								'Ads.expiry >' => 0,
							),
							array(
								'Ads.package_type' => 'Days',
								'Ads.expiry_date >= NOW()', 
							),
						),
					),
				),
			);
		} else {
			$user_id = null;
			$membership_id = null;
			$locations = null;
			$categoryContain = array(
				'ClickValue' => array(
					'conditions' => array(
						'membership_id' => $this->Membership->getDefaultId(),
					),
				),
				'Ads' => array(
					'fields' => array('id', 'title', 'description'),
					'conditions' => array(
						'Ads.status' => 'Active',
						'Ads.package_type !=' => '',
						'OR' => array(
							array(
								'Ads.package_type' => 'Clicks',
								'Ads.expiry >' => 0,
							),
							array(
								'Ads.package_type' => 'Days',
								'Ads.expiry_date >= NOW()', 
							),
						),
					),
				),
			);
		}

		$this->AdsCategory->recursive = -1;
		$this->AdsCategory->contain($categoryContain);
		$adsCategories = $this->AdsCategory->find('all', array(
			'conditions' => array(
				'AdsCategory.status' => 'Active',
			),
			'order' => array(
				'AdsCategory.position' => 'ASC',
			),
		));

		if(Configure::read('expressAdsActive')) {
			$expressSettings = $this->Settings->fetchOne('expressAds');
			$expressAdsActive = true;
			$expressAds = ClassRegistry::init('ExpressAd')->fetchAdsForUser($user_id, $membership_id, $expressSettings['geo_targetting'] ? $locations : null);
		} else {
			$expressSettings = array();
			$expressAdsActive = false;
			$expressAds = array();
		}

		if(Configure::read('explorerAdsActive')) {
			$explorerSettings = $this->Settings->fetchOne('explorerAds');
			$explorerAdsActive = true;
			$geoTargeting = isset($expressSettings['geo_targetting']) && $expressSettings['geo_targetting'];
			$explorerAds = ClassRegistry::init('ExplorerAd')->fetchAdsForUser($user_id, $membership_id, $geoTargeting ? $locations : null);
		} else {
			$explorerSettings = array();
			$explorerAdsActive = false;
			$explorerAds = array();
		}

		if($this->Auth->loggedIn()) {
			foreach($adsCategories as $adsCategoryId => $adsCategory) {
				foreach($adsCategory['Ads'] as $adIdx => $ad) {
					$adId = $ad['id'];
					foreach($this->User->data['VisitedAds'] as $visitedAd) {
						if($adId == $visitedAd['ad_id']) {
							$adsCategories[$adsCategoryId]['Ads'][$adIdx]['Visited'] = true;
						}
					}
					if(count($ad['TargettedMemberships']) == 0 || ($adsCategory['AdsCategory']['geo_targetting'] && count($ad['TargettedLocations']) == 0)) {
						unset($adsCategories[$adsCategoryId]['Ads'][$adIdx]);
					}
				}
			}

			$availableAtLeastOne = false;

			foreach($adsCategories as $adsCategoryId => $adsCategory) {
				if(count($adsCategory['Ads']) == 0) {
					unset($adsCategories[$adsCategoryId]);
				}
				foreach($adsCategory['Ads'] as $ad) {
					if(!isset($ad['Visited'])) {
						$availableAtLeastOne = true;
					}
				}
			}

			if($expressAdsActive) {
				foreach($expressAds as $k => $ad) {
					if($expressSettings['geo_targetting'] && empty($ad['TargettedLocations'])) {
						unset($expressAds[$k]);
						continue;
					}
					if(empty($ad['VisitedAd'])) {
						$availableAtLeastOne = true;
					}
				}
			}

			if($explorerAdsActive) {
				foreach($explorerAds as $k => $ad) {
					if(empty($ad['VisitedAd'])) {
						$availableAtLeastOne = true;
					}
				}
			}

			if(!$availableAtLeastOne) {
				$mode = $this->Settings->fetchOne('clearVisitedAds');

				if(in_array($mode, array('accurate', 'first', 'last'))) {
					$res = $this->VisitedAd->getDateVisitedByUser($this->Auth->user('id'));

					if($res !== null) {
						$nextAdTime = strtotime($res.' +1 day');
						$nextAdDate = date('H:i:s Y-m-d', $nextAdTime);
						$jsTimeout = ($nextAdTime - time()) * 1000;
						$this->set(compact('nextAdDate', 'jsTimeout'));
					}
				} elseif($mode == 'daily') {
					$nextAdTime = strtotime('+1 day midnight +1 sec');
					$nextAdDate = date('H:i:s Y-m-d', $nextAdTime);
					$jsTimeout = ($nextAdTime - time()) * 1000;
					$this->set(compact('nextAdDate', 'jsTimeout'));
				} elseif($mode == 'constPerUser') {
					$nextAdTime = strtotime('+1 day '.date('H:i:s', strtotime($this->User->data['User']['first_click'])));
					$nextAdDate = date('H:i:s Y-m-d', $nextAdTime);
					$jsTimeout = ($nextAdTime - time()) * 1000;
					$this->set(compact('nextAdDate', 'jsTimeout'));
				}
			}
		}

		$this->set(compact('adsCategories', 'availableAtLeastOne', 'expressAdsActive', 'expressAds', 'expressSettings', 'explorerAdsActive', 'explorerAds', 'explorerSettings'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if(!$this->AdsCategory->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid ad category'));
		}
		$options = array('conditions' => array('AdsCategory.' . $this->AdsCategory->primaryKey => $id));
		$this->set('adsCategory', $this->AdsCategory->find('first', $options));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->AdsCategory->recursive = 0;
		$this->paginate = array('order' => 'id DESC');
		$this->set('adsCategories', $this->Paginator->paginate());
	}

/**
 * admin_massaction method
 *
 * @throws NotFoundException
 * @return void
 */
	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {
			if(!isset($this->request->data['AdsCategories']) || empty($this->request->data['AdsCategories'])) {
				$this->Notice->error(__d('admin', 'Please select at least one category.'));
				return $this->redirect($this->referer());
			}
			$categories = 0;
			foreach($this->request->data['AdsCategories'] as $id => $on) {
				if($on) {
					$categories++;
					if(!$this->AdsCategory->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid ad category'));
					}
				}
			}
			foreach($this->request->data['AdsCategories'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'delete':
							$this->AdsCategory->delete($id);
						break;
						
						case 'enable':
							$this->AdsCategory->enable($id);
						break;
						
						case 'disable':
							$this->AdsCategory->disable($id);
						break;
					}
				}
			}
			if($categories) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one category.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect($this->referer());
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if($this->request->is('post')) {
			$this->AdsCategory->create();
			if($this->AdsCategory->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'The ad category has been saved.'));
				return $this->redirect(array('action' => 'edit', $this->AdsCategory->id));
			} else {
				$this->Notice->error(__d('admin', 'The ad category could not be saved. Please, try again.'));
			}
		}
	}


/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if(!$this->AdsCategory->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid ad category'));
		}
		if($this->request->is(array('post', 'put'))) {
			if ($this->AdsCategory->saveAll($this->request->data)) {
				$this->Notice->success(__d('admin', 'The ad category has been saved.'));
			} else {
				$this->Notice->error(__d('admin', 'The ad category could not be saved. Please, try again.'));
			}
		}
		$options = array('conditions' => array('AdsCategory.' . $this->AdsCategory->primaryKey => $id));
		$this->AdsCategory->recursive = 1;
		$this->request->data = Hash::merge($this->AdsCategory->find('first', $options), $this->request->data);

		$memberships = $this->Membership->getList();

		if(count($this->request->data['ClickValue']) != count($memberships)) {
			foreach($memberships as $membershipId => $membershipName) {
				$isThere = false;
				foreach($this->request->data['ClickValue'] as $clickValue) {
					if($clickValue['membership_id'] == $membershipId) {
						$isThere = true;
						break;
					}
				}
				if($isThere == false) {
					$newClickValue['ads_category_id'] = $id;
					$newClickValue['membership_id'] = $membershipId;
					$this->request->data['ClickValue'][] = $newClickValue;
				}
			}
		}

		if(isset($this->request->data['AdsCategoryPackage'])) {
			$packetsNo = count($this->request->data['AdsCategoryPackage']);
		} else {
			$packetsNo = 1;
		}

		$this->set('packagesTypes', $this->AdsCategoryPackage->getTypesList());
		$this->set(compact('memberships', 'packetsNo'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if(!$this->AdsCategory->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid ad category'));
		}
		$this->request->allowMethod('post', 'delete');
		if($this->AdsCategory->delete($id)) {
			$this->Notice->success(__d('admin', 'The ad category has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The ad category could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_enable method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_enable($id = null) {
		if(!$this->AdsCategory->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid ad category'));
		}
		$this->request->allowMethod('post', 'delete');
		if($this->AdsCategory->enable($id)) {
			$this->Notice->success(__d('admin', 'The ad category has been enabled.'));
		} else {
			$this->Notice->error(__d('admin', 'The ad category could not be enabled. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_disable method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_disable($id = null) {
		if(!$this->AdsCategory->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid ad category'));
		}
		$this->request->allowMethod('post', 'delete');
		if($this->AdsCategory->disable($id)) {
			$this->Notice->success(__d('admin', 'The ad category has been disabled.'));
		} else {
			$this->Notice->error(__d('admin', 'The ad category could not be disabled. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_order method
 *
 * @return void
 */
	public function admin_order() {
		if($this->request->is(array('post', 'put'))) {
			if($this->AdsCategory->saveMany($this->request->data['AdsCategoriesOrder'])) {
				$this->Notice->success(__d('admin', 'The ads categories order has been successfully saved.'));
			} else {
				$this->Notice->error(__d('admin', 'The ads categories order could not be saved. Please, try again.'));
			}
		}
		$this->AdsCategory->contain();
		$adsCategories = $this->AdsCategory->find('all', array(
			'conditions' => array(
				'status' => 'Active',
			),
			'order' => array(
				'position' => 'ASC',
			),
		));
		$this->set(compact('adsCategories'));
	}

/**
 * admin_settings method
 *
 * @return void
 */
	public function admin_settings() {
		$keys = array(
			'PTCTitleLength',
			'PTCDescLength',
			'PTCAutoApprove',
			'PTCCheckConnection',
			'PTCCheckConnectionTimeout',
			'PTCPreviewTime',
		);

		if($this->request->is(array('post', 'put'))) {
			if($this->Settings->store($this->request->data, $keys)) {
				$this->Notice->success(__d('admin', 'Settings saved successfully.'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to save settings. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Settings->fetch($keys);
		}
	}
}
