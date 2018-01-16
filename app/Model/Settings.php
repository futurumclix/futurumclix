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
App::uses('AppModel', 'Model');
App::uses('GatewaysList', 'Payments');
/**
 * Settings Model
 *
 */
class Settings extends AppModel {
/**
 * useTable variable
 *
 * @var string
 */
	public $useTable = 'settings';

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'value';

/**
 * Primary key
 *
 * @var string
 */
	public $primaryKey = 'key';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'key' => array(
			'characters' => array(
				'rule' => array('custom', '/^[a-z\d\-_\.\!\s]+$/i'),
				'allowEmpty' => false,
			),
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'on' => 'create',
			),
			'minLength' => array(
				'rule' => array('minLength', 5),
				'on' => 'create',
			),
		),
		'siteTitle' => array(
			'nonEmpty' => array(
				'rule' => array('minLength', 1),
				'message' => 'Please enter site title.',
				'allowEmpty' => false,
			),
		),
		'siteName' => array(
			'nonEmpty' => array(
				'rule' => array('minLength', 1),
				'message' => 'Please enter site name.',
				'allowEmpty' => false,
			),
		),
		'siteURL' => array(
			'nonEmpty' => array(
				'rule' => array('minLength', 7), // 7 == strlen('http://')
				'message' => 'Please enter site URL.',
				'allowEmpty' => false,
			),
			'url' => array(
				'rule' => array('url', true),
				'message' => 'Please enter a valid URL.',
			),
		),
		'siteEmail' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Site email should be a valid e-mail address.',
				'allowEmpty' => false,
			),
		),
		'siteTheme' => array(
			'rule' => array('checkTheme'),
			'allowEmpty' => true,
		),
		'focusAdView' => 'boolean',
		'loadTimeAdView' => array(
			'natural' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Time should be a natural number.',
				'allowEmpty' => false,
			),
		),
		'typeTimeAdView' => array(
			'list' => array(
				'rule' => array('inList', array('dual', 'immediately', 'afterLoad')),
				'message' => 'Wrong value for start timer.',
				'allowEmpty' => false,
			),
		),
		'captchaType' => array(
			'list' => array(
				'rule' => array('inList', array('SolveMedia', 'reCaptcha', 'SweetCaptcha', 'disabled')),
				'message' => 'Unsupported captcha type.',
				'allowEmpty' => false,
			),
		),
		'captchaOnLogin' => 'boolean',
		'captchaOnRegistration' => 'boolean',
		'captchaOnSupport' => 'boolean',
		'captchaTypeSurfer' => array(
			'list' => array(
				'rule' => array('inList', array('disabled', 'SolveMedia', 'reCaptcha', 'SweetCaptcha')),
				'message' => 'Unsupported captcha type.',
				'allowEmpty' => false,
			),
		),
		'enableRentingReferrals' => 'boolean',
		'rentingOption' => array(
			'list' => array(
				'rule' => array('inList', array('realOnly')), /* when BotSystem module is added this field can also have value: 'botsOnly' */
				'message' => 'Unsupported renting option.',
				'allowEmpty' => false,
			),
		),
		'rentMinClickDays' => array(
			'natural' => array(
				'rule' => array('naturalNumber'),
				'message' => 'Number of days should be a natural number.',
				'allowEmpty' => false,
			),
		),
		'rentFilter' => array(
			'list' => array(
				'rule' => array('inList', array('clickDays', 'onlyActive', 'all')),
				'message' => 'Unsupported renting filter',
				'allowEmpty' => false,
			),
		),
		'rentPeriod' => array(
			'natural' => array(
				'rule' => array('naturalNumber'),
				'message' => 'Number of days should be a natural number.',
				'allowEmpty' => false,
			),
		),
		'enableBuyingReferrals' => 'boolean',
		'directFilter' => array(
			'list' => array(
				'rule' => array('inList', array('clickDays', 'onlyActive', 'all')),
				'message' => 'Unsupported renting filter.',
				'allowEmpty' => false,
			),
		),
		'directMinClickDays' => array(
			'natural' => array(
				'rule' => array('naturalNumber'),
				'message' => 'Number of days should be a natural number.',
				'allowEmpty' => false,
			),
		),
		'activeStatsNumber' => array(
			'rule' => array('range', -1, 7),
			'allowEmpty' => false,
		),
		'blockSameSignupIP' => 'boolean',
		'blockSameLoginIP' => 'boolean',
		'cashoutMode' => array(
			'list' => array(
				'rule' => array('inList', array('all', 'most', 'mostUnlimited')),
				'message' => 'Unknown cashout mode',
				'allowEmpty' => false,
			),
		),
		'maximumTransfer' => array(
			'rule' => 'checkMonetary',
			'message' => 'Maximum transfer value should be a decimal value.',
			'allowEmpty' => false,
		),
		'minimumTransfer' => array(
			'rule' => 'checkMonetary',
			'message' => 'Minimum transfer value should be a decimal value.',
			'allowEmpty' => false,
		),
		'currencySymbol' => array(
			'rule' => array('inList', array('ls', 'l', 'rs', 'r')),
			'message' => 'Invalid value for currency symbol display mode.',
			'allowEmpty' => false,
		),
		'commaPlaces' => array(
			'range' => array(
				'rule' => array('range', -1, 9),
				'message' => 'Available places after comma should be in range [0, 8].',
				'allowEmpty' => false,
			),
			'natural' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Available places after comma should be a valid natural number (0 included).',
 			),
		),
		'totalDeposits' => array(
			'rule' => array('checkMonetary', true),
			'message' => 'Total deposits amount should be a decimal value.',
			'allowEmpty' => false,
		),
		'totalCashouts' => array(
			'rule' => array('checkMonetary', true),
			'message' => 'Total deposits amount should be a decimal value.',
			'allowEmpty' => false,
		),
		'allowUpgradeFromPBalance' => 'boolean',
		'commissionTo' => array(
			'list' => array(
				'rule' => array('inList', array('account_balance', 'purchase_balance')),
				'message' => 'Commission can be added only to Account or Purchase Balance.',
			),
		),
		'checkLoginIpDays' => array(
			'rule' => array('naturalNumber', true),
			'message' => 'Number of days should be a natural number.',
			'allowEmpty' => false,
		),
		'checkSignupIpDays' => array(
			'rule' => array('naturalNumber', true),
			'message' => 'Number of days should be a natural number.',
			'allowEmpty' => false,
		),
		'deleteReferralsBalance' => array(
			'rule' => array('inList', array('account', 'purchase', 'both')),
			'message' => 'Money can be taken only from Account or Purchase balance.',
			'allowEmpty' => false,
		),
		'PTCTitleLength' => array(
			'range' => array(
				'rule' => array('range', -1, 129),
				'message' => 'Title length should be between 0 and 128.',
				'allowEmpty' => false,
			),
			'natural' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Title length should be a valid natural number.',
			),
		),
		'PTCDescLength' => array(
			'range' => array(
				'rule' => array('range', -1, 1025),
				'message' => 'Description length should be between 0 and 1024.',
				'allowEmpty' => false,
			),
			'natural' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Description length should be a valid natural number.',
			),
		),
		'PTCAutoApprove' => 'boolean',
		'PTCCheckConnection' => 'boolean',
		'PTCPreviewTime' => array(
			'rule' => array('naturalNumber', true),
			'message' => 'Preview time should be a valid natural number.',
		),
		'PTCCheckConnectionTimeout' => array(
			'rule' => array('naturalNumber', true),
			'message' => 'Timeout should be a valid natural number.',
		),
		'Forum.newestUser' => 'boolean',
		'Forum.indexStatistics' => 'boolean',
		'userActivityClicks' => array(
			'rule' => array('naturalNumber', true),
			'message' => 'Activity clicks should be a valid natural number.',
		),
		'withdrawClicks' => array(
			'rule' => array('naturalNumber', true),
			'message' => 'Withdraw clicks should be a valid natural number.',
		),
		'inactivitySuspendDays' => array(
			'rule' => array('naturalNumber', true),
			'message' => 'Days should be a valid natural number.',
		),
		'inactivityDeleteDays' => array(
			'rule' => array('naturalNumber', true),
			'message' => 'Days should be a valid natural number.',
		),
		'emailVerification' => 'boolean',
		'cashoutVerification' => 'boolean',
		'cashoutBlockTime' => array(
			'rule' => array('naturalNumber', true),
			'message' => 'Number of hours should be a valid natural number.',
		),
		'maintenanceMode' => 'boolean',
		'maintenanceIPs' => array(
			'rule' => '/^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}(,\n|,?))*$/',
			'message' => 'Please enter a comma separated list of valid IP addresses',
			'allowEmpty' => true,
		),
		'googleAnalEnable' => 'boolean',
		'removeReferralsOverflow' => 'boolean',
		'featuredAdsAutoApprove' => 'boolean',
		'featuredAdsPerBox' => array(
			'rule' => array('naturalNumber', true),
			'message' => 'Ads per box should be a valid natural number.',
		),
		'featuredAdsTitleMaxLen' => array(
			'range' => array(
				'rule' => array('range', -1, 129),
				'message' => 'Title length should be between 0 and 128.',
				'allowEmpty' => false,
			),
			'natural' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Title length should be a valid natural number.',
			),
		),
		'featuredAdsDescMaxLen' => array(
			'range' => array(
				'rule' => array('range', -1, 301),
				'message' => 'Description length should be between 0 and 300.',
				'allowEmpty' => false,
			),
			'natural' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Description length should be a valid natural number.',
			),
		),
		'bannerAdsAutoApprove' => 'boolean',
		'bannerAdsTitleMaxLen' => array(
			'range' => array(
				'rule' => array('range', -1, 129),
				'message' => 'Title length should be between 0 and 128.',
				'allowEmpty' => false,
			),
			'natural' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Title length should be a valid natural number.',
			),
		),
		'loginAdsAutoApprove' => 'boolean',
		'loginAdsTitleMaxLen' => array(
			'range' => array(
				'rule' => array('range', -1, 129),
				'message' => 'Title length should be between 0 and 128.',
				'allowEmpty' => false,
			),
			'natural' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Title length should be a valid natural number.',
			),
		),
		'loginAdsPerBox' => array(
			'natural' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Title length should be a valid natural number.',
			),
		),
		'loginAdsShowMode' => array(
			'list' => array(
				'rule' => array('inList', array('never', 'login', 'day')),
				'messsage' => 'Login Ads can be showed only on every login or once per day.'
			),
		),
		'supportEnabled' => 'boolean',
		'supportRequireLogin' => 'boolean',
		'supportMinMsgLen' => array(
			'natural' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Title length should be a valid natural number.',
			),
		),
		'signUpBonus' => array(
			'rule' => array('SettingsArray', array(
				'enable' => 'boolean',
				'start' => array(
					'rule' => array('datetime'),
					'message' => 'Please enter a valid date and time.',
					'allowEmpty' => false,
				),
				'end' => array(
					'rule' => array('datetime'),
					'message' => 'Please enter a valid date and time.',
					'allowEmpty' => false,
				),
				'type' => array(
					'rule' => array('inList', array('money', 'membership')),
					'message' => 'Type can be only "money" or "membership".',
					'allowEmpty' => false,
				),
				'amount' => array(
					'rule' => array('checkMonetary'),
					'message' => 'Please enter a valid monetary value',
					'allowEmpty' => false,
				),
				'credit' => array(
					'rule' => array('inList', array('account', 'purchase')),
					'message' => 'Balance can be only "account" or "purchase".',
					'allowEmpty' => true,
				),
				'membership' => array(
					'rule' => array('numeric'),
					'message' => 'Please select a membership.',
					'allowEmpty' => false,
				),
				'period' => array(
					'rule' => array('naturalNumber', false),
					'message' => 'Period should be a natural number.',
					'allowEmpty' => false,
				),
			)),
		),
		'autoRenewDays' => array(
			'numbers' => array(
				'rule' => '/^\d+(?:,\d+)*$/',
				'message' => 'AutoRenew days should be numbers separated by commas.',
				'allowEmpty' => false,
			),
		),
		'autoRenewTries' => array(
			'rule' => array('range', -2, 129),
			'message' => 'AutoRenew tries number should be between -1 and 128.',
			'allowEmpty' => false,
		),
		'paidOffersActive' => array(
			'rule' => 'boolean',
			'message' => 'Paid Offers active setting should be a boolean value',
			'allowEmpty' => false,
		),
		'paidOffers' => array(
			'rule' => array('SettingsArray', array(
				'autoApprove' => 'boolean',
				'titleLength' => array(
					'range' => array(
						'rule' => array('range', -1, 129),
						'message' => 'Title length should be between 0 and 128.',
						'allowEmpty' => false,
					),
					'natural' => array(
						'rule' => array('naturalNumber', true),
						'message' => 'Title length should be a valid natural number.',
					),
				),
				'descLength' => array(
					'range' => array(
						'rule' => array('range', -1, 301),
						'message' => 'Description length should be between 0 and 300.',
						'allowEmpty' => false,
					),
					'natural' => array(
						'rule' => array('naturalNumber', true),
						'message' => 'Description length should be a valid natural number.',
					),
				),
				'banApplications' => array(
					'natural' => array(
						'rule' => array('naturalNumber', true),
						'message' => 'Ban applications should be a valid natural number.',
					),
				),
				'applicationAutoApproveDays' => array(
					'natural' => array(
						'rule' => array('naturalNumber', true),
						'message' => 'Application approve days should be a valid natural number.',
					),
				),
			),
		)),
		'Evercookie' => array(
			'rule' => array('SettingsArray', array(
				'enable' => 'boolean',
				'name' => array(
					'notEmpty' => array(
						'rule' => array('minLength', 3),
						'message' => 'Evercookie Cookie name have to be atleast %d characters long.',
					),
				),
				'mode' => array(
					'list' => array(
						'rule' => array('inList', array('suspend', 'email')),
					)
				),
				'options' => array('SettingsArray', array(
					'java' => array(
						'boolean' => array(
							'rule' => 'boolean',
						),
					),
					'silverlight' => array(
						'boolean' => array(
							'rule' => 'boolean',
						),
					),
					'history' => array(
						'boolean' => array(
							'rule' => 'boolean',
						),
					),
				)),
				'exceptions' => array('minLength', 0), /* only for SettingsArray validation, to check keys */
			)),
		),
		'allowHttpCron' => 'boolean',
		'httpCronIPs' => array(
			'commaSeparatedIPs' => array(
				'rule' => '/^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}(,\n|,?))*$/',
				'message' => 'Please enter a comma separated list of valid IP addresses',
				'allowEmpty' => true,
			),
		),
		'clearVisitedAds' => array(
			'list' => array(
				'rule' => array('inList', array('accurate', 'daily', 'first', 'last', 'constPerUser')),
			),
		),
		'SMTP' => array(
			'rule' => array('SettingsArray', array(
				'enable' => 'boolean',
				'port' => array(
					'numeric' => array(
						'rule' => 'numeric',
						'message' => 'Port number should be a valid numeric value',
						'allowEmpty' => true,
					),
				),
				'tls' => 'boolean',
				'host' => array(),
				'password' => array(),
				'username' => array(),
			)),
		),
		'expressAds' => array(
			'rule' => array('SettingsArray', array(
				'titleLen' => array(
					'range' => array(
						'rule' => array('range', -1, 129),
						'message' => 'Title length should be between 0 and 128.',
						'allowEmpty' => false,
					),
					'natural' => array(
						'rule' => array('naturalNumber', true),
						'message' => 'Title length should be a valid natural number.',
					),
				),
				'descLen' => array(
					'range' => array(
						'rule' => array('range', -1, 1025),
						'message' => 'Description length should be between 0 and 1024.',
						'allowEmpty' => false,
					),
					'natural' => array(
						'rule' => array('naturalNumber', true),
						'message' => 'Description length should be a valid natural number.',
					),
				),
				'autoApprove' => 'boolean',
				'descShow' => 'boolean',
				'geo_targetting' => 'boolean',
				'referrals_earnings' => 'boolean',
			)),
		),
		'explorerAds' => array(
			'rule' => array('SettingsArray', array(
				'titleLen' => array(
					'range' => array(
						'rule' => array('range', -1, 129),
						'message' => 'Title length should be between 0 and 128.',
						'allowEmpty' => false,
					),
					'natural' => array(
						'rule' => array('naturalNumber', true),
						'message' => 'Title length should be a valid natural number.',
					),
				),
				'descLen' => array(
					'range' => array(
						'rule' => array('range', -1, 1025),
						'message' => 'Description length should be between 0 and 1024.',
						'allowEmpty' => false,
					),
					'natural' => array(
						'rule' => array('naturalNumber', true),
						'message' => 'Description length should be a valid natural number.',
					),
				),
				'maxSubpages' => array(
					'range' => array(
						'rule' => array('range', -1, 256),
						'message' => 'Subpages amount should be in rage [0, 255].',
						'allowEmpty' => false,
					),
					'natural' => array(
						'rule' => array('naturalNumber', true),
						'message' => 'Subpages amount should be a valid natural number.',
					),
				),
				'previewSubpages' => array(
					'range' => array(
						'rule' => array('range', -1, 256),
						'message' => 'Preview subpages amount should be in rage [0, 255].',
						'allowEmpty' => false,
					),
					'natural' => array(
						'rule' => array('naturalNumber', true),
						'message' => 'Preview subpages amount should be a valid natural number.',
					),
				),
				'previewTime' => array(
					'natural' => array(
						'rule' => array('naturalNumber', true),
						'message' => 'Preview time should be a valid natural number.',
					),
				),
				'autoApprove' => 'boolean',
				'descShow' => 'boolean',
				'geo_targetting' => 'boolean',
				'referrals_earnings' => 'boolean',
				'timers' => array('SettingsArray'),
			)),
		),
		'unverifiedDeleteDays' => array(
			'natural' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Unverified users delete days should be a valid natural number.',
			),
		),
		'depositsPendingPurgeHours' => array(
			'natural' => array(
				'rule' => array('naturalNumber', true),
				'message' => 'Hours amount should be a valid natural number.',
			),
		),
		'autoCashoutFail' => array(
			'list' => array(
				'rule' => array('inList', array('failed', 'new', 'cancelled')),
				'message' => 'Please select how to behave when automatic cashout fails.',
			),
		),
	);

