<div class="container advertise padding30-bottom margin30-top">
	<div class="row">
		<div class="col">
			<h2 class="text-xs-center"><?=__('Advertise on %s', h(Configure::read('siteTitle')))?></h2>
		</div>
	</div>
	<?php if(isset($ads['PaidToClickAds']) && !empty($ads['PaidToClickAds'])): ?>
	<div class="row margin30-top">
		<div class="col-md-12">
			<div class="panel">
				<div class="padding30-col">
					<h5>
					<?=__('Paid To Click Ads')?></h3>
					<p><?=__('This is some description.')?></p>
					<table class="table table-striped table-sm">
						<?php foreach($ads['PaidToClickAds'] as $cat): ?>
						<thead>
							<tr>
								<td colspan="2">
									<h6 class="text-xs-center"><?=__('%s', $cat['AdsCategory']['name'])?></h6>
								</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?=__('Package')?></td>
								<td><?=__('Price')?></td>
							</tr>
							<?php foreach($cat['AdsCategoryPackage'] as $pack): ?>
							<tr>
								<td>
									<?=__('%s %s', $pack['amount'], $pack['type'])?>
								</td>
								<td>
									<?=__('%s', $this->Currency->format($pack['price']))?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
						<?php endforeach; ?>
					</table>
					<div class="text-xs-right">
						<?=
							$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'ads'), array(
								'class' => 'btn btn-primary',
								))
								?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if(isset($ads['AdGridAdsPackage']) && !empty($ads['AdGridAdsPackage'])): ?>
	<div class="row margin30-top">
		<div class="col-md-12">
			<div class="panel">
				<div class="padding30-col">
					<h5>
					<?=__('AdGrid Ads')?></h3>
					<p><?=__('This is some description.')?></p>
					<table class="table table-striped table-sm">
						<tbody>
							<tr>
								<td><?=__('Package')?></td>
								<td><?=__('Price')?></td>
							</tr>
							<?php foreach($ads['AdGridAdsPackage'] as $pack): ?>
							<tr>
								<td><?=__('%s %s', $pack['AdGridAdsPackage']['amount'], $pack['AdGridAdsPackage']['type'])?></td>
								<td><?=__('%s', $this->Currency->format($pack['AdGridAdsPackage']['price']))?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div class="text-xs-right">
						<?=
							$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'ad_grid'), array(
								'class' => 'btn btn-primary',
								))
								?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if(isset($ads['BannerAdsPackage']) && !empty($ads['BannerAdsPackage'])): ?>
	<div class="row margin30-top">
		<div class="col-md-12">
			<div class="panel">
				<div class="padding30-col">
					<h5>
					<?=__('Banner Ads')?></h3>
					<p><?=__('This is some description.')?></p>
					<table class="table table-striped table-sm">
						<tbody>
							<tr>
								<td><?=__('Package')?></td>
								<td><?=__('Price')?></td>
							</tr>
							<?php foreach($ads['BannerAdsPackage'] as $pack): ?>
							<tr>
								<td><?=__('%s %s', $pack['BannerAdsPackage']['amount'], $pack['BannerAdsPackage']['type'])?></td>
								<td><?=__('%s', $this->Currency->format($pack['BannerAdsPackage']['price']))?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div class="text-xs-right">
						<?=
							$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'banner_ads'), array(
								'class' => 'btn btn-primary',
								))
								?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if(isset($ads['ExplorerAdsPackage']) && !empty($ads['ExplorerAdsPackage'])): ?>
	<div class="row margin30-top">
		<div class="col-md-12">
			<div class="panel">
				<div class="padding30-col">
					<h5>
					<?=__('Explorer Ads')?></h3>
					<p><?=__('This is some description.')?></p>
					<table class="table table-striped table-sm">
						<tbody>
							<tr>
								<td><?=__('Package')?></td>
								<td><?=__('Price')?></td>
							</tr>
							<?php foreach($ads['ExplorerAdsPackage'] as $pack): ?>
							<tr>
								<td><?=__('%s %s', $pack['ExplorerAdsPackage']['amount'], $pack['ExplorerAdsPackage']['type'])?></td>
								<td><?=__('%s', $this->Currency->format($pack['ExplorerAdsPackage']['price']))?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div class="text-xs-right">
						<?=
							$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'explorer_ads'), array(
								'class' => 'btn btn-primary',
								))
								?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if(isset($ads['ExpressAdsPackage']) && !empty($ads['ExpressAdsPackage'])): ?>
	<div class="row margin30-top">
		<div class="col-md-12">
			<div class="panel">
				<div class="padding30-col">
					<h5>
					<?=__('Express Ads')?></h3>
					<p><?=__('This is some description.')?></p>
					<table class="table table-striped table-sm">
						<tbody>
							<tr>
								<td><?=__('Package')?></td>
								<td><?=__('Price')?></td>
							</tr>
							<?php foreach($ads['ExpressAdsPackage'] as $pack): ?>
							<tr>
								<td><?=__('%s %s', $pack['ExpressAdsPackage']['amount'], $pack['ExpressAdsPackage']['type'])?></td>
								<td><?=__('%s', $this->Currency->format($pack['ExpressAdsPackage']['price']))?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div class="text-xs-right">
						<?=
							$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'express_ads'), array(
								'class' => 'btn btn-primary',
								))
								?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if(isset($ads['FeaturedAdsPackage']) && !empty($ads['FeaturedAdsPackage'])): ?>
	<div class="row margin30-top">
		<div class="col-md-12">
			<div class="panel">
				<div class="padding30-col">
					<h5>
					<?=__('Featured Ads')?></h3>
					<p><?=__('This is some description.')?></p>
					<table class="table table-striped table-sm">
						<tbody>
							<tr>
								<td><?=__('Package')?></td>
								<td><?=__('Price')?></td>
							</tr>
							<?php foreach($ads['FeaturedAdsPackage'] as $pack): ?>
							<tr>
								<td><?=__('%s %s', $pack['FeaturedAdsPackage']['amount'], $pack['FeaturedAdsPackage']['type'])?></td>
								<td><?=__('%s', $this->Currency->format($pack['FeaturedAdsPackage']['price']))?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div class="text-xs-right">
						<?=
							$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'featured_ads'), array(
								'class' => 'btn btn-primary',
								))
								?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<?php if(isset($ads['LoginAdsPackage']) && !empty($ads['LoginAdsPackage'])): ?>
	<div class="row margin30-top">
		<div class="col-md-12">
			<div class="panel">
				<div class="padding30-col">
					<h5>
					<?=__('Login Ads')?></h3>
					<p><?=__('This is some description.')?></p>
					<table class="table table-striped table-sm">
						<tbody>
							<tr>
								<td><?=__('Package')?></td>
								<td><?=__('Price')?></td>
							</tr>
							<?php foreach($ads['LoginAdsPackage'] as $pack): ?>
							<tr>
								<td><?=__('%s %s', $pack['LoginAdsPackage']['amount'], $pack['LoginAdsPackage']['type'])?></td>
								<td><?=__('%s', $this->Currency->format($pack['LoginAdsPackage']['price']))?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div class="text-xs-right">
						<?=
							$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'login_ads'), array(
								'class' => 'btn btn-primary',
								))
								?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>

