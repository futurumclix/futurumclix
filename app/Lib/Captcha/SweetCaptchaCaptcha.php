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
require_once(APP.'Vendor'.DS.'Captcha'.DS.'sweetcaptcha.php');

class SweetCaptchaCaptcha implements CaptchaInterface {
	private $_settings = array();
	private $_lib = null;

	public function init() {
		$this->_settings = ClassRegistry::init('Settings')->fetchOne('SweetCaptcha');
		$this->_lib = new Sweetcaptcha($this->_settings['applicationID'], $this->_settings['applicationKey'], $this->_settings['applicationSecret'], Router::url('', true));
	}

	public function getName() {
		return 'SweetCaptcha';
	}

	public function usesFields() {
		return array(
			'sckey',
			'scvalue',
			'scvalue2',
		);
	}

	public function checkRequest() {
		return $this->_lib->check(array('sckey' => $_POST['sckey'], 'scvalue' => $_POST['scvalue'])) == 'true';
	}

	public function render() {
		return $this->_lib->get_html(array('is_auto_submit' => '1'));
	}
}