/**
 * beforeValidate callback
 *
 * @return void
 */
	public function beforeValidate($options = array()) {
		parent::beforeValidate($options);
		if(Module::active('BotSystem')) {
			$this->validate['rentingOption']['list']['rule'][1][] = 'botsOnly';
		}
	}

/**
 * beforeSave callback
 *
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if(isset($this->data[$this->alias]['value'])) {
			$this->data[$this->alias]['value'] = serialize($this->data[$this->alias]['value']);
		}
		return true;
	}

/**
 * afterFind callback
 *
 * @return array
 */
	public function afterFind($results, $primary = false) {
		for($i = 0, $e = count($results); $i < $e; ++$i) {
			if(isset($results[$i][$this->alias]['value'])) {
				$results[$i][$this->alias]['value'] = unserialize($results[$i][$this->alias]['value']);
			}
		}
		return $results;
	}

/**
 * settingsArray method
 *
 * @return boolean
 */
	public function settingsArray($check, $rules, $checkKeys = false) {
		if($checkKeys !== false) {
			reset($check);
			$key = key($check);
			$data = &$check[$key];
			$diff = array_diff_key($data, array_flip(array_keys($rules)));
			if(!empty($diff)) {
				return __d('admin', 'Too many keys in Settings "%s" array (%s).', $key, implode(array_flip($diff), ', '));
			}
		}

		$oldValidate = $this->validate;
		$this->validate = $rules;
		$res = $this->validateMany($check);
		$this->validate = $oldValidate;
		return $res;
	}

