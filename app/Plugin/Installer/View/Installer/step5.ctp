<h5 class="text-xs-center"><?=__d('installer', 'Creating default Administrator account')?></h5>
<p><?=__d('installer', 'This is the last thing we need from you. We have to create default Administrator account so you can log in straight away after we will finish this installation process.')?></p>
<?=$this->AdminForm->create('Admin');?>
	<fieldset class="form-group">
		<label><?=__d('installer', 'E-mail address')?></label>
		<?=$this->AdminForm->input('email', array('class' => 'form-control', 'placeholder' => __d('installer', 'Please enter your e-mail address.')))?>
	</fieldset>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Password')?></label>
		<?=$this->AdminForm->input('password', array('class' => 'form-control', 'placeholder' => __d('installer', 'Please enter your password.')))?>
	</fieldset>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Allowed IP\'s')?></label>
		<?=
			$this->AdminForm->input('allowed_ips', array(
				'class' => 'form-control',
				'placeholder' => __d('installer', 'You can enter more than one IP, separated with comma with no spaces. Leave this field empty if you want to allow connections from any IP.'),
			))
		?>
	</fieldset>
	<div class="text-xs-right">
		<button class="btn btn-primary"><?=__d('installer', 'Save admin')?></button>
	</div>
<?=$this->AdminForm->end()?>
