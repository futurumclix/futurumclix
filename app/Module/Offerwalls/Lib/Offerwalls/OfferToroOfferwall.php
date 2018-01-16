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

class OfferToroOfferwall implements OfferwallInterface {
	private $api_settings = null;
	private $check_ips = null;

	public function __construct($settings = array()) {
		$this->api_settings = $settings['api_settings'];
		$this->check_ips = $settings['allowed_ips'];
	}

	public function offers($user) {
		return '<iframe src="http://www.offertoro.com/ifr/show/'.$this->api_settings['pubid'].'/'.$user['User']['id'].'/'.$this->api_settings['applicationid'].'" frameborder="0" width="'.$this->api_settings['width'].'" height="'.$this->api_settings['height'].'" ></iframe>';
	}

	public function getTransactionId() {
		if(!empty($this->check_ips) && !in_array($_SERVER['REMOTE_ADDR'], explode(',', $this->check_ips))) {
			throw new ForbiddenException(__d('offerwalls_admin', 'Not allowed: %s', $_SERVER['REMOTE_ADDR']));
		}
		return @$_REQUEST['id'];
	}

	public function offerCallback() {
		$transaction_id = $_REQUEST['id'];
		$user_id        = $_REQUEST['user_id'];
		$points         = $_REQUEST['amount'];
		$hash_signature = strval($_REQUEST['sig']);

		$hash = md5($_REQUEST['oid'].'-'.$user_id.'-'.$this->api_settings['secret_key']);
		if($hash !== $hash_signature) {
			echo "0\n";
			throw new BadRequestException(__d('offerwalls_admin', 'Invalid signature'));
		}

		if(!ClassRegistry::init('User')->exists($user_id)) {
			echo "0\n";
			throw new NotFoundException(__d('offerwalls_admin', 'Invalid user'));
		}

		echo "1\n";

		return array(
			OfferwallsOffer::COMPLETED,
			$user_id,
			$points,
		);
	}

	public static function getAllowedIPs() {
		return array();
	}
}
