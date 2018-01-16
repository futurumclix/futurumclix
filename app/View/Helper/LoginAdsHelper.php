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
 * LoginAdsHelper
 *
 *
 */

class LoginAdsHelper extends AppHelper {
/**
 * helpers
 *
 * @var array
 */
	public $helpers = array(
		'Session',
		'Html',
		'Js',
		'News',
	);

	private $ads = array();
	private $bannerSize = null;

	private function getAds($ads_no) {
		$fads = ClassRegistry::init('LoginAd');

		$fads->contain();
		$ads = $fads->find('all', array(
			'conditions' => array(
				'LoginAd.status' => 'Active',
				'LoginAd.expiry >' => 0,
				'CASE WHEN LoginAd.package_type = "Clicks" THEN LoginAd.clicks < LoginAd.expiry
				 ELSE (LoginAd.start IS NULL OR DATEDIFF(NOW(), LoginAd.start) < LoginAd.expiry) END',
			),
			'limit' => $ads_no,
			'order' => 'RAND()',
		));

		$to_update = Hash::extract($ads, '{n}.LoginAd.id');

		$this->ads = array_merge($this->ads, $to_update);
		CakeSession::write('LoginAds', $this->ads);

		return $ads;
	}

	public function box($ads_no = null, $imageOptions = array(), $linkOptions = array()) {
		if(!Configure::read('loginAdsActive') || !CakeSession::read('showLoginAds')) {
			return '';
		}

		if($ads_no == null) {
			$ads_no = ClassRegistry::init('Settings')->fetchOne('loginAdsPerBox');
		}

		$ads = $this->getAds($ads_no);

		$news = $this->News->getNews(1, true);
		$news = reset($news);

		if(empty($ads) && empty($news)) {
			return '';
		}

		$this->Html->script('futurumclix.js', array('inline' => false));
		$this->Js->buffer("
			showModal('LoginAds');
		");

		CakeSession::delete('showLoginAds');

		if(!$this->bannerSize) {
			$this->bannerSize = ClassRegistry::init('Settings')->fetchOne('loginAdsSize');
		}

		$imageOptions = Hash::merge(array(
			'width' => $this->bannerSize['width'],
			'height' => $this->bannerSize['height'],
			'target' => 'blank',
		), $imageOptions);

		$linkOptions = Hash::merge(array(
			'target' => 'blank',
			'escape' => false,
		), $linkOptions);

		return $this->_View->element('loginAdsBox', compact('ads', 'imageOptions', 'linkOptions'));
	}
}
