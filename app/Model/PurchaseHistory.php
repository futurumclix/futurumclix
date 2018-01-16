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
 * PurchaseHistory Model
 *
 */
class PurchaseHistory extends AppModel {
	public $useTable = false;

/**
 * Models which will be used
 * to create history list
 *
 * @var array
 */
	private $uses = array(
		'Deposits',
		'AutopayHistory',
		'AutorenewHistory',
	);

/**
 * var fields
 *
 * if starts with:
 * _ - call method, it's result is field definition
 * ' - static string
 *
 * @array
 */
	private $fields = array(
		'Deposits' => array(
			'date' => 'date',
			'amount' => 'amount',
			'title' => '_bindDepositTitle',
			'method' => 'gateway',
			'status' => 'status',
		),
		'AutopayHistory' => array(
			'date' => 'created',
			'amount' => 'amount',
			'title' => '\'Autopay\'',
			'method' => '\'PurchaseBalance\'', 
			'status' => '\'Success\''
		),
		'AutorenewHistory' => array(
			'date' => 'created',
			'amount' => 'amount',
			'title' => '\'Autorenew\'',
			'method' => '\'PurchaseBalance\'',
			'status' => '\'Success\'',
		),
	);

	private $conditions = array(
		'Deposits' => array(
			'status' => array('\'Success\'', '\'Pending\''),
		),
		'AutorenewHistory' => array(
			'amount !=' => '0',
		),
	);

	private $orderFields = array(
		'date',
		'amount',
		'title',
		'method',
	);

	private $models = array();

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		foreach($this->uses as $mName) {
			$this->models[$mName] = ClassRegistry::init($mName);
		}
	}

	private function _selectFields($fields = array()) {
		if(!empty($fields)) {
			$fields = array_flip($fields);
			foreach($this->fields as $modelName => $modelFields) {
				$selected[$modelName] = array_intersect_key($modelFields, $fields);
			}
			return $selected;
		}
		return $this->fields;
	}

	private function _createConditions($modelAlias, $conditions = array()) {
		$condStr = '';
		if(isset($this->conditions[$modelAlias])) {
			$conds = $conditions + $this->conditions[$modelAlias];
		} else {
			$conds = $conditions;
		}

		if(!empty($conds)) {
			$condStr = ' WHERE ';
			foreach($conds as $key => $value) {
				if(is_array($value)) {
					$condStr .= $key.' IN ('.implode(', ', $value).') AND';
				} else {
					$lastKeyChar = $key{strlen($key) - 1};
					if($lastKeyChar == '=' || $lastKeyChar == '<' || $lastKeyChar == '>') {
						$condStr .= $key.' '.$value.' AND ';
					} else {
						$condStr .= $key.' = '.$value.' AND ';
					}
				}
			}
			$condStr = rtrim($condStr, ' AND ');
		}
		return $condStr;
	}

	private function _createSort($order, $extra = array()) {
		if($order) {
			return ' ORDER BY '.$order;
		}

		if(isset($extra['sort']) && in_array($extra['sort'], $this->orderFields)) {
			if(!isset($extra['direction']) || !in_array($extra['direction'], array('asc', 'desc'))) {
				$extra['direction'] = 'asc';
			}
			return ' ORDER BY '.$extra['sort'].' '.$extra['direction'].' ';
		}

		return '';
	}

	private function _bindDepositTitle() {
		App::uses('PaymentsComponent', 'Controller'.DS.'Component');
		$items = PaymentsComponent::$items;
		$items['membership']['title'] = 'Account upgrade';

		$virtualstr = "CASE SUBSTRING_INDEX(Deposits.item, '-', 1)";
		foreach($items as $k => $v) {
			$v = __($v['title']);

			switch($k) {
				case 'extend':
				case 'rent':
				case 'recycle':
				case 'referrals':
					$v = "REPLACE('$v', ':refs_no:', SUBSTRING_INDEX(SUBSTRING_INDEX(Deposits.item, '-', 4), '-', -1))";
				break;

				default:
					$v = "'$v'";
			}

			$virtualstr .= " WHEN '$k' THEN $v";
		}
		$virtualstr .= " END";
		return $virtualstr;
	}

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = -1, $extra = array()) {
		$startFrom = ($page - 1) * $limit;
		$fields = $this->_selectFields($fields);

		$sql = '';
		foreach($this->models as $k => $model) {
			$sql .= 'SELECT ';

			foreach($fields[$model->alias] as $label => $field) {
				if($field{0} == "'") {
					$sql .= $field.' AS '.$label.', ';
				} elseif($field{0} == '_') {
					$funcRes = $this->$field();
					$sql .= $funcRes.' AS '.$label.', ';
				} else {
					$sql .= $model->alias.'.'.$field.' AS '.$label.', ';
				}
			}

			$sql = rtrim($sql, ', ');

			$sql .= ' FROM '.$model->tablePrefix.$model->table.' AS '.$model->alias;

			$sql .= $this->_createConditions($model->alias, $conditions);

			$sql .= " UNION ALL ";
		}

		$sql = rtrim($sql, " UNION ALL ");

		$sql .= $this->_createSort($order, $extra);

		$sql .= " LIMIT $startFrom,$limit";
		return $this->query($sql);
	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$this->recursive = $recursive;
		$result = 0;

		foreach($this->models as $model) {
			$condStr = $this->_createConditions($model->alias, $conditions);
			$sql = 'SELECT COUNT(*) as d_count FROM `'.$model->tablePrefix.$model->table.'`'.$condStr;
			$data = $this->query($sql);
			$result += $data[0][0]['d_count'];
		}
		return $result;
	}
}
