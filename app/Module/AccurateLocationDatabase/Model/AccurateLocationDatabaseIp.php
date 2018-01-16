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
App::uses('AppModel', 'Model');
/**
 * AccurateLocationDatabaseIp Model
 *
 */
class AccurateLocationDatabaseIp extends AppModel {
	function getLocationByIp2($ip) {
		$table = $this->tablePrefix.$this->table;
		$qS = "SELECT {$this->alias}.country, {$this->alias}.region, {$this->alias}.city FROM `$table` as {$this->alias}
				 WHERE {$this->alias}.ip_start <= INET_ATON('$ip') ORDER BY {$this->alias}.ip_start DESC
				 LIMIT 5";
		$res = $this->query($qS);

		if(empty($res)) {
			return '*';
		}

		$res = $res[0];
		return $res[$this->alias]['country'].'/'.$res[$this->alias]['region'].'/'.$res[$this->alias]['city'];
	}

	function getLocationByIp($ip) {
		$table = $this->tablePrefix.$this->table;
		$qS = "SELECT {$this->alias}.country, {$this->alias}.region, {$this->alias}.city FROM `$table` as {$this->alias}
				 WHERE {$this->alias}.ip_start <= INET_ATON('$ip') AND {$this->alias}.ip_end >= INET_ATON('$ip')
				 LIMIT 5";
		$res = $this->query($qS);

		if(empty($res)) {
			return $this->getLocationByIp2($ip);
		}

		$res = $res[0];
		return $res[$this->alias]['country'].'/'.$res[$this->alias]['region'].'/'.$res[$this->alias]['city'];
	}
}
