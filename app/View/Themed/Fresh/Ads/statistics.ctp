<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Advertisement Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'ads', 'action' => 'add'),
				'buy' => array('controller' => 'ads_category_packages', 'action' => 'buy'),
				'assign' => array('controller' => 'ads', 'action' => 'assign'),
				))
				?>
			<h2 class="uk-margin-top">
			<?=__('Clicks statistics for: "%s".', $ad['Ad']['title'])?></h5>
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
			<?php if($ad['AdsCategory']['geo_targetting']): ?>
			<h2 class="uk-margin-top">
			<?=__('Geographic statistics for: "%s".', $ad['Ad']['title'])?></h5>
			<?=$this->Map->show($geo, array('height' => '400px'))?>
			<?php endif ?>
		</div>
	</div>
</div>
