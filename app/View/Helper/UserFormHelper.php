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
App::uses('AppFormHelper', 'View/Helper');
App::uses('PaymentsInflector', 'Payments');

/**
 * UserFormHelper
 *
 *
 */

class UserFormHelper extends AppFormHelper {
/**
 * getGatewaysButtons
 *
 * @param array $gateways
 * @param array $options
 * @return string
 */
	public function getGatewaysButtons($gateways, $options = array()) {
		$result = array();
		$i = 0;
		$prices = isset($options['prices']) ? $options['prices'] : false;

		unset($options['prices']);

		foreach($gateways as $k => $v) {
			$result[] = array(
				'gateway' => PaymentsInflector::classify(PaymentsInflector::underscore($v)),
				'name' => 'gateway',
				'price' => $prices[$k],
				'id' => 'gatewayButton'.$i++,
			);
		}

		return $this->_View->element('userGatewayButtons', array('gateways' => $result));
	}

/**
 * getAvailableTranslations method
 *
 * @return array
 */
	public function getAvailableTranslations() {
		App::uses('L10n', 'I18n');
		$l10n = new L10n();

		$res = array();
		$paths = App::path('Locale');
		$dir = new Folder(reset($paths));
		$content = $dir->read();
		$content = $content[0];

		foreach($content as $loc) {
			$cat = $l10n->catalog($loc);
			$res[$loc] = __($cat['language']);
		}

		$res['en'] = __('English');

		return $res;
	}

/**
 * end method
 *
 * @param null $options
 * @param array $secureAttributes
 * @return string
 */
	public function end($options = null, $secureAttributes = array()) {
		return $this->captcha().parent::end($options, $secureAttributes);
	}
}
