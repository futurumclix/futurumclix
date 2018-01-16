<div id="PerfectMoney" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('PaymentsGateway.PerfectMoney.name', array('value' => 'PerfectMoney'))?>
	<div class="title2">
		<h2><?=__d('admin', 'PerfectMoney SCI settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Account UID')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.PerfectMoney.api_settings.merchantName', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter your Account UID in format UXXXXXXX')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Alternate Passphrase')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.PerfectMoney.api_settings.passphrase', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter Aleternate Passphrase from your profile settings')
			))?>
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'PerfectMoney API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Account ID')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.PerfectMoney.api_settings.accountID', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter your Account ID, the one you use to log in to your account')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Account Password')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.PerfectMoney.api_settings.accountPassword', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter your Account Passwored, the on you use to log in to your account')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Payment Description')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.PerfectMoney.api_settings.cashoutMemo', array(
				'type' => 'textarea',
			))?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
