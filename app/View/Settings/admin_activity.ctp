<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Activity settings')?></h2>
	</div>
	<?=$this->AdminForm->create('Settings', array('class' => 'form-horizontal'))?>
		<div class="form-group">
			<label class="col-sm-6 control-label"><?=__d('admin', 'How Many Ads User Needs To Click The Day Before To Earn Referral\'s Commission')?></label>
			<div class="col-sm-2">
				<?=
					$this->AdminForm->input('userActivityClicks', array(
						'type' => 'number',
						'step' => 1,
						'min' => 0,
						'data-trigger' => 'focus',
						'data-toggle' => 'popover',
						'data-placement' => 'top',
						'data-content' => __d('admin', 'Put 0 to add referral\'s commission all the time'),
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-6 control-label"><?=__d('admin', 'How Many Clicks User Needs To Be Able To Withdraw')?></label>
			<div class="col-sm-2">
				<?=
					$this->AdminForm->input('withdrawClicks', array(
						'type' => 'number',
						'step' => 1,
						'min' => 0,
						'data-trigger' => 'focus',
						'data-toggle' => 'popover',
						'data-placement' => 'top',
						'data-content' => __d('admin', 'Put 0 to disable'),
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-6 control-label"><?=__d('admin', 'After How Many Days Of Inactivity Do You Want To Suspend Users')?></label>
			<div class="col-sm-2">
				<?=
					$this->AdminForm->input('inactivitySuspendDays', array(
						'type' => 'number',
						'step' => 1,
						'min' => 0,
						'data-trigger' => 'focus',
						'data-toggle' => 'popover',
						'data-placement' => 'top',
						'data-content' => __d('admin', 'Put 0 to not to suspended inactive users at all'),
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-6 control-label"><?=__d('admin', 'After How Many Days Of Inactivity Do You Want To Delete Users')?></label>
			<div class="col-sm-2">
				<?=
					$this->AdminForm->input('inactivityDeleteDays', array(
						'type' => 'number',
						'step' => 1,
						'min' => 0,
						'data-trigger' => 'focus',
						'data-toggle' => 'popover',
						'data-placement' => 'top',
						'data-content' => __d('admin', 'Put 0 to not to delete inactive users at all'),
					))
				?>
			</div>
		</div>
		<div class="title2">
			<h2><?=__d('admin', 'Security settings')?></h2>
		</div>
		<div class="form-group">
			<label class="col-sm-6 control-label"><?=__d('admin', 'E-mail Verification Required')?></label>
			<div class="col-sm-2">
				<?=
					$this->AdminForm->input('emailVerification', array(
						'type' => 'checkbox',
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-6 control-label"><?=__d('admin', 'For How Many Hours Block Cashout After Payment E-mail Change')?></label>
			<div class="col-sm-2">
				<?=
					$this->AdminForm->input('cashoutBlockTime', array(
						'type' => 'number',
						'step' => 1,
						'min' => 0,
						'data-trigger' => 'focus',
						'data-toggle' => 'popover',
						'data-placement' => 'top',
						'data-content' => __d('admin', 'Put 0 to disable'),
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-6 control-label"><?=__d('admin', 'Force SSL Connection')?></label>
			<div class="col-sm-2">
				<?=
					$this->AdminForm->input('forceSSL', array(
						'type' => 'checkbox',
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-6 control-label"><?=__d('admin', 'Disable SSL Connection On Forum')?></label>
			<div class="col-sm-2">
				<?=
					$this->AdminForm->input('disableSSLForum', array(
						'type' => 'checkbox',
					))
				?>
			</div>
		</div>
		<div class="title2">
			<h2><?=__d('admin', 'Google Authenticator')?></h2>
		</div>
		<div class="form-group">
			<label class="col-sm-6 control-label"><?=__d('admin', 'Choose where user has to use GA')?></label>
			<div class="col-sm-6">
				<?=
					$this->AdminForm->input('googleAuthenticator', array(
						'type' => 'select',
						'class' => 'fancy form-control',
						'multiple' => 'multiple',
						'options' => array('login' => 'Logging in', 'profile' => 'Editing profile', 'cashout' => 'Withdrawing money'),
					))
				?>
			</div>
		</div>
		<div class="col-sm-12 text-center paddingten">
			<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
