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
App::uses('Controller', 'Controller');
App::uses('CurrencyHelper', 'View/Helper');
App::uses('Captcha', 'Component');
App::uses('Module', 'Lib');

class AppController extends Controller {
/**
 * components
 *
 * @var array
 */
	public $components = array(
		'Security',
		'DebugKit.Toolbar' => array('panels' => array('history' => false)),
		'Session',
		'Auth',
		'Notice',
		'Cookie',
	);

/**
 * helpers
 *
 * @var array
 */
	public $helpers = array(
		'Utility.Utility',
		'Evercookie.Evercookie',
	);

/**
 * Models
 *
 * @var array
 */
	public $uses = array(
		'Settings',
		'Currency',
		'User',
		'Online.Online',
	);

/**
 * Surfers
 *
 * Actions defined below will force HTTP (without SSL!).
 *
 * @var array
 */
	private $surfers = array(
		array('plugin' => null, 'controller' => 'AdsCategories', 'action' => 'index'),
		array('plugin' => null, 'controller' => 'ads', 'action' => 'view'),
		array('plugin' => null, 'controller' => 'ads', 'action' => 'fetchProgressBar'),
		array('plugin' => null, 'controller' => 'ads', 'action' => 'verifyCaptcha'),
		array('plugin' => null, 'controller' => 'ads', 'action' => 'fetchCaptcha'),
		array('plugin' => null, 'controller' => 'ads', 'action' => 'preview'),
		array('plugin' => null, 'controller' => 'ads', 'action' => 'add'),
		array('plugin' => null, 'controller' => 'explorer_ads', 'action' => 'add'),
		array('plugin' => null, 'controller' => 'explorer_ads', 'action' => 'preview'),
		array('plugin' => null, 'controller' => 'explorer_ads', 'action' => 'view'),
		array('plugin' => null, 'controller' => 'explorer_ads', 'action' => 'next_subpage'),
		array('plugin' => null, 'controller' => 'explorer_ads', 'action' => 'verify_captcha'),
		array('plugin' => null, 'controller' => 'express_ads', 'action' => 'view'),
		array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'view'),
		array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'fetchProgressBar'),
		array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'done'),
		array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'grid'),
		array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'preview'),
		array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'add'),
		array('plugin' => 'offerwalls', 'controller' => 'offerwalls', 'action' => 'index'),
	);

	public function constructClasses() {
		if(isset($this->params['prefix']) && $this->params['prefix'] == 'admin') {
			if(isset($this->components['Pagniator'])) {
				$this->components['Paginator']['className'] = 'AdminPaginator';
			} else {
				$k = array_search('Paginator', $this->components);

				if($k !== false) {
					unset($this->components[$k]);
					$this->components['Paginator'] = array(
						'className' => 'AdminPaginator',
					);
				}
			}
		}

		return parent::constructClasses();
	}

/**
 * loginDecrypt method
 *
 * @return string
 */
	protected function loginDecrypt($secret) {
		$magic = Configure::read('Security.salt');
		$magic_len = strlen($magic);
		$secret = base64_decode(base64_decode($secret));
		$result = '';

		for($i = strlen($secret); $i > 0; $i--) {
			$act_char = substr($secret, -$i, 1);
			$magic_char = substr($magic, -($i % $magic_len) - 1, 1);
			$act_char = chr(ord($act_char) - ord($magic_char));
			$result .= $act_char;
		}
		return $result;
	}

