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
							<h5><?=__('Banner Ads Panel')?></h5>
						</div>
						<?=
							$this->element('adsPanelBoxes', array(
								'add' => array('controller' => 'banner_ads', 'action' => 'add'),
								'buy' => array('controller' => 'banner_ads', 'action' => 'buy'),
								'assign' => array('controller' => 'banner_ads', 'action' => 'assign'),
							))
							?>
						<?php if(empty($ads)): ?>
							<div class="row">
								<div class="col-md-12">
									<h6 class="text-xs-center"><?=__('You have no advertisement campaigns at the moment. Please click %s to add one.', $this->Html->link(__('here'), array('controller' => 'bannerAds', 'action' => 'add')))?></h6>
								</div>
							</div>
						<?php else: ?>
							<div class="padding30-col">
								<h5><?=__('My current Banner ads advertisements')?></h5>
								<?php foreach($ads as $ad): ?>
									<div class="margin30-top advert">
										<div class="row">
											<div class="col-md-6">
												<?=
													$this->Html->image($ad['BannerAd']['image_url'], array(
														'title' => $ad['BannerAd']['title'],
														'alt' => $ad['BannerAd']['title'],
														'width' => $bannerSize['width'],
														'height' => $bannerSize['height'],
														'border' => 0,
													))
													?>
												<h6><?=h($ad['BannerAd']['title'])?></h6>
												<div class="adverturl"><?=$this->Html->link($ad['BannerAd']['url'], null, array('target' => 'blank'))?></div>
												<p><?=$this->Html->link($ad['BannerAd']['image_url'], null, array('target' => 'blank'))?></p>
											</div>
											<div class="col-md-6 text-xs-right advertcontrols">
												<div class="advertstatus">
													<?php if($ad['BannerAd']['status'] != 'Pending'): ?>
														<?php if($ad['BannerAd']['status'] == 'Active'): ?>
															<h6><?=h(__($ad['BannerAd']['status']));?>
															<?php if($ad['BannerAd']['package_type'] != 'Days'): ?>
																<?=
																	$this->UserForm->postLink('<i class="fa fa-pause" title="'.__('Pause advertisement').'" data-toggle="tooltip" data-placement="top"></i></h6>',
																		array('action' => 'inactivate', $ad['BannerAd']['id']),
																		array('escape' => false),
																		__('Are you sure you want to pause "%s"?', $ad['BannerAd']['title'])
																	)
																?>
															<?php endif; ?>
														<?php else: ?>
														<?=
															$this->UserForm->postLink('<h6>'.h(__($ad['BannerAd']['status'])).'<i class="fa fa-play" title="'.__('Activate advertisement').'" data-toggle="tooltip" data-placement="top"></i></h6>',
																array('action' => 'activate', $ad['BannerAd']['id']),
																array('escape' => false),
																__('Are you sure you want to activate "%s"?', $ad['BannerAd']['title'])
															)
															?>
														<?php endif; ?>
													<?php else: ?>
														<h6><?=__('Pending')?></h6>
													<?php endif; ?>
												</div>
												<?php if($ad['BannerAd']['package_type'] != 'Days' || $auto_approve): ?>
													<?=
														$this->Html->link('<i class="fa fa-pencil"></i>', array('action' => 'edit', $ad['BannerAd']['id']), array(
															'escape' => false,
															'title' => __('Click to edit advertisement data'),
															'data-toggle' => 'tooltip',
															'data-placement' => 'top',
														))
													?>
												<?php endif; ?>
												<?php if($ad['BannerAd']['status'] != 'Pending'): ?>
													<?=
														$this->Html->link('<i class="fa fa-plus"></i>', array('action' => 'assignTo', $ad['BannerAd']['id']), array(
															'escape' => false,
															'title' => __('Add more %s', empty($ad['BannerAd']['package_type']) ? __('exposures') : __(strtolower($ad['BannerAd']['package_type']))),
															'data-toggle' => 'tooltip',
															'data-placement' => 'top',
														))
													?>
													<?=
														$this->Html->link('<i class="fa fa-bar-chart"></i>', array('action' => 'statistics', $ad['BannerAd']['id']), array(
															'escape' => false,
															'title' => __('Show statistics'),
															'data-toggle' => 'tooltip',
															'data-placement' => 'top',
														))
													?>
													<i class="fa fa-info" data-toggle="tooltip" data-html="true" title="
														<?=__('Exposure type')?>: 
														<?= empty($ad['BannerAd']['package_type']) ? __('Not assigned') : __($ad['BannerAd']['package_type'])?>
														<br />
														<?=__('Total clicks')?>: 
														<?=h($ad['BannerAd']['total_clicks']);?>
														<br />
														<?=__('Clicks today')?>: 
														<?=isset($clicksToday[$ad['BannerAd']['id']]) ? h($clicksToday[$ad['BannerAd']['id']]) : 0?>
														<br />
														<?php if(!empty($ad['BannerAd']['package_type'])): ?>
															<?php if($ad['BannerAd']['package_type'] == 'Days'): ?>
																<?=__('Valid until')?>: <?=$this->Time->nice($ad['BannerAd']['start']." + {$ad['BannerAd']['expiry']} day")?>
															<?php else: ?>
																<?=__('%s left', __($ad['BannerAd']['package_type']))?> <?=$ad['BannerAd']['expiry'] - $ad['BannerAd'][Inflector::tableize($ad['BannerAd']['package_type'])]?>
															<?php endif; ?>
														<?php endif; ?>
														">
													</i>
												<?php endif; ?>
												<?=
													$this->UserForm->postLink('<i class="fa fa-trash" title="'.__('Delete this advertisement').'" data-toggle="tooltip" data-placement="left"></i>',
														array('action' => 'delete', $ad['BannerAd']['id']),
														array('escape' => false),
														__('Are you sure you want to delete "%s"?', $ad['BannerAd']['title'])
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
