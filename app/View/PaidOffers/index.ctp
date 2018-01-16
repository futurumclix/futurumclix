<?=$this->element('userSidebar')?>
<div class="page">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 padding30-sides">
				<?=$this->element('userBreadcrumbs')?>
				<?=$this->Notice->show()?>
				<div class="panel">
					<div class="padding30-col">
						<div class="col-md-12">
							<h5><?=__('Paid Offers Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('controller' => 'paid_offers', 'action' => 'add'),
								'buy' => array('controller' => 'paid_offers', 'action' => 'buy'),
								'assign' => array('controller' => 'paid_offers', 'action' => 'assign'),
							))
							?>
						<?php if(empty($offers)): ?>
						<div class="row">
							<div class="col-md-12">
								<h6 class="text-xs-center"><?=__('You have no advertisement campaigns at the moment. Please click %s to add one.', $this->Html->link(__('here'), array('controller' => 'paid_offers', 'action' => 'add')))?></h6>
							</div>
						</div>
						<?php else: ?>
						<div class="padding30-col">
							<h5><?=__('My Current Paid Offers Advertisements')?></h5>
							<?php foreach($offers as $offer): ?>
							<div class="margin30-top advert">
								<div class="row">
									<div class="col-md-6">
										<h6><?=h($offer['PaidOffer']['title'])?></h6>
										<p><?=h($offer['PaidOffer']['description'])?></p>
										<p class="advertcategory"><?=h($offer['Category']['name'])?></p>
										<div class="adverturl"><?=$this->Html->link($offer['PaidOffer']['url'])?></div>
									</div>
									<div class="col-md-6 text-xs-right advertcontrols">
										<div class="advertstatus">
											<?php if($offer['PaidOffer']['status'] != 'Pending'): ?>
											<?php if($offer['PaidOffer']['status'] == 'Active'): ?>
											<?=
												$this->UserForm->postLink('<h6>'.h(__($offer['PaidOffer']['status'])).'<i class="fa fa-pause" title="'.__('Pause advertisement').'" data-toggle="tooltip" data-placement="top"></i></h6>',
													array('action' => 'inactivate', $offer['PaidOffer']['id']),
													array('escape' => false),
													__('Are you sure you want to pause "%s"?', $offer['PaidOffer']['title'])
												)
												?>		
											<?php else: ?>
											<?=
												$this->UserForm->postLink('<h6>'.h(__($offer['PaidOffer']['status'])).'<i class="fa fa-play" title="'.__('Activate advertisement').'" data-toggle="tooltip" data-placement="top"></i></h6>',
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
											$this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $offer['PaidOffer']['id']), array(
												'escape' => false,
												'title' => __('Click to edit advertisement data'),
												'data-toggle' => 'tooltip',
												'data-placement' => 'top',
											))
											?>
										<?php if($offer['PaidOffer']['status'] != 'Pending'): ?>
										<?=
											$this->Html->link('<i class="fa fa-plus"></i>', array('action' => 'assignTo', $offer['PaidOffer']['id']), array(
												'escape' => false,
												'title' => __('Add more %s', empty($offer['PaidOffer']['package_type']) ? __('slots') : __(strtolower($offer['PaidOffer']['package_type']))),
												'data-toggle' => 'tooltip',
												'data-placement' => 'top',
											))
											?>
										<div class="btn-group paidoffersmenu">
											<i data-toggle="dropdown" class="fa fa-bar-chart dropdown-toggle"></i>
											<div class="dropdown-menu">
												<div class="dropdown-header"><?=__('Applications')?></div>
												<?=
													$this->Html->link(__('All applications'), array('action' => 'applications', $offer['PaidOffer']['id']), array(
														'escape' => false,
														'class' => 'dropdown-item',
													))
													?>
												<?=
													$this->Html->link(__('Pending applications'), array('action' => 'applications', $offer['PaidOffer']['id'], 'pending'), array(
														'escape' => false,
														'class' => 'dropdown-item',
													))
													?>
												<?=
													$this->Html->link(__('Accepted applications'), array('action' => 'applications', $offer['PaidOffer']['id'], 'accepted'), array(
														'escape' => false,
														'class' => 'dropdown-item',
													))
													?>
												<?=
													$this->Html->link(__('Rejected applications'), array('action' => 'applications', $offer['PaidOffer']['id'], 'rejected'), array(
														'escape' => false,
														'class' => 'dropdown-item',
													))
													?>
											</div>
										</div>
										<i class="fa fa-info" data-toggle="tooltip" data-html="true" title="<?=__('Pending applications')?>: <?=h($offer['PaidOffer']['pending_applications'])?><br /><?=__('Applications today')?>: <?=h(isset($today[$offer['PaidOffer']['id']]) ? $today[$offer['PaidOffer']['id']] : 0)?><br /><?=__('Total applications')?>: <?=h($offer['PaidOffer']['taken_slots'])?><br /><?=__('Slots left')?>: <?=h($offer['PaidOffer']['slots_left'])?>"></i>
										<?php endif; ?>
										<?=
											$this->UserForm->postLink('<i class="fa fa-trash" title="'.__('Delete this advertisement').'" data-toggle="tooltip" data-placement="left"></i>',
												array('action' => 'delete', $offer['PaidOffer']['id']),
												array('escape' => false),
												__('Are you sure you want to delete "%s"?', $offer['PaidOffer']['title'])
											)
											?>
									</div>
									<div class="clearfix"></div>
									<div class="advertline"></div>
								</div>
							</div>
							<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
