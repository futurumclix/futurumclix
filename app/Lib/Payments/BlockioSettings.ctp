<div id="Blockio" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('PaymentsGateway.Blockio.name', array('value' => 'Blockio'))?>
	<div class="title2">
		<h2><?=__d('admin', 'API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'IPN URL')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'index', 'Blockio'), true)?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'API key')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Blockio.api_settings.api_key', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Blockio Public API key'),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Secret PIN')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Blockio.api_settings.pin', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Blockio Private API key'),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Confirmations')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Blockio.api_settings.confirmations', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Blockio confirmations'),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Cashout Priority')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Blockio.api_settings.cashout_priority', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Blockio cashout_priority'),
				'options' => array('low' => __d('admin', 'Low'), 'medium' => __d('admin', 'Medium'), 'high' => __d('admin', 'High')),
			))?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
