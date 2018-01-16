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
							<h5><?=__('Advertisement Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('controller' => 'ads', 'action' => 'add'),
								'buy' => array('controller' => 'ads_category_packages', 'action' => 'buy'),
								'assign' => array('controller' => 'ads', 'action' => 'assign'),
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
								<h5><?=__('My Current Paid To Click Advertisements')?></h5>
								<?php foreach($ads as $ad): ?>
									<div class="margin30-top advert">
										<div class="row">
											<div class="col-md-6">
												<h6><?=h($ad['Ad']['title'])?></h6>
												<p><?=h($ad['Ad']['description'])?></p>
												<p class="advertcategory"><?=h($ad['AdsCategory']['name'])?></p>
												<div class="adverturl"><?=$this->Html->link($ad['Ad']['url'])?></div>
											</div>
											<div class="col-md-6 text-xs-right advertcontrols">
												<div class="advertstatus">
													<?php if($ad['Ad']['status'] != 'Pending'): ?>
														<?php if($ad['Ad']['status'] == 'Active'): ?>
															<h6>
															<?=h(__($ad['Ad']['status']));?>
															<?php if($ad['Ad']['package_type'] != 'Days'): ?>
																<?=
																	$this->UserForm->postLink('<i class="fa fa-pause" title="'.__('Pause advertisement').'" data-toggle="tooltip" data-placement="top"></i></h6>',
																		array('action' => 'inactivate', $ad['Ad']['id']),
																		array('escape' => false),
																		__('Are you sure you want to pause "%s"?', $ad['Ad']['title'])
																	)
																?>
															<?php endif; ?>
														<?php else: ?>
															<?=
																$this->UserForm->postLink('<h6>'.h(__($ad['Ad']['status'])).'<i class="fa fa-play" title="'.__('Activate advertisement').'" data-toggle="tooltip" data-placement="top"></i></h6>',
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
														$this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $ad['Ad']['id']), array(
															'escape' => false,
															'title' => __('Click to edit advertisement data'),
															'data-toggle' => 'tooltip',
															'data-placement' => 'top',
														))
													?>
												<?php endif; ?>
												<?php if($ad['Ad']['status'] != 'Pending'): ?>
													<?=
														$this->Html->link('<i class="fa fa-plus"></i>', array('controller' => 'ads', 'action' => 'assignTo', $ad['Ad']['id']), array(
															'escape' => false,
															'title' => __('Add more %s', empty($ad['Ad']['package_type']) ? __('exposures') : __(strtolower($ad['Ad']['package_type']))),
															'data-toggle' => 'tooltip',
															'data-placement' => 'top',
														))
													?>
													<?php if($ad['AdsCategory']['name']): ?>
														<?php if($ad['AdsCategory']['geo_targetting']): ?>
															<?=
																$this->Html->link('<i class="fa fa-globe"></i>', array('action' => 'targetting', 'geo', $ad['Ad']['id']), array(
																	'escape' => false,
																	'title' => __('Click to set geotargeting'),
																	'data-toggle' => 'tooltip',
																	'data-placement' => 'top',
																))
															?>
														<?php endif; ?>
														<?=
															$this->Html->link('<i class="fa fa-users"></i>', array('action' => 'targetting', 'memberships', $ad['Ad']['id']), array(
																'escape' => false,
																'title' => __('Click to set memberships targeting'),
																'data-toggle' => 'tooltip',
																'data-placement' => 'top',
															))
														?>
													<?php endif; ?>
													<?=
														$this->Html->link('<i class="fa fa-bar-chart"></i>', array('action' => 'statistics', $ad['Ad']['id']), array(
															'escape' => false,
															'title' => __('Show statistics'),
															'data-toggle' => 'tooltip',
															'data-placement' => 'top',
														))
													?>
													<i class="fa fa-info" data-toggle="tooltip" data-html="true" title="<?=__('Exposure type')?>: <?=h(__($ad['Ad']['package_type']))?><br />
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
													$this->UserForm->postLink('<i class="fa fa-trash" title="'.__('Delete this advertisement').'" data-toggle="tooltip" data-placement="left"></i>',
														array('action' => 'delete', $ad['Ad']['id']),
														array('escape' => false),
														__('Are you sure you want to delete "%s"?', $ad['Ad']['title'])
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
