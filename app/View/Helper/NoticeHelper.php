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
App::uses('FormHelper', 'View/Helper');

/**
 * NoticeHelper
 *
 *
 */

class NoticeHelper extends FormHelper {

/**
 * helpers
 *
 * @var array
 */
	public $helpers = array('Session');

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

/**
 * getValidationErrors method
 *
 * @return string Notice tags for displaying validation errors
 */
	protected function _getValidationErrors() {
		$errors = Hash::flatten($this->validationErrors);
		$errors = array_filter($errors, 'is_string');
		$errors = array_unique($errors);
		$result = '';

		foreach($errors as $error) {
			if(is_string($error)) {
				$tmpVars['message'] = $error;
				$result .= $this->_View->element($this->_getElement().'Error', $tmpVars);
			}
		}

		return $result;
	}

/**
 * show method
 *
 * @return string
 */
	public function show($showTypes = array('error', 'info', 'success')) {
		$result = '';
		$key = $this->_getKey();

		foreach($showTypes as $type) {
			$typeU = ucfirst($type);
			$path = 'Notice.'.$this->_getKey($key).'.'.$typeU;
			$data = $this->Session->consume($path);

			if($data) {
				foreach($data as $msg) {
					$result .= $this->_View->element($this->_getElement().$typeU, array('message' => $msg));
				}
			}
		}

		if(isset($this->validationErrors) && in_array('error', $showTypes)) {
			$result .= $this->_getValidationErrors();
		}

		return $result;
	}
}
