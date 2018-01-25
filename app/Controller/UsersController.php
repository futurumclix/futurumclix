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
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
App::uses('Sanitize', 'Utility');
App::uses('PaymentsInflector', 'Payments');

/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {
/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'User',
		'MembershipsUser',
		'Membership',
		'PaymentGateway',
		'Settings',
		'RentExtensionPeriod',
		'DirectReferralsPrice',
		'RequestObject',
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		'UserPanel',
		'Payments',
		'Session',
		'Location',
		'Captcha',
		'GoogleAuthenticator.GoogleAuthenticator',
	);

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('google_authenticator', 'signUp', 'login', 'verify', 'resendVerificationEmail', 'sendPasswordRequestEmail', 'resetPassword'));

		if(Module::active('BotSystem')) {
			$this->uses[] = 'BotSystem.BotSystemRentedReferral';
		}

		if(strtolower($this->request->params['action']) == 'login') {
			$captchaOn = $this->Settings->fetchOne('captchaOnLogin');
			if($captchaOn) {
				$this->Captcha->protect('login');
			}
		} elseif(strtolower($this->request->params['action']) == 'signup') {
			$captchaOn = $this->Settings->fetchOne('captchaOnRegistration');
			if($captchaOn) {
				$this->Captcha->protect('signUp');
			}
		}
	}

/**
 * signUp method
 *
 * @return void
 */
	public function signUp() {
		if($this->Auth->loggedIn()) {
			$this->Notice->success(__('You are already registered and logged in'));
			return $this->redirect('/');
		}
		$settings = $this->Settings->fetch(array(
			'captchaOnRegistration',
			'captchaType',
			'blockSameSignupIP',
			'checkSignupIpDays',
			'emailVerification',
		));
		$this->set('captchaOnRegistration', $settings['Settings']['captchaOnRegistration'] && $settings['Settings']['captchaType'] != 'disabled');

		if($this->request->is('post') && !isset($this->request->data['User']['short_signup'])) {
			$notify_upline = false;

			$this->request->data('User.role', $settings['Settings']['emailVerification'] ? 'Un-verified' : 'Active');
			$this->request->data('User.signup_ip', $this->request->clientIp());

			if($this->Session->check('User.uplineName')) {
				$this->User->contain(array('ActiveMembership' => array('Membership' => array('direct_referrals_limit', 'points_enabled', 'points_per_dref'))));
				if(($upline = $this->User->findByUsername($this->Session->read('User.uplineName'), array('id', 'refs_count', 'role', 'email', 'username', 'allow_emails')))) {
					if($upline['ActiveMembership']['Membership']['direct_referrals_limit'] == -1 
					   || $upline['ActiveMembership']['Membership']['direct_referrals_limit'] >= $upline['User']['refs_count'] + 1) {
						$this->request->data('User.upline_id', $upline['User']['id']);
						$this->request->data('User.dref_since', date('Y-m-d H:i:s'));
						$notify_upline = $upline['User']['allow_emails'];
					}
				}
			}

			if($this->Session->check('User.comesFrom')) {
				$this->request->data('User.comes_from', $this->Session->read('User.comesFrom'));
			}

			if(!$settings['Settings']['blockSameSignupIP']) {
				unset($this->User->validate['signup_ip']['unique']);
			}

			if($settings['Settings']['blockSameSignupIP'] && $settings['Settings']['checkSignupIpDays'] > 0) {
				unset($this->User->validate['signup_ip']['unique']);

				$this->User->contain();
				$res = $this->User->find('first', array(
					'fields' => array('id'),
					'conditions' => array(
						'User.signup_ip' => $this->request->clientIp(),
						'DATEDIFF(NOW(), User.created) <=' => $settings['Settings']['checkSignupIpDays'],
					)
				));

				if(!empty($res)) {
					return $this->Notice->error(__('Your IP is already in use.'));
				}
			}

			$this->User->create();
			$this->User->data['User']['location'] = $this->Location->getByIp($this->request->clientIp());
			if($this->User->save($this->request->data)) {
				if($notify_upline) {
					$this->_sendNewDRefNotice($upline, $this->request->data);
				}

				if($upline['ActiveMembership']['Membership']['points_enabled']) {
					$this->User->pointsAdd($upline['ActiveMembership']['Membership']['points_per_dref'], $upline['User']['id']);
				}

				if($settings['Settings']['emailVerification']) {
					$this->User->contain(array('UserMetadata'));
					$this->User->read();
					$this->_sendVerificationEmail($this->User->id, $this->User->data);
					$this->Notice->success(__('Please check your e-mail for activation link'));
					return $this->redirect(array('controller' => 'pages', 'action' => 'display', 'finishRegistration'));
				} else {
					$this->Notice->success(__('Your account has been successfully created. Please, log in.'));
					return $this->redirect(array('action' => 'login'));
				}
			}

			$this->Notice->error(__('User could not be saved. Please, try again'));
		}

		if($this->Session->check('User.uplineName') && !$this->Session->read('User.uplineSecret')) {
			$this->set('uplineName', $this->Session->read('User.uplineName'));
		}
	}

/**
 * verify method
 *
 * @throws InternalErrorException
 * @return void
 */
	public function verify($id = null, $token = null) {
		if($this->request->is('get')) {
			if($id === null || $token === null) {
				throw new BadRequestException(__d('exception', 'Wrong arguments'));
			}

			$this->User->contain(array(
				'UserMetadata'
			));

			if(!($user = $this->User->find('first', array('conditions' => array('User.id' => $id))))) {
				throw new NotFoundException(__d('exception', 'Invalid user'));
			}

			if($user['User']['role'] !== 'Un-verified') {
				$this->Notice->success(__('Your account has been already verified.'));
				return $this->redirect(array('action' => 'login'));
			}

			if($user['UserMetadata']['verify_token'] !== $token) {
				throw new NotFoundException(__d('exception', 'Invalid token.'));
			}

			if(!$this->User->UserMetadata->removeVerify($id) || !$this->User->activate($id)) {
				throw new InternalErrorException(__d('exception', 'Failed to activate user.'));
			}

			$this->Notice->success(__('Your account has been successfully verified.'));
			$this->redirect(array('action' => 'login'));
		} else {
			throw new MethodNotAllowedException();
		}
	}

/**
 * emailChange method
 *
 * @return void
 */
	public function emailChange($id = null, $token = null) {
		if($this->request->is('get')) {
			if($id === null || $token === null) {
				throw new BadRequestException(__d('exception', 'Wrong arguments'));
			}

			$this->User->contain(array(
				'UserMetadata',
			));

			if($this->Auth->user('id') != $id || !($user = $this->User->findById($id))) {
				throw new NotFoundException(__d('exception', 'Invalid user'));
			}

			if(!$user['UserMetadata']['next_email']) {
				throw new InternalErrorException(__d('exception', 'New e-mail not found'));
			}

			if($user['UserMetadata']['verify_token'] !== $token) {
				throw new NotFoundException(__d('exception', 'Invalid token'));
			}

			$this->User->id = $id;
			if(!$this->User->saveField('email', $user['UserMetadata']['next_email'])) {
				throw new InternalErrorException(__d('exception', 'Failed to save new email'));
			}

			if(!$this->User->UserMetadata->clearNextEmail($id)) {
				throw new InternalErrorException(__d('exception', 'Failed to clear next email'));
			}

			$this->Notice->success(__('Your e-mail has been successfully saved.'));
			$this->redirect(array('action' => 'dashboard'));
		} else {
			throw new MethodNotAllowedException();
		}
	}

/**
 * _sendNewDRefNotice method
 *
 * @return void
 */
	protected function _sendNewDRefNotice($upline, $ref) {
		if($upline['User']['role'] == 'Active') {
			$email = ClassRegistry::init('Email');

			$email->setVariables(array(
				'%username%' => $ref['User']['username'],
				'%uplineusername%' => $upline['User']['username'],
				'%comesFrom%' => isset($ref['User']['comes_from']) ? $ref['User']['comes_from'] : __('Direct link'),
			));

			$email->send('New direct referral', $upline['User']['email']);
		}
	}

/**
 * _sendVerificationEmail method
 *
 * @return void
 */
	protected function _sendVerificationEmail($userid, $user) {
		$email = ClassRegistry::init('Email');

		$email->setVariables(array(
			'%username%' => $user['User']['username'],
			'%firstname%' => $user['User']['first_name'],
			'%lastname%' => $user['User']['last_name'],
			'%verifyurl%' => Router::url(array('controller' => 'users', 'action' => 'verify', $user['User']['id'], $user['UserMetadata']['verify_token']), true),
		));

		$email->send('Account verification', $user['User']['email']);
	}

/**
 * _sendPasswordRequestEmail method
 *
 * @return void
 */
	protected function _sendPasswordRequestEmail($userid, $user, $token) {
		$email = ClassRegistry::init('Email');

		$email->setVariables(array(
			'%username%' => $user['User']['username'],
			'%firstname%' => $user['User']['first_name'],
			'%lastname%' => $user['User']['last_name'],
			'%confirmationurl%' => Router::url(array('controller' => 'users', 'action' => 'resetPassword', $user['User']['id'], $token), true),
		));

		$email->send('Password request', $user['User']['email']);
	}

/**
 * _sendPasswordEmail method
 *
 * @return void
 */
	protected function _sendPasswordEmail($password, $user, $options = array()) {
		$email = ClassRegistry::init('Email');

		$email->setVariables(array(
			'%username%' => $user['User']['username'],
			'%firstname%' => $user['User']['first_name'],
			'%lastname%' => $user['User']['last_name'],
			'%password%' => $password,
		));

		$email->send('New password', $user['User']['email']);
	}

/**
 * google_authenticator method
 *
 * @return void
 */
	public function google_authenticator() {
		$ga = $this->Session->read('GoogleAuthenticator');
		if(!$ga) {
			throw new NotFoundException(__d('exception', 'Invalid data'));
		}
		if($this->request->is(array('post', 'put'))) {
			if($this->GoogleAuthenticator->check($this->request->data['ga_code'])) {
				$this->Session->write('GoogleAuthenticator.pass', 1);
				$this->Session->write('ExternalLogin', 1);
				return $this->redirect(array('action' => 'login'));
			} else {
				$this->Notice->error(__('Wrong Google Authenticator code'));
			}
		}
	}

