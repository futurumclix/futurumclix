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
 * BannerAdsHelper
 *
 *
 */

class BannerAdsHelper extends AppHelper {
/**
 * helpers
 *
 * @var array
 */
	public $helpers = array(
		'Session',
		'Html',
	);

	private $ads = array();
	private $bannerSize = null;

	private function getAd() {
		$fads = ClassRegistry::init('BannerAd');

		$fads->contain();
		$ad = $fads->find('first', array(
			'fields' => array('id', 'title', 'image_url'),
			'conditions' => array(
				'BannerAd.status' => 'Active',
				'BannerAd.expiry >' => 0,
				'CASE WHEN BannerAd.package_type = "Clicks" THEN BannerAd.clicks < BannerAd.expiry
				 WHEN BannerAd.package_type = "Impressions" THEN BannerAd.impressions < BannerAd.expiry ELSE
				 (BannerAd.start IS NULL OR DATEDIFF(NOW(), BannerAd.start) < BannerAd.expiry) END',
			),
			'order' => 'RAND()',
		));

		if(empty($ad)) {
			return null;
		}

		$this->ads[] = $ad['BannerAd']['id'];
		CakeSession::write('BannerAds', $this->ads);

		$fads->updateAll(array(
			'BannerAd.impressions' => '`BannerAd`.`impressions` + 1',
		), array(
			'BannerAd.id' => $ad['BannerAd']['id'],
		));

		$fads->ImpressionHistory->addClick('BannerAd', $ad['BannerAd']['id']);

		return $ad;
	}

	public function show($imageOptions = array(), $linkOptions = array()) {
		if(!Configure::read('bannerAdsActive')) {
			return '';
		}

		if(!$this->bannerSize) {
			$this->bannerSize = ClassRegistry::init('Settings')->fetchOne('bannerAdsSize');
		}

		$ad = $this->getAd();

		if($ad === null) {
			return '';
		}

		$imageOptions = Hash::merge(array(
			'title' => $ad['BannerAd']['title'],
			'alt' => $ad['BannerAd']['title'],
			'width' => $this->bannerSize['width'],
			'height' => $this->bannerSize['height'],
			'target' => 'blank',
		), $imageOptions);

		$image = $this->Html->image($ad['BannerAd']['image_url'], $imageOptions);

		$linkOptions = Hash::merge(array(
			'target' => 'blank',
			'escape' => false,
		), $linkOptions);

		return $this->Html->link($image, array('plugin' => '', 'controller' => 'banner_ads', 'action' => 'view', $ad['BannerAd']['id']), $linkOptions);
	}
}
