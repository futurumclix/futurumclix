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
class LoginAdsController extends AppController {
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
		'LoginAd',
		'LoginAdsPackage',
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

		if($this->request->prefix != 'admin' && !Configure::read('loginAdsActive')) {
			throw new NotFoundException(__d('exception', 'Login Ads are disabled'));
		}

		if($this->request->params['action'] == 'admin_settings') {
			$start = $this->LoginAdsPackage->find('count');

			if(isset($this->request->data['LoginAdsPackage'])) {
				$stop = count($this->request->data['LoginAdsPackage']);
			} else {
				$stop = 150;
			}

			for(;$start < $stop; $start++) {
				$this->Security->unlockedFields[] = 'LoginAdsPackage.'.$start.'.type';
				$this->Security->unlockedFields[] = 'LoginAdsPackage.'.$start.'.amount';
				$this->Security->unlockedFields[] = 'LoginAdsPackage.'.$start.'.price';
			}
		}
	}

/**
 * view method
 *
 * @return void
 */
	public function view($id = null) {
		$this->LoginAd->contain();
		$ad = $this->LoginAd->find('first', array(
			'conditions' => array(
				'LoginAd.id' => $id,
				'LoginAd.status' => 'Active',
				'LoginAd.expiry !=' => 0,
				'CASE WHEN LoginAd.package_type = "Clicks" THEN LoginAd.clicks < LoginAd.expiry
				 ELSE (LoginAd.start IS NULL OR DATEDIFF(NOW(), LoginAd.start) < LoginAd.expiry) END',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$allowed = $this->Session->read('LoginAds');

		if($allowed && in_array($ad['LoginAd']['id'], $allowed)) {
			$this->LoginAd->recursive = -1;
			$this->LoginAd->updateAll(array(
				'LoginAd.clicks' => '`LoginAd`.`clicks` + 1',
				'LoginAd.total_clicks' => '`LoginAd`.`total_clicks` + 1',
			), array(
				'LoginAd.id' => $id,
			));

			$this->LoginAd->ClickHistory->addClick('LoginAd', $id);
		}

		return $this->redirect($ad['LoginAd']['url']);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$user_id = $this->Auth->user('id');
		$this->LoginAd->contain();
		$ads_no = $this->LoginAd->find('count', array(
			'conditions' => array(
				'LoginAd.advertiser_id' => $user_id,
				'LoginAd.status' => 'Active',
			)
		));

		$this->LoginAd->contain();
		$ads = $this->LoginAd->find('all', array(
			'conditions' => array(
				'LoginAd.advertiser_id' => $user_id,
			),
		));

		$this->LoginAd->ClickHistory->recursive = -1;
		$clicksToday = $this->LoginAd->ClickHistory->find('all', array(
			'fields' => array(
				'LoginAd.id',
				'SUM(ClickHistory.clicks) as sum',
			),
			'joins' => array(
				array(
					'table' => 'login_ads',
					'alias' => 'LoginAd',
					'type' => 'INNER',
					'conditions' => array('ClickHistory.foreign_key = LoginAd.id')
				),
			),
			'conditions' => array(
				'LoginAd.advertiser_id' => $user_id,
				'ClickHistory.model' => 'LoginAd',
				'ClickHistory.created' => date('Y-m-d'),
			),
			'group' => 'LoginAd.id',
		));

		$clicksToday = Hash::combine($clicksToday, '{n}.LoginAd.id', '{n}.0.sum');

		$this->set(compact('ads_no', 'ads', 'clicksToday'));
		$this->set('breadcrumbTitle', __('Login ads panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->User->BoughtItems->sumLoginAdsPackages());
		$this->set('bannerSize', $this->Settings->fetchOne('loginAdsSize'));
		$this->set('auto_approve', $this->Settings->fetchOne('loginAdsAutoApprove', 0));
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

		$this->LoginAd->contain();
		$ad = $this->LoginAd->find('first', array(
			'fields' => array('LoginAd.id'),
			'conditions' => array(
				'LoginAd.id' => $id,
				'LoginAd.advertiser_id' => $user_id,
			),
		));

		if(empty($ad)) {
			throw new NotFoundExcption(__('Invalid advertisement'));
		}

		if($this->LoginAd->delete($id)) {
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

		$this->LoginAd->id = $adId;
		$this->LoginAd->contain();
		$ad = $this->LoginAd->find('first', array(
			'fields' => array('LoginAd.id'),
			'recursive' => -1,
			'conditions' => array(
				'LoginAd.id' => $adId,
				'LoginAd.advertiser_id' => $this->Auth->user('id'),
				'LoginAd.status' => 'Inactive',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->LoginAd->activate($adId)) {
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

		$this->LoginAd->id = $adId;
		$this->LoginAd->contain();
		$ad = $this->LoginAd->find('first', array(
			'fields' => array('LoginAd.id', 'LoginAd.package_type'),
			'recursive' => -1,
			'conditions' => array(
				'LoginAd.id' => $adId,
				'LoginAd.advertiser_id' => $this->Auth->user('id'),
				'LoginAd.status' => 'Active',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($ad['LoginAd']['package_type'] == 'Days') {
			throw new NotFoundException(__d('exception', 'Inactivating ads with "Days" packages is disabled'));
		}

		if($this->LoginAd->inactivate($adId)) {
			$this->Notice->success(__('Ad successfully paused.'));
		} else {
			$this->Notice->error(__('Error when pausing ad. Please try again.'));
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
			'loginAdsAutoApprove',
			'loginAdsTitleMaxLen',
		));
		$user_id = $this->Auth->user('id');
		$this->LoginAd->contain();
		$ads_no = $this->LoginAd->find('count', array(
			'conditions' => array(
				'LoginAd.advertiser_id' => $user_id,
				'LoginAd.status' => 'Active',
			)
		));

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['LoginAd']['advertiser_id'] = $user_id;

			if($settings['Settings']['loginAdsAutoApprove']) {
				$this->request->data['LoginAd']['status'] = 'Inactive';
			} else {
				$this->request->data['LoginAd']['status'] = 'Pending';
			}

			$this->LoginAd->create();
			if($this->LoginAd->save($this->request->data)) {
				$this->Notice->success(__('Login ad saved successfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to save login ad. Please, try again.'));
			}
		}

		$this->set(compact('ads_no'));
		$this->set('breadcrumbTitle', __('Login ads panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->User->BoughtItems->sumLoginAdsPackages());
		$this->set('titleMax', $settings['Settings']['loginAdsTitleMaxLen']);
	}

/**
 * edit method
 *
 * @return void
 */
	public function edit($id = null) {
		$settings = $this->Settings->fetch(array(
			'loginAdsAutoApprove',
			'loginAdsTitleMaxLen',
		));
		$user_id = $this->Auth->user('id');

		$this->LoginAd->contain();
		$ad = $this->LoginAd->find('first', array(
			'conditions' => array(
				'LoginAd.id' => $id,
				'LoginAd.advertiser_id' => $user_id,
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid ad'));
		}

		if(!$settings['Settings']['loginAdsAutoApprove'] && $ad['LoginAd']['package_type']) {
			throw new NotFoundException(__d('exception', 'Editing ads with package type "Days" is not allowed'));
		}

		$this->LoginAd->contain();
		$ads_no = $this->LoginAd->find('count', array(
			'conditions' => array(
				'LoginAd.advertiser_id' => $user_id,
				'LoginAd.status' => 'Active',
			)
		));

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['LoginAd']['advertiser_id'] = $user_id;

			if(!$settings['Settings']['loginAdsAutoApprove']) {
				$this->request->data['LoginAd']['status'] = 'Pending';
			}

			$this->LoginAd->id = $id;
			if($this->LoginAd->save($this->request->data)) {
				$this->Notice->success(__('Login ad saved successfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to save login ad. Please, try again.'));
			}
		} else {
			$this->request->data = $ad;
		}

		$this->set(compact('ads_no'));
		$this->set('breadcrumbTitle', __('Login ads panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->User->BoughtItems->sumLoginAdsPackages());
		$this->set('titleMax', $settings['Settings']['loginAdsTitleMaxLen']);
	}

/**
 * statistics method
 *
 * @return void
 */
	public function statistics($adId = null) {
		$user_id = $this->Auth->user('id');
		$this->LoginAd->id = $adId;
		$this->LoginAd->contain();
		$ad = $this->LoginAd->find('first', array(
			'fields' => array('LoginAd.id', 'LoginAd.title'),
			'conditions' => array(
				'LoginAd.id' => $adId,
				'LoginAd.advertiser_id' => $user_id,
				'LoginAd.status !=' => 'Pending',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$data = $this->LoginAd->ClickHistory->find('all', array(
			'fields' => array('SUM(clicks) as sum', 'created'),
			'conditions' => array(
				'model' => 'LoginAd',
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));

		$clicks = Hash::combine($data, '{n}.ClickHistory.created', '{n}.0.sum');

		$this->LoginAd->contain();
		$ads_no = $this->LoginAd->find('count', array(
			'conditions' => array(
				'LoginAd.advertiser_id' => $user_id,
				'LoginAd.status' => 'Active',
			)
		));

		$this->set(compact('clicks', 'ad', 'ads_no'));
		$this->set('packsSum', $this->User->BoughtItems->sumLoginAdsPackages());
		$this->set('breadcrumbTitle', __('Login ads panel'));
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

		$this->LoginAd->contain();
		$ads_no = $this->LoginAd->find('count', array(
			'conditions' => array(
				'LoginAd.advertiser_id' => $user_id,
				'LoginAd.status' => 'Active',
			)
		));

		$this->LoginAdsPackage->recursive = -1;
		$packagesData = $this->LoginAdsPackage->find('all', array(
			'order' => 'LoginAdsPackage.price DESC',
		));

		$packages = array();
		foreach($packagesData as &$v) {
			$prices = array();
			$add = false;

			foreach($activeGateways as $k => $g) {
				$price = bcadd($v['LoginAdsPackage']['price'], $this->Payments->getDepositFee($v['LoginAdsPackage']['price'], $k));
				if($this->Payments->checkMinimumDepositAmount($k, $price)) {
					$prices[$k] = $price;
					$add = true;
				} else {
					$prices[$k] = 'disabled';
				}
			}

			if($add) {
				$packages[$v['LoginAdsPackage']['id']] = sprintf('%d %s - %s', $v['LoginAdsPackage']['amount'],
				 __($v['LoginAdsPackage']['type']), CurrencyFormatter::format($v['LoginAdsPackage']['price']));
				$v['LoginAdsPackage']['price_per'] = bcdiv($v['LoginAdsPackage']['price'], $v['LoginAdsPackage']['amount']);
				$v['LoginAdsPackage']['prices'] = $prices;
			}
		}
		$packagesData = Hash::combine($packagesData, '{n}.LoginAdsPackage.id', '{n}');

		if($this->request->is('post')) {
			$pack_id = $this->request->data['package_id'];

			if(!isset($packagesData[$pack_id])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}

			if($this->Payments->checkMinimumDepositAmount($this->request->data['gateway'], $packagesData[$pack_id]['LoginAdsPackage']['price'])) {
				$this->Payments->pay('LoginAdsPackage', $this->request->data['gateway'], $packagesData[$pack_id]['LoginAdsPackage']['price'],
				 $this->Auth->user('id'), array('package_id' => $pack_id));
			} else {
				$this->Notice->error(__('Minimum deposit amount for %s is %s.', $this->request->data['gateway'], CurrencyFormatter::format($this->Payments->getMinimumDepositAmount($this->request->data['gateway']))));
			}
		}

		$this->set(compact('ads_no', 'categories', 'packages', 'activeGateways', 'packagesData'));
		$this->set('breadcrumbTitle', __('Login ads panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumLoginAdsPackages());
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
			'fields' => array('BoughtItem.id', 'LoginAdsPackage.id', 'LoginAdsPackage.type', 'LoginAdsPackage.amount'),
			'recursive' => -1,
			'conditions' => array(
				'BoughtItem.user_id' => $user_id,
				'BoughtItem.model' => 'LoginAdsPackage',
			),
			'joins' => array(
				array(
					'table' => 'login_ads_packages',
					'alias' => 'LoginAdsPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = LoginAdsPackage.id',
					),
				),
			),
		));

		$ads = $this->LoginAd->find('list', array(
			'conditions' => array(
				'LoginAd.advertiser_id' => $user_id,
				'LoginAd.status !=' => 'Pending',
				'OR' => array(
					'LoginAd.expiry' => 0,
					'CASE WHEN LoginAd.package_type = "Clicks" THEN LoginAd.clicks >= LoginAd.expiry
					 ELSE (LoginAd.start IS NULL OR DATEDIFF(NOW(), LoginAd.start) >= LoginAd.expiry) END',
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
			if($this->LoginAd->assignPack($packetsData[$this->request->data['package_id']], $this->request->data['ad_id'])) {
				$this->Notice->success(__('Package assigned successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		$this->LoginAd->contain();
		$ads_no = $this->LoginAd->find('count', array(
			'conditions' => array(
				'LoginAd.advertiser_id' => $user_id,
				'LoginAd.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('%d %s', $v['LoginAdsPackage']['amount'], $v['LoginAdsPackage']['type']);
		}

		$this->set(compact('ads_no', 'packages', 'ads'));
		$this->set('breadcrumbTitle', __('Login ads panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumLoginAdsPackages());
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * assignTo method
 *
 * @return void
 */
	public function assignTo($id = null) {
		$user_id = $this->Auth->user('id');

		$ad = $this->LoginAd->find('first', array(
			'conditions' => array(
				'LoginAd.id' => $id,
				'LoginAd.advertiser_id' => $user_id,
				'LoginAd.status !=' => 'Pending',
				'LoginAd.package_type !=' => '',
			),
		));

		if(empty($ad)) {
			return $this->redirect(array('action' => 'assign'));
		}

		$packetsData = ClassRegistry::init('BoughtItem')->find('all', array(
			'fields' => array('BoughtItem.id', 'LoginAdsPackage.id', 'LoginAdsPackage.type', 'LoginAdsPackage.amount'),
			'recursive' => -1,
			'conditions' => array(
				'BoughtItem.user_id' => $user_id,
				'BoughtItem.model' => 'LoginAdsPackage',
				'LoginAdsPackage.type' => $ad['LoginAd']['package_type'],
			),
			'joins' => array(
				array(
					'table' => 'login_ads_packages',
					'alias' => 'LoginAdsPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = LoginAdsPackage.id',
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
			if($this->LoginAd->addPack($packetsData[$this->request->data['package_id']], $id)) {
				$this->Notice->success(__('Package assigned successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		$this->LoginAd->contain();
		$ads_no = $this->LoginAd->find('count', array(
			'conditions' => array(
				'LoginAd.advertiser_id' => $user_id,
				'LoginAd.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('%d %s', $v['LoginAdsPackage']['amount'], $v['LoginAdsPackage']['type']);
		}

		$this->set(compact('ads_no', 'packages', 'ad'));
		$this->set('breadcrumbTitle', __('Login ads panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumLoginAdsPackages());
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
			'LoginAd.title',
			'LoginAd.url',
			'LoginAd.status',
		));

		$this->LoginAd->contain(array('Advertiser'));
		$this->paginate = array(
			'fields' => array(
				'LoginAd.id',
				'LoginAd.advertiser_id',
				'LoginAd.advertiser_name',
				'LoginAd.title',
				'LoginAd.url',
				'LoginAd.image_url',
				'LoginAd.package_type',
				'LoginAd.start',
				'LoginAd.clicks',
				'LoginAd.total_clicks',
				'LoginAd.expiry',
				'LoginAd.status',
			),
			'order' => 'LoginAd.created DESC'
		);
		$ads = $this->Paginator->paginate($conditions);

		$statuses = $this->LoginAd->getStatusesList();

		$this->set(compact('statuses', 'ads'));
		$this->set('bannerSize', $this->Settings->fetchOne('loginAdsSize'));
	}

/**
 * admin_settings method
 *
 * @return void
 */
	public function admin_settings() {
		$keys = array(
			'loginAdsAutoApprove',
			'loginAdsTitleMaxLen',
			'loginAdsSize',
			'loginAdsPerBox',
			'loginAdsShowMode',
		);
		$globalKeys = array(
			'loginAdsActive',
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

			if(isset($this->request->data['LoginAdsPackage'])) {
				if($this->LoginAdsPackage->saveMany($this->request->data['LoginAdsPackage'])) {
					$this->Notice->success(__d('admin', 'Login ads packages saved successfully'));
				} else {
					$this->Notice->error(__d('admin', 'Failed to save login ads packages. Please, try again.'));
				}
			}
		}
		$settings = $this->Settings->fetch($keys);

		foreach($globalKeys as $k) {
			$settings['Settings'][$k] = Configure::read($k);
		}

		$this->LoginAdsPackage->recursive = -1;
		$packets = $this->LoginAdsPackage->find('all');

		$packets = Hash::extract($packets, '{n}.LoginAdsPackage');

		$this->request->data = Hash::merge($settings, $this->request->data);
		if(isset($this->request->data['LoginAdsPackage'])) {
			$this->request->data['LoginAdsPackage'] = Hash::merge($packets, $this->request->data['LoginAdsPackage']);
		} else {
			$this->request->data['LoginAdsPackage'] = $packets;
		}

		$loginAdsShowModes = array(
			'never' => __d('admin', 'Never'),
			'login' => __d('admin', 'On every login'),
			'day' => __d('admin', 'Once every 24 hours'),
		);

		$this->set('packetsNo', count($packets));
		$this->set('packagesTypes', $this->LoginAdsPackage->getTypesList());
		$this->set(compact('loginAdsShowModes'));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$settings = $this->Settings->fetch(array(
			'loginAdsAutoApprove',
			'loginAdsTitleMaxLen',
		));
		$this->set('titleMax', $settings['Settings']['loginAdsTitleMaxLen']);
		$this->set('packageTypes', $this->LoginAdsPackage->getTypesList());

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['Advertiser']['username'])) {
				$this->LoginAd->Advertiser->contain();
				$advertiser = $this->LoginAd->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));

				if(empty($advertiser)) {
					return $this->Notice->error(__d('admin', 'Advertiser not found.'));
				}

				$this->request->data['LoginAd']['advertiser_id'] = $advertiser['Advertiser']['id'];

				unset($this->request->data['Advertiser']);
			} else {
				$this->request->data['LoginAd']['advertiser_id'] = null;
			}

			$this->request->data['LoginAd']['status'] = 'Active';

			$this->LoginAd->create();
			if($this->LoginAd->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'Login ad saved successfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to save login ad. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @return void
 */
	public function admin_edit($id = null) {
		$this->LoginAd->contain('Advertiser');
		$ad = $this->LoginAd->findById($id);

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$settings = $this->Settings->fetch(array(
			'loginAdsAutoApprove',
			'loginAdsTitleMaxLen',
		));

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['Advertiser']['username'])) {
				$this->LoginAd->Advertiser->contain();
				$advertiser = $this->LoginAd->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));

				if(empty($advertiser)) {
					$this->Notice->error(__d('admin', 'Advertiser not found.'));
				}

				$this->request->data['LoginAd']['advertiser_id'] = $advertiser['Advertiser']['id'];

				unset($this->request->data['Advertiser']);
			} else {
				$this->request->data['LoginAd']['advertiser_id'] = null;
			}

			$this->LoginAd->id = $id;
			if($this->LoginAd->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'Login ad saved successfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to save login ad. Please, try again.'));
			}
		} else {
			$this->request->data = $ad;
		}

		$this->set('titleMax', $settings['Settings']['loginAdsTitleMaxLen']);
		$this->set('packageTypes', $this->LoginAdsPackage->getTypesList());
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if(!$this->LoginAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->LoginAd->delete($id)) {
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
		if(!$this->LoginAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->LoginAd->inactivate($id)) {
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
		if(!$this->LoginAd->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->LoginAd->activate($id)) {
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

			if(!isset($this->request->data['LoginAd']) || empty($this->request->data['LoginAd'])) {
				$this->Notice->error(__d('admin', 'Please select at least one ad.'));
				return $this->redirect($this->referer());
			}

			$ads = 0;
			foreach($this->request->data['LoginAd'] as $id => $on) {
				if($on) {
					$ads++;
					if(!$this->LoginAd->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid advertisement'));
					}
				}
			}

			foreach($this->request->data['LoginAd'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'inactivate':
							$this->LoginAd->inactivate($id);
						break;

						case 'activate':
							$this->LoginAd->activate($id);
						break;

						case 'delete':
							$this->LoginAd->delete($id);
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
		$this->LoginAdsPackage->id = $id;

		if(!$id || !$this->LoginAdsPackage->exists()) {
			throw new NotFoundException(__d('admin', 'Invalid package'));
		}

		if($this->LoginAdsPackage->delete()) {
			$this->Notice->success(__d('admin', 'Login Ads package sucessfully removed.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to delete Login Ads package. Please, try again.'));
		}

		return $this->redirect(array('action' => 'settings', '#' => 'packages'));
	}
}
