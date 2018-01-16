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

class SuperRewardsOfferwall implements OfferwallInterface {
	private $api_settings = null;
	private $check_ips = null;
	private static $allowedIPs = array(
		'54.85.0.76',
		'54.84.205.80',
		'54.84.27.163',
	);

	public function __construct($settings = array()) {
		$this->api_settings = $settings['api_settings'];
		$this->check_ips = $settings['allowed_ips'];
	}

	public function offers($user) {
		return '<iframe 
			src="https://wall.superrewards.com/super/offers?h='.$this->api_settings['h'].'&uid='.$user['User']['id'].'" 
			frameborder="0" 
			width="'.$this->api_settings['width'].'" height="'.$this->api_settings['height'].'" scrolling="no">
		</iframe>';
	}

	public function getTransactionId() {
		if(!empty($this->check_ips) && !in_array($_SERVER['REMOTE_ADDR'], explode(',', $this->check_ips))) {
			throw new ForbiddenException(__d('offerwalls_admin', 'Not allowed: %s', $_SERVER['REMOTE_ADDR']));
		}
		return @$_REQUEST['id'];
	}

	public function offerCallback() {
		header('Content-type: text/plain');

		$transaction_id = $_REQUEST['id'];
		$user_id        = $_REQUEST['uid'];
		$new_currency   = $_REQUEST['new'];
		$hash_signature = strval($_REQUEST['sig']);

		$hash = md5($transaction_id.':'.$new_currency.':'.$user_id.':'.$this->api_settings['secret_key']);

		if($hash !== $hash_signature || !ClassRegistry::init('User')->exists($user_id)) {
			/* sorry, we can't render anything or even change       **
			** HTTP response code or SuperRewards will ignore us.   */
			die("0\n");
		}

		print("1\n");

		return array(
			OfferwallsOffer::COMPLETED,
			$user_id,
			$new_currency,
		);
	}

	public static function getAllowedIPs() {
		return self::$allowedIPs;
	}
}
