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
 * PaidOffersTargettedLocation Model
 *
 * @property Ad $Ad
 */
class PaidOffersTargettedLocation extends AppModel {

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
		'PaidOffer' => array(
			'className' => 'PaidOffer',
			'foreignKey' => 'offer_id',
		)
	);

	public function createFromList($data, $offer_id = null) {
		$res = array();
		foreach($data as $d) {
			$res[] = array(
				'offer_id' => $offer_id,
				'location' => $d,
			);
		}
		return $res;
	}

	public function saveFromList($data, $offerId) {
		$res = array();

		$this->deleteAll(array('offer_id' => $offerId));

		if(!Module::active('AccurateLocationDatabase') && count($data) == ClassRegistry::init('Ip2NationCountry')->find('count') - 1) {
			$res[] = array(
				'offer_id' => $offerId,
				'location' => '*',
			);
		} else {
			foreach($data as $d) {
				$res[] = array(
					'offer_id' => $offerId,
					'location' => $d,
				);
			}
		}

		return $this->saveMany($res);
	}

}
