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
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
App::uses('PaymentsInflector', 'Payments');

/**
 * UserProfiles Controller
 *
 * @property UserProfile $UserProfile
 * @property PaginatorComponent $Paginator
 */
class UserProfilesController extends AppController {

/**
 * Models
 *
 * @var array
 */
	public $uses = array(
		'UserProfile',
		'UserSecret',
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'UserPanel',
		'Payments',
		'GoogleAuthenticator.GoogleAuthenticator',
	);

/**
 * Helpers
 *
 * @var array
 */
	public $helpers = array(
		'Forum.Forum',
		'Utility.OpenGraph',
		'Utility.Breadcrumb',
	);

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit() {
		$id = $this->Auth->user('id');
		if(!$this->UserProfile->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid user profile'));
		}

		$user = $this->UserPanel->getData();
		$g = $this->Payments->getActiveWithUserSettingHumanized('cashouts');
		$gateways = array();
		foreach($g as $k => $name) {
			$gateways[PaymentsInflector::underscore($k)] = $name;
		}
		$this->UserProfile->contain(array(
			'User' => array(
				'signature',
				'avatar',
				'username',
				'email',
				'first_name',
				'last_name',
				'allow_emails',
				'UserSecret',
			),
			'UserMetadata' => array(
				'next_email',
			),
		));
		$profile = $this->UserProfile->findByUserId($id);
		$nextEmail = $profile['UserMetadata']['next_email'];
		$googleAuthenticator = @in_array('profile', $this->Settings->fetchOne('googleAuthenticator'));
		$googleAuthenticator = $googleAuthenticator && $profile['User']['UserSecret']['mode'] == UserSecret::MODE_GA;
		$this->set(compact('googleAuthenticator'));

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['UserMetadata']['user_id'] = $id;
			$this->request->data['User']['id'] = $id;
			$this->request->data['UserProfile']['user_id'] = $id;

			$passwordHasher = new BlowfishPasswordHasher();

			if($passwordHasher->check($this->request->data['UserProfile']['password_check'], $this->Auth->user('password'))) {
				$save = true;
				unset($this->request->data['UserProfile']['password_check']);

				if($googleAuthenticator) {
					if(!$this->GoogleAuthenticator->check($this->request->data['UserSecret']['ga_code'], $profile['User']['UserSecret']['ga_secret'])) {
						$this->Notice->error(__('Wrong Google Authenticator code.'));
						$save = false;
					}
					unset($this->request->data['UserSecret']);
				}

				if(isset($this->request->data['User']['password']) && empty($this->request->data['User']['password'])) {
					unset($this->request->data['User']['password']);
					unset($this->request->data['User']['confirm_password']);
				}

				$this->request->data['User']['username'] = $profile['User']['username'];

				if($this->request->data['UserMetadata']['next_email'] == $profile['User']['email']) {
					unset($this->request->data['UserMetadata']);
				} else {
					$this->request->data['UserMetadata']['verify_token'] = $this->UserProfile->createRandomStr(20);
				}

				$changed = array();
				$now = date('Y-m-d H:i:s');
				foreach($gateways as $name => $gateway) {
					if(isset($this->request->data['UserProfile'][$name]) 
						&& $this->request->data['UserProfile'][$name] != $profile['UserProfile'][$name]
						&& $profile['UserProfile'][$name] !== null) {
						$this->request->data['UserProfile'][$name.'_modified'] = $now;
						$changed[] = $gateway;
					}
				}

				if($save && $this->UserProfile->saveAssociated($this->request->data)) {
					$this->Notice->success(__('The user profile has been saved.'));
					if(!empty($changed)) {
						$email = ClassRegistry::init('Email');

						$email->setVariables(array(
							'%username%' => $user['User']['username'],
							'%firstname%' => $user['User']['first_name'],
							'%lastname%' => $user['User']['last_name'],
							'%gateways%' => implode(', ', $changed),
						));

						$email->send('Gateway notice', $user['User']['email']);
					}

					if(isset($this->request->data['UserMetadata']['next_email'])) {
						$this->_sendVerify(Hash::merge($profile, $this->request->data));
						$nextEmail = $this->request->data['UserMetadata']['next_email'];
					}

				} else {
					$this->Notice->error(__('Failed to save user profile.'));
				}
			} else {
				$this->UserProfile->validationErrors['password_check'] = __('Wrong password');
			}

			unset($this->request->data['User']['password']);
			unset($this->request->data['User']['confirm_password']);
			unset($this->request->data['UserProfile']['password_check']);

			$this->request->data = Hash::merge($profile, $this->request->data);
		} else {
			$this->request->data = $profile;
		}

		if(empty($this->request->data['UserMetadata']['next_email'])) {
			$this->request->data['UserMetadata']['next_email'] = $profile['User']['email'];
		}

		asort($gateways);

		$this->set(compact('gateways', 'user', 'nextEmail'));
		$this->set('breadcrumbTitle', __('Edit your profile'));
	}

	private function _sendVerify($data) {
		$email = ClassRegistry::init('Email');

		$email->setVariables(array(
			'%username%' => $data['User']['username'],
			'%firstname%' => $data['User']['first_name'],
			'%lastname%' => $data['User']['last_name'],
			'%verifyurl%' => Router::url(array('controller' => 'users', 'action' => 'emailChange', $data['User']['id'], $data['UserMetadata']['verify_token']), true),
		));

		$email->send('E-mail change', $data['UserMetadata']['next_email']);
		$this->Notice->success(__('Verification e-mail has been send.'));
	}

	public function resend($id) {
		$this->User->contain(array(
			'UserMetadata' => array(
				'next_email',
				'verify_token',
			),
		));
		$data = $this->UserProfile->User->findById($id, array(
			'id',
			'username',
			'first_name',
			'last_name',
		));

		$this->_sendVerify($data);

		return $this->redirect($this->referer());
	}

	public function security() {
		$user_id = $this->Auth->user('id');
		$user = $this->UserPanel->getData(array('UserSecret'));

		if($this->request->is(array('post', 'put'))) {
			$this->request->data['UserSecret']['user_id'] = $this->Auth->user('id');

			if(!empty($user['UserSecret']['id'])) {
				$this->request->data['UserSecret']['id'] = $user['UserSecret']['id'];
			}

			$this->UserSecret->create();
			if($this->UserSecret->save($this->request->data)) {
				$this->Notice->success(__('Your security data has been sucessfully saved.'));
				$this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
			} else {
				$this->Notice->error(__('Failed to save your data. Please, try again.'));
			}
		} else {
			$this->request->data = array('UserSecret' => $user['UserSecret']);
			unset($this->request->data['UserSecret']['pin']);
		}

		if(empty($this->request->data['UserSecret']) || empty($this->request->data['UserSecret']['ga_secret'])) {
			$this->request->data['UserSecret']['ga_secret'] = $this->GoogleAuthenticator->createSecret();
		}

		$this->set(compact('user', 'secret'));
		$this->set('breadcrumbTitle', __('Edit your security settings'));
	}
}
