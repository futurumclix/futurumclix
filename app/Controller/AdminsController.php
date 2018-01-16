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
App::uses('PaymentStatus', 'Payments');
App::uses('CakeTime', 'Utility');

/**
 * Admins Controller
 *
 * @property Admin $Admin
 * @property PaginatorComponent $Paginator
 */
class AdminsController extends AppController {
/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'Admin',
		'User',
		'Deposit',
		'Cashout',
	);

/**
 * Components
 *
 * @var array
 */
	public $components = array(
		'Paginator',
		'Payments',
		'GoogleAuthenticator.GoogleAuthenticator',
	);

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('admin_login', 'admin_verify', 'admin_google_authenticator'));
	}

/**
 * admin_google_authenticator method
 *
 * @return void
 */
	public function admin_google_authenticator() {
		$ga = $this->Session->read('GoogleAuthenticatorAdmin');
		if(!$ga) {
			throw new NotFoundException(__d('exception', 'Invalid data'));
		}
		if($this->request->is(array('post', 'put'))) {
			if($this->GoogleAuthenticator->check($this->request->data['ga_code'], $ga['secret'])) {
				$this->Session->write('GoogleAuthenticatorAdmin.pass', 1);
				return $this->redirect(array('action' => 'login'));
			} else {
				$this->Notice->error(__('Wrong Google Authenticator code'));
			}
		}
	}

/**
 * admin_login method
 *
 * @return void
 */
	public function admin_login() {
		if($this->Auth->loggedIn()) {
			$this->redirect($this->Auth->redirect());
		}
		$ga = $this->Session->read('GoogleAuthenticatorAdmin');

		if($this->request->is(array('post', 'put')) || isset($ga['pass']) && $ga['pass']) {
			if($this->Session->read('AdminLogin.block')) {
				if($this->Session->read('AdminLogin.block') <= time()) {
					$this->Session->delete('AdminLogin');
				} else {
					throw new InternalErrorException(__d('exception', 'Too many failed attempts'));
				}
			}

			if(!$ga || !$ga['pass']) {
				App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
				$passwordHasher = new BlowfishPasswordHasher();

				$admin = $this->Admin->find('first', array(
					'conditions' => array(
						'email' => $this->request->data['Admin']['email'],
					),
				));

				if($admin && !empty($admin) && $passwordHasher->check($this->request->data['Admin']['password'], $admin['Admin']['password'])) {
					if($admin['Admin']['secret'] == Admin::SECRET_GA && !empty($admin['Admin']['secret_data'])) {
						$this->Session->write('GoogleAuthenticatorAdmin', array(
							'secret' => $admin['Admin']['secret_data'],
							'pass' => 0,
							'email' => $this->request->data['Admin']['email'],
							'password' => $this->request->data['Admin']['password'],
						));
						return $this->redirect(array('action' => 'google_authenticator'));
					}
				}
			} else {
				$this->request->data['Admin']['email'] = $ga['email'];
				$this->request->data['Admin']['password'] = $ga['password'];
				$_SERVER['REQUEST_METHOD'] = 'POST';
			}

			if($this->Auth->login()) {
					$verifyToken = $this->Auth->user('verify_token');
					if(empty($verifyToken)) {
						$ips = $this->Auth->user('allowed_ips');
						if(empty($ips) || $this->request->clientIp() === $ips || in_array($this->request->clientIp(), explode(',', $ips))) {
							if($this->Admin->afterLogin($this->Auth->user('id'))) {
								$this->Session->delete('AdminLogin');
								return $this->redirect($this->Auth->redirect());
							} else {
								$this->Auth->logout();
								throw new InternalErrorException(__d('exception', 'Failed to update database'));
							}
						}
					}
				$this->Auth->logout();
			}
			$this->Notice->error(__d('admin', 'E-mail or password is incorrect'));


			$loginTries = $this->Session->read('AdminLogin.fail');

			if($loginTries) {
				$loginTries++;
			} else {
				$loginTries = 1;
			}

			$this->Session->write('AdminLogin.fail', $loginTries);

			if($loginTries >= 3) {
				$this->Session->write('AdminLogin.block', strtotime('+30 minutes'));
			}
		}
	}

/**
 * admin_logout method
 *
 * @return void
 */
	public function admin_logout() {
		$this->Session->delete('Evercookie.disable');
		$this->Session->delete('Auth.User');
		$this->Session->delete('GoogleAuthenticatorAdmin');
		$this->Notice->success(__d('admin', 'Successfully loged out'));
		$this->redirect($this->Auth->logout());
	}

