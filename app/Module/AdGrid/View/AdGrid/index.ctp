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
							<h5><?=__d('ad_grid', 'AdGrid Ads Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('action' => 'add'),
								'buy' => array('action' => 'buy'),
								'assign' => array('action' => 'assign'),
							))
							?>
						<?php if(empty($ads)): ?>
						<div class="row">
							<div class="col-md-12">
								<h6 class="text-xs-center"><?=__d('ad_grid', 'You have no advertisement campaigns at the moment. Please click %s to add one.', $this->Html->link(__d('ad_grid', 'here'), array('action' => 'add')))?></h6>
							</div>
						</div>
						<?php else: ?>
						<div class="padding30-col">
							<h5><?=__d('ad_grid', 'My current AdGrid ads advertisements')?></h5>
							<?php foreach($ads as $ad): ?>
							<div class="margin30-top advert">
								<div class="row">
									<div class="col-md-6">
										<h6><?=$this->Html->link($ad['AdGridAd']['url'], null, array('target' => 'blank'))?></h6>
									</div>
									<div class="col-md-6 text-xs-right advertcontrols">
										<div class="advertstatus">
											<?php if($ad['AdGridAd']['status'] != 'Pending'): ?>
											<?php if($ad['AdGridAd']['status'] == 'Active'): ?>
												<h6><?=h(__d('ad_grid', $ad['AdGridAd']['status']));?>
												<?php if($ad['AdGridAd']['package_type'] != 'Days'): ?>
													<?=
														$this->UserForm->postLink('<i class="fa fa-pause" title="'.__d('ad_grid', 'Pause advertisement').'" data-toggle="tooltip" data-placement="top"></i></h6>',
															array('action' => 'inactivate', $ad['AdGridAd']['id']),
															array('escape' => false),
															__d('ad_grid', 'Are you sure you want to pause "%s"?', $ad['AdGridAd']['url'])
														)
													?>
												<?php endif; ?>
											<?php else: ?>
											<?=
												$this->UserForm->postLink('<h6>'.h(__d('ad_grid', $ad['AdGridAd']['status'])).'<i class="fa fa-play" title="'.__d('ad_grid', 'Activate advertisement').'" data-toggle="tooltip" data-placement="top"></i></h6>',
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
											$this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $ad['AdGridAd']['id']), array(
												'escape' => false,
												'title' => __d('ad_grid', 'Click to edit advertisement data'),
												'data-toggle' => 'tooltip',
												'data-placement' => 'top',
											))
											?>
										<?php endif; ?>
										<?php if($ad['AdGridAd']['status'] != 'Pending'): ?>
										<?=
											$this->Html->link('<i class="fa fa-plus"></i>', array('action' => 'assignTo', $ad['AdGridAd']['id']), array(
												'escape' => false,
												'title' => __d('ad_grid', 'Add more %s', empty($ad['AdGridAd']['package_type']) ? __d('ad_grid', 'exposures') : __d('ad_grid', strtolower($ad['AdGridAd']['package_type']))),
												'data-toggle' => 'tooltip',
												'data-placement' => 'top',
											))
											?>
										<?=
											$this->Html->link('<i class="fa fa-bar-chart"></i>', array('action' => 'statistics', $ad['AdGridAd']['id']), array(
												'escape' => false,
												'title' => __d('ad_grid', 'Show statistics'),
												'data-toggle' => 'tooltip',
												'data-placement' => 'top',
											))
											?>
										<i class="fa fa-info" data-toggle="tooltip" data-html="true" title="
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
											$this->UserForm->postLink('<i class="fa fa-trash" title="'.__d('ad_grid', 'Delete this advertisement').'" data-toggle="tooltip" data-placement="left"></i>',
												array('action' => 'delete', $ad['AdGridAd']['id']),
												array('escape' => false),
												__d('ad_grid', 'Are you sure you want to delete "%s"?', $ad['AdGridAd']['id'])
											)
											?>
									</div>
									<div class="clearfix"></div>
									<div class="advertline"></div>
								</div>
							</div>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
