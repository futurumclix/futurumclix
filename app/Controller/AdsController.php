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
App::uses('Sanitize', 'Utility');
/**
 * Ads Controller
 *
 * @property Ad $Ad
 * @property PaginatorComponent $Paginator
 */
class AdsController extends AppController {

/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'Ad',
		'User'
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		'Report',
		'UserPanel',
		'Location',
		'Captcha' => array(
			'mode' => 'surfer',
		),
	);

	private $captchaType = 'disabled';

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow(array('view', 'fetchProgressBar'));

		if(Module::active('AccurateLocationDatabase')) {
			if($this->request->params['action'] == 'targetting' || $this->request->params['action'] == 'admin_edit') {
				if(isset($this->request->data['Ad']['id'])) {
					$start = $this->Ad->TargettedLocations->find('count', array(
						'conditions' => array(
							'ad_id' => $this->request->data['Ad']['id'],
						),
					));
				} else {
					$start = 0;
				}

				if(isset($this->request->data['Ad']['AccurateTargettedLocations'])) {
					$stop = count($this->request->data['Ad']['AccurateTargettedLocations']);
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

		if(strtolower($this->request->params['action']) === 'verifycaptcha' || strtolower($this->request->params['action']) === 'fetchcaptcha') {
			$this->captchaType = $this->Settings->fetchOne('captchaTypeSurfer', 'disabled');
			$this->Captcha->protect(array('verifycaptcha', 'fetchcaptcha'));
		}
	}

/**
 * _findAdForUser method
 *
 * @return array or null
 */
	private function _findAdForUser($adId = null, $adRecursive = 0) {
		$ad = null;
		if($this->Auth->loggedIn()) {
			$this->User->id = $this->Auth->user('id');
			$this->User->contain(array(
				'ActiveMembership.Membership',
			));
			$this->User->read();
			$ad = $this->Ad->getAdForUser($adId, $this->User->data['User']['id'], $this->User->data['ActiveMembership']['membership_id'],
			  $this->Location->getConditions($this->User->data['User']['location']), $adRecursive);
		} else {
			$ad = $this->Ad->getActiveAd($adId);
		}
		return $ad;
	}

/**
 * _creditAd method
 *
 * @return integer
 */
	private function _creditAd($adId) {
		$userId = $this->Auth->user('id');
		$todayNumber = $this->Settings->fetch('activeStatsNumber')['Settings']['activeStatsNumber'];
		$ad = $this->_findAdForUser($adId);
		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->Ad->AdsCategory->ClickValue->recursive = -1;
		$clickValues = $this->Ad->AdsCategory->ClickValue->find('first', array(
			'conditions' => array(
				'ClickValue.ads_category_id' => $ad['AdsCategory']['id'],
				'ClickValue.membership_id' => $this->User->data['ActiveMembership']['Membership']['id'],
			),
		));
		if(empty($clickValues)) {
			throw new InternalErrorException(__d('exception', 'ClickValues not found'));
		}

		$updateUserData = array(
			"user_clicks_$todayNumber" => "user_clicks_$todayNumber + 1",
			'total_clicks' => 'total_clicks + 1',
			'total_clicks_earned' => 'total_clicks_earned + '.$clickValues['ClickValue']['user_click_value'],
			'clicks_as_dref' => 'clicks_as_dref + 1',
			'clicks_as_rref' => 'clicks_as_rref + 1',
			'last_click_date' => 'NOW()',
		);

		if($this->User->data['ActiveMembership']['Membership']['points_enabled']) {
			$addPoints = true;
		} else {
			$addPoints = false;
		}

		if(($this->User->Upline->id = $this->Auth->user('upline_id')) != null) {
			if($this->User->Upline->exists()) {
				$updateData = array(
					"dref_clicks_$todayNumber" => "dref_clicks_$todayNumber + 1",
					'total_drefs_clicks' => 'total_drefs_clicks + 1',
				);

				if($this->checkUserActivity($this->User->Upline->id)) {
					$this->User->Upline->contain(array('ActiveMembership.Membership.ClickValue.ads_category_id = '.$ad['AdsCategory']['id'], 'ActiveMembership' => array('Membership' => 'points_enabled')));
					$this->User->Upline->read();

					if(bccomp($this->User->Upline->data['ActiveMembership']['Membership']['ClickValue'][0]['direct_referral_click_value'], '0') >= 1) {
						$updateUserData["clicks_as_dref_credited_$todayNumber"] = "clicks_as_dref_credited_$todayNumber + 1";
						$updateUserData['clicks_as_dref_credited'] = 'clicks_as_dref_credited + 1';
						$updateUserData['earned_as_dref'] = 'earned_as_dref + '.$this->User->Upline->data['ActiveMembership']['Membership']['ClickValue'][0]['direct_referral_click_value'];

						$updateData['total_drefs_credited_clicks'] = 'total_drefs_credited_clicks + 1';
						$updateData["dref_clicks_credited_$todayNumber"] = "dref_clicks_credited_$todayNumber + 1";
						$updateData['total_drefs_clicks_earned'] = 'total_drefs_clicks_earned + '.$this->User->Upline->data['ActiveMembership']['Membership']['ClickValue'][0]['direct_referral_click_value'];
						$this->User->Upline->accountBalanceAdd($this->User->Upline->data['ActiveMembership']['Membership']['ClickValue'][0]['direct_referral_click_value'], $this->User->Upline->id);
					}

					if($this->User->Upline->data['ActiveMembership']['Membership']['points_enabled']) {
						$this->User->Upline->pointsAdd($this->User->Upline->data['ActiveMembership']['Membership']['ClickValue'][0]['direct_referral_click_points'], $this->User->Upline->id);
					}

					$this->User->UserStatistic->recursive = -1;
					if(!$this->User->UserStatistic->updateAll($updateData, array(
						'user_id' => $this->User->Upline->id,
					))) {
						throw new InternalErrorException(__d('exception', 'Cannot save statistic data'));
					}
				}
			}
		}

		if(($this->User->RentedUpline->id = $this->Auth->user('rented_upline_id')) != null) {
			$this->User->RentedUpline->contain(array('ActiveMembership.Membership.ClickValue.ads_category_id = '.$ad['AdsCategory']['id'], 'ActiveMembership.Membership.RentedReferralsPrice', 'ActiveMembership' => array('Membership' => 'points_enabled')));
			$rentedUpline = $this->User->RentedUpline->findById($this->User->RentedUpline->id);

			if(!empty($rentedUpline)) {
				$updateData = array(
					"rref_clicks_$todayNumber" => "rref_clicks_$todayNumber + 1",
					'total_rrefs_clicks' => 'total_rrefs_clicks + 1',
				);

				if($this->checkUserActivity($this->User->RentedUpline->id)) {
					if(bccomp($rentedUpline['ActiveMembership']['Membership']['ClickValue'][0]['rented_referral_click_value'], '0') >= 1) {
						$updateUserData["clicks_as_rref_credited_$todayNumber"] = "clicks_as_rref_credited_$todayNumber + 1";
						$updateUserData['clicks_as_rref_credited'] = 'clicks_as_rref_credited + 1';
						$updateUserData['earned_as_rref'] = 'earned_as_rref + '.$rentedUpline['ActiveMembership']['Membership']['ClickValue'][0]['rented_referral_click_value'];

						$updateData['total_rrefs_credited_clicks'] = 'total_rrefs_credited_clicks + 1';
						$updateData["rref_clicks_credited_$todayNumber"] = "rref_clicks_credited_$todayNumber + 1";
						$updateData['total_rrefs_clicks_earned'] = 'total_rrefs_clicks_earned + '.$rentedUpline['ActiveMembership']['Membership']['ClickValue'][0]['rented_referral_click_value'];
						if(!$this->User->RentedUpline->accountBalanceAdd($rentedUpline['ActiveMembership']['Membership']['ClickValue'][0]['rented_referral_click_value'], $this->User->RentedUpline->id)) {
							throw new InternalErrorException(__d('exception', 'Cannot save upline data'));
						}
					}

					if($rentedUpline['ActiveMembership']['Membership']['points_enabled']) {
						$this->User->RentedUpline->pointsAdd($rentedUpline['ActiveMembership']['Membership']['ClickValue'][0]['direct_referral_click_points'], $this->User->RentedUpline->id);
					}

					$this->User->UserStatistic->recursive = -1;
					if(!$this->User->UserStatistic->updateAll($updateData, array(
						'user_id' => $this->User->RentedUpline->id,
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
							if(!$this->User->RentedUpline->purchaseBalanceSub($range['autopay_price'], $this->User->RentedUpline->id)) {
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
									'RentedUpline.id' => $this->User->RentedUpline->id,
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
							ClassRegistry::init('AutopayHistory')->add($range['autopay_price'], $this->User->RentedUpline->id);
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

		$this->User->accountBalanceAdd($clickValues['ClickValue']['user_click_value'], $userId);
		$this->User->id = $userId;
		$this->User->saveField('autopay_done', true);

		if($addPoints) {
			$this->User->pointsAdd($clickValues['ClickValue']['user_click_points'], $userId);
		}

		$this->User->VisitedAds->create();
		$data = array(
			'VisitedAds' => array(
				'user_id' => $userId,
				'ad_id' => $adId,
			),
		);
		if(!$this->User->VisitedAds->save($data)) {
			throw new InternalErrorException(__d('exception', 'Failed to mark ad visited'));
		}

		$this->Ad->id = $adId;
		$adData = array(
			'clicks' => $ad['Ad']['clicks'] + 1,
		);

		if(($ad['Ad']['package_type'] === 'Clicks')) {
			$adData['expiry'] = $ad['Ad']['expiry'] - 1;

			if($ad['Ad']['expiry'] - 1 <= 0) {
				$adData['status'] = 'Inactive';
			}
		}

		if(!$this->Ad->save(array('Ad' => $adData), true, array('clicks', 'expiry', 'status'))) {
			throw new InternalErrorException(__d('exception', 'Cannot save ad data'));
		}

		$this->User->id = $userId;
		$this->User->contain();
		$this->User->read(array('location', 'first_click'));

		if(!$this->Ad->ClickHistory->addClick('Ad', $adId, $this->User->data['User']['location'])) {
			throw new InternalErrorException(__d('exception', 'Cannot save ad statistics'));
		}

		if($this->User->data['User']['first_click'] == null) {
			$this->User->saveField('first_click', date('Y-m-d H:i:s'));
		}

		return $clickValues['ClickValue']['user_click_value'];
	}

/**
 * view method
 *
 * @return void
 */
	public function view($adId = null) {
		$this->layout = 'surfer';
		$ad = $this->_findAdForUser($adId);
		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		if(!$this->Session->write("Ads.$adId.Status", 'view')) {
			throw new InternalErrorException(__d('exception', 'Session write failed'));
		}
		$adTime = $ad['AdsCategory']['time'] * 1000;
		$settings = $this->Settings->fetch(array('focusAdView', 'loadTimeAdView', 'typeTimeAdView'));

		$this->set(compact('ad', 'adTime', 'settings'));
	}

/**
 * report method
 *
 * @return void
 */
	public function report($adId = null) {
		$this->layout = 'ajax';
		$this->request->allowMethod('ajax', 'post');

		$ad = $this->Ad->exists($adId);

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->request->is('post')) {
			if(!$this->Report->reportItem($this->request->data['type'], $this->Ad, $adId, $this->request->data['reason'], $this->Auth->user('id'))) {
				throw new InternalErrorException(__d('exception', 'Failed to save report'));
			}
		}

		$this->set(compact('adId'));
	}

/**
 * fetchProgressBar method
 *
 * @return void
 */
	public function fetchProgressBar($adId = null) {
		$this->request->allowMethod('ajax');
		$this->layout = 'ajax';
		$ad = $this->_findAdForUser($adId);
		if($this->Session->read("Ads.$adId.Status") === 'view') {
			if($this->Auth->loggedIn()) {
				if(!empty($ad)) {
					$adTime = $ad['AdsCategory']['time'] * 1000;
					if($this->Session->write("Ads.$adId.Status", 'progressBar')) {
						if($this->Session->write("Ads.$adId.ProgressBarTime", time())) {
							$this->set(compact('adTime'));
							return $this->render('progressBar');
						}
					}
				}
			} else {
				if(!empty($ad)) {
					$this->Ad->id = $ad['Ad']['id'];
					$this->Ad->set(array('id' => $ad['Ad']['id'], 'outside_clicks' => $ad['Ad']['outside_clicks'] + 1));
					if($this->Ad->save() && $this->Session->write("Ads.$adId.Status", 'viewedNotLogged')) {
						return $this->render('notLoggedIn');
					} else {
						throw new InternalErrorException(__d('exception', 'Failed to update advertisement'));
					}
				}
			}
		}
		throw new NotFoundException(__d('exception', 'Invalid advertisement'));
	}

/**
 * fetchCaptcha method
 *
 * @return void
 */
	public function fetchCaptcha($adId = null) {
		$this->request->allowMethod('ajax');
		$this->layout = 'ajax';
		$ad = $this->_findAdForUser($adId);

		if(empty($ad)) {
			/* TODO: cheater? */
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->Session->read("Ads.$adId.Status") !== 'progressBar') {
			/* TODO: cheater? */
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$progressBarTime = intval($this->Session->read("Ads.$adId.ProgressBarTime"));
		$adTime = intval($ad['AdsCategory']['time']);

		if(time() < $progressBarTime + $adTime) {
			/* TODO: cheater? */
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->captchaType === 'disabled') {
			$earn = $this->_creditAd($adId);
			$this->set(compact('earn'));
			$this->Session->delete("Ads.$adId");
			return $this->render('credited');
		}

		$this->Session->write("Ads.$adId.Status", 'verifyCaptcha');
		$this->set(compact('adId'));
		return $this->render('captcha');
	}

/**
 * verifyCaptcha method
 *
 * @return void
 */
	public function verifyCaptcha($adId = null) {
		$this->request->allowMethod('ajax');
		$this->layout = 'ajax';
		if($this->request->is('post')) {
			if($this->Session->read("Ads.$adId.Status") === 'verifyCaptcha') {
				$earn = $this->_creditAd($adId);
				$this->set(compact('earn'));
				$this->Session->delete("Ads.$adId");
				return $this->render('credited');
			}
		}
		throw new NotFoundException(__d('exception', 'Invalid advertisement'));
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->helpers[] = 'Forum.Forum';

		$user_id = $this->Auth->user('id');

		$this->Ad->expireDaysAds($user_id);

		$this->Ad->recursive = -1;
		$ads = $this->Ad->find('all', array(
			'fields' => array(
				'Ad.id',
				'Ad.title',
				'Ad.url',
				'Ad.clicks',
				'Ad.outside_clicks',
				'Ad.status',
				'Ad.package_type',
				'Ad.expiry',
				'Ad.expiry_date',
				'Ad.description',
				'Ad.modified',
				'Ad.created',
				'AdsCategory.name',
				'AdsCategory.geo_targetting',
			),
			'joins' => array(
				array(
					'table' => 'ads_categories',
					'alias' => 'AdsCategory',
					'type' => 'LEFT',
					'conditions' => array('Ad.ads_category_id = AdsCategory.id')
				),
			),
			'conditions' => array(
				'Ad.advertiser_id' => $user_id,
			),
		));
		$ads_no = $this->Ad->find('count', array(
			'conditions' => array(
				'Ad.advertiser_id' => $user_id,
				'Ad.status' => 'Active',
			)
		));
		$this->Ad->ClickHistory->recursive = -1;
		$clicksToday = $this->Ad->ClickHistory->find('all', array(
			'fields' => array(
				'Ad.id',
				'SUM(ClickHistory.clicks) as sum',
			),
			'joins' => array(
				array(
					'table' => 'ads',
					'alias' => 'Ad',
					'type' => 'INNER',
					'conditions' => array('ClickHistory.foreign_key = Ad.id')
				),
			),
			'conditions' => array(
				'Ad.advertiser_id' => $user_id,
				'ClickHistory.model' => 'Ad',
				'ClickHistory.created' => date('Y-m-d'),
			),
			'group' => 'Ad.id',
		));

		$clicksToday = Hash::combine($clicksToday, '{n}.Ad.id', '{n}.0.sum');

		$this->set(compact('ads', 'ads_no', 'clicksToday'));
		$this->set('packsSum', $this->User->BoughtItems->sumPTCPackages());
		$this->set('breadcrumbTitle', __('Advertisement panel'));
		$this->set('auto_approve', $this->Settings->fetchOne('PTCAutoApprove', 0));
		$this->set('user', $this->UserPanel->getData());
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

		$ad = $this->Ad->find('first', array(
			'fields' => array('id'),
			'conditions' => array(
				'Ad.id' => $id,
				'Ad.advertiser_id' => $user_id,
			),
		));

		if(empty($ad)) {
			throw new NotFoundExcption(__('Invalid advertisement'));
		}

		$this->Ad->id = $id;
		if($this->Ad->delete()) {
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

		$this->Ad->expireDaysAds($user_id);

		$this->Ad->id = $adId;
		$ad = $this->Ad->find('first', array(
			'conditions' => array(
				'Ad.id' => $adId,
				'Ad.advertiser_id' => $user_id,
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$auto_approve = $this->Settings->fetchOne('PTCAutoApprove', 0);

		if(!$auto_approve && $ad['Ad']['package_type'] == 'Days') {
			throw new NotFoundException(__d('exception', 'Editing ads with package_type "Days" is not allowed'));
		}

		if(!$this->request->is(array('post', 'put'))) {
			if($this->Session->check('NewAd')) {
				$this->request->data['Ad'] = $this->Session->read('NewAd');
				$this->Session->delete('NewAd');
			} else {
				$this->request->data = $ad;
			}
			if($this->Session->check('NewAdErrors')) {
				$this->Ad->validationErrors = $this->Session->read('NewAdErrors');
				$this->Session->delete('NewAdErrors');
			}
		}

		$this->Ad->recursive = 1;
		$ads_no = $this->Ad->find('count', array(
			'conditions' => array(
				'Ad.advertiser_id' => $user_id,
				'Ad.status' => 'Active',
			)
		));

		$titleMax = $this->Ad->validate['title']['maxLength']['rule'][1];
		$descMax = $this->Ad->validate['description']['maxLength']['rule'][1];

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

		$ads_no = $this->Ad->find('count', array(
			'conditions' => array(
				'Ad.advertiser_id' => $user_id,
				'Ad.status' => 'Active',
			)
		));

		if(!$this->request->is(array('post', 'put'))) {
			if($this->Session->check('NewAd')) {
				$this->request->data['Ad'] = $this->Session->read('NewAd');
				$this->Session->delete('NewAd');
			}
			if($this->Session->check('NewAdErrors')) {
				$this->Ad->validationErrors = $this->Session->read('NewAdErrors');
				$this->Session->delete('NewAdErrors');
			}
		}

		$titleMax = $this->Ad->validate['title']['maxLength']['rule'][1];
		$descMax = $this->Ad->validate['description']['maxLength']['rule'][1];

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
		$settings = $this->Settings->fetch(array('PTCCheckConnection', 'PTCCheckConnectionTimeout', 'PTCPreviewTime', 'PTCAutoApprove'));

		if(isset($this->request->data['Ad']['checked']) && $this->request->data['Ad']['checked']) {
			unset($this->request->data['Ad']['checked']);

			if(!$this->Session->check('NewAd')) {
				throw new InternalErrorException(__d('exception', 'No advertisement data'));
			}
			$this->request->data['Ad'] = $this->Session->read('NewAd');
			$this->request->data['Ad']['advertiser_id'] = $this->Auth->user('id');

			$memberships = $this->Ad->TargettedMemberships->find('list');
			$this->request->data['TargettedMemberships']['TargettedMemberships'] = array_keys($memberships);

			if(!$settings['Settings']['PTCAutoApprove']) {
				$this->request->data['Ad']['status'] = 'Pending';
			}

			if(!$this->Session->check('NewAd.id')) {
				$this->Ad->create();
			}

			if($this->Ad->save($this->request->data)) {
				$this->Notice->success(__('Advertisement saved successfully.'));
				$this->Session->delete('NewAd');
				$this->Session->delete('NewAdErrors');
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->write('NewAdErrors', $this->Ad->validationErrors);
				$this->Notice->error(__('Failed to save advertisement. Please, try again.'));
				return $this->redirect($this->referer());
			}
		} else {
			$this->Session->write('NewAd', $this->request->data['Ad']);
		}

		$this->layout = 'surfer';

		$hide_referer = $this->request->data['Ad']['hide_referer'];
		$url = $this->request->data['Ad']['url'];

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

		$this->Ad->create();
		$this->Ad->set($this->request->data);
		if(!$this->Ad->validates()) {
			$this->Session->write('NewAdErrors', $this->Ad->validationErrors);
			return $this->redirect($this->referer());
		}

		$adTime = $settings['Settings']['PTCPreviewTime'] * 1000;
		$this->set(compact('url', 'adTime', 'hide_referer'));
	}

/**
 * assign method
 *
 * @return void
 */
	public function assign($adId = null) {
		$user_id = $this->Auth->user('id');

		$this->Ad->expireDaysAds($user_id);

		$packetsData = ClassRegistry::init('BoughtItem')->find('all', array(
			'fields' => array('BoughtItem.id', 'AdsCategoryPackage.id', 'AdsCategoryPackage.type', 'AdsCategoryPackage.amount', 'AdsCategory.id', 'AdsCategory.name'),
			'recursive' => -1,
			'conditions' => array(
				'BoughtItem.user_id' => $user_id,
				'BoughtItem.model' => 'AdsCategoryPackage',
			),
			'joins' => array(
				array(
					'table' => 'ads_category_packages',
					'alias' => 'AdsCategoryPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = AdsCategoryPackage.id',
					),
				),
				array(
					'table' => 'ads_categories',
					'alias' => 'AdsCategory',
					'type' => 'INNER',
					'conditions' => array(
						'AdsCategoryPackage.ads_category_id = AdsCategory.id',
					),
				),
			),
		));

		$ads = $this->Ad->find('list', array(
			'conditions' => array(
				'Ad.advertiser_id' => $user_id,
				'Ad.status !=' => 'Pending',
				'Ad.expiry' => 0,
			),
		));

		if($this->request->is(array('post', 'put'))) {
			if(!isset($ads[$this->request->data['ad_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Wrong package'));
			}
			$packetsData = Hash::combine($packetsData, '{n}.BoughtItem.id', '{n}');
			if(!isset($packetsData[$this->request->data['package_id']]) || empty($packetsData[$this->request->data['package_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Wrong package'));
			}
			if($this->Ad->assignPack($packetsData[$this->request->data['package_id']], $this->request->data['ad_id'])) {
				$this->Notice->success(__('Package successfully assigned.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		$ads_no = $this->Ad->find('count', array(
			'conditions' => array(
				'Ad.advertiser_id' => $user_id,
				'Ad.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('%d %s on %s', $v['AdsCategoryPackage']['amount'], $v['AdsCategoryPackage']['type'], $v['AdsCategory']['name']);
		}

		if(isset($adId) && in_array($adId, array_keys($ads))) {
			$this->request->data['ad_id'] = $adId;
		}

		$this->set(compact('ads_no', 'packages', 'ads'));
		$this->set('packsSum', $this->User->BoughtItems->sumPTCPackages());
		$this->set('breadcrumbTitle', __('Advertisement panel'));
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

		$this->Ad->expireDaysAds($user_id);

		$packetsConditions = array(
			'BoughtItem.user_id' => $user_id,
			'BoughtItem.model' => 'AdsCategoryPackage',
		);

		$activeAd = $this->Ad->findById($adId);

		if(empty($activeAd) || $activeAd['Ad']['advertiser_id'] != $user_id) {
			/* cheater? */
			throw new NotFoundException(__d('exception', 'Invalid ad'));
		}

		if(!$activeAd['Ad']['ads_category_id']) {
			return $this->redirect(array('action' => 'assign', $activeAd['Ad']['id']));
		}

		if($activeAd['Ad']['expiry'] > 0) {
			$packetsConditions['AdsCategoryPackage.type'] = $activeAd['Ad']['package_type'];
			$packetsConditions['AdsCategoryPackage.ads_category_id'] = $activeAd['Ad']['ads_category_id'];
		}

		$packetsData = ClassRegistry::init('BoughtItem')->find('all', array(
			'fields' => array('BoughtItem.id', 'AdsCategoryPackage.id', 'AdsCategoryPackage.type', 'AdsCategoryPackage.amount', 'AdsCategory.id', 'AdsCategory.name'),
			'recursive' => -1,
			'conditions' => $packetsConditions,
			'joins' => array(
				array(
					'table' => 'ads_category_packages',
					'alias' => 'AdsCategoryPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = AdsCategoryPackage.id',
					),
				),
				array(
					'table' => 'ads_categories',
					'alias' => 'AdsCategory',
					'type' => 'INNER',
					'conditions' => array(
						'AdsCategoryPackage.ads_category_id = AdsCategory.id',
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
			if($this->Ad->addPack($packetsData[$this->request->data['package_id']], $adId)) {
				$this->Notice->success(__('Package successfully assigned.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		$ads = Hash::combine($activeAd, 'Ad.id', 'Ad.title');

		$ads_no = $this->Ad->find('count', array(
			'conditions' => array(
				'Ad.advertiser_id' => $user_id,
				'Ad.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('%d %s on %s', $v['AdsCategoryPackage']['amount'], $v['AdsCategoryPackage']['type'], $v['AdsCategory']['name']);
		}

		$this->set(compact('ads_no', 'packages', 'ads', 'activeAd'));
		$this->set('packsSum', $this->User->BoughtItems->sumPTCPackages());
		$this->set('breadcrumbTitle', __('Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * targetting method
 *
 * @return void
 */
	public function targetting($type, $adId = null) {
		if($type != 'memberships' && $type != 'geo') {
			throw new NotFoundException(__d('exception', 'Invalid targeting mode'));
		}

		$user_id = $this->Auth->user('id');

		$this->Ad->id = $adId;
		$this->Ad->recursive = 1;
		$ad = $this->Ad->find('first', array(
			'conditions' => array(
				'Ad.id' => $adId,
				'Ad.advertiser_id' => $user_id,
				'Ad.status !=' => 'Pending',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->request->is(array('post', 'put'))) {
			$this->Ad->id = $adId;
			if($this->Ad->save($this->request->data)) {
				$this->Notice->success(__('Ad sucessfully saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Error while saving ad. Please try again.'));
			}
		}

		$this->request->data = Hash::merge($ad, $this->request->data);

		$ads_no = $this->Ad->find('count', array(
			'conditions' => array(
				'Ad.advertiser_id' => $user_id,
				'Ad.status' => 'Active',
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
				$options = $this->Ad->TargettedMemberships->find('list');
				$selected = null;
			break;
		}


		$this->set(compact('ads_no', 'options', 'saveField', 'title', 'selected'));
		$this->set('packsSum', $this->User->BoughtItems->sumPTCPackages());
		$this->set('breadcrumbTitle', __('Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * statistics method
 *
 * @return void
 */
	public function statistics($adId = null) {
		$this->Ad->id = $adId;
		$ad = $this->Ad->find('first', array(
			'fields' => array('Ad.id', 'Ad.title', 'AdsCategory.geo_targetting'),
			'recursive' => 1,
			'conditions' => array(
				'Ad.id' => $adId,
				'Ad.advertiser_id' => $this->Auth->user('id'),
				'Ad.status !=' => 'Pending',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		$data = $this->Ad->ClickHistory->find('all', array(
			'fields' => array('SUM(clicks) as sum', 'created'),
			'conditions' => array(
				'model' => 'Ad',
				'foreign_key' => $adId,
			),
			'recursive' => -1,
			'order' => 'created',
			'group' => 'created',
		));

		$clicks = Hash::combine($data, '{n}.ClickHistory.created', '{n}.0.sum');

		if($ad['AdsCategory']['geo_targetting']) {
			$this->helpers[] = 'Map';

			$data = $this->Ad->ClickHistory->find('all', array(
				'fields' => array('SUM(clicks) as sum', 'Country.code as code'),
				'conditions' => array(
					'model' => 'Ad',
					'foreign_key' => $adId,
				),
				'recursive' => 1,
				'order' => 'Country.country',
				'group' => 'country_id',
			));

			$geo = Hash::combine($data, '{n}.Country.code', '{n}.0.sum');

			$this->set(compact('geo'));
		}

		$ads_no = $this->Ad->find('count', array(
			'conditions' => array(
				'Ad.advertiser_id' => $this->Auth->user('id'),
				'Ad.status' => 'Active',
			)
		));

		$this->set(compact('clicks', 'ad', 'ads_no'));
		$this->set('packsSum', $this->User->BoughtItems->sumPTCPackages());
		$this->set('breadcrumbTitle', __('Advertisement panel'));
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * activate method
 *
 * @return void
 */
	public function activate($adId = null) {
		$this->request->allowMethod('post', 'delete');

		$this->Ad->id = $adId;
		$ad = $this->Ad->find('first', array(
			'fields' => array('Ad.id'),
			'recursive' => -1,
			'conditions' => array(
				'Ad.id' => $adId,
				'Ad.advertiser_id' => $this->Auth->user('id'),
				'Ad.status' => 'Inactive',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($this->Ad->activate($adId)) {
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

		$this->Ad->id = $adId;
		$ad = $this->Ad->find('first', array(
			'fields' => array('Ad.id', 'Ad.package_type'),
			'recursive' => -1,
			'conditions' => array(
				'Ad.id' => $adId,
				'Ad.advertiser_id' => $this->Auth->user('id'),
				'Ad.status' => 'Active',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}

		if($ad['Ad']['package_type'] == 'Days') {
			throw new NotFoundException(__d('exception', 'Inactivating ads with "Days" packages is disabled'));
		}

		if($this->Ad->inactivate($adId)) {
			$this->Notice->success(__('Ad successfully paused.'));
		} else {
			$this->Notice->error(__('Error while pausing ad. Please try again.'));
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$conditions = $this->createPaginatorConditions(array(
			'Ad.advertiser_name',
			'Ad.url',
			'Ad.title',
		));

		$inCollapse = array(
			'Ad.status',
			'Ad.ads_category_id',
		);

		$inCollapse = array_intersect_key($conditions, array_flip($inCollapse));
		if(!empty($inCollapse)) {
			$this->set('searchCollapse', 'in');
		} else {
			$this->set('searchCollapse', '');
		}

		$this->Ad->recursive = -1;
		$this->paginate = array(
			'order' => 'Ad.created DESC',
			'fields' => array(
				'Ad.id',
				'Ad.advertiser_name',
				'Ad.advertiser_id',
				'Ad.title',
				'Ad.url',
				'Ad.package_type',
				'Ad.expiry',
				'Ad.clicks',
				'Ad.outside_clicks',
				'Ad.status',
				'Ad.expiry_date',
				'AdsCategory.id',
				'AdsCategory.name',
			),
		);
		$ads = $this->Paginator->paginate($conditions);
		$this->set('ads', $ads);
		$this->set('statuses', $this->Ad->getStatusesList());
		$this->set('adsCategories', $this->Ad->AdsCategory->find('list'));
	}

/**
 * admin_massaction method
 *
 * @throws NotFoundException
 * @return void
 */
	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {

			if(!isset($this->request->data['Ads']) || empty($this->request->data['Ads'])) {
				$this->Notice->error(__d('admin', 'Please select at least one ad.'));
				return $this->redirect($this->referer());
			}

			$ads = 0;
			foreach($this->request->data['Ads'] as $id => $on) {
				if($on) {
					$ads++;
					if(!$this->Ad->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid advertisement'));
					}
				}
			}

			foreach($this->request->data['Ads'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'inactivate':
							$this->Ad->inactivate($id);
						break;

						case 'activate':
							$this->Ad->activate($id);
						break;

						case 'delete':
							$this->Ad->delete($id);
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
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		$memberships = $this->Ad->TargettedMemberships->find('list');
		$adsCategories = $this->Ad->AdsCategory->find('list');
		$packageTypes = $this->Ad->AdsCategory->AdsCategoryPackage->getTypesList();
		$countries = $this->Location->getCountriesList();

		$this->set(compact('adsCategories', 'countries', 'memberships', 'packageTypes'));

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['TargettedMemberships']['TargettedMemberships'])) {
				if(!empty($this->request->data['Advertiser']['username'])) {
					$this->Ad->Advertiser->contain();
					$advertiser = $this->Ad->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));

					if(empty($advertiser)) {
						return $this->Notice->error(__d('admin', 'Advertiser not found.'));
					}

					$this->request->data['Ad']['advertiser_id'] = $advertiser['Advertiser']['id'];
				}

				if($this->request->data['Ad']['package_type'] == 'Days') {
					$this->request->data['Ad']['expiry'] = 1;
				} else {
					$this->request->data['Ad']['expiry_date'] = null;
				}

				$this->Ad->create();
				if($this->Ad->save($this->request->data)) {
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
		if(!$this->Ad->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['TargettedMemberships']['TargettedMemberships'])) {
				if(Module::active('AccurateLocationDatabase') || !empty($this->request->data['Ad']['TargettedLocations'])) {
					$this->Ad->Advertiser->contain();
					$advertiser = $this->Ad->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));
					if(!empty($advertiser) || empty($this->request->data['Advertiser']['username'])) {
						if(empty($this->request->data['Advertiser']['username'])) {
							$this->request->data['Ad']['advertiser_id'] = null;
						} else {
							$this->request->data['Ad']['advertiser_id'] = $advertiser['Advertiser']['id'];
						}

						if($this->request->data['Ad']['package_type'] == 'Days') {
							$this->request->data['Ad']['expiry'] = 1;
						} else {
							$this->request->data['Ad']['expiry_date'] = null;
						}

						if($this->Ad->save($this->request->data)) {
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

		$options = array('conditions' => array('Ad.'.$this->Ad->primaryKey => $id));
		$this->Ad->recursive = 1;
		$this->request->data = Hash::merge($this->Ad->find('first', $options), $this->request->data);

		if(Module::active('AccurateLocationDatabase')) {
			$tald = ClassRegistry::init('AccurateLocationDatabase.AccurateLocationDatabaseLocation');
			foreach($this->request->data['AccurateTargettedLocations'] as &$l) {
				$data = $tald->getListByParentName($l['country']);
				$l['country_regions'] = array_combine($data, $data);
				$data = $tald->getListByParentName($l['region']);
				$l['region_cities'] = array_combine($data, $data);
			}
		}

		$memberships = $this->Ad->TargettedMemberships->find('list');
		$countries = $this->Location->getCountriesList();
		$adsCategories = $this->Ad->AdsCategory->find('list');
		$packageTypes = $this->Ad->AdsCategory->AdsCategoryPackage->getTypesList();
		$this->set(compact('adsCategories', 'countries', 'memberships', 'packageTypes'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if(!$this->Ad->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->Ad->delete($id)) {
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
		if(!$this->Ad->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->Ad->inactivate($id)) {
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
		if(!$this->Ad->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid advertisement'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->Ad->activate($id)) {
			$this->Notice->success(__d('admin', 'The advertisement has been activated.'));
		} else {
			$this->Notice->error(__d('admin', 'The advertisement could not be activated. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
