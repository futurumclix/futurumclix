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
App::import('Vendor', 'FacebookLogin.autoload', array('file' => 'Facebook/autoload.php'));

class FacebookLoginController extends FacebookLoginAppController {
	public $uses = array(
		'User',
	);

	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow(array('login', 'signup'));
	}

	public function admin_settings() {
		if($this->request->is(array('post', 'put'))) {
			if($this->FacebookLoginSettings->store($this->request->data, 'facebookLogin')) {
				$this->Notice->success(__d('facebook_login_admin', 'FacebookLogin settings saved successfully.'));
			} else {
				$this->Notice->error(__d('facebook_login_admin', 'The settings could not be saved. Please, try again.'));
			}
		}

		$settings = $this->FacebookLoginSettings->fetch('facebookLogin');
		$this->request->data = Hash::merge($settings, $this->request->data);
		$this->set(compact('contests'));
	}

	public function login() {
		$settings = $this->FacebookLoginSettings->fetchOne('facebookLogin');

		$fb = new Facebook\Facebook(array(
			'app_id' => $settings['appID'],
			'app_secret' => $settings['appSecret'],
			'default_graph_version' => 'v2.2',
		));

		$helper = $fb->getRedirectLoginHelper();

		try {
			$accessToken = $helper->getAccessToken();
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			throw new BadRequestException(__d('facebook_login', 'Facebook Graph returned an error: %s', $e->getMessage()));
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			throw new InternalErrorException(__d('facebook_login', 'Facebook SDK error: %s', $e->getMessage()));
		}

		if(!isset($accessToken)) {
			if($helper->getError()) {
				throw new UnauthorizedException(__d('facebook_login', 'Error %s, code %s, reason %s, description %s', $helper->getError(), $helper->getErrorCode(), $helper->getErrorReason(), $helper->getErrorDescription()));
			} else {
				throw new BadRequestException(__d('facebook_login', 'Bad request'));
			}
		}

		$oAuth2Client = $fb->getOAuth2Client();
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);

		try {
			$tokenMetadata->validateAppId($settings['appID']);
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			/* TODO: cheater? */
			throw new ForbiddenException(__d('facebook_login', 'Invalid Facebook appID'));
		}
		$tokenMetadata->validateExpiration();

		if(!$accessToken->isLongLived()) {
			try {
				$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
			}	catch (Facebook\Exceptions\FacebookSDKException $e) {
				throw new InternalErrorException(__d('facebook_login', 'Failed to get long-lived access token: %s', $helper->getMessage()));
			}
		}

		$fb->setDefaultAccessToken($accessToken);

		try {
			$response = $fb->get('/me?fields=name,email,picture,first_name,last_name,gender,third_party_id');
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			throw new BadRequestException(__d('facebook_login', 'Facebook Graph returned an error: %s', $e->getMessage()));
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			throw new InternalErrorException(__d('facebook_login', 'Facebook SDK error: %s', $e->getMessage()));
		}

		$userData = $response->getDecodedBody();

		$this->Session->write('ExternalLogin', array('Facebook' => $userData, 'redirect' => array('plugin' => 'facebook_login', 'controller' => 'facebook_login', 'action' => 'signup')));
		return $this->redirect(array('plugin' => null, 'controller' => 'users', 'action' => 'login'));
	}

	public function signup() {
		$userData = $this->Session->read('ExternalLogin.Facebook');

		if(!$userData || empty($userData)) {
			throw new BadRequestException(__d('facebook_login', 'Invalid user data'));
		}

		$settings = $this->Settings->fetch(array(
			'blockSameSignupIP',
			'checkSignupIpDays',
		));

		if($this->request->is(array('post', 'put'))) {
			$this->request->data('User.role', 'Active');
			$this->request->data('User.signup_ip', $this->request->clientIp());
			$this->request->data('User.first_name', $userData['first_name']);
			$this->request->data('User.last_name', $userData['last_name']);
			$this->request->data('User.avatar', $userData['picture']['data']['url']);
			$this->request->data('User.email', $userData['email']);
			$this->request->data('ExternalLogin.facebook', $userData['id']);
			$this->request->data('UserProfile.gender', ucfirst(strtolower($userData['gender'])));
			$this->request->data('User.country', ClassRegistry::init('Ip2Nation')->getCountryIdByIp($this->request->clientIp()));

			if($this->Session->check('User.uplineName')) {
				$this->User->contain(array('ActiveMembership' => array('Membership' => 'direct_referrals_limit')));
				if(($upline = $this->User->findByUsername($this->Session->read('User.uplineName'), array('id', 'refs_count', 'email', 'allow_emails')))) {
					if($upline['ActiveMembership']['Membership']['direct_referrals_limit'] == -1 
					   || $upline['ActiveMembership']['Membership']['direct_referrals_limit'] >= $upline['User']['refs_count'] + 1) {
						$this->request->data('User.upline_id', $upline['User']['id']);
						$this->request->data('User.dref_since', date('Y-m-d H:i:s'));
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
					$this->Notice->error(__d('facebook_login', 'Your IP is already in use.'));
					return $this->redirect(array('plugin' => null, 'controller' => 'users', 'action' => 'signup'));
				}
			}

			$this->User->create();
			if($this->User->saveAssociated($this->request->data)) {
				if(isset($upline) && !empty($upline)) {
					$this->_sendNewDRefNotice($upline, $this->request->data);
				}
				$this->Notice->success(__d('facebook_login', 'Your account has been successfully created.'));
				return $this->redirect(array('plugin' => null, 'controller' => 'users', 'action' => 'login'));
			}

			$this->Notice->error(__d('facebook_login', 'User could not be saved. Please, try again'));
		}

		if($this->Session->check('User.uplineName') && !$this->Session->read('User.uplineSecret')) {
			$this->set('uplineName', $this->Session->read('User.uplineName'));
		}
	}

	protected function _sendNewDRefNotice($upline, $ref) {
		if($upline['User']['role'] != 'Active' && $upline['User']['allow_emails']) {
			$email = ClassRegistry::init('Email');

			$email->setVariables(array(
				'%username%' => $ref['User']['username'],
				'%uplineusername%' => $upline['User']['username'],
				'%comesFrom%' => isset($ref['User']['comes_from']) ? $ref['User']['comes_from'] : __d('facebook_login', 'Direct link'),
			));

			$email->send('New direct referral', $upline['User']['email']);
		}
	}
}
