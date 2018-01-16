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
define('BANNERS_DIRECTORY', APP.'Media'.DS.'Banners'.DS);
define('BANNERS_CACHE_DIRECTORY', APP.'Media'.DS.'Banners'.DS.'Cache'.DS);
/**
 * Banner Model
 *
 */
class Banner extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'filename';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'file' => array(
			'extension' => array(
				'rule' => array('extension', array('jpg', 'jpeg', 'png', 'gif')),
				'message' => 'Please supply a valid image',
			),
			'mimeType' => array(
				'rule' => array('mimeType', array('image/gif', 'image/png','image/jpg','image/jpeg')),
				'message' => 'Invalid file, only images allowed (gif, png, jpg)',
			),
			'fileSize' => array(
				'rule' => array('fileSize', '<=', '2MB'),
				'message' => 'Image must be less than 2MB',
			),
			'error' => array(
				'rule' => 'uploadError',
				'message' => 'Something went wrong with upload. Please try again.',
			),
			'exists' => array(
				'rule' => 'fileExists',
				'message' => 'File with this name already exists',
			),
			'finishUpload' => array(
				'rule' => 'finishUpload',
				'message' => 'Something went wrong with upload. Please try again.',
				'last' => true,
			),
		),
		'remote' => array(
			'url' => array(
				'rule' => array('url', true),
				'message' => 'Please supply a valid URL to image file',
				'allowEmpty' => true,
			),
			'download' => array(
				'rule' => 'download',
				'message' => 'Failed to download given file',
				'last' => true,
			),
		),
		'filename' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Something went wrong with upload. Please try again.',
				'allowEmpty' => false,
			),
		),
		'statistical' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'allowEmpty' => false,
			),
		),
		'user_paid' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'allowEmpty' => false,
			),
		),
		'user_earned' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'allowEmpty' => false,
			),
		),
		'site_paid' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'allowEmpty' => false,
			),
		),
		'font_name' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 50),
				'message' => 'Font name have to be shorter than 50 characters',
				'allowEmpty' => false,
			),
		),
		'font_color' => array(
			'hex' => array(
				'rule' => '/([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/',
				'message' => 'Font color should be in format RRGGBB',
			),
		),
		'font_size' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Font size should be a numeric value',
			)
		),
		'user_paid_x' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Coordinate should be a numeric value',
			)
		),
		'user_paid_y' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Coordinate should be a numeric value',
			)
		),
		'user_earned_x' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Coordinate should be a numeric value',
			)
		),
		'user_earned_y' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Coordinate should be a numeric value',
			)
		),
		'site_paid_x' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Coordinate should be a numeric value',
			)
		),
		'site_paid_y' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'message' => 'Coordinate should be a numeric value',
			)
		),
	);

	public function beforeValidate($options = array()) {
		if(isset($this->data[$this->alias]['remote']) && !empty($this->data[$this->alias]['remote'])) {
			unset($this->validate['file']);
		} elseif(isset($this->data[$this->alias]['file'])) {
			unset($this->validate['remote']);
		}
		return true;
	}

	public function afterDelete() {
		$file = new File(BANNERS_DIRECTORY.$this->data[$this->alias]['filename']);
		$file->delete();
		$file->close();
	}

	public function download($check = array()) {
		$data = pathinfo($check['remote']);

		if(empty($data['filename'])) {
			$data['filename'] = $this->createRandomStr(20);
		}

		$tmppath = TMP.$data['filename'];

		if(file_exists($tmppath)) {
			return false;
		}

		if(!copy($check['remote'], $tmppath)) {
			return false;
		}

		switch($this->getImageMimeType($tmppath)) {
			case 'image/jpeg':
			case 'image/jpg':
				$extension = '.jpg';
			break;

			case 'image/gif':
				$extension = '.gif';
			break;

			case 'image/png':
				$extension = '.png';
			break;

			default:
				return false;
		}

		$dstpath = BANNERS_DIRECTORY.$data['filename'].$extension;

		if(file_exists($dstpath)) {
			return false;
		}

		if(!rename($tmppath, $dstpath)) {
			unlink($tmppath);
			return false;
		}

		$this->data[$this->alias]['filename'] = $data['filename'].$extension;

		return true;
	}

	public function finishUpload($check = array()) {
		if(!empty($check['file']['tmp_name'])) {
			if(is_uploaded_file($check['file']['tmp_name'])) {

				$this->data[$this->alias]['filename'] = $check['file']['name'];

				if(move_uploaded_file($check['file']['tmp_name'], BANNERS_DIRECTORY.$this->data[$this->alias]['filename'])) {
					return true;
				}
			}
		}
		return false;
	}

	public function fileExists($check = array()) {
		return !file_exists(BANNERS_DIRECTORY.$check['file']['name']);
	}

	public function getPath($filename = null) {
		if($filename === null) {
			if(!isset($this->data[$this->alias]['filename'])) {
				if(!$this->read('filename')) {
					throw new NotFoundException(__d('exception', 'Banner not found'));
				}
			}
			$filename = $this->data[$this->alias]['filename'];
		}
		return BANNERS_DIRECTORY.$filename;
	}

	public function getStatisticalPath($user_id, $user_earn, $user_paid, $site_paid) {
		$path = BANNERS_CACHE_DIRECTORY.$this->data[$this->alias]['id'].'_'.$user_id.'.png';

		if(!file_exists($path) || time() - filemtime($path) > 12 * 60 * 60 || strtotime($this->data[$this->alias]['modified']) >= filemtime($path)
		 || strtotime(Configure::read('currencyChangeDate')) >= filemtime($path)) {
			$image = $this->loadImage($this->getPath());
			$tcolor = $this->hex2rgb($this->data[$this->alias]['font_color']);
			$color = imagecolorallocate($image, $tcolor['r'], $tcolor['g'], $tcolor['b']);
			$font_size = $this->data[$this->alias]['font_size'];
			$font_path = $this->data[$this->alias]['font_path'];

			if($color === false) {
				throw new InternalErrorException(__d('exception', 'Failed to allocate color'));
			}

			$attrs = array(
				'user_paid' => __('I already got paid: %s', CurrencyFormatter::format($user_paid)),
				'user_earned' => __('I already earned: %s', CurrencyFormatter::format($user_earn)),
				'site_paid' => __('Site paid: %s', CurrencyFormatter::format($site_paid)),
			);

			foreach($attrs as $attr => $txt) {
				if($this->data[$this->alias][$attr]) {
					$res = imagettftext($image, $font_size, 0, $this->data[$this->alias][$attr.'_x'], $this->data[$this->alias][$attr.'_y'],
						$color, $font_path, $txt);

					if($res === false) {
						throw new InternalErrorException(__d('exception', 'Failed to draw text'));
					}
				}
			}

			if(!imagepng($image, $path)) {
				throw new InternalErrorException(__d('exception', 'Failed to save image'));
			}

			imagedestroy($image);
		}

		return $path;
	}

	protected function getImageMimeType($path) {
		$data = getimagesize($path);
		return $data['mime'];
	}

	protected function loadImage($path) {
		$res = false;

		switch($this->getImageMimeType($path)) {
			case 'image/jpeg':
			case 'image/jpg':
				$res = imagecreatefromjpeg($path);
			break;

			case 'image/gif':
				$res = imagecreatefromgif($path);
			break;

			case 'image/png':
				$res = imagecreatefrompng($path);
			break;
		}

		if($res === false) {
			throw new InternalErrorException(__d('exception', 'Failed to load image %s', $path));
		}

		return $res;
	}

	protected function hex2rgb($hex) {
		$hex = str_replace('#', '', $hex);
		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
			$g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
			$b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
		} else {
			$r = hexdec(substr($hex, 0, 2));
			$g = hexdec(substr($hex, 2, 2));
			$b = hexdec(substr($hex, 4, 2));
		}
		return array('r' => $r, 'g' => $g, 'b' => $b);
	}

}
