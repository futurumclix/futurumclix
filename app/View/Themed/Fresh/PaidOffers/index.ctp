<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Paid Offers Panel')?></h2>
			<?=
				$this->element('adsPanelBoxes', array(
				'add' => array('controller' => 'paid_offers', 'action' => 'add'),
				'buy' => array('controller' => 'paid_offers', 'action' => 'buy'),
				'assign' => array('controller' => 'paid_offers', 'action' => 'assign'),
				))
				?>
			<?php if(empty($offers)): ?>
			<div class="uk-width-1-1">
				<h6 class="uk-text-center uk-margin-top"><?=__('You have no advertisement campaigns at the moment. Please click %s to add one.', $this->Html->link(__('here'), array('controller' => 'paid_offers', 'action' => 'add')))?></h6>
			</div>
			<?php else: ?>
			<h2 class="uk-margin-top"><?=__('My Current Paid Offers Advertisements')?></h2>
			<?php foreach($offers as $offer): ?>
			<div class="uk-card uk-card-body uk-margin-top advertlist">
				<div class="uk-child-width-1-2@m" uk-grid>
					<div>
						<h6><?=h($offer['PaidOffer']['title'])?></h6>
						<p><?=h($offer['PaidOffer']['description'])?></p>
						<p class="advertcategory"><?=h($offer['Category']['name'])?></p>
						<div class="adverturl"><?=$this->Html->link($offer['PaidOffer']['url'])?></div>
					</div>
					<div class="uk-text-right">
						<div class="advertstatus">
							<?php if($offer['PaidOffer']['status'] != 'Pending'): ?>
							<?php if($offer['PaidOffer']['status'] == 'Active'): ?>
							<?=
								$this->UserForm->postLink('<h6>'.h(__($offer['PaidOffer']['status'])).'<i class="mdi mdi-18px mdi-pause" title="'.__('Pause advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'inactivate', $offer['PaidOffer']['id']),
								array('escape' => false),
								__('Are you sure you want to pause "%s"?', $offer['PaidOffer']['title'])
								)
								?>		
							<?php else: ?>
							<?=
								$this->UserForm->postLink('<h6>'.h(__($offer['PaidOffer']['status'])).'<i class="mdi mdi-18px mdi-play" title="'.__('Activate advertisement').'" uk-tooltip></i></h6>',
								array('action' => 'activate', $offer['PaidOffer']['id']),
								array('escape' => false),
								__('Are you sure you want to activate "%s"?', $offer['PaidOffer']['title'])
								)
								?>
							<?php endif; ?>
							<?php else: ?>
							<h6><?=__('Pending')?></h6>
							<?php endif; ?>
						</div>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-pencil"></i>', array('action' => 'edit', $offer['PaidOffer']['id']), array(
							'escape' => false,
							'title' => __('Click to edit advertisement data'),
							'uk-tooltip' => '',
							))
							?>
						<?php if($offer['PaidOffer']['status'] != 'Pending'): ?>
						<?=
							$this->Html->link('<i class="mdi mdi-18px mdi-plus"></i>', array('action' => 'assignTo', $offer['PaidOffer']['id']), array(
							'escape' => false,
							'title' => __('Add more %s', empty($offer['PaidOffer']['package_type']) ? __('slots') : __(strtolower($offer['PaidOffer']['package_type']))),
							'uk-tooltip' => '',
							))
							?>
						<i class="mdi mdi-18px mdi-chart-bar" type="button"></i>
						<div uk-dropdown="pos: bottom-center; animation: uk-animation-slide-top-small; duration: 400">
							<ul class="uk-nav uk-dropdown-nav">
								<li class="uk-nav-header"><?=__('Applications')?></li>
								<li><?=
									$this->Html->link(__('All applications'), array('action' => 'applications', $offer['PaidOffer']['id']), array(
									'escape' => false,
									))
									?>
								</li>
								<li><?=
									$this->Html->link(__('Pending applications'), array('action' => 'applications', $offer['PaidOffer']['id'], 'pending'), array(
									'escape' => false,
									))
									?>
								</li>
								<li><?=
									$this->Html->link(__('Accepted applications'), array('action' => 'applications', $offer['PaidOffer']['id'], 'accepted'), array(
									'escape' => false,
									))
									?>
								</li>
								<li><?=
									$this->Html->link(__('Rejected applications'), array('action' => 'applications', $offer['PaidOffer']['id'], 'rejected'), array(
									'escape' => false,
									))
									?>
								</li>
							</ul>
						</div>
						<i class="mdi mdi-18px mdi-information" uk-tooltip title="<?=__('Pending applications')?>: <?=h($offer['PaidOffer']['pending_applications'])?><br /><?=__('Applications today')?>: <?=h(isset($today[$offer['PaidOffer']['id']]) ? $today[$offer['PaidOffer']['id']] : 0)?><br /><?=__('Total applications')?>: <?=h($offer['PaidOffer']['taken_slots'])?><br /><?=__('Slots left')?>: <?=h($offer['PaidOffer']['slots_left'])?>"></i>
						<?php endif; ?>
						<?=
							$this->UserForm->postLink('<i class="mdi mdi-18px mdi-delete" title="'.__('Delete this advertisement').'" uk-tooltip></i>',
							array('action' => 'delete', $offer['PaidOffer']['id']),
							array('escape' => false),
							__('Are you sure you want to delete "%s"?', $offer['PaidOffer']['title'])
							)
							?>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
