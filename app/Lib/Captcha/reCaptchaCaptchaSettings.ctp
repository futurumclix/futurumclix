<div id="recaptcha" class="tab-pane fade in">
	<div class="title2">
		<h2><?=__d('admin', 'reCaptcha settings')?></h2>
	</div>
	<?=
		$this->AdminForm->create('Settings', array(
			'url' => array('#' => 'recaptcha'),
			'class' => 'form-horizontal',
		))
	?>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'reCaptcha Public Key')?></label>
		<div class="col-sm-8">
			<?=
				$this->AdminForm->input('Settings.reCaptcha.publicKey', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-content' => __d('admin', 'Please put your own public key. If you do not have one you need to sign up on Google Recaptcha site.'),
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'reCaptcha Private Key')?><</label>
		<div class="col-sm-8">
			<?=
				$this->AdminForm->input('Settings.reCaptcha.privateKey', array(
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-content' => __d('admin', 'Please put your own private key. If you do not have one you need to sign up on Google Recaptcha site.'),
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'reCaptcha Theme')?></label>
		<div class="col-sm-8">
			<?=
				$this->AdminForm->input('Settings.reCaptcha.theme', array(
					'options' => array('light' => 'Light', 'dark' => 'Dark'),
				))
			?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button></td>
	</div>
	<?=$this->AdminForm->end()?>
</div>
