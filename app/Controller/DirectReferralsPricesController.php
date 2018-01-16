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
App::uses('AppController', 'Controller');
/**
 * DirectReferralsPrices Controller
 *
 * @property DirectReferralsPrice $DirectReferralsPrice
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 */
class DirectReferralsPricesController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Notice');

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->DirectReferralsPrice->id = $id;
		if(!$this->DirectReferralsPrice->exists()) {
			throw new NotFoundException(__d('exception', 'Invalid direct referrals price'));
		}
		$this->request->allowMethod('post', 'delete');
		if($this->DirectReferralsPrice->delete()) {
			$this->Notice->success(__d('admin', 'The direct referrals price has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The direct referrals price could not be deleted. Please, try again.'));
		}
		return $this->redirect($this->referer());
	}
}
