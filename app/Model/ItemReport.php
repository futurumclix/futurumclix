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
 * @copyright   2006-2013, Miles Johnson - http://milesj.me
 * @license     https://github.com/milesj/admin/blob/master/license.md
 * @link        http://milesj.me/code/cakephp/admin
 */

App::uses('AppModel', 'Model');

class ItemReport extends AppModel {
	/**
	 * Behaviors.
	 *
	 * @type array
	 */
	public $actsAs = array('Containable', 'Utility.Enumerable', 'Utility.Cacheable' => array('cacheConfig' => 'forum'), 'Utility.Validateable');

	// Status
	const PENDING = 0;
	const RESOLVED = 1;
	const INVALID = 2;

	// Type
	const OTHER = 0;
	const VIOLENCE = 1; // physical fighting / abuse
	const OFFENSIVE = 2; // animal / child abuse
	const HATEFUL = 3; // hate crimes, bullying
	const HARMFUL = 4; // dangerous acts, self injury, etc
	const SPAM = 5; // spam, fraud, misleading ads
	const COPYRIGHT = 6; // infringement, etc
	const SEXUAL = 7; // nudity, sex, etc
	const HARASSMENT = 8; // user to user, threats, trolling

	/**
	 * Belongs to.
	 *
	 * @type array
	 */
	public $belongsTo = array('Reporter' => array('className' => 'User', 'foreignKey' => 'reporter_id'), 'Resolver' => array('className' => 'Admin', 'foreignKey' => 'resolver_id'));

	/**
	 * Enum mapping.
	 *
	 * @type array
	 */
	public $enum = array('status' => array(self::PENDING => 'Pending', self::RESOLVED => 'Resolved', self::INVALID => 'Invalid'), 'type' => array(self::OTHER => 'Other', self::VIOLENCE => 'Violence', self::OFFENSIVE => 'Offensive', self::HATEFUL => 'Hateful', self::HARMFUL => 'Harmful', self::SPAM => 'Spam', self::COPYRIGHT => 'Copyright', self::SEXUAL => 'Sexual', self::HARASSMENT => 'Harassment'));

	/**
	 * Count record by status.
	 *
	 * @param int $status
	 * @return array
	 */
	public function getCountByStatus($status = self::PENDING) {
		return $this->find('count', array('conditions' => array('ItemReport.status' => $status), 'cache' => array(__METHOD__, $status)));
	}

	/**
	 * Mark a report as resolved or invalid.
	 *
	 * @param int $id
	 * @param int $status
	 * @param int $user_id
	 * @param string $comment
	 * @return bool
	 */
	public function markAs($id, $status, $user_id, $comment = null) {
		$this->id = $id;

		return $this->save(array('status' => $status, 'resolver_id' => $user_id, 'comment' => $comment));
	}

	/**
	 * Log a unique report only once every 7 days.
	 *
	 * @param array $query
	 * @return bool
	 */
	public function reportItem($query) {
		$conditions = $query;
		$conditions[$this->alias . '.created >='] = date('Y-m-d H:i:s', strtotime('-7 days'));

		$unset = array('type', 'item', 'reason', 'comment',);

		foreach($unset as $u) {
			$aname = $this->alias . '.' . $u;
			if(isset($conditions[$u])) {
				unset($conditions[$u]);
			}
			if(isset($conditions[$aname])) {
				unset($conditions[$aname]);
			}
		}

		$count = $this->find('count', array('conditions' => $conditions));

		if($count) {
			return true;
		}

		$this->create();

		return $this->save($query, false);
	}

	public function beforeSave($options = array()) {
		if($this->data[$this->alias]['status'] == self::RESOLVED && isset($this->data[$this->alias]['model']) && !empty($this->data[$this->alias]['model'])) {
			if(isset($this->data[$this->alias]['action']) && !empty($this->data[$this->alias]['action'])) {
				$model = ClassRegistry::init($this->data[$this->alias]['model']);

				if(!$model) {
					throw new InternalErrorException(__d('exception', 'Unknown model %s', $this->data[$this->alias]['model']));
				}

				if(!isset($model->reportActions[$this->data[$this->alias]['action']])) {
					throw new InternalErrorExcpetion(__('Wrong action %s on model %s', $this->data[$this->alias]['action'], $this->data[$this->alias]['model']));
				}

				$methodName = $model->reportActions[$this->data[$this->alias]['action']];

				return (boolean)$model->$methodName($this->data[$this->alias]['foreign_key']);
			}
		}
		return true;
	}
}
