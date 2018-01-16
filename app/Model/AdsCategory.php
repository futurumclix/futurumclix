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
 * AdsCategory Model
 *
 */
class AdsCategory extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * actAs
 *
 * @var array
 */
	public $actsAs = array(
		'Containable',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 32),
				'message' => 'Name cannot be longer than 32 characters',
			),
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Name cannot be empty',
				'allowEmpty' => false,
			),
			'characters' => array(
				'rule' => array('custom', '/^[a-z\d\-_\s]+$/i'),
				'message' => 'Name can only contain alphanumerical symbols',
			),
		),
		'time' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Time should be a numeric value (seconds)',
				'allowEmpty' => false,
			),
			'notNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Time cannot be a negative value',
			),
		),
		'allow_description' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => 'Allow description should be a boolean value',
				'allowEmpty' => false,
			),
		),
		'geo_targetting' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => 'Geo-targeting should be a boolean value',
				'allowEmpty' => false,
			),
		),
		'referrals_earnings' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => 'Referrals earnings should be a boolean value',
				'allowEmpty' => false,
			),
		),
		'status' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 20),
				'message' => 'Status cannot be longer than 20 characters',
				'allowEmpty' => false,
			),
			'inList' => array(
				'rule' => array('inList', array('Active', 'Disabled')),
				'message' => '',
				'allowEmpty' => false,
			),
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'AdsCategoryPackage' => array(
			'className' => 'AdsCategoryPackage',
			'foreignKey' => 'ads_category_id',
			'dependent' => true,
		),
		'ClickValue' => array(
			'className' => 'ClickValue',
			'foreignKey' => 'ads_category_id',
			'dependent' => true,
		),
		'Ads' => array(
			'className' => 'Ad',
			'foreignKey' => 'ads_category_id',
			'dependent' => true,
		),
	);

/**
 * afterSave callback
 *
 * @return void
 */
	public function afterSave($created, $options = array()) {
		if($created) {
			$this->ClickValue->createDefault(null, $this->data[$this->alias]['id']);
		}
	}

/**
 * enable method
 *
 * @return void
 */
	public function enable($id) {
		$this->id = $id;
		$this->set(array('id' => $id, 'status' => 'Active'));
		return $this->save();
	}

/**
 * disable method
 *
 * @return void
 */
	public function disable($id) {
		$this->id = $id;
		$this->set(array('id' => $id, 'status' => 'Disabled'));
		return $this->save();
	}
}
