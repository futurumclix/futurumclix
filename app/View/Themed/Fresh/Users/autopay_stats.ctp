<button class="uk-modal-close-default" type="button" uk-close></button>
<div class="uk-modal-header">
	<h2 class="uk-modal-title" id="myModalLabel"><?=__('AutoPay Statistics')?></h2>
</div>
<div class="uk-modal-body">
	<?php
		echo $this->Chart->show(array(
			__('Amount') => $data,
			),
		array(
			'label' => array(
				'content' => false,
				),
			'continous' => 'day',
			'width' => '550px',
			'height' => '200px'),
		array(
			'colors' => array('#9b59b6'), 
			'xAxis' => array('visible' => true, 'lineColor' => '#ffffff', 'tickWidth' => '0' , 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '10px', 'font-weight' => '700', 'font-family' => 'Roboto'))),
			'yAxis' => array('gridLineColor' => '#ffffff', 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '14px', 'font-weight' => '700', 'font-family' => 'Roboto'))),
			)
		)
		?>
</div>
