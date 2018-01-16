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
App::uses('Module', 'Lib');

class ModulesController extends AppController {
/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$modules = Module::getAll();
		$this->set(compact('modules'));
	}

/**
 * admin_activate
 *
 * @return void
 */
	public function admin_activate($name = null) {
		$res = Module::install($name);
		if($res === true) {
			$this->Notice->success(__d('admin', 'Module activated sucessfully.'));
		} elseif(is_string($res)) {
			$this->Notice->error($res);
		} else {
			$this->Notice->error(__d('admin', 'Failed to install module. Please, try again later.'));
		}
		return $this->redirect($this->referer());
	}

/**
 * admin_deactivate
 *
 * @return void
 */
	public function admin_deactivate($name = null) {
		if(Module::deactivate($name)) {
			$this->Notice->success(__d('admin', 'Module deactivated sucessfully.'));
		} else {
			$this->Notice->error(__d('admin', 'Failed to install module. Please, try again later.'));
		}
		return $this->redirect($this->referer());
	}
}
