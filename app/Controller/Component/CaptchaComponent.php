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
App::uses('CaptchasList', 'Captcha');
/**
 * CaptchaComponent
 *
 *
 */
class CaptchaComponent extends Component {
	public $components = array(
		'Security',
		'Notice',
	);

	protected $_protectedMethods = array();
	protected $_controllerMethods;
	protected $_settings = array(
		'type' => null,
		'mode' => 'normal', // or 'surfer'
	);

	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);

		$this->_settings = Hash::merge($this->_settings, $settings);
	}

	public function initialize(Controller $controller) {
		$this->_controllerMethods = array_map('strtolower', $controller->methods);
	}

	public function startup(Controller $controller) {
		$action = strtolower($controller->request->params['action']);
		$isMissingAction = (
			$controller->scaffold === false && 
			array_search($action, $this->_controllerMethods, true) === false
		);
		$isRequestAction = (
			isset($controller->request->params['requested']) &&
			$controller->request->params['requested'] == 1
		);

		if($isMissingAction || $isRequestAction) {
			return true;
		}

		if(!in_array($action, $this->_protectedMethods)) {
			return true;
		}

		return $this->inspectRequest($controller);
	}

	public function protect($methods) {
		if(is_string($methods)) {
			$this->_protectedMethods[] = strtolower($methods);
		} elseif(is_array($methods)) {
			$this->_protectedMethods = array_merge($this->_protectedMethods, array_map('strtolower', $methods));
		}
	}

	public function beforeRender(Controller $controller) {
		/* DEPRECATED: compatibility only, will suppress warnings in old themes */
		$controller->set('captchaType', $this->_settings['type']);
		$controller->set('captchaKeys', 'DEPRECATED');
	}

	protected function createCaptcha() {
		$list = new CaptchasList();
		$captcha = null;

		if(is_string($this->_settings['type'])) {
			if($list->have($this->_settings['type'])) {
				$captcha = $list->init($this->_settings['type']);
			}
		}

		if($captcha === null) {
			$key = $this->_settings['mode'] == 'surfer' ? 'captchaTypeSurfer': 'captchaType';
			$this->_settings['type'] = ClassRegistry::init('Settings')->fetchOne($key, 'disabled');
			$captcha = $list->init($this->_settings['type']);
		}

		return $captcha;
	}

	protected function inspectRequest(Controller $controller) {
		$captcha = $this->createCaptcha();

		if(!$captcha) {
			return true;
		}

		$controller->request->params['captcha'] = $captcha;

		if(!$controller->request->is(array('post', 'put', 'delete'))) {
			return true;
		}

		if(!$captcha->checkRequest()) {
			if(!$controller->request->is('ajax')) {
				$this->Notice->error(__('Failed to verify captcha. Please try again.'));
			}
			$_SERVER['REQUEST_METHOD'] = 'GET';
			return false;
		}

		return true;
	}
}
 
