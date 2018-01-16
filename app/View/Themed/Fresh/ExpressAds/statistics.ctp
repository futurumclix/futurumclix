<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Express Advertisements Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'express_ads', 'action' => 'add'),
				'buy' => array('controller' => 'express_ads', 'action' => 'buy'),
				'assign' => array('controller' => 'express_ads', 'action' => 'assign'),
				))
				?>
			<h2 class="uk-margin-top"><?=__('Clicks statistics for: "%s".', $ad['ExpressAd']['title'])?></h2>
			<?=
				$this->Chart->show(array(
				__('Clicks') => $clicks,
				),
				array('continous' => 'day'),
				array(
				'colors' => array('#2ecc71'),
				'xAxis' => array(
				'visible' => true,
				'lineColor' => '#ffffff', 
				'tickWidth' => '0', 
				'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '12px', 'font-weight' => '700', 'font-family' => 'Roboto')),
				),
				'yAxis' => array(
				'gridLineColor' => '#ffffff', 
				'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '14px', 'font-weight' => '700', 'font-family' => 'Roboto')),
				),
				)
				)
				?>
			<?php if($geo_targetting): ?>
			<h2 class="uk-margin-top"><?=__('Geographic statistics for: "%s".', $ad['ExpressAd']['title'])?></h2>
			<?=$this->Map->show($geo, array('height' => '400px'))?>
			<?php endif ?>
		</div>
	</div>
</div>
