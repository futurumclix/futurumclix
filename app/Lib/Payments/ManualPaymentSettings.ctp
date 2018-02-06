<div id="ManualPayment" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('PaymentsGateway.ManualPayment.name', array('value' => 'ManualPayment'))?>
	<div class="title2">
		<h2><?=__d('admin', 'API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Gateway Name')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.ManualPayment.api_settings.gateway_name', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please specify name to be shown to users on payment page'),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Account')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.ManualPayment.api_settings.account', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Please specify account name to be shown to users on payment page'),
			))?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
