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
App::uses('PaidOffersApplication', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
App::uses('Security', 'Utility');
App::uses('String', 'Utility');
/**
 * User Model
 *
 */
class User extends AppModel {
/**
 * actAs
 *
 * @var array
 */
	public $actsAs = array(
		'Containable',
	);

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'username';

/**
 * hasOne associations
 *
 * @var array
 */
	public $hasOne = array(
		'UserMetadata' => array(
			'className' => 'UserMetadata',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'UserProfile' => array(
			'className' => 'UserProfile',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'UserStatistic' => array(
			'className' => 'UserStatistic',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'UserSecret' => array(
			'className' => 'UserSecret',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'LastCashout' => array(
			'className' => 'Cashout',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'order' => 'LastCashout.created DESC',
			'limit' => 1,
		),
	);

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Upline' => array(
			'className' => 'User',
			'foreignKey' => 'upline_id',
			'dependent' => false,
			'counterCache' => 'refs_count',
		),
		'RentedUpline' => array(
			'className' => 'User',
			'foreignKey' => 'rented_upline_id',
			'dependent' => false,
			'counterCache' => 'rented_users_count',
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Refs' => array(
			'className' => 'User',
			'foreignKey' => 'upline_id',
			'dependent' => false,
		),
		'MembershipsUser' => array(
			'className' => 'MembershipsUser',
			'foreignKey' => 'user_id', 
			'dependent' => true,
		),
		'VisitedAds' => array(
			'className' => 'VisitedAd',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'RentedRefs' => array(
			'className' => 'User',
			'foreignKey' => 'rented_upline_id',
			'dependent' => false,
		),
		'Deposits' => array(
			'className' => 'Deposit',
			'foreignKey' => 'user_id',
			'dependent' => false,
		),
		'DepositBonuses' => array(
			'className' => 'DepositBonus',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'Cashouts' => array(
			'className' => 'Cashout',
			'foreignKey' => 'user_id',
			'dependent' => false,
		),
		'Commissions' => array(
			'className' => 'Commission',
			'foreignKey' => 'upline_id',
			'dependent' => false,
		),
		'RequestObject' => array(
			'className' => 'RequestObject',
			'foreignKey' => 'foreign_key',
			'depenedent' => true,
		),
		'BoughtItems' => array(
			'className' => 'BoughtItem',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'PendingApplication' => array(
			'className' => 'PaidOffersApplication',
			'foreignKey' => 'user_id',
			'conditions' => array('PendingApplication.status' => PaidOffersApplication::PENDING),
			'dependent' => true,
		),
		'AcceptedApplication' => array(
			'className' => 'PaidOffersApplication',
			'foreignKey' => 'user_id',
			'conditions' => array('AcceptedApplication.status' => PaidOffersApplication::ACCEPTED),
			'dependent' => true,
		),
		'RejectedApplication' => array(
			'className' => 'PaidOffersApplication',
			'foreignKey' => 'user_id',
			'conditions' => array('RejectedApplication.status' => PaidOffersApplication::REJECTED),
			'dependent' => true,
		),
		'IgnoredOffer' => array(
			'className' => 'IgnoredOffer',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'Ads' => array(
			'className' => 'Ad',
			'foreignKey' => 'advertiser_id',
			'dependent' => true,
		),
		'BannerAds' => array(
			'className' => 'BannerAd',
			'foreignKey' => 'advertiser_id',
			'dependent' => true,
		),
		'FeaturedAds' => array(
			'className' => 'FeaturedAd',
			'foreignKey' => 'advertiser_id',
			'dependent' => true,
		),
		'LoginAds' => array(
			'className' => 'LoginAd',
			'foreignKey' => 'advertiser_id',
			'dependent' => true,
		),
		'PaidOffers' => array(
			'className' => 'PaidOffer',
			'foreignKey' => 'advertiser_id',
			'dependent' => true,
		),
		'ExpressAdsVisitedAds' => array(
			'className' => 'ExpressAdsVisitedAd',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'ExplorerAdsVisitedAds' => array(
			'className' => 'ExplorerAdsVisitedAd',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
/* forum */
		'ForumSubscription' => array(
			'className' => 'Forum.Subscription',
			'dependent' => true,
		),
		'ForumModerator' => array(
			'className' => 'Forum.Moderator',
			'dependent' => true,
		),
		'ForumPollVote' => array(
			'className' => 'Forum.PollVote',
		),
		'ForumPost' => array(
			'className' => 'Forum.Post',
		),
		'ForumPostRating' => array(
			'className' => 'Forum.PostRating',
		),
		'ForumTopic' => array(
			'className' => 'Forum.Topic',
		),
	);

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'username' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Username cannot be blank',
			),
			'length' => array(
				'rule' => array('between', 3, 50),
				'message' => 'Username cannot be longer than 50 and shorter than 3 characters',
			),
			'alphaNum' => array(
				'rule' => 'alphaNumeric',
				'message' => 'Username must only contain letters and numbers',
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'This username is already taken',
			),
			'lock' => array(
				'rule' => array('checkUsernameLock'),
				'message' => 'This username is blocked',
				'on' => 'create',
			),
		),
		'password' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Password cannot be blank',
			),
			'length' => array(
				'rule' => array('minLength', 6),
				'message' => 'Password have to be longer than 6 characters',
			),
			'notEqual' => array(
				'rule' => array('notEqualToField', 'username'),
				'message' => 'You cannot use your username as password',
			),
		),
		'confirm_password' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Please retype your password',
				'allowEmpty' => 'false',
				'required' => true,
				'on' => 'create',
			),
			'equal' => array(
				'rule' => array('equalToField', 'password'),
				'message' => 'Passwords do not match',
			),
		),
		'role' => array(
			'valid' => array(
				'rule' => array('inList', array('Un-verified', 'Active', 'Suspended')),
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please enter a valid e-mail address',
			),
			'unique' => array(
				'rule' => array('isUnique'),
				'message' => 'This e-mail is in use',
			),
			'lock' => array(
				'rule' => array('checkEmailLock'),
				'message' => 'This email is blocked.',
			),
		),
		'first_name' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter your first name',
			),
		),
		'last_name' => array(
			'notEmpty' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter your last name',
			),
		),
		'acceptTos' => array(
			'accept'=> array(
				'rule' => array('comparison', '!=', 0),
				'message' => 'Please read and accept Terms Of Service',
				'required' => 'true',
				'on' => 'create',
			),
			'notEmpty' => array(
				'rule' => array('notBlank'),
			),
		),
		'signup_ip' => array(
			'ip' => array(
				'rule' => 'ip',
				'message' => 'Please supply a valid IP address',
				'allowEmpty' => true,
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'This IP is already in use',
			),
		),
		'last_ip' => array(
			'ip' => array(
				'rule' => 'ip',
				'message' => 'Please supply a valid IP address',
				'allowEmpty' => true,
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'This IP is already in use',
			),
		),
		'account_balance' => array(
			'decimal' => array(
				'rule' => array('checkMonetary', true),
				'message' => 'Account balance should be a decimal value',
			),
		),
		'purchase_balance' => array(
			'decimal' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Purchase balance should be a decimal value',
			),
			'nonNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Purchase balance cannot be a negative number',
			),
		),
		'upline_commission' => array(
			'decimal' => array(
				'rule' => array('checkMonetary'),
				'message' => 'Upline commission should be a decimal value',
			),
			'nonNegative' => array(
				'rule' => array('comparison', '>=', 0),
				'message' => 'Upline commission cannot be a negative number',
			),
		),
		'comes_from' => array(
			'length' => array(
				'rule' => array('between', 1, 128),
				'allowEmpty' => true,
				'on' => 'create',
			),
		),
		'avatar' => array(
			'length' => array(
				'rule' => array('between', 1, 255),
				'message' => 'Avatar URL cannot be longer than 255 characters',
				'allowEmpty' => true,
			),
			'url' => array(
				'rule' => array('url', true),
				'message' => 'Avatar should be a valid HTTP URL (with "http://")',
			)
		),
		'signature' => array(
			'length' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Signature cannot be longer than 255 characters',
				'allowEmpty' => true,
			),
		),
		'autopay_enable' => array(
			'boolean' => array(
				'rule' => 'boolean',
				'message' => 'AutoPay enable should be a boolean value',
				'allowEmpty' => false,
			),
		),
		'autopay_done' => array(
			'boolean' => array(
				'rule' => 'boolean',
				'message' => 'AutoPay done should be a boolean value',
				'allowEmpty' => false,
			),
		),
		'auto_renew_days' => array(
			'smallint' => array(
				'rule' => array('range', -1, 65536),
				'message' => 'AutoRenew days should be between 0 and 65535',
				'allowEmpty' => false,
			),
		),
		'auto_renew_extend' => array(
			'smallint' => array(
				'rule' => array('range', -1, 65536),
				'message' => 'AutoRenew extend should be between 0 and 65535',
				'allowEmpty' => false,
			),
		),
		'location' => array(
			'lock' => array(
				'rule' => array('checkCountryLock'),
				'message' => 'This country is blocked.',
			),
		),
		'first_click' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'message' => 'First click should be a valid datetime value',
				'allowEmpty' => true,
			),
		),
		'points' => array(
			'rule' => 'checkPoints',
			'message' => 'Points should be a valid decimal value.',
		),
	);

