<div class="container advertise">
	<div class="row">
		<div class="col">
			<h2 class="text-xs-center"><?=__('Advertisement statistics')?></h2>
		</div>
	</div>
	<div class="row margin30-top padding30-bottom">
		<div class="col-md-12">
			<div class="panel">
				<div class="padding30-col">
					<table class="table">
						<thead>
							<tr>
								<td><?=__('Advertisement status')?></td>
								<td><?=$stats[$alias]['status']?></td>
							</tr>
							<tr>
								<td><?=__('Advertised page')?></td>
								<td><?=$stats[$alias]['url']?></td>
							</tr>
							<tr>
								<td><?=__('Clicks')?></td>
								<td><?=$stats[$alias]['clicks']?></td>
							</tr>
							<?php if(isset($stats[$alias]['outside_clicks'])): ?>
								<tr>
									<td><?=__('Outside clicks')?></td>
									<td><?=$stats[$alias]['outside_clicks']?></td>
								</tr>
							<?php endif; ?>
							<?php if($stats[$alias]['package_type'] == 'Days'): ?>
									<tr>
										<td><?=__('Expiry time')?></td>
										<?php if(isset($stats[$alias]['expiry_date'])): ?>
											<td><?=$stats[$alias]['expiry_date']?></td>
										<?php else: ?>
											<td><?=date('Y-m-d H:i:s', strtotime($stats[$alias]['start'].' +'.$stats[$alias]['expiry'].' day'))?></td>
										<?php endif; ?>
									</tr>
							<?php elseif(isset($stats[$alias]['expiry'])): ?>
								<tr>
									<td><?=__('Expires')?></td>
									<td><?=__('%d %s', $stats[$alias]['expiry'], __($stats[$alias]['package_type']))?></td>
								</tr>
							<?php endif; ?>
						</thead>
					</table>
					<h5 class="text-xs-center"><?=__('Statistics')?></h5>
					<?php if(isset($stats['chart'])): ?>
						<?=
							$this->Chart->show($stats['chart'],
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
					<?php endif; ?>
					<?php if(isset($stats['geo'])): ?>
						<?=$this->Map->show($stats['geo'], array('height' => '400px'))?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
