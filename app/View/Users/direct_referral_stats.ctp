<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h5 class="modal-title" id="myModalLabel"><?=__('%s clicks', h($refname))?></h5>
    </div>
<div class="modal-body">
<?php
	echo $this->Chart->show(array(
			__('%s clicks', h($refname)) => $drclicks,
			__('%s clicks credited', h($refname)) => $drclicksCredited
		),
		array(
			'continous' => 'day',
			'label' => array(
				'content' => false,
			),
			'width' => '550px',
			'height' => '200px'),
			array(
				'colors' => array('#e74c3c'), 
				'xAxis' => array('visible' => true, 'lineColor' => '#ffffff', 'tickWidth' => '0' , 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '10px', 'font-weight' => '700', 'font-family' => 'Lato'))),
				'yAxis' => array('gridLineColor' => '#ffffff', 'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '14px', 'font-weight' => '700', 'font-family' => 'Lato'))),
		)
	)
?>
</div>
