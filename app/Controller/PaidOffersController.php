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
class PaidOffersController extends AppController {
/**
 * components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		'UserPanel',
		'Payments',
		'Report',
		'Location',
	);

/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'PaidOffer',
		'PaidOffersCategory',
		'PaidOffersValue',
		'PaidOffersPackage',
		'PaidOffersApplication',
	);

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		if($this->request->prefix != 'admin') {
			if(!Configure::read('paidOffersActive')) {
				throw new NotFoundException(__d('exception', 'Paid offers are disabled'));
			}

			if($this->request->params['action'] == 'view' || $this->request->params['action'] == 'applicationAdd') {
				$settings = $this->Settings->fetchOne('paidOffers');

				$this->User->contain();
				$user = $this->User->findById($this->Auth->user('id'), array('rejected_applications'));

				if($user['User']['rejected_applications'] >= $settings['banApplications']) {
					$this->Notice->info(__('You have been banned from Paid Offers.'));
					return $this->redirect(array('plugin' => null, 'controller' => 'users', 'action' => 'dashboard'));
				}
			}
		}

		if($this->request->params['action'] == 'admin_settings') {
			$start = $this->PaidOffersCategory->find('count');

			if(isset($this->request->data['PaidOffersCategory'])) {
				$stop = count($this->request->data['PaidOffersCategory']);
			} else {
				$stop = 30;
			}

			for(;$start < $stop; $start++) {
				$this->Security->unlockedFields[] = 'PaidOffersCategory.'.$start.'.name';
			}

			$start = $this->PaidOffersValue->find('count');

			if(isset($this->request->data['PaidOffersValue'])) {
				$stop = count($this->request->data['PaidOffersValue']);
			} else {
				$stop = 30;
			}

			for(;$start < $stop; $start++) {
				$this->Security->unlockedFields[] = 'PaidOffersValue.'.$start.'.value';
			}

			$start = $this->PaidOffersPackage->find('count');

			if(isset($this->request->data['PaidOffersPackage'])) {
				$stop = count($this->request->data['PaidOffersPackage']);
			} else {
				$stop = 30;
			}

			for(;$start < $stop; $start++) {
				$this->Security->unlockedFields[] = 'PaidOffersPackage.'.$start.'.value';
				$this->Security->unlockedFields[] = 'PaidOffersPackage.'.$start.'.quantity';
				$this->Security->unlockedFields[] = 'PaidOffersPackage.'.$start.'.price';
			}
		}

		if(Module::active('AccurateLocationDatabase')) {
			if(in_array($this->request->params['action'], array('admin_add', 'admin_edit', 'edit', 'add'))) {
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
	}

	public function admin_settings() {
		$keys = array(
			'paidOffers',
		);
		$globalKeys = array(
			'paidOffersActive',
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

			if(isset($this->request->data['PaidOffersCategory'])) {
				if($this->PaidOffersCategory->saveMany($this->request->data['PaidOffersCategory'])) {
					$this->Notice->success(__d('admin', 'Paid offers categories saved successfully.'));
				} else {
					$this->Notice->error(__d('admin', 'Failed to save paid offers categories. Please, try again.'));
				}
			}

			if(isset($this->request->data['PaidOffersValue'])) {
				if($this->PaidOffersValue->saveMany($this->request->data['PaidOffersValue'])) {
					$this->Notice->success(__d('admin', 'Paid offers values saved successfully.'));
				} else {
					$this->Notice->error(__d('admin', 'Failed to save paid offers values. Please, try again.'));
				}
			}

			if(isset($this->request->data['PaidOffersPackage'])) {
				if($this->PaidOffersPackage->saveMany($this->request->data['PaidOffersPackage'])) {
					$this->Notice->success(__d('admin', 'Paid offers packages saved successfully.'));
				} else {
					$this->Notice->error(__d('admin', 'Failed to save paid offers packages. Please, try again.'));
				}
			}
		}
		$settings = $this->Settings->fetch('paidOffers');

		foreach($globalKeys as $k) {
			$settings['Settings'][$k] = Configure::read($k);
		}

		$values = $this->PaidOffersValue->find('all');

		$this->request->data = Hash::merge($settings, $this->request->data);
		$this->request->data = Hash::merge(array('PaidOffersCategory' => Hash::extract($this->PaidOffersCategory->find('all'), '{n}.PaidOffersCategory', 'PaidOffersCategory')), $this->request->data);
		$this->request->data = Hash::merge(array('PaidOffersValue' => Hash::extract($values, '{n}.PaidOffersValue', 'PaidOffersValue')), $this->request->data);
		$this->request->data = Hash::merge(array('PaidOffersPackage' => Hash::extract($this->PaidOffersPackage->find('all'), '{n}.PaidOffersPackage', 'PaidOffersPackage')), $this->request->data);

		$packageValues = array();
		foreach($values as $v) {
			$packageValues[$v['PaidOffersValue']['value']] = CurrencyFormatter::format($v['PaidOffersValue']['value']);
		}
		$this->set(compact('packageValues'));
	}

	public function admin_deleteCategory($id = null) {
		$this->PaidOffersCategory->id = $id;

		if(!$this->PaidOffersCategory->exists()) {
			throw new InternalErrorException(__d('exception', 'Invalid category'));
		}

		if($this->PaidOffersCategory->delete()) {
			$this->Notice->success(__d('admin', 'Paid offers category successfully deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to delete paid offers category. Please, try again.'));
		}

		$this->redirect($this->referer());
	}

	public function admin_deleteValue($id = null) {
		$this->PaidOffersValue->id = $id;

		if(!$this->PaidOffersValue->exists()) {
			throw new InternalErrorException(__d('exception', 'Invalid value'));
		}

		if($this->PaidOffersValue->delete()) {
			$this->Notice->success(__d('admin', 'Paid offers value successfully deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to delete paid offers value. Please, try again.'));
		}

		$this->redirect($this->referer());
	}

	public function admin_deletePackage($id = null) {
		$this->PaidOffersPackage->id = $id;

		if(!$this->PaidOffersPackage->exists()) {
			throw new InternalErrorException(__d('exception', 'Invalid package'));
		}

		if($this->PaidOffersPackage->delete()) {
			$this->Notice->success(__d('admin', 'Paid offers package successfully deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to delete paid offers package. Please, try again.'));
		}

		$this->redirect($this->referer());
	}

	public function admin_index() {
		$conditions = $this->createPaginatorConditions(array(
			'Advertiser.username',
			'PaidOffer.title',
			'PaidOffer.url',
			'PaidOffer.status',
		));

		if(isset($conditions['Advertiser.username LIKE']) && $conditions['Advertiser.username LIKE'] == '%Admin%') {
			unset($conditions['Advertiser.username LIKE']);
			$conditions['PaidOffer.advertiser_id'] = null;
		}

		$this->PaidOffer->contain(array('Advertiser'));
		$this->paginate = array('order' => 'PaidOffer.created DESC');
		$offers = $this->Paginator->paginate($conditions);

		$statuses = $this->PaidOffer->getStatusesList();

		$this->set(compact('statuses', 'offers'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if(!$this->PaidOffer->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->PaidOffer->delete($id)) {
			$this->Notice->success(__d('admin', 'The offer has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The offer could not be deleted. Please, try again.'));
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
		if(!$this->PaidOffer->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->PaidOffer->inactivate($id)) {
			$this->Notice->success(__d('admin', 'The offer has been inactivated.'));
		} else {
			$this->Notice->error(__d('admin', 'The offer could not be inactivated. Please, try again.'));
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
		if(!$this->PaidOffer->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}
		$this->request->allowMethod(array('post', 'delete'));
		if($this->PaidOffer->activate($id)) {
			$this->Notice->success(__d('admin', 'The offer has been activated.'));
		} else {
			$this->Notice->error(__d('admin', 'The offer could not be activated. Please, try again.'));
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

			if(!isset($this->request->data['PaidOffer']) || empty($this->request->data['PaidOffer'])) {
				$this->Notice->error(__d('admin', 'Please select at least one offer.'));
				return $this->redirect($this->referer());
			}

			$ads = 0;
			foreach($this->request->data['PaidOffer'] as $id => $on) {
				if($on) {
					$ads++;
					if(!$this->PaidOffer->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid offer'));
					}
				}
			}

			foreach($this->request->data['PaidOffer'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'inactivate':
							$this->PaidOffer->inactivate($id);
						break;

						case 'activate':
							$this->PaidOffer->activate($id);
						break;

						case 'delete':
							$this->PaidOffer->delete($id);
						break;
					}
				}
			}
			if($ads) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one offer.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect($this->referer());
	}

	public function admin_add() {
		$settings = $this->Settings->fetchOne('paidOffers');

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['Advertiser']['username'])) {
				$this->PaidOffer->Advertiser->contain();
				$advertiser = $this->PaidOffer->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));

				if(empty($advertiser)) {
					return $this->Notice->error(__d('admin', 'Advertiser not found.'));
				}

				$this->request->data['PaidOffer']['advertiser_id'] = $advertiser['Advertiser']['id'];

				unset($this->request->data['Advertiser']);
			} else {
				$this->request->data['PaidOffer']['advertiser_id'] = null;
			}

			$this->request->data['PaidOffer']['status'] = 'Active';

			if($this->PaidOffer->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'Paid Offer saved successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to save paid offer. Please, try again.'));
			}
		}

		if(!Module::active('AccurateLocationDatabase')) {
			$selectedCountries = isset($this->request->data['TargettedLocations']) ? $this->request->data['TargettedLocations'] : null;
		} else {
			$tald = ClassRegistry::init('AccurateLocationDatabase.AccurateLocationDatabaseLocation');
			if(isset($this->request->data['AccurateTargettedLocations'])) {
				foreach($this->request->data['AccurateTargettedLocations'] as &$l) {
					$data = $tald->getListByParentName($l['country']);
					$l['country_regions'] = array_combine($data, $data);
					$data = $tald->getListByParentName($l['region']);
					$l['region_cities'] = array_combine($data, $data);
				}
			}
			$selectedCountries = null;
		}

		$titleMax = $settings['titleLength'];
		$descMax = $settings['descLength'];
		$categories = $this->PaidOffersCategory->find('list');
		$values = $this->PaidOffersValue->find('all', array('fields' => 'value'));
		$values = Hash::combine($values, '{n}.PaidOffersValue.value', '{n}.PaidOffersValue.value');
		$memberships = $this->PaidOffer->TargettedMemberships->find('list');
		$countries = $this->Location->getCountriesList();

		$this->set(compact('titleMax', 'descMax', 'categories', 'values', 'memberships', 'countries', 'selectedCountries'));
	}

	public function admin_edit($id = null) {
		$this->PaidOffer->contain(array('Advertiser', 'TargettedMemberships', 'TargettedLocations'));
		$offer = $this->PaidOffer->findById($id);

		if(empty($offer)) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}
		$settings = $this->Settings->fetchOne('paidOffers');

		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['Advertiser']['username'])) {
				$this->PaidOffer->Advertiser->contain();
				$advertiser = $this->PaidOffer->Advertiser->findByUsername($this->request->data['Advertiser']['username'], array('id'));

				if(empty($advertiser)) {
					return $this->Notice->error(__d('admin', 'Advertiser not found.'));
				}

				$this->request->data['PaidOffer']['advertiser_id'] = $advertiser['Advertiser']['id'];

				unset($this->request->data['Advertiser']);
			} else {
				$this->request->data['PaidOffer']['advertiser_id'] = null;
			}

			if($this->PaidOffer->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'Paid offer saved successfully'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to save paid offer. Please, try again.'));
			}
		} else {
			$this->request->data = $offer;
		}

		if(!Module::active('AccurateLocationDatabase')) {
			$selectedCountries = $this->request->data['TargettedLocations'];
		} else {
			$tald = ClassRegistry::init('AccurateLocationDatabase.AccurateLocationDatabaseLocation');
			foreach($this->request->data['AccurateTargettedLocations'] as &$l) {
				$data = $tald->getListByParentName($l['country']);
				$l['country_regions'] = array_combine($data, $data);
				$data = $tald->getListByParentName($l['region']);
				$l['region_cities'] = array_combine($data, $data);
			}
			$selectedCountries = null;
		}

		$titleMax = $settings['titleLength'];
		$descMax = $settings['descLength'];
		$statuses = $this->PaidOffer->getStatusesList();
		$categories = $this->PaidOffersCategory->find('list');
		$values = $this->PaidOffersValue->find('all', array('fields' => 'value'));
		$values = Hash::combine($values, '{n}.PaidOffersValue.value', '{n}.PaidOffersValue.value');
		$memberships = $this->PaidOffer->TargettedMemberships->find('list');
		$countries = $this->Location->getCountriesList();

		$this->set(compact('titleMax', 'descMax', 'statuses', 'categories', 'values', 'memberships', 'countries', 'selectedCountries'));
	}

	public function admin_applications() {
		$conditions = $this->createPaginatorConditions(array(
			'Advertiser.username',
			'Applicant.username',
			'PaidOffer.title',
			'PaidOffer.url',
			'PaidOfferApplication.status',
		));

		if(isset($conditions['Advertiser.username LIKE'])) {
			if($conditions['Advertiser.username LIKE'] == '%Admin%') {
				$conditions['PaidOffer.advertiser_id'] = null;
			} else {
				$advertiser = $conditions['Advertiser.username LIKE'];
			}
			unset($conditions['Advertiser.username LIKE']);
		}

		$this->PaidOffersApplication->contain(array(
			'PaidOffer' => array('title', 'url', 'value', 'Advertiser.username'),
			'Applicant.username',
		));
		$this->paginate = array('order' => 'PaidOffersApplication.created DESC');
		$applications = $this->Paginator->paginate('PaidOffersApplication', $conditions);

		$statuses = $this->PaidOffersApplication->enum('status');
		$this->set(compact('statuses', 'applications'));
	}

/**
 * admin_applicationsMassaction method
 *
 * @throws NotFoundException
 * @return void
 */
	public function admin_applicationsMassaction() {
		if(!empty($this->request->data['Action'])) {

			if(!isset($this->request->data['PaidOffersApplication']) || empty($this->request->data['PaidOffersApplication'])) {
				$this->Notice->error(__d('admin', 'Please select at least one application.'));
				return $this->redirect($this->referer());
			}

			$ads = 0;
			foreach($this->request->data['PaidOffersApplication'] as $id => $on) {
				if($on) {
					$ads++;
					if(!$this->PaidOffersApplication->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid application'));
					}
				}
			}

			foreach($this->request->data['PaidOffersApplication'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'accept':
							$this->PaidOffersApplication->accept($id);
						break;

						case 'reject':
							$this->PaidOffersApplication->reject($id);
						break;

						case 'delete':
							$this->PaidOffersApplication->delete($id);
						break;
					}
				}
			}
			if($ads) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one application.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect($this->referer());
	}

