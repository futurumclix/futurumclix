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

class PagesController extends AppController {

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	public $allowedPages = array('home', 'finishRegistration', 'locked');

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 * @throws NotFoundException When the view file could not be found
 * @throws MissingViewException When the view file could not be found in debug mode
 * @throws Exception
 */
	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		if(in_array($page, $this->allowedPages) || $this->Auth->loggedIn())
		{
			if($page == 'home') {
				$this->_setStatsVariables();
			}

			try {
				$this->render(implode('/', $path));
			} catch (MissingViewException $e) {
				if (Configure::read('debug')) {
					throw $e;
				}
				throw new NotFoundException();
			}
		}
		else
			$this->redirect($this->Auth->redirectUrl());
	}

	protected function _setStatsVariables() {
		$total_clicks = ClassRegistry::init('UserStatistic')->find('all', array(
			'fields' => array('SUM(total_clicks) AS total_clicks'),
		));
		$total_clicks = $total_clicks[0][0]['total_clicks'];

		$magic_yesterday = ClassRegistry::init('Settings')->magicStatsNumber(1);

		$total_clicks_yesterday = ClassRegistry::init('UserStatistic')->find('all', array(
			'fields' => array('SUM(user_clicks_'.$magic_yesterday.') AS total_clicks'),
		));
		$total_clicks_yesterday = $total_clicks_yesterday[0][0]['total_clicks'];

		$cashoutModel = ClassRegistry::init('Cashout');

		$total_cashouts = $cashoutModel->query('
			SELECT COALESCE(SUM(IF(Cashout.status = \'Completed\', Cashout.amount, 0)), 0) as paid
			FROM `'.$cashoutModel->tablePrefix.$cashoutModel->table.'` as Cashout
		');
		$total_cashouts = $total_cashouts[0][0]['paid'];

		$cashouts_yesterday = $cashoutModel->query('
			SELECT COALESCE(SUM(`Cashout`.`amount`), 0) as total
			FROM `'.$cashoutModel->tablePrefix.$cashoutModel->table.'` as Cashout WHERE DATE(`Cashout`.`created`) = SUBDATE(CURDATE(), 1) AND `Cashout`.`status` = \'Completed\'
		')[0][0]['total'];

		$stats = array(
			'users' => $this->User->find('count'),
			'yesterday_users' => $this->User->find('count', array(
				'conditions' => array(
					'DATE(created) = SUBDATE(CURDATE(), 1)'
				),
			)),
			'clicks' => $total_clicks,
			'total_clicks_yesterday' => $total_clicks_yesterday,
			'total_cashouts' => $total_cashouts,
			'cashouts_yesterday' => $cashouts_yesterday,
		);

		if(Configure::read('onlineActive')) {
			$stats['users_online'] = ClassRegistry::init('Online.Online')->countOnline();
		}

		$this->set(compact('stats'));
	}

	public function content($name = null) {
		$sites = array(
			'tos' => array('siteToSActive', 'siteToS', 'siteToSTitle'),
			'faq' => array('siteFAQActive', 'siteFAQ', 'siteFAQTitle'),
			'privacy' => array('sitePrivacyPolicyActive', 'sitePrivacyPolicy', 'sitePrivacyPolicyTitle'),
		);

		if(!isset($sites[$name])) {
			throw new NotFoundException();
		}

		$keys = $sites[$name];

		if(!Configure::read($keys[0])) {
			throw new NotFoundException();
		}

		array_shift($keys);

		$settings = ClassRegistry::init('Settings')->fetch($keys);

		$this->set('text', $settings['Settings'][$keys[0]]);
		$this->set('title', $settings['Settings'][$keys[1]]);
	}

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('display', 'content');
	}
}
