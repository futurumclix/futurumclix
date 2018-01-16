<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Featured Ads Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'featured_ads', 'action' => 'add'),
				'buy' => array('controller' => 'featured_ads', 'action' => 'buy'),
				'assign' => array('controller' => 'featured_ads', 'action' => 'assign'),
				))
				?>
			<?php if(empty($ads)): ?>
			<div class="uk-width-1-1">
				<h6 class="uk-text-center uk-margin-top"><?=__('You have no advertisement campaigns at the moment. Please click %s to add one.', $this->Html->link(__('here'), array('controller' => 'featuredAds', 'action' => 'add')))?></h6>
			</div>
			<?php else: ?>
			<h2 class="uk-margin-top"><?=__('My current Featured ads advertisements')?></h2>
			<?php foreach($ads as $ad): ?>
			<div class="uk-card uk-card-body uk-margin-top advertlist">
				<div class="uk-child-width-1-2@m" uk-grid>
					<div>
						<h6><?=h($ad['FeaturedAd']['title'])?></h6>
						<p><?=h($ad['FeaturedAd']['description'])?></p>
						<div class="adverturl"><?=$this->Html->link($ad['FeaturedAd']['url'], null, array('target' => 'blank'))?></div>
					</div>
					<div class="uk-text-right">
						<div class="advertstatus">
							<?php if($ad['FeaturedAd']['status'] != 'Pending'): ?>
							<?php if($ad['FeaturedAd']['status'] == 'Active'): ?>
							<h6>
							<?=h(__($ad['FeaturedAd']['status']));?>
							<?php if($ad['FeaturedAd']['package_type'] != 'Days'): ?>
							<?=
								$this->UserForm->postLink('<i class="mdi mdi-18px mdi-pause" title="'.__('Pause advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'inactivate', $ad['FeaturedAd']['id']),
								array('escape' => false),
								__('Are you sure you want to pause "%s"?', $ad['FeaturedAd']['title'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<?=
								$this->UserForm->postLink('<h6>'.h(__($ad['FeaturedAd']['status'])).'<i class="mdi mdi-18px mdi-play" title="'.__('Activate advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'activate', $ad['FeaturedAd']['id']),
								array('escape' => false),
								__('Are you sure you want to activate "%s"?', $ad['FeaturedAd']['title'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<h6><?=__('Pending')?></h6>
							<?php endif; ?>
						</div>
						<?php if($ad['FeaturedAd']['package_type'] != 'Days' || $auto_approve): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-pencil"></i>', array('action' => 'edit', $ad['FeaturedAd']['id']), array(
							'escape' => false,
							'title' => __('Click to edit advertisement data'),
							'uk-tooltip' => '',
							))
							?>
						<?php endif; ?>
						<?php if($ad['FeaturedAd']['status'] != 'Pending'): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-plus"></i>', array('action' => 'assignTo', $ad['FeaturedAd']['id']), array(
							'escape' => false,
							'title' => __('Add more %s', empty($ad['FeaturedAd']['package_type']) ? __('exposures') : __(strtolower($ad['FeaturedAd']['package_type']))),
							'uk-tooltip' => '',
							))
							?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-chart-bar"></i>', array('action' => 'statistics', $ad['FeaturedAd']['id']), array(
							'escape' => false,
							'title' => __('Show statistics'),
							'uk-tooltip' => '',
							))
							?>
						<i class="mdi mdi-18px mdi-information" uk-tooltip title="
							<?=__('Exposure type')?>: 
							<?= empty($ad['FeaturedAd']['package_type']) ? __('Not assigned') : __($ad['FeaturedAd']['package_type'])?>
							<br />
							<?=__('Total clicks')?>: 
							<?=h($ad['FeaturedAd']['total_clicks']);?>
							<br />
							<?=__('Clicks today')?>: 
							<?=isset($clicksToday[$ad['FeaturedAd']['id']]) ? h($clicksToday[$ad['FeaturedAd']['id']]) : 0?>
							<br />
							<?php if(!empty($ad['FeaturedAd']['package_type'])): ?>
							<?php if($ad['FeaturedAd']['package_type'] == 'Days'): ?>
							<?=__('Valid until')?>: <?=$this->Time->nice($ad['FeaturedAd']['start']." + {$ad['FeaturedAd']['expiry']} day")?>
							<?php else: ?>
							<?=__('%s left', __($ad['FeaturedAd']['package_type']))?> <?=$ad['FeaturedAd']['expiry'] - $ad['FeaturedAd'][Inflector::tableize($ad['FeaturedAd']['package_type'])]?>
							<?php endif; ?>
							<?php endif; ?>
							">
						</i>
						<?php endif; ?>
						<?=
							$this->UserForm->postLink('<i class="mdi mdi-18px mdi-delete" title="'.__('Delete this advertisement').'" uk-tooltip></i>',
							array('action' => 'delete', $ad['FeaturedAd']['id']),
							array('escape' => false),
							__('Are you sure you want to delete "%s"?', $ad['FeaturedAd']['title'])
							)
							?>
					</div>
				</div>
			</div>
			<?php endforeach;?>
		</div>
	</div>
	<?php endif; ?>
</div>