/**
 * constuctor
 *
 * @return void
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->_bindActiveMembership();

		if(Module::installed('BotSystem')) {
			$this->hasMany['RentedBots'] = array(
				'className' => 'BotSystem.BotSystemBot',
				'foreignKey' => 'rented_upline_id',
				'dependent' => false,
				'conditions' => '', // NOTE: without that we have 'undefined index' notice.
			);
		}

		if(Module::active('BotSystem')) {
			$this->virtualFields['rented_refs_count'] = $this->alias.'.rented_users_count + '.$this->alias.'.rented_bots_count';
		} else {
			$this->virtualFields['rented_refs_count'] = $this->alias.'.rented_users_count';
		}

		if(Module::installed('FacebookLogin')) {
			$this->hasOne['ExternalLogin'] = array(
				'className' => 'FacebookLogin.ExternalLogin',
				'foreignKey' => 'user_id',
				'dependent' => true,
				'conditions' => '', // NOTE: without that we have 'undefined index' notice.
			);
		}

		if(Module::installed('RevenueShare')) {
			$this->hasMany['RevenueSharePacket'] = array(
				'className' => 'RevenueShare.RevenueSharePacket',
				'foreignKey' => 'user_id',
				'dependent' => true,
				'conditions' => '', // NOTE: without that we have 'undefined index' notice.
			);
		}

		if(Module::installed('Offerwalls')) {
			$this->hasMany['OfferwallsOffer'] = array(
				'className' => 'Offerwalls.OfferwallsOffer',
				'foreignKey' => 'user_id',
				'dependent' => true,
				'conditions' => '', // NOTE: without that we have 'undefined index' notice.
			);
		}

		if(Module::installed('AdGrid')) {
			$this->hasMany['AdGridAds'] = array(
				'className' => 'AdGrid.AdGridAd',
				'foreignKey' => 'advertiser_id',
				'dependent' => true,
				'conditions' => '', // NOTE: without that we have 'undefined index' notice.
			);
		}
	}

/**
 * beforeSave callback
 *
 * @return boolean
 */
	public function beforeSave($options = array()) {
		if(isset($this->data[$this->alias]['password'])) {
			$passwordHasher = new BlowfishPasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash($this->data[$this->alias]['password']);
		}

		if(!$this->id && !isset($this->data[$this->alias][$this->primaryKey])) {
			$this->data[$this->alias]['evercookie'] = Security::hash($this->data[$this->alias]['username'].$this->data[$this->alias]['created'], 'md5', true);
		}

		return true;
	}

/**
 * afterSave callback
 *
 * @return void
 */
	public function afterSave($created, $options = array()) {
		if(!$created) {
			App::uses('CakeSession', 'Model/Datasource');
			if(CakeSession::read('Auth.User') != null && isset($this->data[$this->alias]['id'])) {
				if(CakeSession::read('Auth.User.id') === $this->data[$this->alias]['id']) {
					$this->contain();
					$this->read();
					CakeSession::write('Auth.User', $this->data[$this->alias]);
				}
			}
		} else {
			if(!$this->createAssociated($this->data[$this->alias]['id'])) {
				throw new InternalErrorException(__d('exception', 'Failed to create user associated records'));
			}
			if(!$this->getSignUpBonus($this->data[$this->alias]['id'])) {
				throw new InternalErrorException(__d('exception', 'Failed to get signup bonus'));
			}
		}
	}

/**
 * afterDelete callback
 *
 * @return void
 */
	public function afterDelete() {
		$upline_id = $this->id;

		$this->updateAll(array(
			$this->alias.'.upline_id' => null,
			$this->alias.'.upline_commission' => 0,
			$this->alias.'.dref_since' => null,
			'UserStatistic.earned_as_dref' => 0,
			'UserStatistic.clicks_as_dref' => 0,
			'UserStatistic.clicks_as_dref_credited' => 0,
		), array(
			$this->alias.'.upline_id' => $upline_id,
		));

		$this->updateAll(array(
			$this->alias.'.rented_upline_id' => null,
			$this->alias.'.rent_starts' => null,
			$this->alias.'.rent_ends' => null,
			$this->alias.'.auto_renew_attempts' => 0,
			'UserStatistic.earned_as_rref' => 0,
			'UserStatistic.clicks_as_rref' => 0,
			'UserStatistic.clicks_as_rref_credited' => 0,
		), array(
			$this->alias.'.rented_upline_id' => $upline_id,
		));

		if(Module::installed('BotSystem')) {
			$this->RentedBots->unhookByUplineId($upline_id);
		}
	}

/**
 * checkUsernameLock
 *
 * @return boolean
 */
	public function checkUsernameLock($check) {
		$username = array_values($check)[0];

		return !ClassRegistry::init('UsernameLock')->isLocked($username);
	}

/**
 * checkEmailLock
 *
 * @return boolean
 */
	public function checkEmailLock($check) {
		$email = array_values($check)[0];

		return !ClassRegistry::init('EmailLock')->isLocked($email);
	}

/**
 * checkCountryLock
 *
 * @return boolean
 */
	public function checkCountryLock($check) {
		$location = array_values($check)[0];

		$exp = explode('/', $location);

		if(is_array($exp)) {
			$country = $exp[0];
		} else {
			$country = $location;
		}

		return !ClassRegistry::init('CountryLock')->isLocked($country);
	}

/**
 * activate method
 *
 * @return boolean
 */
	public function activate($id) { 
		$this->id = $id;
		return $this->saveField('role', 'Active');
	}

/**
 * suspend method
 *
 * @return boolean
 */
	public function suspend($id) {
		$this->id = $id;
		return $this->saveField('role', 'Suspended');
	}

/**
 * afterLogin method
 *
 * @return boolean
 */
	public function afterLogin($id, $location, $requestIp, $date = null) {
		$date = $date === null ? date('Y-m-d H:i:s') : $date;
		$this->id = $id;
		$this->set(array(
			'id'=> $id,
			'last_log_in' => $date,
			'last_ip' => $requestIp,
			'modified' => false,
			'location' => $location,
		));

		if($this->save()) {
			return true;
		}

		return false;
	}

/**
 * setRandomPassword method
 *
 * @return mixed(string/boolean)
 */
	public function setRandomPassword($id) {
		$newPassword = $this->createRandomStr(8);
		$this->id = $id;
		$this->set(array('id'=> $id, 'password' => $newPassword));

		if($this->save()) {
			return $newPassword;
		}

		return false;
	}

/**
 * createAssociated
 *
 * @return boolean
 */
	private function createAssociated($id) {
		if(!$this->UserMetadata->createWithVerify($id)) {
			throw new InternalErrorException(__d('exception', 'Failed to create User Metadata'));
		}

		if(!$this->UserProfile->createEmpty($id)) {
			throw new InternalErrorException(__d('exception', 'Failed to create User Profile'));
		}

		if(!$this->MembershipsUser->createDefault($id)) {
			throw new InternalErrorException(__d('exception', 'Failed to create Default Membership'));
		}

		if(!$this->UserStatistic->createDefault($id)) {
			throw new InternalErrorException(__d('exception', 'Failed to create User Statistics'));
		}

		$this->RequestObject->contain();
		$users_group = $this->RequestObject->find('first', array(
			'fields' => array('RequestObject.id'),
			'conditions' => array(
				'RequestObject.alias' => 'Users',
				'RequestObject.model' => null,
			),
		));

		$data = array(
			'parent_id' => $users_group['RequestObject']['id'],
			'model' => 'User',
			'foreign_key' => $id,
			'alias' => 'Users/'.$id,
		);

		$this->RequestObject->create();

		if(!$this->RequestObject->save(array('RequestObject' => $data))) {
			throw new InternalErrorException(__d('exception', 'Failed to add to default group: '.print_r($this->RequestObject->validationErrors, true)));
		}

		return true;
	}

/**
 * getRolesList
 *
 * @return array
 */
	public function getRolesList() {
		return array(
			'Un-verified' => __('Un-verified'),
			'Active' => __('Active'),
			'Suspended' => __('Suspended'),
		);
	}

/**
 * suspendInactive method
 *
 * @return void
 */
	public function suspendInactive($days) {
		$db = $this->getDataSource();
		$date = date('Y-m-d H:i:s', strtotime("-$days days"));

		return $this->updateAll(array(
			$this->alias.'.role' => $db->value('Suspended', 'string')
		), array(
			$this->alias.'.last_log_in <' => $date,
			$this->alias.'.last_log_in !=' => null,
		));
	}

/**
 * deleteInactive method
 *
 * @return void
 */
	public function deleteInactive($days) {
		$date = date('Y-m-d H:i:s', strtotime("-$days days"));

		$this->recursive = -1;
		return $this->deleteAll(
			array(
				'OR' => array(
					array(
						$this->alias.'.role' => 'Suspended',
						$this->alias.'.last_log_in <' => $date,
						$this->alias.'.last_log_in !=' => null,
					),
					array(
						$this->alias.'.role' => 'Un-verified',
						$this->alias.'.created <' => $date,
						$this->alias.'.last_log_in =' => null,
					),
				),
			), true, true
		);
	}

/**
 * deleteSuspended method
 *
 * @return boolean
 */
	public function deleteSuspended($days) {
		$date = date('Y-m-d H:i:s', strtotime("-$days days"));

		$this->recursive = -1;
		return $this->deleteAll(
			array(
				$this->alias.'.role' => 'Suspended',
				$this->alias.'.last_log_in <' => $date,
				$this->alias.'.last_log_in !=' => null,
			), true, true
		);
	}

