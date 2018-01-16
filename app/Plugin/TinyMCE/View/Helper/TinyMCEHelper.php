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
 * TinyMCE Helper
 *
 */
class TinyMCEHelper extends AppHelper {
/**
 * Helpers
 *
 * @var array
 */
	public $helpers = array(
		'Html',
		'Js',
	);

/**
 * default options for TinyMCE
 *
 * @var array
 */
	protected $defaultOptions = array(
		'mode' => 'textareas',
		'height' => '500px',
		'plugins' => 'code advlist anchor image imagetools insertdatetime lists media paste preview table textcolor',
	);

/**
 * true if helper has already emitted js
 *
 *	@var boolean
 */
	private $emittedScripts = false;

/**
 * _encodeOptions
 *
 * @return string
 */
	private function _encodeOptions($options) {
		return json_encode($options, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
	}

/**
 * emitScripts method
 *
 * @return void
 */
	protected function emitScripts() {
		if(!$this->emittedScripts) {
			$this->Html->script('/TinyMCE/js/tinymce/tinymce.min.js', array('inline' => false));
			$this->emittedScripts = true;
		}
	}

/**
 * editor method
 *
 * @return string
 */
	public function editor($options = array()) {
		$this->emitScripts();

		$optionsStr = $this->_encodeOptions(Hash::merge($this->defaultOptions, $options));

		$this->Js->buffer("tinymce.init($optionsStr)");
	}
}
