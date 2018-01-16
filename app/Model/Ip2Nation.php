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
class Ip2Nation extends AppModel {
	public $primaryKey = 'ip';
	public $useTable = 'ip2nation';
	public $belongsTo = array(
		'Ip2NationCountry' => array(
			'className' => 'Ip2NationCountry',
			'foreignKey' => false,
			'conditions' => array('`Ip2NationCountry`.`code` = `Ip2Nation`.`country`'), 
			'dependent' => false,
		),
	);

	function getCountryByIp($ip) {
		$table = $this->tablePrefix.$this->table;
		$res = $this->query("SELECT c.country FROM `{$this->Ip2NationCountry->tablePrefix}{$this->Ip2NationCountry->table}` c, `$table` i
									WHERE i.ip < INET_ATON('$ip') AND c.code = i.country
									ORDER BY i.ip DESC
									LIMIT 0,1");

		if(empty($res)) {
			return null;
		}

		return $res[0]['c']['country'];
	}

	function getCountryIdByIp($ip_dot) {
		$ip = ip2long($ip_dot);
		$this->recursive = 1;
		$code = $this->find('first', array(
			'order' => 'ip DESC',
			'conditions' => array('ip <' => "$ip"),
		));
		if(empty($code)) {
			throw new InternalErrorException(__d('exception', 'Invalid IP address: %s = %s', $ip_dot, $ip));
		}
		return $code['Ip2NationCountry']['id'];
	}

	function getCountryCodeByIp($ip) {
		$ip = ip2long($ip);
		$this->recursive = 1;
		$code = $this->find('first', array(
			'fields' => 'country',
			'order' => 'ip DESC',
			'conditions' => array('ip <' => "$ip"),
		));
		return $code[$this->alias]['country'];
	}

	function getCountryByCode($country_code) {
		$res = $this->query("SELECT c.country FROM `{$this->Ip2NationCountry->tablePrefix}{$this->Ip2NationCountry->table}` c
									WHERE country = '$country_code' LIMIT 0,1");
		return $res[0]['c']['country'];
	}

	function getLocationByIp($ip) {
		$res = $this->getCountryByIp($ip);

		if($res === null) {
			return '*';
		}

		return $res;
	}
}