/**
 * isSurfer method
 *
 * @return boolean
 */
	private function isSurfer($here) {
		foreach($this->surfers as $surfer) {
			if($surfer['plugin'] == $here['plugin'] && $surfer['controller'] == $here['controller'] && $surfer['action'] == $here['action']) {
				return true;
			}
		}
		return false;
	}

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		if($this->request->params['controller'] != 'pages' || $this->request->params['pass'][0] != 'locked') {
			if(ClassRegistry::init('IpLock')->isLocked($this->request->clientIp())) {
				$this->redirect(array('admin' => false, 'plugin' => null, 'controller' => 'pages', 'locked'));
			}
		}

		$this->theme = Configure::read('siteTheme');

		$this->Security->blackHoleCallback = '_badRequest';

		$isSurfer = $this->isSurfer($this->request->params);

		if(Configure::read('forceSSL') && $this->request->params['plugin'] != 'forum' && !$isSurfer) {
			$this->Security->requireSecure();
		}

		if($this->request->is('ssl') && $isSurfer) {
			return $this->redirect('http://'.env('SERVER_NAME').$this->here);
		}

		if(isset($this->params['prefix']) && $this->params['prefix'] == 'admin') {
			AuthComponent::$sessionKey = 'Auth.Admin';
			$this->Auth->loginAction = array('plugin' => null, 'controller' => 'admins', 'action' => 'login');
			$this->Auth->loginRedirect  = array('plugin' => null, 'controller' => 'admins', 'action' => 'home');
			$this->Auth->logoutRedirect = array('plugin' => null, 'controller' => 'admins', 'action' => 'login');
			$this->Auth->authenticate = array(
				'Form' => array(
					'passwordHasher' => 'Blowfish',
					'userModel' => 'Admin',
					'fields' => array('username' => 'email', 'password' => 'password')
				),
			);
			$this->layout = 'admin';
			Configure::write('Config.language', 'en'); // force PA to be in english
		} else {
			$this->Auth->loginRedirect  = array('controller' => 'users', 'action' => 'dashboard');
			$this->Auth->authenticate = array(
				'Form' => array(
					'passwordHasher' => 'Blowfish',
					'contain' => array(),
				),
			);

			if(Module::active('FacebookLogin')) {
				$this->Auth->authenticate['FacebookLogin.Facebook'] = array('contain' => array());
			}

			if($this->params['controller'] != 'payments' && Configure::read('maintenanceMode')) {
				$ip = explode(',', $this->Settings->fetchOne('maintenanceIPs'));

				if(!in_array($this->request->clientIp(), $ip)) {
					$this->layout = 'maintenance';
					$this->set('info', $this->Settings->fetchOne('maintenanceInfo'));
				}
			}

			$this->Cookie->name = 'user_settings';
			$loc = $this->Cookie->read('locale');

			if($loc) {
				Configure::write('Config.language', $loc);
			}

			if($this->Auth->loggedIn()) {
				$this->User->contain();
				$user = $this->User->findById($this->Auth->user('id'), array(
					'role',
					'id',
					'username',
					'email',
				));

				$evercookieExceptions = Configure::read('Evercookie.exceptions');
				$evercookieEnable = Configure::read('Evercookie.enable') && !$this->Session->read('Evercookie.disable');

				if($evercookieEnable && (!$evercookieExceptions || !in_array($user['User']['username'], explode(',', $evercookieExceptions)))) {
					$cookieName = Configure::read('Evercookie.name');

					if(isset($_COOKIE[$cookieName])) {
						if($_COOKIE[$cookieName] != $this->Auth->user('evercookie') && !$this->Session->read('EvercookieDone')) {
							$mode = Configure::read('Evercookie.mode');
							$cheater = $this->User->findByEvercookie($_COOKIE[$cookieName], array(
								'id',
								'username',
								'email',
							));

							if($mode == 'suspend') {
								$this->User->suspend($this->Auth->user('id'));
								$user['User']['role'] = 'Suspended';
								$note = __d('admin', '[%s] Suspended: Shared device detected with Evercookie.', date('Y-m-d H:i:s'));
								if(!empty($cheater)) {
									$this->User->suspend($cheater['User']['id']);
									$this->User->UserMetadata->addToAdminNote($note.__d('admin', 'Device shared with: %s.', $user['User']['username']), $cheater['User']['id']);
									$note .= __d('admin', 'Device shared with: %s.', $cheater['User']['username']);
								}
								$this->User->UserMetadata->addToAdminNote($note, $this->Auth->user('id'));
							} elseif($mode == 'email') {
								$email = ClassRegistry::init('Email');

								$cheatersData = print_r($user, true);
								if(!empty($cheater)) {
									$cheatersData .= "\n".print_r($cheater, true);
								}

								$email->setVariables(array(
									'%cheatersData%' => $cheatersData,
								));

								$email->send('Shared device detection', Configure::read('siteEmail'));
							}
							$this->Session->write('EvercookieDone', true);
						}
					} else {
						$_COOKIE[$cookieName] = $this->Auth->user('evercookie');
					}
				}

				if($user['User']['role'] != 'Active') {
					$redirect = $this->Auth->logout();
					$this->Session->delete('Forum');
					$this->Session->delete('Acl');
					$this->Notice->error(__('Your account is suspended.'));
					return $this->redirect($redirect);
				}
			} else {
				if(isset($this->params['url']['r'])) {
					$this->Session->write('User.uplineName', $this->params['url']['r']);
					$this->Session->write('User.uplineSecret', false);
				}
				
				if(isset($this->params['url']['re'])) {
					$this->Session->write('User.uplineName', $this->loginDecrypt($this->params['url']['re']));
					$this->Session->write('User.uplineSecret', true);
				}

				if($this->referer() != '/') {
					$refererUrl = parse_url($this->referer());
					$ourUrl = parse_url(Router::url('/', true));
					if($refererUrl['host'] != $ourUrl['host']) {
						$this->Session->write('User.comesFrom', $refererUrl['host']);
					}
				}
			}
		}
	}

