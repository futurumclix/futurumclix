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
App::uses('Folder', 'Utility');
App::uses('PaymentsInflector', 'Payments');

/**
 * Settings Controller
 *
 * @property Setting $Setting
 * @property PaymentsComponent $Payments
 */
class SettingsController extends AppController {
/**
 * modelClass variable
 *
 * @var string
 */
	public $modelClass = 'Settings';

/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'Settings',
		'User',
		'RentExtensionPeriod',
		'UserProfile',
		'UserStatistic',
		'Currency',
		'PaymentGateway',
		'DirectReferralsPrice',
	);

/**
 * components
 *
 * @var array
 */
	public $components = array(
		'Payments',
	);

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('locale'));
		if($this->request->params['action'] == 'admin_rentingReferrals') {
			for($start = $this->RentExtensionPeriod->find('count'); $start < 150; $start++) {
				$this->Security->unlockedFields[] = 'RentExtensionPeriod.'.$start.'.days';
				$this->Security->unlockedFields[] = 'RentExtensionPeriod.'.$start.'.discount';
			}
		}
		if($this->request->params['action'] == 'admin_buyingReferrals') {
			for($start = $this->DirectReferralsPrice->find('count'); $start < 150; $start++) {
				$this->Security->unlockedFields[] = 'DirectReferralsPrice.'.$start.'.amount';
				$this->Security->unlockedFields[] = 'DirectReferralsPrice.'.$start.'.price';
			}
		}
	}

/**
 * locale method
 *
 * @return void
 */
	public function locale($loc = null) {
		$this->Cookie->name = 'user_settings';
		$this->Cookie->time = '1 week';

		if($loc === null) {
			$this->Cookie->write('locale', 'en', false);
			return $this->redirect($this->referer());
		}

		$paths = App::path('Locale');
		$dir = new Folder(reset($paths));
		$content = $dir->read();

		if(!in_array($loc, $content[0])) {
			$this->Cookie->write('locale', 'en', false);
			return $this->redirect($this->referer());
		}

		$this->Cookie->write('locale', $loc, false);

		return $this->redirect($this->referer());
	}

