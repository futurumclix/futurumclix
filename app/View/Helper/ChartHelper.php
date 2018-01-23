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
 * ChartHelper
 *
 *
 */

class ChartHelper extends AppHelper {

/**
 * Helper dependencies
 *
 * @var array
 */
	public $helpers = array('Html', 'Js', 'Time');

/**
 * html tags used by this helper.
 *
 * @var array
 */
	protected $_tags = array(
		'label' => '<div class="chart-heading%s" %s><h6 class="chart-title">%s</h6></div>',
		'placeholder' => '<div class="graph-standard%s" %s></div>',
	);

/**
 * true if HighCharts are installed
 *
 * @var boolean
 */
	private $highChartsInstalled = false;

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
		'id' => '',
		'chart' => array(
			'type' => 'line',
		),
		'title' => false,
		'yAxis' => array(
			'allowDecimals' => false,
			'title' => false,
		),
		'legend' => array(
			'enabled' => false,
		),
		'xAxis' => array(
			'visible' => false,
		),
		'credits' => false,
	);

	private $modeToYAxis, $modeToDate;

/**
 * __construct method
 *
 * @param View $view
 * @param array $settings
 */
	function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);

		if(file_exists(APP.'webroot'.DS.'js'.DS.'charts'.DS.'highcharts.js')) {
			$this->highChartsInstalled = true;
		}

		$this->modeToDate = array(
			'week' => 'D',
			'year' => 'M',
		);

		$this->modeToYAxis = array(
			'week' => array(
				'Sun' => __('Sunday'),
				'Mon' => __('Monday'),
				'Tue' => __('Tuesday'),
				'Wed' => __('Wednesday'),
				'Thu' => __('Thursday'),
				'Fri' => __('Friday'),
				'Sat' => __('Saturday'),
				'Today' => __('Today'),
				'Yesterday' => __('Yesterday'),
			),
			'year' => array(
				'Jan' => __('Jan'),
				'Feb' => __('Feb'),
				'Mar' => __('Mar'),
				'Apr' => __('Apr'),
				'May' => __('May'),
				'Jun' => __('Jun'),
				'Jul' => __('Jul'),
				'Aug' => __('Aug'),
				'Sep' => __('Sep'),
				'Oct' => __('Oct'),
				'Nov' => __('Nov'),
				'Dec' => __('Dec'),
			),
		);
	}

/**
 * emitScripts method
 *
 * @return void
 */
	public function emitScripts() {
		if(!$this->emittedScripts) {
			if($this->highChartsInstalled) {
				$this->Html->script('charts/highcharts.js', array('inline' => false));
			}
			$this->emittedScripts = true;
		}
	}

/**
 * _encodeOptions
 *
 * @param $flatOptions
 * @return string
 */
	private function _encodeOptions($flatOptions) {
		return json_encode($flatOptions, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);
	}

/**
 * _encodeData
 *
 * @param $data array
 * @param $options array
 * @return void
 */
	private function _joinData(&$data, &$options) {
		foreach($data as $k => $v) {
			$options['series'][] = array(	
				'name' => $k,
				'data' => array_values($v),
			);
		}
		if(!isset($options['xAxis']['categories'])) {
			if(isset($options['mode']) && array_key_exists($options['mode'], $this->modeToYAxis)) {
				$dates = array_keys(array_pop($data));

				foreach($dates as $date) {
					if($options['mode'] == 'week') {
						if($this->Time->isToday($date)) {
							$options['xAxis']['categories'][] = $this->modeToYAxis['week']['Today'];
						} elseif($this->Time->wasYesterday($date)) {
							$options['xAxis']['categories'][] = $this->modeToYAxis['week']['Yesterday'];
						} else {
							$options['xAxis']['categories'][] = $this->modeToYAxis['week'][date($this->modeToDate['week'], strtotime($date))];
						}
 					} else {
						$options['xAxis']['categories'][] = $this->modeToYAxis[$options['mode']][date($this->modeToDate[$options['mode']], strtotime($date))];
					}
				}

				unset($options['mode']);
			} else {
				reset($data);
				$options['xAxis']['categories'] = array_keys(array_pop($data));
			}
		}
	}

