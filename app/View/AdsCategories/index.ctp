<div class="viewadspage">
	<div class="container">
		<div class="row">
			<div class="col-md-12 front_text adpage margin30-top">
				<div class="col-md-12 timetitle">
					<?php if($this->Session->read('Auth.User')): ?>
						<?php if($availableAtLeastOne): ?>
							<i class="fa fa-eye"></i> 
							<?=__('You have available advertisement(s)')?>
						<?php elseif(isset($nextAdDate)): ?>
							<i class="fa fa-eye-slash"></i> 
							<?=__('Next ad will be available not later than %s', $nextAdDate)?>
						<?php endif; ?>
					<?php endif; ?>
					<span class="servertime"><i class="fa fa-clock-o"></i> <?=__('Current server time is: %s', date('H:i'))?><span>
				</div>
				<?php foreach($adsCategories as $adsCategory): ?>
					<?php if(!empty($adsCategory['Ads'])): ?>
						<div class="clearfix"></div>
						<div class="col-md-12 adcategory">
							<h5><?=h($adsCategory['AdsCategory']['name'])?></h5>
						</div>
						<?php foreach($adsCategory['Ads'] as $ad): ?>
							<?php if(!isset($ad['Visited'])): ?>
								<a href="<?=$this->Html->url(array('controller' => 'ads', 'action' => 'view', $ad['id']))?>" target="_blank">
							<?php endif; ?>
							<div class="col-sm-4<?php if(isset($ad['Visited'])): ?> disabled<?php endif; ?>">
								<div class="adbox">
									<div class="adtitle"><?=h($ad['title'])?></div>
									<?php if($adsCategory['AdsCategory']['allow_description']): ?>
									<div class="addescription"><?=h($ad['description'])?></div>
									<?php endif; ?>
									<div class="bottomleft bottom"><?=h($this->Currency->format($adsCategory['ClickValue'][0]['user_click_value']))?></div>
									<div class="bottomright bottom"><?=h($adsCategory['AdsCategory']['time'])?> <?=__('seconds')?> <i class="fa fa-clock-o"></i></div>
									<div class="clearfix"></div>
								</div>
							</div>
							<?php if(!isset($ad['Visited'])): ?>
								</a>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php if($expressAdsActive && !empty($expressAds)): ?>
						<div class="clearfix"></div>
						<div class="col-md-12 adcategory">
							<h5><?=__('Express Ads')?></h5>
						</div>
					<?php foreach($expressAds as $ad): ?>
						<?php if(empty($ad['VisitedAd'])): ?>
							<a href="<?=$this->Html->url(array('controller' => 'express_ads', 'action' => 'view', $ad['ExpressAd']['id']))?>" target="_blank" onclick="setTimeout(function(){location.reload();}, 100);">
						<?php endif; ?>
						<div class="col-sm-4<?php if(!empty($ad['VisitedAd'])): ?> disabled<?php endif; ?>">
							<div class="adbox">
								<div class="adtitle"><?=h($ad['ExpressAd']['title'])?></div>
								<?php if($expressSettings['descShow']): ?>
									<div class="addescription"><?=h($ad['ExpressAd']['description'])?></div>
								<?php endif; ?>
								<div class="bottomleft bottom"><?=h($this->Currency->format($ad['ClickValue'][0]['user_click_value']))?></div>
								<div class="bottomright bottom"><?=__('Express Ad')?></div>
								<div class="clearfix"></div>
							</div>
						</div>
						<?php if(empty($ad['VisitedAd'])): ?>
							</a>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif;?>
				<?php if($explorerAdsActive && !empty($explorerAds)): ?>
						<div class="clearfix"></div>
						<div class="col-md-12 adcategory">
							<h5><?=__('Explorer Ads')?></h5>
						</div>
					<?php foreach($explorerAds as $ad): ?>
						<?php if(empty($ad['VisitedAd'])): ?>
							<a href="<?=$this->Html->url(array('controller' => 'explorer_ads', 'action' => 'view', $ad['ExplorerAd']['id']))?>" target="_blank">
						<?php endif; ?>
						<div class="col-sm-4<?php if(!empty($ad['VisitedAd'])): ?> disabled<?php endif; ?>">
							<div class="adbox">
								<div class="adtitle"><?=h($ad['ExplorerAd']['title'])?></div>
								<?php if($explorerSettings['descShow']): ?>
									<div class="addescription"><?=h($ad['ExplorerAd']['description'])?></div>
								<?php endif; ?>
								<div class="bottomleft bottom"><?=h($this->Currency->format($ad['ClickValue'][0]['user_click_value']))?></div>
								<div class="bottomright bottom"><?=__('Explorer Ad')?></div>
								<div class="clearfix"></div>
							</div>
						</div>
						<?php if(empty($ad['VisitedAd'])): ?>
							</a>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>
<?php
if(isset($jsTimeout)) {
	$this->Js->buffer("
		setTimeout(function(){window.location.reload(true);}, $jsTimeout);
	");
}
?>