/**
 * deleteUnverified
 *
 * @return boolean
 */
	public function deleteUnverified($days) {
		$date = date('Y-m-d H:i:s', strtotime("-$days days"));

		$this->recursive = -1;
		return $this->deleteAll(
			array(
				$this->alias.'.role' => 'Un-verified',
				$this->alias.'.created <' => $date,
			), true, true
		);
	}

/**
 * countAvailRRefs
 *
 * @return integer
 */
	public function countAvailRRefs($settings = null) {
		if(!is_array($settings)) {
			$settingsKeys = array(
				'rentMinClickDays',
				'rentFilter',
			);
			$settings = ClassRegistry::init('Settings')->fetch($settingsKeys);
		}

		switch($settings['Settings']['rentFilter']) {
			case 'clickDays':
				$res = $this->countNotRentedActiveClicked($settings['Settings']['rentMinClickDays'], ClassRegistry::init('Settings')->magicStatsNumber($settings['Settings']['rentMinClickDays']));
			break;

			case 'onlyActive':
				$res = $this->countNotRentedActive();
			break;

			case 'all':
				$res = $this->countNotRented();
			break;
			
			default:
				$res = 0;
			break;
		}

		return $res;
	}

/**
 * countNotRentedUsers method
 *
 * @return integer
 */
	public function countNotRentedUsers($upline_id = null) {
		$this->contain(array());
		return $this->find('count', array(
			'conditions' => array(
				$this->alias.'.rented_upline_id' => null,
				$this->alias.'.id !=' => $upline_id, 
			),
		));
	}

/**
 * countNotRentedActiveUsers method
 *
 * @return integer
 */
	public function countNotRentedActiveUsers($upline_id = null) {
		$this->contain(array());
		return $this->find('count', array(
			'conditions' => array(
				$this->alias.'.rented_upline_id' => null,
				$this->alias.'.role' => 'Active',
				$this->alias.'.id !=' => $upline_id,
			),
		));
	}

/**
 * countNotRentedActiveClickedUsers method
 *
 * @return integer
 */
	public function countNotRentedActiveClickedUsers($days, $magicAgo, $upline_id = null) {
		$fields = array();

		for($i = 0; $i < $days; $i++) {
			$m = ($magicAgo + $i) % 7;
			$fields[] = 'user_clicks_'.$m;
		}

		$this->contain(array('UserStatistic' => $fields));

		$conditions = array();
		foreach($fields as $v) {
			$conditions['UserStatistic.'.$v.' >'] = 0;
		}

		$res = $this->find('count', array(
			'conditions' => array(
				$this->alias.'.rented_upline_id' => null,
				$this->alias.'.role' => 'Active',
				$this->alias.'.id !=' => $upline_id,
				$conditions,
			)
		));

		return $res;
	}

/**
 * countAvailDRefs
 *
 * @return integer
 */
	public function countAvailDRefs($settings = null) {
		if(!is_array($settings)) {
			$settingsKeys = array(
				'directFilter',
				'directMinClickDays',
			);
			$settings = ClassRegistry::init('Settings')->fetch($settingsKeys);
		}

		switch($settings['Settings']['directFilter']) {
			case 'clickDays':
				$res = $this->countNotReferredActiveClickedUsers($settings['Settings']['directMinClickDays'], ClassRegistry::init('Settings')->magicStatsNumber($settings['Settings']['directMinClickDays']));
			break;

			case 'onlyActive':
				$res = $this->countNotReferredActiveUsers();
			break;

			case 'all':
				$res = $this->countNotReferredUsers();
			break;

			default:
				$res = 0;
			break;
		}

		return $res;
	}

/**
 * countNotReferredUsers method
 *
 * @return integer
 */
	public function countNotReferredUsers() {
		$this->contain();
		return $this->find('count', array(
			'conditions' => array(
				$this->alias.'.upline_id' => null,
			),
		));
	}

/**
 * countNotReferredActiveUsers method
 *
 * @return integer
 */
	public function countNotReferredActiveUsers() {
		$this->contain();
		return $this->find('count', array(
			'conditions' => array(
				$this->alias.'.upline_id' => null,
				$this->alias.'.role' => 'Active',
			),
		));
	}

/**
 * countNotReferredActiveClickedUsers method
 *
 * @return integer
 */
	public function countNotReferredActiveClickedUsers($days, $magicAgo) {
		$fields = array();

		for($i = 0; $i < $days; $i++) {
			$m = ($magicAgo + $i) % 7;
			$fields[] = 'user_clicks_'.$m;
		}

		$this->contain(array('UserStatistic' => $fields));

		$conditions = array();
		foreach($fields as $v) {
			$conditions['UserStatistic.'.$v.' >'] = 0;
		}

		$res = $this->find('count', array(
			'conditions' => array(
				$this->alias.'.upline_id' => null,
				$this->alias.'.role' => 'Active',
				$conditions,
			)
		));

		return $res;
	}


/**
 * assignDirectRefs method
 *
 * @return boolean
 */
	public function assignDirectRefs($upline_id, $limit) {
		$this->contain(array());
		$refs = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.upline_id' => null,
				$this->alias.'.id !=' => $upline_id,
			),
			'order' => 'RAND()',
			'limit' => (int)$limit,
		));
		if(count($refs) == $limit) {
			if($this->updateAll(array(
					$this->alias.'.upline_id' => $upline_id,
					$this->alias.'.upline_commission' => 0,
					$this->alias.'.dref_since' => 'NOW()',
					'UserStatistic.earned_as_dref' => 0,
					'UserStatistic.clicks_as_dref' => 0,
					'UserStatistic.clicks_as_dref_credited' => 0,
				), array(
					$this->alias.'.id' => array_flip($refs),
				)
			)) {
				$this->updateCounterCache(array('upline_id' => $upline_id));
				return true;
			}
		}
		return false;
	}

/**
 * assignDirectActiveRefs method
 *
 * @return boolean
 */
	public function assignDirectActiveRefs($upline_id, $limit) {
		$this->contain(array());
		$refs = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.upline_id' => null,
				$this->alias.'.role' => 'Active',
				$this->alias.'.id !=' => $upline_id,
			),
			'order' => 'RAND()',
			'limit' => (int)$limit,
		));
		if(count($refs) == $limit) {
			if($this->updateAll(array(
					$this->alias.'.upline_id' => $upline_id,
					$this->alias.'.upline_commission' => 0,
					$this->alias.'.dref_since' => 'NOW()',
					'UserStatistic.earned_as_dref' => 0,
					'UserStatistic.clicks_as_dref' => 0,
					'UserStatistic.clicks_as_dref_credited' => 0,
				), array(
					$this->alias.'.id' => array_flip($refs),
				)
			)) {
				$this->updateCounterCache(array('upline_id' => $upline_id));
				return true;
			}
		}
		return false;
	}

/**
 * assignDirectActiveClickedRefs method
 *
 * @return boolean
 */
	public function assignDirectActiveClickedRefs($upline_id, $limit, $clickDays) {
		$fields = array();

		$magicAgo = ClassRegistry::init('Settings')->magicStatsNumber($clickDays);

		for($i = 0; $i < $clickDays; $i++) {
			$m = ($magicAgo + $i) % 7;
			$fields[] = 'user_clicks_'.$m;
		}

		$this->contain(array('UserStatistic' => $fields));

		$conditions = array();
		foreach($fields as $v) {
			$conditions['UserStatistic.'.$v.' >'] = 0;
		}

		$refs = $this->find('all', array(
			'fields' => array($this->alias.'.id'),
			'conditions' => array(
				$this->alias.'.upline_id' => null,
				$this->alias.'.role' => 'Active',
				$this->alias.'.id !=' => $upline_id,
				$conditions,
			),
			'limit' => $limit,
		));
		$refs = Hash::extract($refs, '{n}.'.$this->alias.'.id');

		if(count($refs) == $limit) {
			if($this->updateAll(array(
					$this->alias.'.upline_id' => $upline_id,
					$this->alias.'.upline_commission' => 0,
					$this->alias.'.dref_since' => 'NOW()',
					'UserStatistic.earned_as_dref' => 0,
					'UserStatistic.clicks_as_dref' => 0,
					'UserStatistic.clicks_as_dref_credited' => 0,
				), array(
					$this->alias.'.id' => $refs,
				)
			)) {
				$this->updateCounterCache(array('upline_id' => $upline_id));
				return true;
			}
		}
		return false;
	}

/**
 * assignRentedRefsUsers method
 *
 * @return boolean
 */
	public function assignRentedRefsUsers($upline_id, $limit, $days) {
		$date = date('Y-m-d H:i:s', strtotime("+$days days"));
		$this->contain(array());
		$refs = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.rented_upline_id' => null,
				$this->alias.'.id !=' => $upline_id,
			),
			'order' => 'RAND()',
			'limit' => (int)$limit,
		));
		if(count($refs) == $limit) {
			if($this->updateAll(array(
					$this->alias.'.rented_upline_id' => $upline_id,
					$this->alias.'.rent_ends' => "'$date'",
					$this->alias.'.rent_starts' => 'NOW()',
					$this->alias.'.auto_renew_attempts' => 0,
					'UserStatistic.earned_as_rref' => 0,
					'UserStatistic.clicks_as_rref' => 0,
					'UserStatistic.clicks_as_rref_credited' => 0,
				), array(
					$this->alias.'.id' => array_flip($refs),
				)
			)) {
				$this->id = $upline_id;
				$this->set(array('last_rent_action' => date('Y-m-d H:i:s')));
				$this->save(null, array('fieldList' => array('last_rent_action')));
				$this->updateCounterCache(array('rented_upline_id' => $upline_id));
				return true;
			}
		}
		return false;
	}