/**
 * checkTheme method
 *
 * @return boolean
 */
	public function checkTheme($check) {
		$theme = array_values($check)[0];

		$path = APP.'View'.DS.'Themed'.DS;

		$dir = new Folder($path);
		$themesDirs = $dir->read(true, true, false);

		return in_array($theme, $themesDirs[0]);
	}

/**
 * fetchGlobals method
 *
 * @return array
 */
	public function fetchGlobals($keys = null) {
		return $this->fetch($keys, array('global' => true));
	}

/**
 * fetch method
 *
 * @return array
 */
	public function fetch($keys = null, $conditions = null) {
		if($keys !== null) {
			if(!is_array($keys)) {
				$conditions = array(
					'key' => $keys,
				);
			} else {
				$conditions = array(
					'key IN' => $keys,
				);
			}
		}
		$data = $this->find('list', array(
			'conditions' => $conditions,
		));
		return array($this->alias => $data);
	}

/**
 * fetchOne method
 *
 * @return mixed
 */
	public function fetchOne($key, $defVal = array()) {
		$settings = $this->fetch($key);

		if(empty($settings) || !isset($settings[$this->alias][$key])) {
			return $defVal;
		}

		return $settings[$this->alias][$key];
	}

/**
 * keyExists method
 *
 * @return boolean
 */
	public function keyExists($key) {
		$res = $this->find('count', array(
			'conditions' => array(
				'key' => $key,
			),
		));

		return $res > 0;
	}

