<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__d('ad_grid', 'AdGrid Ads Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('action' => 'add'),
				'buy' => array('action' => 'buy'),
				'assign' => array('action' => 'assign'),
				))
				?>
			<?php if(empty($ads)): ?>
			<div class="uk-width-1-1">
				<h6 class="uk-text-center uk-margin-top"><?=__d('ad_grid', 'You have no advertisement campaigns at the moment. Please click %s to add one.', $this->Html->link(__d('ad_grid', 'here'), array('action' => 'add')))?></h6>
			</div>
			<?php else: ?>
			<h2 class="uk-margin-top"><?=__d('ad_grid', 'My current AdGrid ads advertisements')?></h2>
			<?php foreach($ads as $ad): ?>
			<div class="uk-card uk-card-body uk-margin-top advertlist">
				<div class="uk-child-width-1-2@m" uk-grid>
					<div>
						<h6><?=$this->Html->link($ad['AdGridAd']['url'], null, array('target' => 'blank'))?></h6>
					</div>
					<div class="uk-text-right">
						<div class="advertstatus">
							<?php if($ad['AdGridAd']['status'] != 'Pending'): ?>
							<?php if($ad['AdGridAd']['status'] == 'Active'): ?>
							<h6>
							<?=h(__d('ad_grid', $ad['AdGridAd']['status']));?>
							<?php if($ad['AdGridAd']['package_type'] != 'Days'): ?>
							<?=
								$this->UserForm->postLink('<i class="mdi mdi-18px mdi-pause" title="'.__d('ad_grid', 'Pause advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'inactivate', $ad['AdGridAd']['id']),
								array('escape' => false),
								__d('ad_grid', 'Are you sure you want to pause "%s"?', $ad['AdGridAd']['url'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<?=
								$this->UserForm->postLink('<h6>'.h(__d('ad_grid', $ad['AdGridAd']['status'])).'<i class="mdi mdi-18px mdi-play" title="'.__d('ad_grid', 'Activate advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'activate', $ad['AdGridAd']['id']),
								array('escape' => false),
								__d('ad_grid', 'Are you sure you want to activate "%s"?', $ad['AdGridAd']['url'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<h6><?=__d('ad_grid', 'Pending')?></h6>
							<?php endif; ?>
						</div>
						<?php if($ad['AdGridAd']['package_type'] != 'Days' || $auto_approve): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-pencil"></i>', array('action' => 'edit', $ad['AdGridAd']['id']), array(
							'escape' => false,
							'title' => __d('ad_grid', 'Click to edit advertisement data'),
							'uk-tooltip' => '',
							))
							?>
						<?php endif; ?>
						<?php if($ad['AdGridAd']['status'] != 'Pending'): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-plus"></i>', array('action' => 'assignTo', $ad['AdGridAd']['id']), array(
							'escape' => false,
							'title' => __d('ad_grid', 'Add more %s', empty($ad['AdGridAd']['package_type']) ? __d('ad_grid', 'exposures') : __d('ad_grid', strtolower($ad['AdGridAd']['package_type']))),
							'uk-tooltip' => ''
							))
							?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-chart-bar"></i>', array('action' => 'statistics', $ad['AdGridAd']['id']), array(
							'escape' => false,
							'title' => __d('ad_grid', 'Show statistics'),
							'uk-tooltip' => ''
							))
							?>
						<i class="mdi mdi-18px mdi-information" uk-tooltip title="
							<?=__d('ad_grid', 'Exposure type')?>: 
							<?=empty($ad['AdGridAd']['package_type']) ? __d('ad_grid', 'Not assigned') : __d('ad_grid', $ad['AdGridAd']['package_type'])?>
							<br />
							<?=__d('ad_grid', 'Total clicks')?>: 
							<?=h($ad['AdGridAd']['total_clicks'])?>
							<br />
							<?=__d('ad_grid', 'Clicks today')?>: 
							<?=isset($clicksToday[$ad['AdGridAd']['id']]) ? h($clicksToday[$ad['AdGridAd']['id']]) : 0?>
							<br />
							<?php if(!empty($ad['AdGridAd']['package_type'])): ?>
							<?php if($ad['AdGridAd']['package_type'] == 'Days'): ?>
							<?=__d('ad_grid', 'Valid until')?>: <?=$this->Time->nice($ad['AdGridAd']['start']." + {$ad['AdGridAd']['expiry']} day")?>
							<?php else: ?>
							<?=__d('ad_grid', '%s left', __d('ad_grid', $ad['AdGridAd']['package_type']))?> <?=$ad['AdGridAd']['expiry'] - $ad['AdGridAd'][Inflector::tableize($ad['AdGridAd']['package_type'])]?>
							<?php endif; ?>
							<?php endif; ?>
							">
						</i>
						<?php endif; ?>
						<?=
							$this->UserForm->postLink('<i class="mdi mdi-18px mdi-delete" title="'.__d('ad_grid', 'Delete this advertisement').'" uk-tooltip></i>',
							array('action' => 'delete', $ad['AdGridAd']['id']),
							array('escape' => false),
							__d('ad_grid', 'Are you sure you want to delete "%s"?', $ad['AdGridAd']['id'])
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
