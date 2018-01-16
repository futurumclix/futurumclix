<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Banner Ads Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'banner_ads', 'action' => 'add'),
				'buy' => array('controller' => 'banner_ads', 'action' => 'buy'),
				'assign' => array('controller' => 'banner_ads', 'action' => 'assign'),
				))
				?>
			<?php if(empty($ads)): ?>
			<div class="uk-width-1-1">
				<h6 class="uk-text-center uk-margin-top"><?=__('You have no advertisement campaigns at the moment. Please click %s to add one.', $this->Html->link(__('here'), array('controller' => 'bannerAds', 'action' => 'add')))?></h6>
			</div>
			<?php else: ?>
			<h2 class="uk-margin-top"><?=__('My current Banner ads advertisements')?></h2>
			<?php foreach($ads as $ad): ?>
			<div class="uk-card uk-card-body uk-margin-top advertlist">
				<div class="uk-child-width-1-2@m" uk-grid>
					<div>
						<?=
							$this->Html->image($ad['BannerAd']['image_url'], array(
							'title' => $ad['BannerAd']['title'],
							'alt' => $ad['BannerAd']['title'],
							'width' => $bannerSize['width'],
							'height' => $bannerSize['height'],
							'border' => 0,
							))
							?>
						<h6 style="margin-top: 5px;"><?=h($ad['BannerAd']['title'])?></h6>
						<div class="adverturl"><?=$this->Html->link($ad['BannerAd']['url'], null, array('target' => 'blank'))?></div>
						<p><?=$this->Html->link($ad['BannerAd']['image_url'], null, array('target' => 'blank'))?></p>
					</div>
					<div class="uk-text-right">
						<div class="advertstatus">
							<?php if($ad['BannerAd']['status'] != 'Pending'): ?>
							<?php if($ad['BannerAd']['status'] == 'Active'): ?>
							<h6>
							<?=h(__($ad['BannerAd']['status']));?>
							<?php if($ad['BannerAd']['package_type'] != 'Days'): ?>
							<?=
								$this->UserForm->postLink('<i class="mdi mdi-18px mdi-pause" title="'.__('Pause advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'inactivate', $ad['BannerAd']['id']),
								array('escape' => false),
								__('Are you sure you want to pause "%s"?', $ad['BannerAd']['title'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<?=
								$this->UserForm->postLink('<h6>'.h(__($ad['BannerAd']['status'])).'<i class="mdi mdi-18px mdi-play" title="'.__('Activate advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'activate', $ad['BannerAd']['id']),
								array('escape' => false),
								__('Are you sure you want to activate "%s"?', $ad['BannerAd']['title'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<h6><?=__('Pending')?></h6>
							<?php endif; ?>
						</div>
						<?php if($ad['BannerAd']['package_type'] != 'Days' || $auto_approve): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-pencil"></i>', array('action' => 'edit', $ad['BannerAd']['id']), array(
							'escape' => false,
							'title' => __('Click to edit advertisement data'),
							'uk-tooltip' => ''
							))
							?>
						<?php endif; ?>
						<?php if($ad['BannerAd']['status'] != 'Pending'): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-plus"></i>', array('action' => 'assignTo', $ad['BannerAd']['id']), array(
							'escape' => false,
							'title' => __('Add more %s', empty($ad['BannerAd']['package_type']) ? __('exposures') : __(strtolower($ad['BannerAd']['package_type']))),
							'uk-tooltip' => ''
							))
							?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-earth"></i>', array('action' => 'statistics', $ad['BannerAd']['id']), array(
							'escape' => false,
							'title' => __('Show statistics'),
							'uk-tooltip' => ''
							))
							?>
						<i class="mdi mdi-18px mdi-information" uk-tooltip title="
							<?=__('Exposure type')?>: 
							<?= empty($ad['BannerAd']['package_type']) ? __('Not assigned') : __($ad['BannerAd']['package_type'])?>
							<br />
							<?=__('Total clicks')?>: 
							<?=h($ad['BannerAd']['total_clicks']);?>
							<br />
							<?=__('Clicks today')?>: 
							<?=isset($clicksToday[$ad['BannerAd']['id']]) ? h($clicksToday[$ad['BannerAd']['id']]) : 0?>
							<br />
							<?php if(!empty($ad['BannerAd']['package_type'])): ?>
							<?php if($ad['BannerAd']['package_type'] == 'Days'): ?>
							<?=__('Valid until')?>: <?=$this->Time->nice($ad['BannerAd']['start']." + {$ad['BannerAd']['expiry']} day")?>
							<?php else: ?>
							<?=__('%s left', __($ad['BannerAd']['package_type']))?> <?=$ad['BannerAd']['expiry'] - $ad['BannerAd'][Inflector::tableize($ad['BannerAd']['package_type'])]?>
							<?php endif; ?>
							<?php endif; ?>
							">
						</i>
						<?php endif; ?>
						<?=
							$this->UserForm->postLink('<i class="mdi mdi-18px mdi-delete" title="'.__('Delete this advertisement').'" uk-tooltip></i>',
							array('action' => 'delete', $ad['BannerAd']['id']),
							array('escape' => false),
							__('Are you sure you want to delete "%s"?', $ad['BannerAd']['title'])
							)
							?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
</div>
