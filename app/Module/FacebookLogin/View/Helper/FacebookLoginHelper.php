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
App::uses('FacebookLoginAppHelper', 'FacebookLogin.View/Helper');
App::import('Vendor', 'FacebookLogin.autoload', array('file' => 'Facebook/autoload.php'));

class FacebookLoginHelper extends FacebookLoginAppHelper {
	private $loginUrl = null;

	public $helpers = array(
		'Html',
	);

	private function getLoginUrl() {
		if($this->loginUrl) {
			return $this->loginUrl;
		}

		$settings = ClassRegistry::init('FacebookLogin.FacebookLoginSettings')->fetchOne('facebookLogin');

		if(empty($settings) || !isset($settings['appID']) || empty($settings['appID'] || !isset($settings['appSecret']) || empty($settings['appSecret']))) {
			return false;
		}

		$fb = new Facebook\Facebook(array(
			'app_id' => $settings['appID'],
			'app_secret' => $settings['appSecret'],
			'default_graph_version' => 'v2.2',
		));
		$helper = $fb->getRedirectLoginHelper();

		$this->loginUrl = $helper->getLoginUrl(Router::url(array('plugin' => 'facebook_login', 'controller' => 'facebook_login', 'action' => 'login'), true), array('email'));

		return $this->loginUrl;
	}

	public function link($text, $options = array(), $confirmMessage = false) {
		$url = $this->getLoginUrl();

		if(!$url) {
			return '';
		}

		return $this->Html->link($text, $url, $options, $confirmMessage);
	}
}
