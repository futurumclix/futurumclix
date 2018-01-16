<div class="col-md-12">
	<div class="title">
		<h2><?=__d('bot_system_admin', 'Bot System')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#settings"><?=__d('bot_system_admin', 'General Settings')?></a></li>
		<?php foreach($memberships as $membership): ?>
			<li><a data-toggle="tab" href="#<?=Inflector::underscore(Inflector::camelize($membership))?>"><?=__d('bot_system_admin', '%s settings', $membership)?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="tab-content">
		<div id="settings" class="tab-pane fade in active">
			<div class="title2">
				<h2><?=__d('bot_system_admin', 'General settings')?></h2>
			</div>
			<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Upline Activity Requirement')?></label>
					<div class="col-sm-2">
						<?=
							$this->AdminForm->input('BotSystemSettings.botSystem.activity', array(
								'type' => 'checkbox',
								'data-placement' => 'top',
								'data-toggle' => 'popover',
								'data-trigger' => 'hover',
								'data-content' => __d('bot_system_admin', 'Enable to force users to click specified amount of ads (in activity settings) the day before to get credited from his referrals clicks'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Show Bot Clicks When Activity Requirement Is Not Met')?></label>
					<div class="col-sm-2">
						<?=
							$this->AdminForm->input('BotSystemSettings.botSystem.countNotCredited', array(
								'type' => 'checkbox',
								'data-placement' => 'top',
								'data-toggle' => 'popover',
								'data-trigger' => 'hover',
								'data-content' => __d('bot_system_admin', 'It will show bot clicks in user\'s statistics even they will not be credited.'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Available Bots')?></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" readonly value="<?=h($botsAvailable)?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Bots in Use')?></label>
					<div class="col-sm-3">
						<input type="text" class="form-control" readonly value="<?=h($botsNumber - $botsAvailable)?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Add Bots Automatically (Daily)')?></label>
					<div class="col-sm-2">
						<?=
							$this->AdminForm->input('BotSystemSettings.botSystem.autoAdd', array(
								'type' => 'checkbox',
								'data-placement' => 'top',
								'data-toggle' => 'popover',
								'data-trigger' => 'hover',
								'data-content' => __d('bot_system_admin', 'Enable auto adding bots (daily)'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'How Much Bots To Add Per Day')?></label>
					<div class="col-sm-2">
						<div class="input-group">
							<div class="input-group-addon"><?=__d('bot_system_admin', 'Min')?></div>
							<?=
								$this->AdminForm->input('BotSystemSettings.botSystem.autoAddMin', array(
									'type' => 'number',
									'min' => 0,
									'step' => 1,
								))
							?>
						</div>
					</div>
					<div class="col-sm-2">
						<div class="input-group">
							<div class="input-group-addon"><?=__d('bot_system_admin', 'Max')?></div>
							<?=
								$this->AdminForm->input('BotSystemSettings.botSystem.autoAddMax', array(
									'type' => 'number',
									'min' => 0,
									'step' => 1,
								))
							?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Cleanup Bot System statistics after')?></label>
					<div class="col-sm-2">
						<?=
							$this->AdminForm->input('BotSystemSettings.botSystem.statsCleanupDays', array(
								'type' => 'number',
								'min' => 0,
								'step' => 1,
							))
						?>
					</div>
					<label class="col-sm-2 control-label"><?=__d('bot_system_admin', 'days')?></label>
				</div>
				<div class="col-md-12 text-center paddingten">
					<button class="btn btn-primary"><?=__d('bot_system_admin', 'Save changes')?></button>
				</div>
				<div class="clearfix"></div>
			<?=$this->AdminForm->end()?>
			<div class="title2">
				<h2><?=__d('bot_system_admin', 'Generate new bots')?></h2>
			</div>
			<?=$this->AdminForm->create(false, array('class' => 'form-horizontal', 'url' => array('action' => 'create')))?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Generate New Bots')?></label>
					<div class="col-sm-3">
						<div class="input-group">
							<?=$this->AdminForm->input('no', array('type' => 'number', 'min' => 1, 'step' => 1))?>
							<span class="input-group-btn">
								<button class="btn btn-primary"><?=__d('bot_system_admin', 'Generate')?></button>
							</span>
						</div>
					</div>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<?php foreach($memberships as $membership_id => $membership): ?>
			<div id="<?=Inflector::underscore(Inflector::camelize($membership))?>" class="tab-pane fade in">
				<div class="title2">
					<h2><?=__d('bot_system_admin', '%s membership\'s Bots Settings', $membership)?></h2>
				</div>
				<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
					<?php if(isset($this->request->data['BotSystemGroup'][$membership_id]['id'])): ?>
						<?=$this->AdminForm->input('BotSystemGroup.'.$membership_id.'.id')?>
					<?php endif; ?>
					<?=$this->AdminForm->input('BotSystemGroup.'.$membership_id.'.membership_id', array('type' => 'hidden', 'value' => $membership_id))?>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Click Value')?></label>
						<div class="col-sm-3">
							<div class="input-group">
								<?=
									$this->AdminForm->input('BotSystemGroup.'.$membership_id.'.click_value', array(
										'data-placement' => 'top',
										'data-toggle' => 'popover',
										'data-trigger' => 'focus',
										'data-content' => __d('bot_system_admin', 'This is how much upline will get per bot clicks')
									))
								?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Points per click')?></label>
						<div class="col-sm-3">
							<div class="input-group">
								<?=
									$this->AdminForm->input('BotSystemGroup.'.$membership_id.'.points_per_click', array(
										'data-placement' => 'top',
										'data-toggle' => 'popover',
										'data-trigger' => 'focus',
										'data-content' => __d('bot_system_admin', 'This is how much points upline will get per bot clicks')
									))
								?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Minimum Clicks Per Day')?></label>
						<div class="col-sm-3">
							<?=
								$this->AdminForm->input('BotSystemGroup.'.$membership_id.'.min_clicks', array(
									'min' => 0,
									'max' => 255,
									'step' => 1,
									'data-placement' => 'top',
									'data-toggle' => 'popover',
									'data-trigger' => 'focus',
									'data-content' => __d('bot_system_admin', 'Minimum amount of clicks this bot will make per day')
								))
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Maximum Clicks Per Day')?></label>
						<div class="col-sm-3">
							<?=
								$this->AdminForm->input('BotSystemGroup.'.$membership_id.'.max_clicks', array(
									'min' => 0,
									'max' => 255,
									'step' => 1,
									'data-placement' => 'top',
									'data-toggle' => 'popover',
									'data-trigger' => 'focus',
									'data-content' => __d('bot_system_admin', 'Maximum amount of clicks this bot will make per day')
								))
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Chance To Skip A Day')?></label>
						<div class="col-sm-3">
							<?=
								$this->AdminForm->input('BotSystemGroup.'.$membership_id.'.skip_chance', array(
									'min' => 0,
									'max' => 100,
									'step' => 1,
									'data-placement' => 'top',
									'data-toggle' => 'popover',
									'data-trigger' => 'focus',
									'data-content' => __d('bot_system_admin', 'Chance (in percent) that bot will not click certain day')
								))
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Maximum Average')?></label>
						<div class="col-sm-3">
							<?=
								$this->AdminForm->input('BotSystemGroup.'.$membership_id.'.max_avg', array(
									'min' => 0,
									'max' => 255,
									'step' => 0.01,
									'data-placement' => 'top',
									'data-toggle' => 'popover',
									'data-trigger' => 'focus',
									'data-content' => __d('bot_system_admin', 'Maximum Average (in X.XX format) this group can make')
								))
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Activity Days')?></label>
						<div class="col-sm-2">
							<div class="input-group">
								<div class="input-group-addon"><?=__d('bot_system_admin', 'Min')?></div>
								<?=
									$this->AdminForm->input('BotSystemGroup.'.$membership_id.'.min_activity_days', array(
										'min' => 0,
										'max' => 65535,
										'step' => 1,
										'data-placement' => 'top',
										'data-toggle' => 'popover',
										'data-trigger' => 'focus',
										'data-content' => __d('bot_system_admin', 'Minimum amount of days this bot will stay active')
									))
								?>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="input-group">
								<div class="input-group-addon"><?=__d('bot_system_admin', 'Max')?></div>
								<?=
									$this->AdminForm->input('BotSystemGroup.'.$membership_id.'.max_activity_days', array(
										'min' => 0,
										'max' => 65535,
										'step' => 1,
										'data-placement' => 'top',
										'data-toggle' => 'popover',
										'data-trigger' => 'focus',
										'data-content' => __d('bot_system_admin', 'Maximum amount of days this bot will stay active')
									))
								?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=__d('bot_system_admin', 'Chance To Stop Clicking')?></label>
						<div class="col-sm-3">
							<?=
								$this->AdminForm->input('BotSystemGroup.'.$membership_id.'.stop_chance', array(
									'min' => 0,
									'max' => 100,
									'step' => 1,
									'data-placement' => 'top',
									'data-toggle' => 'popover',
									'data-trigger' => 'focus',
									'data-content' => __d('bot_system_admin', 'How much (in percent) chances for bot to stop clicking.')
								))
							?>
						</div>
					</div>
					<div class="col-md-12 text-center paddingten">
						<button class="btn btn-primary"><?=__d('bot_system_admin', 'Save')?></button>
					</div>
				<?=$this->AdminForm->end()?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php $this->Js->buffer("
	function setRequiredOnAutoAdd(v) {
		$('#BotSystemSettingsBotSystemAutoAddMin').prop('required', v);
		$('#BotSystemSettingsBotSystemAutoAddMax').prop('required', v);
	}
	$('#BotSystemSettingsBotSystemAutoAdd').on('change', function() {
		setRequiredOnAutoAdd($(this).is(':checked'));
	});
	setRequiredOnAutoAdd($('#BotSystemSettingsBotSystemAutoAdd').is(':checked'));
")?>
