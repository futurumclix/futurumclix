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
class BannerAdsController extends AppController {
/**
 * components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		'UserPanel',
		'Payments',
	);

/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'BannerAd',
		'BannerAdsPackage',
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

		if($this->request->prefix != 'admin' && !Configure::read('bannerAdsActive')) {
			throw new NotFoundException(__d('exception', 'Banner Ads are disabled'));
		}

		$this->Auth->allow('view');
		if($this->request->params['action'] == 'admin_settings') {
			$start = $this->BannerAdsPackage->find('count');

			if(isset($this->request->data['BannerAdsPackage'])) {
				$stop = count($this->request->data['BannerAdsPackage']);
			} else {
				$stop = 150;
			}

			for(;$start < $stop; $start++) {
				$this->Security->unlockedFields[] = 'BannerAdsPackage.'.$start.'.type';
				$this->Security->unlockedFields[] = 'BannerAdsPackage.'.$start.'.amount';
				$this->Security->unlockedFields[] = 'BannerAdsPackage.'.$start.'.price';
			}
		}
	}

/**
 * view method
 *
 * @return void
 */
	public function view($id = null) {
		$this->BannerAd->contain();
		$ad = $this->BannerAd->find('first', array(
			'conditions' => array(
				'BannerAd.id' => $id,
				'BannerAd.status' => 'Active',
				'BannerAd.expiry !=' => 0,
				'CASE WHEN BannerAd.package_type = "Clicks" THEN BannerAd.clicks < BannerAd.expiry
				 WHEN BannerAd.package_type = "Impressions" THEN BannerAd.impressions < BannerAd.expiry ELSE
				 (BannerAd.start IS NULL OR DATEDIFF(NOW(), BannerAd.start) < BannerAd.expiry) END',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$allowed = $this->Session->read('BannerAds');

		if($allowed && in_array($ad['BannerAd']['id'], $allowed)) {
			$this->BannerAd->recursive = -1;
			$this->BannerAd->updateAll(array(
				'BannerAd.clicks' => '`BannerAd`.`clicks` + 1',
				'BannerAd.total_clicks' => '`BannerAd`.`total_clicks` + 1',
			), array(
				'BannerAd.id' => $id,
			));

			$this->BannerAd->ClickHistory->addClick('BannerAd', $id);
		}

		return $this->redirect($ad['BannerAd']['url']);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$user_id = $this->Auth->user('id');
		$this->BannerAd->contain();
		$ads_no = $this->BannerAd->find('count', array(
			'conditions' => array(
				'BannerAd.advertiser_id' => $user_id,
				'BannerAd.status' => 'Active',
			)
		));

		$this->BannerAd->contain();
		$ads = $this->BannerAd->find('all', array(
			'conditions' => array(
				'BannerAd.advertiser_id' => $user_id,
			),
		));

		$this->BannerAd->ClickHistory->recursive = -1;
		$clicksToday = $this->BannerAd->ClickHistory->find('all', array(
			'fields' => array(
				'BannerAd.id',
				'SUM(ClickHistory.clicks) as sum',
			),
			'joins' => array(
				array(
					'table' => 'banner_ads',
					'alias' => 'BannerAd',
					'type' => 'INNER',
					'conditions' => array('ClickHistory.foreign_key = BannerAd.id')
				),
			),
			'conditions' => array(
				'BannerAd.advertiser_id' => $user_id,
				'ClickHistory.model' => 'BannerAd',
				'ClickHistory.created' => date('Y-m-d'),
			),
			'group' => 'BannerAd.id',
		));

		$clicksToday = Hash::combine($clicksToday, '{n}.BannerAd.id', '{n}.0.sum');

		$this->set(compact('ads_no', 'ads', 'clicksToday'));
		$this->set('breadcrumbTitle', __('Banner ads panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->User->BoughtItems->sumBannerAdsPackages());
		$this->set('bannerSize', $this->Settings->fetchOne('bannerAdsSize'));
		$this->set('auto_approve', $this->Settings->fetchOne('bannerAdsAutoApprove', 0));
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

		$this->BannerAd->contain();
		$ad = $this->BannerAd->find('first', array(
			'fields' => array('BannerAd.id'),
			'conditions' => array(
				'BannerAd.id' => $id,
				'BannerAd.advertiser_id' => $user_id,
			),
		));

		if(empty($ad)) {
			throw new NotFoundExcption(__('Invalid advertisement'));
		}

		if($this->BannerAd->delete($id)) {
			$this->Notice->success(__('Advertisement deleted.'));
		} else {
			$this->Notice->error(__('Failed to delete advertisement. Please try again.'));
		}

		return $this->redirect(array('action' => 'index'));
	}

/**
 * activate method
 *
 * @return void
 */
	public function activate($adId = null) {
		$this->request->allowMethod('post', 'delete');

		$this->BannerAd->id = $adId;
		$this->BannerAd->contain();
		$ad = $this->BannerAd->find('first', array(
			'fields' => array('BannerAd.id'),
			'recursive' => -1,
			'conditions' => array(
				'BannerAd.id' => $adId,
				'BannerAd.advertiser_id' => $this->Auth->user('id'),
				'BannerAd.status' => 'Inactive',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->BannerAd->activate($adId)) {
			$this->Notice->success(__('Ad successfully activated.'));
		} else {
			$this->Notice->error(__('Error while activating ad. Please try again.'));
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

		$this->BannerAd->id = $adId;
		$this->BannerAd->contain();
		$ad = $this->BannerAd->find('first', array(
			'fields' => array('BannerAd.id', 'BannerAd.package_type'),
			'recursive' => -1,
			'conditions' => array(
				'BannerAd.id' => $adId,
				'BannerAd.advertiser_id' => $this->Auth->user('id'),
				'BannerAd.status' => 'Active',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($ad['BannerAd']['package_type'] == 'Days') {
			throw new NotFoundException(__d('exception', 'Inactivating ads with "Days" packages is disabled'));
		}

		if($this->BannerAd->inactivate($adId)) {
			$this->Notice->success(__('Ad successfully paused.'));
		} else {
			$this->Notice->error(__('Error while pausing ad. Please try again.'));
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$settings = $this->Settings->fetch(array(
			'bannerAdsAutoApprove',
			'bannerAdsTitleMaxLen',
		));
		$user_id = $this->Auth->user('id');
		$this->BannerAd->contain();
		$ads_no = $this->BannerAd->find('count', array(
			'conditions' => array(
				'BannerAd.advertiser_id' => $user_id,
				'BannerAd.status' => 'Active',
			)
		));

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['BannerAd']['advertiser_id'] = $user_id;

			if($settings['Settings']['bannerAdsAutoApprove']) {
				$this->request->data['BannerAd']['status'] = 'Inactive';
			} else {
				$this->request->data['BannerAd']['status'] = 'Pending';
			}

			$this->BannerAd->create();
			if($this->BannerAd->save($this->request->data)) {
				$this->Notice->success(__('Banner ad saved successfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to save banner ad. Please, try again.'));
			}
		}

		$this->set(compact('ads_no'));
		$this->set('breadcrumbTitle', __('Banner ads panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->User->BoughtItems->sumBannerAdsPackages());
		$this->set('titleMax', $settings['Settings']['bannerAdsTitleMaxLen']);
	}

/**
 * edit method
 *
 * @return void
 */
	public function edit($id = null) {
		$settings = $this->Settings->fetch(array(
			'bannerAdsAutoApprove',
			'bannerAdsTitleMaxLen',
		));
		$user_id = $this->Auth->user('id');

		$this->BannerAd->contain();
		$ad = $this->BannerAd->find('first', array(
			'conditions' => array(
				'BannerAd.id' => $id,
				'BannerAd.advertiser_id' => $user_id,
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid ad'));
		}

		if(!$settings['Settings']['bannerAdsAutoApprove'] && $ad['BannerAd']['package_type'] == 'Days') {
			throw new NotFoundException(__d('exception', 'Editing ads with package type "Days" is not allowed'));
		}

		$this->BannerAd->contain();
		$ads_no = $this->BannerAd->find('count', array(
			'conditions' => array(
				'BannerAd.advertiser_id' => $user_id,
				'BannerAd.status' => 'Active',
			)
		));

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['BannerAd']['advertiser_id'] = $user_id;

			if(!$settings['Settings']['bannerAdsAutoApprove']) {
				$this->request->data['BannerAd']['status'] = 'Pending';
			}

			$this->BannerAd->id = $id;
			if($this->BannerAd->save($this->request->data)) {
				$this->Notice->success(__('Banner ad saved successfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to save banner ad. Please, try again.'));
			}
		} else {
			$this->request->data = $ad;
		}

		$this->set(compact('ads_no'));
		$this->set('breadcrumbTitle', __('Banner ads panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->User->BoughtItems->sumBannerAdsPackages());
		$this->set('titleMax', $settings['Settings']['bannerAdsTitleMaxLen']);
	}

/**
 * statistics method
 *
 * @return void
 */
	public function statistics($adId = null) {
		$user_id = $this->Auth->user('id');
		$this->BannerAd->id = $adId;
		$this->BannerAd->contain();
		$ad = $this->BannerAd->find('first', array(
			'fields' => array('BannerAd.id', 'BannerAd.title'),
			'conditions' => array(
				'BannerAd.id' => $adId,
				'BannerAd.advertiser_id' => $user_id,
				'BannerAd.status !=' => 'Pending',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$data = $this->BannerAd->ClickHistory->find('all', array(
			'fields' => array('SUM(clicks) as sum', 'created'),
			'conditions' => array(
				'model' => 'BannerAd',
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));

		$clicks = Hash::combine($data, '{n}.ClickHistory.created', '{n}.0.sum');

		$data = $this->BannerAd->ImpressionHistory->find('all', array(
			'fields' => array('SUM(impressions) as sum', 'created'),
			'conditions' => array(
				'model' => 'BannerAd',
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));

		$impressions = Hash::combine($data, '{n}.ImpressionHistory.created', '{n}.0.sum');

		$this->BannerAd->contain();
		$ads_no = $this->BannerAd->find('count', array(
			'conditions' => array(
				'BannerAd.advertiser_id' => $user_id,
				'BannerAd.status' => 'Active',
			)
		));

		$this->set(compact('clicks', 'impressions', 'ad', 'ads_no'));
		$this->set('packsSum', $this->User->BoughtItems->sumBannerAdsPackages());
		$this->set('breadcrumbTitle', __('Banner ads panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * buy method
 *
 * @return void
 */
	public function buy() {
		$user_id = $this->Auth->user('id');

		$activeGateways = $this->Payments->getActiveDepositsHumanized();

		$this->BannerAd->contain();
		$ads_no = $this->BannerAd->find('count', array(
			'conditions' => array(
				'BannerAd.advertiser_id' => $user_id,
				'BannerAd.status' => 'Active',
			)
		));

		$this->BannerAdsPackage->recursive = -1;
		$packagesData = $this->BannerAdsPackage->find('all', array(
			'order' => 'BannerAdsPackage.price DESC',
		));

		$packages = array();
		foreach($packagesData as &$v) {
			$prices = array();
			$add = false;

			foreach($activeGateways as $k => $g) {
				$price = bcadd($v['BannerAdsPackage']['price'], $this->Payments->getDepositFee($v['BannerAdsPackage']['price'], $k));
				if($this->Payments->checkMinimumDepositAmount($k, $price)) {
					$prices[$k] = $price;
					$add = true;
				} else {
					$prices[$k] = 'disabled';
				}
			}

			if($add) {
				$packages[$v['BannerAdsPackage']['id']] = sprintf('%d %s - %s', $v['BannerAdsPackage']['amount'],
				 __($v['BannerAdsPackage']['type']), CurrencyFormatter::format($v['BannerAdsPackage']['price']));
				$v['BannerAdsPackage']['price_per'] = bcdiv($v['BannerAdsPackage']['price'], $v['BannerAdsPackage']['amount']);
				$v['BannerAdsPackage']['prices'] = $prices;
			}
		}
		$packagesData = Hash::combine($packagesData, '{n}.BannerAdsPackage.id', '{n}');

		if($this->request->is('post')) {
			$pack_id = $this->request->data['package_id'];

			if(!isset($packagesData[$pack_id])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}

			if($this->Payments->checkMinimumDepositAmount($this->request->data['gateway'], $packagesData[$pack_id]['BannerAdsPackage']['price'])) {
				$this->Payments->pay('BannerAdsPackage', $this->request->data['gateway'], $packagesData[$pack_id]['BannerAdsPackage']['price'],
				 $this->Auth->user('id'), array('package_id' => $pack_id));
			} else {
				$this->Notice->error(__('Minimum deposit amount for %s is %s.', $this->request->data['gateway'], CurrencyFormatter::format($this->Payments->getMinimumDepositAmount($this->request->data['gateway']))));
			}
		}

		$this->set(compact('ads_no', 'categories', 'packages', 'activeGateways', 'packagesData'));
		$this->set('breadcrumbTitle', __('Banner ads panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumBannerAdsPackages());
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * assign method
 *
 * @return void
 */
	public function assign() {
		$user_id = $this->Auth->user('id');

		$packetsData = ClassRegistry::init('BoughtItem')->find('all', array(
			'fields' => array('BoughtItem.id', 'BannerAdsPackage.id', 'BannerAdsPackage.type', 'BannerAdsPackage.amount'),
			'recursive' => -1,
			'conditions' => array(
				'BoughtItem.user_id' => $user_id,
				'BoughtItem.model' => 'BannerAdsPackage',
			),
			'joins' => array(
				array(
					'table' => 'banner_ads_packages',
					'alias' => 'BannerAdsPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = BannerAdsPackage.id',
					),
				),
			),
		));

		$ads = $this->BannerAd->find('list', array(
			'conditions' => array(
				'BannerAd.advertiser_id' => $user_id,
				'BannerAd.status !=' => 'Pending',
				'OR' => array(
					'BannerAd.expiry' => 0,
					'CASE WHEN BannerAd.package_type = "Clicks" THEN BannerAd.clicks >= BannerAd.expiry
					 WHEN BannerAd.package_type = "Impressions" THEN BannerAd.impressions >= BannerAd.expiry ELSE
					 (BannerAd.start IS NULL OR DATEDIFF(NOW(), BannerAd.start) >= BannerAd.expiry) END',
				),
			),
		));

		if($this->request->is(array('post', 'put'))) {
			if(!isset($ads[$this->request->data['ad_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}
			$packetsData = Hash::combine($packetsData, '{n}.BoughtItem.id', '{n}');
			if(!isset($packetsData[$this->request->data['package_id']]) || empty($packetsData[$this->request->data['package_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}
			if($this->BannerAd->assignPack($packetsData[$this->request->data['package_id']], $this->request->data['ad_id'])) {
				$this->Notice->success(__('Package assigned successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		$this->BannerAd->contain();
		$ads_no = $this->BannerAd->find('count', array(
			'conditions' => array(
				'BannerAd.advertiser_id' => $user_id,
				'BannerAd.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('%d %s', $v['BannerAdsPackage']['amount'], $v['BannerAdsPackage']['type']);
		}

		$this->set(compact('ads_no', 'packages', 'ads'));
		$this->set('breadcrumbTitle', __('Banner ads panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumBannerAdsPackages());
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * assignTo method
 *
 * @return void
 */
	public function assignTo($id = null) {
		$user_id = $this->Auth->user('id');

		$ad = $this->BannerAd->find('first', array(
			'conditions' => array(
				'BannerAd.id' => $id,
				'BannerAd.advertiser_id' => $user_id,
				'BannerAd.status !=' => 'Pending',
				'BannerAd.package_type !=' => '',
			),
		));

		if(empty($ad)) {
			return $this->redirect(array('action' => 'assign'));
		}

		$packetsData = ClassRegistry::init('BoughtItem')->find('all', array(
			'fields' => array('BoughtItem.id', 'BannerAdsPackage.id', 'BannerAdsPackage.type', 'BannerAdsPackage.amount'),
			'recursive' => -1,
			'conditions' => array(
				'BoughtItem.user_id' => $user_id,
				'BoughtItem.model' => 'BannerAdsPackage',
				'BannerAdsPackage.type' => $ad['BannerAd']['package_type'],
			),
			'joins' => array(
				array(
					'table' => 'banner_ads_packages',
					'alias' => 'BannerAdsPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = BannerAdsPackage.id',
					),
				),
			),
		));

		if($this->request->is(array('post', 'put'))) {
			$packetsData = Hash::combine($packetsData, '{n}.BoughtItem.id', '{n}');
			if(!isset($packetsData[$this->request->data['package_id']]) || empty($packetsData[$this->request->data['package_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}
			if($this->BannerAd->addPack($packetsData[$this->request->data['package_id']], $id)) {
				$this->Notice->success(__('Package assigned successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		$this->BannerAd->contain();
		$ads_no = $this->BannerAd->find('count', array(
			'conditions' => array(
				'BannerAd.advertiser_id' => $user_id,
				'BannerAd.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('%d %s', $v['BannerAdsPackage']['amount'], $v['BannerAdsPackage']['type']);
		}

		$this->set(compact('ads_no', 'packages', 'ad'));
		$this->set('breadcrumbTitle', __('Banner ads panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumBannerAdsPackages());
		$this->set('user', $this->UserPanel->getData());
	}


/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$conditions = $this->createPaginatorConditions(array(
			'Advertiser.username',
			'BannerAd.title',
			'BannerAd.url',
			'BannerAd.status',
		));

		if(isset($conditions['Advertiser.username LIKE']) 
		 && $conditions['Advertiser.username LIKE'] == '%Admin%') {
			unset($conditions['Advertiser.username LIKE']);
			$conditions['BannerAd.advertiser_id'] = null;
		}

		$this->paginate = array(
			'fields' => array(
				'BannerAd.id',
				'BannerAd.advertiser_id',
				'BannerAd.advertiser_name',
				'BannerAd.title',
				'BannerAd.url',
				'BannerAd.image_url',
				'BannerAd.package_type',
				'BannerAd.start',
				'BannerAd.impressions',
				'BannerAd.clicks',
				'BannerAd.total_clicks',
				'BannerAd.expiry',
				'BannerAd.status',
			),
			'order' => 'BannerAd.created DESC'
		);
		$this->BannerAd->contain(array('Advertiser'));
		$ads = $this->Paginator->paginate($conditions);

		$statuses = $this->BannerAd->getStatusesList();

		$this->set(compact('statuses', 'ads'));
		$this->set('bannerSize', $this->Settings->fetchOne('bannerAdsSize'));
	}

/**
 * admin_settings method
 *
 * @return void
 */
	public function admin_settings() {
		$keys = array(
			'bannerAdsAutoApprove',
			'bannerAdsTitleMaxLen',
			'bannerAdsSize',
		);
		$globalKeys = array(
			'bannerAdsActive',
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

			if(isset($this->request->data['BannerAdsPackage'])) {
				if($this->BannerAdsPackage->saveMany($this->request->data['BannerAdsPackage'])) {
					$this->Notice->success(__d('admin', 'Banner ads packages saved successfully'));
				} else {
					$this->Notice->error(__d('admin', 'Failed to save banner ads packages. Please, try again.'));
				}
			}
		}
		$settings = $this->Settings->fetch($keys);

		foreach($globalKeys as $k) {
			$settings['Settings'][$k] = Configure::read($k);
		}

		$this->BannerAdsPackage->recursive = -1;
		$packets = $this->BannerAdsPackage->find('all');

		$packets = Hash::extract($packets, '{n}.BannerAdsPackage');

		$this->request->data = Hash::merge($settings, $this->request->data);
		if(isset($this->request->data['BannerAdsPackage'])) {
			$this->request->data['BannerAdsPackage'] = Hash::merge($packets, $this->request->data['BannerAdsPackage']);
		} else {
			$this->request->data['BannerAdsPackage'] = $packets;
		}

		$this->set('packetsNo', count($packets));
		$this->set('packagesTypes', $this->BannerAdsPackage->getTypesList());
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$settings = $this->Settings->fetch(array(
			'bannerAdsAutoApprove',
			'bannerAdsTitleMaxLen',
		));
		$this->set('titleMax', $settings['Settings']['bannerAdsTitleMaxLen']);
		$this->set('packageTypes', $this->BannerAdsPackage->getTypesList());

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['Advertiser']['username'])) {
				$this->BannerAd->Advertiser->contain();
				$advertiser = $this->BannerAd->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));

				if(empty($advertiser)) {
					return $this->Notice->error(__d('admin', 'Advertiser not found.'));
				}

				$this->request->data['BannerAd']['advertiser_id'] = $advertiser['Advertiser']['id'];

				unset($this->request->data['Advertiser']);
			} else {
				$this->request->data['BannerAd']['advertiser_id'] = null;
			}

			$this->request->data['BannerAd']['status'] = 'Active';

			$this->BannerAd->create();
			if($this->BannerAd->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'Banner ad saved sucessfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to save banner ad. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @return void
 */
	public function admin_edit($id = null) {
		$this->BannerAd->contain('Advertiser');
		$ad = $this->BannerAd->findById($id);

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['Advertiser']['username'])) {
				$this->BannerAd->Advertiser->contain();
				$advertiser = $this->BannerAd->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));

				if(empty($advertiser)) {
					$this->Notice->error(__d('admin', 'Advertiser not found.'));
				}

				$this->request->data['BannerAd']['advertiser_id'] = $advertiser['Advertiser']['id'];

				unset($this->request->data['Advertiser']);
			} else {
				$this->request->data['BannerAd']['advertiser_id'] = null;
			}

			$this->BannerAd->id = $id;
			if($this->BannerAd->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'Banner ad saved sucessfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to save banner ad. Please, try again.'));
			}
		} else {
			$this->request->data = $ad;
		}

		$this->set('titleMax', $this->Settings->fetchOne('bannerAdsTitleMaxLen'));
		$this->set('packageTypes', $this->BannerAdsPackage->getTypesList());
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if(!$this->BannerAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->BannerAd->delete($id)) {
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
		if(!$this->BannerAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->BannerAd->inactivate($id)) {
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
		if(!$this->BannerAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->BannerAd->activate($id)) {
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

			if(!isset($this->request->data['BannerAd']) || empty($this->request->data['BannerAd'])) {
				$this->Notice->error(__d('admin', 'Please select at least one ad.'));
				return $this->redirect($this->referer());
			}

			$ads = 0;
			foreach($this->request->data['BannerAd'] as $id => $on) {
				if($on) {
					$ads++;
					if(!$this->BannerAd->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid advertisement'));
					}
				}
			}

			foreach($this->request->data['BannerAd'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'inactivate':
							$this->BannerAd->inactivate($id);
						break;

						case 'activate':
							$this->BannerAd->activate($id);
						break;

						case 'delete':
							$this->BannerAd->delete($id);
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
		$this->BannerAdsPackage->id = $id;

		if(!$id || !$this->BannerAdsPackage->exists()) {
			throw new NotFoundException(__d('admin', 'Invalid package'));
		}

		if($this->BannerAdsPackage->delete()) {
			$this->Notice->success(__d('admin', 'Banner Ads package sucessfully removed.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to delete Banner Ads package. Please, try again.'));
		}

		return $this->redirect(array('action' => 'settings', '#' => 'packages'));
	}
}
