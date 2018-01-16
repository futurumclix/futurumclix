<div id="Neteller" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('PaymentsGateway.Neteller.name', array('value' => 'Neteller'))?>
	<div class="title2">
		<h2><?=__d('admin', 'Neteller API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Client ID')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Neteller.api_settings.clientId', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter Client ID from Developer / Apps section')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Client Secret')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Neteller.api_settings.clientSecret', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter Client Secret from Developer / Apps section')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Webhook Secret Key')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Neteller.api_settings.webhookKey', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please enter Webhook secret key from Developer / Webhooks section')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Webhook URL')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'index', 'Neteller'), true)?>">
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'MassPay settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Payment Subject')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Neteller.api_settings.masspaySubject', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Payment subject for every payment')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Payment Description')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Neteller.api_settings.masspayDescription', array(
				'type' => 'textarea',
			))?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
