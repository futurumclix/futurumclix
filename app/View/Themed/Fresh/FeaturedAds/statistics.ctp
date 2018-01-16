<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Featured Ads Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'featured_ads', 'action' => 'add'),
				'buy' => array('controller' => 'featured_ads', 'action' => 'buy'),
				'assign' => array('controller' => 'featured_ads', 'action' => 'assign'),
				))
				?>
			<h2 class="uk-margin-top"><?=__('Clicks statistics for: "%s".', $ad['FeaturedAd']['title'])?></h2>
			<?=
				$this->Chart->show(array(
				__('Clicks') => $clicks,
				__('Impressions') => $impressions,
				),
				array('continous' => 'day'),
				array(
				'colors' => array('#2ecc71','#9b59b6'),
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
		</div>
	</div>
</div>
