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

class MinuteStaffOfferwall implements OfferwallInterface {
	private $api_settings = null;
	private $check_ips = null;
	private static $allowedIPs = array(
		'108.170.27.234',
		'108.170.27.238',
		'198.15.95.74',
		'198.15.113.58',
	);

	public function __construct($settings = array()) {
		$this->api_settings = $settings['api_settings'];
		$this->check_ips = $settings['allowed_ips'];
	}

	public function offers($user) {
		return '<iframe 
			src="https://offerwall.minutecircuit.com/display.php?app_id='.$this->api_settings['app_id'].
				'&site_code='.$this->api_settings['site_code'].'&user_id='.$user['User']['id'].
				'&user_email='.$user['User']['email'].'&site_type='.$this->api_settings['site_type'].'" 
			frameborder="0" 
			width="'.$this->api_settings['width'].'" height="'.$this->api_settings['height'].'" scrolling="no">
		</iframe>';
	}

	public function getTransactionId() {
		if(!empty($this->check_ips) && !in_array($_SERVER['REMOTE_ADDR'], explode(',', $this->check_ips))) {
			throw new ForbiddenException(__d('offerwalls_admin', 'Not allowed: %s', $_SERVER['REMOTE_ADDR']));
		}
		return @$_POST['notify_id'];
	}

	public function offerCallback() {
		$notify_id = intval($_POST['notify_id']);
		$app_id = intval($_POST['app_id']);
		$user_id = intval($_POST['user_id']);
		$real_amount = floatval($_POST['real_amount']);
		$verify_code = preg_replace('/[^a-zA-Z0-9]/', '', $_POST['verify_code']);

		if($verify_code !== md5($this->api_settings['notify_code'].$app_id.$user_id.$notify_id)) {
			throw new BadRequestException(__d('offerwalls_admin', 'Invalid verify code'));
		}

		if(bccomp($real_amount, '0') == 0) {
			return array(
				OfferwallsOffer::FAILED,
				$user_id,
				$real_amount,
			);
		}

		return array(
			OfferwallsOffer::COMPLETED,
			$user_id,
			$real_amount,
		);
	}

	public static function getAllowedIPs() {
		return self::$allowedIPs;
	}
}
