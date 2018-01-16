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

class ClixWallOfferwall implements OfferwallInterface {
	private $api_settings = null;
	private $check_ips = null;
	private static $allowedIPs = array(
		/* TODO: add some */
	);

	public function __construct($settings = array()) {
		$this->api_settings = $settings['api_settings'];
		$this->check_ips = $settings['allowed_ips'];
	}

	public function offers($user) {
		return '<iframe 
			src="https://www.clixwall.com/wall.php?p='.$this->api_settings['api_code'].'&u='.$user['User']['id'].'&email='.$user['User']['email'].'" 
			frameborder="0" 
			width="'.$this->api_settings['width'].'" height="'.$this->api_settings['height'].'" scrolling="no">
		</iframe>';
	}

	public function getTransactionId() {
		if(!empty($this->check_ips) && !in_array($_SERVER['REMOTE_ADDR'], explode(',', $this->check_ips))) {
			throw new ForbiddenException(__d('offerwalls_admin', 'Not allowed: %s', $_SERVER['REMOTE_ADDR']));
		}
		return @$_REQUEST['cid'];
	}

	public function offerCallback() {
		$sent_pw = strval($_REQUEST['pwd']);
		$credited = intval($_REQUEST['s']);
		$user_id = trim($_REQUEST['u']);
		$rate = trim($_REQUEST['c']);

		if($sent_pw !== $this->api_settings['secret_password']) {
			throw new BadRequestException(__d('offerwalls_admin', 'Invalid password'));
		}

		if($credited == 2) {
			/* substract action, just make amount negative */
			$rate = bcsub('0', $rate);
		}

		print("Done\n");

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