/**
 * login method
 *
 * @throws InternalErrorException
 * @return void
 */
	public function login() {
		$isExternalLogin = $this->Session->check('ExternalLogin');
		$settings = $this->Settings->fetch(array(
			'captchaOnLogin',
			'captchaType',
			'blockSameLoginIP',
			'checkLoginIpDays',
			'loginAdsShowMode',
			'googleAuthenticator',
		));
		$this->set('captchaOnLogin', $settings['Settings']['captchaOnLogin'] && $settings['Settings']['captchaType'] != 'disabled');

		if($this->request->is('post') || $isExternalLogin) {
			if($this->Session->read('UserLogin.block')) {
				if($this->Session->read('UserLogin.block') <= time()) {
					$this->Session->delete('UserLogin');
				} else {
					throw new InternalErrorException(__d('exception', 'Too many failed attempts.'));
				}
			}

			if(@in_array('login', $settings['Settings']['googleAuthenticator'])) {
				$ga = $this->Session->read('GoogleAuthenticator');

				if(!$ga || !$ga['pass']) {
					App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
					$passwordHasher = new BlowfishPasswordHasher();

					$this->User->contain(array('UserSecret'));
					$user = $this->User->find('first', array(
						'conditions' => array(
							'username' => $this->request->data['User']['username'],
						),
					));

					if($user && !empty($user) && $passwordHasher->check($this->request->data['User']['password'], $user['User']['password'])) {
						if($user['UserSecret']['mode'] == UserSecret::MODE_GA && !empty($user['UserSecret']['ga_secret'])) {
							$this->Session->write('GoogleAuthenticator', array(
								'secret' => $user['UserSecret']['ga_secret'],
								'pass' => 0,
								'username' => $this->request->data['User']['username'],
								'password' => $this->request->data['User']['password'],
							));
							return $this->redirect(array('action' => 'google_authenticator'));
						}
					}
				} else {
					$this->request->data['User']['username'] = $ga['username'];
					$this->request->data['User']['password'] = $ga['password'];
					$_SERVER['REQUEST_METHOD'] = 'POST';
					$this->Session->delete('ExternalLogin');
				}
			}

			if($this->Auth->login()) {

				switch($this->Auth->user('role')) {
					case 'Un-verified':
						$this->Auth->logout();
						$this->Notice->error(__('Your account is not verified. Please check your e-mail or ').
							'<a href='.Router::url(array('action' => 'resendVerificationEmail'), true).'>'.__('resend verification link').'</a>'
						);
					return;

					case 'Suspended':
						$this->Auth->logout();
						$this->Notice->error(__('Your account is suspended'));
					return;
				}

				if(!$settings['Settings']['blockSameLoginIP']) {
					unset($this->User->validate['last_ip']['unique']);
				}

				if($settings['Settings']['blockSameLoginIP'] && $settings['Settings']['checkLoginIpDays'] > 0) {
					unset($this->User->validate['last_ip']['unique']);

					$this->User->contain();
					$res = $this->User->find('first', array(
						'fields' => array('id'),
						'conditions' => array(
							'User.last_ip' => $this->request->clientIp(),
							'User.id !=' => $this->Auth->user('id'),
							'DATEDIFF(NOW(), User.last_log_in) <=' => $settings['Settings']['checkLoginIpDays'],
						)
					));

					if(!empty($res)) {
						/* TODO: cheater? */
						$this->Auth->logout();
						return $this->Notice->error(__('Your IP is already in use.'));
					}
				}

				if(Configure::read('loginAdsActive')) {
					switch($settings['Settings']['loginAdsShowMode']) {
						case 'day':
							if(time() - strtotime($this->Auth->user('last_log_in')) < 60 * 60 * 24) {
								break;
							}
						case 'login':
							$this->Session->write('showLoginAds', 1);
						break;
					}
				}

				if($this->User->afterLogin($this->Auth->user('id'), $this->Location->getByIp($this->request->clientIp()), $this->request->clientIp())) {
					$this->Session->delete('UserLogin');
					$this->Session->delete('User');
					$this->Session->delete('ExternalLogin');
					return $this->redirect($this->Auth->redirect());
				}
				else {
					$this->Auth->logout();
					if(isset($this->User->validationErrors['last_ip'])) {
						/* we found that someone is sharing IP with other account. cheater? */
						return;
					} else {
						throw new InternalErrorException(__d('exception', 'Failed to update login data'));
					}
				}
			}

			$loginTries = $this->Session->read('UserLogin.fail');

			if($loginTries) {
				$loginTries++;
			} else {
				$loginTries = 1;
			}

			$this->Session->write('UserLogin.fail', $loginTries);

			if($loginTries >= Configure::read('UserLogin.tries')) {
				$this->Session->write('UserLogin.block', strtotime('+30 minutes'));
			}

			if($isExternalLogin) {
				return $this->redirect($this->Session->read('ExternalLogin.redirect'));
			}

			$this->Notice->error(__('Username or password is incorrect.'));
		}
	}

/**
 * logout method
 *
 * @return void
 */
	public function logout() {
		$redirect = $this->Auth->logout();
		$this->Session->delete('Forum');
		$this->Session->delete('Acl');
		$this->Session->delete('ExternalLogin');
		$this->Session->delete('EvercookieDone');
		$this->Session->delete('Evercookie.disable');
		$this->Session->delete('GoogleAuthenticator');
		if($redirect) {
			$this->Notice->success(__('You are logged out.'));
		}
		return $this->redirect($redirect);
	}

/**
 * resendVerificationEmail method
 *
 * @return void
 */
 	public function resendVerificationEmail() {
 		if($this->request->is('post')) {
 			$email = $this->request->data['User']['email'];

 			if(empty($email) || $email == null) {
 				return $this->Notice->error(__('E-mail not found.'));
 			}

			$this->User->contain(array('UserMetadata'));
 			if(!($user = $this->User->find('first', array('conditions' => array('User.email' => $email))))) {
				return $this->Notice->error(__('E-mail not found.'));
			}

			if($user['User']['role'] != 'Un-verified') {
				return $this->Notice->success(__('Your account is already verified.'));
			}

			$this->_sendVerificationEmail($user['User']['id'], $user);
			$this->Notice->success(__('Please check your e-mail for activation link.'));
			$this->redirect(array('action' => 'login'));
 		}
 	}

 /**
 * sendPasswordRequestEmail method
 *
 * @return void
 */
 	public function sendPasswordRequestEmail() {
 		if($this->request->is('post')) {
 			$email = $this->request->data['User']['email'];

 			if(empty($email) || $email == null) {
 				return $this->Notice->error(__('E-mail not found.'));
 			}

 			if(!($user = $this->User->find('first', ['conditions' => ['User.email' => $email]]))) {
				return $this->Notice->error(__('E-mail not found.'));
			}

			if($user['User']['role'] == 'Un-verified') {
				return $this->Notice->error(__('Your account is not verified.'));
			}

			if(!($token = $this->User->UserMetadata->createPasswordResetToken($user['User']['id']))) {
				throw new InternalErrorException(__d('exception', 'Failed to create password reset token.'));
			}

			$this->_sendPasswordRequestEmail($user['User']['id'], $user, $token);
			$this->Notice->success(__('Please check your e-mail for password reset link.'));
			$this->redirect(array('action' => 'login'));
 		}
 	}

/**
 * resetPassword method
 *
 * @return void
 */
 	public function resetPassword($id = null, $token = null) {
 		if($this->request->is('get')) {
			if($id === null || $token === null) {
				throw new BadRequestException(__d('exception', 'Wrong arguments'));
			}

			$this->User->contain(array('UserMetadata' => array('reset_token')));
			$user = $this->User->find('first', array(
				'conditions' => array(
					'User.id' => $id
				),
			));

			if(empty($user)) {
				throw new NotFoundException(__d('exception', 'Invalid user.'));
			}

			if($user['UserMetadata']['reset_token'] !== $token) {
				throw new NotFoundException(__d('exception', 'Invalid token.'));
			}

			$newPassword = $this->User->setRandomPassword($id);

			if($newPassword === false) {
				throw new InternalErrorException(__d('exception', 'Failed to reset password.'));
			}

			$this->_sendPasswordEmail($newPassword, $user);

			if(!$this->User->UserMetadata->clearResetToken($id)) {
				throw new InternalErrorException(__d('exception', 'Failed to clear reset token.'));
			}

			$this->Notice->success(__('New password has been send to your e-mail.'));
			$this->redirect(array('action' => 'login'));
		} else {
			throw new MethodNotAllowedException();
		}
 	}

/**
 * dashboard method
 *
 * @return void
 */
	public function dashboard() {
		$user = $this->UserPanel->getData(array('UserStatistic'));

		$myclicks = array();
		$drclicks = array();
		$rrclicks = array();
		$n = $this->magicStatsNumber(6);

		for($i = 6; $i >= 0; --$i, $n = ($n + 1) % 7) {
			$idx = date('Y-m-d', strtotime("-$i days"));

			$myclicks[$idx] = $user['UserStatistic']['user_clicks_'.$n];
			$drclicks[$idx] = $user['UserStatistic']['dref_clicks_'.$n];
			$drclicksCredited[$idx] = $user['UserStatistic']['dref_clicks_credited_'.$n];
			$rrclicks[$idx] = $user['UserStatistic']['rref_clicks_'.$n];
			$rrclicksCredited[$idx] = $user['UserStatistic']['rref_clicks_credited_'.$n];
		}

		$user['UserStatistic']['credited_commissions'] = $this->User->getCreditedCommissionsAmount($user['User']['id']);
		$user['UserStatistic']['waiting_commissions'] = $this->User->getWaitingCommissionsAmount($user['User']['id']);
		$user['UserStatistic']['waiting_cashouts'] = $this->User->getWaitingCashoutsAmount($user['User']['id']);
		$user['UserStatistic']['total_earnings'] = $this->User->getEarnings($user);

		$activityClicks = Configure::read('userActivityClicks') - $user['UserStatistic']['user_clicks_'.$this->magicStatsNumber()];
		if($activityClicks > 0) {
			$this->Notice->info(__('You need to click %d more ads to earn commissions from referrals tomorrow.', $activityClicks));
		}

		$this->User->Deposits->recursive = -1;
		$refunds = $this->User->Deposits->find('all', array(
			'fields' => array(
				'COUNT(*) as count',
				'gateway',
			),
			'conditions' => array(
				'user_id' => $user['User']['id'],
				'status' => 'Auto-Refunded',
				'DATEDIFF(NOW(), date) <=' => 3,
			),
			'group' => 'gateway',
		));

		foreach($refunds as $v) {
			$this->Notice->info(__('You need verified %s account to make purchases on the site. Your last purchase was refunded.', PaymentsInflector::humanize($v['Deposits']['gateway'])));
		}

		$this->User->Deposits->recursive = 1;
		$this->User->Deposits->bindTitle();
		$pendings = $this->User->Deposits->find('all', array(
			'conditions' => array(
				'Deposits.user_id' => $user['User']['id'],
				'Deposits.status' => 'Pending',
				'Deposits.gateway' => 'ManualPayPal',
				'DATEDIFF(NOW(), Deposits.date) <=' => 1,
			),
			'group' => 'gateway',
		));
		$this->User->Deposits->unbindTitle();

		foreach($pendings as $v) {
			$this->Notice->info(__('You have a %spending deposit%s', '<a href="'.Router::url(array('plugin' => null, 'controller' => 'deposits', 'action' => 'show', $v['Deposits']['id'])).'">', '</a>'));
		}

		$this->set(compact('user', 'myclicks', 'drclicks', 'rrclicks', 'drclicksCredited', 'rrclicksCredited'));
	}