/**
 * assignRentedActiveRefsUsers method
 *
 * @return boolean
 */
	public function assignRentedActiveRefsUsers($upline_id, $limit, $days) {
		$date = date('Y-m-d H:i:s', strtotime("+$days days"));
		$this->contain(array());
		$refs = $this->find('list', array(
			'conditions' => array(
				$this->alias.'.rented_upline_id' => null,
				$this->alias.'.role' => 'Active',
				$this->alias.'.id !=' => $upline_id,
			),
			'order' => 'RAND()',
			'limit' => (int)$limit,
		));
		if(count($refs) == $limit) {
			if($this->updateAll(array(
					$this->alias.'.rented_upline_id' => $upline_id,
					$this->alias.'.rent_ends' => "'$date'",
					$this->alias.'.rent_starts' => 'NOW()',
					$this->alias.'.auto_renew_attempts' => 0,
					'UserStatistic.earned_as_rref' => 0,
					'UserStatistic.clicks_as_rref' => 0,
					'UserStatistic.clicks_as_rref_credited' => 0,
				), array(
					$this->alias.'.id' => array_flip($refs),
				)
			)) {
				$this->id = $upline_id;
				$this->set(array('last_rent_action' => date('Y-m-d H:i:s')));
				$this->save(null, array('fieldList' => array('last_rent_action')));
				$this->updateCounterCache(array('rented_upline_id' => $upline_id));
				return true;
			}
		}
		return false;
	}

/**
 * assignRentedActiveClickedRefsUsers method
 *
 * @return boolean
 */
	public function assignRentedActiveClickedRefsUsers($upline_id, $limit, $clickDays, $days) {
		$rentDate = date('Y-m-d H:i:s', strtotime("+$days days"));
		$fields = array();

		$magicAgo = ClassRegistry::init('Settings')->magicStatsNumber($clickDays);

		for($i = 0; $i < $clickDays; $i++) {
			$m = ($magicAgo + $i) % 7;
			$fields[] = 'user_clicks_'.$m;
		}

		$this->contain(array('UserStatistic' => $fields));

		$conditions = array();
		foreach($fields as $v) {
			$conditions['UserStatistic.'.$v.' >'] = 0;
		}

		$refs = $this->find('all', array(
			'fields' => $this->alias.'.id',
			'conditions' => array(
				$this->alias.'.rented_upline_id' => null,
				$this->alias.'.role' => 'Active',
				$this->alias.'.id !=' => $upline_id,
				$conditions,
			),
			'limit' => $limit,
		));

		$refs = Hash::extract($refs, '{n}.'.$this->alias.'.id');

		if(count($refs) == $limit) {
			if($this->updateAll(array(
					$this->alias.'.rented_upline_id' => $upline_id,
					$this->alias.'.rent_ends' => "'$rentDate'",
					$this->alias.'.rent_starts' => 'NOW()',
					$this->alias.'.auto_renew_attempts' => 0,
					'UserStatistic.earned_as_rref' => 0,
					'UserStatistic.clicks_as_rref' => 0,
					'UserStatistic.clicks_as_rref_credited' => 0,
				), array(
					$this->alias.'.id' => $refs,
				)
			)) {
				$this->id = $upline_id;
				$this->set(array('last_rent_action' => date('Y-m-d H:i:s')));
				$this->save(null, array('fieldList' => array('last_rent_action')));
				$this->updateCounterCache(array('rented_upline_id' => $upline_id));
				return true;
			}
		}
		return false;
	}

/**
 * unhookDirectReferrals method
 *
 * @return boolean
 */
	public function unhookDirectReferrals($uplineId) {
		if($this->updateAll(array(
			$this->alias.'.upline_id' => null,
			$this->alias.'.upline_commission' => 0,
			$this->alias.'.dref_since' => null,
			'UserStatistic.earned_as_dref' => 0,
			'UserStatistic.clicks_as_dref' => 0,
			'UserStatistic.clicks_as_dref_credited' => 0,
		), array(
			$this->alias.'.upline_id' => $uplineId,
		))) {
			$this->updateCounterCache(array('upline_id' => $uplineId));
			return true;
		}
		return false;
	}

/**
 * unhookRentedReferrals method
 *
 * @return boolean
 */
	public function unhookRentedReferrals($uplineId) {
		if($this->updateAll(array(
			$this->alias.'.rented_upline_id' => null,
			$this->alias.'.rent_ends' => null,
			$this->alias.'.rent_starts' => null,
			$this->alias.'.auto_renew_attempts' => 0,
			'UserStatistic.earned_as_rref' => 0,
			'UserStatistic.clicks_as_rref' => 0,
			'UserStatistic.clicks_as_rref_credited' => 0,
		), array(
			$this->alias.'.rented_upline_id' => $uplineId,
		))) {
			$this->updateCounterCache(array('rented_upline_id' => $uplineId));

			if(Module::installed('BotSystem')) {
				return $this->RentedBots->unhookByUplineId($uplineId);
			}

			return true;
		}
		return false;
	}

/**
 * removeExpiredRentedReferrals
 *
 * @return boolean
 */
	public function removeExpiredRentedReferrals($date = null) {
		if($date === null) {
			$date = date('Y-m-d H:i:s');
		}

		$conditions = array(
			$this->alias.'.rent_ends <=' => $date,
		);

		$this->contain();
		$toUpdate = $this->find('all', array(
			'fields' => array('DISTINCT(rented_upline_id)'),
			'conditions' => $conditions,
		));

		$toUpdate = Hash::extract($toUpdate, '{n}.'.$this->alias.'.rented_upline_id');

		if(!$this->updateAll(array(
			$this->alias.'.rented_upline_id' => null,
			$this->alias.'.rent_ends' => null,
			$this->alias.'.rent_starts' => null,
			$this->alias.'.auto_renew_attempts' => 0,
			'UserStatistic.earned_as_rref' => 0,
			'UserStatistic.clicks_as_rref' => 0,
			'UserStatistic.clicks_as_rref_credited' => 0,
		), $conditions)) {
			return false;
		}

		foreach($toUpdate as $uplineId) {
			$this->updateCounterCache(array('rented_upline_id' => $uplineId));
		}

		if(Module::installed('BotSystem')) {
			$this->RentedBots->removeExpiredRentedReferrals($date);
		}

		return true;
	}


/**
 * rentReferrals
 *
 * @param string $amount
 * @param integer $user_id
 * @param integer $rrefs_no
 * @param array $settings
 * @return boolean
 */
	public function rentReferrals($amount, $user_id, $rrefs_no, $settings) {
		$this->id = $user_id;
		$this->contain(array(
			'ActiveMembership.Membership' => array(
				'name',
				'available_referrals_packs',
				'rented_referrals_limit',
				'time_between_renting',
				'points_enabled',
				'points_per_rref',
				'RentedReferralsPrice',
			),
		));
		$user = $this->findById($user_id);

		switch($settings['Settings']['rentFilter']) {
			case 'clickDays':
				$availableRefs = $this->countNotRentedActiveClicked($settings['Settings']['rentMinClickDays'], ClassRegistry::init('Settings')->magicStatsNumber($settings['Settings']['rentMinClickDays']), $user[$this->alias]['id']);
			break;

			case 'onlyActive':
				$availableRefs = $this->countNotRentedActive($user[$this->alias]['id']);
			break;

			case 'all':
				$availableRefs = $this->countNotRented($user[$this->alias]['id']);
			break;
		}

		if($rrefs_no <= $availableRefs) {
			if($user['ActiveMembership']['Membership']['rented_referrals_limit'] == -1 || $rrefs_no <= $user['ActiveMembership']['Membership']['rented_referrals_limit'] - $user[$this->alias]['rented_refs_count']) {
				if(!empty($user['ActiveMembership']['Membership']['available_referrals_packs'])) {
					$packs = explode(',', $user['ActiveMembership']['Membership']['available_referrals_packs']);

					if(in_array($rrefs_no, $packs)) {
						$range = $this->MembershipsUser->Membership->RentedReferralsPrice->getRangeByRRefsNo($user['ActiveMembership']['Membership']['RentedReferralsPrice'], $rrefs_no);

						if($range !== null) {
							$price = bcmul($range['price'], $rrefs_no);

							if(bccomp($amount, $price) >= 0) {

								if($settings['Settings']['rentingOption'] == 'botsOnly') {
									ClassRegistry::init('BotSystem.BotSystemStatistic')->addData(array('income' => $price));
								}

								if($user['ActiveMembership']['Membership']['points_enabled']) {
									$this->pointsAdd(bcmul($user['ActiveMembership']['Membership']['points_per_rref'], $rrefs_no), $user[$this->alias]['id']);
								}

								switch($settings['Settings']['rentFilter']) {
									case 'clickDays':
										return $this->assignRentedActiveClickedRefs($user[$this->alias]['id'], $rrefs_no, $settings['Settings']['rentMinClickDays'], $settings['Settings']['rentPeriod']);

									case 'onlyActive':
										return $this->assignRentedActiveRefs($user[$this->alias]['id'], $rrefs_no, $settings['Settings']['rentPeriod']);

									case 'all':
										return $this->assignRentedRefs($user[$this->alias]['id'], $rrefs_no, $settings['Settings']['rentPeriod']);
								}
							} else {
								$this->Notice->error(__('You do not have enough funds on Purchase Balance'));
							}
						} else {
							/* cheater? */
							throw new NotFoundException(__d('exception', 'Package is not in range'));
						}
					} else {
						/* cheater? */
						throw new NotFoundException(__d('exception', 'Package is not available'));
					}
				} else {
					/* cheater? */
					throw new InternalErrorException(__d('exception', 'Available referrals packs is empty'));
				}
			} else {
				/* cheater? */
				throw new InternalErrorException(__d('exception', 'Amount is too small'));
			}
		}
		return false;
	}

