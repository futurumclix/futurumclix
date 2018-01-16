<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Explorer Advertisement Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'explorer_ads', 'action' => 'add'),
				'buy' => array('controller' => 'explorer_ads', 'action' => 'buy'),
				'assign' => array('controller' => 'explorer_ads', 'action' => 'assign'),
				))
				?>
			<?php if(empty($ads)): ?>
			<div class="uk-width-1-1">
				<h6 class="uk-text-center uk-margin-top"><?=__('You have no advertisement campaigns at the moment. Please click %s to add one.', $this->Html->link(__('here'), array('action' => 'add')))?></h6>
			</div>
			<?php else: ?>
			<h2 class="uk-margin-top"><?=__('My Current Explorer Advertisements')?></h2>
			<?php foreach($ads as $ad): ?>
			<div class="uk-card uk-card-body uk-margin-top advertlist">
				<div class="uk-child-width-1-2@m" uk-grid>
					<div>
						<h6><?=h($ad['ExplorerAd']['title'])?></h6>
						<p><?=h($ad['ExplorerAd']['description'])?></p>
						<div class="adverturl"><?=$this->Html->link($ad['ExplorerAd']['url'])?></div>
					</div>
					<div class="uk-text-right">
						<div class="advertstatus">
							<?php if($ad['ExplorerAd']['status'] != 'Pending'): ?>
							<?php if($ad['ExplorerAd']['status'] == 'Active'): ?>
							<h6>
							<?=h(__($ad['ExplorerAd']['status']));?>
							<?php if($ad['ExplorerAd']['package_type'] != 'Days'): ?>
							<?=
								$this->UserForm->postLink('<i class="mdi mdi-18px mdi-pause" title="'.__('Pause advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'inactivate', $ad['ExplorerAd']['id']),
								array('escape' => false),
								__('Are you sure you want to pause "%s"?', $ad['ExplorerAd']['title'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<h6>
							<?=h(__($ad['ExplorerAd']['status']));?>
							<?=
								$this->UserForm->postLink('<i class="mdi mdi-18px mdi-play" title="'.__('Activate advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'activate', $ad['ExplorerAd']['id']),
								array('escape' => false),
								__('Are you sure you want to activate "%s"?', $ad['ExplorerAd']['title'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<h6><?=__('Pending')?></h6>
							<?php endif; ?>
						</div>
						<?php if($ad['ExplorerAd']['package_type'] != 'Days' || $auto_approve): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-pencil"></i>', array('action' => 'edit', $ad['ExplorerAd']['id']), array(
							'escape' => false,
							'title' => __('Click to edit advertisement data'),
							'uk-tooltip' => '',
							))
							?>
						<?php endif; ?>
						<?php if($ad['ExplorerAd']['status'] != 'Pending'): ?>
						<?php if($ad['ExplorerAd']['subpages']): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-plus"></i>', array('action' => 'assignTo', $ad['ExplorerAd']['id']), array(
							'escape' => false,
							'title' => __('Add more %s', empty($ad['ExplorerAd']['package_type']) ? __('exposures') : __(strtolower($ad['ExplorerAd']['package_type']))),
							'uk-tooltip' => '',
							))
							?>
						<?php endif; ?>
						<?php if($settings['geo_targetting']): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-earth"></i>', array('action' => 'targetting', 'geo', $ad['ExplorerAd']['id']), array(
							'escape' => false,
							'title' => __('Click to set geotargeting'),
							'uk-tooltip' => '',
							))
							?>
						<?php endif; ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-account-multiple"></i>', array('action' => 'targetting', 'memberships', $ad['ExplorerAd']['id']), array(
							'escape' => false,
							'title' => __('Click to set memberships targeting'),
							'uk-tooltip' => '',
							))
							?>
						<?php endif; ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-chart-bar"></i>', array('action' => 'statistics', $ad['ExplorerAd']['id']), array(
							'escape' => false,
							'title' => __('Show statistics'),
							'uk-tooltip' => '',
							))
							?>
						<i class="mdi mdi-18px mdi-information" uk-tooltip title="<?=__('Exposure type')?>: <?=h(__($ad['ExplorerAd']['package_type']))?><br />
							<?=__('Total clicks')?>: <?=h($ad['ExplorerAd']['clicks']);?><br />
							<?=__('Clicks today')?>: <?=isset($clicksToday[$ad['ExplorerAd']['id']]) ? h($clicksToday[$ad['ExplorerAd']['id']]) : 0?><br />
							<?php if($ad['ExplorerAd']['subpages']): ?>
							<?=__('SubPages')?>: <?=h($ad['ExplorerAd']['subpages'])?><br />
							<?php endif; ?>
							<?php if(empty($ad['ExplorerAd']['package_type']) || ($ad['ExplorerAd']['package_type'] == 'Days' && $ad['ExplorerAd']['expiry'] == 0)):?>
							<?=__('%s left', __('Exposures'))?>: <?=h($ad['ExplorerAd']['expiry'])?>
							<?php elseif($ad['ExplorerAd']['package_type'] == 'Clicks'): ?>
							<?=__('%s left', __('Clicks'))?>: <?=h($ad['ExplorerAd']['expiry'])?>
							<?php elseif($ad['ExplorerAd']['package_type'] == 'Days'): ?>
							<?=__('Expires at: %s', $this->Time->nice($ad['ExplorerAd']['expiry_date']))?>
							<?php endif;?>
							<br />
							<?=__('Outside clicks')?>: <?=h($ad['ExplorerAd']['outside_clicks'])?>">
						</i>
						<?=
							$this->UserForm->postLink('<i class="mdi mdi-18px mdi-delete" title="'.__('Delete this advertisement').'" uk-tooltip></i>',
							array('action' => 'delete', $ad['ExplorerAd']['id']),
							array('escape' => false),
							__('Are you sure you want to delete "%s"?', $ad['ExplorerAd']['title'])
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
