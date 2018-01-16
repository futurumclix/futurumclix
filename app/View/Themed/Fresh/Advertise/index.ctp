<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<li><?=
						$this->Html->link('', array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home', 'escape' => false), array('class' => 'mdi mdi-home mdi-18px'));
						?>
					</li>
					<li class="uk-active"><?=__('Advertise on %s', h(Configure::read('siteTitle')))?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<div class="uk-container content">
	<div class="uk-grid">
		<div class="uk-width-1-1">
			<?=$this->Notice->show()?>
			<h2 class="uk-text-center"><?=__('Advertise on %s', h(Configure::read('siteTitle')))?></h2>
			<?php if(isset($ads['PaidToClickAds']) && !empty($ads['PaidToClickAds'])): ?>
			<div class="uk-card uk-card-body uk-card-default uk-overflow-auto">
				<h5><?=__('Paid To Click Ads')?></h5>
				<p><?=__('This is some description.')?></p>
				<table class="uk-table uk-table-small uk-table-striped">
					<?php foreach($ads['PaidToClickAds'] as $cat): ?>
					<thead>
						<tr>
							<td colspan="2">
								<h5 class="uk-text-center"><?=__('%s', $cat['AdsCategory']['name'])?></h5>
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
				<div class="uk-text-right">
					<?=
						$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'ads'), array(
							'class' => 'uk-button uk-button-primary',
							))
							?>
				</div>
			</div>
			<?php endif; ?>
			<?php if(isset($ads['AdGridAdsPackage']) && !empty($ads['AdGridAdsPackage'])): ?>
			<div class="uk-card uk-card-body uk-card-default uk-overflow-auto uk-margin-small-top">
				<h5><?=__('AdGrid Ads')?></h5>
				<p><?=__('This is some description.')?></p>
				<table class="uk-table uk-table-small uk-table-striped">
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
				<div class="uk-text-right">
					<?=
						$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'ad_grid'), array(
							'class' => 'uk-button uk-button-primary',
							))
							?>
				</div>
			</div>
			<?php endif; ?>
			<?php if(isset($ads['BannerAdsPackage']) && !empty($ads['BannerAdsPackage'])): ?>
			<div class="uk-card uk-card-body uk-card-default uk-overflow-auto uk-margin-small-top">
				<h5><?=__('Banner Ads')?></h5>
				<p><?=__('This is some description.')?></p>
				<table class="uk-table uk-table-small uk-table-striped">
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
				<div class="uk-text-right">
					<?=
						$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'banner_ads'), array(
							'class' => 'uk-button uk-button-primary',
							))
							?>
				</div>
			</div>
			<?php endif; ?>
			<?php if(isset($ads['ExplorerAdsPackage']) && !empty($ads['ExplorerAdsPackage'])): ?>
			<div class="uk-card uk-card-body uk-card-default uk-overflow-auto uk-margin-small-top">
				<h5><?=__('Explorer Ads')?></h5>
				<p><?=__('This is some description.')?></p>
				<table class="uk-table uk-table-small uk-table-striped">
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
				<div class="uk-text-right">
					<?=
						$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'explorer_ads'), array(
							'class' => 'uk-button uk-button-primary',
							))
							?>
				</div>
			</div>
			<?php endif; ?>
			<?php if(isset($ads['ExpressAdsPackage']) && !empty($ads['ExpressAdsPackage'])): ?>
			<div class="uk-card uk-card-body uk-card-default uk-overflow-auto uk-margin-small-top">
				<h5><?=__('Express Ads')?></h5>
				<p><?=__('This is some description.')?></p>
				<table class="uk-table uk-table-small uk-table-striped">
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
				<div class="uk-text-right">
					<?=
						$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'express_ads'), array(
							'class' => 'uk-button uk-button-primary',
							))
							?>
				</div>
			</div>
			<?php endif; ?>
			<?php if(isset($ads['FeaturedAdsPackage']) && !empty($ads['FeaturedAdsPackage'])): ?>
			<div class="uk-card uk-card-body uk-card-default uk-overflow-auto uk-margin-small-top">
				<h5><?=__('Featured Ads')?></h5>
				<p><?=__('This is some description.')?></p>
				<table class="uk-table uk-table-small uk-table-striped">
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
				<div class="uk-text-right">
					<?=
						$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'featured_ads'), array(
							'class' => 'uk-button uk-button-primary',
							))
							?>
				</div>
			</div>
			<?php endif; ?>
			<?php if(isset($ads['LoginAdsPackage']) && !empty($ads['LoginAdsPackage'])): ?>
			<div class="uk-card uk-card-body uk-card-default uk-overflow-auto uk-margin-small-top">
				<h5><?=__('Login Ads')?></h5>
				<p><?=__('This is some description.')?></p>
				<table class="uk-table uk-table-small uk-table-striped">
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
				<div class="uk-text-right">
					<?=
						$this->Html->link(__('BUY'), array('controller' => 'advertise', 'action' => 'choose', 'login_ads'), array(
							'class' => 'uk-button uk-button-primary',
							))
							?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
