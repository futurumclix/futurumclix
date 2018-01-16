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
							<h5><?=__('Express Advertisements Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('controller' => 'express_ads', 'action' => 'add'),
								'buy' => array('controller' => 'express_ads', 'action' => 'buy'),
								'assign' => array('controller' => 'express_ads', 'action' => 'assign'),
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
								<h5><?=__('My Current Express Advertisements')?></h5>
								<?php foreach($ads as $ad): ?>
									<div class="margin30-top advert">
										<div class="row">
											<div class="col-md-6">
												<h6><?=h($ad['ExpressAd']['title'])?></h6>
												<p><?=h($ad['ExpressAd']['description'])?></p>
												<div class="adverturl"><?=$this->Html->link($ad['ExpressAd']['url'])?></div>
											</div>
											<div class="col-md-6 text-xs-right advertcontrols">
												<div class="advertstatus">
													<?php if($ad['ExpressAd']['status'] != 'Pending'): ?>
														<?php if($ad['ExpressAd']['status'] == 'Active'): ?>
															<h6><?=h(__($ad['ExpressAd']['status']));?>
															<?php if($ad['ExpressAd']['package_type'] != 'Days'): ?>
																<?=
																	$this->UserForm->postLink('<i class="fa fa-pause" title="'.__('Pause advertisement').'" data-toggle="tooltip" data-placement="top"></i></h6>',
																		array('action' => 'inactivate', $ad['ExpressAd']['id']),
																		array('escape' => false),
																		__('Are you sure you want to pause "%s"?', $ad['ExpressAd']['title'])
																	)
																?>
															<?php endif; ?>
														<?php else: ?>
															<h6><?=h(__($ad['ExpressAd']['status']));?>
															<?=
																$this->UserForm->postLink('<i class="fa fa-play" title="'.__('Activate advertisement').'" data-toggle="tooltip" data-placement="top"></i></h6>',
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
														$this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $ad['ExpressAd']['id']), array(
															'escape' => false,
															'title' => __('Click to edit advertisement data'),
															'data-toggle' => 'tooltip',
															'data-placement' => 'top',
														))
													?>
												<?php endif; ?>
												<?php if($ad['ExpressAd']['status'] != 'Pending'): ?>
													<?=
														$this->Html->link('<i class="fa fa-plus"></i>', array('action' => 'assignTo', $ad['ExpressAd']['id']), array(
															'escape' => false,
															'title' => __('Add more %s', empty($ad['ExpressAd']['package_type']) ? __('exposures') : __(strtolower($ad['ExpressAd']['package_type']))),
															'data-toggle' => 'tooltip',
															'data-placement' => 'top',
														))
														?>
													<?php if($settings['geo_targetting']): ?>
														<?=
															$this->Html->link('<i class="fa fa-globe"></i>', array('action' => 'targetting', 'geo', $ad['ExpressAd']['id']), array(
																'escape' => false,
																'title' => __('Click to set geotargeting'),
																'data-toggle' => 'tooltip',
																'data-placement' => 'top',
															))
														?>
													<?php endif; ?>
													<?=
														$this->Html->link('<i class="fa fa-users"></i>', array('action' => 'targetting', 'memberships', $ad['ExpressAd']['id']), array(
															'escape' => false,
															'title' => __('Click to set memberships targeting'),
															'data-toggle' => 'tooltip',
															'data-placement' => 'top',
														))
													?>
													<?=
														$this->Html->link('<i class="fa fa-bar-chart"></i>', array('action' => 'statistics', $ad['ExpressAd']['id']), array(
															'escape' => false,
															'title' => __('Show statistics'),
															'data-toggle' => 'tooltip',
															'data-placement' => 'top',
														))
													?>
													<i class="fa fa-info" data-toggle="tooltip" data-html="true" title="<?=__('Exposure type')?>: <?=h(__($ad['ExpressAd']['package_type']))?><br />
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
													$this->UserForm->postLink('<i class="fa fa-trash" title="'.__('Delete this advertisement').'" data-toggle="tooltip" data-placement="left"></i>',
														array('action' => 'delete', $ad['ExpressAd']['id']),
														array('escape' => false),
														__('Are you sure you want to delete "%s"?', $ad['ExpressAd']['title'])
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
