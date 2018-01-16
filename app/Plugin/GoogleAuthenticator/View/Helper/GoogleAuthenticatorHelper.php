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
App::uses('AppHelper', 'View/Helper');
App::uses('GoogleAuthenticator', 'GoogleAuthenticator.Vendor/GA');

class GoogleAuthenticatorHelper extends AppHelper {
	private $defaultHelpers = array('Session', 'Html');
	private $GoogleAuthenticator = null;

	public function __construct(View $View, array $settings = array()) {
		$this->helpers = Hash::merge($this->defaultHelpers, $this->helpers);
		$this->GoogleAuthenticator = new GoogleAuthenticator();
		parent::__construct($View, $settings);
	}

	public function getQRCode($secret = null, $options = array()) {
		if(Router::getParam('prefix', true) == 'admin') {
			$username = $this->Session->read('Auth.Admin.email');
		} else {
			$username = $this->Session->read('Auth.User.username');
		}

		if(!$secret || !$username) {
			return '';
		}

		$siteURL = parse_url(Configure::read('siteURL'), PHP_URL_HOST);
		$url = $this->GoogleAuthenticator->getURL($username, $siteURL, $secret);

		return $this->Html->image($url, $options);
	}
}
