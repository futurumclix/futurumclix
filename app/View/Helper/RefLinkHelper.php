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

/**
 * RefLinkHelper
 *
 *
 */

class RefLinkHelper extends AppHelper {
/**
 * helpers
 *
 * @var array
 */
	public $helpers = array(
		'Html',
		'Session'
	);

/**
 * loginEncrypt
 *
 * @param string $login
 * @return string
 */
	protected function loginEncrypt($login) {
		$magic = Configure::read('Security.salt');
		$magic_len = strlen($magic);
		$result = '';

		if(!$magic_len) {
			throw new InternalErrorException(__d('exception', 'Invalid security salt'));
		}

		for($i = strlen($login); $i > 0; $i--) { 
			$act_char = substr($login, -$i, 1);
			$magic_char = substr($magic, -($i % $magic_len) - 1, 1);
			$char = chr(ord($act_char) + ord($magic_char));
			$result .= $char;
		}

		return urlencode(base64_encode(base64_encode($result)));
	} 

/**
 * create method
 *
 * @param string $title
 * @param string/array $url
 * @param string $username
 * @param boolean $secret
 * @return string
 */
	public function create($title, $url, $secret = false, $username = null) {
		return $this->Html->link($title, $this->get($url, $secret, $username));
	}

/**
 * get method
 *
 * @param string/array $url
 * @param string $username
 * @param boolean $secret
 * @return string
 */
	public function get($url, $secret = false, $username = null) {
		if(is_array($url)) {
			$url = Router::url($url);
		}
		if($secret) {
			$param = '?re=';
		} else {
			$param = '?r=';
		}
		$param .= $this->getUsername($secret, $username);
		return Router::url($url.$param, true);
	}

/**
 * getUsername
 *
 * @param string username
 * @param boolean $secret
 * @return string
 */
	public function getUsername($secret = false, $username = null) {
		if($username == null) {
			$username = $this->Session->read('Auth.User.username');
		}
		if($secret) {
			$res = $this->loginEncrypt($username);
		} else {
			$res = $username;
		}
		return $res;
	}
 }
 