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
class BoughtItemsController extends AppController {
	public $components = array(
		'Paginator',
	);

	private $models = array(
		'AdsCategoryPackage' => 'PTC',
		'FeaturedAdsPackage' => 'Featured Ads',
		'BannerAdsPackage' => 'Banner Ads',
		'LoginAdsPackage' => 'Login Ads',
		'PaidOffersPackage' => 'Paid Offers',
		'ExpressAdsPackage' => 'Express Ads',
		'ExplorerAdsPackage' => 'Explorer Ads',
	);

	public function beforeFilter() {
		parent::beforeFilter();

		foreach($this->models as $k => &$v) {
			$v = __d('admin', $v);
		}

		if(Module::installed('AdGrid')) {
			$this->models['AdGrid.AdGridAdsPackage'] = __d('admin', 'AdGrid');
		}
	}

	public function admin_view($user_id = null) {
		if($user_id === null) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}

		$this->BoughtItem->User->contain();
		$user = $this->BoughtItem->User->findById($user_id);

		if(empty($user)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}

		$items = $this->Paginator->paginate(array('BoughtItem.user_id' => $user_id));

		$packages = array();

		foreach($this->models as $name => $title) {
			$model = ClassRegistry::init($name);
			list(,$alias) = pluginSplit($name);

			$packages[$name] = Hash::combine($model->find('all'), '{n}.'.$alias.'.id', '{n}.'.$alias);
		}

		$cats = ClassRegistry::init('AdsCategory')->find('list');

		foreach($items as &$item) {
			$name = $item['BoughtItem']['model'];
			$data = $packages[$name][$item['BoughtItem']['foreign_key']];

			if($name == 'PaidOffersPackage') {
				$item['BoughtItem']['title'] = sprintf('%d Paid Offers slots for %s', $data['quantity'], CurrencyFormatter::format($data['value']));
			} elseif($name == 'AdsCategoryPackage') {
				$item['BoughtItem']['title'] = sprintf('%d %s for %s', $data['amount'], __($data['type']), $cats[$data['ads_category_id']]);
			} elseif($name == 'ExplorerAdsPackage') {
				$item['BoughtItem']['title'] = sprintf('%d %s for %s subpages', $data['amount'], __($d['type']), $d['subpages']);
			} else {
				$item['BoughtItem']['title'] = sprintf('%d %s for %s', $data['amount'], __($data['type']), $this->models[$name]);
			}
		}

		$this->set(compact('user', 'items'));
	}

	public function admin_add($username = null) {
		$packages = array();

		foreach($this->models as $name => $title) {
			$model = ClassRegistry::init($name);

			$data = $model->find('all');

			if($name == 'PaidOffersPackage') {
				foreach($data as $d) {
					$packages[$this->models[$name]][$name.'-'.$d[$name]['id']] = sprintf('%d Paid Offers slots for %s', $d[$name]['quantity'], CurrencyFormatter::format($d[$name]['value']));
				}
			} elseif($name == 'AdsCategoryPackage') {
				$cats = ClassRegistry::init('AdsCategory')->find('list');
				foreach($data as $d) {
					$packages[$this->models[$name]][$name.'-'.$d[$name]['id']] = sprintf('%d %s for %s', $d[$name]['amount'], __($d[$name]['type']), $cats[$d[$name]['ads_category_id']]);
				}
			} elseif($name == 'ExplorerAdsPackage') {
				foreach($data as $d) {
					$packages[$this->models[$name]][$name.'-'.$d[$name]['id']] = sprintf('%d %s for %s subpages', $d[$name]['amount'], __($d[$name]['type']), $d[$name]['subpages']);
				}
			} else {
				list(,$alias) = pluginSplit($name);
				foreach($data as $d) {
					$packages[$this->models[$name]][$name.'-'.$d[$alias]['id']] = sprintf('%d %s for %s', $d[$alias]['amount'], __($d[$alias]['type']), $this->models[$name]);
				}
			}
		}

		if($this->request->is(array('post', 'put'))) {
			$toSave = array();

			if(!is_numeric($this->request->data['quantity'])) {
				throw new InternalErrorException(__d('exception', 'Invalid quantity'));
			}

			$this->BoughtItem->User->contain();
			$user = $this->BoughtItem->User->findByUsername($this->request->data['username']);

			list($model, $foreign_key) = split('-', $this->request->data['package']);

			for($i = 0; $i < $this->request->data['quantity']; $i++) {
				$toSave[] = array(
					'user_id' => $user['User']['id'],
					'model' => $model,
					'foreign_key' => $foreign_key,
				);
			}

			if($this->BoughtItem->saveMany($toSave)) {
				$this->Notice->success(__d('admin', 'Items saved successfully'));
				return $this->redirect($this->referer());
			} else {
				$this->Notice->error(__d('admin', 'Failed to save items. Please, try again.'));
			}
		}
		if($username) {
			$this->request->data['username'] = $username;
		}
		$this->set(compact('packages'));
	}

	public function admin_delete($id = null) {
		$this->BoughtItem->id = $id;

		if(!$id || !$this->BoughtItem->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid item'));
		}

		if($this->BoughtItem->delete()) {
			$this->Notice->success(__d('admin', 'Item sucessfully deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to delete item. Please, try again.'));
		}

		$this->redirect($this->referer());
	}

	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {

			if(!isset($this->request->data['BoughtItems']) || empty($this->request->data['BoughtItems'])) {
				$this->Notice->error(__d('admin', 'Please select at least one ad.'));
				return $this->redirect($this->referer());
			}

			$ads = 0;
			foreach($this->request->data['BoughtItems'] as $id => $on) {
				if($on) {
					$ads++;
					if(!$this->BoughtItem->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid item'));
					}
				}
			}

			foreach($this->request->data['BoughtItems'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'delete':
							$this->BoughtItem->delete($id);
						break;
					}
				}
			}
			if($ads) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one item.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect($this->referer());
	}
}
