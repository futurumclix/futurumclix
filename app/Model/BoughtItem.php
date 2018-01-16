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
App::uses('AppModel', 'Model');
/**
 * BoughtItem Model
 *
 * @property User $User
 * @property Deposit $Deposit
 */
class BoughtItem extends AppModel {
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'model' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 100),
				'allowEmpty' => false,
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

/**
 * sumPackages method
 *
 * @return array
 */
	public function sumPackages($alias, $user_id = null, $default = array('Clicks' => 0, 'Days' => 0)) {
		if($user_id === null) {
			$user_id = CakeSession::read('Auth.User.id');
		}

		list($plugin, $model) = pluginSplit($alias);

		$data = $this->find('all', array(
			'fields' => array('SUM('.$model.'.amount) as sum', $model.'.type'),
			'conditions' => array(
				'model' => $alias,
				'user_id' => $user_id,
			),
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => Inflector::tableize($model),
					'alias' => $model,
					'type' => 'INNER',
					'conditions' => array(
						$this->alias.'.foreign_key = '.$model.'.id',
					),
				),
			),
			'group' => array(
				$model.'.type',
			),
		));

		return Hash::combine($data, '{n}.'.$model.'.type', '{n}.0.sum') + $default;
	}

/**
 * sumPTCPackages method
 *
 * @return array
 */
	public function sumPTCPackages($user_id = null) {
		return $this->sumPackages('AdsCategoryPackage', $user_id);
	}

/**
 * sumFeaturedAdsPackages method
 *
 * @return array
 */
	public function sumFeaturedAdsPackages($user_id = null) {
		return $this->sumPackages('FeaturedAdsPackage', $user_id, array(
			'Days' => 0,
			'Clicks' => 0,
			'Impressions' => 0,
		));
	}

/**
 * sumBannerAdsPackages method
 *
 * @return array
 */
	public function sumBannerAdsPackages($user_id = null) {
		return $this->sumPackages('BannerAdsPackage', $user_id, array(
			'Days' => 0,
			'Clicks' => 0,
			'Impressions' => 0,
		));
	}

/**
 * sumBannerAdsPackages method
 *
 * @return array
 */
	public function sumLoginAdsPackages($user_id = null) {
		return $this->sumPackages('LoginAdsPackage', $user_id);
	}

/**
 * sumPaidOffersPackages method
 *
 * @return array
 */
	public function sumPaidOffersPackages($user_id = null) {
		if($user_id === null) {
			$user_id = CakeSession::read('Auth.User.id');
		}

		$data = $this->find('all', array(
			'fields' => array('SUM(`PaidOffersPackage`.`quantity`) as sum'),
			'conditions' => array(
				'model' => 'PaidOffersPackage',
				'user_id' => $user_id,
			),
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => 'paid_offers_packages',
					'alias' => 'PaidOffersPackage',
					'type' => 'INNER',
					'conditions' => array(
						$this->alias.'.foreign_key = `PaidOffersPackage`.`id`',
					),
				),
			),
		));

		return $data[0][0]['sum'] ?  array('Slots' => $data[0][0]['sum']) : array('Slots' => 0);
	}

/**
 * sumExpressAdsPackages method
 *
 * @return array
 */
	public function sumExpressAdsPackages($user_id = null) {
		return $this->sumPackages('ExpressAdsPackage', $user_id);
	}

/**
 * sumExpressAdsPackages method
 *
 * @return array
 */
	public function sumExplorerAdsPackages($user_id = null) {
		return $this->sumPackages('ExplorerAdsPackage', $user_id);
	}
}
