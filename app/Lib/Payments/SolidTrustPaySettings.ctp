<div id="SolidTrustPay" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('PaymentsGateway.SolidTrustPay.name', array('value' => 'SolidTrustPay'))?>
	<div class="title2">
		<h2><?=__d('admin', 'SCI settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Merchant Account')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.SolidTrustPay.api_settings.merchantAccount', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-content' => __d('admin', 'Merchant account')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Payment Button Name (Sci_Name)')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.SolidTrustPay.api_settings.paymentName', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-content' => __d('admin', 'Payment Button Name (sci_name)')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Payment Button Password')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.SolidTrustPay.api_settings.paymentPassword', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-content' => __d('admin', 'Payment Button Password')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Notify URL')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'index', 'SolidTrustPay'), true)?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Return URL')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'index', 'SolidTrustPay'), true)?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Confirm URL')?></label>
		<div class="col-sm-6">
			<input type="text" class="form-control" readonly value="<?=Router::url(array('admin' => false, 'controller' => 'payments', 'action' => 'success', 'SolidTrustPay'), true)?>">
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'API Name')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.SolidTrustPay.api_settings.APIName', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-content' => __d('admin', 'API Name')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'API Password')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.SolidTrustPay.api_settings.APIPassword', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-content' => __d('admin', 'API Password')
			))?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
