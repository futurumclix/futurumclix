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
class ExpressAdsController extends AppController {
/**
 * components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		'UserPanel',
		'Payments',
		'Location',
	);

/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'ExpressAd',
		'ExpressAdsPackage',
		'Settings',
		'User',
	);

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		if($this->request->prefix != 'admin' && !Configure::read('expressAdsActive')) {
			throw new NotFoundException(__d('exception', 'Express Ads are disabled'));
		}

		$this->Auth->allow('view');

		if(Module::active('AccurateLocationDatabase')) {
			if($this->request->params['action'] == 'targetting' || $this->request->params['action'] == 'admin_edit') {
				if(isset($this->request->data['ExpressAd']['id'])) {
					$start = $this->ExpressAd->TargettedLocations->find('count', array(
						'conditions' => array(
							'express_ad_id' => $this->request->data['ExpressAd']['id'],
						),
					));
				} else {
					$start = 0;
				}

				if(isset($this->request->data['ExpressAd']['AccurateTargettedLocations'])) {
					$stop = count($this->request->data['ExpressAd']['AccurateTargettedLocations']);
				} else {
					$stop = 50;
				}

				for(; $start < $stop; $start++) {
					$this->Security->unlockedFields[] = 'AccurateTargettedLocations.'.$start.'.country';
					$this->Security->unlockedFields[] = 'AccurateTargettedLocations.'.$start.'.region';
					$this->Security->unlockedFields[] = 'AccurateTargettedLocations.'.$start.'.city';
				}
			}
		}

		if(!$this->request->is(array('post', 'put'))) {
			return;
		}

		if($this->request->params['action'] == 'admin_settings') {
			if(isset($this->request->data['ExpressAdsPackage'])) {
				$start = $this->ExpressAdsPackage->find('count');
				$stop = count($this->request->data['ExpressAdsPackage']);

				if($start <= 0) {
					$start = 1;
				}

				for(;$start < $stop; $start++) {
					$this->Security->unlockedFields[] = 'ExpressAdsPackage.'.$start.'.type';
					$this->Security->unlockedFields[] = 'ExpressAdsPackage.'.$start.'.amount';
					$this->Security->unlockedFields[] = 'ExpressAdsPackage.'.$start.'.price';
				}
			}
		}
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$settings = $this->Settings->fetchOne('expressAds');
		$user_id = $this->Auth->user('id');

		$this->ExpressAd->expireDaysAds($user_id);

		$this->ExpressAd->contain();
		$ads = $this->ExpressAd->find('all', array(
			'fields' => array(
				'ExpressAd.id',
				'ExpressAd.title',
				'ExpressAd.url',
				'ExpressAd.clicks',
				'ExpressAd.outside_clicks',
				'ExpressAd.status',
				'ExpressAd.package_type',
				'ExpressAd.expiry',
				'ExpressAd.expiry_date',
				'ExpressAd.description',
				'ExpressAd.modified',
				'ExpressAd.created',
			),
			'conditions' => array(
				'ExpressAd.advertiser_id' => $user_id,
			),
		));
		$this->ExpressAd->contain();
		$ads_no = $this->ExpressAd->find('count', array(
			'conditions' => array(
				'ExpressAd.advertiser_id' => $user_id,
				'ExpressAd.status' => 'Active',
			)
		));
		$this->ExpressAd->ClickHistory->recursive = -1;
		$clicksToday = $this->ExpressAd->ClickHistory->find('all', array(
			'fields' => array(
				'ExpressAd.id',
				'SUM(ClickHistory.clicks) as sum',
			),
			'joins' => array(
				array(
					'table' => 'express_ads',
					'alias' => 'ExpressAd',
					'type' => 'INNER',
					'conditions' => array('ClickHistory.foreign_key = ExpressAd.id')
				),
			),
			'conditions' => array(
				'ExpressAd.advertiser_id' => $user_id,
				'ClickHistory.model' => 'ExpressAd',
				'ClickHistory.created' => date('Y-m-d'),
			),
			'group' => 'ExpressAd.id',
		));

		$clicksToday = Hash::combine($clicksToday, '{n}.ExpressAd.id', '{n}.0.sum');

		$this->set(compact('ads', 'ads_no', 'clicksToday', 'settings'));
		$this->set('packsSum', $this->User->BoughtItems->sumExpressAdsPackages());
		$this->set('breadcrumbTitle', __('Express Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('auto_approve', $settings['autoApprove']);
	}

/**
 * delete method
 *
 * @return void
 */
	public function delete($id = null) {
		$user_id = $this->Auth->user('id');

		if(!$id || !$user_id) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$this->ExpressAd->contain();
		$ad = $this->ExpressAd->find('first', array(
			'fields' => array('id'),
			'conditions' => array(
				'ExpressAd.id' => $id,
				'ExpressAd.advertiser_id' => $user_id,
			),
		));

		if(empty($ad)) {
			throw new NotFoundExcption(__('Invalid advertisement'));
		}

		$this->ExpressAd->id = $id;
		if($this->ExpressAd->delete()) {
			$this->Notice->success(__('Advertisement deleted.'));
		} else {
			$this->Notice->error(__('Failed to delete advertisement. Please try again.'));
		}

		return $this->redirect(array('action' => 'index'));
	}

/**
 * edit method
 *
 * @return void
 */
	public function edit($adId = null) {
		$settings = $this->Settings->fetchOne('expressAds');

		$user_id = $this->Auth->user('id');

		$this->ExpressAd->expireDaysAds($user_id);

		$this->ExpressAd->id = $adId;
		$this->ExpressAd->contain();
		$ad = $this->ExpressAd->find('first', array(
			'conditions' => array(
				'ExpressAd.id' => $adId,
				'ExpressAd.advertiser_id' => $user_id,
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($ad['ExpressAd']['package_type'] == 'Days' && !$settings['autoApprove']) {
			throw new NotFoundException(__d('exception', 'Editing ads with package type "Days" is not allowed'));
		}

		if($this->request->is(array('post', 'put'))) {
			$this->ExpressAd->id = $adId;
			$this->request->data['ExpressAd']['advertiser_id'] = $user_id;

			if(!$settings['autoApprove']) {
				$this->request->data['ExpressAd']['status'] = 'Pending';
			}

			if($this->ExpressAd->save($this->request->data)) {
				$this->Notice->success(__('Advertisement saved successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to save advertisement. Please, try again.'));
			}
		} else {
			$this->request->data = $ad;
		}

		$this->ExpressAd->contain();
		$ads_no = $this->ExpressAd->find('count', array(
			'conditions' => array(
				'ExpressAd.advertiser_id' => $user_id,
				'ExpressAd.status' => 'Active',
			)
		));

		$titleMax = $this->ExpressAd->validate['title']['maxLength']['rule'][1];
		$descMax = $this->ExpressAd->validate['description']['maxLength']['rule'][1];

		$this->set(compact('ads_no', 'titleMax', 'descMax'));
		$this->set('packsSum', $this->User->BoughtItems->sumExpressAdsPackages());
		$this->set('breadcrumbTitle', __('Express Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$user_id = $this->Auth->user('id');

		$ads_no = $this->ExpressAd->find('count', array(
			'conditions' => array(
				'ExpressAd.advertiser_id' => $user_id,
				'ExpressAd.status' => 'Active',
			)
		));

		if($this->request->is(array('post', 'put'))) {
			$settings = ClassRegistry::init('Settings')->fetchOne('expressAds');

			$this->ExpressAd->create();

			if($settings['autoApprove']) {
				$this->request->data['ExpressAd']['status'] = 'Inactive';
			} else {
				$this->request->data['ExpressAd']['status'] = 'Pending';
			}

			$this->request->data['ExpressAd']['advertiser_id'] = $user_id;
			if($this->ExpressAd->save($this->request->data)) {
				$this->Notice->success(__('Advertisement saved successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to save advertisement. Please, try again.'));
			}
		}

		$titleMax = $this->ExpressAd->validate['title']['maxLength']['rule'][1];
		$descMax = $this->ExpressAd->validate['description']['maxLength']['rule'][1];

		$this->set(compact('ads_no', 'titleMax', 'descMax'));
		$this->set('packsSum', $this->User->BoughtItems->sumExpressAdsPackages());
		$this->set('breadcrumbTitle', __('Express Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * assign method
 *
 * @return void
 */
	public function assign() {
		$user_id = $this->Auth->user('id');

		$this->ExpressAd->expireDaysAds($user_id);

		$packetsData = ClassRegistry::init('BoughtItem')->find('all', array(
			'fields' => array('BoughtItem.id', 'ExpressAdsPackage.id', 'ExpressAdsPackage.type', 'ExpressAdsPackage.amount'),
			'recursive' => -1,
			'conditions' => array(
				'BoughtItem.user_id' => $user_id,
				'BoughtItem.model' => 'ExpressAdsPackage',
			),
			'joins' => array(
				array(
					'table' => 'express_ads_packages',
					'alias' => 'ExpressAdsPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = ExpressAdsPackage.id',
					),
				),
			),
		));

		$this->ExpressAd->contain();
		$ads = $this->ExpressAd->find('list', array(
			'conditions' => array(
				'ExpressAd.advertiser_id' => $user_id,
				'ExpressAd.status !=' => 'Pending',
				'ExpressAd.expiry' => 0,
			),
		));

		if($this->request->is(array('post', 'put'))) {
			if(!isset($ads[$this->request->data['express_ad_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Wrong package'));
			}
			$packetsData = Hash::combine($packetsData, '{n}.BoughtItem.id', '{n}');
			if(!isset($packetsData[$this->request->data['package_id']]) || empty($packetsData[$this->request->data['package_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Wrong package'));
			}
			if($this->ExpressAd->assignPack($packetsData[$this->request->data['package_id']], $this->request->data['express_ad_id'])) {
				$this->Notice->success(__('Package successfully assigned.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		$this->ExpressAd->contain();
		$ads_no = $this->ExpressAd->find('count', array(
			'conditions' => array(
				'ExpressAd.advertiser_id' => $user_id,
				'ExpressAd.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('%d %s', $v['ExpressAdsPackage']['amount'], $v['ExpressAdsPackage']['type']);
		}

		$this->set(compact('ads_no', 'packages', 'ads'));
		$this->set('packsSum', $this->User->BoughtItems->sumExpressAdsPackages());
		$this->set('breadcrumbTitle', __('Express Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * assignTo
 *
 * @return void
 */
	public function assignTo($adId = null) {
		if($adId === null) {
			return $this->redirect(array('action' => 'assign'));
		}

		$user_id = $this->Auth->user('id');

		$this->ExpressAd->expireDaysAds($user_id);

		$packetsConditions = array(
			'BoughtItem.user_id' => $user_id,
			'BoughtItem.model' => 'ExpressAdsPackage',
		);

		$activeAd = $this->ExpressAd->findById($adId);

		if(empty($activeAd) || $activeAd['ExpressAd']['advertiser_id'] != $user_id) {
			/* cheater? */
			throw new NotFoundException(__d('exception', 'Invalid ad'));
		}

		if(empty($activeAd['ExpressAd']['package_type'])) {
			return $this->redirect(array('action' => 'assign', $adId));
		}

		if($activeAd['ExpressAd']['expiry'] > 0) {
			$packetsConditions['ExpressAdsPackage.type'] = $activeAd['ExpressAd']['package_type'];
		}

		$packetsData = ClassRegistry::init('BoughtItem')->find('all', array(
			'fields' => array('BoughtItem.id', 'ExpressAdsPackage.id', 'ExpressAdsPackage.type', 'ExpressAdsPackage.amount'),
			'recursive' => -1,
			'conditions' => $packetsConditions,
			'joins' => array(
				array(
					'table' => 'express_ads_packages',
					'alias' => 'ExpressAdsPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = ExpressAdsPackage.id',
					),
				),
			),
		));

		if($this->request->is(array('post', 'put'))) {
			$packetsData = Hash::combine($packetsData, '{n}.BoughtItem.id', '{n}');
			if(!isset($packetsData[$this->request->data['package_id']]) || empty($packetsData[$this->request->data['package_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Wrong package'));
			}
			if($this->ExpressAd->addPack($packetsData[$this->request->data['package_id']], $adId)) {
				$this->Notice->success(__('Package successfully assigned.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		$ads = Hash::combine($activeAd, 'ExpressAd.id', 'ExpressAd.title');

		$this->ExpressAd->contain();
		$ads_no = $this->ExpressAd->find('count', array(
			'conditions' => array(
				'ExpressAd.advertiser_id' => $user_id,
				'ExpressAd.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('%d %s', $v['ExpressAdsPackage']['amount'], $v['ExpressAdsPackage']['type']);
		}

		$this->set(compact('ads_no', 'packages', 'ads', 'activeAd'));
		$this->set('packsSum', $this->User->BoughtItems->sumExpressAdsPackages());
		$this->set('breadcrumbTitle', __('Express Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * targetting method
 *
 * @return void
 */
	public function targetting($type, $adId = null) {
		$settings = $this->Settings->fetchOne('expressAds', null);

		if($settings === null) {
			throw new InternalErrorException(__d('exception', 'Invalid express ads settings'));
		}

		switch($type) {
			case 'memberships':
				$contain = 'TargettedMemberships';
			break;

			case 'geo':
				$contain = 'TargettedLocations';
				if($settings['geo_targetting']) {
					break;
				}

			default:
				throw new NotFoundException(__d('exception', 'Invalid targeting mode'));
		}
		$user_id = $this->Auth->user('id');

		$this->ExpressAd->id = $adId;
		$this->ExpressAd->contain($contain);
		$ad = $this->ExpressAd->find('first', array(
			'conditions' => array(
				'ExpressAd.id' => $adId,
				'ExpressAd.advertiser_id' => $user_id,
				'ExpressAd.status !=' => 'Pending',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->request->is(array('post', 'put'))) {
			$this->ExpressAd->id = $adId;

			if($this->ExpressAd->save($this->request->data)) {
				$this->Notice->success(__('ExpressAd sucessfully saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Error while saving ad. Please try again.'));
			}
		}

		$this->request->data = Hash::merge($ad, $this->request->data);

		$this->ExpressAd->contain();
		$ads_no = $this->ExpressAd->find('count', array(
			'conditions' => array(
				'ExpressAd.advertiser_id' => $user_id,
				'ExpressAd.status' => 'Active',
			)
		));

		switch($type) {
			case 'geo':
				$saveField = 'TargettedLocations';
				$title = __('Please choose locations for Geo-Targeting');
				$options = $this->Location->getCountriesList();
				if(!Module::active('AccurateLocationDatabase')) {
					$selected = $this->request->data['TargettedLocations'];
				} else {
					$tald = ClassRegistry::init('AccurateLocationDatabase.AccurateLocationDatabaseLocation');
					foreach($this->request->data['AccurateTargettedLocations'] as &$l) {
						$data = $tald->getListByParentName($l['country']);
						$l['country_regions'] = array_combine($data, $data);
						$data = $tald->getListByParentName($l['region']);
						$l['region_cities'] = array_combine($data, $data);
					}
					$selected = null;
				}
			break;

			case 'memberships':
				$saveField = 'TargettedMemberships';
				$title = __('Please choose memberships for targeting');
				$options = $this->ExpressAd->TargettedMemberships->find('list');
				$selected = null;
			break;
		}

		$this->set(compact('ads_no', 'options', 'saveField', 'title', 'selected'));
		$this->set('packsSum', $this->User->BoughtItems->sumExpressAdsPackages());
		$this->set('breadcrumbTitle', __('Express Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * statistics method
 *
 * @return void
 */
	public function statistics($adId = null) {
		$settings = $this->Settings->fetchOne('expressAds', null);

		if($settings === null) {
			throw new InternalErrorException(__d('exception', 'Invalid express ads settings'));
		}

		$geo_targetting = $settings['geo_targetting'];

		$this->ExpressAd->id = $adId;
		$this->ExpressAd->contain();
		$ad = $this->ExpressAd->find('first', array(
			'fields' => array('ExpressAd.id', 'ExpressAd.title'),
			'recursive' => 1,
			'conditions' => array(
				'ExpressAd.id' => $adId,
				'ExpressAd.advertiser_id' => $this->Auth->user('id'),
				'ExpressAd.status !=' => 'Pending',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$data = $this->ExpressAd->ClickHistory->find('all', array(
			'fields' => array('SUM(clicks) as sum', 'created'),
			'conditions' => array(
				'model' => 'ExpressAd',
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));

		$clicks = Hash::combine($data, '{n}.ClickHistory.created', '{n}.0.sum');

		if($geo_targetting) {
			$this->helpers[] = 'Map';

			$data = $this->ExpressAd->ClickHistory->find('all', array(
				'fields' => array('SUM(clicks) as sum', 'Country.code as code'),
				'conditions' => array(
					'model' => 'ExpressAd',
					'foreign_key' => $adId,
				),
				'recursive' => 1,
				'order' => 'Country.country',
				'group' => 'country_id',
			));

			$geo = Hash::combine($data, '{n}.Country.code', '{n}.0.sum');

			$this->set(compact('geo'));
		}

		$this->ExpressAd->contain();
		$ads_no = $this->ExpressAd->find('count', array(
			'conditions' => array(
				'ExpressAd.advertiser_id' => $this->Auth->user('id'),
				'ExpressAd.status' => 'Active',
			)
		));

		$this->set(compact('clicks', 'ad', 'ads_no', 'geo_targetting'));
		$this->set('packsSum', $this->User->BoughtItems->sumExpressAdsPackages());
		$this->set('breadcrumbTitle', __('Express Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * activate method
 *
 * @return void
 */
	public function activate($adId = null) {
		$this->request->allowMethod('post', 'delete');

		$this->ExpressAd->id = $adId;
		$ad = $this->ExpressAd->find('first', array(
			'fields' => array('ExpressAd.id'),
			'recursive' => -1,
			'conditions' => array(
				'ExpressAd.id' => $adId,
				'ExpressAd.advertiser_id' => $this->Auth->user('id'),
				'ExpressAd.status' => 'Inactive',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->ExpressAd->activate($adId)) {
			$this->Notice->success(__('Ad successfully activated.'));
		} else {
			$this->Notice->error(__('Error when activating ad. Please try again.'));
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * inactivate method
 *
 * @return void
 */
	public function inactivate($adId = null) {
		$this->request->allowMethod('post', 'put');

		$this->ExpressAd->id = $adId;
		$ad = $this->ExpressAd->find('first', array(
			'fields' => array('ExpressAd.id', 'ExpressAd.package_type'),
			'recursive' => -1,
			'conditions' => array(
				'ExpressAd.id' => $adId,
				'ExpressAd.advertiser_id' => $this->Auth->user('id'),
				'ExpressAd.status' => 'Active',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($ad['ExpressAd']['package_type'] == 'Days') {
			throw new NotFoundException(__d('exception', 'Inactivating ads with "Days" packages is disabled'));
		}

		if($this->ExpressAd->inactivate($adId)) {
			$this->Notice->success(__('Ad successfully paused.'));
		} else {
			$this->Notice->error(__('Error while pausing ad. Please try again.'));
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * buy method
 *
 * @return void
 */
	public function buy() {
		$user_id = $this->Auth->user('id');

		$activeGateways = $this->Payments->getActiveDepositsHumanized();

		$this->ExpressAd->contain();
		$ads_no = $this->ExpressAd->find('count', array(
			'conditions' => array(
				'ExpressAd.advertiser_id' => $user_id,
				'ExpressAd.status' => 'Active',
			)
		));

		$this->ExpressAd->ExpressAdsPackage->recursive = -1;
		$packagesData = $this->ExpressAd->ExpressAdsPackage->find('all', array(
			'order' => 'ExpressAdsPackage.price DESC',
		));

		$packages = array();
		foreach($packagesData as &$v) {
			$prices = array();
			$add = false;

			foreach($activeGateways as $k => $g) {
				$price = bcadd($v['ExpressAdsPackage']['price'], $this->Payments->getDepositFee($v['ExpressAdsPackage']['price'], $k));
				if($this->Payments->checkMinimumDepositAmount($k, $price)) {
					$prices[$k] = $price;
					$add = true;
				} else {
					$prices[$k] = 'disabled';
				}
			}

			if($add) {
				$packages[$v['ExpressAdsPackage']['id']] = sprintf('%d %s - %s', $v['ExpressAdsPackage']['amount'],
				 __($v['ExpressAdsPackage']['type']), CurrencyFormatter::format($v['ExpressAdsPackage']['price']));
				$v['ExpressAdsPackage']['price_per'] = bcdiv($v['ExpressAdsPackage']['price'], $v['ExpressAdsPackage']['amount']);
				$v['ExpressAdsPackage']['prices'] = $prices;
			}
		}
		$packagesData = Hash::combine($packagesData, '{n}.ExpressAdsPackage.id', '{n}');

		if($this->request->is('post')) {
			$pack_id = $this->request->data['package_id'];

			if(!isset($packagesData[$pack_id])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}

			if($this->Payments->checkMinimumDepositAmount($this->request->data['gateway'], $packagesData[$pack_id]['ExpressAdsPackage']['price'])) {
				$this->Payments->pay('ExpressAdsPackage', $this->request->data['gateway'], $packagesData[$pack_id]['ExpressAdsPackage']['price'],
				 $this->Auth->user('id'), array('package_id' => $pack_id));
			} else {
				$this->Notice->error(__('Minimum deposit amount for %s is %s.', $this->request->data['gateway'], CurrencyFormatter::format($this->Payments->getMinimumDepositAmount($this->request->data['gateway']))));
			}
		}

		$this->set(compact('ads_no', 'categories', 'packages', 'activeGateways', 'packagesData'));
		$this->set('breadcrumbTitle', __('Express ads panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumExpressAdsPackages());
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * view method
 *
 * @return void
 */
	public function view($adId = null) {
		$this->layout = 'surfer';
		$expressSettings = $this->Settings->fetchOne('expressAds');

		if(!$this->Auth->loggedIn()) {
			$ad = $this->ExpressAd->getAdForUser($adId);

			if(!$ad || empty($ad)) {
				throw new NotFoundException(__d('exception', 'Invalid express ad'));
			}

			$this->ExpressAd->id = $ad['ExpressAd']['id'];
			$this->ExpressAd->set(array('id' => $ad['ExpressAd']['id'], 'outside_clicks' => $ad['ExpressAd']['outside_clicks'] + 1));

			$this->ExpressAd->save();
		} else {
			$todayNumber = $this->Settings->fetch('activeStatsNumber')['Settings']['activeStatsNumber'];
			$userId = $this->Auth->user('id');

			$this->User->contain(array(
				'ActiveMembership.Membership',
			));
			$user = $this->User->findById($userId);
			$membership_id = $user['ActiveMembership']['Membership']['id'];
			$locations = $this->Location->getConditions($user['User']['location']);

			$ad = $this->ExpressAd->getAdForUser($adId, $userId, $membership_id, $expressSettings['geo_targetting'] ? $locations : null);

			if(!$ad || empty($ad) || !empty($ad['VisitedAd']) || ($expressSettings['geo_targetting'] && empty($ad['TargettedLocations']))) {
				throw new NotFoundException(__d('exception', 'Invalid express ad'));
			}

			$updateUserData = array(
				"user_clicks_$todayNumber" => "user_clicks_$todayNumber + 1",
				'total_clicks' => 'total_clicks + 1',
				'total_clicks_earned' => 'total_clicks_earned + '.$ad['ClickValue'][0]['user_click_value'],
				'clicks_as_dref' => 'clicks_as_dref + 1',
				'clicks_as_rref' => 'clicks_as_rref + 1',
				'last_click_date' => 'NOW()',
			);

			if($expressSettings['referrals_earnings'] && $user['User']['upline_id'] != null) {
				$this->User->Upline->contain(array(
					'ActiveMembership.Membership',
				));
				$upline = $this->User->Upline->findById($user['User']['upline_id']);
				if(!empty($upline)) {
					$updateData = array(
						"dref_clicks_$todayNumber" => "dref_clicks_$todayNumber + 1",
						'total_drefs_clicks' => 'total_drefs_clicks + 1',
					);

					if($this->checkUserActivity($user['User']['upline_id'])) {
						$uplineClickValue = $this->ExpressAd->ClickValue->findByMembershipId($upline['ActiveMembership']['Membership']['id']);

						if(bccomp($uplineClickValue['ClickValue']['direct_referral_click_value'], '0') >= 1) {
							$updateUserData["clicks_as_dref_credited_$todayNumber"] = "clicks_as_dref_credited_$todayNumber + 1";
							$updateUserData['clicks_as_dref_credited'] = 'clicks_as_dref_credited + 1';

							$updateData['total_drefs_credited_clicks'] = 'total_drefs_credited_clicks + 1';
							$updateData["dref_clicks_credited_$todayNumber"] = "dref_clicks_credited_$todayNumber + 1";
							$updateData['total_drefs_clicks_earned'] = 'total_drefs_clicks_earned + '.$uplineClickValue['ClickValue']['direct_referral_click_value'];
							$this->User->Upline->accountBalanceAdd($uplineClickValue['ClickValue']['direct_referral_click_value'], $user['User']['upline_id']);
						}

						if($upline['ActiveMembership']['Membership']['points_enabled']) {
							$this->User->Upline->pointsAdd($uplineClickValue['ClickValue']['direct_referral_click_points'], $user['User']['upline_id']);
						}

						$this->User->UserStatistic->recursive = -1;
						if(!$this->User->UserStatistic->updateAll($updateData, array(
							'user_id' => $user['User']['upline_id'],
						))) {
							throw new InternalErrorException(__d('exception', 'Cannot save statistic data'));
						}
					}
				}
			}

			if($expressSettings['referrals_earnings'] && $user['User']['rented_upline_id'] != null) {
				$this->User->RentedUpline->contain(array(
					'ActiveMembership.Membership',
				));
				$rentedUpline = $this->User->RentedUpline->findById($user['User']['rented_upline_id']);
				$uplineClickValue = $this->ExpressAd->ClickValue->findByMembershipId($rentedUpline['ActiveMembership']['Membership']['id']);

				if(!empty($rentedUpline)) {
					$updateData = array(
						"rref_clicks_$todayNumber" => "rref_clicks_$todayNumber + 1",
						'total_rrefs_clicks' => 'total_rrefs_clicks + 1',
					);

					if($this->checkUserActivity($user['User']['rented_upline_id'])) {
						if(bccomp($uplineClickValue['ClickValue']['rented_referral_click_value'], '0') >= 1) {
							$updateUserData["clicks_as_rref_credited_$todayNumber"] = "clicks_as_rref_credited_$todayNumber + 1";
							$updateUserData['clicks_as_rref_credited'] = 'clicks_as_rref_credited + 1';

							$updateData['total_rrefs_credited_clicks'] = 'total_rrefs_credited_clicks + 1';
							$updateData["rref_clicks_credited_$todayNumber"] = "rref_clicks_credited_$todayNumber + 1";
							$updateData['total_rrefs_clicks_earned'] = 'total_rrefs_clicks_earned + '.$uplineClickValue['ClickValue']['rented_referral_click_value'];
							if(!$this->User->RentedUpline->accountBalanceAdd($uplineClickValue['ClickValue']['rented_referral_click_value'], $user['User']['rented_upline_id'])) {
								throw new InternalErrorException(__d('exception', 'Cannot save upline data'));
							}
						}

						if($rentedUpline['ActiveMembership']['Membership']['points_enabled']) {
							$this->User->RentedUpline->pointsAdd($uplineClickValue['ClickValue']['rented_referral_click_points'], $user['User']['rented_upline_id']);
						}

						$this->User->UserStatistic->recursive = -1;
						if(!$this->User->UserStatistic->updateAll($updateData, array(
							'user_id' => $user['User']['rented_upline_id'],
						))) {
							throw new InternalErrorException(__d('exception', 'Cannot save statistic data'));
						}
					}
					if($rentedUpline['RentedUpline']['autopay_enabled']) {
						$this->User->contain();
						$userAP = $this->User->find('first', array(
							'fields' => array('autopay_done'),
							'conditions' => array(
								'User.id' => $userId,
								'DATEDIFF(User.rent_ends, NOW()) >=' => $rentedUpline['ActiveMembership']['Membership']['autopay_trigger_days'],
							)
						));

						if(!empty($userAP) && !$userAP['User']['autopay_done']) {
							$done = false;
							$range = ClassRegistry::init('RentedReferralsPrice')->getRangeByRRefsNo($rentedUpline['ActiveMembership']['Membership']['RentedReferralsPrice'], $rentedUpline['RentedUpline']['rented_refs_count']);

							if(bccomp($rentedUpline['RentedUpline']['purchase_balance'], $range['autopay_price']) >= 0) {
								if(!$this->User->RentedUpline->purchaseBalanceSub($range['autopay_price'], $user['User']['rented_upline_id'])) {
									throw new InternalErrorException(__d('exception', 'Cannot save autopay data'));
								}
								$done = true;
							} else {
								if(bccomp($rentedUpline['RentedUpline']['purchase_balance'], '0') <= 0) {
									$acc = $range['autopay_price'];
								} else {
									$acc = bcsub($range['autopay_price'], $rentedUpline['RentedUpline']['purchase_balance']);
								}

								if(bccomp($rentedUpline['RentedUpline']['account_balance'], $acc) >= 0) {
									$this->User->RentedUpline->recursive = -1;
									$res = $this->User->RentedUpline->updateAll(array(
										'RentedUpline.account_balance' => "`RentedUpline`.`account_balance` - '$acc'",
										'RentedUpline.purchase_balance' => "0",
									), array(
										'RentedUpline.id' => $user['User']['rented_upline_id'],
									));
									if(!$res) {
										throw new InternalErrorException(__d('exception', 'Cannot save autopay data'));
									}
									$done = true;
								}
							}

							if($done) {
								$this->User->recursive = -1;
								$res = $this->User->updateAll(array(
									'User.rent_ends' => 'DATE_ADD(User.rent_ends, INTERVAL 1 DAY)',
								), array(
									'User.id' => $userId,
								));

								if(!$res) {
									throw new InternalErrorException(__d('exception', 'Cannot save autopay data'));
								}
								ClassRegistry::init('AutopayHistory')->add($range['autopay_price'], $userAP['User']['rented_upline_id']);
							}
						}
					}
				}
			}

			$this->User->UserStatistic->recursive = -1;
			if(!$this->User->UserStatistic->updateAll($updateUserData, array(
				'user_id' => $userId,
			))) {
				throw new InternalErrorException(__d('exception', 'Cannot save statistic data'));
			}

			$this->User->accountBalanceAdd($ad['ClickValue'][0]['user_click_value'], $userId);
			if($user['ActiveMembership']['Membership']['points_enabled']) {
				$this->User->pointsAdd($ad['ClickValue'][0]['user_click_points'], $userId);
			}
			$this->User->id = $userId;
			$this->User->saveField('autopay_done', true);

			$this->User->ExpressAdsVisitedAds->create();
			$data = array(
				'ExpressAdsVisitedAds' => array(
					'user_id' => $userId,
					'express_ad_id' => $adId,
				),
			);
			if(!$this->User->ExpressAdsVisitedAds->save($data)) {
				throw new InternalErrorException(__d('exception', 'Failed to mark express ad visited'));
			}

			$this->ExpressAd->id = $adId;
			$adData = array(
				'clicks' => $ad['ExpressAd']['clicks'] + 1,
			);

			if(($ad['ExpressAd']['package_type'] === 'Clicks')) {
				$adData['expiry'] = $ad['ExpressAd']['expiry'] - 1;

				if($ad['ExpressAd']['expiry'] - 1 <= 0) {
					$adData['status'] = 'Inactive';
				}
			}

			if(!$this->ExpressAd->save(array('ExpressAd' => $adData), true, array('clicks', 'expiry', 'status'))) {
				throw new InternalErrorException(__d('exception', 'Cannot save express ad data'));
			}

			$this->User->id = $userId;
			$this->User->contain();
			$this->User->read(array('location', 'first_click'));

			if(!$this->ExpressAd->ClickHistory->addClick('ExpressAd', $adId, $this->User->data['User']['location'])) {
				throw new InternalErrorException(__d('exception', 'Cannot save ad statistics'));
			}

			if($this->User->data['User']['first_click'] == null) {
				$this->User->saveField('first_click', date('Y-m-d H:i:s'));
			}
		}

		if(!$ad['ExpressAd']['hide_referer']) {
			return $this->redirect($ad['ExpressAd']['url']);
		}

		$this->set(compact('ad'));
	}

/**
 * admin_settings method
 *
 * @return void
 */
	public function admin_settings() {
		$keys = array(
			'expressAds',
		);
		$globalKeys = array(
			'expressAdsActive',
		);

		if($this->request->is(array('post', 'put'))) {
			if(isset($this->request->data['Settings'])) {
				if($this->Settings->store($this->request->data, $globalKeys, true)
				 && $this->Settings->store($this->request->data, $keys)) {
					$this->Notice->success(__d('admin', 'Settings saved successfully'));
				} else {
					$this->Notice->error(__d('admin', 'The settings could not be saved. Please, try again.'));
				}
			}

			if(isset($this->request->data['ExpressAdsPackage'])) {
				if($this->ExpressAdsPackage->saveMany($this->request->data['ExpressAdsPackage'])) {
					$this->Notice->success(__d('admin', 'Express ads packages saved successfully'));
				} else {
					$this->Notice->error(__d('admin', 'Failed to save Express ads packages. Please, try again.'));
				}
			}

			if(isset($this->request->data['ClickValue'])) {
				if($this->ExpressAd->ClickValue->saveMany($this->request->data['ClickValue'])) {
					$this->Notice->success(__d('admin', 'Express ads click values saved successfully'));
					unset($this->request->data['ClickValue']);
				} else {
					$this->Notice->error(__d('admin', 'Failed to save Express ads click values. Please, try again.'));
				}
			}
		}
		$settings = $this->Settings->fetch($keys);

		foreach($globalKeys as $k) {
			$settings['Settings'][$k] = Configure::read($k);
		}

		$this->ExpressAdsPackage->recursive = -1;
		$packets = $this->ExpressAdsPackage->find('all');
		$packets = Hash::extract($packets, '{n}.ExpressAdsPackage');

		$this->request->data = Hash::merge($settings, $this->request->data);
		if(isset($this->request->data['ExpressAdsPackage'])) {
			$this->request->data['ExpressAdsPackage'] = Hash::merge($packets, $this->request->data['ExpressAdsPackage']);
		} else {
			$this->request->data['ExpressAdsPackage'] = $packets;
		}

		$memberships = ClassRegistry::init('Membership')->getList();
		$this->ExpressAd->ClickValue->recursive = -1;
		$clickValues = $this->ExpressAd->ClickValue->find('all');
		$clickValues = Hash::combine($clickValues, '{n}.ClickValue.id', '{n}.ClickValue');

		if(count($clickValues) != count($memberships)) {
			foreach($memberships as $membershipId => $membershipName) {
				$isThere = false;
				foreach($clickValues as $clickValue) {
					if($clickValue['membership_id'] == $membershipId) {
						$isThere = true;
						break;
					}
				}
				if($isThere == false) {
					$newClickValue['membership_id'] = $membershipId;
					$clickValues[] = $newClickValue;
				}
			}
		}

		if(isset($this->request->data['ClickValue'])) {
			$this->request->data['ClickValue'] = Hash::merge($clickValues, $this->request->data['ClickValue']);
		} else {
			$this->request->data['ClickValue'] = $clickValues;
		}

		$this->set('packetsNo', count($packets));
		$this->set('packagesTypes', $this->ExpressAdsPackage->getTypesList());
		$this->set(compact('memberships'));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$memberships = $this->ExpressAd->TargettedMemberships->find('list');
		$packageTypes = $this->ExpressAdsPackage->getTypesList();
		$countries = $this->Location->getCountriesList();

		$this->set(compact('countries', 'memberships', 'packageTypes'));

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['TargettedMemberships']['TargettedMemberships'])) {
				if(!empty($this->request->data['Advertiser']['username'])) {
					$this->ExpressAd->Advertiser->contain();
					$advertiser = $this->ExpressAd->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));

					if(empty($advertiser)) {
						return $this->Notice->error(__d('admin', 'Advertiser not found.'));
					}

					$this->request->data['ExpressAd']['advertiser_id'] = $advertiser['Advertiser']['id'];
				}

				if($this->request->data['ExpressAd']['package_type'] == 'Days') {
					$this->request->data['ExpressAd']['expiry'] = 1;
				} else {
					$this->request->data['ExpressAd']['expiry_date'] = null;
				}

				$this->ExpressAd->create();
				if($this->ExpressAd->save($this->request->data)) {
					$this->Notice->success(__d('admin', 'The advertisement has been saved.'));
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Notice->error(__d('admin', 'The advertisement could not be saved. Please, try again.'));
				}
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one membership'));
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
		if(!$this->ExpressAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['TargettedMemberships']['TargettedMemberships'])) {
				if(Module::active('AccurateLocationDatabase') || !empty($this->request->data['ExpressAd']['TargettedLocations'])) {
					$settings = ClassRegistry::init('Settings')->fetchOne('expressAds');

					$this->ExpressAd->Advertiser->contain();
					$advertiser = $this->ExpressAd->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));
					if(!empty($advertiser) || empty($this->request->data['Advertiser']['username'])) {
						if(empty($this->request->data['Advertiser']['username'])) {
							$this->request->data['ExpressAd']['advertiser_id'] = null;
						} else {
							$this->request->data['ExpressAd']['advertiser_id'] = $advertiser['Advertiser']['id'];
						}

						if($this->request->data['ExpressAd']['package_type'] == 'Days') {
							$this->request->data['ExpressAd']['expiry'] = 1;
						} else {
							$this->request->data['ExpressAd']['expiry_date'] = null;
						}

						$this->request->data['ExpressAd']['id'] = $id;

						if($this->ExpressAd->save($this->request->data)) {
							$this->Notice->success(__d('admin', 'The advertisement has been saved.'));
							return $this->redirect(array('action' => 'index'));
						} else {
							$this->Notice->error(__d('admin', 'The advertisement could not be saved. Please, try again.'));
						}
					} else {
						$this->Notice->error(__d('admin', 'Wrong advertiser'));
					}
				} else {
					$this->Notice->error(__d('admin', 'Please select at least one country'));
				}
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one membership'));
			}
		}

		$this->ExpressAd->contain(array('TargettedLocations', 'TargettedMemberships', 'Advertiser' => array('id', 'username')));
		$this->request->data = Hash::merge($this->ExpressAd->findById($id), $this->request->data);

		if(Module::active('AccurateLocationDatabase')) {
			$tald = ClassRegistry::init('AccurateLocationDatabase.AccurateLocationDatabaseLocation');
			foreach($this->request->data['AccurateTargettedLocations'] as &$l) {
				$data = $tald->getListByParentName($l['country']);
				$l['country_regions'] = array_combine($data, $data);
				$data = $tald->getListByParentName($l['region']);
				$l['region_cities'] = array_combine($data, $data);
			}
		}

		$memberships = $this->ExpressAd->TargettedMemberships->find('list');
		$countries = $this->Location->getCountriesList();
		$packageTypes = $this->ExpressAd->ExpressAdsPackage->getTypesList();
		$this->set(compact('countries', 'memberships', 'packageTypes'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$conditions = $this->createPaginatorConditions(array(
			'Advertiser.username',
			'ExpressAd.url',
			'ExpressAd.title',
		));

		if(isset($conditions['Advertiser.username LIKE']) 
		 && $conditions['Advertiser.username LIKE'] == '%Admin%') {
			unset($conditions['Advertiser.username LIKE']);
			$conditions['ExpressAd.advertiser_id'] = null;
		}

		$inCollapse = array(
			'ExpressAd.status',
		);

		$inCollapse = array_intersect_key($conditions, array_flip($inCollapse));
		if(!empty($inCollapse)) {
			$this->set('searchCollapse', 'in');
		} else {
			$this->set('searchCollapse', '');
		}

		$this->ExpressAd->contain(array('Advertiser'));
		$this->paginate = array(
			'fields' => array(
				'ExpressAd.id',
				'ExpressAd.advertiser_name',
				'ExpressAd.advertiser_id',
				'ExpressAd.title',
				'ExpressAd.url',
				'ExpressAd.package_type',
				'ExpressAd.expiry',
				'ExpressAd.clicks',
				'ExpressAd.outside_clicks',
				'ExpressAd.status',
				'ExpressAd.expiry_date',
				'ExpressAd.hide_referer',
			),
			'order' => 'ExpressAd.created DESC',
		);
		$ads = $this->Paginator->paginate($conditions);
		$this->set('ads', $ads);
		$this->set('statuses', $this->ExpressAd->getStatusesList());
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if(!$this->ExpressAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->ExpressAd->delete($id)) {
			$this->Notice->success(__d('admin', 'The advertisement has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The advertisement could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_inactivate
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_inactivate($id = null) {
		if(!$this->ExpressAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->ExpressAd->inactivate($id)) {
			$this->Notice->success(__d('admin', 'The advertisement has been inactivated.'));
		} else {
			$this->Notice->error(__d('admin', 'The advertisement could not be inactivated. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_activate
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_activate($id = null) {
		if(!$this->ExpressAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->ExpressAd->activate($id)) {
			$this->Notice->success(__d('admin', 'The advertisement has been activated.'));
		} else {
			$this->Notice->error(__d('admin', 'The advertisement could not be activated. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * admin_massaction method
 *
 * @throws NotFoundException
 * @return void
 */
	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {

			if(!isset($this->request->data['ExpressAds']) || empty($this->request->data['ExpressAds'])) {
				$this->Notice->error(__d('admin', 'Please select at least one ad.'));
				return $this->redirect($this->referer());
			}

			$ads = 0;
			foreach($this->request->data['ExpressAds'] as $id => $on) {
				if($on) {
					$ads++;
					if(!$this->ExpressAd->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid advertisement'));
					}
				}
			}

			foreach($this->request->data['ExpressAds'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'inactivate':
							$this->ExpressAd->inactivate($id);
						break;

						case 'activate':
							$this->ExpressAd->activate($id);
						break;

						case 'delete':
							$this->ExpressAd->delete($id);
						break;
					}
				}
			}
			if($ads) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one ad.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect($this->referer());
	}

/**
 * admin_delete_package method
 *
 * @return void
 */
	public function admin_delete_package($id = null) {
		$this->ExpressAdsPackage->id = $id;

		if(!$id || !$this->ExpressAdsPackage->exists()) {
			throw new NotFoundException(__d('admin', 'Invalid package'));
		}

		if($this->ExpressAdsPackage->delete()) {
			$this->Notice->success(__d('admin', 'Package sucessfully removed.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to delete package. Please, try again.'));
		}

		return $this->redirect(array('action' => 'settings', '#' => 'packages'));
	}
}