/**
 * buyReferrals
 *
 *
 * @param integer $user_id
 * @param integer $rrefs_no
 * @param array $settings
 * @return boolean
 */
	public function buyReferrals($amount, $user_id, $rrefs_no, $settings) {
		$this->id = $user_id;
		$this->contain(array(
			'ActiveMembership.Membership' => array(
				'direct_referrals_limit',
			),
		));
		$user = $this->findById($user_id);

		switch($settings['Settings']['directFilter']) {
			case 'all':
				$availableRefs = $this->countNotReferredUsers();
			break;

			case 'onlyActive':
				$availableRefs = $this->countNotReferredActiveUsers();
			break;

			case 'clickDays':
				$availableRefs = $this->countNotReferredActiveClickedUsers($settings['Settings']['directMinClickDays'], ClassRegistry::init('Settings')->magicStatsNumber($settings['Settings']['directMinClickDays']));
			break;

			default:
				$availableRefs = 0;
		}

		if($rrefs_no <= $availableRefs) {
			if($user['ActiveMembership']['Membership']['direct_referrals_limit'] == -1 || $rrefs_no <= $user['ActiveMembership']['Membership']['direct_referrals_limit'] - $user[$this->alias]['refs_count']) {

				switch($settings['Settings']['directFilter']) {
					case 'clickDays':
						return $this->assignDirectActiveClickedRefs($user[$this->alias]['id'], $rrefs_no, $settings['Settings']['directMinClickDays']);
					break;

					case 'onlyActive':
						return $this->assignDirectActiveRefs($user[$this->alias]['id'], $rrefs_no);
					break;

					case 'all':
						return $this->assignDirectRefs($user[$this->alias]['id'], $rrefs_no);
					break;
				}

			} else {
				/* cheater? */
				throw new InternalErrorException(__d('exception', 'Direct referrals limit overflow'));
			}
		}
		return false;
	}

/**
 * getRandomNotRentedRefs method
 *
 * @return array
 */
	public function getRandomNotRentedRefs($not_in = array(), $limit = null) {
		$this->contain();
		$refs = $this->find('all', array(
			'fields' => array(
				$this->alias.'.id',
			),
			'conditions' => array(
				$this->alias.'.rented_upline_id' => null,
				$this->alias.'.id NOT IN' => $not_in,
			),
			'order' => 'RAND()',
			'limit' => $limit,
		));

		return Hash::extract($refs, '{n}.'.$this->alias.'.id');
	}

/**
 * getRandomNotRentedActiveRefs method
 *
 * @return array
 */
	public function getRandomNotRentedActiveRefs($not_in = array(), $limit = null) {
		$this->contain();
		$refs = $this->find('all', array(
			'fields' => array(
				$this->alias.'.id',
			),
			'conditions' => array(
				$this->alias.'.rented_upline_id' => null,
				$this->alias.'.role' => 'Active',
				$this->alias.'.id !=' => $not_in,
			),
			'order' => 'RAND()',
			'limit' => $limit,
		));

		return Hash::extract($refs, '{n}.'.$this->alias.'.id');
	}

/**
 * getRandomNotRentedActiveClickedRefs method
 *
 * @return array
 */
	public function getRandomNotRentedActiveClickedRefs($not_in = array(), $clickDays = null, $limit = null) {
		$fields = array();

		$settingsModel = ClassRegistry::init('Settings');

		if($clickDays === null) {
			$clickDays = $settingsModel->fetchOne('rentMinClickDays');
		}

		$magicAgo = $settingsModel->magicStatsNumber($clickDays);

		for($i = 0; $i < $clickDays; $i++) {
			$m = ($magicAgo + $i) % 7;
			$fields[] = 'user_clicks_'.$m;
		}

		$conditions = array();
		foreach($fields as $v) {
			$conditions['UserStatistic.'.$v.' >'] = 0;
		}

		$this->contain(array('UserStatistic' => $fields));
		$refs = $this->find('all', array(
			'fields' => array($this->alias.'.id'),
			'conditions' => array(
				$this->alias.'.rented_upline_id' => null,
				$this->alias.'.role' => 'Active',
				$this->alias.'.id !=' => $not_in,
				$conditions,
			),
			'limit' => $limit,
			'order' => 'RAND()',
		));

		return Hash::extract($refs, '{n}.'.$this->alias.'.id');
	}

/**
 * recycleReferralsUsers
 *
 * @return boolean
 */
	public function recycleReferralsUsers($upline_id, $rrefs, $rentFilter, $click_days = null, $rrefs_no = null) {
		if($rrefs_no === null) {
			$rrefs_no = count($rrefs);
		}

		$skip = $unhook = Hash::extract($rrefs, '{n}.'.$this->alias.'.id');
		$skip[] = $upline_id;

		switch($rentFilter) {
			case 'clickDays':
				$new = $this->getRandomNotRentedActiveClickedRefs($skip, $click_days, $rrefs_no);
			break;

			case 'onlyActive':
				$new = $this->getRandomNotRentedActiveRefs($skip, $rrefs_no);
			break;

			case 'all':
				$new = $this->getRandomNotRentedRefs($skip, $rrefs_no);
			break;

			default:
				throw new InternalErrorException(__d('exception', 'Invalid rent filter'));
		}

		if(count($new) != $rrefs_no) {
			return false;
		}

		if(!ClassRegistry::init('User')->removeRentedUplines($unhook)) {
			return false;
		}

		$now = date('Y-m-d H:i:s');
		$data = array();
		for($i = 0; $i < $rrefs_no; $i++) {
			$data[$i] = array(
				'id' => $new[$i],
				'rented_upline_id' => $upline_id,
				'rent_starts' => $now,
				'rent_ends' => $rrefs[$i][$this->alias]['rent_ends'],
			);
		}

		return $this->saveAll($data, array(
			'validate' => false,
			'atomic' => true,
			'counterCache' => true,
			'deep' => false,
		));
	}

/**
 * autoRecycleReferrals method
 *
 * @return void
 */
	public function autoRecycleReferrals() {
		$botSystemActive = Module::active('BotSystem');
		$settings = ClassRegistry::init('Settings')->fetch(array(
			'rentFilter',
			'rentMinClickDays',
		));

		$contain = array(
			'RentedRefs' => array(
				'id',
				'rent_starts',
				'rent_ends',
				'UserStatistic' => array('last_click_date'),
			),
			'ActiveMembership' => array('Membership' => array('id', 'autorecycle_time')),
		);

		if($botSystemActive) {
			$contain['RentedBots'] = array(
				'id',
				'rent_starts',
				'rent_ends',
				'last_click_as_rref',
				'today_done',
			);
			$conditions = array(
				$this->alias.'.rented_refs_count >' => 0,
			);
		} else {
			$conditions = array(
				$this->alias.'.rented_users_count >' => 0,
			);
		}

		$this->contain($contain);
		$uplines = $this->find('all', array(
			'fields' => array(
				$this->alias.'.id',
				$this->alias.'.account_balance',
				$this->alias.'.purchase_balance',
				$this->alias.'.rented_users_count',
				'ActiveMembership.membership_id',
			),
			'conditions' => $conditions,
		));

		$now = new DateTime();

		foreach($uplines as $upline) {
			if($upline['ActiveMembership']['Membership']['autorecycle_time'] <= 0) {
				continue;
			}

			$toRecycle = array();

			foreach($upline['RentedRefs'] as $ref) {
				if($ref['UserStatistic']['last_click_date']) {
					$date = $ref['UserStatistic']['last_click_date'];
				} else {
					$date = $ref['rent_starts'];
				}
				$interval = $now->diff(new DateTime($date));

				if($interval->format('%a') >= $upline['ActiveMembership']['Membership']['autorecycle_time']) {
					$toRecycle[] = array('User' => $ref);
				}
			}

			if(!empty($toRecycle)) {
				if(!$this->recycleReferralsUsers($upline[$this->alias]['id'], $toRecycle, $settings['Settings']['rentFilter'], $settings['Settings']['rentMinClickDays'])) {
					throw new InternalErrorException(__d('exception', 'Failed to recycle %d\'s referrals', $upline[$this->alias]['id']));
				}
			}


			if($botSystemActive && !empty($upline['RentedBots'])) {
				$toRecycleBots = array();

				foreach($upline['RentedBots'] as $bot) {
					if($bot['last_click_as_rref']) {
						$date = $bot['last_click_as_rref'];
					} else {
						$date = $bot['rent_starts'];
					}
					$interval = $now->diff(new DateTime($date));

					if($interval->format('%a') >= $upline['ActiveMembership']['Membership']['autorecycle_time']) {
						$toRecycleBots[] = array('BotSystemBot' => $bot);
					}
				
				}

				if(!empty($toRecycleBots)) {
					if(!ClassRegistry::init('BotSystem.BotSystemBot')->recycleReferralsBots($upline[$this->alias]['id'], $toRecycleBots)) {
						throw new InternalErrorException(__d('exception', 'Failed to recycle %d\'s R-type referrals, probably we are too short on R-type referrals.', $upline[$this->alias]['id']));
					}
				}
			}

		}
	}

