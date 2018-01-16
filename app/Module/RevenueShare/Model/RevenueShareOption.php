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
App::uses('RevenueShareAppModel', 'RevenueShare.Model');
/**
 * RevenueShareOption Model
 *
 * @property Membership $Membership
 * @property RevenueShareLimit $RevenueShareLimit
 */
class RevenueShareOption extends RevenueShareAppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'title';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'title' => array(
			'between' => array(
				'rule' => array('between', 1, 100),
				'message' => 'Title length should be between 1 and 100 characters.',
				'allowEmpty' => false,
			),
		),
		'running_days' => array(
			'range' => array(
				'rule' => array('range', 0, 65536),
				'message' => 'Runinng days should be between 1 and 65535.',
				'allowEmpty' => false,
			),
			'lessThanMax' => array(
				'rule' => array('comparisonWithField', '<=', 'running_days_max'),
				'message' => 'Running days should be less or equal running days max.'
			),
		),
		'running_days_max' => array(
			'range' => array(
				'rule' => array('range', 0, 65536),
				'message' => 'Runinng days should be between 1 and 65535.',
				'allowEmpty' => false,
			),
		),
		'price' => array(
			'userDefined' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Price should be a valid monetary value.',
				'allowEmpty' => false,
			),
		),
		'overall_return' => array(
			'bcrange' => array(
				'rule' => array('bcrange', '0', '1000'),
				'message' => 'Overall return should be a percentage value',
				'allowEmpty' => false,
			),
		),
		'step' => array(
			'natural' => array(
				'rule' => array('naturalNumber'),
				'message' => 'Step should be a natural number',
				'allowEmpty' => false,
			),
		),
	);

	public $hasMany = array(
		'Item' => array(
			'className' => 'RevenueShare.RevenueShareItemsOption',
			'dependent' => true,
		),
	);

	private $packagesToString = array(
		'AdsCategoryPackage' => array(
			'format' => '%s %s for PTC category "%s"',
			'params' => array(
				'AdsCategoryPackage.amount',
				'-AdsCategoryPackage.type',
				'AdsCategory.name',
			),
		),
		'BannerAdsPackage' => array(
			'format' => '%s %s for Banner Ads',
			'params' => array(
				'BannerAdsPackage.amount',
				'-BannerAdsPackage.type',
			),
		),
		'FeaturedAdsPackage' => array(
			'format' => '%s %s for Featured Ads',
			'params' => array(
				'FeaturedAdsPackage.amount',
				'-FeaturedAdsPackage.type',
			),
		),
		'LoginAdsPackage' => array(
			'format' => '%s %s for Login Ads',
			'params' => array(
				'LoginAdsPackage.amount',
				'-LoginAdsPackage.type',
			),
		),
		'PaidOffersPackage' => array(
			'format' => '%s application(s) worth %s for Paid Offers',
			'params' => array(
				'PaidOffersPackage.quantity',
				'$PaidOffersPackage.price',
			),
		),
		'AdGrid.AdGridAdsPackage' => array(
			'format' => '%s %s for AdGrid',
			'params' => array(
				'AdGridAdsPackage.amount',
				'-AdGridAdsPackage.type',
			),
		),
	);

	public function packageToString($modelName, $data) {
		if(!isset($this->packagesToString[$modelName])) {
			throw new InternalErrorException(__d('revenue_share', 'Missing conversion data'));
		}
		$currencyHelper = new CurrencyHelper(new View()); /* lame... */

		$format = $this->packagesToString[$modelName]['format'];
		$params = array();

		foreach($this->packagesToString[$modelName]['params'] as $param) {
			if($param{0} == '-') {
				$ex = Hash::extract($data, substr($param, 1));
				$ex = strtolower($ex[0]);
			} elseif($param{0} == '$') {
				$ex = Hash::extract($data, substr($param, 1));
				$ex = $currencyHelper->format($ex[0]);
			} else {
				$ex = Hash::extract($data, $param);
				$ex = $ex[0];
			}

			$params[] = $ex;
		}

		return __d('revenue_share', $format, $params);
	}

	public function getAvailableItems($mode = 'list') {
		$list = array();
		$models = array(
			'AdsCategoryPackage' => 1,
			'BannerAdsPackage' => -1,
			'FeaturedAdsPackage' => -1,
			'LoginAdsPackage' => -1,
			'PaidOffersPackage' => -1,
		);

		if(Module::active('AdGrid')) {
			$models['AdGrid.AdGridAdsPackage'] = -1;
		}

		$data = array();
		foreach($models as $modelPath => $recursive) {
			list(, $modelName) = pluginSplit($modelPath);
			$model = ClassRegistry::init($modelPath);

			$fields = $this->packagesToString[$modelPath]['params'];
			foreach($fields as &$field) {
				if(in_array($field{0}, array('-', '$'))) {
					$field = substr($field, 1);
				}
			}
			$fields[] = $modelName.'.id';

			$model->recursive = $recursive;
			$data[$modelName] = $model->find('all', array(
				'fields' => $fields,
			));

			foreach($data[$modelName] as &$v) {
				$title = $this->packageToString($modelPath, $v);

				if($mode == 'all') {
					$v['title'] = $title;
				} elseif($mode == 'list') {
					$list[$modelPath.'-'.$v[$modelName]['id']] = $title;
				}
			}
		}

		if($mode == 'list') {
			return $list;
		}

		return $data;
	}

	public function assign($amount, $user_id, $option_id, $quantity) {
		$this->recursive = 1;
		$option = $this->findById($option_id);

		if(empty($option)) {
			throw new NotFoundException(__d('revenue_share', 'Invalid option'));
		}

		$total_revenue = bcmul($option[$this->alias]['price'], bcdiv($option[$this->alias]['overall_return'], '100'));
		$end_date = new DateTime("+ {$option[$this->alias]['running_days']} days");
		$interval = $end_date->diff(new DateTime());
		$minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

		$revenue_per_step = bcdiv($total_revenue, $minutes / $option[$this->alias]['step']);

		$packet = array(
			'revenue_share_option_id' => $option_id,
			'user_id' => $user_id,
			'step' => $option[$this->alias]['step'],
			'running_days' => $option[$this->alias]['running_days'],
			'running_days_max' => $option[$this->alias]['running_days_max'],
			'total_revenue' => $total_revenue,
			'per_step_revenue' => $revenue_per_step,
		);

		if(!ClassRegistry::init('RevenueShare.RevenueSharePacket')->saveMany(array_fill(0, $quantity, $packet))) {
			throw new InternalErrorException('Failed to save packet %s', print_r($packet, true));
		}

		$items = array();
		foreach($option['Item'] as $item) {
			$items[] = array(
				'user_id' => $user_id,
				'model' => $item['model'],
				'foreign_key' => $item['foreign_key'],
			);
		}

		if(!empty($items)) {
			if(!ClassRegistry::init('BoughtItem')->saveMany($items)) {
				throw new InternalErrorException('Failed to save packet items %s', print_r($items, true));
			}
		}

		ClassRegistry::init('RevenueShare.RevenueShareHistory')->add($amount);

		return true;
	}
}
