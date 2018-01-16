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
App::uses('FormHelper', 'View/Helper');

class AppFormHelper extends FormHelper {
/**
 * Helper dependencies
 *
 * @var array
 */
	private $defaultHelpers = array('Html', 'Js', 'Currency');

/**
 * true if dateTime has already emitted css and js
 *
 *	@var boolean
 */
	private $dateTimeScripts = false;

/**
 * money type field default settings
 *
 * @var array
 */
	protected $moneyDefaults;

	protected $appDefaults = array(
		'inputDefaults' => array(
			'error' => false,
			'div' => false,
			'label' => false,
			'required' => false,
		),
	);

	protected $captchaEmmited;
/**
 * __construct
 *
 * 
 */
	public function __construct(View $View, array $settings = array()) {
		$this->helpers = Hash::merge($this->defaultHelpers, $this->helpers);
		parent::__construct($View, $settings);
	}

/**
 * create method
 *
 * overrided to set default options
 */
	public function create($model = null, $options = array()) {
		if(is_array($model) && empty($options)) {
			$options = $model;
			$model = null;
		}

		$options = Hash::merge($this->appDefaults, $options);

		if(Configure::read('Compatibility.formDisablePUT')) {
			$options['type'] = 'post';
		}

		$this->captchaEmmited = false;

		return parent::create($model, $options);
	}

/**
 * _magicOptions
 *
 * overrided to include money fields
 */
	protected function _magicOptions($options) {
		$r = parent::_magicOptions($options);

		$modelKey = $this->model();
		$fieldKey = $this->field();

		$fieldDef = $this->_introspectModel($modelKey, 'fields', $fieldKey);

		if($fieldDef['type'] == 'decimal' && $fieldDef['length'] == '17,8') {
			$r['type'] = 'money';
			unset($r['step']); /* why it is here? :-) */
		}

		if($fieldDef['type'] == 'decimal' && $fieldDef['length'] == '10,2') {
			$r['type'] = 'points';
		}

		return $r;
	}

/**
 * dateTime method
 *
 * @return string
 */
	public function dateTime($fieldNameOrig, $dateFormat = 'MDY', $timeFormat = '12', $attributes = array()) {
		$attributes += array('empty' => true, 'value' => null, 'secure' => true);
		$modelName = substr($fieldNameOrig, 0, strrpos($fieldNameOrig, '.'));
		$fieldName = ltrim(strstr($fieldNameOrig, '.'), '.');

		if(!$this->dateTimeScripts) {
			$this->Html->script('jquery.datetimepicker', array('inline' => false));
			$this->Html->css('jquery.datetimepicker', array('inline' => false));
			$this->dateTimeScripts = true;
		}

		if(empty($attributes['value'])) {
			$attributes = $this->value($attributes, $fieldNameOrig);
		}

		$defaults = array('minYear' => null, 'maxYear' => null, 'interval' => 1, 'timepicker' => true);

		$attributes = array_merge($defaults, (array)$attributes);
		if(isset($attributes['minuteInterval'])) {
			$attributes['interval'] = $attributes['minuteInterval'];
			unset($attributes['minuteInterval']);
		}

		$minYear = $attributes['minYear'];
		$maxYear = $attributes['maxYear'];
		$interval = $attributes['interval'];
		$timepicker = $attributes['timepicker'];
		$attributes = array_diff_key($attributes, $defaults);

		if((!isset($attributes['id']) || empty($attributes['id']))) {
			$attributes['id'] = uniqid('datetimePicker_');
		}

		$attrs = $this->_initInputField($fieldNameOrig, array_merge(
			(array)$attributes, array('secure' => self::SECURE_SKIP)
		));

		$format = '';
		if($dateFormat != null) {
			$format .= 'Y-m-d';
		}
		if($timeFormat != null) {
			if($format != '') {
				$format .= ' ';
			}
			$format .= 'H:i:s';
		}

		if(!empty($minYear)) {
			$minDate = 'minDate: '."'".$minYear.'-01-01'."',";
		} else {
			$minDate = '';
		}
		if(!empty($maxYear)) {
			$maxDate = 'maxDate: '."'".$maxYear.'-12-31'."',";
		} else {
			$maxDate = '';
		}

		if($timepicker === false) {
			$timepicker = 'timepicker: false,';
		} else {
			$timepicker = '';
		}


		$this->Js->buffer('$(\'#'.$attributes['id'].'\').datetimepicker({'
				."format: '$format',"
				.$maxDate
				.$minDate
				.$timepicker
				."formatDate: 'Y-m-d',"
				."value: '{$attributes['value']}',"
				."step: $interval,"
		.'});');

		if(isset($attributes['class']) && !empty($attributes['class'])) {
			$attributes['class'] = 'class="'.$attributes['class'].'"';
		} else {
			$attributes['class'] = '';
		}

		if(substr($attrs['name'], 0, 4) == 'data') {
			$name = substr($attrs['name'], 4);
		} else {
			$name = $attrs['name'];
		}

		$name = str_replace(array('[', ']'), array('.', '',), $name);
		$this->_secure($attributes['secure'], $name);

		return '<input name="'.$attrs['name'].'" id="'.$attributes['id'].'" '.$attributes['class'].' type="text"/>';
	}

/**
 * booleanRadio
 *
 * @return string
 */
	public function booleanRadio($fieldName, $options = array()) {
		$fields = array(
			'1' => '&nbsp;'.__('Yes').'&nbsp;&nbsp;',
			'0' => '&nbsp;'.__('No'),
		);
		$options = Hash::merge(array('legend' => false, 'hiddenField' => false, 'default' => 0), $options);
		return parent::radio($fieldName, $fields, $options);
	}

/**
 * points
 *
 * @return string
 */
	public function points($fieldName, $options = array()) {
		$default = array(
			'step' => '0.01',
			'min' => '0',
			'max' => '99999999.99',
			'symbol' => 'input-group-addon',
		);
		$options = Hash::merge($default, $options);

		$symbol = $options['symbol'];
		$options['symbol'] = false;

		return $this->money($fieldName, $options).$this->Html->div($symbol, __('PTS'));
	}

/**
 * money
 *
 * @return string
 */
	public function money($fieldName, $options = array()) {
		$result = '';
		$defaults = array(
			'type' => 'number',
			'step' => $this->Currency->defaultStep(),
			'symbol' => false,
			'min' => '0'
		);
		$optionsDef = Hash::merge($defaults, $this->moneyDefaults);
		$options = Hash::merge($optionsDef, $options);

		if(isset($options['readonly']) && $options['readonly']) {
			unset($options['step']);
			unset($options['min']);
			unset($options['max']);
			$options['type'] = 'text';
		} else {
			switch($options['step']) {
				case 'real':
					$options['step'] = $this->Currency->realStep();
				break;

				case 'max':
					$options['step'] = '0.00000001';
				break;

				case 'default':
					$options['step'] = $this->Currency->defaultStep();
				break;
			}
		}

		if($options['symbol']) {
			if(is_array($options['symbol'])) {
				$class = $options['symbol']['class'];
				unset($options['symbol']['class']);
				$symbol = $this->Html->div($class, $this->Currency->formattedSymbol(), $options['symbol']);
			} elseif(is_string($options['symbol'])) {
				$symbol = $this->Html->div($options['symbol'], $this->Currency->formattedSymbol());
			} else {
				$symbol = $this->Currency->formattedSymbol();
			}
		}

		unset($options['symbol']);

		if(isset($symbol) && $this->Currency->symbolPosition() == 'left') {
			$result .= $symbol;
		}

		$result .= $this->input($fieldName, $options);

		if(isset($symbol) && $this->Currency->symbolPosition() == 'right') {
			$result .= $symbol;
		}

		return $result;
	}

/**
 * postLink
 *
 * @return string
 */
	public function postLink($title, $url = null, $options = array(), $confirmMessage = false) {
		if(!isset($options['inline'])) {
			$options['inline'] = false;
		}

		$oldFields = $this->fields;
		$this->fields = array();
		$oldUnlocked = $this->_unlockedFields;
		$this->_unlockedFields = array();
		$oldLastAction = $this->_lastAction;
		$this->_lastAction = array();

		$out = parent::postLink($title, $url, $options, $confirmMessage);

		$this->fields = $oldFields;
		$this->_unlockedFields = $oldUnlocked;
		$this->_lastAction = $oldLastAction;

		return $out;
	}

/**
 * captcha method
 * 
 * @return string
 */
	public function captcha() {
		if($this->captchaEmmited) {
			return '';
		}

		$captcha = $this->request->param('captcha');

		if($captcha) {
			foreach($captcha->usesFields() as $field) {
				$this->unlockField($field);
			}
			$this->captchaEmmited = true;

			return $this->_View->element('captcha', array('captcha' => $captcha->render(), 'name' => $captcha->getName()));
		}

		return '';
	}
}
