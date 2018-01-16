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
App::uses('ModelBehavior', 'Model');
App::uses('Folder', 'Utility');

class AdsBehavior extends ModelBehavior {
/**
 * acceptedTypes
 *
 * @var array
 */
	private $acceptedTypes = array(
		'Ad' => 'ads',
		'BannerAd' => 'banner_ads',
		'ExplorerAd' => 'explorer_ads',
		'ExpressAd' => 'express_ads',
		'FeaturedAd' => 'featured_ads',
		'LoginAd' => 'login_ads',
		'AdGridAd' => 'ad_grid',
	);

/**
 * bindAdvertiserName method
 *
 * @return void
 */
	public function bindAdvertiserName(Model $model) {
		$model->virtualFields['advertiser_name'] = "CASE WHEN {$model->alias}.advertiser_id IS NOT NULL THEN Advertiser.username ELSE (CASE WHEN {$model->alias}.anonymous_advertiser_id IS NOT NULL THEN AnonymousAdvertiser.email ELSE '".__('Admin')."' END) END";
	}

/**
 * unbindAdvertiserName method
 *
 * @return void
 */
	public function unbindAdvertiserName(Model $model) {
		if(isset($model->virtualFields['advertiser_name'])) {
			unset($model->virtualFields['advertiser_name']);
		}
	}

/**
 * beforeFind callback
 *
 * @return void
 */
	public function beforeFind(Model $model, $query) {
		if($model->Behaviors->attached('Containable')) {
			$contain = array($model->AnonymousAdvertiser->alias => array('id', 'email'), $model->Advertiser->alias => array('id', 'username'));

			if(isset($query['contain']) && is_array($query['contain'])) {
				$contain = array_merge($contain, $query['contain']);
			}

			if(is_array($query['fields'])) {
				if(empty($query['fields']) || in_array('advertiser_name', $query['fields']) || in_array($model->alias.'.advertiser_name', $query['fields'])) {
					$query['contain'] = $contain;
					$this->bindAdvertiserName($model);
				}
			} elseif($query['fields'] == 'advertiser_name' || $query['fields'] == $model->alias.'.advertiser_name') {
				$query['contain'] = $contain;
				$this->bindAdvertiserName($model);
			}
		} else {
			if($model->recursive <= -1) {
				if(is_array($query['fields'])) {
					if(empty($query['fields']) || in_array('advertiser_name', $query['fields']) || in_array($model->alias.'.advertiser_name', $query['fields'])) {
						$model->recursive = 1;
						$this->bindAdvertiserName($model);
					}
				} elseif($query['fields'] == 'advertiser_name' || $query['fields'] == $model->alias.'.advertiser_name') {
					$model->recursive = 1;
					$this->bindAdvertiserName($model);
				}
			}
		}
		return $query;
	}

/**
 * afterFind callback
 *
 * @return void
 */
	public function afterFind(Model $model, $results, $primary = false) {
		$this->unbindAdvertiserName($model);
	}

/**
 * beforeDelete callback
 *
 * @return void
 */
	public function beforeDelete(Model $model, $cascade = true) {
		$this->checkStatusChange($model, $model->id, 'Ad declined');
		$this->deleteAnonymousAdvertiser($model);
		return true;
	}

/**
 * beforeSave callback
 *
 * @return boolean
 */
	public function beforeSave(Model $model, $options = array()) {
		if(isset($model->data[$model->alias]['id']) && isset($model->data[$model->alias]['status'])) {
			$this->checkStatusChange($model, $model->data[$model->alias]['id'], 'Ad approve');
		}
		return true;
	}

/**
 * sendStatusChangeNotification method
 *
 * @return void
 */
	protected function sendStatusChangeNotification($user, $title, $status, $type) {
		$email = ClassRegistry::init('Email');
		$email->setVariables(array(
			'%username%' => $user['username'],
			'%firstname%' => $user['first_name'],
			'%lastname%' => $user['last_name'],
			'%adstatus%' => __($status),
			'%adtitle%' => $title,
		));

		$email->send($type, $user['email']);
	}

/**
 * checkStatusChange method
 *
 * @return void
 */
	protected function checkStatusChange(Model $model, $id, $notifyType) {
		$fields = array($model->displayField, 'advertiser_id', 'anonymous_advertiser_id', 'status');

		$oldData = $model->findById($id, $fields);
		$oldStatus = $oldData[$model->alias]['status'];
		$data = Hash::merge($oldData, $model->data);

		if($oldStatus == 'Pending' && $oldStatus != $data[$model->alias]['status']) {
			if($data[$model->alias]['advertiser_id'] !== null) {
				$adver = $model->Advertiser->findById($data[$model->alias]['advertiser_id'], array('username' , 'first_name', 'last_name', 'email', 'role', 'allow_emails'));

				if(!empty($user) && $user[$model->Advertiser->alias]['role'] == 'Active' && $user[$model->Advertiser->alias]['allow_emails']) {
					$user = $adver[$model->Advertiser->alias];
				}
			} elseif($data[$model->alias]['anonymous_advertiser_id'] !== null) {
				$model->AnonymousAdvertiser->recursive = -1;

				$anon = $model->AnonymousAdvertiser->findById($data[$model->alias]['anonymous_advertiser_id'], array('email'));
				if(!empty($anon)) {
					$user = array(
						'username' => $anon[$model->AnonymousAdvertiser->alias]['email'],
						'email' => $anon[$model->AnonymousAdvertiser->alias]['email'],
						'first_name' => '',
						'last_name' => '',
					);
				}
			}

			if(isset($user)) {
				$this->sendStatusChangeNotification($user, $data[$model->alias][$model->displayField], $data[$model->alias]['status'], $notifyType);
			}
		}
	}

/**
 * activate method
 *
 * @return boolean
 */
	public function activate(Model $model, $id = null) {
		if($id) {
			$model->id = $id;
		}

		return $model->saveField('status', 'Active');
	}

/**
 * inactivate method
 *
 * @return boolean
 */
	public function inactivate(Model $model, $id = null) {
		if($id) {
			$model->id = $id;
		}

		return $model->saveField('status', 'Inactive');
	}

/**
 * getStatusesList method
 *
 * @return array
 */
	public function getStatusesList(Model $model) {
		return array(
			'Inactive' => __('Inactive'),
			'Pending' => __('Pending'),
			'Active' => __('Active'),
			'Unpaid' => __('Unpaid'),
		);
	}

/**
 * notifyBuy
 *
 * @return void
 */
	public function notifyBuy(Model $model, $ad_id) {
		$model->recursive = -1;
		$ad = $model->findById($ad_id, array('anonymous_advertiser_id'));

		if(empty($ad)) {
			throw new InternalErrorException(__d('exception', 'Ad not found'));
		}

		$model->AnonymousAdvertiser->recursive = -1;
		$anon = $model->AnonymousAdvertiser->findById($ad[$model->alias]['anonymous_advertiser_id'], array('email'));

		$type = $this->acceptedTypes[$model->alias];

		$email = ClassRegistry::init('Email');
		$email->setVariables(array(
			'%outsidestats%' => Router::url(array('admin' => false, 'plugin' => null, 'controller' => 'advertise', 'action' => 'statistics', $type, $ad_id), true),
		));

		$email->send('Outside advertiser notification', $anon[$model->AnonymousAdvertiser->alias]['email']);
	}

/**
 * deleteAnonymousAdvertiser
 *
 * @return void
 */
	public function deleteAnonymousAdvertiser(Model $model) {
		$ad = $model->findById($model->id, array('anonymous_advertiser_id'));

		if(empty($ad) || $ad[$model->alias]['anonymous_advertiser_id']) {
			$model->AnonymousAdvertiser->id = $ad[$model->alias]['anonymous_advertiser_id'];
			$model->AnonymousAdvertiser->delete();
		}
	}
}
