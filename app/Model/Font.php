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
App::uses('AppModel', 'Model');
App::uses('Folder', 'Utility');
define('FONTS_DIRECTORY', APP.'Media'.DS.'Fonts'.DS);
/**
 * Font Model
 *
 */
class Font extends AppModel {
	public $useTable = false;

	private $list = array();

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$path = FONTS_DIRECTORY;

		$dir = new Folder($path);
		$fontsFiles = $dir->find('.*\.ttf', false);

		foreach($fontsFiles as $file) {
			$name = substr($file, 0, -strlen('.ttf'));
			$this->list[$name] = $name;
		}
	}

	public function getList() {
		return $this->list;
	}

	public function getPath($fontName) {
		return FONTS_DIRECTORY.$fontName.'.ttf';
	}
}
