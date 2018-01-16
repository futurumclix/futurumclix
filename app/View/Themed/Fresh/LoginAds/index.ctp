<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Login Ads Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'login_ads', 'action' => 'add'),
				'buy' => array('controller' => 'login_ads', 'action' => 'buy'),
				'assign' => array('controller' => 'login_ads', 'action' => 'assign'),
				))
				?>
			<?php if(empty($ads)): ?>
			<div class="uk-width-1-1">
				<h6 class="uk-text-center uk-margin-top"><?=__('You have no advertisement campaigns at the moment. Please click %s to add one.', $this->Html->link(__('here'), array('controller' => 'loginAds', 'action' => 'add')))?></h6>
			</div>
			<?php else: ?>
			<h2 class="uk-margin-top"><?=__('My current Login Ads Advertisements')?></h2>
			<?php foreach($ads as $ad): ?>
			<div class="uk-card uk-card-body uk-margin-top advertlist">
				<div class="uk-child-width-1-2@m" uk-grid>
					<div>
						<?=
							$this->Html->image($ad['LoginAd']['image_url'], array(
							'title' => $ad['LoginAd']['title'],
							'alt' => $ad['LoginAd']['title'],
							'width' => $bannerSize['width'],
							'height' => $bannerSize['height'],
							'border' => 0,
							))
							?>
						<h6 style="margin-top: 5px;"><?=h($ad['LoginAd']['title'])?></h6>
						<div class="adverturl"><?=$this->Html->link($ad['LoginAd']['url'], null, array('target' => 'blank'))?></div>
						<p><?=$this->Html->link($ad['LoginAd']['image_url'], null, array('target' => 'blank'))?></p>
					</div>
					<div class="uk-text-right">
						<div class="advertstatus">
							<?php if($ad['LoginAd']['status'] != 'Pending'): ?>
							<?php if($ad['LoginAd']['status'] == 'Active'): ?>
							<h6>
							<?=h(__($ad['LoginAd']['status']));?>
							<?php if($ad['LoginAd']['package_type'] != 'Days'): ?>
							<?=
								$this->UserForm->postLink('<i class="mdi mdi-18px mdi-pause" title="'.__('Pause advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'inactivate', $ad['LoginAd']['id']),
								array('escape' => false),
								__('Are you sure you want to pause "%s"?', $ad['LoginAd']['title'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<?=
								$this->UserForm->postLink('<h6>'.h(__($ad['LoginAd']['status'])).'<i class="mdi mdi-18px mdi-play" title="'.__('Activate advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'activate', $ad['LoginAd']['id']),
								array('escape' => false),
								__('Are you sure you want to activate "%s"?', $ad['LoginAd']['title'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<h6><?=__('Pending')?></h6>
							<?php endif; ?>
						</div>
						<?php if($ad['LoginAd']['package_type'] != 'Days' || $auto_approve): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-pencil"></i>', array('action' => 'edit', $ad['LoginAd']['id']), array(
							'escape' => false,
							'title' => __('Click to edit advertisement data'),
							'uk-tooltip' => '',
							))
							?>
						<?php endif; ?>
						<?php if($ad['LoginAd']['status'] != 'Pending'): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-plus"></i>', array('action' => 'assignTo', $ad['LoginAd']['id']), array(
							'escape' => false,
							'title' => __('Add more %s', empty($ad['LoginAd']['package_type']) ? __('exposures') : __(strtolower($ad['LoginAd']['package_type']))),
							'uk-tooltip' => '',
							))
							?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-earth"></i>', array('action' => 'statistics', $ad['LoginAd']['id']), array(
							'escape' => false,
							'title' => __('Show statistics'),
							'uk-tooltip' => '',
							))
							?>
						<i class="mdi mdi-18px mdi-information" uk-tooltip title="
							<?=__('Exposure type')?>: 
							<?= empty($ad['LoginAd']['package_type']) ? __('Not assigned') : __($ad['LoginAd']['package_type'])?>
							<br />
							<?=__('Total clicks')?>: 
							<?=h($ad['LoginAd']['total_clicks']);?>
							<br />
							<?=__('Clicks today')?>: 
							<?=isset($clicksToday[$ad['LoginAd']['id']]) ? h($clicksToday[$ad['LoginAd']['id']]) : 0?>
							<br />
							<?php if(!empty($ad['LoginAd']['package_type'])): ?>
							<?php if($ad['LoginAd']['package_type'] == 'Days'): ?>
							<?=__('Valid until')?>: <?=$this->Time->nice($ad['LoginAd']['start']." + {$ad['LoginAd']['expiry']} day")?>
							<?php else: ?>
							<?=__('%s left', __($ad['LoginAd']['package_type']))?> <?=$ad['LoginAd']['expiry'] - $ad['LoginAd'][Inflector::tableize($ad['LoginAd']['package_type'])]?>
							<?php endif; ?>
							<?php endif; ?>
							">
						</i>
						<?php endif; ?>
						<?=
							$this->UserForm->postLink('<i class="mdi mdi-18px mdi-delete" title="'.__('Delete this advertisement').'" uk-tooltip=""></i>',
							array('action' => 'delete', $ad['LoginAd']['id']),
							array('escape' => false),
							__('Are you sure you want to delete "%s"?', $ad['LoginAd']['title'])
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
