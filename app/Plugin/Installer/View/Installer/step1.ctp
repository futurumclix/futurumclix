<h5 class="text-xs-center"><?=__d('installer', 'Database settings')?></h5>
<p><?=__d('installer', 'Please provide your database data below. If you do not have any, you need to create new database and user to be able to install our script. You can also ask your hosting provider for more details.')?></p>
<?=$this->AdminForm->create(false)?>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Hostname')?></label>
		<?=$this->AdminForm->input('host', array('class' => 'form-control', 'placeholder' => __d('installer', 'Most likely it will be localhost'), 'required' => true))?>
	</fieldset>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Database username')?></label>
		<?=$this->AdminForm->input('dlogin', array('class' => 'form-control', 'placeholder' => __d('installer', 'Enter your database username'), 'required' => true))?>
	</fieldset>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Database password')?></label>
		<?=$this->AdminForm->input('dpassword', array('class' => 'form-control', 'placeholder' => __d('installer', 'Enter your database password'), 'required' => true))?>
	</fieldset>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Database name')?></label>
		<?=$this->AdminForm->input('database', array('class' => 'form-control', 'placeholder' => __d('installer', 'Enter your database name'), 'required' => true))?>
	</fieldset>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Database prefix (optional)')?></label>
		<?=$this->AdminForm->input('prefix', array('class' => 'form-control', 'placeholder' => __d('installer', 'Enter your database prefix, most likely for shared hostings and databases')))?>
	</fieldset>
	<div class="text-xs-right">
		<button class="btn btn-primary"><?=__d('installer', 'Save and go forward')?></button>
	</div>
<?=$this->AdminForm->end()?>