/**
 * admin_general method
 *
 * @return void
 */
	public function admin_general() {
		$keys = array(
			'focusAdView',
			'loadTimeAdView',
			'typeTimeAdView',
			'blockSameSignupIP',
			'blockSameLoginIP',
			'checkLoginIpDays',
			'checkSignupIpDays',
			'maintenanceInfo',
			'maintenanceIPs',
			'clearVisitedAds',
			'SMTP',
		);
		$globalKeys = array(
			'siteName',
			'siteTitle',
			'siteURL',
			'siteEmail',
			'siteEmailSender',
			'siteCurrency',
			'currencySymbol',
			'commaPlaces',
			'cutTrailing',
			'remindProfile',
			'currencyChangeDate',
			'googleAnalEnable',
			'googleAnalID',
			'maintenanceMode',
			'siteTheme',
			'onlineActive',
		);
		$currencyAttrs = array(
			'siteCurrency',
			'currencySymbol',
			'commaPlaces',
			'cutTrailing',
		);
		if($this->request->is(array('post', 'put'))) {

			foreach($currencyAttrs as $attr) {
				if(isset($this->request->data['Settings'][$attr]) && $this->request->data['Settings'][$attr] != Configure::read($attr)) {
					$this->request->data['Settings']['currencyChangeDate'] = date('Y-m-d H:i:s');
					break;
				}
			}

			if(isset($this->request->data['Settings']['siteCurrency']) && $this->request->data['Settings']['siteCurrency'] != Configure::read('siteCurrency')) {
				$actDeposits = $this->Payments->getActiveDeposits();
				$actCashouts = $this->Payments->getActiveCashouts();
				$turnOffDeposits = array();
				$turnOffCashouts = array();

				foreach($actDeposits as $gatewayHuman) {
					$gatewayName = PaymentsInflector::classify(PaymentsInflector::underscore($gatewayHuman));
					if(!$this->Payments->checkSupportedCurrency($this->request->data['Settings']['siteCurrency'], $gatewayName, 'Deposit')) {
						$turnOffDeposits[] = $gatewayHuman;
					}
				}
				foreach($actCashouts as $gatewayHuman) {
					$gatewayName = PaymentsInflector::classify(PaymentsInflector::underscore($gatewayHuman));
					if(!$this->Payments->checkSupportedCurrency($this->request->data['Settings']['siteCurrency'], $gatewayName, 'Cashout')) {
						$turnOffCashouts[] = $gatewayHuman;
					}
				}

				if(!empty($turnOffDeposits)) {
					$actDeposits = array_diff($actDeposits, $turnOffDeposits);

					if(!$this->Settings->store(array('Settings' => array('depositsGateways' => $actDeposits)), array('depositsGateways'))) {
						throw new InternalErrorException(__d('exception', 'Failed to turn off payments gateways.'));
					}

					$this->PaymentGateway->recursive = -1;
					$res = $this->PaymentGateway->updateAll(array(
						'PaymentGateway.deposits' => false,
					), array(
						'PaymentGateway.name' => $turnOffDeposits,
					));
					if(!$res) {
						throw new InternalErrorException(__d('exception', 'Failed to turn off payments gateways.'));
					}

					$this->Payments->refreshData();
					$this->Notice->info(__d('admin', 'The following gateways were turned off (Deposits): %s', implode($turnOffDeposits, ', ')));
				}

				if(!empty($turnOffCashouts)) {
					$actCashouts = array_diff($actCashouts, $turnOffCashouts);

					if(!$this->Settings->store(array('Settings' => array('cashoutsGateways' => $actCashouts)), array('cashoutsGateways'))) {
						throw new InternalErrorException(__d('exception', 'Failed to turn off payments gateways.'));
					}

					$this->PaymentGateway->recursive = -1;
					$res = $this->PaymentGateway->updateAll(array(
						'PaymentGateway.cashouts' => false,
					), array(
						'PaymentGateway.name' => $turnOffCashouts,
					));
					if(!$res) {
						throw new InternalErrorException(__d('exception', 'Failed to turn off payments gateways.'));
					}

					$this->Payments->refreshData();
					$this->Notice->info(__d('admin', 'The following gateways were turned off (Cashouts): %s', implode($turnOffCashouts, ', ')));
				}
			}
			if($this->Settings->store($this->request->data, $globalKeys, true) 
			 && $this->Settings->store($this->request->data, $keys)) {
				$this->Notice->success(__d('admin', 'The settings has been saved.'));
			} else {
				$this->Notice->error(__d('admin', 'The settings could not be saved. Please, try again.'));
			}
		}
		$settings = array();

		foreach($globalKeys as $k) {
			$settings[$k] = Configure::read($k);
		}

		$path = APP.'View'.DS.'Themed'.DS;

		$dir = new Folder($path);
		$themesDirs = $dir->read(true, true, false);

		$settings = Hash::merge($settings, $this->Settings->fetch($keys)['Settings']);
		$this->set(compact('settings'));
		$this->set('currencies', $this->Currency->find('list', array(
			'fields' => array('Currency.code', 'Currency.NameAndCode'),
			'order' => 'Currency.NameAndCode',
		)));
		$this->set('themes', array_combine($themesDirs[0], $themesDirs[0]));
	}

/**
 * admin_captcha method
 *
 * @return void
 */
	public function admin_captcha() {
		App::uses('CaptchasList', 'Captcha');
		$list = (new CaptchasList())->getList();

		$keys = array(
			'captchaType',
			'captchaOnLogin',
			'captchaOnRegistration',
			'captchaOnSupport',
			'captchaTypeSurfer',
			'captchaOnAdvertise',
		);

		$keys = Hash::merge($keys, $list);

		if($this->request->is(array('post', 'put'))) {
			if($this->Settings->store($this->request->data, $keys)) {
				$this->Notice->success(__d('admin', 'The settings has been saved.'));
			} else {
				$this->Notice->error(__d('admin', 'The settings could not be saved. Please, try again.'));
			}
		}
		$settings = $this->Settings->fetch($keys);
		$this->request->data = Hash::filter(Hash::merge($settings, $this->request->data));
		$this->set('available', $list);
	}

