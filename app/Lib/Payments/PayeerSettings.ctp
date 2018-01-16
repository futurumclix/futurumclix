<div id="Payeer" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('PaymentsGateway.Payeer.name', array('value' => 'Payeer'))?>
	<div class="title2">
		<h2><?=__d('admin', 'Payeer Payment API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Shop Number (ID)')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Payeer.api_settings.shopNumber', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter your Shop ID from Merchant section')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Secret Key')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Payeer.api_settings.secretKey', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter your Secret Key from Merchant section')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Success URL')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'success', 'Payeer'), true)?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Fail URL')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'cancel', 'Payeer'), true)?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Status URL')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'index', 'Payeer'), true)?>">
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'Payeer Cashout API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Account Number')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Payeer.api_settings.accountNumber', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter your Payeer Account number in format PXXXXXXX')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Shop Number (ID)')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Payeer.api_settings.cashoutApiId', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter your ID from Mass Payment section')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Secret Key')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Payeer.api_settings.cashoutApiKey', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter your Secret Key from Mass Payment section')
			))?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