/**
 * _extendReferrals
 *
 * @return boolean
 */
	private function _extendReferrals($upline, $rrefs, $buy_price, $extend) {
		if(Module::active('BotSystem')) {
			$real = array();
			$bots = array();

			foreach($rrefs as $rid) {
				if($rid{0} == 'R') {
					$bots[] = substr($rid, 1);
				} else {
					$real[] = $rid;
				}
			}

			$this->User->RentedRefs->contain();
			$rrefs_count = $this->User->RentedRefs->find('count', array(
				'conditions' => array(
					'id' => $real,
					'rented_upline_id' => $upline['User']['id'],
				),
			));

			$this->User->RentedBots->recursive = -1;
			$rrefs_count += $this->User->RentedBots->find('count', array(
				'conditions' => array(
					'id' => $bots,
					'rented_upline_id' => $upline['User']['id'],
				),
			));
		} else {
			$this->User->RentedRefs->contain();
			$rrefs_count = $this->User->RentedRefs->find('count', array(
				'conditions' => array(
					'id' => $rrefs,
					'rented_upline_id' => $upline['User']['id'],
				),
			));
		}

		if($rrefs_count != count($rrefs)) {
			/* cheater? */
			throw new InternalErrorException(__d('exception', 'Invalid rrefs ids'));
		}

		$price = bcmul($rrefs_count, $buy_price);
		$price = bcmul($price, $extend['modifier']);
		$price = bcsub($price, bcmul($price, bcdiv($extend['discount'], 100)));

		if(bccomp($upline['User']['purchase_balance'], $price) >= 0) {
			if(Module::active('BotSystem')) {
				ClassRegistry::init('BotSystem.BotSystemStatistic')->addData(array('income' => $price));
				if($this->User->RentedRefs->extendReferrals($upline['User']['id'], $real, $extend['days']) && $this->User->RentedBots->extendReferrals($upline['User']['id'], $bots, $extend['days'])) {
					$this->Payments->pay('extend', 'PurchaseBalance', $price, $upline['User']['id'], array(
						'refs_no' => $rrefs_count,
					));
					return true;
				} else {
					throw new InternalErrorException(__d('exception', 'Failed to extend referrals.'));
				}
			} else {
				if($this->User->RentedRefs->extendReferrals($upline['User']['id'], $rrefs, $extend['days'])) {
					$this->Payments->pay('extend', 'PurchaseBalance', $price, $upline['User']['id'], array(
						'refs_no' => $rrefs_count,
					));
					return true;
				} else {
					throw new InternalErrorException(__d('exception', 'Failed to extend referrals.'));
				}
			}
		} else {
			$this->Notice->error(__('Sorry, you do not have enough funds.'));
		}

		return false;
	}

/**
 * recycleReferral
 *
 * @return void
 */
	public function recycleReferral($ref_id = null) {
		$this->User->RentedUpline->id = $this->Auth->user('id');
		$upline = $this->UserPanel->getData(array(
			'ActiveMembership.Membership' => array(
				'referral_recycle_cost',
			),
		));
		if(($msg = $this->User->recycleReferrals($upline, array($ref_id), $upline['ActiveMembership']['Membership']['referral_recycle_cost'])) === true) {
			$this->Payments->pay('recycle', 'PurchaseBalance', $upline['ActiveMembership']['Membership']['referral_recycle_cost'], $upline['User']['id'], array(
				'refs_no' => 1,
			));
		} elseif(is_string($msg)) {
			$this->Notice->error($msg);
		} else {
			$this->Notice->error(__('An error occurred. Please try again.'));
		}
		return $this->redirect($this->referer());
	}

/**
 * rentedReferrals method
 *
 * @return void
 */
	public function rentedReferrals() {
		$settings = $this->Settings->fetch(array(
			'rentPeriod',
			'autoRenewDays',
		));
		$autoRenewDays = array(0 => __('Disabled'));
		$ard = explode(',', $settings['Settings']['autoRenewDays']);
		foreach($ard as $v) {
			$autoRenewDays[$v] = __('%d days', $v);
		}
		$user = $this->UserPanel->getData(array(
			'ActiveMembership.Membership' => array(
				'referral_recycle_cost',
				'RentedReferralsPrice',
			),
		));
		$range = $this->User->MembershipsUser->Membership->RentedReferralsPrice->getRangeByRRefsNo(
			$user['ActiveMembership']['Membership']['RentedReferralsPrice'], 
			$user['User']['rented_refs_count']
		);

		$autoRenewExtends = array(0 => __('Disabled'));
		$eps = $this->RentExtensionPeriod->find('all', array(
			'order' => 'days',
		));
		foreach($eps as $v) {
			$autoRenewExtends[$v['RentExtensionPeriod']['days']] = __('%d days', $v['RentExtensionPeriod']['days']);
		}

		if($range == null) {
			$costs = null;
			$options = null;
		} else {
			$costs = array(
				'recycle' => $user['ActiveMembership']['Membership']['referral_recycle_cost'],
				'buy' => $range['price'],
				'extend' => array(),
			);
			foreach($eps as $ep) {
				$id = $ep['RentExtensionPeriod']['id'];
				unset($ep['RentExtensionPeriod']['id']);
				$costs['extend'][$id] = $ep['RentExtensionPeriod'];
				$costs['extend'][$id]['modifier'] = bcdiv($ep['RentExtensionPeriod']['days'], $settings['Settings']['rentPeriod']);
			}

			$options = array(
				'recycle' => __('I want to recycle them --'),
			);

			foreach($costs['extend'] as $id => $ex) {
				$options[100 + $id] = __('I want to extend their rental time for %d days --', $ex['days']);
			}
		}

		if($this->request->is('post')) {
			if(!isset($this->request->data['action']) || empty($this->request->data['action'])) {
				$this->Notice->error(__('Please select action.'));
				return;
			}

			$rrefs = array();
			$refs_no = 0;
			foreach($this->request->data['rrefs'] as $rref => $on) {
				if($on) {
					$rrefs[] = $rref;
					$refs_no++;
				}
			}

			if(empty($rrefs)) {
				$this->Notice->error(__('Please select at least one rented referral'));
				return $this->redirect($this->referer());
			}
			if($this->request->data['action'] == 'recycle') {
				if(($msg = $this->User->recycleReferrals($user, $rrefs, $costs['recycle'])) === true) {
					$price = bcmul($refs_no, $costs['recycle']);
					$this->Payments->pay('recycle', 'PurchaseBalance', $price, $user['User']['id'], array(
						'refs_no' => $refs_no,
					));
				} elseif(is_string($msg)) {
					$this->Notice->error($msg);
				} else {
					$this->Notice->error(__('An error occurred. Please try again.'));
				}
			} elseif(is_numeric($this->request->data['action']) && $this->request->data['action'] > 100) {
				$id = $this->request->data['action'] - 100;
				if(!isset($costs['extend'][$id])) {
					/* cheater? */
					throw new InternalErrorException(__d('exception', 'Invalid RentExtensionPeriod'));
				}
				if(!$this->_extendReferrals($user, $rrefs, $costs['buy'], $costs['extend'][$id])) {
					$this->Notice->error(__('Failed to extend rented referrals. Please try again later.'));
				} else {
					$this->Notice->success(__('Rented referrals extended.'));
				}
			}
		}

		$this->Paginator->settings['limit'] = $user['ActiveMembership']['Membership']['results_per_page'];

		if(Module::active('BotSystem')) {
			$refs = $this->Paginator->paginate('BotSystemRentedReferral', array('rented_upline_id' => $this->Auth->user('id')));
		} else {
			$this->User->contain(array('UserStatistic' => array('clicks_as_rref', 'last_click_date', 'earned_as_rref')));
			$this->User->recursive = -1;
			$this->User->bindClicksAVG();
			$this->Paginator->settings['fields'] = array('id', 'username', 'rent_starts', 'rent_ends', 'clicks_avg_as_rref', 'id as url_id');
			$refs = $this->Paginator->paginate(array(
				'User.rented_upline_id' => $this->Auth->user('id'),
			));
		}

		$this->set(compact('user', 'refs', 'costs', 'options', 'autoRenewDays', 'autoRenewExtends'));
		$this->set('breadcrumbTitle', __('Rented Referrals'));
	}

/**
 * directReferrals method
 *
 * @return void
 */
	public function directReferrals($onlyCredited = 'false') {
		$onlyCredited = $onlyCredited === 'true';
		$settings = $this->Settings->fetch('deleteReferralsBalance');
		$mode = $settings['Settings']['deleteReferralsBalance'];
		$this->User->contain(array('ActiveMembership.Membership' => array('direct_referrals_delete_cost', 'results_per_page')));
		$user = $this->User->findById($this->Auth->user('id'));
		$this->Paginator->settings['limit'] = $user['ActiveMembership']['Membership']['results_per_page'];

		if($this->request->is('post')) {
			if($this->request->data['action'] == 'delete') {
				$refsIds = array();

				foreach($this->request->data['Referrals'] as $id => $on) {
					if($on) {
						$refsIds[] = $id;
					}
				}

				if(empty($refsIds)) {
					$this->Notice->error(__('Please select at least one direct referral.'));
					return $this->redirect($this->referer());
				}

				foreach($refsIds as $refId) {
					$this->User->contain();
					$ref = $this->User->findById($refId);

					if(empty($ref)) {
						throw new NotFoundException(__d('exception', 'Invalid referral id'));
					}

					if($ref['User']['upline_id'] != $this->Auth->user('id')) {
						throw new InternalErrorException(__d('exception', 'You cannot delete someone else referral'));
					}
				}

				$cost = bcmul($user['ActiveMembership']['Membership']['direct_referrals_delete_cost'], count($refsIds));

				$canAfford = false;
				$this->User->id = $this->Auth->user('id');
				$dataSource = $this->User->getDataSource();
				$dataSource->begin();

				if(($mode == 'purchase' || $mode == 'both') && bccomp($user['User']['purchase_balance'], $cost) >= 0) {
					$newbalance = bcsub($user['User']['purchase_balance'], $cost);

					if(!$this->User->saveField('purchase_balance', $newbalance)) {
						$dataSource->rollback();
						throw new InternalErrorException(__d('exception', 'Failed to change purchase balance.'));
					} else {
						$canAfford = true;
					}
				} elseif(($mode == 'account' || $mode == 'both') && bccomp($user['User']['account_balance'], $cost) >= 0) {
					$newbalance = bcsub($user['User']['account_balance'], $cost);

					if(!$this->User->saveField('account_balance', $newbalance)) {
						$dataSource->rollback();
						throw new InternalErrorException(__d('exception', 'Failed to change account balance.'));
					} else {
						$canAfford = true;
					}
				} else {
					$this->Notice->error(__('You do not have sufficient funds on your balance.'));
				}

				if($canAfford) {
					if($this->User->removeDirectUplines($this->Auth->user('id'), $refsIds)) {
						$dataSource->commit();
						$this->Notice->success(__('Referrals removed successfully.'));
						$this->Paginator->settings['page'] = 0;
					} else {
						$dataSource->rollback();
						$this->Notice->error(__('Failed to remove referrals. Please, try again.'));
					}
				}
			} else {
				$this->Notice->error(__('Please select action.'));
			}
		}

		$user = $this->UserPanel->getData(array('ActiveMembership.Membership' => 'direct_referrals_delete_cost'));

		$this->User->contain(array('UserStatistic' => array('clicks_as_dref', 'clicks_as_dref_credited', 'last_click_date', 'earned_as_dref')));
		$this->User->recursive = -1;
		$this->User->bindClicksAVG($onlyCredited);

		try {
			$this->paginate = array('order' => 'User.created DESC');
			$refs = $this->Paginator->paginate(array(
				'User.upline_id' => $this->Auth->user('id'),
			));
		} catch(NotFoundException $e) {
			$this->redirect(array('action' => 'directReferrals'));
		}

		$this->set(compact('user', 'membership', 'refs', 'onlyCredited'));
		$this->set('actions', array(
			'delete' => __('I want to delete them -- for $0.01')
		));
		$this->set('breadcrumbTitle', __('Direct Referrals'));
	}

