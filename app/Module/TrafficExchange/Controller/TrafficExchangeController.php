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
class TrafficExchangeController extends TrafficExchangeAppController {
	public $components = array(
		'UserPanel',
		'Paginator',
	);
	public $uses = array(
		'TrafficExchange.TrafficExchangeMembership',
	);

	public function admin_settings() {
		if($this->request->is(array('post', 'put'))) {
			$success = true;

			if(!empty($this->request->data['TrafficExchangeSettings']) && $success) {
				$success = $this->TrafficExchangeSettings->store($this->request->data, array('trafficExchange'));
			}

			if(!empty($this->request->data['TrafficExchangeMembership']) && $success) {
				$success = $this->TrafficExchangeMembership->saveMany($this->request->data['TrafficExchangeMembership']);
			}

			if($success) {
				$this->Notice->success(__d('traffic_exchange_admin', 'The settings has been saved.'));
			} else {
				$this->Notice->error(__d('traffic_exchange_admin', 'Error occurred when saving settings. Please, try again.'));
			}
		}

		$settings = $this->TrafficExchangeSettings->fetch('trafficExchange');
		$this->request->data = Hash::merge($settings, $this->request->data);

		$this->TrafficExchangeMembership->recursive = -1;
		$options = $this->TrafficExchangeMembership->find('all');
		$options = Hash::combine($options, '{n}.TrafficExchangeMembership.membership_id', '{n}.TrafficExchangeMembership');
		if(isset($this->request->data['TrafficExchangeMembership'])) {
			$this->request->data['TrafficExchangeMembership'] = Hash::merge($options, $this->request->data['TrafficExchangeMembership']);
		} else {
			$this->request->data['TrafficExchangeMembership'] = $options;
		}

		$memberships = ClassRegistry::init('Membership')->getList();

		$this->set(compact('available', 'active', 'memberships'));
	}
}
