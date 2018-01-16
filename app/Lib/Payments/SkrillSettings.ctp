<div id="Skrill" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('PaymentsGateway.Skrill.name', array('value' => 'Skrill'))?>
	<div class="title2">
		<h2><?=__d('admin', 'API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'E-mail')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Skrill.api_settings.email', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', ''),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Secret word')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Skrill.api_settings.secret', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', ''),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Send e-mail notifications about IPN events')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Skrill.api_settings.notify', array(
				'type' => 'checkbox',
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', ''),
			))?>
		</div>
	</div>


	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'API password')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Skrill.api_settings.api_password', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', ''),
			))?>
		</div>
	</div>

	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
