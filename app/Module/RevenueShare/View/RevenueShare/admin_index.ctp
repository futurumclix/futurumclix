<style>
.tab-content > .tab-pane:not(.active),
.pill-content > .pill-pane:not(.active) {
    display: block;
    height: 0;
    overflow-y: hidden;
} 
</style>
<div class="col-md-12">
	<div class="title">
		<h2><?=__d('revenue_share_admin', 'Revenue Share')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#settings"><?=__d('revenue_share_admin', 'Settings')?></a></li>
		<?php foreach($memberships as $membership): ?>
			<li><a data-toggle="tab" href="#<?=Inflector::underscore(Inflector::camelize($membership))?>_settings"><?=__d('revenue_share_admin', '%s settings', $membership)?></a></li>
		<?php endforeach; ?>
		<li><a data-toggle="tab" href="#log"><?=__d('revenue_share_admin', 'Share Log')?></a></li>
		<li><a data-toggle="tab" href="#statistics"><?=__d('revenue_share_admin', 'Statistics')?></a></li>
	</ul>
	<div class="tab-content">
		<div id="statistics" class="tab-pane fade in">
			<?=
				$this->Chart->show(array(
						__d('revenue_share_admin', 'Income') => $income,
						__d('revenue_share_admin', 'Outcome') => $outcome,
					),
					array(
						'label' => array(
							'content' => false,
						),
						'continous' => 'day',
						'width' => '100%',
						'height' => '500px',
					)
				)
			?>
		</div>
		<div id="settings" class="tab-pane fade in active">
			<div class="title2">
				<h2><?=__d('revenue_share_admin', 'Settings')?></h2>
			</div>
			<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=__d('revenue_share_admin', 'Show Historic Revenue Shares')?></label>
					<div class="col-sm-2">
						<?=
							$this->AdminForm->input('RevenueShareSettings.revenueShare.showHistoric', array(
								'type' => 'checkbox',
								'data-placement' => 'top',
								'data-toggle' => 'popover',
								'data-trigger' => 'hover',
								'data-content' => __d('revenue_share_admin', 'Check this option to show all finished shares on the user side.'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=__d('revenue_share_admin', 'Upline Activity Requirement')?></label>
					<div class="col-sm-2">
						<?=
							$this->AdminForm->input('RevenueShareSettings.revenueShare.activity', array(
								'type' => 'checkbox',
								'data-placement' => 'top',
								'data-toggle' => 'popover',
								'data-trigger' => 'hover',
								'data-content' => __d('revenue_share_admin', 'Enable to force users to click specified amount of ads (in activity settings) the day before to get credited from his revenue shares.'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=__d('revenue_share_admin', 'Allow Buying With Purchase Balance')?></label>
					<div class="col-sm-2">
						<?=
							$this->AdminForm->input('RevenueShareSettings.revenueShare.purchaseBalance', array(
								'type' => 'checkbox',
								'data-placement' => 'top',
								'data-toggle' => 'popover',
								'data-trigger' => 'focus',
								'data-content' => __d('revenue_share_admin', 'Enable to allow buying shares from purchase balance.'),
							))
						?>
					</div>
				</div>
				<div class="col-sm-12 text-center paddingten">
					<button class="btn btn-primary"><?=__d('revenue_share_admin', 'Save changes')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<?php foreach($memberships as $membership_id => $membership): ?>
			<div id="<?=Inflector::underscore(Inflector::camelize($membership))?>_settings" class="tab-pane fade in">
				<div class="title2">
					<h2><?=__d('revenue_share_admin', '%s Settings', $membership)?></h2>
				</div>
				<?=$this->AdminForm->create(false, array('class' => 'form-horizontal', 'url' => array('controller' => 'revenue_share', '#' => Inflector::underscore(Inflector::camelize($membership)).'_settings')))?>
					<?php if(isset($this->request->data['RevenueShareLimit'][$membership_id]['id'])): ?>
						<?=$this->AdminForm->input('RevenueShareLimit.'.$membership_id.'.id')?>
					<?php endif; ?>
					<?=$this->AdminForm->input('RevenueShareLimit.'.$membership_id.'.membership_id', array('value' => $membership_id, 'type' => 'hidden'))?>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('revenue_share_admin', 'Enable Revenue Share For This Membership')?></label>
						<div class="col-sm-2">
							<?=
								$this->AdminForm->input('RevenueShareLimit.'.$membership_id.'.enabled', array(
									'type' => 'checkbox',
									'data-placement' => 'top',
									'data-toggle' => 'popover',
									'data-trigger' => 'hover',
									'data-content' => __d('revenue_share_admin', 'Check this option if you want to enable revenue sharing for this membership.'),
								))
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('revenue_share_admin', 'Where To Credit Revenue Shares Income')?></label>
						<div class="col-sm-2">
							<?=
								$this->AdminForm->input('RevenueShareLimit.'.$membership_id.'.credit', array(
									'type' => 'select',
									'options' => $this->Utility->enum('RevenueShare.RevenueShareLimit', 'credit'),
								))
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('revenue_share_admin', 'Maximum Amount Of Shares Allowed')?></label>
						<div class="col-sm-2">
							<?=
								$this->AdminForm->input('RevenueShareLimit.'.$membership_id.'.max_packs', array(
									'min' => '-1',
									'max' => '32767',
									'step' => '1',
									'data-placement' => 'top',
									'data-toggle' => 'popover',
									'data-trigger' => 'hover',
									'data-content' => __d('revenue_share_admin', 'Enter the maximum amount of shares this membership can own (-1 means unlimited).'),
								))
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('revenue_share_admin', 'Maximum Amount Of Shares Allowed In One Purchase')?></label>
						<div class="col-sm-2">
							<?=
								$this->AdminForm->input('RevenueShareLimit.'.$membership_id.'.max_packs_one_purchase', array(
									'min' => '-1',
									'max' => '32767',
									'step' => '1',
									'data-placement' => 'top',
									'data-toggle' => 'popover',
									'data-trigger' => 'focus',
									'data-content' => __d('revenue_share_admin', 'Enter the maximum amount of shares this membership can buy in one purchase (-1 means unlimited).'),
								))
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('revenue_share_admin', 'How Many Days Between Buying Shares')?></label>
						<div class="col-sm-2">
							<?=
								$this->AdminForm->input('RevenueShareLimit.'.$membership_id.'.days_between', array(
									'min' => '0',
									'max' => '65535',
									'step' => '1',
									'data-placement' => 'top',
									'data-toggle' => 'popover',
									'data-trigger' => 'focus',
									'data-content' => __d('revenue_share_admin', 'Enter the amount of days this membership have to wait to buy another pack of shares.'),
								))
							?>
						</div>
					</div>
					<div class="col-sm-12 text-center paddingten">
						<button class="btn btn-primary"><?=__d('revenue_share_admin', 'Save changes')?></button>
					</div>
					<div class="clearfix"></div>
				<?=$this->AdminForm->end()?>
				<div class="title2">
					<h2><?=__d('revenue_share_admin', 'Current Share Packs')?></h2>
				</div>
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<tbody>
							<tr>
								<th><?=__d('revenue_share_admin', 'Name')?></th>
								<th><?=__d('revenue_share_admin', 'Running days')?></th>
								<th><?=__d('revenue_share_admin', 'Pack price')?></th>
								<th><?=__d('revenue_share_admin', 'Overall return')?></th>
								<th><?=__d('revenue_share_admin', 'Step')?></th>
								<th><?=__d('revenue_share_admin', 'Items')?></th>
								<th><?=__d('revenue_share_admin', 'Actions')?></th>
							</tr>
							<?php if(isset($options[$membership_id])) foreach($options[$membership_id] as $option): ?>
								<tr>
									<td><?=h($option['title'])?></td>
									<td><?=__d('revenue_share_admin', '%d - %d', $option['running_days'], $option['running_days_max'])?></td>
									<td><?=$this->Currency->format($option['price'])?></td>
									<td><?=__d('revenue_share_admin', '%s%%', $option['overall_return'])?></td>
									<td><?=__d('revenue_share_admin', '%d hours and %d minutes', $option['step'] / 60, $option['step'] % 60)?></td>
									<td>
										<?php if(!empty($option['items'])): ?>
											<?php foreach($option['items'] as $item): ?>
												<?php if(isset($items[$item])): ?>
													<?=h($items[$item])?><br/>
												<?php endif; ?>
											<?php endforeach; ?>
										<?php else: ?>
											<?=__d('revenue_share_admin', 'None')?>
										<?php endif; ?>
									</td>
									<td>
										<?=
											$this->AdminForm->postLink(
												'<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('revenue_share_admin', 'Delete').'"></i>',
												array('action' => 'deleteOption', $option['id']), 
												array('escape' => false), 
												__d('revenue_share_admin', 'Are you sure you want to delete "%s"?', $option['title'])
											)
										?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div class="title2">
					<h2><?=__d('revenue_share_admin', 'Add New Revenue Share Pack')?></h2>
				</div>
				<?=$this->AdminForm->create(false, array('class' => 'form-horizontal', 'url' => array('controller' => 'revenue_share', '#' => Inflector::underscore(Inflector::camelize($membership)).'_settings')))?>
					<?=$this->AdminForm->input('RevenueShareOption.membership_id', array('value' => $membership_id, 'type' => 'hidden'))?>
				<div class="row">
					<div class="col-md-7">	
						<div class="form-group">
							<label class="col-sm-5 control-label"><?=__d('revenue_share_admin', 'Name')?></label>
							<div class="col-sm-7">
								<?=
									$this->AdminForm->input('RevenueShareOption.title', array(
										'data-placement' => 'top',
										'data-toggle' => 'popover',
										'data-trigger' => 'focus',
										'data-content' => __d('revenue_share_admin', 'Enter the name of this pack, it is only for information purposes.'),
										'id' => 'RevenueShareOption'.$membership_id.'Title',
									))
								?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 control-label"><?=__d('revenue_share_admin', 'Running Days')?></label>
							<div class="col-sm-7">
								<?=
									$this->AdminForm->input('RevenueShareOption.running_days', array(
										'data-placement' => 'top',
										'data-toggle' => 'popover',
										'data-trigger' => 'focus',
										'data-content' => __d('revenue_share_admin', 'Enter the amount of days this pack will be valid for.'),
										'data-membership' => $membership_id,
										'id' => 'RevenueShareOption'.$membership_id.'RunningDays',
									))
								?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 control-label"><?=__d('revenue_share_admin', 'Max Running Days')?></label>
							<div class="col-sm-7">
								<?=
									$this->AdminForm->input('RevenueShareOption.running_days_max', array(
										'data-placement' => 'top',
										'data-toggle' => 'popover',
										'data-trigger' => 'focus',
										'data-content' => __d('revenue_share_admin', 'Enter the amount of days this pack will be valid for.'),
										'id' => 'RevenueShareOption'.$membership_id.'RunningDaysMax',
									))
								?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 control-label"><?=__d('revenue_share_admin', 'Pack Price')?></label>
							<div class="col-sm-7">
								<div class="input-group">
									<?=
										$this->AdminForm->input('RevenueShareOption.price', array(
											'data-placement' => 'top',
											'data-toggle' => 'popover',
											'data-trigger' => 'focus',
											'data-content' => __d('revenue_share_admin', 'Enter the price for this share pack.'),
											'data-membership' => $membership_id,
											'id' => 'RevenueShareOption'.$membership_id.'Price',
										))
									?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 control-label"><?=__d('revenue_share_admin', 'Overall Return')?></label>
							<div class="col-sm-7">
								<div class="input-group">
									<?=
										$this->AdminForm->input('RevenueShareOption.overall_return', array(
											'min' => '0',
											'step' => '0.001',
											'max' => '999.999',
											'data-placement' => 'top',
											'data-toggle' => 'popover',
											'data-trigger' => 'focus',
											'data-content' => __d('revenue_share_admin', 'Enter the overall return for this pack.'),
											'data-membership' => $membership_id,
											'id' => 'RevenueShareOption'.$membership_id.'OverallReturn',
										))
									?>
									<div class="input-group-addon">%</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 control-label"><?=__d('revenue_share_admin', 'Credit Revenue Step')?></label>
							<div class="col-sm-7">
								<?=
									$this->AdminForm->input('RevenueShareOption.step', array(
										'data-placement' => 'top',
										'data-toggle' => 'popover',
										'data-trigger' => 'focus',
										'data-content' => __d('revenue_share_admin', 'Step (in minutes).'),
										'data-membership' => $membership_id,
										'id' => 'RevenueShareOption'.$membership_id.'Step',
									))
								?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-5 control-label"><?=__d('revenue_share_admin', 'Items Included In This Pack')?></label>
							<div class="col-sm-7">
								<?=
									$this->AdminForm->input('RevenueShareOption.items', array(
										'type' => 'select',
										'class' => 'fancy form-control',
										'multiple' => 'multiple',
										'options' => $items,
										'data-placement' => 'top',
										'data-toggle' => 'popover',
										'data-trigger' => 'focus',
										'data-content' => __d('revenue_share_admin', 'Hold control key to select more than one item'),
										'id' => 'RevenueShareOption'.$membership_id.'Items',
									))
								?>
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="form-group">
							<label class="col-sm-6 control-label"><?=__d('revenue_share_admin', 'Expected Income Per Step')?></label>
							<div class="col-sm-6">
								<div class="input-group">
									<?=
										$this->AdminForm->input('RevenueShareOption.per_step', array(
											'type' => 'money',
											'class' => 'form-control',
											'placeholder' => 0,
											'symbol' => 'input-group-addon',
											'readonly' => true,
											'id' => 'RevenueShareOption'.$membership_id.'PerStep',
										))
									?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label"><?=__d('revenue_share_admin', 'Expected Income Per Day')?></label>
							<div class="col-sm-6">
								<div class="input-group">
									<?=
										$this->AdminForm->input('RevenueShareOption.per_day', array(
											'type' => 'money',
											'class' => 'form-control',
											'placeholder' => 0,
											'symbol' => 'input-group-addon',
											'readonly' => true,
											'id' => 'RevenueShareOption'.$membership_id.'PerDay',
										))
									?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-6 control-label"><?=__d('revenue_share_admin', 'Expected Overall Income')?></label>
							<div class="col-sm-6">
								<div class="input-group">
									<?=
										$this->AdminForm->input('RevenueShareOption.expected', array(
											'type' => 'money',
											'class' => 'form-control',
											'placeholder' => 0,
											'symbol' => 'input-group-addon',
											'readonly' => true,
											'id' => 'RevenueShareOption'.$membership_id.'Expected',
										))
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
					<div class="col-sm-12 text-center paddingten">
						<button class="btn btn-primary"><?=__d('revenue_share_admin', 'Add')?></button>
					</div>
					<div class="clearfix"></div>
				<?=$this->AdminForm->end()?>
			</div>
		<?php endforeach; ?>
		<div id="log" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('revenue_share_admin', 'Revenue Share Log')?></h2>
			</div>
			<?=
				$this->AdminForm->create(false, array(
					'url' => array('action' => 'massactionPacket'),
				))
			?>
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<tbody>
							<tr>
								<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
								<th><?=$this->Paginator->sort('User.username', __d('revenue_share_admin', 'Username'))?></th>
								<th><?=$this->Paginator->sort('User.ActiveMembership.Membership.name', __d('revenue_share_admin', 'Membership'))?></th>
								<th><?=$this->Paginator->sort('title', __d('revenue_share_admin', 'Pack name'))?></th>
								<th><?=$this->Paginator->sort('days_left')?></th>
								<th><?=$this->Paginator->sort('revenued', __d('revenue_share_admin', 'Income'))?></th>
								<th><?=__d('revenue_share_admin', 'Actions')?></th>
							</tr>
							<?php foreach($packets as $packet): ?>
								<tr>
									<td>
										<?=
											$this->AdminForm->checkbox('RevenueSharePacket.'.$packet['RevenueSharePacket']['id'], array(
												'value' => $packet['RevenueSharePacket']['id'],
												'class' => 'ActionCheckbox'
											))
										?>
									</td>
									<td>
										<?php if(!empty($packet['User'])): ?>
											<?=$this->Html->link($packet['User']['username'], array('plugin' => null, 'controller' => 'users', 'action' => 'edit', $packet['User']['id']))?>
										<?php else: ?>
											<?=__d('revenue_share_admin', 'User deleted')?>
										<?php endif; ?>
									</td>
									<td>
										<?php if(!empty($packet['User'])): ?>
											<?=$this->Html->link($packet['User']['ActiveMembership']['Membership']['name'], array('plugin' => null, 'controller' => 'memberships', 'action' => 'edit', $packet['User']['ActiveMembership']['membership_id']))?>
										<?php else: ?>
											<?=__d('revenue_share_admin', 'User deleted')?>
										<?php endif; ?>
									</td>
									<td>
										<?php if($packet['RevenueShareOption']['title']): ?>
											<?=h($packet['RevenueShareOption']['title'])?>
										<?php else: ?>
											<?=__d('revenue_share_admin', 'Not longer available')?>
										<?php endif; ?>
									</td>
									<td><?=$packet['RevenueSharePacket']['days_left'] > 0 ? h($packet['RevenueSharePacket']['days_left']) : __d('revenue_share_admin', 'Completed')?></td>
									<td><?=$this->Currency->format($packet['RevenueSharePacket']['revenued'])?></td>
									<td>
										<?=
											$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('revenue_share_admin', 'Delete').'"></i>',
												array('action' => 'deletePacket',  $packet['RevenueSharePacket']['id']),
												array('escape' => false),
												__d('revenue_share_admin', 'Are you sure you want to delete # %s?', $packet['RevenueSharePacket']['id'])
											)
										?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div class="col-sm-5 text-left">
					<div class="input-group">
						<label for="selectMassAction" class="input-group-addon"><?=__d('revenue_share_admin', 'Mass action')?></label>
						<?=
							$this->AdminForm->input('Action', array(
								'empty' => __d('revenue_share_admin', '--Choose--'),
								'required' => true,
								'id' => 'actionSelect',
								'options' => array(
									'delete' => __d('revenue_share_admin', 'Delete'),
								)
							))
						?>
						<div class="input-group-btn">
							<button class="btn btn-danger"><?=__d('revenue_share_admin', 'Perform action')?></button>
						</div>
					</div>
				</div>
			<?=$this->AdminForm->end()?>
			<div class="col-sm-7 text-right">
				<?=$this->Paginator->counter(array('format' => __d('revenue_share_admin', 'Page {:page} of {:pages}')))?>
			</div>
			<div class="col-sm-12 text-center paddingten">
				<nav>
					<ul class="pagination pagination-sm">
						<?php
							echo $this->Paginator->first('&laquo;', array('tag' => 'li', 'escape' => false));
							echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'a'));
							echo $this->Paginator->last('&raquo;', array('tag' => 'li', 'escape' => false));
						?>
					</ul>
				</nav>
			</div>
		</div>
		
	</div>
</div>
<?php
	$this->Js->buffer("
		jumpToTabByAnchor();

		$('[data-membership]').on('change input', function() {
			var membership = $(this).data('membership');
			var days = $('#RevenueShareOption' + membership  + 'RunningDays').val();
			var price = $('#RevenueShareOption' + membership  + 'Price').val();
			var percent = $('#RevenueShareOption' + membership  + 'OverallReturn').val();
			var step = $('#RevenueShareOption' + membership  + 'Step').val();
			var expected, perDay, perStep;

			if(!days || !price || !percent || !step) {
				$('#RevenueShareOption' + membership  + 'PerDay').val(0);
				$('#RevenueShareOption' + membership  + 'PerStep').val(0);
				$('#RevenueShareOption' + membership  + 'PerExpected').val(0);
				return;
			}

			price = Big(price);
			percent = Big(percent).div(100);

			expected = price.mul(percent);
			$('#RevenueShareOption' + membership  + 'Expected').val(formatCurrency(expected, CurrencyHelperData['commaPlaces'], null, false));

			perDay = expected.div(days);
			$('#RevenueShareOption' + membership  + 'PerDay').val(formatCurrency(perDay, CurrencyHelperData['commaPlaces'], null, false));

			perStep = expected.div(Big(days).mul(24 * 60).div(step));
			$('#RevenueShareOption' + membership  + 'PerStep').val(formatCurrency(perStep, CurrencyHelperData['commaPlaces'], null, false));
		});
	");
?>
