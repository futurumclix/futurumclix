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
App::uses('OfferwallInterface', 'Offerwalls.Lib/Offerwalls');

class PTCWallOfferwall implements OfferwallInterface {
	private $api_settings = null;
	private $check_ips = null;
	private static $allowedIPs = array(
		'199.193.247.113',
	);

	public function __construct($settings = array()) {
		$this->api_settings = $settings['api_settings'];
		$this->check_ips = $settings['allowed_ips'];
	}

	public function offers($user) {
		return '<iframe 
			src="http://www.ptcwall.com/index.php?view=ptcwall&pubid='.$this->api_settings['publisher_id'].'&usrid='.$user['User']['id'].'" 
			frameborder="0" 
			width="'.$this->api_settings['width'].'" height="'.$this->api_settings['height'].'" scrolling="no">
		</iframe>';
	}

	private function makeUniqId() {
		if($_REQUEST['none'] == 'ptcwall_ptc') {
			App::uses('CakeText', 'Utility');
			return CakeText::uuid();
		}
		throw new ForbiddenException(__d('offerwalls_admin', 'Unknown type: %s', $_REQUEST['none']));
	}

	public function getTransactionId() {
		if(!empty($this->check_ips) && !in_array($_SERVER['REMOTE_ADDR'], explode(',', $this->check_ips))) {
			throw new ForbiddenException(__d('offerwalls_admin', 'Not allowed: %s', $_SERVER['REMOTE_ADDR']));
		}
		return $this->makeUniqId();
	}

	public function offerCallback() {
		$sent_pw = strval($_REQUEST['pwd']);
		$credited = intval($_REQUEST['c']);
		$user_id = trim($_REQUEST['usr']);
		$rate = trim($_REQUEST['r']);

		if($sent_pw !== $this->api_settings['user_password']) {
			/* sorry, we can't render anything or even change  **
			** HTTP response code or PTCWall will ignore us.   */
			die("0\n");
		}

		if($credited == 2) {
			/* substract action, just make amount negative */
			$rate = bcsub('0', $rate);
		}

		print("1\n");

		return array(
			OfferwallsOffer::COMPLETED,
			$user_id,
			$rate,
		);
	}

	public static function getAllowedIPs() {
		return self::$allowedIPs;
	}
}