/**
 * admin_rentingReferrals method
 *
 * @return void
 */
	public function admin_rentingReferrals() {
		$keys = array(
			'rentMinClickDays',
			'rentFilter',
			'rentPeriod',
			'autoRenewDays',
			'autoRenewTries',
		);
		$globalKeys = array(
			'enableRentingReferrals',
			'rentingOption',
		);
		$data = $this->request->data;
		if($this->request->is(array('post', 'put'))) {
			$save = true;

			if(isset($data['RentExtensionPeriod']) && !empty($data['RentExtensionPeriod'])) {
				if(!$this->RentExtensionPeriod->saveMany($data['RentExtensionPeriod'])) {
					$this->Notice->error(__d('admin', 'Failed to save rent extension periods.'));
					$save = false;
				}
			}

			if($save) {
				if($this->Settings->store($data, $keys) 
				 && $this->Settings->store($data, $globalKeys, true)) {
					$this->Notice->success(__d('admin', 'The settings has been saved.'));
				} else {
					$this->Notice->error(__d('admin', 'The settings could not be saved. Please, try again.'));
				}
			}
		}
		$settings = $this->Settings->fetch($keys);
		foreach($globalKeys as $k) {
			$settings['Settings'][$k] = Configure::read($k);
		}
		$this->request->data = Hash::filter(Hash::merge($settings, $data));

		$rentingPeriods = $this->RentExtensionPeriod->find('all');
		$this->set(compact('rentingPeriods'));
	}

/**
 * admin_directReferrals method
 *
 * @return void
 */
	public function admin_buyingReferrals() {
		$keys = array(
			'directFilter',
			'directMinClickDays',
		);
		$globalKeys = array(
			'enableBuyingReferrals',
		);
		$success = false;
		if($this->request->is(array('post', 'put'))) {
			if($this->Settings->store($this->request->data, $keys)
			 && $this->Settings->store($this->request->data, $globalKeys, true)) {
				if($this->DirectReferralsPrice->saveMany($this->request->data['DirectReferralsPrice'])) {
					$success = true;
				}
			}

			if($success) {
				$this->Notice->success(__d('admin', 'The settings has been saved.'));
			} else {
				$this->Notice->error(__d('admin', 'The settings could not be saved. Please, try again.'));
			}
		}

		$prices = $this->DirectReferralsPrice->find('all');

		$settings = $this->Settings->fetch($keys);
		foreach($globalKeys as $k) {
			$settings['Settings'][$k] = Configure::read($k);
		}
		$this->request->data = Hash::filter(Hash::merge($settings, $this->request->data));
		$this->set(compact('prices'));
	}

