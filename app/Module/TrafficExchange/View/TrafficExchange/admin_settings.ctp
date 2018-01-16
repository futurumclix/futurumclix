<div class="col-md-12">
	<div class="title">
		<h2><?=__d('traffic_exchange_admin', 'Traffic Exchange settings')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#settings"><?=__d('traffic_exchange_admin', 'Settings')?></a></li>
		<?php foreach($memberships as $membership):?>
			<li><a data-toggle="tab" href="#<?=strtolower($membership)?>"><?=__d('traffic_exchange_admin', '%s settings', $membership)?></a></li>
		<?php endforeach; ?>
		<li><a data-toggle="tab" href="#packages"><?=__d('traffic_exchange_admin', 'Packages')?></a></li>
	</ul>
	<div class="tab-content">
		<div id="settings" class="tab-pane fade in active">
			<div class="title2">
				<h2><?=__d('traffic_exchange_admin', 'Settings')?></h2>
			</div>
			<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('traffic_exchange_admin', 'Auto Approve Ads')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('TrafficExchangeSettings.trafficExchange.autoApprove', array(
								'type' => 'checkbox',
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('traffic_exchange_admin', 'Check Connection Before New Ad Preview')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('TrafficExchangeSettings.trafficExchange.checkConnection', array(
								'type' => 'checkbox',
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('traffic_exchange_admin', 'Check Connection Timeout')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('TrafficExchangeSettings.trafficExchange.checkConnectionTimeout', array(
								'type' => 'number',
								'step' => 1,
								'min' => 0,
								'max' => 3600,
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('traffic_exchange_admin', 'Preview Time')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('TrafficExchangeSettings.trafficExchange.previewTime', array(
								'type' => 'number',
								'step' => 1,
								'min' => 0,
								'max' => 3600,
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('traffic_exchange_admin', 'Activity Minimum')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('TrafficExchangeSettings.trafficExchange.activityMinimum', array(
								'type' => 'number',
								'step' => 1,
								'min' => 0,
								'max' => 3600,
							))
						?>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="text-center col-md-12 paddingten">
					<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<?php foreach($memberships as $membership_id => $membership):?>
			<div id="<?=strtolower($membership)?>" class="tab-pane fade in">
				<div class="title2">
					<h2><?=__d('traffic_exchange_admin', '%s settings', $membership)?></h2>
				</div>
				<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
					<?php if(isset($this->request->data['TrafficExchangeMembership'][$membership_id]['id'])): ?>
						<?=$this->AdminForm->input('TrafficExchangeMembership.'.$membership_id.'.id', array('type' => 'hidden'))?>
					<?php endif; ?>
					<?=$this->AdminForm->input('TrafficExchangeMembership.'.$membership_id.'.membership_id', array('value' => $membership_id, 'type' => 'hidden'))?>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('traffic_exchange_admin', 'Allow To Exchange Points To Cash')?></label>
						<div class="col-sm-4">
							<?=
								$this->AdminForm->input('TrafficExchangeMembership.allow_exchange', array(
									'type' => 'checkbox',
								))
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('traffic_exchange_admin', '1 point cash value')?></label>
						<div class="col-sm-4 input-group">
							<?=
								$this->AdminForm->input('TrafficExchangeMembership.point_value', array(
									'type' => 'money',
									'data-toggle' => 'popover',
									'data-content' => __d('traffic_exchange_admin', 'How much 1 point is worth while exchanging it to cash.'),
									'data-placement' => 'top',
									'data-trigger' => 'focus',
								))
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('traffic_exchange_admin', 'Surf Ratio')?></label>
						<div class="col-sm-4 input-group">
							<?=
								$this->AdminForm->input('TrafficExchangeMembership.surf_ratio', array(
									'type' => 'money',
									'data-toggle' => 'popover',
									'data-content' => __d('traffic_exchange_admin', 'How much points user will earn for watching one advertisement'),
									'data-placement' => 'top',
									'data-trigger' => 'focus',
								))
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('traffic_exchange_admin', 'Surf Time')?></label>
						<div class="col-sm-4">
							<?=
								$this->AdminForm->input('TrafficExchangeMembership.surf_time', array(
									'type' => 'number',
									'step' => 1,
									'min' => 0,
									'max' => 3600,
									'data-toggle' => 'popover',
									'data-content' => __d('traffic_exchange_admin', 'How much points user will earn for watching one advertisement'),
									'data-placement' => 'top',
									'data-trigger' => 'focus',
								))
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('traffic_exchange_admin', 'Enable Captcha On Surfing')?></label>
						<div class="col-sm-4">
							
						</div>
					</div>
					<div class="clearfix"></div>
					<div class="text-center col-md-12 paddingten">
						<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
					</div>
				<?=$this->AdminForm->end()?>
			</div>
			<?php endforeach; ?>
		<div id="packages" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('traffic_exchange_admin', 'Click packages')?></h2>
			</div>
			<form class="form-horizontal">
				<div class="form-group">
					<label class="col-sm-1 control-label"><?=__d('admin', 'No.')?></label>
					<label class="col-sm-3 control-label"><?=__d('admin', 'Type')?></label>
					<label class="col-sm-4 control-label"><?=__d('admin', 'Amount')?></label>
					<label class="col-sm-4 control-label"><?=__d('admin', 'Price')?></label>
				</div>
				<div class="form-group" id="exampleRow">
					<label class="col-sm-1 control-label">1.</label>
					<div class="col-sm-3">
						<input type="number" class="form-control">
					</div>
					<div class="col-sm-4">
						<input type="number" class="form-control">
					</div>
					<div class="col-sm-4">
						<input type="number" class="form-control">
					</div>
				</div>
				<div class="col-md-12 text-right">
				<a id="addTableRowButton">
					<i title="<?=__d('admin', 'Click To Add More Packages')?>" class="fa fa-plus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>
				</a>
				</div>
				<div class="clearfix"></div>
				<div class="text-center col-md-12 paddingten">
					<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
				</div>
			</form>
		</div>
	</div>
</div>
