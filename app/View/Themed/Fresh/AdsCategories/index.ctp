<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<div class="uk-display-inline">
					<?=__('View Ads')?>
				</div>
				<div class="uk-float-right uk-display-inline">
					<?php if($this->Session->read('Auth.User')): ?>
					<?php if($availableAtLeastOne): ?>
					<?=__('You have available advertisement(s)')?>
					<?php elseif(isset($nextAdDate)): ?>
					<?=__('Next ad will be available not later than %s', $nextAdDate)?>
					<?php endif; ?>
					<?php endif; ?>
					<span class="servertime"><i class="mdi mdi-clock-fast"></i> <?=__('Current server time is: %s', date('H:i'))?></span>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="uk-container viewadspage">
	<?php foreach($adsCategories as $adsCategory): ?>
	<?php if(!empty($adsCategory['Ads'])): ?>
	<div uk-grid>
		<div class="uk-width-1-1 uk-margin-top">
			<h2><?=h($adsCategory['AdsCategory']['name'])?></h2>
		</div>
	</div>
	<div class="uk-child-width-1-4@m uk-grid-small uk-grid-match" uk-grid>
		<?php foreach($adsCategory['Ads'] as $ad): ?>
		<?php if(!isset($ad['Visited'])): ?>
		<a href="<?=$this->Html->url(array('controller' => 'ads', 'action' => 'view', $ad['id']))?>" target="_blank">
			<?php endif; ?>
			<div class="<?php if(isset($ad['Visited'])): ?> disabled<?php endif; ?><?php if(!isset($ad['Visited'])): ?> uk-card-hover<?php endif; ?>">
				<div class="uk-card uk-card-default uk-card-body">
					<div class="adtitle"><?=h($ad['title'])?></div>
					<?php if($adsCategory['AdsCategory']['allow_description']): ?>
					<div class="addescription"><?=h($ad['description'])?></div>
					<?php endif; ?>
					<div class="bottom">
						<div class="money uk-display-inline"><?=h($this->Currency->format($adsCategory['ClickValue'][0]['user_click_value']))?></div>
						<div class="uk-display-inline uk-float-right time"><?=h($adsCategory['AdsCategory']['time'])?> <?=__('seconds')?> <i class="fa fa-clock-o"></i></div>
					</div>
				</div>
			</div>
			<?php if(!isset($ad['Visited'])): ?>
		</a>
		<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	<?php endforeach; ?>
	<?php if($expressAdsActive && !empty($expressAds)): ?>
	<div uk-grid>
		<div class="uk-width-1-1">
			<h2><?=__('Express Ads')?></h2>
		</div>
	</div>
	<div class="uk-child-width-1-4@m uk-grid-small uk-grid-match" uk-grid>
		<?php foreach($expressAds as $ad): ?>
		<?php if(empty($ad['VisitedAd'])): ?>
		<a href="<?=$this->Html->url(array('controller' => 'express_ads', 'action' => 'view', $ad['ExpressAd']['id']))?>" target="_blank" onclick="setTimeout(function(){location.reload();}, 100);">
			<?php endif; ?>
			<div class="<?php if(!empty($ad['VisitedAd'])): ?> disabled<?php endif; ?><?php if(empty($ad['VisitedAd'])): ?> uk-card-hover<?php endif; ?>">
				<div class="uk-card uk-card-default uk-card-body">
					<div class="adtitle"><?=h($ad['ExpressAd']['title'])?></div>
					<?php if($expressSettings['descShow']): ?>
					<div class="addescription"><?=h($ad['ExpressAd']['description'])?></div>
					<?php endif; ?>
					<div class="bottom">
						<div class="money uk-display-inline"><?=h($this->Currency->format($ad['ClickValue'][0]['user_click_value']))?></div>
						<div class="uk-display-inline uk-float-right time"><?=__('Express Ad')?></div>
					</div>
				</div>
			</div>
			<?php if(empty($ad['VisitedAd'])): ?>
		</a>
		<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php endif;?>
	<?php if($explorerAdsActive && !empty($explorerAds)): ?>
	<div uk-grid>
		<div class="uk-width-1-1">
			<h2><?=__('Explorer Ads')?></h2>
		</div>
	</div>
	<div class="uk-child-width-1-4@m uk-grid-small uk-grid-match" uk-grid>
		<?php foreach($explorerAds as $ad): ?>
		<?php if(empty($ad['VisitedAd'])): ?>
		<a href="<?=$this->Html->url(array('controller' => 'explorer_ads', 'action' => 'view', $ad['ExplorerAd']['id']))?>" target="_blank">
			<?php endif; ?>
			<div class="<?php if(!empty($ad['VisitedAd'])): ?> disabled<?php endif; ?><?php if(empty($ad['VisitedAd'])): ?> uk-card-hover<?php endif; ?>">
				<div class="uk-card uk-card-default uk-card-body">
					<div class="adtitle"><?=h($ad['ExplorerAd']['title'])?></div>
					<?php if($explorerSettings['descShow']): ?>
					<div class="addescription"><?=h($ad['ExplorerAd']['description'])?></div>
					<?php endif; ?>
					<div class="bottom">
						<div class="money uk-display-inline"><?=h($this->Currency->format($ad['ClickValue'][0]['user_click_value']))?></div>
						<div class="uk-display-inline uk-float-right time"><?=__('Explorer Ad')?></div>
					</div>
				</div>
			</div>
			<?php if(empty($ad['VisitedAd'])): ?>
		</a>
		<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php endif;?>
</div>
<?php
	if(isset($jsTimeout)) {
		$this->Js->buffer("
			setTimeout(function(){window.location.reload(true);}, $jsTimeout);
			");
	}
	?>