/**
 * changeDirectUpline method
 *
 * @return boolean
 */
	public function changeDirectUpline($userId, $newUplineId, $oldUplineId = null) {
		if($userId !== null) {
			$this->id = $userId;
		}
		$data = array(
			'id' => $userId,
			'upline_id' => $newUplineId,
			'upline_commission' => 0,
			'dref_since' => $newUplineId === null ? null : date('Y-m-d H:i:s'),
		);
		if($this->save($data, true, array('upline_id', 'upline_commission', 'dref_since'))) {
			return $this->UserStatistic->clearClicksAsDRef($userId);
		}
		return false;
	}

/**
 * changeRentedUpline method
 *
 * Doesn't change rent period end field and autorenew attempts counter field, those should be set manually
 *
 * @return boolean
 */
	public function changeRentedUpline($userId, $newUplineId, $oldUplineId = null) {
		if($userId !== null) {
			$this->id = $userId;
		}
		$data = array(
			'id' => $userId,
			'rented_upline_id' => $newUplineId,
			'rent_starts' => $newUplineId === null ? null : date('Y-m-d H:i:s'),
		);
		if($this->save($data, true, array('rented_upline_id', 'rent_starts'))) {
			return $this->UserStatistic->clearClicksAsRRef($userId);
		}
		return false;
	}

/**
 * removeDirectUpline method
 *
 * @return boolean
 */
	public function removeDirectUpline($userId = null) {
		if($userId !== null) {
			$this->id = $userId;
		}
		return $this->changeDirectUpline($userId, null);
	}

/**
 * removeDirectUplines method
 *
 * @return boolean
 */
	public function removeDirectUplines($uplineId, $usersIds) {
		if($usersIds === null) {
			throw InternalErrorException(__d('exception', 'null argument'));
		}
		if($this->updateAll(array(
			$this->alias.'.upline_id' => null,
			$this->alias.'.upline_commission' => 0,
			$this->alias.'.dref_since' => null,
			'UserStatistic.earned_as_dref' => 0,
			'UserStatistic.clicks_as_dref' => 0,
			'UserStatistic.clicks_as_dref_credited' => 0,
		), array(
			$this->alias.'.id' => $usersIds,
		))) {
			$this->updateCounterCache(array('upline_id' => $uplineId));
			return true;
		}
	}

/**
 * removeRentedUpline method
 *
 * @return boolean
 */
	public function removeRentedUpline($userId = null) {
		if($userId !== null) {
			$this->id = $userId;
		}
		$this->contain(array('UserStatistic'));
		$this->set(array(
			$this->alias.'.rented_upline_id' => null,
			$this->alias.'.rent_ends' => null,
			$this->alias.'.rent_starts' => null,
			$this->alias.'.auto_renew_attempts' => 0,
			'UserStatistic.earned_as_rref' => 0,
			'UserStatistic.clicks_as_rref' => 0,
			'UserStatistic.clicks_as_rref_credited' => 0,
		));
		return $this->save();
	}

/**
 * removeRentedUplines method
 *
 * @return boolean
 */
	public function removeRentedUplines($usersIds, $uplineId = null) {
		if($usersIds === null || empty($usersIds)) {
			throw new InternalErrorException(__d('exception', 'null argument'));
		}
		if($this->updateAll(array(
			$this->alias.'.rented_upline_id' => null,
			$this->alias.'.rent_ends' => null,
			$this->alias.'.rent_starts' => null,
			$this->alias.'.auto_renew_attempts' => 0,
			'UserStatistic.earned_as_rref' => 0,
			'UserStatistic.clicks_as_rref' => 0,
			'UserStatistic.clicks_as_rref_credited' => 0,
		), array(
			$this->alias.'.id' => $usersIds,
		))) {
			if($uplineId !== null) {
				$this->updateCounterCache(array('rented_upline_id' => $uplineId));
			}
			return true;
		}
	}

/**
 * extendReferrals
 *
 * @return boolean
 */
	public function extendReferrals($uplineId, $rrefs, $days) {
		$this->RentedRefs->recursive = -1;
		return $this->RentedRefs->updateAll(array(
			$this->alias.'.rent_ends' => "DATE_ADD(`{$this->alias}`.`rent_ends`, INTERVAL $days DAY)",
		), array(
			$this->alias.'.id' => $rrefs,
			$this->alias.'.rented_upline_id' => $uplineId,
		));
	}

/**
 * _bindActiveMembership method
 *
 * @return void
 */
	protected function _bindActiveMembership() {
		if(isset($this->hasOne['ActiveMembership'])) {
			return;
		}

		$ds = $this->MembershipsUser->getDataSource();

		$q1 = $ds->buildStatement(array(
			'fields' => array('MAX(NewestMembershipsUser.begins)'),
			'table' => $this->MembershipsUser->tablePrefix.$this->MembershipsUser->table,
			'alias' => 'NewestMembershipsUser',
			'limit' => 1,
			'conditions' => array('NewestMembershipsUser.user_id = ActiveMembership.user_id'),
			'group' => 'NewestMembershipsUser.user_id',
		), $this->MembershipsUser);

		$q = $ds->buildStatement(array(
				'fields' => array('`MembershipsUser`.`id'),
				'table' => $this->MembershipsUser->tablePrefix.$this->MembershipsUser->table,
				'alias' => 'MembershipsUser',
				'limit' => 1,
				'conditions' => array("MembershipsUser.begins = ($q1)", 'MembershipsUser.user_id = ActiveMembership.user_id'),
				'order' => array('MembershipsUser.id DESC'),
		), $this->MembershipsUser);

		$subQuery = String::insert('`ActiveMembership`.`id` = (:q)', array('q' => $q));

		$this->bindModel(array(
			'hasOne' => array(
				'ActiveMembership' => array(
					'className' => 'MembershipsUser',
					'dependent' => false,
					'conditions' => array(
						$subQuery,
					),
				),
			),
		), false);
	}

	public function purchaseBalanceDeposit($amount, $user_id) {
		$res = $this->purchaseBalanceAdd($amount, $user_id);

		if(!$res) {
			return false;
		}

		$this->contain(array(
			'ActiveMembership' => array(
				'Membership' => array(
					'points_enabled',
					'points_per_deposit',
				),
			),
		));
		$user = $this->findById($user_id, array('id'));

		if($user['ActiveMembership']['Membership']['points_enabled']) {
			$total_points = bcadd(bcmul($amount, $user['ActiveMembership']['Membership']['points_per_deposit']), '0.005');
			return $this->pointsAdd($total_points, $user_id);
		}

		return $res;
	}

/**
 * purchaseBalanceAdd
 *
 * @return boolean
 */
	public function purchaseBalanceAdd($amount, $user_id = null) {
		if($user_id === null || !$this->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}

		$this->recursive = -1;
		return $this->updateAll(array($this->alias.'.purchase_balance' => "`{$this->alias}`.`purchase_balance` + '$amount'"), array($this->alias.'.id' => $user_id));
	}

/**
 * purchaseBalanceSub
 *
 * @return boolean
 */
	public function purchaseBalanceSub($amount, $user_id = null) {
		if($user_id === null || !$this->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}

		$this->recursive = -1;
		return $this->updateAll(array($this->alias.'.purchase_balance' => "`{$this->alias}`.`purchase_balance` - '$amount'"), array($this->alias.'.id' => $user_id));
	}

/**
 * accountBalanceAdd
 *
 * @return boolean
 */
	public function accountBalanceAdd($amount, $user_id = null) {
		if($user_id === null || !$this->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}

		$this->recursive = -1;
		return $this->updateAll(array($this->alias.'.account_balance' => "`{$this->alias}`.`account_balance` + '$amount'"), array($this->alias.'.id' => $user_id));
	}

/**
 * accountBalanceSub
 *
 * @return boolean
 */
	public function accountBalanceSub($amount, $user_id = null) {
		if($user_id === null || !$this->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}

		$this->recursive = -1;
		return $this->updateAll(array($this->alias.'.account_balance' => "`{$this->alias}`.`account_balance` - '$amount'"), array($this->alias.'.id' => $user_id));
	}

/**
 * pointsAdd
 *
 * @return boolean
 */
	public function pointsAdd($amount, $user_id = null) {
		if($user_id === null || !$this->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}

		$this->recursive = -1;
		return $this->updateAll(array($this->alias.'.points' => "`{$this->alias}`.`points` + '$amount'"), array($this->alias.'.id' => $user_id));
	}

/**
 * pointsSub
 *
 * @return boolean
 */
	public function pointsSub($amount, $user_id = null) {
		if($user_id === null || !$this->exists($user_id)) {
			throw new NotFoundException(__d('exception', 'Invalid user'));
		}

		$this->recursive = -1;
		return $this->updateAll(array($this->alias.'.points' => "`{$this->alias}`.`points` - '$amount'"), array($this->alias.'.id' => $user_id));
	}

