<?=$this->element('userSidebar')?>
<div class="page">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 padding30-sides">
				<?=$this->element('userBreadcrumbs')?>
				<?=$this->Notice->show()?>
				<div class="panel">
					<div class="padding30-col">
						<div class="col-md-12">
							<h5><?=__('Express Advertisements Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('controller' => 'express_ads', 'action' => 'add'),
								'buy' => array('controller' => 'express_ads', 'action' => 'buy'),
								'assign' => array('controller' => 'express_ads', 'action' => 'assign'),
							))
						?>
						<div class="row padding30-col">
							<div class="col-md-12">
								<h5 class="padding30-bottom"><?=__('Clicks statistics for: "%s".', $ad['ExpressAd']['title'])?></h5>
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
												'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '12px', 'font-weight' => '700', 'font-family' => 'Lato')),
											),
											'yAxis' => array(
												'gridLineColor' => '#ffffff', 
												'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '14px', 'font-weight' => '700', 'font-family' => 'Lato')),
											),
										)
									)
									?>
								<?php if($geo_targetting): ?>
									<h5 class="margin30-top padding30-bottom"><?=__('Geographic statistics for: "%s".', $ad['ExpressAd']['title'])?></h5>
									<?=$this->Map->show($geo, array('height' => '400px'))?>
								<?php endif ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
