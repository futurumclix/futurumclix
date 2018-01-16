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
							<h5><?=__('Explorer Advertisement Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('controller' => 'explorer_ads', 'action' => 'add'),
								'buy' => array('controller' => 'explorer_ads', 'action' => 'buy'),
								'assign' => array('controller' => 'explorer_ads', 'action' => 'assign'),
							))
						?>
						<?php if(empty($ads)): ?>
							<div class="row">
								<div class="col-md-12">
									<h6 class="text-xs-center"><?=__('You have no advertisement campaigns at the moment. Please click %s to add one.', $this->Html->link(__('here'), array('action' => 'add')))?></h6>
								</div>
							</div>
						<?php else: ?>
							<div class="padding30-col">
								<h5><?=__('My Current Explorer Advertisements')?></h5>
								<?php foreach($ads as $ad): ?>
									<div class="margin30-top advert">
										<div class="row">
											<div class="col-md-6">
												<h6><?=h($ad['ExplorerAd']['title'])?></h6>
												<p><?=h($ad['ExplorerAd']['description'])?></p>
												<div class="adverturl"><?=$this->Html->link($ad['ExplorerAd']['url'])?></div>
											</div>
											<div class="col-md-6 text-xs-right advertcontrols">
												<div class="advertstatus">
													<?php if($ad['ExplorerAd']['status'] != 'Pending'): ?>
														<?php if($ad['ExplorerAd']['status'] == 'Active'): ?>
															<h6><?=h(__($ad['ExplorerAd']['status']));?>
															<?php if($ad['ExplorerAd']['package_type'] != 'Days'): ?>
																<?=
																	$this->UserForm->postLink('<i class="fa fa-pause" title="'.__('Pause advertisement').'" data-toggle="tooltip" data-placement="top"></i></h6>',
																		array('action' => 'inactivate', $ad['ExplorerAd']['id']),
																		array('escape' => false),
																		__('Are you sure you want to pause "%s"?', $ad['ExplorerAd']['title'])
																	)
																?>
															<?php endif; ?>
														<?php else: ?>
															<h6><?=h(__($ad['ExplorerAd']['status']));?>
															<?=
																$this->UserForm->postLink('<i class="fa fa-play" title="'.__('Activate advertisement').'" data-toggle="tooltip" data-placement="top"></i></h6>',
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
														$this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $ad['ExplorerAd']['id']), array(
															'escape' => false,
															'title' => __('Click to edit advertisement data'),
															'data-toggle' => 'tooltip',
															'data-placement' => 'top',
														))
													?>
												<?php endif; ?>
												<?php if($ad['ExplorerAd']['status'] != 'Pending'): ?>
													<?php if($ad['ExplorerAd']['subpages']): ?>
														<?=
															$this->Html->link('<i class="fa fa-plus"></i>', array('action' => 'assignTo', $ad['ExplorerAd']['id']), array(
																'escape' => false,
																'title' => __('Add more %s', empty($ad['ExplorerAd']['package_type']) ? __('exposures') : __(strtolower($ad['ExplorerAd']['package_type']))),
																'data-toggle' => 'tooltip',
																'data-placement' => 'top',
															))
														?>
													<?php endif; ?>
													<?php if($settings['geo_targetting']): ?>
														<?=
															$this->Html->link('<i class="fa fa-globe"></i>', array('action' => 'targetting', 'geo', $ad['ExplorerAd']['id']), array(
																'escape' => false,
																'title' => __('Click to set geotargeting'),
																'data-toggle' => 'tooltip',
																'data-placement' => 'top',
															))
														?>
													<?php endif; ?>
													<?=
														$this->Html->link('<i class="fa fa-users"></i>', array('action' => 'targetting', 'memberships', $ad['ExplorerAd']['id']), array(
															'escape' => false,
															'title' => __('Click to set memberships targeting'),
															'data-toggle' => 'tooltip',
															'data-placement' => 'top',
														))
													?>
												<?php endif; ?>
												<?=
													$this->Html->link('<i class="fa fa-bar-chart"></i>', array('action' => 'statistics', $ad['ExplorerAd']['id']), array(
														'escape' => false,
														'title' => __('Show statistics'),
														'data-toggle' => 'tooltip',
														'data-placement' => 'top',
													))
												?>
												<i class="fa fa-info" data-toggle="tooltip" data-html="true" title="<?=__('Exposure type')?>: <?=h(__($ad['ExplorerAd']['package_type']))?><br />
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
													$this->UserForm->postLink('<i class="fa fa-trash" title="'.__('Delete this advertisement').'" data-toggle="tooltip" data-placement="left"></i>',
														array('action' => 'delete', $ad['ExplorerAd']['id']),
														array('escape' => false),
														__('Are you sure you want to delete "%s"?', $ad['ExplorerAd']['title'])
													)
												?>
											</div>
											<div class="clearfix"></div>
											<div class="advertline"></div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
