<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('Advertisement statistics')?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<div class="uk-card uk-card-body uk-card-default uk-margin-top uk-overflow-auto">
				<table class="uk-table uk-table-small">
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
				<h5 class="uk-text-center"><?=__('Statistics')?></h5>
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
								'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '12px', 'font-weight' => '700', 'font-family' => 'Roboto')),
							),
							'yAxis' => array(
								'gridLineColor' => '#ffffff', 
								'labels' => array('style' => array('color' => '#95a5a6', 'font-size' => '14px', 'font-weight' => '700', 'font-family' => 'Roboto')),
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
