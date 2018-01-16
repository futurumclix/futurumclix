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
/**
 * ConverterShell
 *
 * Converts database IP2Location (http://www.ip2location.com).
 *
 */
class ConverterShell extends AppShell {
	public $uses = array(
		'AccurateLocationDatabase.AccurateLocationDatabaseLocation',
		'AccurateLocationDatabase.AccurateLocationDatabaseIp',
	);

	public function extractGeo() {
		$filename = $this->args[0];

		$fh = fopen($filename, 'r');

		if($fh === false) {
			$this->out('Error: cannot open file: '.$filename);
			return;
		}

		$data = array();
		$id = 1;

		while($l = fgetcsv($fh)) {
			if(!isset($l[5])) {
				debug($l);exit;
			}

			if($l[3] == '-' || $l[4] == '-' || $l[5] == '-') {
				continue;
			}

			if(!isset($data[$l[3]])) {
				$data[$l[3]] = array(
					'id' => $id++,
					'code' => $l[2],
					'country' => $l[3],
					'regions' => array(
						$l[4] => array(
							'id' => $id++,
							'region' => $l[4],
							'cities' => array(
								$l[5] => array(
									'id' => $id++,
									'city' => $l[5],
								),
							),
						),
					),
				);
			} else {
				if(!isset($data[$l[3]]['regions'][$l[4]])) {
					$data[$l[3]]['regions'][$l[4]] = array(
						'id' => $id++,
						'region' => $l[4],
						'cities' => array(
							$l[5] => array(
								'id' => $id++,
								'city' => $l[5],
							),
						),
					);
				} else {
					if(!isset($data[$l[3]]['regions'][$l[4]]['cities'][$l[5]])) {
						$data[$l[3]]['regions'][$l[4]]['cities'][$l[5]] = array(
							'id' => $id++,
							'city' => $l[5],
						);
					}
				}
			}
		}

		$countries = array();

		foreach($data as $c) {
			$countries[] = array(
				'parent_id' => null,
				'name' => $c['country'],
			);
		}

		$res = $this->AccurateLocationDatabaseLocation->saveMany($countries);
		debug($res);
		debug($this->AccurateLocationDatabaseLocation->validationErrors);

		if(!$res) {
			throw new InternalErrorException('Error when saving countries');
		}

		unset($countries);

		$this->AccurateLocationDatabaseLocation->recursive = -1;
		$countries = $this->AccurateLocationDatabaseLocation->find('all', array(
			'conditions' => array(
				'parent_id' => null,
			),
		));

		$regions = array();

		foreach($countries as $c) {
			foreach($data[$c['AccurateLocationDatabaseLocation']['name']]['regions'] as $r) {
				$regions[] = array(
					'parent_id' => $c['AccurateLocationDatabaseLocation']['id'],
					'name' => $r['region'],
				);
			}
		}

		$res = $this->AccurateLocationDatabaseLocation->saveMany($regions);
		debug($res);
		debug($this->AccurateLocationDatabaseLocation->validationErrors);

		if(!$res) {
			throw new InternalErrorException('Error when saving regions');
		}

		unset($regions);
		unset($countries);

		$this->AccurateLocationDatabaseLocation->recursive = -1;
		$regions = $this->AccurateLocationDatabaseLocation->find('list', array(
			'conditions' => array(
				'parent_id !=' => null,
			),
		));

		$cities = array();

		foreach($data as $c) {
			foreach($c['regions'] as $r) {
				foreach($r['cities'] as $t) {
					$cities[] = array(
						'parent_id' => array_search($r['region'], $regions),
						'name' => $t['city'],
					);
				}
			}
		}

		$res = $this->AccurateLocationDatabaseLocation->saveMany($cities);
		debug($res);
		debug($this->AccurateLocationDatabaseLocation->validationErrors);

		if(!$res) {
			throw new InternalErrorException('Error when saving cities');
		}

		fclose($fh);
	}

	public function recoverLocations() {
		$res = $this->AccurateLocationDatabaseLocation->recover();
		debug($res);
	}

	public function import() {
		$filename = $this->args[0];

		$fh = fopen($filename, 'r');

		if($fh === false) {
			$this->out('Error: cannot open file: '.$filename);
			return;
		}

		$data = array();

		while($l = fgetcsv($fh)) {
			$data[] = array(
				'ip_start' => $l[0],
				'ip_end' => $l[1],
				'code' => $l[2],
				'country' => $l[3],
				'region' => $l[4],
				'city' => $l[5],
			);
		}

		fclose($fh);

		$res = $this->AccurateLocationDatabaseIp->saveMany($data);
		debug($res);
		debug($this->AccurateLocationDatabaseIp->validationErrors);

		if(!$res) {
			throw new InternalErrorException('Error when saving cities');
		}

		$this->out('done');
	}
}
