<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Promotion Settings')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#signuppromo"><?=__d('admin', 'Signup Bonus')?></a></li>
		<li><a data-toggle="tab" href="#depositpromo"><?=__d('admin', 'Deposit Bonus')?></a></li>
	</ul>
	<div class="tab-content">
		<div id="signuppromo" class="tab-pane fade in active">
			<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Enable Signup Bonus')?></label>
					<div class="col-sm-8">
						<?=
							$this->AdminForm->input('Settings.signUpBonus.enable', array(
								'type' => 'checkbox',
							))
						?>
					</div>
				</div>
				<div id="signupbonusenabled">
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('admin', 'Bonus Period')?></label>
						<div class="col-sm-3">
							<div class="input-group">
								<div class="input-group-addon"><?=__d('admin', 'From')?></div>
								<?=
									$this->AdminForm->input('Settings.signUpBonus.start', array(
										'type' => 'datetime',
									))
								?>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="input-group">
								<div class="input-group-addon"><?=__d('admin', 'To')?></div>
								<?=
									$this->AdminForm->input('Settings.signUpBonus.end', array(
										'type' => 'datetime',
									))
								?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('admin', 'Bonus Type')?></label>
						<div class="col-sm-2">
							<?=
								$this->AdminForm->input('Settings.signUpBonus.type', array(
									'empty' => 'Please select...',
								))
							?>
						</div>
					</div>
					<div id="moneyType" style="display: none;">
						<div class="form-group">
							<label class="col-sm-4 control-label"><?=__d('admin', 'Bonus Value')?></label>
							<div class="col-sm-2">
								<div class="input-group">
									<?=
										$this->AdminForm->input('Settings.signUpBonus.amount', array(
											'type' => 'money',
										))
									?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label"><?=__d('admin', 'Where To Credit Bonus')?></label>
							<div class="col-sm-2">
								<?=
									$this->AdminForm->input('Settings.signUpBonus.credit', array(
										'options' => array(
											'account' => __d('admin', 'Account Balance'),
											'purchase' => __d('admin', 'Purchase Balance'),
										),
									))
								?>
							</div>
						</div>
					</div>
					<div id="membershipType" style="display: none;">
						<div class="form-group">
							<label class="col-sm-4 control-label"><?=__d('admin', 'Membership Type')?></label>
							<div class="col-sm-2">
								<?=
									$this->AdminForm->input('Settings.signUpBonus.membership', array(
										'options' => $membershipsB,
									))
								?>
							</div>
							<div class="col-sm-3">
								<div class="input-group">
									<div class="input-group-addon"><?=__d('admin', 'Period (In Days)')?></div>
									<?=
										$this->AdminForm->input('Settings.signUpBonus.period', array(
											'type' => 'number',
											'min' => 1,
											'step' => 1,
										))
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="text-center">
					<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<div id="depositpromo" class="tab-pane fade in">
			<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Enable Deposit Bonus')?></label>
					<div class="col-sm-8">
						<?=
							$this->AdminForm->input('Settings.depositBonus.enable', array(
								'type' => 'checkbox',
							))
						?>
					</div>
				</div>
				<div id="depositbonusenabled">
					<?php foreach($gateways as $gateway => $gatewayName): ?>
						<div class="title2">
							<h2><?=h($gatewayName)?></h2>
						</div>
						<?php foreach($memberships as $membershipId => $membershipName): ?>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?=__d('admin', '%s members', $membershipName)?></label>
								<div class="col-sm-3">
									<div class="input-group">
										<?=
											$this->AdminForm->input("Settings.depositBonus.$gateway.$membershipId.amount", array(
												'type' => 'money',
											))
										?>
									</div>
								</div>
								<div class="col-sm-1 text-center">
									<label class="label label-danger">+</label>
								</div>
								<div class="col-sm-3">
									<div class="input-group">
										<?=
											$this->AdminForm->input("Settings.depositBonus.$gateway.$membershipId.percent", array(
												'type' => 'number',
												'min' => 0,
												'step' => 1,
											))
										?>
										<div class="input-group-addon">%</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</div>
				<div class="text-center">
					<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
	</div>
</div>
<?php $this->Js->buffer("
	function toggle_type() {
		if($('#SettingsSignUpBonusType').val() == 'membership') {
			$('#membershipType').show();
			$('#moneyType').hide();
		} else if($('#SettingsSignUpBonusType').val() == 'money') {
			$('#membershipType').hide();
			$('#moneyType').show();
		} else {
			$('#membershipType').hide();
			$('#moneyType').hide();
		}
	}
	function toggle_signupbonus() {
		if($('#SettingsSignUpBonusEnable').is(':checked')) {
			$('#signupbonusenabled').show();
		} else {
			$('#signupbonusenabled').hide();
		}
	}
	function toggle_depositbonus() {
		if($('#SettingsDepositBonusEnable').is(':checked')) {
			$('#depositbonusenabled').show();
		} else {
			$('#depositbonusenabled').hide();
		}
	}
	$('#SettingsSignUpBonusType').change(toggle_type);
	$('#SettingsSignUpBonusEnable').change(toggle_signupbonus);
	$('#SettingsDepositBonusEnable').change(toggle_depositbonus);
	toggle_type();
	toggle_signupbonus();
	toggle_depositbonus();
")?>
