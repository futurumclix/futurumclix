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
class AdvertiseController extends AppController {
	public $uses = array(
		'AdsCategory',
		'BannerAdsPackage',
		'ExplorerAdsPackage',
		'ExpressAdsPackage',
		'FeaturedAdsPackage',
		'LoginAdsPackage',
		'Ad',
		'Membership',
		'AnonymousAdvertiser',
	);

	public $components = array(
		'Location',
		'Payments',
		'Captcha',
	);

	public $helpers = array(
	);

	private $acceptedTypes = array(
		'Ad' => 'ads',
		'BannerAd' => 'banner_ads',
		'ExplorerAd' => 'explorer_ads',
		'ExpressAd' => 'express_ads',
		'FeaturedAd' => 'featured_ads',
		'LoginAd' => 'login_ads',
	);

	public function beforeFilter() {
		parent::beforeFilter();

		if(Module::active('AdGrid')) {
			$this->acceptedTypes['AdGrid.AdGridAd'] = 'ad_grid';
			$this->AdGridAdsPackage = ClassRegistry::init('AdGrid.AdGridAdsPackage');
		}

		if(Module::active('AccurateLocationDatabase')) {
			if($this->request->params['action'] == 'buy') {
				for($i = 0; $i < 50; ++$i) {
					$this->Security->unlockedFields[] = 'AccurateTargettedLocations.'.$i.'.country';
					$this->Security->unlockedFields[] = 'AccurateTargettedLocations.'.$i.'.region';
					$this->Security->unlockedFields[] = 'AccurateTargettedLocations.'.$i.'.city';
				}
			}
			$this->helpers[] = 'AccurateLocationDatabase.Locations';
		}

		$this->Auth->allow(array('index', 'buy', 'choose', 'get_form', 'statistics'));
		if($this->Settings->fetchOne('captchaOnAdvertise', 0)) {
			$this->Captcha->protect(array('buy'));
		}
	}

	public function index() {
		$ads = array();
		$settingsKeys = array();
		$models = array();
		foreach($this->uses as $modelName) {
			$key = str_replace('Package', 'Active', $modelName, $count);
			if($count == 1) {
				$models[] = $modelName;
				$settingsKeys[$modelName] = lcfirst($key);
			}
		}

		$this->AdsCategory->contain('AdsCategoryPackage');
		$ads['PaidToClickAds'] = $this->AdsCategory->find('all', array(
			'fields' => array(
				'AdsCategory.name',
			),
		));
		$ads['PaidToClickAds'] = array_filter($ads['PaidToClickAds'], function($v) {
			if(empty($v['AdsCategoryPackage'])) {
				return false;
			}
			return true;
		});


		if(Module::active('AdGrid')) {
			$this->AdGridAdsPackage->recursive = -1;
			$ads['AdGridAdsPackage'] = $this->AdGridAdsPackage->find('all');
		}

		$settings = $this->Settings->fetch($settingsKeys);

		foreach($models as $modelName) {
			if($settings['Settings'][$settingsKeys[$modelName]]) {
				$this->$modelName->recursive = -1;
				$ads[$modelName] = $this->$modelName->find('all');
			}
		}

		$this->set(compact('ads'));
	}

