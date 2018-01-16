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
 * ExpressAdsTargettedLocation Model
 *
 * @property Ad $Ad
 */
class ExpressAdsTargettedLocation extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'location';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'location' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ExpressAd' => array(
			'className' => 'ExpressAd',
			'foreignKey' => 'express_ad_id',
		)
	);

	public function createFromList($data, $ad_id = null) {
		$res = array();
		foreach($data as $d) {
			$res[] = array(
				'express_ad_id' => $ad_id,
				'location' => $d,
			);
		}
		return $res;
	}

	public function saveFromList($data, $adId) {
		$res = array();

		$this->deleteAll(array('express_ad_id' => $adId));

		if(!Module::active('AccurateLocationDatabase') && count($data) == ClassRegistry::init('Ip2NationCountry')->find('count') - 1) {
			$res[] = array(
				'express_ad_id' => $adId,
				'location' => '*',
			);
		} else {
			foreach($data as $d) {
				$res[] = array(
					'express_ad_id' => $adId,
					'location' => $d,
				);
			}
		}

		return $this->saveMany($res);
	}

}
