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
class AdGridController extends AdGridAppController {
	public $uses = array(
		'AdGrid.AdGridAd',
		'AdGrid.AdGridAdsPackage',
		'AdGrid.AdGridSettings',
		'AdGrid.AdGridMembershipsOption',
		'AdGrid.AdGridUserClick',
		'AdGrid.AdGridWinHistory',
	);

	public $components = array(
		'Paginator',
		'UserPanel',
		'Report',
		'Payments',
	);

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('grid', 'view', 'fetchProgressBar'));
		if($this->request->params['action'] == 'admin_settings') {
			$this->AdGridAdsPackage->recursive = -1;
			$start = $this->AdGridAdsPackage->find('count');

			if(isset($this->request->data['AdGridAdsPackage'])) {
				$stop = count($this->request->data['AdGridAdsPackage']);
			} else {
				$stop = 150;
			}

			for(;$start < $stop; $start++) {
				$this->Security->unlockedFields[] = 'AdGridAdsPackage.'.$start.'.type';
				$this->Security->unlockedFields[] = 'AdGridAdsPackage.'.$start.'.amount';
				$this->Security->unlockedFields[] = 'AdGridAdsPackage.'.$start.'.price';
			}

			$memberships = array_keys(ClassRegistry::init('Membership')->getList());

			foreach($memberships as $id) {
				for($start = 0; $start < 50; $start++) {
					$this->Security->unlockedFields[] = 'AdGridMembershipsOption.'.$id.'.prizes.'.$start.'.prize';
					$this->Security->unlockedFields[] = 'AdGridMembershipsOption.'.$id.'.prizes.'.$start.'.points';
					$this->Security->unlockedFields[] = 'AdGridMembershipsOption.'.$id.'.prizes.'.$start.'.probability';
				}
				for($start = 1000; $start < 1050; $start++) {
					$this->Security->unlockedFields[] = 'AdGridMembershipsOption.'.$id.'.prizes.'.$start.'.prize';
					$this->Security->unlockedFields[] = 'AdGridMembershipsOption.'.$id.'.prizes.'.$start.'.points';
					$this->Security->unlockedFields[] = 'AdGridMembershipsOption.'.$id.'.prizes.'.$start.'.probability';
				}
			}
		}
	}

/**
 * grid method
 *
 * @return void
 */
	public function grid() {
		$active = true;
		$settings = $this->Settings->fetchOne('adGrid');

		$mainPrize = 0;

		if($this->Auth->loggedIn()) {
			$user_id = $this->Auth->user('id');
			$userModel = ClassRegistry::init('User');
			$userModel->bindModel(array(
				'hasOne' => array(
					'AdGridUserClick' => array(
						'className' => 'AdGrid.AdGridUserClick',
					),
				),
			));
			$userModel->ActiveMembership->Membership->bindModel(array(
				'hasOne' => array(
					'AdGridMembershipsOption' => array(
						'className' => 'AdGrid.AdGridMembershipsOption',
					),
				),
			));

			$userModel->contain(array(
				'AdGridUserClick',
				'ActiveMembership' => array(
					'Membership' => array(
						'id',
						'AdGridMembershipsOption' => array(
							'clicks_per_day',
							'prizes',
						),
					),
				),
			));
			$user = $userModel->findById($user_id, array(
				'User.id',
				'AdGridUserClick.fields',
				'AdGridUserClick.clicks',
			));

			foreach($user['ActiveMembership']['Membership']['AdGridMembershipsOption']['prizes'] as $prize) {
				if(bccomp($prize['prize'], $mainPrize) > 0) {
					$mainPrize = $prize['prize'];
				}
			}

			$totalPrizes = $this->AdGridWinHistory->sumForUser($user_id);
			$todayPrizes = $this->AdGridWinHistory->sumForUserToday($user_id);

			if($user['AdGridUserClick']['clicks'] !== null && $user['AdGridUserClick']['clicks'] >= $user['ActiveMembership']['Membership']['AdGridMembershipsOption']['clicks_per_day']) {
				$active = false;
			}

			$this->set(compact('user', 'todayPrizes', 'totalPrizes'));
		} else {
			$this->AdGridMembershipsOption->recursive = -1;
			$options = $this->AdGridMembershipsOption->find('all', array(
				'fields' => array('prizes'),
			));

			foreach($options as $option) {
				foreach($option['AdGridMembershipsOption']['prizes'] as $prize) {
					if(bccomp($prize['prize'], $mainPrize) > 0) {
						$mainPrize = $prize['prize'];
					}
				}
			}
		}

		$this->AdGridWinHistory->recursive = -1;
		$lastWinners = $this->AdGridWinHistory->getLastWinners();

		$this->AdGridWinHistory->recursive = -1;
		$lastMaxWinners = $this->AdGridWinHistory->getLastMaxWinners($mainPrize);

		if($this->theme) {
			$themePath = App::themePath($this->theme).'webroot'.DS.'ad_grid'.DS.'img'.DS.'backgrounds';

			if(file_exists($themePath)) {
				$dir = new Folder($themePath);
				$content = $dir->read();
			}
		}

		if(!isset($content) || empty($content[1])) {
			$dir = new Folder(CakePlugin::path('AdGrid').'webroot'.DS.'img'.DS.'backgrounds');
			$content = $dir->read();
		}

		$image = $content[1][mt_rand(0, count($content[1]) - 1)];

		$this->set(compact('settings', 'mainPrize', 'lastWinners', 'lastMaxWinners', 'image', 'active'));
	}

