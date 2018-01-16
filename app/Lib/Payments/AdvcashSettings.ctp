<div id="Advcash" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('PaymentsGateway.Advcash.name', array('value' => 'Advcash'))?>
	<div class="title2">
		<h2><?=__d('admin', 'SCI settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Sci Name')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Advcash.api_settings.sciName', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Sci Name')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Account E-mail')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Advcash.api_settings.accountEmail', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter your AdvCash e-mail address')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Account Number')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Advcash.api_settings.accountNumber', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter your AdvCash account number')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'SCI Password')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Advcash.api_settings.secret', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter password from SCI section')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Successful Transaction Page')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'success', 'Advcash'), true)?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Failed Transaction Page')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'cancel', 'Advcash'), true)?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Status Page')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'index', 'Advcash'), true)?>">
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'Merchant API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'API Name')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Advcash.api_settings.name', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter API name from your API section')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Account E-mail')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Advcash.api_settings.email', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter your AdvCash email address')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'API Password')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Advcash.api_settings.password', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter API Password from your API section')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Payment Description')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Advcash.api_settings.cashoutNote', array(
				'type' => 'textarea',
			))?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
