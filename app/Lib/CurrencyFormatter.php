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
class CurrencyFormatter {
	static protected $currency;
	static protected $commaPlaces;
	static protected $useSpace;
	static protected $symbolPosition;
	static protected $decimalPoint = '.';
	static protected $cutTrialing;
	static protected $defaultStep;
	static protected $formatOptions;
	static protected $cutTrailing;
	static protected $defaultFormatOptions = array(
		'zeroValue' => false,
	);

	public static function init() {
		$symbol = Configure::read('currencySymbol');

		self::$currency = ClassRegistry::init('Currency')->findByCode(Configure::read('siteCurrency'));
		self::$currency = self::$currency['Currency'];

		self::$commaPlaces = Configure::read('commaPlaces');
		self::$useSpace = isset($symbol{1}) ? true : false;
		self::$symbolPosition = $symbol{0};
		self::$cutTrailing = Configure::read('cutTrailing');
		if(self::$commaPlaces) {
			self::$defaultStep = '0.'.str_repeat('0', self::$commaPlaces - 1).'1';
		} else {
			self::$defaultStep = '0.00000001';
		}
	}

	public static function round($value, $precision = 0) {
		$pos = strpos($value, '.');

		if($pos !== false && strlen($value) - $pos - 1 > $precision) {
			$toAdd = '0.'.str_repeat('0', $precision).'5';
			return bcadd($value, $toAdd, $precision);
		}

		return $value;
	}

	public static function symbolPosition() {
		if(self::$symbolPosition == 'l') {
			return 'left';
		} else {
			return 'right';
		}
	}

	public static function formattedSymbol() {
		$res = '';

		if(self::$useSpace && self::$symbolPosition == 'r') {
			$res .= ' ';
		}

		$res .= self::$currency['symbol'];

		if(self::$useSpace && self::$symbolPosition == 'l') {
			$res .= ' ';
		}

		return $res;
	}

	public static function realStep() {
		return self::$currency['step'];
	}

	public static function realCommaPlaces() {
		$v = explode('.', self::$currency['step']);
		return strlen(rtrim($v[1], '0'));
	}

	public static function defaultStep() {
		return self::$defaultStep;
	}

	public static function name() {
		return self::$currency['name'];
	}

	public static function cutTrailingZeros($v) {
		if(strpos($v, '.') !== false) {
			$v = explode('.', $v);

			$v[1] = rtrim($v[1], '0');

			if(strlen($v[1])) {
				return $v[0].'.'.$v[1];
			} else {
				return $v[0];
			}
		} else {
			return $v;
		}
	}

	public static function format($value, $options = array()) {
		if($value === null) {
			$value = 0;
		}

		$res = '';
		$v = self::round($value, self::$commaPlaces);

		$options = Hash::merge(self::$defaultFormatOptions, $options);

		if(is_string($options['zeroValue'])) {
			if(bccomp($v, '0') == 0) {
				return $options['zeroValue'];
			}
		}

		if(self::$symbolPosition == 'l') {
			$res .= self::$currency['symbol'];
			if(self::$useSpace) {
				$res .= ' ';
			}
		}

		if(strpos($v, '.') !== false) {
			$v = explode('.', $v);

			if(self::$cutTrailing) {
				$v[1] = rtrim($v[1], '0');
			};

			if(strlen($v[1])) {
				$res .= $v[0].self::$decimalPoint.$v[1];
			} else {
				$res .= $v[0];
			}
		} else {
			$res .= $v;
		}

		if(self::$symbolPosition == 'r') {
			if(self::$useSpace) {
				$res .= ' ';
			}
			$res .= self::$currency['symbol'];
		}

		return $res;
	}

	public static function getSettings() {
		return array(
			'commaPlaces' => self::$commaPlaces,
			'realCommaPlaces' => self::realCommaPlaces(),
			'symbolPosition' => self::symbolPosition(),
			'formattedSymbol' => self::formattedSymbol(),
			'cutTrailing' => self::$cutTrailing,
			'decimalPoint' => self::$decimalPoint,
		);
	}
}