/**
 * autoRenew method
 *
 * @return void
 */
	public function autoRenew() {
		$this->request->allowMethod(array('post', 'put'));

		if(!isset($this->request->data['User']['auto_renew_extend'])) {
			$this->request->data['User']['auto_renew_extend'] = 0;
		}
		if(!isset($this->request->data['User']['auto_renew_days'])) {
			$this->request->data['User']['auto_renew_days'] = 0;
		}
		if(!isset($this->request->data['User']['id'])) {
			$this->request->data['User']['id'] = $this->Auth->user('id');
		}

		$autoRenewDays = $this->Settings->fetchOne('autoRenewDays');
		$ard = explode(',', $autoRenewDays);
		$ard[] = 0; /* 0 == disabled, must be always available */

		if(!in_array($this->request->data['User']['auto_renew_days'], $ard)) {
			/* cheater? */
			throw new InternalErrorException(__d('exception', 'Invalid autorenew value'));
		}

		$this->RentExtensionPeriod->recursive = -1;
		$avail = Hash::extract($this->RentExtensionPeriod->find('all'), '{n}.RentExtensionPeriod.days');
		$avail[] = 0;

		if(!in_array($this->request->data['User']['auto_renew_extend'], $avail)) {
			throw new InternalErrorException(__d('exception', 'Invalid referrals package'));
		}

		if($this->User->save($this->request->data, true, array('auto_renew_days', 'auto_renew_extend'))) {
			$this->Notice->success(__('AutoRenew settings changed successfully.'));
		} else {
			$this->Notice->error(__('Failed to change AutoRenew settings. Please, try again.'));
		}

		return $this->redirect($this->referer());
	}

