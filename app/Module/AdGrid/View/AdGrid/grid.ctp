<div class="container">
	<div class="row">
		<div class="col-md-12 adgridpanel margin30-top padding30">
			<div class="col-md-8">
				<h2><?=__d('ad_grid', 'AdGrid - win up to %s', $this->Currency->format($mainPrize))?></h2>
				<p><?=__d('ad_grid', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.')?></p>
				<div class="adgridblock" style="background: url(<?=$this->Html->assetUrl('AdGrid.img'.DS.'backgrounds'.DS.$image)?>) no-repeat; background-size: 600px 400px;">
					<table class="adgridtable" cellspacing="0" cellpadding="0">
						<?php for($y = 1; $y <= $settings['size']['height']; $y++): ?>
							<tr>
								<?php for($x = 1; $x <= $settings['size']['width']; $x++): ?>
									<?php if(isset($user) && $user['AdGridUserClick']['fields'] !== null && in_array($x.':'.$y, $user['AdGridUserClick']['fields'])): ?>
										<td class="adgridclicked" data-y="<?=$y?>" data-x="<?=$x?>"></td>
									<?php else: ?>
										<td class="adgridactive" data-y="<?=$y?>" data-x="<?=$x?>"></td>
									<?php endif; ?>
								<?php endfor; ?>
							</tr>
						<?php endfor; ?>
					</table>
					<div class="adgridblockinfo">
						<?=__d('ad_grid', 'Position:')?> <strong id="position"><?=__d('ad_grid', 'Click anywhere on the picture')?></strong>
					</div>
				</div>
			</div>
			<div class="col-md-4 statsbox padding30">
				<?php if(isset($user)): ?>
					<h4><?=__d('ad_grid', 'Your stats')?></h4>
					<i class="fa fa-pie-chart titleicon"></i>
					<table class="table gridstats">
						<tr>
							<th><?=__d('ad_grid', 'Your chances (left/total)')?></th>
							<td><span class="badge"><?=__d('ad_grid', '%d / %d', $user['ActiveMembership']['Membership']['AdGridMembershipsOption']['clicks_per_day'] - $user['AdGridUserClick']['clicks'], $user['ActiveMembership']['Membership']['AdGridMembershipsOption']['clicks_per_day'])?></td>
						</tr>
						<tr>
							<th><?=__d('ad_grid', 'Clicks (left/total)')?></th>
							<td><span class="badge"><?=__d('ad_grid', '%d / %d', $user['AdGridUserClick']['clicks'], $user['ActiveMembership']['Membership']['AdGridMembershipsOption']['clicks_per_day'])?></td>
						</tr>
						<tr>
							<th><?=__d('ad_grid', 'Winnings (today/total)')?></th>
							<td><span class="badge"><?=__d('ad_grid', '%s / %s', $this->Currency->format($todayPrizes), $this->Currency->format($totalPrizes))?></td>
						</tr>
					</table>
				<hr>
				<?php endif; ?>
				<h4><?=__d('ad_grid', 'Latest winners')?></h4>
				<i class="fa fa-users titleicon"></i>
				<div class="gridstatsdiv">
					<table class="table latestwinners">
						<?php foreach($lastWinners as $w): ?>
							<tr>
								<td class="gridavatar"><i class="fa fa-user"></i></td>
								<td class="gridusername"><?=h($w['AdGridWinHistory']['username'])?><br />
								<span><?=$this->Time->nice($w['AdGridWinHistory']['date'])?></span></td>
								<td class="gridwinning"><span class="badge"><?=$this->Currency->format($w['AdGridWinHistory']['prize'])?></span></td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
				<hr>
				<h4><?=__d('ad_grid', 'Latest %s winners', $this->Currency->format($mainPrize))?></h4>
				<i class="fa fa-usd titleicon"></i>
				<div class="gridstatsdiv">
					<table class="table latestwinners">
						<?php foreach($lastMaxWinners as $w): ?>
							<tr>
								<td class="gridavatar"><i class="fa fa-user fa"></i></td>
								<td class="gridusername"><?=h($w['AdGridWinHistory']['username'])?><br />
								<span><?=$this->Time->nice($w['AdGridWinHistory']['date'])?></span></td>
								<td class="gridwinning"><span class="badge"><?=$this->Currency->format($w['AdGridWinHistory']['prize'])?></span></td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	$out = __d('ad_grid', 'Click anywhere on the picture');
	$link = Router::url(array('action' => 'view'));
	$this->Js->buffer("
		$('.adgridblock').each(function(idx, obj) {
			$(obj).on('mouseleave', function() {
				$('#position').html('$out');
			});
		});
		$('.adgridtable').each(function(idx, obj) {
			$(obj).find('td').each(function(idx, obj) {
				$(obj).on('mouseenter', function() {
					$('#position').html($(obj).data('x') + ' ' + $(obj).data('y'));
				});
			});
		});
	");
	if($active) {
		$this->Js->buffer("
			$('.adgridactive').on('click', function() {
				window.open('$link' + '/' + $(this).data('x') + '/' + $(this).data('y'), '_blank');
			});
		");
	}
?>
