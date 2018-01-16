<div id="PayPal" class="tab-pane fade in">
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
	<?=$this->AdminForm->hidden('PaymentsGateway.PayPal.name', array('value' => 'PayPal'))?>
	<div class="title2">
		<h2><?=__d('admin', 'PayPal settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'PayPal E-mail Address')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.PayPal.api_settings.account_id', array(
				'type' => 'email',
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-content' => __d('admin', 'Please put your own PayPal address for receiving money.'),
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Allow Purchases From Non Verified Accounts')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('PaymentsGateway.PayPal.api_settings.allow_unverified', array(
					'type' => 'checkbox',
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-content' => __d('admin', 'Enable if you want to receive money from non verified accounts. Otherwise payments will be refunded to owner\'s account.'),
				))
			?>
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'API settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'API Username')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.PayPal.api_settings.api_username', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-content' => __d('admin', 'API username')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'API Password')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.PayPal.api_settings.api_password', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-content' => __d('admin', 'API password')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'API Signature')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.PayPal.api_settings.api_signature', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-content' => __d('admin', 'API signature')
			))?>
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'MassPay settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Payment Subject')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.PayPal.api_settings.masspay_subject', array(
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-content' => __d('admin', 'Payment subject')
			))?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Payment Description')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->input('PaymentsGateway.PayPal.api_settings.masspay_description', array(
				'type' => 'textarea',
			))?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
