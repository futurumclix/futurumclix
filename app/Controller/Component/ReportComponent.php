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
App::uses('Component', 'Controller');

/**
 * ReportComponent
 *
 *
 */
class ReportComponent extends Component {

	private $ItemReport;

	public function startup(Controller $controller) {
		parent::startup($controller);

		$this->ItemReport = ClassRegistry::init('ItemReport');
	}

	public function reportItem($type, Model $model, $item_id = null, $reason = null, $reporter_id = null) {
		if(!$this->ItemReport->enum('type', $type)) {
			throw new InvalidArgumentException(__d('exception', 'Invalid item report type'));
		}

		if(!$item_id) {
			$item_id = $model->id;
		}

		if(!$reporter_id) {
			$reporter_id = $this->Auth->user('id');
		}

		$model->id = $item_id;
		if(!$model->read(array($model->displayField))) {
			throw new NotFoundException(__d('exception', 'Item not found'));
		}

		$data = array(
			'reporter_id' => $reporter_id,
			'foreign_key' => $item_id,
			'type' => $type,
			'model' => ($model->plugin ? $model->plugin . '.' : '') . $model->name,
			'reason' => $reason,
			'item' => $model->data[$model->alias][$model->displayField],
		);

		return $this->ItemReport->reportItem($data);
	}

	public function getModelActionsList($modelName, $domain = null) {
		$result = array();
		$model = ClassRegistry::init($modelName);

		list($plugin, $modelName) = pluginSplit($modelName);

		if($domain === null) {
			if($plugin !== null) {
				$domain = Inflector::underscore($plugin);
			} else {
				$domain = 'default';
			}
		}

		if(isset($model->reportActions)) {
			$actions = array_keys($model->reportActions);

			foreach($actions as &$v) {
				$result[$v] = __d($domain, Inflector::humanize($v));
			}
		}

		return $result;
	}

	public function getModelViewURL($modelName, $foreign_key) {
		$result = array('action' => 'edit', $foreign_key);
		$model = ClassRegistry::init($modelName);

		if(!$model->exists($foreign_key)) {
			return null;
		}

		list($plugin, $modelName) = pluginSplit($modelName);

		$result['plugin'] = strtolower($plugin);
		$result['controller'] = Inflector::tableize($modelName);

		if(isset($model->reportViewURL) && !empty($model->reportViewURL)) {
			if(is_callable(array($model, $model->reportViewURL))) {
				$action = $model->reportViewURL;
				return $model->$action($foreign_key);
			} else {
				$result = Hash::merge($result, $model->reportViewURL);
			}
		}

		return $result;
	}

}