/**
 * store method
 *
 * @return mixed
 */
	public function store($data = null, $allowedKeys = array(), $globals = false) {
		if($data === null) {
			return false;
		}

		$validate = $data;

		if(!$this->validateMany($validate)) {
			$this->validationErrors = $this->validationErrors[$this->alias];
			return false;
		}

		if(!isset($data[$this->alias])) {
			return false;
		}

		if(!is_array($allowedKeys)) {
			$allowedKeys = array($allowedKeys);
		}

		$data = $data[$this->alias];
		$data = array_intersect_key($data, array_flip($allowedKeys));
		$toDelete = array();
		$toSave = array();

		foreach($data as $key => $value) {
			if($value != 0 && empty($value)) {
				$toDelete[] = $key;
			} else {
				$toSave[] = array(
					'key' => $key,
					'value' => $value,
					'global' => $globals,
				);
			}
		};

		if(!empty($toDelete)) {
			if(count($toDelete) == 1) {
				$toDelete = $toDelete[0];
			}
			$this->deleteAll(array('key' => $toDelete));
		}

		if(!empty($toSave)) {
			if(!$this->saveMany($toSave, array('validate' => false))) {
				return false;
			}
			if($globals) {
				foreach($data as $key => $value) {
					Configure::write($key, $value);
				}
			}
			return true;
		} else {
			return true;
		}
	}

