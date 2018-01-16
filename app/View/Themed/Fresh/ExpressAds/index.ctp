<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Express Advertisements Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'express_ads', 'action' => 'add'),
				'buy' => array('controller' => 'express_ads', 'action' => 'buy'),
				'assign' => array('controller' => 'express_ads', 'action' => 'assign'),
				))
				?>
			<?php if(empty($ads)): ?>
			<div class="uk-width-1-1">
				<h6 class="uk-text-center uk-margin-top"><?=__('You have no advertisement campaigns at the moment. Please click %s to add one.', $this->Html->link(__('here'), array('action' => 'add')))?></h6>
			</div>
		</div>
		<?php else: ?>
		<div class="padding30-col">
			<h2 class="uk-margin-top"><?=__('My Current Express Advertisements')?></h2>
			<?php foreach($ads as $ad): ?>
			<div class="uk-card uk-card-body uk-margin-top advertlist">
				<div class="uk-child-width-1-2@m" uk-grid>
					<div>
						<h6><?=h($ad['ExpressAd']['title'])?></h6>
						<p><?=h($ad['ExpressAd']['description'])?></p>
						<div class="adverturl"><?=$this->Html->link($ad['ExpressAd']['url'])?></div>
					</div>
					<div class="uk-text-right">
						<div class="advertstatus">
							<?php if($ad['ExpressAd']['status'] != 'Pending'): ?>
							<?php if($ad['ExpressAd']['status'] == 'Active'): ?>
							<h6>
							<?=h(__($ad['ExpressAd']['status']));?>
							<?php if($ad['ExpressAd']['package_type'] != 'Days'): ?>
							<?=
								$this->UserForm->postLink('<i class="mdi mdi-18px mdi-pause" title="'.__('Pause advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'inactivate', $ad['ExpressAd']['id']),
								array('escape' => false),
								__('Are you sure you want to pause "%s"?', $ad['ExpressAd']['title'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<h6>
							<?=h(__($ad['ExpressAd']['status']));?>
							<?=
								$this->UserForm->postLink('<i class="mdi mdi-18px mdi-play" title="'.__('Activate advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'activate', $ad['ExpressAd']['id']),
								array('escape' => false),
								__('Are you sure you want to activate "%s"?', $ad['ExpressAd']['title'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<h6><?=__('Pending')?></h6>
							<?php endif; ?>
						</div>
						<?php if($ad['ExpressAd']['package_type'] != 'Days' || $auto_approve): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-pencil"></i>', array('action' => 'edit', $ad['ExpressAd']['id']), array(
							'escape' => false,
							'title' => __('Click to edit advertisement data'),
							'uk-tooltip' => '',
							))
							?>
						<?php endif; ?>
						<?php if($ad['ExpressAd']['status'] != 'Pending'): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-plus"></i>', array('action' => 'assignTo', $ad['ExpressAd']['id']), array(
							'escape' => false,
							'title' => __('Add more %s', empty($ad['ExpressAd']['package_type']) ? __('exposures') : __(strtolower($ad['ExpressAd']['package_type']))),
							'uk-tooltip' => '',
							))
							?>
						<?php if($settings['geo_targetting']): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-earth"></i>', array('action' => 'targetting', 'geo', $ad['ExpressAd']['id']), array(
							'escape' => false,
							'title' => __('Click to set geotargeting'),
							'uk-tooltip' => '',
							))
							?>
						<?php endif; ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-account-multiple"></i>', array('action' => 'targetting', 'memberships', $ad['ExpressAd']['id']), array(
							'escape' => false,
							'title' => __('Click to set memberships targeting'),
							'uk-tooltip' => '',
							))
							?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-chart-bar"></i>', array('action' => 'statistics', $ad['ExpressAd']['id']), array(
							'escape' => false,
							'title' => __('Show statistics'),
							'uk-tooltip' => '',
							))
							?>
						<i class="mdi mdi-18px mdi-information" uk-tooltip title="<?=__('Exposure type')?>: <?=h(__($ad['ExpressAd']['package_type']))?><br />
							<?=__('Total clicks')?>: <?=h($ad['ExpressAd']['clicks']);?><br />
							<?=__('Clicks today')?>: <?=isset($clicksToday[$ad['ExpressAd']['id']]) ? h($clicksToday[$ad['ExpressAd']['id']]) : 0?><br />
							<?php if(empty($ad['ExpressAd']['package_type']) || ($ad['ExpressAd']['package_type'] == 'Days' && $ad['ExpressAd']['expiry'] == 0)):?>
							<?=__('%s left', __('Exposures'))?>: <?=h($ad['ExpressAd']['expiry'])?>
							<?php elseif($ad['ExpressAd']['package_type'] == 'Clicks'): ?>
							<?=__('%s left', __('Clicks'))?>: <?=h($ad['ExpressAd']['expiry'])?>
							<?php elseif($ad['ExpressAd']['package_type'] == 'Days'): ?>
							<?=__('Expires at: %s', $this->Time->nice($ad['ExpressAd']['expiry_date']))?>
							<?php endif;?>
							<br />
							<?=__('Outside clicks')?>: <?=h($ad['ExpressAd']['outside_clicks'])?>">
						</i>
						<?php endif; ?>
						<?=
							$this->UserForm->postLink('<i class="mdi mdi-18px mdi-delete" title="'.__('Delete this advertisement').'" uk-tooltip></i>',
							array('action' => 'delete', $ad['ExpressAd']['id']),
							array('escape' => false),
							__('Are you sure you want to delete "%s"?', $ad['ExpressAd']['title'])
							)
							?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php endif; ?>
</div>
