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
App::uses('GoogleAuthenticator', 'GoogleAuthenticator.Vendor/GA');

class GoogleAuthenticatorComponent extends Component {
	public $components = array(
		'Session',
	);

	protected $_settings = array(
		
	);

	private $GoogleAuthenticator = null;

	public function initialize(Controller $controller) {
		$this->GoogleAuthenticator = new GoogleAuthenticator();
	}

	public function startup(Controller $controller) {
		$ga = $this->Session->read('GoogleAuthenticator');
		if($ga && !$ga['pass']) {
			/* not authorized */
		}
	}

	public function beforeRender(Controller $controller) {
		$controller->helpers[] = 'GoogleAuthenticator.GoogleAuthenticator';
	}

	public function createSecret() {
		return $this->GoogleAuthenticator->generateSecret();
	}

	public function check($code, $secret = null) {
		if($secret === null) {
			$secret = $this->Session->read('GoogleAuthenticator.secret');
		}

		if($secret === null) {
			throw new InternalErrorException(__d('exception', 'Invalid GA data'));
		}

		if($this->GoogleAuthenticator->checkCode($secret, $code)) {
			$this->Session->write('GoogleAuthenticator.pass', true);
			return true;
		}

		return false;
	}
}