/**
 * getCreditedCommissionsAmount
 *
 * @return string
 */
	public function getCreditedCommissionsAmount($user_id = null) {
		if($user_id === null) {
			$user_id = $this->id;
		}

		$this->Commissions->recursive = -1;
		return $this->Commissions->find('all', array(
				'fields' => array(
					'COALESCE(SUM(amount), 0) as sum',
				),
				'conditions' => array(
					'upline_id' => $user_id,
					'status' => 'Credited',
				)
			)
		)[0][0]['sum'];
	}

/**
 * getWaitingCommissionsAmount
 *
 * @return string
 */
	public function getWaitingCommissionsAmount($user_id = null) {
		if($user_id === null) {
			$user_id = $this->id;
		}

		$this->Commissions->recursive = -1;
		return $this->Commissions->find('all', array(
				'fields' => array(
					'COALESCE(SUM(amount), 0) as sum',
				),
				'conditions' => array(
					'upline_id' => $user_id,
					'status' => 'Pending'
				)
			)
		)[0][0]['sum'];
	}

/**
 * getWaitingCashoutsAmount
 *
 * @return string
 */
	public function getWaitingCashoutsAmount($user_id = null) {
		if($user_id === null) {
			$user_id = $this->id;
		}

		$this->Cashouts->recursive = -1;
		return $this->Cashouts->find('all', array(
			'fields' => array(
				'COALESCE(SUM(Cashouts.amount), 0) as sum',
			),
			'conditions' => array(
				'user_id' => $user_id,
				'status' => array('New', 'Pending'),
			),
		))[0][0]['sum'];
	}

/**
 * getTotalPurchases
 *
 * @return string
 */
	public function getTotalPurchases($user_id = null) {
		if($user_id === null) {
			$user_id = $this->id;
		}

		$this->Deposits->recursive = -1;
		return $this->Deposits->find('all', array(
			'fields' => array(
				'COALESCE(SUM(Deposits.amount), 0) as sum',
			),
			'conditions' => array(
				'Deposits.user_id' => $user_id,
				'Deposits.status' => 'Success',
				'Deposits.item NOT LIKE' => 'deposit-%'
			),
		))[0][0]['sum'];
	}

/**
 * getTotalFunding
 *
 * @return string
 */
	public function getTotalFunding($user_id = null) {
		if($user_id === null) {
			$user_id = $this->id;
		}

		$this->Deposits->recursive = -1;
		return $this->Deposits->find('all', array(
			'fields' => array(
				'COALESCE(SUM(Deposits.amount), 0) as sum',
			),
			'conditions' => array(
				'Deposits.user_id' => $user_id,
				'Deposits.status' => 'Success',
				'Deposits.item LIKE' => 'deposit-%'
			),
		))[0][0]['sum'];
	}

/**
 * bindClicksAVG()
 *
 * @return void
 */
	public function bindClicksAVG($creditedOnly = false) {
		if($creditedOnly) {
			$this->virtualFields['clicks_avg_as_dref'] = 'ROUND(UserStatistic.clicks_as_dref_credited / (COALESCE(DATEDIFF(CURDATE(), User.dref_since), 0) + 1), 2)';
			$this->virtualFields['clicks_avg_as_rref'] = 'ROUND(UserStatistic.clicks_as_rref_credited / (COALESCE(DATEDIFF(CURDATE(), User.rent_starts), 0) + 1), 2)';
		} else {
			$this->virtualFields['clicks_avg_as_dref'] = 'ROUND(UserStatistic.clicks_as_dref / (COALESCE(DATEDIFF(CURDATE(), User.dref_since), 0) + 1), 2)';
			$this->virtualFields['clicks_avg_as_rref'] = 'ROUND(UserStatistic.clicks_as_rref / (COALESCE(DATEDIFF(CURDATE(), User.rent_starts), 0) + 1), 2)';
		}
	}

/**
 * unbindClicksAVG()
 *
 * @return void
 */
	public function unbindClicksAVG() {
		unset($this->virtualFields['clicks_avg_dref']);
		unset($this->virtualFields['clicks_avg_rref']);
	}

/**
 * getEarnings()
 *
 * @return string
 */
	public function getEarnings($data = null) {
		return $this->UserStatistic->getUserEarnings($data);
	}

/**
 * getLaziestDirectReferrals method
 *
 * @return array
 */
	public function getLaziestDirectReferrals($limit = null, $upline_id = null) {
		if($upline_id === null) {
			$upline_id = $this->id;
		}

		$this->contain('UserStatistic.total_clicks');

		$res = $this->find('list', array(
			'fields' => 'id',
			'conditions' => array(
				$this->alias.'.upline_id' => $upline_id,
			),
			'order' => 'UserStatistic.total_clicks',
			'limit' => $limit,
		));

		return array_keys($res);
	}

/**
 * getLaziestRentedReferrals method
 *
 * @return array
 */
	public function getLaziestRentedReferrals($limit = null, $upline_id = null) {
		if($upline_id === null) {
			$upline_id = $this->id;
		}

		$this->contain('UserStatistic.total_clicks');

		$res = $this->find('list', array(
			'fields' => 'id',
			'conditions' => array(
				$this->alias.'.rented_upline_id' => $upline_id,
			),
			'order' => 'UserStatistic.total_clicks',
			'limit' => $limit,
		));

		return array_keys($res);
	}

/**
 * removeReferralsOverflow method
 *
 * @return void
 */
	public function removeReferralsOverflow($uplineId) {
		$this->id = $uplineId;
		$this->contain(array(
			'ActiveMembership' => array(
				'Membership' => array(
					'direct_referrals_limit', 
					'rented_referrals_limit'
				)
			)
		));
		$this->read();

		if($this->data['ActiveMembership']['Membership']['direct_referrals_limit'] != -1) {
			$limit = $this->data[$this->alias]['refs_count'] - $this->data['ActiveMembership']['Membership']['direct_referrals_limit'];

			if($limit > 0) {
				$toRemove = $this->getLaziestDirectReferrals($limit);

				$this->removeDirectUplines($uplineId, $toRemove);
			}
		}

		if($this->data['ActiveMembership']['Membership']['rented_referrals_limit'] != -1) {
			$limit = $this->data[$this->alias]['rented_refs_count'] - $this->data['ActiveMembership']['Membership']['rented_referrals_limit'];

			if($limit > 0) {
				$toRemove = $this->getLaziestRentedReferrals($limit);

				$this->removeRentedUplines($toRemove, $uplineId);
			}

			if(Module::active('BotSystem')) {
				$this->RentedBots->removeReferralsOverflow($uplineId);
			}
		}
	}

/**
 * getSignUpBonus method
 *
 * @return boolean
 */
	public function getSignUpBonus($userId) {
		$settings = ClassRegistry::init('Settings')->fetchOne('signUpBonus');

		if(empty($settings) || !$settings['enable']) {
			return true;
		}

		if(empty($this->data)) {
			$this->contain();
			$user = $this->findById($userId);
		} else {
			$user = $this->data;
		}

		if($settings['start'] > $user[$this->alias]['created']) {
			return true;
		}

		if($settings['end'] < $user[$this->alias]['created']) {
			return true;
		}

		if($settings['type'] == 'money') {
			switch($settings['credit']) {
				case 'account':
					return $this->accountBalanceAdd($settings['amount'], $userId);

				case 'purchase':
					return $this->purchaseBalanceAdd($settings['amount'], $userId);
			}
		} elseif($settings['type'] == 'membership') {
			return $this->MembershipsUser->addNew($userId, $settings['membership'], date('Y-m-d H:i:s'), date('Y-m-d H:i:s', strtotime("+ {$settings['period']} day")));
		}

		return false;
	}

/**
 * getDepositBonus method
 *
 * @return boolean
 */
	public function getDepositBonus($depositData) {
		$settings = ClassRegistry::init('Settings')->fetchOne('depositBonus');

		if(empty($settings) || !$settings['enable']) {
			return true;
		}

		$itemData = explode('-', $depositData['item']);

		$this->contain(array(
			'ActiveMembership',
		));
		$user = $this->findById($depositData['user_id'], array('ActiveMembership.membership_id'));
		$membership = $user['ActiveMembership']['membership_id'];

		if(!isset($settings[$depositData['gateway']])) {
			return true;
		}

		$bonus = $settings[$depositData['gateway']][$membership]['amount'];
		$bonus = bcadd($bonus, bcmul($itemData[1], bcdiv($settings[$depositData['gateway']][$membership]['percent'], 100)));

		if(!$this->purchaseBalanceAdd($bonus, $depositData['user_id'])) {
			throw new InternalErrorException(__d('exception', 'Failed to assign deposit bonus'));
		}

		$this->DepositBonuses->create();
		$this->DepositBonuses->set(array(
			'amount' => $bonus,
			'user_id' => $depositData['user_id'],
			'deposit_id' => $depositData['id'],
			'status' => DepositBonus::CREDITED,
		));
		return $this->DepositBonuses->save();
	}

