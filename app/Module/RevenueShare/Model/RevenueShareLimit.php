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
 * RevenueShareLimit Model
 *
 * @property Membership $Membership
 */
class RevenueShareLimit extends RevenueShareAppModel {
/**
 * credit values
 *
 * @const
 */
	const ACCOUNT = 0;
	const PURCHASE = 1;

/**
 * enum
 *
 * @var array
 */
	public $enum = array(
		'credit' => array(
			self::ACCOUNT => 'Account Balance',
			self::PURCHASE => 'Purchase Balance',
		),
	);

/**
 * actAs
 *
 * @var array
 */
	public $actsAs = array(
		'Containable',
		'Utility.Enumerable',
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'enabled' => 'boolean',
		'credit' => array(
			'inList' => array(
				'rule' => array('inList', array(self::ACCOUNT, self::PURCHASE)),
				'message' => 'Shares income can be credited only to Account or Purchase balance.',
				'allowEmpty' => false,
			),
		),
		'max_packs' => array(
			'range' => array(
				'rule' => array('range', -2, 32768),
				'message' => 'Maximum of packages have to be between -1 and 32767 (-1 means unlimited).',
				'allowEmpty' => false,
			),
		),
		'max_packs_one_purchase' => array(
			'range' => array(
				'rule' => array('range', -2, 32768),
				'message' => 'Maximum of packages in one purchase have to be between -1 and 32767 (-1 means unlimited).',
				'allowEmpty' => false,
			),
		),
		'days_between' => array(
			'range' => array(
				'rule' => array('range', -1, 65536),
				'message' => 'Days between purchases have to be between 0 and 65535.',
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
		'Membership' => array(
			'className' => 'Membership',
			'foreignKey' => 'membership_id',
		),
	);

	public function whereCreditList() {
		$this->contain();
		$data = $this->find('all', array(
			'fields' => array(
				$this->alias.'.enabled',
				$this->alias.'.credit',
				$this->alias.'.membership_id',
			),
		));
		return Hash::combine($data, '{n}.'.$this->alias.'.membership_id', '{n}.'.$this->alias);
	}
}
