<div id="sweetcaptcha" class="tab-pane fade in">
	<div class="title2">
		<h2><?=__d('admin', 'SweetCaptcha Settings')?></h2>
	</div>
	<?=
		$this->AdminForm->create('Settings', array(
			'url' => array('#' => 'sweetcaptcha'),
			'class' => 'form-horizontal',
		))
	?>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Application ID')?></label>
		<div class="col-sm-8">
			<?=$this->AdminForm->input('Settings.SweetCaptcha.applicationID')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Application Key')?></label>
		<div class="col-sm-8">
			<?=$this->AdminForm->input('Settings.SweetCaptcha.applicationKey')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Application Secret')?></label>
		<div class="col-sm-8">
			<?=$this->AdminForm->input('Settings.SweetCaptcha.applicationSecret')?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button></td>
	</div>
	<?=$this->AdminForm->end()?>
</div>
