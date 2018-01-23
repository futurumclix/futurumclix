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

/**
 * Class PaymentsInflector
 *
 * @method static string humanize($word)
 * @method static string classify($word)
 * @method static string tableize($word)
 * @method static string underscore($word)
 */
class PaymentsInflector {
	private static $exceptions = array(
		'humanize' => array(
			'pay_pal' => 'PayPal',
			'manual_pay_pal' => 'Manual PayPal',
			'solid_trust_pay' => 'SolidTrust Pay',
			'okpay' => 'OKPAY',
			'cubits' => 'Cubits',
			'blockio' => 'Block.io',
			'block.io' => 'Block.io',
		),
		'classify' => array(
			'coinpayments' => 'Coinpayments',
			'okpay' => 'OKPAY',
			'cubits' => 'Cubits',
			'blockio' => 'Blockio',
			'block.io' => 'Blockio',
		),
		'tableize' => array(
			'okpay' => 'okpay',
			'cubits' => 'cubits',
			'blockio' => 'blockio',
			'block.io' => 'blockio',
		),
		'underscore' => array(
			'okpay' => 'okpay',
			'cubtis' => 'cubits',
			'blockio' => 'blockio',
			'block.io' => 'blockio',
		),
	);

	public static function __callStatic($name, $arguments) {
		$arg = reset($arguments);

		if($arg == 'OKPAY') {
			$arg = 'okpay';
		} else { 
			@$arg = Inflector::underscore($arg);
		}

		if(isset(self::$exceptions[strtolower($name)][$arg])) {
			return self::$exceptions[strtolower($name)][$arg];
		}
		return call_user_func_array(array('Inflector', $name), $arguments);
	}
}