/**
 * directReferralStats method
 *
 * @return void
 */
	public function directReferralStats($ref_id) {
		$this->request->allowMethod('ajax');
		$this->layout = 'ajax';

		if(!$this->User->exists($ref_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}

		$this->User->id = $ref_id;
		$this->User->contain(array('UserStatistic'));
		$this->User->read(array(
			'upline_id',
			'username',
			'UserStatistic.user_clicks_0',
			'UserStatistic.user_clicks_1',
			'UserStatistic.user_clicks_2',
			'UserStatistic.user_clicks_3',
			'UserStatistic.user_clicks_4',
			'UserStatistic.user_clicks_5',
			'UserStatistic.user_clicks_6',
			'UserStatistic.clicks_as_dref_credited_0',
			'UserStatistic.clicks_as_dref_credited_1',
			'UserStatistic.clicks_as_dref_credited_2',
			'UserStatistic.clicks_as_dref_credited_3',
			'UserStatistic.clicks_as_dref_credited_4',
			'UserStatistic.clicks_as_dref_credited_5',
			'UserStatistic.clicks_as_dref_credited_6',
		));

		if($this->User->data['User']['upline_id'] != $this->Auth->user('id')) {
			throw new InternalErrorException(__d('exception', 'Invalid referral'));
		}

		$drclicks = array();
		$drclicksCredited = array();
		$n = $this->magicStatsNumber(6);

		for($i = 6; $i >= 0; --$i, $n = ($n + 1) % 7) {
			$idx = date('Y-m-d', strtotime("-$i days"));
			$drclicks[$idx] = $this->User->data['UserStatistic']['user_clicks_'.$n];
			$drclicksCredited[$idx] = $this->User->data['UserStatistic']['clicks_as_dref_credited_'.$n];
		}

		$this->set('refname', $this->User->data['User']['username']);
		$this->set(compact('drclicks', 'drclicksCredited'));
	}

/**
 * rentedReferralStats method
 *
 * @return void
 */
	public function rentedReferralStats($ref_id) {
		$this->request->allowMethod('ajax');
		$this->layout = 'ajax';

		if(Module::active('BotSystem') && $ref_id{0} == 'R') {
			$data = ClassRegistry::init('BotSystem.BotSystemBot')->getStatistics(substr($ref_id, 1));
			$refname = $ref_id;
		} else {
			if(!$this->User->exists($ref_id)) {
				throw new NotFoundException(__d('exception', 'Invalid user'));
			}

			$this->User->id = $ref_id;
			$this->User->contain(array('UserStatistic'));
			$this->User->read(array(
				'rented_upline_id',
				'username',
				'UserStatistic.user_clicks_0',
				'UserStatistic.user_clicks_1',
				'UserStatistic.user_clicks_2',
				'UserStatistic.user_clicks_3',
				'UserStatistic.user_clicks_4',
				'UserStatistic.user_clicks_5',
				'UserStatistic.user_clicks_6',
				'UserStatistic.clicks_as_rref_credited_0',
				'UserStatistic.clicks_as_rref_credited_1',
				'UserStatistic.clicks_as_rref_credited_2',
				'UserStatistic.clicks_as_rref_credited_3',
				'UserStatistic.clicks_as_rref_credited_4',
				'UserStatistic.clicks_as_rref_credited_5',
				'UserStatistic.clicks_as_rref_credited_6',
			));

			if($this->User->data['User']['rented_upline_id'] != $this->Auth->user('id')) {
				throw new InternalErrorException(__d('exception', 'Invalid referral'));
			}
			$data = $this->User->data['UserStatistic'];
			$refname = $this->User->data['User']['username'];
		}

		$rrclicks = array();
		$rrclicksCredtied = array();
		$n = $this->magicStatsNumber(6);

		for($i = 6; $i >= 0; --$i, $n = ($n + 1) % 7) {
			$idx = date('Y-m-d', strtotime("-$i days"));
			$rrclicks[$idx] = $data['user_clicks_'.$n];
			$rrclicksCredited[$idx] = $data['clicks_as_rref_credited_'.$n];
		}

		$this->set(compact('rrclicks', 'rrclicksCredited', 'refname'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->request->allowMethod('post', 'delete');
		$this->User->id = $id;

		if(!$this->User->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}
		if($this->Auth->user('id') != $id) {
			throw new NotFoundException(__d('exception', 'You cannot delete someone else'));
		}
		if($this->User->delete()) {
			$this->Notice->success(__('User has been deleted'));
		} else {
			$this->Notice->error(__('User could not be deleted. Please, try again'));
		}
		return $this->redirect(array('action' => 'index'));
	}

/**
 * unhookDirectReferral method
 *
 * @param string $refId
 * @return void
 */
	public function unhookDirectReferral($refId) {
		$this->request->allowMethod('post', 'delete');

		$this->User->contain();
		$ref = $this->User->findById($refId);

		if(empty($ref)) {
			throw new NotFoundException(__d('exception', 'Invalid referral id'));
		}

		if($ref['User']['upline_id'] != $this->Auth->user('id')) {
			throw new InternalErrorException(__d('exception', 'You cannot delete someone else referral'));
		}

		$this->User->id = $this->Auth->user('id');
		$this->User->contain(array('ActiveMembership.Membership.direct_referrals_delete_cost'));
		$this->User->read();

		$cost = $this->User->data['ActiveMembership']['Membership']['direct_referrals_delete_cost'];

		if(bccomp($this->User->data['User']['purchase_balance'], $cost) >= 0) {
			$dataSource = $this->User->getDataSource();
			$dataSource->begin();
			$newbalance = bcsub($this->User->data['User']['purchase_balance'], $cost);

			if(!$this->User->saveField('purchase_balance', $newbalance)) {
				$dataSource->rollback();
				throw new InternalErrorException(__d('exception', 'Failed to change purchase balance.'));
			}

			if($this->User->removeDirectUpline($refId, $this->Auth->user('id'))) {
				$dataSource->commit();
				$this->Notice->success(__('Referral removed successfully.'));
			} else {
				$dataSource->rollback();
				$this->Notice->error(__('Failed to remove referral. Please, try again.'));
			}
		} else {
			$this->Notice->error(__('You do not have sufficient funds on your Purchase Balance.'));
		}

		return $this->redirect($this->referer());
	}

/**
 * _creditTransferCommission
 *
 * @retuirn void
 */
	private function _creditTransferCommission($ref_id, $upline_id, $transfer_amount, $deposit_id) {
		$settings = $this->Settings->fetch('commissionTo');
		$this->User->Upline->id = $upline_id;

		$this->User->Upline->contain(array(
			'ActiveMembership.Membership' => array(
				'transfering_commission',
				'fund_commission',
				'commission_delay',
				'commission_items',
			)
		));
		$this->User->Upline->read(array('id'));

		if(!empty($this->User->Upline->data) && $this->User->Upline->data['ActiveMembership']['Membership']['transfering_commission']) {
			if(strpos($this->User->Upline->data['ActiveMembership']['Membership']['commission_items'], 'deposit') !== false) {
				$amount = bcmul(bcdiv($this->User->Upline->data['ActiveMembership']['Membership']['fund_commission'], '100'), $transfer_amount);

				if(bccomp($amount, '0') == 1) {
					$this->User->Upline->Commissions->addNew($ref_id, $amount, $settings['Settings']['commissionTo'], $deposit_id);
				}
			}
		}
	}

/**
 * transfer
 *
 * @return void
 */
	public function transfer() {
		$keys = array(
			'enableTransfers',
			'maximumTransfer',
			'minimumTransfer',
		);

		$settings = $this->Settings->fetch($keys)['Settings'];
		$user = $this->UserPanel->getData();

		$gData['AccountBalance'] = array(
			'name' => __('Account Balance'),
			'minimum_deposit_amount' => $settings['minimumTransfer'],
			'maximum_deposit_amount' => $settings['maximumTransfer'],
			'deposit_fee_amount' => '0',
			'deposit_fee_percent' => '0',
		);

		if($this->request->is('post')) {
				if(isset($this->request->data['amount']) && $this->User->checkMonetary(array($this->request->data['amount']))) {
					if((bccomp($this->request->data['amount'], $gData['AccountBalance']['maximum_deposit_amount']) <= 0
						  || bccomp($gData['AccountBalance']['maximum_deposit_amount'], '0') == 0)) {
						if(bccomp($this->request->data['amount'], $gData['AccountBalance']['minimum_deposit_amount']) >= 0) {
							if(bccomp($this->request->data['amount'], $user['User']['account_balance']) <= 0) {
								if($this->User->accountBalanceSub($this->request->data['amount'], $user['User']['id'])) {
									if($this->User->purchaseBalanceAdd($this->request->data['amount'], $user['User']['id'])) {
										$this->Notice->success(__('Transfer completed successfully'));
										$deposit_id = null;
										$depositData = array(
											'user_id' => $user['User']['id'],
											'gateway' => 'AccountBalance',
											'amount' => $this->request->data['amount'],
											'account' => $user['User']['id'],
											'status' => 'Success',
											'item' => 'transfer',
											'gatewayid' => md5(time()),
											'date' => date('Y-m-d H:i:s'),
										);
										$this->User->Deposits->create();
										if($this->User->Deposits->save($depositData)) {
											$deposit_id = $this->User->Deposits->id;
										}
										if($user['User']['upline_id'] != null) {
											$this->_creditTransferCommission($user['User']['id'], $user['User']['upline_id'], $this->request->data['amount'], $deposit_id);
										}
										$this->User->UserStatistic->newTransfer($user['User']['id'], $this->request->data['amount']);
									} else {
										$this->User->accountBalanceAdd($this->request->data['amount'], $user['User']['id']);
										$this->Notice->error(__('Error when transferring funds, please try again later.'));
									}
								} else {
									$this->Notice->error(__('Error when transferring funds, please try again later.'));
								}
							} else {
								$this->Notice->error(__('Not enough funds on Account Balance.'));
							}
						} else {
							$this->Notice->error(__('Minimum deposit amount is %s.', CurrencyFormatter::format($gData['AccountBalance']['minimum_deposit_amount'])));
						}
					} else {
						$this->Notice->error(__('Maximum deposit amount is %s.', CurrencyFormatter::format($gData['AccountBalance']['maximum_deposit_amount'])));
					}
				} else {
					$this->Notice->error(__('Please enter an amount.'));
				}
		}

		$this->set(compact('user'));
		$this->set('gData', $gData);
		$this->set('breadcrumbTitle', __('Add Funds'));
	}

/**
 * deposit
 *
 * @return void
 */
	public function deposit() {
		$settings = $this->Settings->fetch('enableTransfers');
		$user = $this->UserPanel->getData();

		$activeGateways = $this->Payments->getActiveDepositsHumanized();

		if(($key = array_search('Purchase Balance', $activeGateways)) !== false) {
			unset($activeGateways[$key]);
		}

		$activeGateways['AccountBalance'] = __('Account Balance');

		$gData = $this->PaymentGateway->find('all', array(
			'conditions' => array(
				'deposits' => true,
				'name !=' => 'PurchaseBalance'
			),
			'fields' => array(
				'name',
				'minimum_deposit_amount',
				'deposit_fee_amount',
				'deposit_fee_percent',
			),
		));

		$gData = $this->PaymentGateway->arrayByName($gData);

		if($this->request->is('post')) {
			if(isset($this->request->data['gateway'])) {
				if(isset($this->request->data['amount']) && $this->User->checkMonetary(array($this->request->data['amount']))) {
					if(isset($gData[$this->request->data['gateway']])) {
						if(bccomp($this->request->data['amount'], $gData[$this->request->data['gateway']]['minimum_deposit_amount']) >= 0) {
							return $this->Payments->pay('deposit', $this->request->data['gateway'], $this->request->data['amount'], $this->Auth->user('id'));
						} else {
							$this->Notice->error(__('Minimum deposit amount is %s.', CurrencyFormatter::format($gData[$this->request->data['gateway']]['minimum_deposit_amount'])));
						}
					} else {
						/* cheater? */
						throw new InternalErrorException(__d('exception', 'Invalid gateway'));
					}
				} else {
					$this->Notice->error(__('Please enter amount.'));
				}
			} else {
				$this->Notice->error(__('Please select payment gateway.'));
			}
		}

		$this->set('enableTransfers', $settings['Settings']['enableTransfers']);
		$this->set(compact('user', 'activeGateways'));
		$this->set('gData', json_encode($gData));
		$this->set('breadcrumbTitle', __('Add Funds'));
	}

/**
 * cashout method
 *
 * @return void
 */
	public function cashout() {
		$user_id = $this->Auth->User('id');
		$this->set('breadcrumbTitle', __('Withdraw funds'));

		$this->User->contain(array('ActiveMembership.Membership', 'LastCashout', 'UserSecret', 'UserStatistic' => array('total_clicks')));
		$user = $this->User->findById($user_id);
		$membership = &$user['ActiveMembership'];
		$this->set(compact('user'));

		$this->User->UserStatistic->recursive = -1;
		$totals = $this->User->UserStatistic->findByUserId($user_id, array(
			'total_cashouts',
			'total_deposits',
			'total_rrefs_clicks_earned',
			'total_external_deposits',
		));

		if($user['ActiveMembership']['Membership']['total_cashouts_limit_mode'] == Membership::TOTAL_CASHOUTS_LIMIT_VALUE) {
			$limit = bcsub($user['ActiveMembership']['Membership']['total_cashouts_limit_value'], $totals['UserStatistic']['total_cashouts']);

			$userROI = $totals['UserStatistic']['total_cashouts'];
			$maxROI = $user['ActiveMembership']['Membership']['total_cashouts_limit_value'];
		} elseif($user['ActiveMembership']['Membership']['total_cashouts_limit_mode'] == Membership::TOTAL_CASHOUTS_LIMIT_PERCENTAGE_DEPOSITS) {
			if(bccomp($totals['UserStatistic']['total_external_deposits'], '0') != 0) {
				$limit = bcdiv($user['ActiveMembership']['Membership']['total_cashouts_limit_percentage'], 100);
				$limit = bcmul($limit, $totals['UserStatistic']['total_external_deposits']);
				$limit = bcsub($limit, $totals['UserStatistic']['total_cashouts']);

				$userROI = bcdiv(bcmul($totals['UserStatistic']['total_cashouts'], 100), $totals['UserStatistic']['total_external_deposits']);
			} else {
				$userROI = 0;
			}
			$maxROI = $user['ActiveMembership']['Membership']['total_cashouts_limit_percentage'];
		} elseif($user['ActiveMembership']['Membership']['total_cashouts_limit_mode'] == Membership::TOTAL_CASHOUTS_LIMIT_PERCENTAGE_RR_INCOME) {
			$userROI = $this->User->UserStatistic->getROI($user_id, $totals);
			$maxROI = $user['ActiveMembership']['Membership']['maximum_roi'];

			if(bccomp($userROI, '0') != 0) { // NOTE: 0 on TOTAL_CASHOUTS_LIMIT_PERCENTAGE_RR_INCOME means unlimited
				$leftROI = bcsub($maxROI, $userROI);

				if(bccomp($leftROI, '0') <= 0) {
					$this->set('mode', $user['ActiveMembership']['Membership']['total_cashouts_limit_mode']);
					$this->set(compact('totals', 'userROI', 'maxROI'));
					return $this->Notice->info(__('Sorry, you exceed total ROI limit.'));
				}

				if(bccomp($totals['UserStatistic']['total_external_deposits'], '0') > 0) {
					$limit = bcmul(bcdiv($leftROI, 100), $totals['UserStatistic']['total_external_deposits']);
				} else {
					$limit = bcmul(bcdiv($leftROI, 100), $totals['UserStatistic']['total_rrefs_clicks_earned']);
				}

				if(bccomp($limit, '0') == 0) { // NOTE: 0 on TOTAL_CASHOUTS_LIMIT_PERCENTAGE_RR_INCOME means unlimited
					unset($limit);
				}
			} else {
				if(bccomp($totals['UserStatistic']['total_cashouts'], '0') == 0 && bccomp($totals['UserStatistic']['total_external_deposits'], '0') > 0) {
					$limit = bcdiv(bcmul($totals['UserStatistic']['total_external_deposits'], $maxROI), '100');
				} elseif(bccomp($totals['UserStatistic']['total_cashouts'], '0') == 0 && bccomp($totals['UserStatistic']['total_rrefs_clicks_earned'], '0') > 0) {
					$limit = bcdiv(bcmul($totals['UserStatistic']['total_rrefs_clicks_earned'], $maxROI), '100');
				}
			}
		} else {
			$userROI = 0;
		}

		$this->set('mode', $user['ActiveMembership']['Membership']['total_cashouts_limit_mode']);
		$this->set(compact('totals', 'userROI', 'maxROI', 'limit'));

		if(bccomp($user['User']['account_balance'], 0) <= 0) {
			return $this->Notice->info(__('Sorry, you need funds on Account Balance to cashout.'));
		}

		$settings = $this->Settings->fetch(array('withdrawClicks', 'cashoutBlockTime', 'cashoutMode', 'googleAuthenticator'));

		if(@in_array('cashout', $settings['Settings']['googleAuthenticator'])
			&& $user['UserSecret']['mode'] == UserSecret::MODE_GA && !empty($user['UserSecret']['ga_secret'])) {
			$googleAuthenticator = true;
		} else {
			$googleAuthenticator = false;
		}

		$clicksLeft = $settings['Settings']['withdrawClicks'] - $user['UserStatistic']['total_clicks'];
		if($clicksLeft > 0) {
			return $this->Notice->info(__('Sorry, but you need %d more clicks to cashout.', $clicksLeft));
		}

		$minimum = explode(',', $user['ActiveMembership']['Membership']['minimum_cashout']);
		$minimum = isset($minimum[$user['User']['cashouts']]) ? $minimum[$user['User']['cashouts']] : array_pop($minimum);

		if(bccomp($minimum, $user['User']['account_balance']) >= 1) {
			return $this->Notice->info(__('Sorry, but minimum cashout for you is %s', CurrencyFormatter::format($minimum)));
		}

		if($user['LastCashout']['id'] !== null) {
			$next = new DateTime($user['LastCashout']['created']);
			$next->add(new DateInterval("P{$membership['Membership']['cashout_waiting_time']}D"));
			$today = new DateTime('today');
			$days = $today->diff($next)->format('%R%a');

			if($days > 0) {
				return $this->Notice->info(__('Sorry, but you need to wait %d more day(s). You can request next cashout at %s', $days, $next->format('Y-m-d H:i:s')));
			}
		}

		if(!$membership['Membership']['allow_more_cashouts']) {
			$waiting = $this->User->Cashouts->find('count', array(
				'conditions' => array(
					'user_id' => $user_id,
					'status' => array('New', 'Pending'),
				),
			));
			if($waiting > 0) {
				return $this->Notice->info(__('Sorry, you are allowed to have only one pending cashout request.'));
			}
		}

		$maxAmount = $amount = $membership['Membership']['maximum_cashout_amount'];

		if(bccomp($amount, $user['User']['account_balance']) == 1 || bccomp($amount, '0') == 0) {
			$maxAmount = $amount = $user['User']['account_balance'];
		}

		if(isset($limit) && bccomp($maxAmount, $limit) == 1) {
			$amount = $maxAmount = $limit;
		}

		if(bccomp($maxAmount, '0') <= 0) {
			return $this->Notice->info(__('Sorry, you exceed total cashouts limit.'));
		}

		$activeGateways = $gData = $this->Payments->getActiveCashoutsHumanized();
		if(empty($activeGateways)) {
			$this->Notice->error(__('Sorry, there are currently no available gateways. Please, try again later.'));
			return $this->redirect(array('action' => 'dashboard'));
		}
		unset($activeGateways['PurchaseBalance']);

		$mode = $settings['Settings']['cashoutMode'];

		if($mode == 'most') {
			$fields = array();

			foreach($activeGateways as $k => $v) {
				$fields[] = PaymentsInflector::underscore($k).'_deposits';
			}
			$this->User->UserStatistic->recursive = -1;
			$stats = $this->User->UserStatistic->findByUserId($user_id, $fields);

			$gData = array();
			$max = 0;

			foreach($stats['UserStatistic'] as $k => $v) {
				if(bccomp($v, $max) == 1) {
					$max = $v;
				}
			}

			foreach($stats['UserStatistic'] as $k => $v) {
				if(bccomp($v, $max) == 0) {
					$v = 'unlimited';
				} else {
					$v = 'disabled';
				}
				$gData[substr($k, 0, strpos($k, '_deposits'))] = $v;
			}
		} elseif($mode == 'mostUnlimited') {
			$fields = array();

			foreach($activeGateways as $k => $v) {
				$fields[] = PaymentsInflector::underscore($k).'_deposits';
				$fields[] = PaymentsInflector::underscore($k).'_cashouts';
			}

			$this->User->UserStatistic->recursive = -1;
			$stats = $this->User->UserStatistic->findByUserId($user_id, $fields);

			$gData = array();
			$max = 0;

			foreach($activeGateways as $k => $v) {
				$vA = $stats['UserStatistic'][PaymentsInflector::underscore($k).'_deposits'];
				if(bccomp($vA, $max) == 1) {
					$max = $vA;
				}
			}

			foreach($activeGateways as $k => $v) {
				$underscoreName = PaymentsInflector::underscore($k);
				if(bccomp($stats['UserStatistic'][$underscoreName.'_deposits'], $max) == 0) {
					$gData[$underscoreName] = 'unlimited';
				} else {
					$a = bcsub($stats['UserStatistic'][$underscoreName.'_deposits'], $stats['UserStatistic'][$underscoreName.'_cashouts']);

					if(bccomp($a, '0') == 1) {
						$gData[$underscoreName] = $a;
					} else {
						$gData[$underscoreName] = 'disabled';
					}
				}
			}
		} elseif($mode == 'all') {
			$gData = array();
			foreach($activeGateways as $k => $v) {
				$gData[PaymentsInflector::underscore($k)] = 'unlimited';
			}
		} else {
			throw new InternalErrorException(__d('exception', 'Wrong cashout configuration'));
		}

		$accounts = array_diff(array_keys($gData), array('purchase_balance'));
		$this->User->UserProfile->recursive = -1;

		$fields = array();
		foreach($accounts as $account) {
			$fields[] = $account;
			$fields[] = $account.'_modified';
		}

		$accounts = $this->User->UserProfile->findByUserId($user_id, $fields)['UserProfile'];

		foreach($gData as $k => $v) {
			if(!isset($accounts[$k]) || empty($accounts[$k])) {
				$gData[$k] = array('state' => 'noAccount');
			} elseif($v == 'disabled') {
				$gData[$k] = array('state' => 'disabled');
			} elseif($accounts[$k.'_modified'] !== null && date('Y-m-d H:i:s', strtotime($accounts[$k.'_modified']." +{$settings['Settings']['cashoutBlockTime']} hour")) > date('Y-m-d H:i:s')) {
				$gData[$k] = array('state' => 'blocked', 'available' => date('Y-m-d H:i:s', strtotime($accounts[$k.'_modified']." +{$settings['Settings']['cashoutBlockTime']} hour")));
			} elseif($v == 'unlimited') {
				$fee = $this->Payments->getCashoutFee($amount, PaymentsInflector::classify($k));

				$gData[$k] = array(
					'state' => $v,
					'amount' => $amount,
					'fee' => $fee,
					'receive' => bcsub($amount, $fee),
					'account' => $accounts[$k],
					'maxAmount' => $maxAmount,
				);
			} else {
				if(bccomp($amount, $v) == 1) {
					$curamount = $v;
				} else {
					$curamount = $amount;
				}
				if(bccomp($maxAmount, $v) == 1) {
					$max = $v;
				} else {
					$max = $maxAmount;
				}
				$fee = $this->Payments->getCashoutFee($curamount, PaymentsInflector::classify($k));

				$gData[$k] = array(
					'state' => 'limited',
					'amount' => $curamount,
					'fee' => $fee,
					'receive' => bcsub($curamount, $fee),
					'account' => $accounts[$k],
					'maxAmount' => $max,
				);
			}
		}

		$fData = $gData;

		foreach($fData as $k => $v) {
			if(isset($fData[$k]['amount'])) {
				$fData[$k]['amount'] = CurrencyFormatter::format($fData[$k]['amount']);
			}
			if(isset($fData[$k]['fee'])) {
				$fData[$k]['fee'] = CurrencyFormatter::format($fData[$k]['fee']);
			}
			if(isset($fData[$k]['receive'])) {
				$fData[$k]['receive'] = CurrencyFormatter::format($fData[$k]['receive']);
			}
			if(isset($fData[$k]['maxAmount'])) {
				$fData[$k]['maxAmount'] = CurrencyFormatter::format($fData[$k]['maxAmount']);
			}
			$fData[$k]['humanizedName'] = PaymentsInflector::humanize($k);
		}
		$this->set(compact('googleAuthenticator'));
		$this->set('gData', $fData);

		if($this->request->is('post')) {
			$gateway = $this->request->data['gateway'];

			if(!isset($gData[$gateway]) || empty($gData[$gateway]) || in_array($gData[$gateway], array('noAccount', 'disabled', 'blocked'))) {
				/* cheater? */
				throw new InternalErrorException(__d('exception', 'Wrong gateway'));
			}

			if($googleAuthenticator) {
				if(!$this->GoogleAuthenticator->check($this->request->data['ga_code'], $user['UserSecret']['ga_secret'])) {
					return $this->Notice->error(__('Wrong Google Authenticator code'));
				}
			}

			$newCashout = array(
				'user_id' => $user_id,
				'amount' => $gData[$gateway]['receive'],
				'fee' => $gData[$gateway]['fee'],
				'payment_account' => $gData[$gateway]['account'],
				'gateway' => PaymentsInflector::classify($gateway),
				'status' => 'New',
			);

			$this->User->Cashouts->create();

			if(!$this->User->accountBalanceSub($gData[$gateway]['amount'], $user_id)) {
				throw new InternalErrorException(__d('exception', 'Failed to save user account balance.'));
			}

			if(!$this->User->Cashouts->save($newCashout)) {
				throw new InternalErrorException(__d('exception', 'Failed to save cashout.'));
			}

			if($membership['Membership']['instant_cashouts']) {
				$this->User->Cashouts->id = $this->User->Cashouts->getLastInsertId();
				$this->User->Cashouts->recursive = -1;
				$this->User->Cashouts->read();

				$result = $this->Payments->cashout(array('Cashout' => $this->User->Cashouts->data['Cashouts']));

				if($result === false || $result == 'Failed') {
					$set = $this->Settings->fetchOne('autoCashoutFail', 'failed');
					$msg = __('Error while sending cashout request to gateway.');

					switch($set) {
						case 'failed':
							$result = 'Failed';
						break;

						case 'new':
							$result = 'New';
							$msg .= ' '.__('Your request will be processed manualy.');
						break;

						case 'cancelled':
							if($this->User->accountBalanceAdd(bcadd($cashout['Cashout']['amount'], $cashout['Cashout']['fee']), $cashout['Cashout']['user_id'])) {
								$msg .= ' '.__('Funds refunded to user.');
							} else {
								$msg .= ' '.__('Failed to refund funds. Please open a support ticket.');
							}
							$result = 'Cancelled';
						break;
					}
					$this->Notice->error($msg);
				}

				$this->User->Cashouts->set('status', $result);
				if(!$this->User->Cashouts->save()) {
					throw new InternalErrorException(__d('exception', 'Failed to save cashout status.'));
				}
			}

			$this->Notice->success(__('Cashout successfully done'));
			$this->redirect(array('action' => 'dashboard'));
		}
	}

/**
 * rentReferrals method
 *
 * @return void
 */
	public function rentReferrals() {
		$settingsKeys = array(
			'enableRentingReferrals',
			'rentMinClickDays',
			'rentFilter',
			'rentPeriod',
		);
		$settings = $this->Settings->fetch($settingsKeys);

		if(!$settings['Settings']['enableRentingReferrals']) {
			return $this->redirect(array('action' => 'dashboard'));
		}

		$this->set('breadcrumbTitle', __('Rent Referrals'));

		$this->User->contain(array(
			'ActiveMembership.Membership' => array(
				'name',
				'available_referrals_packs',
				'rented_referrals_limit',
				'time_between_renting',
				'points_enabled',
				'points_conversion',
				'RentedReferralsPrice',
			),
		));
		$user = $this->User->findById($this->Auth->user('id'));

		if($user['User']['last_rent_action'] != null) {
			$next = new DateTime($user['User']['last_rent_action']);
			$next->add(new DateInterval("P{$user['ActiveMembership']['Membership']['time_between_renting']}D"));
			$today = new DateTime('today');
			$days = $today->diff($next)->format('%R%a');

			if($days > 0) {
				$refsPacks = array();
				$this->set(compact('user', 'refsPacks'));
				return $this->Notice->info(__('Sorry, but you need to wait %d more day(s). You can rent next referrals at %s', $days, $next->format('Y-m-d H:i:s')));
			}
		}

		switch($settings['Settings']['rentFilter']) {
			case 'clickDays':
				$availableRefs = $this->User->countNotRentedActiveClicked($settings['Settings']['rentMinClickDays'], $this->magicStatsNumber($settings['Settings']['rentMinClickDays']), $user['User']['id']);
			break;

			case 'onlyActive':
				$availableRefs = $this->User->countNotRentedActive($user['User']['id']);
			break;

			case 'all':
				$availableRefs = $this->User->countNotRented($user['User']['id']);
			break;
		}

		$refsPacks = array();

		if(!empty($user['ActiveMembership']['Membership']['available_referrals_packs'])) {
			$packs = explode(',', $user['ActiveMembership']['Membership']['available_referrals_packs']);

			sort($packs);

			foreach($packs as $p) {
				$range = $this->User->MembershipsUser->Membership->RentedReferralsPrice->getRangeByRRefsNo($user['ActiveMembership']['Membership']['RentedReferralsPrice'], $user['User']['rented_refs_count']);
				if($range !== null) {
					if($p > $availableRefs) {
						$refsPacks[$p]['tooltip'] = __('Sorry, we do not have enough referrals available now.');
						$refsPacks[$p]['disabled'] = true;
					} elseif($user['ActiveMembership']['Membership']['rented_referrals_limit'] != -1 && $p > $user['ActiveMembership']['Membership']['rented_referrals_limit'] - $user['User']['rented_refs_count']) {
						$refsPacks[$p]['tooltip'] = __('This packet will exceed your rented referrals limit.');
						$refsPacks[$p]['disabled'] = true;
					} else {
						$price = bcmul($range['price'], $p);
						if(bccomp($user['User']['purchase_balance'], $price) >= 0) {
							$refsPacks[$p]['tooltip'] = $price;
							$refsPacks[$p]['disabled'] = false;
						} else {
							$refsPacks[$p]['tooltip'] = __('Sorry, you have not enough funds for this pack.');
							$refsPacks[$p]['disabled'] = true;
						}
					}
				}
			}
		}

		if($this->request->is('post')) {
			if(isset($refsPacks[$this->request->data['refs_no']]) && $refsPacks[$this->request->data['refs_no']]['disabled'] == false) {
				$this->Payments->pay('rent', 'PurchaseBalance', $refsPacks[$this->request->data['refs_no']]['tooltip'], $this->Auth->user('id'), array(
					'refs_no' => $this->request->data['refs_no'],
				));
			} else {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}
		}
		$this->set(compact('user', 'refsPacks'));
	}

/**
 * buyReferrals method
 *
 * @return void
 */
	public function buyReferrals() {
		$settingsKey = array(
			'enableBuyingReferrals',
			'directFilter',
			'directMinClickDays',
		);

		$settings = $this->Settings->fetch($settingsKey);

		if(!$settings['Settings']['enableBuyingReferrals']) {
			return $this->redirect(array('action' => 'dashboard'));
		}

		$this->User->contain(array(
			'ActiveMembership.Membership' => array(
				'name',
				'direct_referrals_limit',
				'points_enabled',
				'points_conversion',
			),
		));
		$user = $this->User->findById($this->Auth->user('id'));

		$available = $this->User->countAvailDRefs($settings);

		$packs = array();
		$pa = $this->DirectReferralsPrice->find('all', array('order' => 'DirectReferralsPrice.amount'));
		$showPacks = false;
		foreach($pa as $p) {
			if($user['ActiveMembership']['Membership']['direct_referrals_limit'] != -1 && $user['ActiveMembership']['Membership']['direct_referrals_limit'] < $p['DirectReferralsPrice']['amount'] + $user['User']['refs_count']) {
				$packs[$p['DirectReferralsPrice']['amount']]['tooltip'] = __('Sorry, your limit will overflow.');
				$packs[$p['DirectReferralsPrice']['amount']]['disabled'] = true;
				$showPacks = true;
			} elseif(bccomp($user['User']['purchase_balance'], $p['DirectReferralsPrice']['price']) < 0) {
				$packs[$p['DirectReferralsPrice']['amount']]['tooltip'] = __('Sorry, you do not have enough funds on Purchase Balance.');
				$packs[$p['DirectReferralsPrice']['amount']]['disabled'] = true;
				$showPacks = true;
			} elseif($p['DirectReferralsPrice']['amount'] > $available) {
				$packs[$p['DirectReferralsPrice']['amount']]['tooltip'] = __('Sorry, we do not have so many referrals available at now.');
				$packs[$p['DirectReferralsPrice']['amount']]['disabled'] = true;
			} else {
				$packs[$p['DirectReferralsPrice']['amount']]['tooltip'] = $p['DirectReferralsPrice']['price'];
				$packs[$p['DirectReferralsPrice']['amount']]['disabled'] = false;
				$showPacks = true;
			}
		}

		if($this->request->is('post')) {
			if(isset($packs[$this->request->data['refs_no']]) && $packs[$this->request->data['refs_no']]['disabled'] == false) {
				$this->Payments->pay('referrals', 'PurchaseBalance', $packs[$this->request->data['refs_no']]['tooltip'], $this->Auth->user('id'), array(
					'refs_no' => $this->request->data['refs_no'],
				));
			} else {
				/* cheater? */
				throw new NotFoundException(__d('exception', 'Invalid package'));
			}
		}

		$this->set('breadcrumbTitle', __('Buy referrals'));
		$this->set(compact('user', 'packs', 'available', 'showPacks'));
	}

/**
 * setAutopay method
 *
 * @return void
 */
	public function setAutopay($val = 0) {
		$this->request->allowMethod(array('post', 'put'));
		$val = (boolean)$val;
		$this->User->id = $this->Auth->user('id');

		if($this->User->saveField('autopay_enabled', $val)) {
			$this->Notice->success(__('Autopay settings successfully changed.'));
		} else {
			$this->Notice->error('Failed to save Autopay settings. Please, try again.');
		}
		return $this->redirect($this->referer());
	}

/**
 * autopayStats method
 *
 * @return void
 */
	public function autopayStats() {
		$this->request->allowMethod('ajax');
		$this->layout = 'ajax';

		$autopayHistory = ClassRegistry::init('AutopayHistory');

		$autopayHistory->recursive = -1;
		$data = $autopayHistory->find('all', array(
			'conditions' => array(
				'user_id' => $this->Auth->user('id'),
			),
		));
		$data = Hash::combine($data, '{n}.AutopayHistory.created', '{n}.AutopayHistory.amount');
		$this->set(compact('data'));
	}

/**
 * autorenewStats method
 *
 * @return void
 */
	public function autorenewStats() {
		$this->request->allowMethod('ajax');
		$this->layout = 'ajax';

		$autorenewHistory = ClassRegistry::init('AutorenewHistory');

		$autorenewHistory->recursive = -1;
		$data = $autorenewHistory->find('all', array(
			'conditions' => array(
				'user_id' => $this->Auth->user('id'),
			),
		));
		$data = Hash::combine($data, '{n}.AutorenewHistory.created', '{n}.AutorenewHistory.amount');
		$this->set(compact('data'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$conditions = $this->createPaginatorConditions(array(
			'User.username',
			'User.email',
			'User.last_ip',
			'User.signup_ip',
			'User.payment',
			'User.location',
			'Upline.username',
			'RentedUpline.username',
		));

		$inCollapse = array(
			'User.signup_ip LIKE',
			'User.role',
			'User.payment LIKE',
			'Upline.username LIKE',
			'RentedUpline.username LIKE',
			'ActiveMembership.membership_id',
			'ActiveMembership.period',
			'User.location LIKE',
			'User.forum_status',
		);

		$inCollapse = array_intersect_key($conditions, array_flip($inCollapse));
		if(!empty($inCollapse)) {
			$this->set('searchCollapse', 'in');
		} else {
			$this->set('searchCollapse', '');
		}

		if(isset($conditions['User.payment LIKE'])) {
			$val = $conditions['User.payment LIKE'];
			unset($conditions['User.payment LIKE']);

			$gateways = array_flip($this->Payments->getActiveWithUserSettingHumanized());
			foreach($gateways as $gateway) {
				$fieldName = PaymentsInflector::underscore($gateway);
				$conditions['OR']['LOWER(UserProfile.'.$fieldName.') LIKE'] = $val;
			}
		}

		if(isset($conditions['ActiveMembership.period'])) {
			if($conditions['ActiveMembership.period'] == 'Upgraded') {
				$conditions['ActiveMembership.period !='] = 'Default';
				unset($conditions['ActiveMembership.period']);
			}
		}

		$this->paginate = array(
			'contain' => array(
				'ActiveMembership' => array('Membership' => array('id', 'name')),
				'Upline',
				'RentedUpline',
				'UserProfile',
			),
			'fields' => array(
				'User.id',
				'User.username',
				'User.refs_count',
				'User.rented_refs_count',
				'User.email',
				'User.location',
				'User.account_balance',
				'User.purchase_balance',
				'User.role',
				'User.last_log_in',
				'User.location',
				'Upline.username',
				'RentedUpline.username',
				'UserProfile.user_id',
			),
			'order' => 'User.created DESC',
		);

		$users = $this->Paginator->paginate($conditions);

		$this->set('userRoles', $this->User->getRolesList());
		$this->set('memberships', $this->User->ActiveMembership->Membership->getList());
		$this->set('users', $users);
	}

/**
 * admin_massaction method
 *
 * @throws NotFoundException
 * @return void
 */
	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {

			if(!isset($this->request->data['Users']) || empty($this->request->data['Users'])) {
				$this->Notice->error(__d('admin', 'Please select at least one user.'));
				return $this->redirect($this->referer());
			}

			$users = 0;
			foreach($this->request->data['Users'] as $id => $on) {
				if($on) {
					$users++;
					if(!$this->User->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid user'));
					}
				}
			}

			foreach($this->request->data['Users'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'suspend':
							$this->User->suspend($id);
						break;

						case 'activate':
							$this->User->activate($id);
						break;

						case 'delete':
							$this->User->delete($id);
						break;

						case 'ban':
							$this->User->id = $id;
							$this->User->saveField('forum_status', 2);
						break;

						case 'unban':
							$this->User->id = $id;
							$this->User->saveField('forum_status', 1);
						break;
					}
				}
			}
			if($users) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one user.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect($this->referer());
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if($this->request->is('post')) {
			$this->request->data('User.role', 'Active');
			$this->request->data('User.comes_from', 'Manually added');
			$this->User->create();
			if($this->User->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'The user has been saved.'));
				return $this->redirect(array('action' => 'index'));
			}
			$this->Notice->error(__d('admin', 'The user could not be saved. Please, try again.'));
		}
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if(!$this->User->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}
		$this->User->id = $id;
		$this->RequestObject->contain();
		$groups = $this->RequestObject->find('list', array('conditions' => array('RequestObject.model' => null)));

		if($this->request->is(array('post', 'put'))) {
			$fail = false;

			if(!empty($this->request->data['User'])) {
				$this->User->set(array($this->request->data));
				$fail = !$this->User->validates();
			}

			$this->User->id = $id;
			$this->User->contain(array(
				'ActiveMembership',
				'Upline',
				'RentedUpline',
			));
			$this->User->read();

			if(!$fail) {
				if(isset($this->request->data['User']['upline']) && $this->request->data['User']['upline'] != $this->User->data['Upline']['username']) {
					if(!empty($this->request->data['User']['upline'])) {
						$this->User->contain();
						$newUpline = $this->User->findByUsername($this->request->data['User']['upline']);
						if(!empty($newUpline)) {
							if(!$this->User->changeDirectUpline($this->User->data['User']['id'], $newUpline['User']['id'], $this->User->data['Upline']['id'])) {
								throw new InternalErrorException(__d('exception', 'Failed to change direct upline'));
							}
						} else {
							$this->Notice->error('Cannot find new direct upline.');
							$fail = true;
						}
					} else {
						$this->User->removeDirectUpline($this->User->data['User']['id']);
					}
					unset($this->request->data['User']['upline']);
				}
				$this->User->id = $id;
				$this->User->contain(array(
					'ActiveMembership',
					'Upline',
					'RentedUpline',
				));
				$this->User->read();
			}

			if(!$fail) {
				if(isset($this->request->data['User']['rentedUpline']) && $this->request->data['User']['rentedUpline'] != $this->User->data['RentedUpline']['username']) {
					if(!empty($this->request->data['User']['rentedUpline'])) {
						$this->User->contain();
						$newUpline = $this->User->findByUsername($this->request->data['User']['rentedUpline']);
						if(!empty($newUpline)) {
							if(!empty($this->User->data['User']['rent_ends']) || !empty($this->request->data['User']['rent_ends'])) {
								if(!$this->User->changeRentedUpline($this->User->data['User']['id'], $newUpline['User']['id'], $this->User->data['RentedUpline']['id'])) {
									throw new InternalErrorException(__d('exception', 'Failed to change rented upline.'));
								}
							} else {
								$this->Notice->error('Please select when renting is over.');
								$fail = true;
							}
						} else {
							$this->Notice->error('Cannot find new rented upline.');
							$fail = true;
						}
					} else {
						$this->User->removeRentedUpline($this->User->data['User']['id']);
						unset($this->request->data['User']['rent_ends']);
					}
					unset($this->request->data['User']['rentedUpline']);
				}
			}

			if(!$fail) {
				if(isset($this->request->data['groups'])) {
					if($this->RequestObject->deleteAll(array(
						'RequestObject.Model' => 'User',
						'RequestObject.foreign_key' => $id,
					))) {
						if(!empty($this->request->data['groups'])) {
							$this->User->id = $id;
							$this->User->contain();
							$this->User->read(array('username'));

							$toadd = array();

							foreach($this->request->data['groups'] as $group) {
								$toadd[] = array(
									'parent_id' => $group,
									'model' => 'User',
									'foreign_key' => $id,
									'alias' => $groups[$group].'/'.$this->User->data['User']['username'],
								);
							}

							$this->RequestObject->recursive = -1;
							if(!$this->RequestObject->saveAll($toadd)) {
								$this->Notice->error(__d('admin', 'Failed to save groups associations.'));
								$fail = true;
							}
						} else {
							$this->Notice->success(__d('admin', 'Successfully removed all groups associations.'));
						}
					} else {
						$this->Notice->error(__d('admin', 'Failed to delete old groups associations.'));
						$fail = true;
					}
				}
			}

			if(!$fail) {
				if(isset($this->request->data['NewMembership']) && !empty($this->request->data['NewMembership']['membership_id'])) {
					if(!$this->Membership->exists($this->request->data['NewMembership']['membership_id'])) {
						throw new NotFoundException(__d('exception', 'Invalid membership'));
					}
					if(!$this->MembershipsUser->addNew($id, $this->request->data['NewMembership']['membership_id'],
						$this->request->data['NewMembership']['begins'], $this->request->data['NewMembership']['ends']
					)) {
						throw new InternalErrorException(__d('exception', 'Failed to add new membership'));
					}
					unset($this->request->data['NewMembership']);
				}
				if(isset($this->request->data['User'])) {
					$this->request->data['User']['username'] = $this->User->data['User']['username']; /* so we can check if password != username */
					unset($this->User->data['User']);
					if(empty($this->request->data['User']['password'])) {
						unset($this->request->data['User']['password']);
					}
					if($this->User->data['ActiveMembership']['period'] == 'Default') {
						unset($this->request->data['ActiveMembership']);
					}
					
					if($this->User->saveAssociated($this->request->data)) {
						$this->Notice->success(__d('admin', 'The user has been saved.'));
						return $this->redirect(array('action' => 'index'));
					} else {
						$this->Notice->error(__d('admin', 'The user could not be saved. Please, try again.'));
					}
					$userValidationErrors = $this->User->validationErrors; /* Model::read() clears $validationErrors so we need to temporary store them */
				}
			}
		}

		$this->User->contain(array(
			'UserProfile',
			'UserMetadata',
			'ActiveMembership',
			'MembershipsUser' => array(
				'conditions' => array('period != ' => 'Default')
			),
			'UserStatistic',
			'UserSecret',
		));
		$this->User->read();

		$usrclicks = array();
		$drclicks = array();
		$rrclicks = array();
		$n = $this->magicStatsNumber(6);

		for($i = 6; $i >= 0; --$i, $n = ($n + 1) % 7) {
			$idx = date('Y-m-d', strtotime("-$i days"));

			$usrclicks[$idx] = $this->User->data['UserStatistic']['user_clicks_'.$n];
			$drclicks[$idx] = $this->User->data['UserStatistic']['dref_clicks_'.$n];
			$drclicksCredited[$idx] = $this->User->data['UserStatistic']['dref_clicks_credited_'.$n];
			$rrclicks[$idx] = $this->User->data['UserStatistic']['rref_clicks_'.$n];
			$rrclicksCredited[$idx] = $this->User->data['UserStatistic']['rref_clicks_credited_'.$n];
		}

		$roles = $this->RequestObject->getRoles($id);
		$this->User->data['RequestObject'] = Hash::extract($roles, '{n}.RequestObject.id');
		$this->request->data = Hash::merge($this->User->data, $this->request->data);
		$this->request->data['User']['password'] = null;

		$this->User->Upline->id = $this->User->data['User']['upline_id'];
		$this->User->Upline->contain();
		$this->User->Upline->read(array('id', 'username'));
		$this->request->data['Upline'] = &$this->User->Upline->data['Upline'];

		$this->User->RentedUpline->id = $this->User->data['User']['rented_upline_id'];
		$this->User->RentedUpline->contain();
		$this->User->RentedUpline->read(array('id', 'username'));
		$this->request->data['RentedUpline'] = &$this->User->RentedUpline->data['RentedUpline'];

		$this->set('genders', $this->User->UserProfile->getGendersList());
		$this->set('userRoles', $this->User->getRolesList());
		$this->set('memberships', $this->User->ActiveMembership->Membership->getList(false));
		$this->set('waitingCashouts', $this->User->getWaitingCashoutsAmount($id));
		$this->set('totalPurchases', $this->User->getTotalPurchases($id));
		$this->set('totalDeposits', $this->User->getTotalFunding($id));
		if(isset($userValidationErrors)) {
			$this->User->validationErrors = $userValidationErrors;
		}

		$g = $this->Payments->getActiveWithUserSettingHumanized();
		$gateways = array();
		foreach($g as $k => $name) {
			$gateways[PaymentsInflector::underscore($k)] = $name;
		}
		asort($gateways);

		$this->set(compact('gateways', 'groups', 'usrclicks', 'drclicks', 'rrclicks', 'drclicksCredited', 'rrclicksCredited'));
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->request->allowMethod('post', 'delete');
		if(!$this->User->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}
		if($this->User->delete($id)) {
			$this->Notice->success(__d('admin', 'User has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'User could not be deleted. Please, try again.'));
		}
		return $this->redirect($this->referer());
	}

/**
 * admin_suspend
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_suspend($id = null) {
		$this->request->allowMethod('post', 'delete');
		if(!$this->User->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}
		if($this->User->suspend($id)) {
			$this->Notice->success(__d('admin', 'User has been suspended.'));
		} else {
			$this->Notice->error(__d('admin', 'User could not be suspended. Please, try again.'));
		}
		return $this->redirect($this->referer());
	}

/**
 * admin_activate
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_activate($id = null) {
		$this->request->allowMethod('post', 'delete');
		if(!$this->User->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}
		if($this->User->activate($id)) {
			$this->Notice->success(__d('admin', 'User has been activated.'));
		} else {
			$this->Notice->error(__d('admin', 'User could not be activated. Please, try again.'));
		}
		return $this->redirect($this->referer());
	}

/**
 * admin_delete_active_membership method
 *
 * @return void
 */
	public function admin_delete_active_membership($user_id) {
		$this->request->allowMethod('post', 'put');
		if(!$this->User->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}
		if($this->MembershipsUser->deleteLast($user_id)) {
			$this->User->removeReferralsOverflow($user_id);
			$this->Notice->success(__d('admin', 'Active membership deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'You cannot delete default membership.'));
		}
		$this->redirect($this->referer());
	}

/**
 * admin_backToDefaultMembership method
 *
 * @return void
 */
	public function admin_backToDefaultMembership($user_id) {
		$this->request->allowMethod('post', 'put');
		if(!$this->User->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}
		if($this->MembershipsUser->deleteAll(array('MembershipsUser.user_id' => $user_id, 'MembershipsUser.period != ' => 'Default'))) {
			$this->User->removeReferralsOverflow($user_id);
			$this->Notice->success(__d('admin', 'User degraded to default membership.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to degrade user. Please try again.'));
		}
		$this->redirect($this->referer());
	}

/**
 * admin_cleanup method
 *
 * @return void
 */
	public function admin_cleanup() {
		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['User']['action'])) {
				if(empty($this->request->data['User']['days']) && $this->request->data['User']['days'] !== '0') {
					return $this->Notice->error('Please enter number of days.');
				}
				$methodName = $this->request->data['User']['action'];
				if($this->User->$methodName($this->request->data['User']['days'])) {
					$this->Notice->success(__d('admin', 'Action successfully completed.'));
				} else {
					$this->Notice->error(__d('admin', 'An error has occurred. Please, try again.'));
				}
			}
		}
	}

/**
 * admin_resetAdvertisements
 *
 * @return void
 */
	public function admin_resetAdvertisements($user_id) {
		$this->request->allowMethod('post', 'put');
		if(!$this->User->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}
		if($this->User->VisitedAds->deleteVisitedAdsByUser($user_id)) {
			$this->Notice->success(__d('admin', 'Users advertisements reseted.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to reset advertisements. Please try again.'));
		}
		$this->redirect($this->referer());
	}

/**
 * admin_resetLogin
 *
 * @return void
 */
	public function admin_resetLogin($user_id) {
		$this->request->allowMethod('post', 'put');
		if(!$this->User->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}
		$this->User->id = $user_id;
		$this->User->set(array('last_ip' => null, 'last_log_in' => null));
		if($this->User->save()) {
			$this->Notice->success(__d('admin', 'User\'s login data reseted'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to reset user\'s login data'));
		}
		$this->redirect($this->referer());
	}

/**
 * admin_unhookDirectReferrals
 *
 * @return void
 */
	public function admin_unhookDirectReferrals($user_id) {
		$this->request->allowMethod('post', 'put');
		if(!$this->User->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}
		if($this->User->unhookDirectReferrals($user_id)) {
			$this->Notice->success(__d('admin', 'Users direct referrals unhooked.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to unhook direct referrals. Please try again.'));
		}
		$this->redirect($this->referer());
	}

/**
 * admin_unhookRentedReferrals
 *
 * @return void
 */
	public function admin_unhookRentedReferrals($user_id) {
		$this->request->allowMethod('post', 'put');
		if(!$this->User->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}
		if($this->User->unhookRentedReferrals($user_id)) {
			$this->Notice->success(__d('admin', 'Users rented referrals unhooked.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to unhook rented referrals. Please try again.'));
		}
		$this->redirect($this->referer());
	}

/**
 * admin_sendMessage method
 *
 * @return void
 */
	public function admin_sendMessage() {
		$modes = array(
			'all' => __d('admin', 'All users'),
			'single' => __d('admin', 'Single user'),
		);

		$this->helpers[] = 'TinyMCE.TinyMCE';
		$this->PendingEmail = ClassRegistry::init('PendingEmail');

		if($this->request->is(array('post', 'put'))) {
			if($this->request->data['PendingEmail']['mode'] == 'all') {
				if($this->PendingEmail->save($this->request->data)) {
					$this->Notice->success(__d('admin', 'E-mail queued to save.'));
				} else {
					$this->Notice->error(__d('admin', 'Failed to add e-mail to queue. Please try again.'));
				}
			} else {
				$this->User->contain();
				$user = $this->User->findByUsername($this->request->data['PendingEmail']['recipient']);
				if(!empty($user)) {

					if($user['User']['allow_emails']) {
						$this->PendingEmail->setVariables(array(
							'%username%' => $user['User']['username'],
							'%firstname%' => $user['User']['first_name'],
							'%lastname%' => $user['User']['last_name'],
						));
						$this->PendingEmail->send($this->request->data, $user['User']['email']);
						$this->Notice->success(__d('admin', 'E-mail successfully sent.'));
					} else {
						$this->Notice->info(__d('admin', 'This user have disabled receiving e-mails from site.'));
					}
				} else {
					$this->Notice->error(__d('admin', 'User not found.'));
				}
			}
		}

		if(isset($this->request->params['named']['user'])) {
			$this->User->contain();
			$user = $this->User->findByUsername($this->request->params['named']['user'], array('id'));

			if(!empty($user)) {
				$this->request->data['PendingEmail']['mode'] = 'single';
				$this->request->data['PendingEmail']['recipient'] = $this->request->params['named']['user'];
			}
		}

		$formats = $this->PendingEmail->getFormats();
		$variables = $this->PendingEmail->getVariables();

		$this->set(compact('emails', 'formats', 'variables', 'modes'));
	}

/**
 * autopayStats method
 *
 * @return void
 */
	public function admin_autopayStats($user_id = null) {
		$this->request->allowMethod('ajax');
		$this->layout = 'ajax';

		$autopayHistory = ClassRegistry::init('AutopayHistory');

		$autopayHistory->recursive = -1;
		$data = $autopayHistory->find('all', array(
			'conditions' => array(
				'user_id' => $user_id,
			),
		));

		$data = Hash::combine($data, '{n}.AutopayHistory.created', '{n}.AutopayHistory.amount');
		$this->set(compact('data'));
	}

/**
 * autorenewStats method
 *
 * @return void
 */
	public function admin_autorenewStats($user_id = null) {
		$this->request->allowMethod('ajax');
		$this->layout = 'ajax';

		$autorenewHistory = ClassRegistry::init('AutorenewHistory');

		$autorenewHistory->recursive = -1;
		$data = $autorenewHistory->find('all', array(
			'conditions' => array(
				'user_id' => $user_id,
			),
		));
		$data = Hash::combine($data, '{n}.AutorenewHistory.created', '{n}.AutorenewHistory.amount');
		$this->set(compact('data'));
	}

/**
 * admin_login method
 *
 * @return void
 */
	public function admin_login($user_id = null) {
		if($user_id === null || !$this->User->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}

		$this->User->contain();
		$user = $this->User->findById($user_id);

		$this->Session->write('Auth.User', $user['User']);
		$this->Session->write('Evercookie.disable', true);

		$this->redirect(array('admin' => false, 'action' => 'dashboard'));
	}

/**
 * admin_bought_items
 *
 * @return void
 */
	public function admin_bought_items() {
		if($this->request->is(array('post', 'put'))) {
			$this->User->contain();
			$user = $this->User->findByUsername($this->request->data['User']['username'], array('id'));

			if(empty($user)) {
				return $this->Notice->error(__d('admin', 'Failed to find user.'));
			}

			return $this->redirect(array('controller' => 'bought_items', 'action' => 'view', $user['User']['id']));
		}
	}
}