	private function _redirectIfLoggedIn($type) {
		if($this->Auth->loggedIn()) {
			if($type == 'ad_grid') {
				return $this->redirect(array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'index'));
			} else {
				return $this->redirect(array('plugin' => null, 'controller' => $type, 'action' => 'index'));
			}
		}
	}

	public function choose($type = null) {
		if($type === null || !in_array($type, $this->acceptedTypes)) {
			return $this->redirect(array('action' => 'index'));
		}

		$this->_redirectIfLoggedIn($type);

		$this->set(compact('type'));
	}

	private function _get_location_data($alias) {
		$options = $this->Location->getCountriesList();
		$selected = null;
		if($this->request->is(array('post', 'put'))) {
			if(!Module::active('AccurateLocationDatabase')) {
				$selected = @$this->request->data[$alias]['TargettedLocations'];
			} else {
				if(isset($this->request->data['AccurateTargettedLocations'])) {
					$tald = ClassRegistry::init('AccurateLocationDatabase.AccurateLocationDatabaseLocation');
					foreach($this->request->data['AccurateTargettedLocations'] as &$l) {
						$data = $tald->getListByParentName($l['country']);
						$l['country_regions'] = array_combine($data, $data);
						$data = $tald->getListByParentName($l['region']);
						$l['region_cities'] = array_combine($data, $data);
					}
				}
			}
		}
		return array($options, $selected);
	}

	private function _get_memberships_data() {
		$options = $this->Membership->getList();
		$selected = null;

		if($this->request->is(array('post', 'put'))) {
			$selected = $this->request->data['TargettedMemberships'];
		}

		return array($options, $selected);
	}

	private function _create_packets_list($alias, $data, $gateways) {
		$packages = array();
		$prices = array();

		foreach($data as $d) {
			$packages[$d[$alias]['id']] = __('%d %s for %s', $d[$alias]['amount'], __($d[$alias]['type']), CurrencyFormatter::format($d[$alias]['price']));

			foreach($gateways as $k => $v) {
				$prices[$d[$alias]['id']][$k] = array(
					'price' => $d[$alias]['price'],
					'withFee' => bcadd($d[$alias]['price'], $this->Payments->getDepositFee($d[$alias]['price'], $k)),
				);
			}
		};

		return array($packages, $prices);
	}

	private function _ads_buy() {
		$titleMax = $this->Ad->validate['title']['maxLength']['rule'][1];
		$descMax = $this->Ad->validate['description']['maxLength']['rule'][1];

		$this->AdsCategory->contain('AdsCategoryPackage');
		$options = $this->AdsCategory->find('all', array(
			'fields' => array(
				'AdsCategory.id',
				'AdsCategory.name',
				'AdsCategory.allow_description',
				'AdsCategory.geo_targetting',
			),
			'conditions' => array(
				'AdsCategory.status' => 'Active',
			),
			'order' => 'AdsCategory.position',
		));

		$options = array_filter($options, function($v) {
			if(empty($v['AdsCategoryPackage'])) {
				return false;
			}
			return true;
		});

		list($locations, $selectedLocations) = $this->_get_location_data('Ad');
		list($memberships, $selectedMemberships) = $this->_get_memberships_data();

		$gateways = $this->Payments->getActiveDepositsHumanized(false);
		$categories = Hash::combine($options, '{n}.AdsCategory.id', '{n}.AdsCategory.name');
		$packages = array();
		$prices = array();

		foreach($options as &$option) {
			foreach($option['AdsCategoryPackage'] as $pack) {
				$packages[$option['AdsCategory']['id']][$pack['id']] = __('%d %s for %s', $pack['amount'], __($pack['type']), CurrencyFormatter::format($pack['price']));
				foreach($gateways as $k => $v) {
					$prices[$pack['id']][$k] = array(
						'withFee' => bcadd($pack['price'], $this->Payments->getDepositFee($pack['price'], $k)),
						'price' => $pack['price'],
					);
				}
			}
		}

		$options = Hash::combine($options, '{n}.AdsCategory.id', '{n}.AdsCategory');

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['Ad']['status'] = 'Unpaid';

			$gateway = $this->request->data['gateway'];
			unset($this->request->data['gateway']);

			$email = $this->request->data['email'];
			unset($this->request->data['email']);

			if(!array_key_exists($gateway, $gateways)) {
				throw new NotFoundException(__d('exception', 'Invalid gateway'));
			}

			if(!array_key_exists($this->request->data['Ad']['ads_category_package_id'], $packages[$this->request->data['Ad']['ads_category_id']])) {
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}

			if($this->AnonymousAdvertiser->save(array('AnonymousAdvertiser' => array('email' => $email)))) {
				$this->request->data['Ad']['anonymous_advertiser_id'] = $this->AnonymousAdvertiser->id;
				if($this->Ad->save($this->request->data)) {
					return $this->Payments->pay('Ad', $gateway, $prices[$this->request->data['Ad']['ads_category_package_id']][$gateway]['price'], null, array(
						'model' => 'Ad',
						'ad_id' => $this->Ad->id,
						'package_id' => $this->request->data['Ad']['ads_category_package_id'],
					));
				}
			}

			$this->Notice->error(__('Failed to save ad data. Please, try again.'));
			$this->request->data['gateway'] = $gateway;
			$this->request->data['email'] = $email;
		}

		$this->set(array(
			'ads_categories' => $categories,
			'ads_category_packages' => $packages,
		));

		$this->set(compact('titleMax', 'descMax', 'options', 'locations', 'selectedLocations', 'gateways', 'memberships', 'selectedMemberships', 'prices'));
	}

	private function _ad_grid_buy() {
		$gateways = $this->Payments->getActiveDepositsHumanized(false);
		$this->AdGridAdsPackage->recursive = -1;
		$data = $this->AdGridAdsPackage->find('all');

		list($packages, $prices) = $this->_create_packets_list($this->AdGridAdsPackage->alias, $data, $gateways);

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['AdGridAd']['status'] = 'Unpaid';

			$gateway = $this->request->data['gateway'];
			unset($this->request->data['gateway']);

			$email = $this->request->data['email'];
			unset($this->request->data['email']);

			if(!array_key_exists($gateway, $gateways)) {
				throw new NotFoundException(__d('exception', 'Invalid gateway'));
			}

			$package_id = $this->request->data['AdGridPackageId'];
			unset($this->request->data['AdGridPackageId']);

			if(!array_key_exists($package_id, $packages)) {
				throw new NotFoundException(__d('exception', 'Invalid package id'));
			}

			if($this->AnonymousAdvertiser->save(array('AnonymousAdvertiser' => array('email' => $email)))) {
				$this->request->data['AdGridAd']['anonymous_advertiser_id'] = $this->AnonymousAdvertiser->id;
				$adGridAd = ClassRegistry::init('AdGrid.AdGridAd');
				if($adGridAd->save($this->request->data)) {
					return $this->Payments->pay('Ad', $gateway, $prices[$package_id][$gateway]['price'], null, array(
						'model' => 'AdGrid.AdGridAd',
						'ad_id' => $adGridAd->id,
						'package_id' => $package_id,
					));
				}
			}

			$this->Notice->error(__('Failed to save ad data. Please, try again.'));

			$this->request->data['gateway'] = $gateway;
			$this->request->data['email'] = $email;
			$this->request->data['AdGridPackageId'] = $package_id;
		}

		$this->set(compact('packages', 'gateways', 'prices'));
	}

	private function _banner_ads_buy() {
		$gateways = $this->Payments->getActiveDepositsHumanized(false);
		$this->BannerAdsPackage->recursive = -1;
		$data = $this->BannerAdsPackage->find('all');

		list($packages, $prices) = $this->_create_packets_list($this->BannerAdsPackage->alias, $data, $gateways);

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['BannerAd']['status'] = 'Unpaid';

			$gateway = $this->request->data['gateway'];
			unset($this->request->data['gateway']);

			$email = $this->request->data['email'];
			unset($this->request->data['email']);

			if(!array_key_exists($gateway, $gateways)) {
				throw new NotFoundException(__d('exception', 'Invalid gateway'));
			}

			$package_id = $this->request->data['BannerAdPackageId'];
			unset($this->request->data['BannerAdPackageId']);

			if(!array_key_exists($package_id, $packages)) {
				throw new NotFoundException(__d('exception', 'Invalid package id'));
			}

			$adModel = ClassRegistry::init('BannerAd');

			if($this->AnonymousAdvertiser->save(array('AnonymousAdvertiser' => array('email' => $email)))) {
				$this->request->data['BannerAd']['anonymous_advertiser_id'] = $this->AnonymousAdvertiser->id;
				if($adModel->save($this->request->data)) {
					return $this->Payments->pay('Ad', $gateway, $prices[$package_id][$gateway]['price'], null, array(
						'model' => 'BannerAd',
						'ad_id' => $adModel->id,
						'package_id' => $package_id,
					));
				}
			}

			$this->Notice->error(__('Failed to save ad data. Please, try again.'));

			$this->request->data['gateway'] = $gateway;
			$this->request->data['email'] = $email;
			$this->request->data['BannerAdPackageId'] = $package_id;
		}

		$this->set('titleMax', $this->Settings->fetchOne('bannerAdsTitleMaxLen', 128));
		$this->set(compact('packages', 'gateways', 'prices'));
	}

	private function _explorer_ads_buy() {
		$gateways = $this->Payments->getActiveDepositsHumanized(false);
		$this->ExplorerAdsPackage->recursive = -1;
		$data = $this->ExplorerAdsPackage->find('all');

		$alias = $this->ExplorerAdsPackage->alias;
		$packages = array();
		$prices = array();

		foreach($data as $d) {
			$packages[$d[$alias]['id']] = __('%d %s on %d SubPages for %s', $d[$alias]['amount'], __($d[$alias]['type']), $d[$alias]['subpages'], CurrencyFormatter::format($d[$alias]['price']));

			foreach($gateways as $k => $v) {
				$prices[$d[$alias]['id']][$k] = array(
					'price' => $d[$alias]['price'],
					'withFee' => bcadd($d[$alias]['price'], $this->Payments->getDepositFee($d[$alias]['price'], $k)),
				);
			}
		};

		list($locations, $selectedLocations) = $this->_get_location_data('Ad');
		list($memberships, $selectedMemberships) = $this->_get_memberships_data();

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['ExplorerAd']['status'] = 'Unpaid';

			$gateway = $this->request->data['gateway'];
			unset($this->request->data['gateway']);

			$email = $this->request->data['email'];
			unset($this->request->data['email']);

			if(!array_key_exists($gateway, $gateways)) {
				throw new NotFoundException(__d('exception', 'Invalid gateway'));
			}

			if(!array_key_exists($this->request->data['ExplorerAd']['explorer_ads_package_id'], $packages)) {
				throw new NotFoundException(__d('exception', 'Invalid package id'));
			}

			$adModel = ClassRegistry::init('ExplorerAd');

			if($this->AnonymousAdvertiser->save(array('AnonymousAdvertiser' => array('email' => $email)))) {
				$this->request->data['ExplorerAd']['anonymous_advertiser_id'] = $this->AnonymousAdvertiser->id;
				if($adModel->save($this->request->data)) {
					return $this->Payments->pay('Ad', $gateway, $prices[$this->request->data['ExplorerAd']['explorer_ads_package_id']][$gateway]['price'], null, array(
						'model' => 'ExplorerAd',
						'ad_id' => $adModel->id,
						'package_id' => $this->request->data['ExplorerAd']['explorer_ads_package_id'],
					));
				}
			}

			$this->Notice->error(__('Failed to save ad data. Please, try again.'));

			$this->request->data['gateway'] = $gateway;
			$this->request->data['email'] = $email;
		}

		$settings = $this->Settings->fetchOne('explorerAds', null);

		if(!$settings) {
			throw new NotFoundException(__d('exception', 'Missing configuration'));
		}

		$this->set(compact('gateways', 'prices', 'packages', 'locations', 'selectedLocations', 'memberships', 'selectedMemberships', 'settings'));
	}

	private function _express_ads_buy() {
		$gateways = $this->Payments->getActiveDepositsHumanized(false);
		$this->ExpressAdsPackage->recursive = -1;
		$data = $this->ExpressAdsPackage->find('all');

		list($packages, $prices) = $this->_create_packets_list($this->ExpressAdsPackage->alias, $data, $gateways);
		list($locations, $selectedLocations) = $this->_get_location_data('Ad');
		list($memberships, $selectedMemberships) = $this->_get_memberships_data();

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['ExpressAd']['status'] = 'Unpaid';

			$gateway = $this->request->data['gateway'];
			unset($this->request->data['gateway']);

			$email = $this->request->data['email'];
			unset($this->request->data['email']);

			if(!array_key_exists($gateway, $gateways)) {
				throw new NotFoundException(__d('exception', 'Invalid gateway'));
			}

			if(!array_key_exists($this->request->data['ExpressAd']['express_ads_package_id'], $packages)) {
				throw new NotFoundException(__d('exception', 'Invalid package id'));
			}

			$adModel = ClassRegistry::init('ExpressAd');

			if($this->AnonymousAdvertiser->save(array('AnonymousAdvertiser' => array('email' => $email)))) {
				$this->request->data['ExpressAd']['anonymous_advertiser_id'] = $this->AnonymousAdvertiser->id;
				if($adModel->save($this->request->data)) {
					return $this->Payments->pay('Ad', $gateway, $prices[$this->request->data['ExpressAd']['express_ads_package_id']][$gateway]['price'], null, array(
						'model' => 'ExpressAd',
						'ad_id' => $adModel->id,
						'package_id' => $this->request->data['ExpressAd']['express_ads_package_id'],
					));
				}
			}

			$this->Notice->error(__('Failed to save ad data. Please, try again.'));
			$this->request->data['gateway'] = $gateway;
			$this->request->data['email'] = $email;
		}

		$settings = $this->Settings->fetchOne('expressAds', null);

		if(!$settings) {
			throw new NotFoundException(__d('exception', 'Missing configuration'));
		}

		$this->set(compact('gateways', 'prices', 'packages', 'locations', 'selectedLocations', 'memberships', 'selectedMemberships', 'settings'));
	}

	private function _featured_ads_buy() {
		$gateways = $this->Payments->getActiveDepositsHumanized(false);
		$this->FeaturedAdsPackage->recursive = -1;
		$data = $this->FeaturedAdsPackage->find('all');

		list($packages, $prices) = $this->_create_packets_list($this->FeaturedAdsPackage->alias, $data, $gateways);

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['FeaturedAd']['status'] = 'Unpaid';

			$gateway = $this->request->data['gateway'];
			unset($this->request->data['gateway']);

			$email = $this->request->data['email'];
			unset($this->request->data['email']);

			if(!array_key_exists($gateway, $gateways)) {
				throw new NotFoundException(__d('exception', 'Invalid gateway'));
			}

			$package_id = $this->request->data['FeaturedAdPackageId'];
			unset($this->request->data['FeaturedAdPackageId']);

			if(!array_key_exists($package_id, $packages)) {
				throw new NotFoundException(__d('exception', 'Invalid package id'));
			}

			$adModel = ClassRegistry::init('FeaturedAd');

			if($this->AnonymousAdvertiser->save(array('AnonymousAdvertiser' => array('email' => $email)))) {
				$this->request->data['FeaturedAd']['anonymous_advertiser_id'] = $this->AnonymousAdvertiser->id;
				if($adModel->save($this->request->data)) {
					return $this->Payments->pay('Ad', $gateway, $prices[$package_id][$gateway]['price'], null, array(
						'model' => 'FeaturedAd',
						'ad_id' => $adModel->id,
						'package_id' => $package_id,
					));
				}
			}

			$this->Notice->error(__('Failed to save ad data. Please, try again.'));

			$this->request->data['gateway'] = $gateway;
			$this->request->data['email'] = $email;
			$this->request->data['FeaturedAdPackageId'] = $package_id;
		}

		$this->set('titleMax', $this->Settings->fetchOne('featuredAdsTitleMaxLen', 128));
		$this->set('descMax', $this->Settings->fetchOne('featuredAdsDescMaxLen', 128));
		$this->set(compact('packages', 'gateways', 'prices'));
	}

	private function _login_ads_buy() {
		$gateways = $this->Payments->getActiveDepositsHumanized(false);
		$this->LoginAdsPackage->recursive = -1;
		$data = $this->LoginAdsPackage->find('all');

		list($packages, $prices) = $this->_create_packets_list($this->LoginAdsPackage->alias, $data, $gateways);

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['LoginAd']['status'] = 'Unpaid';

			$gateway = $this->request->data['gateway'];
			unset($this->request->data['gateway']);

			$email = $this->request->data['email'];
			unset($this->request->data['email']);

			if(!array_key_exists($gateway, $gateways)) {
				throw new NotFoundException(__d('exception', 'Invalid gateway'));
			}

			$package_id = $this->request->data['LoginAdPackageId'];
			unset($this->request->data['LoginAdPackageId']);

			if(!array_key_exists($package_id, $packages)) {
				throw new NotFoundException(__d('exception', 'Invalid package id'));
			}

			$adModel = ClassRegistry::init('LoginAd');

			if($this->AnonymousAdvertiser->save(array('AnonymousAdvertiser' => array('email' => $email)))) {
				$this->request->data['LoginAd']['anonymous_advertiser_id'] = $this->AnonymousAdvertiser->id;
				if($adModel->save($this->request->data)) {
					return $this->Payments->pay('Ad', $gateway, $prices[$package_id][$gateway]['price'], null, array(
						'model' => 'LoginAd',
						'ad_id' => $adModel->id,
						'package_id' => $package_id,
					));
				}
			}

			$this->Notice->error(__('Failed to save ad data. Please, try again.'));

			$this->request->data['gateway'] = $gateway;
			$this->request->data['email'] = $email;
			$this->request->data['LoginAdPackageId'] = $package_id;
		}

		$this->set('titleMax', $this->Settings->fetchOne('loginAdsTitleMaxLen', 128));
		$this->set(compact('packages', 'gateways', 'prices'));
	}

	public function buy($type = null) {
		if($type === null || !in_array($type, $this->acceptedTypes)) {
			return $this->redirect(array('action' => 'index'));
		}

		$this->_redirectIfLoggedIn($type);

		$methodName = '_'.$type.'_buy';

		if(method_exists($this, $methodName)) {
			$this->$methodName();
			$this->render($type.'_buy');
		} else {
			throw new NotFoundException(__d('exception', 'Missing method'));
		}
	}

	public function statistics($type = null, $ad_id = null) {
		if($type === null || !in_array($type, $this->acceptedTypes)) {
			return $this->redirect(array('action' => 'index'));
		}

		$model = array_search($type, $this->acceptedTypes, true);
		list(, $alias) = pluginSplit($model);

		$stats = ClassRegistry::init($model)->getStatistics($ad_id);

		if(empty($stats)) {
			throw new NotFoundException(__d('exception', 'Invalid ad'));
		}

		$this->set(compact('stats', 'type', 'alias'));
	}
}