/**
 * _admin_statistics_purchase_balance method
 *
 * @return void
 */
	private function _admin_statistics_purchase_balance() {
		$stats = $this->Deposit->query('
			SELECT SUM(IF(Deposit.status = \''.PaymentStatus::SUCCESS.'\', Deposit.amount, 0)) as deposits
			FROM `'.$this->Deposit->tablePrefix.$this->Deposit->table.'` as Deposit WHERE Deposit.gateway = \'AccountBalance\'
		')[0][0];

		$chartD = $this->Deposit->query('
			SELECT DATE(`date`) as de, SUM(amount) as amount FROM `'.$this->Deposit->tablePrefix.$this->Deposit->table.'` WHERE DATE(`date`) >= NOW() - INTERVAL 1 WEEK AND gateway = \'AccountBalance\' GROUP BY de ORDER BY de ASC
		');

		$chart = array(
			'Deposits' => Hash::combine($chartD, '{n}.0.de', '{n}.0.amount'),
		);

		for($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', strtotime("-$i days"));

			if(!isset($chart['Deposits'][$date])) {
				$chart['Deposits'][$date] = 0;
			}
		}

		ksort($chart['Deposits']);
		$chart['Deposits'] = array_values($chart['Deposits']);

		foreach($stats as $k => $v) {
			if($v === null) {
				$stats[$k] = 0;
			}
		}

		$data['total'] = $stats;
		$data['total']['paid'] = 0;
		$data['total']['new'] = 0;
		$data['total']['profit'] = 0;

		$data['chart'] = $chart;
		$data['chart']['Cashouts'] = array();

		return json_encode($data, JSON_NUMERIC_CHECK);
	}

/**
 * admin_statistics method
 *
 * @return void
 */
	public function admin_statistics($gatewayName = null) {
		$this->request->allowMethod('ajax');
		$this->layout = 'ajax';
		$this->autoRender = false;
		$activeGateways = $this->Payments->getActive();

		if($gatewayName === null || !in_array($gatewayName, $activeGateways)) {
			throw new NotFoundException(__d('exception', 'Invalid gateway'));
		}

		if($gatewayName == 'PurchaseBalance') {
			return $this->_admin_statistics_purchase_balance();
		}

		$totalD = $this->Deposit->query('
			SELECT SUM(IF(Deposit.status = \''.PaymentStatus::SUCCESS.'\', Deposit.amount, 0)) as deposits
			FROM `'.$this->Deposit->tablePrefix.$this->Deposit->table.'` as Deposit WHERE Deposit.gateway = \''.$gatewayName.'\'
		')[0][0];

		$totalC = $this->Cashout->query('
			SELECT SUM(IF(Cashout.status = \'Completed\', Cashout.amount, 0)) as paid, SUM(IF(Cashout.status = \'New\', Cashout.amount, 0)) as new
			FROM `'.$this->Cashout->tablePrefix.$this->Cashout->table.'` as Cashout WHERE Cashout.gateway = \''.$gatewayName.'\'
		')[0][0];

		$stats = array_merge($totalD, $totalC);

		$stats['profit'] = $stats['deposits'] - $stats['paid'];

		$chartD = $this->Deposit->query('
			SELECT DATE(`date`) as de, SUM(amount) as amount FROM `'.$this->Deposit->tablePrefix.$this->Deposit->table.'` WHERE status = \''.PaymentStatus::SUCCESS.'\' AND DATE(`date`) >= NOW() - INTERVAL 1 WEEK AND gateway = \''.$gatewayName.'\' GROUP BY de ORDER BY de ASC
		');

		$chartC = $this->Cashout->query('
			SELECT DATE(created) as cd, SUM(amount) as amount FROM `'.$this->Cashout->tablePrefix.$this->Cashout->table.'` WHERE status = \'Completed\' AND DATE(created) >= NOW() - INTERVAL 1 WEEK AND gateway = \''.$gatewayName.'\' GROUP BY cd ORDER BY cd ASC
		');

		$chart = array(
			'Deposits' => Hash::combine($chartD, '{n}.0.de', '{n}.0.amount'),
			'Cashouts' => Hash::combine($chartC, '{n}.0.cd', '{n}.0.amount'),
		);

		for($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', strtotime("-$i days"));

			if(!isset($chart['Deposits'][$date])) {
				$chart['Deposits'][$date] = 0;
			}

			if(!isset($chart['Cashouts'][$date])) {
				$chart['Cashouts'][$date] = 0;
			}
		}

		ksort($chart['Deposits']);
		$chart['Deposits'] = array_values($chart['Deposits']);

		ksort($chart['Cashouts']);
		$chart['Cashouts'] = array_values($chart['Cashouts']);

		foreach($stats as $k => $v) {
			if($v === null) {
				$stats[$k] = 0;
			}
		}

		$data['total'] = $stats;
		$data['chart'] = $chart;

		return json_encode($data, JSON_NUMERIC_CHECK);
	}

/**
 * _addToDoList method
 *
 * @return void
 */
	private function _addToDoList($text, $link = null, $type = 'info') {
		if($link) {
			if(is_array($link)) {
				$link = Router::url($link);
			}
			$text = '<a href="'.$link.'">'.$text.'</a>';
		}
		$this->Notice->$type($text);
	}

/**
 * admin_home
 *
 * @return void
 */
	public function admin_home() {
		$overallStats = $this->User->query('
		 SELECT COUNT(*) as total, COALESCE(SUM(IF(User.role = \'Active\', 1, 0)), 0) as active, COALESCE(SUM(IF(User.role = \'Un-verified\', 1, 0)), 0) as unverified,
		 COALESCE(SUM(IF(User.role = \'Suspended\', 1, 0)), 0) as suspended, SUM(User.account_balance) as account_balances, SUM(User.purchase_balance) as purchase_balances
		 FROM `'.$this->User->tablePrefix.$this->User->table.'` as User
		')[0][0];

		$overallStats['not_rented'] = $this->User->countNotRented();

		$totalD = $this->Deposit->query('
			SELECT SUM(IF(Deposit.status = \''.PaymentStatus::SUCCESS.'\', Deposit.amount, 0)) as deposits FROM `'.$this->Deposit->tablePrefix.$this->Deposit->table.'` as Deposit WHERE Deposit.gateway != \'PurchaseBalance\' AND Deposit.gateway != \'AccountBalance\'
		')[0][0];

		$totalC = $this->Cashout->query('
			SELECT SUM(IF(Cashout.status = \'Completed\', Cashout.amount, 0)) as paid, SUM(IF(Cashout.status = \'New\', Cashout.amount, 0)) as new
			FROM `'.$this->Cashout->tablePrefix.$this->Cashout->table.'` as Cashout
		')[0][0];

		$total = array_merge($totalD, $totalC);

		$total['profit'] = $total['deposits'] - $total['paid'];

		$chartD = $this->Deposit->query('
			SELECT DATE(`date`) as de, SUM(amount) as amount FROM `'.$this->Deposit->tablePrefix.$this->Deposit->table.'` WHERE status = \''.PaymentStatus::SUCCESS.'\' AND gateway != \'PurchaseBalance\' AND gateway != \'AccountBalance\' AND DATE(`date`) >= NOW() - INTERVAL 1 WEEK GROUP BY de ORDER BY de ASC
		');

		$chartC = $this->Cashout->query('
			SELECT DATE(created) as cd, SUM(amount) as amount FROM `'.$this->Cashout->tablePrefix.$this->Cashout->table.'` WHERE status = \'Completed\' AND DATE(created) >= NOW() - INTERVAL 1 WEEK GROUP BY cd ORDER BY cd ASC
		');

		$chart = array(
			'Deposits' => Hash::combine($chartD, '{n}.0.de', '{n}.0.amount'),
			'Cashouts' => Hash::combine($chartC, '{n}.0.cd', '{n}.0.amount'),
		);

		for($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', strtotime("-$i days"));

			if(!isset($chart['Deposits'][$date])) {
				$chart['Deposits'][$date] = 0;
			}

			if(!isset($chart['Cashouts'][$date])) {
				$chart['Cashouts'][$date] = 0;
			}
		}

		ksort($chart['Deposits']);
		ksort($chart['Cashouts']);


		/* admin's TO DO list */
		if(Configure::read('maintenanceMode')) {
			$this->_addToDoList(__d('admin', 'Site is currently in maintenance mode.'));
		}

		$newCashouts = $this->Cashout->find('count', array(
			'conditions' => array(
				'status' => 'New',
			),
		));

		if($newCashouts > 1) {
			$this->_addToDoList(__d('admin', '%d new cashouts are waiting for your attention.', $newCashouts), array('plugin' => false, 'controller' => 'cashouts', 'status' => 'New'));
		} elseif($newCashouts == 1) {
			$this->_addToDoList(__d('admin', 'New cashout is waiting for your attention.'), array('plugin' => false, 'controller' => 'cashouts', 'status' => 'New'));
		}

		$siteURL = Configure::read('siteURL');
		if(empty($siteURL) || Configure::read('App.fullBaseUrl') != $siteURL) {
			$this->_addToDoList(__d('admin', 'Invalid URL configuration, please check.'), array('plugin' => null, 'controller' => 'settings', 'action' => 'general'), 'error');
		}

		if(empty(Configure::read('siteName'))) {
			$this->_addToDoList(__d('admin', 'Site name is not configured. Please check.'), array('plugin' => null, 'controller' => 'settings', 'action' => 'general'), 'error');
		}
		if(empty(Configure::read('siteTitle'))) {
			$this->_addToDoList(__d('admin', 'Site title is not configured. Please check.'), array('plugin' => null, 'controller' => 'settings', 'action' => 'general'), 'error');
		}
		if(empty(Configure::read('siteEmail'))) {
			$this->_addToDoList(__d('admin', 'Site e-mail is not configured. Please check.'), array('plugin' => null, 'controller' => 'settings', 'action' => 'general'), 'error');
		}
		if(empty(Configure::read('siteEmailSender'))) {
			$this->_addToDoList(__d('admin', 'Site e-mail sender is not configured. Please check.'), array('plugin' => null, 'controller' => 'settings', 'action' => 'general'), 'error');
		}

		$adModel = ClassRegistry::init('Ad');
		$adModel->recursive = -1;
		$ads = $adModel->find('count', array(
			'conditions' => array(
				'status' => 'Pending',
			),
		));
		if($ads > 1) {
			$this->_addToDoList(__d('admin', '%d PTC ads are waiting for your approval.', $ads), array('plugin' => null, 'controller' => 'ads', 'action' => 'index', 'Ad.status' => 'Pending'));
		} elseif($ads == 1) {
			$this->_addToDoList(__d('admin', 'PTC ad is waiting for your approval.'), array('plugin' => null, 'controller' => 'ads', 'action' => 'index', 'Ad.status' => 'Pending'));
		}

		$models = array('BannerAd', 'FeaturedAd', 'LoginAd', 'PaidOffer', 'ExpressAd', 'ExplorerAd');

		foreach($models as $model) {
			$adModel = ClassRegistry::init($model);
			$adModel->contain();
			$ads = $adModel->find('count', array(
				'conditions' => array(
					'status' => 'Pending',
				),
			));

			list($plugin, $modelName) = pluginSplit($model);

			if($ads > 1) {
				$this->_addToDoList(__d('admin', '%d %s are waiting for your approval.', $ads, $modelName), array('plugin' => $plugin, 'controller' => Inflector::pluralize($modelName), 'action' => 'index', $modelName.'.status' => 'Pending'));
			} elseif($ads == 1) {
				$this->_addToDoList(__d('admin', '%s is waiting for your approval.', $modelName), array('plugin' => $plugin, 'controller' => Inflector::pluralize($modelName), 'action' => 'index', $modelName.'.status' => 'Pending'));
			}
		}

		if(Module::active('AdGrid')) {
			$adGridAd = ClassRegistry::init('AdGrid.AdGridAd');
			$adGridAd->contain();
			$ads = $adGridAd->find('count', array(
				'conditions' => array(
					'status' => 'Pending',
				),
			));

			if($ads > 1) {
				$this->_addToDoList(__d('admin', '%d AdGrid ads are waiting for your approval.', $ads), array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'index', 'AdGridAd.status' => 'Pending'));
			} elseif($ads == 1) {
				$this->_addToDoList(__d('admin', 'AdGrid ad is waiting for your approval.'), array('plugin' => 'ad_grid', 'controller' => 'adGrid', 'action' => 'index', 'AdGridAd.status' => 'Pending'));
			}
		}

		$itemReport = ClassRegistry::init('ItemReport');
		$itemReport->recursive = -1;
		$reports = $itemReport->find('count', array(
			'conditions' => array(
				'status' => ItemReport::PENDING,
			),
		));
		if($reports > 1) {
			$this->_addToDoList(__d('admin', '%d item reports are waiting for your attention.', $reports), array('plugin' => null, 'controller' => 'item_reports', 'action' => 'index', 'ItemReport.status' => ItemReport::PENDING));
		} elseif($reports == 1) {
			$this->_addToDoList(__d('admin', 'Item report is waiting for your attention.'), array('plugin' => null, 'controller' => 'item_reports', 'action' => 'index', 'ItemReport.status' => ItemReport::PENDING));
		}

		$supportTicket = ClassRegistry::init('SupportTicket');
		$supportTicket->contain(array('Answers' => array('fields' => array('Answers.sender_flag'), 'order' => 'created DESC', 'limit' => 1)));
		$tickets = $supportTicket->find('all', array(
			'fields' => array(
				'SupportTicket.id',
			),
			'conditions' => array(
				'status' => SupportTicket::OPEN,
			),
		));

		$count = 0;
		foreach($tickets as $t) {
			if(empty($t['Answers']) || $t['Answers'][0]['sender_flag'] == SupportTicketAnswer::OWNER) {
				$count++;
			}
		}
		unset($tickets);

		if($count > 1) {
			$this->_addToDoList(__d('admin', '%d Support tickets are waiting for your attention.', $count), array('plugin' => null, 'controller' => 'support', 'action' => 'index', 'SupportTicket.status' => SupportTicket::OPEN));
		} elseif($count == 1) {
			$this->_addToDoList(__d('admin', 'Support ticket is waiting for your attention.'), array('plugin' => null, 'controller' => 'support', 'action' => 'index', 'SupportTicket.status' => SupportTicket::OPEN));
		}

		$paidOffers = ClassRegistry::init('PaidOffer');
		$paidOffers->contain();
		$pending = $paidOffers->find('all', array(
			'fields' => array(
				'SUM(pending_applications) as pending',
			),
			'conditions' => array(
				'PaidOffer.advertiser_id' => null,
			),
		));
		$pending = $pending[0][0]['pending'];
		if($pending > 1) {
			$this->_addToDoList(__d('admin', '%d Paid Offers applications is waiting for your attention.', $pending), array('plugin' => null, 'controller' => 'paid_offers', 'action' => 'applications'));
		} elseif($pending == 1) {
			$this->_addToDoList(__d('admin', 'Paid Offers application is waiting for your attention.'), array('plugin' => null, 'controller' => 'paid_offers', 'action' => 'applications'));
		}

		$settings = $this->Settings->fetch(array(
			'cronDailyLast',
			'cronFrequentLast',
			'cronBotLast',
			'enableRentingReferrals',
			'rentMinClickDays',
			'rentFilter',
			'enableBuyingReferrals',
			'directFilter',
			'directMinClickDays',
		));

		if(!isset($settings['Settings']['cronDailyLast']) || !CakeTime::wasWithinLast('1 days', $settings['Settings']['cronDailyLast'])) {
			$this->_addToDoList(__d('admin', 'Daily cron is not working'), null, 'error');
		}

		if(!isset($settings['Settings']['cronFrequentLast']) || !CakeTime::wasWithinLast('10 minutes', $settings['Settings']['cronFrequentLast'])) {
			$this->_addToDoList(__d('admin', 'Frequent cron is not working'), null, 'error');
		}

		if(Module::active('BotSystem') && (!isset($settings['Settings']['cronBotLast']) || !CakeTime::wasWithinLast('30 minutes', $settings['Settings']['cronBotLast']))) {
			$this->_addToDoList(__d('admin', 'Bot cron is not working'), null, 'error');
		}

		$activeGateways = $this->Payments->getActive();

		if(Module::active('BotSystem')) {
			$this->helpers[] = 'BotSystem.BotSystem';
		}

		$overallStats['not_rented'] = $this->User->countAvailRRefs($settings);
		$overallStats['to_sale'] = $this->User->countAvailDRefs($settings);

		$this->set(compact('overallStats', 'total', 'chart', 'activeGateways'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		if($this->request->is('post')) {
			$this->Admin->create();
			$this->request->data['Admin']['verify_token'] = $this->Admin->createVerifyToken();
			if($this->Admin->save($this->request->data)) {
				$this->_sendVerificationEmail($this->Admin->getLastInsertId(), $this->request->data);
				$this->Notice->success(__d('admin', 'The admin has been saved. Verification e-mail has been sent.'));
			} else {
				$this->Notice->error(__d('admin', 'The admin could not be saved. Please, try again.'));
			}
		}
		$this->Admin->recursive = -1;
		$this->paginate = array('order' => 'id DESC');
		$this->set('admins', $this->Paginator->paginate());
	}

/**
 * admin_massaction method
 *
 * @throws NotFoundException
 * @return void
 */
	public function admin_massaction() {
		if(!empty($this->request->data['Action'])) {

			if(!isset($this->request->data['Admins']) || empty($this->request->data['Admins'])) {
				$this->Notice->error(__d('admin', 'Please select at least one admin.'));
				return $this->redirect($this->referer());
			}

			$admins = 0;
			foreach($this->request->data['Admins'] as $id => $on) {
				if($on) {
					if(!$this->Admin->exists($id)) {
						throw new NotFoundException(__d('exception', 'Invalid admin'));
					}
					$admins++;
				}
			}
			foreach($this->request->data['Admins'] as $id => $on) {
				if($on) {
					switch($this->request->data['Action']) {
						case 'delete':
							$this->Admin->delete($id);
						break;
					}
				}
			}
			if($admins) {
				$this->Notice->success(__d('admin', 'Mass Action done.'));
			} else {
				$this->Notice->error(__d('admin', 'Please select at least one admin.'));
			}
		} else {
			$this->Notice->error(__d('admin', 'Please select action.'));
		}
		$this->redirect($this->referer());
	}

/**
 * _sendVerificationEmail method
 *
 * @return void
 */
	protected function _sendVerificationEmail($userid, $user) {
		$email = ClassRegistry::init('Email');

		$email->setVariables(array(
			'%verifyurl%' => Router::url(array('controller' => 'admins', 'action' => 'verify', $userid, $user['Admin']['verify_token']), true),
		));

		$email->send('Admin account verification', $user['Admin']['email']);
	}

/**
 * admin_verify method
 *
 * @throws InternalErrorException
 * @return void
 */
 	public function admin_verify($id = null, $token = null) {
 		if($this->request->is('get')) {
			if($id === null || $token === null) {
				throw new BadRequestException(__d('exception', 'Wrong arguments'));
			}

			if(!($admin = $this->Admin->find('first', array('conditions' => array('Admin.id' => $id))))) {
				throw new NotFoundException(__d('exception', 'Invalid Admin'));
			}

			if($admin['Admin']['verify_token'] !== $token) {
				throw new NotFoundException(__d('exception', 'Invalid token'));
			}

			$this->Admin->id = $id;
			$this->Admin->set(array('id' => $id, 'verify_token' => null));
			if($this->Admin->save()) {
				$this->Notice->success(__d('admin', 'Your account has been successfully verified'));
			} else {
				throw new InternalErrorException('Failed to verify');
			}
			$this->redirect(array('action' => 'login'));
		} else {
			throw new MethodNotAllowedException();
		}
 	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if(!$this->Admin->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid admin'));
		}
		$options = array('conditions' => array('Admin.' . $this->Admin->primaryKey => $id));
		$this->set('admin', $this->Admin->find('first', $options));
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if(!$this->Admin->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid admin'));
		}
		if($this->request->is(array('post', 'put'))) {
			if(empty($this->request->data['Admin']['password']))
				unset($this->request->data['Admin']['password']);
			if($this->Admin->save($this->request->data)) {
				$this->Notice->success(__d('admin', 'The admin has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Notice->error(__d('admin', 'The admin could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Admin.' . $this->Admin->primaryKey => $id));
			$this->request->data = $this->Admin->find('first', $options);
			$this->request->data['Admin']['password'] = null;
		}

		if($this->request->data['Admin']['secret'] == Admin::SECRET_NONE || empty($this->request->data['Admin']['secret_data'])) {
			$this->request->data['Admin']['secret_data'] = $this->GoogleAuthenticator->createSecret();
		}
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if(!$this->Admin->exists($id)) {
			throw new NotFoundException(__d('exception', 'Invalid admin'));
		}
		$this->request->allowMethod('post', 'delete');
		if($this->Admin->delete($id)) {
			$this->Notice->success(__d('admin', 'The admin has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The admin could not be deleted. Please, try again.'));
		}
		return $this->redirect(['action' => 'index']);
	}
}
