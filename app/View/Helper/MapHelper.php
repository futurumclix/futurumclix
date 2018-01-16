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
 * MapHelper
 *
 *
 */

class MapHelper extends AppHelper {

/**
 * Helper dependencies
 *
 * @var array
 */
	public $helpers = array('Html', 'Js');

/**
 * html tags used by this helper.
 *
 * @var array
 */
	protected $_tags = array(
		'placeholder' => '<div class="map-standard%s" %s></div>',
	);

/**
 * true if ChartHelper has already emitted js
 *
 *	@var boolean
 */
	private $emittedScripts = false;

/**
 * defaultOptions
 *
 * @var array
 */
	private $defaultOptions = array(
	);

/**
 * emitScripts method
 *
 * @return void
 */
	public function emitScripts() {
		if(!$this->emittedScripts) {
			$this->Html->css('jqvmap', array('inline' => false, 'media' => 'screen'));
			$this->Html->script('jquery.vmap.min', array('inline' => false));
			$this->Html->script('jquery.vmap.world', array('inline' => false, 'charset' => 'utf-8'));
			$this->emittedScripts = true;
		}
	}

/**
 * _encodeOptions
 *
 * @return string
 */
	private function _encodeOptions($flotOptions) {
		return json_encode($flotOptions, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
	}

/**
 * _joinData
 *
 * @return void
 */
	private function _joinData(&$data, &$options) {
		$options['values'] = &$data;
	}

/**
 * createJs method
 *
 * @return string
 */
	protected function createJs($data, $options) {
		if($data !== null && !empty($data)) {
			$this->_joinData($data, $options);
		}

		$id = $options['id'];
		unset($options['id']);

		$optionsStr = $this->_encodeOptions($options);

		$res = "var $id = $('#$id').vectorMap($optionsStr)";

		return $res;
	}

/**
 * createDiv method
 *
 * @return string
 */
	protected function createDiv($options) {
		$res = '';

		if(isset($options['class'])) {
			$class = ' '.$options['class'];
			unset($options['class']);
		} else {
			$class = '';
		}

		if(isset($options['style']) && !empty($options['style'])) {
			if(is_array($options['style'])) {
				$options['style'] = $this->Html->style($options['style'], true);
			} 
			if(rtrim(substr($options['style'], -1)) == ';') {
				$options['style'] .= "width:{$options['width']};height:{$options['height']};";
			} else {
				$options['style'] .= ";width:{$options['width']};height:{$options['height']};";
			}
		} else {
			$options['style'] = '';
			if(isset($options['width'])) {
				$options['style'] .= "width:{$options['width']};";
			}
			if(isset($options['height'])) {
				$options['style'] .= "height:{$options['height']};";
			}
		}

		$res .= sprintf($this->_tags['placeholder'], $class, $this->_parseAttributes($options));

		return $res;
	}

/**
 * show method
 *
 * @return string
 */
	public function show($data = array(), $divOptions = array(), $jsOptions = array()) {
		$this->emitScripts();

		$jsOptions = Hash::merge($this->defaultOptions, $jsOptions);

		if(!isset($divOptions['id']) || empty($divOptions['id'])) {
			$divOptions['id'] = uniqid('map_');
		}

		$jsOptions['id'] = $divOptions['id'];

		$js = $this->createJs($data, $jsOptions);

		$this->Js->buffer($js);

		$this->Js->buffer("
			$('#{$divOptions['id']}').bind('labelShow.jqvmap', function(event, label, code) {
				var visits = {$divOptions['id']}.values[code];
				if(!visits) {
					visits = 0;
				}
				label.append(': ' + visits);
			});
		");


		return $this->createDiv($divOptions);
	}
}
