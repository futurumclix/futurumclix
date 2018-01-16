<?=
	$this->AdminForm->create('Admin', array(
		'inputDefaults' => array('required' => false),
		'class' => 'form-horizontal'
	))
?>
<?=$this->AdminForm->input('id')?>
<div class="col-md-12">
<div class="title">
	<h2><?=__d('admin', 'Edit Admin');?></h2>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label"><?=__d('admin', 'E-mail Address')?></label>
	<div class="col-sm-4">
			<?=$this->AdminForm->input('email')?>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label"><?=__d('admin', 'Password')?></label>
	<div class="col-sm-4">
		<?=
			$this->AdminForm->input('password', array(
				'data-toggle' => 'tooltip',
				'data-placement' => 'top',
				'title' => __d('admin', 'Please left empty if you do not want to change'),
			))
		?>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label"><?=__d('admin', 'Allowed IPs')?></label>
	<div class="col-sm-4">
		<?=
			$this->AdminForm->input('allowed_ips', array(
				'data-toggle' => 'tooltip',
				'data-placement' => 'top',
				'title' => __d('admin', 'You can enter more than one IP, separated with comma with no spaces, for example: 1.1.1.1,2.2.2.2. Leave this field empty if you want to allow connections from any IP.'),
			))
		?>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label"><?=__d('admin', 'Account security')?></label>
	<div class="col-sm-4">
		<?=
			$this->AdminForm->input('secret', array(
				'options' => $this->Utility->enum('Admin', 'secret'),
			));
		?>
	</div>
</div>
<div id="ga">
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__('GA Code:')?></label>
		<div class="col-sm-4">
			<?=$this->GoogleAuthenticator->getQRCode($this->request->data['Admin']['secret_data'])?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__('Secret Code:')?></label>
		<div class="col-sm-4">
			<?=
				$this->AdminForm->input('secret_data', array('type' => 'hidden'))
			?>
			<?=h($this->request->data['Admin']['secret_data'])?>
		</div>
	</div>
</div>
<div class="text-center">
	<button class="btn btn-primary"><?=__d('admin', 'Save')?></button>
</div>
<?=$this->AdminForm->end()?>
<?php $this->Js->buffer("
	function changeMode() {
		if($('#AdminSecret').val() == ".Admin::SECRET_GA.") {
			$('#ga').show();
		} else {
			$('#ga').hide();
		}
	}
	$('#AdminSecret').change(function() {
		changeMode();
	});
	changeMode();
");
