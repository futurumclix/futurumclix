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
App::uses('AppController', 'Controller');
/**
 * Tools Controller
 *
 */
class ToolsController extends AppController {
/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'User',
		'Banner',
		'Settings',
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'UserPanel',
	);

/**
 * promotion method
 *
 * @return void
 */
	public function promotion() {
		$this->set('user', $this->UserPanel->getData());
		$this->set('banners', $this->Banner->find('all'));
		$this->set('breadcrumbTitle', __('Promotion tools'));
	}

/**
 * points_exchange method
 *
 * @return void
 */
	public function points_exchange() {
		$user = $this->UserPanel->getData(array('ActiveMembership.Membership' => array(
			'points_enabled',
			'points_conversion',
			'points_value',
			'points_min_conversion',
		)));

		if(!$user['ActiveMembership']['Membership']['points_enabled'] || $user['ActiveMembership']['Membership']['points_conversion'] == Membership::POINTS_CONVERSION_DISABLED) {
			return $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
		}

		$total_value = bcmul($user['User']['points'], $user['ActiveMembership']['Membership']['points_value']);

		$this->set(compact('user', 'total_value'));
		$this->set('breadcrumbTitle', __('Exchange points'));

		if($this->request->is(array('post', 'put'))) {
			$points = $this->request->data['points'];
			$value = $user['ActiveMembership']['Membership']['points_value'];

			if(empty($points) || bccomp(0, $points) >= 0 || bccomp($points, $user['ActiveMembership']['Membership']['points_min_conversion']) < 0) {
				return $this->Notice->error(__('Minimum points amount for conversion is %s', $user['ActiveMembership']['Membership']['points_min_conversion']));
			}

			if(bccomp($user['User']['points'], $points) < 0) {
				return $this->Notice->error(__('Insufficient points balance.'));
			}

			if(!$this->User->pointsSub($points, $user['User']['id'])) {
				throw new InternalErrorException(__d('exception', 'Failed to save points balance'));
			}

			switch($user['ActiveMembership']['Membership']['points_conversion']) {
				case Membership::POINTS_CONVERSION_ACCOUNT:
					$res = $this->User->accountBalanceAdd(bcmul($points, $value), $user['User']['id']);
				break;

				case Membership::POINTS_CONVERSION_PURCHASE:
					$res = $this->User->purchaseBalanceAdd(bcmul($points, $value), $user['User']['id']);
				break;
			}

			if($res) {
				$this->Notice->success(__('Points sucessfully converted.'));
			} else {
				$this->Notice->error(__('Error during conversion. Please, try again.'));
			}
		}
	}
}
