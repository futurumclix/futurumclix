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
 * FeaturedAdsHelper
 *
 *
 */

class FeaturedAdsHelper extends AppHelper {
/**
 * helpers
 *
 * @var array
 */
	public $helpers = array('Session');

	private $ads = array();

	private function getAds($ads_no) {
		$fads = ClassRegistry::init('FeaturedAd');

		$fads->contain();
		$ads = $fads->find('all', array(
			'fields' => array('id', 'title', 'description'),
			'conditions' => array(
				'FeaturedAd.status' => 'Active',
				'FeaturedAd.expiry >' => 0,
				'CASE WHEN FeaturedAd.package_type = "Clicks" THEN FeaturedAd.clicks < FeaturedAd.expiry
				 WHEN FeaturedAd.package_type = "Impressions" THEN FeaturedAd.impressions < FeaturedAd.expiry ELSE
				 (FeaturedAd.start IS NULL OR DATEDIFF(NOW(), FeaturedAd.start) < FeaturedAd.expiry) END',
			),
			'limit' => $ads_no,
			'order' => 'RAND()',
		));

		$to_update = Hash::extract($ads, '{n}.FeaturedAd.id');

		$this->ads = array_merge($this->ads, $to_update);
		CakeSession::write('FeaturedAds', $this->ads);

		$fads->updateAll(array(
			'FeaturedAd.impressions' => '`FeaturedAd`.`impressions` + 1',
		), array(
			'FeaturedAd.id' => $to_update,
		));

		foreach($to_update as $ad_id) {
			$fads->ImpressionHistory->addClick('FeaturedAd', $ad_id);
		}

		return $ads;
	}

	public function box($ads_no = null) {
		if(!Configure::read('featuredAdsActive')) {
			return '';
		}

		if($ads_no == null) {
			$ads_no = Configure::read('featuredAdsPerBox');
		}

		$ads = $this->getAds($ads_no);

		return $this->_View->element('featuredAdsBox', compact('ads'));
	}
}
