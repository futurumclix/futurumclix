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
App::uses('CaptchaInterface', 'Captcha');
require_once(APP.'Vendor'.DS.'Captcha'.DS.'recaptchalib.php');

/**
 * reCaptchaCaptcha
 *
 */
class reCaptchaCaptcha implements CaptchaInterface {
	private $_settings = array();

	public function init() {
		$this->_settings = ClassRegistry::init('Settings')->fetchOne('reCaptcha');
	}

	public function getName() {
		return 'reCaptcha';
	}

	public function usesFields() {
		return array(
			'g-recaptcha-response',
		);
	}

	public function checkRequest() {
		$resp = recaptcha_check_answer($this->_settings['privateKey'], $_POST['g-recaptcha-response']);

		return $resp->is_valid;	
	}

	public function render() {
		return recaptcha_get_html($this->_settings['publicKey'], $this->_settings['theme']);
	}
}