	public function admin_applicationAccept($id = null) {
		$this->request->allowMethod(array('post', 'put'));
		$this->PaidOffersApplication->id = $id;

		if(!$this->PaidOffersApplication->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid application'));
		}

		if($this->PaidOffersApplication->accept()) {
			$this->Notice->success(__d('admin', 'Application successfully accepted.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to accept application. Please, try again later.'));
		}
		return $this->redirect($this->referer());
	}

	public function admin_applicationReject($id = null) {
		$this->request->allowMethod(array('post', 'put'));
		$this->PaidOffersApplication->id = $id;

		if(!$this->PaidOffersApplication->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid application'));
		}

		if($this->request->is(array('post', 'put')) && isset($this->request->data['reason'])) {
			$reason = $this->request->data['reason'];
		} else {
			$reason = null;
		}

		if($this->PaidOffersApplication->reject($id, $reason)) {
			$this->Notice->success(__d('admin', 'Application successfully rejected.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to reject application. Please, try again later.'));
		}
		return $this->redirect($this->referer());
	}

	public function admin_applicationDetails($id = null) {
		$this->layout = 'ajax';
		$this->request->allowMethod(array('ajax', 'post', 'put'));

		$this->PaidOffersApplication->contain();
		$app = $this->PaidOffersApplication->findById($id, array('description'));

		if(empty($app)) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}