/**
 * admin_assignReferrals
 *
 * @return void
 */
	public function admin_assignReferrals() {
		$settingsKeys = array(
			'enableRentingReferrals',
			'rentMinClickDays',
			'rentFilter',
			'enableBuyingReferrals',
			'directFilter',
			'directMinClickDays',
		);
		$settings = $this->Settings->fetch($settingsKeys);

		if($settings['Settings']['enableRentingReferrals']) {
			$rented = $this->User->countAvailRRefs($settings);
		} else {
			$rented = 'disabled';
		}
		if($settings['Settings']['enableBuyingReferrals']) {
			$direct = $this->User->countAvailDRefs($settings);
		} else {
			$direct = 'disabled';
		}

		if($this->request->is(array('post', 'put'))) {
			$data = &$this->request->data;
			if(!empty($data['number']) && is_numeric($data['number'])) {
				if(!empty($data['username'])) {
					$this->User->contain(array());
					$user = $this->User->findByUsername($data['username']);
					if(!empty($user)) {
						if(!empty($data['rentingType'])) {
							switch($data['rentingType']) {
								case 'direct':
									if($direct !== 'disabled') {
										if($data['number'] <= $direct) {
											switch($settings['Settings']['directFilter']) {
												case 'all':
													if($this->User->assignDirectRefs($user['User']['id'], $data['number'])) {
														$this->Notice->success(__d('admin', 'Direct referrals assigned.'));
													} else {
														$this->Notice->error(__d('admin', 'Failed to assign referrals. Please, try again.'));
													}
												break;

												case 'clickDays':
													if($this->User->assignDirectActiveClickedRefs($user['User']['id'], $data['number'], $settings['Settings']['directMinClickDays'])) {
														$this->Notice->success(__d('admin', 'Direct referrals assigned.'));
													} else {
														$this->Notice->error(__d('admin', 'Failed to assign referrals. Please, try again.'));
													}
												break;

												case 'onlyActive':
													if($this->User->assignDirectActiveRefs($user['User']['id'], $data['number'])) {
														$this->Notice->success(__d('admin', 'Direct referrals assigned.'));
													} else {
														$this->Notice->error(__d('admin', 'Failed to assign referrals. Please, try again.'));
													}
												break;
											}
										} else {
											$this->Notice->error(__d('admin', 'You cannot assign more referrals than there is available.'));
										}
									} else {
										$this->Notice->error(__d('admin', 'Buying referrals is disabled.'));
									}
								break;
								case 'rented':
									if($rented !== 'disabled') {
										if($data['number'] <= $rented) {
											if(is_numeric($data['days']) && $data['days'] > 0) {
												switch($settings['Settings']['rentFilter']) {
													case 'all':
														if($this->User->assignRentedRefs($user['User']['id'], $data['number'], $data['days'])) {
															$this->Notice->success('Rented referrals assigned.');
														} else {
															$this->Notice->error('Failed to assign referrals. Please, try again.');
														}
													break;

													case 'clickDays':
														if($this->User->assignRentedActiveClickedRefs($user['User']['id'], $data['number'], $settings['Settings']['rentMinClickDays'], $data['days'])) {
															$this->Notice->success('Rented referrals assigned.');
														} else {
															$this->Notice->error('Failed to assign referrals. Please, try again.');
														}
													break;

													case 'onlyActive':
														if($this->User->assignRentedActiveRefs($user['User']['id'], $data['number'], $data['days'])) {
															$this->Notice->success('Rented referrals assigned.');
														} else {
															$this->Notice->error('Failed to assign referrals. Please, try again.');
														}
													break;
												}
											} else {
												$this->Notice->error(__d('admin', 'Please select valid number of days'));
											}
										} else {
											$this->Notice->error(__d('admin', 'You cannot assign more referrals than there is available.'));
										}
									} else {
										$this->Notice->errror(__d('admin', 'Referral renting is disabled.'));
									}
								break;
								default:
									$this->Notice->error(__d('admin', 'Unknown type.'));
							}
						} else {
							$this->Notice->error(__d('admin', 'Unknown type.'));
						}
					} else {
						$this->Notice->error(__d('admin', 'User not found.'));
					}
				} else {
					$this->Notice->error(__d('admin', 'Please enter username.'));
				}
			} else {
				$this->Notice->error(__d('admin', 'Please enter number of referrals.'));
			}
		}
		$this->set(compact('direct', 'rented'));
	}

	public function admin_payments() {
		$success = false;
		$keys = array(
			'cashoutMode',
			'enableTransfers',
			'maximumTransfer',
			'minimumTransfer',
			'commissionTo',
			'allowUpgradeFromPBalance',
			'deleteReferralsBalance',
			'expiryReferralsBalance',
			'ignoreMinDeposit',
			'autoCashoutFail',
			'bitcoinDepositsSumHack',
		);
		if($this->request->is(array('post', 'put'))) {
			if(!empty($this->request->data['PaymentsGateway'])) {
				$gateways = &$this->request->data['PaymentsGateway'];
				foreach($gateways as $gateway) {
					if(isset($gateway['deposits']) && $gateway['deposits'] || isset($gateway['cashouts']) && $gateway['cashouts']) {
						if(!$this->UserStatistic->addDepositsColumn($gateway['name'])) {
							throw new InternalErrorException(__d('exception', 'Failed to create new deposits column in UserStatistic'));
						}
						if(!$this->UserStatistic->addCashoutsColumn($gateway['name'])) {
							throw new InternalErrorException(__d('exception', 'Failed to create new cashouts column in UserStatistic'));
						}
						if(!$this->UserProfile->addAccountIdColumn($gateway['name'])) {
							throw new InternalErrorException(__d('exception', 'Failed to create new column in UserProfile'));
						}
					}
				}
				if($this->PaymentGateway->saveMany(array_slice($gateways, 0))) {
					$this->Payments->refreshData();
					$success = true;
				} else {
					$this->Notice->error(__d('admin', 'Failed to save gateway settings'));
				}
			}
			if(!empty($this->request->data['Settings'])) {
				if(!isset($this->request->data['Settings']['cashoutMode'])) {
					$this->request->data['Settings']['cashoutMode'] = 'all';
				}
				if($this->Settings->store($this->request->data, $keys)) {
					$success = true;
				} else {
					$this->Notice->error(__d('admin', 'The settings could not be saved. Please, try again.'));
				}
			}
		}

		if($success) {
			$this->Notice->success(__d('admin', 'The settings has been saved.'));
		}

		$settings = $this->Settings->fetch($keys);
		$this->request->data = Hash::merge($settings, $this->request->data);

		$gatewaysActiveWithSettings = $this->Payments->getActiveWithSettingsHumanized();
		$gatewaysActive = $this->Payments->getActiveHumanized();
		$gatewaysSupportedDeposits = $this->Payments->getSupportedHumanized('Deposit');
		$gatewaysSupportedCashouts = $this->Payments->getSupportedHumanized('Cashout');
		$gatewaysSupported = Hash::merge($gatewaysSupportedDeposits, $gatewaysSupportedCashouts);
		unset($gatewaysActive['PurchaseBalance']);

		$gatewaysData = $this->PaymentGateway->find('all');

		$gatewaysData = $this->PaymentGateway->arrayByName($gatewaysData);

		if(isset($this->request->data['PaymentsGateway'])) {
			$this->request->data['PaymentsGateway'] = Hash::merge($gatewaysData, $this->request->data['PaymentsGateway']);
		} else {
			$this->request->data['PaymentsGateway'] = $gatewaysData;
		}
		$this->set(compact(
			'gatewaysActiveWithSettings',
			'gatewaysSupportedDeposits',
			'gatewaysSupportedCashouts',
			'gatewaysSupported',
			'gatewaysActive'
		));
	}

