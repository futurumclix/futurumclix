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
App::uses('AppHelper', 'View/Helper');

/**
 * NewsHelper
 *
 *
 */

class NewsHelper extends AppHelper {
	public function getNews($limit = 5, $loginAds = false) {
		$newsModel = ClassRegistry::init('News');
		$conditions = array();

		if($loginAds) {
			$conditions['News.show_in_login_ads'] = true;
			$conditions[] = 'News.show_in_login_ads_until >= NOW()';
		}

		$allNews = $newsModel->find('all', array(
			'conditions' => $conditions,
			'limit' => $limit,
			'order' => array('modified' => 'desc'),
		));

		return $allNews;
	}

	public function box($limit = 5, $maxLength = 100, $loginAds = false) {
		$allNews = $this->getNews($limit, $loginAds);

		return $this->_View->element('newsBox', compact('allNews', 'maxLength'));
	}
}