/**
 * createJs method
 *
 * @param $data array
 * @param $options array
 * @return string
 */
	protected function createJs($data, $options) {
		if($data !== null && !empty($data)) {
			$this->_joinData($data, $options);
		}

		$optionsStr = $this->_encodeOptions($options);

		$res = "$('#{$options['id']}').highcharts($optionsStr)";

		return $res;
	}

/**
 * createDiv method
 *
 * @param $options array
 * @return string
 */
	protected function createDiv($options) {
		$res = '';

		if(isset($options['label'])) {
			if(is_string($options['label'])) {
				$res = sprintf($this->_tags['label'], '', '', $options['label']);
			} elseif(is_array($options['label'])) {
				if(isset($options['label']['content'])) {
					$contents = $options['label']['content'];
					unset($options['label']['content']);
				} else {
					$contents = '';
				}

				if(isset($options['label']['class'])) {
					$class = ' '.$options['label']['class'];
					unset($options['label']['class']);
				} else {
					$class = '';
				}

				if(isset($options['label']['width'])) {
					$width = $options['label']['width'];
					unset($options['label']['width']);
				} elseif(isset($options['width'])) {
					$width = $options['width'];
				} else {
					$width = '100%';
				}

				if(isset($options['label']['style']) && !empty($options['label']['style'])) {
					if(is_array($options['label']['style'])) {
						$options['label']['style'] = $this->Html->style($options['label']['style'], true);
					} 
					if(rtrim(substr($options['label']['style'], -1)) == ';') {
						$options['label']['style'] .= "width:{$width};";
					} else {
						$options['label']['style'] .= ";width:{$width};";
					}
				} else {
					$options['label']['style'] = "width:{$width};";
				}

				$res = sprintf($this->_tags['label'], $class, $this->_parseAttributes($options['label']), $contents);
				unset($options['label']);
			}
		}

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
 * makeContinuousByDay method
 *
 * @param $data array
 * @return void
 */
	protected function makeContinuousByDay(&$data) {
		$end = $start = date('Y-m-d');

		foreach($data as &$v) {
			if(empty($v) || !is_array($v)) {
				continue;
			}

			ksort($v);
			reset($v);

			$s = key($v);
			end($v);
			$e = key($v);
			reset($v);

			if($s < $start) {
				$start = $s;
			}

			if($e > $end) {
				$end = $e;
			}
		}

		$timet = strtotime($start);
		$start = new DateTime($start);
		$end = new DateTime($end);
		$diff = $start->diff($end);

		$days = (int)$diff->format('%a');

		foreach($data as &$v) {
			for($i = 0; $i <= $days; $i++) {
				$date = date('Y-m-d', strtotime("+$i day", $timet));

				if(!isset($v[$date])) {
					$v[$date] = 0;
				}
			}
			ksort($v);
		}
	}

/**
 * show method
 *
 * @param array $data
 * @param array $divOptions
 * @param array $jsOptions
 * @return string
 */
	public function show($data = array(), $divOptions = array(), $jsOptions = array()) {
		$this->emitScripts();

		if(!$this->highChartsInstalled) {
			$style = 'style="display: inline-block;';

			if(isset($divOptions['width'])) {
				$style .= 'width: '.$divOptions['width'].';';
			}

			if(isset($divOptions['height'])) {
				$style .= 'height: '.$divOptions['height'].';';
			}

			$style .= '"';

			return '<div '.$style.'>Please install <a href="https://www.highcharts.com">HighCharts</a> to see the chart.</div>';
		}

		if(isset($divOptions['continuous'])) {
			if($divOptions['continuous'] == 'day' || $divOptions['continuous'] == true) {
				$this->makeContinuousByDay($data);
			}
			unset($divOptions['continuous']);
		}

		$jsOptions = Hash::merge($this->defaultOptions, $jsOptions);

		if(!isset($divOptions['id']) || empty($divOptions['id'])) {
			$divOptions['id'] = uniqid('chart_');
		}

		$jsOptions['id'] = $divOptions['id'];

		if(!isset($jsOptions['mode']) && isset($divOptions['mode'])) {
			$jsOptions['mode'] = $divOptions['mode'];
		}

		$js = $this->createJs($data, $jsOptions);

		$this->Js->buffer($js);

		return $this->createDiv($divOptions);
	}
}