/**
 * view method
 *
 * @return void
 */
	public function view($x = null, $y = null) {
		if($x === null || $y === null || !is_numeric($x) || !is_numeric($y)) {
			throw new NotFoundException(__d('ad_grid', 'Invalid coordinates'));
		}

		$settings = $this->Settings->fetchOne('adGrid');

		if($x <= 0 || $x > $settings['size']['width'] || $y <= 0 || $y > $settings['size']['height']) {
			throw new NotFoundException(__d('ad_grid', 'Invalid coordinates'));
		}

		if($this->Auth->loggedIn()) {
			$user_id = $this->Auth->user('id');

			$this->AdGridUserClick->recursive = -1;
			$clicks = $this->AdGridUserClick->findByUserId($user_id, array('ads'));

			$this->AdGridAd->recursive = -1;

			if(!empty($clicks)) {
				$ad = $this->AdGridAd->findAdForUser($user_id, $clicks['AdGridUserClick']['ads']);
			} else {
				$ad = $this->AdGridAd->findAdForUser($user_id);
			}
		} else {
			$this->AdGridAd->recursive = -1;
			$ad = $this->AdGridAd->findAdForUser();
		}

		if(empty($ad)) {
			$this->Notice->error(__d('ad_grid', 'Sorry, we currently do not have any ads to watch. Please try again later.'));
			return $this->redirect(array('plugin' => '', 'controller' => 'users', 'action' => 'dashboard'));
		}

		if(!$this->Session->write("AdGridAds.$x:$y", array('status' => 'view', 'id' => $ad['AdGridAd']['id']))) {
			throw new InternalErrorException(__d('ad_grid', 'Session write failed'));
		}

		$adTime = $settings['time'] * 1000;

		$this->layout = 'surfer';
		$this->set(compact('ad', 'settings', 'x', 'y', 'adTime'));
	}

/**
 * report method
 *
 * @return void
 */
	public function report($adId = null) {
		$this->layout = 'ajax';
		$this->request->allowMethod('ajax', 'post');

		if($adId === null || !is_numeric($adId)) {
			throw new NotFoundException(__d('ad_grid', 'Invalid coordinates'));
		}

		$this->AdGridAd->recursive = -1;
		$ad = $this->AdGridAd->exists($adId);

		if(empty($ad)) {
			throw new NotFoundException(__d('ad_grid', 'Invalid advertisement'));
		}

		if($this->request->is('post')) {
			if(!$this->Report->reportItem($this->request->data['type'], $this->AdGridAd, $adId, $this->request->data['reason'], $this->Auth->user('id'))) {
				throw new InternalErrorException(__d('ad_grid', 'Failed to save report'));
			}
		}

		$this->set(compact('adId'));
	}

/**
 * fetchProgressBar method
 *
 * @return void
 */
	public function fetchProgressBar($x = null, $y = null) {
		$this->request->allowMethod('ajax');
		$this->layout = 'ajax';

		if($x === null || $y === null || !is_numeric($x) || !is_numeric($y)) {
			throw new NotFoundException(__d('ad_grid', 'Invalid coordinates'));
		}

		$data = $this->Session->read("AdGridAds.$x:$y");

		if(!$data || empty($data)) {
			throw new NotFoundException(__d('ad_grid', 'Invalid advertisement'));
		}

		$this->AdGridAd->recursive = -1;
		$ad = $this->AdGridAd->findById($data['id']);

		if(empty($ad)) {
			throw new NotFoundException(__d('ad_grid', 'Invalid advertisement'));
		}

		if($data['status'] == 'view') {
			if($this->Auth->loggedIn()) {
				$settings = $this->Settings->fetchOne('adGrid');
				$adTime = $settings['time'] * 1000;

				$data['status'] = 'progressBar';
				$data['progressBarTime'] = time();

				if($this->Session->write("AdGridAds.$x:$y", $data)) {
					$this->set(compact('adTime'));
					return $this->render('progressBar');
				}
			} else {
				$this->Session->write("AdGridAds.$x:$y.status", 'viewedNotLogged');
				return $this->render('notLoggedIn');
			}
		}
		throw new NotFoundException(__d('ad_grid', 'Invalid advertisement'));
	}

