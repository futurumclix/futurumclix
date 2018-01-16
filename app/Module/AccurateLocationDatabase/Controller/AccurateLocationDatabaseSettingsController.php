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
class AccurateLocationDatabaseSettingsController extends AccurateLocationDatabaseAppController {
	private $cvsPath = TMP.'IP-COUNTRY-REGION-CITY.CSV';
	private $maxLines = 100000;

	public $uses = array(
		'AccurateLocationDatabase.AccurateLocationDatabaseLocation',
		'AccurateLocationDatabase.AccurateLocationDatabaseIp',
	);

	public function admin_index() {
		$cvsPath = $this->cvsPath;
		$installed = $this->AccurateLocationDatabaseLocation->find('count') || $this->AccurateLocationDatabaseIp->find('count');

		$tables = array(
			$this->AccurateLocationDatabaseLocation->tablePrefix.$this->AccurateLocationDatabaseLocation->table,
			$this->AccurateLocationDatabaseIp->tablePrefix.$this->AccurateLocationDatabaseIp->table,
		);

		$cvsExists = file_exists($cvsPath) && is_readable($cvsPath);

		$this->Session->delete('ALDcities');
		$this->Session->delete('ALDregions');
		$this->Session->delete('ALD');

		$this->set(compact('installed', 'tables', 'cvsPath', 'cvsExists'));
	}

	public function admin_install($seek = 0) {
		$done = false;
		$this->autoRender = false;

		$countries = $this->Session->read('ALD');
		if(!$countries) {
			$countries = array();
		}

		@$fh = fopen($this->cvsPath, 'r');

		if($fh === false) {
			return json_encode(array('state' => 'ERROR', 'msg' => __d('accurate_location_database_admin', 'Failed to open CSV file.')));
		}

		fseek($fh, 0, SEEK_END);
		$end = ftell($fh);

		fseek($fh, $seek);

		$query = "INSERT INTO {$this->AccurateLocationDatabaseIp->tablePrefix}{$this->AccurateLocationDatabaseIp->table} (ip_start, ip_end, code, country, region, city) VALUES ";
		$lines = 0;
		while($lines < $this->maxLines && $l = fgetcsv($fh)) {
			$query .= "({$l[0]}, {$l[1]}, \"{$l[2]}\", \"{$l[3]}\", \"{$l[4]}\", \"{$l[5]}\"),";
			@$countries[$l[3]]++;
			$lines++;
		}
		$query = rtrim($query, ',');

		$res = $this->AccurateLocationDatabaseIp->query($query);

		if(!empty($res)) {
			return json_encode(array('state' => 'ERROR', 'msg' => __d('accurate_location_database_admin', 'Failed to run query: '.print_r($res, true))));
		}

		$seek = ftell($fh);
		$done = feof($fh);

		fclose($fh);

		$this->Session->write('ALD', $countries);

		if($done) {
			return json_encode(array('state' => 'DONE'));
		} else {
			return json_encode(array('state' => 'CONTINUE', 'seek' => $seek, 'percent' => round($seek / $end * 100, 1)));
		}
	}

	public function admin_extract_countries() {
		$this->autoRender = false;
		$countries = $this->Session->read('ALD');

		if(!$countries || empty($countries)) {
			return json_encode(array('state' => 'ERROR', 'msg' => __d('accurate_location_database_admin', 'No countries.')));
		}

		$countries = array_keys($countries);

		$query = "INSERT INTO {$this->AccurateLocationDatabaseLocation->tablePrefix}{$this->AccurateLocationDatabaseLocation->table} (name) VALUES ";
		foreach($countries as $c) {
			if($c != '-') {
				$query .= "(\"$c\"),";
			}
		}
		$query = rtrim($query, ",");

		$res = $this->AccurateLocationDatabaseLocation->query($query);

		if(!empty($res)) {
			return json_encode(array('state' => 'ERROR', 'msg' => __d('accurate_location_database_admin', 'Failed to run query: '.print_r($res, true))));
		}

		$this->Session->delete('ALD');

		$countries = $this->AccurateLocationDatabaseLocation->find('list');
		$this->Session->write('ALD', array_flip($countries));

		return json_encode(array('state' => 'DONE'));
	}

