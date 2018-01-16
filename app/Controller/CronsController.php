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

/**
 * Crons Controller
 *
 */
class CronsController extends AppController {

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('run'));
	}

/**
 * run callback
 *
 * @return void
 */
	public function run($name = null) {
		$this->autoRender = false;
		$this->layout = '';
		App::import('Console', 'ShellDispatcher');
		App::import('Console/Command', 'AppShell');
		App::import('Console/Command', 'CronShell');

		$allowed = array(
			'daily',
			'frequent',
		);

		if(Module::installed('BotSystem')) {
			$allowed[] = 'botSystem'; 
		}

		if(!$name || !in_array($name, $allowed)) {
			throw new NotFoundException(__d('exception', 'Invalid cron'));
		}

		$settings = $this->Settings->fetch(array(
			'allowHttpCron',
			'httpCronIPs',
		));

		if(empty($settings) || !$settings['Settings']['allowHttpCron']) {
			throw new NotFoundException(__d('exception', 'HTTP cron jobs not allowed'));
		}

		if(!empty($settings['Settings']['httpCronIPs']) && !in_array($this->request->clientIp(), explode(',', $settings['Settings']['httpCronIPs']))) {
			throw new UnauthorizedException(__d('exception', 'Unknown IP address'));
		}

		$shell = new CronShell();
		$shell->startup();

		$shell->dispatchMethod($name);

		echo __('Cron job executed sucesfully');
	}

}
