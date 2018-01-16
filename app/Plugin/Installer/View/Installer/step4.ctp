<h5 class="text-xs-center"><?=__d('installer', 'Import database settings')?></h5>
<p><?=__d('installer', 'This is quite important step. You have to choose between importing empty database or most common PTC settings which we have prepared especially for you. Whatever option you will choose, you can change all the settings in your Admin Panel so no need to worry about that.')?></p>
<?=$this->AdminForm->create(false)?>
	<fieldset class="form-group">
		<label><?=__d('installer', 'Please choose your database settings')?></label>
	<?=$this->AdminForm->input('option', array(
		'type' => 'select',
		'class' => 'form-control',
		'options' => array(
			'empty' => __d('installer', 'Empty'),
			'default' => __d('installer', 'Default'),
		)
	))?>
	</fieldset>
	<div class="text-xs-right">
		<button class="btn btn-primary"><?=__d('installer', 'Import')?></button>
	</div>
<?=$this->AdminForm->end()?>
