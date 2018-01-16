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
App::uses('SessionComponent', 'Controller');
/**
 * NoticeComponent
 *
 *
 */
class NoticeComponent extends Component {
	var $components = array('Session');

	protected function _getKey($key = null) {
		if($key === null || empty($key)) {
			switch(Router::getParam('prefix', true)) {
				case 'admin':
					return 'admin_action';
				default:
					return 'user_action';
			}
		}
		return $key;
	}

	protected function _getElement($element = null) {
		if($element === null || empty($element)) {
			switch(Router::getParam('prefix', true)) {
				case 'admin':
					return 'adminNotice';
				default:
					return 'userNotice';
			}
		}
		return $element;
	}

	public function success($message, $key = null, $options = array()) {
		$data = $this->Session->read('Notice.'.$this->_getKey($key).'.Success');
		if($data === null || !in_array($message, $data)) {
			$data[] = $message;
			$this->Session->write('Notice.'.$this->_getKey($key).'.Success', $data);
		}
	}

	public function error($message, $key = null, $options = array()) {
		$data = $this->Session->read('Notice.'.$this->_getKey($key).'.Error');
		if($data === null || !in_array($message, $data)) {
			$data[] = $message;
			$this->Session->write('Notice.'.$this->_getKey($key).'.Error', $data);
		}
	}

	public function info($message, $key = null, $options = array()) {
		$data = $this->Session->read('Notice.'.$this->_getKey($key).'.Info');
		if($data === null || !in_array($message, $data)) {
			$data[] = $message;
			$this->Session->write('Notice.'.$this->_getKey($key).'.Info', $data);
		}
	}
}
