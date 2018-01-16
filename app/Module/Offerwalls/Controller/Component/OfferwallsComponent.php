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
App::uses('OfferwallsList', 'Offerwalls.Lib/Offerwalls');

class OfferwallsComponent extends Component {
	private $Settings = null;
	private $Offerwall = null;

	protected $available = null;
	protected $active = array();

	public function startup(Controller $controller) {
		parent::startup($controller);

		$this->controller = $controller;

		$this->Offerwall = ClassRegistry::init('Offerwalls.Offerwall');
		$this->Settings = ClassRegistry::init('Offerwalls.OfferwallsSettings');

		$offerwalls = new OfferwallsList();
		$this->available = $offerwalls->getList();

		$this->refreshData();
	}

	public function refreshData() {
		$this->Offerwall->recursive = -1;
		$this->active = $this->Offerwall->find('list', array(
			'conditions' => array(
				'enabled' => true,
			),
			'order' => 'name',
		));
	}

	public function createOfferwall($name) {
		$class = $name.'Offerwall';
		App::uses($class, 'Offerwalls.Lib/Offerwalls');

		$this->Offerwall->recursive = -1;
		$settings = $this->Offerwall->findByName($name);

		if(empty($settings)) {
			throw new InternalErrorException(__d('offerwalls', 'Offerwall settings not found'));
		}

		$settings = $settings['Offerwall'];

		$offerwall = new $class($settings);
		return $offerwall;
	}

	public function getAvailable() {
		return $this->available;
	}

	public function getAvailableHumanized() {
		$res = array();

		foreach($this->available as $offerwall) {
			$res[$offerwall] = $offerwall;
		}

		return $res;
	}

	public function getActive() {
		return $this->active;
	}

	public function getActiveHumanized() {
		$res = array();
		$active = $this->getActive();

		foreach($active as $offerwall) {
			$res[$offerwall] = $offerwall;
		}

		return $res;
	}

	public function getOffers($user = array()) {
		$res = array();

		foreach($this->getActiveHumanized() as $name => $humanized) {
			$offerwall = $this->createOfferwall($name);
			$res[$humanized] = $offerwall->offers($user);
		}

		return $res;
	}

}