	public function admin_extract_regions($seek = 0) {
		$done = false;
		$this->autoRender = false;

		$countries = $this->Session->read('ALD');
		if(!$countries) {
			return json_encode(array('state' => 'ERROR', 'msg' => __d('accurate_location_database_admin', 'No countries.')));
		}

		$regions = $this->Session->read('ALDregions');
		if(!$regions) {
			$regions = array();
		}

		@$fh = fopen($this->cvsPath, 'r');

		if($fh === false) {
			return json_encode(array('state' => 'ERROR', 'msg' => __d('accurate_location_database_admin', 'Failed to open CSV file.')));
		}

		fseek($fh, 0, SEEK_END);
		$end = ftell($fh);

		fseek($fh, $seek);

		$query = "INSERT INTO {$this->AccurateLocationDatabaseLocation->tablePrefix}{$this->AccurateLocationDatabaseLocation->table} (parent_id, name) VALUES ";
		$atleastone = false;
		$lines = 0;
		while($lines < $this->maxLines && $l = fgetcsv($fh)) {
			if($l[4] != '-' && !isset($regions[$l[4]])) {
				$query .= "({$countries[$l[3]]}, \"{$l[4]}\"),";
				$regions[$l[4]] = 1;
				$atleastone = true;
			}
			$lines++;
		}
		$query = rtrim($query, ',');

		if($atleastone) {
			$res = $this->AccurateLocationDatabaseIp->query($query);

			if(!empty($res)) {
				return json_encode(array('state' => 'ERROR', 'msg' => __d('accurate_location_database_admin', 'Failed to run query: '.print_r($res, true))));
			}
		}

		$seek = ftell($fh);
		$done = feof($fh);

		fclose($fh);

		$this->Session->write('ALDregions', $regions);

		if($done) {
			$this->Session->delete('ALD');
			$this->Session->delete('ALDregions');
			return json_encode(array('state' => 'DONE'));
		} else {
			return json_encode(array('state' => 'CONTINUE', 'seek' => $seek, 'percent' => round($seek / $end * 100, 1)));
		}
	}

	public function admin_extract_cities($seek = 0) {
		$done = false;
		$this->autoRender = false;

		$regions = $this->Session->read('ALDregions');
		if(!$regions || empty($regions)) {
			$regions = $this->AccurateLocationDatabaseLocation->find('list', array('conditions' => array('parent_id !=' => null)));
			if(empty($regions)) {
				return json_encode(array('state' => 'ERROR', 'msg' => __d('accurate_location_database_admin', 'No regions.')));
			}
			$regions = array_flip($regions);
			$this->Session->write('ALDregions', $regions);
		}

		$cities = $this->Session->read('ALDcities');
		if(!$cities) {
			$cities = array();
		}

		@$fh = fopen($this->cvsPath, 'r');

		if($fh === false) {
			return json_encode(array('state' => 'ERROR', 'msg' => __d('accurate_location_database_admin', 'Failed to open CSV file.')));
		}

		fseek($fh, 0, SEEK_END);
		$end = ftell($fh);

		fseek($fh, $seek);

		$query = "INSERT INTO {$this->AccurateLocationDatabaseLocation->tablePrefix}{$this->AccurateLocationDatabaseLocation->table} (parent_id, name) VALUES ";
		$atleastone = false;
		$lines = 0;
		while($lines < $this->maxLines && $l = fgetcsv($fh)) {
			if($l[5] != '-' && !isset($cities[$l[5]])) {
				$query .= "({$regions[$l[4]]}, \"{$l[5]}\"),";
				@$cities[$l[5]] = 1;
				$atleastone = true;
			}
			$lines++;
		}
		$query = rtrim($query, ',');

		if($atleastone) {
			$res = $this->AccurateLocationDatabaseIp->query($query);

			if(!empty($res)) {
				return json_encode(array('state' => 'ERROR', 'msg' => __d('accurate_location_database_admin', 'Failed to run query: '.print_r($res, true))));
			}
		}

		$seek = ftell($fh);
		$done = feof($fh);

		fclose($fh);

		$this->Session->write('ALDcities', $cities);

		if($done) {
			$this->Session->delete('ALDcities');
			$this->Session->delete('ALDregions');
			$this->Session->delete('ALD');
			return json_encode(array('state' => 'DONE'));
		} else {
			return json_encode(array('state' => 'CONTINUE', 'seek' => $seek, 'percent' => round($seek / $end * 100, 1)));
		}
	}
}
