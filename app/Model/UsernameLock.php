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
 * UsernameLock Model
 *
 */
class UsernameLock extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'template';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'template' => array(
			'empty' => array(
				'rule' => array('notBlank'),
				'message' => 'Template cannot be empty.',
				'allowEmpty' => false,
			),
			'maxLength' => array(
				'rule' => array('maxLength', 50),
				'message' => 'Template cannot be longer than 50 characters.',
				'allowEmpty' => false,
			),
		),
		'note' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Note cannot be longer than 255 characters.',
			),
		),
	);

/**
 * beforeSave callback
 *
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if(isset($this->data[$this->alias]['template'])) {
			$this->data[$this->alias]['template'] = str_replace(array('\\', '_', '%', '?', '*'), array('\\\\', '\\_', '\\%', '_', '%'), $this->data[$this->alias]['template']);
		}

		return true;
	}

/**
 * afterFind callback
 *
 * @return array
 */
	public function afterFind($results, $primary = false) {
		for($i = 0, $e = count($results); $i < $e; ++$i) {
			if(isset($results[$i][$this->alias]['template'])) {
				$results[$i][$this->alias]['template'] = str_replace(array('_', '%', '\\?', '\\*', '\\\\'), array('?', '*', '_', '%', '\\'), $results[$i][$this->alias]['template']);
			}
		}
		return $results;
	}

/**
 * isLocked
 *
 * @return boolean
 */
	public function isLocked($username) {
		$this->recursive = -1;
		$data = $this->find('first', array(
			'fields' => array('id'),
			'conditions' => array(
				"'$username' LIKE `{$this->alias}`.`template`",
			),
		));

		return !empty($data);
	}
}
