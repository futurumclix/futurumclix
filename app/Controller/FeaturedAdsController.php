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
class FeaturedAdsController extends AppController {
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
		'FeaturedAd',
		'FeaturedAdsPackage',
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
		$this->Auth->allow('view');

		if($this->request->prefix != 'admin' && !Configure::read('featuredAdsActive')) {
			throw new NotFoundException(__d('exception', 'Featured Ads are disabled'));
		}

		if($this->request->params['action'] == 'admin_settings') {
			$start = $this->FeaturedAdsPackage->find('count');

			if(isset($this->request->data['FeaturedAdsPackage'])) {
				$stop = count($this->request->data['FeaturedAdsPackage']);
			} else {
				$stop = 150;
			}

			for(;$start < $stop; $start++) {
				$this->Security->unlockedFields[] = 'FeaturedAdsPackage.'.$start.'.type';
				$this->Security->unlockedFields[] = 'FeaturedAdsPackage.'.$start.'.amount';
				$this->Security->unlockedFields[] = 'FeaturedAdsPackage.'.$start.'.price';
			}
		}
	}

/**
 * view method
 *
 * @return void
 */
	public function view($id = null) {
		$this->FeaturedAd->contain();
		$ad = $this->FeaturedAd->find('first', array(
			'fields' => array('id', 'url'),
			'conditions' => array(
				'FeaturedAd.id' => $id,
				'FeaturedAd.status' => 'Active',
				'FeaturedAd.expiry !=' => 0,
				'CASE WHEN FeaturedAd.package_type = "Clicks" THEN FeaturedAd.clicks < FeaturedAd.expiry
				 WHEN FeaturedAd.package_type = "Impressions" THEN FeaturedAd.impressions < FeaturedAd.expiry ELSE
				 (FeaturedAd.start IS NULL OR DATEDIFF(NOW(), FeaturedAd.start) < FeaturedAd.expiry) END',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$allowed = $this->Session->read('FeaturedAds');

		if($allowed && in_array($ad['FeaturedAd']['id'], $allowed)) {
			$this->FeaturedAd->recursive = -1;
			$this->FeaturedAd->updateAll(array(
				'FeaturedAd.clicks' => '`FeaturedAd`.`clicks` + 1',
				'FeaturedAd.total_clicks' => '`FeaturedAd`.`total_clicks` + 1',
			), array(
				'FeaturedAd.id' => $id,
			));

			$this->FeaturedAd->ClickHistory->addClick('FeaturedAd', $id);
		}

		return $this->redirect($ad['FeaturedAd']['url']);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$user_id = $this->Auth->user('id');
		$this->FeaturedAd->contain();
		$ads_no = $this->FeaturedAd->find('count', array(
			'conditions' => array(
				'FeaturedAd.advertiser_id' => $user_id,
				'FeaturedAd.status' => 'Active',
			)
		));

		$this->FeaturedAd->contain();
		$ads = $this->FeaturedAd->find('all', array(
			'conditions' => array(
				'FeaturedAd.advertiser_id' => $user_id,
			),
		));

		$this->FeaturedAd->ClickHistory->recursive = -1;
		$clicksToday = $this->FeaturedAd->ClickHistory->find('all', array(
			'fields' => array(
				'FeaturedAd.id',
				'SUM(ClickHistory.clicks) as sum',
			),
			'joins' => array(
				array(
					'table' => 'featured_ads',
					'alias' => 'FeaturedAd',
					'type' => 'INNER',
					'conditions' => array('ClickHistory.foreign_key = FeaturedAd.id')
				),
			),
			'conditions' => array(
				'FeaturedAd.advertiser_id' => $user_id,
				'ClickHistory.model' => 'FeaturedAd',
				'ClickHistory.created' => date('Y-m-d'),
			),
			'group' => 'FeaturedAd.id',
		));

		$clicksToday = Hash::combine($clicksToday, '{n}.FeaturedAd.id', '{n}.0.sum');

		$this->set(compact('ads_no', 'ads', 'clicksToday'));
		$this->set('breadcrumbTitle', __('Featured ads panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->User->BoughtItems->sumFeaturedAdsPackages());
		$this->set('auto_approve', $this->Settings->fetchOne('featuredAdsAutoApprove'));
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

		$this->FeaturedAd->contain();
		$ad = $this->FeaturedAd->find('first', array(
			'fields' => array('FeaturedAd.id'),
			'conditions' => array(
				'FeaturedAd.id' => $id,
				'FeaturedAd.advertiser_id' => $user_id,
			),
		));

		if(empty($ad)) {
			throw new NotFoundExcption(__('Invalid advertisement'));
		}

		if($this->FeaturedAd->delete($id)) {
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

		$this->FeaturedAd->id = $adId;
		$this->FeaturedAd->contain();
		$ad = $this->FeaturedAd->find('first', array(
			'fields' => array('FeaturedAd.id'),
			'recursive' => -1,
			'conditions' => array(
				'FeaturedAd.id' => $adId,
				'FeaturedAd.advertiser_id' => $this->Auth->user('id'),
				'FeaturedAd.status' => 'Inactive',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->FeaturedAd->activate($adId)) {
			$this->Notice->success(__('Ad sucessfully activated.'));
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

		$this->FeaturedAd->id = $adId;
		$this->FeaturedAd->contain();
		$ad = $this->FeaturedAd->find('first', array(
			'fields' => array('FeaturedAd.id', 'FeaturedAd.package_type'),
			'recursive' => -1,
			'conditions' => array(
				'FeaturedAd.id' => $adId,
				'FeaturedAd.advertiser_id' => $this->Auth->user('id'),
				'FeaturedAd.status' => 'Active',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($ad['FeaturedAd']['package_type'] == 'Days') {
			throw new NotFoundException(__d('exception', 'Inactivating ads with "Days" packages is disabled'));
		}

		if($this->FeaturedAd->inactivate($adId)) {
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
			'featuredAdsAutoApprove',
			'featuredAdsTitleMaxLen',
			'featuredAdsDescMaxLen',
		));
		$user_id = $this->Auth->user('id');
		$this->FeaturedAd->contain();
		$ads_no = $this->FeaturedAd->find('count', array(
			'conditions' => array(
				'FeaturedAd.advertiser_id' => $user_id,
				'FeaturedAd.status' => 'Active',
			)
		));

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['FeaturedAd']['advertiser_id'] = $user_id;

			if($settings['Settings']['featuredAdsAutoApprove']) {
				$this->request->data['FeaturedAd']['status'] = 'Inactive';
			} else {
				$this->request->data['FeaturedAd']['status'] = 'Pending';
			}

			$this->FeaturedAd->create();
			if($this->FeaturedAd->save($this->request->data)) {
				$this->Notice->success(__('Featured ad saved sucessfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to save featured ad. Please, try again.'));
			}
		}

		$this->set(compact('ads_no'));
		$this->set('breadcrumbTitle', __('Featured ads panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->User->BoughtItems->sumFeaturedAdsPackages());
		$this->set('titleMax', $settings['Settings']['featuredAdsTitleMaxLen']);
		$this->set('descMax', $settings['Settings']['featuredAdsDescMaxLen']);
		$this->set('auto_approve', $settings['Settings']['featuredAdsAutoApprove']);
	}

/**
 * edit method
 *
 * @return void
 */
	public function edit($id = null) {
		$settings = $this->Settings->fetch(array(
			'featuredAdsAutoApprove',
			'featuredAdsTitleMaxLen',
			'featuredAdsDescMaxLen',
		));
		$user_id = $this->Auth->user('id');

		$this->FeaturedAd->contain();
		$ad = $this->FeaturedAd->find('first', array(
			'conditions' => array(
				'FeaturedAd.id' => $id,
				'FeaturedAd.advertiser_id' => $user_id,
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid ad'));
		}

		if(!$settings['Settings']['featuredAdsAutoApprove'] && $ad['FeaturedAd']['package_type'] == 'Days') {
			throw new NotFoundException(__d('exception', 'Editing ads with package type "Days" is not allowed'));
		}

		$this->FeaturedAd->contain();
		$ads_no = $this->FeaturedAd->find('count', array(
			'conditions' => array(
				'FeaturedAd.advertiser_id' => $user_id,
				'FeaturedAd.status' => 'Active',
			)
		));

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['FeaturedAd']['advertiser_id'] = $user_id;

			if(!$settings['Settings']['featuredAdsAutoApprove']) {
				$this->request->data['FeaturedAd']['status'] = 'Pending';
			}

			$this->FeaturedAd->id = $id;
			if($this->FeaturedAd->save($this->request->data)) {
				$this->Notice->success(__('Featured ad saved sucessfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to save featured ad. Please, try again.'));
			}
		} else {
			$this->request->data = $ad;
		}

		$this->set(compact('ads_no'));
		$this->set('breadcrumbTitle', __('Featured ads panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->User->BoughtItems->sumFeaturedAdsPackages());
		$this->set('titleMax', $settings['Settings']['featuredAdsTitleMaxLen']);
		$this->set('descMax', $settings['Settings']['featuredAdsDescMaxLen']);
	}

/**
 * statistics method
 *
 * @return void
 */
	public function statistics($adId = null) {
		$user_id = $this->Auth->user('id');
		$this->FeaturedAd->id = $adId;
		$this->FeaturedAd->contain();
		$ad = $this->FeaturedAd->find('first', array(
			'fields' => array('FeaturedAd.id', 'FeaturedAd.title'),
			'conditions' => array(
				'FeaturedAd.id' => $adId,
				'FeaturedAd.advertiser_id' => $user_id,
				'FeaturedAd.status !=' => 'Pending',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$data = $this->FeaturedAd->ClickHistory->find('all', array(
			'fields' => array('SUM(clicks) as sum', 'created'),
			'conditions' => array(
				'model' => 'FeaturedAd',
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));

		$clicks = Hash::combine($data, '{n}.ClickHistory.created', '{n}.0.sum');

		$data = $this->FeaturedAd->ImpressionHistory->find('all', array(
			'fields' => array('SUM(impressions) as sum', 'created'),
			'conditions' => array(
				'model' => 'FeaturedAd',
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));

		$impressions = Hash::combine($data, '{n}.ImpressionHistory.created', '{n}.0.sum');

		$this->FeaturedAd->contain();
		$ads_no = $this->FeaturedAd->find('count', array(
			'conditions' => array(
				'FeaturedAd.advertiser_id' => $user_id,
				'FeaturedAd.status' => 'Active',
			)
		));

		$this->set(compact('clicks', 'impressions', 'ad', 'ads_no'));
		$this->set('packsSum', $this->User->BoughtItems->sumFeaturedAdsPackages());
		$this->set('breadcrumbTitle', __('Featured ads panel'));
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

		$this->FeaturedAd->contain();
		$ads_no = $this->FeaturedAd->find('count', array(
			'conditions' => array(
				'FeaturedAd.advertiser_id' => $user_id,
				'FeaturedAd.status' => 'Active',
			)
		));

		$this->FeaturedAdsPackage->recursive = -1;
		$packagesData = $this->FeaturedAdsPackage->find('all', array(
			'order' => 'FeaturedAdsPackage.price DESC',
		));

		$packages = array();
		foreach($packagesData as &$v) {
			$prices = array();
			$add = false;

			foreach($activeGateways as $k => $g) {
				$price = bcadd($v['FeaturedAdsPackage']['price'], $this->Payments->getDepositFee($v['FeaturedAdsPackage']['price'], $k));
				if($this->Payments->checkMinimumDepositAmount($k, $price)) {
					$prices[$k] = $price;
					$add = true;
				} else {
					$prices[$k] = 'disabled';
				}
			}

			if($add) {
				$packages[$v['FeaturedAdsPackage']['id']] = sprintf('%d %s - %s', $v['FeaturedAdsPackage']['amount'],
				 __($v['FeaturedAdsPackage']['type']), CurrencyFormatter::format($v['FeaturedAdsPackage']['price']));
				$v['FeaturedAdsPackage']['price_per'] = bcdiv($v['FeaturedAdsPackage']['price'], $v['FeaturedAdsPackage']['amount']);
				$v['FeaturedAdsPackage']['prices'] = $prices;
			}
		}
		$packagesData = Hash::combine($packagesData, '{n}.FeaturedAdsPackage.id', '{n}');

		if($this->request->is('post')) {
			$pack_id = $this->request->data['package_id'];

			if(!isset($packagesData[$pack_id])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}

			if($this->Payments->checkMinimumDepositAmount($this->request->data['gateway'], $packagesData[$pack_id]['FeaturedAdsPackage']['price'])) {
				$this->Payments->pay('FeaturedAdsPackage', $this->request->data['gateway'], $packagesData[$pack_id]['FeaturedAdsPackage']['price'],
				 $this->Auth->user('id'), array('package_id' => $pack_id));
			} else {
				$this->Notice->error(__('Minimum deposit amount for %s is %s.', $this->request->data['gateway'], CurrencyFormatter::format($this->Payments->getMinimumDepositAmount($this->request->data['gateway']))));
			}
		}

		$this->set(compact('ads_no', 'categories', 'packages', 'activeGateways', 'packagesData'));
		$this->set('breadcrumbTitle', __('Featured ads panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumFeaturedAdsPackages());
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
			'fields' => array('BoughtItem.id', 'FeaturedAdsPackage.id', 'FeaturedAdsPackage.type', 'FeaturedAdsPackage.amount'),
			'recursive' => -1,
			'conditions' => array(
				'BoughtItem.user_id' => $user_id,
				'BoughtItem.model' => 'FeaturedAdsPackage',
			),
			'joins' => array(
				array(
					'table' => 'featured_ads_packages',
					'alias' => 'FeaturedAdsPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = FeaturedAdsPackage.id',
					),
				),
			),
		));

		$ads = $this->FeaturedAd->find('list', array(
			'conditions' => array(
				'FeaturedAd.advertiser_id' => $user_id,
				'FeaturedAd.status !=' => 'Pending',
				'OR' => array(
					'FeaturedAd.expiry' => 0,
					'CASE WHEN FeaturedAd.package_type = "Clicks" THEN FeaturedAd.clicks >= FeaturedAd.expiry
					 WHEN FeaturedAd.package_type = "Impressions" THEN FeaturedAd.impressions >= FeaturedAd.expiry ELSE
					 (FeaturedAd.start IS NULL OR DATEDIFF(NOW(), FeaturedAd.start) >= FeaturedAd.expiry) END',
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
			if($this->FeaturedAd->assignPack($packetsData[$this->request->data['package_id']], $this->request->data['ad_id'])) {
				$this->Notice->success(__('Package successfully assigned.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		$this->FeaturedAd->contain();
		$ads_no = $this->FeaturedAd->find('count', array(
			'conditions' => array(
				'FeaturedAd.advertiser_id' => $user_id,
				'FeaturedAd.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('%d %s', $v['FeaturedAdsPackage']['amount'], $v['FeaturedAdsPackage']['type']);
		}

		$this->set(compact('ads_no', 'packages', 'ads'));
		$this->set('breadcrumbTitle', __('Featured ads panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumFeaturedAdsPackages());
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * assignTo method
 *
 * @return void
 */
	public function assignTo($id = null) {
		$user_id = $this->Auth->user('id');

		$ad = $this->FeaturedAd->find('first', array(
			'conditions' => array(
				'FeaturedAd.id' => $id,
				'FeaturedAd.advertiser_id' => $user_id,
				'FeaturedAd.status !=' => 'Pending',
				'FeaturedAd.package_type !=' => '',
			),
		));

		if(empty($ad)) {
			return $this->redirect(array('action' => 'assign'));
		}

		$packetsData = ClassRegistry::init('BoughtItem')->find('all', array(
			'fields' => array('BoughtItem.id', 'FeaturedAdsPackage.id', 'FeaturedAdsPackage.type', 'FeaturedAdsPackage.amount'),
			'recursive' => -1,
			'conditions' => array(
				'BoughtItem.user_id' => $user_id,
				'BoughtItem.model' => 'FeaturedAdsPackage',
				'FeaturedAdsPackage.type' => $ad['FeaturedAd']['package_type'],
			),
			'joins' => array(
				array(
					'table' => 'featured_ads_packages',
					'alias' => 'FeaturedAdsPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = FeaturedAdsPackage.id',
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
			if($this->FeaturedAd->addPack($packetsData[$this->request->data['package_id']], $id)) {
				$this->Notice->success(__('Package successfully assigned.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		$this->FeaturedAd->contain();
		$ads_no = $this->FeaturedAd->find('count', array(
			'conditions' => array(
				'FeaturedAd.advertiser_id' => $user_id,
				'FeaturedAd.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('%d %s', $v['FeaturedAdsPackage']['amount'], $v['FeaturedAdsPackage']['type']);
		}

		$this->set(compact('ads_no', 'packages', 'ad'));
		$this->set('breadcrumbTitle', __('Featured ads panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumFeaturedAdsPackages());
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
			'FeaturedAd.title',
			'FeaturedAd.url',
			'FeaturedAd.status',
		));

		if(isset($conditions['Advertiser.username LIKE']) 
		 && $conditions['Advertiser.username LIKE'] == '%Admin%') {
			unset($conditions['Advertiser.username LIKE']);
			$conditions['FeaturedAd.advertiser_id'] = null;
		}

		$this->FeaturedAd->contain(array('Advertiser'));
		$this->paginate = array(
			'fields' => array(
				'FeaturedAd.id',
				'FeaturedAd.advertiser_id',
				'FeaturedAd.advertiser_name',
				'FeaturedAd.title',
				'FeaturedAd.url',
				'FeaturedAd.package_type',
				'FeaturedAd.start',
				'FeaturedAd.impressions',
				'FeaturedAd.clicks',
				'FeaturedAd.total_clicks',
				'FeaturedAd.expiry',
				'FeaturedAd.status',
			),
			'order' => 'FeaturedAd.created DESC'
		);
		$ads = $this->Paginator->paginate($conditions);

		$statuses = $this->FeaturedAd->getStatusesList();

		$this->set(compact('statuses', 'ads'));
	}

/**
 * admin_settings method
 *
 * @return void
 */
	public function admin_settings() {
		$keys = array(
			'featuredAdsAutoApprove',
			'featuredAdsTitleMaxLen',
			'featuredAdsDescMaxLen',
		);
		$globalKeys = array(
			'featuredAdsPerBox',
			'featuredAdsActive',
		);

		if($this->request->is(array('post', 'put'))) {
			if(isset($this->request->data['Settings'])) {
				if($this->Settings->store($this->request->data, $globalKeys, true) 
				 && $this->Settings->store($this->request->data, $keys)) {
					$this->Notice->success(__d('admin', 'Settings saved successfully.'));
				} else {
					$this->Notice->error(__d('admin', 'The settings could not be saved. Please, try again.'));
				}
			}

			if(isset($this->request->data['FeaturedAdsPackage'])) {
				if($this->FeaturedAdsPackage->saveMany($this->request->data['FeaturedAdsPackage'])) {
					$this->Notice->success(__d('admin', 'Featured ads packages saved successfully.'));
				} else {
					$this->Notice->error(__d('admin', 'Failed to save featured ads packages. Please, try again.'));
				}
			}
		}
		$settings = $this->Settings->fetch($keys);

		foreach($globalKeys as $k) {
			$settings['Settings'][$k] = Configure::read($k);
		}

		$this->FeaturedAdsPackage->recursive = -1;
		$packets = $this->FeaturedAdsPackage->find('all');

		$packets = Hash::extract($packets, '{n}.FeaturedAdsPackage');

		$this->request->data = Hash::merge($settings, $this->request->data);
		if(isset($this->request->data['FeaturedAdsPackage'])) {
			$this->request->data['FeaturedAdsPackage'] = Hash::merge($packets, $this->request->data['FeaturedAdsPackage']);
		} else {
			$this->request->data['FeaturedAdsPackage'] = $packets;
		}

		$this->set('packetsNo', count($packets));
		$this->set('packagesTypes', $this->FeaturedAdsPackage->getTypesList());
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$settings = $this->Settings->fetch(array(
			'featuredAdsAutoApprove',
			'featuredAdsTitleMaxLen',
			'featuredAdsDescMaxLen',
		));
		$this->set('titleMax', $settings['Settings']['featuredAdsTitleMaxLen']);
		$this->set('descMax', $settings['Settings']['featuredAdsDescMaxLen']);
		$this->set('packageTypes', $this->FeaturedAdsPackage->getTypesList());

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['Advertiser']['username'])) {
				$this->FeaturedAd->Advertiser->contain();
				$advertiser = $this->FeaturedAd->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));

				if(empty($advertiser)) {
					return $this->Notice->error(__d('admin', 'Advertiser not found.'));
				}

				$this->request->data['FeaturedAd']['advertiser_id'] = $advertiser['Advertiser']['id'];

				unset($this->request->data['Advertiser']);
			} else {
				$this->request->data['FeaturedAd']['advertiser_id'] = null;
			}

			$this->request->data['FeaturedAd']['status'] = 'Active';

			$this->FeaturedAd->create();
			if($this->FeaturedAd->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'Featured ad saved successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to save featured ad. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @return void
 */
	public function admin_edit($id = null) {
		$this->FeaturedAd->contain(array(
			'Advertiser' => array(
				'id', 
				'username',
			),
		));
		$ad = $this->FeaturedAd->findById($id);

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$settings = $this->Settings->fetch(array(
			'featuredAdsAutoApprove',
			'featuredAdsTitleMaxLen',
			'featuredAdsDescMaxLen',
		));
		$this->set('titleMax', $settings['Settings']['featuredAdsTitleMaxLen']);
		$this->set('descMax', $settings['Settings']['featuredAdsDescMaxLen']);
		$this->set('packageTypes', $this->FeaturedAdsPackage->getTypesList());

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['Advertiser']['username'])) {
				$this->FeaturedAd->Advertiser->contain();
				$advertiser = $this->FeaturedAd->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));

				if(empty($advertiser)) {
					return $this->Notice->error(__d('admin', 'Advertiser not found.'));
				}

				$this->request->data['FeaturedAd']['advertiser_id'] = $advertiser['Advertiser']['id'];

				unset($this->request->data['Advertiser']);
			} else {
				$this->request->data['FeaturedAd']['advertiser_id'] = null;
			}

			$this->FeaturedAd->id = $id;
			if($this->FeaturedAd->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'Featured ad saved sucessfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to save featured ad. Please, try again.'));
			}
		} else {
			$this->request->data = $ad;
		}
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if(!$this->FeaturedAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->FeaturedAd->delete($id)) {
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
		if(!$this->FeaturedAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->FeaturedAd->inactivate($id)) {
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
		if(!$this->FeaturedAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->FeaturedAd->activate($id)) {
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

			if(!isset($this->request->data['FeaturedAd']) || empty($this->request->data['FeaturedAd'])) {
				$this->Notice->error(__d('admin', 'Please select at least one ad.'));
				return $this->redirect($this->referer());
			}

			$ads = 0;
			foreach($this->request->data['FeaturedAd'] as $id => $on) {
				if($on) {
					$ads++;
					if(!$this->FeaturedAd->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid advertisement'));
					}
				}
			}

			foreach($this->request->data['FeaturedAd'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'inactivate':
							$this->FeaturedAd->inactivate($id);
						break;

						case 'activate':
							$this->FeaturedAd->activate($id);
						break;

						case 'delete':
							$this->FeaturedAd->delete($id);
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
		$this->FeaturedAdsPackage->id = $id;

		if(!$id || !$this->FeaturedAdsPackage->exists()) {
			throw new NotFoundException(__d('admin', 'Invalid package'));
		}

		if($this->FeaturedAdsPackage->delete()) {
			$this->Notice->success(__d('admin', 'Package sucessfully removed.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to delete package. Please, try again.'));
		}

		return $this->redirect(array('action' => 'settings', '#' => 'packages'));
	}
}
