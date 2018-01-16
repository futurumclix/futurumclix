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

class WoobiOfferwall implements OfferwallInterface {
	private $api_settings = null;
	private $check_ips = null;
	private static $allowedIPs = array(
		'149.255.37.122',
		'149.255.37.123',
		'149.255.37.124',
		'149.255.37.125',
		'149.255.37.126',
		'46.21.154.210',
		'46.21.154.211',
		'46.21.154.212',
		'46.21.154.213',
		'46.21.154.214',
		'149.255.38.83',
		'149.255.38.82',
		'149.255.38.84',
		'149.255.38.85',
		'52.50.5.210',
	);

	public function __construct($settings = array()) {
		$this->api_settings = $settings['api_settings'];
		$this->check_ips = $settings['allowed_ips'];
	}

	public function offers($user) {
		switch($user['UserProfile']['gender']) {
			case 'Male':
				$gender = '&gender=1';
			break;

			case 'Female':
				$gender = '&gender=2';
			break;

			default:
				$gender = '&gender=0';
		}

		if($user['UserProfile']['birth_day']) {
			$now = new DateTime();
			$birth = new DateTime($user['UserProfile']['birth_day']);
			$age = '&age='.$now->diff($birth)->y + 1;
		} else {
			$age = '';
		}

		return '<iframe 
			src="//products.woobi.com/offerwall/?aid='.$this->api_settings['applicationid'].'&cid='.$user['User']['id'].$gender.$age.'" 
			frameborder="0" width="'.$this->api_settings['width'].'" height="'.$this->api_settings['height'].'">
		</iframe>';
	}

	public function getTransactionId() {
		if(!empty($this->check_ips) && !in_array($_SERVER['REMOTE_ADDR'], explode(',', $this->check_ips))) {
			throw new ForbiddenException(__d('offerwalls_admin', 'Not allowed: %s', $_SERVER['REMOTE_ADDR']));
		}
		return @$_REQUEST['transaction_id'];
	}

	public function offerCallback() {
		$user_id        = $_REQUEST['uid'];
		$points         = $_REQUEST['award'];
		$hash_signature = strval($_REQUEST['sign']);

		$hash = md5($_REQUEST['hash'].$this->api_settings['secret_key']);
		if($hash !== $hash_signature) {
			throw new BadRequestException(__d('offerwalls_admin', 'Invalid signature'));
		}

		if(!ClassRegistry::init('User')->exists($user_id)) {
			throw new NotFoundException(__d('offerwalls_admin', 'Invalid user'));
		}

		return array(
			OfferwallsOffer::COMPLETED,
			$user_id,
			$points,
		);
	}

	public static function getAllowedIPs() {
		return self::$allowedIPs;
	}
}
