<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Advertisement Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'ads', 'action' => 'add'),
				'buy' => array('controller' => 'ads_category_packages', 'action' => 'buy'),
				'assign' => array('controller' => 'ads', 'action' => 'assign'),
				))
				?>
			<?php if(empty($ads)): ?>
			<div class="uk-width-1-1">
				<h6 class="uk-text-center uk-margin-top"><?=__('You have no advertisement campaigns at the moment. Please click %s to add one.', $this->Html->link(__('here'), array('action' => 'add')))?></h6>
			</div>
			<?php else: ?>
			<h2 class="uk-margin-top"><?=__('My Current Paid To Click Advertisements')?></h2>
			<?php foreach($ads as $ad): ?>
			<div class="uk-card uk-card-body uk-margin-top advertlist">
				<div class="uk-child-width-1-2@m" uk-grid>
					<div>
						<h6><?=h($ad['Ad']['title'])?></h6>
						<p><?=h($ad['Ad']['description'])?></p>
						<p class="advertcategory"><?=h($ad['AdsCategory']['name'])?></p>
						<div class="adverturl"><?=$this->Html->link($ad['Ad']['url'])?></div>
					</div>
					<div class="uk-text-right">
						<div class="advertstatus">
							<?php if($ad['Ad']['status'] != 'Pending'): ?>
							<?php if($ad['Ad']['status'] == 'Active'): ?>
							<h6>
							<?=h(__($ad['Ad']['status']));?>
							<?php if($ad['Ad']['package_type'] != 'Days'): ?>
							<?=
								$this->UserForm->postLink('<i class="mdi mdi-18px mdi-pause" title="'.__('Pause advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'inactivate', $ad['Ad']['id']),
								array('escape' => false),
								__('Are you sure you want to pause "%s"?', $ad['Ad']['title'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<?=
								$this->UserForm->postLink('<h6>'.h(__($ad['Ad']['status'])).'<i class="mdi mdi-18px mdi-play" title="'.__('Activate advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'activate', $ad['Ad']['id']),
								array('escape' => false),
								__('Are you sure you want to activate "%s"?', $ad['Ad']['title'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<h6><?=__('Pending')?></h6>
							<?php endif; ?>
						</div>
						<?php if($ad['Ad']['package_type'] != 'Days' || $auto_approve): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-pencil"></i>', array('action' => 'edit', $ad['Ad']['id']), array(
							'escape' => false,
							'title' => __('Click to edit advertisement data'),
							'uk-tooltip' => ''
							))
							?>
						<?php endif; ?>
						<?php if($ad['Ad']['status'] != 'Pending'): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-plus"></i>', array('controller' => 'ads', 'action' => 'assignTo', $ad['Ad']['id']), array(
							'escape' => false,
							'title' => __('Add more %s', empty($ad['Ad']['package_type']) ? __('exposures') : __(strtolower($ad['Ad']['package_type']))),
							'uk-tooltip' => ''
							))
							?>
						<?php if($ad['AdsCategory']['name']): ?>
						<?php if($ad['AdsCategory']['geo_targetting']): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-earth"></i>', array('action' => 'targetting', 'geo', $ad['Ad']['id']), array(
							'escape' => false,
							'title' => __('Click to set geotargeting'),
							'uk-tooltip' => '',
							))
							?>
						<?php endif; ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-account-multiple"></i>', array('action' => 'targetting', 'memberships', $ad['Ad']['id']), array(
							'escape' => false,
							'title' => __('Click to set memberships targeting'),
							'uk-tooltip' => '',
							))
							?>
						<?php endif; ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-chart-bar"></i>', array('action' => 'statistics', $ad['Ad']['id']), array(
							'escape' => false,
							'title' => __('Show statistics'),
							'uk-tooltip' => '',
							))
							?>
						<i class="mdi mdi-18px mdi-information" uk-tooltip title="<?=__('Exposure type')?>: <?=h(__($ad['Ad']['package_type']))?><br />
							<?=__('Total clicks')?>: <?=h($ad['Ad']['clicks']);?><br />
							<?=__('Clicks today')?>: <?=isset($clicksToday[$ad['Ad']['id']]) ? h($clicksToday[$ad['Ad']['id']]) : 0?><br />
							<?php if(empty($ad['Ad']['package_type']) || ($ad['Ad']['package_type'] == 'Days' && $ad['Ad']['expiry'] == 0)):?>
							<?=__('%s left', __('Exposures'))?>: <?=h($ad['Ad']['expiry'])?>
							<?php elseif($ad['Ad']['package_type'] == 'Clicks'): ?>
							<?=__('%s left', __('Clicks'))?>: <?=h($ad['Ad']['expiry'])?>
							<?php elseif($ad['Ad']['package_type'] == 'Days'): ?>
							<?=__('Expires at: %s', $this->Time->nice($ad['Ad']['expiry_date']))?>
							<?php endif;?>
							<br />
							<?=__('Outside clicks')?>: <?=h($ad['Ad']['outside_clicks'])?>">
						</i>
						<?php endif; ?>
						<?=
							$this->UserForm->postLink('<i class="mdi mdi-18px mdi-delete" title="'.__('Delete this advertisement').'" uk-tooltip></i>',
							array('action' => 'delete', $ad['Ad']['id']),
							array('escape' => false),
							__('Are you sure you want to delete "%s"?', $ad['Ad']['title'])
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
