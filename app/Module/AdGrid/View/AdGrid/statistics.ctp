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
							<h5><?=__d('ad_grid', 'AdGrid Ads Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('action' => 'add'),
								'buy' => array('action' => 'buy'),
								'assign' => array('action' => 'assign'),
							))
							?>
						<div class="row padding30-col">
							<div class="col-md-12">
								<h5 class="padding30-bottom"><?=__d('ad_grid', 'Clicks statistics for: "%s".', $ad['AdGridAd']['url'])?></h5>
								<?=
									$this->Chart->show(array(
											__d('ad_grid', 'Clicks') => $clicks,
										),
										array('continous' => 'day'),
										array(
											'colors' => array('#2ecc71','#9b59b6'),
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
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
