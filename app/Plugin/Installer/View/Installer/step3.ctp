<h5 class="text-xs-center"><?=__d('installer', 'Site settings')?></h5>
<p><?=__d('installer', 'Great, we have just installed database on your server. All we need now is to gather few basic information about your site from you. Please fill in the form below and press the button to move forward.')?></p>
<?=$this->AdminForm->create('Settings')?>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Install for production?')?></label>
		<?=$this->AdminForm->booleanRadio('productionInstall')?>
	</fieldset>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Site name')?></label>
		<?=$this->AdminForm->input('siteName', array('class' => 'form-control', 'placeholder' => __d('installer', 'Please enter your site name.'), 'required' => true))?>
	</fieldset>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Site title')?></label>
		<?=$this->AdminForm->input('siteTitle', array('class' => 'form-control', 'placeholder' => __d('installer', 'Please enter your site title. It will be used for web browsers title etc.'), 'required' => true))?>
	</fieldset>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Site url')?></label>
		<?=$this->AdminForm->input('siteURL', array('class' => 'form-control', 'placeholder' => __d('installer', 'Please enter your site url. Make sure to enter full address like http://futurumclix.com'), 'required' => true))?>
	</fieldset>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Site e-mail')?></label>
		<?=$this->AdminForm->input('siteEmail', array('class' => 'form-control', 'placeholder' => __d('installer', 'Please enter your site e-mail address, where we will send notifications etc.'), 'required' => true))?>
	</fieldset>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Sender e-mail')?></label>
		<?=$this->AdminForm->input('siteEmailSender', array('class' => 'form-control', 'placeholder' => __d('installer', 'Please enter your site e-mail address, which will be used as a sender address for all user\'s notifications.'), 'required' => true))?>
	</fieldset>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Random security values')?></label>
		<span data-toggle="tooltip" data-original-title="Leave checked if you want to use random generated security values (requires OpenSSL PHP extension installed">
			<?=$this->AdminForm->input('Security.auto', array('type' => 'checkbox', 'value' => 1, 'checked' => true, 'class' => '', 'placeholder' => __d('installer', 'Leave checked if you want to use random generated security values (requires OpenSSL PHP extension installed).'), 'required' => true))?>
		</span>
	</fieldset>
	<span id="securityValues" style="display:none">
		<fieldset class="form-group">
			<label><?=__d('installer', 'Security salt')?></label>
			<?=$this->AdminForm->input('Security.salt', array('class' => 'form-control', 'placeholder' => __d('installer', 'A random string used in security hashing methods.')))?>
		</fieldset>
		<fieldset class="form-group">
			<label><?=__d('installer', 'Security key')?></label>
			<?=$this->AdminForm->input('Security.key', array('class' => 'form-control', 'placeholder' => __d('installer', 'A random string used as key in security encryption/decryption methods.')))?>
		</fieldset>
		<fieldset class="form-group">
			<label><?=__d('installer', 'Security cripher seed')?></label>
			<?=$this->AdminForm->input('Security.cipherSeed', array('class' => 'form-control', 'placeholder' => __d('installer', 'A random numeric string (digits only) used to encrypt/decrypt strings.')))?>
		</fieldset>
	</span>
	<div class="text-xs-right">
		<button class="btn btn-primary"><?=__d('installer', 'Save info')?></button>
	</div>
<?=$this->AdminForm->end()?>
<?=$this->Js->buffer("
	$('#SecurityAuto').on('change', function() {
		if($(this).is(':checked')) {
			$('#securityValues').find(':input').removeAttr('required');
			$('#securityValues').hide();
		} else {
			$('#securityValues').show();
			$('#securityValues').find(':input').prop('required', true);
		}
	})
")?>
