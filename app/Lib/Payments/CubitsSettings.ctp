<div id="Cubits" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('PaymentsGateway.Cubits.name', array('value' => 'Cubits'))?>
	<div class="title2">
		<h2><?=__d('admin', 'API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'IPN URL')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'index', 'Cubits'), true)?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'API key')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Cubits.api_settings.api_key', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Cubits Public API key'),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'API secret')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Cubits.api_settings.api_secret', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Cubits Private API key'),
			))?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