/**
 * newDeposit
 *
 * @return mixed
 */
	public function newDeposit($amount) {
		$old = $this->fetch('totalDeposits');

		if(empty($old)) {
			$old = '0';
		} else {
			$old = $old[$this->alias]['totalDeposits'];
		}

		$new = bcadd($old, $amount);

		return $this->store(array($this->alias => array('totalDeposits' => $new)), array('totalDeposits'));
	}

/**
 * cancelDeposit
 *
 * @return mixed
 */
	public function cancelDeposit($amount) {
		$old = $this->fetch('totalDeposits');

		if(empty($old)) {
			$new = '0';
		} else {
			$new = bcsub($old[$this->alias]['totalDeposits'], $amount);
		}

		return $this->store(array($this->alias => array('totalDeposits' => $new)), array('totalDeposits'));
	}

/**
 * newDeposit
 *
 * @return mixed
 */
	public function newCashout($amount) {
		$old = $this->fetch('totalCashouts');

		if(empty($old)) {
			$old = '0';
		} else {
			$old = $old[$this->alias]['totalCashouts'];
		}

		$new = bcadd($old, $amount);

		return $this->store(array($this->alias => array('totalCashouts' => $new)), array('totalCashouts'));
	}

/**
 * cancelDeposit
 *
 * @return mixed
 */
	public function cancelCashout($amount) {
		$old = $this->fetch('totalCashouts');

		if(empty($old)) {
			$new = '0';
		} else {
			$new = bcsub($old[$this->alias]['totalCashouts'], $amount);
		}

		return $this->store(array($this->alias => array('totalCashouts' => $new)), array('totalCashouts'));
	}

/**
 * magicStatsNumber
 *
 * @return int
 */
	public function magicStatsNumber($daysAgo = 0) {
		$magic = $this->fetchOne('activeStatsNumber');

		for($i = 0; $i < $daysAgo; $i++) {
			if(--$magic < 0) {
				$magic = 6;
			}
		}

		return $magic;
	}
}
