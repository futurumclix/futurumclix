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
class ExplorerAdsController extends AppController {
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
		'Report',
		'Captcha' => array(
			'mode' => 'surfer',
		),
	);

/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'ExplorerAd',
		'ExplorerAdsPackage',
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

		if($this->request->prefix != 'admin' && !Configure::read('explorerAdsActive')) {
			throw new NotFoundException(__d('exception', 'Explorer Ads are disabled'));
		}

		$this->Auth->allow(array('view', 'next_subpage'));
		$this->Captcha->protect(array('next_subpage', 'verify_captcha'));

		if(Module::active('AccurateLocationDatabase')) {
			if($this->request->params['action'] == 'targetting' || $this->request->params['action'] == 'admin_edit') {
				if(isset($this->request->data['ExplorerAd']['id'])) {
					$start = $this->ExplorerAd->TargettedLocations->find('count', array(
						'conditions' => array(
							'explorer_ad_id' => $this->request->data['ExplorerAd']['id'],
						),
					));
				} else {
					$start = 0;
				}

				if(isset($this->request->data['ExplorerAd']['AccurateTargettedLocations'])) {
					$stop = count($this->request->data['ExplorerAd']['AccurateTargettedLocations']);
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
			if(isset($this->request->data['ExplorerAdsPackage'])) {
				$start = $this->ExplorerAdsPackage->find('count');
				$stop = count($this->request->data['ExplorerAdsPackage']);

				if($start <= 0) {
					$start = 1;
				}

				for(;$start < $stop; $start++) {
					$this->Security->unlockedFields[] = 'ExplorerAdsPackage.'.$start.'.type';
					$this->Security->unlockedFields[] = 'ExplorerAdsPackage.'.$start.'.amount';
					$this->Security->unlockedFields[] = 'ExplorerAdsPackage.'.$start.'.price';
					$this->Security->unlockedFields[] = 'ExplorerAdsPackage.'.$start.'.subpages';
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
		$settings = $this->Settings->fetchOne('explorerAds');
		$user_id = $this->Auth->user('id');

		$this->ExplorerAd->expireDaysAds($user_id);

		$this->ExplorerAd->contain();
		$ads = $this->ExplorerAd->find('all', array(
			'fields' => array(
				'ExplorerAd.id',
				'ExplorerAd.title',
				'ExplorerAd.url',
				'ExplorerAd.clicks',
				'ExplorerAd.outside_clicks',
				'ExplorerAd.status',
				'ExplorerAd.package_type',
				'ExplorerAd.expiry',
				'ExplorerAd.expiry_date',
				'ExplorerAd.description',
				'ExplorerAd.subpages',
				'ExplorerAd.modified',
				'ExplorerAd.created',
			),
			'conditions' => array(
				'ExplorerAd.advertiser_id' => $user_id,
			),
		));
		$this->ExplorerAd->contain();
		$ads_no = $this->ExplorerAd->find('count', array(
			'conditions' => array(
				'ExplorerAd.advertiser_id' => $user_id,
				'ExplorerAd.status' => 'Active',
			)
		));
		$this->ExplorerAd->ClickHistory->recursive = -1;
		$clicksToday = $this->ExplorerAd->ClickHistory->find('all', array(
			'fields' => array(
				'ExplorerAd.id',
				'SUM(ClickHistory.clicks) as sum',
			),
			'joins' => array(
				array(
					'table' => 'explorer_ads',
					'alias' => 'ExplorerAd',
					'type' => 'INNER',
					'conditions' => array('ClickHistory.foreign_key = ExplorerAd.id')
				),
			),
			'conditions' => array(
				'ExplorerAd.advertiser_id' => $user_id,
				'ClickHistory.model' => 'ExplorerAd',
				'ClickHistory.created' => date('Y-m-d'),
			),
			'group' => 'ExplorerAd.id',
		));

		$clicksToday = Hash::combine($clicksToday, '{n}.ExplorerAd.id', '{n}.0.sum');

		$this->set(compact('ads', 'ads_no', 'clicksToday', 'settings'));
		$this->set('packsSum', $this->User->BoughtItems->sumExplorerAdsPackages());
		$this->set('breadcrumbTitle', __('Explorer Advertisement panel'));
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

		$this->ExplorerAd->contain();
		$ad = $this->ExplorerAd->find('first', array(
			'fields' => array('id'),
			'conditions' => array(
				'ExplorerAd.id' => $id,
				'ExplorerAd.advertiser_id' => $user_id,
			),
		));

		if(empty($ad)) {
			throw new NotFoundExcption(__('Invalid advertisement'));
		}

		$this->ExplorerAd->id = $id;
		if($this->ExplorerAd->delete()) {
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
		$user_id = $this->Auth->user('id');

		$this->ExplorerAd->expireDaysAds($user_id);

		$this->ExplorerAd->id = $adId;
		$ad = $this->ExplorerAd->find('first', array(
			'conditions' => array(
				'ExplorerAd.id' => $adId,
				'ExplorerAd.advertiser_id' => $user_id,
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$settings = $this->Settings->fetchOne('explorerAds', null);

		if($settings === null) {
			throw new InternalErrorException(__d('exception', 'Failed to read settings'));
		}

		if(!$settings['autoApprove'] && $ad['ExplorerAd']['package_type'] == 'Days') {
			throw new NotFoundException(__d('exception', 'Editing ads with package_type "Days" is not allowed'));
		}

		if(!$this->request->is(array('post', 'put'))) {
			if($this->Session->check('NewExplorerAd')) {
				$this->request->data['Ad'] = $this->Session->read('NewExplorerAd');
				$this->Session->delete('NewExplorerAd');
			} else {
				$this->request->data = $ad;
			}
			if($this->Session->check('NewExplorerAdErrors')) {
				$this->ExplorerAd->validationErrors = $this->Session->read('NewExplorerAdErrors');
				$this->Session->delete('NewExplorerAdErrors');
			}
		}

		$this->ExplorerAd->recursive = 1;
		$ads_no = $this->ExplorerAd->find('count', array(
			'conditions' => array(
				'ExplorerAd.advertiser_id' => $user_id,
				'ExplorerAd.status' => 'Active',
			)
		));

		$titleMax = $this->ExplorerAd->validate['title']['maxLength']['rule'][1];
		$descMax = $this->ExplorerAd->validate['description']['maxLength']['rule'][1];

		$this->request->data['ExplorerAd']['preview_subpages'] = $settings['previewSubpages'];

		$this->set(compact('ads_no', 'titleMax', 'descMax'));
		$this->set('packsSum', $this->User->BoughtItems->sumPTCPackages());
		$this->set('breadcrumbTitle', __('Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$user_id = $this->Auth->user('id');
		$settings = $this->Settings->fetchOne('explorerAds', null);

		if($settings === null) {
			throw new InternalErrorException(__d('exception', 'Failed to read settings'));
		}

		$ads_no = $this->ExplorerAd->find('count', array(
			'conditions' => array(
				'ExplorerAd.advertiser_id' => $user_id,
				'ExplorerAd.status' => 'Active',
			)
		));

		if(!$this->request->is(array('post', 'put'))) {
			if($this->Session->check('NewAd')) {
				$this->request->data['ExplorerAd'] = $this->Session->read('NewExplorerAd');
				$this->Session->delete('NewExplorerAd');
			}
			if($this->Session->check('NewExplorerAdErrors')) {
				$this->ExplorerAd->validationErrors = $this->Session->read('NewExplorerAdErrors');
				$this->Session->delete('NewExplorerAdErrors');
			}
		}

		$titleMax = $this->ExplorerAd->validate['title']['maxLength']['rule'][1];
		$descMax = $this->ExplorerAd->validate['description']['maxLength']['rule'][1];

		$this->request->data['ExplorerAd']['preview_subpages'] = $settings['previewSubpages'];

		$this->set(compact('ads_no', 'titleMax', 'descMax'));
		$this->set('packsSum', $this->User->BoughtItems->sumPTCPackages());
		$this->set('breadcrumbTitle', __('Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * preview method
 *
 * @return void
 */
	public function preview() {
		$this->request->allowMethod(array('post', 'put'));
		$settings = $this->Settings->fetch(array('PTCCheckConnection', 'PTCCheckConnectionTimeout', 'PTCPreviewTime', 'PTCAutoApprove', 'explorerAds'));

		$previewSubPages = $settings['Settings']['explorerAds']['previewSubpages'];

		if(isset($this->request->data['ExplorerAd']['checked']) && $this->request->data['ExplorerAd']['checked']) {
			unset($this->request->data['ExplorerAd']['checked']);

			if(!$this->Session->check('NewExplorerAd')) {
				throw new InternalErrorException(__d('exception', 'No advertisement data'));
			}
			$this->request->data['ExplorerAd'] = $this->Session->read('NewExplorerAd');
			$this->request->data['ExplorerAd']['advertiser_id'] = $this->Auth->user('id');

			$memberships = $this->ExplorerAd->TargettedMemberships->find('list');
			$this->request->data['TargettedMemberships']['TargettedMemberships'] = array_keys($memberships);

			if(!$settings['Settings']['explorerAds']['autoApprove']) {
				$this->request->data['ExplorerAd']['status'] = 'Pending';
			}

			if(!$this->Session->check('NewAd.id')) {
				$this->ExplorerAd->create();
			}

			if($this->ExplorerAd->save($this->request->data)) {
				$this->Notice->success(__('Advertisement saved successfully.'));
				$this->Session->delete('NewExplorerAd');
				$this->Session->delete('NewExplorerAdErrors');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->write('NewExplorerAdErrors', $this->ExplorerAd->validationErrors);
				$this->Notice->error(__('Failed to save advertisement. Please, try again.'));
				return $this->redirect($this->referer());
			}
		} else {
			if(!empty($this->request->data['ExplorerAd']['preview_subpages']) && $this->request->data['ExplorerAd']['preview_subpages'] != 0) {
				$previewSubPages = $this->request->data['ExplorerAd']['preview_subpages'];
				if($previewSubPages >= 255 || $previewSubPages < 0) {
					$previewSubPages = 3;
				}
			}
			unset($this->request->data['ExplorerAd']['preview_subpages']);
			$this->Session->write('NewExplorerAd', $this->request->data['ExplorerAd']);
		}

		$this->layout = 'surfer';

		$hide_referer = $this->request->data['ExplorerAd']['hide_referer'];
		$url = $this->request->data['ExplorerAd']['url'];

		if(!filter_var($url, FILTER_VALIDATE_URL)) {
			$this->Notice->error(__('Please supply a valid http(s) URL.'));
			return $this->redirect($this->referer());
		}

		if($settings['Settings']['PTCCheckConnection']) {
			App::uses('HttpSocket', 'Network/Http');
			$connection = new HttpSocket(array('ssl_verify_peer' => false, 'ssl_verify_host' => false, 'ssl_allow_self_signed' => true, 'ssl_verify_peer_name' => false));

			try {
				$uri = parse_url($url);
				$uri['timeout'] = $settings['Settings']['PTCCheckConnectionTimeout'];
				$result = $connection->get($uri, array(), array('redirect' => 30));
			} catch(CakeException $e) {
				$this->Notice->error(__('Failed to connect to the service. Socket error: %s', $e->getMessage()));
				return $this->redirect($this->referer());
			}

			if(!$result->isOk()) {
				$this->Notice->error(__('Failed to connect to the service. Please check if URL is valid and server is running.'));
				return $this->redirect($this->referer());
			}

			if(!strcasecmp($result->getHeader('X-Frame-Options'), 'SAMEORIGIN')) {
				$this->Notice->error(__('This site have set X-Frame-Options to SAMEORIGIN, it cannot be displayed in frame.'));
				return $this->redirect($this->referer());
			}
		}

		$this->ExplorerAd->create();
		$this->ExplorerAd->set($this->request->data);
		if(!$this->ExplorerAd->validates()) {
			$this->Session->write('NewExplorerAdErrors', $this->ExplorerAd->validationErrors);
			return $this->redirect($this->referer());
		}

		$adTime = $settings['Settings']['explorerAds']['previewTime'] * 1000;
		$this->set(compact('url', 'adTime', 'hide_referer', 'settings', 'previewSubPages'));
	}

/**
 * assign method
 *
 * @return void
 */
	public function assign($adId = null) {
		$user_id = $this->Auth->user('id');

		$this->ExplorerAd->expireDaysAds($user_id);

		$packetsData = ClassRegistry::init('BoughtItem')->find('all', array(
			'fields' => array('BoughtItem.id', 'ExplorerAdsPackage.id', 'ExplorerAdsPackage.type', 'ExplorerAdsPackage.amount', 'ExplorerAdsPackage.subpages'),
			'recursive' => -1,
			'conditions' => array(
				'BoughtItem.user_id' => $user_id,
				'BoughtItem.model' => 'ExplorerAdsPackage',
			),
			'joins' => array(
				array(
					'table' => 'explorer_ads_packages',
					'alias' => 'ExplorerAdsPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = ExplorerAdsPackage.id',
					),
				),
			),
		));

		$this->ExplorerAd->contain();
		$ads = $this->ExplorerAd->find('list', array(
			'conditions' => array(
				'ExplorerAd.advertiser_id' => $user_id,
				'ExplorerAd.status !=' => 'Pending',
				'ExplorerAd.expiry' => 0,
			),
		));

		if($this->request->is(array('post', 'put'))) {
			if(!isset($ads[$this->request->data['explorer_ad_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Wrong package'));
			}
			$packetsData = Hash::combine($packetsData, '{n}.BoughtItem.id', '{n}');
			if(!isset($packetsData[$this->request->data['package_id']]) || empty($packetsData[$this->request->data['package_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Wrong package'));
			}
			if($this->ExplorerAd->assignPack($packetsData[$this->request->data['package_id']], $this->request->data['explorer_ad_id'])) {
				$this->Notice->success(__('Package successfully assigned.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		if($adId && in_array($adId, array_keys($ads))) {
			$this->request->data['explorer_ad_id'] = $adId;
		}

		$this->ExplorerAd->contain();
		$ads_no = $this->ExplorerAd->find('count', array(
			'conditions' => array(
				'ExplorerAd.advertiser_id' => $user_id,
				'ExplorerAd.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('%d %s, %d SubPages', $v['ExplorerAdsPackage']['amount'], $v['ExplorerAdsPackage']['type'], $v['ExplorerAdsPackage']['subpages']);
		}

		$this->set(compact('ads_no', 'packages', 'ads'));
		$this->set('packsSum', $this->User->BoughtItems->sumExplorerAdsPackages());
		$this->set('breadcrumbTitle', __('Explorer Advertisement panel'));
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

		$this->ExplorerAd->expireDaysAds($user_id);

		$packetsConditions = array(
			'BoughtItem.user_id' => $user_id,
			'BoughtItem.model' => 'ExplorerAdsPackage',
		);

		$activeAd = $this->ExplorerAd->findById($adId);

		if(empty($activeAd) || $activeAd['ExplorerAd']['advertiser_id'] != $user_id) {
			/* cheater? */
			throw new NotFoundException(__d('exception', 'Invalid ad'));
		}

		if(!$activeAd['ExplorerAd']['subpages']) {
			return $this->redirect(array('action' => 'assign', $activeAd['ExplorerAd']['id']));
		}

		if($activeAd['ExplorerAd']['expiry'] > 0) {
			$packetsConditions['ExplorerAdsPackage.type'] = $activeAd['ExplorerAd']['package_type'];
		}

		$packetsData = ClassRegistry::init('BoughtItem')->find('all', array(
			'fields' => array('BoughtItem.id', 'ExplorerAdsPackage.id', 'ExplorerAdsPackage.type', 'ExplorerAdsPackage.amount', 'ExplorerAdsPackage.subpages'),
			'recursive' => -1,
			'conditions' => $packetsConditions,
			'joins' => array(
				array(
					'table' => 'explorer_ads_packages',
					'alias' => 'ExplorerAdsPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = ExplorerAdsPackage.id',
						'ExplorerAdsPackage.subpages' => $activeAd['ExplorerAd']['subpages'],
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
			if($this->ExplorerAd->addPack($packetsData[$this->request->data['package_id']], $adId)) {
				$this->Notice->success(__('Package successfully assigned.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		$ads = Hash::combine($activeAd, 'ExplorerAd.id', 'ExplorerAd.title');

		$this->ExplorerAd->contain();
		$ads_no = $this->ExplorerAd->find('count', array(
			'conditions' => array(
				'ExplorerAd.advertiser_id' => $user_id,
				'ExplorerAd.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('%d %s, %d SubPages', $v['ExplorerAdsPackage']['amount'], $v['ExplorerAdsPackage']['type'], $v['ExplorerAdsPackage']['subpages']);
		}

		$this->set(compact('ads_no', 'packages', 'ads', 'activeAd'));
		$this->set('packsSum', $this->User->BoughtItems->sumExplorerAdsPackages());
		$this->set('breadcrumbTitle', __('Explorer Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * targetting method
 *
 * @return void
 */
	public function targetting($type, $adId = null) {
		$settings = $this->Settings->fetchOne('explorerAds', null);

		if($settings === null) {
			throw new InternalErrorException(__d('exception', 'Invalid explorer ads settings'));
		}

		switch($type) {
			case 'memberships':
				$contain = 'TargettedMemberships';
			break;

			case 'geo':
				$contain = 'TargettedLocations';
				if(!$settings['geo_targetting']) {
					throw new NotFoundException(__d('exception', 'Geo-targetting is not available'));
				}
			break;

			default:
				throw new NotFoundException(__d('exception', 'Invalid targeting mode'));
		}
		$user_id = $this->Auth->user('id');

		$this->ExplorerAd->id = $adId;
		$this->ExplorerAd->contain($contain);
		$ad = $this->ExplorerAd->find('first', array(
			'conditions' => array(
				'ExplorerAd.id' => $adId,
				'ExplorerAd.advertiser_id' => $user_id,
				'ExplorerAd.status !=' => 'Pending',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->request->is(array('post', 'put'))) {
			$this->ExplorerAd->id = $adId;

			if($this->ExplorerAd->save($this->request->data)) {
				$this->Notice->success(__('ExplorerAd sucessfully saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Error while saving ad. Please try again.'));
			}
		}

		$this->request->data = Hash::merge($ad, $this->request->data);

		$this->ExplorerAd->contain();
		$ads_no = $this->ExplorerAd->find('count', array(
			'conditions' => array(
				'ExplorerAd.advertiser_id' => $user_id,
				'ExplorerAd.status' => 'Active',
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
				$options = $this->ExplorerAd->TargettedMemberships->find('list');
				$selected = null;
			break;
		}

		$this->set(compact('ads_no', 'options', 'saveField', 'title', 'selected'));
		$this->set('packsSum', $this->User->BoughtItems->sumExplorerAdsPackages());
		$this->set('breadcrumbTitle', __('Explorer Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * statistics method
 *
 * @return void
 */
	public function statistics($adId = null) {
		$settings = $this->Settings->fetchOne('explorerAds', null);

		if($settings === null) {
			throw new InternalErrorException(__d('exception', 'Invalid explorer ads settings'));
		}

		$geo_targetting = $settings['geo_targetting'];

		$this->ExplorerAd->id = $adId;
		$this->ExplorerAd->contain();
		$ad = $this->ExplorerAd->find('first', array(
			'fields' => array('ExplorerAd.id', 'ExplorerAd.title'),
			'recursive' => 1,
			'conditions' => array(
				'ExplorerAd.id' => $adId,
				'ExplorerAd.advertiser_id' => $this->Auth->user('id'),
				'ExplorerAd.status !=' => 'Pending',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$data = $this->ExplorerAd->ClickHistory->find('all', array(
			'fields' => array('SUM(clicks) as sum', 'created'),
			'conditions' => array(
				'model' => 'ExplorerAd',
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));

		$clicks = Hash::combine($data, '{n}.ClickHistory.created', '{n}.0.sum');

		if($geo_targetting) {
			$this->helpers[] = 'Map';

			$data = $this->ExplorerAd->ClickHistory->find('all', array(
				'fields' => array('SUM(clicks) as sum', 'Country.code as code'),
				'conditions' => array(
					'model' => 'ExplorerAd',
					'foreign_key' => $adId,
				),
				'recursive' => 1,
				'order' => 'Country.country',
				'group' => 'country_id',
			));

			$geo = Hash::combine($data, '{n}.Country.code', '{n}.0.sum');

			$this->set(compact('geo'));
		}

		$this->ExplorerAd->contain();
		$ads_no = $this->ExplorerAd->find('count', array(
			'conditions' => array(
				'ExplorerAd.advertiser_id' => $this->Auth->user('id'),
				'ExplorerAd.status' => 'Active',
			)
		));

		$this->set(compact('clicks', 'ad', 'ads_no', 'geo_targetting'));
		$this->set('packsSum', $this->User->BoughtItems->sumExplorerAdsPackages());
		$this->set('breadcrumbTitle', __('Explorer Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * activate method
 *
 * @return void
 */
	public function activate($adId = null) {
		$this->request->allowMethod('post', 'delete');

		$this->ExplorerAd->id = $adId;
		$ad = $this->ExplorerAd->find('first', array(
			'fields' => array('ExplorerAd.id'),
			'recursive' => -1,
			'conditions' => array(
				'ExplorerAd.id' => $adId,
				'ExplorerAd.advertiser_id' => $this->Auth->user('id'),
				'ExplorerAd.status' => 'Inactive',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->ExplorerAd->activate($adId)) {
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

		$this->ExplorerAd->id = $adId;
		$ad = $this->ExplorerAd->find('first', array(
			'fields' => array('ExplorerAd.id', 'ExplorerAd.package_type'),
			'recursive' => -1,
			'conditions' => array(
				'ExplorerAd.id' => $adId,
				'ExplorerAd.advertiser_id' => $this->Auth->user('id'),
				'ExplorerAd.status' => 'Active',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($ad['ExplorerAd']['package_type'] == 'Days') {
			throw new NotFoundException(__d('exception', 'Inactivating ads with "Days" packages is disabled'));
		}

		if($this->ExplorerAd->inactivate($adId)) {
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

		$this->ExplorerAd->contain();
		$ads_no = $this->ExplorerAd->find('count', array(
			'conditions' => array(
				'ExplorerAd.advertiser_id' => $user_id,
				'ExplorerAd.status' => 'Active',
			)
		));

		$this->ExplorerAd->ExplorerAdsPackage->recursive = -1;
		$packagesData = $this->ExplorerAd->ExplorerAdsPackage->find('all', array(
			'order' => 'ExplorerAdsPackage.price DESC',
		));

		$packages = array();
		foreach($packagesData as &$v) {
			$prices = array();
			$add = false;

			foreach($activeGateways as $k => $g) {
				$price = bcadd($v['ExplorerAdsPackage']['price'], $this->Payments->getDepositFee($v['ExplorerAdsPackage']['price'], $k));
				if($this->Payments->checkMinimumDepositAmount($k, $price)) {
					$prices[$k] = $price;
					$add = true;
				} else {
					$prices[$k] = 'disabled';
				}
			}

			if($add) {
				$packages[$v['ExplorerAdsPackage']['id']] = sprintf('%d %s %d SubPages - %s', $v['ExplorerAdsPackage']['amount'],
				 __($v['ExplorerAdsPackage']['type']), $v['ExplorerAdsPackage']['subpages'], CurrencyFormatter::format($v['ExplorerAdsPackage']['price']));
				$v['ExplorerAdsPackage']['price_per'] = bcdiv($v['ExplorerAdsPackage']['price'], $v['ExplorerAdsPackage']['amount']);
				$v['ExplorerAdsPackage']['prices'] = $prices;
			}
		}
		$packagesData = Hash::combine($packagesData, '{n}.ExplorerAdsPackage.id', '{n}');

		if($this->request->is('post')) {
			$pack_id = $this->request->data['package_id'];

			if(!isset($packagesData[$pack_id])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}

			if($this->Payments->checkMinimumDepositAmount($this->request->data['gateway'], $packagesData[$pack_id]['ExplorerAdsPackage']['price'])) {
				$this->Payments->pay('ExplorerAdsPackage', $this->request->data['gateway'], $packagesData[$pack_id]['ExplorerAdsPackage']['price'],
				 $this->Auth->user('id'), array('package_id' => $pack_id));
			} else {
				$this->Notice->error(__('Minimum deposit amount for %s is %s.', $this->request->data['gateway'], CurrencyFormatter::format($this->Payments->getMinimumDepositAmount($this->request->data['gateway']))));
			}
		}

		$this->set(compact('ads_no', 'categories', 'packages', 'activeGateways', 'packagesData'));
		$this->set('breadcrumbTitle', __('Explorer ads panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumExplorerAdsPackages());
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * _getAdForUser method
 *
 * @return array
 */
	protected function _getAdForUser($adId, $settings = null) {
		if($settings == null) {
			$settings = $this->Settings->fetchOne('explorerAds', null);

			if($settings === null) {
				throw new InternalErrorException(__d('exception', 'Missing configuration'));
			}
		}

		if(!$this->Auth->loggedIn()) {
			$ad = $this->ExplorerAd->getAdForUser($adId);

			if(!$ad || empty($ad)) {
				throw new NotFoundException(__d('exception', 'Invalid explorer ad'));
			}
		} else {
			$userId = $this->Auth->user('id');

			$this->User->contain(array(
				'ActiveMembership.Membership',
			));
			$user = $this->User->findById($userId);
			$membership_id = $user['ActiveMembership']['Membership']['id'];
			$locations = $this->Location->getConditions($user['User']['location']);

			$ad = $this->ExplorerAd->getAdForUser($adId, $userId, $membership_id, $settings['geo_targetting'] ? $locations : null);

			if(!$ad || empty($ad) || !empty($ad['VisitedAd'])) {
				throw new NotFoundException(__d('exception', 'Invalid explorer ad'));
			}
		}

		return $ad;
	}

/**
 * _creditAd method
 *
 * @return string
 */
	protected function _creditAd($ad) {
		$adId = $ad['ExplorerAd']['id'];
		$userId = $this->Auth->user('id');
		$todayNumber = $this->Settings->fetchOne('activeStatsNumber', null);
		$explorerSettings = $this->Settings->fetchOne('explorerAds', null);

		if($todayNumber === null || $explorerSettings === null) {
			throw new InternalErrorException(__d('exception', 'Missing settings'));
		}

		$this->User->contain(array(
			'ActiveMembership.Membership',
		));
		$user = $this->User->findById($userId);

		if($user['ActiveMembership']['Membership']['points_enabled']) {
			$this->User->pointsAdd($ad['ClickValue'][0]['user_click_points'], $userId);
		}

		$updateUserData = array(
			"user_clicks_$todayNumber" => "user_clicks_$todayNumber + 1",
			'total_clicks' => 'total_clicks + 1',
			'total_clicks_earned' => 'total_clicks_earned + '.$ad['ClickValue'][0]['user_click_value'],
			'clicks_as_dref' => 'clicks_as_dref + 1',
			'clicks_as_rref' => 'clicks_as_rref + 1',
			'last_click_date' => 'NOW()',
		);

		if($explorerSettings['referrals_earnings'] && $user['User']['upline_id'] != null) {
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
					$uplineClickValue = $this->ExplorerAd->ClickValue->find('first', array(
						'conditions' => array(
							'membership_id' => $upline['ActiveMembership']['Membership']['id'],
							'subpages' => $ad['ExplorerAd']['subpages'],
						),
					));

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

		if($explorerSettings['referrals_earnings'] && $user['User']['rented_upline_id'] != null) {
			$this->User->RentedUpline->contain(array(
				'ActiveMembership.Membership',
			));
			$rentedUpline = $this->User->RentedUpline->findById($user['User']['rented_upline_id']);
			$uplineClickValue = $this->ExplorerAd->ClickValue->find('first', array(
				'conditions' => array(
					'membership_id' => $rentedUpline['ActiveMembership']['Membership']['id'],
					'subpages' => $ad['ExplorerAd']['subpages'],
				),
			));

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
					$user = $this->User->find('first', array(
						'fields' => array('autopay_done'),
						'conditions' => array(
							'User.id' => $userId,
							'DATEDIFF(User.rent_ends, NOW()) >=' => $rentedUpline['ActiveMembership']['Membership']['autopay_trigger_days'],
						)
					));

					if(!empty($user) && !$user['User']['autopay_done']) {
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
							ClassRegistry::init('AutopayHistory')->add($range['autopay_price'], $user['User']['rented_upline_id']);
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
		$this->User->id = $userId;
		$this->User->saveField('autopay_done', true);

		$this->User->ExplorerAdsVisitedAds->create();
		$data = array(
			'ExplorerAdsVisitedAds' => array(
				'user_id' => $userId,
				'explorer_ad_id' => $adId,
			),
		);
		if(!$this->User->ExplorerAdsVisitedAds->save($data)) {
			throw new InternalErrorException(__d('exception', 'Failed to mark explorer ad visited'));
		}

		$this->ExplorerAd->id = $adId;
		$adData = array(
			'clicks' => $ad['ExplorerAd']['clicks'] + 1,
		);

		if(($ad['ExplorerAd']['package_type'] === 'Clicks')) {
			$adData['expiry'] = $ad['ExplorerAd']['expiry'] - 1;

			if($ad['ExplorerAd']['expiry'] - 1 <= 0) {
				$adData['status'] = 'Inactive';
			}
		}

		if(!$this->ExplorerAd->save(array('ExplorerAd' => $adData), true, array('clicks', 'expiry', 'status'))) {
			throw new InternalErrorException(__d('exception', 'Cannot save explorer ad data'));
		}

		$this->User->id = $userId;
		$this->User->contain();
		$this->User->read(array('location', 'first_click'));

		if(!$this->ExplorerAd->ClickHistory->addClick('ExplorerAd', $adId, $this->User->data['User']['location'])) {
			throw new InternalErrorException(__d('exception', 'Cannot save ad statistics'));
		}

		if($this->User->data['User']['first_click'] == null) {
			$this->User->saveField('first_click', date('Y-m-d H:i:s'));
		}

		return $ad['ClickValue'][0]['user_click_value'];
	}

/**
 * view method
 *
 * @return void
 */
	public function view($adId = null) {
		$this->layout = 'surfer';
		$settings = $this->Settings->fetchOne('explorerAds', null);

		if($settings === null) {
			throw new InternalErrorException(__d('exception', 'Missing configuration'));
		}

		$ad = $this->_getAdForUser($adId, $settings);

		$data = array(
			'time' => time(),
			'time_step' => $settings['timers'][$ad['ExplorerAd']['subpages']],
		);

		if(!$this->Session->write("ExplorerAds.$adId", $data)) {
			throw new InternalErrorException(__d('exception', 'Session write failed'));
		}

		$adTime = $settings['timers'][$ad['ExplorerAd']['subpages']] * 1000;
		$settings = $this->Settings->fetch(array('focusAdView'))['Settings'] + array('explorerAds' => $settings);

		if(!$this->Auth->loggedIn()) {
			$this->ExplorerAd->id = $adId;
			$this->ExplorerAd->set(array('id' => $adId, 'outside_clicks' => $ad['ExplorerAd']['outside_clicks'] + 1));
			$this->ExplorerAd->save();
			$loggedIn = false;
		} else {
			$loggedIn = true;
		}

		$this->set(compact('ad', 'adTime', 'settings', 'loggedIn'));
	}

/**
 * next_subpage
 *
 * @return void
 */
	public function next_subpage($adId = null) {
		$this->request->allowMethod('ajax');
		$this->autoRender = false;
		$this->layout = 'ajax';
		$ad = $this->_getAdForUser($adId);
		$data = $this->Session->read("ExplorerAds.$adId");
		$now = time();

		if(!$data || $now < $data['time'] + $data['time_step']) {
			/* cheater? */
			throw new InternalErrorException(__d('exception', 'Wrong data in session'));
		}

		if(isset($data['subpage'])) {
			$data['subpage']++;
		} else {
			$data['subpage'] = 1;
		}

		$data['time'] = $now;

		if($data['subpage'] > $ad['ExplorerAd']['subpages']) {
			if($this->Auth->loggedIn()) {
				$captchaType = $this->Settings->fetchOne('captchaTypeSurfer', 'disabled');
				if($captchaType != 'disabled') {
					$this->set(compact('adId'));
					return $this->render('captcha');
				} else {
					$this->Session->delete("ExplorerAds.$adId");
					$earn = $this->_creditAd($ad);
					$this->set(compact('earn'));
					return $this->render('credited');
				}
			}
		} else {
			if(!$this->Session->write("ExplorerAds.$adId", $data)) {
				throw new InternalErrorException(__d('exception', 'Session write failed'));
			}
			return __('Please enter any of subpages.');
		}
	}

/**
 * verify_captcha method
 *
 * @return void
 */
	public function verify_captcha($adId = null) {
		$this->request->allowMethod('ajax');
		$this->layout = 'ajax';
		$ad = $this->_getAdForUser($adId);

		if(!empty($ad) && $this->request->is('post')) {
			$this->Session->delete("ExplorerAds.$adId");
			$earn = $this->_creditAd($ad);
			$this->set(compact('earn'));
			return $this->render('credited');
		}
		throw new NotFoundException(__d('exception', 'Invalid advertisement'));
	}

/**
 * report method
 *
 * @return void
 */
	public function report($adId = null) {
		$this->layout = 'ajax';
		$this->request->allowMethod('ajax', 'post');

		$ad = $this->ExplorerAd->exists($adId);

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->request->is('post')) {
			if(!$this->Report->reportItem($this->request->data['type'], $this->ExplorerAd, $adId, $this->request->data['reason'], $this->Auth->user('id'))) {
				throw new InternalErrorException(__d('exception', 'Failed to save report'));
			}
		}

		$this->set(compact('adId'));
	}

/**
 * admin_settings method
 *
 * @return void
 */
	public function admin_settings() {
		$clickValuesSaveError = false;

		$keys = array(
			'explorerAds',
		);
		$globalKeys = array(
			'explorerAdsActive',
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

			if(isset($this->request->data['ExplorerAdsPackage'])) {
				if($this->ExplorerAdsPackage->saveMany($this->request->data['ExplorerAdsPackage'])) {
					$this->Notice->success(__d('admin', 'Explorer ads packages saved successfully'));
				} else {
					$this->Notice->error(__d('admin', 'Failed to save Explorer ads packages. Please, try again.'));
				}
			}

			if(isset($this->request->data['ClickValue'])) {
				if($this->ExplorerAd->ClickValue->saveMany($this->request->data['ClickValue'])) {
					$this->Notice->success(__d('admin', 'Explorer ads click values saved successfully'));
				} else {
					$this->Notice->error(__d('admin', 'Failed to save Explorer ads click values. Please, try again.'));
					$clickValuesSaveError = true;
				}
			}
		}
		$settings = $this->Settings->fetch($keys);

		foreach($globalKeys as $k) {
			$settings['Settings'][$k] = Configure::read($k);
		}

		$memberships = ClassRegistry::init('Membership')->getList();
		$this->ExplorerAd->ClickValue->recursive = -1;

		if(!$clickValuesSaveError) {
			$clickValuesData = $this->ExplorerAd->ClickValue->find('all');
			$clickValuesData = Hash::extract($clickValuesData, '{n}.ClickValue');
		} else {
			$clickValuesData = $this->request->data['ClickValue'];
		}

		$clickValues = array();

		foreach($clickValuesData as $clickValue) {
			$clickValues[$clickValue['subpages']][$clickValue['membership_id']] = $clickValue;
		}

		for($i = 1; $i <= $settings['Settings']['explorerAds']['maxSubpages']; $i++) {
			foreach($memberships as $membership_id => $v) {
				if(!isset($clickValues[$i][$membership_id])) {
					$clickValues[$i][$membership_id] = array(
						'membership_id' => $membership_id,
						'subpages' => $i,
						'user_click_value' => 0,
						'direct_referral_click_value' => 0,
						'rented_referral_click_value' => 0,
						'user_click_points' => 0,
						'direct_referral_click_points' => 0,
						'rented_referral_click_points' => 0,
					);
				}
			}
		}

		$this->request->data['ClickValue'] = $clickValues;

		$this->ExplorerAdsPackage->recursive = -1;
		$packets = $this->ExplorerAdsPackage->find('all');
		$packets = Hash::extract($packets, '{n}.ExplorerAdsPackage');

		$this->request->data = Hash::merge($settings, $this->request->data);
		if(isset($this->request->data['ExplorerAdsPackage'])) {
			$this->request->data['ExplorerAdsPackage'] = Hash::merge($packets, $this->request->data['ExplorerAdsPackage']);
		} else {
			$this->request->data['ExplorerAdsPackage'] = $packets;
		}

		$this->set('packetsNo', count($packets));
		$this->set('packagesTypes', $this->ExplorerAdsPackage->getTypesList());
		$this->set(compact('memberships'));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$settings = $this->Settings->fetchOne('explorerAds', null);

		if($settings === null) {
			$this->Notice->error(__d('admin', 'Please enter settings before adding new ad.'));
			return $this->redirect(array('action' => 'settings'));
		}

		$memberships = $this->ExplorerAd->TargettedMemberships->find('list');
		$packageTypes = $this->ExplorerAdsPackage->getTypesList();
		$countries = $this->Location->getCountriesList();

		$this->set(compact('countries', 'memberships', 'packageTypes', 'settings'));

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['TargettedMemberships']['TargettedMemberships'])) {
				if(!empty($this->request->data['Advertiser']['username'])) {
					$this->ExplorerAd->Advertiser->contain();
					$advertiser = $this->ExplorerAd->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));

					if(empty($advertiser)) {
						return $this->Notice->error(__d('admin', 'Advertiser not found.'));
					}

					$this->request->data['ExplorerAd']['advertiser_id'] = $advertiser['Advertiser']['id'];
				}

				if($this->request->data['ExplorerAd']['package_type'] == 'Days') {
					$this->request->data['ExplorerAd']['expiry'] = 1;
				} else {
					$this->request->data['ExplorerAd']['expiry_date'] = null;
				}

				$this->ExplorerAd->create();
				if($this->ExplorerAd->save($this->request->data)) {
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
		if(!$this->ExplorerAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$settings = $this->Settings->fetchOne('explorerAds', null);

		if($settings === null) {
			$this->Notice->error(__d('admin', 'Please enter settings before adding new ad.'));
			return $this->redirect(array('action' => 'settings'));
		}

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['TargettedMemberships']['TargettedMemberships'])) {
				if(Module::active('AccurateLocationDatabase') || !empty($this->request->data['ExplorerAd']['TargettedLocations'])) {
					$this->ExplorerAd->Advertiser->contain();
					$advertiser = $this->ExplorerAd->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));
					if(!empty($advertiser) || empty($this->request->data['Advertiser']['username'])) {
						if(empty($this->request->data['Advertiser']['username'])) {
							$this->request->data['ExplorerAd']['advertiser_id'] = null;
						} else {
							$this->request->data['ExplorerAd']['advertiser_id'] = $advertiser['Advertiser']['id'];
						}

						if($this->request->data['ExplorerAd']['package_type'] == 'Days') {
							$this->request->data['ExplorerAd']['expiry'] = 1;
						} else {
							$this->request->data['ExplorerAd']['expiry_date'] = null;
						}

						$this->request->data['ExplorerAd']['id'] = $id;

						if($this->ExplorerAd->save($this->request->data)) {
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

		$this->ExplorerAd->contain(array('TargettedLocations', 'TargettedMemberships', 'Advertiser' => array('id', 'username')));
		$this->request->data = Hash::merge($this->ExplorerAd->findById($id), $this->request->data);

		if(Module::active('AccurateLocationDatabase')) {
			$tald = ClassRegistry::init('AccurateLocationDatabase.AccurateLocationDatabaseLocation');
			foreach($this->request->data['AccurateTargettedLocations'] as &$l) {
				$data = $tald->getListByParentName($l['country']);
				$l['country_regions'] = array_combine($data, $data);
				$data = $tald->getListByParentName($l['region']);
				$l['region_cities'] = array_combine($data, $data);
			}
		}

		$memberships = $this->ExplorerAd->TargettedMemberships->find('list');
		$countries = $this->Location->getCountriesList();
		$packageTypes = $this->ExplorerAd->ExplorerAdsPackage->getTypesList();
		$this->set(compact('countries', 'memberships', 'packageTypes', 'settings'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$conditions = $this->createPaginatorConditions(array(
			'Advertiser.username',
			'ExplorerAd.url',
			'ExplorerAd.title',
		));

		if(isset($conditions['Advertiser.username LIKE']) 
		 && $conditions['Advertiser.username LIKE'] == '%Admin%') {
			unset($conditions['Advertiser.username LIKE']);
			$conditions['ExplorerAd.advertiser_id'] = null;
		}

		$inCollapse = array(
			'ExplorerAd.status',
		);

		$inCollapse = array_intersect_key($conditions, array_flip($inCollapse));
		if(!empty($inCollapse)) {
			$this->set('searchCollapse', 'in');
		} else {
			$this->set('searchCollapse', '');
		}

		$this->ExplorerAd->contain(array('Advertiser'));
		$this->paginate = array(
			'fields' => array(
				'ExplorerAd.id',
				'ExplorerAd.advertiser_name',
				'ExplorerAd.advertiser_id',
				'ExplorerAd.title',
				'ExplorerAd.url',
				'ExplorerAd.package_type',
				'ExplorerAd.expiry',
				'ExplorerAd.clicks',
				'ExplorerAd.outside_clicks',
				'ExplorerAd.status',
				'ExplorerAd.expiry_date',
				'ExplorerAd.hide_referer',
				'ExplorerAd.subpages',
			),
			'order' => 'ExplorerAd.created DESC',
		);
		$ads = $this->Paginator->paginate($conditions);
		$this->set('ads', $ads);
		$this->set('statuses', $this->ExplorerAd->getStatusesList());
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if(!$this->ExplorerAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->ExplorerAd->delete($id)) {
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
		if(!$this->ExplorerAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->ExplorerAd->inactivate($id)) {
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
		if(!$this->ExplorerAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->ExplorerAd->activate($id)) {
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

			if(!isset($this->request->data['ExplorerAds']) || empty($this->request->data['ExplorerAds'])) {
				$this->Notice->error(__d('admin', 'Please select at least one ad.'));
				return $this->redirect($this->referer());
			}

			$ads = 0;
			foreach($this->request->data['ExplorerAds'] as $id => $on) {
				if($on) {
					$ads++;
					if(!$this->ExplorerAd->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid advertisement'));
					}
				}
			}

			foreach($this->request->data['ExplorerAds'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'inactivate':
							$this->ExplorerAd->inactivate($id);
						break;

						case 'activate':
							$this->ExplorerAd->activate($id);
						break;

						case 'delete':
							$this->ExplorerAd->delete($id);
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
		$this->ExplorerAdsPackage->id = $id;

		if(!$id || !$this->ExplorerAdsPackage->exists()) {
			throw new NotFoundException(__d('admin', 'Invalid package'));
		}

		if($this->ExplorerAdsPackage->delete()) {
			$this->Notice->success(__d('admin', 'Package sucessfully removed.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to delete package. Please, try again.'));
		}

		return $this->redirect(array('action' => 'settings', '#' => 'packages'));
	}
}
