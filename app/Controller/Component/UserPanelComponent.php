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
App::uses('Component', 'Controller');

/**
 * UserPanelComponent
 *
 *
 */
class UserPanelComponent extends Component {

	var $components = array('Auth');

/**
 * getData method
 *
 * @return array
 */
	public function getData($contain = array(), $id = null) {
		$User = ClassRegistry::init('User');
		$Membership = ClassRegistry::init('Membership');

		if($id === null) {
			$id = $this->Auth->user('id');

			if($id == null) {
				throw new InternalErrorException('Not logged in');
			}
		}
		if(isset($contain['ActiveMembership.Membership'])) {
			if(is_array($contain['ActiveMembership.Membership'])) {
				$contain['ActiveMembership.Membership'][] = 'name';
				$contain['ActiveMembership.Membership'][] = 'results_per_page';
				$contain['ActiveMembership.Membership'][] = 'points_enabled';
				$contain['ActiveMembership.Membership'][] = 'points_conversion';
			} else {
				$contain['ActiveMembership.Membership'] = array($contain['ActiveMembership.Membership'], 'name', 'results_per_page', 'points_enabled', 'points_conversion');
			}
		} else {
			$contain['ActiveMembership.Membership'] = array('name', 'results_per_page', 'points_enabled', 'points_conversion');
		}

		$options = array(
			'conditions' => array('User.'.$User->primaryKey => $id),
			'contain' => $contain,
		);

		$user = $User->find('first', $options);

		return $user;
	}

/**
 * beforeRender callback
 *
 * @return void
 */
	public function beforeRender(Controller $controller) {
		$controller->helpers[] = 'Forum.Forum';
	}
}
