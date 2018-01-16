<div id="Coinpayments" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('PaymentsGateway.Coinpayments.name', array('value' => 'Coinpayments'))?>
	<div class="title2">
		<h2><?=__d('admin', 'API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'IPN URL')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'index', 'Coinpayments'), true)?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Merchant ID')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Coinpayments.api_settings.merchant_id', array(
				'type' => 'text',
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'CoinPayments Merchant ID'),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Public API key')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Coinpayments.api_settings.public_key', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'CoinPayments Public API key'),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Private API key')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Coinpayments.api_settings.private_key', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'CoinPayments Private API key'),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Secret')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Coinpayments.api_settings.secret', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'CoinPayments Secret Key'),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Autoconfirm cashouts')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.Coinpayments.api_settings.auto_confirm', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'If enabled withdrawal will complete without email confirmation.'),
				'type' => 'checkbox',
			))?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
