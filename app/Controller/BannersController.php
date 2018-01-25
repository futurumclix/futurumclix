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
 * Banners Controller
 *
 */
class BannersController extends AppController {
/**
 * Models
 *
 * @var uses
 */
	public $uses = array(
		'Banner',
		'Font',
		'User',
	);

/**
 * beforeFilter callback
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('image');
	}

/**
 * image method
 *
 * @throws NotFoundException
 * @param string $id
 * @return CakeResponse
 */
	public function image($id = null, $user = null, $secret = false) {
		$this->Banner->id = $id;

		if(is_string($secret)) {
			$secret = $secret == 'true' ? true : false;
		}

		if(!$this->Banner->read()) {
			throw new NotFoundException(__d('exception', 'Invalid Banner'));
		}

		if($this->Banner->data['Banner']['statistical']) {
			if($user === null) {
				throw new NotFoundException(__d('exception', 'Invalid user'));
			}

			if($secret) {
				$user = $this->loginDecrypt(urldecode($user));
			}

			$this->User->contain('UserStatistic');
			$user = $this->User->findByUsername($user);

			if(empty($user)) {
				throw new NotFoundException(__d('exception', 'Invalid user'));
			}

			$this->Banner->data['Banner']['font_path'] = $this->Font->getPath($this->Banner->data['Banner']['font_name']);

			$path = $this->Banner->getStatisticalPath($user['User']['id'], $this->User->getEarnings($user), 
				$user['UserStatistic']['total_cashouts'], $this->User->UserStatistic->getSiteCashouts());
		} else {
			$path = $this->Banner->getPath();
		}

		$this->response->file($path);

		if(Configure::read('debug') <= 0) {
			$this->response->cache('now', '+12 hours');
			$this->response->expires(new DateTime('+12 hours'));
		}

		return $this->response;
	}

/**
 * admin_settings method
 *
 * @return void
 */
	public function admin_settings() {
		$fonts = $this->Font->getList();

		if($this->request->is('post', 'put')) {
			if(!isset($this->request->data['Banner']['font_name']) || in_array($this->request->data['Banner']['font_name'], $fonts)) {
				if($this->Banner->save($this->request->data)) {
					if(isset($this->request->data['file'])) {
						$this->Notice->success(__d('admin', 'File uploaded successfully'));
					} else {
						$this->Notice->success(__d('admin', 'Banner saved successfully'));
					}
				}
			} else {
				$this->Notice->error(__d('admin', 'Invalid font'));
			}
			unset($this->request->data);
		}

		$banners = $this->Banner->find('all', array(
			'order' => 'id',
		));

		$this->set(compact('banners', 'fonts'));
	}

/**
 * admin_image method
 *
 * @throws NotFoundException
 * @param string $id
 * @return CakeResponse
 */
	public function admin_image($id = null) {
		$this->Banner->id = $id;

		if(!$this->Banner->read()) {
			throw new NotFoundException(__d('exception', 'Invalid Banner'));
		}

		if($this->Banner->data['Banner']['statistical']) {

			$this->Banner->data['Banner']['font_path'] = $this->Font->getPath($this->Banner->data['Banner']['font_name']);
			$banner = $this->Banner->getStatisticalPath('admin', '1234.12345678', '1234.12345678', $this->User->UserStatistic->getSiteCashouts());

			$this->response->file($banner);
		} else {
			$this->response->file($this->Banner->getPath());
		}
		return $this->response;
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->request->allowMethod('post', 'delete');
		$this->Banner->id = $id;
		if(!$this->Banner->read('filename')) {
			throw new NotFoundException(__d('exception', 'Invalid Banner'));
		}
		if($this->Banner->delete()) {
			$this->Notice->success(__d('admin', 'The Banner has been deleted.'));
		} else {
			$this->Notice->error(__d('admin', 'The Banner could not be deleted. Please, try again.'));
		}
		$this->redirect($this->referer());
	}
}