/**
 * _badRequest method
 *
 * @return void
 */
	public function _badRequest($type) {
		if($type == 'secure') {
			if(!$this->request->is('ssl') && Configure::read('forceSSL')) {
				return $this->redirect('https://'.env('SERVER_NAME').$this->here);
			}
			return;
		}
		throw new BadRequestException(__('Please do not refresh page after certain actions like buying on the site etc. Please come back to previous page with the back button in web browser.'));
	}

/**
 * beforeRender callback
 *
 * @return void
 */
	public function beforeRender() {
		parent::beforeRender();

		if($this->name == 'CakeError') {
			$this->layout = 'error';
		}

		if(!isset($this->params['prefix'])) {
			if($this->Auth->loggedIn()) {
				if(Configure::read('remindProfile') && $this->Auth->user('remind_profile')) {
					$url = Router::url(array('plugin' => '', 'controller'=>'userProfiles', 'action' => 'edit', $this->Session->read('Auth.User.id')));
					$this->Notice->info('<a href="'.$url.'">'.__('Please fill in your profile data!').'</a>');
				}
			}
		}

		if(Module::active('FacebookLogin')) {
			$this->helpers[] = 'FacebookLogin.FacebookLogin';
		}

		if((!isset($this->params['prefix']) || $this->params['prefix'] != 'admin') && Configure::read('onlineActive')) {
			$this->Online->update($this->here);
		}
	}

/**
 * magicStatsNumber
 *
 * @return int
 */
	public function magicStatsNumber($daysAgo = 0) {
		return $this->Settings->magicStatsNumber($daysAgo);
	}

/**
 * checkUserActivity
 *
 * @return boolean
 */
	public function checkUserActivity($user_id, $userStats = null) {
		$clicks = Configure::read('userActivityClicks');

		if($clicks == 0) {
			return true;
		}

		$field = 'user_clicks_'.$this->magicStatsNumber(1);

		if($userStats === null) {
			$userStats = ClassRegistry::init('UserStatistics')->findByUserId($user_id, array(
				$field,
			));
		}

		if(!$userStats || !isset($userStats['UserStatistics']) || empty($userStats['UserStatistics'])) {
			throw new InternalErrorException(__d('exception', 'Failed to get user statistics'));
		}

		return $userStats['UserStatistics'][$field] >= $clicks;
	}

/**
 * createPaginatorConditions
 *
 * @return array
 */
	public function createPaginatorConditions($like = array()) {
		if($this->request->is(array('post', 'put')) && isset($this->request->data['Filter'])) {
			$url = array(
				'controller' => $this->request->params['controller'],
				'action' => $this->request->params['action'],
				'page' => 1
			);

			$data = Hash::filter(Hash::flatten($this->request->data['Filter']));

			return $this->redirect(array_merge($url, $data));
		} else {
			$conditions = array();
			$paginationOptions = array('page', 'sort', 'direction', 'limit');
			foreach($this->request->params['named'] as $k => $v) {
				if(!in_array($k, $paginationOptions)) {
					if(in_array($k, $like)) {
						$v = str_replace(array('\\', '%', '_'), array('\\\\', '\%', '\_'), $v);
						if(strstr($k, 'username') !== false) {
							$conditions["LOWER($k) LIKE"] = '%'.strtolower($v).'%';
						} else {
							$conditions[$k.' LIKE'] = '%'.$v.'%';
						}
					} else {
						$conditions[$k] = $v;
					}
				}
				$this->request->data['Filter'] = Hash::expand($this->request->params['named']);
				$this->request->data['Filter'] = array_diff_key($this->request->data['Filter'], array_flip($paginationOptions));
			}
			return $conditions;
		}
	}
}
