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
App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
/**
 * recursive
 *
 * @var integer
 */
	public $recursive = -1;

/**
 * createRandomStr method
 *
 * @return string
 */
	public function createRandomStr($len) {
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
		$res = '';

		for($i = 0; $i < $len; ++$i)
			$res .= $chars[rand()%62];

		return $res;
	}

/**
 * checkPastDate method
 *
 * @return boolean 
 */ 
	public function checkPastDate($check) {
		return strtotime(array_values($check)[0]) < time();
	}

/**
 * equalToField method
 *
 * @return boolean
 */
	public function equalToField($check, $fieldName) {
		return array_values($check)[0] == $this->data[$this->name][$fieldName];
	}

/**
 * notEqualToField method
 *
 * @return boolean
 */
	public function notEqualToField($check, $fieldName) {
		return array_values($check)[0] != $this->data[$this->name][$fieldName];
	}

/**
 * comparsionWithField method
 *
 * @return boolean
 */
	public function comparisonWithField($validationFields = array(), $operator = null, $compareFieldName = '') {
		if(!isset($this->data[$this->name][$compareFieldName])) {
			throw new CakeException(__d('exception', 'Cannot compare to the non-existing field "%s" of model %s.', $compareFieldName, $this->name));
		}
		$compareTo = $this->data[$this->name][$compareFieldName];
		foreach($validationFields as $key => $value) {
			if(!Validation::comparison($value, $operator, $compareTo)) {
				return false;
			}
		}
		return true;
	}

/**
 * checkMonetary method
 *
 * Database is configured to hold DECIMAL(17,8) to be able to work with BTC,
 * so here we check if monetary value is valid (have no more than 17 digits 
 * at all and at most 8 after the decimal dot).
 *
 * @return boolean
 */
	public function checkMonetary($check, $sign = false) {
		$val = (string) array_values($check)[0];

		if($sign === true) {
			$val = ltrim($val, '-');
		}

		if(preg_match('/^[0-9]+(?:\.[0-9]+)?$/', $val)) {
			$all = 0;
			$aDot = 0;
			$dot = false;

			for($i = 0, $len = strlen($val); $i < $len; ++$i) {
				if($val[$i] >= '0' && $val[$i] <= '9') {
					$all++;

					if($dot) {
						$aDot++;
					}

				} else if($val[$i] == '.') {
					$dot = true;
				} else {
					return false;
				}
			}

			if($all <= 17 && $aDot <= 8) {
				return true;
			}
		}
		return false;
	}

/**
 * checkPoints method
 *
 * Points are DECIMAL(10,2) (8 places before decimal separator
 * and 2 after)
 * @return boolean
 */
	public function checkPoints($check, $sign = false) {
		$val = (string) array_values($check)[0];

		if($sign === true) {
			$val = ltrim($val, '-');
		}

		if(preg_match('/^[0-9]+(?:\.[0-9]+)?$/', $val)) {
			$all = 0;
			$aDot = 0;
			$dot = false;

			for($i = 0, $len = strlen($val); $i < $len; ++$i) {
				if($val[$i] >= '0' && $val[$i] <= '9') {
					$all++;

					if($dot) {
						$aDot++;
					}

				} else if($val[$i] == '.') {
					$dot = true;
				} else {
					return false;
				}
			}

			if($all <= 10 && $aDot <= 2) {
				return true;
			}
		}
		return false;
	}

/**
 * checkMonetaryList method
 *
 * Validates monatary values separated by comma
 *
 * @return boolean
 */
	public function checkMonetaryList($check, $sign = false) {
		$val = (string) array_values($check)[0];
		$val = explode(',', $val);

		foreach($val as $v) {
			if(!$this->checkMonetary(array($v), $sign))
				return false;
		}

		return true;
	}

/**
 * bcrange method
 *
 * @return boolean
 */
	public function bcrange($check, $min, $max) {
		$val = (string)reset($check);

		if(bccomp($val, $min) >= 0 && bccomp($max, $val) >= 0) {
			return true;
		}
		return false;
	}

/**
 * checkHTTPContentType method
 *
 * @return boolean
 */
	public function checkHTTPContentType($check, $type) {
		$url = (string) array_values($check)[0];
		stream_context_set_default(array('http' => array('method' => 'HEAD')));
		$headers = get_headers($url, 1);

		foreach($headers as $h => $v) {
			if(strcasecmp($h, 'content-type') == 0) {
				debug($v);
				if(is_array($v)) {
					$v = reset($v);
				}
				if(stristr($v, $type) !== false) {
					return true;
				}
			}
		}
		return false;
	}

/**
 * columnExists method
 *
 * @return boolean
 */
	protected function columnExists($name) {
		$ds = $this->getDataSource();

		$sql = "SELECT COLUMN_NAME
			FROM information_schema.COLUMNS
			WHERE TABLE_SCHEMA =  '{$ds->config['database']}'
			AND TABLE_NAME =  '{$this->tablePrefix}{$this->table}'
			AND COLUMN_NAME = '$name'
			LIMIT 1";

		$res = $this->query($sql);

		return !empty($res);
	}

/**
 * addNewColumn
 *
 * @return boolean
 */
	protected function addNewColumn($name, $type, $null, $length = null) {
		$ds = $this->getDataSource();

		if(!$this->columnExists($name) && isset($this->validate[$name])) {
			$column = $ds->buildColumn(array('name' => $name, 'type' => $type, 'null' => $null, 'length' => $length));
			$sql = "ALTER TABLE `{$this->tablePrefix}{$this->table}` ADD COLUMN $column";

			$res = $this->query($sql);
			return $res;
		}
		return true;
	}

/**
 * updateAll method
 *
 * Adds support for recursive parameter in CakePHP's updateAll().
 * Written by Petr "PePa" Pavel, http://blog.pepa.info/php-html-css/cakephp/getting-rid-of-joins-in-updateall-query/
 * Got at: 05-04-2016.
 *
 * @return boolean
 */
	function updateAll($fields, $conditions = true, $recursive = null) {
		if($recursive === null) {
			$recursive = $this->recursive;
		}

		if($recursive == -1) {
			$join = array();

			foreach($fields as $field => $value) {
				$field = explode('.', $field);

				if($field[0] != $this->alias) {
					$join[] = $field[0];
				}
			}

			if(is_array($conditions)) {
				foreach($conditions as $field => $value) {
					$field = explode('.', $field);

					if($field[0] != $this->alias) {
						$join[] = $field[0];
					}
				}
			}

			$belongsTo = $this->belongsTo;
			$hasOne = $this->hasOne;
			$this->unbindModel(array(
				'belongsTo' => array_diff(array_keys($belongsTo), $join),
				'hasOne' => array_diff(array_keys($hasOne), $join),
			), true);
		}

		$result = parent::updateAll($fields, $conditions);

		if($recursive == -1) {
			$this->bindModel(array(
				'belongsTo' => $belongsTo,
				'hasOne' => $hasOne,
			), true);
		}

		return $result;
	}

}
