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
 * AccurateLocationDatabaseLocation Model
 *
 * @property AccurateLocationDatabaseLocation $ParentAccurateLocationDatabaseLocation
 * @property AccurateLocationDatabaseLocation $ChildAccurateLocationDatabaseLocation
 */
class AccurateLocationDatabaseLocation extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

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
				'rule' => array('maxLength', 180),
				'message' => 'Name cannot be longer than %d',
				'allowEmpty' => false,
			),
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Name cannot be blank',
			),
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ParentAccurateLocationDatabaseLocation' => array(
			'className' => 'AccurateLocationDatabaseLocation',
			'foreignKey' => 'parent_id',
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ChildAccurateLocationDatabaseLocation' => array(
			'className' => 'AccurateLocationDatabaseLocation',
			'foreignKey' => 'parent_id',
			'dependent' => false,
		),
	);

	public function getCountriesList() {
		$this->contain();
		$data = $this->find('list', array(
			'conditions' => array(
				'parent_id' => null,
			),
			'order' => 'name',
		));

		return array_combine($data, $data);
	}

	public function getByParentName($parent) {
		$this->contain(array('ParentAccurateLocationDatabaseLocation'));
		return $this->find('all', array(
			'fields' => array(
				'id',
				'name',
			),
			'conditions' => array(
				'ParentAccurateLocationDatabaseLocation.name' => $parent,
			),
			'order' => 'name',
		));
	}

	public function getListByParentName($parent) {
		if($parent == '*') {
			return array();
		}

		$data = $this->getByParentName($parent);
		return Hash::extract($data, '{n}.'.$this->alias.'.name');
	}
}