/**
 * autoRenew
 *
 * @return boolean
 */
	public function autoRenew() {
		$settings = ClassRegistry::init('Settings')->fetch(array('autoRenewTries', 'rentPeriod'));
		$contain = array(
			'RentedRefs' => array(
				'id',
				'rent_ends',
			),
			'ActiveMembership' => array('Membership' => array('id', 'RentedReferralsPrice')),
		);

		if($settings['Settings']['autoRenewTries'] != -1) {
			$contain[] = 'RentedRefs.auto_renew_attempts < '.$settings['Settings']['autoRenewTries'];
		}

		$this->recursive = -1;
		$this->contain($contain);
		$uplines = $this->find('all', array(
			'fields' => array(
				$this->alias.'.id',
				$this->alias.'.account_balance',
				$this->alias.'.purchase_balance',
				$this->alias.'.auto_renew_extend',
				$this->alias.'.auto_renew_days',
				$this->alias.'.rented_refs_count',
				'ActiveMembership.membership_id',
			),
			'conditions' => array(
				$this->alias.'.rented_refs_count >' => 0,
				$this->alias.'.auto_renew_days !=' => 0,
				$this->alias.'.auto_renew_extend !=' => 0,
			),
		));

		$RentExtensionPeriod = ClassRegistry::init('RentExtensionPeriod');
		$RentExtensionPeriod->recursive = -1;
		$extendPeriods = $RentExtensionPeriod->find('all');
		$extendPeriods = Hash::combine($extendPeriods, '{n}.RentExtensionPeriod.days', '{n}.RentExtensionPeriod');

		$AutorenewHistory = ClassRegistry::init('AutorenewHistory');

		foreach($uplines as $upline) {
			$purchase = $upline[$this->alias]['purchase_balance'];
			$account = $upline[$this->alias]['account_balance'];
			$range = $this->MembershipsUser->Membership->RentedReferralsPrice->getRangeByRRefsNo($upline['ActiveMembership']['Membership']['RentedReferralsPrice'], $upline[$this->alias]['rented_refs_count']);
			$extend = $extendPeriods[$upline[$this->alias]['auto_renew_extend']];
			$historyAmount = '0';

			$price = bcmul($range['price'], bcdiv($upline[$this->alias]['auto_renew_extend'], $settings['Settings']['rentPeriod']));
			$price = bcsub($price, bcmul($price, bcdiv($extend['discount'], 100)));

			$now = new DateTime();
			$toExtend = array();
			$failed = array();

			foreach($upline['RentedRefs'] as $ref) {
				$ends = new DateTime($ref['rent_ends']);
				$interval = $now->diff($ends);

				if($interval->format('%a') <= $upline[$this->alias]['auto_renew_days']) {
					if(bccomp($purchase, $price) >= 0) {
						$purchase = bcsub($purchase, $price);
						$toExtend[] = $ref['id'];
						$historyAmount = bcadd($historyAmount, $price);
					} elseif(bccomp($account, $price) >= 0) {
						$account = bcsub($account, $price);
						$toExtend[] = $ref['id'];
						$historyAmount = bcadd($historyAmount, $price);
					} else {
						$failed[] = $ref['id'];
					}
				}
			}

			$this->clear();
			$this->contain();
			$this->id = $upline[$this->alias]['id'];
			$this->set('purchase_balance', $purchase);
			$this->set('account_balance', $account);
			if(!$this->save()) {
				throw new InternalErrorException(__d('exception', 'Failed to save upline balances'));
			}

			if(!empty($toExtend)) {
				$this->recursive = -1;
				if(!$this->updateAll(array($this->alias.'.rent_ends' => "DATE_ADD(`{$this->alias}`.`rent_ends`, INTERVAL {$upline[$this->alias]['auto_renew_extend']} DAY)"), array($this->alias.'.id' => $toExtend))) {
					throw new InternalErrorException(__d('exception', 'Failed to extend referrals rent time'));
				}
			}

			if(!empty($failed)) {
				$this->recursive = -1;
				if(!$this->updateAll(array($this->alias.'.auto_renew_attempts' => "`{$this->alias}`.`auto_renew_attempts` + 1"), array($this->alias.'.id' => $failed))) {
					throw new InternalErrorException(__d('exception', 'Failed to save attempts number'));
				}
			}

			$AutorenewHistory->add($historyAmount, $upline[$this->alias]['id']);
		}

		if(Module::active('BotSystem')) {
			$this->RentedBots->autoRenew();
		}

		return true;
	}

/**
 * recycleReferrals method
 *
 * @return boolean
 */
	public function recycleReferrals($upline, $selected, $recycle_price) {
		$settingsKeys = array(
			'enableRentingReferrals',
			'rentMinClickDays',
			'rentFilter',
			'rentPeriod',
			'rentingOption',
		);
		$settings = ClassRegistry::init('Settings')->fetch($settingsKeys);

		if($settings['Settings']['rentingOption'] == 'botsOnly') {
			$ids = array();
			foreach($selected as $rid) {
				if($rid{0} != 'R') {
					return __('Sorry, we do not have enough referrals available. Please try again later.');
				}
				$ids[] = substr($rid, 1);
			}
			$this->RentedBots->recursive = -1;
			$rrefs = $this->RentedBots->find('all', array(
				'fields' => array('id', 'rent_ends', 'today_done'),
				'conditions' => array(
					'id' => $ids,
					'rented_upline_id' => $upline['User']['id'],
				),
			));
		} else {
			$this->RentedRefs->contain();
			$rrefs = $this->RentedRefs->find('all', array(
				'fields' => array('id', 'rent_ends'),
				'conditions' => array(
					'id' => $selected,
					'rented_upline_id' => $upline['User']['id'],
				),
			));
		}

		$rrefs_count = count($rrefs);
		if($rrefs_count != count($selected)) {
			/* cheater? */
			throw new InternalErrorException(__d('exception', 'Invalid rrefs ids'));
		}

		switch($settings['Settings']['rentFilter']) {
			case 'clickDays':
				$availableRefs = $this->countNotRentedActiveClicked($settings['Settings']['rentMinClickDays'], ClassRegistry::init('Settings')->magicStatsNumber($settings['Settings']['rentMinClickDays']), $upline['User']['id']);
			break;

			case 'onlyActive':
				$availableRefs = $this->countNotRentedActive($upline['User']['id']);
			break;

			case 'all':
				$availableRefs = $this->countNotRented($upline['User']['id']);
			break;

			default:
				$availableRefs = 0;
		}

		if($rrefs_count > $availableRefs) {
			return __('Sorry, we do not have enough referrals available. Please try again later.');
		}

		$price = bcmul($rrefs_count, $recycle_price);

		if(bccomp($upline['User']['purchase_balance'], $price) >= 0) {

			if($settings['Settings']['rentingOption'] == 'botsOnly') {
				$res = $this->RentedBots->recycleReferralsBots($upline['User']['id'], $rrefs, $rrefs_count);
				ClassRegistry::init('BotSystem.BotSystemStatistic')->addData(array('income' => $price));
			} else {
				$res = $this->RentedRefs->recycleReferralsUsers($upline['User']['id'], $rrefs, $settings['Settings']['rentFilter'], $settings['Settings']['rentMinClickDays'], $rrefs_count);
			}

			if($res) {
				return true;
			}
		} else {
			return __('Sorry, you do not have enough funds on Purchase Balance.');
		}
		return __('Failed to assign referrals. Please try again later.');
	}

/**
 * countNotRented method
 *
 * @return integer
 */
	public function countNotRented($upline_id = null) {
		if(Module::active('BotSystem') && Configure::read('rentingOption') == 'botsOnly') {
			return $this->RentedBots->countNotRentedBots();
		}
		return $this->countNotRentedUsers($upline_id);
	}

/**
 * countNotRentedActive method
 *
 * @return integer
 */
	public function countNotRentedActive($upline_id = null) {
		if(Module::active('BotSystem') && Configure::read('rentingOption') == 'botsOnly') {
			return $this->RentedBots->countNotRentedBots();
		}
		return $this->countNotRentedActiveUsers($upline_id);
	}

/**
 * countNotRentedActiveClicked method
 *
 * @return integer
 */
	public function countNotRentedActiveClicked($days, $magicAgo, $upline_id = null) {
		if(Module::active('BotSystem') && Configure::read('rentingOption') == 'botsOnly') {
			return $this->RentedBots->countNotRentedBots();
		}
		return $this->countNotRentedActiveClickedUsers($days, $magicAgo, $upline_id);
	}

/**
 * assignRentedRefs method
 *
 * @return boolean
 */
	public function assignRentedRefs($upline_id, $limit, $days) {
		if(Module::active('BotSystem') && Configure::read('rentingOption') == 'botsOnly') {
			return $this->RentedBots->assignRentedRefsBots($upline_id, $limit, $days);
		}
		return $this->assignRentedRefsUsers($upline_id, $limit, $days);
	}

/**
 * assignRentedActiveRefs method
 *
 * @return boolean
 */
	public function assignRentedActiveRefs($upline_id, $limit, $days) {
		if(Module::active('BotSystem') && Configure::read('rentingOption') == 'botsOnly') {
			return $this->RentedBots->assignRentedRefsBots($upline_id, $limit, $days);
		}
		return $this->assignRentedActiveRefsUsers($upline_id, $limit, $days);
	}

/**
 * assignRentedActiveClickedRefs method
 *
 * @return boolean
 */
	public function assignRentedActiveClickedRefs($upline_id, $limit, $clickDays, $days) {
		if(Module::active('BotSystem') && Configure::read('rentingOption') == 'botsOnly') {
			return $this->RentedBots->assignRentedRefsBots($upline_id, $limit, $days);
		}
		return $this->assignRentedActiveClickedRefsUsers($upline_id, $limit, $clickDays, $days) ;
	}

}
