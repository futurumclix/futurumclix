<div id="Payza" class="tab-pane fade in">
	<?=$this->AdminForm->create('PaymentsGateway', array('class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('PaymentsGateway.Payza.name', array('value' => 'Payza'))?>
	<div class="title2">
		<h2><?=__d('admin', 'Payza settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Payza E-mail Address')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Payza.api_settings.account_id', array(
				'type' => 'email',
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-content' => __d('admin', 'Please put your own Payza address for receiving money.'),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Alert URL')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'index', 'Payza'), true)?>">
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'API Password')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Payza.api_settings.api_password', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-content' => __d('admin', 'API password')
			))?>
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'MassPay settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Payment Description')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Payza.api_settings.masspay_description', array(
				'type' => 'textarea',
			))?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
