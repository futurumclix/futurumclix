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
 * AdsCategoryPackage Model
 *
 * @property AdsCategory $AdsCategory
 */
class AdsCategoryPackagesController extends AppController {
/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'AdsCategoryPackage',
		'Ad',
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'UserPanel',
		'Payments',
	);

/**
 * buy method
 *
 * @return void
 */
	public function buy($cat_id = null) {
		$user_id = $this->Auth->user('id');

		$activeGateways = $this->Payments->getActiveDepositsHumanized();

		$ads_no = $this->Ad->find('count', array(
			'conditions' => array(
				'Ad.advertiser_id' => $user_id,
				'Ad.status' => 'Active',
			)
		));

		$this->AdsCategoryPackage->AdsCategory->recursive = -1;
		$categories = $this->AdsCategoryPackage->AdsCategory->find('list', array(
			'conditions' => array(
				'AdsCategory.status' => 'Active',
			),
		));
		$this->AdsCategoryPackage->recursive = -1;
		$packagesData = $this->AdsCategoryPackage->find('all', array(
			'order' => 'AdsCategoryPackage.price ASC',
		));

		$packages = array();
		foreach($packagesData as &$v) {
			$prices = array();
			$add = false;

			foreach($activeGateways as $k => $g) {
				$price = bcadd($v['AdsCategoryPackage']['price'], $this->Payments->getDepositFee($v['AdsCategoryPackage']['price'], $k));
				if($this->Payments->checkMinimumDepositAmount($k, $price)) {
					$prices[$k] = $price;
					$add = true;
				} else {
					$prices[$k] = 'disabled';
				}
			}

			if($add) {
				$packages[$v['AdsCategoryPackage']['ads_category_id']]['a'.$v['AdsCategoryPackage']['id']] = sprintf('%d %s - %s', $v['AdsCategoryPackage']['amount'],
				 __($v['AdsCategoryPackage']['type']), CurrencyFormatter::format($v['AdsCategoryPackage']['price']));
				$v['AdsCategoryPackage']['price_per'] = bcdiv($v['AdsCategoryPackage']['price'], $v['AdsCategoryPackage']['amount']);
				$v['AdsCategoryPackage']['prices'] = $prices;
			}
		}
		$packagesData = Hash::combine($packagesData, '{n}.AdsCategoryPackage.id', '{n}');

		foreach($categories as $cid => $name) {
			if(!isset($packages[$cid]) || empty($packages[$cid])) {
				unset($categories[$cid]);
			}
		}

		if($this->request->is('post')) {
			$pack_id = ltrim($this->request->data['package_id'], 'a');

			if(!isset($packagesData[$pack_id])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}

			if($this->Payments->checkMinimumDepositAmount($this->request->data['gateway'], $packagesData[$pack_id]['AdsCategoryPackage']['price'])) {
				$this->Payments->pay('PTCPackage', $this->request->data['gateway'], $packagesData[$pack_id]['AdsCategoryPackage']['price'],
				 $this->Auth->user('id'), array('package_id' => $pack_id));
			} else {
				$this->Notice->error(__('Minimum deposit amount for %s is %s.', $this->request->data['gateway'], CurrencyFormatter::format($this->Payments->getMinimumDepositAmount($this->request->data['gateway']))));
			}
		} else {
			if($cat_id !== null && isset($categories[$cat_id])) {
				$this->request->data['category_id'] = $cat_id;
			}
		}

		$this->set(compact('ads_no', 'categories', 'packages', 'activeGateways', 'packagesData'));
		$this->set('breadcrumbTitle', __('Advertisement panel'));
		$this->set('packsSum', ClassRegistry::init('User')->BoughtItems->sumPTCPackages());
		$this->set('user', $this->UserPanel->getData());
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if(!$this->AdsCategoryPackage->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid ad category package'));
		}
		$this->request->allowMethod('post', 'delete');
		if($this->AdsCategoryPackage->delete($id)) {
			$this->Notice->success(__d('admin', 'The ad category package has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The ad category package could not be deleted. Please, try again.'));
		}
		return $this->redirect($this->referer());
	}
}