/**
 * admin_tos()
 *
 * @return void
 */
	public function admin_tos() {
		$this->helpers[] = 'TinyMCE.TinyMCE';

		if($this->request->is(array('post', 'put'))) {
			$data = array(
				'Settings' => array(
					'siteToS' => $this->request->data['text'],
					'siteToSTitle' => $this->request->data['title'],
					'siteToSActive' => $this->request->data['enable'],
				),
			);
			if($this->Settings->store($data, array('siteToS', 'siteToSTitle')) && $this->Settings->store($data, array('siteToSActive'), true)) {
				$this->Notice->success(__d('admin', 'ToS saved successfully.'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to save, please try again.'));
			}
		} else {
			$settings = $this->Settings->fetch(array('siteToS', 'siteToSTitle'));

			$this->request->data['text'] = $settings['Settings']['siteToS'];
			$this->request->data['title'] = $settings['Settings']['siteToSTitle'];
			$this->request->data['enable'] = Configure::read('siteToSActive');
		}
	}

/**
 * admin_privacy()
 *
 * @return void
 */
	public function admin_privacy() {
		$this->helpers[] = 'TinyMCE.TinyMCE';

		if($this->request->is(array('post', 'put'))) {
			$data = array(
				'Settings' => array(
					'sitePrivacyPolicy' => $this->request->data['text'],
					'sitePrivacyPolicyTitle' => $this->request->data['title'],
					'sitePrivacyPolicyActive' => $this->request->data['enable'],
				),
			);
			if($this->Settings->store($data, array('sitePrivacyPolicy', 'sitePrivacyPolicyTitle')) && $this->Settings->store($data, array('sitePrivacyPolicyActive'), true)) {
				$this->Notice->success(__d('admin', 'Privacy Policy saved successfully.'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to save, please try again.'));
			}
		} else {
			$settings = $this->Settings->fetch(array('sitePrivacyPolicy', 'sitePrivacyPolicyTitle'));

			$this->request->data['text'] = $settings['Settings']['sitePrivacyPolicy'];
			$this->request->data['title'] = $settings['Settings']['sitePrivacyPolicyTitle'];
			$this->request->data['enable'] = Configure::read('sitePrivacyPolicyActive');
		}
	}

/**
 * admin_faq()
 *
 * @return void
 */
	public function admin_faq() {
		$this->helpers[] = 'TinyMCE.TinyMCE';

		if($this->request->is(array('post', 'put'))) {
			$data = array(
				'Settings' => array(
					'siteFAQ' => $this->request->data['text'],
					'siteFAQTitle' => $this->request->data['title'],
					'siteFAQActive' => $this->request->data['enable'],
				),
			);
			if($this->Settings->store($data, array('siteFAQ', 'siteFAQTitle')) && $this->Settings->store($data, array('siteFAQActive'), true)) {
				$this->Notice->success(__d('admin', 'FAQ saved successfully.'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to save, please try again.'));
			}
		} else {
			$settings = $this->Settings->fetch(array('siteFAQ', 'siteFAQTitle'));

			$this->request->data['text'] = $settings['Settings']['siteFAQ'];
			$this->request->data['title'] = $settings['Settings']['siteFAQTitle'];
			$this->request->data['enable'] = Configure::read('siteFAQActive');
		}
	}

/**
 * admin_emails method
 *
 * @return void
 */
	public function admin_emails() {
		$this->helpers[] = 'TinyMCE.TinyMCE';
		$this->Email = ClassRegistry::init('Email');

		if($this->request->is(array('post', 'put'))) {
			if($this->Email->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'E-mail saved successfully.'));
			} else {
				$this->Notice->error(__d('admin', 'Failed to save e-mail. Please try again.'));
			}
		}

		$emails = $this->Email->find('all');
		$formats = $this->Email->getFormats();
		$variables = $this->Email->getVariables();

		$this->set(compact('emails', 'formats', 'variables'));
	}

/**
 * admin_activity method
 *
 * @return void
 */
	public function admin_activity() {
		$keys = array(
			'withdrawClicks',
			'inactivitySuspendDays',
			'inactivityDeleteDays',
			'emailVerification',
			'cashoutBlockTime',
			'googleAuthenticator',
		);
		$globalKeys = array(
			'userActivityClicks',
			'forceSSL',
			'disableSSLForum',
		);

		if($this->request->is(array('post', 'put'))) {
			if(empty($this->request->data['Settings']['googleAuthenticator'])) {
				$this->request->data['Settings']['googleAuthenticator'] = array();
			}

			if($this->Settings->store($this->request->data, $globalKeys, true) 
			 && $this->Settings->store($this->request->data, $keys)) {
				$url = Configure::read('siteURL');

				if(!empty($url)) {
					$url = parse_url($url);
					if($this->request->data['Settings']['forceSSL']) {
						$url['scheme'] = 'https';
					} else {
						$url['scheme'] = 'http';
					}
					$url = $url['scheme'].'://'.$url['host'];
				}

				if($this->Settings->store(array('Settings' => array('siteURL' => $url)), array('siteURL'), true)) {
					$this->Notice->success(__d('admin', 'The settings has been saved.'));
				} else {
					$this->Notice->error(__d('admin', 'Failed to change default site URL protocol. Please try again.'));
				}
			} else {
				$this->Notice->error(__d('admin', 'The settings could not be saved. Please, try again.'));
			}
		}
		$settings = $this->Settings->fetch($keys);

		foreach($globalKeys as $k) {
			$settings['Settings'][$k] = Configure::read($k);
		}

		$this->request->data = Hash::merge($settings, $this->request->data);
	}

/**
 * admin_cron method
 *
 * @return void
 */
	public function admin_cron() {
		$jobs = array(
			'0 0 * * * cd "'.APP.'" && ./Console/cake Cron daily >/dev/null 2>&1',
			'*/10 * * * * cd "'.APP.'" && ./Console/cake Cron frequent >/dev/null 2>&1',
		);

		$httpJobs = array(
			Router::url(array('admin' => false, 'controller' => 'crons', 'action' => 'run', 'daily'), true),
			Router::url(array('admin' => false, 'controller' => 'crons', 'action' => 'run', 'frequent'), true),
		);

		if(Module::installed('BotSystem')) {
			$jobs[] = '*/30 * * * * cd "'.APP.'" && ./Console/cake Cron botSystem >/dev/null 2>&1';
			$httpJobs[] = Router::url(array('admin' => false, 'controller' => 'crons', 'action' => 'run', 'botSystem'), true);
		}

		$keys = array(
			'PTCDeleteAfter',
			'bannerAdsDeleteAfter',
			'featuredAdsDeleteAfter',
			'loginAdsDeleteAfter',
			'paidOffersDeleteAfter',
			'AdGridDeleteAfter',
			'emailsPerShell',
			'PTCStatsDays',
			'removeReferralsOverflow',
			'allowHttpCron',
			'httpCronIPs',
			'unverifiedDeleteDays',
			'depositsPendingPurgeHours',
		);

		if($this->request->is(array('post', 'put'))) {
			if($this->Settings->store($this->request->data, $keys)) {
				$this->Notice->success(__d('admin', 'The settings has been saved.'));
			} else {
				$this->Notice->error(__d('admin', 'The settings could not be saved. Please, try again.'));
			}
		}
		$this->request->data = Hash::merge($this->Settings->fetch($keys), $this->request->data);

		$this->set(compact('jobs', 'httpJobs'));
	}

/**
 * admin_promotions method
 *
 * @return void
 */
	public function admin_promotions() {
		$keys = array(
			'signUpBonus',
			'depositBonus',
		);

		if($this->request->is(array('post', 'put'))) {
			if(isset($this->request->data['Settings']['signUpBonus'])) {
				if($this->request->data['Settings']['signUpBonus']['type'] == 'money') {
					unset($this->request->data['Settings']['signUpBonus']['membership']);
					unset($this->request->data['Settings']['signUpBonus']['period']);
				} elseif($this->request->data['Settings']['signUpBonus']['type'] == 'membership') {
					unset($this->request->data['Settings']['signUpBonus']['amount']);
					unset($this->request->data['Settings']['signUpBonus']['credit']);
				}
			}
			if($this->Settings->store($this->request->data, $keys)) {
				$this->Notice->success(__d('admin', 'The settings has been saved.'));
			} else {
				$this->Notice->error(__d('admin', 'The settings could not be saved. Please, try again.'));
			}
		}
		$settings = $this->Settings->fetch($keys);

		$this->request->data = Hash::merge($settings, $this->request->data);

		$membershipModel = ClassRegistry::init('Membership');
		$membershipsB = $memberships = $membershipModel->getList(true);
		unset($membershipsB[$membershipModel->getDefaultId()]);

		$types = array();
		foreach($this->Settings->validate['signUpBonus']['rule'][1]['type']['rule'][1] as $t) {
			$types[$t] = __d('admin', ucfirst($t));
		}

		$gateways = $this->Payments->getActiveDepositsHumanized();
		if(isset($gateways['PurchaseBalance'])) {
			unset($gateways['PurchaseBalance']);
		}

		$this->set(compact('types', 'memberships', 'membershipsB', 'gateways'));
	}

	public function admin_clear_cache() {
		$this->request->allowMethod(array('post', 'delete'));

		clearCache(null, 'persistent');
		clearCache(null, 'sql');
		clearCache(null, 'forum');
		clearCache(null, 'models');
		Cache::clear();

		return $this->redirect($this->referer());
	}
}
