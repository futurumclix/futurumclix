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
 * ExpressAdsClickValue Model
 *
 * @property Membership $Membership
 */
class ExpressAdsClickValue extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'user_click_value' => array(
			'money' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Click value should be non-negative decimal value',
				'allowEmpty' => false,
				'required' => true,
			),
		),
		'direct_referral_click_value' => array(
			'money' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Click value should be non-negative decimal value',
				'allowEmpty' => false,
				'required' => true,
			),
		),
		'rented_referral_click_value' => array(
			'money' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Click value should be non-negative decimal value',
				'allowEmpty' => false,
				'required' => true,
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Membership' => array(
			'className' => 'Membership',
			'foreignKey' => 'membership_id',
		)
	);

/**
 * createDefault
 *
 * @return boolean
 */
	public function createDefault($membership_id, $adcategory_id = null) {
		if(!is_numeric($membership_id) && !is_numeric($adcategory_id)) {
			return false;
		}

		$data = array();

		if(is_numeric($membership_id)) {
			$ads_categories = $this->AdsCategory->find('list');
			$ads_categories = array_values(array_flip($ads_categories));

			foreach($ads_categories as $cat) {
				$data[] = array(
					'membership_id' => $membership_id,
					'user_click_value' => 0,
					'direct_referral_click_value' => 0,
					'rented_referral_click_value' => 0,
				);
			}
		} else {
			$memberships = $this->Membership->find('list');
			$memberships = array_values(array_flip($memberships));

			foreach($memberships as $membership) {
				$data[] = array(
					'membership_id' => $membership,
					'user_click_value' => 0,
					'direct_referral_click_value' => 0,
					'rented_referral_click_value' => 0,
				);
			}
		}

		return $this->saveAll($data, array('validate' => false));
	}

}