		$description = $app['PaidOffersApplication']['description'];

		if($this->request->is(array('post', 'put'))) {
			switch($this->request->data['status']) {
				case PaidOffersApplication::ACCEPTED:
					if(!$this->PaidOffersApplication->accept($id)) {
						throw new InternalErrorException(__d('exception', 'Failed to save application.'));
					}
				break;

				case PaidOffersApplication::REJECTED:
					if(!$this->PaidOffersApplication->reject($id, $this->request->data['reason'])) {
						throw new InternalErrorException(__d('exception', 'Failed to save application.'));
					}
				break;

				case PaidOffersApplication::PENDING:
					$this->PaidOffersApplication->id = $id;
					if(!$this->PaidOffersApplication->saveField('status', PaidOffersApplication::PENDING)) {
						throw new InternalErrorException(__d('exception', 'Failed to save application.'));
					}
				break;
			}
		}

		$this->set(compact('id', 'description'));
	}

	public function index() {
		$this->PaidOffer->contain(array(
			'Category',
		));
		$offers = $this->PaidOffer->find('all', array(
			'conditions' => array(
				'PaidOffer.advertiser_id' => $this->Auth->user('id'),
			),
		));

		$ads_no = $this->PaidOffer->find('count', array(
			'conditions' => array(
				'PaidOffer.status' => 'Active',
				'PaidOffer.advertiser_id' => $this->Auth->user('id'),
			),
		));

		$this->set('breadcrumbTitle', __('Paid Offers Panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->User->BoughtItems->sumPaidOffersPackages());
		$this->set(compact('offers', 'ads_no'));
	}

	public function add() {
		$settings = $this->Settings->fetchOne('paidOffers');

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['PaidOffer']['advertiser_id'] = $this->Auth->user('id');

			if($settings['autoApprove']) {
				$this->request->data['PaidOffer']['status'] = 'Active';
			} else {
				$this->request->data['PaidOffer']['status'] = 'Pending';
			}

			if($this->PaidOffer->save($this->request->data)) {
				$this->Notice->success(__('Paid offer saved successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to save paid offer. Please, try again.'));
			}
		}

		if(!Module::active('AccurateLocationDatabase')) {
			$selectedCountries = isset($this->request->data['TargettedLocations']) ? $this->request->data['TargettedLocations'] : null;
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
			$selectedCountries = null;
		}

		$titleMax = $settings['titleLength'];
		$descMax = $settings['descLength'];
		$categories = $this->PaidOffersCategory->find('list');
		$memberships = $this->PaidOffer->TargettedMemberships->find('list');
		$countries = $this->Location->getCountriesList();

		$ads_no = $this->PaidOffer->find('count', array(
			'conditions' => array(
				'PaidOffer.status' => 'Active',
				'PaidOffer.advertiser_id' => $this->Auth->user('id'),
			),
		));

		$this->set('breadcrumbTitle', __('Paid Offers Panel'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->User->BoughtItems->sumPaidOffersPackages());
		$this->set(compact('offers', 'ads_no', 'titleMax', 'descMax', 'categories', 'memberships', 'countries', 'selectedCountries'));
	}

	public function edit($id = null) {
		$this->PaidOffer->contain(array(
			'TargettedMemberships.id',
			'TargettedLocations',
		));

		$offer = $this->PaidOffer->find('first', array(
			'conditions' => array(
				'PaidOffer.id' => $id,
				'PaidOffer.advertiser_id' => $this->Auth->user('id'),
			),
		));

		if(empty($offer)) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}

		$settings = $this->Settings->fetchOne('paidOffers');

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['PaidOffer']['advertiser_id'] = $this->Auth->user('id');

			if($settings['autoApprove']) {
				$this->request->data['PaidOffer']['status'] = 'Active';
			} else {
				$this->request->data['PaidOffer']['status'] = 'Pending';
			}

			if($this->PaidOffer->save($this->request->data)) {
				$this->Notice->success(__('Paid offer saved successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to save Paid Offer. Please, try again.'));
			}
		} else {
			$this->request->data = $offer;
		}

		if(!Module::active('AccurateLocationDatabase')) {
			$selectedCountries = $this->request->data['TargettedLocations'];
		} else {
			$tald = ClassRegistry::init('AccurateLocationDatabase.AccurateLocationDatabaseLocation');
			foreach($this->request->data['AccurateTargettedLocations'] as &$l) {
				$data = $tald->getListByParentName($l['country']);
				$l['country_regions'] = array_combine($data, $data);
				$data = $tald->getListByParentName($l['region']);
				$l['region_cities'] = array_combine($data, $data);
			}
			$selectedCountries = null;
		}

		$titleMax = $settings['titleLength'];
		$descMax = $settings['descLength'];
		$categories = $this->PaidOffersCategory->find('list');
		$memberships = $this->PaidOffer->TargettedMemberships->find('list');
		$countries = $this->Location->getCountriesList();

		$ads_no = $this->PaidOffer->find('count', array(
			'conditions' => array(
				'PaidOffer.status' => 'Active',
				'PaidOffer.advertiser_id' => $this->Auth->user('id'),
			),
		));

		$this->set('breadcrumbTitle', __('Paid Offers'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->User->BoughtItems->sumPaidOffersPackages());
		$this->set(compact('offers', 'ads_no', 'titleMax', 'descMax', 'categories', 'memberships', 'countries', 'selectedCountries'));
	}

/**
 * activate method
 *
 * @return void
 */
	public function activate($adId = null) {
		$this->request->allowMethod('post', 'delete');

		$this->PaidOffer->id = $adId;
		$this->PaidOffer->contain();
		$ad = $this->PaidOffer->find('first', array(
			'fields' => array('PaidOffer.id'),
			'recursive' => -1,
			'conditions' => array(
				'PaidOffer.id' => $adId,
				'PaidOffer.advertiser_id' => $this->Auth->user('id'),
				'PaidOffer.status' => 'Inactive',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}

		if($this->PaidOffer->activate($adId)) {
			$this->Notice->success(__('Offer successfully activated.'));
		} else {
			$this->Notice->error(__('Error while activating offer. Please try again.'));
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

		$this->PaidOffer->id = $adId;
		$this->PaidOffer->contain();
		$ad = $this->PaidOffer->find('first', array(
			'fields' => array('PaidOffer.id'),
			'recursive' => -1,
			'conditions' => array(
				'PaidOffer.id' => $adId,
				'PaidOffer.advertiser_id' => $this->Auth->user('id'),
				'PaidOffer.status' => 'Active',
			)
		));

		if(empty($ad)) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}

		if($this->PaidOffer->inactivate($adId)) {
			$this->Notice->success(__('Offer successfully paused.'));
		} else {
			$this->Notice->error(__('Error while pausing offer. Please try again.'));
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * delete method
 *
 * @return void
 */
	public function delete($id = null) {
		$user_id = $this->Auth->user('id');

		if(!$id || !$user_id) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}

		$this->PaidOffer->contain();
		$ad = $this->PaidOffer->find('first', array(
			'fields' => array('PaidOffer.id'),
			'conditions' => array(
				'PaidOffer.id' => $id,
				'PaidOffer.advertiser_id' => $user_id,
			),
		));

		if(empty($ad)) {
			throw new NotFoundExcption(__('Invalid offer'));
		}

		if($this->PaidOffer->delete($id)) {
			$this->Notice->success(__('Offer deleted.'));
		} else {
			$this->Notice->error(__('Failed to delete offer. Please try again.'));
		}

		return $this->redirect(array('action' => 'index'));
	}

	public function buy() {
		$user_id = $this->Auth->user('id');

		$activeGateways = $this->Payments->getActiveDepositsHumanized();

		$this->PaidOffer->contain();
		$ads_no = $this->PaidOffer->find('count', array(
			'conditions' => array(
				'PaidOffer.status' => 'Active',
				'PaidOffer.advertiser_id' => $this->Auth->user('id'),
			),
		));

		$this->PaidOffersPackage->recursive = -1;
		$packagesData = $this->PaidOffersPackage->find('all', array(
			'order' => 'PaidOffersPackage.price DESC',
		));

		$packages = array();
		foreach($packagesData as &$v) {
			$prices = array();
			$add = false;

			foreach($activeGateways as $k => $g) {
				$price = bcadd($v['PaidOffersPackage']['price'], $this->Payments->getDepositFee($v['PaidOffersPackage']['price'], $k));
				if($this->Payments->checkMinimumDepositAmount($k, $price)) {
					$prices[$k] = $price;
					$add = true;
				} else {
					$prices[$k] = 'disabled';
				}
			}

			if($add) {
				$packages[$v['PaidOffersPackage']['id']] = __('Value %s - %s slots - %s', CurrencyFormatter::format($v['PaidOffersPackage']['value']), $v['PaidOffersPackage']['quantity'], CurrencyFormatter::format($v['PaidOffersPackage']['price']));
				$v['PaidOffersPackage']['price_per'] = bcdiv($v['PaidOffersPackage']['price'], $v['PaidOffersPackage']['quantity']);
				$v['PaidOffersPackage']['prices'] = $prices;
			}
		}
		$packagesData = Hash::combine($packagesData, '{n}.PaidOffersPackage.id', '{n}');

		if($this->request->is('post')) {
			$pack_id = $this->request->data['package_id'];

			if(!isset($packagesData[$pack_id])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}

			if($this->Payments->checkMinimumDepositAmount($this->request->data['gateway'], $packagesData[$pack_id]['PaidOffersPackage']['price'])) {
				$this->Payments->pay('PaidOffersPackage', $this->request->data['gateway'], $packagesData[$pack_id]['PaidOffersPackage']['price'],
				 $this->Auth->user('id'), array('package_id' => $pack_id));
			} else {
				$this->Notice->error(__('Minimum deposit amount for %s is %s.', $this->request->data['gateway'], CurrencyFormatter::format($this->Payments->getMinimumDepositAmount($this->request->data['gateway']))));
			}
		}

		$this->set(compact('ads_no', 'categories', 'packages', 'activeGateways', 'packagesData'));
		$this->set('breadcrumbTitle', __('Paid Offers'));
		$this->set('packsSum', $this->User->BoughtItems->sumPaidOffersPackages());
		$this->set('user', $this->UserPanel->getData());
	}

	public function assign() {
		$user_id = $this->Auth->user('id');

		$packetsData = ClassRegistry::init('BoughtItem')->find('all', array(
			'fields' => array('BoughtItem.id', 'PaidOffersPackage.id', 'PaidOffersPackage.quantity', 'PaidOffersPackage.value'),
			'recursive' => -1,
			'conditions' => array(
				'BoughtItem.user_id' => $user_id,
				'BoughtItem.model' => 'PaidOffersPackage',
			),
			'joins' => array(
				array(
					'table' => 'paid_offers_packages',
					'alias' => 'PaidOffersPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = PaidOffersPackage.id',
					),
				),
			),
		));

		$ads = $this->PaidOffer->find('list', array(
			'conditions' => array(
				'PaidOffer.advertiser_id' => $user_id,
				'PaidOffer.status !=' => 'Pending',
				'PaidOffer.slots_left' => 0,
			),
		));

		if($this->request->is(array('post', 'put'))) {
			if(!isset($ads[$this->request->data['ad_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid offer'));
			}
			$packetsData = Hash::combine($packetsData, '{n}.BoughtItem.id', '{n}');
			if(!isset($packetsData[$this->request->data['package_id']]) || empty($packetsData[$this->request->data['package_id']])) {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}
			if($this->PaidOffer->assignPack($packetsData[$this->request->data['package_id']], $this->request->data['ad_id'])) {
				$this->Notice->success(__('Package assigned successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		$this->PaidOffer->contain();
		$ads_no = $this->PaidOffer->find('count', array(
			'conditions' => array(
				'PaidOffer.advertiser_id' => $user_id,
				'PaidOffer.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('%s slots %s value', $v['PaidOffersPackage']['quantity'], CurrencyFormatter::format($v['PaidOffersPackage']['value']));
		}

		$this->set(compact('ads_no', 'packages', 'ads'));
		$this->set('breadcrumbTitle', __('Banner ads panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumPaidOffersPackages());
		$this->set('user', $this->UserPanel->getData());
	}

	public function assignTo($id = null) {
		$user_id = $this->Auth->user('id');

		$this->PaidOffer->contain();
		$ad = $this->PaidOffer->find('first', array(
			'conditions' => array(
				'PaidOffer.id' => $id,
				'PaidOffer.advertiser_id' => $user_id,
				'PaidOffer.status !=' => 'Pending',
			),
		));

		if(empty($ad)) {
			return $this->redirect(array('action' => 'assign'));
		}

		$packetsData = ClassRegistry::init('BoughtItem')->find('all', array(
			'fields' => array('BoughtItem.id', 'PaidOffersPackage.id', 'PaidOffersPackage.quantity', 'PaidOffersPackage.value'),
			'recursive' => -1,
			'conditions' => array(
				'BoughtItem.user_id' => $user_id,
				'BoughtItem.model' => 'PaidOffersPackage',
				'PaidOffersPackage.value' => $ad['PaidOffer']['value'],
			),
			'joins' => array(
				array(
					'table' => 'paid_offers_packages',
					'alias' => 'PaidOffersPackage',
					'type' => 'INNER',
					'conditions' => array(
						'BoughtItem.foreign_key = PaidOffersPackage.id',
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
			if($this->PaidOffer->addPack($packetsData[$this->request->data['package_id']], $id)) {
				$this->Notice->success(__('Package assigned successfully.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__('Failed to assign package. Please try again.'));
			}
		}

		$this->PaidOffer->contain();
		$ads_no = $this->PaidOffer->find('count', array(
			'conditions' => array(
				'PaidOffer.advertiser_id' => $user_id,
				'PaidOffer.status' => 'Active',
			)
		));

		$packages = array();
		foreach($packetsData as $v) {
			$packages[$v['BoughtItem']['id']] = __('slots: %d value: %s', $v['PaidOffersPackage']['quantity'], CurrencyFormatter::format($v['PaidOffersPackage']['value']));
		}

		$this->set(compact('ads_no', 'packages', 'ad'));
		$this->set('breadcrumbTitle', __('Paid Offers panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumPaidOffersPackages());
		$this->set('user', $this->UserPanel->getData());
	}

	public function view() {
		$user = $this->UserPanel->getData(array(
			'PendingApplication' => array('PaidOffer' => array('Category', 'value', 'title')),
			'RejectedApplication' => array('PaidOffer' => array('Category', 'value', 'title')),
			'AcceptedApplication' => array('PaidOffer' => array('Category', 'value', 'title')),
			'IgnoredOffer',
		));

		$ignoredOffersIds = Hash::extract($user, 'IgnoredOffer.{n}.offer_id');

		$this->PaidOffer->contain();
		$offers = $this->PaidOffer->find('all', array(
			'fields' => array(
				'PaidOffer.id',
				'PaidOffer.title',
				'PaidOffer.description',
				'PaidOffer.url',
				'PaidOffer.value',
				'PaidOffer.created',
				'Category.name',
			),
			'joins' => array(
				array(
					'table' => 'memberships_paid_offers',
					'alias' => 'TargettedMemberships',
					'type' => 'INNER',
					'conditions' => array(
						'TargettedMemberships.paid_offer_id = PaidOffer.id',
					),
				),
				array(
					'table' => 'paid_offers_targetted_locations',
					'alias' => 'TargettedLocations',
					'type' => 'INNER',
					'conditions' => array(
						'TargettedLocations.offer_id = PaidOffer.id',
					),
				),
				array(
					'table' => 'paid_offers_categories',
					'alias' => 'Category',
					'type' => 'LEFT',
					'conditions' => array(
						'Category.id = PaidOffer.category_id',
					),
				),
				array(
					'table' => 'paid_offers_applications',
					'alias' => 'PaidOffersApplications',
					'type' => 'LEFT',
					'conditions' => array(
						'PaidOffersApplications.offer_id = PaidOffer.id',
						'PaidOffersApplications.user_id = '.$user['User']['id'],
					),
				),
			),
			'conditions' => array(
				'PaidOffer.id !=' => $ignoredOffersIds,
				'PaidOffer.status' => 'Active',
				'TargettedMemberships.membership_id' => $user['ActiveMembership']['membership_id'],
				'PaidOffersApplications.id IS NULL',
				'OR' => array(
					'PaidOffer.advertiser_id !=' => $this->Auth->user('id'),
					'PaidOffer.advertiser_id IS NULL',
				),
				'PaidOffer.slots_left >' => 0,
				$this->Location->getConditions($user['User']['location']),
			),
		));

		$this->PaidOffer->contain();
		$ignoredOffers = $this->PaidOffer->find('all', array(
			'fields' => array(
				'PaidOffer.id',
				'PaidOffer.title',
				'PaidOffer.description',
				'PaidOffer.value',
				'PaidOffer.created',
				'Category.name',
			),
			'joins' => array(
				array(
					'table' => 'memberships_paid_offers',
					'alias' => 'TargettedMemberships',
					'type' => 'INNER',
					'conditions' => array(
						'TargettedMemberships.paid_offer_id = PaidOffer.id',
					),
				),
				array(
					'table' => 'paid_offers_targetted_locations',
					'alias' => 'TargettedLocations',
					'type' => 'INNER',
					'conditions' => array(
						'TargettedLocations.offer_id = PaidOffer.id',
					),
				),
				array(
					'table' => 'paid_offers_categories',
					'alias' => 'Category',
					'type' => 'LEFT',
					'conditions' => array(
						'Category.id = PaidOffer.category_id',
					),
				),
				array(
					'table' => 'paid_offers_applications',
					'alias' => 'PaidOffersApplications',
					'type' => 'LEFT',
					'conditions' => array(
						'PaidOffersApplications.offer_id = PaidOffer.id',
						'PaidOffersApplications.user_id = '.$user['User']['id'],
					),
				),
			),
			'conditions' => array(
				'PaidOffer.id' => $ignoredOffersIds,
				'PaidOffer.status' => 'Active',
				'TargettedMemberships.membership_id' => $user['ActiveMembership']['membership_id'],
				'PaidOffersApplications.id IS NULL',
				'OR' => array(
					'PaidOffer.advertiser_id !=' => $this->Auth->user('id'),
					'PaidOffer.advertiser_id IS NULL',
				),
				'PaidOffer.slots_left >' => 0,
				$this->Location->getConditions($user['User']['location']),
			),
		));

		$ads_no = $this->PaidOffer->find('count', array(
			'conditions' => array(
				'PaidOffer.status' => 'Active',
				'PaidOffer.advertiser_id' => $this->Auth->user('id'),
			),
		));

		$this->set('breadcrumbTitle', __('Paid Offers Panel'));
		$this->set('packsSum', $this->User->BoughtItems->sumPaidOffersPackages());
		$this->set(compact('user', 'offers', 'ads_no', 'ignoredOffers'));
	}

	public function ignore($id = null) {
		$this->request->allowMethod(array('post', 'put'));
		$this->PaidOffer->contain();
		$offer = $this->PaidOffer->find('first', array(
			'fields' => array('id'),
			'conditions' => array(
				'OR' => array(
					'PaidOffer.advertiser_id !=' => $this->Auth->user('id'),
					'PaidOffer.advertiser_id IS NULL',
				),
				'PaidOffer.status' => 'Active',
				'PaidOffer.slots_left >' => 0,
				'PaidOffer.id' => $id,
			),
		));

		if(empty($offer)) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}

		$data = array(
			'IgnoringUser' => array(
				'user_id' => $this->Auth->user('id'),
				'offer_id' => $id,
			),
		);

		if($this->PaidOffer->IgnoringUser->save($data)) {
			$this->Notice->success(__('Offer successfully added to ignore list.'));
		} else {
			$this->Notice->error(__('Failed to add offer to ignore list. Please, try again later.'));
		}

		return $this->redirect($this->referer());
	}

	public function unignore($id = null) {
		$this->request->allowMethod(array('post', 'put'));
		$this->PaidOffer->contain();
		$offer = $this->PaidOffer->find('first', array(
			'fields' => array('id'),
			'conditions' => array(
				'OR' => array(
					'PaidOffer.advertiser_id !=' => $this->Auth->user('id'),
					'PaidOffer.advertiser_id IS NULL',
				),
				'PaidOffer.status' => 'Active',
				'PaidOffer.slots_left >' => 0,
				'PaidOffer.id' => $id,
			),
		));

		if(empty($offer)) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}

		$res = $this->PaidOffer->IgnoringUser->deleteAll(array(
			'user_id' => $this->Auth->user('id'),
			'offer_id' => $id,
		));

		if($res) {
			$this->Notice->success(__('Offer successfully removed from ignore list.'));
		} else {
			$this->Notice->error(__('Failed to remove offer from ignore list. Please, try again later.'));
		}

		return $this->redirect($this->referer());
	}

/**
 * report method
 *
 * @return void
 */
	public function report($id = null) {
		$this->layout = 'ajax';
		$this->request->allowMethod(array('ajax', 'post', 'put'));

		$this->PaidOffer->contain();
		$offer = $this->PaidOffer->exists($id);

		if(!$offer) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}

		if($this->request->is(array('post', 'put'))) {
			if(!$this->Report->reportItem($this->request->data['type'], $this->PaidOffer, $id, $this->request->data['reason'], $this->Auth->user('id'))) {
				throw new InternalErrorException(__d('exception', 'Failed to save report.'));
			}
		}

		$this->set(compact('id'));
	}

/**
 * applicationAdd method
 *
 * @return void
 */
	public function applicationAdd($id = null) {
		$this->layout = 'ajax';
		$this->request->allowMethod(array('ajax', 'post', 'put'));

		$this->PaidOffer->contain();
		$offer = $this->PaidOffer->findById($id, array('title', 'description'));

		if(empty($offer)) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['PaidOffersApplication']['user_id'] = $this->Auth->user('id');
			$this->request->data['PaidOffersApplication']['offer_id'] = $id;

			if(!isset($this->request->data['PaidOffersApplication']['description']) || empty($this->request->data['PaidOffersApplication']['description'])) {
				throw new BadRequestException(__d('exception', 'Please provide description.'));
			}

			if(!$this->PaidOffer->getSlot($id)) {
				throw new InternalErrorException(__d('exception', 'Failed to save application.'));
			}

			if(!$this->PaidOffersApplication->save($this->request->data)) {
				throw new InternalErrorException(__d('exception', 'Failed to save application.'));
			}
		}

		$this->set(compact('id', 'offer'));
	}

	public function applications($id = null, $type = null) {
		if($type == null) {
			$contain = array(
				'PendingApplication' => array('Applicant' => 'username'),
				'AcceptedApplication' => array('Applicant' => 'username'),
				'RejectedApplication' => array('Applicant' => 'username'),
			);
		} elseif($type == 'pending') {
			$contain = array(
				'PendingApplication' => array('Applicant' => 'username'),
			);
		} elseif($type == 'accepted') {
			$contain = array(
				'AcceptedApplication' => array('Applicant' => 'username'),
			);
		} elseif($type == 'rejected') {
			$contain = array(
				'RejectedApplication' => array('Applicant' => 'username'),
			);
		}

		$this->PaidOffer->contain($contain);
		$offer = $this->PaidOffer->find('first', array(
			'fields' => array('id', 'title'),
			'conditions' => array(
				'PaidOffer.id' => $id,
				'PaidOffer.advertiser_id' => $this->Auth->user('id'),
				'PaidOffer.status !=' => 'Pending',
			),
		));

		if(empty($offer)) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}

		$applications = array();

		foreach(array_keys($contain) as $k) {
			$applications = array_merge($applications, $offer[$k]);
		}

		$this->PaidOffer->contain();
		$ads_no = $this->PaidOffer->find('count', array(
			'conditions' => array(
				'PaidOffer.advertiser_id' => $this->Auth->user('id'),
				'PaidOffer.status' => 'Active',
			)
		));

		$this->set('breadcrumbTitle', __('Paid Offers'));
		$this->set('user', $this->UserPanel->getData());
		$this->set('packsSum', $this->User->BoughtItems->sumPaidOffersPackages());
		$this->set(compact('offer', 'ads_no', 'applications'));
	}

	public function applicationAccept($id = null) {
		$this->request->allowMethod(array('post', 'put'));
		$this->PaidOffersApplication->contain(array('PaidOffer' => array('advertiser_id', 'status')));
		$offer = $this->PaidOffersApplication->find('first', array(
			'fields' => array('id'),
			'conditions' => array(
				'PaidOffersApplication.id' => $id,
				'PaidOffersApplication.status' => PaidOffersApplication::PENDING,
				'PaidOffer.advertiser_id' => $this->Auth->user('id'),
				'PaidOffer.status !=' => 'Pending',
			),
		));

		if(empty($offer)) {
			throw new NotFoundException(__d('exception', 'Invalid offer'));
		}

		if($this->PaidOffersApplication->accept($id)) {
			$this->Notice->success(__('Application successfully accepted.'));
		} else {
			$this->Notice->error(__('Failed to accept application. Please, try again later.'));
		}
		return $this->redirect($this->referer());
	}

	public function applicationReject($id = null) {
		$this->layout = 'ajax';
		$this->request->allowMethod(array('ajax', 'post', 'put'));
		$this->PaidOffersApplication->contain(array('PaidOffer' => array('advertiser_id', 'status')));
		$app = $this->PaidOffersApplication->find('first', array(
			'fields' => array('id', 'offer_id'),
			'conditions' => array(
				'PaidOffersApplication.id' => $id,
				'PaidOffersApplication.status' => PaidOffersApplication::PENDING,
				'PaidOffer.advertiser_id' => $this->Auth->user('id'),
				'PaidOffer.status !=' => 'Pending',
			),
		));

		if(empty($app)) {
			throw new NotFoundException(__d('exception', 'Invalid application'));
		}

		if($this->request->is(array('post', 'put'))) {
			if(!isset($this->request->data['reason']) || empty($this->request->data['reason'])) {
				throw new BadRequestException(__d('exception', 'Please provide reject reason.'));
			}

			if(!$this->PaidOffersApplication->reject($id, $this->request->data['reason'], $app['PaidOffersApplication']['offer_id'])) {
				throw new InternalErrorException(__d('exception', 'Failed to save application.'));
			}
		}

		$this->set(compact('id'));
	}
}