/**
 * done method
 *
 * @return void
 */
	public function done($x = null, $y = null) {
		$this->request->allowMethod('ajax');
		$this->layout = 'ajax';

		if($x === null || $y === null || !is_numeric($x) || !is_numeric($y)) {
			throw new NotFoundException(__d('ad_grid', 'Invalid coordinates'));
		}

		$data = $this->Session->read("AdGridAds.$x:$y");

		if(!$data || empty($data)) {
			throw new NotFoundException(__d('ad_grid', 'Invalid advertisement'));
		}

		$this->AdGridAd->recursive = -1;
		$ad = $this->AdGridAd->findById($data['id']);

		if(empty($ad)) {
			throw new NotFoundException(__d('ad_grid', 'Invalid advertisement'));
		}

		if($data['status'] == 'progressBar') {
			$settings = $this->Settings->fetchOne('adGrid');
			if($data['progressBarTime'] + $settings['time'] <= time()) {
				if($this->Session->delete("AdGridAds.$x:$y")) {
					list($winner, $message) = $this->AdGridAd->credit($ad, $this->Auth->user('id'), $x, $y);
					if($winner) {
						$this->set('prize', $message);
						return $this->render('credited');
					} else {
						$this->set(compact('message'));
						return $this->render('notCredited');
					}
				}
			}
		}
		throw new NotFoundException(__d('ad_grid', 'Invalid advertisement'));
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$user_id = $this->Auth->user('id');
		$this->AdGridAd->contain();
		$ads_no = $this->AdGridAd->find('count', array(
			'conditions' => array(
				'AdGridAd.advertiser_id' => $user_id,
				'AdGridAd.status' => 'Active',
			)
		));

		$this->AdGridAd->contain();
		$ads = $this->AdGridAd->find('all', array(
			'conditions' => array(
				'AdGridAd.advertiser_id' => $user_id,
			),
		));

		$this->AdGridAd->ClickHistory->recursive = -1;
		$clicksToday = $this->AdGridAd->ClickHistory->find('all', array(
			'fields' => array(
				'AdGridAd.id',
				'SUM(ClickHistory.clicks) as sum',
			),
			'joins' => array(
				array(
					'table' => 'ad_grid_ads',
					'alias' => 'AdGridAd',
					'type' => 'INNER',
					'conditions' => array('ClickHistory.foreign_key = AdGridAd.id')
				),
			),
			'conditions' => array(
				'AdGridAd.advertiser_id' => $user_id,
				'ClickHistory.model' => 'AdGrid.AdGridAd',
				'ClickHistory.created' => date('Y-m-d'),
			),
			'group' => 'AdGridAd.id',
		));

		$clicksToday = Hash::combine($clicksToday, '{n}.AdGridAd.id', '{n}.0.sum');

		$this->set(compact('ads_no', 'ads', 'clicksToday'));
		$this->set('breadcrumbTitle', __d('ad_grid', 'AdGrid ads panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->AdGridAdsPackage->sumBoughtPackages());
		$this->set('auto_approve', $this->Settings->fetchOne('adGrid')['autoApprove']);
	}

/**
 * delete method
 *
 * @return void
 */
	public function delete($id = null) {
		$user_id = $this->Auth->user('id');

		if(!$id || !$user_id) {
			throw new NotFoundException(__d('ad_grid', 'Invalid advertisement'));
		}

		$this->AdGridAd->contain();
		$ad = $this->AdGridAd->find('first', array(
			'fields' => array('AdGridAd.id'),
			'conditions' => array(
				'AdGridAd.id' => $id,
				'AdGridAd.advertiser_id' => $user_id,
			),
		));

		if(empty($ad)) {
			throw new NotFoundExcption(__d('ad_grid', 'Invalid advertisement'));
		}

		if($this->AdGridAd->delete($id)) {
			$this->Notice->success(__d('ad_grid', 'Advertisement deleted.'));
		} else {
			$this->Notice->error(__d('ad_grid', 'Failed to delete advertisement. Please try again.'));
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

		$this->AdGridAd->id = $adId;
		$this->AdGridAd->contain();
		$ad = $this->AdGridAd->find('first', array(
			'fields' => array('AdGridAd.id'),
			'recursive' => -1,
			'conditions' => array(
				'AdGridAd.id' => $adId,
				'AdGridAd.advertiser_id' => $this->Auth->user('id'),
				'AdGridAd.status' => 'Inactive',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('ad_grid', 'Invalid advertisement'));
		}

		if($this->AdGridAd->activate($adId)) {
			$this->Notice->success(__d('ad_grid', 'Ad successfully activated.'));
		} else {
			$this->Notice->error(__d('ad_grid', 'Error when activating ad. Please try again.'));
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

		$this->AdGridAd->id = $adId;
		$this->AdGridAd->contain();
		$ad = $this->AdGridAd->find('first', array(
			'fields' => array('AdGridAd.id', 'AdGridAd.package_type'),
			'recursive' => -1,
			'conditions' => array(
				'AdGridAd.id' => $adId,
				'AdGridAd.advertiser_id' => $this->Auth->user('id'),
				'AdGridAd.status' => 'Active',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('ad_grid', 'Invalid advertisement'));
		}

		if($ad['AdGridAd']['package_type'] == 'Days') {
			throw new NotFoundException(__d('exception', 'Inactivating ads with "Days" packages is disabled'));
		}

		if($this->AdGridAd->inactivate($adId)) {
			$this->Notice->success(__d('ad_grid', 'Ad successfully paused.'));
		} else {
			$this->Notice->error(__d('ad_grid', 'Error when pausing ad. Please try again.'));
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$user_id = $this->Auth->user('id');

		$ads_no = $this->AdGridAd->find('count', array(
			'conditions' => array(
				'AdGridAd.advertiser_id' => $user_id,
				'AdGridAd.status' => 'Active',
			)
		));

		if(!$this->request->is(array('post', 'put'))) {
			if($this->Session->check('NewAdGridAd')) {
				$this->request->data['AdGridAd'] = $this->Session->read('NewAdGridAd');
				$this->Session->delete('NewAdGridAd');
			}
			if($this->Session->check('NewAdGridAdErrors')) {
				$this->AdGridAd->validationErrors = $this->Session->read('NewAdGridAdErrors');
				$this->Session->delete('NewAdGridAdErrors');
			}
		}

		$this->set(compact('ads_no'));
		$this->set('packsSum', $this->AdGridAdsPackage->sumBoughtPackages());
		$this->set('breadcrumbTitle', __d('ad_grid', 'AdGrid ads panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * preview method
 *
 * @return void
 */
	public function preview() {
		$this->request->allowMethod(array('post', 'put'));
		$settings = $this->Settings->fetch(array('PTCCheckConnection', 'PTCCheckConnectionTimeout', 'PTCPreviewTime', 'adGrid'));

		if(isset($this->request->data['AdGridAd']['checked']) && $this->request->data['AdGridAd']['checked']) {
			unset($this->request->data['AdGridAd']['checked']);

			if(!$this->Session->check('NewAdGridAd')) {
				throw new InternalErrorException(__d('ad_grid', 'No advertisement data'));
			}
			$this->request->data['AdGridAd'] = $this->Session->read('NewAdGridAd');
			$this->request->data['AdGridAd']['advertiser_id'] = $this->Auth->user('id');

			if($settings['Settings']['adGrid']['autoApprove']) {
				$this->request->data['AdGridAd']['status'] = 'Inactive';
			} else {
				$this->request->data['AdGridAd']['status'] = 'Pending';
			}

			if(!$this->Session->check('NewAdGridAd.id')) {
				$this->AdGridAd->create();
			}

			if($this->AdGridAd->save($this->request->data)) {
				$this->Notice->success(__d('ad_grid', 'Advertisement saved successfully.'));
				$this->Session->delete('NewAdGridAd');
				$this->Session->delete('NewAdGridAdErrors');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->write('NewAdGridAdErrors', $this->AdGridAd->validationErrors);
				$this->Notice->error(__d('ad_grid', 'Failed to save advertisement. Please, try again.'));
				return $this->redirect(array('action' => 'add'));
			}
		} else {
			$this->Session->write('NewAdGridAd', $this->request->data['AdGridAd']);
		}

		$this->layout = 'surfer';

		$url = $this->request->data['AdGridAd']['url'];

		if(!filter_var($url, FILTER_VALIDATE_URL)) {
			$this->Notice->error(__d('ad_grid', 'Please supply a valid http(s) URL.'));
			return $this->redirect(array('action' => 'add'));
		}

		$this->AdGridAd->create();
		$this->AdGridAd->set($this->request->data);
		if(!$this->AdGridAd->validates()) {
			$this->Session->write('NewAdGridAdErrors', $this->AdGridAd->validationErrors);
			return $this->redirect(array('action' => 'add'));
		}

		$adTime = $settings['Settings']['adGrid']['time'] * 1000;
		$this->set(compact('url', 'adTime'));
	}

/**
 * edit method
 *
 * @return void
 */
	public function edit($id = null) {
		$settings = $this->Settings->fetchOne('adGrid');
		$user_id = $this->Auth->user('id');

		$this->AdGridAd->contain();
		$ad = $this->AdGridAd->find('first', array(
			'conditions' => array(
				'AdGridAd.id' => $id,
				'AdGridAd.advertiser_id' => $user_id,
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('ad_grid', 'Invalid ad'));
		}

		if($ad['AdGridAd']['package_type'] == 'Days' && !$settings['autoApprove']) {
			throw new NotFoundException(__d('exception', 'Editing ads with package type "Days" is not allowed'));
		}

		$this->AdGridAd->contain();
		$ads_no = $this->AdGridAd->find('count', array(
			'conditions' => array(
				'AdGridAd.advertiser_id' => $user_id,
				'AdGridAd.status' => 'Active',
			)
		));

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['AdGridAd']['advertiser_id'] = $user_id;

			if(!$settings['autoApprove']) {
				$this->request->data['AdGridAd']['status'] = 'Pending';
			}

			$this->AdGridAd->id = $id;
			if($this->AdGridAd->save($this->request->data)) {
				$this->Notice->success(__d('ad_grid', 'AdGrid ad saved successfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('ad_grid', 'Failed to save AdGrid ad. Please, try again.'));
			}
		} else {
			$this->request->data = $ad;
		}

		$this->set(compact('ads_no'));
		$this->set('breadcrumbTitle', __d('ad_grid', 'AdGrid ads panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->AdGridAdsPackage->sumBoughtPackages());
	}

/**
 * statistics method
 *
 * @return void
 */
	public function statistics($adId = null) {
		$user_id = $this->Auth->user('id');
		$this->AdGridAd->id = $adId;
		$this->AdGridAd->contain();
		$ad = $this->AdGridAd->find('first', array(
			'fields' => array('AdGridAd.id', 'AdGridAd.url'),
			'conditions' => array(
				'AdGridAd.id' => $adId,
				'AdGridAd.advertiser_id' => $user_id,
				'AdGridAd.status !=' => 'Pending',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('ad_grid', 'Invalid advertisement'));
		}

		$data = $this->AdGridAd->ClickHistory->find('all', array(
			'fields' => array('SUM(clicks) as sum', 'created'),
			'conditions' => array(
				'model' => 'AdGrid.AdGridAd',
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));

		$clicks = Hash::combine($data, '{n}.ClickHistory.created', '{n}.0.sum');

		$this->AdGridAd->contain();
		$ads_no = $this->AdGridAd->find('count', array(
			'conditions' => array(
				'AdGridAd.advertiser_id' => $user_id,
				'AdGridAd.status' => 'Active',
			)
		));

		$this->set(compact('clicks', 'ad', 'ads_no'));
		$this->set('packsSum', $this->AdGridAdsPackage->sumBoughtPackages());
		$this->set('breadcrumbTitle', __d('ad_grid', 'AdGrid ads panel'));
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

		$this->AdGridAd->contain();
		$ads_no = $this->AdGridAd->find('count', array(
			'conditions' => array(
				'AdGridAd.advertiser_id' => $user_id,
				'AdGridAd.status' => 'Active',
			)
		));

		$this->AdGridAdsPackage->recursive = -1;
		$packagesData = $this->AdGridAdsPackage->find('all', array(
			'order' => 'AdGridAdsPackage.price DESC',
		));

		App::uses('CurrencyHelper', 'View/Helper');
		$currencyHelper = new CurrencyHelper(new View()); /* lame... */

		$packages = array();
		foreach($packagesData as &$v) {
			$prices = array();
			$add = false;

			foreach($activeGateways as $k => $g) {
				$price = bcadd($v['AdGridAdsPackage']['price'], $this->Payments->getDepositFee($v['AdGridAdsPackage']['price'], $k));
				if($this->Payments->checkMinimumDepositAmount($k, $price)) {
					$prices[$k] = $price;
					$add = true;
				} else {
					$prices[$k] = 'disabled';
				}
			}

			if($add) {
				$packages[$v['AdGridAdsPackage']['id']] = sprintf('%d %s - %s', $v['AdGridAdsPackage']['amount'],
				 __d('ad_grid', $v['AdGridAdsPackage']['type']), $currencyHelper->format($v['AdGridAdsPackage']['price']));
				$v['AdGridAdsPackage']['price_per'] = bcdiv($v['AdGridAdsPackage']['price'], $v['AdGridAdsPackage']['amount']);
				$v['AdGridAdsPackage']['prices'] = $prices;
			}
		}
		$packagesData = Hash::combine($packagesData, '{n}.AdGridAdsPackage.id', '{n}');

		if(empty($packagesData) || empty($packages)) {
			$this->Notice->error(__d('ad_grid', 'Sorry, buying packages is currently unavailable. Please try again later.'));
			return $this->redirect(array('action' => 'index')); 
		}

		if($this->request->is('post')) {
			$pack_id = $this->request->data['package_id'];

			if(!isset($packagesData[$pack_id])) {
				/* cheater? */
				throw new NotFoundException(__d('ad_grid', 'Invalid package'));
			}

			if($this->Payments->checkMinimumDepositAmount($this->request->data['gateway'], $packagesData[$pack_id]['AdGridAdsPackage']['price'])) {
				$this->Payments->pay('AdGridAdsPackage', $this->request->data['gateway'], $packagesData[$pack_id]['AdGridAdsPackage']['price'],
				 $this->Auth->user('id'), array('package_id' => $pack_id));
			} else {
				$this->Notice->error(__d('ad_grid', 'Minimum deposit amount for %s is %s.', $this->request->data['gateway'], $currencyHelper->format($this->Payments->getMinimumDepositAmount($this->request->data['gateway']))));
			}
		}
		$this->set(compact('ads_no', 'categories', 'packages', 'activeGateways', 'packagesData'));
		$this->set('breadcrumbTitle', __d('ad_grid', 'AdGrid ads panel'));
		$this->set('packsSum', $this->AdGridAdsPackage->sumBoughtPackages());
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
			'fields' => array('BoughtItem.id', 'AdGridAdsPackage.id', 'AdGridAdsPackage.type', 'AdGridAdsPackage.amount'),
			'recursive' => -1,
			'conditions' => array(
				'BoughtItem.user_id' => $user_id,
				'BoughtItem.model' => 'AdGrid.AdGridAdsPackage',
			),
			'joins' => array(
				array(
					'table' => 'ad_grid_ads_packages',
					'alias' => 'AdGridAdsPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = AdGridAdsPackage.id',
					),
				),
			),
		));

		$ads = $this->AdGridAd->find('all', array(
			'fields' => array(
				'AdGridAd.id',
				'CONCAT("#", AdGridAd.id, " - ", AdGridAd.url) as title',
			),
			'conditions' => array(
				'AdGridAd.advertiser_id' => $user_id,
				'AdGridAd.status !=' => 'Pending',
				'OR' => array(
					'AdGridAd.expiry' => 0,
					'CASE WHEN AdGridAd.package_type = "Clicks" THEN AdGridAd.clicks >= AdGridAd.expiry
					 ELSE (AdGridAd.start IS NULL OR DATEDIFF(NOW(), AdGridAd.start) >= AdGridAd.expiry) END',
				),
			),
		));
		$ads = Hash::combine($ads, '{n}.AdGridAd.id', '{n}.0.title');

		if($this->request->is(array('post', 'put'))) {
			if(!isset($ads[$this->request->data['ad_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('ad_grid', 'Invalid package'));
			}
			$packetsData = Hash::combine($packetsData, '{n}.BoughtItem.id', '{n}');
			if(!isset($packetsData[$this->request->data['package_id']]) || empty($packetsData[$this->request->data['package_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('ad_grid', 'Invalid package'));
			}
			if($this->AdGridAd->assignPack($packetsData[$this->request->data['package_id']], $this->request->data['ad_id'])) {
				$this->Notice->success(__d('ad_grid', 'Package assigned successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('ad_grid', 'Failed to assign package. Please try again.'));
			}
		}

		$this->AdGridAd->contain();
		$ads_no = $this->AdGridAd->find('count', array(
			'conditions' => array(
				'AdGridAd.advertiser_id' => $user_id,
				'AdGridAd.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __d('ad_grid', '%d %s', $v['AdGridAdsPackage']['amount'], $v['AdGridAdsPackage']['type']);
		}

		$this->set(compact('ads_no', 'packages', 'ads'));
		$this->set('breadcrumbTitle', __d('ad_grid', 'AdGrid ads panel'));
		$this->set('packsSum', $this->AdGridAdsPackage->sumBoughtPackages());
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * assignTo method
 *
 * @return void
 */
	public function assignTo($id = null) {
		$user_id = $this->Auth->user('id');

		$ad = $this->AdGridAd->find('first', array(
			'conditions' => array(
				'AdGridAd.id' => $id,
				'AdGridAd.advertiser_id' => $user_id,
				'AdGridAd.status !=' => 'Pending',
				'AdGridAd.package_type !=' => '',
			),
		));

		if(empty($ad)) {
			return $this->redirect(array('action' => 'assign'));
		}

		$packetsData = ClassRegistry::init('BoughtItem')->find('all', array(
			'fields' => array('BoughtItem.id', 'AdGridAdsPackage.id', 'AdGridAdsPackage.type', 'AdGridAdsPackage.amount'),
			'recursive' => -1,
			'conditions' => array(
				'BoughtItem.user_id' => $user_id,
				'BoughtItem.model' => 'AdGrid.AdGridAdsPackage',
				'AdGridAdsPackage.type' => $ad['AdGridAd']['package_type'],
			),
			'joins' => array(
				array(
					'table' => 'ad_grid_ads_packages',
					'alias' => 'AdGridAdsPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = AdGridAdsPackage.id',
					),
				),
			),
		));

		if($this->request->is(array('post', 'put'))) {
			$packetsData = Hash::combine($packetsData, '{n}.BoughtItem.id', '{n}');
			if(!isset($packetsData[$this->request->data['package_id']]) || empty($packetsData[$this->request->data['package_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('ad_grid', 'Invalid package'));
			}
			if($this->AdGridAd->addPack($packetsData[$this->request->data['package_id']], $id)) {
				$this->Notice->success(__d('ad_grid', 'Package assigned successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('ad_grid', 'Failed to assign package. Please try again.'));
			}
		}

		$this->AdGridAd->contain();
		$ads_no = $this->AdGridAd->find('count', array(
			'conditions' => array(
				'AdGridAd.advertiser_id' => $user_id,
				'AdGridAd.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __d('ad_grid', '%d %s', $v['AdGridAdsPackage']['amount'], $v['AdGridAdsPackage']['type']);
		}

		$this->set(compact('ads_no', 'packages', 'ad'));
		$this->set('breadcrumbTitle', __d('ad_grid', 'AdGrid ads panel'));
		$this->set('packsSum', $this->AdGridAdsPackage->sumBoughtPackages());
		$this->set('user', $this->UserPanel->getData());
	}


/**
 * advertisementPanel
 *
 * @return void
 */
	public function advertisementPanel() {
		$this->set('breadcrumbTitle', __d('ad_grid', 'AdGrid ads panel'));
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
			'AdGridAd.title',
			'AdGridAd.url',
			'AdGridAd.status',
		));

		if(isset($conditions['Advertiser.username LIKE']) 
		 && $conditions['Advertiser.username LIKE'] == '%Admin%') {
			unset($conditions['Advertiser.username LIKE']);
			$conditions['AdGridAd.advertiser_id'] = null;
		}

		$this->AdGridAd->contain(array('Advertiser'));
		$this->paginate = array(
			'fields' => array(
				'AdGridAd.id',
				'AdGridAd.advertiser_name',
				'AdGridAd.advertiser_id',
				'AdGridAd.url',
				'AdGridAd.package_type',
				'AdGridAd.expiry',
				'AdGridAd.total_clicks',
				'AdGridAd.start',
				'AdGridAd.clicks',
				'AdGridAd.status',
			),
			'order' => 'AdGridAd.created DESC',
		);
		$ads = $this->Paginator->paginate($conditions);

		$statuses = $this->AdGridAd->getStatusesList();

		$this->set(compact('statuses', 'ads'));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$this->set('packageTypes', $this->AdGridAdsPackage->getTypesList());

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['Advertiser']['username'])) {
				$this->AdGridAd->Advertiser->contain();
				$advertiser = $this->AdGridAd->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));

				if(empty($advertiser)) {
					return $this->Notice->error(__d('ad_grid_admin', 'Advertiser not found.'));
				}

				$this->request->data['AdGridAd']['advertiser_id'] = $advertiser['Advertiser']['id'];

				unset($this->request->data['Advertiser']);
			} else {
				$this->request->data['AdGridAd']['advertiser_id'] = null;
			}

			$this->request->data['AdGridAd']['status'] = 'Active';

			$this->AdGridAd->create();
			if($this->AdGridAd->save($this->request->data)) {
				$this->Notice->success(__d('ad_grid_admin', 'AdGrid ad saved successfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('ad_grid_admin', 'Failed to save AdGrid ad. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @return void
 */
	public function admin_edit($id = null) {
		$this->AdGridAd->contain(array(
			'Advertiser' => array(
				'id', 
				'username',
			),
		));
		$ad = $this->AdGridAd->findById($id);

		if(empty($ad)) {
			throw new NotFoundException(__d('ad_grid_admin', 'Invalid advertisement'));
		}

		$this->set('packageTypes', $this->AdGridAdsPackage->getTypesList());

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['Advertiser']['username'])) {
				$this->AdGridAd->Advertiser->contain();
				$advertiser = $this->AdGridAd->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));

				if(empty($advertiser)) {
					return $this->Notice->error(__d('ad_grid_admin', 'Advertiser not found.'));
				}

				$this->request->data['AdGridAd']['advertiser_id'] = $advertiser['Advertiser']['id'];

				unset($this->request->data['Advertiser']);
			} else {
				$this->request->data['AdGridAd']['advertiser_id'] = null;
			}

			$this->AdGridAd->id = $id;
			if($this->AdGridAd->save($this->request->data)) {
				$this->Notice->success(__d('ad_grid_admin', 'AdGrid ad saved successfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('ad_grid_admin', 'Failed to save AdGrid ad. Please, try again.'));
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
		if(!$this->AdGridAd->exists($id)) {
			throw new NotFoundException(__d('ad_grid_admin', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->AdGridAd->delete($id)) {
			$this->Notice->success(__d('ad_grid_admin', 'The advertisement has been deleted.'));
		} else {
			$this->Notice->error(__d('ad_grid_admin', 'The advertisement could not be deleted. Please, try again.'));
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
		if(!$this->AdGridAd->exists($id)) {
			throw new NotFoundException(__d('ad_grid_admin', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->AdGridAd->inactivate($id)) {
			$this->Notice->success(__d('ad_grid_admin', 'The advertisement has been inactivated.'));
		} else {
			$this->Notice->error(__d('ad_grid_admin', 'The advertisement could not be inactivated. Please, try again.'));
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
		if(!$this->AdGridAd->exists($id)) {
			throw new NotFoundException(__d('ad_grid_admin', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->AdGridAd->activate($id)) {
			$this->Notice->success(__d('ad_grid_admin', 'The advertisement has been activated.'));
		} else {
			$this->Notice->error(__d('ad_grid_admin', 'The advertisement could not be activated. Please, try again.'));
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

			if(!isset($this->request->data['AdGridAd']) || empty($this->request->data['AdGridAd'])) {
				$this->Notice->error(__d('ad_grid_admin', 'Please select at least one ad.'));
				return $this->redirect($this->referer());
			}

			$ads = 0;
			foreach($this->request->data['AdGridAd'] as $id => $on) {
				if($on) {
					$ads++;
					if(!$this->AdGridAd->exists($id)) {
						throw new NotFoundException(__d('ad_grid_admin', 'Invalid advertisement'));
					}
				}
			}

			foreach($this->request->data['AdGridAd'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'inactivate':
							$this->AdGridAd->inactivate($id);
						break;

						case 'activate':
							$this->AdGridAd->activate($id);
						break;

						case 'delete':
							$this->AdGridAd->delete($id);
						break;
					}
				}
			}
			if($ads) {
				$this->Notice->success(__d('ad_grid_admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('ad_grid_admin', 'Please select at least one ad.'));
			}
		} else {
			$this->Notice->error(__d('ad_grid_admin', 'Please select action.'));
		}
		$this->redirect($this->referer());
	}

/**
 * admin_settings method
 *
 * @return void
 */
	public function admin_settings() {
		if($this->request->is(array('post', 'put'))) {
			if(isset($this->request->data['AdGridSettings'])) {
				if($this->AdGridSettings->store($this->request->data, 'adGrid')) {
					$this->Notice->success(__d('ad_grid_admin', 'AdGridSettings saved successfully.'));
				} else {
					$this->Notice->error(__d('ad_grid_admin', 'The settings could not be saved. Please, try again.'));
				}
			}

			if(isset($this->request->data['AdGridAdsPackage'])) {
				if($this->AdGridAdsPackage->saveMany($this->request->data['AdGridAdsPackage'])) {
					$this->Notice->success(__d('ad_grid_admin', 'AdGrid ads packages saved successfully.'));
				} else {
					$this->Notice->error(__d('ad_grid_admin', 'Failed to save AdGrid ads packages. Please, try again.'));
				}
			}

			if(isset($this->request->data['AdGridMembershipsOption'])) {
				$this->AdGridMembershipsOption->recursive = -1;
				if($this->AdGridMembershipsOption->saveMany($this->request->data['AdGridMembershipsOption'])) {
					$this->Notice->success(__d('ad_grid_admin', 'AdGrid membership options saved successfully.'));
				} else {
					$this->Notice->error(__d('ad_grid_admin', 'Failed to save AdGrid memberships options. Please, try again.'));
				}
			}
		}
		$settings = $this->AdGridSettings->fetch('adGrid');

		$this->AdGridAdsPackage->recursive = -1;
		$packets = $this->AdGridAdsPackage->find('all');

		$packets = Hash::extract($packets, '{n}.AdGridAdsPackage');

		$this->request->data = Hash::merge($settings, $this->request->data);
		if(isset($this->request->data['AdGridAdsPackage'])) {
			$this->request->data['AdGridAdsPackage'] = Hash::merge($packets, $this->request->data['AdGridAdsPackage']);
		} else {
			$this->request->data['AdGridAdsPackage'] = $packets;
		}

		$memberships = ClassRegistry::init('Membership')->getList();
		$this->AdGridMembershipsOption->recursive = -1;
		$options = $this->AdGridMembershipsOption->find('all');
		$options = Hash::combine($options, '{n}.AdGridMembershipsOption.membership_id', '{n}.AdGridMembershipsOption');

		if(isset($this->request->data['AdGridMembershipsOption'])) {
			foreach($this->request->data['AdGridMembershipsOption'] as &$v) {
				unset($v['prizes']);
			}
			$this->request->data['AdGridMembershipsOption'] = Hash::merge($options, $this->request->data['AdGridMembershipsOption']);
		} else {
			$this->request->data['AdGridMembershipsOption'] = $options;
		}

		$this->set('packetsNo', count($packets));
		$this->set('packagesTypes', $this->AdGridAdsPackage->getTypesList());
		$this->set(compact('memberships'));
	}

/**
 * admin_delete_package method
 *
 * @return void
 */
	public function admin_delete_package($id = null) {
		$this->AdGridAdsPackage->id = $id;

		if(!$id || !$this->AdGridAdsPackage->exists()) {
			throw new NotFoundException(__d('ad_grid_admin', 'Invalid package'));
		}

		if($this->AdGridAdsPackage->delete()) {
			$this->Notice->success(__d('ad_grid_admin', 'AdGrid package sucessfully removed.'));
		} else {
			$this->Notice->error(__d('ad_grid_admin', 'Failed to delete AdGrid package. Please, try again.'));
		}

		return $this->redirect(array('action' => 'settings', '#' => 'packages'));
	}
}
